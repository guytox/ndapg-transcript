<?php

namespace App\Jobs;

use App\Models\ApplicantAdmissionRequest;
use App\Models\FeeConfig;
use App\Models\FeePayment;
use App\Models\FeePaymentItem;
use App\Models\FeeTemplate;
use App\Models\FeeTemplateItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FirstTuitionGenerationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $appId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($appId)
    {
        $this->appId = $appId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        #get the appId
        $appStd = ApplicantAdmissionRequest::find($this->appId);
        $stdProg = $appStd->program_id;
        #get the fee config for the student
        $proFeeConfig = FeeConfig::join('fee_categories as f','f.id','=','fee_configs.fee_category_id')
                                ->where('program_id', $stdProg)
                                ->where('f.payment_purpose_slug', 'first-tuition')
                                ->select('fee_configs.*')
                                ->first();
        #if found create, if not roll back the screening
        if ($proFeeConfig) {
            #first get the template
            $fTmpl = FeeTemplate::find($proFeeConfig->fee_template_id);
            # fee found apply fee to the student after collecting the necessary data
            $data =[
                'user_id'=> $appStd->user_id,
                'uid' => uniqid('ftn'),
                'payment_config_id' => $proFeeConfig->id,
                'academic_session_id' => getApplicationSession(),
                'amount_billed' => $fTmpl->total_amount,
                'txn_id' => generateUniqueTransactionReference()
            ];

            $rFirstTuition = FeePayment::updateOrCreate([
                'user_id'=> $appStd->user_id,
                'payment_config_id' => $proFeeConfig->id,
            ], $data);

            #next get the items and populate
            $templateItems = FeeTemplateItem::where('fee_template_id', $fTmpl->id)->get();
            foreach ($templateItems as $v) {
                #make entries in to the table for the candidate
                $itemsData = [
                    'fee_payment_id' => $rFirstTuition->id,
                    'fee_item_id' => $v->fee_item_id,
                    'amount' => $v->item_amount
                ];

                $entry = FeePaymentItem::updateOrCreate($itemsData, $itemsData);
            }

        }else{
            #rollback
            $appStd->is_screened=0;
            $appStd->is_screened_at = null;
            $appStd->is_screened_by = null;
            $appStd->save();
        }

    }
}
