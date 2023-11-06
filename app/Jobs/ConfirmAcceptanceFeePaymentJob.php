<?php

namespace App\Jobs;

use App\Models\ApplicantAdmissionRequest;
use App\Models\FeePayment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ConfirmAcceptanceFeePaymentJob implements ShouldQueue
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
                if ($feePayment->config->feeCategory->payment_purpose_slug == 'acceptance-fee') {
                    #next update the Applicant admission table since this is acceptance
                    $appInfo = ApplicantAdmissionRequest::where('user_id',$feePayment->user_id)
                                                        ->where('session_id', getApplicationSession())
                                                        ->first();
                    $appInfo->acceptance_paid = 1;
                    $appInfo->acceptance_paid_at = now();
                    $appInfo->save();

                    Log::info('Acceptance Fee Confirmation Successful for - '.$feePayment->user->username);

                }
            }
        }
    }
}
