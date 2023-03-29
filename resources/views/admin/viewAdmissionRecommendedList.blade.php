@extends('layouts.setup')

@section('css')
<!-- DataTables -->
<link href="{{ asset('admin/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
{{-- <link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" --}}
type="text/css" />
@endsection



@section('content')

    <h1>{{$title}}</h1>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-4"></h4>

                    @include('includes.messages')

                    <div class="table-responsive">
                        <table id="datatable"  class="table table-striped table-centered table-nowrap mb-0" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>


                                    <th scope="col"></th>
                                    <th scope="col">Chk</th>
                                    <th scope="col">App No</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Session</th>
                                    <th scope="col">status</th>
                                    <th scope="col">O-Level <br> Check</th>
                                    <th scope="col">std</th>
                                    <th scope="col">Recommendation</th>
                                </tr>
                            </thead>

                                {!! Form::open(['route' =>['recommend.selected.applicants'] , 'method' => 'POST']) !!}


                            <tbody>
                                @php
                                    $sn =1;
                                @endphp
                                @foreach ($applicants as $v)

                                    <tr>
                                        <td>{{$sn}}</td>
                                        <td>{!! Form::checkbox('regMonitor[]', $v->uid, true,[]) !!}</td>
                                        <td>{{ $v->form_number}}</td>
                                        <td>{{ getUserById($v->user_id)->name}}</td>
                                        <td>{{getSessionById($v->session_id)->name}}</td>
                                        @if ($v->is_admitted ===0)
                                            <td>Pending</td>
                                        @elseif ($v->is_admitted ===1)
                                            <td>Admitted</td>
                                        @else
                                            <td>Undefined</td>
                                        @endif
                                        <td>
                                            <h3>
                                                @if ($v->is_olevel_verified === 0)
                                                    <span title="O-Level Verification" > &#10060;</span>
                                                @elseif ($v->is_olevel_verified === 1)
                                                    <span title="O-Level Verification" > &#9989;</span>
                                                @endif
                                            </h3>
                                        </td>
                                        <td>
                                            <h3>
                                                @if ($v->is_submitted === 0)
                                                    <span title="Student Submission" > &#10060;</span>
                                                @elseif ($v->is_submitted === 1)
                                                    <span title="Student Submission" > &#9989;</span>
                                                @endif
                                            </h3>
                                        </td>
                                        <td>
                                            <h3>
                                                @if ($v->pg_coord === 0)
                                                    <span title="PG Coordinator" > &#10060;</span>
                                                @elseif ($v->pg_coord === 1)
                                                    <span title="PG Coordinator" > &#9989;</span>
                                                @endif

                                                @if ($v->hod === 0)
                                                    <span title="HOD" > &#10060;</span>
                                                @elseif ($v->hod === 1)
                                                    <span title="HOD" > &#9989;</span>
                                                @endif

                                                @if ($v->dean === 0)
                                                    <span title="Dean" > &#10060;</span>
                                                @elseif ($v->dean === 1)
                                                    <span title="Dean" > &#9989;</span>
                                                @endif

                                                @if ($v->dean_spgs === 0)
                                                    <span title="PG School" > &#10060;</span>
                                                @elseif ($v->dean_spgs === 1)
                                                    <span title="PG School" > &#9989;</span>
                                                @endif
                                            </h3>

                                        </td>


                                        <td>
                                            @if ($v->is_submitted ==1 )

                                                <a class="btn btn-primary" href="{{ route('preview.submitted.application', ['id'=>$v->user_id]) }}" target="_blank">view Details</a>

                                            @else



                                            @endif

                                        </td>
                                    </tr>

                                    @php
                                        $sn++;
                                    @endphp

                                @endforeach



                            </tbody>



                        </table>
                        <table>
                            <hr>
                            <b>Admission Recommendation/Approval Section:</b>
                            <thead>
                                <td></td>
                                <td></td>
                            </thead>
                            <tbody>
                                <br>***** Select checkboxes above before recommending candidates
                                <tr>
                                    <td colspan="2">
                                        {!! Form::label('approveAs', 'In My Capacity As: *') !!}
                                        {!! Form::select('approveAs', $staffRoles, null, ['class' => 'form-control', 'required']) !!}
                                    </td>
                                </tr>

                                <tr>

                                    <td>
                                        {!! Form::label('approveAs', 'Select Action (Approve or reject)') !!}
                                        {!! Form::select('action', [''=>'N/A','1'=>"Recommend", '2'=>'Reject'], '', ['class'=>'form-control','required']) !!}
                                    </td>
                                </tr>
                                <tr>

                                    <td>
                                        {!! Form::label('message', "Insert Message for students if you are rejecting") !!}
                                        {!! Form::text('message', '', ['class'=>'form-control']) !!}
                                    </td>

                                </tr>

                                <tr>

                                    <td>
                                        {!! Form::submit('Submit Approval Decision', ['class'=>'btn btn-success','required']) !!}
                                    </td>

                                </tr>
                            </tbody>

                            {!! Form::close() !!}
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


@section('js')
<!-- Required datatable js -->
<script src="{{ asset('admin/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>


<!-- Datatable init js -->
<script src="{{ asset('admin/assets/js/pages/datatables.init.js') }}"></script>


@endsection
