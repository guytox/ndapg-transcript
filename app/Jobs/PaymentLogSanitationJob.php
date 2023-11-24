<?php

namespace App\Jobs;

use App\Models\FeePayment;
use App\Models\PaymentLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PaymentLogSanitationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $id;
    public $paymentId;
    public $time;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id, $paymentId,  $time)
    {
        $this->id = $id;
        $this->paymentId = $paymentId;
        $this->time = $time;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        #find the entry using the id
        $plogs = PaymentLog::where('fee_payment_id', $this->paymentId)->get();

        $totalPaid = 0;

        foreach ($plogs as $p ) {
            $totalPaid = $totalPaid + $p->amount_paid;

        }
        #$check = PaymentLog::where('fee_payment_id', $plog->fee_payment_id)->get();


            #find the payment
            $fp = FeePayment::find($this->paymentId);

            $balance = $fp->amount_billed - $totalPaid;

            $fp->amount_paid = $totalPaid;
            $fp->balance = $balance;
            $fp->save();

            if ($balance <= 0) {
                # flag as paid
                $fp->payment_status = 'paid';
                GrantPaymentRequestJob::dispatch($fp->id, $this->time);
               # return "yes we flagged it paid";

            }else{

                $fp->payment_status = 'pending';
                GrantPaymentRequestJob::dispatch($fp->id, $this->time);
                #return "we flagged it pending";
            }

            $fp->save();
    }
}
