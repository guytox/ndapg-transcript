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

                        <h4 class="header-title">Approved Schedules for presentation</h4>
                        <p class="card-title-desc"> list of all the schedules approved for the conference

                        </p>

                    </div>

                    @hasrole('admin')

                    <div class="col-sn-6">
                        <a href="{{ route('admin.schedule-create') }}" class="btn btn-primary">Add Schedule &nbsp; <i class="mdi mdi-timeline-plus"></i></a>
                    </div>

                    @endhasanyrole
                </div>

                <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>Topic</th>
                            <th>Author</th>
                            <th>Venue</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Virtual Link</th>
                            <th>Date Scheduled</th>

                        </tr>
                    </thead>


                    <tbody>

                        @foreach($schedules as $schedule)
                        <tr>
                            <td>{{ $schedule->presentation->title }}</td>
                            <td>{{ $schedule->presentation->user->name ??'' }}</td>
                            <td>{{ $schedule->location }}</td>
                            <td>{{ humanReadableDate($schedule->date)  }}</td>
                            <td>{{ $schedule->schedule_time }} HRS (GMT +1)</td>
                            <td>@if($schedule->virtual_link) <a href="{{ $schedule->virtual_link }}" target="_blank">{{ $schedule->virtual_link }} </a>@endif </td>
                            <td>{{ $schedule->updated_at->diffForHumans() }}</td>
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
