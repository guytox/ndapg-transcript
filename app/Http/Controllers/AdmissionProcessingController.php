<?php

namespace App\Http\Controllers;

use App\Models\ApplicantAdmissionRequest;
use App\Models\FeePayment;
use App\Models\OlevelCard;
use App\Models\OlevelResult;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserQualification;
use App\Models\UserReferee;
use App\Models\UserResearch;
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
            //return redirect(route('home'))->with('error'," You have not uploaded any O-Level Results, Please upload before you proceed");

        }elseif ($OlevelResults) {
            # find out if there are oLevel cards for each sitting
            foreach ($OlevelResults as $vt) {
                #fetch card
                $ecard = OlevelCard::where('user_id',$id)
                                    ->where('sitting', $vt->sitting)
                                    ->first();

                if (!$ecard) {
                    # card not found return back
                    //return redirect(route('home'))->with('error'," You have to Upload additional OLevel Verification Card before you continue");
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
        $userReferee = UserReferee::where('user_id',$id)
                                    ->select('uid','is_filled','email','name','phone')
                                    ->get();

        if (!$userReferee) {
            # code...
            //return back()->with('error'," User Referee  has not been found!!!");

        }elseif($userReferee){

            if (count($userReferee)<3) {
                # Return too few referees error
                //return back()->with('error',"Error!!! You need at least three(3) Referees, please add more referees till you have up to three(3)");
            }

            foreach ($userReferee as $v) {
                if ($v->is_filled ==0) {
                    //return redirect(route('home'))->with('error'," One of Your Referees has not responded, Pleas ensure they all respond. Note!!! You can replace them");
                }
            }
        }

        #Next check qualificatons
        $userQualifications = UserQualification::where('user_id',$id)->where('type', 'school')->get();


        if (!$userQualifications) {

            //return back()->with('error'," No Academic Qualification Found!!!");

        }elseif ($userQualifications) {
            # qualifications are found, proceed to check count and uploads
            if (count($userQualifications)<3) {
                # Not enough qualifications

                //return back()->with('error'," You do not have sufficient Academic Qualifications. At least three required (FSLC, SSCE and one of OND/HND/B.Sc)!!!");
            }elseif (count($userQualifications) >=3) {
                # found, loop through and check for consistency
                foreach ($userQualifications as $q) {
                    if ($q->path =='') {
                        # no upload found, return back
                        //return back()->with('error'," Your Uploads for Academic Qualifications is not complete");

                    }elseif ($q->year_obtained =='') {
                        # no year obtained
                        //return back()->with('error'," Kindly specify year obtained for all Academic Qualifications");

                    }elseif ($q->certificate_type =='') {
                        # no year obtained
                        //return back()->with('error'," Kindly specify certificate type for all Academic Qualifications");

                    }elseif ($q->awarding_institution =='') {
                        # no year obtained
                        //return back()->with('error'," Awarding institution cannot be empty for Academic Qualifications");

                    }elseif ($q->uid =='') {
                        # no year obtained
                        //return back()->with('error',"Some Academic Qualifications are not uploaded correctly");

                    }elseif ($q->path =='') {
                        # no year obtained
                        //return back()->with('error',"Some Academic Certificate Uploads are missing, Please Check and re-upload");

                    }

                }
            }
        }

        # professional qualifications are not a must but any one listed must be uploaded
        $userProfessionalQualifications = UserQualification::where('user_id', $id)->where('type', 'professional')->get();
        //return $userProfessionalQualifications;

        if (!$userProfessionalQualifications) {

            //return back()->with('error'," No Professional Qualification Found!!!");

        }elseif ($userProfessionalQualifications) {
            # qualifications are found, proceed to check count and uploads
            if($userProfessionalQualifications) {
                # found, loop through and check for consistency
                foreach ($userProfessionalQualifications as $v) {
                    if ($v->path =='') {
                        # no upload found, return back
                        //return back()->with('error'," Your Uploads for Professional Qualifications is not complete");

                    }elseif ($v->year_obtained =='') {
                        # no year obtained
                        //return back()->with('error'," Kindly specify year obtained for all Professional Qualifications");

                    }elseif ($v->certificate_type =='') {
                        # no year obtained
                        //return back()->with('error'," Kindly specify certificate type for all Professional Qualifications");

                    }elseif ($v->awarding_institution =='') {
                        # no year obtained
                        //return back()->with('error'," Awarding institution cannot be empty for Professional Qualifications");

                    }elseif ($v->uid =='') {
                        # no year obtained
                        //return back()->with('error',"Some Professional Qualifications are not uploaded correctly");

                    }elseif ($v->path =='') {
                        # no year obtained
                        //return back()->with('error',"Some Professional Certificate Uploads are missing, Please Check and re-upload");

                    }

                }
            }
        }

        #get the Research Proposal
        $proposal = UserResearch::where('user_id', $id)->first();

        if (!$proposal) {
            # Proposal not found, make entry for this person with blank proposal and allow him/her to submit without proposal
            $proposal = UserResearch::updateOrCreate(['user_id'=>$id, 'session_id'=>getApplicationSession()],[
                'user_id'=>$id,
                'session_id'=>getApplicationSession(),
            ]);
        }

        # check admission status entry for staff
        $submitted = ApplicantAdmissionRequest::where('user_id', $id)->where('session_id', getApplicationSession())->first();

        if (!$submitted) {
            # The user has not submitted any application this session

            $submitted = ApplicantAdmissionRequest::updateOrCreate(['user_id'=>$id, 'session_id'=> getApplicationSession()],[
                'user_id'=> $id,
                'session_id' => getApplicationSession(),
                'program_id' => $applicantProfile->applicant_program,
                'uid' => uniqid('NDAPG_'),
                'form_number'=> getformNumber(),
            ]);
        }

        return view('applicant.view_preview', compact('applicantUser','applicantProfile', 'OlevelResults','userReferee', 'userQualifications','userProfessionalQualifications', 'submitted','proposal'));

        return "All clear to move";


    }


    public function submitApplication($id){

        # this id is for the user who wants to submit an application, get the user parameters and submit an application for the user
        $submission = ApplicantAdmissionRequest::where('user_id',$id)->first();
        //return $submission;

        if ($submission->is_submitted==0) {
            # submit and move on
            $submission->is_submitted = 1;
            $submission->submitted_at = now();
            $submission->save();

            return back()->with('success', "Congratulations! ! ! Form Submitted Successfully");

        }


    }


    public function viewApplicantAcknowledement($id){

        # fetch the user with the attached parameters and forward same to the user
        $toVerify = ApplicantAdmissionRequest::where('uid', $id)->where('session_id', getApplicationSession())->first();

        //return $toVerify;

        if (!$toVerify) {
            return "Error!!!!! This is not Valid";
        }elseif ($toVerify) {
            #applicant found fetch other parameters and return acknowledgement page

            return view('applicant.view_AcknowledgmentSlip',compact('toVerify'));

            return redirect(route('preview.submitted.application',['id'=> $toVerify->user_id]));
        }

        return "You have submitted your form already, You can check back in order to print out your Acknowledement Slip later";



    }

    public function verifyApplicantPreviewPage($id){

        # fetch the user with the attached parameters and forward same to the user
        $toVerify = ApplicantAdmissionRequest::where('uid', $id)->first();

        if (!$toVerify) {
            return "Error!!!!! This is not Valid";
        }elseif ($toVerify) {
            return redirect(route('preview.submitted.application',['id'=> $toVerify->user_id]));
        }

        return "You have submitted your form already, You can check back in order to print out your Acknowledement Slip later";



    }


    public function viewPaidApplicants(){
        # get all the applicants that have applied for this session and loop through to get their details

        $allPaidApplicants = FeePayment::where('payment_config_id', 1)->where('payment_status','paid')->where('academic_session_id', getApplicationSession())->get();

        #some applicants found next initialize some counters
        $totalAmount = 0;

        foreach ($allPaidApplicants as $m) {
            # increment the total sum collected
            $totalAmount = $totalAmount + $m->amount_paid;

            #next find the user
            $applicant = User::find($m->user_id);

            $paidApplicants[]= collect([
                'userName' => $applicant->name,
                'userEmail' => $applicant->email,
                'userGsm' => $applicant->phone_number,
                'userAmount' => $m->amount_paid,
                'userStatus' => $m->payment_status,
                'userTxId' => $m->txn_id,
                'userPayment' => $m->created_at

            ]);
        }
        return view('admin.viewPaidApplicants',compact('paidApplicants', 'totalAmount'));
    }



    public function viewSubmittedApplications(){
        #get a list of submitted applications from the table for this session
        $submitted = ApplicantAdmissionRequest::where('session_id', getApplicationSession())->where('is_submitted',1)->get();



        foreach ($submitted as $n) {
            #get the user applicant
            $appUser = User::find($n->user_id);
            $appProfile = UserProfile::where('user_id', $n->user_id)->first();


            # collect the orders
            $appList[] = collect([
                'apName' => $appUser->name,
                'apFormNumber' => $n->form_number,
                'uid' => $n->uid,
                'submitted' => $n->is_submitted,
                'oLevelVerified' => $n->is_olevel_verified,
                'sentToDepartment' => $n->is_sent_dept,
                'pg_coord' =>$n->pg_coord,
                'hod' =>$n->hod,
                'dean' =>$n->dean,
                'dean_spgs' =>$n->dean_spgs,
                'admitted' =>$n->is_admitted,
                'programid' => $n->program_id,
                'gender' => $appProfile->gender,
                'userId' => $appUser->id,


            ]);
        }

        //return $appList;

        $title = "List of Applicants that have Submitted Application Form";

        return view('admin.viewSubmittedApplications',compact('appList','title'));
    }



}
