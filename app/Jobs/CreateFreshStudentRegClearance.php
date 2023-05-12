<?php

namespace App\Jobs;

use App\Models\ApplicantAdmissionRequest;
use App\Models\RegClearance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateFreshStudentRegClearance implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $appId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($appId)
    {
        $this->appId = $appId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        #find the applicant details
        $appData = ApplicantAdmissionRequest::find($this->appId);
        #extract the student_id
        $stdId = $appData->student_id;
        #create entries in reg_clearance
        $data = [
            'student_id' => $appData->student_id,
            'school_session_id' => getApplicationSession(),
            'first_semester' => 1,
            'second_semester' => 1,
            'status' => 1
        ];
        $regClearance = RegClearance::updateOrCreate($data, $data);

    }
}
