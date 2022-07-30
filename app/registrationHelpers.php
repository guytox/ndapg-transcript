<?php

use App\Models\Curriculum;
use App\Models\CurriculumItem;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\FeePayment;
use App\Models\MatricConfiguration;
use App\Models\Program;
use App\Models\RegMonitor;
use App\Models\Semester;
use App\Models\SemesterCourse;
use App\Models\StudentRecord;
use App\Models\StudyLevel;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

function getRegMonitorById($id,$param){
    $value = RegMonitor::find($id);

    if ($value) {
        switch ($param) {
            case 'id':
                return $value->id;
                break;

            case 'student':
                return $value->student_id;
                break;

            case 'level':
                return $value->level_id;
                break;

            case 'program':
                return $value->program_id;
                break;

            case 'semester':
                return $value->semester_id;
                break;

            case 'smestersspent':
                return $value->semesters_spent;
                break;

            case 'curricula':
                return $value->curricula_id;
                break;

            case 'schoolsession':
                return $value->session_id;
                break;

            case 'numberofcourses':
                return $value->num_of_courses;
                break;

            case 'totalcredits':
                return $value->total_credits;
                break;

            case 'stdconfirmation':
                return $value->std_confirm;
                break;

            case 'status':
                return $value->status;
                break;

            case 'rostatus':
                return $value->ro_approval;
                break;

            case 'roid':
                return $value->ro_approver;
                break;

            case 'rodate':
                return $value->ro_approvalDate;
                break;

            case 'hodstatus':
                return $value->hod_approval;
                break;

            case 'hodid':
                return $value->hod_approver;
                break;

            case 'hoddate':
                return $value->hod_approvalDate;
                break;

            case 'deanstatus':
                return $value->dean_approval;
                break;

            case 'deanid':
                return $value->dean_approver;
                break;

            case 'deandate':
                return $value->dean_approvalDate;
                break;

            default:
                return "N/A";
                break;
        }
    }
        return false;

}



function getCurriculaById($id,$param){

    $value = Curriculum::find($id);

    if ($value) {
        switch ($param) {
            case 'id':
                return $value->id;
                break;

            case 'name':
                return $value->title;
                break;

            case 'level':
                return $value->studyLevel;
                break;

            case 'program':
                return $value->programs_id;
                break;

            case 'semester':
                return $value->semester;
                break;

            case 'mincredits':
                return $value->minRegCredits;
                break;

            case 'maxcredits':
                return $value->maxRegCredits;
                break;

            case 'numberofcourses':
                return $value->numOfCourses;
                break;

            case 'status':
                return $value->active;
                break;

            default:
                return "N/A";
                break;
        }
    }

    return false;

}


function getRegStudents($userId,$jurisdiction, $status ){

    $pendingRegs = RegMonitor::join('programs','programs.id','=','reg_monitors.program_id')
                                    ->join('departments','departments.id','=','programs.department_id')
                                    ->whereIn('departments.id', $jurisdiction)
                                    ->where('reg_monitors.status','=', $status)
                                    ->select('reg_monitors.*')
                                    ->get();

    return $pendingRegs;

}

function getRoleIdByRoleName($name){
    $roleFind = Role::where('name',$name)->first();
    $roleId = $roleFind->id;
    return $roleId;
}



function getAcademicDepts($id, $role){

    $user = User::find($id);

    if ($user->hasRole('vc')) {
        $dept = Department::all()->pluck('name','id');

        return $dept;
    }


    if ($user->hasRole('dean') && $role =='ityoughKiVesen') {

        $dept = Department::join('faculties', 'faculties.id', '=', 'departments.faculty_id')
                        ->where('faculties.academic', 1)
                        ->Where('faculties.dean_id', $id)
                        ->select('departments.*')
                        ->get()->pluck('id');
        return $dept;


    }

    if ($user->hasRole('hod') && $role =='ityoughKiChukur') {

        $dept = Department::join('faculties', 'faculties.id', '=', 'departments.faculty_id')
                        ->where('faculties.academic', 1)
                        ->where('departments.hod_id',$id)
                        ->select('departments.*')
                        ->get()->pluck('id');
        return $dept;
    }

    if ($user->hasRole('reg_officer') && $role =='ityoughKiNgeren') {

        $dept = Department::join('faculties', 'faculties.id', '=', 'departments.faculty_id')
                        ->where('faculties.academic', 1)
                        ->where('departments.registration_officer_id',$id)
                        ->select('departments.*')
                        ->get()->pluck('id');
        return $dept;

    }

    if ($user->hasRole('exam_officer') && $role =='ityoughKiKyaren') {

        $dept = Department::join('faculties', 'faculties.id', '=', 'departments.faculty_id')
                        ->where('faculties.academic', 1)
                        ->where('departments.exam_officer_id',$id)
                        ->select('departments.*')
                        ->get()->pluck('id');
        return $dept;


    }

    return [];
}



