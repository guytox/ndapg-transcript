@extends('layouts.reports')

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

<style>
.table-bordered td, .table-bordered th {
border: 1px solid #eff2f7;
border: 1px solid #18191a;
}


</style>


<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">

                <table class="table  table-centered table-nowrap mb-0">
                    <tr>
                        <td colspan="">



                        </td>
                        <td>

                            <div class="text-center mb-5">

                                <a href="#" class="logo"><img height="50" width="50" src="{{asset('images/ndalogo.jpg')}}" alt="logo"></a>

                                <h4 class="font-size-24 text-success mb-4">{{env('ORT_NAME_FULL')}}  <br>
                                    {{-- <span class="text-danger font-size-16" ><i> (POSTGRADUATE SCHOOL)</i></span> <br> --}}
                                    {{-- <span class="text-dark font-size-16" >PMB 2109, KADUNA, NIGERIA</span> <br> --}}
                                    {{-- <span class="text-dark font-size-16">https://umm.umm.edu.ng</span> <br> --}}
                                    {{-- <span class="text-dark font-size-20"> <U>COURSE REGISTRATION FORM</U> </span> --}}
                                    <span class="text-dark font-size-20" >{{strtoupper($programme->department->faculty->name)}}</span> <br>
                                    <span class="text-danger font-size-18" >DEPARTMENT OF {{strtoupper($programme->department->name)}}</span> <br>
                                    <span class="text-dark font-size-16" >WITHDRAWAL LIST FOR  {{strtoupper($semester_id->name)}} SEMESTER  {{ $session_id->name}} SESSION</span> <br>
                                    <span class="text-dark font-size-16" >{{getStudyLevelNameById($programme->level_id)}} LEVEL {{strtoupper($programme->name)}}</span>

                                </h4>

                            </div>

                        </td>


                        <td >

                        </td>
                    </tr>
                </table>


                <div class="text-center">


                    <table class="table table-bordered table-hover">
                        <tr>
                            <th rowspan="2" scope="col">S/N</th>
                            <th rowspan="2"  scope="col">NDA No</th>
                            <th rowspan="2"  scope="col">Name</th>
                            <th rowspan="2"  scope="col">Sex</th>
                            <th rowspan="2"  scope="col" align="left">svc</th>
                            <th rowspan="2"  scope="col" align="left">bn</th>
                            <th rowspan="2"  scope="col" align="left">Course <br> Code</th>
                            <th rowspan="2"  rowspan="2"  scope="col" align="left">Unit (U)</th>
                            <th rowspan="2"  scope="col" align="left">Raw <br> Score</th>
                            <th rowspan="2"  scope="col" align="left">Grade <br> Obt'd</th>
                            <th rowspan="2"  scope="col" align="left">Grade <br> Point <br> (GP)</th>
                            <th rowspan="2"  scope="col" align="left">Credit <br> Point <br> (CPxU)</th>
                            <th colspan="2" scope="col" align="left">Cummulative <br> Unit <br> (CU)</th>
                            <th colspan="2" scope="col" align="left">Cummulative Credit <br> Point (CCP)</th>
                            <th colspan="2" scope="col" align="left">CGPA</th>
                            <th rowspan="2" scope="col" align="left">Remarks</th>
                        </tr>
                        <tr>

                            <td>LS</td>
                            <td>TS</td>
                            <td>LS</td>
                            <td>TS</td>
                            <td>LS</td>
                            <td>TS</td>
                        </tr>
                        <tr>
                            <td>(a)</td>
                            <td>(b)</td>
                            <td>(c)</td>
                            <td>(d)</td>
                            <td>(e)</td>
                            <td>(f)</td>
                            <td>(g)</td>
                            <td>(h)</td>
                            <td>(i)</td>
                            <td>(k)</td>
                            <td>(k)</td>
                            <td>(l)</td>
                            <td>(m1)</td>
                            <td>(m2)</td>
                            <td>(n1)</td>
                            <td>(n2)</td>
                            <td>(o1)</td>
                            <td>(o2)</td>
                            <td>(p)</td>

                        </tr>
                        @php
                            $sno =1;
                        @endphp
                    @foreach ($lists as $k)

                        <tr>
                            <td rowspan="{{count($k->regs)+1}}" style="vertical-align: middle;"> {{$sno}} </td>
                            <td rowspan="{{count($k->regs)+1}}" style="vertical-align: middle;">{{$k->student->matric}} </td>
                            <td rowspan="{{count($k->regs)+1}}" style="vertical-align: middle;">{{$k->student->user->name}}  </td>
                            <td rowspan="{{count($k->regs)+1}}" style="vertical-align: middle;">{{ ucfirst($k->student->user->profile->gender) }} </td>
                            <td rowspan="{{count($k->regs)+1}}" style="vertical-align: middle;">{{$k->student->svc}} </td>
                            <td rowspan="{{count($k->regs)+1}}" style="vertical-align: middle;">{{$k->student->bn}} </td>
                            @if ($k->regs->first()->is_reg_sess==1)
                                <td style="color: #f83606"><b><u>{{getCourseDetailsById($k->regs->first()->course_id,'code')}}</u></b></td>
                                <td>{{getCourseDetailsById($k->regs->first()->course_id,'credits')}}</td>
                                <td style="color: #f83606"> <b><u>{{intval(convertToNaira($k->regs->first()->gtotal))}}</u></b> </td>
                                <td>{{$k->regs->first()->ggrade}}</td>
                                <td>{{$k->regs->first()->twgp/getCourseDetailsById($k->regs->first()->course_id,'credits')}}</td>
                                <td>{{sprintf('%02d', $k->regs->first()->twgp)}}</td>
                            @else
                                <td>{{getCourseDetailsById($k->regs->first()->course_id,'code')}}</td>
                                <td>{{getCourseDetailsById($k->regs->first()->course_id,'credits')}}</td>
                                <td> {{intval(convertToNaira($k->regs->first()->gtotal))}}</td>
                                <td>{{$k->regs->first()->ggrade}}</td>
                                <td>{{$k->regs->first()->twgp/getCourseDetailsById($k->regs->first()->course_id,'credits')}}</td>
                                <td>{{sprintf('%02d', $k->regs->first()->twgp)}}</td>
                            @endif

                            <td rowspan="{{count($k->regs)+1}}"  style="vertical-align: middle;"> - </td>
                            <td rowspan="{{count($k->regs)+1}}" style="vertical-align: middle;">{{$k->tcr}} </td>
                            <td rowspan="{{count($k->regs)+1}}" style="vertical-align: middle;"> - </td>
                            <td rowspan="{{count($k->regs)+1}}" style="vertical-align: middle;">{{$k->twgp}} </td>
                            <td rowspan="{{count($k->regs)+1}}" style="vertical-align: middle;"> - </td>
                            <td rowspan="{{count($k->regs)+1}}" style="vertical-align: middle;">{{convertToNaira($k->cgpa)}} </td>
                            <td rowspan="{{count($k->regs)+1}}" style="vertical-align: middle;">{{$k->degree_class}} <br>
                                {{$k->message}} <br>
                                @foreach (getCarryOvers($k->student_id) as $m )
                                    {{ getSemesterCourseById($m->course_id)->courseCode}}, <br>
                                @endforeach

                            </td>
                        </tr>

                        @foreach ($k->regs as $m)

                            @if ($m->course_id == $k->regs->first()->course_id)

                            @else

                                @if ($m->is_reg_sess==1)
                                    <tr>
                                        <td style="color: #f83606"><b><u>{{getCourseDetailsById($m->course_id,'code')}}</u></b></td>
                                        <td>{{getCourseDetailsById($m->course_id,'credits')}}</td>
                                        <td style="color: #f83606"><b><u>{{intval(convertToNaira($m->gtotal))}}</u></b> </td>
                                        <td>{{$m->ggrade}}</td>
                                        <td>{{$m->twgp/getCourseDetailsById($m->course_id,'credits')}}</td>
                                        <td>{{sprintf('%02d', $m->twgp)}}</td>
                                    </tr>
                                @else
                                    <tr>
                                        <td>{{getCourseDetailsById($m->course_id,'code')}}</td>
                                        <td>{{getCourseDetailsById($m->course_id,'credits')}}</td>
                                        <td> {{intval(convertToNaira($m->gtotal))}}</td>
                                        <td>{{$m->ggrade}}</td>
                                        <td>{{$m->twgp/getCourseDetailsById($m->course_id,'credits')}}</td>
                                        <td>{{sprintf('%02d', $m->twgp)}}</td>
                                    </tr>
                                @endif

                            @endif



                        @endforeach

                        <tr>
                            <td></td>
                            <td>{{$k->tcr}}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>{{$k->twgp}}</td>
                        </tr>





                            @php
                                $sno ++;
                            @endphp

                    @endforeach




                    </table>



                    <hr>



                </div>




            </div>
        </div>




    </div>



</div>
<!-- end row -->

@endsection
