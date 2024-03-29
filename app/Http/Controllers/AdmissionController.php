<?php

namespace App\Http\Controllers;

use App\Exports\ApplicantExport;
use App\Imports\AdminAdmissionUpload;
use App\Imports\AdmissionOfferImport;
use App\Jobs\AdmissionNotificationJob;
use App\Jobs\AdmissionRecommendationJob;
use App\Mail\AdmissionOfferNotification;
use App\Models\Admission;
use App\Models\ApplicantAdmissionRequest;
use App\Models\OlevelResult;
use App\Models\Program;
use App\Models\RegClearance;
use App\Models\StudentRecord;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserQualification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Traits\HasRoles;

class AdmissionController extends Controller
{
    use HasRoles;

    public function uploadAdministrativeAdmissionList(Request $request){

        //return $request;

        if (user()->hasRole('admin')) {
            //return $request;

            $validated = $request->validate([
                'file' => 'required|mimes:xlsx|max:3048',

            ]);

            $admissionList = $request->file('file');
            $staff = user()->id;

            Excel::import(new AdminAdmissionUpload($staff), $admissionList);

            return redirect(route('upload.administrative.admission'))->with('success', "Admission List Uploaded Successfully!!!");
        } else{

            return back()->with('error', 'You do not have the privileges to perform this action, contact ICT');

        }
    }


    public function uploadAdministrativeAdmission(){
        if (user()->hasRole('admin')) {


            return view('admin.configs.admin-admission-upload');
        }
    }


    public function uploadStudentAdmissionList(Request $request){
        if (user()->hasRole('admin')) {
            //return $request;

            $validated = $request->validate([
                'file' => 'required|mimes:xlsx|max:3048',
                'programme' => 'required'

            ]);

            $admissionList = $request->file('file');

            Excel::import(new AdmissionOfferImport($request->programme), $admissionList);

            return redirect(route('student.admissionoffer.form'))->with('success', "Admission List Uploaded Successfully!!!");
        } else{

            return back()->with('error', 'You do not have the privileges to perform this action, contact ICT');

        }
    }




    public function uploadStudentsAdmissionForm(){
        if (user()->hasRole('admin')) {

            $admitted = Admission::where('session_id', activeSession()->id)->get();

            $programmes = Program::all()->pluck('name','id');


            return view('admin.configs.import-admission-list', compact('admitted', 'programmes'));
        }
    }



    public function selecPayCodeApplicant(Request $request){
        if (user()->hasRole('admin|bursary|pay_processor')) {

            $applicant = Admission::where('form_number',$request->applicant)->first();

            $lasttenapplicants = Admission::where('cleared_by','!=',null)->orderBy('cleared_at','desc')->take(10)->get();

            return view('bursary.search-applicant-paycode', compact('applicant','lasttenapplicants'));
        }
    }


