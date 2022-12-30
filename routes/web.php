<?php

use App\Http\Controllers\AcademicSessionsController;
use App\Http\Controllers\Admin\AdminReportsController;
use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\AdmissionProcessingController;
use App\Http\Controllers\PaymentHandleController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Applicant\PaymentController as ApplicantPaymentController;
use App\Http\Controllers\BillingController;
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
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

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
    return view('index');
});

Auth::routes([
    'verify' => true,
    'register' => true,
    'login' => true
]);

//Password Reset Routes
Route::get('/forgot-password', function () {
    return view('auth.passwords.email');
})->middleware('web')->name('password.request');

Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink(
        $request->only('email')
    );

    return $status === Password::RESET_LINK_SENT
                ? back()->with(['status' => __($status)])
                : back()->withErrors(['email' => __($status)]);
})->middleware('web')->name('password.email');

Route::get('/reset-password/{token}', function ($token) {
    return view('auth.passwords.reset', ['token' => $token]);
})->middleware('web')->name('password.reset');

Route::post('/reset-password', function (Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->setRememberToken(Str::random(60));

            $user->save();

            event(new PasswordReset($user));
        }
    );

    return $status === Password::PASSWORD_RESET
                ? redirect()->route('login')->with('status', __($status))
                : back()->withErrors(['email' => [__($status)]]);
})->middleware('web')->name('password.update');




//Home Routes

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->middleware('verified')->name('home');

Route::get('profile', [ProfileController::class, 'index'])->middleware('auth')->name('user.profile');
Route::post('profile', [ProfileController::class, 'updateProfile'])->middleware('auth')->name('user.profile');



Route::get('/applicant/application-fee', [ApplicantPaymentController::class, 'applicationFee'])->name('application.fee');

Route::prefix('admin')->middleware(['role:admin|dean|hod|reg_officer|exam_officer|ict_support','auth', 'profile_completed', 'verified'])->group(function(){

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

        Route::view('/update-userpass', 'admin.update-password')->name('update-userpass');
        Route::post('/userpass-update', [AdminReportsController::class, 'adminPasswordUpdate'])->name('userpass-update');

        Route::view('/update-matric', 'admin.update-matric')->name('update-matric');
        Route::post('/matric-update', [AdminReportsController::class, 'updateMatricNo'])->name('matric-update');

        Route::view('/update-programme', 'admin.update-student-programme')->name('update-programme');
        Route::post('/programme-update', [AdminReportsController::class, 'changeOfProgramme'])->name('programme-update');

        Route::view('/updateUserName', 'admin.update-student-name')->name('update.username');
        Route::post('/userNameSearch', [AdminReportsController::class, 'changeOfNameSearch'])->name('username.update.search');
        Route::post('/userNameUpdate', [AdminReportsController::class, 'changeOfName'])->name('username.update');


    });

});

