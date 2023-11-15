<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Jobs\ConfirmCredoApplicationPaymentJob;
use App\Jobs\CredoPaymentConfirmationJob;
use App\Models\CredoRequest;
use App\Models\CredoResponse;
use App\Models\FeeConfig;
use App\Models\FeePayment;
use App\Models\FeePaymentItem;
use App\Models\FeeTemplate;
use App\Models\FeeTemplateItem;
use App\Models\PaymentLog;
use Illuminate\Http\Request;

class StudentPaymentController extends Controller
{



    public function lateRegistrationFee()
    {
        #get the payment id for lateReg
        $applicationFeeConfiguration = FeeConfig::join('fee_categories as f', 'f.id','=','fee_configs.fee_category_id')
                                                ->where('fee_configs.account', 'late_reg')
                                                ->where('fee_configs.semester_id', getActiveSemesterId())
                                                ->select('fee_configs.*')
                                                ->first();

        if($applicationFeeConfiguration) {

            # check fee payment to see if the student has paid then forward appropriately
            $feePayment = FeePayment::where([
                'user_id' => user()->id,
                'payment_config_id' => $applicationFeeConfiguration->id,
                'academic_session_id'=>getActiveAcademicSessionId()])
                ->first();


            if($feePayment){
                #entry found, check the credo request and forward if it was already created if not ignore to crreate another one

                if ($feePayment->status == 'paid') {
                    # I don't know why you ended up here but you shouldn't send him to the safest way out
                    return redirect(route('student.registration.viewAll',['id'=>user()->id]))->with('error', "You have Registered for this Semester Already!!!!!");

                }else{

                    #this payment is pending, check for credo ref and foward to credo
                    $prevousPayment = CredoRequest::where('payee_id', user()->id)
                                                    ->where('session_id',getActiveAcademicSessionId())
                                                    ->where('fee_payment_id', $feePayment->id)
                                                    ->first();

                    if ($prevousPayment) {
                        #previouse payment found check if pending and if credo ref is found then redirect away
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


            }else{
                # There is no entry at all so Just create afresh entry for this user

                #first get the cost from the template

                 $feeCost = FeeTemplate::find($applicationFeeConfiguration->fee_template_id);

                if (!$feeCost) {
                    #fee cost not found, return back with error
                    return redirect(route('home'))->with('error', "Error!!! No Late Registration Template found, contact ICT");

                }else{
                    #fee cost found proceed with getting the items
                     $feeCostItems = FeeTemplateItem::where('fee_template_id', $feeCost->id)->get();

                    if (!$feeCostItems) {
                        # no items found and cost will be zero
                        return redirect(route('home'))->with('error', "Error!!! No Late Registration Billing Items found, contact ICT");

                    }else{
                        # All set to commence writing now
                        # make entry into feePayment.
                        #prepare the data
                        $feePaymentData = [
                            'user_id' => user()->id,
                            'uid' => uniqid('fw'),
                            'payment_config_id' => $applicationFeeConfiguration->id,
                            'academic_session_id' => getActiveAcademicSessionId(),
                            'amount_billed' => $feeCost->total_amount,
                            'txn_id' => generateUniqueTransactionReference(),
                        ];
                        #All Set to write to the table
                        $feePymntEntry = FeePayment::updateOrCreate([
                            'user_id' => user()->id,
                            'uid' => uniqid('fw'),
                        ],$feePaymentData);

                        if ($feePymntEntry) {
                            #entry Successful proceed to enter items and credo ref
                            foreach ($feeCostItems as $k) {
                                # configure items data
                                $feeCostItemsData = [
                                    'fee_payment_id' => $feePymntEntry->id,
                                    'fee_item_id' => $k->fee_item_id,
                                    'amount' => $k->item_amount,
                                ];
                                #write to feePaymentItems Table
                                $newItems = FeePaymentItem::updateOrCreate([
                                    'fee_payment_id' => $feePymntEntry->id,
                                    'fee_item_id' => $k->fee_item_id,
                                ],$feeCostItemsData);
                            }
                            //return $feePymntEntry;
                            #make new credo request entry and forward for payment
                            $credoRequestData = [
                                'payee_id' => $feePymntEntry->user_id,
                                'fee_payment_id' => $feePymntEntry->id,
                                'amount' => convertToNaira($feePymntEntry->amount_billed),
                                'session_id' => $feePymntEntry->academic_session_id,
                                'uid' => uniqid('cfw'),
                                'txn_id' => $feePymntEntry->txn_id,
                            ];

                            $credoPymntRequest = CredoRequest::updateOrCreate([
                                'payee_id' => $feePymntEntry->user_id,
                                'fee_payment_id' => $feePymntEntry->id,
                            ],$credoRequestData);
                            #proceed to get credo ref to update
                            $body = [
                                'amount' => convertToKobo($credoPymntRequest->amount),
                                'email' => user()->email,
                                'bearer' => 0,
                                'callbackUrl' => config('app.credo.response_url'),
                                'channels' => ['card'],
                                'currency' => 'NGN',
                                //'customerPhoneNumber' => $transaction->user->phone_number,
                                'reference' => $credoPymntRequest->txn_id,
                                'serviceCode' => config('app.credo.serviceCode.lateRegistration'),
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
                                            'value' => $credoPymntRequest->uid,
                                            'display_name' => 'Verification Code'
                                        ]
                                    ]
                                ]
                            ];
                            #next prepare to submit data to credo

                            $headers = [
                                'Content-Type' => 'application/json',
                                'Accept' => 'application/json',
                                'Authorization' => config('app.credo.public_key'),
                            ];

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
                            #extract response
                            $credoReturns = json_decode($response->getBody());
                            #write data to credo Request table and redirect to payment page
                            $credoPymntRequest->channel = 'credo';
                            $credoPymntRequest->credo_ref = $credoReturns->data->credoReference;
                            $credoPymntRequest->credo_url = $credoReturns->data->authorizationUrl;
                            $credoPymntRequest->save();
                            //return $transaction;
                            # Now send away for payment
                            return redirect()->away($credoReturns->data->authorizationUrl);
                        }

                    }
                }
                $prevousPayment = ApplicationFeeRequest::where('payee_id', user()->id)->where('session_id',getApplicationSession())->first();

                if ($prevousPayment) {
                    #previous payment attempt has been found perform




                }
            }


        }

