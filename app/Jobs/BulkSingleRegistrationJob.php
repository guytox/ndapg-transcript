<?php

namespace App\Jobs;

use App\Models\RegMonitor;
use App\Models\RegMonitorItems;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BulkSingleRegistrationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $monitorId;
    private $rCourseCode;
    private $cCategory;
    private $cAction;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($monitorId, $rCourseCode, $cCategory, $cAction)
    {
        $this->monitorId = $monitorId;
        $this->rCourseCode = $rCourseCode;
        $this->cCategory = $cCategory;
        $this->cAction = $cAction;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        #find the monitor
        $regMonitor = RegMonitor::find($this->monitorId);

        if ($this->cAction == 1) {
            # We are to add
            #set confirmation parameters
            $cfmca1 = '0';
            $cfmca2 = '0';
            $cfmca3 = '0';
            $cfmca4 = '0';
            $cfmexam = '0';
            $coStatus = 0;
        #prepare the data for import into the table
            $data =[
                'monitor_id' => $regMonitor->id,
                'student_id' => $regMonitor->student_id,
                'course_id' => $this->rCourseCode,
                'session_id' => $regMonitor->session_id,
                'semester_id' => $regMonitor->semester_id,
                'category' => $this->cCategory,
                'status' => $regMonitor->status,
                'is_carryOver' => $coStatus,
                'cfm_ca1' => $cfmca1,
                'cfm_ca2' => $cfmca2,
                'cfm_ca3' => $cfmca3,
                'cfm_ca4' => $cfmca4,
                'cfm_exam' => $cfmexam,
            ];

            //insert the record
            RegMonitorItems::updateOrCreate(['monitor_id' => $regMonitor->id, 'student_id' => $regMonitor->student_id, 'course_id' => $this->rCourseCode, 'session_id' => $regMonitor->session_id], $data);

        }elseif ($this->cAction == 2) {
            # we are to remove
            $toDelete = RegMonitorItems::where('monitor_id', $regMonitor->id)
                                        ->where('course_id', $this->rCourseCode)
                                        ->where('session_id', $regMonitor->session_id)
                                        ->where('semester_id', $regMonitor->semester_id)
                                        ->where('student_id', $regMonitor->student_id)
                                        ->first();

            if ($toDelete) {
                # record found proceed to delete
            $toDelete->delete();
            }
        }


    }
}
