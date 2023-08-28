<?php

namespace App\Http\Controllers;

use App\Models\ComputedResult;
use App\Models\RegMonitor;
use Illuminate\Http\Request;

class GraduationManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //
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

    public function getStudentsForRecommendation($id, $sem){
        #first check required roles
        if (user()->hasRole('admin|dap|acad_eo|dean|hod|exam_officer|reg_officer|vc')) {
            # user has requird roles
            # next get the computed result to find the students
            $cResult = ComputedResult::where('uid', $id)->first();

            $studyLevel = $cResult->study_level;
            $stdProgram =  $cResult->program_id;
            $schoolSession = $cResult->schoolsession_id;
            #correct the semester
            if ($sem=='3') {
                $stdSemester = '2';

            }else{

                $stdSemester = $cResult->semester_id;
            }

            # next get the registered students for this class
            //Next fetch all regMonitors that fit this with their results and show the result columns on the page for further processing
            $regStudents = RegMonitor::where('program_id', $stdProgram)
                                    ->where('session_id', $schoolSession)
                                    ->where('semester_id', $stdSemester)
                                    ->where('level_id', $studyLevel)
                                    ->get();

            if (count($regStudents)>0) {

                return view('results.viewRecommendableGraduantsList',compact('regStudents','studyLevel','stdProgram','schoolSession','stdSemester'));

            }else{

                return "error!!!!";

            }

        }
    }

    public function recommendGraduants(Request $request){
        if (user()->hasRole('exam_officer')) {
            
            return back()->with('success', "Graduants Recommended Successfully, Check Recommended Graduants Menu for List");
        }
    }
}
