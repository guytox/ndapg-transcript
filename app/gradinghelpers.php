<?php

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

    return $nairaFigure;
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

function getSemesterCourseById($course_id){
    $course = SemesterCourse::
                            join('departments as d','d.id','=','semester_courses.department_id')
                            ->join('faculties as f','f.id','=','d.faculty_id')
                            ->select('semester_courses.*', 'd.name as departmentName', 'f.id as faculty_id', 'f.name as facultyName')
                            ->where('semester_courses.id', $course_id)
                            ->first();

    return $course;
}




