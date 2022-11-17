<?php

namespace App\Http\Controllers;

use App\Models\ErrorLog;
use App\Models\Log;
use App\Models\Referral;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class ReferralController extends Controller
{
    public function login(Request $request)
    {
        $credentials = [
            'username' => $request->username,
            'password' => $request->password];

        if (Auth::attempt($credentials)) {
            //GET USER ACCESS
            $user = Auth::user();
            $user->role = "TRIAGGE";
            $user->topic = "TRIAGGE";
            $access_rights = DB::table('access_rights')
                ->join('departments', 'access_rights.department_id', '=', 'departments.id')
                ->where('userid', $user->id)->get()->first();
            if ($access_rights) {
                $user->role = $access_rights->department;
                $user->topic = $access_rights->code;
                if ($access_rights->role == 1) {
                    $user->role = "MANAGEMENT";
                    $user->topic = "MANAGEMENT";
                }
            }

            return ['code' => 200, 'result' => $user];
        }
        return ['code' => 500, 'message' => 'Invalid credentials'];
    }

    public function change_password(Request $request)
    {
        $user = User::find($request->id);
        if (!Hash::check($request->old_password, $user->password)) {

            return ['code' => 500, 'message' => 'Incorrect old password'];
        }

        $user->password = bcrypt($request->new_password);
        $user->save();
        return ['code' => 200, 'message' => 'Password successfully changed'];
    }

    public function insert_referral(Request $request)
    {
        try {
            #Insert new referrals triggered by  mobile app (Notifier)
            $referral = Referral::where('firebase_key', $request->firebase_key)->get()->first();
            if ($referral) {
                return ['code' => 500, 'message' => 'Referral already added'];
            }

            $referral = new Referral;
            $referral->userid = $request->userid;
            $referral->firebase_key = $request->firebase_key;
            $referral->patient = $request->patient;
            $referral->gender = $request->gender;
            $referral->age = $request->age;
            $referral->diagnosis = $request->diagnosis;
            $referral->chief_complaint = $request->chief_complaint;
            $referral->department = $request->department;
            $referral->referring_hospital = $request->referring_hospital;
            $referral->date_created = $request->date_created;
            $referral->save();

            $logs = new Log;
            $logs->referral_id = $referral->id;
            $logs->forwarded_by = $request->userid;
            $logs->origin = 'TRIAGGE';
            $logs->destination = $request->department;
            $logs->remarks = $request->remarks;
            $logs->save();

            return ['code' => 200, 'message' => 'New referral added'];
        } catch (\Exception $err) {
            $this->insert_error($request->userid, $err);
            return ['code' => 500, 'message' => $err];
        }
    }

    public function forward_referral(Request $request)
    {
        try {
            $referral = Referral::where('firebase_key', $request->firebase_key)->get()->first();
            if (!$referral) {
                return ['code' => 401, 'message' => 'Referral not found'];
            }

            if ($request->origin != $referral->department) {
                return ['code' => 500, 'message' => 'Cannot forward, referral no longer belongs to your department'];
            }

            $_logs = Log::where([['referral_id', '=', $referral->id], ['origin', '=', $referral->id]])->get()->last();
            if ($_logs) {
                $_logs->date_tagged = Carbon::now()->toDateTimeString();
                $_logs->save();
            }

            $logs = new Log;
            $logs->referral_id = $referral->id;
            $logs->forwarded_by = $request->forwarded_by;
            $logs->origin = $request->origin;
            $logs->destination = $request->destination;
            $logs->remarks = $request->remarks;
            $logs->save();

            $referral->department = $request->destination;
            $referral->save();
            return ['code' => 200, 'message' => 'Referral forwarded successfully'];
        } catch (\Exception $err) {
            $this->insert_error($request->forwarded_by, $err);
            return ['code' => 500, 'message' => $err];
        }
    }

    public function tag_as_done(Request $request)
    {
        try {
            $referral = Referral::where('firebase_key', $request->firebase_key)->get()->first();
            if (!$referral) {
                return ['code' => 401, 'message' => 'Referral not found'];
            }

            $logs = Log::where('referral_id', $referral->id)->get()->first();
            if (!$logs) {
                return ['code' => 401, 'message' => 'Referral not found'];
            }

            $logs->tagged_by = $request->userid;
            $logs->remarks = $request->remarks;
            $logs->save();

            $referral->status = $request->remarks;
            $referral->save();

            return ['code' => 200, 'message' => 'Tagged successfully'];
        } catch (\Exception $err) {
            $this->insert_error($request->userid, $err);
            return ['code' => 500, 'message' => $err];
        }
    }

    public function get_referrals(Request $request)
    {
        try {
            $search_filters =
                [
                    ['patient', 'LIKE', '%' . $request->search_key . '%'],
                    ['department', '=', $request->department],
                    ['status', '=', null]
                ];

            return ['code' => 200, 'results' => Referral::where($search_filters)
                ->orWhere('status', '<>', 'DONE')
                ->get()];
        } catch (\Exception $err) {
            return ['code' => 500, 'message' => $err];
        }
    }

    public function load_summary(Request $request)
    {
        if ($request->date_created) {
            return ['code' => 200, 'results' => Referral::where('date_created', 'LIKE', '%' . $request->date_created . '%')->orderBy('referring_hospital', 'ASC')->get(['referring_hospital', 'status'])];
        } else {
            return ['code' => 200, 'results' => Referral::orderBy('referring_hospital', 'ASC')->get(['referring_hospital', 'status'])];
        }
    }

    public function load_logs(Request $request)
    {
        $referral = Referral::where('firebase_key', $request->firebase_key)->get()->first();
        if (!$referral) {
            return ['code' => 401, 'message' => 'Referral not found'];
        }

        $columns = ['origin', 'destination', 'remarks', 'date_tagged', 'date_forwarded'];
        return ['code' => 200, 'results' => Log::where('referral_id', '=', $referral->id)->get($columns)];
    }

    public function insert_error($id, $message)
    {
        $error_logs = new ErrorLog;
        $error_logs->userid = $id;
        $error_logs->message = $message;
        $error_logs->save();
    }
}
