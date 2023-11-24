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

                $transRef = $parameters->data->transRef;
                $businessRef = $parameters->data->businessRef;
                $verified_transAmount = covertToInt($parameters->data->transAmount);
                $currencyCode = $parameters->data->currencyCode;
                $response_status = $parameters->data->status;
                $paymentChannel = 'credo-online';
                $time = now();

                if ($response_status == 0) {
                    # payment is confirmed, fire the log entry
                    CredoRequestEnterPaymentLogJob::dispatch($credRequest->fee_payment_id, $credRequest->uid , $verified_transAmount, $paymentChannel, $businessRef, $time);
                    # fire the credo request

                    # flag credo request as paid
                    if ($parameters->data->transAmount == $credRequest->amount ) {
                        $credRequest->status = 'paid';
                        $credRequest->save();
                    }
                    $newCredoResponse = CredoResponse::updateOrCreate(['transRef'=>$transRef],[
                        'transRef'=>$transRef,
                        'currency'=>$currencyCode,
                        'status'=>$response_status,
                        'transAmount'=>$verified_transAmount,
                    ]);

                    #
                }
            }

        }
    }
}
