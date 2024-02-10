<?php

namespace App\Jobs;

use App\Models\StudentRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SubmitForVetoRegJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $sessionId;
    public $semesterId;
    public $studentId;
    public $time;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($sessionId, $semesterId, $studentId,$time)
    {
        $this->sessionId = $sessionId;
        $this->semesterId = $semesterId;
        $this->studentId = $studentId;
        $this->time = $time;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        #first find the student
        $std = StudentRecord::find($this->studentId);

        if ($std) {
            # Student found you can now foward the job
            #std found proceed
            #set the variables
            $sessionId = $this->sessionId;
            $semesterId = $this->semesterId;
            $studentId = $this->studentId;
            $time = now();

            VetoRegistrationJob::dispatch($sessionId, $semesterId, $studentId,$time);
        }
    }
}
