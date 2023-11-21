<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Jobs\BulkRegistrationJob;
use App\Jobs\BulkSingleRegistrationJob;
use App\Jobs\VetoRegistrationJob;
use App\Models\Curriculum;
use App\Models\CurriculumItem;
use App\Models\DroppedCourses;
use App\Models\FeePayment;
use App\Models\RegClearance;
use App\Models\RegMonitor;
use App\Models\RegMonitorItems;
use App\Models\SemesterCourse;
use App\Models\StudentRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Traits\HasRoles;

class StudentRegistrationController extends Controller
{
    use HasRoles;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //show outline for the current semester if the student has not registered before

        $student_id = getStudentByUserId(user()->id)->program_id;

        //return user()->current_level;

        $currentOutline = Curriculum::where([

            'programs_id' => getStudentByUserId(user()->id)->program_id,
            'studyLevel' => user()->current_level,
            'semester' => getSemesterIdByName(activeSession()->currentSemester)
        ])->first();

        $currentOutline;

        if ($currentOutline) {
            return view('students.viewcurriculums', compact('currentOutline'));
        }else{
            return back()->with('error',"No outline found");
        }



        return "Reg Clearance passed, show outlines";
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
        //calculate the maximum credits and return with error if there's a problem
        //return $request;
        $curricula = Curriculum::find($request->curricula_id);

        $totalcredits =$this->getsumCredits($request->carryOvers)+$this->getsumCredits($request->droppedCores)+$this->getsumCredits($request->droppedElectives)+$this->getsumCredits($request->cores)+$this->getsumCredits($request->electives)+$this->getsumCredits($request->registered);

        $diff = $totalcredits-$curricula->maxRegCredits;

        $toadd = $curricula->minRegCredits - $totalcredits;

