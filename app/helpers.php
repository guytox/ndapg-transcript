<?php

use App\Models\AcademicSession;
use App\Models\AdmissionCount;
use App\Models\Country;
use App\Models\CredoRequest;
use App\Models\CredoResponse;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\FeeCategory;
use App\Models\FeeConfig;
use App\Models\FeeItem;
use App\Models\FeePayment;
use App\Models\FeeTemplate;
use App\Models\FeeType;
use App\Models\Gender;
use App\Models\MatricConfig;
use App\Models\MatricConfiguration;
use App\Models\NdaService;
use App\Models\PaymentConfiguration;
use App\Models\Program;
use App\Models\RegMonitor;
use App\Models\Semester;
use App\Models\SemesterCourse;
use App\Models\SiteNotification;
use App\Models\State;
use App\Models\StudentRecord;
use App\Models\StudyLevel;
use App\Models\SystemVariable;
use App\Models\TranscriptDeliveryMode;
use App\Models\TranscriptType;
use App\Models\User;
use App\Models\UserProfile;
use Carbon\Carbon;
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
    $session = AcademicSession::orderBy('id','desc')->get()->pluck('name','id');

    return $session;
}

function getApplicationSession(){
    $sessionId = AcademicSession::where('isApplicationSession',1)->first();

    if ($sessionId) {
        return $sessionId->id;
    }

    return false;
}

function isApplicationOpen(){
    $admissionStatus = SystemVariable::where('name','applications')->first();
    if ($admissionStatus) {
        return $admissionStatus;
    }else{
        return false;
    }
}

function getCarbonDate(){
    $nowDate = Carbon::createFromFormat('Y-m-d H:i:s', now());

    return $nowDate;
}

function getMatricSession(){
    $sessionId = AcademicSession::where('isApplicationSession',1)->first();

    if ($sessionId) {

        $matricYear = substr ($sessionId->name, -2);

        return $matricYear;
    }

    return false;
}

function getMatricSerial(){
    $matricCounters = AdmissionCount::where('category','matric')->first();

    if ($matricCounters) {
        #get the present value to return
        $toreturn = $matricCounters->count;
        #increment the counter by one and store in the table
        $newCount = $toreturn + 1;
        $matricCounters->count = $newCount;
        $matricCounters->save();

        return $toreturn;
    }else{
        return false;
    }

}

