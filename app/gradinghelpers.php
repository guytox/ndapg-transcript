<?php

use App\Models\CourseAllocationItems;
use App\Models\GradingSystemItems;
use App\Models\Program;
use App\Models\SemesterCourse;
use App\Models\StudentRecord;
use App\Models\User;

function covertToInt($boolFigure){

    $koboFigure = $boolFigure * 100;

    return $koboFigure;
}


function convertToBoolean($intFigure){

    $nairaFigure = $intFigure/100;

    return number_format($nairaFigure,2);
}

function getUserByStudentID($student_id){
    $user = User::join('student_records as s', 's.user_id','=','users.id')
                ->where('s.id', $student_id)
                ->select('users.*')
                ->first();

    return $user;
}

function getStudentById($student_id){

    $student = StudentRecord::
                            join('programs as p','p.id','=','student_records.program_id')
                            ->join('departments as d','d.id','=','p.department_id')
                            ->join('faculties as f','f.id','=','d.faculty_id')
                            ->select('student_records.*','p.name as programName', 'd.id as department_id', 'd.name as departmentName', 'f.id as faculty_id', 'f.name as facultyName')
                            ->where('student_records.id', $student_id)
                            ->first();

    return $student;
}

function getStudentByMatric($matric){

    $student = StudentRecord::
                            join('programs as p','p.id','=','student_records.program_id')
                            ->join('departments as d','d.id','=','p.department_id')
                            ->join('faculties as f','f.id','=','d.faculty_id')
                            ->select('student_records.*','p.name as programName', 'd.id as department_id', 'd.name as departmentName', 'f.id as faculty_id', 'f.name as facultyName')
                            ->where('student_records.matric', $matric)
                            ->first();

    return $student;
}

function getSemesterCourseById($course_id){
    $course = SemesterCourse::
                            join('departments as d','d.id','=','semester_courses.department_id')
                            ->join('faculties as f','f.id','=','d.faculty_id')
                            ->select('semester_courses.*', 'd.name as departmentName', 'f.id as faculty_id', 'f.name as facultyName')
                            ->where('semester_courses.id', $course_id)
                            ->first();

    return $course;
}


function getCourseAllocationItemByUid($uid){
    $allocationItem = CourseAllocationItems::join('course_allocation_monitors as m', 'm.id','=','course_allocation_items.allocation_id')
                                            ->select('course_allocation_items.*', 'm.session_id', 'm.semester_id','m.uid as monitor_uid')
                                            ->where('course_allocation_items.uid', $uid)->first();

    return $allocationItem;
}

function getGradeLetter($totalScore, $gradingSystem){
    $gradingItems = GradingSystemItems::where('grading_system_id',$gradingSystem);
}




