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

                        <h4 class="header-title">Billing Report</h4>
                        <p>Total Selected Students : {{ $billingSummary['totalStudents'] }}</p>
                        <p>Total Proposed Students : {{ $billingSummary['totalBilledStudents'] }}</p>
                        <p>Total Exempted Students : {{ $billingSummary['totalExemptedStudents'] }}</p>
                        <p class="card-title-desc"> The List below shows List of Students Recommended for Migration

                        </p>

                    </div>

                    {{-- <div class="col-sn-6">--}}
                    {{-- <a href="{{ route('presenting.create')  }}" class="btn btn-primary">Add new Presentation topic &nbsp; <i--}} {{--                                    class="mdi mdi-file-document-edit-outline"></i></a>--}} {{--                        </div>--}} </div>
                        <a href="{{route('studentMigration.index')}}" class="btn btn-warning">Back</a>
                        <a href="{{route('get.migration')}}" class="btn btn-dark">Proceed to Confirmation</a>
                        <br>
                        <br>
                        <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Matric Number</th>
                                    <th>Name#</th>
                                    <th>Programme</th>
                                    <th>Current Level</th>
                                    <th>Proposed Level</th>
                                    <th>Session</th>
                                    <th>Proposed By</th>
                                    <th>Remark</th>
                                </tr>
                            </thead>


                            <tbody>



                                @foreach( $proposedList as $key => $v )
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $v['matric'] }}</td>
                                    <td>{{ $v['studentName'] }}</td>
                                    <td>{{ $v['program'] }}</td>
                                    <td>{{ $v['old_level'] }}</td>
                                    <td>{{ $v['proposed_level'] }}</td>
                                    <td>{{ getAcademicSessionNameById($v['academic_session']) }}</td>
                                    <td>{{ getUser($v['proposed_by'])->name}}</td>
                                    <td>{{ $v['remark']}}</td>

                                @endforeach
                            </tbody>


                        </table>

                        <a href="{{route('studentMigration.index')}}" class="btn btn-warning">Back</a>
                        <a href="{{route('get.migration')}}" class="btn btn-dark">Proceed to Confirmation</a>

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
