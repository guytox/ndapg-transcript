<?php

namespace App\Jobs;

use App\Models\PaymentLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CredoRequestEnterPaymentLogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $feePaymentId;
    public $transUid;
    public $transAmount;
    public $paymentChannel;
    public $transactionId;
    public $time;



    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($feePaymentId, $transUid, $transAmount, $paymentChannel, $transactionId, $time)
    {
        $this->feePaymentId = $feePaymentId;
        $this->transUid = $transUid;
        $this->transAmount = $transAmount;
        $this->paymentChannel = $paymentChannel;
        $this->transactionId = $transactionId;
        $this->time = $time;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        #enter the payment log
        $pData = [
            'fee_payment_id' => $this->feePaymentId,
            'uid' => $this->transUid,
            'amount_paid' => $this->transAmount,
            'payment_channel' => $this->paymentChannel,
            'tx_id' => $this->transactionId,
        ];

        $newPayLog = PaymentLog::updateOrCreate(['uid' => $this->transUid], $pData);
        # Entry made, sanitize the log before you go
        PaymentLogSanitationJob::dispatch($newPayLog->id, $this->feePaymentId, $this->time);
        # you are done

    }
}
