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




                @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">

                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    @foreach ($errors->all() as $error)
                    <div class="">{{ $error }}</div>
                    @endforeach
                </div>
                @endif

                <table class="table">
                    <tr>
                        <td colspan="2">
                            <div class="text-left mb-5">

                                <img src="{{asset('/images/'.$studentData->passport)}}" height="240" width="220" alt="Student passport Should appear here">
                            </div>
                        </td>
                        <td>
                            @include('includes.studentInvoiceReportHeader')
                        </td>
                        <td colspan="2">
                            <div class="text-right mb-5" >

                                {!! QrCode::size(200)->generate($payurl) !!}
                                <br> verification link
                                <br> <a href="{{$payurl}}">{{$payurl}}</a>

                            </div>
                        </td>
                    </tr>
                    <tr >
                        <td>Date</td>
                        <td>{{$paymentData->created_at}}</td>
                        <td class="text-center">
                            <u>APPLICANT DATA</u>
                        </td>
                        <td class="text-right"> <b>Ref:</b> </td>
                        <td class="text-danger text-left font-size-20"><b>{{$paymentData->txn_id}}</b></td>

                    </tr>
                    <tr>
                        <td><b>Name <br> <br>Programme <br>Department <br>Faculty</b></td>
                        <td class="text-left">{{$studentData->name}} <br>{{$dept->name}} <br> {{$dept->department->name}} <br> {{$dept->department->faculty->name}} </td>
                        <td></td>
                        <td class="text-right"><b>Matric No. <br> Level <br> Amount Paid <br> Status </b></td>
                        <td class="text-left">{{$paymentData->user->username}} <br> {{$paymentData->user->current_level}} <br> {{number_format(convertToNaira($paymentData->amount_paid),2)}} <br> {{$paymentData->payment_status}} </td>
                    </tr>

                    <tr>
                        <td><b>Invoice Amount</b></td>
                        <td class="text-left">NGN {{number_format(convertToNaira($paymentData->amount_billed),2)}}</td>
                        <td></td>
                        <td class="text-right text-danger"><b>Balance</b></td>
                        <td class="text-left">{{number_format(convertToNaira($paymentData->balance),2)}}</td>
                    </tr>
                    <tr>
                        <td class="text-left">Payment Description</td>
                        <td colspan="4" class="text-left"> <b>{{$paymentData->narration}} </b> </td>
                    </tr>

                </table>



                <table class="table table-bordered">
                    <tr>
                        <th class="text-left">S/N</th>
                        <th class="text-left">item</th>
                        <th class="text-right">Amount</th>
                        <th class="text-right"></th>
                    </tr>
                    <tr>
                        <td>1.</td>
                        <td>{{ getPaymentPurposeById($paymentData->payment_config_id) }}</td>
                        <td class="text-right">{{number_format(convertToNaira($paymentData->amount_billed),2)}}</td>
                        <td></td>
                    </tr>

                    {{-- <tr>
                        <td>2.</td>
                        <td>{{ getPaymentPurposeById($paymentData->payment_config_id) }}</td>
                        <td class="text-right">{{number_format($paymentData->amount_billed,2)}}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>3.</td>
                        <td>{{ getPaymentPurposeById($paymentData->payment_config_id) }}</td>
                        <td class="text-right">{{number_format($paymentData->amount_billed,2)}}</td>
                        <td></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td><b>Total Bill</b></td>
                        <td class="text-right"><b> {{number_format($paymentData->amount_billed,2)}} </b></td>
                        <td></td>
                    </tr> --}}
                </table>
                <hr>

                <div class="text-center">
                    <h5><b> LIST OF PAYMENTS MADE BY APPLICANT WITH DATES</b></h5>
                    <hr>
                    <table class="table table-bordered">
                        <tr>
                            <th>S/N</th>
                            <th>Reference</th>
                            <th>Amount</th>
                            <th>Transaction Date</th>
                        </tr>
                        @php
                            $k=1;
                        @endphp
                        @foreach ($paymentData->paymentLogs as $v)
                        <tr>
                            <td>{{$k}}</td>
                            <td>{{$v->uid}}</td>
                            <td>{{number_format(convertToNaira($v->amount_paid),2)}}</td>
                            <td>{{$v->created_at}}</td>
                        </tr>
                            @php
                                $k++;
                            @endphp
                        @endforeach
                        <tr>
                            <td></td>
                            <td><b>Total</b></td>
                            <td><b>NGN {{number_format(convertToNaira($paymentData->amount_paid),2)}}</b></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-center">
                                <i><b>Note:</b> This form can be verified by scanning the above QR-Code which should resolve to <a href="{{$payurl}}">{{$payurl}}</a> </i>
                            </td>
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
