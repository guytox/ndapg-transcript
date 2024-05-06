<?php

namespace App\Http\Controllers;

use App\Jobs\PaymentStatusCheckJob;
use App\Models\CredoRequest;
use App\Models\FeeConfig;
use App\Models\PaymentLog;
use App\Models\TranscriptDetail;
use App\Models\TranscriptRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TranscriptRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {



         $previousRequests = TranscriptRequest::where('user_id', user()->id)->get();

        if ($previousRequests) {

            if (count($previousRequests)>0) {

                return view('applicant.view-transcript-request-history',compact('previousRequests'));

            }else {

                return redirect(route('home'))->with('error',"You do not have any Previous Requests");
            }

        }else {

            return redirect(route('home'))->with('error',"You do not have any Previous Requests");
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        # Search for an existing request that is not paid and forward for payment
        $previousRequests = TranscriptRequest::where('user_id', user()->id)
                                            ->where('p', 0)
                                            ->first();
        if ($previousRequests) {
            return redirect()->action([TranscriptRequestController::class, 'show'],['transcript'=>$previousRequests->uid]);
        }else {
            return view('applicant.new-transcript-request-form');
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'TranscriptType' => ['required','exists:transcript_types,id'],
            'DeliveryOption' => ['required','exists:transcript_delivery_modes,id'],
            'DeliveryCountry' => ['required','exists:countries,id'],
            'DeliveryEmail' => ['required_if:DeliveryOption,2', 'email','nullable'],
            'MatricNumber' => ['required'],
            'AdmissionYear' => ['required'],
            'GraduationYear' => ['required'],
            'TranscriptReceiver' => ['required','string'],
            'ReceiverEstablishment' => ['required','string'],
            'StreetAddress' => ['required','string'],
            'ZipOrPostalCode' => ['sometimes','nullable'],
            'AddressCity' => ['required','string'],
            'DeliveryState' => ['required','string'],
        ]);

        #first check if he has a pending transcript request that is unpaid
        $unPaidRequest = TranscriptRequest::where('user_id', user()->id)
                                            ->where('p', 0)
                                            ->first();
        if ($unPaidRequest) {
            return "You have a Pending Request, proceed to forward to payment";
        }else{

            $newTRequest = TranscriptRequest::updateOrCreate([
                'user_id' => user()->id,
                'matric' => $validated['MatricNumber'],
                't_type' => $validated['TranscriptType'],
                'p' => 0,
            ],[
                'user_id' => user()->id,
                'uid' => uniqid('tr'),
                'matric' => $validated['MatricNumber'],
                't_type' => $validated['TranscriptType'],
            ]);

            if ($newTRequest) {
                TranscriptDetail::updateOrCreate([
                    'request_id' => $newTRequest->id,
                ],[
                    'request_id' => $newTRequest->id,
                    't_type' => $validated['TranscriptType'],
                    'matric' => $validated['MatricNumber'],
                    'admissionYear' => $validated['AdmissionYear'],
                    'GraduationYear' => $validated['GraduationYear'],
                    'd_option' => $validated['DeliveryOption'],
                    'receiver' => $validated['TranscriptReceiver'],
                    'receiver_email' => $validated['DeliveryEmail'],
                    'establishment' => $validated['ReceiverEstablishment'],
                    'street' => $validated['StreetAddress'],
                    'zip' => $validated['ZipOrPostalCode'],
                    'city' => $validated['AddressCity'],
                    'state' => $validated['DeliveryState'],
                    'country' => $validated['DeliveryCountry'],
                ]);
            }

            return redirect()->action([TranscriptRequestController::class, 'show'],['transcript'=>$newTRequest->uid]);
        }

        return $validated;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $trRequest = TranscriptRequest::where('uid', $id)->first();
        if ($trRequest) {
            #first check if payment is Generated and pending and forward to payment
            if ($trRequest->feepayment) {
                return "Generated Payment Found, Do not show but forward for payment";
            }else {

                #get the billing for this request
                #determine the fee category
                if ($trRequest->t_type == 1) {
                    $feeCategory = getFeeCatoryBySlug('ug-transcript');
                }elseif ($trRequest->t_type == 2) {
                    $feeCategory = getFeeCatoryBySlug('pg-transcript');
                }
                #determine the country value
                switch (getCountryById($trRequest->details->country)->country_name) {
                    case 'Nigeria':
                        $inCountryValue = 1;
                        break;

                    default:
                        $inCountryValue = 0;
                        break;
                }
                # next determine the physical or email value
                switch ($trRequest->details->d_option) {
                    case '1':
                        $physical = 1;
                        break;

                    default:
                        $physical = 0;
                        break;
                }

                # next determine the Express Value or email value
                switch ($trRequest->details->express) {
                    case '1':
                        $express = 1;
                        break;

                    default:
                        $express = 0;
                        break;
                }


                 $countryValue = $inCountryValue;
                $physicalValue = $physical;
                 $expressValue = $express;
                  $category = $feeCategory->id;

                //  return $category.$countryValue.$physicalValue.$expressValue;

                #ready to get the config
                $feeConfig = FeeConfig::where('in_country', $countryValue)
                                            ->where('physical', $physicalValue)
                                            ->where('fee_category_id', $category)
                                            ->first();

                if ($feeConfig) {

                    $amount = $feeConfig->feeTemplate->total_amount;
                    return view('applicant.preview-transcript-request-form',compact('trRequest','amount'));


                }else {
                    return redirect(route('home'))->with('error',"Error!!! No Fee Configuration found, Contact Support");
                }

                // $token = hash('sha256', env('UG_TX_APP_KEY').$trRequest->matric);

            }
        }else {
            return redirect()->action([TranscriptRequestController::class, 'index'])->with('error', "This Request is Invalid");
        }
        # here we look for the correct billing required for this person and show them to view before they can proceed
        #if payment does not exist, the proceed to check and show the end user to confirm before proceeding

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect(route('home'));
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
        $validated = $request->validate([
            'TranscriptType' => ['required','exists:transcript_types,id'],
            'DeliveryOption' => ['required','exists:transcript_delivery_modes,id'],
            'DeliveryCountry' => ['required','exists:countries,id'],
            'DeliveryEmail' => ['required_if:DeliveryOption,2', 'email','nullable'],
            'MatricNumber' => ['required'],
            'AdmissionYear' => ['required'],
            'GraduationYear' => ['required'],
            'TranscriptReceiver' => ['required','string'],
            'ReceiverEstablishment' => ['required','string'],
            'StreetAddress' => ['required','string'],
            'ZipOrPostalCode' => ['sometimes','nullable'],
            'AddressCity' => ['required','string'],
            'DeliveryState' => ['required','string'],
        ]);


        #update the parameters
        $toUpdate = TranscriptRequest::where('uid', $id)->first();
        $toUpdate->matric =  $validated['MatricNumber'];
        $toUpdate->t_type =  $validated['TranscriptType'];
        $toUpdate->save();

        #update transcript details;
        $trDetails = $toUpdate->details;
        $trDetails->request_id = $toUpdate->id;
        $trDetails->t_type = $validated['TranscriptType'];
        $trDetails->matric = $validated['MatricNumber'];
        $trDetails->admissionYear = $validated['AdmissionYear'];
        $trDetails->GraduationYear =  $validated['GraduationYear'];
        $trDetails->d_option =  $validated['DeliveryOption'];
        $trDetails->receiver =  $validated['TranscriptReceiver'];
        $trDetails->receiver_email =  $validated['DeliveryEmail'];
        $trDetails->establishment =  $validated['ReceiverEstablishment'];
        $trDetails->street =  $validated['StreetAddress'];
        $trDetails->zip = $validated['ZipOrPostalCode'];
        $trDetails->city = $validated['AddressCity'];
        $trDetails->state = $validated['DeliveryState'];
        $trDetails->country =  $validated['DeliveryCountry'];
        $trDetails->save();

            return redirect()->action([TranscriptPaymentController::class, 'checkBilling'],['id'=>$toUpdate->uid]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect(route('home'));
    }


    public function viewTimeline($id){
        $reQuestDetails = TranscriptRequest::where('uid', $id)->first();

        // $payLog = PaymentLog::where('fee_payment_id', $reQuestDetails->feePayment->id)->first();
        // return $payLog->feePayment->transcriptRequest;

        // return $reQuestDetails->feepayment;
        return view('applicant.view-transcript-timeline', compact('reQuestDetails'));
    }

    public function verifyTrancriptRequestPayment($id){
        #this is built to verify payment,
        #just call a job that will verify the credo details and proceed to confirm payment
        return $id;
    }

    public function submitTranscriptRequest($id){
        #fetch this fee payment instance and foward to the job
        $feePymnt = TranscriptRequest::where('uid', $id)->first();

        if ($feePymnt->ts == 1) {
            # already submitted, just return without processing
            return back()->with('info', "This request has already been submitted, Please wait for processing ...");
        }
        // return $feePymnt->feepayment;
        // return $feePymnt->feepayment->config->feeCategory->payment_purpose_slug;
        PaymentStatusCheckJob::dispatch($feePymnt->feepayment->uid, now());

        return back()->with('info', "Transcript Request submitted successfully, Please Check back after sometime");
    }

    public function matricNumberVerification(Request $request){
        $validated = $request->validate([
            'tx_ref' => 'required',
            'token' => 'required',
            'request_ref' => 'required'
        ]);

        if ($request->token == hash('sha256', env('UG_TX_PUB_KEY').$request->tx_ref.env('UG_TX_APP_KEY'))) {
            #request is valid proceed to write records and update the record appropriately
            # this request is valid, update the records and proceed
            #grab the instance here
            $feePymnt = TranscriptRequest::where('uid', $request->request_ref)->first();
            if ($feePymnt->tx_ref != 0) {
                #now update the request to submitted since there's a response from ug portal
                $feePymnt->ts = 1;
                $feePymnt->ug_ref = $request->tx_ref;
                $feePymnt->ts_at = now();
                $feePymnt->ug_mssg = "Request Successfully Submitted";
                $feePymnt->save();

           }elseif($feePymnt->tx_ref != 0){
               #this student matric number was not found so the request was submitted as a failed request, so notify the end user of the latest message
               $feePymnt->ug_mssg = "Matric Number Not Found";
               $feePymnt->save();

               $data =[
                'status' => 'Okay',
               ];

               return response($data, 200);
           }
        }else {
            #invalid request return error
            $data = [
                'status' => "ChecksumError"
            ];

            return response($data, 401);
        }
    }


    public function sendProfileInformation(Request $request){


        $validated = $request->validate([
            'token' => 'required|string',
            'public_key' => 'required|string',
            'transcript_ref' => 'required',
        ]);

        $rHash = hash('sha256', $request->public_key.$request->transcript_ref.env('UG_TX_APP_KEY'));

        if ($rHash != $request->token) {
            $data = [
                'status' => 'ChecksumError'
            ];
            return response($data, 401);
        }

        #check if the request has come in before and reply with details
        $requestProfile = TranscriptRequest::where('uid', $request->transcript_ref)->first();
        if ($requestProfile) {
            #This request is found
            $data = [
                'transcript_ref' => $requestProfile->ug_ref,
                'SurName' => $requestProfile->profile->surname,
                'OtherNames' => $requestProfile->profile->othernames,
                'DateOfBirth' => $requestProfile->profile->dob,
                'RegularCourse' => $requestProfile->profile->regularcourse,
                'Batallion' => $requestProfile->profile->batallion,
                'AdmissionYear' => $requestProfile->profile->ugadmissionyear,
                'GraduationYear' => $requestProfile->profile->uggraduationyear,
                'CommissionYear' => $requestProfile->profile->commissiondate,
                'Gender' => $requestProfile->profile->gender,
                'Service' => $requestProfile->profile->service,
                'Country' => $requestProfile->profile->nationality,
                'public_key' => env('UG_TX_PUB_KEY'),
                'token' => hash('sha256', $request->public_key.$requestProfile->ug_ref.env('UG_TX_APP_KEY'))
            ];

            return response($data, 200);
        }
    }


    public function receiveProfileUpdateIformation(Request $request){

        // return $request
        $validated = $request->validate([
            'token' => 'required|string',
            'public_key' => 'required|string',
            'transcript_ref' => 'required',
        ]);

        $rHash = hash('sha256', $request->public_key.$request->transcript_ref.$request->MatricNumber.env('UG_TX_APP_KEY'));

        if ($rHash === $request->token ) {
            #validation passed, process data
            $profileToUpdate = TranscriptRequest::where('uid', $request->transcript_ref)->first();
            $profile =  $profileToUpdate->profile;
            #update with received data
            $profile->ndanumber = $request->MatricNumber;
            $profile->batallion = $request->NdaBatallion;
            $profile->regularcourse = $request->RegularCourse;
            $profile->ndaservice = $request->NdaService;
            $profile->ugadmissionyear = $request->AdmissionYear;
            $profile->uggraduationyear = $request->GraduationYear;
            $profile->commissiondate = $request->CommissionYear;
            $profile->gender = $request->Gender;
            $profile->dob = $request->DateOfBirth;
            $profile->surname = $request->SurName;
            $profile->othernames = $request->OtherNames;
            $profile->nationality = $request->Country;
            $profile->state_id = $request->State;
            $profile->local_government = $request->LocalGovernment;
            $profile->save();
            #update Profile Details
            $details = $profileToUpdate->details;
            $details->matric = $request->MatricNumber;
            $details->admissionYear = $request->AdmissionYear;
            $details->graduationYear = $request->GraduationYear;
            $details->save();
            #now update the request
            $profileToUpdate->pu =1;
            $profileToUpdate->pu_at = now();
            $profileToUpdate->pu_by = $request->ProfileUpdatedBy;
            $profileToUpdate->ug_mssg = "Profile Updated Successfully, Please Wait for Transcript Generation";
            $profileToUpdate->save();
            // return $profileToUpdate;
            #update complete, now return response

            $responseData =[
                'status' => 0,
                'request_ref' => $profileToUpdate->ug_ref,
                'token' => hash('sha256', $request->public_key.$profileToUpdate->ug_ref.env('UG_TX_APP_KEY')),
                'public_key' => env('UG_TX_PUB_KEY'),
            ];

            return response($responseData, 200);

        }
    }

    public function receiveTranscriptProcessedInfo(Request $request){
        return $request;
    }

    public function receiveTranscriptGenerationStatus(Request $request){
        // return $request
        $validated = $request->validate([
            'token' => 'required|string',
            'public_key' => 'required|string',
            'transcript_ref' => 'required',
            'TranscriptGeneratedBy' => 'required',
        ]);

        $rHash = hash('sha256', $request->public_key.$request->transcript_ref.$request->TranscriptGeneratedBy.env('UG_TX_APP_KEY'));

        if ($rHash === $request->token ) {
            #validation passed, process data
            $profileToUpdate = TranscriptRequest::where('uid', $request->transcript_ref)->first();


            #now update the request
            $profileToUpdate->tp =1;
            $profileToUpdate->tp_at = now();
            $profileToUpdate->tp_by = $request->TranscriptGeneratedBy;
            $profileToUpdate->ug_mssg = "Transcript Generated Successfully, Waiting for Verification";
            $profileToUpdate->save();
            // return $profileToUpdate;
            #update complete, now return response

            $responseData =[
                'status' => 0,
                'request_ref' => $profileToUpdate->ug_ref,
                'token' => hash('sha256', $request->public_key.$profileToUpdate->ug_ref.env('UG_TX_APP_KEY')),
                'public_key' => env('UG_TX_PUB_KEY'),
            ];

            return response($responseData, 200);

        }
    }


    public function receiveTranscriptVerificationStatus(Request $request){
        // return $request
        $validated = $request->validate([
            'token' => 'required|string',
            'public_key' => 'required|string',
            'transcript_ref' => 'required',
            'TranscriptVerifiedBy' => 'required',
        ]);

        $rHash = hash('sha256', $request->public_key.$request->transcript_ref.$request->TranscriptVerifiedBy.env('UG_TX_APP_KEY'));

        if ($rHash === $request->token ) {
            #validation passed, process data
            $profileToUpdate = TranscriptRequest::where('uid', $request->transcript_ref)->first();


            #now update the request
            $profileToUpdate->tv =1;
            $profileToUpdate->tv_at = now();
            $profileToUpdate->tv_by = $request->TranscriptVerifiedBy;
            $profileToUpdate->ug_mssg = "Transcript Verified Successfully, Awaiting Dispatch";

            $profileToUpdate->save();
            // return $profileToUpdate;
            #update complete, now return response

            $responseData =[
                'status' => 0,
                'request_ref' => $profileToUpdate->ug_ref,
                'token' => hash('sha256', $request->public_key.$profileToUpdate->ug_ref.env('UG_TX_APP_KEY')),
                'public_key' => env('UG_TX_PUB_KEY'),
            ];

            return response($responseData, 200);

        }
    }

    public function receiveTranscriptDispatchStatus(Request $request){
        // return $request
        $validated = $request->validate([
            'token' => 'required|string',
            'public_key' => 'required|string',
            'transcript_ref' => 'required',
            'TranscriptDispatchedBy' => 'required',
        ]);

        $rHash = hash('sha256', $request->public_key.$request->transcript_ref.$request->TranscriptDispatchedBy.env('UG_TX_APP_KEY'));

        if ($rHash === $request->token ) {
            #validation passed, process data
            $profileToUpdate = TranscriptRequest::where('uid', $request->transcript_ref)->first();


            #now update the request
            $profileToUpdate->td =1;
            $profileToUpdate->td_at = now();
            $profileToUpdate->td_by = $request->TranscriptDispatchedBy;
            $profileToUpdate->ug_mssg = "Transcript Dispatch Successfully, Awaiting Receipt";

            $profileToUpdate->save();
            // return $profileToUpdate;
            #update complete, now return response

            $responseData =[
                'status' => 0,
                'request_ref' => $profileToUpdate->ug_ref,
                'token' => hash('sha256', $request->public_key.$profileToUpdate->ug_ref.env('UG_TX_APP_KEY')),
                'public_key' => env('UG_TX_PUB_KEY'),
            ];

            return response($responseData, 200);

        }
    }


}
