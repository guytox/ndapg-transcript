<?php

namespace App\Jobs;

use App\Models\RegMonitorItems;
use App\Models\ResultAuditTrail;
use App\Models\SemesterCourse;
use App\Models\StudentRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GradeAllJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $gradeCourse;
    public $gradeSession;
    public $gradeSemester;
    public $gradeStaff;
    public $grading;
    public $allocationUid;
    public $matric;
    public $ca1;
    public $ca2;
    public $ca3;
    public $ca4;
    public $exam;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($gradeCourse,$gradeSession,$gradeSemester,$gradeStaff, $grading, $allocationUid, $matric, $ca1, $ca2, $ca3, $ca4, $exam)
    {
        $this->gradeCourse = $gradeCourse;
        $this->gradeSession = $gradeSession;
        $this->gradeSemester = $gradeSemester;
        $this->gradeStaff = $gradeStaff;
        $this->grading = $grading;
        $this->allocationUid = $allocationUid;
        $this->matric = $matric;
        $this->ca1 = $ca1;
        $this->ca2 = $ca2;
        $this->ca3 = $ca3;
        $this->ca4 = $ca4;
        $this->exam = $exam;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //get the semester course in question
        $semesterCourse = SemesterCourse::find($this->gradeCourse);
        # get the student in question
        $student = StudentRecord::where('matric', $this->matric)->first();

        if ($student) {
            if ($semesterCourse) {
                # both Semester course and student have been found for us to get here so feel free to move
                # get the reg monitor item for this upload at this point
                $monitorItem = RegMonitorItems::where([
                    'student_id' => $student->id,
                    'session_id' => $this->gradeSession,
                    'course_id' => $this->gradeCourse,
                    'semester_id' => $this->gradeSemester,
                ])
                ->first();
                #next check if this course is approved before proceeding
                if ($monitorItem) {
                    # this regMonitorItem has been found proceed to check approval status
                    if ($monitorItem->status == 'approved') {
                        # nothing should prevent us from uploading now
                        $caTotal = $this->ca1 + $this->ca2 + $this->ca3 + $this->ca4 + $this->exam;
                        $prevScore = convertToNaira($monitorItem->ca1 + $monitorItem->ca2 + $monitorItem->ca3 + $monitorItem->ca4 + $monitorItem->exam);
                        $maxScore = $semesterCourse->max_ca + $semesterCourse->max_exam;
                        #check to see if this addition will not cause problem
                        if ($caTotal <= $maxScore) {
                            #this entry is valid, check to see if there is a difference in exam then ignore the change request completely
                            if ($caTotal != $prevScore) {
                                #there's a difference to fire on

                                $monitorItem->ca1 = convertToKobo($this->ca1);
                                $monitorItem->ca2 = convertToKobo($this->ca2);
                                $monitorItem->ca3 = convertToKobo($this->ca3);
                                $monitorItem->ca4 = convertToKobo($this->ca4);
                                $monitorItem->exam = convertToKobo($this->exam);
                                $monitorItem->save();

                                LecturerSemesterCourseGradingJob::dispatch($monitorItem->id);

                                Log::info("Total  Score of ". $caTotal . " for ". $semesterCourse->courseCode. " Entered Successfully for " . $this->matric);

                                # update the Audit trail
                                Log::info("updating Audit Trail .......");

                                $data =[
                                    'user_id' => $this->gradeStaff,
                                    'changes' => "[ALL]",
                                    'old_values' => '['.convertToBoolean($monitorItem->ca1).']['. convertToBoolean($monitorItem->ca2).']['. convertToBoolean($monitorItem->ca3).']['. convertToBoolean($monitorItem->ca4).']['. convertToBoolean($monitorItem->exam).']',
                                    'new_values' => '['.number_format($this->ca1, 2).']['.number_format($this->ca2, 2).']['.number_format($this->ca3, 2).']['.number_format($this->ca4, 2).']['.number_format($this->exam, 2).'][',
                                    'course_id' => $this->gradeCourse,
                                    'session_id' => $this->gradeSession,
                                    'semester_id' => $this->gradeSemester,
                                    'student_id' => $student->id,
                                ];

                                $logEntry = ResultAuditTrail::create($data);

                                Log::info(" Audit Trail Updated Successfully !!!");

                            }else{

                                Log::info("No differnce in Total found for grading of ". $semesterCourse->courseCode. " for ". $this->matric. " Skipping ....");
                            }


                        }else {
                            Log::info("Total Grading of ". $semesterCourse->courseCode . "for " . $this->matric . " Failed  because max Exam Score has been exceeded .......");
                        }


                    }else{

                        Log::info("Registration of ". $semesterCourse->courseCode . " not yet Approved for ". $this->matric . " - Skipping grading .......");
                    }

                }else{
                    Log::info($this->matric ." has not registered for ". $semesterCourse->courseCode . " Skipping grading .........." );
                }


            }else{
                Log::info("**** Semester cours with id - ". $this->gradeCourse ." Not Found for grade upload ***");

            }
        }else {
            Log::info("**** Student with matric no - ". $this->matric ." Not Found ***");
        }
    }
}
