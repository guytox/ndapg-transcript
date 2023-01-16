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
                            <img src="{{asset(getUserById($toVerify->user_id)->passport)}}" alt="Passport Not Yet Uploaded" height="200" width="150">
                        </td>
                        <td>
                            @include('includes.applicantAcknowledgementSheetHeader')

                        </td>


                        <td >
                            {!! QrCode::size(160)->generate(route('verify.applicant.form',['id'=> $toVerify->uid])) !!}
                            {{-- <a href="{{route('verify.applicant.form',['id'=> $submitted->uid])}}"> Visit Link</a> --}}
                        </td>
                    </tr>
                </table>
                <hr>

                <div class="text-center">
                    <h5><b> CANDIDATE DETAILS </b></h5>
                    <hr>
                    <table class="table table-bordered">
                        <tr >
                            <td></td>
                            <td>
                                {{-- <h5><u>COURSE APPLIED FOR</u> </h5> --}}
                            </td>
                            <td class="text-center">

                            </td>
                            <td class="text-right"> <b>Application No:</b> </td>
                            <td class="text-danger text-left font-size-20"><b>{{$toVerify->form_number}}</b></td>

                        </tr>
                        <tr>
                            <td align="left">
                                <b>Name
                                <br>Applied Programme
                                <br>Date of Birth
                                <br>E-mail
                                <br> Contact Address
                            </b>
                                {{-- <br>Department --}}
                                {{-- <br>Faculty</b> --}}
                            </td>
                            <td class="text-left"><b>{{getUserById($toVerify->user_id)->name}}</b>
                                <br>{{getProgrammeDetailById($toVerify->program_id, 'name')}}
                                <br>{{getUserById($toVerify->user_id)->profile->dob}}
                                <br> {{getUserById($toVerify->user_id)->email}}
                                <br> {{getUserById($toVerify->user_id)->profile->contact_address}}
                            </td>
                                {{-- <br> {{getDepartmentDetailById(getProgrammeDetailById($applicantProfile->applicant_program, 'department'), 'name')}} --}}
                                {{-- <br> {{getDepartmentDetailById(getProgrammeDetailById($applicantProfile->applicant_program, 'department'), 'all')->faculty->name}} </td> --}}
                            <td></td>
                            <td class="text-right">
                                <b>Session.
                                <br>State.
                                <br> GSM
                                {{-- <br> Semester --}}
                                {{-- <br> Study Level --}}
                                {{-- <br> <i class="text-danger">Admission Status</i> --}}
                                </b>
                            </td>
                            <td class="text-left">
                                {{ getsessionById(getApplicationSession())->name}}
                                <br> {{getStateNameById(getUserById($toVerify->user_id)->profile->state_id)}}
                                <br> {{getUserById($toVerify->user_id)->phone_number }}

                                {{-- <br> {{ getSemesterDetailsById(2)}} --}}
                                {{-- <br> ...... --}}
                                {{-- <br> Not Admitted --}}

                            </td>
                        </tr>

                    </table>





                </div>

                <hr>
                <p><b>NOTE:</b> Printing this form only signifes that your application is received and being processed. You
                    are required to bring along this "ACKNOWLEDGEMENT SLIP" and a print out of your  submitted "SPGS APPLICATION FORM"  to the PG School on the screening date.</p>

                    <p>All Academic transcripts should be sent in hard copy by respective University to the Secretary, Nigerian Defence Academy Postgraduate School, Kaduna.</p>




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
