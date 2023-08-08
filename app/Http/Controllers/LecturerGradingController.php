<?php

namespace App\Http\Controllers;

use App\Exports\CourseRegistrantExport;
use App\Imports\LecturerGradeUploadImport;
use App\Jobs\GradeAllJob;
use App\Jobs\GradeExamJob;
use App\Jobs\GradeFirstCaJob;
use App\Jobs\GradeFourthCaJob;
use App\Jobs\GradeSecondCaJob;
use App\Jobs\GradeThirdCaJob;
use App\Jobs\HodGradeApprovalJob;
use App\Jobs\LecturerGradeUploadJob;
use App\Jobs\LecturerSemesterCourseGradingJob;
use App\Models\CourseAllocationItems;
use App\Models\CourseAllocationMonitor;
use App\Models\CurriculumItem;
use App\Models\GradingSystem;
use App\Models\GradingSystemItems;
use App\Models\RegMonitorItems;
use App\Models\ResultAuditTrail;
use App\Models\SemesterCourse;
use App\Models\StudentRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Traits\HasRoles;

class LecturerGradingController extends Controller
{
    use HasRoles;

    public function showMyCourses($as){
        if (user()->hasRole('lecturer') && $as =='ortesenKwagh') {

            $session_id = activeSession()->id;
            $session_name = activeSession()->name;
            $semester = activeSession()->currentSemester;
            $semester_id = getSemesterIdByName($semester);


            $courses = CourseAllocationItems::join('course_allocation_monitors as m', 'm.id', '=', 'course_allocation_items.allocation_id')
                                            ->where(['staff_id'=>user()->id,
                                            'm.session_id' => $session_id,
                                            'm.semester_id' =>$semester_id ])
                                            ->select('course_allocation_items.*', 'm.uid as monitor_uid', 'm.id as monitor_id', 'm.session_id', 'm.semester_id')
                                            ->get();

            //return $courses;

            return view('lecturers.viewMyCourses', compact('courses','session_name', 'semester'));

        }
    }

    public function showMyPreviousCourses(Request $request, $as){

        if (user()->hasRole('lecturer') && $as =='ortesenKwagh') {

            //return $request;

            $session_id = $request->session_id;
            $session_name = activeSession()->name;
            $semester = activeSession()->currentSemester;
            $semester_id = $request->semester_id;


            $courses = CourseAllocationItems::join('course_allocation_monitors as m', 'm.id', '=', 'course_allocation_items.allocation_id')
                                            ->where(['staff_id'=>user()->id,
                                            'm.session_id' => $session_id,
                                            'm.semester_id' =>$semester_id ])
                                            ->select('course_allocation_items.*', 'm.uid as monitor_uid', 'm.id as monitor_id', 'm.session_id', 'm.semester_id')
                                            ->get();

            //return $courses;

            return view('lecturers.viewMyCourses', compact('courses','session_name', 'semester'));

        }
    }

    public function showScoreSheet($as,$id){
        if (user()->hasRole('lecturer') && $as =='ortesenKwagh') {

            //get the course allocation item
            // get the total No of registants grouped by departments
            //perform confirmation checks and pass the records to relevant views
            //
            //return $id;
           $course = CourseAllocationItems::join('course_allocation_monitors as m', 'm.id', '=', 'course_allocation_items.allocation_id')
                                            ->where(['course_allocation_items.uid' =>$id,
                                                        //'course_allocation_items.staff_id' => user()->id,
                                                        'course_allocation_items.can_grade' =>1
                                            ])
                                            ->select('course_allocation_items.*', 'm.session_id', 'm.semester_id')
                                            ->first();

            //return $course;

            $regs = RegMonitorItems::where(['course_id'=>$course->course_id, 'session_id'=>$course->session_id,'semester_id'=>$course->semester_id])->get();

            //return $regs;

            return view('lecturers.viewMyScoresheet', compact('course', 'regs'));

        }
    }


