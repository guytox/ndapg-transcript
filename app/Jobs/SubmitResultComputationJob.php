<?php

namespace App\Jobs;

use App\Models\ComputedResult;
use App\Models\RegMonitor;
use App\Models\RegMonitorItems;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SubmitResultComputationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $computeResultUId;
    public $regMonitorUid;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($computeResultUId, $regMonitorUid)
    {
        $this->computeResultUId = $computeResultUId;
        $this->regMonitorUid = $regMonitorUid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        #fetch the computed result id here
        $computeResultId = ComputedResult::where('uid', $this->computeResultUId)->first();
        $computeResultId->last_updated_at = now();
        $computeResultId->updated_at = now();
        $computeResultId->save();
        # Right now just fetch the registration records
        $regMonitorEntry = RegMonitor::where('uid', $this->regMonitorUid)->first();
        #update the entry with the computed result id
        $regMonitorEntry->r_computed_result_id = $computeResultId->id;
        # if this result is being recomputed after computation, leave the status untouched, but that should happen only if the approval status is approved right now
        if ($regMonitorEntry->r_status == 'approved') {
            # Do nothing, this is a recomputation case
        }else{

            $regMonitorEntry->r_status = 'pending';
        }

        $regMonitorEntry->save();
        # send them for result courseRegistration grading immediatelly immediately
        $regEntries = RegMonitorItems::where('monitor_id', $regMonitorEntry->id)->get();
        #Check if you found something then proceed else end the job
        if (count($regEntries)>0) {
            #All set to shoot the jobs
            foreach ($regEntries as $q) {
                $regId = $q->id;
                $time = now();
                SemesterCourseGradingJob::dispatch($regId, $time);
            }
        }

        # Schedule the result for computation one minute after now
        $regMonitorId = $regMonitorEntry->id;
        $scTime = Carbon::now()->addSeconds(10);
        ResultComputeJob::dispatch($regMonitorId)->delay($scTime);

    }
}
