@extends('layouts.setup')


@section('content')


    @if($feePayment)

        you have paid

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
