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
        <h4 class="mt-0 mb-4">Specify Details to Upload Student List</h4>
        {!! Form::open(['route' => 'student.list.upload', 'method' => 'POST', 'file'=>true, 'enctype'=>"multipart/form-data"]) !!}

        <div class="form-group">
            {!! Form::label('program_id', 'Select Programme') !!}
            {!! Form::select('program_id', $programlist,'',['class'=>'form-control', 'required' ]) !!}
        </div>

        <div class="form-group">
            {!! Form::label('file', 'Select .xlsx File to upload (format: name// matricno // email// gsm // programme // department // faculty)') !!}
            {!! Form::file('file', ['class'=> 'form-control']) !!}
        </div>

        {!! Form::submit('Upload Student List') !!}

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
