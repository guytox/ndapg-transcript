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

        <h4 class="mt-0 mb-4">To Check Sessional Fail List (Select Programme,  Session and Semester)</h4>
            {!! Form::open(['route' => ['get.failist.summary', 'as'=>'ityoughKiKyaren'], 'method' => 'POST']) !!}

            <div class="form-group">
                {!! Form::label('c_prog', 'Select Program of Study') !!}
                {!! Form::select('c_prog', $programs , null, ['class' => 'form-control' ]) !!}
            </div>

            <div class="form-group">
                {!! Form::label('school_session', 'Select Session') !!}
                {!! Form::select('school_session', getSessionsDropdown() , null, ['class' => 'form-control' ]) !!}
            </div>

            <div class="form-group">
                {!! Form::label('semester', 'Select Semester') !!}
                {!! Form::select('semester', getSemestersDropdown() , null, ['class' => 'form-control' ]) !!}
            </div>

            {{-- <div class="form-group">
                {!! Form::label('study_level', 'Select Study Level') !!}
                {!! Form::select('study_level', getAllStudyLevelsDropdown() , null, ['class' => 'form-control' ]) !!}
            </div> --}}

            <br>

            {!! Form::submit('Retrieve Records',['class'=>'btn btn-danger form-control']) !!}

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
