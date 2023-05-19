<?php

namespace App\Jobs;

use App\Models\ApplicantAdmissionRequest;
use App\Models\Program;
use App\Models\StudentRecord;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateStudentRecordJob implements ShouldQueue
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
        #generate a matric number for the student
        #get the program
        $stdProg = Program::find($appData->program_id);
        #get the matric prefix
        $prefix = $stdProg->matric_prefix;
        #get the year suffix
        $matricYear = getMatricSession();
        #get matric suffix
        $matricSerial = getMatricSerial();
        #we are ready to form the matric number now
        $newMatricNumber = $prefix.$matricYear.'/'.$matricSerial;
        #update the user table
        $newStdUser = User::find($appData->user_id);
        $newStdUser->username = $newMatricNumber;
        $newStdUser->current_level = $stdProg->level_id;
        $newStdUser->save();
        #assign student role to the user
        $newStdUser->assignRole('student');
        #get the user profile
        $stdProfile = UserProfile::where('user_id',$appData->user_id)->first();
        #create the student record for the student
        $data = [
            'user_id' => $newStdUser->id,
            'program_id' => $appData->program_id,
            'matric' => $newMatricNumber,
            'state_origin' => $stdProfile->state_id,
            'admission_session' => getApplicationSession(),
        ];
        #make entry into the student record table
        $newStdRecord = StudentRecord::updateOrCreate([
            'user_id' => $newStdUser->id,
            'admission_session' => getApplicationSession(),

        ], $data);
        #update the matric in the applicant table
        $appData->matric = $newMatricNumber;
        $appData->student_id = $newStdRecord->id;
        $appData->save();

        #now we're done now

    }
}
