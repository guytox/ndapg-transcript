@extends('layouts.setup')
@section('css')
<!-- DataTables -->
<link href="{{ asset('admin/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
{{-- <link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" --}}
type="text/css" />
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @include('includes.messages')
                <div class="row">
                    <div class="col-sm-6">

                        <h4 class="header-title">Student Balances Report</h4>
                        <p class="card-title-desc"> The List below shows the Account Balance of Each Student

                        </p>

                    </div>

                    {{-- <div class="col-sn-6">--}}
                    {{-- <a href="{{ route('presenting.create')  }}" class="btn btn-primary">Add new Presentation topic &nbsp; <i--}} {{--                                    class="mdi mdi-file-document-edit-outline"></i></a>--}} {{--                        </div>--}} </div>

                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Matric Number</th>
                                    <th>Name</th>
                                    <th>Programme</th>
                                    <th>Level</th>
                                    <th># Amoount Billed</th>
                                    <th># Discounts</th>
                                    <th># Paid</th>
                                    <th># Balance</th>
                                    <th># Over Payment</th>
                                    <th>Action</th>
                                </tr>
                            </thead>


                            <tbody>



                                @foreach( $studentsList as $key => $v )
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $v['StudentMatricNo'] }}</td>
                                    <td>{{ $v['StudentName'] }}</td>
                                    <td>{{ $v['StudentProgramme'] }}</td>
                                    <td>{{ $v['StudentCurrentLevel'] }}</td>
                                    <td>{{ $v['StudentTotalBill'] }}</td>
                                    <td>{{ $v['StudentTotalDiscount'] }}</td>
                                    <td>{{ $v['StudentTotalPaid'] }}</td>
                                    <td>{{ $v['StudentBAlance'] }}</td>
                                    <td>{{ $v['StudentEscessFee'] }}</td>

                                    <td>
                                        @if($v['StudentMatricNo'] == null)



                                        @else

                                        <a href="{{ route('view.students.ledger', $v['id']) }}" class="btn btn-primary">View Ledger</a>
                                    </td>

                                    @endif
                                </tr>


                                @endforeach

                            </tbody>
                        </table>

                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
    @endsection
    @section('js')
    <!-- Required datatable js -->
    <script src="{{ asset('admin/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('admin/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>


    <!-- Datatable init js -->
    <script src="{{ asset('admin/assets/js/pages/datatables.init.js') }}"></script>


    @endsection
