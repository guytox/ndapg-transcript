<?php

use App\Http\Controllers\AcademicSessionsController;
use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\PaymentHandleController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Applicant\PaymentController as ApplicantPaymentController;
use App\Http\Controllers\curriculaController;
use App\Http\Controllers\CurriculaItemsController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\GradingSystemController;
use App\Http\Controllers\LecturerGradingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgrammeController;
use App\Http\Controllers\RegistrationApprovalController;
use App\Http\Controllers\RoleManagementController;
use App\Http\Controllers\SemesterCourseAllocationController;
use App\Http\Controllers\SemesterCoursesController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\Student\StudentRegistrationController;
use App\Http\Controllers\StudentInformationController;
use App\Http\Controllers\StudyLevelsController;
use App\Jobs\RegistrationApprovalJob;
use App\Models\Faculty;

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

Route::get('profile', [ProfileController::class, 'index'])->middleware('auth')->name('user.profile');
Route::post('profile', [ProfileController::class, 'updateProfile'])->middleware('auth')->name('user.profile');



Route::get('/applicant/application-fee', [ApplicantPaymentController::class, 'applicationFee'])->name('application.fee');

Route::prefix('admin')->middleware(['role:admin','auth', 'profile_completed', 'verified'])->group(function(){

    Route::prefix('appointments')->group(function(){
        Route::get('/getdeans', '\App\Http\Controllers\FacultyController@getDeans')->name('appointments.get.deans');
        Route::post('/revokedeans/{faultyid}', '\App\Http\Controllers\FacultyController@revokeDeans')->name('appointments.revoke.deans');
        Route::post('/assigndeans/{faultyid}', '\App\Http\Controllers\FacultyController@assignDean')->name('appointments.assign.deans');
        Route::get('/gethods', '\App\Http\Controllers\DepartmentController@getHods')->name('appointments.get.hods');
        Route::post('/revokehods/{departmentyid}', '\App\Http\Controllers\DepartmentController@revokeHods')->name('appointments.revoke.hod');
        Route::post('/assignhods/{departmentid}', '\App\Http\Controllers\DepartmentController@assignHod')->name('appointments.assign.hods');
        Route::resource('/stafflist', StaffController::class);
        Route::post('/staffListUpload', [StaffController::class, 'uploadStaffList'])->name('stafflist.upload');
        Route::get('/studentlist', [StudentInformationController::class, 'index'])->name('view.all.active');
    });

    Route::prefix('configurations')->group(function(){
        Route::resource('/faculties', FacultyController::class);
        Route::resource('/departments', DepartmentController::class);
        Route::resource('/programs', ProgrammeController::class);
        Route::resource('/studylevels', StudyLevelsController::class);
        Route::resource('/semestercourses', SemesterCoursesController::class);
        Route::post('/semcoursesupload', [SemesterCoursesController::class, 'uploadSemesterCourse'])->name('semestercourses.upload');
        Route::resource('/curricula', curriculaController::class);
        Route::resource('/curriculaitems', CurriculaItemsController::class);
        Route::resource('/rolemanagement', RoleManagementController::class);
        Route::resource('/acadsessions', AcademicSessionsController::class);
        Route::resource('/gradingsystems', GradingSystemController::class);
        Route::post('/addgradingsystemItem', [GradingSystemController::class, 'addGradingItem'])->name('add.grading.item');
        Route::post('/removegradingsystemItem', [GradingSystemController::class, 'deleteGradingItem'])->name('delete.grading.item');
        Route::post('/editgradingsystemItem', [GradingSystemController::class, 'editGradingItem'])->name('edit.grading.item');
        Route::post('/studentlistupload', [StudentInformationController::class, 'uploadStudentList'])->name('student.list.upload');
        Route::get('/studentlistupload', [StudentInformationController::class, 'uploadStudentsForm'])->name('student.upload.form');
        Route::post('/studentAdmissionlistupload', [StudentInformationController::class, 'uploadStudentAdmissionList'])->name('student.admission.upload');
        Route::get('/studentAdmissionlistupload', [StudentInformationController::class, 'uploadStudentsAdmissionForm'])->name('student.admission.form');

        //Admission Offer Upload
        Route::post('/studentAdmissionOfferupload', [AdmissionController::class, 'uploadStudentAdmissionList'])->name('student.admissionoffer.upload');
        Route::get('/studentAdmissionOfferupload', [AdmissionController::class, 'uploadStudentsAdmissionForm'])->name('student.admissionoffer.form');
    });

});

Route::prefix('RegManagement')->middleware('auth', 'role:hod|dean|reg_officer|vc|dvc|exam_officer', 'profile_completed', 'verified')->group(function(){

    Route::prefix('Approvals')->middleware('role:hod|dean|reg_officer|vc|dvc|exam_officer')->group(function(){
        Route::resource('reg', RegistrationApprovalController::class);
        Route::get('get/Approvals', [RegistrationApprovalController::class, 'showApproved'])->name('reg.approvals');
        Route::get('get/Approvals/{id}/{student_id}', [RegistrationApprovalController::class, 'showStudentConfirmedReg'])->name('show.single.student.reg');
    });

});

Route::prefix('ResultManagement')->middleware('auth', 'role:hod|dean|reg_officer|vc|dvc|exam_officer|lecturer', 'profile_completed','verified')->group(function(){

    Route::prefix('CourseGrading')->middleware('role:hod|dean|reg_officer|vc|dvc|exam_officer|lecturer')->group(function(){
        //Semester Course Allcoaton Routes
        Route::resource('/course-allocation', SemesterCourseAllocationController::class);
        Route::post('/add-allocation',[SemesterCourseAllocationController::class, 'addAllocationItem'])->name('add.allocation.staff');
        Route::post('/remove-allocation/{id}',[SemesterCourseAllocationController::class, 'deleteAllocationItem'])->name('delete.allocation.staff');

        //lecturer Grading routes
        Route::get('/lecturerGrading/{as}', [LecturerGradingController::class, 'showMyCourses'])->name('lecturer.grading.home');

        Route::get('/lecturerGrading/Start/{as}/{id}', [LecturerGradingController::class, 'startGrading'])->name('lecturer.grading.start');

        Route::post('/lecturerGrading/{as}', [LecturerGradingController::class, 'showMyPreviousCourses'])->name('lecturer.grading.previous');

        Route::post('/lecturerGrading/DownloadStudents/{as}/{id}', [LecturerGradingController::class, 'downloadRegistrants'])->name('lecturer.grading.download');

        Route::post('/lecturerGrading/UploadGrades/{as}', [LecturerGradingController::class, 'uploadGrades'])->name('lecturer.grading.upload');

        Route::get('/lecturerGrading/UploadManualGrades/{as}/{id}', [LecturerGradingController::class, 'manualUploadofGrades'])->name('lecturer.grading.manualupload');

        Route::post('/lecturerGrading/UploadManualGrades/{as}', [LecturerGradingController::class, 'uploadManualGrades'])->name('lecturer.manual.upload');

        Route::post('/lecturerGrading/ConfirmGrades/{as}', [LecturerGradingController::class, 'gradeConfirmation'])->name('lecturer.grading.confirm');

        Route::post('/lecturerGrading/DeConfirmGrades/{as}', [LecturerGradingController::class, 'reverseGradeConfirmation'])->name('lecturer.grading.deconfirm');


        Route::post('/lecturerGrading/SubmitGrades/{as}', [LecturerGradingController::class, 'submitGrades'])->name('lecturer.grading.submit');

        Route::get('/lecturerGrading/hodHome/{as}', [LecturerGradingController::class, 'hodGradeHome'])->name('hod-confirm.index');

        Route::post('/lecturerGrading/hodPrevious/{as}', [LecturerGradingController::class, 'hodShowSelected'])->name('hod-confirm.previous');

        Route::post('/lecturerGrading/hodConfirmGrades/{as}', [LecturerGradingController::class, 'hodConfirmGrades'])->name('hod.grading.confirm');

        Route::post('/lecturerGrading/deanRejectGrades/{as}', [LecturerGradingController::class, 'deanDeConfirmGrades'])->name('dean.grading.confirm');


        Route::get('/lecturerScoreSheet/{as}/{id}', [LecturerGradingController::class, 'showScoreSheet'])->name('lecturer.grading.scoresheet');



    });

});

