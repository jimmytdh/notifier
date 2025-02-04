<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Kreait\Firebase\Auth;

class QueueController extends Controller
{
    public function index(Request $request)
    {
        try {
            $patient = $request->search_patient;
            $department = $request->search_department;
            $list = DB::table('csmc_notifier.queue_referrals')
                ->where(function ($query) use ($department, $patient) {
                    if ($patient != "") {
                        $query->where("patient", "LIKE", "%" . $patient . "%");
                    }

                    if ($department != "") {
                        $query->where('department', '=', $department);
                    }
                })
                ->get(['id', 'referring_hospital', 'patient', 'age', 'gender', 'department','remarks', 'created_at']);
            return response()->json(['results' => $list]);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    public function delete(Request $request)
    {
        try {
            DB::table('csmc_notifier.queue_referrals')
                ->where('id', '=', $request->id)
                ->delete();

            return response()->json(['message' => 'Queue deleted']);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }
}
