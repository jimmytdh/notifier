<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\ErrorLog;
use App\Models\Log;
use App\Models\Referral;
use App\Models\User;
use App\Models\UserPrivilege;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ReferralController extends Controller
{

    protected $jwt_token;

    public function __construct()
    {
        $this->jwt_token = "";
    }

    public function login(Request $request)
    {
        try {
            $credentials = ['username' => $request->username, 'password' => $request->password];
            if (Auth::attempt($credentials)) {
                $user_priv = UserPrivilege::where([['user_id', Auth::id()], ['syscode', 'notifier']])->get()->first();
                if ($user_priv == null) {
                    Auth::logout();
                    return ['code' => 500, 'message' => 'Access denied'];
                }

                $user = Auth::user();
                $user->role = $user_priv->level;
                $user->topic = $user_priv->level;
                if ($user_priv->level == "EMED") {
                    $user->topic = "TRIAGGE";
                }

                // #CHECK IF USER IS DEPARTMENT/DOCTORS
                // if ($user_priv->level != 'TRIAGGE' && $user_priv->level != 'MANAGEMENT') {
                //     $department = Department::where('code', $user_priv->level)->first();
                //     if ($department) {
                //         $user->role = $department->department;
                //     }
                // }
                return ['code' => 200, 'result' => $user];
            }
            return ['code' => 500, 'message' => 'Invalid credentials'];
        } catch (\Exception $exception) {
            return ['code' => 500, 'message' => 'Server Error'];
        }
    }

    public function change_password(Request $request)
    {
        try {
            $user = User::find($request->id);

            if (!$user) {
                return ['code' => 500, 'message' => 'User dont exists'];
            }

            if (!Hash::check($request->old_password, $user->password)) {
                return ['code' => 500, 'message' => 'Incorrect old password'];
            }

            $user->password = bcrypt($request->new_password);
            $user->save();
            return ['code' => 200, 'message' => 'Password successfully changed'];
        } catch (\Exception $exception) {
            return ['code' => 500, 'message' => 'Server Error'];
        }
    }

    public function insert_referral_5pm(Request $request)
    {
        try {
            if (true) {
                #ADDING EARLY RETURN STATEMENT BECAUSE REFERRAL SYSTEM STILL INSERT DATA FROM FRIREBASE
                #POSSIBLE DUPLICATE ENTRY (FIREBASE + MYSQL DATABASE())
                return ['code' => 200, 'message' => 'New referral added'];
            }

            $dateReferred = Carbon::parse($request->date_referred);
            $hour = (int) $dateReferred->format('G');
            if ($hour >= 8 && $hour < 17) {
                return ['code' => 200, 'message' => 'New referral added'];
            }

            $referral = Referral::where('firebase_key', $request->firebase_key)->first();
            if (!$referral) {
                $referral = new Referral;
                $referral->userid = 99999;
                $referral->firebase_key = $request->firebase_key;
                $referral->patient = $request->patient;
                $referral->gender = $request->sex;
                $referral->age = $request->age;
                $referral->diagnosis = $request->diagnosis;
                $referral->chief_complaint = $request->chiefComplaint;
                $referral->department = 'ER';
                $referral->referring_hospital = $request->referring_hospital;
                $referral->date_created = $request->date_referred;
                $referral->save();

                $logs = new Log;
                $logs->referral_id = $referral->id;
                $logs->forwarded_by = 0;
                $logs->origin = $request->referring_hospital;
                $logs->destination = 'EMERGENCY ROOM';
                $logs->remarks = '';
                $logs->date_forwarded = Carbon::now();
                $logs->save();
            }
            return ['code' => 200, 'message' => 'New referral added'];
        } catch (\Exception $err) {
            $this->insert_error(99999, $err->getMessage());
            return ['code' => 500, 'message' => $err->getMessage()];
        }
    }

    public function insert_referral(Request $request)
    {
        try {
            $referral = Referral::where('firebase_key', $request->firebase_key)->first();
            if ($referral) {
                return response()->json(['code' => 500, 'message' => 'Referral already added']);
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
            $referral->date_created = Carbon::now();
            $referral->save();

            $logs = new Log;
            $logs->referral_id = $referral->id;
            $logs->forwarded_by = 0;
            $logs->origin = $request->referring_hospital;
            $logs->destination = 'TRIAGGE';
            $logs->remarks = '';
            $logs->date_forwarded = $request->date_created;
            $logs->save();

            $logs = new Log;
            $logs->referral_id = $referral->id;
            $logs->forwarded_by = $request->userid;
            $logs->origin = 'TRIAGGE';
            $logs->destination = $request->department;
            $logs->remarks = $request->remarks;
            $logs->save();

            $this->forwardPushNotification('referrals_' . $request->code, $request->referring_hospital, $request->patient, $request->age, $request->gender);
            return ['code' => 200, 'message' => "Referral successfully forwarded"];
        } catch (\Exception $err) {
            $this->insert_error($request->userid, $err->getMessage());
            return ['code' => 500, 'message' => $err->getMessage()];
        }
    }

    public function forward_referral(Request $request)
    {
        try {
            $referral = Referral::where('firebase_key', $request->firebase_key)->first();
            if (!$referral) {
                return ['code' => 401, 'message' => 'Referral not found'];
            }

            if ($request->origin != $referral->department) {
                return ['code' => 500, 'message' => 'Cannot forward, referral no longer belongs to your department'];
            }

            $_logs = Log::where([['referral_id', '=', $referral->id], ['origin', '=', $referral->department]])->get()->last();
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

            $this->forwardPushNotification('referrals_' . $request->code, $referral->referring_hospital, $referral->patient, $referral->age, $referral->gender);
            return response()->json(['code' => 200, 'message' => 'Referral forwarded successfully']);
        } catch (\Exception $err) {
            $this->insert_error($request->forwarded_by, $err->getMessage());
            return response()->json(['code' => 500, 'message' => $err->getMessage()]);
        }
    }

    public function tag_as_done(Request $request)
    {
        try {
            $referral = Referral::where('firebase_key', $request->firebase_key)->get()->first();
            if (!$referral) {
                return ['code' => 401, 'message' => 'Referral not found'];
            }

            if ($referral->status == "DONE") {
                return ['code' => 401, 'message' => 'Referrals already tagged as DONE'];
            }

            $logs = Log::where('referral_id', $referral->id)->get()->last();
            if (!$logs) {
                return ['code' => 401, 'message' => 'Referral not found'];
            }

            $logs->tagged_by = $request->userid;
            $logs->remarks = $request->remarks;
            $logs->save();

            $referral->status = "DONE";
            $referral->save();

            return ['code' => 200, 'message' => 'Tagged successfully'];
        } catch (\Exception $err) {
            $this->insert_error($request->userid, $err->getMessage());
            return ['code' => 500, 'message' => $err->getMessage()];
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

            return [
                'code' => 200,
                'results' => Referral::where($search_filters)
                    ->orWhere('status', '<>', 'DONE')
                    ->orderBy('date_created', 'DESC')
                    ->skip($request->input('page', 0) * 10)
                    ->take(11)
                    ->get()
            ];
        } catch (\Exception $err) {
            return ['code' => 500, 'message' => $err->getMessage()];
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

        $columns = ['origin', 'destination', 'remarks', 'date_tagged', 'date_forwarded', 'forwarded_by', 'tagged_by'];
        $logs = Log::where('referral_id', '=', $referral->id)->get($columns);
        $userIds = $logs->pluck('forwarded_by')
            ->merge($logs->pluck('tagged_by'))
            ->filter(function ($id) {
                return !is_null($id) && (int) $id > 0;
            })
            ->unique()
            ->values();

        $usersById = User::whereIn('id', $userIds)->get(['id', 'fname', 'lname', 'mname'])->keyBy('id');

        $logs = $logs->map(function ($log) use ($usersById) {
            $forwardId = (int) ($log->forwarded_by ?? 0);
            $taggedId = (int) ($log->tagged_by ?? 0);
            $log->forwarded_by_name = $forwardId > 0 && $usersById->has($forwardId)
                ? trim(($usersById[$forwardId]->fname ?? '') . ' ' . ($usersById[$forwardId]->lname ?? '')) ?: ($usersById[$forwardId]->name ?? null)
                : ($forwardId === 0 ? 'System' : null);
            $log->tagged_by_name = $taggedId > 0 && $usersById->has($taggedId)
                ? trim(($usersById[$taggedId]->fname ?? '') . ' ' . ($usersById[$taggedId]->lname ?? '')) ?: ($usersById[$taggedId]->name ?? null)
                : null;
            return $log;
        });

        return [
            'code' => 200,
            'referral' => [
                'ward' => $referral->ward,
                'bed_no' => $referral->bed_no
            ],
            'results' => $logs
        ];
    }

    public function insert_error($id, $message)
    {
        $error_logs = new ErrorLog;
        $error_logs->userid = $id;
        $error_logs->message = $message;
        $error_logs->save();
    }


    #WEB API
    public function insertError($message)
    {
        $error_logs = new ErrorLog;
        $error_logs->userid = Auth::id();
        $error_logs->message = $message;
        $error_logs->save();
    }

    public function insertReferral(Request $request)
    {
        try {
            $referral = Referral::where('firebase_key', $request->firebase_key)->first();
            if ($referral) {
                // Already inserted (DB succeeded but Firebase delete failed previously).
                // Return success so the frontend still runs deleteChild to clean up Firebase.
                return response(['code' => 200, 'message' => 'Referral successfully forwarded']);
            }

            $referral = new Referral;
            $referral->userid = Auth::id();
            $referral->firebase_key = $request->firebase_key;
            $referral->patient = $request->patient;
            $referral->gender = $request->gender;
            $referral->age = $request->age;
            $referral->diagnosis = $request->diagnosis;
            $referral->chief_complaint = $request->chief_complaint;
            $referral->department = $request->department;
            $referral->referring_hospital = $request->referring_hospital;
            $referral->date_created = Carbon::now();
            $referral->save();

            $logs = new Log;
            $logs->referral_id = $referral->id;
            $logs->forwarded_by = 0;
            $logs->origin = $request->referring_hospital;
            $logs->destination = 'TRIAGGE';
            $logs->remarks = '';
            $logs->date_forwarded = $request->date_created;
            $logs->save();

            $logs = new Log;
            $logs->referral_id = $referral->id;
            $logs->forwarded_by = Auth::id();
            $logs->origin = 'TRIAGGE';
            $logs->destination = $request->department;
            $logs->remarks = $request->remarks;
            $logs->save();
            return response(['code' => 200, 'message' => "Referral successfully forwarded"]);
        } catch (\Exception $exception) {
            return response(['code' => 500, 'message' => $exception->getMessage()]);
        }
    }

    public function storeManualReferral(Request $request)
    {
        try {
            $request->validate([
                'patient' => 'required|string|max:255',
                'gender' => 'required|string|max:20',
                'age' => 'required|integer|min:0',
                'department' => 'required|string|max:255',
                'code' => 'required|string|max:20',
                'remarks' => 'required|string|max:500',
                'diagnosis' => 'nullable|string',
                'chief_complaint' => 'nullable|string',
                'ward' => 'nullable|string|max:255',
                'bed_no' => 'nullable|string|max:255'
            ]);

            $referral = new Referral;
            $referral->userid = Auth::id();
            $referral->firebase_key = (string) Str::uuid();
            $referral->patient = trim($request->patient);
            $referral->gender = trim($request->gender);
            $referral->age = (int) $request->age;
            $referral->diagnosis = $request->diagnosis;
            $referral->chief_complaint = $request->chief_complaint;
            $referral->ward = $request->ward;
            $referral->bed_no = $request->bed_no;
            $referral->department = $request->department;
            $referral->referring_hospital = session('access_rights');
            $referral->date_created = Carbon::now();
            $referral->type = 'Manual';
            $referral->save();

            $logs = new Log;
            $logs->referral_id = $referral->id;
            $logs->forwarded_by = Auth::id();
            $logs->origin = session('access_rights');
            $logs->destination = $request->department;
            $logs->remarks = $request->remarks;
            $logs->date_forwarded = Carbon::now();
            $logs->save();

            $this->forwardPushNotification('referrals_' . $request->code, $referral->referring_hospital, $referral->patient, $referral->age, $referral->gender);
            return response(['code' => 200, 'message' => 'Manual referral created and forwarded']);
        } catch (\Exception $exception) {
            self::insertError($exception->getMessage());
            return response(['code' => 500, 'message' => $exception->getMessage()]);
        }
    }

    public function forwardReferral(Request $request)
    {
        try {
            $referral = Referral::find($request->id);
            if (!$referral) {
                return ['code' => 401, 'message' => 'Referral not found'];
            }

            if (session('code') !== 'TRIAGGE' && session('access_rights') != $referral->department) {
                return ['code' => 500, 'message' => 'Cannot forward, referral no longer belongs to your department'];
            }

            $_logs = Log::where([['referral_id', '=', $referral->id], ['origin', '=', $referral->department]])->get()->last();
            if ($_logs) {
                $_logs->date_tagged = Carbon::now()->toDateTimeString();
                $_logs->save();
            }

            $logs = new Log;
            $logs->referral_id = $referral->id;
            $logs->forwarded_by = Auth::id();
            $logs->origin = $referral->department;
            $logs->destination = $request->department;
            $logs->remarks = $request->remarks;
            $logs->save();

            $ward = trim((string) $request->ward);
            $bedNo = trim((string) $request->bed_no);
            $referral->ward = $ward !== '' ? $ward : null;
            $referral->bed_no = $bedNo !== '' ? $bedNo : null;
            $referral->department = $request->department;
            $referral->save();

            $this->forwardPushNotification('referrals_' . $request->code, $referral->referring_hospital, $referral->patient, $referral->age, $referral->gender);
            return response(['code' => 200, 'message' => 'Referral forwarded successfully']);
        } catch (\Exception $err) {
            self::insert_error(Auth::id(), $err->getMessage());
            return response(['code' => 500, 'message' => $err->getMessage()]);
        }
    }

    public function tagAsDone(Request $request)
    {
        try {
            $referral = Referral::find($request->id);
            if (!$referral) {
                return ['code' => 500, 'message' => 'Referral not found'];
            }

            if ($referral->status == "DONE") {
                return ['code' => 500, 'message' => 'Referrals already tagged as DONE'];
            }

            $logs = new Log;
            $logs->referral_id = $request->id;
            $logs->forwarded_by = Auth::id();
            $logs->tagged_by = Auth::id();
            $logs->origin = $referral->department;
            $logs->destination = 'CYCLE END';
            $logs->remarks = $request->remarks;
            $logs->date_forwarded = Carbon::now();
            $logs->save();

            $ward = trim((string) $request->ward);
            $bedNo = trim((string) $request->bed_no);
            $referral->ward = $ward !== '' ? $ward : null;
            $referral->bed_no = $bedNo !== '' ? $bedNo : null;
            $referral->status = "DONE";
            $referral->save();

            #TODO: THIS ACTION TAG AS DONE
            return ['code' => 200, 'message' => 'Tagged successfully'];
        } catch (\Exception $err) {
            self::insertError($err->getMessage());
            return ['code' => 500, 'message' => $err->getMessage()];
        }
    }

    public function tagAsDoneTriagge(Request $request)
    {
        try {
            $referral = Referral::where('firebase_key', $request->firebase_key)->first();
            if ($referral) {
                return response(['code' => 500, 'message' => 'Referral already added']);
            }

            $referral = new Referral;
            $referral->userid = Auth::id();
            $referral->firebase_key = $request->firebase_key;
            $referral->patient = $request->patient;
            $referral->gender = $request->gender;
            $referral->age = $request->age;
            $referral->diagnosis = $request->diagnosis;
            $referral->chief_complaint = $request->chief_complaint;
            $referral->department = $request->department;
            $referral->referring_hospital = $request->referring_hospital;
            $referral->date_created = Carbon::now();
            $referral->status = "DONE";
            $referral->save();

            $logs = new Log;
            $logs->referral_id = $referral->id;
            $logs->forwarded_by = 0;
            $logs->origin = $request->referring_hospital;
            $logs->destination = 'TRIAGGE';
            $logs->remarks = "";
            $logs->date_forwarded = $request->date_created;
            $logs->save();

            $logs = new Log;
            $logs->referral_id = $referral->id;
            $logs->forwarded_by = Auth::id();
            $logs->origin = "TRIAGGE";
            $logs->destination = 'CYCLE END';
            $logs->remarks = $request->remarks;
            $logs->date_forwarded = Carbon::now();
            $logs->save();

            return ['code' => 200, 'message' => 'Tagged successfully'];
        } catch (\Exception $err) {
            self::insertError($err->getMessage());
            return ['code' => 500, 'message' => $err->getMessage()];
        }
    }

    public function getReferrals(Request $request)
    {
        try {
            $patient = $request->search_patient;
            $referrals = Referral::
                where('department', session('access_rights'))
                ->where(function ($query) use ($patient) {
                    $query->whereNull('status');
                    if ($patient != "") {
                        $query->where("patient", "LIKE", "%" . $patient . "%");
                    }
                })
                ->orderBy('date_created', 'DESC')
                ->skip($request->page * 10)
                ->take(11)
                ->get();
            return response(['code' => 200, 'results' => $referrals]);
        } catch (\Exception $exception) {
            return response(['code' => 500, 'message' => $exception->getMessage()]);
        }
    }

    public function getReturnedReferrals(Request $request)
    {
        try {
            $patient = $request->search_patient ?? '';
            $referrals = Referral::where('department', 'EMED')
                ->whereNull('status')
                ->when($patient !== '', function ($query) use ($patient) {
                    $query->where('patient', 'LIKE', '%' . $patient . '%');
                })
                ->orderBy('date_created', 'DESC')
                ->skip($request->page * 10)
                ->take(11)
                ->get();
            return response(['code' => 200, 'results' => $referrals]);
        } catch (\Exception $exception) {
            return response(['code' => 500, 'message' => $exception->getMessage()]);
        }
    }

    public function getTriaggeReferrals(Request $request)
    {
        try {
            $patient = $request->search_patient ?? '';
            $referrals = Referral::where(function ($query) {
                $query->whereNull('type')->orWhere('type', '!=', 'Manual');
            })
                ->when($patient !== '', function ($query) use ($patient) {
                    $query->where('patient', 'LIKE', '%' . $patient . '%');
                })
                ->orderBy('date_created', 'DESC')
                ->skip($request->page * 10)
                ->take(11)
                ->get();
            return response(['code' => 200, 'results' => $referrals]);
        } catch (\Exception $exception) {
            return response(['code' => 500, 'message' => $exception->getMessage()]);
        }
    }

    public function getAllReferrals(Request $request)
    {
        try {
            $patient = $request->search_patient;
            $referrals = Referral::where(function ($query) use ($patient) {
                if ($patient != "") {
                    $query->where("patient", "LIKE", "%" . $patient . "%");
                }
            })
                ->orderBy('date_created', 'DESC')
                ->skip($request->page * 10)
                ->take(11)
                ->get();
            return response(['code' => 200, 'results' => $referrals]);
        } catch (\Exception $exception) {
            return response(['code' => 500, 'message' => $exception->getMessage()]);
        }
    }

    public function loadLogs(Request $request)
    {
        $referral = Referral::find($request->id);
        if (!$referral) {
            return ['code' => 401, 'message' => 'Referral not found'];
        }

        $columns = ['origin', 'destination', 'remarks', 'date_tagged', 'date_forwarded', 'forwarded_by', 'tagged_by'];
        $logs = Log::where('referral_id', '=', $referral->id)->get($columns);
        $userIds = $logs->pluck('forwarded_by')
            ->merge($logs->pluck('tagged_by'))
            ->filter(function ($id) {
                return !is_null($id) && (int) $id > 0;
            })
            ->unique()
            ->values();

        $usersById = User::whereIn('id', $userIds)->get(['id', 'fname', 'lname'])->keyBy('id');

        $logs = $logs->map(function ($log) use ($usersById) {
            $forwardId = (int) ($log->forwarded_by ?? 0);
            $taggedId = (int) ($log->tagged_by ?? 0);
            $log->forwarded_by_name = $forwardId > 0 && $usersById->has($forwardId)
                ? trim(($usersById[$forwardId]->fname ?? '') . ' ' . ($usersById[$forwardId]->lname ?? '')) ?: ($usersById[$forwardId]->name ?? null)
                : ($forwardId === 0 ? 'System' : null);
            $log->tagged_by_name = $taggedId > 0 && $usersById->has($taggedId)
                ? trim(($usersById[$taggedId]->fname ?? '') . ' ' . ($usersById[$taggedId]->lname ?? '')) ?: ($usersById[$taggedId]->name ?? null)
                : null;
            return $log;
        });

        return response([
            'code' => 200,
            'referral' => [
                'ward' => $referral->ward,
                'bed_no' => $referral->bed_no
            ],
            'results' => $logs
        ]);
    }


    #These will create a jwt token to be used to create a bearer token
    public static function createJwtToken()
    {
        $serviceAccountPath = public_path('assets/notifier-5e4e8-firebase-adminsdk-ihhu7-65600f7d74.json');

        if (!File::exists($serviceAccountPath)) {
            throw new \Exception('Service account file not found');
        }

        $serviceAccount = json_decode(File::get($serviceAccountPath), true);
        $now = time();

        // Create JWT payload
        $payload = [
            "iss" => $serviceAccount['client_email'], // Issuer (client email)
            "scope" => "https://www.googleapis.com/auth/firebase.messaging", // Scope for Firebase Messaging
            "aud" => "https://oauth2.googleapis.com/token", // Audience for Google OAuth
            "iat" => $now, // Issued at time
            "exp" => $now + 3600 // Expire in 1 hour
        ];

        // Sign the JWT with the private key from the service account
        $privateKey = $serviceAccount['private_key'];
        return JWT::encode($payload, $privateKey, 'RS256'); // RS256 algorithm
    }

    #Returns a bearer token to be used when sending push notification
    public function generateBearerToken()
    {

        if (Cache::has('firebase_bearer_token')) {
            return Cache::get('firebase_bearer_token');
        }

        if (!$this->jwt_token) {
            $this->jwt_token = $this->createJwtToken();
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://oauth2.googleapis.com/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => http_build_query([
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $this->jwt_token
            ]),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
            CURLOPT_SSL_VERIFYHOST => 0,  // Disable SSL host verification (only for development)
            CURLOPT_SSL_VERIFYPEER => 0   // Disable SSL certificate verification (only for development)
        ));

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            throw new \Exception(curl_error($curl));  // Catch curl errors
        }

        curl_close($curl);

        // Parse the response to extract the token and expiration time
        $responseArray = json_decode($response, true);
        if (isset($responseArray['access_token'])) {
            $bearerToken = $responseArray['access_token'];
            $expiresIn = $responseArray['expires_in'];  // Expiry time in seconds, usually 3600

            // Cache the token with an expiration time slightly shorter than its actual expiration
            Cache::put('firebase_bearer_token', $bearerToken, now()->addSeconds($expiresIn - 60));  // Subtract 1 minute for buffer

            return $bearerToken;
        } else {
            throw new \Exception('Failed to retrieve access token.');
        }


    }

    public function sendPushNotification(Request $request)
    {
        try {
            $request->validate([
                'topic' => 'required|string',
                'hospital_referrer' => 'required|string',
                'patient' => 'required|string',
                'age' => 'required|integer',
                'sex' => 'required|string'
            ]);

            $bearer_token = 'Authorization: Bearer ' . $this->generateBearerToken();
            $payload = [
                "message" => [
                    "topic" => $request->topic,
                    "notification" => [
                        "title" => "New Referral — " . $request->hospital_referrer,
                        "body"  => $request->patient . ", " . $request->age . " y/o " . $request->sex,
                    ],
                    "data" => [
                        "hospital_referrer" => $request->hospital_referrer,
                        "patient"           => $request->patient,
                        "age"               => (string) $request->age,
                        "sex"               => $request->sex,
                    ],
                ]
            ];

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://fcm.googleapis.com/v1/projects/notifier-5e4e8/messages:send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => array(
                    $bearer_token,
                    'Content-Type: application/json'
                ),
                CURLOPT_SSL_VERIFYHOST => 0,  // Disable SSL host verification
                CURLOPT_SSL_VERIFYPEER => 0   // Disable SSL certificate verification
            ));

            $response = curl_exec($curl);
            if ($response === false) {
                $error = curl_error($curl);
                curl_close($curl);
                return response()->json(['error' => $error], 500); // Return the cURL error
            }

            curl_close($curl);
            return response()->json($response);
        } catch (\Exception $exception) {
            $this->insert_error(99999, "PUSH NOTIF=>" . $exception->getMessage());
            return response()->json($exception->getMessage(), 500);
        }
    }


    public function forwardPushNotification($topic, $hospital_referrer, $patient, $age, $sex)
    {
        try {
            $bearer_token = 'Authorization: Bearer ' . $this->generateBearerToken();
            $payload = [
                "message" => [
                    "topic" => $topic,
                    "notification" => [
                        "title" => "New Referral — " . $hospital_referrer,
                        "body"  => $patient . ", " . $age . " y/o " . $sex,
                    ],
                    "data" => [
                        "hospital_referrer" => $hospital_referrer,
                        "patient"           => $patient,
                        "age"               => (string) $age,
                        "sex"               => (string) $sex,
                    ],
                ]
            ];

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://fcm.googleapis.com/v1/projects/notifier-5e4e8/messages:send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION
                => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => array(
                    $bearer_token,
                    'Content-Type: application/json'
                ),
                CURLOPT_SSL_VERIFYHOST => 0,  // Disable SSL host verification
                CURLOPT_SSL_VERIFYPEER => 0   // Disable SSL certificate verification
            ));

            $response = curl_exec($curl);
            if ($response === false) {
                $error = curl_error($curl);
                $this->insert_error(99999, "PUSH NOTIF=>Curl error: " . $error);
            }

            curl_close($curl);
        } catch (\Exception $exception) {
            $this->insert_error(99999, "PUSH NOTIF=>" . $exception->getMessage());
        }
    }

    //QUEUE FUNCTION
    public function store(Request $request)
    {
        try {
            if (empty(trim((string) $request->chief_complaint)) && empty(trim((string) $request->diagnosis))) {
                return response()->json(['code' => 500, 'message' => 'No diagnosis and Chief Complaint passed']);
            }

            $existing = DB::table('csmc_notifier.queue_referrals')
                ->where('referring_hospital', $request->referring_hospital)
                ->where('patient', $request->patient)
                ->whereNull('confirmed_at')
                ->exists();

            if ($existing) {
                return response()->json([
                    'code' => 500,
                    'message' => 'Patient is already queued'
                ]);
            }

            DB::table('csmc_notifier.queue_referrals')->insert([
                'chief_complaint' => $request->chief_complaint,
                'diagnosis' => $request->diagnosis,
                'referring_hospital' => $request->referring_hospital,
                'patient' => $request->patient,
                'age' => $request->age,
                'gender' => $request->gender,
                'department' => $request->department,
                'queued_by' => $request->userid,
                'remarks' => $request->remarks,
                'created_at' => Carbon::now()
            ]);

            return ['code' => 200, 'message' => 'Queued successfully'];
        } catch (\Exception $exception) {
            $this->insert_error(99999, $exception->getMessage());
            return ['code' => 500, 'message' => $exception];
        }
    }

    public function queueList(Request $request)
    {
        try {
            $patient = trim($request->patient ?? '');
            $department = trim($request->department ?? '');

            $list = DB::table('csmc_notifier.queue_referrals')
                ->whereNull('confirmed_at')
                ->where(function ($query) use ($department, $patient) {
                    if ($patient != "") {
                        $query->where("patient", "LIKE", "%" . $patient . "%");
                    }

                    if ($department != "") {
                        if (strtoupper($department) === 'EMERGENCY ROOM') {
                            $query->where(function ($subQuery) {
                                $subQuery->where('department', 'LIKE', 'ER%')
                                    ->orWhere('department', 'LIKE', '%EMERGENCY ROOM%');
                            });
                        } else {
                            $query->where('department', '=', $department);
                        }
                    }
                })
                ->orderBy('created_at', 'asc')
                ->get(['id', 'referring_hospital', 'patient', 'age', 'gender', 'department', 'remarks', 'created_at']);
            return response()->json(['code' => 200, 'results' => $list]);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }


  public function sendPushNotificationPayload(Request $request)
    {
        try {
            $request->validate([
                'topic' => 'required|string',
                'hospital_referrer' => 'required|string',
                'patient' => 'required|string',
                'age' => 'required|integer',
                'sex' => 'required|string'
            ]);

            $bearer_token = 'Authorization: Bearer ' . $this->generateBearerToken();
            $payload = [
                "message" => [
                    "topic" => $request->topic,
                    "notification" => [
                        "title" => "New Referral — " . $request->hospital_referrer,
                        "body"  => $request->patient . ", " . $request->age . " y/o " . $request->sex,
                    ],
                    "data" => [
                        "hospital_referrer" => $request->hospital_referrer,
                        "patient"           => $request->patient,
                        "age"               => (string) $request->age,
                        "sex"               => $request->sex,
                    ],
                ]
            ];

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://fcm.googleapis.com/v1/projects/notifier-5e4e8/messages:send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => array(
                    $bearer_token,
                    'Content-Type: application/json'
                ),
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0
            ));

            $response = curl_exec($curl);
            if ($response === false) {
                $error = curl_error($curl);
                curl_close($curl);
                return response()->json(['error' => $error], 500);
            }

            curl_close($curl);
            return response()->json($response);
        } catch (\Exception $exception) {
            $this->insert_error(99999, "PUSH NOTIF=>" . $exception->getMessage());
            return response()->json($exception->getMessage(), 500);
        }
    }
}