    public function activateStudentAccount(Request $request){

        if (user()->hasRole('admin|bursary|pay_processor')) {

            $validated = $request->validate([

                'formnumber' => 'required',
                'amount' => 'required|numeric'

            ]);

            $newStudent = Admission::find($request->formnumber);

            //return $newStudent;

            if ($newStudent->cleared_by !='') {
                //return back()->with('error','INFO!!!! This candidate has been verified already, Contact ICT');
            }

            if ($newStudent->id !='') {

                $newStudent->amount_paid = convertToKobo($request->amount);
                $newStudent->cleared_by = user()->id;
                $newStudent->cleared_at = now();
                $newStudent->save();

                //next create the student account
                $data = [
                    'name' => $newStudent->surname.' '. $newStudent->other_names,
                    //'email' => $newStudent->name,
                    'password' => Hash::make('1234'),
                    'email_verified_at' => now(),
                    'username' => $newStudent->matric_number,
                    //'phone_number'=> formatPhoneNumber($row['gsm']),
                    'current_level' => getProgrammeDetailById($newStudent->programme_id, 'level'),
                ];

                $studentUser = User::upsert($data, $uniqueBy =['username'],[
                    'email_verified_at',
                ]);
                //add student role
                $newuser = User::where('username', $newStudent->matric_number)->first();
                if ($newuser) {
                    $newuser->assignRole('student');
                }

                // create student record
                $studentData = [
                    'user_id'=>$newuser->id,
                    'matric'=>$newuser->username,
                    'program_id' => $newStudent->programme_id,
                    'admission_session' => activeSession()->id,
                    'state_origin' => getStateIdByName($newStudent->state)
                ];

                $studentUserRecord = StudentRecord::updateOrCreate(['user_id'=>$newuser->id, 'matric'=>$newuser->username],$studentData);
                // crete student profile
                $profileData = [
                    'user_id'=>$newuser->id,
                    'department_id'=> getStudentByMatric($newStudent->matric_number)->department_id,
                    'gender' => $newStudent->gender,
                    'nationality' => $newStudent->country,
                ];

                $newprofile = UserProfile::updateOrCreate(['user_id'=>$newuser->id], $profileData);

                // create reg_Clearance entry
                $regClearanceData = [
                    'student_id'=>$studentUserRecord->id,
                    'school_session_id'=> activeSession()->id,
                ];

                $newregClearance = RegClearance::updateOrCreate(['student_id'=>$studentUserRecord->id, 'school_session_id'=> activeSession()->id,],$regClearanceData);

                switch ($request->clearedfor) {
                    case '3':
                        $newregClearance->first_semester = 1;
                        $newregClearance->second_semester = 1;
                        $newregClearance->save();
                        break;
                    case '1':
                        $newregClearance->first_semester =1;
                        $newregClearance->save();
                        break;

                    case '2':
                        $newregClearance->second_semester = 1;
                        $newregClearance->save();
                        break;

                    default:
                        # code...
                        break;
                }
                //return $newStudent;

            }
            //return $request;

            return back()->with('info',"profile created successfully !!! NEW PASSWORD: 1234");
        }
    }


    public function selectProgrammeForAdmission(){
        #select programme based on user and fire on

        #get all academic programmes

        return view('admin.select-applicants-for-admission');

    }

    public function selectProgrammeForDownload(){
        #select programme based on user and fire on

        #get all academic programmes

        return view('admin.select-applicants-for-download');

    }

    public function selectApplicantsForAdmission(Request $request){


        if (user()->hasRole('admin|dean_pg|dean|hod|reg_officer|acad_eo')) {
            $validated = $request->validate([
                'c_prog' => 'required',
                'school_session' => 'required|numeric'
            ]);

            #get the list of applicants based on specified parameters
            $applicants = ApplicantAdmissionRequest::where('session_id', $request->school_session)
                                                    ->where('program_id', $request->c_prog)
                                                    ->where('is_submitted', 1)
                                                    ->orderBy('is_admitted','asc')
                                                    ->orderBy('pg_coord','asc')
                                                    ->orderBy('hod','asc')
                                                    ->orderBy('dean','asc')
                                                    ->get();
            #get the academic roles
            $staffRoles = getAcademicRoles(user()->id);
            #set the title of the page
            $title = "List of Applicants for ".getProgramNameById($request->c_prog)." ";

            return view('admin.viewAdmissionRecommendedList',compact('applicants','title','staffRoles'));
        }



    }

    public function viewCurrentAdmissionList(){


        if (user()->hasRole('admin|dean_pg|dean|hod|reg_officer|acad_eo')) {
            $sessionId = getApplicationSession();

            #get the list of applicants based on specified parameters
            $applicants = ApplicantAdmissionRequest::where('session_id', $sessionId)
                                                    ->where('is_admitted', 1)
                                                    ->orderBy('program_id','asc')
                                                    ->orderBy('form_number','asc')
                                                    ->get();
            #get the academic roles
            $staffRoles = getAcademicRoles(user()->id);
            #set the title of the page
            $title = "Admission List for ".getsessionById($sessionId)->name." Session";

            return view('admin.viewAdmissionList',compact('applicants','title','staffRoles'));
        }



    }

