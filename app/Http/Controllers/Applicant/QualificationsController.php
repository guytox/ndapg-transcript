<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Applicant\QualificationStoreRequest;
use App\Models\UserQualification;
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

        (new StoreQualificationsService($validated, user()))->run();

        return redirect()->back()->with(['success' => 'qualification saved successfully']);
    }


}
