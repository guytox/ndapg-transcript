<?php

namespace App\Jobs;

use App\Models\CredoRequest;
use App\Models\CredoResponse;
use App\Models\FeePayment;
use App\Models\PaymentLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CredoPaymentConfirmationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $transactionId;
    public $currency;
    public $statusCode;
    public $amount;
    public $time;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($transactionId, $currency, $statusCode, $amount, $time)
    {
        $this->transactionId = $transactionId;
        $this->currency = $currency;
        $this->statusCode = $statusCode;
        $this->amount = $amount;
        $this->time = $time;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
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

        #is the payment made?
        if ($response_status == 0) {
            #get the credo request
            $toVerify = CredoRequest::where('uid', $payee_code)->first();
            $toVerify->status = 'paid';
            $toVerify->save();

            #write Payment Log Entry for the payment
            $updateData = [
                'fee_payment_id' => $toVerify->fee_payment_id,
                'uid' => $payee_code,
                'amount_paid' => convertToKobo($settlementAmount),
                'tx_id' => $businessRef,
                'payment_channel' => config('app.payment_methods.credo')


            ];
            #write the Log
            PaymentLog::updateOrCreate([
                'fee_payment_id' => $toVerify->fee_payment_id,
                'tx_id' => $businessRef,
            ], $updateData);

            #update the fee payment
            $totalLogs = PaymentLog::where('fee_payment_id', $toVerify->fee_payment_id)
                                        ->get();
            if ($totalLogs) {

                if (count($totalLogs) > 0) {

                    $totalPaid = 0;
                    foreach ($totalLogs as $v ) {
                        $totalPaid = $totalPaid + $v->amount_paid;
                    }
                    #fetch the monitor
                    $fpMonitor = FeePayment::find($toVerify->fee_payment_id);

                    $rBalance = $fpMonitor->amount_billed - $totalPaid;

                    if ($fpMonitor) {
                        #perform updates
                        $fpMonitor->amount_paid = $totalPaid;
                        $fpMonitor->balance = $rBalance;
                        $fpMonitor->txn_id = generateUniqueTransactionReference();

                        if ($rBalance <= 0) {
                            $fpMonitor->payment_status = 'paid';
                        }

                        $fpMonitor->save();
                    }

                }

            }

            if ($toVerify) {
                #get the purpose of the payment
                switch ($toVerify->payment->config->feeCategory->payment_purpose_slug) {

                    case 'application-fee':
                        ConfirmApplicationFeePaymentJob::dispatch($toVerify->fee_payment_id, $this->time);
                        break;

                    case 'acceptance-fee':
                        ConfirmAcceptanceFeePaymentJob::dispatch($toVerify->fee_payment_id, $this->time);
                        break;

                    case 'first-tuition':
                        ConfirmFirstTuitionFeePaymentJob::dispatch($toVerify->fee_payment_id, $this->time);
                        break;

                    case 'late-registration':
                        return config('app.credo.serviceCode.lateRegistration');
                        break;

                    case 'tuition':
                        ConfirmTuitionFeePaymentJob::dispatch($toVerify->fee_payment_id, $this->time);
                        break;

                    case 'portal-services':
                        return config('app.credo.serviceCode.ExtraCharges');
                        break;

                    case 'spgs-charges':
                        return config('app.credo.serviceCode.ExtraCharges');
                        break;

                    default:
                        return config('app.credo.serviceCode.ExtraCharges');
                        break;
                }
            }
        }





    }
}
