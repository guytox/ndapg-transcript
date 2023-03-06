<?php

namespace App\Jobs;

use App\Models\RegMonitorItems;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RegradeAllRegisteredCoursesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $toregrade = RegMonitorItems::all();

        foreach ($toregrade as $v) {

            $dTime = Carbon::now()->addMinutes(1);
            SemesterCourseGradingJob::dispatch($v->id);

            $wTime = Carbon::now()->addMinutes(2);
            SemesterCourseSessionalGradingJob::dispatch($v->id)->delay($wTime);



        }
    }
}
