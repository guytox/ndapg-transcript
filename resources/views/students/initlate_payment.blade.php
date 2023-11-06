@extends('layouts.setup')


@section('content')


<div class="row">
    <div class="col-xl-5">
        <div class="card">

            @include('includes.messages')
            <div class="card-body">


                <h2 class="header-title">Complete Payment Online</h2>


                <form method="post" action="{{ route('initiate.credo.payment',['id'=>$payment->uid]) }}">
                    @csrf
                    <input type="text" name="type" value="{{convertToNaira($payment->balance)}}" class="form-control" readonly>
                    <br>
                    <button type="submit" class="btn btn-outline-primary form-control">Proceed to Pay Online</button>
                </form>
                <form>

                </form>

            </div>
        </div>




    </div>




    <div class="col-xl-6">
        <div class="card">
            <div class="card-body">
                {{-- <h4 class="header-title">Payment Details</h4> --}}
                <h3>Payment Description: <hr> {{$payment->config->narration}}</h3>



                    <hr>

                <h3>Balance: NGN {{number_format(convertToNaira($payment->balance),2)}}</h3>




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
