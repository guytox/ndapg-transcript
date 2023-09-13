<?php

namespace App\Jobs;

use App\Models\PendingGraduant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RecommendGraduantsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $programId;
    public $sessionId;
    public $semesterId;
    public $studyLevel;
    public $reg;
    public $staffId;
    public $time;
    public $approveAs;
    public $action;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($programId, $sessionId, $semesterId, $studyLevel, $reg, $staffId, $time, $approveAs, $action)
    {
        $this->programId = $programId;
        $this->sessionId = $sessionId;
        $this->semesterId = $semesterId;
        $this->studyLevel = $studyLevel;
        $this->reg = $reg;
        $this->staffId = $staffId;
        $this->time = $time;
        $this->approveAs = $approveAs;
        $this->action = $action;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $toApprove = PendingGraduant::where('uid', $this->reg)->first();
        if ($this->action ==1) {
            #this is approval
            if ($this->approveAs == getRoleIdByRoleName('reg_officer') && $toApprove->pg_coord==0) {
                # exam officer is ready to approve set the approval parameters now
                $toApprove->pg_coord = 1;
                $toApprove->pg_coord_by = $this->staffId;
                $toApprove->pg_coord_at = $this->time;
                #save the
                $toApprove->save();

                Log::info("PG Coordinator Approval Successful for ".$this->reg);

            }elseif ($this->approveAs == getRoleIdByRoleName('hod') && $toApprove->pg_coord==1 && $toApprove->hod==0) {
                # hod is ready to approve set the approval parameters now
                $toApprove->hod = 1;
                $toApprove->hod_by = $this->staffId;
                $toApprove->hod_at = $this->time;
                #save the
                $toApprove->save();

                Log::info("HOD Graduation Recommendation Successful for ".$this->reg);

            }elseif ($this->approveAs == getRoleIdByRoleName('dean') && $toApprove->pg_coord==1 && $toApprove->hod==1 && $toApprove->dean==0) {
                # dean is ready to approve set the approval parameters now
                $toApprove->dean = 1;
                $toApprove->dean_by = $this->staffId;
                $toApprove->dean_at = $this->time;

                #save the
                $toApprove->save();

                Log::info("Dean Approval Successful for ".$this->reg);

            }elseif ($this->approveAs == getRoleIdByRoleName('dean_pg') && $toApprove->pg_coord==1 && $toApprove->hod==1 && $toApprove->dean==1 && $toApprove->dean_spgs==0) {

                # dean approval on behalf of the commiittee
                $toApprove->dean_spgs = 1;
                $toApprove->dean_spgs_by = $this->staffId;
                $toApprove->dean_spgs_at = $this->time;
                #save the
                $toApprove->save();

                Log::info("Dean SPGS Approval Successful for ".$this->reg);

            }elseif ($this->approveAs == getRoleIdByRoleName('vc') && $toApprove->pg_coord==1 && $toApprove->hod==1 && $toApprove->dean==1 && $toApprove->dean_spgs==1 && $toApprove->senate==0) {
                # senate approval is ready is ready to approve set the approval parameters now
                $toApprove->senate = 1;
                $toApprove->senate_by = $this->staffId;
                $toApprove->senate_at = $this->time;
                #set the status of the result to approved
                $toApprove->approval_status='approved';
                #save the entry
                $toApprove->save();

                Log::info("FINAL Approval Successful for ".$this->reg);

            }
        }elseif ($this->action == 2) {
            #this action is disapproval
            if ($this->approveAs == getRoleIdByRoleName('reg_officer') && $toApprove->pg_coord==1) {

                # PG COORDINATOR TO CONTACT HOD FOR DELETING RECOMMENDATIONS

            }elseif ($this->approveAs == getRoleIdByRoleName('hod') && $toApprove->pg_coord==1 && $toApprove->hod==0) {

                # HOD TO DELETE RECOMMENDATIONS ONLY

            }elseif ($this->approveAs == getRoleIdByRoleName('dean') && $toApprove->pg_coord==1 && $toApprove->hod==1 && $toApprove->dean==0) {
                # hod is ready to approve set the approval parameters now
                $toApprove->hod = 0;
                $toApprove->hod_by = null;
                $toApprove->hod_at = null;

                #save the
                $toApprove->save();

                Log::info("Dean Approval Successful for ".$this->reg);

            }elseif ($this->approveAs == getRoleIdByRoleName('dean_pg') && $toApprove->pg_coord==1 && $toApprove->hod==1 && $toApprove->dean==1 && $toApprove->dean_spgs==0) {

                # dean approval rollback
                $toApprove->dean = 0;
                $toApprove->dean_by = null;
                $toApprove->dean_at =null;
                # hod is ready to approve set the approval parameters now
                $toApprove->hod = 0;
                $toApprove->hod_by = null;
                $toApprove->hod_at = null;
                #save the
                $toApprove->save();

                Log::info("Dean SPGS Disapproval Successful for ".$this->reg);

            }elseif ($this->approveAs == getRoleIdByRoleName('vc') && $toApprove->pg_coord==1 && $toApprove->hod==1 && $toApprove->dean==1 && $toApprove->dean_spgs==1 && $toApprove->senate==0) {
                # dean approval rollback
                $toApprove->dean_spgs = 0;
                $toApprove->dean_spgs_by = null;
                $toApprove->dean_spgs_at = null;
                # dean approval rollback
                $toApprove->dean = 0;
                $toApprove->dean_by = null;
                $toApprove->dean_at =null;
                # hod is ready to approve set the approval parameters now
                $toApprove->hod = 0;
                $toApprove->hod_by = null;
                $toApprove->hod_at = null;

                #save the entry
                $toApprove->save();

                Log::info("SENATE Disapproval Successful for ".$this->reg);

            }
        }

    }


}
