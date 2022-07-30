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

                                <img src="{{asset('/images/'.getUser(getStudentByStudentId($Monitors->student_id)->user_id,'all')->passport)}}" height="240" width="220" alt="{{getUser(getStudentByStudentId($Monitors->student_id)->user_id,'all')->name}}">
                            </div>
                        </td>
                        <td>
                            <div class="text-center mb-5">
                                <a href="#" class="logo"><img height="50" width="50" src="{{asset('assets/img/logo/ummlogo.jpg')}}" alt="logo"></a>

                                <h4 class="font-size-24 text-default mb-4">UNIVERSIT OF MKAR, MKAR <br>
                                    <span class="text-success font-size-16" >P.M.B. 05 Gboko, Benue State</span> <br>
                                    <span class="text-success font-size-16">https://umm.edu.ng, +234 803 314 2160</span> <br>
                                    <span class="text-danger font-size-20"> <U>COURSE REGISTRATION FORM</U> </span></h5>
                            </div>
                        </td>


                        <td >
                            <div class="text-right mb-5" >

                                @if ($Monitors->status ==='approved')
                                    {!! QrCode::size(160)->generate(route('verify.student.reg',['$id'=>$Monitors->uid])) !!}
                                @else
                                    Reg. Not Approved Yet
                                @endif



                            </div>
                        </td>
                    </tr>
                </table>
                <table class="table table-centered table-nowrap mb-0">
                    <tr >
                        <td>Date Submitted</td>
                        <td>{{$Monitors->created_at}}</td>
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
                            <br> {{getStudyLevelDetailsById(getUser(getStudentByStudentId($Monitors->student_id)->user_id, 'level'))}}
                            <br> {{ $Monitors->status }}

                        </td>
                    </tr>

                    <tr>
                        <td class="text-left">Reg. Description</td>
                        <td colspan="4" class="text-left"> <b>{{ getCurriculaById($Monitors->curricula_id,"name")}}</b> </td>
                    </tr>

                </table>

                <hr>

                <div class="text-center">
                    <h5><b> LIST OF REGISTERED COURSES</b></h5>
                    <hr>
                    <table class="table table-bordered">
                        <tr>
                            <th scope="col">S/N</th>
                            <th scope="col">Code</th>
                            <th scope="col">Title</th>
                            <th scope="col">Credits</th>
                            <th scope="col" align="left">category</th>

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
                            <td align="left">{{$item->category}}</td>


                        </tr>
                            @php
                                $k++;
                            @endphp
                        @endforeach

                        <tr>
                            <td></td>
                            <td></td>
                            <td>
                                <b>Total Registered Credits</b>
                            </td>
                            <td><b>{{$Monitors->total_credits}}</b></td>
                            <td></td>
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
                                    __________________________
                                    <br> {{getuser($Monitors->ro_approver,'name') }}
                                    <br> Reg. Officer
                                    <br> Date: {{ $Monitors->ro_approvalDate }}
                                </td>
                                <td>
                                    __________________________
                                    <br> {{getuser($Monitors->hod_approver,'name') }}
                                    <br> H.O.D.
                                    <br> Date: {{ $Monitors->hod_approvalDate }}
                                </td>
                                <td>
                                    __________________________
                                    <br> {{getuser($Monitors->dean_approver,'name') }}
                                    <br> Dean
                                    <br> Date: {{ $Monitors->dean_approvalDate }}
                                </td>
                            @endif

                        </tr>
                    </table>


                    @if ($Monitors->status ==='approved')

                        <i><b>Note:</b> This form can be verified by scanning the above QR-Code
                    @else

                    <hr>

                    <table>
                        <hr>
                        <b>Approval Section:</b>
                        <thead>
                            <td></td>
                            <td></td>
                        </thead>
                        <tbody>
                            <tr>
                                {!! Form::open(['route' =>['reg.store'] , 'method' => 'POST']) !!}

                                {!! Form::hidden('regMonitor[]', $Monitors->uid, ['class' => 'form-control']) !!}

                                <td colspan="2">
                                    {!! Form::label('approveAs', 'In My Capacity As: *') !!}
                                    {!! Form::select('approveAs', $staffRoles, null, ['class' => 'form-control', 'required']) !!}
                                </td>
                            </tr>

                            <tr>

                                <td>
                                    {!! Form::label('approveAs', 'Select Action (Approve or reject)') !!}
                                    {!! Form::select('action', [''=>'N/A','1'=>"Approve", '2'=>'Reject'], '', ['class'=>'form-control','required']) !!}
                                </td>
                            </tr>
                            <tr>

                                <td>
                                    {!! Form::label('message', "Insert Message for students if you are rejecting") !!}
                                    {!! Form::text('message', '', ['class'=>'form-control']) !!}
                                </td>

                            </tr>

                            <tr>

                                <td>
                                    {!! Form::submit('Submit Approval Decision', ['class'=>'btn btn-success','required']) !!}
                                </td>

                            </tr>

                        </tbody>

                        {!! Form::close() !!}
                    </table>

                    <a href="{{route('reg.index')}}" class='btn btn-warning'> Back</a>

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
