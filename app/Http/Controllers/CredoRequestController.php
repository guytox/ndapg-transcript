<?php

namespace App\Http\Controllers;

use App\Jobs\CredoPaymentConfirmationJob;
use App\Models\CredoRequest;
use App\Models\CredoResponse;
use App\Models\FeePayment;
use Illuminate\Http\Request;

class CredoRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //
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

    public function generateCredoRequest($id, $amount){
        # TODO: check if this request has been paid for the redirect back to submit
        #grab the feePayment instance
        $feeDetails = FeePayment::where('uid', $id)->where('payment_status', 'pending')->first();
        if ($feeDetails) {
            #next look for a pending entry in Credo Payment request
            $crdoRequest = CredoRequest::where('fee_payment_id', $feeDetails->id)->where('status','pending')->first();
            if ($crdoRequest) {
                # There's a pending payment, forward to credo Reference Generation and proceed
                return redirect()->action([CredoRequestController::class, 'generateCredoRef'], ['id'=>$crdoRequest->uid]);
            }else {
                # nothing found, proceed to process new entry
                $newCredoRequest = CredoRequest::updateOrCreate([
                    'payee_id' => $feeDetails->user_id,
                    'fee_payment_id' => $feeDetails->id,
                    'amount' => $feeDetails->balance,
                    'uid' => uniqid('ctr'),
                ],[
                    'payee_id' => $feeDetails->user_id,
                    'fee_payment_id' => $feeDetails->id,
                    'amount' => $feeDetails->balance,
                    'uid' => uniqid('ctr'),
                    'txn_id' => generateUniqueTransactionReference(),
                ]);

                if ($newCredoRequest) {
                    #write the response entry
                    $newCredoResponse = CredoResponse::updateOrCreate([
                        'businessRef' => $newCredoRequest->txn_id,
                        'payee_code' => $newCredoRequest->uid
                    ],[
                        'businessRef' => $newCredoRequest->txn_id,
                        'payee_code' => $newCredoRequest->uid

                    ]);

                    #all set forward the user to generate credo ref
                    return redirect()->action([CredoRequestController::class, 'generateCredoRef'], ['id'=>$newCredoRequest->uid]);
                }
            }

        }else{
            return redirect(route('home'))->with('info', "Payment Error !!!! Please try again");
        }


        return redirect(route('home'))->with('info', "Payment Error !!!! Please try again");

    }

    public function generateCredoRef($id){
        #fetch the request to process further
        $toFetch = CredoRequest::where('uid', $id)->first();


        if ($toFetch) {

            if ($toFetch->credo_url !=null) {

                return redirect()->action([CredoRequestController::class, 'processCredoPayment'],[$toFetch->uid]);

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

                    'amount' => $toFetch->amount,
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
