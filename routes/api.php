<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|get_departments
*/


Route::post('/insert_referral', [\App\Http\Controllers\ReferralController::class, 'insert_referral']);
Route::post('/insert_referral_5pm', [\App\Http\Controllers\ReferralController::class, 'insert_referral_5pm']);
Route::post('/login', [\App\Http\Controllers\ReferralController::class, 'login']);
Route::post('/forward_referral', [\App\Http\Controllers\ReferralController::class, 'forward_referral']);
Route::post('/tag_as_done', [\App\Http\Controllers\ReferralController::class, 'tag_as_done']);
Route::put('/change_password', [\App\Http\Controllers\ReferralController::class, 'change_password']);
Route::get('/get_referrals', [\App\Http\Controllers\ReferralController::class, 'get_referrals']);
Route::get('/get_departments', [\App\Http\Controllers\DepartmentController::class, 'get_departments']);
Route::get('/load_summary', [\App\Http\Controllers\ReferralController::class, 'load_summary']);
Route::get('/load_logs', [\App\Http\Controllers\ReferralController::class, 'load_logs']);

Route::get('/queue_list', [\App\Http\Controllers\ReferralController::class, 'queueList']);
Route::post('/queue_referral', [\App\Http\Controllers\ReferralController::class, 'store']);
Route::post('/send_push_notification', [\App\Http\Controllers\ReferralController::class, 'sendPushNotification']);
Route::get('/get_all_referrals', [\App\Http\Controllers\ReferralController::class, 'getAllReferrals']);

// ─── TEST ROUTES (remove before deploying to production) ───────────────────

// Test with custom params via query string:
// GET /api/test/push-notification?topic=referrals_TRIAGGE&hospital_referrer=Test+Hospital&patient=Juan+Dela+Cruz&age=35&sex=Male
Route::get('/test/push-notification', [\App\Http\Controllers\ReferralController::class, 'sendPushNotification']);

// Test with hardcoded sample data — hit this URL directly to fire a notification
Route::get('/test/push-notification/sample', function (\Illuminate\Http\Request $request) {
    $request->merge([
        'topic' => 'referrals_' . $request->topic,
        'hospital_referrer' => 'Sample Referring Hospital',
        'patient' => 'Juan Dela Cruz',
        'age' => 35,
        'sex' => 'Male',
    ]);
    return (new \App\Http\Controllers\ReferralController())->sendPushNotificationPayload($request);
});

// ───────────────────────────────────────────────────────────────────────────


