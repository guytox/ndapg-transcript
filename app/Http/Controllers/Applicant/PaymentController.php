<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Jobs\ConfirmCredoAcceptancePaymentJob;
use App\Jobs\ConfirmCredoApplicationPaymentJob;
use App\Jobs\ConfirmCredoExtraChargesJob;
use App\Jobs\ConfirmCredoFirstTuitionPaymentJob;
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

            # check to see if payment has been made
            $feePayment = FeePayment::where(['user_id'=>user()->id ,'payment_config_id' => $applicationFeeConfiguration->id, 'payment_status'=>'paid','academic_session_id'=>getApplicationSession()])->first();


            if($feePayment){

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

            #split the name
            $userName = user()->name;
            $splitName = explode(' ', $userName, 2);
            $firstName = $splitName[0];
            $lastName = !empty($splitName[1]) ? $splitName[1] : '';
            #get the user number
            $userNumber = user()->phone_number;

            $body = [
                'amount' => $amount,
                'email' => $transaction->user->email,
                'bearer' => 0,
                'callbackUrl' => config('app.credo.response_url'),
                'channels' => ['card'],
                'currency' => 'NGN',
                'customerFirstName' => $firstName,
                'customerLastName' => $lastName,
                'customerPhoneNumber' => $userNumber,
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
                    $CredoprevousPayment = CredoRequest::where('payee_id', user()->id)
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
            $CredoTransaction = CredoRequest::updateOrCreate(['payee_id' =>user()->id, 'session_id' => getApplicationSession(), 'fee_payment_id' => $transaction->id, 'amount'=>$amount, 'status'=>'pending' ], [
                'payee_id' => user()->id,
                'uid' => $uid,
                'fee_payment_id' => $transaction->id,
                'amount' => $amount,
                'session_id' => getApplicationSession(),
                'txn_id' => $transactionId,
                'checksum' => $checkSum,
            ]);
            //return config('app.credo.response_url');

            #split the name
            $userName = user()->name;
            $splitName = explode(' ', $userName, 2);
            $firstName = $splitName[0];
            $lastName = !empty($splitName[1]) ? $splitName[1] : '';
            #get the user number
            $userNumber = user()->phone_number;

            $body = [
                'amount' => $transaction->amount_billed,
                'email' => $transaction->user->email,
                'bearer' => 0,
                'callbackUrl' => config('app.credo.response_url'),
                'channels' => ['card'],
                'currency' => 'NGN',
                'customerFirstName' => $firstName,
                'customerLastName' => $lastName,
                'customerPhoneNumber' => $userNumber,
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

    public function firstTuitionFee(Request $request){

        #validate the entry
        $validated = $request->validate([
            'usr' =>'required|numeric',
            'fConfig' =>'required|numeric',
            'pAmount' =>'required|numeric'
        ]);
        #ensure there is no pending credo request, if there is forward the student back to pay so we don't have two
        $pendingPayment = CredoRequest::where('status','pending')
                                        ->where('fee_payment_id', $request->fConfig)
                                        ->first();
        if ($pendingPayment) {
            #payment found
            return back()->with('error',"Error!!! Please pay for previous pending transactions before intiating another one");

        }else{
            #get the transaction from the fee payment
            $transaction = FeePayment::find($request->fConfig);
            #get the user
            $pUser = User::find($transaction->user_id);
            #split the name
            $userName = $pUser->name;
            $splitName = explode(' ', $userName, 2);
            $firstName = $splitName[0];
            $lastName = !empty($splitName[1]) ? $splitName[1] : '';
            #get the user number
            $userNumber = $pUser->phone_number;
            #get the transaction id to use
            $transactionId = $transaction->txn_id;
            #get the specified amount to pass to credo
            $amount =  $request->pAmount;
            #generate the checksum for further processing
            $checkSum = md5($transactionId.'nda.@edu.ng'.$amount);
            #get the uid from the fee payment record
            $uid = $transaction->uid;
            # Start Credo processes here
            #first enter details in the credo transaction table
            $CredoTransaction = CredoRequest::updateOrCreate(['payee_id' => $pUser->id,
                'session_id' => $transaction->academic_session_id,
                'fee_payment_id' => $transaction->id,
                'amount'=>$amount,
                'status'=>'pending'],
                [
                'payee_id' => $pUser->id,
                'uid' => $uid,
                'fee_payment_id' => $transaction->id,
                'amount' => $amount,
                'session_id' => $transaction->academic_session_id,
                'txn_id' => $transactionId,
                'checksum' => $checkSum,
            ]);
            #change the reference to avoid replay of same id
            $transaction->txn_id = generateUniqueTransactionReference();
            $transaction->save();
            //return config('app.credo.response_url');

            $body = [
                'amount' => convertToKobo($CredoTransaction->amount),
                'email' => user()->email,
                'bearer' => 0,
                'callbackUrl' => config('app.credo.response_url'),
                'channels' => ['card'],
                'currency' => 'NGN',
                'customerFirstName' => $firstName,
                'customerLastName' => $lastName,
                'customerPhoneNumber' => $userNumber,
                'reference' => $transaction->txn_id,
                'serviceCode' => config('app.credo.serviceCode.TuitionFee'),
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

            $client = new \GuzzleHttp\Client();

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


    }

    public function firstExtraCharges(Request $request){

        // return "Pls split the name here and include customer details before proceeding, thanks";

        #validate the entry
        $validated = $request->validate([
            'usr' =>'required|numeric',
            'fConfig' =>'required|numeric',
            'pAmount' =>'required|numeric'
        ]);
        #ensure there is no pending credo request, if there is forward the student back to pay so we don't have two
        $pendingPayment = CredoRequest::where('status','pending')
                                        ->where('fee_payment_id', $request->fConfig)
                                        ->first();
        if ($pendingPayment) {
            #payment found
            return back()->with('error',"Error!!! Please pay for previous pending transactions before intiating another one");

        }else{
            #get the transaction from the fee payment
            $transaction = FeePayment::find($request->fConfig);
            #get the user
            $pUser = User::find($transaction->user_id);
            #split the name
            $userName = $pUser->name;
            $splitName = explode(' ', $userName, 2);
            $firstName = $splitName[0];
            $lastName = !empty($splitName[1]) ? $splitName[1] : '';
            #get the user number
            $userNumber = $pUser->phone_number;
            #get the transaction id to use
            $transactionId = $transaction->txn_id;
            #get the specified amount to pass to credo
            $amount =  $request->pAmount;
            #generate the checksum for further processing
            $checkSum = md5($transactionId.'nda.@edu.ng'.$amount);
            #get the uid from the fee payment record
            $uid = $transaction->uid;
            # Start Credo processes here
            #first enter details in the credo transaction table
            $CredoTransaction = CredoRequest::updateOrCreate(['payee_id' => $pUser->id,
                'session_id' => $transaction->academic_session_id,
                'fee_payment_id' => $transaction->id,
                'amount'=>$amount,
                'status'=>'pending'],
                [
                'payee_id' => $pUser->id,
                'uid' => $uid,
                'fee_payment_id' => $transaction->id,
                'amount' => $amount,
                'session_id' => $transaction->academic_session_id,
                'txn_id' => $transactionId,
                'checksum' => $checkSum,
            ]);
            #change the reference to avoid replay of same id
            $transaction->txn_id = generateUniqueTransactionReference();
            $transaction->save();
            //return config('app.credo.response_url');

            $body = [
                'amount' => convertToKobo($CredoTransaction->amount),
                'email' => user()->email,
                'bearer' => 0,
                'callbackUrl' => config('app.credo.response_url'),
                'channels' => ['card'],
                'currency' => 'NGN',
                'customerFirstName' => $firstName,
                'customerLastName' => $lastName,
                'customerPhoneNumber' => $userNumber,
                'reference' => $transaction->txn_id,
                'serviceCode' => config('app.credo.serviceCode.ExtraCharges'),
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

            $client = new \GuzzleHttp\Client();

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


    }

    public function reprocessCredoFee($id){
        #this is to be used to reprocess credo transactions only
        #get the Credo Request details
        $credoRequest = CredoRequest::find($id);
        #get the user for some personal details
         $transaction = FeePayment::find($credoRequest->fee_payment_id);
        #get the user
        $pUser = User::find($credoRequest->payee_id);

        #split the name
        $userName = $pUser->name;
        $splitName = explode(' ', $userName, 2);
        $firstName = $splitName[0];
        $lastName = !empty($splitName[1]) ? $splitName[1] : '';
        #get the user number
        $userNumber = $pUser->phone_number;

        if ($credoRequest->status =='pending' && $credoRequest->credo_url !='') {
            #this means you can redirect away
            # call verify
            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => config('app.credo.private_key'),
            ];

            $newurl = 'https://api.credocentral.com/transaction/'.$credoRequest->credo_ref.'/verify';

            $client = new \GuzzleHttp\Client();

            $response = $client->request('GET', $newurl,[
                'headers' => $headers,
            ]);

            $parameters = json_decode($response->getBody());

            //return $parameters->data;
            $businessCode = $parameters->data->businessCode;
            $transRef = $parameters->data->transRef;
            $businessRef = $parameters->data->businessRef;
            $debitedAmount = $parameters->data->debitedAmount;
            $verified_transAmount = $parameters->data->transAmount;
            $transFeeAmount = $parameters->data->transFeeAmount;
            //$settlementAmount = $parameters->data->settlementAmount;
            $customerId = $parameters->data->customerId;
            //$transactionDate = $parameters->data->transactionDate;
            //$channelId = $parameters->data->channelId;
            $currencyCode = $parameters->data->currencyCode;
            $response_status = $parameters->data->status;

            if ($response_status == 0) {
                # payment is made next search for the status in the switch
                $fpayment = FeePayment::join('fee_configs as f','f.id','=','fee_payments.payment_config_id')
                                    ->join('fee_categories as c','c.id','=','f.fee_category_id')
                                    ->where('fee_payments.id',$credoRequest->fee_payment_id)
                                    ->first();

                switch ($fpayment->payment_purpose_slug) {
                    case 'application-fee':
                            # this payment is for application
                            // send background job to confirm the payment with checksum and transaction id
                            ConfirmCredoApplicationPaymentJob::dispatch($transRef, $currencyCode, $response_status, $verified_transAmount);
                            # return home and give the job some time to confirm payment
                            return redirect()->route('home')->with(['message' => 'Your payment confirmation is processing, Please Check back in about two(2) Minutes']);
                        break;
                    case 'acceptance-fee':
                        # send to acceptance fee job
                        // send background job to confirm the payment with checksum and transaction id
                        ConfirmCredoAcceptancePaymentJob::dispatch($transRef, $currencyCode, $response_status, $verified_transAmount);
                        # return home and give the job some time to confirm payment
                        return redirect()->route('home')->with(['message' => 'Your Acceptance Fee Payment Comfirmation is  submitted for processing Successfully!!! Please Check back in about two(2) Minutes']);
                        break;
                    case 'late-registration':
                        # code...
                        break;
                    case 'first-tuition':
                        # send to first tuition fee job
                        // send background job to confirm the payment with checksum and transaction id
                        ConfirmCredoFirstTuitionPaymentJob::dispatch($transRef, $currencyCode, $response_status, $verified_transAmount);
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
                        ConfirmCredoExtraChargesJob::dispatch($transRef, $currencyCode, $response_status, $verified_transAmount);
                        # return home and give the job some time to confirm payment
                        return redirect()->route('home')->with(['message' => 'Your Extra Charges Fee Payment Comfirmation is  submitted for processing Successfully!!! Please Check back in about two(2) Minutes']);
                        break;

                    default:
                        # code...
                        break;
                }
            } else{
                return redirect()->away($credoRequest->credo_url);
            }


        }else{
            // return "Nothing found so proceed to regenerate";
        }

        //return config('app.credo.response_url');

        $body = [

            'amount' => convertToKobo($credoRequest->amount),
            'email' => $pUser->email,
            'bearer' => 0,
            'callbackUrl' => config('app.credo.response_url'),
            'channels' => ['card'],
            'currency' => 'NGN',
            'customerFirstName' => $firstName,
            'customerLastName' => $lastName,
            'customerPhoneNumber' => $userNumber,
            'reference' => $transaction->txn_id,
            'serviceCode' => config('app.credo.serviceCode.TuitionFee'),
            'metadata' => [
                'customFields' =>[
                    [
                        'variable_name' => 'name',
                        'value' => $pUser->name,
                        'display_name' => 'Payers Name'
                    ],
                    [
                        'variable_name' => 'payee_id',
                        'value' => $pUser->id,
                        'display_name' => 'Payee ID'
                    ],
                    [
                        'variable_name' => 'verification_code',
                        'value' => $credoRequest->uid,
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

        $client = new \GuzzleHttp\Client();

        # These setting are for the live environment
        $response = $client->request('POST', 'https://api.credocentral.com/transaction/initialize',[
            'headers' => $headers,
            'json' => $body
        ]);

        //print_r($response->getBody()->getContents());

        $credoReturns = json_decode($response->getBody());

        $credoRequest->channel = 'credo';
        $credoRequest->credo_ref = $credoReturns->data->credoReference;
        $credoRequest->credo_url = $credoReturns->data->authorizationUrl;
        $credoRequest->save();

        // return $CredoTransaction;

        return redirect()->away($credoReturns->data->authorizationUrl);

        return $credoReturns->data->authorizationUrl;
        return $response->getBody()->getContents();

    }

    public function viewAcceptanceInvoice($id){

        $appStd = ApplicantAdmissionRequest::where('uid', $id)->first();

        if ($appStd) {
            #user found, do nothing
        }else{

            return redirect(route('home'))->with('error','Error!!!!! Something went wrong');
        }
        #get the payment
        $paymentData = FeePayment::join('fee_configs as f','f.id','=','fee_payments.payment_config_id')
                                        ->join('fee_categories as c','c.id','=','f.fee_category_id')
                                        ->where('c.payment_purpose_slug', 'acceptance-fee')
                                        ->where('fee_payments.user_id',$appStd->user_id)
                                        ->where('fee_payments.academic_session_id',getApplicationSession())
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