Route::prefix('RegManagement')->middleware('auth', 'role:hod|dean|reg_officer|vc|dvc|exam_officer|dean_pg|admin', 'profile_completed', 'verified')->group(function(){

    Route::prefix('Approvals')->middleware('role:hod|dean|reg_officer|vc|dvc|exam_officer')->group(function(){
        Route::resource('reg', RegistrationApprovalController::class);
        Route::get('get/Approvals', [RegistrationApprovalController::class, 'showApproved'])->name('reg.approvals');
        Route::get('get/Approvals/{id}/{student_id}', [RegistrationApprovalController::class, 'showStudentConfirmedReg'])->name('show.single.student.reg');

    });


    Route::prefix('curriculum')->middleware('role:dean|hod|reg_officer|exam_officer')->group(function(){
        Route::get('/Dept/viewCurriculums',[curriculaController::class, 'getMyCurricula'])->name('get.mycurricula');
        Route::get('/Dept/showCurriculum',[curriculaController::class, 'showMyCurricula'])->name('show.mycurriculum');
    });


    Route::prefix('Reports')->middleware('role:admin|dean_pg')->group(function(){

        Route::view('/RegReport', 'admin.search-registered-students')->name('search.registered.students');
        Route::post('RegReport', [RegistrationApprovalController::class, 'registeredStudentsReport'])->name('show.registered.students');

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

Route::prefix('applicant')->middleware(['auth', 'role:applicant', 'application_fee.confirm', 'verified','application.submission'])->group(function () {
    Route::prefix('profile')->group(function () {
        Route::get('/contact-details', [\App\Http\Controllers\Applicant\ProfileController::class, 'contactDetails'])->name('applicant.profile.contact_details');
        Route::get('/personal-details', [\App\Http\Controllers\Applicant\ProfileController::class, 'personalDetails'])->name('applicant.profile.personal_details');
        Route::post('/store', [\App\Http\Controllers\Applicant\ProfileController::class, 'storeUserProfile'])->name('applicant.profile.store');
        Route::get('/bio-data', [\App\Http\Controllers\Applicant\ProfileController::class, 'applicantProfile'])->name('applicants.profile.biodata');
        Route::post('/store-biodata', [\App\Http\Controllers\Applicant\ProfileController::class, 'storeApplicantBiodata'])->name('applicant.biodata.store');


    });

    Route::prefix('qualifications')->group(function () {
        Route::get('/school', [\App\Http\Controllers\Applicant\QualificationsController::class, 'school'])->name('applicant.qualifications.school');
        Route::get('/delete/{id}', [\App\Http\Controllers\Applicant\QualificationsController::class, 'deleteQualification'])->name('applicant.delete.qualification');
        Route::get('/professional', [\App\Http\Controllers\Applicant\QualificationsController::class, 'professional'])->name('applicant.qualifications.professional');
        Route::post('/store', [\App\Http\Controllers\Applicant\QualificationsController::class, 'store'])->name('applicant.qualifications.store');

        # NYSC
        Route::get('/nysc', [\App\Http\Controllers\Applicant\QualificationsController::class, 'nyscget'])->name('applicant.nysc');
        Route::post('/nysc/store', [\App\Http\Controllers\Applicant\QualificationsController::class, 'nyscStore'])->name('applicant.nysc.store');

    });

    Route::prefix('referees')->group(function() {
        Route::get('add-referee', [\App\Http\Controllers\Applicant\RefereeController::class, 'addReferee'])->name('applicant.referee');
        Route::get('delete-referee/{uid}', [\App\Http\Controllers\Applicant\RefereeController::class, 'deleteNominatedReferee'])->name('delete.referee');
        Route::post('add-referee', [\App\Http\Controllers\Applicant\RefereeController::class, 'storeReferee'])->name('applicant.referee.store');
    });

    Route::prefix('academics')->group(function() {
        Route::get('add-card', [\App\Http\Controllers\Applicant\AcademicController::class, 'addCardView'])->name('applicant.add_card')->middleware('application_submitted_result');
        Route::post('add-card', [\App\Http\Controllers\Applicant\AcademicController::class, 'addCardStore'])->name('applicant.add_card.store')->middleware('application_submitted_result');
        Route::get('add-result', [\App\Http\Controllers\Applicant\AcademicController::class, 'addResultView'])->name('applicant.add_result');
        Route::get('view-result', [\App\Http\Controllers\Applicant\AcademicController::class, 'viewResultSubmitted'])->name('applicant.view_result');
        Route::post('add-result', [\App\Http\Controllers\Applicant\AcademicController::class, 'addResultStore'])->name('applicant.add_result.store');

        Route::get('view-programme', [\App\Http\Controllers\Applicant\AcademicController::class, 'viewApprovedProgrammes'])->name('applicant.view_programme');
        Route::post('add-programme', [\App\Http\Controllers\Applicant\AcademicController::class, 'addProgrammeStore'])->name('applicant.add_programme.store');
        Route::post('add-programme', [\App\Http\Controllers\Applicant\AcademicController::class, 'addProgrammeStore'])->name('applicant.add_programme.store');

        # Research Proposal
        Route::get('/research', [\App\Http\Controllers\Applicant\QualificationsController::class, 'researchget'])->name('applicant.research');
        Route::post('/research/store', [\App\Http\Controllers\Applicant\QualificationsController::class, 'researchStore'])->name('applicant.research.store');


    });

    Route::prefix('preview')->group(function(){

        Route::get('/applicationPreview/{id}',[AdmissionProcessingController::class, 'previewApplication'])->name('preview.application');

    });


    # Submit form Here
    Route::get('/application/submit/{id}',[AdmissionProcessingController::class, 'submitApplication'])->name('application.submit');

});

Route::prefix('submission')->middleware(['auth','role:admin|ict_support|dean|hod|exam_officer|reg_officer'])->group(function(){

    Route::get('preview/applicationPreview/{id}',[AdmissionProcessingController::class, 'printAcknowledgement'])->name('print.acknowledgment');
    Route::get('/applicationPreview/{id}',[AdmissionProcessingController::class, 'previewApplication'])->name('preview.submitted.application');

    # Applliction Processing Reports
    Route::get('/view/Applicant/Payments',[AdmissionProcessingController::class, 'viewPaidApplicants'])->name('view.applicant.payments');
    Route::get('/view/Applicant/Submissions',[AdmissionProcessingController::class, 'viewSubmittedApplications'])->name('view.submitted.applications');

});



Route::get('departments-get/{id}', [\App\Http\Controllers\Applicant\AcademicController::class, 'getDepartmentsFromFaculty']);
Route::get('programmes-get/{id}', [\App\Http\Controllers\Applicant\AcademicController::class, 'getProgrammeFromDepartment']);


Route::prefix('student')->middleware(['auth', 'role:student', 'coursereg_clearance.confirm', 'profile_completed','verified'])->group(function () {

    Route::prefix('outstanding')->group(function () {
        Route::get('/school', [\App\Http\Controllers\Applicant\QualificationsController::class, 'school'])->name('applicants.qualifications.school');
        Route::get('/professional', [\App\Http\Controllers\Applicant\QualificationsController::class, 'professional'])->name('applicantss.qualifications.professional');
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

Route::prefix('PayProcessor')->middleware(['auth', 'role:pay_processor|admin|bursary|dean_pg', 'verified'])->group(function () {

    Route::prefix('UploadPayments')->group(function () {

        Route::view('/studentpaymentsupload','admin.configs.import-student-payments')->name('student.paymentupload.form');
        Route::post('/studentpaymentupload', [StudentInformationController::class, 'uploadStudentPayments'])->name('student.payment.upload');
        Route::view('/applicantpaycodesearch','bursary.search-applicant-paycode')->name('applicant.paycode.form');
        Route::post('/applicantpaycodesearch', [AdmissionController::class, 'selecPayCodeApplicant'])->name('select.paycode.upload');
        Route::post('/applicantpaycodeconfirm', [AdmissionController::class, 'activateStudentAccount'])->name('activate.student.account');

        Route::view('/student-payment-report','bursary.search-applicant-payments')->name('search.paid.applicants');
        Route::post('/student-payment-report', [BillingController::class, 'getPaidAdmittedStudents'])->name('view.paid.applicants');

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
Route::get('api/payb', [PaymentHandleController::class, 'confirmcredoApplicationPayment']);
Route::post('api/payb', [PaymentHandleController::class, 'confirmcredoApplicationPayment']);

