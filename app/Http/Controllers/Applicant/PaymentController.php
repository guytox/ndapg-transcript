<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Jobs\ConfirmCredoAcceptancePaymentJob;
use App\Jobs\ConfirmCredoApplicationPaymentJob;
use App\Models\ApplicantAdmissionRequest;
use App\Models\ApplicationFeeRequest;
use App\Models\CredoRequest;
use App\Models\CredoResponse;
use App\Models\FeeConfig;
use App\Models\FeePayment;
use App\Models\FeePaymentItem;
use App\Models\PaymentConfiguration;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function applicationFee(){
        $applicationFeeConfiguration = PaymentConfiguration::where('payment_purpose_slug', 'application-fee')->first();

        if($applicationFeeConfiguration) {

            # check to see if
            $feePayment = FeePayment::where(['user_id' => user()->id, 'payment_config_id' => $applicationFeeConfiguration->id, 'payment_status'=>'paid','academic_session_id'=>getApplicationSession()])->first();


            if($feePayment !== null){

                return view('applicant.application_fee', compact('feePayment'));

            }else{
                # check to see if he/she abandoned the transaction and redirect to the same page for payment
                $prevousPayment = ApplicationFeeRequest::where('payee_id', user()->id)->where('session_id',getApplicationSession())->first();

                if ($prevousPayment) {
                    #previous payment attempt has been found perform

                    if ($prevousPayment->credo_ref !='' && $prevousPayment->status=='pending') {

                            $headers = [
                                'Content-Type' => 'application/JSON',
                                'Accept' => 'application/JSON',
                                'Authorization' => config('app.credo.private_key'),
                            ];

                            //return $headers;

                            $newurl = 'https://api.credocentral.com/transaction/'.$prevousPayment->credo_ref.'/verify';

                            //return $newurl;

                            $client = new \GuzzleHttp\Client();

                            $response = $client->request('GET', $newurl,[
                                'headers' => $headers,
                            ]);

                            $parameters = json_decode($response->getBody());

                            //$businessCode = $parameters->data->businessCode;
                            $transRef = $parameters->data->transRef;
                            $businessRef = $parameters->data->businessRef;
                            //$debitedAmount = $parameters->data->debitedAmount;
                            $verified_transAmount = $parameters->data->transAmount;
                            //$transFeeAmount = $parameters->data->transFeeAmount;
                            //$settlementAmount = $parameters->data->settlementAmount;
                            //$customerId = $parameters->data->customerId;
                            //$transactionDate = $parameters->data->transactionDate;
                            //$channelId = $parameters->data->channelId;
                            $currencyCode = $parameters->data->currencyCode;
                            $response_status = $parameters->data->status;

                            //return $parameters;
                            if ($response_status ==0) {
                                #store the response

                                $newrequest = CredoResponse::updateOrCreate(['transRef'=>$transRef],[
                                    'transRef'=>$transRef,
                                    'currency'=>$currencyCode,
                                    'status'=>$response_status,
                                    'transAmount'=>$verified_transAmount,
                                ]);


                                // send background job to confirm the payment with checksum and transaction id
                                ConfirmCredoApplicationPaymentJob::dispatch($businessRef, $currencyCode, $response_status, $verified_transAmount);

                                return redirect()->route('home')->with(['message' => 'Your payment confirmation is processing, Please Check back in about two(2) Minutes']);
                            }else{
                                # the status of this payment is not paid so forward the user to go and pay again
                                return redirect()->away($prevousPayment->credo_url);
                            }
                    }elseif($prevousPayment->status == 'paid'){
                        return view('home')->with('info', "Error!!! This payment has been made before, contact support if in doubt");
                    }
                    # forward for payment because nothing has been found
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

            $transaction = ApplicationFeeRequest::updateOrCreate(['payee_id' =>user()->id, 'session_id' => getApplicationSession()], [
                'amount' => $applicationFeeConfiguration->amount,
                'payee_id' => user()->id,
                'txn_id' => $transactionId,
                'checksum' => $checkSum,
                'uid' => $uid,
                'session_id' => getApplicationSession(),
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
                'bearer' => 0,
                'callbackUrl' => config('app.credo.response_url'),
                'channels' => ['card'],
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

        abort(403, 'Application Fee not configured');

    }


    public function acceptanceFee(){

        $acceptanceFeeConfiguration = FeeConfig::join('fee_categories as f','f.id','=','fee_configs.fee_category_id')
                                                ->join('fee_templates as t','t.id','=','fee_configs.fee_template_id')
                                                ->where('f.payment_purpose_slug', 'acceptance-fee')
                                                ->select('fee_configs.*','t.total_amount')
                                                ->first();
        // return getApplicationSession();
                #fee config found, now search the fee payment to see if student has this payment then proceed to next step
        if($acceptanceFeeConfiguration) {


            # check to see if
            $feePayment = FeePayment::where('user_id', user()->id)
                                    ->where('payment_config_id' , $acceptanceFeeConfiguration->id)
                                    ->where('academic_session_id', getApplicationSession())
                                    ->first();


            if($feePayment && $feePayment->payment_status=='paid'){
                #this payment has been made forward user to the invoice page directely
                return view('applicant.acceptance_fee_invoice', compact('feePayment'));

            }elseif($feePayment){

                # check to see if he/she abandoned the transaction and redirect to the same page for payment
               $prevousPayment = CredoRequest::where('payee_id', user()->id)
                                                ->where('session_id',getApplicationSession())
                                                ->where('fee_payment_id', $feePayment->id)
                                                ->where('status','pending')
                                                ->first();

                if ($prevousPayment) {
                    #previous payment attempt has been found perform

                    if ($prevousPayment->credo_ref !='' && $prevousPayment->status=='pending') {

                            $headers = [
                                'Content-Type' => 'application/JSON',
                                'Accept' => 'application/JSON',
                                'Authorization' => config('app.credo.private_key'),
                            ];

                            //return $headers;

                            $newurl = 'https://api.credocentral.com/transaction/'.$prevousPayment->credo_ref.'/verify';

                            //return $newurl;

                            $client = new \GuzzleHttp\Client();

                            $response = $client->request('GET', $newurl,[
                                'headers' => $headers,
                            ]);

                            $parameters = json_decode($response->getBody());

                            //$businessCode = $parameters->data->businessCode;
                            $transRef = $parameters->data->transRef;
                            $businessRef = $parameters->data->businessRef;
                            //$debitedAmount = $parameters->data->debitedAmount;
                            $verified_transAmount = $parameters->data->transAmount;
                            //$transFeeAmount = $parameters->data->transFeeAmount;
                            //$settlementAmount = $parameters->data->settlementAmount;
                            //$customerId = $parameters->data->customerId;
                            //$transactionDate = $parameters->data->transactionDate;
                            //$channelId = $parameters->data->channelId;
                            $currencyCode = $parameters->data->currencyCode;
                            $response_status = $parameters->data->status;

                            //return $parameters;
                            if ($response_status ==0) {
                                #store the response

                                $newrequest = CredoResponse::updateOrCreate(['transRef'=>$transRef],[
                                    'transRef'=>$transRef,
                                    'currency'=>$currencyCode,
                                    'status'=>$response_status,
                                    'transAmount'=>$verified_transAmount,
                                ]);


                                // send background job to confirm the payment with checksum and transaction id
                                ConfirmCredoAcceptancePaymentJob::dispatch($businessRef, $currencyCode, $response_status, $verified_transAmount);

                                return redirect()->route('home')->with(['message' => 'Your payment confirmation is processing, Please Check back in about two(2) Minutes']);
                            }else{
                                # the status of this payment is not paid so forward the user to go and pay again
                                return redirect()->away($prevousPayment->credo_url);
                            }
                    }elseif($prevousPayment->status == 'paid'){
                        return view('home')->with('info', "Error!!! This payment has been made before, contact support if in doubt");
                    }
                    # forward for payment because nothing has been found
                    if ($prevousPayment->credo_url !='') {
                        # the credo response code is not  empty
                        return redirect()->away($prevousPayment->credo_url);
                    }


                }else{
                    #check if the payment is flaggged as paid the forward to job and return home
                    return $CredoprevousPayment = CredoRequest::where('payee_id', user()->id)
                                                ->where('session_id',getApplicationSession())
                                                ->where('fee_payment_id', $feePayment->id)
                                                ->where('status','paid')
                                                ->first();
                    if ($CredoprevousPayment) {
                        #payment has been made redirect home
                        return view('home')->with('info', "Error!!! This payment has been flagged paid before, contact support if in doubt");

                    }
                }



            }

            $transactionId = generateUniqueTransactionReference();

            $amount =  convertToNaira($acceptanceFeeConfiguration->total_amount);

             $checkSum = md5($transactionId.'nda.@edu.ng'.$amount);

            //return $checkSum;

            $uid = uniqid('fac');


            $transaction = FeePayment::updateOrCreate(['user_id' =>user()->id, 'academic_session_id' => getApplicationSession(), 'payment_config_id' => $acceptanceFeeConfiguration->id, ], [
                'user_id' => user()->id,
                'uid' => $uid,
                'payment_config_id' => $acceptanceFeeConfiguration->id,
                'academic_session_id' => getApplicationSession(),
                'amount_billed' => $acceptanceFeeConfiguration->total_amount,
                'txn_id' => $transactionId,
                'checksum' => $checkSum,
            ]);

            # Start Credo processes here
            #first enter details in the credo transaction table
            $CredoTransaction = CredoRequest::updateOrCreate(['payee_id' =>user()->id, 'session_id' => getApplicationSession(), 'fee_payment_id' => $acceptanceFeeConfiguration->id, 'amount'=>$amount, 'status'=>'pending' ], [
                'payee_id' => user()->id,
                'uid' => $uid,
                'fee_payment_id' => $transaction->id,
                'amount' => $amount,
                'session_id' => getApplicationSession(),
                'txn_id' => $transactionId,
                'checksum' => $checkSum,
            ]);
            //return config('app.credo.response_url');

            $body = [
                'amount' => $transaction->amount_billed,
                'email' => $transaction->user->email,
                'bearer' => 0,
                'callbackUrl' => config('app.credo.response_url'),
                'channels' => ['card'],
                'currency' => 'NGN',
                //'customerPhoneNumber' => $transaction->user->phone_number,
                'reference' => $transaction->txn_id,
                'serviceCode' => config('app.credo.serviceCode.acceptanceFee'),
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

            $CredoTransaction->channel = 'credo';
            $CredoTransaction->credo_ref = $credoReturns->data->credoReference;
            $CredoTransaction->credo_url = $credoReturns->data->authorizationUrl;
            $CredoTransaction->save();

            // return $CredoTransaction;

            return redirect()->away($credoReturns->data->authorizationUrl);

            return $credoReturns->data->authorizationUrl;
            return $response->getBody()->getContents();

        }

        abort(403, 'Application Fee not configured');




    }

    public function viewAcceptanceInvoice($id){
        #get the payment
        $paymentData = FeePayment::join('fee_configs as f','f.id','=','fee_payments.payment_config_id')
                                        ->where('uid',$id)
                                        ->select('fee_payments.*','f.narration')
                                        ->first();
        #get the payment items
        $pitems = FeePaymentItem::where('fee_payment_id', $paymentData->id)->get();

        $pLogs = $paymentData->paymentLogs;

        $studentData = User::find($paymentData->user_id);

        $appData = ApplicantAdmissionRequest::where('user_id',$studentData->id)
                                            ->where('session_id',$paymentData->academic_session_id)
                                            ->first();
        $dept = Program::find($appData->program_id);

        $payurl = route('view.acceptance.invoice',['id'=>$paymentData->uid]);

        return view('applicant.acceptance_fee_invoice', compact('paymentData','pitems','pLogs','studentData','payurl','appData','dept'));

    }

    public function viewInvoice($id){
        return $id;
    }


}
