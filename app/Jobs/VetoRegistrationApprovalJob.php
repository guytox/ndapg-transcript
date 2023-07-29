<?php

namespace App\Jobs;

use App\Models\RegMonitor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class VetoRegistrationApprovalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $id;

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
        #get the reg monitor entry

        $ap = RegMonitor::find($this->id);

        $ap->ro_approval = '1';
        $ap->ro_approvalDate = now();
        $ap->ro_approver = $ap->student->programme->department->registration_officer_id;
        $ap->hod_approval = '1';
        $ap->hod_approvalDate = now();
        $ap->hod_approver = $ap->student->programme->department->hod_id;
        $ap->dean_approval = '1';
        $ap->dean_approvalDate = now();
        $ap->dean_approver = $ap->student->programme->department->faculty->dean_id;
        $ap->status = 'approved';
        $ap->save();

        Log::info($ap->session_id . " - ". $ap->semester_id . ' - Veto Registration Approval Sucessful for - '. $ap->student->matric );

    }
}
