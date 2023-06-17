<?php

namespace App\Jobs;

use App\Models\CredoRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AutomaticCredoVerificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $uid;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($uid)
    {
        $this->uid = $uid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $paymentDetails = CredoRequest::where('uid',$this->uid)
                                        ->where('credo_ref', '!=','')
                                        ->first();

        #get the payment purpose from the request
        $feePurpose = $paymentDetails->payment->config->feeCategory->payment_purpose_slug;

        if ($paymentDetails) {
            # something found


            $headers = [
            'Content-Type' => 'application/JSON',
            'Accept' => 'application/JSON',
            'Authorization' => config('app.credo.private_key'),
            ];

            //return $headers;

            $newurl = 'https://api.credocentral.com/transaction/'.$paymentDetails->credo_ref.'/verify';



            $client = new \GuzzleHttp\Client();

            $response = $client->request('GET', $newurl,[
                'headers' => $headers,
            ]);

            $parameters = json_decode($response->getBody());

            //$parameters;

            $transactionId = $parameters->data->transRef;
            $currency = $parameters->data->currencyCode;
            $statusCode = $parameters->data->status;
            $amount = $parameters->data->transAmount;

            if ($statusCode==0) {
                # send background job to confirm the payment with checksum and transaction id
                #get the reason for the payment

                switch ($feePurpose) {
                    case 'acceptance-fee':
                        # this is acceptance fees send to appropriate job
                        # send to acceptance fee job
                        // send background job to confirm the payment with checksum and transaction id
                        ConfirmCredoAcceptancePaymentJob::dispatch($transactionId, $currency, $statusCode, $amount);
                        Log::info("Automatic Acceptance Fees Processing Subitted for - ".$this->uid);
                        # return home and give the job some time to confirm payment
                        // return redirect()->route('manual.payment.verification')->with(['message' => 'Your Acceptance Fee Payment Comfirmation is  submitted for processing Successfully!!! Please Check back in about two(2) Minutes']);

                        break;
                    case 'first-tuition':
                        // send background job to confirm the payment with checksum and transaction id
                        ConfirmCredoFirstTuitionPaymentJob::dispatch($transactionId, $currency, $statusCode, $amount);
                        Log::info("Automatic First Tuition Fees Processing Subitted for - ".$this->uid);
                        # return home and give the job some time to confirm payment
                        // return redirect()->route('manual.payment.verification')->with(['message' => 'Your Tuition Fee Payment Comfirmation is  submitted for processing Successfully!!! Please Check back in about two(2) Minutes']);

                        break;

                    case 'spgs-charges':
                        #this payment is for ID card, Medical and Laboratory
                        #send to background job
                        ConfirmCredoExtraChargesJob::dispatch($transactionId, $currency, $statusCode, $amount);
                        Log::info("Automatic Extra Fees Processing Subitted for - ".$this->uid);
                        # return home and give the job some time to confirm payment
                        // return redirect()->route('manual.payment.verification')->with(['message' => 'Your Extra Charges Fee Payment Comfirmation is  submitted for processing Successfully!!! Please Check back in about two(2) Minutes']);

                        break;

                    case 'tuition':
                        # code...
                        break;

                    case 'late-registration':
                        # code...
                        break;

                    case 'application-fee':
                        #This payment is application fee
                        ConfirmCredoApplicationPaymentJob::dispatch($transactionId, $currency, $statusCode, $amount);
                        Log::info("Automatic Application Fees Processing Subitted for - ".$this->uid);
                        // return redirect(route('manual.payment.verification'))->with('info', "Payment Successfully Submitted for processing, check back again after a minute");

                        break;

                    default:

                        Log::info("No Transaction Purpose found for - ".$this->uid);

                        break;
                }




            }else{

                Log::info("Credo Transaction Still Pending for - ".$this->uid);

                // return redirect(route('manual.payment.verification'))->with('error', "Error!!! Payment was not successful");
            }


        }else{

            Log::info("Credo Request Entry Not Found for - ".$this->uid);

            // return redirect(route('manual.payment.verification'))->with('error', "Error!!! Requested resource not found");
        }


    }
}
