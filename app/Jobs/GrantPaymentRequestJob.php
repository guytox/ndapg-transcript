<?php

namespace App\Jobs;

use App\Models\FeePayment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GrantPaymentRequestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $feePaymentId;
    public $time;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($feePaymentId, $time)
    {
        $this->feePaymentId = $feePaymentId;
        $this->time = $time;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $feeType = FeePayment::find($this->feePaymentId);

        $fpayment = FeePayment::join('fee_configs as f','f.id','=','fee_payments.payment_config_id')
                                    ->join('fee_categories as c','c.id','=','f.fee_category_id')
                                    ->where('fee_payments.id',$pDetails->fee_payment_id)
                                    ->first();


    }
}
