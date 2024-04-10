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

class PaymentLogCleaningJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $logRef;
    public $JobTime;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($logRef, $JobTime)
    {
        $this->logRef = $logRef;
        $this->JobTime = $JobTime;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        #here we are to check if there's a log entry that has a corresponding entry in credo request and delete the request.
        # afterward check the balance of the specified payment and do the needful
        #grab the payment log
        $verifiedLog = PaymentLog::find($this->logRef);

        if ($verifiedLog->credoRequest->amount == $verifiedLog->amount_paid) {
            #the amounts are similar, you can delete the Credo Request Entry and process to check for paid status to render the service
            $crRequestToDelete = CredoRequest::where('txn_id', $verifiedLog->tx_id)->first();
            $crRequestToDelete->delete();

            PaymentStatusCheckJob::dispatch($verifiedLog->feePayment->uid, now());
        }


    }
}
