<?php

use App\Http\Controllers\AcademicSessionsController;
use App\Http\Controllers\Admin\AdminReportsController;
use App\Http\Controllers\Admin\FeeCategoriesController;
use App\Http\Controllers\Admin\FeeConfigurationsController;
use App\Http\Controllers\Admin\FeeItemsController;
use App\Http\Controllers\Admin\FeeTemplateItemsController;
use App\Http\Controllers\Admin\FeeTemplatessController;
use App\Http\Controllers\Admin\FeeTypesController;
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
use App\Http\Controllers\ScholarshipController;
use App\Http\Controllers\SemesterCourseAllocationController;
use App\Http\Controllers\SemesterCoursesController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\Student\StudentPaymentController;
use App\Http\Controllers\Student\StudentRegistrationController;
use App\Http\Controllers\StudentDeffermentController;
use App\Http\Controllers\StudentInformationController;
use App\Http\Controllers\StudyLevelsController;
use App\Http\Controllers\SystemVariablesController;
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
Route::get('/applicant/acceptance-fee', [ApplicantPaymentController::class, 'acceptanceFee'])->name('acceptance.fee');
Route::post('/applicant/first-tuition-fee', [ApplicantPaymentController::class, 'firstTuitionFee'])->name('first.tuition.fee');
Route::post('/applicant/first-extra-charges', [ApplicantPaymentController::class, 'firstExtraCharges'])->name('first.extra.charge');
Route::get('/applicant/reprocess-credo-fee/{id}', [ApplicantPaymentController::class, 'reprocessCredoFee'])->name('reprocess.credo.payment');

