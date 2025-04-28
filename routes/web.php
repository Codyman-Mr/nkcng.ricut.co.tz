<?php

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Reports;
use App\Http\Controllers\TestingController;
use App\Http\Controllers\LoanPackageController;
use App\Http\Controllers\GpsController;
use App\Models\Loan;
use Illuminate\Http\Request;
use App\Models\DeviceLocation;
use App\Events\LocationUpdated;
use App\Http\Controllers\LocationController;

Route::get('/', [AuthController::class, 'dashboard'])->middleware('auth');

Route::post('/send-reminder', [Controller::class, 'sendMessage'])->middleware('auth');

Route::get('/registration', [AuthController::class, 'registrationPage'])->middleware('guest');
Route::post('/user-registration', [AuthController::class, 'registration'])->name('create-user')->middleware('guest');
Route::get('/otp-verification', [AuthController::class, 'verificationPage'])->name('otp.verification')->middleware('guest');
Route::post('/otp-verify/{user}', [AuthController::class, 'verifyOtp'])->name('verify.otp')->middleware('guest');
Route::get('/welcome-page', [AuthController::class, 'welcomePage'])->name('welcome.page')->middleware('auth');
Route::get('/login', [AuthController::class, 'loginPage'])->name('login-page')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login')->middleware('guest');

Route::post('/logout', [AuthController::class, 'logout'])->name('user-logout')->middleware('auth');

Route::post('/approve-loan/{loan}', [LoanController::class, 'approveLoan'])->name('loan-approve')->middleware('auth');
Route::post('/store-loan-application/{user}', [LoanController::class, 'store'])->name('store-loan-application')->middleware('auth');
Route::get('/loan-application/{user?}', [LoanController::class, 'create'])->name('loan-application')->middleware('auth');
Route::get('/loans-pending', [LoanController::class, 'pendingLoans'])->name('loans-pending')->middleware('auth');
Route::get('/loans-ongoing', [LoanController::class, 'ongoingLoans'])->name('loans-ongoing')->middleware('auth');
Route::get('/show-loan/{loan}', [LoanController::class, 'show'])->name('show-loan')->middleware('auth');



Route::get('loan-packages', [LoanPackageController::class, 'index'])->name('loan-packages')->middleware('auth');
Route::get('/loan-payments/{loan}', [PaymentController::class, 'index'])->name('loan-payments')->middleware('auth');
Route::get('/repayment-alerts', [PaymentController::class, 'repaymentAlerts'])->name('repayment-alerts')->middleware('auth');
Route::post('/send-repayment-reminders', [PaymentController::class, 'sendRepaymentReminders'])->middleware('auth');
Route::post('/store-payment/{loan}', [PaymentController::class, 'store'])->name('store-payment')->middleware('auth');
Route::delete('/delete-payment/{payment}', [PaymentController::class, 'destroy'])->name('delete-payment')->middleware('auth');
Route::get('/filter', [PaymentController::class, 'filter']);
Route::get('/report', [Reports::class, 'index'])->name('report')->middleware('auth');



Route::get('/users', [UserController::class, 'index'])->name('users')->middleware('auth');
Route::get('/show-user/{user}',[UserController::class,'show'])->name('show-user')->middleware('auth');
Route::post('/store-user', [UserController::class, 'store'])->middleware('auth');
Route::put('/update-user/{user}', [UserController::class, 'update'])->name('update-user')->middleware('auth');
Route::delete('/delete-user/{user}', [UserController::class , 'destroy'])->name('delete-user')->middleware('auth');


Route::get('/testing', [TestingController::class,'index'])->name('testing')->middleware('auth');

