@extends('layouts.setup')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="text-danger" ><marquee behavior="" direction="left"> <b> <font size="6">{{ getSiteNotification()}}</font> </b> </marquee></div>

            <div class="card">
                <div class="card-header">{{ __('Admissions Home') }}</div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif

                    @include('includes.messages')

                    {{ __('Congratulations on your admission!!!') }}

                    <p><b><u>Follow the Steps below to complete your Admission Process</u></b></p>
                    <p class="text-success">*** Instructions: follow the process step by step to avoid complications <b>(Do Not Jump or Skip any Step(s) )</b></p>
                    <p class="text-danger"><b>*** Payment Instructions:</b>  All Payments are online using your ATM Card (No Direct Bank Transfer, POS, USSD or Direct Debit) </p>

                    <table class="table-control table-bordered" width="100%">
                        <thead>
                            <tr>
                                <th>s/N</th>
                                <th>Step/ Activity</th>
                                <th>Status</th>
                                <th>Action Required</th>
                            </tr>
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

                                        <a href="#" class="btn btn-danger">Print Admission Letter</a> <br>


                                    @else
                                        <a href="{{route('acceptance.fee')}} " class="btn btn-success">Proceed to Pay Acceptance</a>
                                    @endif
                                </td>
                            </tr>

                            <tr>
                                <td>3</td>
                                <td>Acceptance Fee Verified</td>
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

                                        Proceed For Screening

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
                                    @if ($appData->is_screened==1 && $appData->is_paid_tuition==1)

                                        COMPLETED

                                    @elseif ($appData->is_screened==1 && $appData->is_paid_tuition==0)

                                        COMPLETED

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

                                        Proceed to Bursary to Verify School Fees

                                    @elseif ($appData->is_paid_tuition==0 && $appData->is_screened==1)

                                        <a href="#" class="btn btn-success">Proceed to Pay School Fees</a>

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
                                    @if ($appData->schfee_verified==1 && $appData->file_issued==1)

                                        COMPLETED

                                    @elseif ($appData->schfee_verified==1 && $appData->file_issued==0)

                                        Proceed to Screening Office for Collection of File

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
                                    @if ($appData->file_issued==1 && $appData->reg_clearance==1)

                                        COMPLETED

                                    @elseif ($appData->file_issued==1 && $appData->reg_clearance==0)

                                        Proceed to Screening Office for Submission of File

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
                                    @if ($appData->reg_clearance==1 && $appData->reg_courses==1)

                                        COMPLETED

                                    @elseif ($appData->reg_clearance==1 && $appData->reg_courses==0)

                                        COMPLETED


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

                                        CONGRATULATIONS !!!! REGISTRATION COMPLETED, YOU CAN PROCEED FOR LECTURES!!!!

                                    @elseif ($appData->reg_courses==0 && $appData->reg_clearance==1)

                                    <a href="#" class="btn btn-warning">Collect Matric. No and Proceed for Course Registration</a>


                                    @else


                                    @endif
                                </td>

                            </tr>

                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
