<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Applicant\StoreRefreeRequest;
use App\Models\UserReferee;
use App\Services\Profile\StoreRefereeService;
use Illuminate\Http\Request;

class RefereeController extends Controller
{
    public function addReferee()
    {
        return view('applicant.add_referee');
    }

    public function storeReferee(StoreRefreeRequest $request)
    {
        $validated = $request->validated();

        return (new StoreRefereeService($validated, user(), 'applicant'))->run();
    }

    public function viewApplicantReferred($uid)
    {
        $details = UserReferee::where('uid', $uid)->first();
        if($details->is_filled === 1) {
            abort('403', 'This information had already been submitted successfully');
        }elseif($details){
            return view('applicant.referee_form', compact('details'));
        }else{
            abort(403, 'This link does not exist or has expired');
        }

    }

    public function updateRefereeDetails(StoreRefreeRequest $request, $uid)
    {
        $validated = $request->validated();

        $validated['uid'] = $uid;

        $details = (new StoreRefereeService($validated, user(), 'referred'))->run();

        return view('applicant.referee_success', compact('details'));
    }


}