        // Check the min and Max Credits Required
        if($diff>0){

            return redirect(route('coursereg.index'))->with('error', "Your Selected credit Units exceed the maximum, kindly remove ".$diff." credit Units to complete your registration");

        }elseif($totalcredits<$curricula->minRegCredits){

            return redirect(route('coursereg.index'))->with('error', "Your Selected credit Units are below the required minimum, kindly add ".$toadd." credit Units to complete your registration");

        }elseif(!$this->checkRegClearance($curricula)){

            return redirect(route('coursereg.index'))->with('error', "Error!! You need to make payment to complete your registration to complete your registration");

        }
        else{

            //All Checks clear, Prepare to write records.

            $MonitorDetails = $this->insertNewMonitor($curricula->id);

            //return $request;

            if ($MonitorDetails) {
                //Monitor found, get Reg Monitor Details for Items writing;
                $monitor = RegMonitor::where(['curricula_id'=>$curricula->id, 'student_id'=>getStudentIdByUserId(user()->id),'session_id'=>activeSession()->id ])->first();

                //carryOverCourses
                if ($request->carryOvers) {
                    foreach ($request->carryOvers as $key => $v) {
                        $carryover = RegMonitorItems::where(['course_id'=>$v, 'student_id'=>$monitor->student_id, 'is_co_passed'=>'0'])->where('session_id','!=',activeSession()->id)->first();
                        if ($carryover) {
                            //carry over found write to table
                            $registerDroppedCourse = $this->registerCourse($monitor,$v,$carryover->category,1);
                            if ($registerDroppedCourse) {
                                //registration successful
                            }else{
                                abort(403,"Error Registering Carry Over Courses, Contact ICT");
                            }
                        }
                    }

                }

                //Dropped Core Courses Registration
                if ($request->droppedCores) {

                    //return $request->droppedCores;
                    //return $monitor->student_id;
                    foreach ($request->droppedCores as $v) {
                        //return $v;
                        $droppedcores = DroppedCourses::where(['course_id'=>$v, 'student_id'=>$monitor->student_id, 'category'=>'core'])->first();
                        if ($droppedcores) {

                            //return "found";

                            $registerDroppedCore = $this->registerCourse($monitor,$v,'core',0);
                            if ($registerDroppedCore) {
                                //registration successful, next delete the dropped course
                                $droppedcores->delete();
                            }else{
                                abort(403,"Error Code 40324 Registering Dropped Core Courses, Contact ICT");
                            }
                        }
                    }
                }

                // Write Dropped Elective Courses
                if ($request->droppedElectives) {
                    foreach ($request->droppedElectives as $key => $v) {
                        //return $v;
                        $droppedelectives = DroppedCourses::where(['course_id'=>$v, 'student_id'=>$monitor->student_id, 'category'=>'elective'])->first();
                        if ($droppedelectives) {
                            //carry over found write to table
                            $registerDroppedElectives = $this->registerCourse($monitor,$v,'elective',0);
                            if ($registerDroppedElectives) {
                                // courses registered successfully
                                $droppedelectives->delete();
                            }else{
                                abort(403,"Error Registering Dropped Elective Courses, Contact ICT");
                            }

                        }
                    }

                }

                //Write Core Courses
                if ($request->cores) {
                    foreach ($request->cores as $key => $v) {
                        $registerCore = $this->registerCourse($monitor,$v,'core',0);
                        if ($registerCore) {

                        }
                        else{
                            abort(403, "Error 4011 Registering Core Course");
                        }


                    }

                }

                // Write Elective Courses
                if ($request->electives) {
                    foreach ($request->electives as $key => $v) {
                        $registerElective = $this->registerCourse($monitor,$v,'elective',0);
                        if ($registerElective) {

                        }
                        else{
                            abort(403, "Error Registering  Elective Course");
                        }
                    }

                }

                //Populate Dropped Courses Now

                $curData = CurriculumItem::where('curricula_id',$curricula->id)->get();




                foreach ($curData as $key => $t) {
                    switch ($t->category) {
                        case 'core/optional':
                            //find the course in courseReg, if found skip,
                            //if not found, check the alternative
                            //if alternative found, skip
                            //if alternative not found, enter the main course as
                            $checkEntry = RegMonitorItems::whereIn('course_id',[$t->semester_courses_id,$t->alternative])->where(['monitor_id' => $monitor->id, 'student_id' => $monitor->student_id, 'session_id' => activeSession()->id, 'semester_id'=> $monitor->semester_id,])->count();

                            //return $checkEntry;


                            if ($checkEntry===0) {
                                //Entry was not found make entry into dropped courses
                                $dataentry = [
                                    'student_id' => $monitor->student_id,
                                    'course_id' => $t->semester_courses_id,
                                    'sesstion_id' => activeSession()->id,
                                    'semester_id'=> $monitor->semester_id,
                                    'category' => 'core',
                                ];

                                //return $dataentry;

                                $newEntry = DroppedCourses::upsert($dataentry,$uniqueBy=['student_id','course_id'], $update=[
                                    'category'
                                ]);

                                //return $newEntry;

                                if (!$newEntry) {
                                    abort(403, "Error Updating dropped optional Courses, Contact ICT ERROR 40321");
                                }
                            }


                            break;

                        case 'core':
                            //find course -- if Not found, make entry, else just move on
                            $checkEntry = RegMonitorItems::where(['monitor_id' => $monitor->id, 'student_id' => $monitor->student_id, 'session_id' => activeSession()->id, 'semester_id'=> $monitor->semester_id, 'course_id'=>$t->semester_courses_id])->count();

                            if ($checkEntry ===0) {
                                //Entry was not found make entry into dropped courses
                                $dataentryCore = [
                                    'student_id' => $monitor->student_id,
                                    'course_id' => $t->semester_courses_id,
                                    'sesstion_id' => activeSession()->id,
                                    'semester_id'=> $monitor->semester_id,
                                    'category' => 'core',
                                ];

                                //return $dataentryCore;

                                $newEntryCore = DroppedCourses::upsert($dataentryCore,$uniqueBy=['student_id','course_id'], $update=[
                                    'category'
                                ]);

                                if (!$newEntryCore) {
                                    abort(403, "Error Updating dropped Core Courses, Contact ICT");
                                }
                            }


                            break;

                        case 'elective':
                            //find course -- if Not found, make entry, else just move on
                            $checkEntry = RegMonitorItems::where(['monitor_id' => $monitor->id, 'student_id' => $monitor->student_id, 'session_id' => activeSession()->id, 'semester_id'=> $monitor->semester_id, 'course_id'=>$t->semester_courses_id])->count();

                            if ($checkEntry ===0) {
                                //Entry was not found make entry into dropped courses
                                $dataentryElective = [
                                    'student_id' => $monitor->student_id,
                                    'course_id' => $t->semester_courses_id,
                                    'sesstion_id' => activeSession()->id,
                                    'semester_id'=> $monitor->semester_id,
                                    'category' => 'elective',
                                ];

                                $newEntryElective = DroppedCourses::upsert($dataentryElective,$uniqueBy=['student_id','course_id'], $update=[
                                    'category'
                                ]);

                                if (!$newEntryElective) {
                                    abort(403, "Error Updating dropped Elective Courses, Contact ICT");
                                }

                            }

                            break;

                        default:
                            # code...
                            break;
                    }
                }

                //Dropped courses writing complete, forward back home with success message
                return redirect(route('student.registration.viewSingle', ['id'=>$monitor->id]))->with('info', "Temporary Registration Entered, Kindly Preview and Submit your registration to complete the process. NOTE: YOUR REGISTRATION IS NOT COMPLETE");


            }

            return redirect(route('home'))->with('error', "Error!!! There was a problem Intiating this registration, Please try again or contact ICT");


            //Initial Course Reg Complete
        }

