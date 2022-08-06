@extends('layouts.setup')

@section('css')

 <!-- DataTables -->
 <link href="{{asset('admin/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />
 <link href="{{asset('admin/assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />

 <!-- Responsive datatable examples -->
 <link href="{{asset('admin/assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />

 <!-- Lightbox css -->
 <link href="{{ asset('admin/assets/libs/magnific-popup/magnific-popup.css') }}" rel="stylesheet" type="text/css" />



@endsection


@section('content')


<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">

                <table class="table table-centered table-nowrap mb-0"">
                    <tr>
                        <td colspan="">
                        </td>
                        <td>
                            @include('includes.reportheaderlecturer')

                        </td>


                        <td >
                        </td>
                    </tr>
                </table>
                <hr>

                @if ($course->submitted ==='2' && $course->graded ==='1')



                <div class="text-center">
                    <h5><b> LIST OF REGISTERED STUDENTS FOR:--> {{ getSemesterCourseById($course->course_id)->courseCode}}( {{ getSemesterCourseById($course->course_id)->courseTitle}}):--> {{ ucfirst(getSemesterDetailsById($course->semester_id)) }} Semester:--> {{ getsessionById($course->session_id)->name }} Session</b></h5>
                    <hr>
                    <table class="table table-bordered">
                        {!! Form::open(['route' => ['lecturer.manual.upload', 'as'=>'ortesenKwagh']  , 'method' => 'POST']) !!}

                        {!! Form::hidden('id', $course->uid, ['class'=>'form-control']) !!}

                        <tr>
                            <th>{!! Form::checkbox('checkbox[]', 'student_id', true, []) !!}</th>
                            <th scope="col">S/N</th>
                            <th scope="col">ID</th>
                            <th scope="col">Name</th>
                            <th scope="col">Prog</th>
                            <th scope="col">Approval</th>
                            <th scope="col">CA1</th>
                            <th scope="col">CA2</th>
                            <th scope="col">CA3</th>
                            <th scope="col">CA4</th>
                            <th scope="col">EXAM</th>
                            <th scope="col">TOTAL</th>
                            <th scope="col">GRADE</th>


                        </tr>
                        @php
                            $k=1;
                        @endphp

                        @foreach ($regs as $item)
                        <tr>
                            <td>{!! Form::checkbox('student_id[][]', $item->student_id, true) !!}</td>
                            <td>{{$k}}</td>
                            <td align="left">{{ getStudentById($item->student_id)->matric }}</td>
                            <td align="left">{{ getUserByStudentID($item->student_id)->name }}</td>
                            <td align="left">{{ getStudentById($item->student_id)->programName }}</td>
                            <td align="left">{{ ucfirst($item->status) }}</td>
                            <td align="left"> {!! Form::text('student_id[ca1]', number_format(convertToBoolean($item->ca1),2), ['class'=>'form-control']) !!}</td>
                            <td align="left"> {!! Form::text('student_id[ca2]', number_format(convertToBoolean($item->ca2),2), ['class'=>'form-control']) !!}</td>
                            <td align="left"> {!! Form::text('student_id[ca3]', number_format(convertToBoolean($item->ca3),2), ['class'=>'form-control']) !!}</td>
                            <td align="left"> {!! Form::text('student_id[ca4]', number_format(convertToBoolean($item->ca4),2), ['class'=>'form-control']) !!}</td>
                            <td align="left"> {!! Form::text('student_id[exam]', number_format(convertToBoolean($item->exam),2), ['class'=>'form-control']) !!}</td>
                            <td align="left">{{ number_format(convertToBoolean($item->ltotal),2) }}</td>
                            <td align="left">{{ $item->lgrade }}</td>

                        </tr>
                            @php
                                $k++;
                            @endphp
                        @endforeach

                        <tr>
                            <td></td>
                            <td></td>
                            <td>
                                <b>Last updated :</b>

                            </td>
                            <td><b>{{$course->updated_at}}</b></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>

                    </table>

                    {!! Form::submit('Submit Entered Grades', ['class'=>'form-control btn btn-success']) !!}
                    {!! Form::close() !!}




                </div>

                @else

                    <div>
                        You have Submitted this before, Kindly request for re-opening
                    </div>

                @endif



            </div>
        </div>




    </div>



</div>
<!-- end row -->



<script type="text/javascript">
    var toggle = document.getElementById('toggle');
toggle.onclick = function (){
    var multiple = document.getElementsByName(' []');
for (i = 0; i < multiple.length; i ++) {

multiple[i].checked = this.checked;

    }

}
</script>

@endsection
