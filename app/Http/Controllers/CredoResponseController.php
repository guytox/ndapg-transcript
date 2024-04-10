<?php

namespace App\Http\Controllers;

use App\Jobs\CredoPaymentConfirmationJob;
use App\Models\CredoResponse;
use Illuminate\Http\Request;

class CredoResponseController extends Controller
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

    public function confirmcredoApplicationPayment(Request $request){
        if ($request->has('transRef') && $request->has('transAmount')) {
            // return $request->get('reference');
            $transactionId = $request->get('transRef');
            $currency = $request->get('currency');
            $statusCode = $request->get('status');
            $amount = $request->get('transAmount');
            //  return $transactionId;

            #store the response
            $newrequest = CredoResponse::updateOrCreate([
                'businessRef'=>$request->reference
            ],[
                'businessRef'=>$request->reference,
                'transRef'=>$request->transRef,
                'currency'=>$request->currency,
                'status'=>$request->status,
                'transAmount'=>$request->transAmount,
            ]);

            if ($newrequest->status == 0) {
                    CredoPaymentConfirmationJob::dispatch($request->transRef, now());

                    return redirect(route('home'))->with('success', "Payment Submitted for verification Successfully, Please Check Back Later");

            }else{

                return redirect(route('home'))->with('error', "Payment Confirmation Error, Please Try again");
            }


        }else {
            return "nothing found";
        }
    }
}