Route::prefix('applicant')->middleware(['auth', 'role:applicant', 'application_fee.confirm','verified'])->group(function () {
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

    Route::prefix('referees')->group(function() {
        Route::get('add-referee', [\App\Http\Controllers\Applicant\RefereeController::class, 'addReferee'])->name('applicant.referee');
        Route::post('add-referee', [\App\Http\Controllers\Applicant\RefereeController::class, 'storeReferee'])->name('applicant.referee.store');
    });

    Route::prefix('academics')->group(function() {
        Route::get('add-card', [\App\Http\Controllers\Applicant\AcademicController::class, 'addCardView'])->name('applicant.add_card')->middleware('application_submitted_result');
        Route::post('add-card', [\App\Http\Controllers\Applicant\AcademicController::class, 'addCardStore'])->name('applicant.add_card.store')->middleware('application_submitted_result');
        Route::get('add-result', [\App\Http\Controllers\Applicant\AcademicController::class, 'addResultView'])->name('applicant.add_result');
        Route::get('view-result', [\App\Http\Controllers\Applicant\AcademicController::class, 'viewResultSubmitted'])->name('applicant.view_result');
        Route::post('add-result', [\App\Http\Controllers\Applicant\AcademicController::class, 'addResultStore'])->name('applicant.add_result.store');
    });
});

Route::prefix('student')->middleware(['auth', 'role:student', 'coursereg_clearance.confirm', 'profile_completed','verified'])->group(function () {

    Route::prefix('outstanding')->group(function () {
        Route::get('/school', [\App\Http\Controllers\Applicant\QualificationsController::class, 'school'])->name('applicant.qualifications.school');
        Route::get('/professional', [\App\Http\Controllers\Applicant\QualificationsController::class, 'professional'])->name('applicant.qualifications.professional');
        Route::post('/payments', [\App\Http\Controllers\Applicant\QualificationsController::class, 'store'])->name('student.outstanding.payments');
    });

    Route::prefix('registration')->group(function () {
        Route::resource('/coursereg', StudentRegistrationController::class);
        Route::get('/viewMyRegistrations/{id}',[StudentRegistrationController::class, 'showPrevious'])->name('student.registration.viewAll');
        Route::get('/viewSingleRegistration/{id}',[StudentRegistrationController::class, 'showSingleReg'])->name('student.registration.viewSingle');
        Route::get('/showSubmitedRegistration/{id}',[StudentRegistrationController::class, 'showConfirmedReg'])->name('student.registration.viewMyConfirmed');
        Route::get('/printExamCard/{id}',[StudentRegistrationController::class, 'printExamCard'])->name('student.registration.printexamcard');
        Route::get('/deRegisterCourse/{id}',[StudentRegistrationController::class, 'deRegisterCourse'])->name('student.registration.removecourse');
        Route::get('/submitRegistration/{id}',[StudentRegistrationController::class, 'submitCourseReg'])->name('student.registration.submit');
        Route::get('/checkoutline', [\App\Http\Controllers\Applicant\QualificationsController::class, 'school'])->name('student.registration.outlinecheck');

    });


});


//Routes for external Staff for Pay Processing etc

Route::prefix('PayProcessor')->middleware(['auth', 'role:pay_processor|admin|bursary', 'verified'])->group(function () {

    Route::prefix('UploadPayments')->group(function () {

        Route::view('/studentpaymentsupload','admin.configs.import-student-payments')->name('student.paymentupload.form');
        Route::post('/studentpaymentupload', [StudentInformationController::class, 'uploadStudentPayments'])->name('student.payment.upload');
        Route::view('/applicantpaycodesearch','bursary.search-applicant-paycode')->name('applicant.paycode.form');
        Route::post('/applicantpaycodesearch', [AdmissionController::class, 'selecPayCodeApplicant'])->name('select.paycode.upload');
        Route::post('/applicantpaycodeconfirm', [AdmissionController::class, 'activateStudentAccount'])->name('activate.student.account');

    });

});




Route::get('/Confirmation/CourseReg/{$id}', [StudentRegistrationController::class, 'verifyReg'])->middleware('auth', 'role:staff')->name('verify.student.reg');


Route::get('applicant-referee/{uid}', [\App\Http\Controllers\Applicant\RefereeController::class, 'viewApplicantReferred'])->name('referee.view_details');
Route::post('applicant-referee/{uid}', [\App\Http\Controllers\Applicant\RefereeController::class, 'updateRefereeDetails'])->name('referee.update_details');


/**
 * payment routes
 */
Route::get('complete-application-payment', function () {
    return view('payments.application_payment');
})->name('pay.application.now');

Route::get('api/paya', [PaymentHandleController::class, 'confirmApplicationPayment']);

