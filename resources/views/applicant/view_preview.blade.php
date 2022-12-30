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
                            <img src="{{asset($applicantUser->passport)}}" alt="Passport Not Yet Uploaded" height="200" width="150">
                        </td>
                        <td>
                            @include('includes.applicantPreviewSheet')

                        </td>


                        <td >
                            {!! QrCode::size(160)->generate(route('verify.student.reg',['$id'=> $submitted->uid])) !!}
                        </td>
                    </tr>
                </table>
                <hr>

                <div class="text-center">
                    <h5><b> APPLICATION DETAILS </b></h5>
                    <hr>
                    <table class="table table-bordered">
                        <tr >
                            <td></td>
                            <td> <h5><u>COURSE APPLIED FOR</u> </h5> </td>
                            <td class="text-center">

                            </td>
                            <td class="text-right"> <b>Application No:</b> </td>
                            <td class="text-danger text-left font-size-20"><b>{{$submitted->form_number}}</b></td>

                        </tr>
                        <tr>
                            <td align="left">
                                <b>Name
                                <br>Applied Programme
                                <br>Department
                                <br>Faculty</b>
                            </td>
                            <td class="text-left"><b>{{$applicantUser->name}}</b>
                                <br>{{getProgrammeDetailById($applicantProfile->applicant_program, 'name')}}
                                <br> {{getDepartmentDetailById(getProgrammeDetailById($applicantProfile->applicant_program, 'department'), 'name')}}
                                <br> {{getDepartmentDetailById(getProgrammeDetailById($applicantProfile->applicant_program, 'department'), 'all')->faculty->name}} </td>
                            <td></td>
                            <td class="text-right">
                                <b>Session.
                                <br> Semester
                                <br> Study Level
                                <br> <i class="text-danger">Admission Status</i>
                                </b>
                            </td>
                            <td class="text-left">
                                {{ getsessionById(2)->name}}
                                <br> {{ getSemesterDetailsById(2)}}
                                <br> ......
                                <br> Not Admitted

                            </td>
                        </tr>

                    </table>



                    <table class="table table-centered table-nowrap mb-0">
                        <tr >
                            <td></td>
                            <td> <h5><u>BIO-DATA</u> </h5> </td>
                            <td class="text-center">

                            </td>
                            <td class="text-right"> </b> </td>
                            <td class="text-danger text-left font-size-20"></td>

                        </tr>
                        <tr>
                            <td align="left">
                                <b>Gender
                                <br>Marrital Status
                                <br>Date of Birth
                                <br>E-mail</b>
                            </td>
                            <td class="text-left">{{$applicantProfile->gender}}
                                <br>{{$applicantProfile->marital_status}}
                                <br>{{$applicantProfile->dob}}
                                <br> {{$applicantUser->email}} </td>
                            <td></td>
                            <td class="text-right">
                                <b>Nationality.
                                <br>State.
                                <br> Local Government
                                <br> GSM
                                </b>
                            </td>
                            <td class="text-left">
                                {{$applicantProfile->nationality}}
                                <br> {{getStateNameById($applicantProfile->state_id)}}
                                <br> {{$applicantProfile->local_government}}
                                <br> {{$applicantUser->phone_number                                                                                                                                                                                                         }}

                            </td>
                        </tr>

                    </table>
                    <hr>




                </div>

                <hr>

                <div class="text-center">
                    <h5><b> Service Records</b></h5>
                    <hr>

                    <h2 class="header-title">NYSC Discharge Status</h2>

                    <hr>
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>NYSC Status</th>
                                <td><b><i>"{{$applicantProfile->nysc}}"</i></b></td>
                            </tr>
                            <tr>
                                <th>NYSC Certificate</th>
                                <td><a class="btn btn-success" href="{{asset($applicantProfile->nysc_path)}}">View NYSC Certificate</a></td>
                            </tr>
                            <tr>
                                <th>Serving Military Official</th>
                                @if ($applicantProfile->is_serving_officer==1)
                                <th>Yes</th>

                                @else
                                <th>No</th>
                                @endif
                            </tr>
                        </thead>
                    </table>

                </div>

                <hr>

                <div class="text-center">
                    <h5><b> O-LEVEL DETAILS </b></h5>
                    <hr>
                    {{-- Begin O-level details --}}

                    @foreach($OlevelResults as $olevel)

                        <h4>Result For: {{ $olevel->exam_details['Exam_body'] }}  {{ $olevel->exam_details['Exam_type'] }} {{ $olevel->exam_details['Exam_year'] }}  - {{ $olevel->sitting }}</h4>
                        <br>
                    <div class="body table-responsive mt-4">
                        <table class="table table-hover table-bordered">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Subject</th>
                                <th>Grade</th>
                            </tr>
                            </thead>
                            <tbody>

                            <tr>
                                <td>1</td>
                                <td>English Language</td>
                                <td>{{ $olevel->exam_details['English'] }}</td>
                            </tr>

                            <tr>
                                <td>2</td>
                                <td>Mathematics </td>
                                <td>{{ $olevel->exam_details['Mathematics'] }}</td>
                            </tr>

                            <tr>
                                <td>3</td>
                                <td>{{ $olevel->exam_details['subject_3']['subject_name'] }}</td>
                                <td>{{ $olevel->exam_details['subject_3']['grade'] }}</td>
                            </tr>

                            <tr>
                                <td>4</td>
                                <td>{{ $olevel->exam_details['subject_4']['subject_name'] }}</td>
                                <td>{{ $olevel->exam_details['subject_4']['grade'] }}</td>
                            </tr>

                            <tr>
                                <td>5</td>
                                <td>{{ $olevel->exam_details['subject_5']['subject_name'] }}</td>
                                <td>{{ $olevel->exam_details['subject_5']['grade'] }}</td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                    @endforeach

                    {{-- Begin Academic Qualifications Upload --}}

                </div>

                <hr>

                <div class="text-center">
                    <h5><b> Academic Qualifications </b></h5>
                    <hr>
                    {{-- Begin O-level details --}}
                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                        <tr>
                            <th>Cert Type</th>
                            <th>Awarding Institution</th>
                            <th>Qualification Obtained</th>
                            <th>Class</th>
                            <th>Year Obtained</th>
                            <th>View</th>
                        </tr>
                        </thead>


                        <tbody>

                        @foreach($userQualifications as $qualification)
                            <tr>
                                <td>{{ $qualification->certificate_type  }}</td>
                                <td>{{ $qualification->awarding_institution  }}</td>
                                <td>{{ $qualification->qualification_obtained  }}</td>
                                <td>{{ $qualification->class ?? 'N/A'  }}</td>
                                <td>{{ \Carbon\Carbon::parse($qualification->year_obtained)->year  }}</td>
                                <td>
                                {{-- <a href="{{$qualification->uid}}" target="_blank" class="btn btn-danger btn-sm" target="_blank">View Certificate</a> --}}
                                <img class="rounded-circle header-profile-user" src="{{ asset($qualification->path) }}" alt="No Upload Yet">
                                <a href="{{asset($qualification->path)}}" target="_blank" class="btn btn-warning btn-sm">Show Certificate</a>
                                </td>

                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                    {{-- End Academic Qualifications Upload --}}
                </div>


                <hr>

                <div class="text-center">
                    <h5><b> Professional Qualifications </b></h5>
                    <hr>
                    {{-- Begin O-level details --}}
                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                        <tr>
                            <th>Cert Type</th>
                            <th>Awarding Institution</th>
                            <th>Qualification Obtained</th>
                            <th>Class</th>
                            <th>Year Obtained</th>
                            <th>View</th>
                        </tr>
                        </thead>


                        <tbody>

                        @foreach($userProfessionalQualifications as $q)
                            <tr>
                                <td>{{ $q->certificate_type  }}</td>
                                <td>{{ $q->awarding_institution  }}</td>
                                <td>{{ $q->qualification_obtained  }}</td>
                                <td>{{ $q->class ?? 'N/A'  }}</td>
                                <td>{{ \Carbon\Carbon::parse($q->year_obtained)->year  }}</td>
                                <td>
                                {{-- <a href="{{$qualification->uid}}" target="_blank" class="btn btn-danger btn-sm" target="_blank">View Certificate</a> --}}
                                <img class="rounded-circle header-profile-user" src="{{ asset($q->path) }}" alt="No Upload Yet">
                                <a href="{{asset($q->path)}}" target="_blank" class="btn btn-warning btn-sm">Show Certificate</a>
                                </td>

                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                    {{-- End Academic Qualifications Upload --}}
                </div>

                <hr>

                <div class="text-center">
                    <h5><b> Research Proposal </b></h5>
                    <hr>

                    <h2 class="header-title">Details of Research Proposal</h2>

                    <hr>
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <td><b><i>"{{$proposal->summary}}"</i></b></td>
                            </tr>
                            <tr>
                                <th>Proposal text</th>
                                <td><a class="btn btn-success" href="{{asset($proposal->path)}}">View Research Proposal text</a></td>
                            </tr>
                        </thead>
                    </table>

                </div>

                <hr>

                <div class="text-center">
                    <h5><b> Referees </b></h5>
                    <hr>
                    {{-- Begin O-level details --}}

                    <h2 class="header-title">List of Nominated Referees</h2>

                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>s/n</th>
                                <th>Name</th>
                                <th>email</th>
                                <th>gsm</th>
                                <th>Respnse Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($userReferee)
                            @php
                                $sn = 1;
                            @endphp
                                @foreach ($userReferee as $v)

                                <tr>
                                    <td> {{$sn}}</td>
                                    <td>{{$v->name}}</td>
                                    <td>{{$v->email}}</td>
                                    <td>{{$v->phone}}</td>
                                    @if ($v->is_filled ==1)
                                        <td class="text text-success">Referee has Responded</td>
                                    @else
                                        <td class="text text-danger">Not Responded <a href="{{route('delete.referee',['uid'=>$v->uid])}}" class="btn btn-danger"> Remove</a></td>
                                    @endif

                                </tr>

                                @php
                                    $sn++
                                @endphp

                                @endforeach
                            @else
                            <tr>
                                <td colspan="5"> No Referees Nominated Yet</td>
                            </tr>

                            @endif


                        </tbody>
                    </table>


                    {{-- End Academic Qualifications Upload --}}
                </div>

                @if ($submitted->is_submitted ==0)
                    <div class="text-center">
                        <a href="{{route('application.submit',['id'=>$applicantUser->id])}}" class="btn btn-danger form-control">Submit Application Form</a>

                    </div>
                @else

                    <div class="text-center">
                        <a href="{{route('preview.submitted.application',['id'=>$applicantUser->id])}}" class="btn btn-success form-control">Print Acknowledgement Slip</a>

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
