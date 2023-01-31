@extends('layouts.setup')
@section('css')
<!-- DataTables -->
<link href="{{ asset('admin/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
{{-- <link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" --}}
type="text/css" />
@endsection

@section('content')

@include('includes.messages')

<h4 class="mt-0 mb-4">List students who have not registered courses</h4>
<div class="row">
    <div class="card ">
        <div class="card-body">

            {!! Form::open(['route' => 'show.notregistered.students', 'method' => 'POST']) !!}

                <div class="form-group">
                    {!! Form::label('schoolsession', 'Select a Session') !!}
                    {!! Form::select('schoolsession', getSessionsDropdown(), null, ['class'=>'form-control', 'required']) !!}

                </div>

                <div class="form-group">
                    {!! Form::label('semester', 'Select a Semester') !!}
                    {!! Form::select('semester', getSemestersDropdown(), null, ['class'=>'form-control', 'required']) !!}

                </div>

            {!! Form::submit('View Not Registered Students', ['class'=>'form-control btn btn-success']) !!}

            {!! Form::close() !!}
        </div>
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
