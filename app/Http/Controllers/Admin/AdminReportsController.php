<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\UserTraits;
use App\Imports\carryOverImport;
use App\Imports\StudentOldResultImport;
use App\Models\AuthorizedEmail;
use App\Models\Curriculum;
use App\Models\FeeConfig;
use App\Models\FeePayment;
use App\Models\RegMonitor;
use App\Models\StudentMigration;
use App\Models\StudentRecord;
use App\Models\User;
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
            Excel::import(new carryOverImport, $grades);

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
            Excel::import(new StudentOldResultImport, $grades);

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


            if ($student = User::where('username', $request->oldmatric)->first()) {

                $student->username = $request->newmatric;
                $student->password = Hash::make($request->newmatric);
                $student->save();

                //get the student record and update matric number alone

                if ($studetnAcad = StudentRecord::where('matric', $request->oldmatric)->first()) {

                    $studetnAcad->matric = $request->newmatric;
                    $studetnAcad->save();

                    return back()->with('info', "Matric Number and Login info Updated Successfully!!!");

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