    public function startGrading($as,$id){
        if (user()->hasRole('lecturer') && $as =='ortesenKwagh') {
            $start = CourseAllocationItems::where('uid', $id)->first();
            if ($start->staff_id == user()->id) {

                $start->graded = '1';
                $start->save();

            return back()->with('success', "Grading Initiated Successfully");

            }else{

                return back()->with('error', "Error!!!, There was a problem initiating grading, try again or contact IcT");
            }



        }else{

            return back()->with('error', "Error!!!, There was a problem initiating grading, try again or contact IcT");
        }
    }


    public function downloadRegistrants(Request $request, $as, $id){

        //get the course allocation item
            // get the total No of registants grouped by departments
            //perform confirmation checks and pass the records to relevant views
            //

            //return $request;

            $course = CourseAllocationItems::join('course_allocation_monitors as m', 'm.id', '=', 'course_allocation_items.allocation_id')
                                            ->join('semester_courses as s','s.id','=','course_allocation_items.course_id')
                                            ->where(['course_allocation_items.uid' =>$id,
                                                        'course_allocation_items.staff_id' => user()->id,

                                            ])
                                            ->select('course_allocation_items.*', 'm.session_id', 'm.semester_id','s.courseCode')
                                            ->first();
            $filename = $course->courseCode."_registrants.xlsx";
            $sessionId = $course->session_id;
            $semesterId = $course->semester_id;
            $courseCode = $course->course_id;

            //return $filename;



            $regs = RegMonitorItems::join('student_records as r','r.id','=','reg_monitor_items.student_id')
                                            ->join('users as u','u.id','=','r.user_id')
                                            ->where(['course_id'=>$course->course_id, 'session_id'=>$course->session_id,'semester_id'=>$course->semester_id])
                                            ->select('r.matric','u.name','ca1','ca2','ca3','ca4','exam')
                                            ->get();


            return Excel::download(new CourseRegistrantExport($sessionId,$semesterId,$courseCode), $filename);

        return back()->with('info', "file successfully downloaded");
    }


    public function uploadGrades(Request $request, $as){

        if (user()->hasRole('lecturer') && $as=='ortesenKwagh') {

            //return "Kumator, Good Morning, It's an interesting interaction with you this morning and I'm enjoying it.";

            $validated = $request->validate([
                'file' => 'required|mimes:xlsx|max:3048',
                'id' =>'required',
                'grading' => 'required',
            ]);

            $grades = $request->file('file');

            //get details of the semester course allocation from the uid (power query!!!!
            $course = CourseAllocationItems::join('course_allocation_monitors as m', 'm.id','=','course_allocation_items.allocation_id')
                                            ->select('course_allocation_items.*', 'm.session_id', 'm.semester_id')
                                            ->where(['course_allocation_items.uid'=>$request->id, 'course_allocation_items.staff_id'=>user()->id, 'course_allocation_items.can_grade'=>1, 'graded'=>1, 'grading_completed'=>2, 'submitted'=>2])->first();
            if ($course->id !='') {
                //all set to call lecturerGradeImport class
                $courseId = $course->course_id;
                $sessionId = $course->session_id;
                $semesterId = $course->semester_id;

                Excel::import(new LecturerGradeUploadImport($request->id,$request->grading,$courseId,$sessionId,$semesterId,user()->id), $grades);


            }


            return back()->with('info', "grades uploaded successfully, check entries after two (2) Minutes");
        }
        return back()->with('error', "Error!!!! You do not have the required privileges to perform this task");
    }

    public function manualUploadofGrades($as, $id){

        if (user()->hasRole('lecturer') && $as =='ortesenKwagh') {
            //get the course allocation item
            // get the total No of registants grouped by departments
            //perform confirmation checks and pass the records to relevant views
            //

            $course = CourseAllocationItems::join('course_allocation_monitors as m', 'm.id', '=', 'course_allocation_items.allocation_id')
                                            ->where(['course_allocation_items.uid' =>$id,
                                                        'course_allocation_items.staff_id' => user()->id,
                                                        'course_allocation_items.can_grade' =>1
                                            ])
                                            ->select('course_allocation_items.*', 'm.session_id', 'm.semester_id')
                                            ->first();

            //return $course;

            $regs = RegMonitorItems::where(['course_id'=>$course->course_id, 'session_id'=>$course->session_id,'semester_id'=>$course->semester_id])->get();

            //return $regs;

            return view('lecturers.uploadMyScores', compact('course', 'regs'));
        }

    }

