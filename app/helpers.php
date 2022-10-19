<?php

use App\Models\AcademicSession;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\FeePayment;
use App\Models\MatricConfig;
use App\Models\MatricConfiguration;
use App\Models\Program;
use App\Models\RegMonitor;
use App\Models\Semester;
use App\Models\SemesterCourse;
use App\Models\State;
use App\Models\StudentRecord;
use App\Models\StudyLevel;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Auth;

function activeSession(){
    $session = \App\Models\AcademicSession::where('status', true)->first();
    if($session) {
        return $session;
    }

    throw new Exception('No active session configured');
}

function getsessionById($id){
    $session = AcademicSession::find($id);
    if($session) {
        return $session;
    }

    throw new Exception('No active session configured');
}

function getSessionsDropdown(){
    $session = AcademicSession::all()->pluck('name','id');

    return $session;
}


function generateMatriculationNumber()
{
    $matricNo = MatricConfiguration::where('session_id', activeSession()->id)->first();

    if ($matricNo) {
        if ($matricNo->matric_count===0) {
            return $matricNo->student_number.($matricNo->matric_count +1);
        }else{
            return $matricNo->student_number.$matricNo->matric_count;
        }
    }

}

function updateMatriculationNumber($number)
{
    $number = substr($number, -5);

    $applicationNo = \App\Models\MatricConfiguration::where('session_id', activeSession()->id)->first();
    $modifiedNumber = (string)((int)($number));
    if($applicationNo) {
        $applicationNo->update([
            'matric_count' => intval($modifiedNumber) + 1
        ]);
    }
}

function generateApplicationNumber(){
    $applicationNo = \App\Models\MatricConfiguration::where('session_id', activeSession()->id)->first();

    if($applicationNo) {
        if($applicationNo->application_number_count === 0) {
            return $applicationNo->application_number . ($applicationNo->application_number_count + 1);
        }
        return $applicationNo->application_number . $applicationNo->application_number_count;
    }
}

function updateApplicationNumber($number)
{
    $number = substr($number, -5);

    $applicationNo = \App\Models\MatricConfiguration::where('session_id', activeSession()->id)->first();
    $modifiedNumber = (string)((int)($number));
    if($applicationNo) {
        $applicationNo->update([
            'application_number_count' => intval($modifiedNumber) + 1
        ]);
    }
}

function user()
{
    return Auth::user();
}

function getUser($id,$param){

    $getuser = User::find($id);
    switch ($param) {

        case 'name':
            return $getuser->name;
            break;

        case 'email':
            return $getuser->email;
            break;

        case 'phone_number':
            return $getuser->phone_number;
            break;

        case 'username':
            return $getuser->username;
            break;

        case 'level':
            return $getuser->current_level;
            break;

        case 'all':
            return $getuser;
            break;

        default:
            return "N/A";
            break;
    }

}

function getUserByUsername($username){
    $user = User::where('username',$username)->first();

    return $user;
}

function getUserById($id){
    $user = User::find($id);

    return $user;
}

/**
 * Carbon get todays date only.
 */
function currentDate()
{
    return Carbon\Carbon::today()->toDateString();
}

function humanReadableDate($date)
{
    return Carbon\Carbon::parse($date)->format('D d, M Y');
}

function presentationFileURL($file)
{
    return asset('presentations/' . $file);
}


function generateUniqueTransactionReference()
{
    do {
        $code = random_int(100000, 999999);
    } while (FeePayment::where("txn_id", "=", $code)->first());

    return $code;
}

function generateCheckSum($amount, $transactionId, $terminalId, $responseURL, $secretKey)
{
    return md5($amount . $terminalId . $transactionId . $responseURL . $secretKey);
}

function checkSamePaymentUID($feePaymentUID, $paymentLogUID)
{
    if ($feePaymentUID === $paymentLogUID) {
        return false;
    } else {
        return true;
    }
}

function checkSamePaymentID($feePaymentID, $paymentLogID)
{
    if ($feePaymentID === $paymentLogID) {
        return false;
    } else {
        return true;
    }
}

function changeBillingPurpose($purpose)
{
    if ($purpose === config('app.constants.initial_billing')) {
        return 'Tuition';
    }

    return $purpose;
}

function calculateBalance($amountBilled, $amountPaid)
{
    return $amountBilled - $amountPaid;
}


