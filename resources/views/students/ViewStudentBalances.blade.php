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
                                    <th scope="col">Balance To Pay</th>
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
                                            <td>{{ $v->config->narration }}</td>
                                            <td>{{getSessionById($v->academic_session_id)->name}}</td>
                                            <td>{{ number_format(convertToNaira($v->balance),2) }}</td>
                                            <td>
                                                <a class="btn btn-danger" href="{{ route('initiate.student.payment', ['id'=>$v->uid]) }}">Pay Now</a>
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