    public function uploadManualGrades(Request $request, $as){

        if (user()->hasRole('lecturer') && $as =='ortesenKwagh') {

            //return $request;

            //get details of the semester course allocation from the uid (power query!!!!)
            $courseAll = CourseAllocationItems::where(['course_allocation_items.uid'=>$request->id, 'course_allocation_items.staff_id'=>user()->id, 'course_allocation_items.can_grade'=>1, 'graded'=>1, 'grading_completed'=>2, 'submitted'=>2])->first();

            if ($courseAll->uid !='') {
                //collect every general thing that is required to fire the job here
            $staffId = $courseAll->staff_id;




            switch ($request->context) {
                case '8X34':
                    $grading = 'ca1';
                    break;
                case '8OE4':
                    $grading = 'ca2';
                    break;
                case '3XS4':
                    $grading = 'ca3';
                    break;
                case '3x34':
                    $grading = 'ca4';
                    break;
                case '8X3X':
                    $grading = 'exam';
                    break;
                case '3XE8':
                    $grading = 'all';
                    break;

                default:
                    # code...
                    break;
            }

            $gradeCourse = $courseAll->course_id;
            $gradeSession = $courseAll->courseAllocation->session_id;
            $gradeSemester = $courseAll->courseAllocation->semester_id;
            $gradeStaff = $courseAll->staff_id;
            $allocationUid = $courseAll->uid;




                //run the loop and create the job now;
                foreach ($request->student_id as $k => $v) {
                    $regItem = RegMonitorItems::find($v['id']);
                    $matric = $regItem->RegMonitor->student->matric;
                     $regItem->id;
                     $itemId = $v['id'];


                    if ($grading =='ca1') {
                        $ca1 = floatval($v['ca1']);
                        #Next fire the job
                        GradeFirstCaJob::dispatch($gradeCourse,$gradeSession,$gradeSemester,$gradeStaff, $grading, $allocationUid, $matric, $ca1);

                    }elseif ($grading =='ca2') {
                        $ca2 = floatval($v['ca2']);
                        #Next fire the job
                        GradeSecondCaJob::dispatch($gradeCourse,$gradeSession,$gradeSemester,$gradeStaff, $grading, $allocationUid, $matric, $ca2);

                    }elseif ($grading =='ca3') {
                        $ca3 = floatval($v['ca3']);
                        #Next fire the job
                        GradeThirdCaJob::dispatch($gradeCourse,$gradeSession,$gradeSemester,$gradeStaff, $grading, $allocationUid, $matric, $ca3);

                    }elseif ($grading =='ca4') {
                        $ca4 = floatval($v['ca4']);
                        #Next fire the job
                        GradeFourthCaJob::dispatch($gradeCourse,$gradeSession,$gradeSemester,$gradeStaff, $grading, $allocationUid, $matric, $ca4);

                    }elseif ($grading =='exam') {
                        $exam = floatval($v['exam']);
                        #Next fire the job
                        GradeExamJob::dispatch($gradeCourse,$gradeSession,$gradeSemester,$gradeStaff, $grading, $allocationUid, $matric, $exam);

                    }elseif ($grading =='all') {

                        $ca1 = floatval($v['ca1']);
                        $ca2 = floatval($v['ca2']);
                        $ca3 = floatval($v['ca3']);
                        $ca4 = floatval($v['ca4']);
                        $exam = floatval($v['exam']);
                        #Next fire the job
                        GradeAllJob::dispatch($gradeCourse,$gradeSession,$gradeSemester,$gradeStaff, $grading, $allocationUid, $matric, $ca1, $ca2, $ca3, $ca4 , $exam);
                    }



                }


                return back()->with('success', 'Grades Successfully Updated, preview to confirm');

            }

        }
        return back()->with('error', 'Error!!! Semester Course Search Error, Search Again');

    }




