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


        <h4 class="mt-0 mb-4">Research Proposal </h4>
        {!! Form::open(['route' => 'applicant.research.store', 'method' => 'POST', 'file'=>true, 'enctype'=>"multipart/form-data"]) !!}

        <div class="form-group">
            {!! Form::label('research', 'In a few sentenses, provide your research proposal') !!}
            {!! Form::text('research', $researchDetails->summary, ['class'=>'form-control', 'required', 'maxlength'=>200]) !!}
        </div>

        <div class="form-group">
            {!! Form::label('file', 'Upload a 1 Page Research Proposal in .pdf format  Note***(Must be .pdf of not more than 200kb)') !!}
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
