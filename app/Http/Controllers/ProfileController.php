<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index(Request $request) {
        return view('admin.profile-update-form');
    }

    public function updateProfile(Request $request){
        $validated = $request->validate([
            'useremail' => 'required|email',
            'gsmnumber' => 'required',
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
        $profile->email = $request->useremail;
        $profile->phone_number = formatPhoneNumber($request->gsmnumber);
        $profile->email_verified_at = null;
        $profile->save();

        return back()->with('info', "Profile Updated Successfully");
    }
}
