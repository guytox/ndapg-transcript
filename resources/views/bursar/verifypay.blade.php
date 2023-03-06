@extends('layouts.apply')


@section('content')


    <div class="row">





        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">


                        <h4 class="header-title"> Etranzact payment Page</h4>
                            <p class="card-title-desc">Note: <code>**(You will be redirected to the payment gateway to complete payment)</code></p>

                            <table class="table-control">

                                <tr>
                                    <td>Full Name : </td>
                                    <td> <b>{{$FULL_NAME}} </b></td>
                                </tr>

                                <tr>
                                    <td>Total Amount to pay : </td>
                                    <td> <b>{{$AMOUNT}} </b></td>
                                </tr>

                                <tr>
                                    <td>Payment Description : </td>
                                    <td> <b>{{$DESCRIPTION}} </b></td>
                                </tr>

                            </table>

                                {!! Form::open(['url' => 'https://demo.etranzact.com/webconnect/v3/caller.jsp', 'method' => 'POST']) !!}


                                {!! Form::hidden('TERMINAL_ID', $TERMINAL_ID) !!}
                                {!! Form::hidden('TRANSACTION_ID', $TRANSACTION_ID) !!}
                                {!! Form::hidden('AMOUNT', $AMOUNT) !!}
                                {!! Form::hidden('DESCRIPTION', $DESCRIPTION) !!}
                                {!! Form::hidden('EMAIL', $EMAIL) !!}
                                {!! Form::hidden('CURRENCY_CODE', $CURRENCY_CODE) !!}
                                {!! Form::hidden('RESPONSE_URL', $RESPONSE_URL) !!}
                                {!! Form::hidden('CHECKSUM', $CHECKSUM) !!}
                                {!! Form::hidden('FULL_NAME', $FULL_NAME) !!}
                                {!! Form::hidden('LOGO_URL', $LOGO_URL) !!}
                                {!! Form::hidden('PHONENO', $PHONENO) !!}
                                {!! Form::hidden('PAYEE_ID', $CUSTOMER_ID) !!}

                                <hr>

                                {!! Form::submit('Proceed to make pay Online',['class' => 'form-control']) !!}

                                {!! Form::close() !!}

                                <br>

                                <span class="logo-lg">
                                    <img src="{{asset('images/etranzact/etzLogo.PNG')}}" alt="" height="40">
                                </span>

                                <span class="logo-lg">
                                    <img src="{{asset('images/etranzact/webconnectLogo.PNG')}}" alt="" height="40">
                                </span>

                                <span class="logo-lg">
                                    <img src="{{asset('images/etranzact/MasterCard.png')}}" alt="" height="40">
                                </span>

                                <span class="logo-lg">
                                    <img src="{{asset('images/etranzact/Verve.png')}}" alt="" height="40">
                                </span>

                                <span class="logo-lg">
                                    <img src="{{asset('images/etranzact/Visa.png')}}" alt="" height="40">
                                </span>

                                <span class="logo-lg">
                                    <img src="{{asset('images/etranzact/PocketMoni.png')}}" alt="" height="40">
                                </span>






                </div>
            </div>

        </div>


    </div>
    <!-- end row -->

@endsection
