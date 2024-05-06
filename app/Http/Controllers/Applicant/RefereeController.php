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
        $referees = UserReferee::where('user_id', user()->id)->get();
        if ($referees) {

            return view('applicant.add_referee', compact('referees'));
        }
        return view('applicant.add_referee');
    }

    public function storeReferee(StoreRefreeRequest $request)
    {

        $validated = $request->validated();

        $trefs = (UserReferee::where('user_id', user()->id)->get());

        if ($trefs) {
            if (count($trefs)>=3) {

                return back()->with('error', "Info !!! You have up to three(3) nominees already, Delete some if you can.!!!");

            }
        }

        return (new StoreRefereeService($validated, user(), 'applicant'))->run();
    }

    public function viewApplicantReferred($uid)
    {
        $details = UserReferee::where('uid', $uid)->first();
        if($details->is_filled === 1) {
            abort(403, 'This information had already been submitted successfully');
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

    public function deleteNominatedReferee($uid){
        $toDelete = UserReferee::where('uid', $uid)->first();

        if ($toDelete) {
            # check to see if this form has been filled and return error!!!

            if ($toDelete->is_filled==1) {
                $message = "Error!!! Referee cannot be deleted, This report has been submitted already";
            }else{
                $toDelete->delete();
                $message = "Referee record deleted Successfully !!!";
            }


        }else{
            $message = "Error!!!  No Referee Record found ";
        }

        return back()->with('info',$message);

    }


}
