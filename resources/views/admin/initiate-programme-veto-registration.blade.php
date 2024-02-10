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

        <h4 class="mt-0 mb-4">Single Veto Registration</h4>
            {!! Form::open(['route' => 'post.prog.vetoreg', 'method' => 'POST']) !!}

            <div class="form-group">
                {!! Form::label('d_prog', 'Select Programme') !!}
                {!! Form::select('d_prog', getAllProgrammesDropdown() , null, ['class' => 'form-control' ]) !!}
            </div>

            <div class="form-group">
                {!! Form::label('level_id', 'Select Study Year') !!}
                {!! Form::select('level_id',[1 => 'First Year', 2 =>'Second Year', 3=>'Third year', 4=>'Fouth Year'] , null, ['class' => 'form-control' ]) !!}
            </div>

            <div class="form-group">
                {!! Form::label('school_session', 'Select Session') !!}
                {!! Form::select('school_session', getSessionsDropdown() , null, ['class' => 'form-control' ]) !!}
            </div>

            <div class="form-group">
                {!! Form::label('semester', 'Select Semester') !!}
                {!! Form::select('semester', getSemestersDropdown() , null, ['class' => 'form-control' ]) !!}
            </div>


            <div class="form-group">
                {!! Form::label('action', 'Select Action (Add or Remove)') !!}
                {!! Form::select('action', [''=>"Select Action",'1'=>"Register Student",'2'=>"Remove Registration"] , null, ['class' => 'form-control', 'required' ]) !!}
            </div>

            <br>

            {!! Form::submit('Submit for Veto Registration',['class'=>'btn btn-dark form-control']) !!}

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