    public function gradeConfirmation(Request $request, $as){

        if (user()->hasRole('lecturer') && $as === 'ortesenKwagh') {

            $course = CourseAllocationItems::where('uid',$request->id)
                                            ->where('submitted','2')
                                            ->where('staff_id', user()->id)
                                            ->first();
            //return $request;

            if ($request->confirm ==='mlumun') {

                if ($request->grading =='ca1') {
                    $course->cfm_ca1 = 1;
                }elseif ($request->grading =='ca2') {
                    $course->cfm_ca2 = 1;
                }elseif ($request->grading =='ca3') {
                    $course->cfm_ca3 = 1;
                }elseif ($request->grading =='ca4') {
                    $course->cfm_ca4 = 1;
                }elseif ($request->grading =='exam') {
                    $course->cfm_exam = 1;
                }elseif ($request->grading =='all') {
                    $course->cfm_ca1 = 1;
                    $course->cfm_ca2 = 1;
                    $course->cfm_ca3 = 1;
                    $course->cfm_ca4 = 1;
                    $course->cfm_exam = 1;
                }

                //return $course;

                $course->save();

                return back()->with('success',"Grade submission Successful");
            }


            return back()->with('error',"There was a problem with the upgrade");

        }else{
            return back()->with('error',"You cannot confirm grade because of allocation constraint, Contact HOD");
        }
    }

    public function reverseGradeConfirmation(Request $request, $as){
        if (user()->hasRole('lecturer') && $as === 'ortesenKwagh') {

            $course = CourseAllocationItems::where('uid',$request->id)
                                            ->where('submitted','2')
                                            ->where('staff_id', user()->id)
                                            ->first();
            //return $request;

            if ($request->confirm ==='mvenda') {

                if ($request->grading =='ca1') {
                    $course->cfm_ca1 = 0;
                }elseif ($request->grading =='ca2') {
                    $course->cfm_ca2 = 0;
                }elseif ($request->grading =='ca3') {
                    $course->cfm_ca3 = 0;
                }elseif ($request->grading =='ca4') {
                    $course->cfm_ca4 = 0;
                }elseif ($request->grading =='exam') {
                    $course->cfm_exam = 0;
                }elseif ($request->grading =='all') {
                    $course->cfm_ca1 = 0;
                    $course->cfm_ca2 = 0;
                    $course->cfm_ca3 = 0;
                    $course->cfm_ca4 = 0;
                    $course->cfm_exam = 0;
                }

                //return $course;

                $course->save();

                return back()->with('success',"Grade Submission Reversal Successful");
            }


            return back()->with('error',"There was a problem with the reversal");

        }else{

            return back()->with('error',"You cannot reverse the grade confirmation because of allocation constraint, Contact HOD");
        }
    }

    public function submitGrades(Request $request, $as){

        if (user()->hasRole('lecturer') && $as === 'ortesenKwagh') {

            $course = CourseAllocationItems::where('uid',$request->id)
                                            ->where('submitted','2')
                                            ->where('staff_id', user()->id)
                                            ->first();

            if ($request->confirm ==='mna') {

                $course->submitted = 1;
                $course->submitted_by = user()->id;
                $course->submitted_at = now();
                $course->save();

                #ToDo: submit this entire course to be graded by a job before hod approval

                return back()->with('success',"Grade Submitted to HOD Successfully !!!!");
            }

            return back()->with('error',"There was a problem with the reversal");

        }else{

            return back()->with('error',"You cannot proceed with grade submission because of allocation constraint, Contact HOD");
        }
    }

