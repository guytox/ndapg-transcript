<?php

namespace App\Http\Middleware;

use App\Models\FeePayment;
use App\Models\RegMonitor;
use App\Models\SystemVariable;
use Closure;
use Illuminate\Http\Request;

class CheckLateRegistration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        #find the late registration variable
        $lateRegVal = SystemVariable::where('name','late_reg')->first();

        if ($lateRegVal->value ==='On') {
            #late Reg is on  now check to see if he has paid
            $StdLateReg = RegMonitor::where('student_id', user()->student->id)
                                    ->where('session_id', getActiveAcademicSessionId())
                                    ->where('semester_id', getActiveSemesterId())
                                    ->first();
            if ($StdLateReg) {

                return redirect(route('student.registration.viewAll',['id'=>user()->id]))->with('error', "You have Registered for this Semester Already!!!!!");

            }else{
                #check if the student has paid late registrations

                $ChkLateReg = FeePayment::join('fee_configs as f','f.id','=','fee_payments.payment_config_id')
                                        ->join('fee_categories as c','c.id','=','f.fee_category_id')
                                        ->where('fee_payments.user_id', user()->id)
                                        ->where('c.payment_purpose_slug', 'late-registration')
                                        ->select('fee_payments.*')
                                        ->first();
                if ($ChkLateReg) {
                    #entry found, it means fees have been generated for the user let us check if payment has been made if not redirect give me the feed back
                    if ($ChkLateReg->payment_status === 'paid') {
                        #The late Registration has already been paid at this point so just allow the guy
                        return $next($request);

                    }else{

                        return back()->with('error', "We are in Late Registration Now, Your Payment Ref is unpaid");
                    }
                }else{

                    return back()->with('error', "We are in Late Registration Now, you need to generate a transaction Id");

                }

                return back()->with('error', "We are in Late Registration Now, We Could not generate a Payment Ref for you, Contact ICT");
            }



        }elseif ($lateRegVal->value ==='Off') {

            return $next($request);

        }


    }
}
