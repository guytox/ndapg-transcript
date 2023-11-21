@extends('layouts.setup')

@section('content')

    <h1>List of Previous Semester Registrations</h1>

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
                                    <th scope="col">Title</th>
                                    <th scope="col">Session</th>
                                    <th scope="col">#Credit Units</th>
                                    <th scope="col">No.Of Courses</th>
                                    <th scope="col">status</th>
                                    <th scope="col">Approval</th>
                                    <th scope="col">Recommendation</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $sn =1;
                                @endphp
                                @foreach ($Monitors as $v)

                                    <tr>
                                        <td>{{$sn}}</td>
                                        <td>{{ getCurriculaById(getRegMonitorById($v->id, 'curricula'),'name')}}</td>
                                        <td>{{getSessionById($v->session_id)->name}}</td>
                                        <td>{{getRegMonitorById($v->id, 'totalcredits')}}</td>
                                        <td>{{getRegMonitorById($v->id, 'numberofcourses')}}</td>
                                        <td>{{getRegMonitorById($v->id, 'status')}}</td>

                                        <td>
                                            <h3>
                                                @if (getRegMonitorById($v->id, 'stdconfirmation')==='0')
                                                    <span title="Student Confirmation" > &#10060;</span>
                                                @elseif (getRegMonitorById($v->id, 'stdconfirmation')==='1')
                                                    <span title="Student Confirmation" > &#9989;</span>
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
                                        <td>
                                            @if (getRegMonitorById($v->id, 'stdconfirmation')==0)
                                                <span>Registration Not Confirmed, Department cannot See it</span>
                                            @elseif (getRegMonitorById($v->id, 'stdconfirmation')==1 &&getRegMonitorById($v->id, 'status')=='pending' )
                                                <span>Registration Not Approved, Contact Department</span>
                                            @elseif (getRegMonitorById($v->id, 'stdconfirmation')==1 && getRegMonitorById($v->id, 'status')=='approved' )
                                                <span>All Well, No Action Required</span>
                                            @endif
                                        </td>

                                        <td>
                                            @if (getRegMonitorById($v->id, 'stdconfirmation')==1 )
                                                <a class="btn btn-primary" href="{{ route('student.registration.viewMyConfirmed', ['id'=>$v->id]) }}">view Details</a>
                                            @else
                                                <a class="btn btn-danger" href="{{ route('student.registration.viewSingle', ['id'=>$v->id]) }}">Preview and Submit Registration</a>
                                            @endif



                                            @if ($v->session_id===activesession()->id && getRegMonitorById($v->id, 'status')=='approved' )

                                                <a class="btn btn-danger" href="{{ route('student.registration.printexamcard', ['id'=>$v->uid]) }}">Print Exam. Card</a>

                                            @endif
                                        </td>
                                    </tr>

                                    @php
                                        $sn++;
                                    @endphp

                                @endforeach
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
