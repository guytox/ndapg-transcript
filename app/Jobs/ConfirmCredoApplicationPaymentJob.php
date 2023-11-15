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
use App\Models\CredoRequest;
use App\Models\CredoResponse;
use App\Models\FeeConfig;
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

        #find what the payment is for
        $submission = ApplicationFeeRequest::where('uid', $payee_code)->first();
        if (!$submission) {
            Log::info('Inconsistent Job Submitted in ApplicaitonFeeConfirmationJob for - '.$businessRef);

            #payment is for application, follow the application route
            $payCheck = CredoRequest::where('uid', $payee_code)->first();

            if ($payCheck) {
                # this payment is not applicaiton fee, check the status before proceeding
                if ($response_status==0) {
                    #the payment returns as paid get what this payment is for
                    $feeReason = FeePayment::find($payCheck->fee_payment_id);
                    if ($feeReason) {
                        #feePayment found proceed with checks
                        #verify the amount paid
                        if (convertToNaira($feeReason->amount_billed) == $settlementAmount) {
                            # find out the reason why the payment was made and treat as such
                            $payCheck->status = 'paid';
                            $payCheck->save();

                            #write the log
                            PaymentLog::create([
                                'fee_payment_id' => $feeReason->id,
                                'amount_paid' => $settlementAmount,
                                'uid' => $payee_code,
                                'tx_id' => $businessRef,
                                'payment_channel' => config('app.payment_methods.credo')
                            ]);
                            #update the fee payment monitor with the amount paid
                            #first ge the total paid under this payment_id
                            $totalLogs = PaymentLog::where('fee_payment_id', $feeReason->id)->get();
                            #sum the total logs for this fee payment id
                            $totalPaidLogs = $totalLogs->sum('amount_paid');
                            # apply the computed total to fee payment model
                            $feeReason->amount_paid = $totalPaidLogs;

                            if ($totalPaidLogs == $feeReason->amount_billed) {
                                #payment complete mark as paid
                                $feeReason->status = 'paid';
                            }
                            $feeReason->save();

                            # Next find the reason
                            $feePurpose = FeeConfig::join('fee_categories as f', 'f.id','=','fee_configs.fee_category_id')
                                                    ->where('fee_configs.id', $feeReason->payment_config_id)
                                                    ->select('fee_configs.*', 'fee_categories.payment_purpose_slug')
                                                    ->first();

                            switch ($feePurpose->payment_purpose_slug) {

                                case 'late-registration':
                                    # code...
                                    break;

                                case 'acceptance-fee':
                                    # code...
                                    break;

                                default:
                                    # code...
                                    Log::info('Wrong purpose Job found in ConfirmCredoApplicaitonFeeJob for - '.$businessRef);

                                    break;
                            }
                        }

                    }else{
                        #this feePayment entry is not found Do nothing (this is strange)
                    }

                }else{
                    #Do nothing because the payment has not been made (payment status is not 0)
                }
            }else{

                # Do Nothing because this payment is not found

            }


        }elseif($submission) {
            #payment found for application
            $submittedAmount = $submission->amount;

            if ($submittedAmount == $settlementAmount && $submission->uid == $payee_code && $response_status==0) {
                # make log entry
                Log::info('Application Fee Payment  has been confirmed -'.$businessRef);
                #get the credo request and flag it as paid
                #COMMENTED OUT ON 220523 BECAUSE ENTRY IS NOT MADE INTO CREDO REQUEST AT TIME OF APPLICATION
                // $payCheck2 = CredoRequest::where('uid', $payee_code)->first();
                // $payCheck2->status = 'paid';
                // $payCheck2->save();
                #find the user based on retrieved payment details
                $user = User::find($payee_id);
                # get the configuration for applicaton fees
                $applicationFeeConfiguration = PaymentConfiguration::where('payment_purpose_slug', 'application-fee')->first();
                #
                $feeRequest = ApplicationFeeRequest::where('payee_id', $user->id)->first();

                $feePaymentTransaction = FeePayment::updateOrCreate([
                    'user_id' => $user->id,
                    'payment_config_id' => $applicationFeeConfiguration->id,
                    'academic_session_id' => getApplicationSession(),
                ],[
                    'amount_billed' => $feeRequest->amount,
                    'user_id' => $user->id,
                    'payment_config_id' => $applicationFeeConfiguration->id,
                    'academic_session_id' => getApplicationSession(),
                    'payment_status' => config('app.status.paid'),
                    'amount_paid' => $verified_transAmount,
                    'uid' => $payee_code,
                    'balance' => 0,
                    'tx_id' =>$businessRef,
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

                PaymentLogSanitationJob::dispatch($submission->id, $feePaymentTransaction->id, now());


                // genericMail($emailSubject, $validPaymentMessage, $this->email);
            } else {
                #nothing found
                Log::info('Error in Final Application Fee Confirmation for - '.$businessRef);

                // genericMail($emailSubject, $invalidPaymentMessage, $this->email);
            }
        }

    }
}
