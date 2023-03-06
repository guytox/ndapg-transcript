@extends('layouts.setup')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h3>{{ __('Select Search Criteria') }}</h3></div>

                <div class="card-body">
                    <form action="{{ route('search.daily.transactions') }}" method="post" enctype="multipart/form-data">
                        @csrf

                        <label for="SchoolSession">Select School Session</label>
                        <select name="school_session" id="SchoolSession" class="form-control">
                            <option value="">all Sessions</option>
                            @foreach ($SchoolSessions as $sess )
                                <option value="{{ $sess['id']}}">{{$sess['name']}}</option>
                            @endforeach
                        </select>
                        <br>
                        <label for="paymentChannel">Select Payment Channel</label>
                        <select name="payment_channel" id="paymentChannel" class="form-control">
                            <option value="">all </option>
                            @foreach ($PymntChannel as $sess )
                                <option value="{{ $sess['payment_channel']}}">{{$sess['payment_channel']}}</option>
                            @endforeach
                        </select>
                        <br>

                        <label for="PaymentType">Select Payment Type</label>
                        <select name="payment_type" id="PaymentType" class="form-control">
                            <option value="">all types</option>
                            @foreach ($PymntType as $sess )
                                <option value="{{ $sess['id']}}">{{$sess['category_name']}}</option>
                            @endforeach
                        </select>

                        <br>

                        <label for="PaymentType">Select Payment Date</label>
                        <input type="date" id="txn_date" name="txn_date" class="form-control" required>

                        <br>
                        <br><br>
                        <button type="submit" class="form-control btn btn-info">Search Transactions</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection




