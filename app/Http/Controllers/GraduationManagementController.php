<?php

namespace App\Http\Controllers;

use App\Jobs\SubmitGradRecommendationJob;
use App\Models\ComputedResult;
use App\Models\PendingGraduant;
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
                                    ->orderBy('cgpa', 'desc')
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

            $programId = $request->programme;
            $sessionId = $request->schsession;
            $semesterId = $request->schsemester;
            $studyLevel = $request->studylevel;
            $time = now();
            $staffId = user()->id;

            foreach ($request->regMonitor as $reg) {
                # submit recommendation job
                SubmitGradRecommendationJob::dispatch($programId, $sessionId, $semesterId, $studyLevel, $reg, $staffId, $time);
            }

            return back()->with('success', "Graduants Recommended Successfully, Check Recommended Graduants Menu for List");
        }
    }


    public function searchGraduantsForApproval(){
         # get user programmes
         $programs = getUserProgramsDropdown(user()->id);

         return view('results.search-recommended-graduants', compact('programs'));
    }



    public function getGraduantsForApproval(Request $request){
        #first check required roles
        if (user()->hasRole('admin|dap|acad_eo|dean_pg|dean|hod|exam_officer|reg_officer|vc')) {

            // return $request;

            #get the staff jurrisdiction
            $progIds = getUserProgramIds(user()->id);

            $schoolSession = $request->school_session;
            $stdSemester = $request->semester;

             $pendingGraduants = PendingGraduant::where('grad_session_id', $request->school_session)
                                                ->where('grad_semester_id', $request->semester)
                                                ->whereIn('program_id', $progIds)
                                                ->get();

            if (count($pendingGraduants)>0) {

                return view('results.viewRecommendedGraduants',compact('pendingGraduants','schoolSession','stdSemester'));

            }else{

                return redirect('home')->with('error', "No Graduants Found");

            }

        }
    }

}
