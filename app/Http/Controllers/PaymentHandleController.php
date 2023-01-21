<?php

namespace App\Http\Controllers;

use App\Jobs\ConfirmApplicationPaymentJob;
use App\Models\FeePayment;
use Illuminate\Http\Request;
use App\Jobs\ConfirmCredoApplicationPaymentJob;
use App\Jobs\ConfirmPaymentJob;
use App\Models\CredoResponse;

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


            // send background job to confirm the payment with checksum and transaction id
            ConfirmCredoApplicationPaymentJob::dispatch($transactionId, $currency, $statusCode, $amount);

            return redirect()->route('home')->with(['message' => 'Your payment confirmation is processing, Please Check back in about two(2) Minutes']);
        }

        abort(403, 'Unable to confirm payment information');
    }

}
