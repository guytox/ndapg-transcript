<?php

namespace App\Http\Controllers;

use App\Imports\StudentScholarshipImport;
use App\Models\AcademicSession;
use App\Models\BursarScholarshipQueue;
use App\Models\BursarsScholarshipExcess;
use App\Models\FeeConfig;
use App\Models\FeePayment;
use App\Models\PaymentLog;
use App\Models\Scholarship;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ScholarshipProcessingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {


         $schoolSession = AcademicSession::where('id','>=', getActiveAcademicSessionId())->get()->pluck('name', 'id');
         // get fee categories
         $categories = Scholarship::all()->pluck('type', 'id');



         //return $categories;

         return view('bursar.initiate-scholarship-billing', compact('schoolSession', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if (user()->hasRole('admin|upload_payment')) {

            $validated = $request->validate([

                'category_id' => 'required|integer',
                'school_session' => 'required|string',
                'studentList' => 'required|mimes:xlsx',

            ]);

            //get parameters and pass into the scholarship import

            $sessionId = intval($request->school_session);
            $categoryId = $request->category_id;
            $staffId = user()->id;
            $studentList = $request->studentList;

            //check if scholarship exist before passing to upload

            $scholarship = Scholarship::find($categoryId);

            if ($scholarship) {
                Excel::import(new StudentScholarshipImport($sessionId,$categoryId,$staffId),$studentList);
            }else {
                return back()->with('error', "Error!!! Something went wrong");
            }


            return back()->with('info', "file successfully uploaded, check status of upload and confirm");
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }




    public function getScholarshipForConfirmations(){

        //check bursary or bursar role
        if (user()->hasRole('upload_payment|admin')) {
            //get the list of canditates for
            $billingDetails = BursarScholarshipQueue::where('billed_by', user()->id)
                                                    ->where('bill_confirmed','0')
                                                    ->get();

            $studentDetails = [];
            $totalBill =0;
            $billedStudents =0;


            foreach ($billingDetails as $v) {

                //get the fee config

                $feeconfig = FeePayment::join('fee_configs as c', 'c.id','=','fee_payments.payment_config_id')
                                                ->join('fee_categories as l','l.id','=','c.fee_category_id')
                                                ->select('fee_payments.*','c.fee_category_id', 'c.semester_id','l.category_name')
                                                ->find($v->fee_payment_id);

                $studentDetails [] = collect([
                    'bill_id' =>$v->id,
                    'user_id' => $v->user_id,
                    'fee_category_id' => $feeconfig->fee_category_id,
                    'fee_config_id' => $v->payment_config_id,
                    'academic_session_id' => $v->academic_session_id,
                    'academic_semester_id' => $feeconfig->semester_id,
                    'purpose' => getScholarshipNameById($v->scholarship_category),
                    'billed_amount' => $feeconfig->amount_billed,
                    'proposed_amount' => $v->proposed_amount,
                    'billed_by' => $v->billed_by,
                    'checked_by' => $v->checked_by,
                    'billed_at' => $v->created_at,
                    'checked_at' => $v->updated_at,
                ]);

                $totalBill = $totalBill + $v->proposed_amount;
                $billedStudents = $billedStudents + 1;
            }

            $billingSummary = [
                'totalBill' => $totalBill,
                'billedStudents' => $billedStudents
            ];

            return view('bursar.check-scholarship-proposal', compact('billingSummary','studentDetails'));

        }

        return back()->with('error',"You do not have permissions to view this page");

    }

    public function getScholarshipForChecking(){

        //check bursary or bursar role
        if (user()->hasRole('check_payment|admin')) {
            //get the list of canditates for

            $billingDetails = BursarScholarshipQueue::where('bill_confirmed','1')
                                                    ->where('bill_checked','0')
                                                    ->get();


            $studentDetails = [];
            $totalBill =0;
            $billedStudents =0;


            foreach ($billingDetails as $v) {

                //get the fee config


                $feeconfig = FeePayment::join('fee_configs as c', 'c.id','=','fee_payments.payment_config_id')
                                                ->join('fee_categories as l','l.id','=','c.fee_category_id')
                                                ->select('fee_payments.*','c.fee_category_id', 'c.semester_id','l.category_name')
                                                ->find($v->fee_payment_id);

                    $studentDetails [] = collect([
                        'bill_id' =>$v->id,
                        'user_id' => $v->user_id,
                        'fee_category_id' => $feeconfig->fee_category_id,
                        'fee_config_id' => $v->payment_config_id,
                        'academic_session_id' => $v->academic_session_id,
                        'academic_semester_id' => $feeconfig->semester_id,
                        'purpose' => getScholarshipNameById($v->scholarship_category),
                        'billed_amount' => $feeconfig->amount_billed,
                        'proposed_amount' => $v->proposed_amount,
                        'billed_by' => $v->billed_by,
                        'checked_by' => $v->checked_by,
                        'billed_at' => $v->created_at,
                        'checked_at' => $v->updated_at,
                    ]);

                $totalBill = $totalBill + $v->proposed_amount;
                $billedStudents = $billedStudents + 1;
            }

            $billingSummary = [
                'totalBill' => $totalBill,
                'billedStudents' => $billedStudents
            ];

            return view('bursar.check-scholarship-proposal', compact('billingSummary','studentDetails'));

        }

        return back()->with('error',"You do not have permissions to view this page");

    }


    public function getScholarshipForApproval(){

        //check bursary or bursar role
        if (user()->hasRole('bursar|admin')) {
            //get the list of canditates for

            $billingDetails = BursarScholarshipQueue::where('bill_confirmed','1')
                                                    ->where('bill_checked','1')
                                                    ->where('bill_approved','0')
                                                    ->get();


            $studentDetails = [];
            $totalBill =0;
            $billedStudents =0;


            foreach ($billingDetails as $v) {

                //get the fee config


                $feeconfig = FeePayment::join('fee_configs as c', 'c.id','=','fee_payments.payment_config_id')
                                                ->join('fee_categories as l','l.id','=','c.fee_category_id')
                                                ->select('fee_payments.*','c.fee_category_id', 'c.semester_id','l.category_name')
                                                ->find($v->fee_payment_id);

                    $studentDetails [] = collect([
                        'bill_id' =>$v->id,
                        'user_id' => $v->user_id,
                        'fee_category_id' => $feeconfig->fee_category_id,
                        'fee_config_id' => $v->payment_config_id,
                        'academic_session_id' => $v->academic_session_id,
                        'academic_semester_id' => $feeconfig->semester_id,
                        'purpose' => getScholarshipNameById($v->scholarship_category),
                        'billed_amount' => $feeconfig->amount_billed,
                        'proposed_amount' => $v->proposed_amount,
                        'billed_by' => $v->billed_by,
                        'checked_by' => $v->checked_by,
                        'billed_at' => $v->created_at,
                        'checked_at' => $v->updated_at,
                    ]);

                $totalBill = $totalBill + $v->proposed_amount;
                $billedStudents = $billedStudents + 1;
            }

            $billingSummary = [
                'totalBill' => $totalBill,
                'billedStudents' => $billedStudents
            ];

            return view('bursar.check-scholarship-proposal', compact('billingSummary','studentDetails'));

        }

        return back()->with('error',"You do not have permissions to view this page");

    }


    public function getApprovedScholarship(){

        //check bursary or bursar role
        if (user()->hasRole('bursar|admin')) {
            //get the list of canditates for

            $billingDetails = BursarScholarshipQueue::where('bill_confirmed','1')
                                                    ->where('bill_checked','1')
                                                    ->where('bill_approved','1')
                                                    ->get();


            $studentDetails = [];
            $totalBill =0;
            $billedStudents =0;


            foreach ($billingDetails as $v) {

                //get the fee config

                $feeconfig = FeePayment::join('fee_configs as c', 'c.id','=','fee_payments.payment_config_id')
                                                ->join('fee_categories as l','l.id','=','c.fee_category_id')
                                                ->select('fee_payments.*','c.fee_category_id', 'c.semester_id','l.category_name')
                                                ->find($v->fee_payment_id);

                    $studentDetails [] = collect([
                        'bill_id' =>$v->id,
                        'user_id' => $v->user_id,
                        'fee_category_id' => $feeconfig->fee_category_id,
                        'fee_config_id' => $v->payment_config_id,
                        'academic_session_id' => $v->academic_session_id,
                        'academic_semester_id' => $feeconfig->semester_id,
                        'purpose' => getScholarshipNameById($v->scholarship_category),
                        'billed_amount' => $feeconfig->amount_billed,
                        'proposed_amount' => $v->proposed_amount,
                        'billed_by' => $v->billed_by,
                        'checked_by' => $v->checked_by,
                        'billed_at' => $v->created_at,
                        'checked_at' => $v->updated_at,
                    ]);

                $totalBill = $totalBill + $v->proposed_amount;
                $billedStudents = $billedStudents + 1;
            }

            $billingSummary = [
                'totalBill' => $totalBill,
                'billedStudents' => $billedStudents
            ];

            return view('bursar.view-approved-scholarship', compact('billingSummary','studentDetails'));

        }

        return back()->with('error',"You do not have permissions to view this page");

    }


    public function deleteProposal($id){


        if (user()->hasRole('upload_payment|admin')) {
            $todelete = BursarScholarshipQueue::find($id);
            $todelete->delete();
            return back()->with('error',"Record deleted Successfully !!!!");
        }

        return back()->with('error', "Error!!!! User Permission Missing");
    }

    public function reverseProposal($id){


        if (user()->hasRole('bursar|admin|check_payment')) {

             $reverse = BursarScholarshipQueue::find($id);
            $reverse->bill_confirmed = '0';
            $reverse->bill_checked = '0';
            $reverse->save();
            return back()->with('error',"Record Disapproved Successfully !!!!");
        }

        return back()->with('error', "Error!!!! User Permission Missing");
    }


    public function confirmBilling(){
        if (user()->hasRole('upload_payment|admin')) {
            //find all pending bills for confirmation

            $toConfirm = BursarScholarshipQueue::where('bill_confirmed', '0')
                                            ->where('billed_by',user()->id)
                                            ->get();

            foreach ($toConfirm as $k) {
                $confirmed = BursarScholarshipQueue::find($k->id);
                $confirmed->bill_confirmed = '1';
                $confirmed->billed_by = user()->id;
                $confirmed->confirmed_at = now();
                $confirmed->save();
            }

            return back()->with('success', "Payments Proposal Confirmed Successfuly, inform the checker");
        }
    }



    public function checkBilling(){
        if (user()->hasRole('check_payment|admin')) {
            //find all pending bills for confirmation

            $toConfirm = BursarScholarshipQueue::where('bill_confirmed', '1')
                                            ->where('bill_checked', '0')
                                            ->get();

            foreach ($toConfirm as $k) {
                $confirmed = BursarScholarshipQueue::find($k->id);
                $confirmed->bill_checked = '1';
                $confirmed->checked_by = user()->id;
                $confirmed->checked_at = now();
                $confirmed->save();
            }

            return back()->with('success', "Payments Proposal Checked Successfuly, inform the Bursar to approve");
        }
    }




    public function approveBilling(){
        if (user()->hasRole('bursar|admin')) {
            //find all pending bills for confirmation

            $toConfirm = BursarScholarshipQueue::where('bill_confirmed', '1')
                                            ->where('bill_checked', '1')
                                            ->where('bill_approved', '0')
                                            ->get();
            //return $toConfirm;

            foreach ($toConfirm as $k) {


                $confirmed = BursarScholarshipQueue::find($k->id);
                $confirmed->approved_by = user()->id;
                $confirmed->bill_approved = '1';
                $confirmed->approved_at = now();
                $confirmed->save();

                 //next update insert the bill into the fee-payment table

                //find the payment entry for manipulation

                $paymentEntry = FeePayment::find($k->fee_payment_id);



                //next check the balance from the wallet and enter payment log as neccessary
                if ($paymentEntry) {
                    //enter the log

                    PaymentLog::create([
                        'fee_payment_id' => $paymentEntry->id,
                        'amount_paid' => $k->proposed_amount,
                        'uid' => uniqid('pl_'),
                        'payment_channel' => getScholarshipNameById($k->scholarship_category),
                        'description' => "Scholarship Discount"
                    ]);


                    //next update the amount paid

                    $totalPaid = 0;

                    $paidLogs = PaymentLog::where('fee_payment_id',$k->fee_payment_id);

                    foreach ($paidLogs as $py) {
                        $totalPaid = $totalPaid + $py->amount_paid;
                    }

                    $balance = $paymentEntry->amount_billed - $totalPaid;



                    switch (getScholarshipNameById($k->scholarship_category)) {
                        case 'nkst_discount':
                                $paymentEntry->amount_paid = $totalPaid;
                                $paymentEntry->balance = $balance;
                                $paymentEntry->discount_nkst = $k->proposed_amount;
                                $paymentEntry->save();

                            break;

                        case 'university_scholarship':
                                $paymentEntry->amount_paid = $totalPaid;
                                $paymentEntry->balance = $balance;
                                $paymentEntry->discount_scholarship = $k->proposed_amount;
                                $paymentEntry->save();
                            break;

                        default:
                                $paymentEntry->amount_paid = $totalPaid;
                                $paymentEntry->balance = $balance;
                                $paymentEntry->discount_other = $k->proposed_amount;
                                $paymentEntry->save();
                            break;
                    }


                    //allow Registration for the student for that specified semester
                        //get the semester
                        $feeconfig = FeeConfig::find($paymentEntry->payment_config_id);
                        $semesterId = $feeconfig->semester_id;
                        $studentId = getStudentIdByUserId($paymentEntry->user_id);
                        $sessionId = $paymentEntry->academic_session_id;

                        updateRegClearance($studentId,$sessionId,$semesterId);


                }

            }

            return back()->with('success', "Scholarship Proposal Approved  Successfuly !!!");
        }

        return back()->with('error', "Error!!!! User Permissions Missing !!!");

    }



    public function DeConfirmBilling(){
        if (user()->hasRole('bursar|admin')) {
            //find all pending bills for confirmation

            $toConfirm = BursarScholarshipQueue::where('bill_confirmed', '1')
                                            ->where('bill_checked', '1')
                                            ->where('bill_approved', '0')
                                            ->get();

            foreach ($toConfirm as $k) {
                $confirmed = BursarScholarshipQueue::find($k->id);
                $confirmed->bill_confirmed = '0';
                $confirmed->bill_checked = '0';
                $confirmed->checked_by = null;
                $confirmed->confirmed_at = null;
                $confirmed->checked_at = null;
                $confirmed->save();
            }

            return back()->with('success', "Payments Proposal Disapproved Successfuly, inform the checker");
        }
    }


    public function viewExcessReport(){
        if (user()->hasRole('bursar|admin')) {
            //find all Excess bills for viewing
            $allExcess = BursarsScholarshipExcess::all();
            $returnedExcess = [];

            foreach ($allExcess as $k) {
                $returnedExcess[] = collect([
                    'excess_id' => $k->id,
                    'user_id' => $k->user_id,
                    'matno' => getUserById($k->user_id)->username,
                    'name' => getUserById($k->user_id)->name,
                    'program' => getProgrammeNameById(getUserById($k->user_id)->program_id),
                    'level' => getUserById($k->user_id)->current_level,
                    'amount' => number_format(convertToNaira($k->scholarship_amount),2),
                    'scholarshipType' => getScholarshipNameById($k->scholarship_category),
                    'schoolSession' => getsessionById($k->school_session)->name,
                    'uploadedBy' => getUserById($k->uploaded_by)->name
                ]);
            }

            return view('bursar.view-scholarship-excess-report',compact('returnedExcess'));

        }



    }

    public function deleteExcessScholarship($id){
        if (user()->hasRole('bursar|upload_payment|admin')) {
            //find all pending bills for confirmation

            $todelete = BursarsScholarshipExcess::find($id);

            $todelete->delete();

            return back()->with('success', "Excess Payment deleted Successfully!!!, ");
        }
    }




}
