<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\ApplicationFeeRequest;
use App\Models\FeePayment;
use App\Models\PaymentConfiguration;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function applicationFee()
    {
        $applicationFeeConfiguration = PaymentConfiguration::where('payment_purpose_slug', 'application-fee')->first();
        if($applicationFeeConfiguration) {


            $feePayment = FeePayment::where(['user_id' => user()->id, 'payment_config_id' => $applicationFeeConfiguration->id, 'payment_status'=>'paid','academic_session_id'=>activeSession()->id])->first();


            if($feePayment !== null){
                return view('applicant.application_fee', compact('feePayment'));
            }else{
                # check to see if he/she abandoned the transaction and redirect to the same page for payment
                $prevousPayment = ApplicationFeeRequest::where('payee_id', user()->id)->where('status','pending')->first();

                if ($prevousPayment) {
                    # forward for payment
                    if ($prevousPayment->credo_url !='') {
                        # the credo response code is not  empty
                        return redirect()->away($prevousPayment->credo_url);
                    }


                }
            }

            $terminalId = config('app.etranzact.terminal_id');

            $responseURL = config('app.etranzact.application_fee_response_url');

            $secretKey = config('app.etranzact.secret_key');

            $logoURL = config('app.etranzact.logo_url');

            $transactionId = generateUniqueTransactionReference();

            $amount =  convertToKobo($applicationFeeConfiguration->amount);

            $checkSum = generateCheckSum(
                $amount,
                $transactionId,
                $terminalId,
                $responseURL,
                $secretKey
            );

            //return $checkSum;

            $uid = uniqid('fw');

            $transaction = ApplicationFeeRequest::updateOrCreate(['payee_id' =>user()->id], [
                'amount' => $applicationFeeConfiguration->amount,
                'payee_id' => user()->id,
                'txn_id' => $transactionId,
                'checksum' => $checkSum,
                'uid' => $uid,
            ]);

            $paymentData = [
                'email' => $transaction->user->email,
                'amount' => $amount,
                'description' => $transaction->description,
                'txn_id' => $transaction->txn_id,
                'checksum' => $transaction->checksum,
                'name' => $transaction->user->name,
                'payee_id' => $transaction->user->id,
                'responseurl' => $responseURL,
                'logourl' => $logoURL,
            ];

            # Start Credo processes here
            //return config('app.credo.response_url');

            $body = [
                'amount' => $amount,
                'email' => $transaction->user->email,
                'bearer' => 1,
                'callbackUrl' => config('app.credo.response_url'),
                'channels' => ['card', 'bank'],
                'currency' => 'NGN',
                //'customerPhoneNumber' => $transaction->user->phone_number,
                'reference' => $transaction->txn_id,
                'serviceCode' => config('app.credo.serviceCode.applicationFee'),
                'metadata' => [
                    'customFields' =>[
                        [
                            'variable_name' => 'name',
                            'value' => user()->name,
                            'display_name' => 'Payers Name'
                        ],
                        [
                            'variable_name' => 'payee_id',
                            'value' => user()->id,
                            'display_name' => 'Payee ID'
                        ],
                        [
                            'variable_name' => 'verification_code',
                            'value' => $uid,
                            'display_name' => 'Verification Code'
                        ]
                    ]
                ]
            ];



            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => config('app.credo.public_key'),
            ];

            //return $headers;
            //return $body;

            $client = new \GuzzleHttp\Client();

            # These settings are for a demo environment
            // $response = $client->request('POST', 'api.public.credodemo.com/transaction/initialize',[
            //     'headers' => $headers,
            //     'json' => $body
            // ]);

            # These setting are for the live environment
            $response = $client->request('POST', 'https://api.credocentral.com/transaction/initialize',[
                'headers' => $headers,
                'json' => $body
            ]);



            //print_r($response->getBody()->getContents());


            $credoReturns = json_decode($response->getBody());

            $transaction->channel = 'credo';
            $transaction->credo_ref = $credoReturns->data->credoReference;
            $transaction->credo_url = $credoReturns->data->authorizationUrl;
            $transaction->save();

            //return $transaction;

            return redirect()->away($credoReturns->data->authorizationUrl);

            return $credoReturns->data->authorizationUrl;
            return $response->getBody()->getContents();



            return redirect()->route('pay.application.now')->with(['paymentData' => $paymentData ]);

        }

        abort(403, 'Application not configured');

    }
}