        abort(403, 'Late Registration  Fee not configured');

    }

    public function viewStudentBalances($id){
        #fetch all pending payments
        $pendingPayments = FeePayment::where('user_id', $id)
                                    ->where('balance','>', 0)
                                    ->get();

        if ($pendingPayments) {
            if (count($pendingPayments)>0) {
                return view('students.ViewStudentBalances')->with(['Monitors'=>$pendingPayments]);
            }
        }
            return redirect(route('home'))->with('info',"You do not have any pending payments at this time");


    }

    public function viewStudentPaymentHistory($id){
        #fetch all pending payments
        $paymentHistory = PaymentLog::join('fee_payments as f', 'f.id', 'payment_logs.fee_payment_id')
                                    ->where('f.user_id', $id)
                                    ->select('payment_logs.*')
                                    ->get();



        if ($paymentHistory) {
            if (count($paymentHistory)>0) {
                return view('students.ViewStudentPaymentHistory')->with(['Monitors'=>$paymentHistory]);
            }
        }
            return redirect(route('home'))->with('info',"You have not made any payments at this time");


    }

    public function initiatePayment($id){
        #grab the payment
        $toPay = FeePayment::where('uid', $id)->first();

        if ($toPay) {
            # CHeck for pending requests
            if ($toPay->credoRequests) {
                foreach ($toPay->credoRequests as $r) {
                    #check payment status here
                    if ($r->status == 'pending') {
                        return redirect()->action([StudentPaymentController::class, 'processCredoPayment'],[$r->uid]);
                    }
                }
            }
                #No pending request found, proceed to show form for selecting amount
                return view('students.initlate_payment')->with('payment', $toPay);

        }else{
            return redirect(route('home'))->with('error', "Error!!! Fee transaction Not Found, try again");
        }
    }

    public function postPaymentRequest(Request $request, $id){
        #lets validate some stuff

        $validated = $request->validate([
            'type' => ['required','numeric', 'min:100'],
        ]);

        #fetch fee Payment record
        $feePayment = FeePayment::where('id', $id)->orWhere('uid', $id)->first();
        if (!$feePayment) {
            return back()->with('error', "Error!!!! Payment details Not found");
        }

        #All Clear Proceed to deal
        if (isPaymentPaid($id)) {

            return redirect('home')->with('info', "Notice!!! This Payment has been fully paid for");

        }else{
            #check if credoRequest is pending
            if ($pendingCredoRequest = isCredoRequestPending($feePayment->id)) {

                return redirect()->action([StudentPaymentController::class, 'processCredoPayment'],[$pendingCredoRequest->uid]);

            }else{
                return redirect()->action([StudentPaymentController::class, 'writeCredoRequest'],[$id, $request->type]);
            }
        }
    }


    public function writeCredoRequest($feeMonitor, $amount){
        #fetch the feepayment records
        $feePayment = FeePayment::where('uid', $feeMonitor)->first();

        if (!$feePayment) {
            return redirect('home')->with('error', "Error in Payment Processing");
        }

        #prepare to write
        $data =[
            'payee_id' => $feePayment->user_id,
            'fee_payment_id' =>$feePayment->id,
            'amount' => $amount,
            'session_id' => $feePayment->academic_session_id,
            'uid' => uniqid('ftn'),
            'status' => 'pending',
            'txn_id' => generateUniqueTransactionReference(),
            'channel' => 'credo',
        ];

        $newCredoRequest = CredoRequest::updateOrCreate([
            'payee_id' => $feePayment->user_id,
            'fee_payment_id' =>$feePayment->id,
            'amount' => $amount,
            'session_id' => $feePayment->academic_session_id,
            'status' => 'pending',
            'channel' => 'credo',

        ],$data);

        #if successful forward to credo payment
        if ($newCredoRequest) {
            return redirect()->action([StudentPaymentController::class, 'generateCredoReference'],[$newCredoRequest->id]);
        }else{
            return redirect('home')->with('error', "Failed to record credo student payment request");

        }

    }

    public function generateCredoReference($id){
        #fetch the request to process further
        $toFetch = CredoRequest::find($id);
        if ($toFetch) {
            if ($toFetch->credo_url !=null) {
                return redirect()->action([StudentPaymentController::class, 'processCredoPayment'],[$toFetch->uid]);
            }else{
                #get parameters required to generate credo ref
                #split the name
                $splitName = explode(' ', $toFetch->payment->user->name, 2);
                $firstName = $splitName[0];
                $lastName = !empty($splitName[1]) ? $splitName[1] : '';
                #get the user number
                $userNumber = $toFetch->payment->user->phone_number;
                // return $userNumber;

                $body = [

                    'amount' => convertToKobo($toFetch->amount),
                    'email' => $toFetch->payment->user->email,
                    'bearer' => 0,
                    'callbackUrl' => config('app.credo.response_url'),
                    'channels' => ['card'],
                    'currency' => 'NGN',
                    'customerFirstName' => $firstName,
                    'customerLastName' => $lastName,
                    'customerPhoneNumber' => $userNumber,
                    'reference' => $toFetch->txn_id,
                    'serviceCode' => generateServiceCode($toFetch->id),
                    'metadata' => [
                        'customFields' =>[
                            [
                                'variable_name' => 'name',
                                'value' => $toFetch->payment->user->name,
                                'display_name' => 'Payers Name'
                            ],
                            [
                                'variable_name' => 'payee_id',
                                'value' => $toFetch->payment->user->id,
                                'display_name' => 'Payee ID'
                            ],
                            [
                                'variable_name' => 'verification_code',
                                'value' => $toFetch->uid,
                                'display_name' => 'Verification Code'
                            ]
                        ]
                    ]
                ];

                #configure the headers now
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

                $toFetch->channel = 'credo';
                $toFetch->credo_ref = $credoReturns->data->credoReference;
                $toFetch->credo_url = $credoReturns->data->authorizationUrl;
                $toFetch->save();

                // return $CredoTransaction;

                return redirect()->away($credoReturns->data->authorizationUrl);

            }
        }else{
            return redirect('home')->with('error', "Error in Payment Processing");

        }
    }


    public function processCredoPayment($id){
        #grab the payment
        $toProcess = CredoRequest::where('uid', $id)->first();

        if ($toProcess) {

            if ($toProcess->credo_url != null) {
                #check status of the transaction at credo
                # call verify
                $headers = [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => config('app.credo.private_key'),
                ];

                $newurl = 'https://api.credocentral.com/transaction/'.$toProcess->credo_ref.'/verify';

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

                    $time = now();

                    CredoPaymentConfirmationJob::dispatch($transRef, $currencyCode, $response_status, $verified_transAmount, $time);

                }else {
                    #payment not successful redirect to payment
                    return redirect()->away($toProcess->credo_url);

                }

            }

        }else{

            return redirect('home')->with('error', "Error in Payment Processing, Payment Request Not Found");
        }

        return redirect('home')->with('error', "Error in Payment Processing, Payment Request could not be Verified");

    }





}
