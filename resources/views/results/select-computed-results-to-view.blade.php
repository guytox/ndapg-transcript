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

        <h4 class="mt-0 mb-4">View Previously Computed Results</h4>
            {!! Form::open(['route' => ['view.computed.results', 'as'=>'ityoughKiKyaren'], 'method' => 'POST']) !!}

            {{-- <div class="form-group">
                {!! Form::label('c_prog', 'Select Program of Study') !!}
                {!! Form::select('c_prog', $programs , null, ['class' => 'form-control' ]) !!}
            </div> --}}

            <div class="form-group">
                {!! Form::label('sch_session', 'Select Session') !!}
                {!! Form::select('sch_session', getSessionsDropdown() , null, ['class' => 'form-control', 'required']) !!}
            </div>

            <div class="form-group">
                {!! Form::label('sch_semester', 'Select Semester') !!}
                {!! Form::select('sch_semester', getSemestersDropdown() , null, ['class' => 'form-control', 'required' ]) !!}
            </div>

            <br>

            {!! Form::submit('View Computed Results',['class'=>'btn btn-warning form-control']) !!}

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
