<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\AcceptanceBillImport;
use App\Models\AcademicSession;
use App\Models\BursarsApprovalQueue;
use App\Models\FeeCategory;
use App\Models\FeeConfig;
use App\Models\FeePayment;
use App\Models\FeePaymentItem;
use App\Models\FeeTemplate;
use App\Models\FeeTemplateItem;
use App\Models\PaymentLog;
use App\Models\RegClearance;
use App\Models\StudentRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class BillingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {



        //get fee templates
        $feeTemplate = getPaymentTemplateByTypeName('General');

        $schoolSession = AcademicSession::where('id','>=', getActiveAcademicSessionId())->get()->pluck('name', 'id');
        // get fee categories
        $categories = FeeCategory::all()->pluck('category_name', 'id');



        //return $categories;

        return view('bursar.initiate-tuition-billing', compact('schoolSession', 'feeTemplate', 'categories'));

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

        //dd($request);

        $validated = $request->validate([
            'fee_template_id' => 'present',
            'fee_category_id' => 'required|integer',
            'school_session' => 'required|string',
            'study_level' => 'present',
            'program_id' => 'present',
            'semester' => 'required',
            'user_id' => 'present',
        ]);

        // Check payment parameters to determine action

        //return $request;

        switch(getFeeCategoryName($request->fee_category_id)){

            case('tuition'):

                $cumStudents=0;
                $cumBilled = 0;
                $cumSkipped =0;

                //get a list of all students
                $studentList = $this->getListOfStudentsBillableStudents($request->program_id, $request->study_level, $request->user_id);

                //return $studentList;





                //search for the appropriate template based on config
                $billingDetails = [];

                foreach ($studentList as $v) {



                    $cumStudents ++;

                    //get the config to use
                    $configInfo = $this->getPaymentFeeConfigId($request->fee_category_id, getStudentCurrentLevelIdByUserId($v->user_id), getStudentProgrammeIdByUserId($v->user_id), $request->semester, getInStateStatusByStudentUserId($v->user_id));



                    //if found, check if there's a payment for it, then skip the entry

                    $duplicateCheck = $this->checkDuplicatePayment($v->user_id,$request->fee_category_id,$request->school_session);

                    if ($configInfo[0]['fee_config_id']=="Fee Not Configured") {

                        $tx_status = "No Fee Config Found";

                    }elseif ($configInfo[0]['fee_config_id']>0 ) {

                        $tx_status = "Billed Successfully";

                    }else{
                        $tx_status = "Not Billed";
                    }

                   $studentDetails [] = collect([
                        'user_id' => $v->user_id,
                        'fee_category_id' => $request->fee_category_id,
                        'fee_config_id' => $configInfo[0]['fee_config_id'],
                        'academic_session_id' => $request->school_session,
                        'purpose' => 'tuition',
                        'academic_semester_id' => $request->semester,
                        'duplicate_check' => $duplicateCheck,
                        'proposed_amount' => $configInfo[0]['fee_config_amount'],
                        'billed_by' => user()->id,
                        'status' => $tx_status,
                    ]);


                    if ($configInfo[0]['fee_config_amount']==0) {
                        // Skip this entry and do not enter it into the database

                        $cumSkipped ++;

                    }elseif ($configInfo[0]['fee_config_amount']>0 && $duplicateCheck == false) {

                        $cumBilled ++;

                        // check if the student has been billed and proceed
                        $newEntry = BursarsApprovalQueue::updateOrCreate(['user_id' => $v->user_id, 'fee_config_id' => $configInfo[0]['fee_config_id'] ],[

                            'user_id' => $v->user_id,
                            'fee_config_id' => $configInfo[0]['fee_config_id'],
                            'academic_session_id' => $request->school_session,
                            'proposed_amount' => $configInfo[0]['fee_config_amount'],
                            'billed_by' => user()->id,
                        ]);


                        //next bill the student for ICT Charges

                        $ictPayConfig = $this->getIctPaymentFeeConfigId(getStudentCurrentLevelIdByUserId($v->user_id), getStudentProgrammeIdByUserId($v->user_id), $request->semester);

                        if ($ictPayConfig[0]['fee_config_id']=='Fee Not Configured') {
                            //record fee not configured in the collection

                            $studentDetails [] = collect([
                                'user_id' => $v->user_id,
                                'fee_category_id' => $request->fee_category_id,
                                'fee_config_id' => $ictPayConfig[0]['fee_config_id'],
                                'academic_session_id' => $request->school_session,
                                'purpose' => 'Portal Charges',
                                'academic_semester_id' => $request->semester,
                                'duplicate_check' => $duplicateCheck,
                                'proposed_amount' => $ictPayConfig[0]['fee_config_amount'],
                                'billed_by' => user()->id,
                                'status' => 'No Fee Config Found',
                            ]);


                        }else {
                            //get Internet Charges config
                            $portalCharge = FeeConfig::join('fee_categories as f', 'f.id','=','fee_configs.fee_category_id')
                            ->join('fee_templates as t','t.id','=','fee_configs.fee_template_id')
                            ->where('f.category_name','portal_services')
                            ->where('fee_configs.id',$ictPayConfig[0]['fee_config_id'])
                            ->select('fee_configs.*', 't.total_amount')
                            ->first();

                            $duplicateCheck2 = $this->checkDuplicatePayment($v->user_id, $ictPayConfig[0]['fee_config_id'],$request->school_session);

                            if ($duplicateCheck2) {
                                //Skip, entry exists
                            }else{
                                //entry not found, create

                                $studentDetails [] = collect([
                                    'user_id' => $v->user_id,
                                    'fee_category_id' => $portalCharge->fee_category_id,
                                    'fee_config_id' => $portalCharge->id,
                                    'academic_session_id' => $request->school_session,
                                    'academic_semester_id' => $request->semester,
                                    'purpose' => 'Portal Charges',
                                    'duplicate_check' => $duplicateCheck,
                                    'proposed_amount' => $portalCharge->total_amount,
                                    'billed_by' => user()->id,
                                    'status' => $tx_status,
                                ]);

                                $newEntry2 = BursarsApprovalQueue::updateOrCreate(['user_id' => $v->user_id, 'fee_config_id' => $portalCharge->id ],[

                                    'user_id' => $v->user_id,
                                    'fee_config_id' => $portalCharge->id,
                                    'academic_session_id' => $request->school_session,
                                    'proposed_amount' => $portalCharge->total_amount,
                                    'billed_by' => user()->id,
                                ]);

                            }
                        }







                    }



                }



                //check if student is already billed and skip
                //write to table
                //return report for admin to view
                //produce show page
                $billingSummary = [
                    'totalStudents' => $cumStudents,
                    'totalBilledStudents' => $cumBilled,
                    'totalExemptedStudents' => $cumSkipped
                ];



                //return "Total Students =". $cumStudents. "Total Billed =".$cumBilled." Total Skipped =".$cumSkipped;
                //return dd($studentDetails);

                return view('bursar.view-billing-proposal', compact('studentDetails','billingSummary'));



            break;

            case('other_charges'):
                return "You have Selected Other Charges";
            break;

            case('acceptance_fees'):
                return "You have Selected Other Charges";
            break;

        }

        return "Selected Not found";
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


    public function getBillingForConfirmations(){

        //check bursary or bursar role
        if (user()->hasRole('upload_payment|admin')) {
            //get the list of canditates for
            $billingDetails = BursarsApprovalQueue::where('billed_by', user()->id)
                                                    ->where('bill_confirmed','0')
                                                    ->get();

            $studentDetails = [];
            $totalBill =0;
            $billedStudents =0;


            foreach ($billingDetails as $v) {

                //get the fee config

                $feeconfig = FeeConfig::find($v->fee_config_id);

                $studentDetails [] = collect([
                    'bill_id' =>$v->id,
                    'user_id' => $v->user_id,
                    'fee_category_id' => $feeconfig->fee_category_id,
                    'fee_config_id' => $v->fee_config_id,
                    'academic_session_id' => $v->academic_session_id,
                    'academic_semester_id' => $feeconfig->semester_id,
                    'purpose' => getPaymentCategoryPurposeById($feeconfig->fee_category_id),
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

            return view('bursar.check-billing-proposal', compact('billingSummary','studentDetails'));

        }

        return back()->with('error',"You do not have permissions to view this page");

    }

    public function getBillingForChecking(){

        //check bursary or bursar role
        if (user()->hasRole('check_payment|admin')) {
            //get the list of canditates for
            $billingDetails = BursarsApprovalQueue::where('bill_confirmed','1')
                                                    ->where('bill_checked','0')
                                                    ->get();

            $studentDetails = [];
            $totalBill =0;
            $billedStudents =0;


            foreach ($billingDetails as $v) {

                //get the fee config

                $feeconfig = FeeConfig::find($v->fee_config_id);

                $studentDetails [] = collect([
                    'bill_id' =>$v->id,
                    'user_id' => $v->user_id,
                    'fee_category_id' => $feeconfig->fee_category_id,
                    'fee_config_id' => $v->fee_config_id,
                    'academic_session_id' => $v->academic_session_id,
                    'academic_semester_id' => $feeconfig->semester_id,
                    'purpose' => getPaymentCategoryPurposeById($feeconfig->fee_category_id),
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

            return view('bursar.check-billing-proposal', compact('billingSummary','studentDetails'));

        }

        return back()->with('error',"You do not have permissions to view this page");

    }

    public function getBillingForApproval(){

        //check bursary or bursar role
        if (user()->hasRole('bursar|admin')) {
            //get the list of canditates for
            $billingDetails = BursarsApprovalQueue::where('bill_confirmed','1')
                                                    ->where('bill_checked','1')
                                                    ->where('bill_approved','0')
                                                    ->get();

            $studentDetails = [];
            $totalBill =0;
            $billedStudents =0;


            foreach ($billingDetails as $v) {

                //get the fee config

                $feeconfig = FeeConfig::find($v->fee_config_id);

                $studentDetails [] = collect([
                    'bill_id' =>$v->id,
                    'user_id' => $v->user_id,
                    'fee_category_id' => $feeconfig->fee_category_id,
                    'fee_config_id' => $v->fee_config_id,
                    'academic_session_id' => $v->academic_session_id,
                    'academic_semester_id' => $feeconfig->semester_id,
                    'purpose' => getPaymentCategoryPurposeById($feeconfig->fee_category_id),
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

            return view('bursar.check-billing-proposal', compact('billingSummary','studentDetails'));

        }

        return back()->with('error',"You do not have permissions to view this page");

    }

    public function initiateAcceptanceBilling(Request $request){


        if (user()->hasRole('admin|bursar|upload_payment')) {

            //validate input and perform import while posting to job
            $validated = $request->validate([
                'file' => 'required|mimes:xlsx|max:3048',
            ]);

            $studentsToBill = $request->file('file');

            //fire the import at this time
            Excel::import(new AcceptanceBillImport, $studentsToBill);

            return redirect(route('billing.confirmation'))->with('success', "Carry Over Successfully uploaded");

        }else {
            return "you are not admin";
        }


        return "we are here";


    }



    public function getApprovedBilling(){

        //check bursary or bursar role
        if (user()->hasRole('bursar|admin')) {
            //get the list of canditates for
            $billingDetails = BursarsApprovalQueue::where('bill_confirmed','1')
                                                    ->where('bill_checked','1')
                                                    ->where('bill_approved','1')
                                                    ->get();

            $studentDetails = [];
            $totalBill =0;
            $billedStudents =0;


            foreach ($billingDetails as $v) {

                //get the fee config

                $feeconfig = FeeConfig::find($v->fee_config_id);

                $studentDetails [] = collect([
                    'bill_id' =>$v->id,
                    'user_id' => $v->user_id,
                    'fee_category_id' => $feeconfig->fee_category_id,
                    'fee_config_id' => $v->fee_config_id,
                    'academic_session_id' => $v->academic_session_id,
                    'academic_semester_id' => $feeconfig->semester_id,
                    'purpose' => getPaymentCategoryPurposeById($feeconfig->fee_category_id),
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

            return view('bursar.view-approved-billing', compact('billingSummary','studentDetails'));

        }

        return back()->with('error',"You do not have permissions to view this page");

    }

    public function deleteProposal($id){
        if (user()->hasRole('upolad_payment|admin')) {
            $todelete = BursarsApprovalQueue::find($id);
            $todelete->delete();
            return back()->with('error',"Record deleted Successfully !!!!");
        }
    }

    public function reverseProposal($id){
        if (user()->hasRole('bursar|admin')) {
            $reverse = BursarsApprovalQueue::find($id);
            $reverse->bill_confirmed = '0';
            $reverse->bill_checked = '0';
            $reverse->save();
            return back()->with('error',"Record Disapproved Successfully !!!!");
        }
    }

    public function confirmBilling(){
        if (user()->hasRole('upload_payment|admin')) {
            //find all pending bills for confirmation

            $toConfirm = BursarsApprovalQueue::where('bill_confirmed', '0')
                                            ->where('billed_by',user()->id)
                                            ->get();

            foreach ($toConfirm as $k) {
                $confirmed = BursarsApprovalQueue::find($k->id);
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

            $toConfirm = BursarsApprovalQueue::where('bill_confirmed', '1')
                                            ->where('bill_checked', '0')
                                            ->get();

            foreach ($toConfirm as $k) {
                $confirmed = BursarsApprovalQueue::find($k->id);
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

            $toConfirm = BursarsApprovalQueue::where('bill_confirmed', '1')
                                            ->where('bill_checked', '1')
                                            ->where('bill_approved', '0')
                                            ->get();
            //return $toConfirm;

            foreach ($toConfirm as $k) {


                $confirmed = BursarsApprovalQueue::find($k->id);
                $confirmed->approved_by = user()->id;
                $confirmed->bill_approved = '1';
                $confirmed->approved_at = now();
                $confirmed->save();

                 //next update insert the bill into the fee-payment table



                $paymentEntry = FeePayment::updateOrCreate(['user_id' => $confirmed->user_id, 'payment_config_id'=>$confirmed->fee_config_id, 'academic_session_id'=>$confirmed->academic_session_id], [
                    'user_id' => $confirmed->user_id,
                    'payment_config_id' => $confirmed->fee_config_id,
                    'uid' => uniqid('fp_'),
                    'academic_session_id' => $confirmed->academic_session_id,
                    'txn_id' => generateUniqueTransactionReference(),
                    'amount_billed' => $confirmed->proposed_amount,
                    'balance' => $confirmed->proposed_amount,
                    'billing_by' => $confirmed->billed_by,
                    'checked_by' => $confirmed->checked_by,
                    'approved_by' => $confirmed->approved_by,
                    'billing_at' => $confirmed->confirmed_at,
                    'checked_at' => $confirmed->checked_at,
                    'approved_at' => $confirmed->approved_at,

                ]);

                //next check the balance from the wallet and enter payment log as neccessary
                if ($paymentEntry) {

                    //enter the payment_items table with the information
                    //get the fee_template_items for populating the receipt
                    $template = getTemplateFromConfigId($paymentEntry->payment_config_id);
                    $templateItems = FeeTemplateItem::where('fee_template_id', $template->id)->get();

                    foreach ($templateItems as $v) {
                        $dataset = [
                            'fee_payment_id' => $paymentEntry->id,
                            'fee_item_id' => $v['fee_item_id'],
                            'amount' => $v['item_amount'],
                        ];

                        $itemEntry = FeePaymentItem::updateOrCreate(['fee_payment_id' => $paymentEntry->id, 'fee_item_id' => $v['fee_item_id']], $dataset);


                    }

                    //check the wallet balance for the student
                    $user = User::find($paymentEntry->user_id);


                    //if balance is 0 then ignore
                    //if balance is more than billed amount, then take the total figure and flag the payment to paid
                    // if the balance is less than the billed, then take all the balance and compute the left over

                    if ($user->balance ==0) {

                    }elseif ($user->balance > $paymentEntry->amount_billed) {

                        $balance = abs($paymentEntry->balance) - $paymentEntry->amount_billed;
                        $totalAmountPaid = abs($paymentEntry->amount_paid) + $paymentEntry->amount_billed;
                        $status = $balance === 0 ? 'paid' : 'pending';
                        $paymentEntry->balance = $balance;
                        $paymentEntry->amount_paid = $totalAmountPaid;
                        $paymentEntry->payment_status = $status;
                        $paymentEntry->txn_id = generateUniqueTransactionReference();
                        $paymentEntry->save();


                        PaymentLog::create([
                            'fee_payment_id' => $paymentEntry->id,
                            'amount_paid' => $paymentEntry->amount_billed,
                            'uid' => uniqid('pl_'),
                            'payment_channel' => config('app.payment_methods.umm-wallet'),
                            'description' => 'Student Wallet Payment' . ' - ' . config('app.payment_methods.umm-wallet')
                        ]);

                        $user->withdraw($paymentEntry->amount_billed);

                        //allow Registration for the student for that specified semester
                        //get the semester
                        $feeconfig = FeeConfig::find($paymentEntry->payment_config_id);


                        if ($feeconfig->account == 'school') {

                            $semesterId = $feeconfig->semester_id;
                            $studentId = getStudentIdByUserId($paymentEntry->user_id);
                            $sessionId = $paymentEntry->academic_session_id;
                            updateRegClearance($studentId,$sessionId,$semesterId);
                        }




                    }elseif ($user->balance < $paymentEntry->amount_billed) {

                        $amount = $user->balance;

                        $balance = abs($paymentEntry->balance) - $amount;
                        $totalAmountPaid = abs($paymentEntry->amount_paid) + $amount;
                        $status = $balance === 0 ? 'paid' : 'pending';
                        $paymentEntry->balance = $balance;
                        $paymentEntry->amount_paid = $totalAmountPaid;
                        $paymentEntry->payment_status = $status;
                        $paymentEntry->txn_id = generateUniqueTransactionReference();
                        $paymentEntry->save();


                        PaymentLog::create([
                            'fee_payment_id' => $paymentEntry->id,
                            'amount_paid' => $amount,
                            'uid' => uniqid('pl_'),
                            'payment_channel' => config('app.payment_methods.umm-wallet'),
                            'description' => 'Student Wallet Payment' . ' - ' . config('app.payment_methods.umm-wallet')
                        ]);

                        $user->withdraw($amount);

                        //allow Registration for the student for that specified semester
                        //get the semester
                        $feeconfig = FeeConfig::find($paymentEntry->payment_config_id);

                        if ($feeconfig->account == 'school') {
                            $semesterId = $feeconfig->semester_id;
                            $studentId = getStudentIdByUserId($paymentEntry->user_id);
                            $sessionId = $paymentEntry->academic_session_id;
                            updateRegClearance($studentId,$sessionId,$semesterId);
                        }

                        updateRegClearance($studentId,$sessionId,$semesterId);
                    }



                }

            }

            return back()->with('success', "Payments Proposal Checked Successfuly, inform the Bursar to approve");
        }
    }




    public function getListOfStudentsBillableStudents($programmeId, $studyLevel, $userId){

        $query = StudentRecord::join('users', 'users.id', '=', 'student_records.user_id')
                                            ->where('in_defferment', false)
                                            ->where('is_suspended', false)
                                            ->where('has_graduated', false)
                                            ->where('is_disabled', false)
                                            ->with('user');
        if ($programmeId=='') {
            //
        }else{
            $query->where('users.program_id', $programmeId);
        }

        if ($studyLevel=='') {
            //
        }else{

            $query->where('users.current_level', getStudyLevelNameById($studyLevel));
        }

        if ($userId=='') {
            //
        }else{
            $query->where('matric', $userId);
        }

        $students = $query->get();

        return $students;

    }

    public function getPaymentFeeConfigId($feeCatetory_id, $currentLevel, $programme_id, $semster, $instateValue){

        $details = [];

        $configDetails = FeeConfig::where('fee_category_id', $feeCatetory_id)
                                    ->where('study_level_id', $currentLevel)
                                    ->where('program_id', $programme_id)
                                    ->where('semester_id', $semster)
                                    ->where('in_state', $instateValue)
                                    ->with('feeTemplate')
                                    ->first();

        if ($configDetails) {

            if ($configDetails->count()==0) {

                $details [] = collect([
                    'fee_config_id' => 'Fee Not Configured',
                    'fee_config_amount' => 0
                ]);

            }else {

                $details [] = collect([
                    'fee_config_id' => $configDetails->id,
                    'fee_config_amount' => $configDetails->feeTemplate->total_amount
                ]);

            }

        }else {

            $details [] = collect([
                'fee_config_id' => 'Fee Not Configured',
                'fee_config_amount' => 0
            ]);

        }

        return $details;

    }

    public function getIctPaymentFeeConfigId($currentLevel, $programme_id, $semster){

        $details = [];

        $feeCatetory_id =getfeeCategoryIdByCategoryName('portal_services');

        $configDetails = FeeConfig::where('fee_category_id', $feeCatetory_id)
                                    ->where('study_level_id', $currentLevel)
                                    ->where('program_id', $programme_id)
                                    ->where('semester_id', $semster)
                                    ->with('feeTemplate')
                                    ->first();

        if ($configDetails) {

            if ($configDetails->count()==0) {

                $details [] = collect([
                    'fee_config_id' => 'Fee Not Configured',
                    'fee_config_amount' => 0
                ]);

            }else {

                $details [] = collect([
                    'fee_config_id' => $configDetails->id,
                    'fee_config_amount' => $configDetails->feeTemplate->total_amount
                ]);

            }

        }else {

            //dd('Nothing Found');

            $details [] = collect([
                'fee_config_id' => 'Fee Not Configured',
                'fee_config_amount' => 0
            ]);

            return $details;

        }

        return $details;

    }



    public function checkDuplicatePayment($user_id, $payment_Config, $schoolSession){

        $duplicatePay = FeePayment::where('user_id', $user_id)
                                    ->where('payment_config_id', $payment_Config)
                                    ->where('academic_session_id', $schoolSession)
                                    ->get();

        if ($duplicatePay->count()>0) {

            return true;

        }else{

            return false;
        }
        return true;
    }



}
