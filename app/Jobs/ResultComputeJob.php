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
use Illuminate\Support\Facades\Log;

class ResultComputeJob implements ShouldQueue
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


        ComputedResultFixJob::dispatch($result->id, now());


        $resultItems = RegMonitorItems::where('monitor_id', $this->regMonitorId)->get();

         //let's declare some variables
         $cue = 0;
         $wgp =0;
         $gpa = 0;
         $cur = 0;
        //loop through and

        foreach($resultItems as $k) {

            $cur = $cur + getCreditUnitsByCourseId($k->course_id);

            if ($k->is_passed ==='1') {

                //the course is passed
                // - add the twgp
                $wgp = $wgp + $k->twgp;
                // - compute the cue
                $cue = $cue + getCreditUnitsByCourseId($k->course_id);
            }elseif ($k->is_passed ==='0') {
                //nothing to add student result
            }

        }
        // compute the gpa and set the parameter
        $gpa = $wgp / $cur;

        Log::info("gpa computed successfully -".$gpa);

        //store the result parameters so far generated

        $result->cur = $cur;
        $result->cue = $cue;
        $result->wgp = $wgp;
        $result->total_credits = $cur;
        $result->gpa = convertToKobo($gpa);
        $result->updated_at = now();
        $result->save();

        //Log::info("current semester result computed successfully");

        // fetch the last result RegMonitor for the student and extract the following

        if ($result->semesters_spent ===1) {
            //this is the first semester therefore all previous records are zero
            $ltcr = 0;
            $ltce = 0;
            $ltwgp = 0;
            $lcgpa = 0;
            $lprobation = 0;

        }elseif ($result->semesters_spent >1) {
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
                $ltce = $lastResult->s_tce;
                // get ltwgp
                $ltwgp = $lastResult->s_twgp;
                // get lcgpa
                $lcgpa = $lastResult->s_cgpa;
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
        $result->ltcr = $ltcr;
        $result->ltce = $ltce;
        $result->ltwgp = $ltwgp;
        $result->lcgpa = $lcgpa;
        $result->updated_at = now();
        $result->save();

        //Log::info("Previous Result Entries updated successfully !!!");

        // Compute the CGPA and update the RegMonitor with the following:
        // compute the tcr
        $tcr = $cur + $ltcr;
        // tce
        $tce = $cue + $ltce;
        // twgp
        $twgp = $wgp + $ltwgp;
        // cgpa
        $cgpa = $twgp / $tcr;


        if ($cue < $cur) {
            # there is a difference in credit units check and effect the message
            $creditDifference = $tcr - $tce;

            if ($creditDifference > 0) {
                $result->message = "CARRY OVER";
            }else{
                $result->message= null;
            }
        }else{

            $result->message = null;

        }

        //Log::info("CGPA computed successfully !!!");

        // Enter the computed result entires to the database
        $result->tcr = $tcr;
        $result->tce = $tce;
        $result->twgp = $twgp;
        $result->updated_at = now();
        $result->cgpa = convertToKobo($cgpa);
        $result->save();

        # if the semester is first, then duplicate this entry to the sessional values because there'd be no changes. If it second semester then the sessional computation will alter the value.

        if ($result->semester_id == 1) {
            $result->s_tce = $tce;
            $result->updated_at = now();
            $result->s_twgp = $twgp;
            $result->s_cgpa = convertToKobo($cgpa);
            $result->save();
        }

        //Log::info("Cummulative Result Recorded Successfully");

        // Evaluate the probation count based on established records and update the requisite column
        if ($result->cgpa <250 && $result->semester_id == '2') {

            switch ($result->level_id) {
                case '2':
                        $result->message = "TO WITHDRAW";
                    break;
                case '3':
                        $result->message = "TO WITHDRAW";
                    break;
                default:
                    # code...
                    break;
            }
            //You should increment the probation count at this point and update the remark column respectively
            $probationCount = $lprobation +1;
            $result->r_probation_count = $probationCount;

            $result->save();

            //Log::info("Probation Considerations made successfully");

        }elseif ($result->cgpa > 250 && $result->semester_id == '2') {
            //nothing to update here for now since the cgpa is above probation..

        }
        // update the ComputedResultId

        # Next Look for the next result that has been computed and schedule for update in the case of re-computation to correct other future occurences of this result
        ###########################################################################################

        # NEXT COMPUTED RESULT ENTRY PROCESSING (This is to reduce the stress of Manual grading)

        ###########################################################################################

        # first get the semesters spent for the next result
        $nextSemSpent = $result->semesters_spent + 1;

        if ($result->semester_id == 1) {
            #look for the next entry and submit for recomputation and that ends it

            $nextResult = RegMonitor::where('student_id', $result->student_id)
                                    ->where('semesters_spent', $nextSemSpent)
                                    ->first();
            if ($nextResult) {
                # Then result is found, check if it has been computed by looking for the compute_id entry
                if ($nextResult->r_computed_result_id >0 ) {
                    # find the computed result UID and forward it along with this computation
                    $computeResultEntry = ComputedResult::find($nextResult->r_computed_result_id);
                    $resultUid = $computeResultEntry->uid;
                    # This result has been computed, schedule it for compution
                    $time = now();
                    SubmitResultComputationJob::dispatch($resultUid, $nextResult->uid, $time);

                }

            }

        }elseif ($result->semester_id == 2) {
            # this is a second semester entry
            # check if the sessional has been computed prior to now and schdule it for immediate computation
            if ($result->s_status=='1') {
                # find the appropriate compute result uid
                $SessionalcomputeResultEntry = ComputedResult::find($result->r_computed_result_id);
                $sessionalresultUid = $SessionalcomputeResultEntry->uid;
                # Send this result for sessional computation
                $time = now();
                SubmitSessionalResultComputationJob::dispatch($sessionalresultUid, $result->uid, $time);

            }

            # check to see if there is the next session's result computation and schedule it for a delayed computation so the sessional computes first before that one

            $nextResult = RegMonitor::where('student_id', $result->student_id)
                                    ->where('semesters_spent', $nextSemSpent)
                                    ->first();
            if ($nextResult) {
                # Then result is found, check if it has been computed by looking for the compute_id entry
                if ($nextResult->r_computed_result_id >0 ) {
                    # find the computed result UID and forward it along with this computation
                    $computeResultEntry = ComputedResult::find($nextResult->r_computed_result_id);
                    $resultUid = $computeResultEntry->uid;
                    # This result has been computed, schedule it for compution
                    $scTime = Carbon::now()->addSeconds(30);
                    $time = now();
                    SubmitResultComputationJob::dispatch($resultUid, $nextResult->uid, $time)->delay($scTime);

                }

            }

        }

        # This ends next result computation.

    }
}
