<?php

namespace App\Jobs;

use App\Models\ComputedResult;
use App\Models\RegMonitor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ComputedResultFixJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $regMonitorId;
    public $time;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($regMonitorId, $time)
    {
        $this->regMonitorId = $regMonitorId;
        $this->time = $time;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        # first get the reg Monitor from which to search
        $result = RegMonitor::find($this->regMonitorId);

        if ($result) {
            #find the neccessary result computation for this retistration
            if ($result->r_computed_result_id == '') {
                # there is no computed result id updated on this, get it and update
                #find if it exist and update it on the regMonitor or Create and update the regMonitor
                $oldComputeResult = ComputedResult::where('schoolsession_id', $result->session_id)
                                                    ->where('semester_id', $result->semester_id)
                                                    ->where('program_id', $result->program_id)
                                                    ->where('study_level', $result->level_id)
                                                    ->first();

                if ($oldComputeResult) {
                    # this result is found, update the regMonitor and be on your way
                    $result->r_computed_result_id = $oldComputeResult->id;
                    $result->save();

                    #update the compute result entry and be ready to move
                    $oldComputeResult->last_updated_at = now();
                    $oldComputeResult->save();

                    Log::info('Old Computed Result id = '.$oldComputeResult->id);


                }else {
                    # This is the first time so cretae a new entry and be on your way
                    $newData =[
                        'uid' => uniqid('cmpr_'),
                        'program_id' => $result->program_id,
                        'schoolsession_id' => $result->session_id,
                        'semester_id' => $result->semester_id,
                        'study_level' => $result->level_id,
                        'computed_by' => $result->programme->department->exam_officer_id ,
                        'computed_at' => now(),
                        'last_updated_at' => now(),
                    ];

                    $newComputeResult = ComputedResult::updateOrCreate([
                        'program_id' => $result->program_id,
                        'schoolsession_id' => $result->session_id,
                        'semester_id' => $result->semester_id,
                        'study_level' => $result->level_id,
                    ], $newData);


                    #now created update the result entry now with the created id
                    $result->r_computed_result_id = $newComputeResult->id;
                    $result->save();

                    Log::info('New Computed Result id = '.$newComputeResult->id);

                }


            }elseif ($result->r_computed_result_id > 0) {
                # There is a result compute_id so just find it and update the last updated value;

                $CompResult = ComputedResult::find($result->r_computed_result_id);
                if ($CompResult) {
                    $CompResult->last_updated_at = now();
                    $CompResult->save();

                    Log::info('Updated Computed ResultEntry = '.$CompResult->id);
                }else {
                    # This computed result was not found, let us correct it then though very rare
                    $freshData =[
                        'uid' => uniqid('cmpr_'),
                        'program_id' => $result->program_id,
                        'schoolsession_id' => $result->session_id,
                        'semester_id' => $result->semester_id,
                        'study_level' => $result->level_id,
                        'computed_by' => $result->programme->department->exam_officer_id ,
                        'computed_at' => now(),
                        'last_updated_at' => now(),
                    ];

                    $createdComputeResult = ComputedResult::updateOrCreate([
                        'program_id' => $result->program_id,
                        'schoolsession_id' => $result->session_id,
                        'semester_id' => $result->semester_id,
                        'study_level' => $result->level_id,
                    ], $freshData);

                    #now created update the result entry now with the created id
                    $result->r_computed_result_id = $createdComputeResult->id;
                    $result->save();

                    Log::info('Fresh Computed Result id = '.$createdComputeResult->id);

                }

            }


        }else {
            # This registration entry is not found, No action required
        }
    }
}
