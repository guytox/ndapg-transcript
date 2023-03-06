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

                        <h4 class="header-title">{{$reportTitle." ". $reportDate}}</h4>
                        <p class="card-title-desc"> The List below shows transactions {{$reportDate}}

                        </p>

                    </div>

                    {{-- <div class="col-sn-6">--}}
                    {{-- <a href="{{ route('presenting.create')  }}" class="btn btn-primary">Add new Presentation topic &nbsp; <i--}} {{--                                    class="mdi mdi-file-document-edit-outline"></i></a>--}} {{--                        </div>--}} </div>
                        <a href="{{route('search.daily.transactions')}}" class="btn btn-dark">Back</a>
                        <br>
                        <br>
                        <table id="datatable-buttons" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Txn Date</th>
                                    <th>Matric Number</th>
                                    <th>Name#</th>
                                    <th>Reference</th>
                                    <th>Narration</th>
                                    <th># Paid</th>
                                    <th># Payment Channel</th>
                                    <th>Action</th>

                                </tr>
                            </thead>


                            <tbody>



                                @foreach( $dailyTransactions as $key => $v )
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $v['txn_date'] }}</td>
                                    <td>{{ $v['matric'] }}</td>
                                    <td>{{ $v['name'] }}</td>
                                    <td>{{ $v['txn_ref'] }}</td>
                                    <td>{{ $v['txn_desc'] }}</td>
                                    <td>NGN {{ number_format(convertToNaira($v['txn_amount']),2) }}</td>
                                    <td>{{ $v['txn_channel'] }}</td>



                                    <td>
                                        @if($v['matric'] == null)



                                        @else

                                        <a href="{{ route('view.students.ledger', $v['txn_ref']) }}" target="blank" class="btn btn-primary">View Student Profile</a>
                                    </td>

                                    @endif
                                </tr>


                                @endforeach
                            </tbody>


                        </table>

                        <a href="{{route('search.daily.transactions')}}" class="btn btn-dark">Back</a>

                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
    @endsection

    @section('js')
    <script src="{{ asset('admin/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('admin/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

    
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
