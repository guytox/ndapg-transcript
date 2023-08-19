<?php

namespace App\Jobs;

use App\Models\ComputedResult;
use App\Models\RegMonitor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class VetoResultRecomputeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $resultUid;
    public $time;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($resultUid, $time)
    {
        $this->resultUid = $resultUid;
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
            # do nothing just for bypass sake
        }else{

        }
        #first find the computed result entry
        $computedResult = ComputedResult::where('uid', $this->resultUid)->first();
        #next get all regMonitors that match this registration criteria
        $toCompute = RegMonitor::where('session_id', $computedResult->schoolsession_id)
                                ->where('semester_id', $computedResult->semester_id)
                                ->where('level_id', $computedResult->study_level)
                                ->where('program_id', $computedResult->program_id)
                                ->get();

        if ($toCompute) {
            foreach ($toCompute as $cr) {
                $time = now();
                $computeResultUId = $computedResult->uid;
                $regMonitorUid = $cr->uid;
                SubmitResultComputationJob::dispatch($computeResultUId, $regMonitorUid, $time);
            }
        }
    }
}
