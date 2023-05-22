<?php

namespace App\Http\Middleware;

use App\Models\SystemVariable;
use Closure;
use Illuminate\Http\Request;

class ApplicationOpenStatus
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
        $check = SystemVariable::where('name','applications')->first();
        if ($check->value == 'On') {
            #registration is on allow to proceed
            return $next($request);
        }else{
            return redirect(route('login'))->with('error', "We Are not Accepting Application Forms Now");

        }
    }
}
