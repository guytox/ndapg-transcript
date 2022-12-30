@extends('layouts.setup')

@section('css')
<!-- DataTables -->
<link href="{{ asset('admin/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('admin/assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />

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
                        <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive table-nowrap mb-0" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>

                                    <th scope="col">S/N</th>
                                    <th scope="col">Form No</th>
                                    <th scope="col">Name</th>
                                    {{-- <th scope="col">Faculty</th>
                                    <th scope="col">Department</th> --}}
                                    <th scope="col">Prog.</th>
                                    {{-- <th scope="col">Category</th>
                                    <th scope="col">email</th>
                                    <th scope="col">GSM</th>
                                    <th scope="col">State</th> --}}
                                    <th scope="col">Gender</th>
                                    {{-- <th scope="col">#Level</th>
                                    <th scope="col">#Units</th>
                                    <th scope="col">status</th>
                                    <th scope="col">Form-Number</th> --}}
                                    <th scope="col">Approval</th>
                                    {{-- <th scope="col">Recommendation</th> --}}
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @php
                                    $sn =1;
                                @endphp
                                @foreach ($appList as $v)

                                    <tr>
                                        <td>{{$sn}}</td>
                                        <td>{{ $v['apFormNumber']}}</td>
                                        <td>{{ $v['apName']}}</td>
                                        {{-- <td>{{ getUserByUsername(getStudentByStudentId($v->student_id)->matric)->name}}</td> --}}
                                        {{-- <td>{{getFacultyByStudentId($v->student_id)}}</td>
                                        <td>{{getDepartmentByStudentId($v->student_id)}}</td> --}}
                                        <td>{{getProgrammeDetailById($v['programid'], 'name')}}</td>
                                        <td>{{ $v['gender']}}</td>
                                        {{-- <td>{{ucfirst($v->category)}}</td>
                                        <td>{{getUserByStudentID($v->student_id)->email}}</td>
                                        <td>{{getUserByStudentID($v->student_id)->phone_number}}</td>
                                        <td>{{getStateNameById($v->state_origin)}}</td>
                                        <td>{{ucfirst($v->gender)}}</td>
                                        <td>{{getStudyLevelDetailsById($v->level_id)}}Level</td>
                                        <td>{{getRegMonitorById($v->id, 'totalcredits')}}</td>
                                        <td>{{getRegMonitorById($v->id, 'status')}}</td>
                                        <td>{{getFormNumberByStudentId($v->student_id)}}</td> --}}


                                        <td>
                                            <h4>
                                                @if ($v['submitted'] ===0)
                                                    <span title="Student Submission" > &#10060;</span>
                                                @elseif ($v['submitted'] ===1)
                                                    <span title="Student Submission" > &#9989;</span>
                                                @endif

                                                @if ($v['oLevelVerified'] ===0)
                                                    <span title="O-Level Verification" > &#10060;</span>
                                                @elseif ($v['oLevelVerified']===1)
                                                    <span title="O-Level Verification" > &#9989;</span>
                                                @endif

                                                @if ($v['sentToDepartment'] ===0)
                                                    <span title="Forwarded to Dept" > &#10060;</span>
                                                @elseif ($v['sentToDepartment']===1)
                                                    <span title="Forwarded to Dept" > &#9989;</span>
                                                @endif

                                                @if ($v['pg_coord'] ===0)
                                                    <span title="PG Coordinator" > &#10060;</span>
                                                @elseif ($v['pg_coord']===1)
                                                    <span title="PG Coordinator" > &#9989;</span>
                                                @endif

                                                @if ($v['hod'] ===0)
                                                    <span title="HOD" > &#10060;</span>
                                                @elseif ($v['hod'] ===1)
                                                    <span title="HOD" > &#9989;</span>
                                                @endif


                                                @if ($v['dean'] ===0)
                                                    <span title="Dean" > &#10060;</span>
                                                @elseif ($v['dean'] ===1)
                                                    <span title="Dean" > &#9989;</span>
                                                @endif

                                                @if ($v['dean_spgs'] ===0)
                                                    <span title="Dean SPGS" > &#10060;</span>
                                                @elseif ($v['dean_spgs'] ===1)
                                                    <span title="Dean SPGS" > &#9989;</span>
                                                @endif

                                                @if ($v['admitted']===0)
                                                    <span title="Admission Status" > &#10060;</span>
                                                @elseif ($v['admitted'] ===1)
                                                    <span title="Admission Status" > &#9989;</span>
                                                @endif

                                                {{-- @if (getRegMonitorById($v->id, 'rostatus')==0)
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
                                                @endif --}}
                                            </h4>

                                        </td>


                                        <td>
                                            @if ($v['submitted'] ===1 )

                                                <a class="btn btn-primary" href="{{ route('preview.submitted.application', ['id'=>$v['userId'],]) }}">view Details</a>

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
                            <b></b>
                            <thead>
                                <td></td>
                                <td></td>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="2">

                                    </td>
                                </tr>

                                <tr>

                                    <td>

                                    </td>
                                </tr>
                                <tr>

                                    <td>

                                    </td>

                                </tr>

                                <tr>

                                    <td>

                                    </td>

                                </tr>
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


@section('js')

<script src="{{ asset('admin/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>


<script src="{{ asset('admin/assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/jszip/jszip.min.js') }}"></script>

<script src="{{ asset('admin/assets/libs/pdfmake/build/pdfmake.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/pdfmake/build/vfs_fonts.js') }}"></script>
<script src="{{ asset('admin/assets/libs/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/datatables.net-buttons/js/buttons.colVis.min.js') }}"></script>
<!-- Responsive examples -->

<script src="{{ asset('admin/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>

<!-- Datatable init js -->
<script src="{{ asset('admin/assets/js/pages/datatables.init.js') }}"></script>

@endsection
