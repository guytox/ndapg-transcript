<?php

namespace App\Http\Controllers;

use App\Jobs\ConfirmCredoAcceptancePaymentJob;
use App\Jobs\ConfirmCredoApplicationPaymentJob;
use App\Jobs\ConfirmCredoExtraChargesJob;
use App\Jobs\ConfirmCredoFirstTuitionPaymentJob;
use App\Models\Admission;
use App\Models\CredoRequest;
use App\Models\FeeItem;
use App\Models\FeePaymentItem;
use Illuminate\Http\Request;

class BillingController extends Controller
{

    public function getPaidAdmittedStudents(Request $request){

        if (user()->hasRole('admin|dean_pg')) {
            $validated = $request->validate([
                'schoolsession' => 'required',
            ]);

            $paymentList = Admission::where('amount_paid','>',0)
                                    ->where('session_id',$request->schoolsession)
                                    ->get();

            if ($paymentList->count()<1) {
                return back()->with('error', "Error, No record found");
            }
            $totalPaid = 0;

            //$payList []='';

            foreach ($paymentList as $key => $v) {

                $payList [] = collect([
                    'matno' => $v->matric_number,
                    'formno' => $v->form_number,
                    'name' => $v->surname. " ".$v->other_names,
                    'paycode' => $v->payment_code,
                    'programme' => getProgrammeDetailById($v->programme_id,'name'),
                    'clearedby' => getUserById($v->cleared_by)->name,
                    'clearedat' => $v->cleared_at,
                    'amountpaid' => $v->amount_paid
                ]);

                $totalPaid = $totalPaid + $v->amount_paid;

            }

            //return $payList;



            return view('bursary.view-uploaded-payments', compact('payList','totalPaid'));
        }

        return back()->with('info', "You do not have the required privileges to view this resource");

    }


    public function verifyManualPayments(){

        $payList = CredoRequest::where('status','pending')
                                        ->get();

        return view('admin.viewPendingCredoPayments', compact('payList'));

    }

    public function checkCredoPaymentStatus($id){
        if (user()->hasRole('admin')) {
            #proceed
        }else{
            return redirect(route('home'))->with('error', "Error!!! You do not have the required priviledges to access this resource");
        }
        #all Clear to move, lets extract the payment details ready for verification
        $paymentDetails = CredoRequest::where('uid',$id)
                                        ->where('credo_ref', '!=','')
                                        ->first();

        #get the payment purpose from the request
        $feePurpose = $paymentDetails->payment->config->feeCategory->payment_purpose_slug;

        if ($paymentDetails) {
            # something found


            $headers = [
            'Content-Type' => 'application/JSON',
            'Accept' => 'application/JSON',
            'Authorization' => config('app.credo.private_key'),
            ];

            //return $headers;

            $newurl = 'https://api.credocentral.com/transaction/'.$paymentDetails->credo_ref.'/verify';



            $client = new \GuzzleHttp\Client();

            $response = $client->request('GET', $newurl,[
                'headers' => $headers,
            ]);

            $parameters = json_decode($response->getBody());

            //$parameters;

            $transactionId = $parameters->data->transRef;
            $currency = $parameters->data->currencyCode;
            $statusCode = $parameters->data->status;
            $amount = $parameters->data->transAmount;

            if ($statusCode==0) {
                # send background job to confirm the payment with checksum and transaction id
                #get the reason for the payment

                switch ($feePurpose) {
                    case 'acceptance-fee':
                        # this is acceptance fees send to appropriate job
                        # send to acceptance fee job
                        // send background job to confirm the payment with checksum and transaction id
                        ConfirmCredoAcceptancePaymentJob::dispatch($transactionId, $currency, $statusCode, $amount);
                        # return home and give the job some time to confirm payment
                        return redirect()->route('manual.payment.verification')->with(['message' => 'Your Acceptance Fee Payment Comfirmation is  submitted for processing Successfully!!! Please Check back in about two(2) Minutes']);

                        break;
                    case 'first-tuition':
                        // send background job to confirm the payment with checksum and transaction id
                        ConfirmCredoFirstTuitionPaymentJob::dispatch($transactionId, $currency, $statusCode, $amount);
                        # return home and give the job some time to confirm payment
                        return redirect()->route('manual.payment.verification')->with(['message' => 'Your Tuition Fee Payment Comfirmation is  submitted for processing Successfully!!! Please Check back in about two(2) Minutes']);

                        break;

                    case 'spgs-charges':
                        #this payment is for ID card, Medical and Laboratory
                        #send to background job
                        ConfirmCredoExtraChargesJob::dispatch($transactionId, $currency, $statusCode, $amount);
                        # return home and give the job some time to confirm payment
                        return redirect()->route('manual.payment.verification')->with(['message' => 'Your Extra Charges Fee Payment Comfirmation is  submitted for processing Successfully!!! Please Check back in about two(2) Minutes']);

                        break;

                    case 'tuition':
                        # code...
                        break;

                    case 'late-registration':
                        # code...
                        break;

                    case 'application-fee':
                        #This payment is application fee
                        ConfirmCredoApplicationPaymentJob::dispatch($transactionId, $currency, $statusCode, $amount);
                        return redirect(route('manual.payment.verification'))->with('info', "Payment Successfully Submitted for processing, check back again after a minute");

                        break;

                    default:
                        # code...
                        break;
                }




            }else{

                return redirect(route('manual.payment.verification'))->with('error', "Error!!! Payment was not successful");
            }


        }else{


            return redirect(route('manual.payment.verification'))->with('error', "Error!!! Requested resource not found");
        }
    }

    public function feePaymentReport($id){

        switch ($id) {
            case 'acceptance':
                $feePurpose = FeeItem::where('description','postgraduate-acceptance-fees')->first();
                break;

            case 'idcard':
                $feePurpose = FeeItem::where('description','identity-id-card')->first();
                # code...
                break;
            case 'medical':
                $feePurpose = FeeItem::where('description','medical-expenses')->first();
                # code...
                break;

            case 'laboratory':
                $feePurpose = FeeItem::where('description','laboratory-fees')->first();
                # code...
                break;
            case 'pgfees':
                $feePurpose = FeeItem::where('description','pg-tuition-fee')->first();
                # code...
                break;

            default:
                # code...
                break;
        }


        $paymentDetails = FeePaymentItem::where('fee_item_id', $feePurpose->id)->where('status','paid')->get();

        $totalPaid = $paymentDetails->sum('amount');

        return view('admin.reports.viewPaidPaymentsByItem',compact('paymentDetails','totalPaid','feePurpose'));
    }


}
