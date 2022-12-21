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
                            <img src="{{asset(user()->passport)}}" alt="Passport Not Yet Uploaded" height="200" width="150">
                        </td>
                        <td>
                            @include('includes.applicantPreviewSheet')

                        </td>


                        <td >
                            {!! QrCode::size(160)->generate(route('verify.student.reg',['$id'=>user()->id])) !!}
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
                            <td class="text-danger text-left font-size-20"><b>NDA/SPGS/2022/000001</b></td>

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
                                <br> Level
                                <br> <i class="text-danger">Approval Status</i>
                                </b>
                            </td>
                            <td class="text-left">
                                {{ getsessionById(2)->name}}
                                <br> {{ getSemesterDetailsById(2)}}
                                <br> ......
                                <br> ....

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
                                <br>Faculty</b>
                            </td>
                            <td class="text-left">{{$applicantProfile->gender}}
                                <br>{{$applicantProfile->marital_status}}
                                <br>{{$applicantProfile->dob}}
                                <br> {{getDepartmentDetailById(getProgrammeDetailById($applicantProfile->applicant_program, 'department'), 'all')->faculty->name}} </td>
                            <td></td>
                            <td class="text-right">
                                <b>Nationality.
                                <br>State.
                                <br> Local Government
                                <br> Town
                                </b>
                            </td>
                            <td class="text-left">
                                {{$applicantProfile->nationality}}
                                <br> {{$applicantProfile->state_id}}
                                <br> {{$applicantProfile->local_government}}
                                <br> {{$applicantProfile->town}}

                            </td>
                        </tr>

                    </table>


                </div>


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
