<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QueueController extends Controller
{
    private $tableName = 'csmc_notifier.queue_referrals';

    public function index(Request $request)
    {
        try {
            $department = strtoupper(trim($request->search_department ?? ''));

            if (empty($department)) {
                return response()->json(['results' => []]);
            }

            $list = DB::table($this->tableName)
                ->whereNull('confirmed_at')
                ->where('department', $department)
                ->orderBy('created_at', 'asc')
                ->select([
                    'id', 'referring_hospital', 'patient', 'age', 'gender',
                    'department', 'diagnosis', 'chief_complaint', 'remarks', 'created_at',
                ])
                ->get()
                ->values()
                ->map(function ($item, $index) {
                    $item->queue_position = $index + 1;
                    return $item;
                });

            return response()->json(['results' => $list]);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        if (session('code') !== 'TRIAGGE') {
            return response()->json(['code' => 403, 'message' => 'Access denied'], 403);
        }

        try {
            $request->validate([
                'referring_hospital' => 'required|string|max:255',
                'patient'            => 'required|string|max:255',
                'age'                => 'nullable|max:50',
                'gender'             => 'nullable|string|max:50',
                'department'         => 'nullable|string|max:255',
                'diagnosis'          => 'nullable|string',
                'chief_complaint'    => 'nullable|string',
                'remarks'            => 'nullable|string|max:255',
            ]);

            $referringHospital = strtoupper(trim($request->referring_hospital));
            $patient           = strtoupper(trim($request->patient));
            $department        = $request->department ? strtoupper(trim($request->department)) : null;
            $gender            = $request->gender    ? strtoupper(trim($request->gender))     : null;
            $age               = ($request->age !== null && $request->age !== '') ? trim((string) $request->age) : null;
            $remarks           = $request->remarks         ? trim($request->remarks)         : null;
            $diagnosis         = $request->diagnosis       ? trim($request->diagnosis)       : null;
            $chief_complaint   = $request->chief_complaint ? trim($request->chief_complaint) : null;

            $existing = DB::table($this->tableName)
                ->where('referring_hospital', $referringHospital)
                ->where('patient', $patient)
                ->whereNull('confirmed_at')
                ->exists();

            if ($existing) {
                return response()->json([
                    'code' => 500,
                    'message' => 'Patient is already queued'
                ]);
            }

            DB::table($this->tableName)->insert([
                'referring_hospital' => $referringHospital,
                'patient' => $patient,
                'age' => $age,
                'gender' => $gender,
                'department' => $department,
                'queued_by' => Auth::id(),
                'remarks' => $remarks,
                'diagnosis' => $diagnosis,
                'chief_complaint' => $chief_complaint,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            return response()->json(['code' => 200, 'message' => 'Patient queued successfully']);
        } catch (\Exception $exception) {
            return response()->json(['code' => 500, 'message' => $exception->getMessage()], 500);
        }
    }

    public function deleteQueue(Request $request)
    {
        if (!$request->expectsJson() && session('code') !== 'TRIAGGE') {
            return response()->json(['code' => 403, 'message' => 'Access denied'], 403);
        }

        try {
            DB::table($this->tableName)
                ->where('id', '=', $request->id)
                ->delete();

            return response()->json(['message' => 'Queue deleted']);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    public function archives(Request $request)
    {
        try {
            $patient = trim($request->search_patient ?? '');
            $department = trim($request->search_department ?? '');
            $page = max((int) $request->page, 0);

            $list = DB::table($this->tableName)
                ->whereNotNull('confirmed_at')
                ->where(function ($query) use ($department, $patient) {
                    if ($patient != "") {
                        $query->where("patient", "LIKE", "%" . $patient . "%");
                    }
                    if ($department != "") {
                        $query->where('department', '=', $department);
                    }
                })
                ->orderBy('confirmed_at', 'desc')
                ->skip($page * 10)
                ->take(11)
                ->get(['id', 'referring_hospital', 'patient', 'age', 'gender', 'department', 'remarks', 'created_at', 'confirmed_at']);

            return response()->json(['results' => $list]);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    public function confirm(Request $request)
    {
        if (!$request->expectsJson() && session('code') !== 'TRIAGGE') {
            return response()->json(['code' => 403, 'message' => 'Access denied'], 403);
        }

        try {
            $record = DB::table($this->tableName)->where('id', $request->id)->first();

            if (!$record) {
                return response()->json(['code' => 404, 'message' => 'Record not found'], 404);
            }

            $confirmedAt = Carbon::now();
            $queuedAt = Carbon::parse($record->created_at);
            $minutes = $queuedAt->diffInMinutes($confirmedAt);
            $seconds = $queuedAt->diffInSeconds($confirmedAt) % 60;

            DB::table($this->tableName)
                ->where('id', $request->id)
                ->update(['confirmed_at' => $confirmedAt, 'updated_at' => $confirmedAt]);

            return response()->json([
                'code' => 200,
                'message' => 'Patient confirmed',
                'turnaround_minutes' => $minutes,
                'turnaround_seconds' => $seconds,
                'patient' => $record->patient,
            ]);
        } catch (\Exception $exception) {
            return response()->json(['code' => 500, 'message' => $exception->getMessage()], 500);
        }
    }
}
