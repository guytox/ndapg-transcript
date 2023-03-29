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

        <h4 class="mt-0 mb-4">Student Defferment Form</h4>
            {!! Form::open(['route' => 'view.defferment.student', 'method' => 'POST']) !!}

            <div class="form-group">
                {!! Form::label('d_std', 'Enter the Matric. Number of the Student to be derfferred') !!}
                {!! Form::text('d_std', null, ['class' => 'form-control','placeholder'=>"Enter Matric Here e.g. 11124",'required' ]) !!}
            </div>

            <div class="form-group">
                {!! Form::label('c_sess', 'Select Commencement Session') !!}
                {!! Form::select('c_sess', getSessionsDropdown() , null, ['class' => 'form-control' ]) !!}
            </div>

            <div class="form-group">
                {!! Form::label('r_sess', 'Enter the Return Session') !!}
                {!! Form::text('r_sess', null, ['class' => 'form-control','placeholder'=>"Enter Return Session e.g. 2025/2026",'required' ]) !!}
            </div>

            <div class="form-group">
                {!! Form::label('d_amount', 'Enter the Applicable Fees to be paid by Student') !!}
                {!! Form::number('d_amount', null, ['class' => 'form-control','placeholder'=>"Enter Return Fees (e.g 0 for non)",'required', 'step' => '0.01' ]) !!}
            </div>

            {!! Form::submit('Search Student Details',['class'=>'btn btn-dark form-control']) !!}

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