function formatPhoneNumber($formatted_phone_number)
{
    switch ($formatted_phone_number) {
        case str_starts_with($formatted_phone_number, '0'):
            $formatted_phone_number = '234' . substr($formatted_phone_number, 1);
            break;
        case str_starts_with($formatted_phone_number, '234'):
            break;
        case substr($formatted_phone_number, 1) !== '0':
            $formatted_phone_number = '234' . $formatted_phone_number;
            break;
    }
    return $formatted_phone_number;
}

function getBalanceFromUID($uid)
{

    $feeBalance  =  FeePayment::where('uid', $uid)->first();

    if ($feeBalance) {
        return $feeBalance->balance;
    }

    throw new Exception('Invalid UID');
}

function getPaymentPurposeById($paymentConfigId){

    $purpose = PaymentConfiguration::where('id', $paymentConfigId)->first();

    return $purpose->purpose;
}

function getActiveAcademicSessionId() {
    $academicSession = \App\Models\AcademicSession::where('status',1)->first();

    if($academicSession) {
        return $academicSession->id;
    }

    abort(403, 'Session Not Configured');
}

function getPaymentConfigBySlug($configSlug){
    $slug = \App\Models\PaymentConfiguration::where('payment_purpose_slug', $configSlug)->first();

    if($slug) {
        return $slug;
    }

    \Illuminate\Support\Facades\Log::error('payment for this slug: ' . $configSlug . 'Does not exist');
    abort(403, 'An Error Occured');
}

//*********************************************************************************************** */
// Currency / Naira Helpers
//*********************************************************************************************** */

function convertToKobo($nairaFigure){

    $koboFigure = $nairaFigure * 100;

    return $koboFigure;
}


function convertToNaira($koboFigure){

    $nairaFigure = $koboFigure/100;

    return $nairaFigure;
}

//*********************************************************************************************** */
// StudyLevel Helpers
//*********************************************************************************************** */

function getStudyLevelDetailsById($id){
    $level = StudyLevel::find($id);
    return $level->level;
}


//*********************************************************************************************** */
// Semester Helpers
//*********************************************************************************************** */

function getSemesterDetailsById($id){
    $semester = Semester::find($id);
    return $semester->name;
}

function getSemesterIdByName($id){
    $semester = Semester::where('name', $id)->first();
    return $semester->id;
}

function getSemestersDropdown(){
    $semesters = Semester::all()->pluck('name', 'id');
    return $semesters;
}


//*********************************************************************************************** */
// Semester Course Helpers
//*********************************************************************************************** */

function getCourseDetailsById($id, $param){
    $course = SemesterCourse::find($id);


    switch ($param) {
        case 'id':
            return $course->id;
            break;

        case 'code':
            return $course->courseCode;
            break;

        case 'title':
            return $course->courseTitle;
            break;

        case 'credits':
            return $course->creditUnits;
            break;

        case 'all':
            return $course;
            break;

        default:
            return "N/A";
            break;
    }
}

function getSemesterCoursesDropdown(){
    $courses = SemesterCourse::where('activeStatus', 1)->get()->pluck('courseCode','id');
    return $courses;
}

//*********************************************************************************************** */
// Faculty and Department and Programme Helpers
//*********************************************************************************************** */

function getFacultyDetailsById($id,$param){
    $faculty = Faculty::find($id);
    switch ($param) {
        case 'id':
            return $faculty->id;
            break;

        case 'name':
            return $faculty->name;
            break;

        case 'dean':
            return $faculty->dean_id;
            break;
        case 'all':
            return $faculty;
            break;

        default:
            return "N/A";
            break;
    }

}

function getDepartmentDetailById($id,$param){
    $department = Department::with('faculty')->find($id);
    switch ($param) {
        case 'id':
            return $department->id;
            break;

        case 'name':
            return $department->name;
            break;

        case 'faculty':
            return $department->faculty_id;
            break;
        case 'hod':
            return $department->hod_id;
            break;
        case 'examOfficer':
            return $department->exam_officer_id;
            break;
        case 'RegistrationOfficer':
            return $department->registration_officer_id;
            break;
        case 'all':
            return $department;
            break;

        default:
            return "N/A";
            break;
    }

}

function getDepartmentByName($name){

    $department = Department::where('name', $name)->first();

    return $department;
}





