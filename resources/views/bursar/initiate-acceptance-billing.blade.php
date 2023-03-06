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
    <h1>Acceptance Fees Billing</h1>

    <p>Please Select Excel file to Upload and click on "Bill Acceptance"</p>

    <form action="{{ route('acceptance.billing.upload') }}" method="post" enctype="multipart/form-data">
        @csrf
        <label for="file">Must Contain // jambnumber //</label> <br>
        <input type="file" name="file" id="">

        <button type="submit" class="btn btn-primary">Bill Acceptance</button>
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
