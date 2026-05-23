<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Models\Department;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if (!Auth::check()) return view('login');

    if (session('code') == "TRIAGGE")
        return redirect()->route('triagge');

    return redirect()->route('referrals');

})->name('login');

Route::get('/logout', function () {
    Auth::logout();
    Session::flush();
    return redirect()->route('login');
})->name('logout');

Route::get('/download', function () {
    $path = public_path('apk/notifier_apk_1.3.apk');
    return response()->download($path, 'notifier_apk_1.3.apk', [
        'Content-Type' => 'application/vnd.android.package-archive',
    ]);
})->name('download');

Route::post('/authenticate', [\App\Http\Controllers\UserController::class, 'authenticate'])->name('authenticate');

Route::middleware('auth')->group(function () {
    Route::get('/triagge', function () {
        if (session('code') != 'TRIAGGE') return redirect()->route('login');
        return view('triagge');
    })->name('triagge');

    Route::get('/track_referrals', function () {
        return view('track_referrals');
    })->name('track_referrals');

    Route::get('/referrals', function () {
        if (session('code') == 'TRIAGGE') return redirect()->route('login');
        return view('referrals');
    })->name('referrals');

    Route::get('/referral_queue', function () {
        return view('referral_queue');
    })->name('referral_queue');

    Route::get('/manual_referral', function () {
        // if (session('code') == 'TRIAGGE') return redirect()->route('login');
        $departments = Department::orderBy('department')->get(['code', 'department']);
        return view('manual_referral', compact('departments'));
    })->name('manual_referral');

    Route::get('/all_referrals', function () {
        if (session('code') !== 'TRIAGGE') return redirect()->route('login');
        return view('all_referrals');
    })->name('all_referrals');

    Route::get('/returned_referrals', function () {
        if (session('code') !== 'TRIAGGE') return redirect()->route('login');
        return view('returned_referrals');
    })->name('returned_referrals');

    Route::get('/get_referrals', [\App\Http\Controllers\ReferralController::class, 'getReferrals'])->name('getReferrals');
    Route::get('/get_all_referrals', [\App\Http\Controllers\ReferralController::class, 'getAllReferrals'])->name('getAllReferrals');
    Route::get('/get_triagge_referrals', [\App\Http\Controllers\ReferralController::class, 'getTriaggeReferrals'])->name('getTriaggeReferrals');
    Route::get('/get_returned_referrals', [\App\Http\Controllers\ReferralController::class, 'getReturnedReferrals'])->name('getReturnedReferrals');
    Route::get('/get_queue', [\App\Http\Controllers\QueueController::class, 'index'])->name('getQueue');
    Route::get('/load_logs', [\App\Http\Controllers\ReferralController::class, 'loadLogs'])->name('loadLogs');
    
    Route::post('/insert_queue', [\App\Http\Controllers\QueueController::class, 'store'])->name('insertQueue');
    Route::post('/confirm_queue', [\App\Http\Controllers\QueueController::class, 'confirm'])->name('confirmQueue');
    Route::delete('/delete_queue', [\App\Http\Controllers\QueueController::class, 'deleteQueue'])->name('deleteQueue');
    Route::get('/queue_archives', function () { return view('queue_archives'); })->name('queue_archives');
    Route::get('/get_queue_archives', [\App\Http\Controllers\QueueController::class, 'archives'])->name('getQueueArchives');
    Route::get('/get_departments', [\App\Http\Controllers\DepartmentController::class, 'get_departments'])->name('getDepartments');

    Route::post('/insert_referral', [\App\Http\Controllers\ReferralController::class, 'insertReferral'])->name('insertReferral');
    Route::post('/manual_referral', [\App\Http\Controllers\ReferralController::class, 'storeManualReferral'])->name('storeManualReferral');
    Route::post('/forward_referral', [\App\Http\Controllers\ReferralController::class, 'forwardReferral'])->name('forwardReferral');
    Route::post('/tag_as_done', [\App\Http\Controllers\ReferralController::class, 'tagAsDone'])->name('tagAsDone');
    Route::post('/tag_as_done_triagge', [\App\Http\Controllers\ReferralController::class, 'tagAsDoneTriagge'])->name('tagAsDoneTriagge');
    Route::post('/password/update', [\App\Http\Controllers\UserController::class, 'changePassword'])->name('changePassword');
});
