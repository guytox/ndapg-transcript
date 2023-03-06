<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeePayment;
use App\Models\Program;
use App\Http\Traits\UserTraits;
use App\Models\AcademicSession;
use App\Models\FeeCategory;
use App\Models\PaymentConfiguration;
use App\Models\PaymentLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionsController extends Controller
{
    use UserTraits;

    public function viewTransactions(){

        $SchoolSessions= $this->getSchoolSessionsForDropDown();
        $PymntStatus = FeePayment::select('payment_status')->distinct()->get();
        $PymntType = FeeCategory::select('id', 'category_name')->get();

        //return $PymntType;
        return view('imports.viewTransactions', compact('SchoolSessions', 'PymntStatus', 'PymntType')) ;
    }

    public function searchDailyTransactions(){

        $SchoolSessions= $this->getSchoolSessionsForDropDown();
        $PymntChannel = PaymentLog::select('payment_channel')->distinct()->get();
        $PymntType = FeeCategory::select('id', 'category_name')->get();

        //return $PymntType;
        return view('admin.searchDailyTransactions', compact('SchoolSessions', 'PymntChannel', 'PymntType')) ;
    }


    public function searchTransactionsByDate(){

        $SchoolSessions= $this->getSchoolSessionsForDropDown();
        $PymntChannel = PaymentLog::select('payment_channel')->distinct()->get();
        $PymntType = FeeCategory::select('id', 'category_name')->get();

        //return $PymntType;
        return view('admin.searchTransactionsByDate', compact('SchoolSessions', 'PymntChannel', 'PymntType')) ;
    }



    public function showTransactions(Request $request){

        $validated = $request->validate([
            //'password' => 'required|min:8|confirmed'
        ]);

        //return $request;

        $query = FeePayment::with('user')->with('user.programme');

        if (!is_null($request->payment_type)) {

            $query->where('fee_payments.payment_config_id', $request->payment_type);
        }

        if (!is_null($request->school_session)) {
            $query->where('fee_payments.academic_session_id', $request->school_session);
        }

        if (!is_null($request->payment_status)) {
            $query->where('fee_payments.payment_status', $request->payment_status);
        }




        $transactions = $query->get();

        //return $transactions;

        //return $request->payment_type.$request->payment_status.$request->school_session;


        $paymentDetails =[];
        $BilledTotal = 0;
        $PaidTotal = 0;
        $BalTotal = 0;
        $ExcessTotal = 0;
        $DidscountsTotal = 0;

        foreach ($transactions as $transaction) {

            //return $transaction;

            $discountAmount = intval($transaction->discount_scholarship) + intval($transaction->discount_nkst) + intval($transaction->discount_amount)+ intval($transaction->discount_other);

            $paymentDetails [] = collect([
                'StdName' => $this->getUserNameById($transaction->user_id),
                'StdMatric' => $this->getStudentMatricById($transaction->user_id),
                'StdProgramme' => getProgrammeNameById($transaction->user->program_id),
                'StdCurrentLevel' => strval($this->getStudentCurrentLevelById($transaction->user_id). ' Level'),
                'SchoolSession' => $this->getSchoolSessionNameById($transaction->academic_session_id),
                'PymntAmount' => number_format(convertToNaira($transaction->amount_billed),2),
                'PymntDiscount' => number_format(convertToNaira($discountAmount),2),
                'PymntStatus' => $transaction->payment_status,
                'PymntCummulative' => number_format(convertToNaira($transaction->amount_paid),2),
                'PymntBalance' => number_format(convertToNaira($transaction->balance),2),
                'PymntExcess' => number_format(convertToNaira($transaction->excess_fee),2),
                'PymntDescription' => getFeeCategoryName($transaction->payment_config_id),
                'PymntBillingDate' =>  $transaction->created_at,
                'BilledAt' =>  $transaction->created_at,
                'BilledBy' => $this->getUserNameById($transaction->billing_by),
            ]);


            $BilledTotal += $transaction->amount_billed;
            $PaidTotal += $transaction->amount_paid;
            $BalTotal += $transaction->balance;
            $ExcessTotal += $transaction->excess_fee;
            $DidscountsTotal += $discountAmount;

        }

        $TotalBill = number_format(convertToNaira($BilledTotal),2);
        $TotalPaid = number_format(convertToNaira($PaidTotal),2);
        $TotalBal = number_format(convertToNaira($BalTotal),2);
        $TotalExcess = number_format(convertToNaira($ExcessTotal),2);
        $TotalDiscount = number_format(convertToNaira($DidscountsTotal),2);

        //return $paymentDetails;

        return view('admin.view-transactions', compact('paymentDetails', 'TotalBill','TotalPaid','TotalBal','TotalExcess', 'TotalDiscount')); //->with('paymentDetails', $paymentDetails)->with('TotalBill', $TotalBill);
    }


    public function showDailyTransactions(Request $request){

        //return $request;

        $reportTitle = "Daily Transaction Report";

        $reportDate = 'for '. $request->txn_date;

        $query = PaymentLog::whereDate('payment_logs.created_at','=', $request->txn_date)->with('feePayment');

        if (!is_null($request->payment_type)) {

            $query = $query->where('fee_configs.fee_category_id', $request->payment_type);
        }

        if (!is_null($request->school_session)) {

            $query = $query->where('fee_payments.academic_session_id', $request->school_session);
        }

        if (!is_null($request->payment_channel)) {
            $query = $query->where('payment_logs.payment_channel', $request->payment_channel);
        }

        $dailyTransactions = $query ->join('fee_payments', 'fee_payments.id','=', 'payment_logs.fee_payment_id')
                            ->join('users', 'fee_payments.user_id','=', 'users.id')
                            ->join('fee_configs', 'fee_payments.payment_config_id', '=', 'fee_configs.id')
                            ->select('users.name as name' , 'users.username as matric', 'payment_logs.created_at as txn_date', 'payment_logs.uid as txn_ref', 'payment_logs.amount_paid as txn_amount', 'fee_configs.narration as txn_desc', 'payment_logs.payment_channel as txn_channel')
                            ->get();

        //return $dailyTransactions;

        return view('admin.view-daily-transactions', compact('dailyTransactions','reportDate', 'reportTitle'));



    }


    public function showTransactionsByDate(Request $request){

        //return $request;

        $reportTitle = "Transaction Report By Date";

        $reportDate = 'from '. $request->date_from.' to '. $request->date_to;

        //return $query = PaymentLog::whereDate('payment_logs.created_at','=', $request->date_to)->with('feePayment')->get();

        $query = PaymentLog::whereBetween('payment_logs.created_at', [$request->date_from, $request->date_to])->with('feePayment');

        //return false;

        if (!is_null($request->payment_type)) {

            $query = $query->where('fee_configs.fee_category_id', $request->payment_type);
        }

        if (!is_null($request->school_session)) {

            $query = $query->where('fee_payments.academic_session_id', $request->school_session);
        }

        if (!is_null($request->payment_channel)) {

            $query = $query->where('payment_logs.payment_channel', $request->payment_channel);
        }

        $dailyTransactions = $query ->join('fee_payments', 'fee_payments.id','=', 'payment_logs.fee_payment_id')
                            ->join('users', 'fee_payments.user_id','=', 'users.id')
                            ->join('fee_configs', 'fee_payments.payment_config_id', '=', 'fee_configs.id')
                            ->select('users.name as name' , 'users.username as matric', 'payment_logs.created_at as txn_date', 'payment_logs.uid as txn_ref', 'payment_logs.amount_paid as txn_amount', 'fee_configs.narration as txn_desc', 'payment_logs.payment_channel as txn_channel')
                            ->get();

        //return $dailyTransactions;

        return view('admin.view-daily-transactions', compact('dailyTransactions','reportDate', 'reportTitle'));


    }

}
