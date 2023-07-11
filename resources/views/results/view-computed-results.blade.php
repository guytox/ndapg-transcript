@extends('layouts.setup')

@section('content')

    @include('includes.messages')

    {{-- <h4>List of Allocated Courses for {{ucfirst($semester)}} Semester,  {{$session_name}} Academic Session</h4> --}}
    <h4>List of Computed Results</h4>

    <div class="row">
        <div class="col-lg-12">

            <div class="card">
                <div class="card-body">
                    <div>
                        {{-- <a class="popup-form btn btn-primary" href="#test-form">Search Another Session/Semester</a> --}}
                    </div>
                    <h4 class="header-title mb-4"></h4>

                    @error('error')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $error }}</strong>
                        </span>
                    @enderror

                    @include('includes.messages')

                    {{-- {!! Form::open(['route'=>['view.computed.results', 'as'=>"ItyoughKiChukur"]],'method'=>'POST') !!} --}}
                    {!! Form::open(['route' => ['approve.computed.results'], 'method' => 'POST']) !!}

                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">S/N</th>
                                    <th scope="col">Prog.</th>
                                    <th scope="col">Session</th>
                                    <th scope="col"> Semester</th>
                                    <th scope="col">Level</th>
                                    <th scope="col">status</th>
                                    <th scope="col">Approval</th>
                                    <th scope="col">Action</th>

                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($result as $key => $val)


                                    <tr>
                                        <td>
                                            {!! Form::checkbox('cResults[]', $val->uid, 'checked', ) !!}
                                        </td>

                                        <td>
                                            {{getProgrammeNameById($val->program_id)}}
                                        </td>

                                        <td>
                                            {{getsessionById($val->schoolsession_id)->name}}
                                        </td>

                                        <td>
                                            {{getSemesterNameById($val->semester_id)}}
                                        </td>

                                        <td>
                                            {{getStudyLevelNameById($val->study_level)}}
                                        </td>

                                        <td>
                                            {{$val->cr_status}}
                                        </td>


                                        <td>

                                            @if ($val->eo_approval ===0)
                                                <span title="Exam Officer" > &#10060;</span>
                                            @elseif ($val->eo_approval ===1)
                                                <span title="Exam Officer" > &#9989;</span>
                                            @endif

                                            @if ($val->hod_approval ===0)
                                                <span title="HOD" > &#10060;</span>
                                            @elseif ($val->hod_approval ===1)
                                                <span title="HOD" > &#9989;</span>
                                            @endif

                                            @if ($val->dean_approval ===0)
                                                <span title="Dean" > &#10060;</span>
                                            @elseif ($val->dean_approval ===1)
                                                <span title="Dean" > &#9989;</span>
                                            @endif

                                            @if ($val->committee_approval ===0)
                                                <span title="Senate Business Committee" > &#10060;</span>
                                            @elseif ($val->committee_approval ===1)
                                                <span title="Senate Business Committee" > &#9989;</span>
                                            @endif

                                            @if ($val->senate_approval ===0)
                                                <span title="Senate Approval" > &#10060;</span>
                                            @elseif ($val->senate_approval ===1)
                                                <span title="Senate Approval" > &#9989;</span>
                                            @endif

                                        </td>

                                        <td>
                                            <a href="{{route('check.computed.results', ['uid'=>$val->uid, 'sem'=>$val->semester_id])}}" class="btn btn-danger">View Result</a>
                                        </td>




                                    </tr>


                                @endforeach

                            </tbody>
                        </table>




                                <div class="card" >
                                    <div class="card-body">
                                        <h4 class="header-title mb-4">Result Approval</h4>

                                        <table>
                                            <thead>
                                                <tr>
                                                    <td>
                                                        *** Select the check box to approve any result of your choice
                                                    </td>
                                                </tr>

                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="2">
                                                        {!! Form::label('approveAs', 'In My Capacity As: *') !!}
                                                        {!! Form::select('approveAs', getAcademicRoles(user()->id), null, ['class' => 'form-control', 'required']) !!}
                                                    </td>
                                                </tr>

                                                <tr>

                                                    <td>
                                                        {!! Form::label('action', 'Select Action (Approve or reject)') !!}
                                                        {!! Form::select('action', [''=>'N/A','1'=>"Approve", '2'=>'Reject'], '', ['class'=>'form-control','required']) !!}
                                                    </td>
                                                </tr>
                                                <tr>

                                                    {{-- <td>
                                                        {!! Form::label('message', "Insert Message for Stake if you are rejecting") !!}
                                                        {!! Form::text('message', '', ['class'=>'form-control']) !!}
                                                    </td> --}}

                                                </tr>

                                                <tr>

                                                    <td>
                                                        {!! Form::submit('Submit Approval Decision', ['class'=>'btn btn-success','required']) !!}
                                                    </td>

                                                </tr>
                                            </tbody>


                                    </div>
                                </div>




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
