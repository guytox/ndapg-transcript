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
            <h4 class="mt-0 mb-4">Admission Details</h4>
            {!! Form::open(['route' => 'transcripts.store', 'method' => 'POST', 'file'=>true, 'enctype'=>"multipart/form-data"]) !!}

            <div class="form-group">
                {!! Form::label('TranscriptType', 'Select Transcript Type', []) !!}
                {!! Form::select('TranscriptType', selectTranscriptTypeDropdown(), null, ['class'=>'form-control', 'placeholder'=> '------------', 'required']) !!}
                @error('TranscriptType')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                {!! Form::label('MatricNumber', 'Please Enter Your NDA/Matric Number',[]) !!}
                {!! Form::text('MatricNumber', null, ['class'=>'form-control', 'placeholder'=> 'NDA/Matric Number', 'required']) !!}
                @error('MatricNumber')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                {!! Form::label('AdmissionYear', 'Enter Your Admission Year', []) !!}
                {!! Form::text('AdmissionYear', null, ['class'=>'form-control', 'placeholder'=> 'Enter Admission Year', 'required']) !!}
                @error('AdmissionYear')
                    <div class="alert alert-danger">{{ $message }} </div>
                @enderror
            </div>

            <div class="form-group">
                {!! Form::label('GraduationYear', 'Enter Your Graduation Year ', []) !!}
                {!! Form::text('GraduationYear', null, ['class'=>'form-control', 'placeholder'=> 'Enter Graduation Year', 'required']) !!}
                @error('GraduationYear')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                {!! Form::label('DeliveryOption', 'Select Delivery Mode', []) !!}
                {!! Form::select('DeliveryOption', selectDeliveryModeDropdown(), null, ['class'=>'form-control', 'placeholder'=> '------------', 'required']) !!}
                @error('Gender')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                {!! Form::label('DeliveryEmail', 'Please Enter Delivery Email (If Delivery Mode is E-mail)', []) !!}
                {!! Form::email('DeliveryEmail', null, ['class'=>'form-control', 'placeholder'=> 'Enter Delivery E-mail']) !!}
                @error('DeliveryEmail')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            





        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h4 class="mt-0 mb-4">Transcript Delivery Details</h4>


            <div class="form-group">
                {!! Form::label('TranscriptReceiver', 'Please Enter Transcript Receiver Name/Office Title', ['required']) !!}
                {!! Form::text('TranscriptReceiver', null, ['class'=>'form-control', 'required','placeholder'=>'e.g. The Registrar or The Secretary']) !!}
                @error('TranscriptReceiver')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                {!! Form::label('ReceiverEstablishment', 'Please Enter Receiver\'s Office Name', ['required']) !!}
                {!! Form::text('ReceiverEstablishment', null, ['class'=>'form-control', 'required', 'placeholder'=>'e.g. NDA Professionals Registration Council']) !!}
                @error('ReceiverEstablishment')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                {!! Form::label('StreetAddress', 'Please Enter Street/Road Number and Name', ['required']) !!}
                {!! Form::text('StreetAddress', null, ['class'=>'form-control', 'required', 'placeholder'=> 'e.g. 177 abc Street']) !!}
                @error('StreetAddress')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                {!! Form::label('ZipOrPostalCode', 'Please Enter ZIP or Postal Code (Optional)', ['required']) !!}
                {!! Form::text('ZipOrPostalCode', null, ['class'=>'form-control', 'placeholder'=> 'e.g. 821101']) !!}
                @error('ZipOrPostalCode')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                {!! Form::label('AddressCity', 'Please Enter City Name', ['required']) !!}
                {!! Form::text('AddressCity', null, ['class'=>'form-control', 'required', 'placeholder'=> 'e.g. Kachia']) !!}
                @error('AddressCity')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                {!! Form::label('DeliveryState', 'Please Enter State or Province Name', ['required']) !!}
                {!! Form::text('DeliveryState', null, ['class'=>'form-control', 'required', 'placeholder'=> 'e.g. Lagos State']) !!}
                @error('DeliveryState')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                {!! Form::label('DeliveryCountry', 'Select Delivery Country', []) !!}
                {!! Form::select('DeliveryCountry', selectTranscriptCountryDropdown(), 160, ['class'=>'form-control', 'placeholder'=> '------------', 'required']) !!}
                @error('DeliveryCountry')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>


        </div>
    </div>


</div>


<div class="row">

    {!! Form::submit('Request For New Transcript', ['class'=> 'form-control btn btn-dark']) !!}

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
