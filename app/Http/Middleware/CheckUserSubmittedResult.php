<?php

namespace App\Http\Middleware;

use App\Models\OlevelResult;
use Closure;
use Illuminate\Http\Request;

class CheckUserSubmittedResult
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
        $userResults = OlevelResult::where('user_id', user()->id)->get();
        if($userResults->count() < 1) {
            return redirect()->route('applicant.add_result');
        }else {
            return $next($request);
        }
    }
}
