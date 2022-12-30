@extends('layouts.setup')
@section('css')
    <!-- DataTables -->
    <link href="{{ asset('admin/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet"
          type="text/css" />
    {{-- <link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" --}}
    type="text/css" />
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @include('includes.messages')

                    <h4 class="header-title">List of Applicants who have Paid Application Fees</h4>
                    <p class="card-title-desc">Application Payment Report</p>
                    <table id="tech-companies-2" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Total Billed Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $totalAmount }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <br>

                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <br>
                            <button type="submit" class=""> <a href="{{ route('home') }}" >Close</a> </button>
                            <table id="tech-companies-1" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th data-priority="1">Tx ID</th>
                                        <th data-priority="1">Name</th>
                                        <th data-priority="1">Email</th>
                                        <th data-priority="1">GSM</th>
                                        <th data-priority="3">Amount</th>
                                        <th data-priority="3">Payment Status</th>
                                        <th data-priority="6">Payment Date</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($paidApplicants as $key => $val)

                                    <tr>
                                        <th><span class="co-name">{{$key +1 }}</span> </th>
                                        <td>{{$val['userTxId']}}</td>
                                        <td>{{$val['userName']}}</td>
                                        <td>{{$val['userEmail']}}</td>
                                        <td>{{$val['userGsm']}}</td>
                                        <td>{{$val['userAmount']}}</td>
                                        <td>{{$val['userStatus']}}</td>
                                        <td>{{$val['userPayment']}}</td>
                                    </tr>



                                    @endforeach
                                    <tr>
                                        <td>S/N</td>
                                        <td >Tx Id</td>
                                        <td >Name</td>
                                        <td >Email</td>
                                        <td >GSM</td>
                                        <td >Amount</td>
                                        <td >Payment Status</td>
                                        <td >Payment Date</td>
                                    </tr>

                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>Totals</td>
                                        <td>{{ $totalAmount }}</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                            <button type="submit" class=""> <a href="{{ route('home') }}" >Close</a> </button>
                        </div>

                    </div>



                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
@endsection
@section('js')
    <!-- Responsive Table js -->
    <script src="{{ asset('admin/assets/libs/RWD-Table-Patterns/js/rwd-table.min.js') }}"></script>



    <!-- Datatable init js -->
    <script src="{{ asset('admin/assets/js/pages/table-responsive.init.js') }}"></script>


@endsection
