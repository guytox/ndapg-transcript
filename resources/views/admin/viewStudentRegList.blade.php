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

                                    <th scope="col">S/N</th>
                                    <th scope="col">Matric</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Prog.</th>
                                    <th scope="col">Session</th>
                                    <th scope="col">Semester</th>
                                    <th scope="col">#Units</th>
                                    <th scope="col">#Courses</th>
                                    <th scope="col">status</th>
                                    <th scope="col">Approval</th>
                                    {{-- <th scope="col">Recommendation</th> --}}
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>

                                {!! Form::open(['route' =>['reg.store'] , 'method' => 'POST']) !!}


                            <tbody>
                                @php
                                    $sn =1;
                                @endphp
                                @foreach ($pendingStdRegs as $v)

                                    <tr>
                                        <td>{{$sn}}</td>
                                        <td>{!! Form::checkbox('regMonitor[]', $v->uid, true,[]) !!}</td>
                                        <td>{{ getStudentByStudentId($v->student_id)->matric}}</td>
                                        <td>{{ getUserByUsername(getStudentByStudentId($v->student_id)->matric)->name}}</td>
                                        <td>{{getProgrammeDetailById($v->program_id, 'name')}}</td>
                                        <td>{{getSessionById($v->session_id)->name}}</td>
                                        <td>{{ucfirst(getSemesterDetailsById($v->semester_id))}}</td>
                                        <td>{{getRegMonitorById($v->id, 'totalcredits')}}</td>
                                        <td>{{getRegMonitorById($v->id, 'numberofcourses')}}</td>
                                        <td>{{getRegMonitorById($v->id, 'status')}}</td>

                                        <td>
                                            <h3>
                                                @if (getRegMonitorById($v->id, 'stdconfirmation')==='0')
                                                    <span title="Student" > &#10060;</span>
                                                @elseif (getRegMonitorById($v->id, 'stdconfirmation')==='1')
                                                    <span title="Student" > &#9989;</span>
                                                @endif

                                                @if (getRegMonitorById($v->id, 'rostatus')==0)
                                                    <span title="registration Officer" > &#10060;</span>
                                                @elseif (getRegMonitorById($v->id, 'rostatus')==1)
                                                    <span title="registration Officer" > &#9989;</span>
                                                @endif

                                                @if (getRegMonitorById($v->id, 'hodstatus')==0)
                                                    <span title="HOD"> &#10060; </span>
                                                @elseif (getRegMonitorById($v->id, 'hodstatus')==1)
                                                    <span title="HOD"> &#9989; </span>
                                                @endif

                                                @if (getRegMonitorById($v->id, 'deanstatus')==0)
                                                    <span title="Dean"> &#10060; </span>
                                                @elseif (getRegMonitorById($v->id, 'deanstatus')==1)
                                                    <span title="Dean"> &#9989; </span>
                                                @endif
                                            </h3>

                                        </td>
                                        {{-- <td>
                                            @if (getRegMonitorById($v->id, 'stdconfirmation')==0)
                                                <span>Registration Not Confirmed, Department cannot See it</span>
                                            @elseif (getRegMonitorById($v->id, 'stdconfirmation')==1 &&getRegMonitorById($v->id, 'status')=='pending' )
                                                <span>Registration Not Approved, Contact Department to Secure Approval</span>
                                            @elseif (getRegMonitorById($v->id, 'stdconfirmation')==1 && getRegMonitorById($v->id, 'status')=='approved' )
                                                <span>All Well, No Action Required</span>
                                            @endif
                                        </td> --}}

                                        <td>
                                            @if (getRegMonitorById($v->id, 'stdconfirmation')==1 )
                                                <a class="btn btn-primary" href="{{ route('student.registration.viewMyConfirmed', ['id'=>$v->id]) }}">view Details</a>
                                            @else
                                                <a class="btn btn-danger" href="{{ route('student.registration.viewSingle', ['id'=>$v->id]) }}">Preview and Submit Registration</a>
                                            @endif



                                            @if ($v->session_id===activesession()->id && getRegMonitorById($v->id, 'status')=='approved' )
                                            
                                            @role('student')

                                                <a class="btn btn-danger" href="{{ route('student.registration.viewSingle', ['id'=>$v->id]) }}">Print Exam. Card</a>

                                            @endrole

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
                            <b>Approval Section:</b>
                            <thead>
                                <td></td>
                                <td></td>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="2">
                                        {!! Form::label('approveAs', 'In My Capacity As: *') !!}
                                        {!! Form::select('approveAs', $staffRoles, null, ['class' => 'form-control', 'required']) !!}
                                    </td>
                                </tr>

                                <tr>

                                    <td>
                                        {!! Form::label('approveAs', 'Select Action (Approve or reject)') !!}
                                        {!! Form::select('action', [''=>'N/A','1'=>"Approve", '2'=>'Reject'], '', ['class'=>'form-control','required']) !!}
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
