<?php

namespace App\Jobs;

use App\Models\CourseAllocationItems;
use App\Models\RegMonitorItems;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class HodGradeApprovalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $toConfirm;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($toConfirm)
    {
        $this->toConfirm = $toConfirm;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //fetch the lecturer course entry from the database
        $toGrade = CourseAllocationItems::join('course_allocation_monitors as c', 'c.id', '=', 'course_allocation_items.allocation_id')
                                        ->where('course_allocation_items.id', $this->toConfirm)
                                        ->select('course_allocation_items.*', 'c.session_id', 'c.semester_id')
                                        ->first();
        //get course
        $theCourse = $toGrade->course_id;
        //get the session
        $theSession = $toGrade->session_id;
        //get the semester
        $theSemester = $toGrade->semester_id;
        //fetch all the students that have registered for the selection
        $allRegistrants = RegMonitorItems::where('session_id', $theSession)
                                        ->where('semester_id', $theSemester)
                                        ->where('course_id', $theCourse)
                                        ->get();
        # set the time
        $time = now();
        // pass them to the grading job

        if (count($allRegistrants)>0) {

            //records found, pass each entry to the SemesterCourseGradingJob
            foreach ($allRegistrants as $v) {

                $regId = $v->id;

                SemesterCourseGradingJob::dispatch($regId, $time);

            }


        }
        // End the process here
    }
}
