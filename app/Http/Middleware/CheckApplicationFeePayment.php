<?php

namespace App\Http\Middleware;

use App\Models\FeePayment;
use Closure;
use Illuminate\Http\Request;

class CheckApplicationFeePayment
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
        $applicationConfiguration = getPaymentConfigBySlug('application-fee');
        $applicationFee = FeePayment::where([
            'payment_config_id' => $applicationConfiguration->id,
            'user_id' => user()->id
        ])->first();

        if($applicationFee) {
            return $next($request);
        }else {
            return redirect()->route('application.fee')->with(['payment_error' => 'you need to complete payment']);
        }


    }
}
