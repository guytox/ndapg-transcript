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
                                    <th scope="col">Prog.</th>
                                    <th scope="col">Session</th>
                                    <th scope="col">Semester</th>
                                    <th scope="col">CUR</th>
                                    <th scope="col">CCE</th>
                                    <th scope="col">WGP</th>
                                    <th scope="col">GPA</th>
                                    <th scope="col">CGPA</th>
                                    <th scope="col">Approval</th>
                                    {{-- <th scope="col">Recommendation</th> --}}
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @php
                                    $sn =1;
                                @endphp
                                @foreach ($allResults as $v)

                                    <tr>
                                        <td>{{$sn}}</td>
                                        <td>{{getProgrammeDetailById($v->program_id, 'name')}}</td>
                                        <td>{{getSessionById($v->session_id)->name}}</td>
                                        <td>{{ucfirst(getSemesterDetailsById($v->semester_id))}}</td>
                                        <td>{{$v->cur}}</td>
                                        <td>{{$v->cue}}</td>
                                        <td>{{$v->wgp}}</td>
                                        <td>{{number_format(convertToNaira($v->gpa),2)}}</td>
                                        <td>{{number_format(convertToNaira($v->cgpa),2)}}</td>
                                        <td>{{$v->r_status}}</td>

                                        <td>

                                            @role('student')

                                                <a class="btn btn-danger" href="{{ route('show.student.single.result', ['id'=>$v->uid, 'student_id'=>$v->student_id, 'semester'=>$v->semester_id]) }}">View Result Sheet</a>

                                            @endrole

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


@section('js')
<!-- Required datatable js -->
<script src="{{ asset('admin/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>


<!-- Datatable init js -->
<script src="{{ asset('admin/assets/js/pages/datatables.init.js') }}"></script>


@endsection
