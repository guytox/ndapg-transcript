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

class LecturerSemesterCourseGradingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $regId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($regId)
    {
        $this->regId = $regId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //fetch the regMonitorItem including the monitor to determine the present semesters spent
        $GradeCheck = RegMonitorItems::join('reg_monitors as r','r.id','=', 'reg_monitor_items.monitor_id')
                                ->where('reg_monitor_items.id', $this->regId)
                                ->select('reg_monitor_items.*', 'r.semesters_spent')
                                ->first();


        $toGrade = RegMonitorItems::find($this->regId);

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

            //fetch the grading system items and run a loop to grade.
            $gradeItems =GradingSystemItems::where('grading_system_id', $gradeQuery->grading_system)
                                            ->get();
            foreach ($gradeItems as $v) {

                // check to see if total falls between the lower boundary and the upper boundary
                if ($total <= convertToKobo($v->upper_boundary) && $total >= convertToKobo($v->lower_boundary)) {
                    //total matches this particular selection, lets sort out some variables.

                    //gradeLetter.
                    $gradeLetter = $v->grade_letter;

                    $toGrade->lgrade = $gradeLetter;
                    $toGrade->save();

                    //Log::info("Grade Letter Recorded Successfully !!!");

                    //determine if the student has passed or failed based on grading system loop.
                    //is passed value.
                    
                    // - weighted points.

                    $twgp = $semestercourse->creditUnits * $v->weight_points;

                    $toGrade->twgp = $twgp;
                    $toGrade->save();

                    //Log::info("Total Weighted grade Point Recorded Successfully");

                }
            }


        }
    }
}
