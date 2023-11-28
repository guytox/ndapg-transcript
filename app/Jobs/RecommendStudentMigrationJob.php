<?php

namespace App\Jobs;

use App\Models\StudentMigration;
use App\Models\StudentRecord;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RecommendStudentMigrationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $studentId;
    public $activeYear;
    public $staffId;
    public $time;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($studentId, $activeYear, $staffId,  $time)
    {
        $this->studentId = $studentId;
        $this->activeYear = $activeYear;
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
                #now you can roll forward this user qualifies to roll
                #first get the student details
                $toMigrate = StudentRecord::find($this->studentId);

                if ($toMigrate ) {
                    #student found, you can proceed to check the entry year
                    if ($toMigrate->admission_session < $this->activeYear) {
                        # next check for defferment, graduation and suspension
                        if ($toMigrate->in_defferment == 0 && $toMigrate->is_suspended ==0 && $toMigrate->has_graduated==0 ) {
                            # finnally check the max year available
                             if ($toMigrate->study_year < 6) {
                                # we are ready to bill now
                                $newYear = $toMigrate->study_year + 1;
                                $data = [
                                    'student_id' => $this->studentId,
                                    'session_id' => $this->activeYear,
                                    'old_year' => $toMigrate->study_year,
                                    'new_year' => $newYear,
                                    'recommended_by' => $this->staffId,
                                    'recommended_at' => $this->time,
                                ];

                                #make the entry and exit the job

                                StudentMigration::updateOrCreate(['student_id' => $this->studentId,
                                                                    'session_id' => $this->activeYear,
                                                                ], $data);


                             }
                        }
                    }

                }else{
                    Log::info('student '.$this->studentId.' could not be migrated');
                }
            }
        }
    }
}
