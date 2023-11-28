<?php

namespace App\Jobs;

use App\Models\StudentMigration;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FireApproveStudentMigrationJob implements ShouldQueue
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
            if ($owner->hasRole('admin' )) {
                # we are ready to move
                # fetch all pending migrations
                $pendingMigrations = StudentMigration::where('migration_status', 0)->get();

                if ($pendingMigrations ) {
                    # pending migratins found, you can proceed
                    foreach ($pendingMigrations as $m ) {
                        # send the job immediately
                        ApproveStudentMigrationJob::dispatch($m->id, $this->staffId, $this->time);
                    }
                }
            }
        }


    }


}
