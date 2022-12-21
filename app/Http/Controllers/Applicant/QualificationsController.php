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


}
