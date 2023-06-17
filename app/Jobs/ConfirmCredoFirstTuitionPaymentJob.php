<?php

namespace App\Jobs;

use App\Models\ApplicantAdmissionRequest;
use App\Models\CredoRequest;
use App\Models\CredoResponse;
use App\Models\FeeConfig;
use App\Models\FeePayment;
use App\Models\PaymentLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ConfirmCredoFirstTuitionPaymentJob implements ShouldQueue
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
                                ->where('c.payment_purpose_slug' ,'first-tuition')
                                ->first();
        if (!$submission) {

            Log::info('Inconsistent Job Submitted in FirstTuitionConfirmationJob for - '.$businessRef);

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
                            $paData = [
                                'fee_payment_id' => $feeReason->id,
                                'amount_paid' => convertToKobo($settlementAmount),
                                'uid' => $payee_code,
                                'tx_id' => $businessRef,
                                'payment_channel' => config('app.payment_methods.credo')
                            ];

                            PaymentLog::updateOrCreate([
                                'fee_payment_id' => $feeReason->id,
                                'tx_id' => $businessRef,
                            ], $paData);


                            #update the fee payment monitor with the amount paid
                            $feeReason->amount_paid = $feeReason->amount_paid + convertToKobo($settlementAmount);
                            if ($feeReason->amount_paid == $feeReason->amount_billed) {
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
                                    # find the Applicant
                                    $appInfo = ApplicantAdmissionRequest::where('user_id',$payee_id)
                                                    ->where('session_id', getApplicationSession())
                                                    ->first();
                                    $appInfo->acceptance_paid = 1;
                                    $appInfo->acceptance_paid_at = now();
                                    $appInfo->save();

                                    break;

                                default:
                                    # code...
                                    Log::info('Wrong purpose Job found in ConfirmCredoFirstTuitionJob for - '.$businessRef);

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
                Log::info('fresher fee payment has been confirmed - '.$businessRef);
                #update the credo request to paid
                $payCheck2 = CredoRequest::where('uid', $payee_code)->first();
                $payCheck2->status = 'paid';
                $payCheck2->save();

                #transaction successful get the reference
                $fpEntry = FeePayment::find($submission->fee_payment_id);
                #reference found

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


                #get the total this applicant has paid

                $totalLogs = PaymentLog::where('fee_payment_id', $fpEntry->id)
                                        ->get();
                $totalPaid = $totalLogs->sum('amount_paid');
                #compute the balance
                $rBalance = $fpEntry->amount_billed - $totalPaid;
                if ($rBalance == 0) {
                    #payment is complete, flag all payments as paid
                    $fpEntry->payment_status = 'paid';
                    $submission->status = 'paid';
                    $submission->save();

                }
                #change the
                #update fee payment records
                $fpEntry->amount_paid = $totalPaid;
                $fpEntry->balance = $rBalance;
                #change the reference to allow user to pay a second time.
                $fpEntry->txn_id = generateUniqueTransactionReference();
                $fpEntry->save();

                #next update the Applicant admission table since this is first tuition
                $appInfo = ApplicantAdmissionRequest::where('user_id',$payee_id)
                                                    ->where('session_id', getApplicationSession())
                                                    ->first();
                $appInfo->is_paid_tuition = 1;
                $appInfo->paid_tuition_at = now();
                $appInfo->save();

                // genericMail($emailSubject, $validPaymentMessage, $this->email);
            } else {
                #nothing found
                Log::info('Error in Final First Tuition Confirmation for - '.$businessRef);

                // genericMail($emailSubject, $invalidPaymentMessage, $this->email);
            }
        }
    }
}
