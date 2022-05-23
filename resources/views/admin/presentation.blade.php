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
                    <div class="row">
                        <div class="col-sm-6">

                            <h4 class="header-title">Approved Presentations</h4>
                            <p class="card-title-desc"> list of all the presentation that has been reviewed and approved for the conference

                            </p>

                        </div>

{{--                        <div class="col-sn-6">--}}
{{--                            <a href="{{ route('presenting.create')  }}" class="btn btn-primary">Add new Presentation topic &nbsp; <i--}}
{{--                                    class="mdi mdi-file-document-edit-outline"></i></a>--}}
{{--                        </div>--}}
                    </div>

                    <table id="datatable" class="table table-bordered dt-responsive nowrap"
                           style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                        <tr>
                            <th>Topic</th>
                            <th>Details</th>
                            <th>Added By</th>
                            <th>Status</th>
                            <th>Date Added</th>
                            <th>Date Updated</th>
                            <th> Reviewer</th>
                        </tr>
                        </thead>


                        <tbody>

                        @foreach($presentations as $presentation)
                            <tr>
                                <td>{{ $presentation->title }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($presentation->description, '100') }}</td>
                                <td>{{ $presentation->user->name }}</td>
                                <td>{{ $presentation->status }}</td>
                                <td>{{$presentation->created_at->diffForHumans() }}</td>
                                <td>{{$presentation->updated_at->diffForHumans() }}</td>
                                <td>
                                    @if($presentation->reviewer_id == null)
                                    <a href="{{ route('admin.assign-reviewer', $presentation->id) }}" class="btn btn-primary">Assign Reviewer</a></td>
                                    @else
                                        {{ $presentation->reviewer->name ?? '' }}
{{--                                        TODO: add reviewer relationship --}}
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
