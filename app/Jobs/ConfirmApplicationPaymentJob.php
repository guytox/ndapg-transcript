<?php

namespace App\Jobs;

use App\Models\PaymentConfiguration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\PaymentLog;
use App\Models\FeePayment;
use Illuminate\Support\Facades\Log;
use App\Models\ApplicationFeeRequest;
use App\Models\User;

class ConfirmApplicationPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $transactionId;
    public $checksum;
    public $finalchecksum;
    public $statusCode;
    public $amount;
    public $email;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($transactionId, $checksum, $finalchecksum, $statusCode, $amount, $email)
    {
        $this->transactionId = $transactionId;
        $this->checksum = $checksum;
        $this->finalchecksum = $finalchecksum;
        $this->statusCode = $statusCode;
        $this->amount = $amount;
        $this->email = $email;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $validPaymentMessage = 'Congratulations your  payment has been confirmed';

        $invalidPaymentMessage = 'Sorry we could not confirm your payment contact the administrator with the email julipels@yahoo.com';

        $terminal_id = config('app.etranzact.terminal_id');
        $response_url = config('app.etranzact.application_fee_response_url');
        $secretKey = config('app.etranzact.secret_key');

        $emailSubject = 'payment vericfication UMM';

        $generatedFinalChecksum = md5($this->statusCode . $this->amount . $terminal_id . $this->transactionId . $response_url . $secretKey);

        Log::info("generatedFinal checksum" . $generatedFinalChecksum);

        Log::info("final" . $this->finalchecksum);

        if (strtoupper($generatedFinalChecksum) === strtoupper($this->finalchecksum)) {
            Log::info('payment has been confirmed');
            $user = User::whereEmail($this->email)->first();
            $applicationFeeConfiguration = PaymentConfiguration::where('payment_purpose_slug', 'application-fee')->first();

            $feeRequest = ApplicationFeeRequest::where('payee_id', $user->id)->first();

            $feePaymentTransaction = FeePayment::create([
                'amount_billed' => $feeRequest->amount,
                'user_id' => $user->id,
                'payment_config_id' => $applicationFeeConfiguration->id,
                'academic_session_id' => activeSession()->id,
                'payment_status' => config('app.status.paid'),
                'amount_paid' => $feeRequest->amount,
                'uid' => uniqid('fp_'),
                'balance' => 0,
                'txn_id' => generateUniqueTransactionReference(), // change the transaction id to avoid replay attacks
            ]);

            PaymentLog::create([
                'fee_payment_id' => $feePaymentTransaction->id,
                'amount_paid' => $feePaymentTransaction->amount_paid,
                'uid' => uniqid('pl_'),
                'payment_channel' => config('app.payment_methods.e-tranzact')
            ]);

            // genericMail($emailSubject, $validPaymentMessage, $this->email);
        } else {
            FeePayment::where('txn_id', $this->transactionId)->where('checksum', $this->checksum)->update([
                'payment_status' => 'pending'
            ]);
            // genericMail($emailSubject, $invalidPaymentMessage, $this->email);
        }
    }
}
