<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\ApplicationFeeRequest;
use App\Models\FeePayment;
use App\Models\PaymentConfiguration;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function applicationFee()
    {
        $applicationFeeConfiguration = PaymentConfiguration::where('payment_purpose_slug', 'application-fee')->first();
        if($applicationFeeConfiguration) {
            $feePayment = FeePayment::where(['user_id' => user()->id, 'payment_config_id' => $applicationFeeConfiguration->id])->first();

            if($feePayment !== null){
                return view('applicant.application_fee', compact('feePayment'));
            }

            $terminalId = config('app.etranzact.terminal_id');

            $responseURL = config('app.etranzact.application_fee_response_url');

            $secretKey = config('app.etranzact.secret_key');

            $logoURL = config('app.etranzact.logo_url');

            $transactionId = generateUniqueTransactionReference();

            $amount =  $applicationFeeConfiguration->amount;

            $checkSum = generateCheckSum(
                $amount,
                $transactionId,
                $terminalId,
                $responseURL,
                $secretKey
            );

            $transaction = ApplicationFeeRequest::updateOrCreate(['payee_id' =>user()->id], [
                'amount' => $applicationFeeConfiguration->amount,
                'payee_id' => user()->id,
                'txn_id' => $transactionId,
                'checksum' => $checkSum,
                'uid' => uniqid('fw_'),
            ]);

            $paymentData = [
                'email' => $transaction->user->email,
                'amount' => $amount,
                'description' => $transaction->description,
                'txn_id' => $transaction->txn_id,
                'checksum' => $transaction->checksum,
                'name' => $transaction->user->name,
                'payee_id' => $transaction->user->id,
                'responseurl' => $responseURL,
                'logourl' => $logoURL,
            ];

            return redirect()->route('pay.application.now')->with(['paymentData' => $paymentData ]);

        }

        abort(403, 'Application not configured');

    }
}
