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

                                <img src="{{asset(user()->passport)}}" height="240" width="220" alt="{{user()->name}}">
                            </div>
                        </td>
                        <td>
                            @include('includes.examcardheader')
                        </td>


                        <td >
                            <div class="text-right mb-5" >

                                @if ($Monitors->status ==='approved')
                                    {!! QrCode::size(160)->generate(route('verify.student.examcard',['$id'=>$Monitors->uid])) !!}
                                @else
                                    Reg. Not Yet Approved
                                @endif



                            </div>
                        </td>
                    </tr>
                </table>
                <table class="table table-centered table-nowrap mb-0">
                    <tr >
                        <td></td>
                        <td> <h3><u>STUDENT DATA</u> </h3> </td>
                        <td class="text-center">

                        </td>
                        <td class="text-right"> <b>Matric:</b> </td>
                        <td class="text-danger text-left font-size-20"><b>{{getStudentByUserId(user()->id)->matric}}</b></td>

                    </tr>
                    <tr>
                        <td>
                            <b>Name
                            <br>Programme
                            <br>Department
                            <br>Faculty</b>
                        </td>
                        <td class="text-left">{{user()->name}}
                            <br>{{getProgrammeDetailById(getStudentByUserId(user()->id)->program_id, 'name')}}
                            <br> {{getDepartmentDetailById(getProgrammeDetailById(getStudentByUserId(user()->id)->program_id, 'department'), 'name')}}
                            <br> {{getDepartmentDetailById(getProgrammeDetailById(getStudentByUserId(user()->id)->program_id, 'department'), 'all')->faculty->name}} </td>
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
                            <br> {{getStudyLevelDetailsById(getUser(user()->id,'all')->current_level)}}
                            <br> {{ $Monitors->status }}

                        </td>
                    </tr>

                    <tr>
                        <td class="text-left">Reg. Description</td>
                        <td colspan="4" class="text-left"> <b>{{ getCurriculaById($Monitors->curricula_id,"name")}},  {{activeSession()->name}} Session.</b> </td>
                    </tr>

                </table>

                <hr>

                <div class="text-center">
                    <h5><b> LIST OF REGISTERED COURSES</b></h5>
                    <hr>
                    <table class="table table-bordered ">
                        <tr>
                            <th scope="col">S/N</th>
                            <th scope="col">Code</th>
                            <th scope="col">Title</th>
                            <th scope="col">Credits</th>
                            <th scope="col" align="left">Signature</th>

                        </tr>
                        @php
                            $k=1;
                        @endphp
                        @foreach ($Monitors->RegMonitorItems as $item)
                        <tr>
                            <td>{{$k}}</td>
                            <td align="left">{{ getCourseDetailsById($item->course_id,'code') }}</td>
                            <td align="left">{{ getCourseDetailsById($item->course_id,'title') }}</td>
                            <td>{{ getCourseDetailsById($item->course_id,'credits') }}</td>
                            {{-- <td align="left">{{$item->category}}</td> --}}
                            <td></td>


                        </tr>
                            @php
                                $k++;
                            @endphp
                        @endforeach

                        <tr>
                            <td></td>
                            <td></td>
                            <td>
                                <b>Date Submitted :</b>{{$Monitors->created_at}},
                                <b>Total Registered Credits</b>
                            </td>
                            <td><b>{{$Monitors->total_credits}}</b></td>
                            <td></td>
                        </tr>
                    </table>

                    <table class="table table-centered table-nowrap mb-0">
                        <tr>
                            <td colspan="3" class="text-centered">
                                <b>FINANCIAL RECORDS</b>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Total Fee Paid:</b> {{number_format(convertToNaira($submission->amount_paid), 2)}}
                            </td>
                            <td>
                                <b>Payment Status:</b> {{strtoupper($submission->payment_status)}}
                            </td>
                            <td>
                                <b>Receipt No:</b> {{$submission->uid}}
                            </td>
                        </tr>

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
                                    __________________________
                                    <br>
                                    <br> Accountant Signature & Stamp
                                    <br>
                                    <br> Date: _______________________________
                                </td>
                                <td>
                                    __________________________
                                    <br>
                                    <br> Student Signature
                                    <br>
                                    <br> Date: _______________________________
                                </td>
                                <td>
                                    __________________________
                                    <br> Prof. George Moses
                                    <br> Deputy Dean  Signature
                                    <br>
                                    <br> Date: _______________________________
                                </td>
                            @endif

                        </tr>
                    </table>


                    @if ($Monitors->status ==='approved')

                        <i><b>Note:</b> This Examination Card can be verified by scanning the above QR-Code</i>,  <b>Date Printed:</b>{{now()}}

                    @endif

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
