<?php

namespace App\Http\Controllers;

use App\Models\OlevelCard;
use App\Models\OlevelResult;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserReferee;
use Illuminate\Http\Request;

class AdmissionProcessingController extends Controller
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

    public function previewApplication($id){
        #get the user profile, everything is there
        $applicantProfile = UserProfile::where('user_id', $id)->first();

        # Applicant profile and checks

        if (!$applicantProfile) {
            return redirect(route('home'))->with('error'," You have to fill all sections of the form");
        }elseif ($applicantProfile->applicant_program =='') {
            # No Programme Selected
            return redirect(route('home'))->with('error'," You have to select a programme");

        }elseif ($applicantProfile->state_id =='') {
            # No State Selected
            return redirect(route('home'))->with('error'," You have to select your state of origin");

        }elseif ($applicantProfile->dob =='') {
            # No Date Of Birth Selected
            return redirect(route('home'))->with('error'," You have to select your Date of Birth");

        }elseif ($applicantProfile->gender =='') {
            # No Date Of Birth Selected
            return redirect(route('home'))->with('error'," You have to select your Gender");

        }elseif ($applicantProfile->marital_status =='') {
            # No Date Of Birth Selected
            return redirect(route('home'))->with('error'," You have to select your Marrital Status");

        }

        #next, check the olevel entries and verification cards

        $OlevelResults = OlevelResult::where('user_id', $id)->get();

        if (!$OlevelResults) {
            # No Oleve result found return candidate back
            return redirect(route('home'))->with('error'," You have not uploaded any O-Level Results, Pleas upload before you proceed");

        }elseif ($OlevelResults) {
            # find out if there are oLevel cards for each sitting
            foreach ($OlevelResults as $vt) {
                #fetch card
                $ecard = OlevelCard::where('user_id',$id)
                                    ->where('sitting', $vt->sitting)
                                    ->first();

                if (!$ecard) {
                    # card not found return back
                    return redirect(route('home'))->with('error'," You have to Upload additional OLevel Verification Card before you continue");
                }
            }
        }

        #next obtain the user and beging checks for passport gsm and email
        $applicantUser = User::find($id);

        #check for phone number
        if ($applicantUser) {
            if ($applicantUser->phone_number=='') {
                # phone number not found
                return redirect(route('home'))->with('error'," You have to provide your gsm number!!!");

            }elseif ($applicantUser->passport=='') {
                #passport not found
                return redirect(route('home'))->with('error'," You have to provide your passport!!!");
            }

        }else{
            return back()->with('error'," This is strange, but this user has not been found!!!");
        }

        #next fetch the Referee information and perform checks
        $userReferee = UserReferee::where('user_id',$id)->get();

        if (!$userReferee) {
            # code...
            return back()->with('error'," User Referee  has not been found!!!");

        }

        return "All clear to move";


    }


    public function submitApplication(){

        # this id is for the user who wants to submit an application, get the user parameters and submit an application for the user

    }
}
