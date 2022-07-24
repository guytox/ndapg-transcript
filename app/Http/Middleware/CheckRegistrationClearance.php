<?php

namespace App\Http\Middleware;

use App\Models\RegClearance;
use Closure;
use Illuminate\Http\Request;

class CheckRegistrationClearance
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


        $regClearance = RegClearance::where([
            'school_session_id' => activeSession()->id,
            'student_id' => getStudentIdByUserId(user()->id),
            activeSession()->currentSemester.'_semester' => 1

        ])->first();

        if($regClearance) {
            return $next($request);
        }else {
            return redirect()->route('home')->with(['payment_error' => 'Error!!! you need to make payment before registering for '.activeSession()->currentSemester.' semester']);
        }
    }
}
