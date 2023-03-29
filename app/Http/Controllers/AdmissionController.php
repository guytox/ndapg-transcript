<?php

namespace App\Http\Controllers;

use App\Imports\AdmissionOfferImport;
use App\Jobs\AdmissionRecommendationJob;
use App\Models\Admission;
use App\Models\ApplicantAdmissionRequest;
use App\Models\Program;
use App\Models\RegClearance;
use App\Models\StudentRecord;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Traits\HasRoles;

class AdmissionController extends Controller
{
    use HasRoles;

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




}
