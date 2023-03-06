<?php

namespace App\Jobs;

use App\Models\RegMonitor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BulkRegistrationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private $theProgramme;
    private $rCourseCode;
    private $rSchoolSession;
    private $rSemester;
    private $studyLevel;
    private $cCategory;
    private $cAction;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($theProgramme,$rCourseCode,$rSchoolSession,$rSemester,$studyLevel,$cCategory,$cAction)
    {
        $this->theProgramme = $theProgramme;
        $this->rCourseCode = $rCourseCode;
        $this->rSchoolSession = $rSchoolSession;
        $this->rSemester = $rSemester;
        $this->studyLevel = $studyLevel;
        $this->cCategory = $cCategory;
        $this->cAction = $cAction;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        #fetch all students that have registered for this session and semester
        $allRegistrants = RegMonitor::where('program_id', $this->theProgramme)
                                    ->where('semester_id', $this->rSemester)
                                    ->where('session_id', $this->rSchoolSession)
                                    ->where('level_id', $this->studyLevel)
                                    ->get();

        foreach ($allRegistrants as $k) {
            #pass the findings to the registration job
            BulkSingleRegistrationJob::dispatch($k->id, $this->rCourseCode, $this->cCategory, $this->cAction);
        }

    }
}