    public function hodGradeHome($as){

        if (user()->hasRole('hod') && $as =='ityoughKiChukur') {

            //return $request;

            $session_id = activeSession()->id;
            $session_name = activeSession()->name;
            $semester = activeSession()->currentSemester;
            $semester_id = getSemesterIdByName($semester);

            $courses = CourseAllocationItems::join('course_allocation_monitors as m', 'm.id', '=', 'course_allocation_items.allocation_id')
                                            ->join('semester_courses', 'semester_courses.id','=','course_allocation_items.course_id')
                                            ->join('departments', 'departments.id','=','semester_courses.department_id')
                                            ->where('departments.hod_id',user()->id)
                                            ->where('course_allocation_items.can_grade', 1)
                                            ->where([
                                            'm.session_id' => $session_id,
                                            'm.semester_id' =>$semester_id ])
                                            ->orderBy('course_allocation_items.accepted', 'asc')
                                            ->orderBy('course_allocation_items.submitted', 'asc')
                                            ->orderBy('course_allocation_items.cfm_exam', 'desc')
                                            ->orderBy('course_allocation_items.cfm_ca4', 'desc')
                                            ->orderBy('course_allocation_items.cfm_ca3', 'desc')
                                            ->orderBy('course_allocation_items.cfm_ca2', 'desc')
                                            ->orderBy('course_allocation_items.cfm_ca1', 'desc')
                                            ->select('course_allocation_items.*', 'm.uid as monitor_uid', 'm.id as monitor_id', 'm.session_id', 'm.semester_id')
                                            ->get();


            return view('admin.view-graded-courses', compact('courses','session_name', 'semester'));

        }elseif (user()->hasRole('dean')) {

            $session_id = activeSession()->id;
            $session_name = activeSession()->name;
            $semester = activeSession()->currentSemester;
            $semester_id = getSemesterIdByName($semester);

            $courses = CourseAllocationItems::join('course_allocation_monitors as m', 'm.id', '=', 'course_allocation_items.allocation_id')
                                            ->join('semester_courses', 'semester_courses.id','=','course_allocation_items.course_id')
                                            ->join('departments', 'departments.id','=','semester_courses.department_id')
                                            ->join('faculties','faculties.id','=','departments.faculty_id')
                                            ->where('faculties.dean_id',user()->id)
                                            ->where('course_allocation_items.can_grade', 1)
                                            ->where([
                                            'm.session_id' => $session_id,
                                            'm.semester_id' =>$semester_id ])
                                            ->orderBy('course_allocation_items.accepted', 'asc')
                                            ->orderBy('course_allocation_items.submitted', 'asc')
                                            ->orderBy('course_allocation_items.cfm_exam', 'desc')
                                            ->orderBy('course_allocation_items.cfm_ca4', 'desc')
                                            ->orderBy('course_allocation_items.cfm_ca3', 'desc')
                                            ->orderBy('course_allocation_items.cfm_ca2', 'desc')
                                            ->orderBy('course_allocation_items.cfm_ca1', 'desc')
                                            ->select('course_allocation_items.*', 'm.uid as monitor_uid', 'm.id as monitor_id', 'm.session_id', 'm.semester_id')
                                            ->get();


            return view('admin.view-graded-courses', compact('courses','session_name', 'semester'));

        }elseif (user()->hasRole('dap|acad_eo|admin|dean_pg') && $as =='dap') {

            $session_id = activeSession()->id;
            $session_name = activeSession()->name;
            $semester = activeSession()->currentSemester;
            $semester_id = getSemesterIdByName($semester);

            $courses = CourseAllocationItems::join('course_allocation_monitors as m', 'm.id', '=', 'course_allocation_items.allocation_id')
                                            ->join('semester_courses', 'semester_courses.id','=','course_allocation_items.course_id')
                                            ->join('departments', 'departments.id','=','semester_courses.department_id')
                                            ->where('course_allocation_items.can_grade', 1)
                                            ->where([
                                            'm.session_id' => $session_id,
                                            'm.semester_id' =>$semester_id ])
                                            ->orderBy('course_allocation_items.accepted', 'asc')
                                            ->orderBy('course_allocation_items.submitted', 'asc')
                                            ->orderBy('course_allocation_items.cfm_exam', 'desc')
                                            ->orderBy('course_allocation_items.cfm_ca4', 'desc')
                                            ->orderBy('course_allocation_items.cfm_ca3', 'desc')
                                            ->orderBy('course_allocation_items.cfm_ca2', 'desc')
                                            ->orderBy('course_allocation_items.cfm_ca1', 'desc')
                                            ->select('course_allocation_items.*', 'm.uid as monitor_uid', 'm.id as monitor_id', 'm.session_id', 'm.semester_id')
                                            ->get();

            //$courses;

            return view('admin.view-graded-courses', compact('courses','session_name', 'semester'));
        }else {
            return back()->with('error','Error 40012 !!! Contact ICT');
        }

    }

