<?php

namespace App\Jobs;

use App\Models\ApplicantAdmissionRequest;
use App\Models\FeePayment;
use App\Models\RegClearance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ConfirmTuitionFeePaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $transactionId;
    public $time;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($transactionId, $time)
    {
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
        #get the fee payment
        $feePayment = FeePayment::find($this->transactionId);

        if ($feePayment ) {
            if ($feePayment->payment_status == 'paid') {
                if ($feePayment->config->feeCategory->payment_purpose_slug == 'tuition') {
                    
                    Log::info('Tuition Fee Confirmation Successful for - '.$feePayment->user->username);

                    #next update the reg_clearance
                    RegClearance::updateOrCreate([
                        'student_id' => $feePayment->user->student->id,
                        'school_session_id' => $feePayment->academic_session_id,
                    ],[
                        'student_id' => $feePayment->user->student->id,
                        'school_session_id' => $feePayment->academic_session_id,
                        'first_semester' => 1,
                        'second_semester' => 1,
                        'status' => 1
                    ]);

                }
            }elseif ($feePayment->amount_paid > 0) {
                #something has been paid, check and allow registration for first semester alone
                #next update the reg_clearance
                RegClearance::updateOrCreate([
                    'student_id' => $feePayment->user->student->id,
                    'school_session_id' => $feePayment->academic_session_id,
                ],[
                    'student_id' => $feePayment->user->student->id,
                    'school_session_id' => $feePayment->academic_session_id,
                    'first_semester' => 1,
                    'second_semester' => 1,
                    'status' => 1
                ]);
            }
        }
    }
}
