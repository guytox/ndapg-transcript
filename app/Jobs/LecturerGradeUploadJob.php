<?php

namespace App\Jobs;

use App\Models\CourseAllocationItems;
use App\Models\RegMonitorItems;
use App\Models\ResultAuditTrail;
use App\Models\SemesterCourse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LecturerGradeUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $itemId;
    public $ca1;
    public $ca2;
    public $ca3;
    public $ca4;
    public $exam;
    public $monitorUid;
    public $staffId;
    public $userId;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($monitorUid, $itemId, $staffId, $userId,  $ca1, $ca2, $ca3, $ca4, $exam, $whatToGrade,$semesterCourseId)
    {
        $this->monitorUid = $monitorUid;
        $this->itemId = $itemId;
        $this->ca1 = floatval($ca1);
        $this->ca2 = floatval($ca2);
        $this->ca3 = floatval($ca3);
        $this->ca4 = floatval($ca4);
        $this->exam = floatval($exam);
        $this->staffId = $staffId;
        $this->userId = $userId;
        $this->whatToGrade = $whatToGrade;
        $this->semesterCourseId = $semesterCourseId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->staffId ===$this->userId) {

            $courseAll = CourseAllocationItems::join('course_allocation_monitors as m', 'm.id','=','course_allocation_items.allocation_id')
                                                ->select('course_allocation_items.*', 'm.session_id', 'm.semester_id')
                                                ->where(['course_allocation_items.uid'=>$this->itemId, 'course_allocation_items.staff_id'=>$this->staffId, 'course_allocation_items.can_grade'=>1, 'graded'=>1, 'grading_completed'=>2, 'submitted'=>2])->first();
            //create general course monitor variables variables

            $courseId = $this->semesterCourseId;
            $session_id = $courseAll->session_id;
            $semester_id = $courseAll->semester_id;

            //generate received data
            $uData = '['.number_format($this->ca1,2).']'.'['.number_format($this->ca2,2).']'.'['.number_format($this->ca3,2).']'.'['.number_format($this->ca4,2).']'.'['.number_format($this->exam,2).']';

            //fetch the entry and let's begin analysis;
            $oldentries = RegMonitorItems::where(['id'=> $this->itemId, 'status'=> 'approved'])->first();

            if ($oldentries->id !='') {
                //compile oldentries for comparison
                $oldData = '['.convertToBoolean($oldentries->ca1).']'.'['.convertToBoolean($oldentries->ca2).']'.'['.convertToBoolean($oldentries->ca3).']'.'['.convertToBoolean($oldentries->ca4).']'.'['.convertToBoolean($oldentries->exam).']';

                if ($oldData!=$uData) {
                    //you have a difference, perform checks and proceed
                    //get the semester course
                    $semCourse = SemesterCourse::find($courseId);

                    //check that the uploaded scores do not exceed the limits for ca and exam
                    if ($this->ca1+$this->ca2+$this->ca3+$this->ca4<=$semCourse->max_ca && $this->exam<=$semCourse->max_exam) {

                        if ($this->whatToGrade ==='8X34' && $this->ca1+floatval(convertToBoolean($oldentries->ca2))+floatval(convertToBoolean($oldentries->ca3))+floatval(convertToBoolean($oldentries->ca4))<=floatval($semCourse->max_ca)  && $oldentries->cfm_ca1==='0') {

                            //prepare for logging
                            $data =[
                                'user_id' => $this->staffId,
                                'changes' => "[CA1]",
                                'old_values' => '['.convertToBoolean($oldentries->ca1).']',
                                'new_values' => '['.number_format($this->ca1,2).']',
                                'course_id' => $courseId,
                                'session_id' => $session_id,
                                'semester_id' => $semester_id,
                                'student_id' => $oldentries->student_id,
                            ];

                            $logEntry = ResultAuditTrail::create($data);

                            if ($logEntry) {
                                //all clear, you may write and proceed
                                $oldentries->ca1 = covertToInt($this->ca1);
                                $oldentries->ltotal = $oldentries->ca2 + $oldentries->ca3 + $oldentries->ca4 + $oldentries->exam  + covertToInt($this->ca1);
                                $oldentries->save();
                            }


                        }elseif ($this->whatToGrade ==='8OE4' && $this->ca2+floatval(convertToBoolean($oldentries->ca1))+floatval(convertToBoolean($oldentries->ca3))+floatval(convertToBoolean($oldentries->ca4))<=floatval($semCourse->max_ca)  && $oldentries->cfm_ca2==='0') {

                            //prepare for logging
                            $data =[
                                'user_id' => $this->staffId,
                                'changes' => "[CA2]",
                                'old_values' => '['.convertToBoolean($oldentries->ca2).']',
                                'new_values' => '['.number_format($this->ca2,2).']',
                                'course_id' => $courseId,
                                'session_id' => $session_id,
                                'semester_id' => $semester_id,
                                'student_id' => $oldentries->student_id,
                            ];

                            $logEntry = ResultAuditTrail::create($data);

                            if ($logEntry) {
                                //all clear, you may write and proceed
                                $oldentries->ca2 = covertToInt($this->ca2);
                                $oldentries->ltotal = $oldentries->ca1 + $oldentries->ca3 + $oldentries->ca4 + $oldentries->exam + covertToInt($this->ca2);
                                $oldentries->save();
                            }



                        }elseif ($this->whatToGrade ==='3XS4' && $this->ca3+floatval(convertToBoolean($oldentries->ca2))+floatval(convertToBoolean($oldentries->ca1))+floatval(convertToBoolean($oldentries->ca4))<=floatval($semCourse->max_ca)  && $oldentries->cfm_ca3==='0') {

                            //prepare for logging
                            $data =[
                                'user_id' => $this->staffId,
                                'changes' => "[CA3]",
                                'old_values' => '['.convertToBoolean($oldentries->ca3).']',
                                'new_values' => '['.number_format($this->ca3,2).']',
                                'course_id' => $courseId,
                                'session_id' => $session_id,
                                'semester_id' => $semester_id,
                                'student_id' => $oldentries->student_id,
                            ];

                            $logEntry = ResultAuditTrail::create($data);

                            if ($logEntry) {
                                //all clear, you may write and proceed
                                $oldentries->ca3 = covertToInt($this->ca3);
                                $oldentries->ltotal = $oldentries->ca2 + $oldentries->ca1 + $oldentries->ca4 + $oldentries->exam  + covertToInt($this->ca3);
                                $oldentries->save();
                            }



                        }elseif ($this->whatToGrade ==='3x34' && $this->ca4+floatval(convertToBoolean($oldentries->ca2))+floatval(convertToBoolean($oldentries->ca3))+floatval(convertToBoolean($oldentries->ca1))<=floatval($semCourse->max_ca)  && $oldentries->cfm_ca4==='0') {

                            //prepare for logging
                            $data =[
                                'user_id' => $this->staffId,
                                'changes' => "[CA4]",
                                'old_values' => '['.convertToBoolean($oldentries->ca4).']',
                                'new_values' => '['.number_format($this->ca4,2).']',
                                'course_id' => $courseId,
                                'session_id' => $session_id,
                                'semester_id' => $semester_id,
                                'student_id' => $oldentries->student_id,
                            ];

                            $logEntry = ResultAuditTrail::create($data);

                            if ($logEntry) {
                                //all clear, you may write and proceed
                                $oldentries->ca4 = covertToInt($this->ca4);
                                $oldentries->ltotal = $oldentries->ca2 + $oldentries->ca3 + $oldentries->ca1 + $oldentries->exam  + covertToInt($this->ca4);
                                $oldentries->save();
                            }



                        }elseif ($this->whatToGrade ==='8X3X' && $this->exam<=floatval($semCourse->max_exam)  && $oldentries->cfm_exam==='0') {

                            //prepare for logging
                            $data =[
                                'user_id' => $this->staffId,
                                'changes' => "[EXAM]",
                                'old_values' => '['.convertToBoolean($oldentries->exam).']',
                                'new_values' => '['.number_format($this->exam,2).']',
                                'course_id' => $courseId,
                                'session_id' => $session_id,
                                'semester_id' => $semester_id,
                                'student_id' => $oldentries->student_id,
                            ];

                            $logEntry = ResultAuditTrail::create($data);

                            if ($logEntry) {
                                //all clear, you may write and proceed
                                $oldentries->exam = covertToInt($this->exam);
                                $oldentries->ltotal = $oldentries->ca2 + $oldentries->ca3 + $oldentries->ca4 + $oldentries->ca1  + covertToInt($this->exam);
                                $oldentries->save();
                            }



                        }elseif ($this->whatToGrade ==='3XE8' && $this->ca1+$this->ca2+$this->ca3+$this->ca4+$this->exam <=floatval($semCourse->max_ca)+floatval($semCourse->max_exam) && $oldentries->cfm_ca1==='0' && $oldentries->cfm_ca2==='0' && $oldentries->cfm_ca3==='0' && $oldentries->cfm_ca4==='0' && $oldentries->cfm_exam==='0') {

                            //prepare for logging
                            $data =[
                                'user_id' => $this->staffId,
                                'changes' => "[ALL]",
                                'old_values' => $oldData,
                                'new_values' => $uData,
                                'course_id' => $courseId,
                                'session_id' => $session_id,
                                'semester_id' => $semester_id,
                                'student_id' => $oldentries->student_id,
                            ];

                            $logEntry = ResultAuditTrail::create($data);

                            if ($logEntry) {
                                //all clear, you may write and proceed
                                $oldentries->ca1 = covertToInt($this->ca1);
                                $oldentries->ca2 = covertToInt($this->ca2);
                                $oldentries->ca3 = covertToInt($this->ca3);
                                $oldentries->ca4 = covertToInt($this->ca4);
                                $oldentries->exam = covertToInt($this->exam);
                                $oldentries->ltotal = covertToInt($this->ca1) + covertToInt($this->ca2) + covertToInt($this->ca3) + covertToInt($this->ca4) + covertToInt($this->exam);
                                $oldentries->save();
                            }





                        }


                    }


                }

            }




        }

    }
}
