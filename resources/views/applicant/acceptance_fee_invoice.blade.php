@extends('layouts.setup')


@section('content')


    @if($feePayment)

        <h2>Application Fee Payment Receipt</h2>
        <br>

        <table class="table table-bordered table-striped table-responsive">
            <tr>
                <th>Payer's Name</th>
                <th>Payer's Email</th>
            </tr>
            <tr>
                <td>{{user()->name}} </td>
                <td>{{user()->email}}</td>
            </tr>

            <tr>
                <th>Amount</th>
                <th>Payment Date</th>
            </tr>
            <tr>
                <td>NGN{{number_format($feePayment->amount_paid,2)}}</td>
                <td>{{$feePayment->updated_at}}</td>
            </tr>

            <tr>
                <th>Transaction Id</th>
                <th>Transaction Reference</th>
            </tr>
            <tr>
                <td>{{$feePayment->txn_id}} </td>
                <td>{{$feePayment->uid}} </td>
            </tr>

            <tr>
                <th>Narration</th>
                <th></th>
            </tr>
            <tr>
                <td colspan="2"><b><i>{{env('ORG_NAME_SHORT')}} Application Fees-{{user()->name}}-{{$feePayment->uid}}</i></b></td>
            </tr>
        </table>


    @else

    <div class="row">
        <div class="col-xl-5">
            <div class="card">
                <div class="card-body">



                    <h2 class="header-title">Initiate Payment</h2>


                    <form method="post" action="">
                        @csrf
                        <input type="hidden" name="amount" value="{{ $applicationFeeConfiguration->amount }}">
                        <button type="submit" class="btn btn-outline-primary">Initiate Payment</button>
                    </form>
                    <form>

                    </form>

                </div>
            </div>




        </div>


        @endif







    </div>
    <!-- end row -->

@endsection
