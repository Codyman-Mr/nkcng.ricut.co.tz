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
use PhpParser\Node\Expr\Assign;
use App\Http\Controllers\GpsDeviceController;
use App\Http\Controllers\InstallationController;
use App\Livewire\PaymentHistoryComponent;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Log;

Route::get('/test-error', function () {
    try {
        // Trigger some error
        $data = 1 / 0;

        return 'Success!';
    } catch (\Throwable $e) {
        Log::error('❌ Caught Error: ' . $e->getMessage());
        return response()->view('errors.custom', [], 500); // You can return a custom view
    }
});

Route::post('/loan-submit', [AuthController::class, 'submitLoan'])->name('loan.submit');

Route::get('/', [AuthController::class, 'dashboard'])->middleware('auth');
use App\Http\Controllers\Admin\PasswordController;

Route::middleware(['auth'])->group(function () {
    Route::get('/change-password', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('/change-password', [PasswordController::class, 'update'])->name('password.update');
});

Route::get('/debug-error', function () {
    return nl2br(file_get_contents(storage_path('logs/laravel.log')));
});
Route::get('/test-log', function () {
    Log::error('Test error logged at ' . now());
    return 'Error logged!';
});

Route::get('/debug-error', function () {
    try {
        // Jaribu chama ambalo linasababisha error, kama DB example:
        auth()->user(); // Kama haufanyi login, hii inakosa
        return 'No error detected';
    } catch (\Throwable $e) {
        return '<pre style="white-space: pre-wrap;">' . $e->__toString() . '</pre>';
    }
});


Route::post('/send-reminder', [Controller::class, 'sendMessage'])->middleware('auth');

Route::get('/registration', [AuthController::class, 'registrationPage'])->middleware('guest');
Route::post('/user-registration', [AuthController::class, 'registration'])->name('create-user')->middleware('guest');
Route::get('/otp-verification', [AuthController::class, 'verificationPage'])->name('otp.verification')->middleware('guest');
Route::post('/otp-verify/{user}', [AuthController::class, 'verifyOtp'])->name('verify.otp')->middleware('guest');
Route::get('/welcome-page', [AuthController::class, 'welcomePage'])->name('welcome.page')->middleware('auth');
Route::get('/login', [AuthController::class, 'loginPage'])->name('login-page')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login')->middleware('guest');

Route::post('/logout', [AuthController::class, 'logout'])->name('user-logout')->middleware('auth');
Route::get('/repayments/upcoming', [LoanController::class, 'showUpcomingRepayments']);

use App\Jobs\SendSmsJob;

Route::get('/test-sms', function () {
    SendSmsJob::dispatch(['+255712345678'], 'Test SMS from queue', 999);
    return 'Test SMS Job dispatched!';
});


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
Route::get('/show-user/{user}', [UserController::class, 'show'])->name('show-user')->middleware('auth');
Route::post('/store-user', [UserController::class, 'store'])->middleware('auth');
Route::put('/update-user/{user}', [UserController::class, 'update'])->name('update-user')->middleware('auth');
Route::delete('/delete-user/{user}', [UserController::class, 'destroy'])->name('delete-user')->middleware('auth');


Route::get('/testing', [TestingController::class, 'index'])->name('testing')->middleware('auth');

Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store')->middleware('auth');
;

Route::get('/assign-gps-device', [GpsDeviceController::class, 'assignGpsDevice'])
    ->name('assign-gps-device')
    ->middleware('auth');

Route::get('/gps-devices', [GpsDeviceController::class, 'index'])
    ->name('gps-devices')
    ->middleware('auth');

Route::get('/show-gps-device/{gpsDevice}', [GpsDeviceController::class, 'show'])->name('gps-device.show');

Route::get('/installations', [InstallationController::class, 'index'])->name('installations')->middleware('auth');

Route::get('/approve-installation/{installationId}', [InstallationController::class, 'approveInstallation'])->name('approve-installation')->middleware('auth');

Route::post('/approve-installation/{installationId}', [InstallationController::class, 'updateInstallation'])->name('approve-installation.update')->middleware('auth');





Route::post('/payment/initiate', [PaymentController::class, 'initiatePayment'])->name('payment.initiate');
Route::post('/payment/callback', [PaymentController::class, 'handleCallback'])->name('payment.callback');

Route::get('/payment-history/{loan}', [PaymentController::class, 'paymentHistory'])
    ->name('payment-history')
    ->middleware('auth');
