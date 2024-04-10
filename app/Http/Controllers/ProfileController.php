<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule as ValidationRule;

class ProfileController extends Controller
{
    public function index(Request $request) {
        return view('admin.profile-update-form');
    }

    public function updateProfile(Request $request){
        $validated = $request->validate([
            'SurName' => ['required','string'],
            'OtherNames' => ['required','string'],
            'Gender' => ['required','exists:genders,id'],
            'PhoneNumber' => ['required','numeric', 'min:11'],
            'DateOfBirth' => ['required', 'date'],
            'NdaNumber' => ['required_if:PgMatricNumber,null','unique:user_profiles,ndanumber','numeric','nullable'],
            'PgMatricNumber' => ['required_if:NdaNumber,null','unique:user_profiles,pgnumber', 'string','nullable'],
            'RegularCourseNumber' => [ValidationRule::requiredIf($request->NdaNumber !=null),'numeric','nullable', 'min:40'],
            'NdaService' => [ValidationRule::requiredIf($request->NdaNumber !=null), 'exists:nda_services,id','nullable'],
            'RegularGraduationYear' => [ValidationRule::requiredIf($request->NdaNumber !=null),'numeric', 'nullable', 'min:1995'],
            'RegularCommissionDate' => [ValidationRule::requiredIf($request->NdaNumber !=null),'numeric','nullable'],
            'PostgraduateAdmissionYear' => [ValidationRule::requiredIf($request->PgMatricNumber !=null),'numeric','nullable', 'min:1993'],
            'PostgraduateGraduationYear' => [ValidationRule::requiredIf($request->PgMatricNumber !=null),'numeric', 'nullable', 'min:1995'],
            'RegularAdmissionYear' => [ValidationRule::requiredIf($request->NdaNumber !=null),'numeric', 'nullable', 'min:1990'],
            // 'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:64101',
        ]);

        // return $request;

        //get the file object and perform checks
        // $file = $request->file('file');

        //Get the file size to and determine if we should proceed or not

        // $picSize = $file->getSize();

        // if ($picSize>='64101') {
        //     //return error because the picture is greater than the 60Kb

        //     return back()->with('error', "This Picture is too big, reduce it to 50kb");
        // }

        //all set to go upload the passport to the server
        // $path = $request->file('file')->store('storage/images/userPassports');

        //next find the user and update the parameters

        $profile = UserProfile::updateOrCreate([
            'user_id' => user()->id,
        ],[
            'user_id' => user()->id,
            'surname' => $validated['SurName'],
            'othernames' => $validated['OtherNames'],
            'gender' => $validated['Gender'],
            'dob' => $validated['DateOfBirth'],
            'ndanumber' => $validated['NdaNumber'],
            'pgnumber' => $validated['PgMatricNumber'],
            'regularcourse' => $validated['RegularCourseNumber'],
            'uggraduationyear' => $validated['RegularGraduationYear'],
            'commissiondate' => $validated['RegularCommissionDate'],
            'pgadmissionyear' => $validated['PostgraduateAdmissionYear'],
            'pggraduationyear' => $validated['PostgraduateGraduationYear'],
            'ugadmissionyear' => $validated['RegularAdmissionYear'],
        ]);

        $userUpdate = User::updateOrCreate([
            'id' => user()->id,
        ],[
            'phone_number' => $validated['PhoneNumber'],
        ]);



        return redirect(route('home'))->with('info', "Profile Updated Successfully");
    }

    public function emailindex(Request $request) {
        return view('admin.email-update-form');
    }

    public function updateemail(Request $request){
        $validated = $request->validate([
            'useremail' => ['required','email','unique:users,email'],
        ]);

        $profile = User::find(user()->id);

        $profile->email = $request->useremail;
        $profile->email_verified_at = null;
        $profile->save();

        return back()->with('info', "Email Updated Successfully");
    }
}
