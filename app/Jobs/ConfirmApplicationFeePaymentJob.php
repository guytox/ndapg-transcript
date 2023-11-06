<?php

namespace App\Jobs;

use App\Models\FeePayment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ConfirmApplicationFeePaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $transactionId;
    public $time;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($transactionId, $time)
    {
        $this->transactionId = $transactionId;
        $this->time = $time;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        #get the fee payment
        $feePayment = FeePayment::find($this->transactionId);

        if ($feePayment ) {
            if ($feePayment->payment_status == 'paid') {
                if ($feePayment->config->feeCategory->payment_purpose_slug == 'application-fee') {
                    #everything set to take action
                    
                }
            }
        }
    }
}
