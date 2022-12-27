<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Applicant\QualificationStoreRequest;
use App\Models\UserProfile;
use App\Models\UserQualification;
use App\Models\UserResearch;
use App\Services\Profile\StoreQualificationsService;
use Illuminate\Http\Request;

class QualificationsController extends Controller
{
    public function school()
    {
        $qualifications = UserQualification::where('type', 'school')->where('user_id', user()->id)->get();

        return view('applicant.qualifications.school', compact('qualifications'));

    }

    public function deleteQualification($id){

        $toDelete = UserQualification::where('uid',$id)->where('user_id', user()->id)->first();

        if ($toDelete) {
            $toDelete->delete();

            return back()->with('info', "Qualification Deleted Successfully!!!");

        }else{

            return back()->with('error', "Error!!! Could not delte, Pleas try again");
        }

    }

    public function professional()
    {
        $qualifications = UserQualification::where('type', 'professional')->where('user_id', user()->id)->get();
        return view('applicant.qualifications.professional', compact('qualifications'));
    }

    public function store(QualificationStoreRequest $request)
    {
        $validated = $request->validated();

        //get the file object and perform checks
        $file = $request->file('file');

        //Get the file size to and determine if we should proceed or not

        $picSize = $file->getSize();

        if ($picSize>='100101') {
            //return error because the picture is greater than the 60Kb
            return back()->with('error', "This Picture is too big, reduce it to 100kb");
        }
        //all set to go upload the passport to the server
        $path = $request->file('file')->store('storage/images/userCertificates');



        $newQualification = UserQualification::updateOrCreate(['user_id' => user()->id, 'certificate_type' => $request->certificate_type], [
            'certificate_type' => $request->certificate_type ?? user()->qualifications->certificate_type ?? null,
            'qualification_obtained' => $request->qualification_obtained ?? null,
            'year_obtained' => $request->year_obtained ?? user()->qualifications->year_obtained ?? null,
            'type' => $request->action ?? user()->qualifications->action ?? 'school',
            'class' => $request->class ?? null,
            'expiry_date' => $request->expiry_date ?? null,
            'certificate_no' => $request->certificate_no ?? null,
            'awarding_institution' => $request->awarding_institution ?? null,
            'user_id' => user()->id,
            'path' => $path,
        ]);

        //(new StoreQualificationsService($validated, user()))->run();

        return redirect()->back()->with(['success' => 'qualification saved successfully']);
    }

    public function nyscget()
    {
        $nyscDetails = UserProfile::where('user_id', user()->id)->first();

        return view('applicant.nysc_form', compact('nyscDetails'));

    }

    public function nyscStore(Request $request)
    {

        $this->validate($request,[
            'nysc' => 'required|in:yes,no',
            'file' => 'image|mimes:jpeg,png,jpg,gif,svg|max:60|required_if:nysc,yes',
        ]);

        //return $request;

        //next find the user profile and update the parameters

        $nyscDetails = UserProfile::where('user_id', user()->id)->first();

        $nyscDetails->nysc = $request->nysc;

        if ($request->nysc=='yes') {
            # there should be a file to upload, proceed with the manipulations

            //get the file object and perform checks
        $file = $request->file('file');


        //Get the file size to and determine if we should proceed or not

        $picSize = $file->getSize();

        if ($picSize>='64101') {
            //return error because the picture is greater than the 60Kb

            return back()->with('error', "This Picture is too big, reduce it to 50kb");
        }

        //all set to go upload the passport to the server
        $path = $request->file('file')->store('storage/images/userCertificates');

        $nyscDetails->nysc_path = $path;

        }

        $nyscDetails->save();

        return view('applicant.nysc_form', compact('nyscDetails'))->with('success', "NYSC Details uploaded successfully! ! !");

    }



    public function researchget()
    {
        $researchDetails = UserResearch::where('user_id', user()->id)->first();

        if ($researchDetails) {
            # code...
        }else{
            $researchDetails = UserResearch::updateOrCreate(['user_id'=>user()->id, 'session_id'=>getApplicationSession()],['user_id'=>user()->id, 'session_id'=>getApplicationSession()]);
        }

        return view('applicant.research_form', compact('researchDetails'));

    }

    public function researchStore(Request $request)
    {

        $this->validate($request,[
            'research' => 'required',
            'file' => 'mimes:pdf|max:200|required',
        ]);

        //return $request;

        //next find the user profile and update the parameters

        $researchDetails = UserResearch::where('user_id', user()->id)->first();



            # there should be a file to upload, proceed with the manipulations

            //get the file object and perform checks
        $file = $request->file('file');


        //Get the file size to and determine if we should proceed or not

        $picSize = $file->getSize();

        if ($picSize>='200101') {
            //return error because the picture is greater than the 60Kb

            return back()->with('error', "This file is too big, reduce it to 200kb");
        }

        //all set to go upload the passport to the server
        $path = $request->file('file')->store('storage/images/userResearchProposals');


        $researchDetails->summary = $request->research;
        $researchDetails->path = $path;

        $researchDetails->save();

        return view('applicant.research_form', compact('researchDetails'))->with('info', "Research Proposal Details uploaded successfully! ! !");

    }





}
