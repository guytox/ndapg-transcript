<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use Illuminate\Http\Request;

class BillingController extends Controller
{

    public function getPaidAdmittedStudents(Request $request){

        if (user()->hasRole('admin|dean_pg')) {
            $validated = $request->validate([
                'schoolsession' => 'required',
            ]);

            $paymentList = Admission::where('amount_paid','>',0)
                                    ->where('session_id',$request->schoolsession)
                                    ->get();
            $totalPaid = 0;

            //$payList []='';

            foreach ($paymentList as $key => $v) {

                $payList [] = collect([
                    'matno' => $v->matric_number,
                    'formno' => $v->form_number,
                    'name' => $v->surname. " ".$v->other_names,
                    'paycode' => $v->payment_code,
                    'programme' => getProgrammeDetailById($v->programme_id,'name'),
                    'clearedby' => getUserById($v->cleared_by)->name,
                    'clearedat' => $v->cleared_at,
                    'amountpaid' => $v->amount_paid
                ]);

                $totalPaid = $totalPaid + $v->amount_paid;

            }

            //return $payList;



            return view('bursary.view-uploaded-payments', compact('payList','totalPaid'));
        }

    }
}
