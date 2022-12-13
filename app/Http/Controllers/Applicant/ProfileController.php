<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileStoreRequest;
use App\Models\State;
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
    
}
