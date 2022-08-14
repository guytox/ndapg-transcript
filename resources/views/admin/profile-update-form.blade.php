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
        <h4 class="mt-0 mb-4">Profile Update form</h4>
        {!! Form::open(['route' => 'user.profile', 'method' => 'POST', 'file'=>true, 'enctype'=>"multipart/form-data"]) !!}

        <div class="form-group">
            {!! Form::label('useremail', 'Please Enter Your valid email Note*** (You will be required to verify this email)', ['class'=>'text-danger','required']) !!}
            {!! Form::email('useremail', null, ['class'=>'form-control']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('gsmnumber', 'Enter Your phone number (eg. 080X XXXX XXX)', []) !!}
            {!! Form::text('gsmnumber', null, ['class'=>'form-control']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('file', 'Select Your Passport Photo Note***(Must be jpeg or png of not more than 50kb)') !!}
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
