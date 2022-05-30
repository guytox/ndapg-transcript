<?php

use App\Http\Controllers\PaymentHandleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Applicant\PaymentController as ApplicantPaymentController;

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
    return redirect()->route('login');
});

Auth::routes(['verify' => true]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->middleware('verified')->name('home');


Route::prefix('applicant')->middleware('auth')->group(function (){
    Route::get('/application-fee', [ApplicantPaymentController::class, 'applicationFee'])->name('application.fee');
});


/**
 * payment routes
 */
Route::get('complete-application-payment', function () {
    return view('payments.application_payment');
})->name('pay.application.now');

Route::get('api/paya', [PaymentHandleController::class, 'confirmApplicationPayment']);
