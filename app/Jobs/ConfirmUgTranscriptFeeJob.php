<?php

namespace App\Jobs;

use App\Models\FeePayment;
use App\Models\TranscriptRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ConfirmUgTranscriptFeeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $feePaymentUId;
    public $JobTime;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($feePaymentUId, $JobTime)
    {
        $this->feePaymentUId = $feePaymentUId;
        $this->JobTime = $JobTime;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // PaymentStatusCheckJob
        # flag the request to paid first before and call the submit JOB
        #lets grab the payment instance

        $fPayment = FeePayment::where('uid', $this->feePaymentUId)->first();

        if ($fPayment) {
            #instance found, you may proceed
            if ($fPayment->balance === 0) {
                #payment for this is complete
                #first flat the payment status for the request to zero
                $tRequest = TranscriptRequest::find($fPayment->transcriptRequest->id);
                if ($tRequest) {
                    #request found, set the status to paid
                    $tRequest->p = 1;
                    $tRequest->save();

                    #foward the request
                    FowardUGTranscriptRequestJob::dispatch($tRequest->uid, now());

                }
            }
        }
    }
}
