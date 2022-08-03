<?php

namespace App\Http\Controllers;

use App\Models\CourseAllocationItems;
use App\Models\CourseAllocationMonitor;
use App\Models\CurriculumItem;
use App\Models\User;
use Illuminate\Http\Request;
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

    public function showScoreSheet($as){
        if (user()->hasRole('lecturer') && $as =='ortesenKwagh') {

            return "write code for scoresheet here";

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

        return $request;

        return back()->with('info', "This module is not ready yet");
    }


    public function uploadGrades(Request $request, $as){
        return $request;
    }

    public function manualUploadofGrades($as, $id){
        return "we are ready for manual uplaod, write the codes here";
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
