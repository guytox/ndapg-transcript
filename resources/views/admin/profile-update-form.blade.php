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
        <div class="card-body">
            <h4 class="mt-0 mb-4">Bio Data</h4>
            {!! Form::open(['route' => 'user.profile', 'method' => 'POST', 'file'=>true, 'enctype'=>"multipart/form-data"]) !!}

            <div class="form-group">
                {!! Form::label('SurName', 'Please Enter Your Surname/Fathers Name/ Family Name', ['required']) !!}
                {!! Form::text('SurName', null, ['class'=>'form-control', 'required']) !!}
                @error('SurName')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                {!! Form::label('OtherNames', 'Please Enter Your Other Names', ['required']) !!}
                {!! Form::text('OtherNames', null, ['class'=>'form-control', 'required']) !!}
                @error('OtherNames')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                {!! Form::label('Gender', 'Select Your Gender', []) !!}
                {!! Form::select('Gender',selectNdaGenderDropdown() ,null, ['class'=>'form-control', 'placeholder'=> '------------']) !!}
                @error('Gender')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                {!! Form::label('PhoneNumber', 'Please Enter Your GSM Number', ['required']) !!}
                {!! Form::text('PhoneNumber', null, ['class'=>'form-control', 'required']) !!}
                @error('PhoneNumber')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                {!! Form::label('DateOfBirth', 'Please Select Your Date of Birth', ['required']) !!}
                {!! Form::date('DateOfBirth', null, ['class'=>'form-control', 'required']) !!}
                @error('DateOfBirth')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>


            {{-- <div class="form-group">
                {!! Form::label('file', 'Select Your Passport Photo Note***(Must be jpeg or png of not more than 50kb)') !!}
                {!! Form::file('file', ['class'=> 'form-control']) !!}
            </div> --}}


        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h4 class="mt-0 mb-4">Admission Details</h4>

            <div class="form-group">
                {!! Form::label('NdaNumber', 'Please Enter Your NDA Number (For Regular Course Only)',[]) !!}
                {!! Form::text('NdaNumber', null, ['class'=>'form-control', 'placeholder'=> 'NDA Number']) !!}
                @error('NdaNumber')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                {!! Form::label('PgMatricNumber', 'Enter Your Postgraduate Matric Number (For Postgraduate Students Only)', []) !!}
                {!! Form::text('PgMatricNumber', null, ['class'=>'form-control', 'placeholder'=> 'PG Matric Number']) !!}
                @error('PgMatricNumber')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>



        </div>
    </div>


</div>

<div class="row">
    <div class="card">
        <div class="card-body">
            <h4 class="mt-0 mb-4 text-danger">Regular Course Details (if Regular Course)</h4>

            <div class="form-group">
                {!! Form::label('RegularCourseNumber', 'Enter Your Regular Course Number', []) !!}
                {!! Form::text('RegularCourseNumber', null, ['class'=>'form-control', 'placeholder'=> 'Regular Course Number']) !!}

                @error('RegularCourseNumber')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                {!! Form::label('NdaService', 'NDA Service', []) !!}
                {!! Form::select('NdaService',selectServiceDropdown() ,null, ['class'=>'form-control', 'placeholder'=> '------------']) !!}
                @error('NdaService')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                {!! Form::label('RegularAdmissionYear', 'Enter Your Admission Year (For Regular Course Only)', []) !!}
                {!! Form::text('RegularAdmissionYear', null, ['class'=>'form-control', 'placeholder'=> 'Enter Admission Year']) !!}
                @error('RegularAdmissionYear')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                {!! Form::label('RegularGraduationYear', 'Enter Your Graduation Year (For Regular Course Only)', []) !!}
                {!! Form::text('RegularGraduationYear', null, ['class'=>'form-control', 'placeholder'=> 'Enter Graduation Year']) !!}
                @error('RegularGraduationYear')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                {!! Form::label('RegularCommissionDate', 'Enter Your Commission Year (For Regular Course Only)', []) !!}
                {!! Form::text('RegularCommissionDate', null, ['class'=>'form-control', 'placeholder'=> 'Commission Year']) !!}
                @error('CommissionDate')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>




        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h4 class="mt-0 mb-4 text-success">First Postgraduate Course Details (if Postgraduate Student)</h4>

            <div class="form-group">
                {!! Form::label('PostgraduateAdmissionYear', 'Enter Your Postgraduate Admission Year (For Regular Course Only)', []) !!}
                {!! Form::text('PostgraduateAdmissionYear', null, ['class'=>'form-control', 'placeholder'=> 'Enter Postgraduate Admission Year']) !!}
                @error('PostgraduateAdmissionYear')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                {!! Form::label('PostgraduateGraduationYear', 'Enter Your Graduation Year (For Regular Course Only)', []) !!}
                {!! Form::text('PostgraduateGraduationYear', null, ['class'=>'form-control', 'placeholder'=> 'Enter Postgraduate Graduation Year']) !!}
                @error('PostgraduateGraduationYear')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>





        </div>
    </div>
</div>
<div class="row">

    {!! Form::submit('Upload My Details', ['class'=> 'form-control btn btn-danger']) !!}

    {!! Form::close() !!}

</div>
@endsection
@section('js')
<!-- Required datatable js -->
<script src="{{ asset('admin/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>


<!-- Datatable init js -->
<script src="{{ asset('admin/assets/js/pages/datatables.init.js') }}"></script>


@endsection
