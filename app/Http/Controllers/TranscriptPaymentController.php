<?php

namespace App\Http\Controllers;

use App\Models\FeeConfig;
use App\Models\FeePayment;
use App\Models\TranscriptRequest;
use Illuminate\Http\Request;

class TranscriptPaymentController extends Controller
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

    public function checkBilling($id){
        #here we check the billing for this and if we find it we send for billing, if not we forward back home with error
        #grab the instance
        $tRequest = TranscriptRequest::where('uid', $id)->first();

        if ($tRequest) {
            #first check if payment is Generated and pending and forward to payment
            if ($tRequest->feepayment) {
                return "Generated Payment Found, Do not show but forward for payment";
            }else {

                #get the billing for this request
                #determine the fee category
                if ($tRequest->t_type == 1) {
                    $feeCategory = getFeeCatoryBySlug('ug-transcript');
                }elseif ($tRequest->t_type == 2) {
                    $feeCategory = getFeeCatoryBySlug('pg-transcript');
                }
                #determine the country value
                switch (getCountryById($tRequest->details->country)->country_name) {
                    case 'Nigeria':
                        $inCountryValue = 1;
                        break;

                    default:
                        $inCountryValue = 0;
                        break;
                }
                # next determine the physical or email value
                switch ($tRequest->details->d_option) {
                    case '1':
                        $physical = 1;
                        break;

                    default:
                        $physical = 0;
                        break;
                }

                # next determine the Express Value or email value
                switch ($tRequest->details->express) {
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
                    #payment config found, you may now save config and proceed
                    $tRequest->fconfig = $feeConfig->id;
                    $tRequest->save();
                    return redirect()->action([TranscriptPaymentController::class, 'generateBilling'],['id'=> $tRequest->uid,'fee'=>convertToNaira($feeConfig->feeTemplate->total_amount)])->with('error', "This Request is Invalid");

                }else {
                    return redirect(route('home'))->with('error',"Error!!! No Fee Configuration found, Contact Support");
                }

                // $token = hash('sha256', env('UG_TX_APP_KEY').$trRequest->matric);

            }
        }else {
            return redirect()->action([TranscriptRequestController::class, 'index'])->with('error', "This Request is Invalid");
        }

    }

    public function generateBilling($id, $fee){
        #grab the instance

        $toBill = TranscriptRequest::where('uid', $id)->first();

        if ($toBill->fconfig != null) {
            #test the faithfullness of this user if you find something wrong, delet the request
            if (convertToKobo($fee) != $toBill->feeConfig->feeTemplate->total_amount) {

                $toBill->delete();

                return redirect()->action([TranscriptRequestController::class, 'index'])->with('error', "Error!!!!, We detected something funny, Request Refreshed");

            }elseif (convertToKobo($fee) === $toBill->feeConfig->feeTemplate->total_amount) {
                #All Tests Passed, proceed to generate fees for this request for onward processing
                $newFeePayment = FeePayment::updateOrCreate([
                    'user_id' => $toBill->user_id,
                    'request_id' => $toBill->id,
                    'uid' => $toBill->uid,
                ],[
                    'user_id' => $toBill->user_id,
                    'request_id' => $toBill->id,
                    'uid' => $toBill->uid,
                    'payment_config_id' => $toBill->fconfig,
                    'fee_config_id' => $toBill->fconfig,
                    'amount_billed' => $toBill->feeConfig->feeTemplate->total_amount,
                    'balance' => $toBill->feeConfig->feeTemplate->total_amount,
                ]);

                $amount = $toBill->feeConfig->feeTemplate->total_amount;

                if ($newFeePayment) {
                    #Fee Payment entry successful, forward the user to Credo Payment Request
                    return redirect()->action([CredoRequestController::class, 'generateCredoRequest'], ['id'=> $newFeePayment->uid, 'fee'=> $amount]);

                }else {

                    return redirect()->action([TranscriptRequestController::class, 'index'])->with('error', "Billing Error, Please try again");

                }

            }else {
                return "somehow nothing found";
            }

        }else {
            # I don't know why this user is here, send them back home to start again
            return redirect()->action([TranscriptPaymentController::class, 'checkBilling'],['id'=> $id])->with('error', "This Request is Invalid");


        }

    }


}
