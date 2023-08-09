<?php

namespace App\Jobs;

use App\Models\Curriculum;
use App\Models\DroppedCourses;
use App\Models\RegMonitor;
use App\Models\RegMonitorItems;
use App\Models\StudentRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;


class VetoRegistrationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $sessionId;
    public $semesterId;
    public $studentId;
    public $time;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($sessionId, $semesterId, $studentId,$time)
    {
        $this->sessionId = $sessionId;
        $this->semesterId = $semesterId;
        $this->studentId = $studentId;
        $this->time = $time;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        #first get the student instance
        $std = StudentRecord::find($this->studentId);

        Log::info("Beginning Veto Course Registration for ". $std->matric. " ...");
        #find the correct curriculum for the student
        $curr = Curriculum::where('programs_id', $std->program_id)
                            ->where('semester', $this->semesterId)
                            ->where('studyLevel', $std->programme->level_id)
                            ->first();

        # set the parameters required min and max credit units
        $tcr = 0;


        $maxCr = $curr->maxRegCredits;

        #make a new RegMonitor Entry for onward processing
        $data = [
            'student_id' => $this->studentId,
            'uid' => uniqid('crf_'),
            'semester_id' => $this->semesterId,
            'curricula_id' => $curr->id,
            'session_id' => $this->sessionId,
            'level_id' => $std->programme->level_id,
            'program_id' => $std->program_id,
            'semesters_spent' => $std->semesters_spent+1,
        ];

        $newReg = RegMonitor::create($data);


        $monitorId = $newReg->id;

        #next begin to write the courses
        # get carry over courses if any
         $cos = RegMonitorItems::distinct()
                                ->where('semester_id', $this->semesterId)
                                ->where('student_id', $this->studentId)
                                ->where('is_co_passed',0)
                                ->select('course_id', 'category' )
                                ->get();
        # Register the cos
        foreach ($cos as $c) {
            #check the available credits
            if ($tcr < $maxCr) {
                #check the implication of adding this course
                $semCourse = getSemesterCourseById($c->course_id);

                if ($tcr + $semCourse->creditUnits <= $maxCr) {
                    #loop through and enter the registration for them for core courses
                    $regData = [
                        'monitor_id' => $monitorId,
                        'student_id' => $this->studentId,
                        'course_id' => $semCourse->id,
                        'session_id' => $this->sessionId,
                        'semester_id' => $this->semesterId,
                        'category' => $c->category,
                    ];

                    RegMonitorItems::updateOrCreate([
                        'monitor_id' => $monitorId,
                        'student_id' => $this->studentId,
                        'course_id' => $semCourse->id,
                        'session_id' => $this->sessionId,
                        'semester_id' => $this->semesterId,
                    ],$regData);

                    $tcr = $tcr + $semCourse->creditUnits;
                }

            }

        }

        #get the dropped courses for this candidate if any
        $dropped = DroppedCourses::where('student_id', $this->studentId)
                                    ->where('semester_id', $this->semesterId)
                                    ->where('category', 'core')
                                    ->select('course_id')
                                    ->distinct()
                                    ->get();

        foreach ($dropped as $d) {
            #check the available credits
            if ($tcr < $maxCr) {
                #check the implication of adding this course
                $semCourse = getSemesterCourseById($d->course_id);

                if ($tcr + $semCourse->creditUnits <= $maxCr) {
                    #loop through and enter the registration for them for core courses
                    $regData = [
                        'monitor_id' => $monitorId,
                        'student_id' => $this->studentId,
                        'course_id' => $semCourse->id,
                        'session_id' => $this->sessionId,
                        'semester_id' => $this->semesterId,
                        'category' => 'core',
                    ];

                    RegMonitorItems::updateOrCreate([
                        'monitor_id' => $monitorId,
                        'student_id' => $this->studentId,
                        'course_id' => $semCourse->id,
                        'session_id' => $this->sessionId,
                        'semester_id' => $this->semesterId,
                    ],$regData);

                    $tcr = $tcr + $semCourse->creditUnits;
                }

            }
        }

        # get the core courses
        foreach ($curr->curriculumItems as $e) {
            #check the available credits
            if ($tcr < $maxCr && $e->category == 'core') {
                #check the implication of adding this course
                $semCourse = getSemesterCourseById($e->semester_courses_id);

                if ($tcr + $semCourse->creditUnits <= $maxCr) {
                    #loop through and enter the registration for them for core courses
                    $regData = [
                        'monitor_id' => $monitorId,
                        'student_id' => $this->studentId,
                        'course_id' => $semCourse->id,
                        'session_id' => $this->sessionId,
                        'semester_id' => $this->semesterId,
                        'category' => 'core',
                    ];

                    RegMonitorItems::updateOrCreate([
                        'monitor_id' => $monitorId,
                        'student_id' => $this->studentId,
                        'course_id' => $semCourse->id,
                        'session_id' => $this->sessionId,
                        'semester_id' => $this->semesterId,
                    ],$regData);

                    $tcr = $tcr + $semCourse->creditUnits;
                }

            }
        }

        foreach ($curr->curriculumItems as $f) {
            #check the available credits
            if ($tcr < $maxCr && $e->category == 'elective') {
                #check the implication of adding this course
                $semCourse = getSemesterCourseById($e->semester_courses_id);

                if ($tcr + $semCourse->creditUnits <= $maxCr) {
                    #loop through and enter the registration for them for elective courses
                    $regData = [
                        'monitor_id' => $monitorId,
                        'student_id' => $this->studentId,
                        'course_id' => $semCourse->id,
                        'session_id' => $this->sessionId,
                        'semester_id' => $this->semesterId,
                        'category' => 'elective',
                    ];

                    RegMonitorItems::updateOrCreate([
                        'monitor_id' => $monitorId,
                        'student_id' => $this->studentId,
                        'course_id' => $semCourse->id,
                        'session_id' => $this->sessionId,
                        'semester_id' => $this->semesterId,
                    ],$regData);

                    $tcr = $tcr + $semCourse->creditUnits;
                }

            }
        }

        # submit the registration
        Log::info("Submitting Course Registration");

        $newReg->std_confirm = '1';
        $newReg->save();


        Log::info("Veto Course Registration Completed Successfully for ". $std->matric. " ...");

    }

}
