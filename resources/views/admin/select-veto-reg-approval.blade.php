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

        <h4 class="mt-0 mb-4">Veto Approval Selection</h4>
            {!! Form::open(['route' => 'veto.approval', 'method' => 'POST']) !!}

            <div class="form-group">
                {!! Form::label('c_sess', 'Select Commencement Session') !!}
                {!! Form::select('c_sess', getSessionsDropdown() , null, ['class' => 'form-control' ]) !!}
            </div>

            <div class="form-group">
                {!! Form::label('c_sem', 'Select Commencement Session') !!}
                {!! Form::select('c_sem', getAllSemesters() , null, ['class' => 'form-control' ]) !!}
            </div>


            {!! Form::submit('Approve All Registrations',['class'=>'btn btn-dark form-control']) !!}

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
