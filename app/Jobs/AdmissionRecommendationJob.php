<?php

namespace App\Jobs;

use App\Models\ApplicantAdmissionRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AdmissionRecommendationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $appId;
    public $actionBy;
    public $actionAt;
    public $as;
    public $actionToTake;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($appId, $actionBy, $actionAt, $as, $actionToTake)
    {
        $this->appId = $appId;
        $this->actionBy = $actionBy;
        $this->actionAt = $actionAt;
        $this->as = $as;
        $this->actionToTake = $actionToTake;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        #find the role_id;
        $roleName = getRoleNameByRoleId($this->as);

        if ($roleName == 'reg_officer') {
            if ($this->actionToTake == 1) {
                # proceed to recommend
                $toadmin = ApplicantAdmissionRequest::where('uid', $this->appId)
                                                ->where('is_admitted', 0)
                                                ->where('pg_coord',0)
                                                ->where('hod', 0)
                                                ->first();
                $toadmin->pg_coord = 1;
                $toadmin->pg_coord_at = $this->actionAt;
                $toadmin->pg_coord_by = $this->actionBy;
                $toadmin->save();

            }elseif ($this->actionToTake == 2) {
                # reject as log as hod has not approved
                $toadmin = ApplicantAdmissionRequest::where('uid', $this->appId)
                                                ->where('is_admitted', 0)
                                                ->where('pg_coord',1)
                                                ->where('hod', 0)
                                                ->first();
                $toadmin->pg_coord = 0;
                $toadmin->pg_coord_at = null;
                $toadmin->pg_coord_by = null;
                $toadmin->save();
            }


        }elseif ($roleName =='hod') {

            if ($this->actionToTake == 1) {
                # effect approval by hod
                $toadmin = ApplicantAdmissionRequest::where('uid', $this->appId)
                                                ->where('is_admitted', 0)
                                                ->where('pg_coord',1)
                                                ->where('hod',0)
                                                ->first();
                $toadmin->hod = 1;
                $toadmin->hod_at = $this->actionAt;
                $toadmin->hod_by = $this->actionBy;
                $toadmin->save();

            }elseif ($this->actionToTake == 2) {
                # disapprove on the condition that the dean has not disapproved
                $toadmin = ApplicantAdmissionRequest::where('uid', $this->appId)
                                                ->where('is_admitted', 0)
                                                ->where('pg_coord',1)
                                                ->where('hod', 1)
                                                ->where('dean', 0)
                                                ->first();
                $toadmin->hod = 0;
                $toadmin->hod_at = null;
                $toadmin->hod_by = null;
                $toadmin->save();
            }

        }elseif ($roleName =='dean') {

            if ($this->actionToTake == 1) {
                # effect hod approval
                $toadmin = ApplicantAdmissionRequest::where('uid', $this->appId)
                                        ->where('is_admitted', 0)
                                        ->where('pg_coord',1)
                                        ->where('hod',1)
                                        ->where('dean',0)
                                        ->first();
                $toadmin->dean = 1;
                $toadmin->dean_at = $this->actionAt;
                $toadmin->dean_by = $this->actionBy;
                $toadmin->save();

            }elseif ($this->actionToTake == 2) {
                # rollback dean approval provided dean pg has not acted
                $toadmin = ApplicantAdmissionRequest::where('uid', $this->appId)
                                        ->where('is_admitted', 0)
                                        ->where('pg_coord',1)
                                        ->where('hod',1)
                                        ->where('dean',1)
                                        ->where('dean_spgs',0)
                                        ->first();
                $toadmin->dean = 0;
                $toadmin->dean_at = null;
                $toadmin->dean_by = null;
                $toadmin->save();
            }


        }elseif ($roleName =='dean_pg') {

            if ($this->actionToTake == 1) {
                # this is dean of pg, any approval means admission granted proceed to do more including email
                $toadmin = ApplicantAdmissionRequest::where('uid', $this->appId)
                                                    ->where('is_admitted', 0)
                                                    ->where('pg_coord',1)
                                                    ->where('hod',1)
                                                    ->where('dean',1)
                                                    ->where('dean_spgs',0)
                                                    ->first();
                if ($toadmin) {
                    #query entry found proceed
                    $toadmin->dean_spgs = 1;
                    $toadmin->dean_spgs_at = $this->actionAt;
                    $toadmin->dean_spgs_by = $this->actionBy;
                    #admit student
                    $toadmin->is_admitted = 1;
                    $toadmin->admitted_at = $this->actionAt;
                    $toadmin->admitted_by = $this->actionBy;
                    #done save the entry
                    $toadmin->save();
                    #next assign role of admitted to the candidate
                    $appDetails = User::find($toadmin->user_id);
                    $appDetails->assignRole('admitted');
                    #next notify the student via email of the admission
                }

            }elseif ($this->actionToTake == 2) {
                #effect dissaproval for dean at all times
                $toadmin = ApplicantAdmissionRequest::where('uid', $this->appId)
                                                    ->where('pg_coord',1)
                                                    ->where('hod',1)
                                                    ->where('dean',1)
                                                    ->first();
                if ($toadmin) {
                    $toadmin->dean_spgs = 0;
                    $toadmin->dean_spgs_at = null;
                    $toadmin->dean_spgs_by = null;
                    #rollback admission
                    $toadmin->is_admitted = 0;
                    $toadmin->admitted_at = null;
                    $toadmin->admitted_by = null;
                    #rollback dean
                    $toadmin->dean = 0;
                    $toadmin->dean_at = null;
                    $toadmin->dean_by = null;
                    #rollback hod
                    $toadmin->hod = 0;
                    $toadmin->hod_at = null;
                    $toadmin->hod_by = null;
                    #rollback exam_officer
                    $toadmin->pg_coord = 0;
                    $toadmin->pg_coord_at = null;
                    $toadmin->pg_coord_by = null;
                    #done save the entry
                    $toadmin->save();
                    #next assign role of admitted to the candidate
                    $appDetails = User::find($toadmin->user_id);
                    $appDetails->removeRole('admitted');
                    #next notify the student via email of the admission
                }

            }

        }


    }
}
