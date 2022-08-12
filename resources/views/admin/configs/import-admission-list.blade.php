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
        <h4 class="mt-0 mb-4">Choose .xlsx File to  Upload Student Admission List</h4>
        {!! Form::open(['route' => 'student.admissionoffer.upload', 'method' => 'POST', 'file'=>true, 'enctype'=>"multipart/form-data"]) !!}

        <div class="form-group">
            {!! Form::label('file', 'Select .xlsx File to upload (format: faculty// category // formnumber // surname // othernames // state // programme // programme_id // department // country // gender // qualifications // remarks //)') !!}
            {!! Form::file('file', ['class'=> 'form-control']) !!}
        </div>

        <div>
            {!! Form::label('programme', 'Select Programme', []) !!}
            {!! Form::select('programme', $programmes, null, ['class'=>'form-control']) !!}

        </div>

        <br>


        {!! Form::submit('Upload Admission List') !!}

        {!! Form::close() !!}
        <hr>
        <div>
            <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;" >
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Form No.</th>
                        <th>Matric</th>
                        <th>Name</th>
                        <th>State</th>
                        <th>gender</th>
                        <th>SysProg.</th>
                        <th>PayCode.</th>
                        <th>Category</th>
                    </tr>
                </thead>


                <tbody>

                    @foreach( $admitted as $key => $v )
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $v->form_number }}</td>
                        <td>{{ $v->matric_number }}</td>
                        <td>{{ $v->surname }} {{$v->other_names}}</td>
                        <td>{{ $v->state }}</td>
                        <td>{{ $v->gender }}</td>
                        <td>{{ getProgrammeDetailById($v->programme_id, 'name') }}</td>
                        <td>{{ $v->payment_code }}</td>
                        <td>{{ $v->category }}</td>
                    </tr>


                    @endforeach

                </tbody>
            </table>

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