    public function changeHodGradeHome(Request $request, $as){

        #determine query variables
        $session_id = $request->session_id;
        $session_name = getsessionById($session_id)->name;
        $semester = getSemesterNameById($request->semester_id);
        $semester_id = $request->semester_id;

        if (user()->hasRole('hod') ) {

            $courses = CourseAllocationItems::join('course_allocation_monitors as m', 'm.id', '=', 'course_allocation_items.allocation_id')
                                            ->join('semester_courses', 'semester_courses.id','=','course_allocation_items.course_id')
                                            ->join('departments', 'departments.id','=','semester_courses.department_id')
                                            ->where('departments.hod_id',user()->id)
                                            ->where('course_allocation_items.can_grade', 1)
                                            ->where([
                                            'm.session_id' => $session_id,
                                            'm.semester_id' =>$semester_id ])
                                            ->orderBy('course_allocation_items.accepted', 'asc')
                                            ->orderBy('course_allocation_items.submitted', 'asc')
                                            ->orderBy('course_allocation_items.cfm_exam', 'desc')
                                            ->orderBy('course_allocation_items.cfm_ca4', 'desc')
                                            ->orderBy('course_allocation_items.cfm_ca3', 'desc')
                                            ->orderBy('course_allocation_items.cfm_ca2', 'desc')
                                            ->orderBy('course_allocation_items.cfm_ca1', 'desc')
                                            ->select('course_allocation_items.*', 'm.uid as monitor_uid', 'm.id as monitor_id', 'm.session_id', 'm.semester_id')
                                            ->get();


            return view('admin.view-graded-courses', compact('courses','session_name', 'semester'));

        }elseif (user()->hasRole('dean')) {

            $courses = CourseAllocationItems::join('course_allocation_monitors as m', 'm.id', '=', 'course_allocation_items.allocation_id')
                                            ->join('semester_courses', 'semester_courses.id','=','course_allocation_items.course_id')
                                            ->join('departments', 'departments.id','=','semester_courses.department_id')
                                            ->join('faculties','faculties.id','=','departments.faculty_id')
                                            ->where('faculties.dean_id',user()->id)
                                            ->where('course_allocation_items.can_grade', 1)
                                            ->where([
                                            'm.session_id' => $session_id,
                                            'm.semester_id' =>$semester_id ])
                                            ->orderBy('course_allocation_items.accepted', 'asc')
                                            ->orderBy('course_allocation_items.submitted', 'asc')
                                            ->orderBy('course_allocation_items.cfm_exam', 'desc')
                                            ->orderBy('course_allocation_items.cfm_ca4', 'desc')
                                            ->orderBy('course_allocation_items.cfm_ca3', 'desc')
                                            ->orderBy('course_allocation_items.cfm_ca2', 'desc')
                                            ->orderBy('course_allocation_items.cfm_ca1', 'desc')
                                            ->select('course_allocation_items.*', 'm.uid as monitor_uid', 'm.id as monitor_id', 'm.session_id', 'm.semester_id')
                                            ->get();


            return view('admin.view-graded-courses', compact('courses','session_name', 'semester'));

        }elseif (user()->hasRole('dap|acad_eo|admin|dean_pg')) {

            $courses = CourseAllocationItems::join('course_allocation_monitors as m', 'm.id', '=', 'course_allocation_items.allocation_id')
                                            ->join('semester_courses', 'semester_courses.id','=','course_allocation_items.course_id')
                                            ->join('departments', 'departments.id','=','semester_courses.department_id')
                                            ->where('course_allocation_items.can_grade', 1)
                                            ->where([
                                            'm.session_id' => $session_id,
                                            'm.semester_id' =>$semester_id ])
                                            ->orderBy('course_allocation_items.accepted', 'asc')
                                            ->orderBy('course_allocation_items.submitted', 'asc')
                                            ->orderBy('course_allocation_items.cfm_exam', 'desc')
                                            ->orderBy('course_allocation_items.cfm_ca4', 'desc')
                                            ->orderBy('course_allocation_items.cfm_ca3', 'desc')
                                            ->orderBy('course_allocation_items.cfm_ca2', 'desc')
                                            ->orderBy('course_allocation_items.cfm_ca1', 'desc')
                                            ->select('course_allocation_items.*', 'm.uid as monitor_uid', 'm.id as monitor_id', 'm.session_id', 'm.semester_id')
                                            ->get();

            //$courses;

            return view('admin.view-graded-courses', compact('courses','session_name', 'semester'));

        }else {

            return back()->with('error','Error 40012 !!! Contact ICT');

        }

    }

    public function hodShowSelected(Request $request, $as){

        if (user()->hasRole('hod') && $as =='ityoughKiChukur') {

            //return $request;

            $session_id = $request->session_id;
            $session_name = activeSession()->name;
            $semester = activeSession()->currentSemester;
            $semester_id = $request->semester_id;

            $courses = CourseAllocationItems::join('course_allocation_monitors as m', 'm.id', '=', 'course_allocation_items.allocation_id')
                                            ->join('semester_courses as s', 's.id','=','course_allocation_items.course_id')
                                            ->join('departments', 'departments.id','=','s.department_id')
                                            ->where('departments.hod_id',user()->id)
                                            ->where([
                                            'm.session_id' => $session_id,
                                            'm.semester_id' =>$semester_id ])
                                            ->select('course_allocation_items.*', 'm.uid as monitor_uid', 'm.id as monitor_id', 'm.session_id', 'm.semester_id')
                                            ->get();

            //return $courses;

            return view('admin.view-graded-courses', compact('courses','session_name', 'semester'));

        }elseif (user()->hasRole('dap|admin')) {
            # code...
        }else {

            return back()->with('error','Error 40013 !!! Contact ICT');
        }

    }


    public function hodConfirmGrades(Request $request, $as){

        if (user()->hasRole('hod') && $as === 'ityoughKiChukur') {

            $course = CourseAllocationItems::join('course_allocation_monitors as m', 'm.id', '=', 'course_allocation_items.allocation_id')
                                            ->join('semester_courses as s', 's.id','=','course_allocation_items.course_id')
                                            ->join('departments', 'departments.id','=','s.department_id')
                                            ->where('departments.hod_id',user()->id)
                                            ->where('course_allocation_items.uid',$request->id)
                                            ->where('course_allocation_items.submitted','1')
                                            ->select('course_allocation_items.*')
                                            ->first();
            //return $course;

            if ($request->confirm ==='mlumun') {

                if ($request->action === 'disapprove') {

                    $course->submitted = 2;
                    $course->save();

                    return redirect(route('hod-confirm.index', ['as'=>'ityoughKiChukur']))->with('success',"HOD Rejecttion registered Successfully !!!!");

                }elseif($request->action === 'approve'){

                    $course->accepted = 1;
                    $course->accepted_by = user()->id;
                    $course->accepted_at = now();
                    $course->grading_completed = 1;

                    $course->save();

                    #send approval job here
                    HodGradeApprovalJob::dispatch($course->id);

                    return redirect(route('hod-confirm.index', ['as'=>'ityoughKiChukur']))->with('success',"HOD Accept/Reject registered Successfully !!!!");

                }

            }

            return redirect(route('hod-confirm.index', ['as'=>'ityoughKiChukur']))->with('error',"There was a problem with the reversal");

        }else{

            return redirect(route('hod-confirm.index', ['as'=>'ityoughKiChukur']))->with('error',"You cannot proceed with grade submission because of allocation constraint, Contact HOD");
        }
    }

    public function deanDeConfirmGrades(Request $request, $as){
        return $request;
    }



}
