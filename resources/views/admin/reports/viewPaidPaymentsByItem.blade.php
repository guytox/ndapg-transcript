@extends('layouts.setup')
@section('css')
<!-- DataTables -->
<link href="{{ asset('admin/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('admin/assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @include('includes.messages')
                <div class="row">
                    <div class="col-sm-6">

                        <h4 class="header-title">List of {{$feePurpose->name}} Payments</h4>

                        <h3>Total Payments: NGN{{number_format(convertToNaira($totalPaid),2)}} </h3>


                        <p class="card-title-desc"> The List below shows List of pending Credo payments

                        </p>

                    </div>

                    {{-- <div class="col-sn-6">--}}
                    {{-- <a href="{{ route('presenting.create')  }}" class="btn btn-primary">Add new Presentation topic &nbsp; <i--}} {{--                                    class="mdi mdi-file-document-edit-outline"></i></a>--}} {{--                        </div>--}} </div>
                        <a href="{{route('home')}}" class="btn btn-dark">Back</a>
                        <br>
                        <br>
                        <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>ID</th>
                                    <th>Name</th>
                                    @if ($feePurpose->description ==('laboratory-fees'|| 'medical-expenses'))
                                        <th>Dept.</th>
                                    @endif
                                    <th>Tx ID</th>
                                    <th>Amount </th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>status</th>

                                </tr>
                            </thead>

                            <tbody>

                                @foreach( $paymentDetails as $key => $v )
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $v->feePayment->user->applicant->form_number }}</td>
                                    <td>{{ $v->feePayment->user->name }}</td>
                                    @if ($feePurpose->description ==('laboratory-fees' || 'medical-expenses'))
                                        <td>{{ $v->feePayment->user->applicant->program->department->name}}</td>
                                    @endif
                                    <td>{{ $v->feePayment->txn_id }}</td>
                                    <td>{{ number_format(convertToNaira($v->amount),2) }}</td>
                                    <td>{{ $v->status }}</td>
                                    <td>{{ $v->updated_at }}</td>
                                    <td>{{ $v->status}} </td>



                                @endforeach
                            </tbody>


                        </table>

                        <a href="{{route('home')}}" class="btn btn-dark">Back</a>

                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
    @endsection

    @section('js')
    <script src="{{ asset('admin/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('admin/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>


    <script src="{{ asset('admin/assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('admin/assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('admin/assets/libs/jszip/jszip.min.js') }}"></script>

    <script src="{{ asset('admin/assets/libs/pdfmake/build/pdfmake.min.js') }}"></script>
    <script src="{{ asset('admin/assets/libs/pdfmake/build/vfs_fonts.js') }}"></script>
    <script src="{{ asset('admin/assets/libs/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('admin/assets/libs/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('admin/assets/libs/datatables.net-buttons/js/buttons.colVis.min.js') }}"></script>
    <!-- Responsive examples -->

    <script src="{{ asset('admin/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('admin/assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>

    <!-- Datatable init js -->
    <script src="{{ asset('admin/assets/js/pages/datatables.init.js') }}"></script>
    @endsection