Route::prefix('admin')->middleware(['role:admin|dean_pg|dean|hod|reg_officer|exam_officer|ict_support|bursary|dap|registry','auth', 'profile_completed', 'verified'])->group(function(){

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

    Route::prefix('paymentMgt')->group(function(){
        # Applicant payment report
        Route::get('/view/Applicant/PendingPayments',[AdmissionProcessingController::class, 'verifyApplicantPayments'])->name('verify.applicant.payments');
        Route::get('/check/Applicant/PendingPayments/{id}',[AdmissionProcessingController::class, 'checkPaymentStatus'])->name('check.payment.status');
        Route::get('/check/Credo/PendingPayments/{id}',[BillingController::class, 'checkCredoPaymentStatus'])->name('check.credo.payment.status');
        Route::get('/cleanPaymentLog', [AdmissionProcessingController::class, 'cleanPaymentLog'])->name('clean.payment.log');
    });

    Route::prefix('configurations')->group(function(){
        Route::resource('/faculties', FacultyController::class);
        Route::resource('/departments', DepartmentController::class);
        Route::resource('/programs', ProgrammeController::class);
        Route::resource('/studylevels', StudyLevelsController::class);
        Route::resource('/semestercourses', SemesterCoursesController::class);
        Route::resource('/systemvariables', SystemVariablesController::class);
        Route::resource('/scholarsips', ScholarshipController::class);
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

    Route::prefix('StudentManagement')->middleware('auth','role:admin|dean_pg|dap|vc|dvc','profile_completed','verified')->group(function(){
        Route::post('submitDeffermentDetails', [StudentDeffermentController::class, 'viewStudentDetails'])->name('view.defferment.student');
        Route::resource('defermentMgt', StudentDeffermentController::class);

    });


    Route::prefix('admissionProcessing')->middleware('auth','role:admin|dean_pg|dean|hod|reg_officer|exam_officer|ict_support|dap|registry|bursary')->group(function(){
        Route::get('admissionHome', [AdmissionController::class, 'selectProgrammeForAdmission'])->name('select.admission.applicants');
        Route::post('selectApplicants', [AdmissionController::class, 'selectApplicantsForAdmission'])->name('search.applicants.torecommend');
        Route::post('recommendApplicants', [AdmissionController::class, 'recommendSelectedApplicants'])->name('recommend.selected.applicants');
        #download applicant routes
        Route::get('downloadApplicants', [AdmissionController::class, 'selectProgrammeForDownload'])->name('select.applicant.download');
        Route::post('selectDownloadApplicants', [AdmissionController::class, 'selectApplicantsForDownload'])->name('search.applicants.todownload');
        #view admission List
        Route::get('viewAdmissinList', [AdmissionController::class, 'viewCurrentAdmissionList'])->name('view.admission.list');

        Route::get('vetoAdmission', [AdmissionController::class, 'viewListForVeto'])->middleware('role:admin|dean_pg')->name('view.for.veto');
        Route::get('vetoAdmission/{id}', [AdmissionController::class, 'vetoAdmission'])->middleware('role:admin|dean_pg')->name('veto.admission');

        #change before admitting
        Route::get('viewChangeAdmission', [AdmissionController::class, 'viewListForChangeAdmission'])->middleware('role:admin|dean_pg')->name('view.for.change.admission');
        Route::get('changeAdmission/{id}', [AdmissionController::class, 'previewBeforeChangeAdmission'])->middleware('role:admin|dean_pg')->name('select.change.admission');
        Route::post('changeAdmission', [AdmissionController::class, 'processChangeAdmission'])->middleware('role:admin|dean_pg')->name('effect.change.admission');

        #Notify un-notified candidates here
        Route::get('sendAdmissionNotification', [AdmissionController::class, 'notifyCandiates'])->name('send.admission.notifications');

        #Admission Processing Home
        Route::get('processingHome',[AdmissionProcessingController::class, 'admissionProcessingHome'])->middleware('role:admin|registry|dap|bursary')->name('admission.processing.home');
        Route::post('getApplicantForProcessing',[AdmissionProcessingController::class, 'getApplicantAdmissionDetails'])->middleware('role:admin|registry|dap|bursary')->name('admission.processing.details');
        Route::post('effectAdmissionProcessing',[AdmissionProcessingController::class, 'effectApplicantAdmissionProcessing'])->middleware('role:admin|registry|dap|bursary')->name('effect.admission.processing');

        Route::get('firstTuitionInvoice/{id}',[AdmissionProcessingController::class, 'printFirstTuitionInvoice'])->name('print.first.tuition.invoice');




    });

});

Route::prefix('RegManagement')->middleware('auth', 'role:hod|dean|reg_officer|vc|dvc|exam_officer|dean_pg|admin', 'profile_completed', 'verified')->group(function(){

    Route::prefix('Approvals')->middleware('role:hod|dean|admin|reg_officer|vc|dvc|exam_officer')->group(function(){
        Route::resource('reg', RegistrationApprovalController::class);
        Route::get('get/Approvals', [RegistrationApprovalController::class, 'showApproved'])->name('reg.approvals');
        Route::get('get/Approvals/{id}/{student_id}', [RegistrationApprovalController::class, 'showStudentConfirmedReg'])->name('show.single.student.reg');

    });

    Route::prefix('bulk')->middleware('role:admin')->group(function(){
        Route::get('/RegisterCourse',[StudentRegistrationController::class, 'searchBulkRegistration'])->name('add.bulk.registration');
        Route::post('/RegisterCourse',[StudentRegistrationController::class, 'bulkRegistration'])->name('add.bulk.reg');
        Route::get('/RegisterSingleCourse',[StudentRegistrationController::class, 'searchSingleRegistration'])->name('add.single.course');
        Route::post('/RegisterSingleCourse',[StudentRegistrationController::class, 'singleRegistration'])->name('add.single.reg');


    });


    Route::prefix('curriculum')->middleware('role:dean|hod|reg_officer|exam_officer')->group(function(){
        Route::get('/Dept/viewCurriculums',[curriculaController::class, 'getMyCurricula'])->name('get.mycurricula');
        Route::get('/Dept/showCurriculum',[curriculaController::class, 'showMyCurricula'])->name('show.mycurriculum');
    });


    Route::prefix('Reports')->middleware('role:admin|dean_pg')->group(function(){

        Route::view('/RegReport', 'admin.search-registered-students')->name('search.registered.students');
        Route::view('/NotRegReport', 'admin.search-notregistered-students')->name('search.notregistered.students');
        Route::post('RegReport', [RegistrationApprovalController::class, 'registeredStudentsReport'])->name('show.registered.students');
        Route::post('NotRegReport', [RegistrationApprovalController::class, 'NotRegisteredStudentsReport'])->name('show.notregistered.students');

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

Route::prefix('submission')->middleware(['auth','role:admin|ict_support|dean|hod|exam_officer|reg_officer|dean_pg|applicant'])->group(function(){

    Route::get('preview/applicationPreview/{id}',[AdmissionProcessingController::class, 'viewApplicantAcknowledement'])->name('print.acknowledgment');
    Route::get('submitted/applicationPreview/{id}',[AdmissionProcessingController::class, 'previewApplication'])->name('preview.submitted.application');

    # Applliction Processing Reports
    Route::get('/view/Applicant/Payments',[AdmissionProcessingController::class, 'viewPaidApplicants'])->name('view.applicant.payments');
    Route::get('/view/Applicant/Submissions',[AdmissionProcessingController::class, 'viewSubmittedApplications'])->name('view.submitted.applications');

});

Route::prefix('admissionProcessing')->middleware('auth', 'role:admin|admitted|')->group(function(){

    Route::get('admittedHome', [AdmissionProcessingController::class, 'admittedHome'])->name('admitted.home');
    Route::get('firstTuitionHome/{id}', [AdmissionProcessingController::class, 'beginFresherFeePayment'])->name('begin.first.tuition.payment');
    Route::get('firstExtraChargesHome/{id}', [AdmissionProcessingController::class, 'beginSPGSExtraChargesPayment'])->name('begin.first.spgs.charges');

});

Route::prefix('printReports')->middleware('auth','role:bursary|admitted|student|admin|dean_pg|dap|applicant|staff')->group(function(){
    Route::get('printAcceptanceInvoice/{id}', [ApplicantPaymentController::class, 'viewAcceptanceInvoice'])->name('view.acceptance.invoice');
    Route::get('printInvoice/{id}', [ApplicantPaymentController::class, 'viewInvoice'])->name('view.invoice');
    #Admission Letter printing
    Route::get('printAdmissionLetter/{id}', [AdmissionProcessingController::class, 'printAdmissionLetter'])->name('admission.letter.printing');
    #Student Printing for first tuition fee
    Route::get('firstStdTuitionInvoice/{id}',[AdmissionProcessingController::class, 'printFirstTuitionInvoice'])->name('print.student.first.tuition.invoice');
    #Student Printing for first SPGS Charges
    Route::get('firstStdExtraChargesInvoice/{id}',[AdmissionProcessingController::class, 'printFirstExtraChargesInvoice'])->name('print.first.extra.charges.invoice');


    Route::get('printReceipt/{id}', [PaymentHandleController::class, 'printGeneralReceipt'])->name('print.general.receipt');
});



Route::get('departments-get/{id}', [\App\Http\Controllers\Applicant\AcademicController::class, 'getDepartmentsFromFaculty']);
Route::get('programmes-get/{id}', [\App\Http\Controllers\Applicant\AcademicController::class, 'getProgrammeFromDepartment']);


Route::prefix('student')->middleware(['auth', 'role:student', 'coursereg_clearance.confirm', 'profile_completed','verified'])->group(function () {

    Route::prefix('outstanding')->group(function () {
        Route::get('/school', [\App\Http\Controllers\Applicant\QualificationsController::class, 'school'])->name('applicants.qualifications.school');
        Route::get('/professional', [\App\Http\Controllers\Applicant\QualificationsController::class, 'professional'])->name('applicantss.qualifications.professional');
        Route::post('/payments', [\App\Http\Controllers\Applicant\QualificationsController::class, 'store'])->name('student.outstanding.payments');
        Route::get('/lateRegInitiate', [StudentPaymentController::class ,'lateRegistrationFee'])->name('initiate.late.reg');
    });

    Route::prefix('registration')->group(function () {
        Route::resource('/coursereg', StudentRegistrationController::class)->middleware('check.late.reg');
        Route::get('/viewMyRegistrations/{id}',[StudentRegistrationController::class, 'showPrevious'])->name('student.registration.viewAll');
        Route::get('/viewSingleRegistration/{id}',[StudentRegistrationController::class, 'showSingleReg'])->name('student.registration.viewSingle');
        Route::get('/showSubmitedRegistration/{id}',[StudentRegistrationController::class, 'showConfirmedReg'])->name('student.registration.viewMyConfirmed');
        Route::get('/printExamCard/{id}',[StudentRegistrationController::class, 'printExamCard'])->name('student.registration.printexamcard');
        Route::get('/deRegisterCourse/{id}',[StudentRegistrationController::class, 'deRegisterCourse'])->name('student.registration.removecourse');
        Route::get('/submitRegistration/{id}',[StudentRegistrationController::class, 'submitCourseReg'])->name('student.registration.submit');
        Route::get('/checkoutline', [\App\Http\Controllers\Applicant\QualificationsController::class, 'school'])->name('student.registration.outlinecheck');

    });


});

Route::prefix('bursary')->middleware(['auth', 'role:admin|bursar|bursary|dap'])->group(function () {

    //Fee Configuration Routes

    Route::resource('/fee-items', FeeItemsController::class);
    Route::resource('/fee-categories', FeeCategoriesController::class);
    Route::resource('/fee-types', FeeTypesController::class);
    Route::resource('/fee-templates', FeeTemplatessController::class);
    Route::resource('/fee-configs', FeeConfigurationsController::class);

    // Student Billing routes

    Route::get('/tuition-billing/confirmation', [BillingsController::class, 'getBillingForConfirmations'])->name('billing.confirmation');
    Route::get('/tuition-billing/checking', [BillingsController::class, 'getBillingForChecking'])->name('billing.checking');
    Route::get('/tuition-billing/approval', [BillingsController::class, 'getBillingForApproval'])->name('billing.approval');
    Route::get('/tuition-billing/approved', [BillingsController::class, 'getApprovedBilling'])->name('billing.approved');
    Route::get('/tuition-billing/{id}/deleteAction', [BillingsController::class, 'deleteProposal'])->name('delete.bill.proposal');
    Route::get('/tuition-billing/{id}/reverseAction', [BillingsController::class, 'reverseProposal'])->name('reverse.bill.proposal');
    Route::get('/tuition-billing/confirmAction', [BillingsController::class, 'confirmBilling'])->name('billing.confirm.action');
    Route::get('/tuition-billing/checkAction', [BillingsController::class, 'checkBilling'])->name('billing.check.action');
    Route::get('/tuition-billing/approveAction', [BillingsController::class, 'approveBilling'])->name('billing.approve.action');
    Route::view('/tuition-billing/uploadAcceptance', 'bursar.initiate-acceptance-billing')->name('acceptance.bill.upload');
    Route::post('/tuition-billing/accptanceBilling', [BillingsController::class, 'initiateAcceptanceBilling'])->name('acceptance.billing.upload');
    Route::resource('/tuition-billing', BillingsController::class);


    //Scholarship Processing Routes
    Route::get('/schorlarship-processing/confirmation', [ScholarshipProcessingController::class, 'getScholarshipForConfirmations'])->name('scholarship.confirmation');
    Route::get('/schorlarship-processing/checking', [ScholarshipProcessingController::class, 'getScholarshipForChecking'])->name('scholarship.checking');
    Route::get('/schorlarship-processing/approval', [ScholarshipProcessingController::class, 'getScholarshipForApproval'])->name('scholarship.approval');
    Route::get('/schorlarship-processing/approved', [ScholarshipProcessingController::class, 'getApprovedScholarship'])->name('scholarship.approved');

    Route::get('/schorlarship-processing/{id}/deleteAction', [ScholarshipProcessingController::class, 'deleteProposal'])->name('delete.scholarship.proposal');
    Route::get('/schorlarship-processing/{id}/reverseAction', [ScholarshipProcessingController::class, 'reverseProposal'])->name('reverse.scholarship.proposal');
    Route::get('/schorlarship-processing/confirmAction', [ScholarshipProcessingController::class, 'confirmBilling'])->name('scholarship.confirm.action');
    Route::get('/schorlarship-processing/checkAction', [ScholarshipProcessingController::class, 'checkBilling'])->name('scholarship.check.action');
    Route::get('/schorlarship-processing/approveAction', [ScholarshipProcessingController::class, 'approveBilling'])->name('scholarship.approve.action');
    Route::get('/schorlarship-processing/disApproveAction', [ScholarshipProcessingController::class, 'DeConfirmBilling'])->name('scholarship.disapprove.action');

    Route::get('/schorlarship-processing/viewExcessReport', [ScholarshipProcessingController::class, 'viewExcessReport'])->name('scholarship.excess.report');

    Route::get('/schorlarship-processing/deleteExcessReport/{id}', [ScholarshipProcessingController::class, 'deleteExcessScholarship'])->name('scholarship.excess.delete');

    Route::resource('/schorlarship-processing', ScholarshipProcessingController::class);




    //Manual Payment Processing Routes
    Route::get('/manual-payment-processing/confirmation', [StudentManualPaymentController::class, 'getManualPaymentForConfirmations'])->name('manual.payment.confirmation');
    Route::get('/manual-payment-processing/checking', [StudentManualPaymentController::class, 'getManualPaymentForChecking'])->name('manual.payment.checking');
    Route::get('/manual-payment-processing/approval', [StudentManualPaymentController::class, 'getManualPaymentForApproval'])->name('manual.payment.approval');
    Route::get('/manual-payment-processing/approved', [StudentManualPaymentController::class, 'getApprovedManualPayment'])->name('manual.payment.approved');

    Route::get('/manual-payment-processing/{id}/deleteAction', [StudentManualPaymentController::class, 'deleteProposal'])->name('delete.manual.payment.proposal');
    Route::get('/manual-payment-processing/{id}/reverseAction', [StudentManualPaymentController::class, 'reverseProposal'])->name('reverse.manual.payment.proposal');
    Route::get('/manual-payment-processing/confirmAction', [StudentManualPaymentController::class, 'confirmBilling'])->name('manual.payment.confirm.action');
    Route::get('/manual-payment-processing/checkAction', [StudentManualPaymentController::class, 'checkBilling'])->name('manual.payment.check.action');
    Route::get('/manual-payment-processing/approveAction', [StudentManualPaymentController::class, 'approveBilling'])->name('manual.payment.approve.action');
    Route::get('/manual-payment-processing/disApproveAction', [StudentManualPaymentController::class, 'DeConfirmBilling'])->name('manual.payment.disapprove.action');

    Route::get('/manual-payment-processing/viewExcessReport', [StudentManualPaymentController::class, 'viewExcessReport'])->name('manual.payment.excess.report');

    Route::get('/manual-payment-processing/deleteExcessReport/{id}', [StudentManualPaymentController::class, 'deleteExcessScholarship'])->name('manual.payment.excess.delete');

    Route::resource('/manual-payment-processing', StudentManualPaymentController::class);



    //Manual Payment Proccessing routes

    Route::post('/delete-template-item/{id}', [FeeTemplateItemsController::class, 'deleteFeeTemplateItem'])->name('delete.template.item');
    Route::post('/edit-template-item/{id}', [FeeTemplateItemsController::class, 'editFeeTemplateItem'])->name('edit.template.item');
    Route::post('/add-template-item', [FeeTemplateItemsController::class, 'addNewTemplateItem'])->name('new.template.item');

    // Student Wallet Routes
    Route::resource('/student-wallets', StudentWalletController::class);
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

    Route::prefix('PayVerification')->group(function(){
        Route::get('manualVerification', [BillingController::class, 'verifyManualPayments'])->name('manual.payment.verification');
    });

    Route::prefix('paymentReports')->group(function(){
        Route::get('feePaymentReport/{purpose}',[BillingController::class, 'feePaymentReport'])->name('fee.payment.report');
    });

});



# To view/verify Preview of Application Form
Route::get('/AppFormPreview/{id}/verify', [AdmissionProcessingController::class, 'verifyApplicantPreviewPage'])->middleware('auth', 'role:staff')->name('verify.applicant.form');

# To view/verify courseReg
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

