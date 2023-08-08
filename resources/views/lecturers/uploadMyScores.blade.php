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

                @include('includes.messages')

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
                    <h6><b> LIST OF REGISTERED STUDENTS FOR:--> {{ getSemesterCourseById($course->course_id)->courseCode}}( {{ getSemesterCourseById($course->course_id)->courseTitle}}):--> {{ ucfirst(getSemesterDetailsById($course->semester_id)) }} Semester:--> {{ getsessionById($course->session_id)->name }} Session</b></h6>
                    <hr>
                    <table class="table table-bordered">

                        <form action="{{ route('lecturer.manual.upload', ['as'=>'ortesenKwagh']) }}" method="post" >
                            @csrf
                            <input type="hidden" name="id" value="{{$course->uid}}">
                            <input type="hidden" name="context" value="{{$_GET['context']}}">

                        <tr>
                            <th>Chk</th>

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
                            $k=0;
                        @endphp

                        @foreach ($regs as $v => $item)


                        <tr>
                            <td><input type="checkbox" name="student_id[{{$item->id}}][id]" value="{{$item->id}}" @if ($item->status ==='approved') checked @endif ></td>

                            <td>{{$k+1}}</td>
                            <td align="left">{{ getStudentById($item->student_id)->matric }}</td>
                            <td align="left">{{ getUserByStudentID($item->student_id)->name }}</td>
                            <td align="left">{{ getStudentById($item->student_id)->programName }}</td>
                            <td align="left">{{ ucfirst($item->status) }}</td>

                            @if ($item['cfm_ca1'] === '0' && $_GET['context'] ==='8X34' && $item->status ==='approved')
                                <td align="left"> <input type="text" name="student_id[{{$item->id}}][ca1]" value="{{convertToBoolean($item->ca1)}}" class="form-control"></td>
                            @elseif ($item->cfm_ca1 === '0' && $_GET['context']==='3XE8' && $item->status ==='approved')
                                <td align="left"> <input type="text" name="student_id[{{$item->id}}][ca1]" value="{{convertToBoolean($item->ca1)}}" class="form-control"></td>
                            @else
                                <td align="left"> <input type="text" name="student_id[{{$item->id}}][ca1]" value="{{convertToBoolean($item->ca1)}}" class="form-control text-danger" readonly></td>
                            @endif


                            @if ($item->cfm_ca2 ==='0' && $_GET['context']==='8OE4' && $item->status ==='approved')
                                <td align="left"> <input type="text" name="student_id[{{$item->id}}][ca2]" value="{{convertToBoolean($item->ca2)}}" class="form-control"></td>
                            @elseif ($item->cfm_ca2 ==='0' && $_GET['context']==='3XE8' && $item->status ==='approved')
                                <td align="left"> <input type="text" name="student_id[{{$item->id}}][ca2]" value="{{convertToBoolean($item->ca2)}}" class="form-control"></td>
                            @else
                                <td align="left"> <input type="text" name="student_id[{{$item->id}}][ca2]" value="{{convertToBoolean($item->ca2)}}" class="form-control text-danger" readonly></td>
                            @endif


                            @if ($item->cfm_ca3 ==='0' && $_GET['context']==='3XS4' && $item->status ==='approved')
                                <td align="left"> <input type="text" name="student_id[{{$item->id}}][ca3]" value="{{convertToBoolean($item->ca3)}}" class="form-control"></td>
                            @elseif ($item->cfm_ca3 ==='0' && $_GET['context']==='3XE8' && $item->status ==='approved')
                                <td align="left"> <input type="text" name="student_id[{{$item->id}}][ca3]" value="{{convertToBoolean($item->ca3)}}" class="form-control"></td>
                            @else
                                <td align="left"> <input type="text" name="student_id[{{$item->id}}][ca3]" value="{{convertToBoolean($item->ca3)}}" class="form-control text-danger" readonly></td>
                            @endif


                            @if ($item->cfm_ca4 ==='0' && $_GET['context']==='3x34' && $item->status ==='approved')
                                <td align="left"> <input type="text" name="student_id[{{$item->id}}][ca4]" value="{{convertToBoolean($item->ca4)}}" class="form-control"></td>
                            @elseif ($item->cfm_ca4 ==='0' && $_GET['context']==='3XE8' && $item->status ==='approved')
                                <td align="left"> <input type="text" name="student_id[{{$item->id}}][ca4]" value="{{convertToBoolean($item->ca4)}}" class="form-control"></td>
                            @else
                                <td align="left"> <input type="text" name="student_id[{{$item->id}}][ca4]" value="{{convertToBoolean($item->ca4)}}" class="form-control text-danger" readonly></td>
                            @endif


                            @if ($item->cfm_exam ==='0' && $_GET['context']==='8X3X' && $item->status ==='approved')
                                <td align="left"> <input type="text" name="student_id[{{$item->id}}][exam]" value="{{convertToBoolean($item->exam)}}" class="form-control"></td>
                            @elseif ($item->cfm_exam ==='0' && $_GET['context']==='3XE8' && $item->status ==='approved')
                                <td align="left"> <input type="text" name="student_id[{{$item->id}}][exam]" value="{{convertToBoolean($item->exam)}}" class="form-control "></td>
                            @else
                                <td align="left"> <input type="text" name="student_id[{{$item->id}}][exam]" value="{{convertToBoolean($item->exam)}}" class="form-control text-danger" readonly></td>
                            @endif

                            <td align="left">{{ convertToBoolean($item->ltotal) }}</td>
                            <td align="left">{{ $item->lgrade }}</td>

                        </tr>
                            @php
                                $k++;
                            @endphp
                        @endforeach

                        <tr>
                            <td></td>
                            <td></td>
                            <td colspan="2">
                                <b>Last updated :</b>

                            </td>
                            <td colspan="2"><b>{{$course->updated_at}}</b></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>

                    </table>

                    <button type="submit" class=" form-control btn btn-success">Submit Entered Grades</button>
                    <hr>
                    <a href="{{ route('lecturer.grading.home',['as'=>'ortesenKwagh']) }}" class="btn btn-warning form-control">Return to courses</a>
                </form>




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
