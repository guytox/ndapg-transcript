<?php

namespace App\Jobs;

use App\Models\RegMonitor;
use App\Models\RegMonitorItems;
use App\Models\StudentRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RegistrationApprovalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $actionBy;
    public $regMonitorval;
    public $approveAs;
    public $action;
    public $message;
    public $actionDate;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($actionBy, $regMonitorval, $approveAs, $action, $message, $actionDate)
    {
        $this->actionBy = $actionBy;
        $this->regMonitorval =  $regMonitorval;
        $this->approveAs =  $approveAs;
        $this->action =  $action;
        $this->message =  $message;
        $this->actionDate =  $actionDate;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //check Approval Or Disaaaroval
        //For Approval
        if ($this->action==1) {

                //Next get the Programme, Department and Faculty for neccessary Checks
                $monitorDetails = RegMonitor::where('uid', $this->regMonitorval)->first();
                $stdProgramme = $monitorDetails->program_id;
                $stdDepartment = getProgrammeDetailById($stdProgramme,'department');
                $stdFaculty = getDepartmentDetailById($stdDepartment,'faculty');
                $semestersCount = RegMonitor::where('student_id',$monitorDetails->student_id)->count();

                //find the monitor detail



                    if($this->approveAs == getRoleIdByRoleName('dean')){
                        $StudentMonitor = RegMonitor::where('uid',$this->regMonitorval)->where(['dean_approval'=>'0', 'hod_approval'=>'1','ro_approval'=>'1','std_confirm'=>'1'])->first();

                        if ($StudentMonitor) {

                            if ($this->actionBy ==getFacultyDetailsById($stdFaculty,'dean')) {
                                //effect Dean Approvals
                                $StudentMonitor->dean_approval = '1';
                                $StudentMonitor->dean_approver = $this->actionBy;
                                $StudentMonitor->dean_approvalDate = $this->actionDate;
                                $StudentMonitor->message = null;
                                $StudentMonitor->status = 'approved';
                                $StudentMonitor->save();
                                //unenroll from siwes
                                $std = StudentRecord::find($StudentMonitor->student_id);
                                $std->is_on_siwes = '0';
                                $std->semesters_spent = $semestersCount;
                                $std->save();

                                $stdCarryOvers = RegMonitorItems::where(['student_id'=>$StudentMonitor->student_id, 'is_suspended'=>'1',])->update([
                                    'is_suspended' =>'0'
                                ]);


                            }
                        }


                    }

                    if($this->approveAs == getRoleIdByRoleName('hod')){
                        $StudentMonitor = RegMonitor::where('uid',$this->regMonitorval)->where(['dean_approval'=>'0', 'hod_approval'=>'0','ro_approval'=>'1','std_confirm'=>'1'])->first();

                        if ($StudentMonitor) {

                            if ($this->actionBy ==getDepartmentDetailById($stdDepartment,'hod')) {
                                //effect hod Approvals
                                $StudentMonitor->hod_approval = '1';
                                $StudentMonitor->hod_approver = $this->actionBy;
                                $StudentMonitor->hod_approvalDate = $this->actionDate;
                                $StudentMonitor->message = null;
                                $StudentMonitor->save();
                            }

                        }



                    }

                    if($this->approveAs == getRoleIdByRoleName('reg_officer')){

                        $StudentMonitor = RegMonitor::where('uid',$this->regMonitorval)->where(['dean_approval'=>'0', 'hod_approval'=>'0','ro_approval'=>'0','std_confirm'=>'1'])->first();

                         if ($StudentMonitor) {

                            if ($this->actionBy ==getDepartmentDetailById($stdDepartment,'RegistrationOfficer')) {
                                //effect Reg Officers Approvals
                                $StudentMonitor->ro_approval = '1';
                                $StudentMonitor->ro_approver = $this->actionBy;
                                $StudentMonitor->ro_approvalDate = $this->actionDate;
                                $StudentMonitor->message = null;
                                $StudentMonitor->save();
                            }
                         }



                    }


                // Next

        }
        //For Disapproval
        elseif ($this->action==2) {
            //Action is disapproval
            //basially reverese everything for the student to start afresh

                //find the monitor detail

                $monitorDetails = RegMonitor::where('uid', $this->regMonitorval)->first();
                $stdProgramme = $monitorDetails->program_id;
                $stdDepartment = getProgrammeDetailById($stdProgramme,'department');
                $stdFaculty = getDepartmentDetailById($stdDepartment,'faculty');

                    if($this->approveAs == getRoleIdByRoleName('dean')){

                        $StudentMonitor =RegMonitor::where('uid',$this->regMonitorval)->where([ 'hod_approval'=>'1','ro_approval'=>'1','std_confirm'=>'1'])->first();

                        if ($StudentMonitor) {

                            if ($this->actionBy ==getFacultyDetailsById($stdFaculty,'dean')) {
                                //effect Dean Approvals
                                $StudentMonitor->dean_approval = '0';
                                $StudentMonitor->dean_approver = null;
                                $StudentMonitor->dean_approvalDate = null;
                                $StudentMonitor->hod_approval = '0';
                                $StudentMonitor->hod_approver = null;
                                $StudentMonitor->hod_approvalDate = null;
                                $StudentMonitor->ro_approval = '0';
                                $StudentMonitor->ro_approver = null;
                                $StudentMonitor->ro_approvalDate = null;
                                $StudentMonitor->std_confirm = '0';
                                $StudentMonitor->message = $this->message;
                                $StudentMonitor->status = 'pending';
                                $StudentMonitor->save();

                            }
                        }




                    }

                    if($this->approveAs == getRoleIdByRoleName('hod')){
                        $StudentMonitor = RegMonitor::where('uid',$this->regMonitorval)->where(['dean_approval'=>'0', 'hod_approval'=>'0','ro_approval'=>'1','std_confirm'=>'1'])->first();

                        if ($StudentMonitor) {
                            if ($this->actionBy ==getDepartmentDetailById($stdDepartment,'hod')) {
                                //effect hod Approvals
                                $StudentMonitor->ro_approval = '0';
                                $StudentMonitor->ro_approver = null;
                                $StudentMonitor->ro_approvalDate = null;
                                $StudentMonitor->std_confirm = '0';
                                $StudentMonitor->message = $this->message;
                                $StudentMonitor->save();

                            }
                        }


                    }


                    if($this->approveAs == getRoleIdByRoleName('reg_officer')){

                        $StudentMonitor = RegMonitor::where('uid',$this->regMonitorval)->where(['dean_approval'=>'0', 'hod_approval'=>'0','ro_approval'=>'0','std_confirm'=>'1'])->first();

                         if ($StudentMonitor) {

                            if ($this->actionBy ==getDepartmentDetailById($stdDepartment,'RegistrationOfficer')) {
                                //effect Reg Officers Approvals
                                $StudentMonitor->std_confirm = '0';
                                $StudentMonitor->message = null;
                                $StudentMonitor->save();
                            }
                        }


                    }


                //Next get the Programme, Department and Faculty for neccessary Checks

                // Next

        }
        //End the job here
        else{
            // Something fishy request does not match any requirements

        }


    }
}
