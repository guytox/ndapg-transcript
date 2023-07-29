<?php

namespace App\Http\Controllers;

use App\Jobs\RegistrationApprovalJob;
use App\Jobs\SubmitVetoRegistrationApprovalJob;
use App\Models\Department;
use App\Models\RegMonitor;
use App\Models\StudentRecord;
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

       if (isset($_GET['as'])){
            $role = $_GET['as'];
        }else{
            $role = 'role';
        }

    //    foreach ($staffRoles as $va) {
    //     $roleMenu []= collect([
    //         'role'=> $va['name']
    //     ]);
    //    }

       //return $roleMenu;

       //get appointment jurrisdiction
       $staffJurisdiction = getAcademicDepts(user()->id, $role);
       $staffJurisdiction;


       $title = "List of Pending Registrations";

       //select students in jurisdiction

       $pendingStdRegs = getRegStudents(user()->id, $staffJurisdiction, 'pending');

        return view('admin.viewStudentRegList', compact('pendingStdRegs','staffRoles','title'));


    }

    public function showApproved()
    {
        //check if staff has requisite roles relevant for approval
       $staffRoles = $this->getAcademicRoles(user()->id);

        if (isset($_GET['as'])){
            $role = $_GET['as'];
        }else{
            $role = 'role';
        }





       $staffJurisdiction = getAcademicDepts(user()->id, $role);

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

    public function showStudentConfirmedReg($id, $student_id){

        if (user()->hasRole('reg_officer|hod|dean|admin|vc|dvc|dap|acad_eo|exam_officer')) {

            //fetch all regMonitors

            $Monitors = RegMonitor::where(['student_id'=>$student_id,'std_confirm'=>'1', 'uid'=>$id])->with('RegMonitorItems')->first();

            //return $Monitors;

            if ($Monitors) {

                $staffRoles = $this->getAcademicRoles(user()->id);

                return view('admin.printStudentCourseReg', compact('Monitors', 'staffRoles'));
            }
            return redirect(route('coursereg.index'))->with('error',"Error 40322, Contact ICT");


        }else{
            abort(403,"You do not have permission to view this page, Please Contact ICT");
        }



    }




    public function registeredStudentsReport(Request $request){

        if (user()->hasRole('admin|dean_pg')) {

            $this->validate($request, [

                'schoolsession'=>'required',
                'semester'=>'required',

            ]);

            //return $request;

            $role = 'ReportsGenerator';

            $sess = $request->schoolsession;
            $sem = $request->semester;

            //fetch all regMonitors

                $staffJurisdiction = getAcademicDepts(user()->id, $role);

                $title = "List of Registered Students for " . ucfirst(getSemesterDetailsById($sem))." Semester, ". getSessionById($sess)->name." Session";

                //select students in jurisdiction

                $pendingStdRegs = getRegStudentsReport($staffJurisdiction, $sess, $sem);

                //return $pendingStdRegs;

                return view('admin.viewStudentRegReport', compact('pendingStdRegs','title'));


        }elseif(user()->hasRole('reg_officer|hod|dean|admin|vc|dvc')){


        }else{
            abort(403,"You do not have permission to view this page, Please Contact ICT");
        }

    }


    public function NotRegisteredStudentsReport(Request $request){

        if (user()->hasRole('admin|dean_pg')) {

            $this->validate($request, [

                'schoolsession'=>'required',
                'semester'=>'required',

            ]);

            //return $request;

            $role = 'ReportsGenerator';

            $sess = $request->schoolsession;
            $sem = $request->semester;

            //fetch all regMonitors

                $staffJurisdiction = getAcademicDepts(user()->id, $role);

                $title = "List Students who have NOT Registered for " . ucfirst(getSemesterDetailsById($sem))." Semester, ". getSessionById($sess)->name." Session";

                //select students in jurisdiction

                $pendingStdRegs = getNotRegisteredStudentsReport($staffJurisdiction, $sess, $sem);

                //return $pendingStdRegs;

                return view('admin.viewNotRegisteredStudentReport', compact('pendingStdRegs','title'));


        }elseif(user()->hasRole('reg_officer|hod|dean|admin|vc|dvc')){


        }else{
            abort(403,"You do not have permission to view this page, Please Contact ICT");
        }

    }

    public function showSingleStudentResult($id, $student_id, $semester){

        if (user()->hasRole('reg_officer|hod|dean|admin|vc|dvc|dap|acad_eo|student|exam_officer')) {

            //fetch all regMonitors

            $Monitors = RegMonitor::where(['student_id'=>$student_id,'std_confirm'=>'1', 'uid'=>$id])->with('RegMonitorItems')->first();

            //return $Monitors;

            if ($Monitors) {

                $staffRoles = $this->getAcademicRoles(user()->id);
                $semesterId = $semester;

                return view('admin.printStudentStatementOfResult', compact('Monitors', 'staffRoles','semesterId'));
            }
            return view('home')->with('error',"Error 40323, Contact ICT");


        }else{
            abort(403,"You do not have permission to view this page, Please Contact ICT");
        }



    }


    public function showApprovedResults(){

        if (user()->hasRole('student')) {
            $allResults = RegMonitor::where('student_id', getStudentIdByUserId(user()->id))
                                    ->where('r_status','approved')
                                    ->orderBy('semesters_spent','asc')
                                    ->get();

            $title = "Senate Approved Results for ".user()->username." ( ".user()->name." )";

            return view('results.viewStudentApprovedResultList',compact('allResults','title'));
        }

    }


    public function showRegistrationReport(Request $request){

        //return $request;

        if (user()->hasRole('admin|vc|dvc|dap|acad_eo')) {


            $title = "General Registration Report";

            //select students in jurisdiction

            $pendingStdRegs = getGeneralRegReport($request->std_prog,$request->study_level, $request->school_session, $request->semester);

                return view('admin.viewGeneralRegistrationReport', compact('pendingStdRegs','title'));


        }else{
            abort(403,"You do not have permission to view this page, Please Contact ICT");
        }



    }


    public function initiateVetoRegistrationApproval(){
        return view('admin.select-veto-reg-approval');
    }

    public function vetoRegistrationApproval(Request $request){

       $student = RegMonitor::find(1224);

        

        $sessionId = $request->c_sess;
        $semesterId = $request->c_sem;

        SubmitVetoRegistrationApprovalJob::dispatch($sessionId, $semesterId);

        return back()->with('info', "Veto Approval Submitted for processing Successfully");


    }







}
