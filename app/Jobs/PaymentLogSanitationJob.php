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

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        #find the entry using the id
        $plog = PaymentLog::find($this->id);
        #$check = PaymentLog::where('fee_payment_id', $plog->fee_payment_id)->get();


            #find the payment
            $fp = FeePayment::find($plog->fee_payment_id);
            $fp->amount_paid = $plog->amount_paid;
            $fp->save();

            $fp->balance = $fp->amount_billed - $fp->amount_paid;
            $fp->save();

            if ($fp->balance == 0) {
                # flag as paid
                $fp->payment_status = 'paid';
               # return "yes we flagged it paid";
            }else{

                $fp->payment_status = 'pending';
                #return "we flagged it pending";
            }
    }
}
