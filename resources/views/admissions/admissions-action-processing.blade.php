@extends('layouts.setup')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="text-danger" ><marquee behavior="" direction="left"> <b> <font size="6">{{ getSiteNotification()}}</font> </b> </marquee></div>

            <div class="card">
                <div class="card-header">{{ __('Admissions Processing Details') }}</div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif

                    @include('includes.messages')

                    {{ __('Applicant Details!!!') }}

                    <p><b><u>Step By Step Approvals for Registration</u></b></p>
                    <p class="text-danger">*** Instructions: Do Not Allow the Applicant to skip a step</p>

                    <table class="table-control table-bordered" width="100%">
                        <tr>
                            <th>Application Number</th>
                            <td colspan="2"><h2 class="text-success">{{$appData->form_number}}</h2></td>
                            <td rowspan="6"> <img src="{{ asset($appUser->passport)}}" alt="Applicant Passport" height="200" width="150"> </td>
                        </tr>
                        <tr>
                            <th>Applicant Name</th>
                            <td>{{$appUser->name}}</td>

                        </tr>
                        <tr>
                            <th>Program Applied</th>
                            <td>{{getProgramNameById($appData->program_id)}}</td>
                        </tr>

                        <tr>
                            <th>Admission Status</th>
                            <td>
                                @if ($appData->is_admitted == 1)
                                    ADMITTED
                                @else
                                    NOT ADMITTED
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>GSM</th>
                            <td>{{$appUser->phone_number}} </td>
                        </tr>

                        <tr>
                            <th>Email</th>
                            <td>{{$appUser->email}} </td>
                        </tr>

                    </table>

                    <hr>


                    <table class="table-control table-bordered" width="100%">
                        <tr><h2 class="text-center">ADMISSION PROCESSING PROGRESS CHECKER</h2></tr>

                        <hr>
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Step/ Activity</th>
                                <th>Status</th>
                                <th>Action Required</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Admission Granted</td>
                                <td>
                                    @if ($appData->is_admitted==1)
                                        <span title="Admission Granted" > &#9989;</span>

                                    @else
                                        <span title="Admission Not Granted" > &#10060;</span>
                                    @endif
                                </td>

                                <td>
                                    @if ($appData->is_admitted==1)
                                        COMPLETED <br>
                                    @else
                                        <span title="Admission Not Granted" > Get Admitted First</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Acceptance Fee Payment</td>
                                <td>
                                    @if ($appData->acceptance_paid==1)
                                        <span title="You have Paid Acceptance Fees" > &#9989;</span>

                                    @else
                                        <span title="Acceptance Fee Not Paid" > &#10060;</span>
                                    @endif
                                </td>

                                <td>
                                    @if ($appData->acceptance_paid==1 && $appData->acc_verified==1)

                                        COMPLETED <br>

                                        {{-- <a href="#" class="btn btn-danger">Print Admission Letter</a> --}}

                                    @elseif ($appData->acceptance_paid==1 && $appData->acc_verified==0)

                                        ACCEPTANCE FEE PAYMENT MADE

                                    @elseif ($appData->acceptance_paid==0 && $appData->acc_verified==0)
                                        Request Applicant to go and Pay Acceptance Fees
                                    @endif
                                </td>
                            </tr>

                            <tr>
                                <td>3</td>
                                <td>Acceptance Fee Verification</td>
                                <td>
                                    @if ($appData->acc_verified==1)
                                        <span title="Acceptance Fee Verified" > &#9989;</span>

                                    @else
                                        <span title="Acceptance Fee Not Verified" > &#10060;</span>
                                    @endif
                                </td>

                                <td>
                                    @if ($appData->acc_verified==1 && $appData->is_screened==1)

                                        COMPLETED

                                    @elseif ($appData->acc_verified==1 && $appData->is_screened==0)

                                        COMPLETED

                                        @elseif ($appData->acceptance_paid==1 && $appData->acc_verified==0)

                                        @role('admin|bursary')
                                            <a href="{{route('view.acceptance.invoice',['id'=>$appData->uid])}}" class="btn btn-danger" target="_blank">Check Payment Evidence</a><br> <br>
                                            {!! Form::open(['route'=>'effect.admission.processing', 'method'=>'POST']) !!}
                                            {!! Form::hidden('appId', $appData->id, []) !!}

                                            <div class="form-group">
                                                {!! Form::label('form_action', 'Select What you want to do') !!}
                                                {!! Form::select('form_action', [''=>'N/A', '4'=>'Verify Acceptance Fee','10'=>'Reject Acceptance Fees'], '', ['class'=>'form-control','required']) !!}
                                            </div>

                                            {!! Form::submit('Verify Acceptance Fee', ['class'=>' btn btn-success']) !!}
                                            {!! Form::close() !!}
                                        @endrole
                                        @role('admin|registry')
                                            Request Student to Proceed to Bursary to Verify Acceptance Fee Payment
                                        @endrole
                                    @else

                                    @endif
                                </td>

                            </tr>

                            <tr>
                                <td>4</td>
                                <td>Screening Cleared</td>
                                <td>
                                    @if ($appData->is_screened==1)
                                        <span title="Screening Cleared" > &#9989;</span>

                                    @else
                                        <span title="Screening Not Cleared" > &#10060;</span>
                                    @endif
                                </td>

                                <td>
                                    @if ($appData->is_screened==1 && $appData->acc_verified==1)

                                        COMPLETED

                                    @elseif ($appData->is_screened==0 && $appData->acc_verified==1)

                                    @role('admin|registry')
                                        <a href="{{route('preview.submitted.application',['id'=>$appData->user_id])}} " class="btn btn-danger" target="_blank">Preview Application</a> <br>
                                        {!! Form::open(['route'=>'effect.admission.processing', 'method'=>'POST']) !!}
                                            {!! Form::hidden('appId', $appData->id, []) !!}

                                            <div class="form-group">
                                                {!! Form::label('form_action', 'Select What you want to do') !!}
                                                {!! Form::select('form_action', [''=>'N/A', '3'=>'Screen Candidate','10'=>'Reject Candidate'], '', ['class'=>'form-control','required']) !!}
                                            </div>

                                            {!! Form::submit('Screen Applicant', ['class'=>' btn btn-success']) !!}
                                            {!! Form::close() !!}
                                    @endrole

                                    @role('admin|bursary')
                                        Request Candidate to proceed for Screening
                                    @endrole


                                    @else



                                    @endif
                                </td>

                            </tr>

                            <tr>
                                <td>5</td>
                                <td>School Fee Paid</td>
                                <td>
                                    @if ($appData->is_paid_tuition==1)
                                        <span title="School Fees Paid" > &#9989;</span>

                                    @else
                                        <span title="School Fees Not Paid" > &#10060;</span>
                                    @endif
                                </td>

                                <td>
                                    @if ($appData->is_paid_tuition==1 && $appData->schfee_verified==1)

                                        COMPLETED

                                    @elseif ($appData->is_paid_tuition==1 && $appData->schfee_verified==0)

                                        COMPLETED

                                    @elseif ($appData->is_paid_tuition==0 && $appData->is_screened==1)

                                        Request Student to go and pay school fees

                                    @else

                                    @endif
                                </td>

                            </tr>

                            <tr>
                                <td>6</td>
                                <td>School Fee Verification</td>
                                <td>
                                    @if ($appData->schfee_verified==1)
                                        <span title="School Fees Verified" > &#9989;</span>

                                    @else
                                        <span title="School Fee Not Verified" > &#10060;</span>
                                    @endif
                                </td>

                                <td>
                                    @if ($appData->schfee_verified==1 && $appData->is_paid_tuition==1)

                                        COMPLETED

                                    @elseif ($appData->schfee_verified==0 && $appData->is_paid_tuition==1)

                                    @role('admin|bursary')

                                        <a href="#" class="btn btn-danger">Verify School Fees</a>  Action No 4<br>
                                    @endrole

                                    @role('admin|registry')
                                        Request the Student to Verify School Fees at the Bursary
                                    @endrole


                                    @else

                                    @endif
                                </td>

                            </tr>

                            <tr>
                                <td>7</td>
                                <td>Collected File</td>
                                <td>
                                    @if ($appData->file_issued==1)

                                        <span title="File Collected" > &#9989;</span>

                                    @else
                                        <span title="File Not Collected" > &#10060;</span>
                                    @endif
                                </td>

                                <td>
                                    @if ($appData->file_issued==1 && $appData->schfee_verified==1)

                                        COMPLETED

                                    @elseif ($appData->file_issued==0 && $appData->schfee_verified==1)

                                        @role('admin|registry')
                                            <a href="#" class="btn btn-danger">Issue File</a>  Action No 5<br>
                                        @endrole

                                        @role('admin|bursary')
                                            Request Student to Proceed to PG School for Collection of File
                                        @endrole

                                    @else

                                    @endif
                                </td>

                            </tr>

                            <tr>
                                <td>8</td>
                                <td>Cleared For Course Registration</td>
                                <td>
                                    @if ($appData->reg_clearance==1)
                                        <span title="Cleared For Course Registration" > &#9989;</span>

                                    @else
                                        <span title="You Cannot Register Courses Yet" > &#10060;</span>
                                    @endif
                                </td>

                                <td>
                                    @if ($appData->reg_clearance==1 && $appData->file_issued==1)

                                        COMPLETED

                                    @elseif ($appData->reg_clearance==0 && $appData->file_issued==1)

                                        @role('admin|registry')
                                            <a href="#" class="btn btn-danger">Receive File and Clear for Registration</a>  Action No 6<br>
                                        @endrole

                                        @role('admin|bursary')
                                            Request Candidate to Submit file at the PG School
                                        @endrole


                                    @else

                                    @endif
                                </td>

                            </tr>

                            <tr>
                                <td>9</td>
                                <td>Course Registration</td>
                                <td>
                                    @if ($appData->reg_courses==1)
                                        <span title="Course Registration Successful" > &#9989;</span>

                                    @else
                                        <span title="Courses Not Registered Yet" > &#10060;</span>
                                    @endif
                                </td>

                                <td>
                                    @if ($appData->reg_courses==1)

                                        REGISTRATION COMPLETED, YOU CAN NOW REQUEST STUDENT PROCEED FOR LECTURES!!!!

                                    @elseif ($appData->reg_courses==0 && $appData->reg_clearance==1)

                                        @role('admin|registry')
                                            <a href="#" class="btn btn-warning">Print & Issue Matric Number</a>
                                        @endrole

                                        @role('admin|bursary')
                                            Request Student to Collect New Matric Number from PG School and Proceed for course registration
                                        @endrole


                                    @else


                                    @endif
                                </td>

                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
