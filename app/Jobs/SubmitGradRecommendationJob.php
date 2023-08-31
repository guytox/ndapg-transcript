<?php

namespace App\Jobs;

use App\Models\PendingGraduant;
use App\Models\RegMonitor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SubmitGradRecommendationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $programId;
    public $sessionId;
    public $semesterId;
    public $studyLevel;
    public $reg;
    public $staffId;
    public $time;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($programId, $sessionId, $semesterId, $studyLevel, $reg, $staffId, $time)
    {
        $this->programId = $programId;
        $this->sessionId = $sessionId;
        $this->semesterId = $semesterId;
        $this->studyLevel = $studyLevel;
        $this->reg = $reg;
        $this->staffId = $staffId;
        $this->time = $time;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        #find the result entry
        $gradStudent = RegMonitor::where('uid', $this->reg)->first();
        #next prepare data for entry into the table

        $data = [
            'student_id' => $gradStudent->student_id,
            'user_id' => $gradStudent->student->user_id,
            'uid' => uniqid('grad_'),
            'program_id' => $gradStudent->program_id,
            'result_id' => $gradStudent->id,
            'grad_session_id' => $gradStudent->session_id,
            'grad_semester_id' => $gradStudent->semester_id,
            'grad_cgpa' => $gradStudent->cgpa,
            'degree_class' => getDegreeClass($gradStudent->uid),
            'pg_coord_by' => $this->staffId,
            'pg_coord' => 1,
            'pg_coord_at' => $this->time,
        ];

        #now fire everything into the table
        PendingGraduant::updateOrCreate([
            'student_id' => $gradStudent->student_id,
            'user_id' => $gradStudent->student->user_id,
            'program_id' => $gradStudent->program_id,
        ],$data);

        Log::info("Graduation Successfully Recommeded for ". $this->reg);

    }
}
