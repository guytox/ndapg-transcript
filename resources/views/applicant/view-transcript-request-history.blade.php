@extends('layouts.setup')

@section('content')

    <h1>Transcript Request History</h1>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-4"></h4>

                    @include('includes.messages')

                    <div class="table-responsive">
                        <table class="table table-striped table-centered table-nowrap mb-0">
                            <thead>
                                <tr>

                                    <th scope="col">S/N</th>
                                    <th scope="col">Matric</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Paid</th>
                                    <th scope="col">Submitted</th>
                                    <th scope="col">Processed</th>
                                    <th scope="col">Dispatched</th>
                                    <th scope="col">Received</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $sn =1;
                                @endphp
                                @foreach ($previousRequests as $v)

                                    <tr>
                                        <td>{{$sn}}</td>
                                        <td>{{ $v->matric}}</td>
                                        <td>{{ $v->type->type_name}}</td>
                                        <td>
                                            @if ($v->feePayment->payment_status ==='pending')
                                                <span title="Payment Confirmation" > &#10060;</span>
                                                <a href="{{ route('verify.transcript.request.payment', ['id'=>$v->uid]) }}" class="btn btn-danger">VerifyPayment</a>
                                            @elseif ($v->feePayment->payment_status ==='paid')
                                                <span title="Payment Confirmation" > &#9989;</span>
                                            @endif
                                        </td>   
                                        <td>
                                            @if ($v->ts===0)
                                                <span title="Transcript Request Submission" > &#10060;</span>

                                                @if ($v->p == 1)
                                                <a href="{{ route('submit.transcript.request', ['id'=>$v->uid]) }}" class="btn btn-danger">Submit Transcript Request</a>
                                                @endif
                                            @elseif ($v->ts===1)
                                                <span title="Transcript Request Submission" > &#9989;</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($v->tp===0)
                                                <span title="Transcript Processed" > &#10060;</span>
                                            @elseif ($v->tp===1)
                                                <span title="Transcript Processed" > &#9989;</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($v->td===0)
                                                <span title="Transcript Dispatched" > &#10060;</span>
                                            @elseif ($v->td===1)
                                                <span title="Transcript Dispatched" > &#9989;</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($v->tr===0)
                                                <span title="Transcript Received" > &#10060;</span>
                                            @elseif ($v->tr===1)
                                                <span title="Transcript Received" > &#9989;</span>
                                            @endif
                                        </td>

                                        <td>

                                            <a class="btn btn-primary" target="_blank" href="{{ route('view.transcript.timeline', ['id'=>$v->uid]) }}">Click to View Tracker</a>

                                        </td>
                                    </tr>

                                    @php
                                        $sn++;
                                    @endphp

                                @endforeach
                            </tbody>
                        </table>



                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end row -->

    <script>
        @if(session()->has('error'))
          alert('{{session()->get('error')}}')
        @endif
    </script>

@endsection
