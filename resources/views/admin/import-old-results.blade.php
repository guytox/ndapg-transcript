@extends('layouts.setup')
@section('css')
<!-- DataTables -->
<link href="{{ asset('admin/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
{{-- <link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" --}}
type="text/css" />
@endsection

@section('content')

@include('includes.messages')

<div class="row">

<div class="card">

    <form action="{{ route('upload.oldresults') }}" method="post" enctype="multipart/form-data">
        @csrf
        <label for="file">Must Contain // matricno // currentlevel // session // semester // ltcr // ltce // ltwgp // lcgpa // cur // cue // wgp // gpa // tcr // tce // twgp // cgpa </label> <br>
        <input type="file" name="file" id="">

        <button type="submit" class="btn btn-primary">Upload Old Results</button>
    </form>
</div>

</div>
@endsection
@section('js')
<!-- Required datatable js -->
<script src="{{ asset('admin/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>


<!-- Datatable init js -->
<script src="{{ asset('admin/assets/js/pages/datatables.init.js') }}"></script>


@endsection
