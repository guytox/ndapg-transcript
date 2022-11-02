<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\UserTraits;
use App\Imports\carryOverImport;
use App\Imports\StudentOldResultImport;
use App\Models\Admission;
use App\Models\AuthorizedEmail;
use App\Models\Curriculum;
use App\Models\FeeConfig;
use App\Models\FeePayment;
use App\Models\Program;
use App\Models\RegMonitor;
use App\Models\StudentMigration;
use App\Models\StudentRecord;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class AdminReportsController extends Controller
{


    public function viewEmails()
    {

        $allEmails = AuthorizedEmail::all();

        //return $allEmails;

        $assignedEmails = [];

        foreach ($allEmails as $v) {

            $studentId = $this->getStudentIdByEmail($v->email);

            if ($studentId !='') {

                    $assignedEmails[] = collect([

                        'emailAddress' => $v->email,
                        'StudentMatricNo' => $studentId ? $this->getStudentMatricById($studentId) : null,
                        'StudentName' => $studentId ? $this->getUserNameById($studentId) : null,
                        'StudentProgramme' => $studentId ? $this->getStudentProgrammeNameById($studentId) : null,
                        'StudentCurrentLevel' => $studentId ? $this->getStudentCurrentLevelById($studentId) : null,
                        'StudentPhone' => $studentId ? $this->getStudentPhoneNumberById($studentId) : null,

                    ]);

            }else{

                $assignedEmails[] = collect([

                    'emailAddress' => $v->email,
                    'StudentMatricNo' =>  null,
                    'StudentName' =>  null,
                    'StudentProgramme' =>  null,
                    'StudentCurrentLevel' =>  null,
                    'StudentPhone' => null,

                ]);



            }

        }




        //return $PymntType;
        return view('admin.view-student-emails', compact('assignedEmails'));
    }

    public function revokeEmail(Request $request)
    {

        //return $request->pr;

        $assigned = User::where('email', $request->pr)->first();

        $assigned->update([
            'email' => null
        ]);

        return redirect()->route('view.emails');
    }



    public function viewStudentsList()
    {

        $students = User::where('program_id', '!=', null)->get();
        $studentsList = [];


        //return $students;

        foreach ($students as $v) {

            $studentBalances = $this->getStudentBalancesById($v->id);
            $studentDiscounts = $studentBalances[0]['totalScholarshipDiscounts'] + $studentBalances[0]['totalNkstDiscounts'] + $studentBalances[0]['totalDiscount'] + $studentBalances[0]['totalOtherDiscounts'];

            $student = getUser($v->id);
            $balance = $student->balance;

            $studentsList[] = collect([

                'id' => $v->id,
                'StudentMatricNo' => $this->getStudentMatricById($v->id),
                'StudentName' => $this->getUserNameById($v->id),
                'StudentProgramme' => $this->getStudentsProgrammeNameById($v->id),
                'StudentCurrentLevel' => strval($this->getStudentCurrentLevelById($v->id). 'Level'),
                'StudentTotalBill' => number_format(convertToNaira($studentBalances[0]['totalBilled']), 2),
                'StudentTotalDiscount' => number_format(convertToNaira($studentDiscounts), 2),
                'StudentTotalPaid' => number_format(convertToNaira($studentBalances[0]['totalPayments']),2),
                'StudentBAlance' => number_format(convertToNaira($studentBalances[0]['totalBalance']),2),
                'StudentEscessFee' => number_format(convertToNaira($balance),2),

            ]);
        }




        //return $studentsList;
        return view('admin.view-students-list', compact('studentsList'));
    }

    public function getStudentLedger($studentId)
    {

        $student = User::with('wallet')->findOrFail($studentId);

        //$student->withdraw(intval('2'), ['description' => 'Initial Deposit']);

        $transactions = FeePayment::where('user_id', $student->id)->with('paymentLogs')->with('configuration')->get();

        //return $transactions;

        return view('admin.student-ledger', compact('transactions', 'student'));
    }

    public function updateStudentsImportWallet(){
        $transactions = FeePayment::where('payment_config_id', '1')->with('user')->get();


        foreach ($transactions as $v ) {
            if ($v->excess_fee >0) {

                if ($v->user->wallet->balance <> $v->excess_fee) {

                    //find the payemnt monitor
                    $monitor = FeePayment::find($v->id);
                    $v->user->wallet->deposit($v->excess_fee);

                    //set the excess fee to zero after depositing money into the user's account
                    $monitor->excess_fee = 0;
                    $monitor->save();

                }



            }

        }

        //return $show;

        return redirect(route('view.students'))->with('success', "Student Balances Successfully Uploaded");
    }


    public function correctStudentPrograms(){

        if (user()->hasRole('admin')) {
            $stds = StudentRecord::where('program_id','')->get();

            foreach ($stds as $key => $v) {
                //get the std instance
                $student = StudentRecord::find($v->id);

                //get the user instance
                $userId = User::find($student->user_id);

                //perform update
                $student->program_id = $userId->program_id;
                $student->save();
            }
        }
        return back()->with('success',"Programs Corrected Successfully");
    }

    public function initiateStudentResults(){

        if (user()->hasRole('admin')) {

            //get the first semester curriculum_id

            $firstSemConfig = Curriculum::where('semester',1)->where('title','First Semester Integration')->first();
            $secondSemConfig = Curriculum::where('semester',2)->where('title','Second Semester Integration')->first();


            //get all students on the migration table for the first session
            $stds = StudentMigration::where('acadmic_session', 1)->get();

            //return $stds;

            foreach ($stds as $key => $v) {

                //get the std instance
                $student = StudentRecord::find($v->student_id);

                //prepare data for upload of first semester

                $details = [
                    'student_id' => $v->student_id,
                    'semester_id'=> $firstSemConfig->semester,
                    'curricula_id' => $firstSemConfig->id,
                    'session_id' => 1,
                    'level_id' => getStudyLevelIdByName($v->old_level),
                    'program_id' => $student->program_id,
                    'semesters_spent' => 1,
                    'std_confirm' => "1",
                    'ro_approval' => "1",
                    'hod_approval' => "1",
                    'dean_approval' => "1",
                    'uid' => uniqid('crf_'),

                ];

                $newMonitor = RegMonitor::upsert($details, $uniqueBy =['student_id','curricula_id','session_id'], $update=[

                    'semester_id',
                    'session_id',
                    'std_confirm',
                    'ro_approval',
                    'hod_approval',
                    'dean_approval',

                ]);


                //prepare data for upload of second semester records

                $details = [
                    'student_id' => $v->student_id,
                    'semester_id'=> $secondSemConfig->semester,
                    'curricula_id' => $secondSemConfig->id,
                    'session_id' => 1,
                    'level_id' => getStudyLevelIdByName($v->old_level),
                    'program_id' => $student->program_id,
                    'semesters_spent' => 2,
                    'std_confirm' => "1",
                    'ro_approval' => "1",
                    'hod_approval' => "1",
                    'dean_approval' => "1",
                    'uid' => uniqid('crf_'),

                ];

                $newMonitor = RegMonitor::upsert($details, $uniqueBy =['student_id','curricula_id','session_id'], $update=[

                    'semester_id',
                    'session_id',
                    'std_confirm',
                    'ro_approval',
                    'hod_approval',
                    'dean_approval',

                ]);



            }
        }
        return back()->with('success',"Programs All Intial Entries recorded Successfully !!!");
    }


    public function uploadCarryOvers(Request $request){

        if (user()->hasRole('admin')) {

            //validate input and perform import while posting to job
            $validated = $request->validate([
                'file' => 'required|mimes:xlsx|max:3048',
            ]);

            $grades = $request->file('file');

            //fire the import at this time
            //Excel::import(new carryOverImport, $grades);

            return back()->with('success', "Carry Over Successfully uploaded");

        }else {
            return "you are not admin";
        }

    }

    public function uploadOldResults(Request $request){

        if (user()->hasRole('admin')) {

            //validate input and perform import while posting to job
            $validated = $request->validate([
                'file' => 'required|mimes:xlsx|max:3048',
            ]);

            $grades = $request->file('file');

            //fire the import at this time
            //Excel::import(new StudentOldResultImport, $grades);

            return back()->with('success', "Student Old Results Uploaded Successfully");

        }else {
            return "you are not admin";
        }

    }

    public function updateMatricNo(Request $request){

        //validate input and perform import while posting to job
        $validated = $request->validate([
            'oldmatric' => 'required',
            'newmatric' => 'required',
        ]);



        if (user()->hasRole('admin')) {
            // compare the entries
            if ($request->oldmatric === $request->newmatric) {
                return back()->with('error', "Matric Number thesame, No Changes");
            }
            //get the user and change the username and password

            $message = "";
            if ($student = User::where('username', $request->oldmatric)->first()) {

                $student->username = $request->newmatric;
                $student->password = Hash::make($request->newmatric);
                $student->save();

                $message = $message . " User Records Updated Successfully and ";


                //track the new admission details and update appropriately

                if ($StudentAdmission = Admission::where('matric_number', $request->oldmatric)->first()) {

                    $StudentAdmission->matric_number = $request->newmatric;
                    $StudentAdmission->save();

                    $message = $message . " Admission Records Updated Successfully and ";

                }else{

                    $message = "Unable to correct Admission Records and ";

                }



                //get the student record and update matric number alone

                if ($studetnAcad = StudentRecord::where('matric', $request->oldmatric)->first()) {

                    $studetnAcad->matric = $request->newmatric;
                    $studetnAcad->save();

                    $message = $message . " Student Records Updated Successfully!!!";

                    return back()->with('info', $message);

                }else{
                    return back()->with('error', "There is a Problem, Student Record Not uplated");
                }

                return $student->username. "-".$request->oldmatric."-" .$request->newmatric;

            }else{

                return back()->with('error', "User not found");

            }

            return $request;

        }else{
            return back()->with('error', "You do not have the required privileges");
        }
    }


    public function changeOfProgramme(Request $request){

        //validate input and perform import while posting to job
        $validated = $request->validate([
            'studentmatric' => 'required',
            'newprogramme' => 'required',
        ]);

        //return $request;



        if (user()->hasRole('admin')) {
            // compare the the old and new programme
            $student = StudentRecord::where('matric', $request->studentmatric)->first();

            if ($student->program_id == $request->newprogramme) {
                return back()->with('error', "Old and New Programme is the same, No Changes");
            }
            //get the user and change the current_level
            //return $student;

            //get the new programme
            $theNewProg = Program::find($request->newprogramme);

            $message = "";
            if ($student = User::where('username', $request->studentmatric)->first()) {

                $student->current_level = $theNewProg->level_id;
                $student->save();

                $message = $message . " Current Level Updated Successfully and ";


                //track the new admission details and update appropriately

                if ($StudentAdmission = Admission::where('matric_number', $request->studentmatric)->first()) {

                    $StudentAdmission->programme = $theNewProg->name;
                    $StudentAdmission->programme_id = $theNewProg->id;
                    $StudentAdmission->save();

                    $message = $message . " Admission Records Updated Successfully and ";

                }else{

                    $message = "Unable to correct Admission Records and ";

                }

                //get Profile and change department ID
                if ($studentProfile = UserProfile::where('user_id', $student->id)->first()) {

                    $studentProfile->department_id = $theNewProg->department_id;
                    $studentProfile->save();

                    $message = $message . " Student Profile Updated Successfully and ";

                }else{

                    $message = "Unable to update Student Profile and ";

                }


                //get the student record and update matric number alone

                if ($studetnAcad = StudentRecord::where('matric', $request->studentmatric)->first()) {

                    $studetnAcad->program_id = $request->newprogramme;
                    $studetnAcad->save();

                    $message = $message . " Student Records Updated Successfully!!!";

                    return back()->with('info', $message);

                }else{
                    return back()->with('error', "There is a Problem, Student Record Not uplated");
                }

                return $student->username. "-".$request->oldmatric."-" .$request->newmatric;

            }else{

                return back()->with('error', "User not found");

            }

            return $request;

        }else{
            return back()->with('error', "You do not have the required privileges");
        }
    }


    public function adminPasswordUpdate(Request $request){
        //validate input and perform import while posting to job
        $validated = $request->validate([
            'matric' => 'required',
            'newpass' => 'required',
            'confirmpass' => 'required',
        ]);



        if (user()->hasRole('admin|ict_admin')) {
            // compare the entries
            if ($request->newpass != $request->confirmpass) {
                return back()->with('error', "Error!!! Password Mismatch");
            }
            //get the user and change the username and password


            if ($student = User::where('username', $request->matric)->first()) {


                $student->password = Hash::make($request->newpass);
                $student->save();

                return back()->with('info', "Password Updated Successfully!!!");

            }else{

                return back()->with('error', "User not found");

            }

        }else{

            return back()->with('error', "You do not have the required privileges");
        }
    }


}