function getSiteNotification(){
    $notification = SiteNotification::where('n_status', 1)->first();

    if ($notification) {
        return $notification->n_message;
    }else{
        return false;
    }
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

function getAllStudyLevelsDropdown(){
    $session = StudyLevel::orderBy('id', 'asc')->get()->pluck('level','id');

    return $session;
}





function getAllSemesters(){
    //get Study levels
    $studyLevels = Semester::all()->pluck('name', 'id');

    return $studyLevels;
}

function getformNumber(){
    $formNumber = AdmissionCount::where('category','pg')->first();

    $newCount = $formNumber->count + 1;

    $returned = $formNumber->prefix . $newCount;
    #increment the count
    $formNumber->count = $newCount;
    $formNumber->save();

    return $returned;
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
    $applicationNo = AdmissionCount::where('category', 'transcript')->first();

    if($applicationNo) {
        $newAppNo = $applicationNo->prefix . $applicationNo->count;

        $newCount = $applicationNo->count + 1;
        $applicationNo->count = $newCount;
        $applicationNo->save();

        return $newAppNo;

    }else{

        return false;
    }
}

function updateApplicationNumber($number)
{
    $number = substr($number, -5);

    $applicationNo = MatricConfiguration::where('session_id', activeSession()->id)->first();
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
    return Carbon::today()->toDateString();
}

function humanReadableDate($date)
{
    return Carbon::parse($date)->format('D d, M Y');
}

function presentationFileURL($file)
{
    return asset('presentations/' . $file);
}


function generateUniqueTransactionReference()
{
    do {
        $code = random_int(1000000, 9999999);
    } while (CredoResponse::where("businessRef", "=", $code)->first());

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
// Semester Helpers
//*********************************************************************************************** */

function getSemesterDetailsById($id){
    $semester = Semester::find($id);
    return $semester->name;
}

function getSemesterById($id){

    $semester = Semester::find($id);

    return $semester;
}

function getSemesterIdByName($id){
    $semester = Semester::where('name', $id)->first();
    return $semester->id;
}

function getSemestersDropdown(){
    $semesters = Semester::all()->pluck('name', 'id');
    return $semesters;
}

function getActiveSemesterId() {
    $acad = activeSession();

    if($acad) {
        $semId = getSemesterIdByName($acad->currentSemester);
        return $semId;
    }

    abort(403, 'Session Not Configured');
}


// ******************************************************************************************************************
// Semester  Helpers
// ******************************************************************************************************************


function getSemesterNameById($id){
    if ($id=='') {
        return "N/A";
    }

    if($semester = Semester::where('id', $id)->first()){

        return $semester->name;
    } else{
        return "Wrong Study Level";
    }
}

function getInStateValue($id){

    if ($id== '') {
        return "N/A";
    }

    if($id == 2){

        return "NO";

    } elseif ($id== 1) {

        return "YES";
    }else{

        return "Invalid Figure Supplied";
    }
}


// ******************************************************************************************************************
// Fee Cateogory Helpers
// ******************************************************************************************************************
function getFeeCategoryName($id){

    $feeCategory = FeeCategory::find($id);

    return $feeCategory->category_name;
}

function getfeeCategoryIdByCategoryName($categoryName){

    $purpose = FeeCategory::where('category_name', $categoryName)->first();

    return $purpose->id;
}

function getFeeCatoryBySlug($categoryName){

    $purpose = FeeCategory::where('payment_purpose_slug', $categoryName)->first();

    return $purpose;
}

// ******************************************************************************************************************
// Fee Template  Helpers
// ******************************************************************************************************************
function getFeeTemplateAmount($id){
    $teeTemplateAmount = FeeTemplate::find($id);

    return $teeTemplateAmount->total_amount;
}

function getFeeTemplateIdByTemplateName($name){

    $feeTemplateId = FeeTemplate::where('narration', $name)->first();

    return $feeTemplateId->id;
}

function getPaymentTemplateByTypeName($name){

    $feeTemplates = FeeTemplate::where('fee_types.name', $name)
                                    ->join('fee_types', 'fee_types.id', '=', 'fee_templates.fee_type_id')
                                    ->get()->pluck('narration', 'id');

    return $feeTemplates;
}

function getTemplateFromConfigId($id){
    $feeConfig = FeeConfig::find($id);

    $feeTemplate = FeeTemplate::find($feeConfig->fee_template_id);

    return $feeTemplate;
}

// ******************************************************************************************************************
// fee Payment Helpers
// ******************************************************************************************************************

function getPaymentPurposeById($id){
    $purpose = FeeConfig::find($id);

    return $purpose->narration;
}

function isPaymentPaid($id){
    $payment = FeePayment::where('id', $id)
                        ->orWhere('uid', $id)
                        ->first();
    if ($payment) {
        if ($payment->balance==0) {
            return true;
        }else{
            return false;
        }
    }else{
        return false;
    }

    return false;
}

function isCredoRequestPending($id){
    $feePayment = CredoRequest::where('fee_payment_id', $id)->where('status',"pending")->first();
    if ($feePayment) {
        return $feePayment;
    }else{
        return false;
    }
}

function generateServiceCode($id){

    $fetchCode = CredoRequest::find($id);

    switch ($fetchCode->payment->config->feeCategory->payment_purpose_slug) {

        case 'application-fee':
            return config('app.credo.serviceCode.applicationFee');
            break;

        case 'acceptance-fee':
            return config('app.credo.serviceCode.acceptanceFee');
            break;

        case 'first-tuition':
            return config('app.credo.serviceCode.TuitionFee');
            break;

        case 'late-registration':
            return config('app.credo.serviceCode.lateRegistration');
            break;

        case 'tuition':
            return config('app.credo.serviceCode.TuitionFee');
            break;

        case 'portal-services':
            return config('app.credo.serviceCode.ExtraCharges');
            break;

        case 'spgs-charges':
            return config('app.credo.serviceCode.ExtraCharges');
            break;

        case 'ug-transcript':
            return config('app.credo.serviceCode.TranscriptFee');
            break;

        case 'pg-transcript':
            return config('app.credo.serviceCode.TranscriptFee');
            break;



        default:
            return config('app.credo.serviceCode.ExtraCharges');
            break;
    }
}



// ******************************************************************************************************************
// Study Level  Helpers
// ******************************************************************************************************************

function getAllStudyLevels(){
    //get Study levels
    $studyLevels = StudyLevel::all()->pluck('level', 'id');

    return $studyLevels;
}

function getStudyLevelNameById($id){
    if ($id=='') {
        return "N/A";
    }

    if($studylevel = StudyLevel::where('id', $id)->first()){

        return $studylevel->level;
    } else{
        return "Wrong Study Level";
    }
}

function getStudyLevelDetailsById($id){
    $level = StudyLevel::find($id);
    return $level->level;
}

function getStudyLevelIdByName($name){
    $level = StudyLevel::where('level',$name)->first();
    return $level->id;
}


// ******************************************************************************************************************
// Programme Helpers
// ******************************************************************************************************************

function getAllProgrammes(){
    //get all active programmes
    $programs = Program::orderBy('name')->get()->pluck('name','id');

    return $programs;
}

function getProgrammeNameById($id){
    if ($id=='') {
        return "N/A";
    }

    if($program = Program::where('id', $id)->first()){

        return $program->name;
    } else{
        return "Wrong Program";
    }


}

// ******************************************************************************************************************
// Fee Item Helpers
//*******************************************************************************************************************

function getFeeItemName($id){
    $feeItemName = FeeItem::find($id);

    return $feeItemName->name;
}


// ******************************************************************************************************************
// Fee Type Helpers
// ******************************************************************************************************************
function getFeeTypeName($id){
    $feeItemName = FeeType::find($id);

    return $feeItemName->name;
}

function getFeeTypeIdByTypeName($typeName){

    $feeTypeId = FeeType::where('name', $typeName)->first();

    return $feeTypeId->id;
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
            return $value;
            break;
    }


}

function getProgrammeDetailByName($name){
    $program = Program::where('name','=', $name)->first();
    return $program;
}

function getProgramNameById($id){
    $progName = Program::find($id);

    if ($progName) {

        return $progName->name;

    }else{

        return false;

    }
}

function getAppliableProgrammeDropdown(){
    $listOfProgrammes = Program::where('is_advertised',"1")->OrderBy('name', 'asc')->get()->pluck('name','id');

    if ($listOfProgrammes) {

        return $listOfProgrammes;

    }else{

        return false;
    }
}

function getAllProgrammesDropdown(){
    $listOfProgrammes = Program::OrderBy('name', 'asc')->get()->pluck('name','id');

    if ($listOfProgrammes) {

        return $listOfProgrammes;

    }else{

        return false;
    }
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

function getAcademicRoles($id){

    $allUserRoles = User::join('model_has_roles', 'model_has_roles.model_id','=','users.id')
                                    ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                                    ->where('users.id',$id)
                                    ->whereIn('roles.name',['dean','hod', 'reg_officer', 'dean_pg','vc'])
                                    ->select('roles.*')
                                    ->get()
                                    ->pluck('name','id');
    return $allUserRoles;


}


//*********************************************************************************************** */
// Credo Verification Helper
//*********************************************************************************************** */

function verifyCredoPayment($ref){
    $headers = [
        'Content-Type' => 'application/JSON',
        'Accept' => 'application/JSON',
        'Authorization' => config('app.credo.private_key'),
    ];
    #form the new url
    $newurl = 'https://api.credocentral.com/transaction/'.$ref.'/verify';
    #intiliaze new request
    $client = new \GuzzleHttp\Client();
    #fire request here
    $response = $client->request('GET', $newurl,[
        'headers' => $headers,
    ]);
    #expor the json
    $parameters = json_decode($response->getBody());

    return $parameters;

}


//*********************************************************************************************** */
// NDA Service Dropdown Helpers
//*********************************************************************************************** */

function selectServiceDropdown(){

    $ndaServices = NdaService::all()->pluck('service_name','id');

    return $ndaServices;

}


//*********************************************************************************************** */
// NDA Gender Dropdown Helpers
//*********************************************************************************************** */

function selectNdaGenderDropdown(){

    $ndaServices = Gender::all()->pluck('gender_name','id');

    return $ndaServices;

}

//*********************************************************************************************** */
// NDA Transcript Type Dropdown Helpers
//*********************************************************************************************** */

function selectTranscriptTypeDropdown(){

    $ndaServices = TranscriptType::all()->pluck('type_name','id');

    return $ndaServices;

}

//*********************************************************************************************** */
// NDA Transcript Country Dropdown Helpers
//*********************************************************************************************** */

function selectTranscriptCountryDropdown(){

    $ndaServices = Country::orderBy('country_name','asc')->get()->pluck('country_name','id');

    return $ndaServices;

}

function getCountryById($id){
    $selectedCountry = Country::find($id);

    return $selectedCountry;
}


//*********************************************************************************************** */
// NDA Transcript Country Dropdown Helpers
//*********************************************************************************************** */

function selectDeliveryModeDropdown(){

    $ndaServices = TranscriptDeliveryMode::orderBy('mode','asc')->get()->pluck('mode','id');

    return $ndaServices;

}







