<?php

use Illuminate\Support\Facades\Route;

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
    if (Auth::check()) return redirect()->route('opcen');
    return view('login');
})->name('login');

Route::get('/logout', function () {
    Auth::logout();
    Session::flush();
    return redirect()->route('login');
})->name('logout');

Route::post('/authenticate', [\App\Http\Controllers\UserController::class, 'authenticate'])->name('authenticate');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/opcen', function () {
        return view('opcen');
    })->name('opcen');

    Route::get('/department', function () {
        return view('department');
    })->name('department');

    Route::get('/referrals', function () {
        return view('referrals');
    })->name('referrals');

    Route::get('get_referrals',[\App\Http\Controllers\ReferralController::class,'getReferrals'])->name('getReferrals');
    Route::get('get_all_referrals',[\App\Http\Controllers\ReferralController::class,'getAllReferrals'])->name('getAllReferrals');
    Route::get('load_logs',[\App\Http\Controllers\ReferralController::class,'loadLogs'])->name('loadLogs');

    Route::post('insert_referral',[\App\Http\Controllers\ReferralController::class,'insertReferral'])->name('insertReferral');
    Route::post('forward_referral',[\App\Http\Controllers\ReferralController::class,'forwardReferral'])->name('forwardReferral');
    Route::post('tag_as_done',[\App\Http\Controllers\ReferralController::class,'tagAsDone'])->name('tagAsDone');


});
