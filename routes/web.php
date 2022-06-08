<?php

use App\Http\Controllers\PaymentHandleController;
use Illuminate\Support\Facades\Auth;
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

Auth::routes([
    'verify' => true,
    'register' => true,
    'login' => true
]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->middleware('verified')->name('home');


Route::get('/applicant/application-fee', [ApplicantPaymentController::class, 'applicationFee'])->name('application.fee');

Route::prefix('applicant')->middleware(['auth', 'role:applicant', 'application_fee.confirm'])->group(function () {
    Route::prefix('profile')->group(function () {
        Route::get('/contact-details', [\App\Http\Controllers\Applicant\ProfileController::class, 'contactDetails'])->name('applicant.profile.contact_details');
        Route::get('/personal-details', [\App\Http\Controllers\Applicant\ProfileController::class, 'personalDetails'])->name('applicant.profile.personal_details');
        Route::post('/store', [\App\Http\Controllers\Applicant\ProfileController::class, 'storeUserProfile'])->name('applicant.profile.store');
    });

    Route::prefix('qualifications')->group(function () {
        Route::get('/school', [\App\Http\Controllers\Applicant\QualificationsController::class, 'school'])->name('applicant.qualifications.school');
        Route::get('/professional', [\App\Http\Controllers\Applicant\QualificationsController::class, 'professional'])->name('applicant.qualifications.professional');
        Route::post('/store', [\App\Http\Controllers\Applicant\QualificationsController::class, 'store'])->name('applicant.qualifications.store');
    });
});


/**
 * payment routes
 */
Route::get('complete-application-payment', function () {
    return view('payments.application_payment');
})->name('pay.application.now');

Route::get('api/paya', [PaymentHandleController::class, 'confirmApplicationPayment']);
