<?php

namespace App\Jobs;

use App\Models\CredoRequest;
use App\Models\CredoResponse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CredoRequestSanitationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $id;
    public $time;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id,  $time)
    {
        $this->id = $id;
        $this->time = $time;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        #find the request
        $credRequest = CredoRequest::find($this->id);

        if ($credRequest) {
            if ($credRequest->credo_ref !='') {
                #credo Ref Exist, you can proceed
                #request found fire proceedure
                #verify the status of the payment
                $headers = [
                    'Content-Type' => 'application/JSON',
                    'Accept' => 'application/JSON',
                    'Authorization' => config('app.credo.private_key'),
                ];
                #form the new url
                $newurl = 'https://api.credocentral.com/transaction/'.$credRequest->credo_ref.'/verify';
                #intiliaze new request
                $client = new \GuzzleHttp\Client();
                #fire request here
                $response = $client->request('GET', $newurl,[
                    'headers' => $headers,
                ]);
                #expor the json
                $parameters = json_decode($response->getBody());

                $paymentChannel = 'credo-online';
                $time = now();

                    $businessCode = $parameters->data->businessCode;
                    $transRef = $parameters->data->transRef;
                    $businessRef = $parameters->data->businessRef;
                    $debitedAmount = $parameters->data->debitedAmount;
                    $verified_transAmount = covertToInt($parameters->data->transAmount);
                    $transFeeAmount = $parameters->data->transFeeAmount;
                    $settlementAmount = $parameters->data->settlementAmount;
                    $customerId = $parameters->data->customerId;
                    $transactionDate = $parameters->data->transactionDate;
                    $channelId = $parameters->data->channelId;
                    $currencyCode = $parameters->data->currencyCode;
                    $response_status = $parameters->data->status;

                    #store Response
                    foreach ($parameters->data->metadata as $k) {
                        if ($k->insightTag == 'name') {

                            $payee_name = $k->insightTagValue;

                        }elseif ($k->insightTag == 'payee_id') {

                            $payee_id = $k->insightTagValue;

                        }elseif ($k->insightTag == 'verification_code') {

                            $payee_code = $k->insightTagValue;
                        }
                    }

                if ($response_status == 0) {

                    Log::info('Credo fee payment has been confirmed - '.$businessRef);
                    # payment is confirmed, fire the log entry
                    CredoRequestEnterPaymentLogJob::dispatch($credRequest->fee_payment_id, $credRequest->uid , $verified_transAmount, $paymentChannel, $businessRef, $time);
                    # fire the credo request

                    # flag credo request as paid
                    if ($parameters->data->transAmount == $credRequest->amount ) {
                        $credRequest->status = 'paid';
                        $credRequest->save();
                    }

                    $responseRecords = CredoResponse::updateOrCreate(['transRef'=>$transRef],[
                        'transRef'=>$transRef,
                        'businessCode'=>$businessCode,
                        'businessRef'=>$businessRef,
                        'debitedAmount'=>$debitedAmount,
                        'verified_transAmount'=> $parameters->data->transAmount,
                        'transFeeAmount'=>$transFeeAmount,
                        'settlementAmount'=>$settlementAmount,
                        'customerId'=>$customerId,
                        'transactionDate'=>$transactionDate,
                        'channelId'=>$channelId,
                        'response_status'=>$response_status,
                        'currencyCode'=>$currencyCode,
                        'payee_name'=>$payee_name,
                        'payee_id'=>$payee_id,
                        'payee_code'=>$payee_code,
                    ]);

                    #
                }
            }

        }
    }
}
