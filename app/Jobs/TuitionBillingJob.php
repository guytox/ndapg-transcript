<?php

namespace App\Jobs;

use App\Models\BursarsApprovalQueue;
use App\Models\FeeConfig;
use App\Models\FeePayment;
use App\Models\StudentRecord;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TuitionBillingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $staffId;
    public $studentId;
    public $sessionId;
    public $time;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($staffId, $studentId, $sessionId, $time)
    {
        $this->staffId = $staffId;
        $this->studentId = $studentId;
        $this->sessionId = $sessionId;
        $this->time = $time;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        # first find the staff
        $owner = User::find($this->staffId);

        if ($owner ) {
            # staff found let's continue
            # find the student
            $student = StudentRecord::find($this->studentId);
            if ($student ) {
                # student found, proceed to fetch payment records
                $feeConfig = FeeConfig::join('fee_categories as f', 'f.id','=','fee_configs.fee_category_id')
                                        ->where('fee_configs.program_id', $student->program_id)
                                        ->where('f.payment_purpose_slug', 'tuition')
                                        ->select('fee_configs.*')
                                        ->first();
                if ($feeConfig ) {
                    # free config found
                    $data = [
                        'user_id' => $student->user_id,
                        'uid' => uniqid('ftn'),
                        'payment_config_id' => $feeConfig->id,
                        'academic_session_id' => $this->sessionId,
                        'amount_billed' => $feeConfig->feeTemplate->total_amount,
                        'txn_id' => generateUniqueTransactionReference(),
                        'balance' => $feeConfig->feeTemplate->total_amount,
                        'billing_by' => $this->staffId,
                        'channel' => 'credo'
                    ];

                    $newLog = FeePayment::updateOrCreate([
                        'user_id' => $student->user_id,
                        'academic_session_id' => $this->sessionId,
                        'payment_config_id' => $feeConfig->id,
                    ], $data);

                    # we're done from here we can move

                    #enter the log

                    if ($newLog ) {
                        # payment was successful, enter the log
                        $newData = [
                            'user_id' => $newLog->user_id,
                            'uid' => $newLog->uid,
                            'payment_config_id' => $newLog->payment_config_id,
                            'academic_session_id' => $newLog->academic_session_id,
                            'amount_billed' => $newLog->amount_billed,
                            'txn_id' => $newLog->txn_id,
                            'billing_by' => $newLog->billing_by,
                        ];

                        BursarsApprovalQueue::updateOrCreate([
                            'user_id' => $newLog->user_id,
                            'payment_config_id' => $newLog->payment_config_id,
                            'academic_session_id' => $newLog->academic_session_id,
                        ],$newData);
                    }


                }
            }
        }
    }
}
