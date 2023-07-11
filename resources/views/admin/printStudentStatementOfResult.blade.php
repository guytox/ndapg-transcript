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


                            <div class="text-left mb-5">

                                <img src="{{asset('/images/passport/'.getUser(getStudentByStudentId($Monitors->student_id)->user_id,'all')->passport)}}" height="100" width="80" alt="{{getUser(getStudentByStudentId($Monitors->student_id)->user_id,'all')->name}}">
                            </div>
                        </td>
                        <td>
                            @include('includes.reportheaderstudentStatementOfResult')
                        </td>


                        <td >
                            <div class="text-right mb-5" >

                                @if ($Monitors->status ==='approved')
                                    {!! QrCode::size(100)->generate(route('verify.student.reg',['$id'=>$Monitors->uid])) !!}
                                @else
                                    Reg. Not Yet Approved
                                @endif



                            </div>
                        </td>
                    </tr>
                </table>
                <table class="table table-centered table-nowrap mb-0">
                    <tr >
                        <td>Last Updated</td>
                        <td>{{$Monitors->updated_at}}</td>
                        <td class="text-center">
                            <u>STUDENT DATA</u>
                        </td>
                        <td class="text-right"> <b>Matric:</b> </td>
                        <td class="text-danger text-left font-size-20"><b>{{getStudentByUserId(getStudentByStudentId($Monitors->student_id)->user_id)->matric}}</b></td>

                    </tr>
                    <tr>
                        <td>
                            <b>Name
                            <br>Programme
                            <br>Department
                            <br>Faculty</b>
                        </td>
                        <td class="text-left">{{getUser(getStudentByStudentId($Monitors->student_id)->user_id,'all')->name}}
                            <br>{{getProgrammeDetailById($Monitors->program_id, 'name')}}
                            <br> {{getDepartmentDetailById(getProgrammeDetailById($Monitors->program_id, 'department'), 'name')}}
                            <br> {{getDepartmentDetailById(getProgrammeDetailById($Monitors->program_id, 'department'), 'all')->faculty->name}} </td>
                        <td></td>
                        <td class="text-right">
                            <b>Session.
                            <br> Semester
                            <br> Level
                            <br> <i class="text-danger">Approval Status</i>
                            </b>
                        </td>
                        <td class="text-left">
                            {{ getsessionById($Monitors->session_id)->name}}
                            <br> {{ getSemesterDetailsById($Monitors->semester_id)}}
                            <br> {{ getStudyLevelDetailsById($Monitors->level_id) }}
                            <br> {{ $Monitors->status }}

                        </td>
                    </tr>



                </table>

                <hr>

                <div class="text-center">

                    <hr>
                    <table width="100%" border="1" cellspacing="1" cellpadding="5" >
                        <tr>
                            <th scope="col">S/N</th>
                            <th scope="col">Code</th>
                            <th scope="col">Course Title</th>
                            <th scope="col">Credits</th>
                            <th scope="col">Total</th>
                            <th scope="col">Grade</th>
                            <th scope="col" align="left">category</th>
                            <th scope="col" align="left">Remark</th>

                        </tr>
                        @php
                            $k=1;
                        @endphp
                        @foreach ($Monitors->RegMonitorItems as $item)
                        <tr>
                            <td>{{$k}}</td>
                            <td align="center">{{ getCourseDetailsById($item->course_id,'code') }}</td>
                            <td align="left"  >{{ getCourseDetailsById($item->course_id,'title') }}</td>
                            <td>{{ getCourseDetailsById($item->course_id,'credits') }}</td>
                            <td>{{ number_format(convertToNaira($item->gtotal),2) }}</td>
                            <td>{{ $item->ggrade }}</td>
                            <td align="center">{{$item->category}}</td>
                            <td>
                                @if ($item->is_passed ==1)
                                    PASSED
                                @else
                                    FAILED
                                @endif
                            </td>


                        </tr>

                            @php
                                $k++;
                            @endphp
                        @endforeach
                        <tr>

                            <td colspan="8"> <b>RESULT SUMMARY</b> </td>
                        </tr>
                    </table>

                    <table width="100%" border="1" cellspacing="0" cellpadding="0">
                                    <tr border="1">
                                        <td colspan="2" align="center">CURRENT</td>
                                        <td width="16%" rowspan="5">&nbsp;</td>
                                        <td colspan="2" align="center">PREVIOUS</td>
                                        <td width="19%" rowspan="5">&nbsp;</td>
                                        <td colspan="2" align="center">CUMMULATIVE</td>
                                    </tr>
                                    <tr border="1">
                                        <td width="15%">CUR</td>
                                        <td width="8%">{{$Monitors->cur}}</td>
                                        <td width="10%">LTCR</td>
                                        <td width="9%">{{$Monitors->ltcr}}</td>
                                        <td width="8%">TCR</td>
                                        <td width="14%">{{$Monitors->tcr}}</td>
                                    </tr>
                                    <tr border="1">
                                        <td>CUE</td>
                                        <td>{{$Monitors->cue}}</td>
                                        <td>LTCE</td>
                                        <td>{{$Monitors->ltce}}</td>
                                        <td>TCE</td>
                                        <td>{{$Monitors->tce}}</td>
                                    </tr>
                                    <tr border="1">
                                        <td>CGP</td>
                                        <td>{{$Monitors->wgp}}</td>
                                        <td>LTGP</td>
                                        <td>{{$Monitors->ltwgp}}</td>
                                        <td>TGP</td>
                                        <td>{{$Monitors->twgp}}</td>
                                    </tr>
                                    <tr border="1">
                                        <td>GPA</td>
                                        <td>{{convertToNaira($Monitors->gpa)}}</td>
                                        <td>LCGPA</td>
                                        <td>{{convertToNaira($Monitors->lcgpa)}}</td>
                                        <td>CGPA</td>
                                        <td>{{convertToNaira($Monitors->cgpa)}}</td>
                                    </tr>


                    </table>



                    <table class="table table-centered table-nowrap mb-0">
                        <tr>
                            @if ($Monitors->status==='pending')
                                <td class="text-center">
                                    <i><b>Approvals ->:
                                    Registration Officer Approval: </b>
                                    @if ($Monitors->ro_approval==='0')
                                        Not Approved
                                    @elseif ($Monitors->ro_approval==='1')
                                        Approved @ {{ $Monitors->ro_approvalDate }}
                                    @endif

                                    ,<b> HOD Approval: </b>

                                    @if ($Monitors->hod_approval==='0')
                                        Not Approved
                                    @elseif ($Monitors->hod_approval==='1')
                                        Approved @ {{ $Monitors->hod_approvalDate }}
                                    @endif
                                    ,<b> Dean Approval: </b>
                                    @if ($Monitors->dean_approval==='0')
                                        Not Approved
                                    @elseif ($Monitors->dean_approval==='1')
                                        Approved @ {{ $Monitors->dean_approvalDate }}
                                    @endif
                                    <br>
                                    </i>
                                </td>
                            @elseif ($Monitors->status==='approved')
                                <td>

                                </td>
                                <td>
                                    __________________________
                                    <br> {{getuser($Monitors->hod_approver,'name') }}
                                    <br> H.O.D.
                                    <br> Date: {{ $Monitors->hod_approvalDate }}
                                </td>
                                <td>

                                </td>
                            @endif

                        </tr>
                    </table>




                </div>

                {{-- {{$paymentData}}
                <br> <br>
                {{$studentData}} --}}


            </div>
        </div>




    </div>



</div>
<!-- end row -->

@endsection
