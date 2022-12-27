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

        <img class="rounded-circle header-profile-user" src="{{ asset($nyscDetails->nysc_path) }}" alt="No Certificate Uploaded Yet">

        <h4 class="mt-0 mb-4">NYSC Details </h4>
        {!! Form::open(['route' => 'applicant.nysc.store', 'method' => 'POST', 'file'=>true, 'enctype'=>"multipart/form-data"]) !!}

        <div class="form-group">
            {!! Form::label('nysc', 'Have You completed your NYSC? Or Do you have an Exemption Certificate?') !!}
            {!! Form::select('nysc',[''=>"Select Yes or No",'yes'=>"Yes",'no'=>"No"], $nyscDetails->nysc, ['class'=>'form-control', 'required']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('file', 'If you selected yes above, Upload your NYSC Certificate Note***(Must be jpeg or png of not more than 100kb)') !!}
            {!! Form::file('file', ['class'=> 'form-control']) !!}
        </div>

        {!! Form::submit('Submit NYSC Details',['class'=>'form-control btn btn-success']) !!}

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
