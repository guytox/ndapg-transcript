@extends('layouts.setup')


@section('content')


<div class="row">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body">


                <h2 class="header-title">Initiate Payment</h2>

                @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">

                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    @foreach ($errors->all() as $error)
                    <div class="">{{ $error }}</div>
                    @endforeach
                </div>
                @endif


                <form method="post" action="{{ route('initiate.payment') }}">
                    @csrf
                    <input type="number" name="amount" step="any" value="{{ $paymentDetails['amount'] }}" class="form-control gray">
                    <input type="hidden" name="fee_payment_uid" value="{{ $paymentDetails['fee_payment_uid'] }}">
                    <br>
                    <button type="submit" class="btn btn-outline-primary btn-sm">Initiate Payment</button>
                </form>

            </div>
        </div>




    </div>



</div>
<!-- end row -->

@endsection
