<?php

namespace App\Http\Controllers;

use App\Jobs\RegistrationApprovalJob;
use App\Models\Department;
use App\Models\RegMonitor;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class RegistrationApprovalController extends Controller
{
    use HasRoles;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //check if staff has requisite roles relevant for approval
       $staffRoles = $this->getAcademicRoles(user()->id);

    //    foreach ($staffRoles as $va) {
    //     $roleMenu []= collect([
    //         'role'=> $va['name']
    //     ]);
    //    }

       //return $roleMenu;


       //get appointment jurrisdiction
       $staffJurisdiction = getAcademicDepts(user()->id);
       //return $staffJurisdiction;


       $title = "List of Pending Registrations";

       //select students in jurisdiction

       $pendingStdRegs = getRegStudents(user()->id, $staffJurisdiction, 'pending');

        return view('admin.viewStudentRegList', compact('pendingStdRegs','staffRoles','title'));


    }

    public function showApproved()
    {
        //check if staff has requisite roles relevant for approval
       $staffRoles = $this->getAcademicRoles(user()->id);


       $staffJurisdiction = getAcademicDepts(user()->id);

       $title = "List of Approved Registrations";

       //select students in jurisdiction

       $pendingStdRegs = getRegStudents(user()->id, $staffJurisdiction, 'approved');

        return view('admin.viewStudentRegList', compact('pendingStdRegs','staffRoles','title'));


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [

            'regMonitor'=>'required',
            'approveAs'=>'required',
            'action'=>'required',
            'message'=>'present',

        ]);

        //return $request;
        //return getRoleIdByRoleName('reg_officer');

        if (user()->hasRole('reg_officer|hod|dean')) {
            foreach ($request->regMonitor as $val) {

                $actionBy = user()->id;
                $regMonitorval = $val;
                $approveAs = $request->approveAs;
                $action = $request->action;
                $message = $request->message;
                $actionDate = now();

                RegistrationApprovalJob::dispatch($actionBy,$regMonitorval,$approveAs, $action,$message, $actionDate);

                //return $actionBy;

                // if ($request->action==1) {
                //     //Next get the Programme, Department and Faculty for neccessary Checks
                //     $monitorDetails = RegMonitor::where('uid', $val)->first();
                //     $stdProgramme = $monitorDetails->program_id;
                //     $stdDepartment = getProgrammeDetailById($stdProgramme,'department');
                //     $stdFaculty = getDepartmentDetailById($stdDepartment,'faculty');
                //     $semestersCount = RegMonitor::where('student_id',$monitorDetails->student_id)->count();



                //     if($approveAs == getRoleIdByRoleName('reg_officer')){

                //         $StudentMonitor = RegMonitor::where('uid',$val)->where(['dean_approval'=>'0', 'hod_approval'=>'0','ro_approval'=>'0','std_confirm'=>'1'])->first();

                //          if ($StudentMonitor) {

                //             if ($actionBy !==getDepartmentDetailById($stdDepartment,'RegistrationOfficer')) {
                //                 //effect Reg Officers Approvals
                //                 $StudentMonitor->ro_approval = '1';
                //                 $StudentMonitor->ro_approver = $actionBy;
                //                 $StudentMonitor->ro_approvalDate = $actionDate;
                //                 $StudentMonitor->message = null;
                //                 $StudentMonitor->save();
                //             }
                //          }



                //     }

                // }


            }

            //return "wait";


            return back()->with('info', "Approval submitted Successfully, You may check the status afer a minute !!!");
            //return redirect(route('home'))->with('info', "Approval submitted Successfully, You may check the status afer a minute !!!");

        }else {
            return "You do not have priviledges to view this page";
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getAcademicRoles($id){

        $allUserRoles = User::join('model_has_roles', 'model_has_roles.model_id','=','users.id')
                                        ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                                        ->where('users.id',$id)
                                        ->whereIn('roles.name',['dean','hod', 'reg_officer'])
                                        ->select('roles.*')
                                        ->get()
                                        ->pluck('name','id');
        return $allUserRoles;


    }


    





}
