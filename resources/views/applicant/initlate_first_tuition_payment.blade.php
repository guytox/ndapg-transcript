@extends('layouts.setup')


@section('content')


<div class="row">
    <div class="col-xl-5">
        <div class="card">
            @include('includes.messages')
            <div class="card-body">


                <h2 class="header-title">Initiate Tuition Payment</h2>

                <hr>

                {!! Form::open(['route'=>'first.tuition.fee', 'method'=>'POST'] ) !!}

                {!! Form::hidden('usr', $appData->user_id, []) !!}
                {!! Form::hidden('fConfig', $accConfig->id, []) !!}

                <div class="form-group">
                    {!! Form::label('pAmount', "Enter the Amount You are paying (note: must be above 50% of fees)", []) !!}
                    {!! Form::number('pAmount', $maxValue, ['min'=>$minValue,'max'=>$maxValue, 'class'=>'form-control', 'required']) !!}
                </div>



                {!! Form::submit('Proceed to Pay', ['class'=>'form-control btn btn-danger']) !!}

                {!! Form::close() !!}

                <hr>
                <p><b><u>Payment Instructions</u></b></p>

                <ol>
                    <li>You will be required to make payment online with your ATM Card</li>
                    <li>Clicking on Proceed to Pay above will take you to the payment page</li>
                    <li class="text-danger"> <b>Note: Part Payment of Fees is only for Professional Programs</b> </li>
                </ol>

            </div>

        </div>




    </div>

    <div class="col-xl-6">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title">Payment Details</h4>
                <h3>{{$fConfig->narration}}</h3>

                    <hr>

                    <hr>

                <h3>Amount: NGN {{number_format($maxValue,2)}}</h3>

                @if (count($pcrequest) >=1)

                <hr>
                <h4 class="header-title">Previous Payment Attempts</h4>

                    @foreach ($pcrequest as $m)

                        {{$m->created_at}} => NGN{{number_format($m->amount,2)}}

                        @if ($m->status == 'pending')

                        => <a href="{{route('reprocess.credo.payment',['id'=>$m->id])}}" class="btn btn-danger">Reprocess Payment</a>

                        @endif

                        @if ($m->status == 'pending')
                            <br>
                                    OR
                            <br>

                            <a href="{{route('delete.credo.request',['id'=>$m->id])}}" class="btn btn-success">Delete Transaction</a>(*****NOTE: Do not delete a transaction you have paid for ****)

                        @endif

                    @endforeach

                @endif


                <hr>

                @if (count($pLogs))

                    <h4 class="header-title">Previous Successful Payments</h4>

                    @foreach ($pLogs as $pl)
                        Date: {{$pl->created_at}} => Ref:{{$pl->uid}} => Amount: NGN{{number_format(convertToNaira($pl->amount_paid),2)}}
                    @endforeach

                @endif



                    @if (count($errors)>0)

                <div class="alert-danger">

                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{$error}}</li>

                        @endforeach
                        <li>Logout and Login again</li>
                    </ul>
                </div>

                @endif







            </div>
        </div>

    </div>


</div>
<!-- end row -->

@endsection
