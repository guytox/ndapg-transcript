<?php

namespace App\Jobs;

use App\Models\TranscriptRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FowardUGTranscriptRequestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $transcriptRequestUid;
    public $JobTime;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($transcriptRequestUid, $JobTime)
    {
        $this->transcriptRequestUid = $transcriptRequestUid;
        $this->JobTime = $JobTime;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        #grab the request instance and begin the process
        #verify the matric number
        $feePymnt = TranscriptRequest::where('uid', $this->transcriptRequestUid)->first();

        #configure the headers now
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => env('UG_TX_PUB_KEY '),
        ];

        $body =[
            'matric' => $feePymnt->matric,
            'transcript_ref' => $feePymnt->uid,
            'token' => hash('sha256', env('UG_TX_PUB_KEY').$feePymnt->matric.env('UG_TX_APP_KEY')),
            'public_key' => env('UG_TX_PUB_KEY'),
        ];

        $client = new \GuzzleHttp\Client();

        # These setting are for the live environment
        $response = $client->request('GET', 'http://127.0.0.1:8001/api/checkmatric',[
            'headers' => $headers,
            'json' => $body
        ]);


        $ugResponse = json_decode($response->getBody());

        if ($ugResponse->token == hash('sha256', env('UG_TX_PUB_KEY').$ugResponse->tx_ref.env('UG_TX_APP_KEY'))) {
            # this request is valid, update the records and proceed
            if ($ugResponse->tx_ref != 0) {
                 #now update the request to submitted since there's a response from ug portal
                 $feePymnt->ts = 1;
                 $feePymnt->ug_ref = $ugResponse->tx_ref;
                 $feePymnt->ts_at = now();
                 $feePymnt->ug_mssg = "Request Successfully Submitted";
                 $feePymnt->save();


            }elseif($ugResponse->tx_ref != 0){
                #this student matric number was not found so the request was submitted as a failed request, so notify the end user of the latest message
                $feePymnt->ug_mssg = "Matric Number Not Found";
                $feePymnt->save();
            }
        }else {
            # request is invalid, you may want to log this incidence for analysis
            Log::info("UG Matric Number Not Found :- ". $feePymnt->matric);
        }

        #create the processing request to share with the UG server
        #update the submission status to this request
        # job complete
    }
}
