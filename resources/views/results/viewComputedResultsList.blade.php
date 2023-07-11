@extends('layouts.setup')

@section('css')
<!-- DataTables -->
<link href="{{ asset('admin/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
{{-- <link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" --}}
type="text/css" />
@endsection



@section('content')



    <h3>Results for {{ getSessionById($schoolSession)->name}} {{getSemesterDetailsById($stdSemester) }} Semester {{getStudyLevelNameById($studyLevel)}} Level , {{getProgrammeDetailById($stdProgram, 'name') }} </h3>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-4"></h4>

                    @include('includes.messages')

                    {!! Form::open(['route' =>['recompute.oldResult'] , 'method' => 'POST']) !!}

                    <div class="table-responsive">
                        <table id="datatable"  class="table table-striped table-centered table-nowrap mb-0" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>

                                    <th scope="col">S/N</th>
                                    <th scope="col">chk</th>
                                    <th scope="col">Matric</th>
                                    <th scope="col">Name</th>
                                    {{-- <th scope="col">Prog.</th> --}}
                                    {{-- <th scope="col">Session</th> --}}
                                    {{-- <th scope="col">Semester</th> --}}
                                    {{-- <th scope="col">Spent</th> --}}
                                    <th scope="col">TNC</th>
                                    <th scope="col">status</th>
                                    <th scope="col">CCR</th>
                                    <th scope="col">LTCR</th>
                                    <th scope="col">TCR</th>
                                    <th scope="col">LTWGP</th>
                                    <th scope="col">TWGP</th>
                                    <th scope="col">LCGPA</th>
                                    <th scope="col">CGPA</th>
                                    {{-- <th scope="col">Recommendation</th> --}}
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>



                                {!! Form::hidden('programme', $stdProgram, ["class"=>"form-control"]) !!}
                                {!! Form::hidden('schsession', $schoolSession, ["class"=>"form-control"]) !!}
                                {!! Form::hidden('schsemester', $stdSemester, ["class"=>"form-control"]) !!}
                                {!! Form::hidden('studylevel', $studyLevel, ["class"=>"form-control"]) !!}


                            <tbody>
                                @php
                                    $sn =1;
                                @endphp
                                @foreach ($regStudents as $v)

                                    <tr>
                                        <td>{{$sn}}</td>
                                        <td>{!! Form::checkbox('regMonitor[]', $v->uid, true,[]) !!}</td>
                                        <td>{{ getStudentByStudentId($v->student_id)->matric}}</td>
                                        <td>{{ getUserByUsername(getStudentByStudentId($v->student_id)->matric)->name}}</td>
                                        {{-- <td>{{getProgrammeDetailById($v->program_id, 'name')}}</td> --}}
                                        {{-- <td>{{getSessionById($v->session_id)->name}}</td> --}}
                                        {{-- <td>{{ucfirst(getSemesterDetailsById($v->semester_id))}}</td> --}}

                                        {{-- <td>{{$v->semesters_spent}}</td> --}}
                                        <td>{{getRegMonitorById($v->id, 'numberofcourses')}}</td>
                                        <td>{{$v->r_status =='0'? "Not Computed": "Computed"}}</td>
                                        <td>{{getRegMonitorById($v->id, 'totalcredits')}}</td>
                                        <td>{{ $v->ltcr }}</td>
                                        <td>{{ $v->tcr }}</td>
                                        <td>{{ $v->ltwgp }}</td>
                                        <td>{{ $v->twgp }}</td>
                                        <td>{{ number_format(convertToNaira($v->lcgpa),2) }}</td>
                                        <td>{{ number_format(convertToNaira($v->cgpa),2) }}</td>


                                        <td>
                                            @if (getRegMonitorById($v->id, 'stdconfirmation')==1 )

                                                <a class="btn btn-primary" href="{{ route('show.single.student.result', ['id'=>$v->uid, 'student_id'=>$v->student_id,'semester'=>$stdSemester]) }}">view Result</a>

                                            @else



                                            @endif



                                            @if ($v->session_id===activesession()->id && getRegMonitorById($v->id, 'status')=='approved' )

                                            @role('student')

                                                <a class="btn btn-danger" href="{{ route('student.registration.viewSingle', ['id'=>$v->uid]) }}">Print Exam. Card</a>

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
                            <b>Result Computation:</b>
                            <thead>
                                <td></td>
                                <td></td>
                            </thead>
                            <tbody>

                                <tr>

                                    <td>


                                    </td>

                                </tr>

                            </tbody>


                        </table>





                    </div>
                </div>

            @role('admin|vc|dean_pg|dean|hod|exam_officer')

                @if ($resultComputed)

                    @if (user()->hasRole('exam_officer') && $reComputeCheck->eo_approval=='0')

                            {!! Form::hidden('command', 26, ["class"=>"form-control"]) !!}

                            {!! Form::submit('Re-Compute Result', ['class'=>'btn btn-success','required']) !!}

                    @elseif (user()->hasRole('hod') && $reComputeCheck->eo_approval=='1'  && $reComputeCheck->hod_approval =='0')

                            {!! Form::hidden('command', 26, ["class"=>"form-control"]) !!}

                            {!! Form::submit('Re-Compute Result', ['class'=>'btn btn-success','required']) !!}

                    @elseif (user()->hasRole('dean')&& $reComputeCheck->eo_approval=='1'  && $reComputeCheck->hod_approval =='1' && $reComputeCheck->dean_approval == '0')

                            {!! Form::hidden('command', 26, ["class"=>"form-control"]) !!}

                            {!! Form::submit('Re-Compute Result', ['class'=>'btn btn-success','required']) !!}

                    @elseif (user()->hasRole('vc'))

                            {!! Form::hidden('command', 26, ["class"=>"form-control"]) !!}

                            {!! Form::submit('Re-Compute Result', ['class'=>'btn btn-success','required']) !!}

                    @endif





                @elseif (!$resultComputed)

                            {!! Form::hidden('command', 25, ["class"=>"form-control"]) !!}

                            {!! Form::submit('Compute Result', ['class'=>'form-control btn btn-danger','required']) !!}

                @endif



                        {!! Form::close() !!}
            </div>

        @endrole

            @if ($resultComputed)
            <a href="{{ route('view.senatesheet',['uid'=>$resultId, 'sem'=>$stdSemester])}}" class="form-control btn btn-warning" target="_blank" >Print Senate Sheet</a><br><br>
            <a href="{{ route('view.passedsenatesheet',['uid'=>$resultId, 'sem'=>$stdSemester])}}" class="form-control btn btn-danger" target="_blank" >Print Pass List</a><br><br>
            <a href="{{ route('view.failedsenatesheet',['uid'=>$resultId, 'sem'=>$stdSemester])}}" class="form-control btn btn-dark" target="_blank" >Print Fail List</a><br>
            @endif

            <hr>
        @role('admin|vc|dean_pg|dean|hod|exam_officer')
            @if (user()->hasRole('exam_officer') && $reComputeCheck->eo_approval=='0')
                <div class="card">
                    <div class="card-body">
                        <b>Result Approval</b>

                        {!! Form::open(['route'=>['approve.single.computed.results','resultId'=>$resultId,'sem'=>$sem],'method'=>'POST']) !!}

                        <table>
                            <thead>

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
                                        {!! Form::label('approveAs', 'Select Action (Approve or reject)') !!}
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
                        </table>


                        {!! Form::close() !!}


                    </div>

                </div>
            @elseif (user()->hasRole('hod') && $reComputeCheck->eo_approval=='1'  && $reComputeCheck->hod_approval =='0')
                <div class="card">
                    <div class="card-body">
                        <b>Result Approval</b>

                        {!! Form::open(['route'=>['approve.single.computed.results','resultId'=>$resultId,'sem'=>$sem],'method'=>'POST']) !!}

                        <table>
                            <thead>

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
                                        {!! Form::label('approveAs', 'Select Action (Approve or reject)') !!}
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
                        </table>


                        {!! Form::close() !!}


                    </div>

                </div>
            @elseif (user()->hasRole('dean')&& $reComputeCheck->eo_approval=='1'  && $reComputeCheck->hod_approval =='1' && $reComputeCheck->dean_approval == '0')
                <div class="card">
                    <div class="card-body">
                        <b>Result Approval</b>

                        {!! Form::open(['route'=>['approve.single.computed.results','resultId'=>$resultId,'sem'=>$sem],'method'=>'POST']) !!}

                        <table>
                            <thead>

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
                                        {!! Form::label('approveAs', 'Select Action (Approve or reject)') !!}
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
                        </table>


                        {!! Form::close() !!}


                    </div>

                </div>
            @elseif (user()->hasRole('vc'))
                <div class="card">
                    <div class="card-body">
                        <b>Result Approval</b>

                        {!! Form::open(['route'=>['approve.single.computed.results','resultId'=>$resultId,'sem'=>$sem],'method'=>'POST']) !!}

                        <table>
                            <thead>

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
                                        {!! Form::label('approveAs', 'Select Action (Approve or reject)') !!}
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
                        </table>


                        {!! Form::close() !!}


                    </div>

                </div>
            @endif

        @endrole


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
