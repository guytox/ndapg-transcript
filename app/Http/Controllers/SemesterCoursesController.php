<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\SemesterCourse;
use Illuminate\Http\Request;

class SemesterCoursesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (user()->hasRole('admin')) {

            //Enter you code here

            //get a list of all faculties ready to return to the page, fetch everything

            //$faculties = Faculty::all()->pluck('facultyDescription', 'facultyName', 'facultyDescription','id');
            $courses = SemesterCourse::join('departments', 'departments.id', '=', 'semester_courses.department_id')
                                        ->select('semester_courses.id as id', 'semester_courses.courseCode as courseCode', 'semester_courses.courseTitle as courseTitle', 'semester_courses.courseDescription', 'semester_courses.activeStatus as activeStatus','semester_courses.creditUnits as creditUnits', 'semester_courses.department_id as department_id', 'semester_courses.max_ca as max_ca', 'semester_courses.max_exam', 'departments.name as deptName')
                                        ->orderBy('departments.name','asc')
                                        ->orderBy('courseCode','asc')
                                        ->get();

            //return $departments;

            $departments = Department::where('academic','=','1')->get();
            $departments = $departments->pluck('name','id');

            //return $departments.$faculties;



            return view('admin.configs.viewSemesterCourses', compact('courses','departments'));


        }


    return "<br> You do not have the required permission to visit this page";
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
        if (user()->hasRole('admin')) {

            $this->validate($request, [
                '_token'=>'required',
                'department_id'=>'required',
                'courseCode'=>['required','max:6'],
                'courseTitle'=>'required',
                'creditUnits'=>['required','integer'],
                'courseDescription'=>'required',
            ]);

            SemesterCourse::upsert($request->except('_token','uid'), $uniqueBy ='id', $update=[
                'department_id',
                'courseCode',
                'courseTitle',
                'creditUnits',
                'courseDescription',
                'max_ca',
                'max_exam'
            ]);

            return redirect(route('semestercourses.index'));

            return "You are good to go, we are in store";


        }


    return "<br> You do not have the required permission to visit this page";

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
        if (user()->hasRole('admin')) {

            $semestercourse = SemesterCourse::find($id);

            if ($_GET['action']==='Activate') {
                $semestercourse->activeStatus = 1;
            }elseif ($_GET['action']==='Deactivate') {
                $semestercourse = SemesterCourse::find($id);
                $semestercourse->activeStatus = 0;

            }
            $semestercourse->save();
            return redirect(route('semestercourses.index'));
        }
        return "You cannot edit the field";
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
        if (user()->hasRole('admin')) {

            //return "welcome to update";

            $this->validate($request, [

                '_token'=>'required',
                'department_id'=>'required',
                'courseCode'=>['required','max:6'],
                'courseTitle'=>'required',
                'creditUnits'=>'required',
                'courseDescription'=>'required',

            ]);

            SemesterCourse::upsert($request->except('_token','uid', '_method'), $uniqueBy ='id', $update=[

                'department_id',
                'courseCode',
                'courseTitle',
                'creditUnits',
                'courseDescription',
                'max_ca',
                'max_exam'
            ]);

            return redirect(route('semestercourses.index'));

            return "You are good to go, we are in store";


        }


    return "<br> You do not have the required permission to visit this page";
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (user()->hasRole('admin')) {

            //return $id;
            //Enter you code here
            $toDelete = SemesterCourse::findOrFail($id);

            try {

                $toDelete->deleteOrFail();

                return redirect(route('semestercourses.index'));

            } catch (\Illuminate\Database\QueryException $e) {

                return redirect(route('semestercourses.index'))->with('error',"Error!!! Semester Course could not be deleted");
            }



            return "You are good to go and destroy, This is dangerous so you are not allowed, Call me";


        }
    return "<br> You do not have the required permission to visit this page";
    }
}
