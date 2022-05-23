@extends('layouts.setup')


@section('content')


<div class="row">
    <div class="col-xl-5">
        <div class="card">
            <div class="card-body">


                <h2 class="header-title">Initiate Payment</h2>


                <form method="post" action="{{ route('initiate.presentation.payment') }}">
                    @csrf
                    <input type="hidden" name="type" value="{{}}">
                    <button type="submit" class="btn btn-outline-primary">Initiate Payment</button>
                </form>
                <form>

                </form>

            </div>
        </div>




    </div>




    <div class="col-xl-6">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title">Payment Details</h4>
                <p>Payment Description: Payment for single article</h3>

                    <hr>

                    <hr>

                <p>Amount: NGN 1000</h3>




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
