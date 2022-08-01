<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Program;
use App\Models\Programme;
use App\Models\StudyLevel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgrammeController extends Controller
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
                $programmes = Program::join('departments', 'departments.id','=', 'programs.department_id')
                                        ->join('faculties','faculties.id','=', 'departments.faculty_id')
                                        ->join('study_levels', 'study_levels.id', '=', 'level_id')
                                        ->select('programs.id as progId','programs.degree_title as degreeTitle', 'programs.category as category' ,'programs.name as progName', 'programs.description as progDescription','departments.name as deptName', 'department_id as department_id', 'departments.faculty_id as faculties_id', 'faculties.name as facultyName', 'study_levels.id as level_id', 'study_levels.level as levelName')
                                        ->get();

                //return $programmes;

                $department = Department::all();
                $levels = StudyLevel::all()->pluck('level','id');
                $departments = $department->pluck('name','id');

                //return $departments.$faculties;



                return view('admin.configs.viewProgrammes', compact('departments','programmes','levels'));


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
        auth()->logout();
        return redirect(route('login'));
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
                    'name'=>'required',
                    'description'=>'required',
                    'uid'=>'required',
                    'category' => 'required'

                ]);

                Program::upsert($request->except('_token'), $uniqueBy ='id', $update=[

                    'department_id',
                    'name',
                    'description',
                    'category'
                ]);

                return redirect(route('programs.index'));

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

        auth()->logout();
        return redirect(route('login'));



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


        auth()->logout();
        return redirect(route('login'));


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
            if (user()->hasRole('admin')) {

                //Enter you code here

                $this->validate($request, [

                    'department_id' => 'required',
                    'name' => 'required',
                    'description' => 'required',
                    'category' => 'required'

                ]);

                //return $request;

                // Prepare fields for update
                // Check if record exist for this member, then update, else
                Program::upsert($request->except('_token', '_method'), $uniqueBy ='id', $update=[

                    'department_id',
                    'name',
                    'description',
                    'category'
                ]);

                return redirect(route('programs.index'));


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

                $toDelete = Program::findOrFail($id);

                $toDelete->delete();

                //$toDelete->delete();

                return redirect(route('programs.index'));

                return "You are good to go and destroy, This is dangerous so you are not allowed, Call me";


            }

        return "<br> You do not have the required permission to visit this page";




    }
}
