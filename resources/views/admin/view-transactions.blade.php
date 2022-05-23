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

                    <h4 class="header-title">View Transactions</h4>
                    <p class="card-title-desc">Summary of Report</p>
                    <table id="tech-companies-2" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Total Billed Amount</th>
                                <th>Total Discounts</th>
                                <th>Total Payments</th>
                                <th>Balance</th>
                                <th>Excess Payment</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $TotalBill }}</td>
                                <td>{{ $TotalDiscount }}</td>
                                <td>{{ $TotalPaid }}</td>
                                <td>{{ $TotalBal }}</td>
                                <td>{{ $TotalExcess }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <br>

                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <br>
                            <button type="submit" class=""> <a href="{{ route('view.transactions') }}" >Back</a> </button>
                            <table id="tech-companies-1" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th data-priority="1">Matric Number</th>
                                        <th data-priority="1">Name</th>
                                        <th data-priority="3">Programme</th>
                                        <th data-priority="1">Session</th>
                                        <th data-priority="1">Level</th>
                                        <th data-priority="1">Description</th>
                                        <th data-priority="3">Amount</th>
                                        <th data-priority="3"># Discounts</th>
                                        <th data-priority="3">Payment Status</th>
                                        <th data-priority="3">Total Paid</th>
                                        <th data-priority="6">Balance</th>
                                        <th data-priority="6">Excess Payment</th>
                                        <th data-priority="6">Created At</th>
                                        <th data-priority="6">Billing By</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($paymentDetails as $key => $val)

                                    <tr>
                                        <th><span class="co-name">{{$key +1 }}</span> </th>
                                        <td>{{$val['StdMatric']}}</td>
                                        <td>{{$val['StdName']}}</td>
                                        <td>{{$val['StdProgramme']}}</td>
                                        <td>{{$val['SchoolSession']}}</td>
                                        <td>{{$val['StdCurrentLevel']}}</td>
                                        <td>{{$val['PymntDescription']}}</td>
                                        <td>{{$val['PymntAmount']}}</td>
                                        <td>{{$val['PymntDiscount']}}</td>
                                        <td>{{$val['PymntStatus']}}</td>
                                        <td>{{$val['PymntCummulative']}}</td>
                                        <td>{{$val['PymntBalance']}}</td>
                                        <td>{{$val['PymntExcess']}}</td>
                                        <td>{{$val['PymntBillingDate']}}</td>
                                        <td>{{$val['BilledBy']}}</td>
                                    </tr>



                                    @endforeach
                                    <tr>
                                        <td>S/N</td>
                                        <td >Matric Number</td>
                                        <td >Name</td>
                                        <td >Programme</td>
                                        <td >Session</td>
                                        <td >Level</td>
                                        <td >Description</td>
                                        <td >Amount</td>
                                        <td ># Discounts</td>
                                        <td >Payment Status</td>
                                        <td >Total Paid</td>
                                        <td >Balance</td>
                                        <td >Excess Payment</td>
                                        <td >Created At</td>
                                        <td >Billing By</td>
                                    </tr>

                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>Totals</td>
                                        <td>{{ $TotalBill }}</td>
                                        <td>{{ $TotalDiscount }}</td>
                                        <td></td>
                                        <td>{{ $TotalPaid }}</td>
                                        <td>{{ $TotalBal }}</td>
                                        <td>{{ $TotalExcess }}</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                            <button type="submit" class=""> <a href="{{ route('view.transactions') }}" >Back</a> </button>
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
