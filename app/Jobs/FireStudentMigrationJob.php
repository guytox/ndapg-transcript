<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FireStudentMigrationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $staffId;
    public $time;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($staffId, $time)
    {
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
        $owner = User::find($this->staffId);

        if ($owner ) {
            if ($owner->hasRole('admin')) {
                $staffId = $owner->id;
                #then proceed to run the job

                $students = User::role('student')
                        ->join('student_records as s','s.matric','=', 'users.username')
                        ->where('s.admission_session','<', activeSession()->id)
                        ->select('users.*')
                        ->get();

                if ($students ) {
                    foreach ($students as $h) {
                        if ($h->student) {
                            RecommendStudentMigrationJob::dispatch($h->student->id, activeSession()->id, $staffId,  $this->time);
                        }
                    }
                }
            }
        }
    }
}
