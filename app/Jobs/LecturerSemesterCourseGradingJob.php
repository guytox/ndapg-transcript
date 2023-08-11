<?php

namespace App\Jobs;

use App\Models\GradingSystemItems;
use App\Models\RegMonitor;
use App\Models\RegMonitorItems;
use App\Models\SemesterCourse;
use App\Models\StudentRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class LecturerSemesterCourseGradingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $regId;
    public $time;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($regId, $time)
    {
        $this->regId = $regId;
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
            #nothing unusual
        }else{

            Log::info("something unsuual about this First CA grading");
        }

        //fetch the regMonitorItem including the monitor to determine the present semesters spent

        Log::info("Begining Lecturer Semester Course Grading for ". $this->regId);



        $toGrade = RegMonitorItems::find($this->regId);

        // Log::info($toGrade);

        $semestercourse = SemesterCourse::find($toGrade->course_id);

        //compute the total score and compare with the lecturer total and update where neccessary
        $total=0;

        $total = $total +$toGrade->ca1;
        $total = $total +$toGrade->ca2;
        $total = $total +$toGrade->ca3;
        $total = $total +$toGrade->ca4;
        $total = $total +$toGrade->exam;

        if ($toGrade->ltotal != $total) {

            //Log::info('Lecturer Total Updated after Hod Approval');



        }else{
            //Log::info("Lecturer Total Same with HOD Total, No Change");

        }

        //update the totals
        $toGrade->ltotal = $total;
        $toGrade->save();


        //get the grading system from students record.
        $gradeQuery = StudentRecord::find($toGrade->student_id);



        if ($gradeQuery) {

            foreach ($gradeQuery->gradingItems as $gr) {

                // check to see if total falls between the lower boundary and the upper boundary

                if ( $total >= $gr->lower_boundary && $total <= $gr->upper_boundary) {



                    $toGrade->lgrade = $gr->grade_letter;

                    $twgp = $semestercourse->creditUnits * $gr->weight_points;

                    $toGrade->twgp = $twgp;

                    $toGrade->save();


                    Log::info("This entry has been saved with total of ".$total." which qualifies for ". $gr->grade_letter);

                    Log::info("Grade Update of ". $semestercourse->courseCode . "Successful for " . $gradeQuery->matric);
                }

            }


        }
    }
}
