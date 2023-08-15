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
use Illuminate\Support\Facades\Log;

class ResultSessionalComputeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $regMonitorId;
    public $time;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($regMonitorId, $time)
    {
        $this->regMonitorId = $regMonitorId;
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
        
        //We are ready to compute this result
        // fetch the regMonitor items
        $result = RegMonitor::find($this->regMonitorId);

        $resultItems = RegMonitorItems::where('session_id', $result->session_id)
                                        ->where('student_id', $result->student_id)
                                        ->get();

         //let's declare some variables
         $cue = 0;
         $wgp =0;
         $gpa = 0;
         $cur = $result->tcr;
        //loop through and calculate the cue for the entire session

        foreach($resultItems as $k) {

            if ($k->is_passed ==='1' || $k->sess_is_passed ==='1') {

                //the course is passed

                # Check if it was registered in sessional or normally and know where to pick you twgp from
                if ($k->is_reg_sess ==='1') {
                    # this course is registered as a sessional so choolse the sessional twgp which should not be more than an E grade
                    $wgp = $wgp + $k->sess_twgp;

                }elseif ($k->is_reg_sess ==='0') {
                    // - add the twgp normally
                    $wgp = $wgp + $k->twgp;
                }

                // - compute the cue
                $cue = $cue + getCreditUnitsByCourseId($k->course_id);

            }elseif ($k->is_passed ==='0' || $k->sess_is_passed ==='0' ) {

                //nothing to add student result
            }

        }
        // compute the gpa and set the parameter
        $gpa = $wgp / $cur;

        //Log::info("gpa computed successfully");

        //store the result parameters so far generated

        // $result->s_tce = $cue;
        // $result->s_twgp = $wgp;
        // $result->s_cgpa = convertToKobo($gpa);
        // $result->save();

        //Log::info("current semester result computed successfully");

        // fetch the last result RegMonitor for the student and extract the following

        if ($result->semesters_spent ===2) {
            //this is the first time the sessional result  will be computed therefore all previous records are zero for pupose of total computations
            $ltcr = 0;
            $ltce = 0;
            $ltwgp = 0;
            $lcgpa = 0;
            $lprobation = 0;

        }elseif ($result->semesters_spent >2) {
            // previous record exist find them
            // get the previous semesters spent
            $previousSemSpent = $result->semesters_spent - 1;

            //now fetch the previous records and extract the result from there
            $lastResult = RegMonitor::where('student_id', $result->student_id)
                                    ->where('semesters_spent', $previousSemSpent)
                                    ->first();
            if ($lastResult) {
                //Log::info("Previous Result Found");
                // get ltcr
                $ltcr = $lastResult->tcr;
                // get ltce
                $ltce = $lastResult->ltce;
                // get ltwgp
                $ltwgp = $lastResult->ltwgp;
                // get lcgpa
                $lcgpa = $lastResult->lcgpa;
                // get the probation count
                $lprobation = $lastResult->r_probation_count;

            }else{
                //there was a problet fetching the last result so just assume zero entries
                //Log::info("Error!!! Not able to find the previous result");
                $ltcr = 0;
                $ltce = 0;
                $ltwgp = 0;
                $lcgpa = 0;
            }

        }
        // Update the present RegMonitor the the last result records
        // $result->ltcr = $ltcr;
        // $result->ltce = $ltce;
        // $result->ltwgp = $ltwgp;
        // $result->lcgpa = $lcgpa;
        // $result->save();

        //Log::info("Previous Result Entries updated successfully !!!");

        // Compute the CGPA and update the RegMonitor with the following:
        // compute the tcr
        $tcr = $cur;
        // tce
        $tce = $cue + $ltce;
        // twgp
        $twgp = $wgp + $ltwgp;
        // cgpa
        $cgpa = $twgp / $tcr;

        $carryOvers = RegMonitorItems::join('semester_courses as s', 's.id','=','reg_monitor_items.course_id')
                                            ->where('reg_monitor_items.student_id', $result->student_id)
                                            ->where('reg_monitor_items.session_id', $result->session_id)
                                            ->where('reg_monitor_items.is_reg_sess','1')
                                            ->where('reg_monitor_items.sess_is_passed','0')
                                            ->select('s.courseCode', 's.creditUnits')
                                            ->get();
            $creditDifference = 0;

            if ($carryOvers) {
                # carry overs found
                foreach ($carryOvers as $co) {
                    #loop through and compute the credit units
                    $creditDifference = $creditDifference + $co->creditUnits;
                }

            }


        if ($creditDifference >= 10) {

            $result->s_message = "To Withdraw";

        }elseif ($creditDifference >=1) {
            # code...
                $result->s_message = "CARRY OVER";

        }elseif ($creditDifference == 0) {
            # code...
            $result->s_message = null;

        }else{

            $result->s_message = null;

        }



        //Log::info("CGPA computed successfully !!!");

        // Enter the computed result entires to sessional part of the regMonitor since this result is Sessional
        //$result->tcr = $tcr;
        $result->s_tce = $tce;
        $result->s_twgp = $twgp;
        $result->updated_at = now();
        $result->s_cgpa = convertToKobo($cgpa);
        $result->save();



        //Log::info("Cummulative Result Recorded Successfully");

        // Evaluate the probation count based on established records and update the requisite column
        if ($cgpa <1.5) {
            //You should increment the probation count at this point and update the remark column respectively
            $probationCount = $lprobation +1;
            $result->r_probation_count = $probationCount;
            $result->save();

            //Log::info("Probation Considerations made successfully");

        }elseif ($cgpa > 1.5) {
            //nothing to update here for now since the cgpa is above probation..

        }
        // update the ComputedResultId

    }



}
