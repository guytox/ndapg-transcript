<?php

namespace App\Http\Controllers;

use App\Jobs\ConfirmApplicationPaymentJob;
use App\Models\FeePayment;
use Illuminate\Http\Request;
use App\Jobs\ConfirmCredoApplicationPaymentJob;
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

                $amount = convertToKobo($validated['amount']);

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

                    # Start credo processing here

                    $body = [
                        'amount' => $amount,
                        'email' => $transaction->user->email,
                        'bearer' => 1,
                        'callbackUrl' => config('app.credo.response_url'),
                        'channels' => 'card, bank, ussd, QR, mobile_money, bank_transfer',
                        'currency' => 'NGN',
                        'customerPhoneNumber' => $transaction->user->phone_number,
                        'metadata' => [
                            'customFields' => [
                                [
                                    'variable_name' => 'payee_id',
                                    'value' => $transaction->user->id,
                                    'display_name' => 'StudentID'
                                ],[
                                    'variable_name' => 'name',
                                    'value' => $transaction->user->name,
                                    'display_name' => 'StudentName'
                                ],
                            ]
                        ],
                        'reference' => $transaction->txn_id,
                    ];

                    $headers = [
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                        'Authorization' => config('app.credo.public_key'),
                    ];

                    $client = new \GuzzleHttp\Client();

                    $response = $client->request('POST', 'api.public.credodemo.com',[
                        'headers' => $headers,
                        'json' => $body
                    ]);

                    print_r($response->getBody()->getContents());

                    return "these are the payment details";
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

    public function confirmcredoApplicationPayment(Request $request)
    {

        //return $request;

        # prepare for validation

        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => config('app.credo.private_key'),
        ];

        $newurl = 'api.public.credodemo.com/transaction/'.$request->transRef.'/verify';

        $client = new \GuzzleHttp\Client();

        $response = $client->request('GET', $newurl,[
            'headers' => $headers,
        ]);

        $parameters = json_decode($response->getBody());

        return $parameters;

        if ($request->has('transRef') && $request->has('transAmount')) {

            $transactionId = $request->get('transRef');
            $currency = $request->get('currency');
            $statusCode = $request->get('status');
            $amount = $request->get('transAmount');


            // send background job to confirm the payment with checksum and transaction id
            ConfirmCredoApplicationPaymentJob::dispatch($transactionId, $currency, $statusCode, $amount);

            return redirect()->route('home')->with(['message' => 'Your payment confirmation is processing']);
        }

        abort(403, 'Unable to confirm payment information');
    }

}
