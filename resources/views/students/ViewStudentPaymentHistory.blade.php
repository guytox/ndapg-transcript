@extends('layouts.setup')

@section('content')

    <h1>List of Outstanding Payments</h1>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-4"></h4>

                    @include('includes.messages')

                    <div class="table-responsive">
                        <table class="table table-striped table-centered table-nowrap mb-0">
                            <thead>
                                <tr>

                                    <th scope="col">S/N</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Session</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($Monitors)

                                    @php
                                        $sn =1;
                                    @endphp

                                    @foreach ($Monitors as $v)

                                        <tr>
                                            <td>{{$sn}}</td>
                                            <td>{{ $v->feePayment->config->narration }}</td>
                                            <td>{{getSessionById($v->feePayment->academic_session_id)->name}}</td>
                                            <td>{{ number_format(convertToNaira($v->amount_paid),2) }}</td>
                                            <td>{{ $v->created_at }}</td>
                                            <td>
                                                <a class="btn btn-danger" href="{{ route('print.general.receipt', ['id'=>$v->feePayment->uid]) }}">View Invoice</a>
                                            </td>
                                        </tr>

                                        @php
                                            $sn++;
                                        @endphp

                                    @endforeach
                                @endif

                            </tbody>
                        </table>



                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end row -->

    <script>
        @if(session()->has('error'))
          alert('{{session()->get('error')}}')
        @endif
    </script>

@endsection
