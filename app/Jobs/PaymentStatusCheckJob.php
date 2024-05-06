<?php

namespace App\Jobs;

use App\Models\FeePayment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PaymentStatusCheckJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $feePaymentUId;
    public $JobTime;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($feePaymentUId, $JobTime)
    {
        $this->feePaymentUId = $feePaymentUId;
        $this->JobTime = $JobTime;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        #grab the feePayment_id;
        $toVerify = FeePayment::where('uid', $this->feePaymentUId)->first();
        if ($toVerify->balance == 0) {
            $toVerify->payment_status = 'paid';
            $toVerify->save();
            #get the purpose of the payment
            switch ($toVerify->config->feeCategory->payment_purpose_slug) {

            case 'application-fee':
            ConfirmApplicationFeePaymentJob::dispatch($toVerify->fee_payment_id, $this->JobTime);
            break;

            case 'acceptance-fee':
            ConfirmAcceptanceFeePaymentJob::dispatch($toVerify->fee_payment_id, $this->JobTime);
            break;

            case 'first-tuition':
            ConfirmFirstTuitionFeePaymentJob::dispatch($toVerify->fee_payment_id, $this->JobTime);
            break;

            case 'late-registration':
            return config('app.credo.serviceCode.lateRegistration');
            break;

            case 'tuition':
            ConfirmTuitionFeePaymentJob::dispatch($toVerify->fee_payment_id, $this->JobTime);
            break;

            case 'portal-services':
            return config('app.credo.serviceCode.ExtraCharges');
            break;

            case 'spgs-charges':
            return config('app.credo.serviceCode.ExtraCharges');
            break;

            case 'ug-transcript':
                    ConfirmUgTranscriptFeeJob::dispatch($toVerify->uid, now());
                break;

            case 'pg-transcript':
                    ConfirmPgTranscriptFeeJob::dispatch($toVerify->uid, now());
                break;

            default:
            return config('app.credo.serviceCode.ExtraCharges');
            break;
            }
        }
    }
}
