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
    <div class="card-body">
        <h4 class="mt-0 mb-4">Upload List of SchoolFees Payments</h4>
        {!! Form::open(['route' => 'student.payment.upload', 'method' => 'POST', 'file'=>true, 'enctype'=>"multipart/form-data"]) !!}

        <div class="form-group">
            {!! Form::label('semester', 'Select Semester (use ctrl + click to select all') !!}
            {!! Form::select('semester', [0=>'Both Semesters',1=>'First Semester', 2=>'Second Semester'], 0,['class'=>'form-control', 'required' ]) !!}
        </div>

        <div class="form-group">
            {!! Form::label('file', 'Select .xlsx File to upload (format: //matricno // name //  programme // amountpaid)') !!}
            {!! Form::file('file', ['class'=> 'form-control', 'required']) !!}
        </div>

        {!! Form::submit('Upload Payments') !!}

        {!! Form::close() !!}
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
