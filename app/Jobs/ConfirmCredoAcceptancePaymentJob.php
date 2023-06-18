<?php

namespace App\Jobs;

use App\Models\ApplicantAdmissionRequest;
use App\Models\CredoRequest;
use App\Models\CredoResponse;
use App\Models\FeeConfig;
use App\Models\FeePayment;
use App\Models\PaymentLog;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ConfirmCredoAcceptancePaymentJob implements ShouldQueue
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

        #find what the payment is for - acceptance fees obviously
        $submission = CredoRequest::join('fee_payments as s','s.id','=','credo_requests.fee_payment_id')
                                ->join('fee_configs as f','f.id','=','s.payment_config_id')
                                ->join('fee_categories as c','c.id','=','f.fee_category_id')
                                ->where('credo_requests.uid',$payee_code)
                                ->where('credo_requests.credo_ref', $transRef)
                                ->where('c.payment_purpose_slug' ,'acceptance-fee')
                                ->first();
        if (!$submission) {
            #payment is for application, follow the application route
            Log::info('Inconsistent Job Submitted in AcceptanceConfirmationJob for - '.$businessRef);

        }elseif($submission) {
            #payment found for application
            $submittedAmount = $submission->amount;

            if ($submittedAmount == $settlementAmount && $submission->uid == $payee_code && $response_status==0) {
                # make log entry
                Log::info('Acceptance Fee Payment has been confirmed -'.$businessRef);
                #get the credo request and flag it as paid
                $payCheck2 = CredoRequest::where('uid', $payee_code)->where('credo_ref', $transRef)->first();
                $payCheck2->status = 'paid';
                $payCheck2->save();

                #transaction successful get the reference
                $fpEntry = FeePayment::find($submission->fee_payment_id);

                # Enter the payment log
                $paData = [
                    'fee_payment_id' => $fpEntry->id,
                    'amount_paid' => convertToKobo($settlementAmount),
                    'uid' => $payee_code,
                    'tx_id' => $businessRef,
                    'payment_channel' => config('app.payment_methods.credo')
                ];

                PaymentLog::updateOrCreate([
                    'fee_payment_id' => $fpEntry->id,
                    'tx_id' => $businessRef,
                ], $paData);

                 #first ge the total paid under this payment_id
                 $totalLogs = PaymentLog::where('fee_payment_id',$fpEntry->id)->get();
                 #sum the total logs for this fee payment id
                 $totalPaidLogs = $totalLogs->sum('amount_paid');

                #update the fee payment table
                $fpEntry->payment_status = 'paid';
                $fpEntry->amount_paid = $totalPaidLogs;
                $fpEntry->balance = $fpEntry->amount_billed - $totalPaidLogs;
                $fpEntry->save();

                #next update the Applicant admission table since this is acceptance
                $appInfo = ApplicantAdmissionRequest::where('user_id',$payee_id)
                                                    ->where('session_id', getApplicationSession())
                                                    ->first();
                $appInfo->acceptance_paid = 1;
                $appInfo->acceptance_paid_at = now();
                $appInfo->save();
                $submission->status = 'paid';
                $submission->save();

                // genericMail($emailSubject, $validPaymentMessage, $this->email);
            } else {
                #nothing found
                Log::info('Error in Final Acceptance Fee Confirmation for - '.$businessRef);
                // genericMail($emailSubject, $invalidPaymentMessage, $this->email);
            }
        }


    }
}
