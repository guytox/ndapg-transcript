<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Applicant\StoreOlevelCardRequest;
use App\Http\Requests\Applicant\StoreOlevelRequest;
use App\Http\Requests\ApplicantProgrammeRequest;
use App\Models\OlevelCard;
use App\Models\OlevelResult;
use App\Services\Profile\StoreApplicantProgrammeService;
use App\Services\Profile\StoreOlevelService;
use Illuminate\Http\Request;
use App\Models\Faculty;

class AcademicController extends Controller
{
    public function addCardView(){
        $cards = OlevelCard::where('user_id', user()->id)->get();
        $olevels = OlevelResult::where('user_id', user()->id)->orderBy('sitting', 'ASC')->get();

        return view('applicant.academics.add_card', compact('olevels', 'cards'));
    }

    public function addCardStore(StoreOlevelCardRequest $request){

        //return $request;

        $validated = $request->validated();

        (new StoreOlevelService($validated, user(), 'card'))->run();

        return redirect()->back()->with(['success' => 'card added successfully']);
    }

    public function addResultView(){

        return view('applicant.academics.add_result');
    }

    public function getDepartmentsFromFaculty($id)
    {
        $departments = \App\Models\Department::where('faculty_id', $id)->select('id', 'name')->get();

        return response()->json($departments, 200);
    }
    public function getProgrammeFromDepartment($id)
    {
        $departments = \App\Models\Program::where('department_id', $id)->where('is_advertised', '1')->select('id', 'name')->get();

        return response()->json($departments, 200);
    }

    public function viewResultSubmitted(){
        $olevels = OlevelResult::where('user_id', user()->id)->orderBy('sitting', 'ASC')->get();

        if($olevels->count() < 1) {
            return redirect()->route('applicant.add_result')->with(['error' => 'you have not added any result']);
        }
        return view('applicant.academics.view_result', compact('olevels'));
    }

    public function addResultStore(StoreOlevelRequest $request){
        $validated = $request->validated();

        (new StoreOlevelService($validated, user(), 'olevel'))->run();

        return redirect()->route('applicant.view_result')->with(['success' => 'result added successfully']);
    }

    public function viewApprovedProgrammes(){
        #get all approved programmes
        $approvedProgrammes = getAppliableProgrammeDropdown();

        $faculties = Faculty::select('id', 'name')->get();

        return view('applicant.programme_details', compact('approvedProgrammes', 'faculties'));
    }


    public function addProgrammeStore(ApplicantProgrammeRequest $request){


        $validated = $request->validated();

        //return $validated;

        (new StoreApplicantProgrammeService($validated, user()))->run();

        return redirect()->route('applicant.add_result')->with(['success' => 'program details added successfully, you may proceed to upload Olevel details now']);
    }


}