function getUserDepartmentsDropdown($id){

    $user = User::find($id);

    if ($user->hasRole('vc')) {
        $dept = Department::all()->pluck('name','id');

        return $dept;
    }

    if ($user->hasRole('admin')) {
        $dept = Department::all()->pluck('name','id');

        return $dept;
    }

    if ($user->hasRole('dean')) {

        $dept = Department::join('faculties','faculties.id','=','departments.faculty_id')
                                ->where('faculties.academic',1)
                                ->where('faculties.dean_id', $id)
                                ->orWhere('departments.hod_id', $id)
                                ->orWhere('departments.registration_officer_id', $id)
                                ->orWhere('departments.exam_officer_id', $id)
                                ->select('departments.*')
                                ->get()
                                ->pluck('name','id');

        return $dept;
    }

    if ($user->hasRole('hod')) {
        $dept = Department::join('faculties','faculties.id','=','departments.faculty_id')
                                ->where('faculties.academic',1)
                                ->where('departments.hod_id', $id)
                                ->orWhere('departments.registration_officer_id', $id)
                                ->orWhere('departments.exam_officer_id', $id)
                                ->orWhere('faculties.dean_id', $id)
                                ->select('departments.*')
                                ->get()
                                ->pluck('name','id');

        return $dept;
    }

    if ($user->hasRole('reg_officer')) {
        $dept = Department::join('faculties','faculties.id','=','departments.faculty_id')
                                ->where('faculties.academic',1)
                                ->where('departments.registration_officer_id', $id)
                                ->orWhere('departments.hod_id', $id)
                                ->orWhere('departments.exam_officer_id', $id)
                                ->orWhere('faculties.dean_id', $id)
                                ->select('departments.*')
                                ->get()
                                ->pluck('name','id');

        return $dept;
    }

    if ($user->hasRole('exam_officer')) {
        $dept = Department::join('faculties','faculties.id','=','departments.faculty_id')
                                ->where('faculties.academic',1)
                                ->where('departments.exam_officer_id', $id)
                                ->orWhere('departments.registration_officer_id', $id)
                                ->orWhere('departments.hod_id', $id)
                                ->orWhere('departments.exam_officer_id', $id)
                                ->orWhere('faculties.dean_id', $id)
                                ->select('departments.*')
                                ->get()
                                ->pluck('name','id');

        return $dept;
    }





    return "N/A";
}



function getUserSemesterCoursesDropdown($id){

    $user = User::find($id);

    if ($user->hasRole('vc')) {
        $dept = SemesterCourse::where('activeStatus',1)->all()->pluck('courseCode','id');

        return $dept;
    }

    if ($user->hasRole('dean')) {

        $dept = SemesterCourse::join('departments', 'departments.id','=','semester_courses.department_id')
                                ->join('faculties','faculties.id','=','departments.faculty_id')
                                ->where('faculties.academic',1)
                                ->where('faculties.dean_id', $id)
                                ->orWhere('departments.hod_id', $id)
                                ->orWhere('departments.registration_officer_id', $id)
                                ->orWhere('departments.exam_officer_id', $id)
                                ->where('semester_courses.activeStatus',1)
                                ->select('semester_courses.*')
                                ->get()
                                ->pluck('courseCode','id');

        return $dept;
    }

    if ($user->hasRole('hod')) {
        $dept = SemesterCourse::join('departments', 'departments.id','=','semester_courses.department_id')
                                ->join('faculties','faculties.id','=','departments.faculty_id')
                                ->where('faculties.academic',1)
                                ->where('departments.hod_id', $id)
                                ->orWhere('departments.registration_officer_id', $id)
                                ->orWhere('departments.exam_officer_id', $id)
                                ->orWhere('faculties.dean_id', $id)
                                ->where('semester_courses.activeStatus',1)
                                ->select('semester_courses.*')
                                ->get()
                                ->pluck('courseCode','id');

        return $dept;
    }

    if ($user->hasRole('reg_officer')) {
        $dept = SemesterCourse::join('departments', 'departments.id','=','semester_courses.department_id')
                                ->join('faculties','faculties.id','=','departments.faculty_id')
                                ->where('faculties.academic',1)
                                ->where('departments.registration_officer_id', $id)
                                ->orWhere('departments.hod_id', $id)
                                ->orWhere('departments.exam_officer_id', $id)
                                ->orWhere('faculties.dean_id', $id)
                                ->where('semester_courses.activeStatus',1)
                                ->select('semester_courses.*')
                                ->get()
                                ->pluck('courseCode','id');

        return $dept;
    }

    if ($user->hasRole('exam_officer')) {
        $dept = SemesterCourse::join('departments', 'departments.id','=','semester_courses.department_id')
                                ->join('faculties','faculties.id','=','departments.faculty_id')
                                ->where('faculties.academic',1)
                                ->where('departments.exam_officer_id', $id)
                                ->orWhere('departments.registration_officer_id', $id)
                                ->orWhere('departments.hod_id', $id)
                                ->orWhere('faculties.dean_id', $id)
                                ->where('semester_courses.activeStatus',1)
                                ->select('semester_courses.*')
                                ->get()
                                ->pluck('courseCode','id');

        return $dept;
    }





    return "N/A";
}

function getAllocatonCourses($hodId){

    $userDepts = getAcademicDepts(user()->id);

    $courses = CurriculumItem::join('semester_courses','semester_courses.id','=','curriculum_items.semester_courses_id')
                            ->join('departments', 'departments.id','=','semester_courses.department_id')
                            ->join('faculties','faculties.id','=','departments.faculty_id')
                            ->where('faculties.academic',1)
                            ->where('departments.hod_id', $hodId)
                            ->where('semester_courses.activeStatus',1)
                            ->select('semester_courses.*')
                            ->get()
                            ->pluck('courseCode','id');

    return $courses;

}

