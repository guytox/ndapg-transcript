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

        <img class="rounded-circle header-profile-user" src="{{ asset(user()->passport) }}" alt="No Picture Yet">

        <h4 class="mt-0 mb-4">Bio-Data form</h4>
        {!! Form::open(['route' => 'applicant.biodata.store', 'method' => 'POST', 'file'=>true, 'enctype'=>"multipart/form-data"]) !!}

        <div class="form-group">
            {!! Form::label('dob', 'Please Select your date of birth') !!}
            {!! Form::date('dob', user()->profile->dob, ['class'=>'form-control','required']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('gsmnumber', 'Enter Your phone number (eg. 080X XXXX XXX)') !!}
            {!! Form::text('gsmnumber', user()->phone_number, ['class'=>'form-control', 'required']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('gender', 'Enter Your gender') !!}
            {!! Form::select('gender',[''=>"Select Gender",'Male'=>"Male",'Female'=>"Female"], user()->profile->gender, ['class'=>'form-control', 'required']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('marritalstatus', 'Enter Your Marrital Status') !!}
            {!! Form::select('marritalstatus',[''=>"Select Marrital Status",'single'=>"single",'married'=>"married",'Dovorced'=>"Divorced"], user()->profile->marital_status, ['class'=>'form-control', 'required']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('file', 'Select Your Passport Photo Note***(Must be jpeg or png of not more than 50kb)') !!}
            {!! Form::file('file', ['class'=> 'form-control']) !!}
        </div>

        {!! Form::submit('Submit Bio-data Form',['class'=>'form-control btn btn-success']) !!}

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
