<?php

namespace App\Http\Controllers;

use App\Models\Curriculum;
use App\Models\CurriculumItem;
use App\Models\Program;
use App\Models\Semester;
use App\Models\SemesterCourse;
use App\Models\StudyLevel;
use Illuminate\Http\Request;

class curriculaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (user()->hasRole('admin|dean_pg')) {
            $programs = Program::orderBy('name')->get()->pluck('name', 'id');
            $semesters = Semester::all()->pluck('name', 'id');
            $studyLevels = StudyLevel::all()->pluck('level', 'id');

            $curricula = Curriculum::all();

            foreach ($curricula as $m ) {
                if (!getProgramNameById($m->programs_id) ) {
                    return $m;
                }
            }

            // return "all good";

            return view('admin.configs.viewCurricula', compact('programs', 'semesters', 'studyLevels', 'curricula'));
        }
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
                'programs_id'=>'required',
                'title'=>'required',
                'semester'=>'required',
                'uid'=>'required',
                'studyYear' => 'required',
                'studyLevel'=>'required',
                'minRegCredits'=>'required',
                'maxRegCredits'=>'required',

            ]);

            Curriculum::upsert($request->except('_token'), $uniqueBy ='id', $update=[

                'programs_id',
                'title',
                'semester',
                'studyLevel',
                'studyYear',
                'minRegCredits',
                'maxRegCredits',
                'active'
            ]);

            return redirect(route('curricula.index'));

            return "You are good to go, we are in store";


        }

    return "You do not have the required permission to visit this page";


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (user()->hasRole('admin|dean|hod|reg_officer|exam_officer')) {

            $curriculum = Curriculum::with('curriculumItems')->find($id);
            $courses = SemesterCourse::where('activeStatus', '=', 1)->get()->pluck('courseCode','id');

            //return $curriculum;

            return view('admin.configs.viewCurriculum', compact('curriculum', 'courses'));
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
        if (user()->hasRole('admin')) {

            $this->validate($request, [

                '_token'=>'required',
                'programs_id'=>'required',
                'title'=>'required',
                'semester'=>'required',
                'uid'=>'required',
                'studyLevel'=>'required',
                'minRegCredits'=>'required',
                'maxRegCredits'=>'required',


            ]);

            Curriculum::upsert($request->except('_token','_method'), $uniqueBy ='id', $update=[

                'programs_id',
                'title',
                'semester',
                'studyLevel',
                'minRegCredits',
                'maxRegCredits',
                'active'
            ]);

            return redirect(route('curricula.index'));

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
            $toDelete = Curriculum::findOrFail($id);

            try {

                $toDelete->deleteOrFail();

                return redirect(route('curricula.index'));

            } catch (\Illuminate\Database\QueryException $e) {

                return redirect(route('curricula.index'))->with('error',"Error!!! Curriculum could not be deleted");
            }



            return "You are good to go and destroy, This is dangerous so you are not allowed, Call me";


        }
    return back()->with('error', "You do not have the required permission to visit this page");
    }


    function getMyCurricula(){
        if (user()->hasRole('dean|hod|reg_officer|exam_officer')) {
            //return "ready to extract user curricula";
            $programs = Program::all()->pluck('name', 'id');
            $semesters = Semester::all()->pluck('name', 'id');
            $studyLevels = StudyLevel::all()->pluck('level', 'id');

            $curricula = Curriculum::join('programs as p', 'p.id','=','curricula.programs_id')
                                    ->join('departments as d', 'd.id','=','p.department_id')
                                    ->join('faculties as f','f.id','=','d.faculty_id')
                                    ->where('d.hod_id',user()->id)
                                    ->orWhere('d.exam_officer_id', user()->id)
                                    ->orWhere('d.registration_officer_id', user()->id)
                                    ->orWhere('f.dean_id', user()->id)
                                    ->select('curricula.*')
                                    ->get();

            //return $curricula;

            return view('admin.configs.viewCurricula', compact('programs', 'semesters', 'studyLevels', 'curricula'));
        }
    }

    function showMyCurricula(){
        if (user()->hasRole('dean|hod|reg_officer|exam_officer')) {
            return "ready to show curriculum";
        }
    }






}
