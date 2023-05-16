<?php

namespace App\Http\Controllers;

use App\Jobs\ConfirmApplicationPaymentJob;
use App\Jobs\ConfirmCredoAcceptancePaymentJob;
use App\Models\FeePayment;
use Illuminate\Http\Request;
use App\Jobs\ConfirmCredoApplicationPaymentJob;
use App\Jobs\ConfirmCredoExtraChargesJob;
use App\Jobs\ConfirmCredoFirstTuitionPaymentJob;
use App\Jobs\ConfirmPaymentJob;
use App\Models\CredoRequest;
use App\Models\CredoResponse;
use App\Models\Program;
use App\Models\User;

class PaymentHandleController extends Controller
{


    public function confirmPayment(Request $request)
    {
        if ($request->has('TRANSACTION_ID') && $request->has('CHECKSUM')) {

            $transactionId = $request->get('TRANSACTION_ID');
            $checkSum = $request->get('CHECKSUM');
            $finalCheckSum = $request->get('FINAL_CHECKSUM');
            $statusCode = $request->get('SUCCESS');
            $amount = $request->get('AMOUNT');

            $email = $request->get('EMAIL');

            // send background job to confirm the payment with checksum and transaction id
            ConfirmPaymentJob::dispatch($transactionId, $checkSum, $finalCheckSum, $statusCode, $amount, $email);

            return redirect()->route('student.outstanding.payments')->with(['message' => 'Your payment confirmation is processing']);
        }

        abort(403, 'Unable to confirm payment information');
    }

    public function confirmApplicationPayment(Request $request)
    {
        if ($request->has('TRANSACTION_ID') && $request->has('CHECKSUM')) {

            $transactionId = $request->get('TRANSACTION_ID');
            $checkSum = $request->get('CHECKSUM');
            $finalCheckSum = $request->get('FINAL_CHECKSUM');
            $statusCode = $request->get('SUCCESS');
            $amount = $request->get('AMOUNT');

            $email = $request->get('EMAIL');

            // send background job to confirm the payment with checksum and transaction id
            ConfirmApplicationPaymentJob::dispatch($transactionId, $checkSum, $finalCheckSum, $statusCode, $amount, $email);

            return redirect()->route('home')->with(['message' => 'Your payment confirmation is processing']);
        }

        abort(403, 'Unable to confirm payment information');
    }

    public function confirmcredoApplicationPayment(Request $request)
    {

        //return $request;

        # prepare for validation

        // $headers = [
        //     'Content-Type' => 'application/JSON',
        //     'Accept' => 'application/JSON',
        //     'Authorization' => config('app.credo.private_key'),
        // ];

        // //return $headers;

        // $newurl = 'https://api.credocentral.com/transaction/'.$request->transRef.'/verify';

        // //return $newurl;

        // $client = new \GuzzleHttp\Client();

        // $response = $client->request('GET', $newurl,[
        //     'headers' => $headers,
        // ]);

        // $parameters = json_decode($response->getBody());

        // //return $parameters;


        if ($request->has('transRef') && $request->has('transAmount')) {

            $transactionId = $request->get('transRef');
            $currency = $request->get('currency');
            $statusCode = $request->get('status');
            $amount = $request->get('transAmount');
            //  return $transactionId;
            #store the response
            $newrequest = CredoResponse::updateOrCreate(['transRef'=>$request->transRef],[
                'transRef'=>$request->transRef,
                'currency'=>$request->currency,
                'status'=>$request->status,
                'transAmount'=>$request->transAmount,
            ]);

            #next find what the payment is all about and route it appropriately
            $pDetails = CredoRequest::where('credo_ref', $newrequest->transRef)->first();
            #next find the fee_payment entry for this record
            $fpayment = FeePayment::join('fee_configs as f','f.id','=','fee_payments.payment_config_id')
                                    ->join('fee_categories as c','c.id','=','f.fee_category_id')
                                    ->where('fee_payments.id',$pDetails->fee_payment_id)
                                    ->first();

            switch ($fpayment->payment_purpose_slug) {
                case 'application-fee':
                        # this payment is for application
                        // send background job to confirm the payment with checksum and transaction id
                        ConfirmCredoApplicationPaymentJob::dispatch($transactionId, $currency, $statusCode, $amount);
                        # return home and give the job some time to confirm payment
                        return redirect()->route('home')->with(['message' => 'Your payment confirmation is processing, Please Check back in about two(2) Minutes']);
                    break;
                case 'acceptance-fee':
                    # send to acceptance fee job
                    // send background job to confirm the payment with checksum and transaction id
                    ConfirmCredoAcceptancePaymentJob::dispatch($transactionId, $currency, $statusCode, $amount);
                    # return home and give the job some time to confirm payment
                    return redirect()->route('home')->with(['message' => 'Your Acceptance Fee Payment Comfirmation is  submitted for processing Successfully!!! Please Check back in about two(2) Minutes']);
                    break;
                case 'late-registration':
                    # code...
                    break;
                case 'first-tuition':
                    # send to first tuition fee job
                    // send background job to confirm the payment with checksum and transaction id
                    ConfirmCredoFirstTuitionPaymentJob::dispatch($transactionId, $currency, $statusCode, $amount);
                    # return home and give the job some time to confirm payment
                    return redirect()->route('home')->with(['message' => 'Your Tuition Fee Payment Comfirmation is  submitted for processing Successfully!!! Please Check back in about two(2) Minutes']);
                    break;
                case 'tuition':
                    # code...
                    break;
                case 'wallet-fund':
                    # code...
                    break;
                case 'spgs-charges':
                    #this payment is for ID card, Medical and Laboratory
                    #send to background job
                    ConfirmCredoExtraChargesJob::dispatch($transactionId, $currency, $statusCode, $amount);
                    # return home and give the job some time to confirm payment
                    return redirect()->route('home')->with(['message' => 'Your Extra Charges Fee Payment Comfirmation is  submitted for processing Successfully!!! Please Check back in about two(2) Minutes']);
                    break;

                default:
                    # code...
                    break;
            }

        }

        abort(403, 'Unable to confirm payment information');
    }

    public function printGeneralReceipt($id){
        #first get the fee payment entry
        $feeEntry = FeePayment::where('uid', $id)->first();
        #get balance for fresh entries
        if ($feeEntry->balance == '') {
            $bal = $feeEntry->amount_billed;
        }else {
            $bal = $feeEntry->balance;
        }
        #get the user
        $items = $feeEntry->items;

        if($feeEntry->user->student){

            $prog = Program::find($feeEntry->user->student->program_id);

        }elseif ($feeEntry->user->applicant) {

            $prog = Program::find($feeEntry->user->applicant->program_id);

        }else{

            return redirect(route('home'))->with('error', "The User with this invoice is not found");
        }

        return view('bursar.print-general-invoice', compact('feeEntry','prog','items', 'bal'));
    }



}
