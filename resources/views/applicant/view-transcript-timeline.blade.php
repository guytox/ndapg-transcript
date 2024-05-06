

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>{{env('APP_NAME')}}</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">

        <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
        <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
        <link rel="stylesheet" href="{{asset('assets/css/timeline.css')}}">
        <!------ Include the above in your HEAD tag ---------->


    </head>
    <body>


        <div class="container">
            <h4>Transcript Request To: <br> <b class="text text-danger">{{$reQuestDetails->details->receiver}}</b> <br> ***Message: {{$reQuestDetails->ug_mssg}} ***</h4>
            <div class="row">
                <div class="col-md-12">
                    <div class="main-timeline4">
                        <div class="timeline">
                            <a href="#" class="timeline-content">
                                <span class="year">&#9989;</span>
                                <div class="inner-content">
                                    <h3 class="title">Request Initiated</h3>
                                    <p class="description">
                                        {{$reQuestDetails->created_at}}
                                    </p>
                                </div>
                            </a>
                        </div>

                        @if ($reQuestDetails->feepayment->payment_status =='paid')
                            <div class="timeline">
                                <a href="#" class=" ">
                                    <span class="year">&#9989;</span>
                                    <div class="inner-content">
                                        <h3 class="title">Payment Processed</h3>
                                        <p class="description">
                                                Your Payment was confirmed at {{$reQuestDetails->feepayment->updated_at}} @if ($reQuestDetails->ts !=1)
                                            @endif
                                        </p>
                                    </div>
                                </a>
                            </div>
                        @elseif ($reQuestDetails->feepayment->payment_status =='pending')
                            <div class="timeline">
                                <a href="#" class="timeline-content">
                                    <span class="year">&#10060;</span>
                                    <div class="inner-content">
                                        <h3 class="title">Payment</h3>
                                        <p class="description">
                                            Payment Details not found, Please proceed to Pay and Verify Payment
                                        </p>
                                    </div>
                                </a>
                            </div>
                        @endif

                        @if ($reQuestDetails->ts ==1)
                            <div class="timeline">
                                <a href="#" class="timeline-content">
                                    <span class="year">&#9989;</span>
                                    <div class="inner-content">
                                        <h3 class="title">Request Submitted</h3>
                                        <p class="description">
                                            This request was submitted at {{$reQuestDetails->ts_at}}. There is no action required from you.
                                        </p>
                                    </div>
                                </a>
                            </div>
                        @elseif ($reQuestDetails->ts ==0)
                            <div class="timeline">
                                <a href="#" class="timeline-content">
                                    <span class="year">&#10060;</span>
                                    <div class="inner-content">
                                        <h3 class="title">Request Submitted</h3>
                                        <p class="description">
                                            Request not submitted, Please Proceed to Submit
                                        </p>
                                    </div>
                                </a>
                            </div>
                        @endif

                        @if ($reQuestDetails->tp ==1)
                            <div class="timeline">
                                <a href="#" class="timeline-content">
                                    <span class="year">&#9989;</span>
                                    <div class="inner-content">
                                        <h3 class="title">Transcript Processed</h3>
                                        <p class="description">
                                            {{$reQuestDetails->tp_at}}
                                        </p>
                                    </div>
                                </a>
                            </div>
                        @elseif ($reQuestDetails->tp ==0)
                            <div class="timeline">
                                <a href="#" class="timeline-content">
                                    <span class="year">&#10060;</span>
                                    <div class="inner-content">
                                        <h3 class="title">Transcript Processed</h3>
                                        <p class="description">
                                            Transcript yet to be processed
                                        </p>
                                    </div>
                                </a>
                            </div>
                        @endif

                        @if ($reQuestDetails->tv ==1)
                            <div class="timeline">
                                <a href="#" class="timeline-content">
                                    <span class="year">&#9989;</span>
                                    <div class="inner-content">
                                        <h3 class="title">Transcript Verification</h3>
                                        <p class="description">
                                            {{$reQuestDetails->tv_at}}
                                        </p>
                                    </div>
                                </a>
                            </div>
                        @elseif ($reQuestDetails->tv ==0)
                            <div class="timeline">
                                <a href="#" class="timeline-content">
                                    <span class="year">&#10060;</span>
                                    <div class="inner-content">
                                        <h3 class="title">Transcript Verification</h3>
                                        <p class="description">
                                            Transcript yet to be Verified
                                        </p>
                                    </div>
                                </a>
                            </div>
                        @endif

                        @if ($reQuestDetails->td ==1)
                            <div class="timeline">
                                <a href="#" class="timeline-content">
                                    <span class="year">&#9989;</span>
                                    <div class="inner-content">
                                        <h3 class="title">Transcript Dispatch</h3>
                                        <p class="description">
                                            {{$reQuestDetails->td_at}}
                                        </p>
                                    </div>
                                </a>
                            </div>
                        @elseif ($reQuestDetails->td ==0)
                            <div class="timeline">
                                <a href="#" class="timeline-content">
                                    <span class="year">&#10060;</span>
                                    <div class="inner-content">
                                        <h3 class="title">Transcript Dispatch</h3>
                                        <p class="description">
                                            Transcript yet to be Dispatched
                                        </p>
                                    </div>
                                </a>
                            </div>
                        @endif

                        @if ($reQuestDetails->tr ==1)
                            <div class="timeline">
                                <a href="#" class="timeline-content">
                                    <span class="year">&#9989;</span>
                                    <div class="inner-content">
                                        <h3 class="title">Transcript Received</h3>
                                        <p class="description">
                                            {{$reQuestDetails->tr_at}}
                                        </p>
                                    </div>
                                </a>
                            </div>
                        @elseif ($reQuestDetails->tr ==0)
                            <div class="timeline">
                                <a href="#" class="timeline-content">
                                    <span class="year">&#10060;</span>
                                    <div class="inner-content">
                                        <h3 class="title">Transcript Received</h3>
                                        <p class="description">
                                            Transcript yet to be Received
                                        </p>
                                    </div>
                                </a>
                            </div>
                        @endif






                    </div>
                </div>
            </div>
        </div>
        <hr>



        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    </body>
</html>
