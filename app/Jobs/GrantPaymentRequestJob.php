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

        $fpayment = $feeType->config->feeCategory->payment_purpose_slug;

        switch ($fpayment) {
            case 'first-tuition':
                    # get the session and semester
                    $academicSession = getsessionById($feeType->academic_session_id);
                    # create Student record entry

                        #next update the Applicant admission table since this is first tuition
                        $appInfo = ApplicantAdmissionRequest::where('user_id',$feeType->user_id)
                                                            ->where('session_id', $feeType->academic_session_id)
                                                            ->first();

                        $appInfo->is_paid_tuition = 1;
                        $appInfo->paid_tuition_at = now();
                        $appInfo->save();

                        #treat reg Clearance

                    if ($academicSession ) {
                        if ($academicSession->currentSemester == 'first'  && $feeType->balance == 0) {
                            $aData = [
                                'student_id' => $feeType->user->student->id,
                                'school_session_id' => $feeType->academic_session_id,
                                'first_semester' => 1,
                                'second_semester' => 1,
                                'status' => 1
                            ];

                            RegClearance::updateOrCreate([
                                'student_id' => $feeType->user->student->id,
                                'school_session_id' => $feeType->academic_session_id,
                            ], $aData);

                        }elseif ($academicSession->currentSemester == 'first'  && $feeType->amount_paid >0) {
                            $fData = [
                                'student_id' => $feeType->user->student->id,
                                'school_session_id' => $feeType->academic_session_id,
                                'first_semester' => 1,
                                'second_semester' => 0,
                                'status' => 1
                            ];

                            RegClearance::updateOrCreate([
                                'student_id' => $feeType->user->student->id,
                                'school_session_id' => $feeType->academic_session_id,
                            ], $fData);

                        }elseif ($academicSession->currentSemester == 'second'  && $feeType->balance ==0) {
                            $sData = [
                                'student_id' => $feeType->user->student->id,
                                'school_session_id' => $feeType->academic_session_id,
                                'first_semester' => 1,
                                'second_semester' => 1,
                                'status' => 1
                            ];

                            RegClearance::updateOrCreate([
                                'student_id' => $feeType->user->student->id,
                                'school_session_id' => $feeType->academic_session_id,
                            ], $sData);
                        }
                    }


                break;
            case 'tuition':
                    # get the session and semester
                    $academicSession = getsessionById($feeType->academic_session_id);
                    # create Student record entry

                    if ($academicSession ) {
                        if ($academicSession->currentSemester == 'first'  && $feeType->balance == 0) {
                            $aData = [
                                'student_id' => $feeType->user->student->id,
                                'school_session_id' => $feeType->academic_session_id,
                                'first_semester' => 1,
                                'second_semester' => 1,
                                'status' => 1
                            ];

                            RegClearance::updateOrCreate([
                                'student_id' => $feeType->user->student->id,
                                'school_session_id' => $feeType->academic_session_id,
                            ], $aData);

                        }elseif ($academicSession->currentSemester == 'first'  && $feeType->amount_paid >0) {
                            $fData = [
                                'student_id' => $feeType->user->student->id,
                                'school_session_id' => $feeType->academic_session_id,
                                'first_semester' => 1,
                                'second_semester' => 0,
                                'status' => 1
                            ];

                            RegClearance::updateOrCreate([
                                'student_id' => $feeType->user->student->id,
                                'school_session_id' => $feeType->academic_session_id,
                            ], $fData);

                        }elseif ($academicSession->currentSemester == 'second'  && $feeType->balance ==0) {
                            $sData = [
                                'student_id' => $feeType->user->student->id,
                                'school_session_id' => $feeType->academic_session_id,
                                'first_semester' => 1,
                                'second_semester' => 1,
                                'status' => 1
                            ];

                            RegClearance::updateOrCreate([
                                'student_id' => $feeType->user->student->id,
                                'school_session_id' => $feeType->academic_session_id,
                            ], $sData);
                        }
                    }

                break;

            case 'late-registration':
                # code...
                break;
            case 'application-fee':


                break;
            case 'acceptance-fee':
                        #next update the Applicant admission table since this is acceptance
                        if ($feeType->payment_status ==  'paid') {

                            $appInfo = ApplicantAdmissionRequest::where('user_id',$feeType->user_id)
                                                            ->where('session_id', $feeType->academic_session_id)
                                                            ->first();
                            $appInfo->acceptance_paid = 1;
                            $appInfo->acceptance_paid_at = now();
                            $appInfo->save();
                        }
                break;
            case 'wallet-fund':
                # code...
                break;
            case 'spgs-charges':
                # code...
                break;

            default:
                # code...
                break;
        }




    }
}
