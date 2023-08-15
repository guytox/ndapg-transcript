<?php

namespace App\Http\Controllers;

use App\Jobs\ResultApprovalManagementJob;
use App\Jobs\SubmitResultComputationJob;
use App\Jobs\SubmitSessionalResultComputationJob;
use App\Models\ComputedResult;
use App\Models\CourseAllocationItems;
use App\Models\RegMonitor;
use App\Models\RegMonitorItems;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ResultManagementController extends Controller
{

    public function resultComputeHome(){
        # get user programmes
        $programs = getUserProgramsDropdown(user()->id);

        return view('results.begin-result-computation', compact('programs'));
    }


    public function checkComputeReadiness(Request $request, $as){

        $validated = $request->validate([
            'c_prog' => 'required',
            'school_session' =>'required',
            'semester' => 'required',
            'study_level' => 'required',
        ]);


        #if this request is for sessional, treat it differently
        if ($request->semester == 3) {
            #check to see if the result has been computed already
            $requestSemester = 2;
        }

        #find out if this result exist already


        #set some global variables
        $toCompute = 1;
        $unAllocated = 0;
        $gradingIncomplete = 0;
        $noCourseAllocation = 0;

        // return $request;


        if (user()->hasRole('exam_officer') && $as =='ityoughKiKyaren') {

            #check computed resutls to see if this result is computed already, then send to computed result home for this session and semester

            #get distinct courses registered by students to continue since result has not been computed
            $distinctCourses = RegMonitorItems::join('reg_monitors as r', 'r.id','=','reg_monitor_items.monitor_id')
                                                ->where('r.program_id', $request->c_prog)
                                                ->where('r.level_id', $request->study_level)
                                                ->where('reg_monitor_items.session_id', $request->school_session)
                                                ->where('reg_monitor_items.semester_id', $request->semester)
                                                ->distinct('reg_monitor_items.course_id')
                                                ->get(['reg_monitor_items.course_id'])
                                                ->toArray();
            $distinctSelected = [];
            foreach ($distinctCourses as $v) {
                $distinctSelected[] = collect($v);
            }

            #get the id's only
            $trimmedArrary = Arr::pluck($distinctSelected, 'course_id');


            //return $distinctSelected;

            $session_id = $request->school_session;
            $session_name = getsessionById($request->school_session)->name;
            $semester = activeSession()->currentSemester;
            $semester_id = $request->semester;
            $program_id = $request->c_prog;
            $level_id = $request->study_level;


            $courses = CourseAllocationItems::join('course_allocation_monitors as m', 'm.id', '=', 'course_allocation_items.allocation_id')
                                            ->join('semester_courses', 'semester_courses.id','=','course_allocation_items.course_id')
                                            ->join('departments', 'departments.id','=','semester_courses.department_id')
                                            ->whereIn('course_allocation_items.course_id', $distinctCourses)
                                            ->where('course_allocation_items.can_grade', 1)
                                            ->where([
                                            'm.session_id' => $request->school_session,
                                            'm.semester_id' =>$request->semester])
                                            ->select('course_allocation_items.*', 'm.uid as monitor_uid', 'm.id as monitor_id', 'm.session_id', 'm.semester_id')
                                            ->get();

            if (count($courses)==0) {
                $notAllocated = [];
                $gradingNotCompleted = [];
                $unAllocated = 1;
                $gradingIncomplete = 1;
                $toCompute =0;
                $noCourseAllocation = 1;


            }else{




                    #get list of non-allocated courses and flagg don not comptute
                    $notAllocated = [];
                    $gradingNotCompleted = [];

                    foreach ($courses as $val) {
                        #check if not allocated
                        if (in_array($val->course_id, $trimmedArrary)) {
                            #found so skip


                        }else{
                            #course not found so add to the array of non allocated courses
                            $notAllocated[] = collect(['courseCode'=>getSemesterCourseById($val->course_id)->courseCode]);
                            $toCompute =0;
                            $unAllocated = 1;

                        }

                        # run through courses to see if readiness is achieved then update to state reason
                        if ($val->accepted=='2') {
                            $gradingNotCompleted[] = collect(['courseCode' => getSemesterCourseById($val->course_id)->courseCode]);
                            $gradingIncomplete = 1;
                            $toCompute =0;

                        }
                    }

            }

            //return $gradingNotCompleted;

            //return $courses;
            return view('results.check-grade-readiness', compact('courses','session_name', 'semester', 'toCompute','unAllocated','gradingNotCompleted','notAllocated','gradingIncomplete','noCourseAllocation','semester_id','session_id','level_id','program_id'));

        }else {
            return back()->with('error','Error 42012 !!! Only Examinatio Officers Can Access this Priviledge');
        }

    }



    public function computeResult(Request $request, $as){

        //return redirect(route('home'))->with('info', "Result Computation Submitted Successfully");

        if ($as != 'ityoughKiKyaren') {

            return back()->with('error','Error!!! You are not allowed to compte results, Contact Support');

        }

        if (user()->hasRole('exam_officer')) {

            //validate the input
            $validated = $request->validate([
                'studylevel' =>'required',
                'programme' =>'required',
                'schsession' =>'required',
                'schsemester' =>'required',
                'command' =>'required',
            ]);

            //return $request;

            #Check the semester to see if it is a Sessional Semester then redirect appropriately
            if ($request->schsemester ==3) {

                # Semester is a Sessional Semester, Proceed with sessional computations
                # This result has been computed before
                    # Grab the Computed result Id and move on
                    $computeId = ComputedResult::where('program_id', $request->programme)
                                                    ->where('schoolsession_id', $request->schsession)
                                                    ->where('semester_id', 2)
                                                    ->where('study_level', $request->studylevel)
                                                    ->first();


                    if ($computeId) {
                        # send the regMonitor for result computation

                        if ($computeId->is_s_computed == '1') {
                            # this sessional result has been computed before, so just update
                            $computeId->last_updated_at = now();
                            $computeId->is_s_computed = '1';
                            $computeId->save();

                        }else{

                            #This result has not been compted before so set the new parameters and continue
                            $computeResultUId = $computeId->uid;

                            $computeId->is_s_computed = '1';
                            $computeId->s_computed_status = 'pending';
                            $computeId->save();

                        }

                        #get the total no of students that have registered for this courses for fresh computation.


                        //return $computeResultUId;
                        $regMonitors = RegMonitor::where('program_id', $request->programme)
                                                        ->where('session_id', $request->schsession)
                                                        ->where('semester_id', $request->schsemester)
                                                        ->where('level_id', $request->studylevel)
                                                        ->get();

                        foreach ($regMonitors as $v) {

                            SubmitSessionalResultComputationJob::dispatch($computeResultUId, $v->uid);

                        }

                        # Next update the Absent List with a Job for that purpose

                        return redirect(route('begin-result-computation'))->with('info' , "Recompute Submitted Successfully !!! Please check back after One Minute");

                    }else{

                        return redirect(route('begin-result-computation'))->with('error', "Error!!!! Looks like your Access level is low, You'd be blocked if you try too frequently");
                    }

                //return $request;

                //return redirect(route('select.oldresultsCompute'))->with('error', "Sessional Result Computation Not Ready");

                # End of Sessional Result Submission

            } elseif ($request->schsemester == 1  || $request->schsemester == 2) {

                //return $request;

                //check to see if it is fresh computation or recomputation
                if ($request->command ==25) {
                    # computaton is a fresh one insert records into the compute result

                    $computeDetails = [
                        'uid' => uniqid("cmpr_"),
                        'program_id' => $request->programme,
                        'schoolsession_id' => $request->schsession,
                        'semester_id' => $request->schsemester,
                        'study_level' => $request->studylevel,
                        'computed_by' => user()->id,
                        // 'eo_approver' => user()->id,
                        // 'hod_approver' => user()->id,
                        // 'dean_approver' => user()->id,
                        // 'commitee_approver' => user()->id,
                        // 'eo_approval' => 1,
                        // 'hod_approval' => 1,
                        // 'dean_approval' => 1,
                        // 'committee_approval' => 1,
                        'computed_at' => now(),
                        // 'eo_approved_at' => now(),
                        // 'hod_approved_at' => now(),
                        // 'dean_approved_at' => now(),
                        // 'committee_approved_at' => now(),
                        // 'senate_approved_at' =>now(),
                        'last_updated_at' => now(),

                    ];


                    $newComputedResult = ComputedResult::updateOrCreate([
                        'program_id' => $request->programme,
                        'schoolsession_id' => $request->schsession,
                        'semester_id' => $request->schsemester,
                        'study_level' => $request->studylevel,
                    ], $computeDetails);

                    if ($newComputedResult) {
                        #insert successful, fetch the record and begin to fire computation record
                        # fecth the id and trigger the job with each regMonitor entry

                        $computeId = ComputedResult::where('program_id', $request->programme)
                                                    ->where('schoolsession_id', $request->schsession)
                                                    ->where('semester_id', $request->schsemester)
                                                    ->where('study_level', $request->studylevel)
                                                    ->first();
                        //return $computeId;

                        if ($computeId) {
                            # send the regMonitor for result computation
                            $regMonitors = RegMonitor::where('program_id', $request->programme)
                                                        ->where('session_id', $request->schsession)
                                                        ->where('semester_id', $request->schsemester)
                                                        ->where('level_id', $request->studylevel)
                                                        ->get();

                            $computeResultUId = $computeId->uid;

                            foreach ($regMonitors as $v) {

                                SubmitResultComputationJob::dispatch($computeResultUId, $v->uid);

                            }

                            return redirect(route('begin-result-computation'))->with('info' , "Recompute Submitted Successfully !!! Please check back after One Minute");

                        }else{

                            return redirect(route('begin-result-computation'))->with('error', "Error!!!! There was a problem computing this result, Please try again after a few seconds");
                        }

                    }

                }elseif ($request->command ==26) {
                    # This result has been computed before
                    # Grab the Computed result Id and move on
                    $computeId = ComputedResult::where('program_id', $request->programme)
                                                    ->where('schoolsession_id', $request->schsession)
                                                    ->where('semester_id', $request->schsemester)
                                                    ->where('study_level', $request->studylevel)
                                                    ->first();



                    if ($computeId) {
                        # send the regMonitor for result computation

                        $computeResultUId = $computeId->uid;

                        //return $computeResultUId;
                        $regMonitors = RegMonitor::where('program_id', $request->programme)
                                                        ->where('session_id', $request->schsession)
                                                        ->where('semester_id', $request->schsemester)
                                                        ->where('level_id', $request->studylevel)
                                                        ->get();


                        foreach ($regMonitors as $v) {

                            SubmitResultComputationJob::dispatch($computeResultUId, $v->uid);
                        }

                        return redirect(route('begin-result-computation'))->with('info' , "Recompute Submitted Successfully !!! Please check back after One Minute");

                    }else{

                        return redirect(route('begin-result-computation'))->with('error', "Error!!!! Looks like your Access level is low, You'd be blocked if you try too frequently");
                    }

                    # Just grab the id and pass for each entry of regMonitor
                }
            }


            # return status back to the user to check

            return redirect(route('select.oldresultsCompute'))->with('error', "Error!!!! There was a problem, pls retry!!!");

        }
    }

    public function searchComputedResults(){

        return view('results.select-computed-results-to-view');

    }


    public function viewComputedResults(Request $request){

        //validate the input
        $validated = $request->validate([
            // 'studylevel' =>'required',
            // 'programme' =>'required',
            'sch_session' =>'required',
            'sch_semester' =>'required',
        ]);
        #get the staff user programmes list
        $programs = getUserProgramIds(user()->id);
        Arr::pluck($programs, 'id');

        $result = ComputedResult::where('schoolsession_id', $request->sch_session)
                                ->where('semester_id', $request->sch_semester)
                                ->whereIn('program_id', $programs)
                                ->orderBy('program_id', 'asc')
                                ->orderBy('study_level', 'asc')
                                ->get();

        if ($result) {

            if (count($result)>=1) {

                // return $result;

                return view('results.view-computed-results', compact('result'));

            }else{

                return back()->with('error', "Sorry No Results are found for this search criteria");

            }

        }else{

            return back()->with('error', "Sorry No Results are found for this search criteria");
        }

        return "welcome to view computed Result";


    }


    public function checkComputedResult($id, $sem)
    {

        if (user()->hasRole('admin|dap|acad_eo|dean|hod|exam_officer|reg_officer|vc')) {

            $cResult = ComputedResult::where('uid', $id)->first();


            $studyLevel = $cResult->study_level;
            $stdProgram =  $cResult->program_id;
            $schoolSession = $cResult->schoolsession_id;
            $stdSemester = $cResult->semester_id;

            $resultDetails=[];
            $resultDetails =collect([
                $studyLevel,
                $stdProgram,
                $schoolSession,
                $stdSemester
            ]);


            if ($sem ==3) {
                # this request is for sessional


                # correct the semester to second and treat only those with second semeser result
                $resultSemester = 2;

                # check for the second semester ComputedResult, You must find something else return error
                $reComputeCheck = ComputedResult::where('program_id', $stdProgram)
                                            ->where('schoolsession_id', $schoolSession)
                                            ->where('semester_id', 2)
                                            ->where('study_level', $studyLevel)
                                            ->first();
                if (!$reComputeCheck) {
                    # Nothing found return error and request for computation of second semester result first
                    return back()->with('error', "Error !!! Second Semester has not been computed yet, Please ensure it is computed before you proceed !!!!");

                }else{
                    # Result found, now you can check if the sessional result has been computed or not
                    if ($reComputeCheck->is_s_computed =='1') {

                        $resultComputed = true;
                        $resultId = $reComputeCheck->uid;

                    }elseif ($reComputeCheck->is_s_computed =='0') {
                        # it is not computed
                        $resultComputed = false;
                        $resultId = false;

                    }else{

                        return back()->with('error', "Unable to Interprete request in Transcript Upload");
                    }

                }

                # Fetch second semester RegMonitors but with the sessional column for all results
                //Next fetch all regMonitors that fit this with their results and show the result columns on the page for further processing
                    $regStudents = RegMonitor::where('program_id', $stdProgram)
                                ->where('session_id', $schoolSession)
                                ->where('semester_id', 2)
                                ->where('level_id', $studyLevel)
                                ->select('id','uid','student_id', 's_status as r_status', 'ltcr', 'tcr', 'ltwgp', 's_twgp as twgp', 'lcgpa','s_cgpa as cgpa')
                                ->get();

                    if (count($regStudents)>0) {


                        return view('results.viewComputedResultsList', compact('regStudents','resultComputed','resultId','resultDetails','studyLevel','stdProgram','schoolSession','stdSemester','sem','reComputeCheck'));

                        //return $regStudents;

                    }else{

                        return back()->with('error', "Error !!! No Students have registered for this selection, therefore no Result to Compute!!!");

                    }



                # End of Sessional Semester Request
            }

            //check if result computation record exist in the data base and forward the status to determine comptue button on the results page

            $reComputeCheck = ComputedResult::where('program_id', $stdProgram)
                                            ->where('schoolsession_id', $schoolSession)
                                            ->where('semester_id', $stdSemester)
                                            ->where('study_level', $studyLevel)
                                            ->first();
            if (!$reComputeCheck) {
                //Nothing found set the not computed parameter to true
                $resultComputed = false;
                $resultId = false;

            }else{
                //Result found, set the uid and get set to forward to the page
                $resultComputed = true;
                $resultId = $reComputeCheck->uid;
            }

            //Next fetch all regMonitors that fit this with their results and show the result columns on the page for further processing
            $regStudents = RegMonitor::where('program_id', $stdProgram)
                                    ->where('session_id', $schoolSession)
                                    ->where('semester_id', $stdSemester)
                                    ->where('level_id', $studyLevel)
                                    ->get();

            if (count($regStudents)>0) {


                return view('results.viewComputedResultsList', compact('regStudents','resultComputed','resultId','resultDetails','studyLevel','stdProgram','schoolSession','stdSemester','sem','reComputeCheck'));
                return $regStudents;

            }else{

                return back()->with('error', "Error !!! No Students have registered for this selection, therefore no Result to Compute!!!");

            }



        } else{

            return back()->with('error', 'You do not have the privileges to perform this action, contact ICT');

        }
    }



    public function viewSenateSheet($uid,$sem){

        //return $sem;

        # use the uid to fetch the computed result parameters
        $computedResultDetails = ComputedResult::join('programs as p','p.id','=','computed_results.program_id')
                                                ->join('departments as d', 'd.id','=', 'p.department_id')
                                                ->join('faculties as f', 'f.id','=','d.faculty_id')
                                                ->where('computed_results.uid', $uid)
                                                ->select('computed_results.*','p.name as programName','d.name as departmentName','f.name as facultyName')
                                                ->first();


                                                # next get all results tied to this result sheet
        if ($sem ==3) {
            $results = RegMonitor::join('student_records as s','s.id','=','reg_monitors.student_id')
                            ->join('user_profiles as p', 'p.user_id','=','s.user_id')
                            ->where('r_computed_result_id', $computedResultDetails->id)
                            ->select('reg_monitors.*', 'p.gender','s.matric','s.svc','s.bn', 's.user_id')
                            ->orderBy('reg_monitors.s_message', 'asc')
                            ->get();
        }else{

            $results = RegMonitor::join('student_records as s','s.id','=','reg_monitors.student_id')
                            ->join('user_profiles as p', 'p.user_id','=','s.user_id')
                            ->where('r_computed_result_id', $computedResultDetails->id)
                            ->select('reg_monitors.*', 'p.gender','s.matric','s.svc','s.bn', 's.user_id')
                            ->orderBy('reg_monitors.message', 'asc')
                            ->get();
        }


        $details = [];


        foreach ($results as $k) {
            #get the registration items
            //return $k;
            if ($sem==3) {


                # for the sessional, seperate the sessiona results from the normal results, seperate first semester from second semester.
                $regItems = [];
                $twgpCount = 0;
                $regCount = 0;

                $firstSemester = RegMonitorItems::join('semester_courses as s', 's.id','=','reg_monitor_items.course_id')
                                            ->where('reg_monitor_items.student_id', $k->student_id)
                                            ->where('reg_monitor_items.is_reg_sess','1')
                                            ->where('reg_monitor_items.session_id', $k->session_id)
                                            ->where('reg_monitor_items.semester_id',1)
                                            ->select('reg_monitor_items.*', 's.creditUnits','s.courseCode')
                                            ->get();

                if (count($firstSemester)>0) {

                    $regCount = count($firstSemester);


                    foreach ($firstSemester as $f) {

                        $twgpCount = $twgpCount + $f->sess_twgp;

                        $regItems[] = collect([
                            'student_id' => $f->student_id,
                            'course_id' => $f->course_id,
                            'courseCode' => $f->courseCode,
                            'creditUnits' => $f->creditUnits,
                            'gtotal' => $f->sess_total,
                            'ggrade' => $f->sess_grade,
                            'twgp' => $f->sess_twgp,
                            'is_reg_sess' => $f->is_reg_sess,
                            'sessRegCheck' => true,
                        ]);

                    }
                }


                $secondSemester = RegMonitorItems::join('semester_courses as s', 's.id','=','reg_monitor_items.course_id')
                                            ->where('reg_monitor_items.student_id', $k->student_id)
                                            ->where('reg_monitor_items.session_id', $k->session_id)
                                            ->where('reg_monitor_items.semester_id',2)
                                            ->select('reg_monitor_items.*', 's.creditUnits','s.courseCode')
                                            ->get();

                    if (count($secondSemester)>0) {

                        $regCount = $regCount + count($secondSemester);

                        foreach ($secondSemester as $s) {



                            if ($s->is_reg_sess == '1') {
                                # This is a sessional result
                                $twgpCount = $twgpCount + $s->sess_twgp;

                                $regItems[]= collect([
                                    'student_id' => $s->student_id,
                                    'course_id' => $s->course_id,
                                    'courseCode' => $s->courseCode,
                                    'creditUnits' => $s->creditUnits,
                                    'gtotal' => $s->sess_total,
                                    'ggrade' => $s->sess_grade,
                                    'twgp' => $s->sess_twgp,
                                    'is_reg_sess' => $s->is_reg_sess,
                                    'sessRegCheck' => true,
                                ]);

                            }else {
                                # This is not a sessional result

                                $twgpCount = $twgpCount + $s->twgp;

                                $regItems[] = collect([
                                    'student_id' => $s->student_id,
                                    'course_id' => $s->course_id,
                                    'courseCode' => $s->courseCode,
                                    'creditUnits' => $s->creditUnits,
                                    'gtotal' => $s->gtotal,
                                    'ggrade' => $s->ggrade,
                                    'twgp' => $s->twgp,
                                    'is_reg_sess' => $s->is_reg_sess,
                                    'sessRegCheck' => false,
                                ]);

                            }

                        }
                    }

                # fetch carry overs

                $carryOvers = RegMonitorItems::join('semester_courses as s', 's.id','=','reg_monitor_items.course_id')
                                            ->where('reg_monitor_items.student_id', $k->student_id)
                                            ->where('reg_monitor_items.session_id', $k->session_id)
                                            ->where('reg_monitor_items.is_reg_sess','1')
                                            ->where('reg_monitor_items.sess_is_passed','0')
                                            ->select('s.courseCode')
                                            ->get();


                # next compute the count
                $rollCount = $regCount + 1;

                $details[]=collect([
                    'student_id' => $k->student_id,
                    'matric' => $k->matric,
                    'name' => getUserById($k->user_id)->name,
                    'gender' => $k->gender,
                    'svc' => $k->svc,
                    'bn' => $k->bn,
                    'regCount' => $rollCount,
                    'regItems' => $regItems,
                    'ltcr' =>$k->ltcr,
                    'cur' =>$k->cur,
                    'tcr' =>$k->tcr,
                    'ltwgp' =>$k->ltwgp,
                    'wgp' => $k->wgp,
                    'twgpCount' => $twgpCount,
                    'twgp' => $k->s_twgp,
                    'lcgpa' => number_format(convertToNaira($k->lcgpa),2),
                    'cgpa' => number_format(convertToNaira($k->s_cgpa),2),
                    'remark' => $k->s_message=='' ? 'PASS':$k->s_message,
                    'carryOvers' => $carryOvers
                ]);


            }else{

                $regCount = 0;
                $twgpCount =0;
                $regItems = [];

                $semesterReg = RegMonitorItems::join('semester_courses as s', 's.id','=','reg_monitor_items.course_id')
                                            ->select('reg_monitor_items.*', 's.creditUnits','s.courseCode')
                                            ->where('monitor_id', $k->id)
                                            ->get();

                    if (count($semesterReg)>0) {




                        $regCount = $regCount + count($semesterReg) ;

                        foreach ($semesterReg as $s) {

                            $twgpCount = $twgpCount + $s->twgp;

                            $regItems[]= collect([
                                'student_id' => $s->student_id,
                                'course_id' => $s->course_id,
                                'courseCode' => $s->courseCode,
                                'creditUnits' => $s->creditUnits,
                                'gtotal' => $s->gtotal,
                                'ggrade' => $s->ggrade,
                                'twgp' => $s->twgp,
                                'is_reg_sess' => $s->is_reg_sess,
                                'sessRegCheck' => false,
                            ]);

                        }


                        $carryOvers = RegMonitorItems::join('semester_courses as s', 's.id','=','reg_monitor_items.course_id')
                                            ->where('reg_monitor_items.student_id', $k->student_id)
                                            ->where('reg_monitor_items.session_id', $k->session_id)
                                            ->where('reg_monitor_items.semester_id', $sem)
                                            ->where('reg_monitor_items.is_passed','0')
                                            ->select('s.courseCode')
                                            ->get();


                    }



                $details[]=collect([
                    'student_id' => $k->student_id,
                    'matric' => $k->matric,
                    'name' => getUserById($k->user_id)->name,
                    'gender' => $k->gender,
                    'svc' => $k->svc,
                    'bn' => $k->bn,
                    'regCount' => count($semesterReg)+1,
                    'regItems' => $regItems,
                    'ltcr' =>$k->ltcr,
                    'cur' =>$k->cur,
                    'tcr' =>$k->tcr,
                    'ltwgp' =>$k->ltwgp,
                    'wgp' => $k->wgp,
                    'twgpCount' => $twgpCount,
                    'twgp' => $k->twgp,
                    'lcgpa' => number_format(convertToNaira($k->lcgpa),2),
                    'cgpa' => number_format(convertToNaira($k->cgpa),2),
                    'remark' => $k->message==''? 'PASS':$k->message,
                    'carryOvers' => $carryOvers
                ]);


            }


        }

        //return $details;


        return view('admin.printSenateSheet')->with(['resultDetails'=>$details, 'progDetails'=>$computedResultDetails, 'semester'=>$sem]);
    }

    public function viewPassedSenateSheet($uid,$sem){

        //return $sem;

        # use the uid to fetch the computed result parameters
        $computedResultDetails = ComputedResult::join('programs as p','p.id','=','computed_results.program_id')
                                                ->join('departments as d', 'd.id','=', 'p.department_id')
                                                ->join('faculties as f', 'f.id','=','d.faculty_id')
                                                ->where('computed_results.uid', $uid)
                                                ->select('computed_results.*','p.name as programName','d.name as departmentName','f.name as facultyName')
                                                ->first();


                                                # next get all results tied to this result sheet
        if ($sem ==3) {
            $results = RegMonitor::join('student_records as s','s.id','=','reg_monitors.student_id')
                            ->join('user_profiles as p', 'p.user_id','=','s.user_id')
                            ->where('r_computed_result_id', $computedResultDetails->id)
                            ->where('reg_monitors.s_message',null)
                            ->select('reg_monitors.*', 'p.gender','s.matric','s.svc','s.bn', 's.user_id')
                            ->orderBy('reg_monitors.s_message', 'asc')
                            ->orderBy('s.matric','asc')
                            ->get();
        }else{

            $results = RegMonitor::join('student_records as s','s.id','=','reg_monitors.student_id')
                            ->join('user_profiles as p', 'p.user_id','=','s.user_id')
                            ->where('r_computed_result_id', $computedResultDetails->id)
                            ->where('reg_monitors.message',null)
                            ->select('reg_monitors.*', 'p.gender','s.matric','s.svc','s.bn', 's.user_id')
                            ->orderBy('reg_monitors.message', 'asc')
                            ->orderBy('s.matric','asc')
                            ->get();
        }


        $details = [];


        foreach ($results as $k) {
            #get the registration items
            //return $k;
            if ($sem==3) {


                # for the sessional, seperate the sessiona results from the normal results, seperate first semester from second semester.
                $regItems = [];
                $twgpCount = 0;
                $regCount = 0;

                $firstSemester = RegMonitorItems::join('semester_courses as s', 's.id','=','reg_monitor_items.course_id')
                                            ->where('reg_monitor_items.student_id', $k->student_id)
                                            ->where('reg_monitor_items.is_reg_sess','1')
                                            ->where('reg_monitor_items.session_id', $k->session_id)
                                            ->where('reg_monitor_items.semester_id',1)
                                            ->select('reg_monitor_items.*', 's.creditUnits','s.courseCode')
                                            ->get();

                if (count($firstSemester)>0) {

                    $regCount = count($firstSemester);


                    foreach ($firstSemester as $f) {

                        $twgpCount = $twgpCount + $f->sess_twgp;

                        $regItems[] = collect([
                            'student_id' => $f->student_id,
                            'course_id' => $f->course_id,
                            'courseCode' => $f->courseCode,
                            'creditUnits' => $f->creditUnits,
                            'gtotal' => $f->sess_total,
                            'ggrade' => $f->sess_grade,
                            'twgp' => $f->sess_twgp,
                            'is_reg_sess' => $f->is_reg_sess,
                            'sessRegCheck' => true,
                        ]);

                    }
                }


                $secondSemester = RegMonitorItems::join('semester_courses as s', 's.id','=','reg_monitor_items.course_id')
                                            ->where('reg_monitor_items.student_id', $k->student_id)
                                            ->where('reg_monitor_items.session_id', $k->session_id)
                                            ->where('reg_monitor_items.semester_id',2)
                                            ->select('reg_monitor_items.*', 's.creditUnits','s.courseCode')
                                            ->get();

                    if (count($secondSemester)>0) {

                        $regCount = $regCount + count($secondSemester);

                        foreach ($secondSemester as $s) {



                            if ($s->is_reg_sess == '1') {
                                # This is a sessional result
                                $twgpCount = $twgpCount + $s->sess_twgp;

                                $regItems[]= collect([
                                    'student_id' => $s->student_id,
                                    'course_id' => $s->course_id,
                                    'courseCode' => $s->courseCode,
                                    'creditUnits' => $s->creditUnits,
                                    'gtotal' => $s->sess_total,
                                    'ggrade' => $s->sess_grade,
                                    'twgp' => $s->sess_twgp,
                                    'is_reg_sess' => $s->is_reg_sess,
                                    'sessRegCheck' => true,
                                ]);

                            }else {
                                # This is not a sessional result

                                $twgpCount = $twgpCount + $s->twgp;

                                $regItems[] = collect([
                                    'student_id' => $s->student_id,
                                    'course_id' => $s->course_id,
                                    'courseCode' => $s->courseCode,
                                    'creditUnits' => $s->creditUnits,
                                    'gtotal' => $s->gtotal,
                                    'ggrade' => $s->ggrade,
                                    'twgp' => $s->twgp,
                                    'is_reg_sess' => $s->is_reg_sess,
                                    'sessRegCheck' => false,
                                ]);

                            }

                        }
                    }

                # fetch carry overs

                $carryOvers = RegMonitorItems::join('semester_courses as s', 's.id','=','reg_monitor_items.course_id')
                                            ->where('reg_monitor_items.student_id', $k->student_id)
                                            ->where('reg_monitor_items.session_id', $k->session_id)
                                            ->where('reg_monitor_items.is_reg_sess','1')
                                            ->where('reg_monitor_items.sess_is_passed','0')
                                            ->select('s.courseCode')
                                            ->get();


                # next compute the count
                $rollCount = $regCount + 1;

                $details[]=collect([
                    'student_id' => $k->student_id,
                    'matric' => $k->matric,
                    'name' => getUserById($k->user_id)->name,
                    'gender' => $k->gender,
                    'svc' => $k->svc,
                    'bn' => $k->bn,
                    'regCount' => $rollCount,
                    'regItems' => $regItems,
                    'ltcr' =>$k->ltcr,
                    'cur' =>$k->cur,
                    'tcr' =>$k->tcr,
                    'ltwgp' =>$k->ltwgp,
                    'wgp' => $k->wgp,
                    'twgpCount' => $twgpCount,
                    'twgp' => $k->s_twgp,
                    'lcgpa' => number_format(convertToNaira($k->lcgpa),2),
                    'cgpa' => number_format(convertToNaira($k->s_cgpa),2),
                    'remark' => $k->s_message=='' ? 'PASS':$k->s_message,
                    'carryOvers' => $carryOvers
                ]);


            }else{

                $regCount = 0;
                $twgpCount =0;
                $regItems = [];

                $semesterReg = RegMonitorItems::join('semester_courses as s', 's.id','=','reg_monitor_items.course_id')
                                            ->select('reg_monitor_items.*', 's.creditUnits','s.courseCode')
                                            ->where('monitor_id', $k->id)
                                            ->get();

                    if (count($semesterReg)>0) {




                        $regCount = $regCount + count($semesterReg) ;

                        foreach ($semesterReg as $s) {

                            $twgpCount = $twgpCount + $s->twgp;

                            $regItems[]= collect([
                                'student_id' => $s->student_id,
                                'course_id' => $s->course_id,
                                'courseCode' => $s->courseCode,
                                'creditUnits' => $s->creditUnits,
                                'gtotal' => $s->gtotal,
                                'ggrade' => $s->ggrade,
                                'twgp' => $s->twgp,
                                'is_reg_sess' => $s->is_reg_sess,
                                'sessRegCheck' => false,
                            ]);

                        }


                        $carryOvers = RegMonitorItems::join('semester_courses as s', 's.id','=','reg_monitor_items.course_id')
                                            ->where('reg_monitor_items.student_id', $k->student_id)
                                            ->where('reg_monitor_items.session_id', $k->session_id)
                                            ->where('reg_monitor_items.semester_id', $sem)
                                            ->where('reg_monitor_items.is_passed','0')
                                            ->select('s.courseCode')
                                            ->get();


                    }



                $details[]=collect([
                    'student_id' => $k->student_id,
                    'matric' => $k->matric,
                    'name' => getUserById($k->user_id)->name,
                    'gender' => $k->gender,
                    'svc' => $k->svc,
                    'bn' => $k->bn,
                    'regCount' => count($semesterReg)+1,
                    'regItems' => $regItems,
                    'ltcr' =>$k->ltcr,
                    'cur' =>$k->cur,
                    'tcr' =>$k->tcr,
                    'ltwgp' =>$k->ltwgp,
                    'wgp' => $k->wgp,
                    'twgpCount' => $twgpCount,
                    'twgp' => $k->twgp,
                    'lcgpa' => number_format(convertToNaira($k->lcgpa),2),
                    'cgpa' => number_format(convertToNaira($k->cgpa),2),
                    'remark' => $k->message==''? 'PASS':$k->message,
                    'carryOvers' => $carryOvers
                ]);


            }


        }

        //return $details;


        return view('admin.printSenateSheet')->with(['resultDetails'=>$details, 'progDetails'=>$computedResultDetails, 'semester'=>$sem]);
    }

    public function viewFailedSenateSheet($uid,$sem){

        //return $sem;

        # use the uid to fetch the computed result parameters
        $computedResultDetails = ComputedResult::join('programs as p','p.id','=','computed_results.program_id')
                                                ->join('departments as d', 'd.id','=', 'p.department_id')
                                                ->join('faculties as f', 'f.id','=','d.faculty_id')
                                                ->where('computed_results.uid', $uid)
                                                ->select('computed_results.*','p.name as programName','d.name as departmentName','f.name as facultyName')
                                                ->first();


                                                # next get all results tied to this result sheet
        if ($sem ==3) {
            $results = RegMonitor::join('student_records as s','s.id','=','reg_monitors.student_id')
                            ->join('user_profiles as p', 'p.user_id','=','s.user_id')
                            ->where('r_computed_result_id', $computedResultDetails->id)
                            ->where('reg_monitors.s_message','!=',null)
                            ->select('reg_monitors.*', 'p.gender','s.matric','s.svc','s.bn', 's.user_id')
                            ->orderBy('reg_monitors.s_message', 'asc')
                            ->orderBy('s.matric','asc')
                            ->get();
        }else{

            $results = RegMonitor::join('student_records as s','s.id','=','reg_monitors.student_id')
                            ->join('user_profiles as p', 'p.user_id','=','s.user_id')
                            ->where('r_computed_result_id', $computedResultDetails->id)
                            ->where('reg_monitors.message','!=',null)
                            ->select('reg_monitors.*', 'p.gender','s.matric','s.svc','s.bn', 's.user_id')
                            ->orderBy('reg_monitors.message', 'asc')
                            ->orderBy('s.matric','asc')
                            ->get();
        }


        $details = [];


        foreach ($results as $k) {
            #get the registration items
            //return $k;
            if ($sem==3) {


                # for the sessional, seperate the sessiona results from the normal results, seperate first semester from second semester.
                $regItems = [];
                $twgpCount = 0;
                $regCount = 0;

                $firstSemester = RegMonitorItems::join('semester_courses as s', 's.id','=','reg_monitor_items.course_id')
                                            ->where('reg_monitor_items.student_id', $k->student_id)
                                            ->where('reg_monitor_items.is_reg_sess','1')
                                            ->where('reg_monitor_items.session_id', $k->session_id)
                                            ->where('reg_monitor_items.semester_id',1)
                                            ->select('reg_monitor_items.*', 's.creditUnits','s.courseCode')
                                            ->get();

                if (count($firstSemester)>0) {

                    $regCount = count($firstSemester);


                    foreach ($firstSemester as $f) {

                        $twgpCount = $twgpCount + $f->sess_twgp;

                        $regItems[] = collect([
                            'student_id' => $f->student_id,
                            'course_id' => $f->course_id,
                            'courseCode' => $f->courseCode,
                            'creditUnits' => $f->creditUnits,
                            'gtotal' => $f->sess_total,
                            'ggrade' => $f->sess_grade,
                            'twgp' => $f->sess_twgp,
                            'is_reg_sess' => $f->is_reg_sess,
                            'sessRegCheck' => true,
                        ]);

                    }
                }


                $secondSemester = RegMonitorItems::join('semester_courses as s', 's.id','=','reg_monitor_items.course_id')
                                            ->where('reg_monitor_items.student_id', $k->student_id)
                                            ->where('reg_monitor_items.session_id', $k->session_id)
                                            ->where('reg_monitor_items.semester_id',2)
                                            ->select('reg_monitor_items.*', 's.creditUnits','s.courseCode')
                                            ->get();

                    if (count($secondSemester)>0) {

                        $regCount = $regCount + count($secondSemester);

                        foreach ($secondSemester as $s) {



                            if ($s->is_reg_sess == '1') {
                                # This is a sessional result
                                $twgpCount = $twgpCount + $s->sess_twgp;

                                $regItems[]= collect([
                                    'student_id' => $s->student_id,
                                    'course_id' => $s->course_id,
                                    'courseCode' => $s->courseCode,
                                    'creditUnits' => $s->creditUnits,
                                    'gtotal' => $s->sess_total,
                                    'ggrade' => $s->sess_grade,
                                    'twgp' => $s->sess_twgp,
                                    'is_reg_sess' => $s->is_reg_sess,
                                    'sessRegCheck' => true,
                                ]);

                            }else {
                                # This is not a sessional result

                                $twgpCount = $twgpCount + $s->twgp;

                                $regItems[] = collect([
                                    'student_id' => $s->student_id,
                                    'course_id' => $s->course_id,
                                    'courseCode' => $s->courseCode,
                                    'creditUnits' => $s->creditUnits,
                                    'gtotal' => $s->gtotal,
                                    'ggrade' => $s->ggrade,
                                    'twgp' => $s->twgp,
                                    'is_reg_sess' => $s->is_reg_sess,
                                    'sessRegCheck' => false,
                                ]);

                            }

                        }
                    }

                # fetch carry overs

                $carryOvers = RegMonitorItems::join('semester_courses as s', 's.id','=','reg_monitor_items.course_id')
                                            ->where('reg_monitor_items.student_id', $k->student_id)
                                            ->where('reg_monitor_items.session_id', $k->session_id)
                                            ->where('reg_monitor_items.is_reg_sess','1')
                                            ->where('reg_monitor_items.sess_is_passed','0')
                                            ->select('s.courseCode')
                                            ->get();


                # next compute the count
                $rollCount = $regCount + 1;

                $details[]=collect([
                    'student_id' => $k->student_id,
                    'matric' => $k->matric,
                    'name' => getUserById($k->user_id)->name,
                    'gender' => $k->gender,
                    'svc' => $k->svc,
                    'bn' => $k->bn,
                    'regCount' => $rollCount,
                    'regItems' => $regItems,
                    'ltcr' =>$k->ltcr,
                    'cur' =>$k->cur,
                    'tcr' =>$k->tcr,
                    'ltwgp' =>$k->ltwgp,
                    'wgp' => $k->wgp,
                    'twgpCount' => $twgpCount,
                    'twgp' => $k->s_twgp,
                    'lcgpa' => number_format(convertToNaira($k->lcgpa),2),
                    'cgpa' => number_format(convertToNaira($k->s_cgpa),2),
                    'remark' => $k->s_message=='' ? 'PASS':$k->s_message,
                    'carryOvers' => $carryOvers
                ]);


            }else{

                $regCount = 0;
                $twgpCount =0;
                $regItems = [];

                $semesterReg = RegMonitorItems::join('semester_courses as s', 's.id','=','reg_monitor_items.course_id')
                                            ->select('reg_monitor_items.*', 's.creditUnits','s.courseCode')
                                            ->where('monitor_id', $k->id)
                                            ->get();

                    if (count($semesterReg)>0) {




                        $regCount = $regCount + count($semesterReg) ;

                        foreach ($semesterReg as $s) {

                            $twgpCount = $twgpCount + $s->twgp;

                            $regItems[]= collect([
                                'student_id' => $s->student_id,
                                'course_id' => $s->course_id,
                                'courseCode' => $s->courseCode,
                                'creditUnits' => $s->creditUnits,
                                'gtotal' => $s->gtotal,
                                'ggrade' => $s->ggrade,
                                'twgp' => $s->twgp,
                                'is_reg_sess' => $s->is_reg_sess,
                                'sessRegCheck' => false,
                            ]);

                        }


                        $carryOvers = RegMonitorItems::join('semester_courses as s', 's.id','=','reg_monitor_items.course_id')
                                            ->where('reg_monitor_items.student_id', $k->student_id)
                                            ->where('reg_monitor_items.session_id', $k->session_id)
                                            ->where('reg_monitor_items.semester_id', $sem)
                                            ->where('reg_monitor_items.is_passed','0')
                                            ->select('s.courseCode')
                                            ->get();


                    }



                $details[]=collect([
                    'student_id' => $k->student_id,
                    'matric' => $k->matric,
                    'name' => getUserById($k->user_id)->name,
                    'gender' => $k->gender ,
                    'svc' => $k->svc,
                    'bn' => $k->bn,
                    'regCount' => count($semesterReg)+1,
                    'regItems' => $regItems,
                    'ltcr' =>$k->ltcr,
                    'cur' =>$k->cur,
                    'tcr' =>$k->tcr,
                    'ltwgp' =>$k->ltwgp,
                    'wgp' => $k->wgp,
                    'twgpCount' => $twgpCount,
                    'twgp' => $k->twgp,
                    'lcgpa' => number_format(convertToNaira($k->lcgpa),2),
                    'cgpa' => number_format(convertToNaira($k->cgpa),2),
                    'remark' => $k->message==''? 'PASS':$k->message,
                    'carryOvers' => $carryOvers
                ]);


            }


        }

        //return $details;


        return view('admin.printSenateSheet')->with(['resultDetails'=>$details, 'progDetails'=>$computedResultDetails, 'semester'=>$sem]);
    }

    public function approveComputedResults(Request $request){

        $validated = $request->validate([
            'approveAs' =>'required',
            'action' =>'required',
        ]);

        if ($request->cResults) {
            # retults present, do the needful and send for processing
            $actionBy = user()->id;
            $approveAs = $request->approveAs;
            $action = $request->action;
            $actionDate = now();

            foreach ($request->cResults as $r) {
                #next fire up the approval management job
                $resultId = $r;

                ResultApprovalManagementJob::dispatch($actionBy,$resultId, $approveAs, $action, $actionDate);
            }



            return redirect(route('home'))->with('info', "Approval Request for all selected results submitted successfuly !!!");

            return $request;
        }else{

            #nothing submitted, return this user to the begining of the search

            $programs = getUserProgramsDropdown(user()->id);

            return redirect(route('home'))->with('error', "Error, No result selected for Approval/Disapproval !!!");
        }


    }

    public function singleResultApprovalMgt(Request $request,$id){




        $actionBy = user()->id;
        $resultId = $id;
        $approveAs = $request->approveAs;
        $action = $request->action;
        $actionDate = now();

        #next fire up the approval management job
        ResultApprovalManagementJob::dispatch($actionBy,$resultId, $approveAs, $action, $actionDate);

        return back()->with('info', "Approval Request submitted successfuly !!!");
    }




    public function ResultComputation(Request $request){
        if (user()->hasRole('admin|exam_officer|hod|dean|vc')) {

            //validate the input
            $validated = $request->validate([
                'studylevel' =>'required',
                'programme' =>'required',
                'schsession' =>'required',
                'schsemester' =>'required',
                'regMonitor' =>'required',
                'command' =>'required',
            ]);

            #Check the semester to see if it is a Sessional Semester then redirect appropriately
            if ($request->schsemester ==3) {

                # Semester is a Sessional Semester, Proceed with sessional computations
                # This result has been computed before
                    # Grab the Computed result Id and move on
                    $computeId = ComputedResult::where('program_id', $request->programme)
                                                    ->where('schoolsession_id', $request->schsession)
                                                    ->where('semester_id', 2)
                                                    ->where('study_level', $request->studylevel)
                                                    ->first();


                    if ($computeId) {
                        # send the regMonitor for result computation

                        $computeResultUId = $computeId->uid;

                        $computeId->is_s_computed = '1';
                        $computeId->s_computed_status = 'pending';
                        $computeId->save();

                        //return $computeResultUId;

                        foreach ($request->regMonitor as $v) {

                            SubmitSessionalResultComputationJob::dispatch($computeResultUId, $v);

                        }

                        # Next update the Absent List with a Job for that purpose

                        return back()->with('info' , "Recompute Submitted Successfully !!! Please check back after One Minute");

                    }else{

                        return back()->with('error', "Error!!!! Looks like your Access level is low, You'd be blocked if you try too frequently");
                    }

                //return $request;

                //return redirect(route('select.oldresultsCompute'))->with('error', "Sessional Result Computation Not Ready");

                # End of Sessional Result Submission

            } elseif ($request->schsemester == 1  || $request->schsemester == 2) {

                //return $request;

                //check to see if it is fresh computation or recomputation
                if ($request->command ==25) {
                    # computaton is a fresh one insert records into the compute result

                    $computeDetails = [
                        'uid' => uniqid("cmpr_"),
                        'program_id' => $request->programme,
                        'schoolsession_id' => $request->schsession,
                        'semester_id' => $request->schsemester,
                        'study_level' => $request->studylevel,
                        'computed_by' => user()->id,
                        'last_updated_at' => now(),

                    ];

                    $newComputedResult = ComputedResult::updateOrCreate([
                        'program_id' => $request->programme,
                        'schoolsession_id' => $request->schsession,
                        'semester_id' => $request->schsemester,
                        'study_level' => $request->studylevel,
                    ], $computeDetails);

                    if ($newComputedResult) {
                        #insert successful, fetch the record and begin to fire computation record
                        # fecth the id and trigger the job with each regMonitor entry

                        $computeId = ComputedResult::where('program_id', $request->programme)
                                                    ->where('schoolsession_id', $request->schsession)
                                                    ->where('semester_id', $request->schsemester)
                                                    ->where('study_level', $request->studylevel)
                                                    ->first();
                        //return $computeId;

                        if ($computeId) {
                            # send the regMonitor for result computation

                            $computeResultUId = $computeId->uid;

                            foreach ($request->regMonitor as $v) {

                                SubmitResultComputationJob::dispatch($computeResultUId, $v);

                            }

                            return back()->with('info' , "Recompute Submitted Successfully !!! Please check back after One Minute");

                        }else{

                            return back()->with('error', "Error!!!! There was a problem computing this result, Please try again after a few seconds");
                        }

                    }

                }elseif ($request->command ==26) {
                    # This result has been computed before
                    # Grab the Computed result Id and move on
                    $computeId = ComputedResult::where('program_id', $request->programme)
                                                    ->where('schoolsession_id', $request->schsession)
                                                    ->where('semester_id', $request->schsemester)
                                                    ->where('study_level', $request->studylevel)
                                                    ->first();


                    if ($computeId) {
                        # send the regMonitor for result computation

                        $computeResultUId = $computeId->uid;

                        //return $computeResultUId;

                        foreach ($request->regMonitor as $v) {

                            SubmitResultComputationJob::dispatch($computeResultUId, $v);
                        }

                        return back()->with('info' , "Recompute Submitted Successfully !!! Please check back after One Minute");

                    }else{

                        return back()->with('error', "Error!!!! Looks like your Access level is low, You'd be blocked if you try too frequently");
                    }

                    # Just grab the id and pass for each entry of regMonitor
                }
            }

            # return status back to the user to check

            return back()->with('error', "Error!!!! There was a problem, pls retry!!!");

        }
    }



}
