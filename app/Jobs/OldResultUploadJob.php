<?php

namespace App\Jobs;

use App\Models\CourseAllocationItems;
use App\Models\CourseAllocationMonitor;
use App\Models\RegMonitorItems;
use App\Models\SemesterCourse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class OldResultUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

     public $staffId;
     public $sessionId;
     public $semesterId;
     public $time;
     public $matric;
     public $studentId;
     public $courseId;
     public $courseCode;
     public $totalScore;

    public function __construct($staffId, $sessionId, $semesterId, $time, $matric, $studentId, $courseId, $courseCode, $totalScore)
    {
        $this->staffId = $staffId;
        $this->sessionId = $sessionId;
        $this->semesterId = $semesterId;
        $this->time = $time;
        $this->matric = $matric;
        $this->studentId = $studentId;
        $this->courseId = $courseId;
        $this->courseCode = $courseCode;
        $this->totalScore = $totalScore;


    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        # check the allocation status of the semester course
        $gCourse = SemesterCourse::find($this->courseId);

        if ($gCourse) {
            #course found continue logic
            Log::info("course found");
            # check the coursereg semester and session and fix the allocation
            $regMonItemId = RegMonitorItems::where('course_id', $this->courseId)
                                            ->where('student_id', $this->studentId)
                                            ->where('session_id', $this->sessionId)
                                            ->first();
            if ($regMonItemId) {
                #regMonitorItemFound
                $newSemesterId = $regMonItemId->semester_id;

                #find the student and fetch the matric no


                Log::info("Reg Monitor Entry Found". $regMonItemId->id);

                #next get allocation for the department for this semester
                $allMCheck = CourseAllocationMonitor::where('department_id', $gCourse->department_id)
                                                    ->where('session_id', $this->sessionId)
                                                    ->where('semester_id', $newSemesterId)
                                                    ->first();

                if ($allMCheck) {

                    #Monitor is found, get the id and proceed to set parameters for grading;
                    #next find the course
                    $alCoCheck = CourseAllocationItems::where('allocation_id', $allMCheck->id)
                                ->where('course_id', $this->courseId)
                                ->where('can_grade', '1')
                                ->first();

                    if ($alCoCheck) {
                        # Allocation entry is found, adopt uid and continue
                        $allocationUid = $alCoCheck->uid;
                        $gradeStaff = $alCoCheck->staff_id;
                    }else {
                        # create allocatoin, adopt uid and continue
                        $newAlloc = CourseAllocationItems::updateOrCreate([
                            'allocation_id' => $allMCheck->id,
                            'course_id' => $this->courseId,
                            'can_grade' => '1'
                        ],[
                            'allocation_id' => $allMCheck->id,
                            'course_id' => $this->courseId,
                            'can_grade' => '1',
                            'staff_id' => $gCourse->department->exam_officer_id,
                            'uid' => uniqid('sca_')
                        ]);

                        $allocationUid = $newAlloc->uid;
                        $gradeStaff = $newAlloc->staff_id;
                    }

                }else{
                    # Monitor doesn't exit, proceed to create and alloecate course
                    $newMon = CourseAllocationMonitor::updateOrCreate([
                        'department_id' => $gCourse->department,
                        'session_id' => $this->sessionId,
                        'semester_id' => $newSemesterId
                    ],[
                        'department_id' => $gCourse->department,
                        'session_id' => $this->sessionId,
                        'semester_id' => $newSemesterId,
                        'created_by' => $gCourse->department->hod_id,
                        'uid' => uniqid('cam_')
                    ]);

                    if ($newMon) {
                        # New allocaton sorted, now allocate the course and move forward
                        # create allocatoin, adopt uid and continue
                        $newAllocid = CourseAllocationItems::updateOrCreate([
                            'allocation_id' => $newMon->id,
                            'course_id' => $this->courseId,
                            'can_grade' => '1'
                        ],[
                            'allocation_id' => $newMon->id,
                            'course_id' => $this->courseId,
                            'can_grade' => '1',
                            'staff_id' => $gCourse->department->exam_officer_id,
                            'uid' => uniqid('sca_')
                        ]);

                        $allocationUid = $newAllocid->uid;
                        $gradeStaff = $newAllocid->staff_id;

                    }
                }

                    # allocation issue fixed, proceed to forward the course for grading here
                    # send the course for grading
                    $gradeCourse = $this->courseId;
                    $gradeSession = $this->sessionId;
                    $gradeSemester = $newSemesterId;
                    $grading = 'all'; // check the implication
                    $matric = getStudentByStudentId($this->studentId)->matric;
                    $time = now();
                    $ca1 = 0;
                    $ca2 = 0;
                    $ca3 = 0;
                    $ca4 = 0;
                    $exam = $this->totalScore;

                    GradeAllJob::dispatch($gradeCourse,$gradeSession,$gradeSemester,$gradeStaff, $grading, $allocationUid, $matric, $time, $ca1, $ca2, $ca3, $ca4, $exam);


            }else {
                Log::info('This Student ('.$this->studentId.') has not registered for this course -'. $this->courseId);
            }

        }else{
            #nothing found log error
            Log::info("Requested OldResult Upload CourseId not found = ".$this->courseId);
        }


    }
}
