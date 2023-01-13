<?php

namespace App\Jobs;

use App\Models\PaymentConfiguration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\PaymentLog;
use App\Models\FeePayment;
use Illuminate\Support\Facades\Log;
use App\Models\ApplicationFeeRequest;
use App\Models\CredoResponse;
use App\Models\User;

class ConfirmCredoApplicationPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $transactionId;
    public $currency;
    public $statusCode;
    public $amount;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($transactionId, $currency, $statusCode, $amount)
    {
        $this->transactionId = $transactionId;
        $this->currency = $currency;
        $this->statusCode = $statusCode;
        $this->amount = $amount;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $validPaymentMessage = 'Congratulations your  payment has been confirmed';

        $invalidPaymentMessage = 'Sorry we could not confirm your payment contact the administrator with the email julipels@yahoo.com';

        $secretKey = config('app.credo.private_key');

        $emailSubject = 'payment vericfication '. env('APP_NAME');

        # confirm payment using the privae key

        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => config('app.credo.private_key'),
        ];

        $newurl = 'https://api.credocentral.com/transaction/'.$this->transactionId.'/verify';

        $client = new \GuzzleHttp\Client();

        $response = $client->request('GET', $newurl,[
            'headers' => $headers,
        ]);

        $parameters = json_decode($response->getBody());

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

        //return $parameters->data;
        $businessCode = $parameters->data->businessCode;
        $transRef = $parameters->data->transRef;
        $businessRef = $parameters->data->businessRef;
        $debitedAmount = $parameters->data->debitedAmount;
        $verified_transAmount = $parameters->data->transAmount;
        $transFeeAmount = $parameters->data->transFeeAmount;
        $settlementAmount = $parameters->data->settlementAmount;
        $customerId = $parameters->data->customerId;
        $transactionDate = $parameters->data->transactionDate;
        $channelId = $parameters->data->channelId;
        $currencyCode = $parameters->data->currencyCode;
        $response_status = $parameters->data->status;

        #first update the log

        $responseRecords = CredoResponse::updateOrCreate(['transRef'=>$transRef],[
            'transRef'=>$transRef,
            'businessCode'=>$businessCode,
            'businessRef'=>$businessRef,
            'debitedAmount'=>$debitedAmount,
            'verified_transAmount'=>$verified_transAmount,
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

        #now let us do some basic checks and pass this payment (make sure you avoid replay)
        #get the submitted request
        $submission = ApplicationFeeRequest::where('uid', $payee_code)->first();
        $submittedAmount = $submission->amount;

        if ($submittedAmount == $settlementAmount && $submission->uid == $payee_code && $response_status==0) {
            # make log entry
            Log::info('payment has been confirmed -'.$businessRef);
            #find the user based on retrieved payment details
            $user = User::find($payee_id);
            # get the configuration for applicaton fees
            $applicationFeeConfiguration = PaymentConfiguration::where('payment_purpose_slug', 'application-fee')->first();
            #
            $feeRequest = ApplicationFeeRequest::where('payee_id', $user->id)->first();

            $feePaymentTransaction = FeePayment::create([
                'amount_billed' => $feeRequest->amount,
                'user_id' => $user->id,
                'payment_config_id' => $applicationFeeConfiguration->id,
                'academic_session_id' => getApplicationSession(),
                'payment_status' => config('app.status.paid'),
                'amount_paid' => $verified_transAmount,
                'uid' => $payee_code,
                'balance' => 0,
                'txn_id' => generateUniqueTransactionReference(), // change the transaction id to avoid replay attacks
            ]);

            PaymentLog::create([
                'fee_payment_id' => $feePaymentTransaction->id,
                'amount_paid' => $feePaymentTransaction->amount_paid,
                'uid' => $payee_code,
                'tx_id' => $businessRef,
                'payment_channel' => config('app.payment_methods.credo')
            ]);

            $feeRequest->status = 'paid';
            $feeRequest->save();

            // genericMail($emailSubject, $validPaymentMessage, $this->email);
        } else {
            #nothing found
            // genericMail($emailSubject, $invalidPaymentMessage, $this->email);
        }
    }
}
