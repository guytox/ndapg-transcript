@extends('layouts.auth')

@section('content')

    <h5 class="mb-5 text-center text-danger"> &#9989; Payment verified</h5>

    <form class="form-horizontal" method="GET" action="{{route('home')}}">

        <div class="row">
            <div class="col-lg-12">
                <div class="text-center mb-5">
                    <a href="" class="logo"><img src="{{asset('/images/passport/'.$validationDetails[0]['passport'])}}" alt="logo" height="250" width="200"></a>
                    <br>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-12">
                        <h5 class= "text-center text text-danger">Paymet Reference : <u>{{ $validationDetails[0]['transactionReference'] }}</u> </h5>
                    </div>

                </div>

                <hr>

                <div class="row">
                    <div class="col-md-12">
                        <h5 class= text-left text">Payment for :  {{ $validationDetails[0]['transactionNarration'] }}</h5>
                    </div>

                </div>
                <hr>
                <div class="form-group mb-4">
                    <label for="username">Payer's Name</label>
                    <input type="text" class="form-control" id="username" placeholder="" name="username" value="{{ $validationDetails[0]['studentName'] }}" readonly>

                </div>

                <div class="form-group mb-4">
                    <label for="matric">Matric No</label>
                    <input type="text" class="form-control"  id="matric" placeholder="" name="username" value="{{ $validationDetails[0]['studentMatric'] }}" readonly>

                </div>

                <div class="form-group mb-4">
                    <label for="amount">Transaction Amount</label>
                    <input type="text" class="form-control text-danger text-bold"  id="amount" placeholder="" name="amount" value="NGN {{ number_format($validationDetails[0]['amountPaid'],2) }}" readonly>

                </div>

                <div class="form-group mb-4">
                    <label for="status">Transaction Status</label>
                    <input type="text" class="form-control text "  id="status" placeholder="" name="username" value=" {{ $validationDetails[0]['transactionStatus'] }}" readonly>

                </div>
                <hr>

                <div class="row">
                    <div class="col-md-12">
                        <h5 class= text-left text">{{ $validationDetails[0]['studentCurrentLevel'] }} Level, {{ $validationDetails[0]['studentProgramme'] }}</h5>
                    </div>

                </div>
                <div class="mt-4">
                    <button class="btn btn-success btn-block waves-effect waves-light" >CLOSE </button>
                </div>
                <div class="mt-4 text-center">
                    <a href="{{ route('index') }} " class=" text-muted"><i class="mdi mdi-account-circle mr-1"></i>
                        Return to Home Page</a>
                </div>
            </div>
        </div>
    </form>
    @endsection
