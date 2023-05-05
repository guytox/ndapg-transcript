<?php

namespace App\Jobs;

use App\Mail\AdmissionOfferNotification;
use App\Models\ApplicantAdmissionRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class AdmissionNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $admissionId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($admissionId)
    {
        $this->admissionId = $admissionId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        #first get the applicant id
        $adStd = ApplicantAdmissionRequest::find($this->admissionId);
        // $adStd = ApplicantAdmissionRequest::find(115);
        #get the user email
        $appEmail = strtolower(getUser($adStd->user_id, 'email'));
        #send the mail now
        if (Mail::to($appEmail)->send(new AdmissionOfferNotification('Vincent Achanya', 'Masters in Business Administration'))) {
            #update the notification status
            $adStd->adm_notification = 1;
            $adStd->save();
        }

        $adStd->adm_notification = 1;
        $adStd->save();

    }
}
