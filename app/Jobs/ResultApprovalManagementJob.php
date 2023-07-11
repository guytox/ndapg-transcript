<?php

namespace App\Jobs;

use App\Models\ComputedResult;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ResultApprovalManagementJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $actionBy;
    public $resultId;
    public $approveAs;
    public $action;
    public $actionDate;


    /**
     * Create a new job instance.
     *
     * @return void
     */
     public function __construct($actionBy, $resultId, $approveAs, $action, $actionDate)
    {
        $this->actionBy = $actionBy;
        $this->resultId =  $resultId;
        $this->approveAs =  $approveAs;
        $this->action =  $action;
        $this->actionDate =  $actionDate;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        #fetch the result Id
        $toApprove = ComputedResult::where('uid', $this->resultId)->first();

        if ($toApprove) {
            #result found next get the
            if ($this->action==1) {
                #this result is up for approval
                if ($this->approveAs == getRoleIdByRoleName('exam_officer') && $toApprove->eo_approval==0) {
                    # exam officer is ready to approve set the approval parameters now
                    $toApprove->eo_approval = 1;
                    $toApprove->eo_approver = $this->actionBy;
                    $toApprove->eo_approved_at = $this->actionDate;
                    $toApprove->last_updated_at = $this->actionDate;
                    #save the
                    $toApprove->save();
                }elseif ($this->approveAs == getRoleIdByRoleName('hod') && $toApprove->eo_approval==1 && $toApprove->hod_approval==0) {
                    # hod is ready to approve set the approval parameters now
                    $toApprove->hod_approval = 1;
                    $toApprove->hod_approver = $this->actionBy;
                    $toApprove->hod_approved_at = $this->actionDate;
                    $toApprove->last_updated_at = $this->actionDate;
                    #save the
                    $toApprove->save();
                }elseif ($this->approveAs == getRoleIdByRoleName('dean') && $toApprove->eo_approval==1 && $toApprove->hod_approval==1 && $toApprove->dean_approval==0) {
                    # dean is ready to approve set the approval parameters now
                    $toApprove->dean_approval = 1;
                    $toApprove->dean_approver = $this->actionBy;
                    $toApprove->dean_approved_at = $this->actionDate;
                    # dean approval on behalf of the commiittee
                    $toApprove->committee_approval = 1;
                    $toApprove->commitee_approver = $this->actionBy;
                    $toApprove->committee_approved_at = $this->actionDate;
                    $toApprove->last_updated_at = $this->actionDate;
                    #save the
                    $toApprove->save();
                }elseif ($this->approveAs == getRoleIdByRoleName('vc') && $toApprove->eo_approval==1 && $toApprove->hod_approval==1 && $toApprove->dean_approval==1 && $toApprove->committee_approval==1 && $toApprove->senate_approval==0) {
                    # senate approval is ready is ready to approve set the approval parameters now
                    $toApprove->senate_approval = 1;
                    $toApprove->senate_approver = $this->actionBy;
                    $toApprove->senate_approved_at = $this->actionDate;
                    $toApprove->last_updated_at = $this->actionDate;
                    #set the status of the result to approved
                    $toApprove->cr_status='approved';
                    #save the entry
                    $toApprove->save();

                }
            }elseif ($this->action==2) {
                #this result is up for disassproval
                if ($this->approveAs == getRoleIdByRoleName('hod') && $toApprove->eo_approval==1 && $toApprove->hod_approval==0) {
                    # hod is ready to approve set the approval parameters now
                    #roll back exam officer's approval
                    $toApprove->eo_approval = 0;
                    $toApprove->eo_approver = null;
                    $toApprove->eo_approved_at = null;
                    $toApprove->last_updated_at = $this->actionDate;
                    #save the
                    $toApprove->save();
                }elseif ($this->approveAs == getRoleIdByRoleName('dean') && $toApprove->eo_approval==1 && $toApprove->hod_approval==1 && $toApprove->dean_approval==0) {
                    # dean is ready to approve set the approval parameters now
                    #roll back exam officer's approval
                    $toApprove->eo_approval = 0;
                    $toApprove->eo_approver = null;
                    $toApprove->eo_approved_at = null;
                    #rollback hod
                    $toApprove->hod_approval = 0;
                    $toApprove->hod_approver = null;
                    $toApprove->hod_approved_at = null;
                    #rollback Dean
                    $toApprove->dean_approval = 0;
                    $toApprove->dean_approver = null;
                    $toApprove->dean_approved_at = null;
                    # dean approval on behalf of the commiittee
                    $toApprove->committee_approval = 0;
                    $toApprove->commitee_approver = null;
                    $toApprove->committee_approved_at = null;
                    $toApprove->last_updated_at = $this->actionDate;
                    #save the
                    $toApprove->save();
                }elseif ($this->approveAs == getRoleIdByRoleName('vc') && $toApprove->eo_approval==1 && $toApprove->hod_approval==1 && $toApprove->dean_approval==1 && $toApprove->committee_approval==1) {
                    # senate approval is ready is ready to approve set the approval parameters now
                     #roll back exam officer's approval
                     $toApprove->eo_approval = 0;
                     $toApprove->eo_approver = null;
                     $toApprove->eo_approved_at = null;
                     #rollback hod
                     $toApprove->hod_approval = 0;
                     $toApprove->hod_approver = null;
                     $toApprove->hod_approved_at = null;
                     #rollback Dean
                     $toApprove->dean_approval = 0;
                     $toApprove->dean_approver = null;
                     $toApprove->dean_approved_at = null;
                     # toll back committee
                    $toApprove->committee_approval = 0;
                    $toApprove->commitee_approver = null;
                    $toApprove->committee_approved_at = null;
                     #tollback the senate approval
                    $toApprove->senate_approval = 0;
                    $toApprove->senate_approver = null;
                    $toApprove->senate_approved_at = null;
                    $toApprove->last_updated_at = $this->actionDate;
                    #set the status of the result to approved
                    $toApprove->cr_status='pending';
                    #save the entry
                    $toApprove->save();

                }

            }
        }
    }
}