        abort(403, "Error!!! No Appropriate Config found for Registration, contact ICT");

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //check if the candidate has registered before and redirect them back to course reg page;

        if (checkCourseRegDuplicate($id, user()->id)){

            return redirect(route('coursereg.index'))->with('error', "You have Registered for this item already");

        }else{



            $droppedcores = DroppedCourses::where(['student_id'=> getStudentIdByUserId(user()->id), 'category' =>'core', 'semester_id'=>getSemesterIdByName(activeSession()->currentSemester)])->get();

            $droppedelectives = DroppedCourses::where(['student_id'=> getStudentIdByUserId(user()->id), 'category' =>'elective', 'semester_id'=>getSemesterIdByName(activeSession()->currentSemester) ])->get();

            $carryOvers = RegMonitorItems::where(['student_id'=> getStudentIdByUserId(user()->id), 'semester_id'=>getSemesterIdByName(activeSession()->currentSemester)  ])->get();

            $outline = Curriculum::find($id);

            $cores = CurriculumItem::whereNotIn('semester_courses_id',$carryOvers->pluck('course_id'))->whereNotIn('semester_courses_id',$droppedcores->pluck('course_id'))->where(['curricula_id'=> $id, 'category' =>'core' ])->get();

            $electives = CurriculumItem::whereNotIn('semester_courses_id',$carryOvers->pluck('course_id'))->whereNotIn('semester_courses_id',$droppedelectives->pluck('course_id'))->where(['curricula_id'=> $id, 'category' =>'elective' ])->get();

            $optionals = CurriculumItem::whereNotIn('semester_courses_id',$carryOvers->pluck('course_id'))->where(['curricula_id'=> $id, 'category' =>'core/optional' ])->get();





            return view('students.retister', compact('cores','electives','optionals','droppedcores','droppedelectives','carryOvers','outline'));

        }


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showPrevious($id){

        if (user()->hasRole('student')) {

            //fetch all regMonitors

            $Monitors = RegMonitor::where(['student_id'=>getStudentIdByUserId(user()->id)])->get();

            //return count($Monitors);

            if (count($Monitors)>0) {

                return view('students.viewAllRegHistory', compact('Monitors'));
            }
            return redirect(route('coursereg.index'))->with('info',"You do not have any previous Registrations, Proceed to Current'Current Registration' to Initiate a fresh registration");


        }else{
            abort(403,"You do not have permission to view this page, Please Contact ICT");
        }



    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showConfirmedReg($id){

        if (user()->hasRole('student')) {

            //fetch all regMonitors

            $Monitors = RegMonitor::where(['student_id'=>getStudentIdByUserId(user()->id),'std_confirm'=>'1'])->with('RegMonitorItems')->find($id);

            //return $Monitors;

            if ($Monitors) {

                return view('students.printCourseReg', compact('Monitors'));
            }
            return redirect(route('coursereg.index'))->with('error',"Error 40322, Contact ICT");


        }else{
            abort(403,"You do not have permission to view this page, Please Contact ICT");
        }



    }

    public function printExamCard($id){

        if (user()->hasRole('student')) {

            #get Payment Monitor for this sesssion
            if ($submission = FeePayment::join('fee_configs as f','f.id','=','fee_payments.payment_config_id')
            ->join('fee_categories as c','c.id','=','f.fee_category_id')
            ->where('fee_payments.user_id', user()->id)
            ->where('fee_payments.academic_session_id', getActiveAcademicSessionId())
            ->where('c.payment_purpose_slug' ,'first-tuition')
            ->select('fee_payments.*')
            ->first()) {

            }elseif ($submission = FeePayment::join('fee_configs as f','f.id','=','fee_payments.payment_config_id')
            ->join('fee_categories as c','c.id','=','f.fee_category_id')
            ->where('fee_payments.user_id', user()->id)
            ->where('fee_payments.academic_session_id', getActiveAcademicSessionId())
            ->where('c.payment_purpose_slug' ,'tuition')
            ->select('fee_payments.*')
            ->first()) {

            }else{
                return redirect(route('coursereg.index'))->with('error',"Make Sure you complete your fees before you Print your Examination Card");
            }

            #for you to reach here it means there is a payment record, check against semester to know if to allow printing of examination card or not

            if (getActiveSemesterId()==1 && $submission->amount_paid > 0) {
                # all clear allow student to go
            }elseif (getActiveSemesterId()==2 && $submission->balance == 0) {
                # all clear allow student to go
            }else{
                return redirect(route('coursereg.index'))->with('error',"Make Sure you complete your Fee Payment before you Print your Examination Card");
            }


            //fetch all regMonitors

            $Monitors = RegMonitor::where(['student_id'=>getStudentIdByUserId(user()->id),'std_confirm'=>'1', 'uid'=>$id, 'status'=>'approved'])->with('RegMonitorItems')->first();

            //return $Monitors;

            if ($Monitors) {

                return view('students.printExamCard', compact('Monitors','submission'));
            }
            return redirect(route('coursereg.index'))->with('error',"Error 40322, Contact ICT");


        }else{
            abort(403,"You do not have permission to view this page, Please Contact ICT");
        }

    }

    public function verifyExamCard($id){
        if (user()->hasRole('staff')) {

            //fetch all regMonitors

            $Monitors = RegMonitor::where(['uid'=>$id, 'status'=>'approved'])->with('RegMonitorItems')->first();

            //return $Monitors;

            if ($Monitors) {

                return view('students.printExamCard', compact('Monitors'));
            }
            return redirect(route('dashboard'))->with('error',"Error 40322, Contact ICT");


        }else{
            abort(403,"You do not have permission to view this page, Please Contact ICT");
        }
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showSingleReg($id){



        if (user()->hasRole('student')) {

            //fetch all regMonitors

            //return "welcome to view Single";

            $Monitors = RegMonitor::where(['student_id'=>getStudentIdByUserId(user()->id), 'id'=>$id, 'std_confirm'=>'0'])->with('RegMonitorItems')->first();

            if (!$Monitors) {
                return redirect(route('home'))->with('error', "You Cannot make changes to this registration");
            }
            //next get the registration items
            $items = RegMonitorItems::where('monitor_id', $Monitors->id)->get()->pluck('course_id','id');

            //whereNotIn('course_id',$items)->
            $CorecarryOvers = RegMonitorItems::whereNotIn('course_id',$items)->where(['student_id'=>getStudentIdByUserId(user()->id), 'is_passed'=>'0','is_co_passed'=>'0','semester_id'=>$Monitors->semester_id,'category'=>'core'])->distinct()->get('course_id');

            $ElectivecarryOvers = RegMonitorItems::whereNotIn('course_id',$items)->where(['student_id'=>getStudentIdByUserId(user()->id), 'is_passed'=>'0','is_co_passed'=>'0','semester_id'=>$Monitors->semester_id,'category'=>'elective'])->distinct()->get('course_id');

            $droppedcores = DroppedCourses::whereNotIn('course_id',$items)->where(['student_id'=>getStudentIdByUserId(user()->id),'semester_id'=>$Monitors->semester_id,'category'=>'core'])->get();

            $droppedelectives = DroppedCourses::whereNotIn('course_id',$items)->where(['student_id'=>getStudentIdByUserId(user()->id),'semester_id'=>$Monitors->semester_id,'category'=>'elective'])->get();

            //return $Monitors;

            if ($Monitors) {

                return view('students.editReg', compact('Monitors','CorecarryOvers','ElectivecarryOvers','droppedcores','droppedelectives'));
            }
            return redirect(route('coursereg.index'))->with('error',"Registration Not found, Intiate fresh registration or contact ICT");


        }else{
            abort(403,"You do not have permission to view this page, Please Contact ICT");
        }



    }

    public function deRegisterCourse($id){

        if (user()->hasRole('student')) {

            $item = RegMonitorItems::where(['is_carryOver'=>0,'status'=>'pending', 'student_id'=>getStudentIdByUserId(user()->id)])->find($id);
            //return $item;
            // Check to see that removal doesn't violate minimum standards and is not pending before allowing
            if ($item) {

                $newBalance = getRegMonitorById($item->monitor_id,'totalcredits')- getCourseDetailsById($item->course_id,'credits');
                $minReqCredits = getCurriculaById(getRegMonitorById($item->monitor_id, 'curricula'), "mincredits");
                //return $newBalance."/".$minReqCredits;
                if ($newBalance < $minReqCredits) {
                    return redirect(route('student.registration.viewSingle',['id'=>$item->monitor_id]))->with('error', "Error!!! Minimum Required Credit Unit Limit reached");
                }else{
                    //All Clear insert into dropped courses and delete
                    $dataentryElective = [
                        'student_id' => $item->student_id,
                        'course_id' => $item->course_id,
                        'sesstion_id' => $item->session_id,
                        'semester_id'=> $item->semester_id,
                        'category' => $item->category,
                    ];

                    $newEntryElective = DroppedCourses::upsert($dataentryElective,$uniqueBy=['student_id','course_id'], $update=[
                        'category'
                    ]);

                    if (!$newEntryElective) {
                        abort(403, "Error Updating dropped Elective Courses, Contact ICT");
                    }

                    $item->delete();

                    return redirect(route('student.registration.viewSingle',['id'=>$item->monitor_id]))->with('info',"Course Removed Successfully !!!");
                }



            }
            return redirect(route('home'))->with('error',"You do not have Permission to view this page");


        }else{
            abort(403,"You do not have permission to view this page, Please Contact ICT");
        }



    }


    public function submitCourseReg($id){
        if (user()->hasRole('student')) {
            //find the object
            $toConfirm = RegMonitor::where(['student_id'=>getStudentIdByUserId(user()->id),'std_confirm'=>'0' ])->find($id);
            $toConfirm->std_confirm = '1';
            $toConfirm->save();

            return redirect(route('student.registration.viewAll',['id'=>user()->id]))->with('info',"Congratulations!!!! Course Registration Completed Successfully, NEXT: Contact your Registration Officer for Approvals");
        }else {
            return redirect(route('home'))->with('error', "You do not have permissio to view this page");
        }
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


    public function getsumCredits($courses){
        $cumm = 0;
        if ($courses) {
            foreach ($courses as $key => $v) {
                $sumOfCourses = SemesterCourse::find($v);
                $cumm+=$sumOfCourses->creditUnits;
            }
        }
        return $cumm;
    }


    public function checkRegClearance($v){


        $regCheck = RegClearance::where(['student_id'=>getStudentIdByUserId(user()->id), 'school_session_id' =>activeSession()->id,getSemesterDetailsById($v->semester).'_semester'=>1])->first();

        if ($regCheck) {
            return true;
        }else{
            return false;
        }
    }

    public function insertNewMonitor($id){

        $curriculas = Curriculum::find($id);

        if ($curriculas) {

            $details = [
                'student_id' => getStudentIdByUserId(user()->id),
                'semester_id'=> $curriculas->semester,
                'curricula_id' => $id,
                'session_id' => activeSession()->id,
                'level_id' => user()->current_level,
                'program_id' => getStudentByUserId(user()->id)->program_id,
                'semesters_spent' =>getStudentByUserId(user()->id)->semesters_spent+1,
                'uid' => uniqid('crf_'),

            ];

            $newMonitor = RegMonitor::upsert($details, $uniqueBy =['student_id','curricula_id','session_id'], $update=[

                'semester_id',
                'session_id',

            ]);
            if ($newMonitor) {

                return $newMonitor;
            }
            else{
                return false;
            }

        }
        else{

            return false;

        }


    }

    public function registerCourse($monitor,$course,$category,$coStatus){
        $courseData = [
            'monitor_id' => $monitor->id,
            'student_id' => $monitor->student_id,
            'course_id' => $course,
            'session_id' => $monitor->session_id,
            'semester_id' => $monitor->semester_id,
            'category' => $category,
            'is_carryOver' => $coStatus,

        ];

        $register = RegMonitorItems::upsert($courseData, $uniqueBy=['monitor_id', 'student_id','course_id','session_id'], $update=[
            'category',
            'is_carryOver'

        ]);



        if ($register) {
            return true;
        }else{
            return false;
        }
        //find monitor
        //retisterCourse

    }

    public function writeDroppedCourses ($id,$student_id){

        $outlineItems = Curriculum::find($id);

        if ($outlineItems) {
            foreach ($outlineItems as $key => $v) {
                //search the courseReg and decide if to insert into dropped or not based on current courseReg

            }
        }

    }




    public function searchBulkRegistration(){
        #get all courses according to user
        $semCourses = getUserCurriculumCoursesDropdown(user()->id);
        #get affected programme
        $programs = getUserProgramsDropdown(user()->id);

        #load the page from here
        return view('admin.select-bulk-registration', compact('semCourses','programs'));

    }

    public function searchSingleRegistration(){
        #get all courses according to user
        $semCourses = getUserCurriculumCoursesDropdown(user()->id);
        #get affected programme
        $programs = getUserProgramsDropdown(user()->id);

        #load the page from here
        return view('admin.select-single-registration', compact('semCourses','programs'));

    }

    public function bulkRegistration(Request $request){
        #lets validate some stuff
        $validated = $request->validate([
            'c_prog' => 'required',
            'c_code' => 'required',
            'school_session' => 'required',
            'semester' => 'required',
            'study_level' => 'required',
            'cCategory' => 'required',
            'action' => 'required',
        ]);

        # Pass these entries to the job
        BulkRegistrationJob::dispatch($request->c_prog, $request->c_code, $request->school_session, $request->semester, $request->study_level, $request->cCategory, $request->action);

        //return $request;
        return back()->with('info', "Request Successfully submitted for processing");

    }

    public function singleRegistration(Request $request){
        #lets validate some stuff
        $validated = $request->validate([
            'c_std' => 'required',
            'c_code' => 'required',
            'school_session' => 'required',
            'semester' => 'required',
            'cCategory' => 'required',
            'action' => 'required',
        ]);


        $studentRecord = StudentRecord::where('matric', $request->c_std)->first();

        if ($studentRecord) {

            #get the regMonitor for this registration
            $allRegistrants = RegMonitor::where('student_id', $studentRecord->id)
                                        ->where('semester_id', $request->semester)
                                        ->where('session_id', $request->school_session)
                                        ->first();
            if ($allRegistrants) {
                #pass the findings to the registration job
                BulkSingleRegistrationJob::dispatch($allRegistrants->id, $request->c_code, $request->cCategory, $request->action);
                #return $request;
                return back()->with('info', "Request Successfully submitted for processing");

            }else{
                # No Registration Record found for this student for the selected session and semester
                return back()->with('error', "Error!!!!! The Selected Student has not registered courses for the selected Session and Semester");

            }

        }else{

            return back()->with('error', "Error!!!!! Student Record not found, Please try again.");
        }



    }

    public function initiateSingleVetoReg(){
        return view('admin.initiate-single-veto-registration');
    }

    public function effectSingleVetoReg(Request $request){
        //return $request;
        #first find the student
        $std = StudentRecord::where('matric', $request->d_std)->first();

        if ($std) {
            #std found proceed
            #set the variables
            $sessionId = $request->school_session;
            $semesterId = $request->semester;
            $studentId = $std->id;
            $time = now();

            VetoRegistrationJob::dispatch($sessionId, $semesterId, $studentId,$time);



        }else{

            return back()->with('error', "Student with matric No ". $request->d_std. " Not found");
        }

        return redirect(route('post.single.vetoreg'))->with('info','Single VetoReg Executed Successfully');
    }





}
