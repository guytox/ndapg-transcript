@extends('layouts.setup')

@section('css')
<!-- DataTables -->
<link href="{{ asset('admin/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
{{-- <link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" --}}
type="text/css" />
@endsection



@section('content')



    <h3>Recommendable Students for {{ getSessionById($schoolSession)->name}} {{getSemesterDetailsById($stdSemester) }} Semester {{getStudyLevelNameById($studyLevel)}} Level , {{getProgrammeDetailById($stdProgram, 'name') }} </h3>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-4"></h4>

                    @include('includes.messages')

                    {!! Form::open(['route' =>['recommend.graduants'] , 'method' => 'POST']) !!}

                    <div class="table-responsive">
                        <table id="table-control"  class="table table-striped table-centered table-nowrap mb-0" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
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
                                    <th scope="col">Sem <br> Spent</th>
                                    <th scope="col">status</th>
                                    {{-- <th scope="col">CCR</th> --}}
                                    {{-- <th scope="col">LTCR</th> --}}
                                    <th scope="col">TCR</th>
                                    <th scope="col">TCE</th>
                                    {{-- <th scope="col">LTWGP</th> --}}
                                    {{-- <th scope="col">TWGP</th> --}}
                                    {{-- <th scope="col">LCGPA</th> --}}
                                    <th scope="col">CGPA</th>
                                    {{-- <th scope="col">Recommendation</th> --}}
                                    <th scope="col">Carry Overs</th>
                                    <th scope="col">Degree Class</th>
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
                                        @if ($v->tcr === $v->tce)
                                            <td>{!! Form::checkbox('regMonitor[]', $v->uid, true,[]) !!}</td>
                                        @else
                                            <td>{!! Form::checkbox('regMonitor[]', $v->uid, false,[]) !!}</td>
                                        @endif

                                        <td>{{ getStudentByStudentId($v->student_id)->matric}}</td>
                                        <td>{{ getUserByUsername(getStudentByStudentId($v->student_id)->matric)->name}}</td>
                                        {{-- <td>{{getProgrammeDetailById($v->program_id, 'name')}}</td> --}}
                                        {{-- <td>{{getSessionById($v->session_id)->name}}</td> --}}
                                        {{-- <td>{{ucfirst(getSemesterDetailsById($v->semester_id))}}</td> --}}

                                        {{-- <td>{{$v->semesters_spent}}</td> --}}
                                        <td>{{ $v->semesters_spent}}</td>
                                        <td>{{$v->r_status =='0'? "Not Computed": "Computed"}}</td>
                                        {{-- <td>{{getRegMonitorById($v->id, 'totalcredits')}}</td> --}}
                                        {{-- <td>{{ $v->ltcr }}</td> --}}
                                        <td>{{ $v->tcr }}</td>
                                        <td>{{ $v->tce }}</td>
                                        {{-- <td>{{ $v->ltwgp }}</td> --}}
                                        {{-- <td>{{ $v->twgp }}</td> --}}
                                        {{-- <td>{{ number_format(convertToNaira($v->lcgpa),2) }}</td> --}}
                                        <td>{{ number_format(convertToNaira($v->cgpa),2) }}</td>
                                        @if (getCarryOvers($v->student_id))
                                            <td>
                                                @foreach (getCarryOvers($v->student_id) as $co)
                                                    {{ getSemesterCourseById($co->course_id)->courseCode}}, <br>
                                                @endforeach
                                            </td>
                                        @else
                                            <td> Pass </td>
                                        @endif

                                        <td>N/A</td>



                                        <td>
                                            @if (getRegMonitorById($v->id, 'stdconfirmation')==1 )

                                                <a target="_blank" class="btn btn-primary" href="{{ route('show.single.student.result', ['id'=>$v->uid, 'student_id'=>$v->student_id,'semester'=>$stdSemester]) }}">view Transcript</a>

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



                    @if (user()->hasRole('exam_officer'))



                            {!! Form::submit('RECOMMEND GRADUANTS FOR HOD CONFIRMATION', ['class'=>'btn btn-success','required']) !!}

                    @else

                        Note!!! This Recommendation is for PG Coordinators alone !!!!

                    @endif











                        {!! Form::close() !!}
            </div>

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
