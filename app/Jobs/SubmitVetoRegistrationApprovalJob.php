<?php

namespace App\Jobs;

use App\Models\RegMonitor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SubmitVetoRegistrationApprovalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

     private $sessionId;
     private $semesterId;

    public function __construct($sessionId, $semesterId)
    {
        $this->sessionId = $sessionId;
        $this->semesterId = $semesterId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        #get all pending registrations with this criteria and submit for approval
        $pendingRegs = RegMonitor::where('session_id', $this->sessionId)
                                    ->where('semester_id', $this->semesterId)
                                    ->where('std_confirm', '1')
                                    ->where('status','pending')
                                    ->get();


        foreach ($pendingRegs as $k) {
            VetoRegistrationApprovalJob::dispatch($k->id);
        }

    }
}
