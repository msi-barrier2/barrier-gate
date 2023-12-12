<?php

use App\Http\Controllers\ApiTimbanganController;
use App\Http\Controllers\BarierRealTimeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ResetPassword;
use App\Http\Controllers\ChangePassword;
use App\Http\Controllers\FullRealController;
use App\Http\Controllers\LogBarierGateController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;

Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.perform');
Route::get('/reset-password', [ResetPassword::class, 'show'])->middleware('guest')->name('reset-password');
Route::post('/reset-password', [ResetPassword::class, 'send'])->middleware('guest')->name('reset.perform');
Route::get('/change-password', [ChangePassword::class, 'show'])->middleware('guest')->name('change-password');
Route::post('/change-password', [ChangePassword::class, 'update'])->middleware('guest')->name('change.perform');

Route::post('/api_msi/registrasi', [BarierRealTimeController::class, 'registrasi'])->middleware('guest');
Route::post('/api_msi/delete', [BarierRealTimeController::class, 'destroy'])->name('delete.bg');
Route::post('/api_msi', [BarierRealTimeController::class, 'timbangan'])->middleware('guest');

Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::group(['middleware' => ['auth', 'prevent-back-history']], function () {
	Route::get('/', [FullRealController::class, 'index']);
	Route::get('/home', [HomeController::class, 'index']);
	Route::get('/log', [LogBarierGateController::class, 'index'])->name('log.index');
	Route::get('/report', [ReportController::class, 'index'])->name('report.index');
	Route::get('/full_page/{parameter?}', [FullRealController::class, 'index'])->name('full_page');
	Route::get('/full_table', [FullRealController::class, 'index'])->name('full_table');
	Route::post('/get_bearier1', [ApiTimbanganController::class, 'getBearier1'])->name('get_bearier1');
	Route::post('/register', [RegisterController::class, 'store'])->name('register.perform');
	Route::post('/ajax_get_ts', [FullRealController::class, 'ajax_get_ts'])->name('get.ts');
	Route::post('/ajax_get_sts', [FullRealController::class, 'ajax_get_sts'])->name('get.sts');
	Route::resource('users', UserController::class);
	Route::get('/log_user/{parameter?}', [UserController::class, 'log'])->name('users.log');
});