    public function viewListForVeto(){


        if (user()->hasRole('admin|dean_pg|dean|hod|reg_officer|acad_eo')) {
            $sessionId = getApplicationSession();

            #get the list of applicants based on specified parameters
            $applicants = ApplicantAdmissionRequest::where('session_id', $sessionId)
                                                    ->where('is_admitted', 0)
                                                    ->where('is_submitted', 1)
                                                    ->orderBy('program_id','asc')
                                                    ->orderBy('form_number','asc')
                                                    ->get();
            #get the academic roles
            $staffRoles = getAcademicRoles(user()->id);
            #set the title of the page
            $title = "List of Applicants for ".getsessionById($sessionId)->name." Session";

            return view('admissions.viewVetoApplicantList',compact('applicants','title','staffRoles'));
        }



    }


    public function viewListForChangeAdmission(){


        if (user()->hasRole('admin|dean_pg|dean|hod|reg_officer|acad_eo')) {
            $sessionId = getApplicationSession();

            #get the list of applicants based on specified parameters
            $applicants = ApplicantAdmissionRequest::where('session_id', $sessionId)
                                                    ->where('is_admitted', 0)
                                                    ->where('is_submitted', 1)
                                                    ->orderBy('program_id','asc')
                                                    ->orderBy('form_number','asc')
                                                    ->get();
            #get the academic roles
            $staffRoles = getAcademicRoles(user()->id);
            #set the title of the page
            $title = "Change of Programme Admission Applicant List for ".getsessionById($sessionId)->name." Session";

            return view('admissions.viewChangeBeforeAdmissionList',compact('applicants','title','staffRoles'));
        }



    }


    public function previewBeforeChangeAdmission($id){
        #get the applciant
        $applicant = ApplicantAdmissionRequest::where('uid', $id)->first();

        if ($applicant) {
            # applicant found
            # get the staff roles just in case you need it later
            $staffRoles = getAcademicRoles(user()->id);
            #set the title
            $title = "Change Candidate Programme and Approve Admission ";
            #return the view
            return view('admissions.previewBeforeChangeAdmission',compact('applicant','title','staffRoles'));
        }
    }

    public function processChangeAdmission(Request $request){
        #first validate the entry
        $validated = $request->validate([
            'id' => 'required',
            'progId' => 'required|numeric'
        ]);

        #get the applicant

        $applicant = ApplicantAdmissionRequest::where('uid', $request->id)->first();
        #first change the course candidate has applied for;
        $applicant->program_id = $request->progId;
        #set appropriate parameters to continue
        $actionat = now();
        #next check the pg_coord admission status
        if ($applicant->pg_coord == 0) {
            #pg coordinator has not approved so procedd
            $applicant->pg_coord = 1;
            $applicant->pg_coord_at = $actionat;
            $applicant->pg_coord_by = user()->id;
        }

        if ($applicant->hod== 0) {
            # hod approval pending proceed to approve
            $applicant->hod = 1;
            $applicant->hod_at = $actionat;
            $applicant->hod_by = user()->id;
        }

        if ($applicant->dean == 0) {
            #dean has not approved
            $applicant->dean = 1;
            $applicant->dean_at = $actionat;
            $applicant->dean_by = user()->id;
        }

        if ($applicant->dean_spgs==0) {
            #dean spgs has not approved proceed with the rest
            $applicant->dean_spgs = 1;
            $applicant->dean_spgs_at = $actionat;
            $applicant->dean_spgs_by = user()->id;
        }
        #general admission of candidate
        $applicant->is_admitted = 1;
        $applicant->admitted_at = $actionat;
        $applicant->admitted_by = user()->id;
        $applicant->save();

        return back()->with('info', "Change of Programme and Admission Successfully Processed!!!");



        return $request;
    }


    public function vetoAdmission($id){

        #get the applicant
        $applicant = ApplicantAdmissionRequest::where('uid',$id)->first();
        $actionat = now();

        $applicant->pg_coord = 1;
        $applicant->pg_coord_at = $actionat;
        $applicant->pg_coord_by = user()->id;
        $applicant->hod = 1;
        $applicant->hod_at = $actionat;
        $applicant->hod_by = user()->id;
        $applicant->dean = 1;
        $applicant->dean_at = $actionat;
        $applicant->dean_by = user()->id;
        $applicant->dean_spgs = 1;
        $applicant->dean_spgs_at = $actionat;
        $applicant->dean_spgs_by = user()->id;
        $applicant->is_admitted = 1;
        $applicant->admitted_at = $actionat;
        $applicant->admitted_by = user()->id;
        $applicant->save();
        # assign admitted role;
        $apAdmit = User::find($applicant->user_id);
        $apAdmit->assignRole('admitted');

        return back()->with('info', "Veto Admission Successful!!!");

        return $applicant;

    }



