<?php

namespace App\Http\Controllers;

use App\Models\FeePayment;
use Illuminate\Http\Request;
use App\Jobs\ConfirmApplicationPaymentJob;
use App\Jobs\ConfirmPaymentJob;


class PaymentHandleController extends Controller
{
    public function initiatePayment(Request $request)
    {
        $feePayment = FeePayment::where('uid', $request->get('fee_payment_uid'))->first();
        if ($feePayment) {
            $validated = $request->validate([
                'fee_payment_uid' => 'required',
                'amount' => 'required|integer|max:' . $feePayment->balance,
                'payment_method' => 'required|in:card,umm-wallet'
            ]);

            if($request->get('payment_method') === 'card') {


                $terminalId = config('app.etranzact.terminal_id');

                $responseURL = config('app.etranzact.response_url');

                $secretKey = config('app.etranzact.secret_key');

                $logoURL = config('app.etranzact.logo_url');

                $transactionId = generateUniqueTransactionReference();

                $amount = $validated['amount'];

                $transaction = FeePayment::where('uid', $validated['fee_payment_uid'])->first();

                $checkSum = generateCheckSum(
                    $amount,
                    $transactionId,
                    $terminalId,
                    $responseURL,
                    $secretKey
                );

                if ($transaction) {
                    $transaction->update([
                        'txn_id' => $transactionId,
                        'checksum' => $checkSum
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

                    return redirect()->route('pay.now')->with(['paymentData' => $paymentData]);
                }
            }else {
//                return $this->payWithWallet($feePayment->uid, $validated['amount']);
            }

            abort(403, 'Invalid transaction');
        }

        abort(403, 'Invalid transaction');
    }

    public function confirmPayment(Request $request)
    {
        if ($request->has('TRANSACTION_ID') && $request->has('CHECKSUM')) {

            $transactionId = $request->get('TRANSACTION_ID');
            $checkSum = $request->get('CHECKSUM');
            $finalCheckSum = $request->get('FINAL_CHECKSUM');
            $statusCode = $request->get('SUCCESS');
            $amount = $request->get('AMOUNT');

            $email = $request->get('EMAIL');

            // send background job to confirm the payment with checksum and transaction id
            ConfirmPaymentJob::dispatch($transactionId, $checkSum, $finalCheckSum, $statusCode, $amount, $email);

            return redirect()->route('student.outstanding.payments')->with(['message' => 'Your payment confirmation is processing']);
        }

        abort(403, 'Unable to confirm payment information');
    }

    public function confirmApplicationPayment(Request $request)
    {
        if ($request->has('TRANSACTION_ID') && $request->has('CHECKSUM')) {

            $transactionId = $request->get('TRANSACTION_ID');
            $checkSum = $request->get('CHECKSUM');
            $finalCheckSum = $request->get('FINAL_CHECKSUM');
            $statusCode = $request->get('SUCCESS');
            $amount = $request->get('AMOUNT');

            $email = $request->get('EMAIL');

            // send background job to confirm the payment with checksum and transaction id
            ConfirmApplicationPaymentJob::dispatch($transactionId, $checkSum, $finalCheckSum, $statusCode, $amount, $email);

            return redirect()->route('home')->with(['message' => 'Your payment confirmation is processing']);
        }

        abort(403, 'Unable to confirm payment information');
    }

}