<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileStoreRequest;
use App\Models\State;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use App\Services\Profile\StoreProfileService;

class ProfileController extends Controller
{
    public function contactDetails()
    {
        $contactDetails = UserProfile::where('user_id', user()->id)->first();
        return view('applicant.contact_details', compact( 'contactDetails'));
    }

    public function personalDetails()
    {
        $states = State::all();
        $contactDetails = UserProfile::where('user_id', user()->id)->first();
        return view('applicant.personal_details', compact('states', 'contactDetails'));
    }

    public function storeUserProfile(ProfileStoreRequest $request): \Illuminate\Http\RedirectResponse
    {
        (new StoreProfileService($request->validated(), user()))->run();
        return redirect()->back()->with(['success' => 'profile details updated successfully']);
    }

    public function applicantProfile(Request $request) {
        $uprofile = UserProfile::where('user_id', user()->id)->first();

        if ($uprofile) {
            #profile found just redirect

        }else{
            #create the profile before you forward
            $newprofile = UserProfile::updateOrCreate(['user_id'=> user()->id],[
                'user_id'=> user()->id,
            ]);
        }

        return view('applicant.profile_form');
    }

    public function storeApplicantBiodata(Request $request){

        $validated = $request->validate([
            'gender' => 'required',
            'dob' => 'required',
            'gsmnumber' => 'required',
            'marritalstatus' => 'required',
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:64101',
        ]);

        //get the file object and perform checks
        $file = $request->file('file');


        //Get the file size to and determine if we should proceed or not

        $picSize = $file->getSize();

        if ($picSize>='64101') {
            //return error because the picture is greater than the 60Kb

            return back()->with('error', "This Picture is too big, reduce it to 50kb");
        }

        //all set to go upload the passport to the server
        $path = $request->file('file')->store('storage/images/userPassports');

        //next find the user and update the parameters

        $profile = User::find(user()->id);

        $profile->passport = $path;
        $profile->phone_number = formatPhoneNumber($request->gsmnumber);
        $profile->save();

        # Next Search out the profile and update the date of birth
        $userProfile = UserProfile::where('user_id', user()->id)->first();

        $userProfile->gender = $request->gender;
        $userProfile->dob = $request->dob;
        $userProfile->marital_status = $request->marritalstatus;
        $userProfile->save();



        return back()->with('info', "Bio-Data Update Successfully");
    }

}
