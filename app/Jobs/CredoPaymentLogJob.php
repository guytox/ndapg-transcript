<?php

namespace App\Jobs;

use App\Models\CredoRequest;
use App\Models\PaymentLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CredoPaymentLogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $payRef;
    public $JobTime;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($payRef, $JobTime)
    {
        $this->payRef = $payRef;
        $this->JobTime = $JobTime;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        #get the credo request
        $toVerify = CredoRequest::where('uid', $this->payRef)->first();

        if ($toVerify->amount == $toVerify->credoResponse->settlementAmount) {
            #write Payment Log Entry for the payment
            #get the parameters for the Log entry
            $businessRef = $toVerify->credoResponse->businessRef;
            $settlementAmount = $toVerify->credoResponse->settlementAmount;
            $payee_code = $toVerify->credoResponse->payee_code;

            $updateData = [
                'fee_payment_id' => $toVerify->fee_payment_id,
                'uid' => $payee_code,
                'amount_paid' => $settlementAmount,
                'tx_id' => $businessRef,
                'payment_channel' => config('app.payment_methods.credo')


            ];
            #write the Log
            $newLogEntry = PaymentLog::updateOrCreate([
                'fee_payment_id' => $toVerify->fee_payment_id,
                'tx_id' => $businessRef,
            ], $updateData);

            if ($newLogEntry) {

                PaymentLogCleaningJob::dispatch($newLogEntry->id, now());
            }



        }else{
            #TODO: not able to write for one reason or the other, decide what to do from here
        }




        $toVerify->status = 'paid';
        $toVerify->save();


    }
}
