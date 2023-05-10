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
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body">

                <table class="table  table-centered table-nowrap mb-0">
                    <tr>
                        <td colspan="">
                            <a href="#" class="logo"><img height="200" width="150" src="{{asset('assets/img/logo/logo.jpg')}}" alt="logo"></a>
                        </td>

                        <td>
                            <div class="text-center mb-5">

                                <h4 class="font-size-24 text-success mb-4">NIGERIAN DEFENCE ACADEMY KADUNA  <br>
                                    <span class="text-danger font-size-16" ><i> (POSTGRADUATE SCHOOL, RIBADU CAMPUS)</i></span> <br>
                                    <span class="text-dark font-size-16" >PMB 2109, KADUNA, NIGERIA</span> <br>
                                    <span class="text-dark font-size-16">https://spgs.nda.edu.ng</span> <br>

                                </h4>

                            </div>
                        </td>

                        <td class="text-wrap" >
                            PMB, 2109, <br>
                            Kaduna, <br>
                            Nigeria, <br>
                            Tel:+234 813 2627 428
                        </td>
                    </tr>
                </table>

                <hr>

                <div class="text-left">

                    <table class="table table-bordered table-hover">
                        <tr>
                            <td><h4>{{$appDetails->form_number}}</h4></td>
                            <td></td>
                            <td class="text-center">{{$appDetails->admitted_at}}</td>
                        </tr>

                    </table>


                    <div class="card-body">

                        <p><h4> <b> Dear {{$apUser->name}}, </b> </h4></p>
                        <br>

                        <p class="text-center text-dark"><b><u><h2 class="text-center">PROVISIONAL ADMISSION INTO POSTGRADUATE PROGRAMME FOR 2022/2023 ACADEMIC SESSION</h2 ></u></b></p>
                            <br>
                        <ol>
                            <li><h4>I am pleased to inform you that on the recommendation of the Postgraduate School Admission Committee and the
                                approval of the Commandant Nigerian Defence Academy,you have been offered provisional admission to pursue
                                a postgraduate programme leading to the award of  <b>{{$apProg->name}} </b> in the Department of <b>{{$apProg->department->name}}</b>.
                                </h4>
                            </li>

                            <br> <br>

                            <li>
                                <h4>You are expected to register for the programme at the Postgraduate School not later than 2 weeks from 10 May 2023
                                when the 2022/2023 session commences. Additionally, you are to present your original credentials for screening.
                                Late registration attracts a fine of N10,000.00. Please note that late registration lapses on 2 June 2023.</h4>
                            </li>

                            <br> <br>

                            <li>
                                <h4>Congratulations.</h4>
                            </li>
                        </ol>
                        <br>

                        <br>

                        {{-- <h4><p >Signed</p></h4> --}}
                        <br>
                        <img src="{{asset('assets/img/logo/RegistrarSig.png')}}" alt="">
                        <p><h4> <b>AY BALLA </b> <span class="font-size-10">B.Sc., MPA, MASSA</span> </p>
                        <p><h5>Deputy Registrar</h5></p>

                    </div>



                    <hr>



                </div>




            </div>
        </div>




    </div>



</div>
<!-- end row -->

@endsection
