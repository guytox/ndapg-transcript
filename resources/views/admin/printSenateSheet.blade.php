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

                                <a href="#" class="logo"><img height="50" width="50" src="{{asset('images/ndalogo.png')}}" alt="logo"></a>

                                <h4 class="font-size-24 text-success mb-4">NIGERIAN DEFENCE ACADEMY KADUNA  <br>
                                    {{-- <span class="text-danger font-size-16" ><i> (POSTGRADUATE SCHOOL)</i></span> <br> --}}
                                    {{-- <span class="text-dark font-size-16" >PMB 2109, KADUNA, NIGERIA</span> <br> --}}
                                    {{-- <span class="text-dark font-size-16">https://umm.umm.edu.ng</span> <br> --}}
                                    {{-- <span class="text-dark font-size-20"> <U>COURSE REGISTRATION FORM</U> </span> --}}
                                    <span class="text-dark font-size-20" >{{strtoupper($progDetails->facultyName)}}</span> <br>
                                    <span class="text-danger font-size-18" >DEPARTMENT OF {{strtoupper($progDetails->departmentName)}}</span> <br>
                                    <span class="text-dark font-size-16" >RESULT OF {{strtoupper(getSemesterNameById($semester))}} SEMESTER EXAMINATION {{ getsessionById( $progDetails->schoolsession_id)->name}} SESSION</span> <br>
                                    <span class="text-dark font-size-16" >{{getStudyLevelNameById($progDetails->study_level)}} LEVEL {{strtoupper($progDetails->programName)}}</span>

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
                    @foreach ($resultDetails as $k)

                        <tr>
                            <td rowspan="{{$k['regCount']}}" style="vertical-align: middle;"> {{$sno}} </td>
                            <td rowspan="{{$k['regCount']}}" style="vertical-align: middle;">{{$k['matric']}} </td>
                            <td rowspan="{{$k['regCount']}}" style="vertical-align: middle;">{{$k['name']}}  </td>
                            <td rowspan="{{$k['regCount']}}" style="vertical-align: middle;">{{$k['gender']}} </td>
                            <td rowspan="{{$k['regCount']}}" style="vertical-align: middle;">{{$k['svc']}} </td>
                            <td rowspan="{{$k['regCount']}}" style="vertical-align: middle;">{{$k['bn']}} </td>
                            @if ($k['regItems'][0]['sessRegCheck'])
                                <td style="color: #f83606"><b><u>{{$k['regItems'][0]['courseCode']}}</u></b></td>
                                <td>{{$k['regItems'][0]['creditUnits']}}</td>
                                <td style="color: #f83606"> <b><u>{{intval(convertToNaira($k['regItems'][0]['gtotal']))}}</u></b> </td>
                                <td>{{$k['regItems'][0]['ggrade']}}</td>
                                <td>{{$k['regItems'][0]['twgp']/$k['regItems'][0]['creditUnits']}}</td>
                                <td>{{$k['regItems'][0]['twgp']}}</td>
                            @else
                                <td>{{$k['regItems'][0]['courseCode']}}</td>
                                <td>{{$k['regItems'][0]['creditUnits']}}</td>
                                <td> {{intval(convertToNaira($k['regItems'][0]['gtotal']))}}</td>
                                <td>{{$k['regItems'][0]['ggrade']}}</td>
                                <td>{{$k['regItems'][0]['twgp']/$k['regItems'][0]['creditUnits']}}</td>
                                <td>{{$k['regItems'][0]['twgp']}}</td>
                            @endif

                            <td rowspan="{{$k['regCount']}}"  style="vertical-align: middle;">{{$k['ltcr']}} </td>
                            <td rowspan="{{$k['regCount']}}" style="vertical-align: middle;">{{$k['tcr']}} </td>
                            <td rowspan="{{$k['regCount']}}" style="vertical-align: middle;">{{$k['ltwgp']}} </td>
                            <td rowspan="{{$k['regCount']}}" style="vertical-align: middle;">{{$k['twgp']}} </td>
                            <td rowspan="{{$k['regCount']}}" style="vertical-align: middle;">{{$k['lcgpa']}} </td>
                            <td rowspan="{{$k['regCount']}}" style="vertical-align: middle;">{{$k['cgpa']}} </td>
                            <td rowspan="{{$k['regCount']}}" style="vertical-align: middle;">{{$k['remark']}} <br>
                                @foreach ($k['carryOvers'] as $m )
                                    {{$m->courseCode}},<br>
                                @endforeach

                            </td>
                        </tr>


                        @php
                            $chk =1;
                        @endphp


                        @while ($chk < $k['regCount']-1)


                            @if ($k['regItems'][$chk]['sessRegCheck'])
                                <tr>
                                    <td style="color: #f83606"><b><u>{{$k['regItems'][$chk]['courseCode']}}</u></b></td>
                                    <td>{{$k['regItems'][$chk]['creditUnits']}} </td>
                                    <td style="color: #f83606"><b><u>{{intval(convertToNaira($k['regItems'][$chk]['gtotal']))}}</u></b></td>
                                    <td>{{$k['regItems'][$chk]['ggrade']}}</td>
                                    <td>{{$k['regItems'][$chk]['twgp']/$k['regItems'][$chk]['creditUnits']}}</td>
                                    <td>{{$k['regItems'][$chk]['twgp']}} </td>
                                </tr>
                            @else
                                <tr>
                                    <td>{{$k['regItems'][$chk]['courseCode']}} </td>
                                    <td>{{$k['regItems'][$chk]['creditUnits']}} </td>
                                    <td>{{intval(convertToNaira($k['regItems'][$chk]['gtotal']))}}</td>
                                    <td>{{$k['regItems'][$chk]['ggrade']}}</td>
                                    <td>{{$k['regItems'][$chk]['twgp']/$k['regItems'][$chk]['creditUnits']}}</td>
                                    <td>{{$k['regItems'][$chk]['twgp']}} </td>
                                </tr>
                            @endif



                                @php
                                    $chk ++;
                                @endphp

                        @endwhile





                            <tr>
                                <td></td>
                                <td>{{$k['cur']}}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>{{$k['twgpCount']}}</td>
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
