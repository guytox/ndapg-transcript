<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Jobs\ConfirmCredoApplicationPaymentJob;
use App\Models\CredoRequest;
use App\Models\CredoResponse;
use App\Models\FeeConfig;
use App\Models\FeePayment;
use App\Models\FeePaymentItem;
use App\Models\FeeTemplate;
use App\Models\FeeTemplateItem;
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
}