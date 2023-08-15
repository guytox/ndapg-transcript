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

class SubmitSessionalResultComputationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $computeResultUId;
    public $regMonitorUid;
    public $time;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($computeResultUId, $regMonitorUid, $time)
    {
        $this->computeResultUId = $computeResultUId;
        $this->regMonitorUid = $regMonitorUid;
        $this->time = $time;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        if ($this->time <= now()) {
            # then do nothing
        }else{
            # think of what to do at this time
        }
        #fetch the computed result id here
        $computeResultId = ComputedResult::where('uid', $this->computeResultUId)->first();
        $computeResultId->last_updated_at = now();
        $computeResultId->updated_at = now();
        $computeResultId->save();
        # Right now just fetch the registration records
        $regMonitorEntry = RegMonitor::where('uid', $this->regMonitorUid)->first();
        #update the entry with the computed result id
        $regMonitorEntry->s_status = '1';
        $regMonitorEntry->save();
        # send them for result courseRegistration grading immediatelly immediately
        $regEntries = RegMonitorItems::where('student_id', $regMonitorEntry->id)
                                    ->where('session_id', $regMonitorEntry->session_id)
                                    ->get();
        #Check if you found something then proceed else end the job
        if (count($regEntries)>0) {
            #All set to shoot the jobs
            foreach ($regEntries as $q) {
                $regId = $q->id;
                $time = now();
                SemesterCourseGradingJob::dispatch($regId, $time);
                SemesterCourseSessionalGradingJob::dispatch($regId, $time);
            }
        }

        # Schedule the result for computation one minute after now
        $regMonitorId = $regMonitorEntry->id;
        $scTime = Carbon::now()->addSeconds(15);
        $time = now();
        ResultSessionalComputeJob::dispatch($regMonitorId, $time)->delay($scTime);

    }
}