    public function selectApplicantsForDownload(Request $request){


        if (user()->hasRole('admin|dean_pg|dean|hod|reg_officer|acad_eo')) {
            $validated = $request->validate([
                'c_prog' => 'required',
                'school_session' => 'required|numeric'
            ]);

            $sessionId = $request->school_session;
            $programId = $request->c_prog;
            $filename = $request->c_prog."_".$request->school_session."_applicants.xlsx";

            #extract the list
            return Excel::download(new ApplicantExport($sessionId, $programId), $filename);

            // #get the list of applicants based on specified parameters
            // $appList =  ApplicantAdmissionRequest::where('program_id', $request->c_prog)
            //                                 ->where('session_id', $request->school_session)
            //                                 ->get();

            // $newList = new Collection();
            // $sno=1;
            // foreach ($appList as $k) {

            //     $apUser = getUserById($k->user_id);
            //     $oLevel = OlevelResult::where('user_id', $apUser->id)->get();
            //     $qualf = UserQualification::where('user_id', $apUser->id)->get();
            //     #get the result details
            //     $result = '';
            //     foreach ($oLevel as $o) {
            //         //return $o->exam_details;
            //         $result = $result.$o->sitting."->".$o->exam_details['Exam_body']."-".$o->exam_details['Exam_type']."(".$o->exam_details['Exam_year'].")"."-[English-".$o->exam_details['English'].": ".$o->exam_details['subject_3']['subject_name']."-".$o->exam_details['subject_3']['grade'].": ".$o->exam_details['subject_4']['subject_name']."-".$o->exam_details['subject_4']['grade'].": ".$o->exam_details['subject_5']['subject_name']."-".$o->exam_details['subject_5']['grade']."] ";
            //     }
            //     #next sort out the qualifications
            //     $qualifications = '';
            //     foreach ($qualf as $q) {
            //         #return the qualification details
            //         $qualifications = $qualifications. $q->certificate_type."->".$q->qualification_obtained."(".$q->year_obtained.") ";
            //     }


            //     $newList->push((object)[
            //         'sno' => $sno,
            //         'formnumber' => $k->form_number,
            //         'name' => $apUser->name,
            //         'matricno' => $k->form_number,
            //         'gender' => $apUser->profile->gender,
            //         'state' => getStateNameById($apUser->profile->state_id),
            //         'program' => getProgramNameById($k->program_id),
            //         'Dept' => getProgrammeDetailById($k->program_id, 'all')->department->name,
            //         'country' => $apUser->profile->nationality,
            //         'olevel' => $result,
            //         'qualification' => $qualifications
            //     ]);

            //     $sno++;
            // }

            // return $newList;

        }



    }




    public function recommendSelectedApplicants(Request $request){

        //return $request;
        #The request is here, perform some variation and proceed to see the behaviour
        #find out who is taking an action and spread accross
        foreach ($request->regMonitor as $m) {
            $appId = $m;
            $actionBy = user()->id;
            $actionAt = now();
            $as = $request->approveAs;
            $actionToTake = $request->action;

            AdmissionRecommendationJob::dispatch($appId, $actionBy, $actionAt, $as, $actionToTake);


        }

        return redirect(route('select.admission.applicants'))->with('info', "Candidates recommended successfully!!!");

    }


    public function notifyCandiates(){


        $toNotify = ApplicantAdmissionRequest::where('is_admitted', 1)
                                            ->where('adm_notification',0)
                                            ->get();
        if ($toNotify) {
            #notifications found loop through
            foreach ($toNotify as $v) {
                #forward the job for those users
                AdmissionNotificationJob::dispatch($v->id);
            }
        }

        return redirect(route('home'))->with('info', "Mail Sending Successful");
    }




}
