@extends('layouts.setup')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h3>{{ __('Select Search Criteria') }}</h3></div>

                <div class="card-body">
                    <form action="{{ route('view.transactions') }}" method="post" enctype="multipart/form-data">
                        @csrf

                        <label for="SchoolSession">Select School Session</label>
                        <select name="school_session" id="SchoolSession">
                            <option value="">all Sessions</option>
                            @foreach ($SchoolSessions as $sess )
                                <option value="{{ $sess['id']}}">{{$sess['name']}}</option>
                            @endforeach
                        </select>
                        <br>
                        <label for="PaymentStatus">Select Payment Status</label>
                        <select name="payment_status" id="PaymentStatus">
                            <option value="">all </option>
                            @foreach ($PymntStatus as $sess )
                                <option value="{{ $sess['payment_status']}}">{{$sess['payment_status']}}</option>
                            @endforeach
                        </select>
                        <br>

                        <label for="PaymentType">Select Payment Type</label>
                        <select name="payment_type" id="PaymentType">
                            <option value="">all types</option>
                            @foreach ($PymntType as $sess )
                                <option value="{{ $sess['id']}}">{{$sess['purpose']}}</option>
                            @endforeach
                        </select>
                        <br>
                        <br><br>
                        <button type="submit">Search Transactions</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection




