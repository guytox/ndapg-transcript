<?php

namespace App\Http\Controllers;

use App\Exports\CourseRegistrantExport;
use App\Imports\LecturerGradeUploadImport;
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


            return back()->with('info', "grades uploaded successfully, check entries");
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
            $courseAll = CourseAllocationItems::join('course_allocation_monitors as m', 'm.id','=','course_allocation_items.allocation_id')
                                                ->select('course_allocation_items.*', 'm.session_id', 'm.semester_id')
                                                ->where(['course_allocation_items.uid'=>$request->id, 'course_allocation_items.staff_id'=>user()->id, 'course_allocation_items.can_grade'=>1, 'graded'=>1, 'grading_completed'=>2, 'submitted'=>2])->first();

            if ($courseAll->uid !='') {
                //collect every general thing that is required to fire the job here
            $staffId = $courseAll->staff_id;
            $gradeRight = $courseAll->can_grade;
            $gradeRight = $courseAll->can_grade;
            $confirmCa1 = $courseAll->cfm_ca1;
            $confirmCa2 = $courseAll->cfm_ca2;
            $confirmCa3 = $courseAll->cfm_ca3;
            $confirmCa4 = $courseAll->cfm_ca4;
            $confirmExam = $courseAll->cfm_exam;
            $semesterCourseId = $courseAll->course_id;




                //run the loop and create the job now;
                foreach ($request->student_id as $k => $v) {
                    $itemId = $v['id'];
                    $ca1 = floatval($v['ca1']);
                    $ca2 = floatval($v['ca2']);
                    $ca3 = floatval($v['ca3']);
                    $ca4 = floatval($v['ca4']);
                    $exam = floatval($v['exam']);
                    $monitorUid = $request->id;
                    $whatToGrade = $request->context;
                    $userId = user()->id;
                    $session_id = $courseAll->session_id;
                    $semester_id = $courseAll->semester_id;
                    $courseId = $courseAll->course_id;

                    //LecturerGradeUploadJob::dispatch($monitorUid, $itemId, $staffId, $userId,  $ca1, $ca2, $ca3, $ca4, $exam, $whatToGrade, $semesterCourseId);

                    //next forward the details to the job
                     $uData = '['.number_format(floatval($ca1),2).']'.'['.number_format(floatval($ca2),2).']'.'['.number_format(floatval($ca3),2).']'.'['.number_format(floatval($ca4),2).']'.'['.number_format(floatval($exam),2).']';


                    $oldentries = RegMonitorItems::where(['id'=> $itemId, 'status'=> 'approved'])->first();

                        if ($oldentries->id !='') {
                            $oldData = '['.convertToBoolean($oldentries->ca1).']'.'['.convertToBoolean($oldentries->ca2).']'.'['.convertToBoolean($oldentries->ca3).']'.'['.convertToBoolean($oldentries->ca4).']'.'['.convertToBoolean($oldentries->exam).']';

                            //return "old ".$oldData." New ". $uData;

                            if ($oldData!=$uData) {
                                //you have a difference, perform checks and proceed
                                //get the semester course
                                $semCourse = SemesterCourse::find($courseId);

                                //check that the uploaded scores do not exceed the limits for ca and exam
                                if ($ca1+$ca2+$ca3+$ca4<=$semCourse->max_ca && $exam<=$semCourse->max_exam) {

                                    if ($whatToGrade ==='8X34' && $ca1+floatval(convertToBoolean($oldentries->ca2))+floatval(convertToBoolean($oldentries->ca3))+floatval(convertToBoolean($oldentries->ca4))<=floatval($semCourse->max_ca)  && $oldentries->cfm_ca1==='0') {
                                        //prepare for logging
                                            $data =[
                                                'user_id' => $staffId,
                                                'changes' => "[CA1]",
                                                'old_values' => '['.convertToBoolean($oldentries->ca1).']',
                                                'new_values' => '['.number_format($ca1,2).']',
                                                'course_id' => $courseId,
                                                'session_id' => $session_id,
                                                'semester_id' => $semester_id,
                                                'student_id' => $oldentries->student_id,
                                            ];

                                            $logEntry = ResultAuditTrail::create($data);

                                            if ($logEntry) {
                                                //all clear, you may write and proceed
                                                $oldentries->ca1 = covertToInt($ca1);
                                                $oldentries->ltotal = covertToInt($ca1) + $oldentries->ca2 + $oldentries->ca3 + $oldentries->ca4 + $oldentries->exam;
                                                $oldentries->save();

                                                //next update the student grade from the grading system table




                                            }


                                    }elseif ($whatToGrade ==='8OE4' && $ca2+floatval(convertToBoolean($oldentries->ca1))+floatval(convertToBoolean($oldentries->ca3))+floatval(convertToBoolean($oldentries->ca4))<=floatval($semCourse->max_ca)  && $oldentries->cfm_ca2==='0') {

                                        //prepare for logging
                                        $data =[
                                            'user_id' => $staffId,
                                            'changes' => "[CA2]",
                                            'old_values' => '['.convertToBoolean($oldentries->ca2).']',
                                            'new_values' => '['.number_format($ca2,2).']',
                                            'course_id' => $courseId,
                                            'session_id' => $session_id,
                                            'semester_id' => $semester_id,
                                            'student_id' => $oldentries->student_id,
                                        ];

                                        $logEntry = ResultAuditTrail::create($data);

                                        if ($logEntry) {
                                            //all clear, you may write and proceed
                                            $oldentries->ca2 = covertToInt($ca2);
                                            $oldentries->ltotal = $oldentries->ca1 + $oldentries->ca3 + $oldentries->ca4 + $oldentries->exam + covertToInt($ca2);
                                            $oldentries->save();


                                        }



                                    }elseif ($whatToGrade ==='3XS4' && $ca3+floatval(convertToBoolean($oldentries->ca2))+floatval(convertToBoolean($oldentries->ca1))+floatval(convertToBoolean($oldentries->ca4))<=floatval($semCourse->max_ca)  && $oldentries->cfm_ca3==='0') {

                                        //prepare for logging
                                        $data =[
                                            'user_id' => $staffId,
                                            'changes' => "[CA3]",
                                            'old_values' => '['.convertToBoolean($oldentries->ca3).']',
                                            'new_values' => '['.number_format($ca3,2).']',
                                            'course_id' => $courseId,
                                            'session_id' => $session_id,
                                            'semester_id' => $semester_id,
                                            'student_id' => $oldentries->student_id,
                                        ];

                                        $logEntry = ResultAuditTrail::create($data);

                                        if ($logEntry) {
                                            //all clear, you may write and proceed
                                            $oldentries->ca3 = covertToInt($ca3);
                                            $oldentries->ltotal = $oldentries->ca1 + $oldentries->ca2 + $oldentries->ca4 + $oldentries->exam + covertToInt($ca3);
                                            $oldentries->save();


                                        }



                                    }elseif ($whatToGrade ==='3x34' && $ca4+floatval(convertToBoolean($oldentries->ca2))+floatval(convertToBoolean($oldentries->ca3))+floatval(convertToBoolean($oldentries->ca1))<=floatval($semCourse->max_ca)  && $oldentries->cfm_ca4==='0') {

                                        //prepare for logging
                                        $data =[
                                            'user_id' => $staffId,
                                            'changes' => "[CA4]",
                                            'old_values' => '['.convertToBoolean($oldentries->ca4).']',
                                            'new_values' => '['.number_format($ca4,2).']',
                                            'course_id' => $courseId,
                                            'session_id' => $session_id,
                                            'semester_id' => $semester_id,
                                            'student_id' => $oldentries->student_id,
                                        ];

                                        $logEntry = ResultAuditTrail::create($data);

                                        if ($logEntry) {
                                            //all clear, you may write and proceed
                                            $oldentries->ca4 = covertToInt($ca4);
                                            $oldentries->ltotal = $oldentries->ca1 + $oldentries->ca2 + $oldentries->ca3  + $oldentries->exam + covertToInt($ca4);
                                            $oldentries->save();


                                        }



                                    }elseif ($whatToGrade ==='8X3X' && $exam<=floatval($semCourse->max_exam)  && $oldentries->cfm_exam==='0') {

                                        //prepare for logging
                                        $data =[
                                            'user_id' => $staffId,
                                            'changes' => "[EXAM]",
                                            'old_values' => '['.convertToBoolean($oldentries->exam).']',
                                            'new_values' => '['.number_format($exam,2).']',
                                            'course_id' => $courseId,
                                            'session_id' => $session_id,
                                            'semester_id' => $semester_id,
                                            'student_id' => $oldentries->student_id,
                                        ];

                                        $logEntry = ResultAuditTrail::create($data);

                                        if ($logEntry) {
                                            //all clear, you may write and proceed
                                            $oldentries->exam = covertToInt($exam);
                                            $oldentries->ltotal = $oldentries->ca1 + $oldentries->ca2 + $oldentries->ca3 + $oldentries->ca4 + covertToInt($exam);
                                            $oldentries->save();


                                        }



                                    }elseif ($whatToGrade ==='3XE8' && $ca1+$ca2+$ca3+$ca4+$exam <=floatval($semCourse->max_ca)+floatval($semCourse->max_exam) && $oldentries->cfm_ca1==='0' && $oldentries->cfm_ca2==='0' && $oldentries->cfm_ca3==='0' && $oldentries->cfm_ca4==='0' && $oldentries->cfm_exam==='0') {

                                        //prepare for logging
                                        $data =[
                                            'user_id' => $staffId,
                                            'changes' => "[ALL]",
                                            'old_values' => $oldData,
                                            'new_values' => $uData,
                                            'course_id' => $courseId,
                                            'session_id' => $session_id,
                                            'semester_id' => $semester_id,
                                            'student_id' => $oldentries->student_id,
                                        ];

                                        $logEntry = ResultAuditTrail::create($data);

                                        if ($logEntry) {
                                            //all clear, you may write and proceed
                                            $oldentries->ca1 = covertToInt($ca1);
                                            $oldentries->ca2 = covertToInt($ca2);
                                            $oldentries->ca3 = covertToInt($ca3);
                                            $oldentries->ca4 = covertToInt($ca4);
                                            $oldentries->exam = covertToInt($exam);
                                            $oldentries->ltotal = covertToInt($ca1) + covertToInt($ca2) + covertToInt($ca3) + covertToInt($ca4) + covertToInt($exam);
                                            $oldentries->save();


                                        }



                                    }

                                    $grade = GradingSystemItems::join('grading_systems as g','g.id','=','grading_system_items.grading_system_id')
                                                                            ->join('student_records as r','r.grading_system_id','=','g.id')
                                                                            ->join('reg_monitor_items as m','m.student_id','=','r.id')
                                                                            ->where('m.id',$oldentries->id)
                                                                            //->whereBetween('m.ltotal',['grading_system_items.lower_boundary', 'grading_system_items.upper_boundary'])
                                                                            ->select('m.ltotal','grading_system_items.*')
                                                                            ->get();
                                        foreach ($grade as $key => $v) {
                                            if ($v->ltotal >= $v->lower_boundary && $v->ltotal <= $v->upper_boundary) {

                                                $gradeLetter = $v->grade_letter;

                                                $oldentries->lgrade = $gradeLetter;
                                                $oldentries->save();
                                            }
                                        }

                                        LecturerSemesterCourseGradingJob::dispatch($oldentries->id);


                                    //return back()->with('success', "Records updated Successfully");
                                }

                                //return back()->with('error', "limits are not kept, return back");
                            }

                            //return "Old values are equal to new values";
                        }

                    //return $request;

                    //return $oldentries = RegMonitorItems::where(['id'=> $itemId, 'status'=> 'approved'])->first();


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
                                            ->select('course_allocation_items.*', 'm.uid as monitor_uid', 'm.id as monitor_id', 'm.session_id', 'm.semester_id')
                                            ->get();

            $courses;

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

                    return back()->with('success',"HOD Rejecttion registered Successfully !!!!");

                }elseif($request->action === 'approve'){

                    $course->accepted = 1;
                    $course->accepted_by = user()->id;
                    $course->accepted_at = now();
                    $course->grading_completed = 1;

                    $course->save();

                    return back()->with('success',"HOD Accept/Reject registered Successfully !!!!");

                }

            }

            return back()->with('error',"There was a problem with the reversal");

        }else{

            return back()->with('error',"You cannot proceed with grade submission because of allocation constraint, Contact HOD");
        }
    }

    public function deanDeConfirmGrades(Request $request, $as){
        return $request;
    }



}
