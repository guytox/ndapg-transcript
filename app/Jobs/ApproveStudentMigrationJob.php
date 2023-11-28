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

class ApproveStudentMigrationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $id;
    public $staffId;
    public $time;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id, $staffId,  $time)
    {
        $this->id = $id;
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
            # check if owner has required roles
            if ($owner->hasRole('admin' )) {
                #proceed fetch the required student
                $toApprove = StudentMigration::find($this->id);
                if ($toApprove ) {
                    # student to migrate found, proceed to migrate
                    $student = StudentRecord::find($toApprove->student_id);
                    if ($student ) {
                        # Student found, you can proceed to check and approve
                        if ($student->study_year < $toApprove->new_year) {
                            $student->study_year = $toApprove->new_year;
                            $student->save();

                            $toApprove->migration_status = 1;
                            $toApprove->migrated_at = $this->time;
                            $toApprove->migrated_by = $this->staffId;
                            $toApprove->save();

                            # send the tuition billing job for this student
                            TuitionBillingJob::dispatch($this->staffId, $student->id, activeSession()->id, $this->time);

                        }
                    }

                }
            }
        }
    }
}
