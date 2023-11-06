@extends('layouts.setup')


@section('content')


<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">


                <h2 class="header-title">Outstanding Payments</h2>

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
                <table class="table">
                    <tr>
                        <th>Payment</th>
                        <th>Amout</th>
                        <th>Pay Now</th>
                    </tr>

                    @foreach ($payments as $payment)
                    <tr>
                        <td>@if($payment->configuration->payment_purpose_slug !== 'initial-system-billing') {{ $payment->configuration->purpose }} @else Tution @endif</td>
                        <td>{{ 'NGN ' . number_format($payment->balance, 2) }}</td>
                        <td>
                            <form method="get" action="{{ route('student.payment.amount') }}">
                                @csrf
                                <input type="hidden" name="amount" value="{{$payment->balance }}">
                                <input type="hidden" name="fee_payment_uid" value="{{$payment->uid }}">
                                <button type="submit" class="btn btn-outline-primary btn-sm">Initiate Payment</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach


                </table>



            </div>
        </div>




    </div>



</div>
<!-- end row -->

@endsection
