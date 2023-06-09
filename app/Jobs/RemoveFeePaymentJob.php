<?php

namespace App\Jobs;

use App\Models\FeePayment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RemoveFeePaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        #first get the fee payment monitor
        $toRemove = FeePayment::find($this->id);
        Log::info("[RemoveFeePaymentJob] Attempting to remove - ".$toRemove->uid);

        if ($toRemove->delete()) {
            Log::info("Fee Payment Deleted Successfully!!! [RemoveFeePaymentJob]");
        }else{
            Log::info("[RemoveFeePaymentJob] Error!!! Failed to delete");
        }

    }
}
