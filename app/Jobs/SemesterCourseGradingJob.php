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

class SemesterCourseGradingJob implements ShouldQueue
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

        if ($toGrade->cfm_ca1 ==='1') {
            $total = $total +$toGrade->ca1;
        }

        if ($toGrade->cfm_ca2 ==='1') {
            $total = $total +$toGrade->ca2;
        }

        if ($toGrade->cfm_ca3 ==='1') {
            $total = $total +$toGrade->ca3;
        }

        if ($toGrade->cfm_ca4 ==='1') {
            $total = $total +$toGrade->ca4;
        }

        if ($toGrade->cfm_exam ==='1') {
            $total = $total +$toGrade->exam;
        }

        if ($toGrade->ltotal != $total) {

            //Log::info('Lecturer Total Updated after Hod Approval');



        }else{
            //Log::info("Lecturer Total Same with HOD Total, No Change");

        }

        //update the totals
        $toGrade->ltotal = $total;
        $toGrade->gtotal = $total;
        $toGrade->save();

        //if registered carryOver, update the entry to the semesters spent where the student first failed the course(co_sem_spent).
        if ($toGrade->is_carryOver ===1) {
            //find the first entry for this course fetch the semesers spent and update toGrade
            $firstEntry = RegMonitorItems::where('student_id',$toGrade->student_id)
                                        ->where('course_id', $toGrade->course_id)
                                        ->first();
            //get the co_semesters spent and update toGrade value
            $toGrade->co_sem_spent = $firstEntry->co_sem_spent;
            $toGrade->save();

            //Log:info('First Carry Over Semester spent updated on current registration');

        }
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
                    $toGrade->ggrade = $gradeLetter;
                    $toGrade->save();

                    //Log::info("Grade Letter Recorded Successfully !!!");

                    //determine if the student has passed or failed based on grading system loop.
                    //is passed value.
                    if ($v->credit_earned === 0) {

                        $toGrade->is_passed = '0';
                        $toGrade->is_reg_sess = '1';
                        $toGrade->is_co_passed = '0';


                    }elseif ($v->credit_earned ===1) {

                        $toGrade->is_passed = '1';
                        $toGrade->is_co_passed = '1';
                        //reverse any entry at the sessional that is wrongly entered
                        $toGrade->is_reg_sess = '0';
                        $toGrade->sess_is_passed = '0';
                        $toGrade->sess_total = 0;
                        $toGrade->sess_grade = null;
                        $toGrade->sess_twgp = 0;
                    }

                    // - weighted points.

                    $twgp = $semestercourse->creditUnits * $v->weight_points;

                    $toGrade->twgp = $twgp;
                    $toGrade->save();

                    //Log::info("Total Weighted grade Point Recorded Successfully");

                    if ($v->credit_earned===1) {
                        // Student has passed check if it is a carry over and update the necessary columns
                        if ($toGrade->is_carryOver ===1) {
                            // - set the co_passed_sem_spent to the current semesters spent for all occurences
                            $coPassedUpdate = RegMonitorItems::where('student_id', $toGrade->student_id)
                                                            ->where('course_id', $toGrade->course_id)
                                                            ->update(array('is_co_passed'=>'1', 'co_passed_sem_spent'=>$GradeCheck->semesters_spent));

                            //Log::info("Previous and Present Carry Over details updated successfully !!!");

                        }elseif ($toGrade->is_carryOver ===0) {
                            //in the case of recomputation reverse the co_semesters spent and set it to null
                            //Set the coSemSpent and coPassedSemSpent to null
                            $toGrade->co_sem_spent = null;
                            $toGrade->save();
                            //leave is_carryOver, is_passed and is_co_passed as zero
                            //Log::info("First time carry over entry reversed !!!");
                            $coPassedUpdate = RegMonitorItems::where('student_id', $toGrade->student_id)
                                                            ->where('course_id', $toGrade->course_id)
                                                            ->update(array('is_co_passed'=>'1'));


                        }
                    }elseif ($v->credit_earned===0) {
                        //The student has failed

                        if ($toGrade->is_carryOver ===1) {
                            // This is also a carry over
                            // - set the co_passed_sem_spent to the current semesters spent for all occurences
                            $coPassedUpdate = RegMonitorItems::where('student_id', $toGrade->student_id)
                                                            ->where('course_id', $toGrade->course_id)
                                                            ->update(array('is_co_passed'=>'0', 'co_passed_sem_spent'=>$GradeCheck->semesters_spent));

                            // The carryOver Semesters Spent has been updated previously and no need to update it now


                        }elseif ($toGrade->is_carryOver ===0) {

                            //This is the first time he has failed,
                            //Set the coSemSpent and coPassedSemSpent to the same value
                            $toGrade->co_sem_spent = $GradeCheck->semesters_spent;
                            $toGrade->save();
                            //leave is_carryOver, is_passed and is_co_passed as zero
                            //Log::info("First time carry over recorded Successfully !!!");
                            // - set the co_passed_sem_spent to the current semesters spent for all occurences
                            $coPassedUpdate = RegMonitorItems::where('student_id', $toGrade->student_id)
                                                            ->where('course_id', $toGrade->course_id)
                                                            ->update(array('is_co_passed'=>'0', 'co_passed_sem_spent'=>$GradeCheck->semesters_spent));

                        }

                    }

                }
            }


        }
    }
}