function getProgrammeDetailById($id,$param){
    $value = Program::find($id);

    switch ($param) {
        case 'id':
            return $value->id;
            break;

        case 'name':
            return $value->name;
            break;

        case 'level':
            return $value->level_id;
            break;

        case 'department':
            return $value->department_id;
            break;

        case 'dean':
            return $value->hod_id;
            break;

        case 'hod':
            return $value->hod_id;
            break;

        case 'examOfficer':
            return $value->exam_officer_id;
            break;

        case 'RegistrationOfficer':
            return $value->exam_officer_id;
            break;

        case 'title':
            return $value->degree_title;
            break;

        default:
            return "N/A";
            break;
    }


}

function getProgrammeDetailByName($name){
    $program = Program::where('name','=', $name)->first();
    return $program;
}

//*********************************************************************************************** */
// Studend Record Helpers Helpers
//*********************************************************************************************** */


function getStudentIdByUserId($id){
    $student = StudentRecord::where('user_id', $id)->first();

    return $student->id;
}



function getStudentByUserId($id){
    $student = StudentRecord::where('user_id', $id)->first();

    return $student;
}


//StudentId Helpers

function getStudentByStudentId($id){
    $student = StudentRecord::find($id);

    return $student;
}

function getFacultyByStudentId($studentId){
    $student =StudentRecord::join('programs as p', 'p.id','=','student_records.program_id')
                            ->join('departments as d', 'd.id', '=', 'p.department_id')
                            ->join('faculties as f','f.id','=','d.faculty_id')
                            ->join('admissions as a','a.matric_number','=','student_records.matric')
                            ->where('student_records.id', $studentId)
                            ->select('student_records.matric', 'student_records.program_id', 'p.name as programName', 'd.name as departmentName', 'f.name as facultyName', 'd.id as departmentId', 'f.id as facultyId', 'a.form_number')
                            ->first();

    if ($student) {
        return $student->facultyName;

    }else{

        return false;
    }

}

function getDepartmentByStudentId($studentId){
    $student =StudentRecord::join('programs as p', 'p.id','=','student_records.program_id')
                            ->join('departments as d', 'd.id', '=', 'p.department_id')
                            ->join('faculties as f','f.id','=','d.faculty_id')
                            ->join('admissions as a','a.matric_number','=','student_records.matric')
                            ->where('student_records.id', $studentId)
                            ->select('student_records.matric', 'student_records.program_id', 'p.name as programName', 'd.name as departmentName', 'f.name as facultyName', 'd.id as departmentId', 'f.id as facultyId', 'a.form_number')
                            ->first();

    if ($student) {
        return $student->departmentName;

    }else{

        return false;
    }

}

function getFormNumberByStudentId($studentId){
    $student =StudentRecord::join('programs as p', 'p.id','=','student_records.program_id')
                            ->join('departments as d', 'd.id', '=', 'p.department_id')
                            ->join('faculties as f','f.id','=','d.faculty_id')
                            ->join('admissions as a','a.matric_number','=','student_records.matric')
                            ->where('student_records.id', $studentId)
                            ->select('student_records.matric', 'student_records.program_id', 'p.name as programName', 'd.name as departmentName', 'f.name as facultyName', 'd.id as departmentId', 'f.id as facultyId', 'a.form_number')
                            ->first();

    if ($student) {
        return $student->form_number;

    }else{

        return false;
    }

}


//*********************************************************************************************** */
// State of Origin Helpers Helpers
//*********************************************************************************************** */

function getStateNameById($id){
    $stateOfOrigin = State::find($id);

    return $stateOfOrigin->name;
}

function getStateIdByName($name){

    $stateOfOrigin = State::where('name', $name)->first();

    return $stateOfOrigin->id;
}





//*********************************************************************************************** */
// Course Registration Helpers Helpers
//*********************************************************************************************** */

function checkCourseRegDuplicate($id, $user_id){
    $duplicate = RegMonitor::where(['curricula_id'=>$id, 'student_id'=>getStudentIdByUserId($user_id)])->first();

    if ($duplicate) {
        return true;
    }
    else{

        return false;
    }
}

function getStaffProfileById($id){
    //$staff = User::where('user_id', $id)->with('profile')->first();


    $staff = UserProfile::where('user_id', $id)->first();
    if ($staff) {
        return $staff;
    }else{
        return false;
    }

}
