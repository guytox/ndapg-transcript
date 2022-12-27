<?php

namespace App\Http\Middleware;

use App\Models\ApplicantAdmissionRequest;
use Closure;
use Illuminate\Http\Request;

class CheckApplicantSubmission
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

        $userDetails = ApplicantAdmissionRequest::where('user_id', user()->id)->first();

        if ($userDetails) {

            if ($userDetails->is_submitted==0) {
                # prepare to write the requested resource.
                return $next($request);

            }elseif ($userDetails->is_submitted==1) {
                # code...
                return redirect(route('preview.submitted.application',['id'=>user()->id]))->with('error', "This form has been submitted before");

            }else{

                return redirect(route('preview.submitted.application',['id'=>user()->id]))->with('error', "This form has been submitted before");

            }
        }else{

            return $next($request);
        }



    }
}
