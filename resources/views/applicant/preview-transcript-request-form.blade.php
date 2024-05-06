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
            {!! Form::open(['route' => ['transcripts.update',['transcript'=>$trRequest->uid]], 'method' => 'PUT', 'file'=>true, 'enctype'=>"multipart/form-data"]) !!}

            <div class="form-group">
                {!! Form::label('TranscriptType', 'Select Transcript Type', []) !!}
                {!! Form::select('TranscriptType', selectTranscriptTypeDropdown(), $trRequest->details->t_type, ['class'=>'form-control',  'required']) !!}
                @error('TranscriptType')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                {!! Form::label('MatricNumber', 'Please Enter Your NDA/Matric Number',[]) !!}
                {!! Form::text('MatricNumber', $trRequest->details->matric, ['class'=>'form-control', 'required']) !!}
                @error('MatricNumber')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                {!! Form::label('AdmissionYear', 'Enter Your Admission Year', []) !!}
                {!! Form::text('AdmissionYear', $trRequest->details->admissionYear, ['class'=>'form-control',  'required']) !!}
                @error('AdmissionYear')
                    <div class="alert alert-danger">{{ $message }} </div>
                @enderror
            </div>

            <div class="form-group">
                {!! Form::label('GraduationYear', 'Enter Your Graduation Year ', []) !!}
                {!! Form::text('GraduationYear', $trRequest->details->graduationYear, ['class'=>'form-control', 'placeholder'=> 'Enter Graduation Year', 'required']) !!}
                @error('GraduationYear')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                {!! Form::label('DeliveryOption', 'Select Delivery Mode', []) !!}
                {!! Form::select('DeliveryOption', selectDeliveryModeDropdown(), $trRequest->details->d_option, ['class'=>'form-control',  'required']) !!}
                @error('Gender')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                {!! Form::label('DeliveryEmail', 'Please Enter Delivery Email (If Delivery Mode is E-mail)', []) !!}
                {!! Form::email('DeliveryEmail', $trRequest->details->receiver_email, ['class'=>'form-control', 'placeholder'=> 'Enter Delivery E-mail']) !!}
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
                {!! Form::text('TranscriptReceiver', $trRequest->details->receiver, ['class'=>'form-control', 'required','placeholder'=>'e.g. The Registrar or The Secretary']) !!}
                @error('TranscriptReceiver')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                {!! Form::label('ReceiverEstablishment', 'Please Enter Receiver\'s Office Name', ['required']) !!}
                {!! Form::text('ReceiverEstablishment', $trRequest->details->establishment, ['class'=>'form-control', 'required', 'placeholder'=>'e.g. NDA Professionals Registration Council']) !!}
                @error('ReceiverEstablishment')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                {!! Form::label('StreetAddress', 'Please Enter Street/Road Number and Name', ['required']) !!}
                {!! Form::text('StreetAddress', $trRequest->details->street, ['class'=>'form-control', 'required', 'placeholder'=> 'e.g. 177 abc Street']) !!}
                @error('StreetAddress')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                {!! Form::label('ZipOrPostalCode', 'Please Enter ZIP or Postal Code (Optional)', ['required']) !!}
                {!! Form::text('ZipOrPostalCode', $trRequest->details->zip, ['class'=>'form-control', 'placeholder'=> 'e.g. 821101']) !!}
                @error('ZipOrPostalCode')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                {!! Form::label('AddressCity', 'Please Enter City Name', ['required']) !!}
                {!! Form::text('AddressCity', $trRequest->details->city, ['class'=>'form-control', 'required', 'placeholder'=> 'e.g. Kachia']) !!}
                @error('AddressCity')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                {!! Form::label('DeliveryState', 'Please Enter State or Province Name', ['required']) !!}
                {!! Form::text('DeliveryState', $trRequest->details->state, ['class'=>'form-control', 'required', 'placeholder'=> 'e.g. Lagos State']) !!}
                @error('DeliveryState')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                {!! Form::label('DeliveryCountry', 'Select Delivery Country', []) !!}
                {!! Form::select('DeliveryCountry', selectTranscriptCountryDropdown(), $trRequest->details->country, ['class'=>'form-control', 'placeholder'=> '------------', 'required']) !!}
                @error('DeliveryCountry')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>


        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h4 class="mt-0 mb-4">Transcript Delivery Address</h4>

            <b>{{$trRequest->details->receiver}},</b>
            <p>{{$trRequest->details->establishment}}, <br>
                {{$trRequest->details->street}}, <br>
                {{$trRequest->details->city}}, <br>
                {{$trRequest->details->state}}, <br>
                {{$trRequest->details->nationality->country_name}}. <br>
                {{$trRequest->details->zip}}
            </p>
            <p>
                {{$trRequest->details->receiver_email}}
            </p>
        </div>
    </div>


</div>


<div class="row">
    <div class="card">
        <div class="card-body">

            <div class="form-group">
                <ol>
                    <li>
                        <p>Please Note that the Cost of Processing this Transcript Request is <b class="text text-danger">N{{number_format(convertToNaira($amount),2)}}</b>.</p>
                    </li>
                    <li>
                        <p>Note: You will Now be directed to another site to effect Payment, Please confirm Request Only if You are ready to Pay</p>
                    </li>
                    <li>
                        <input type="checkbox" name="ConfirmRequest" required="Required"> I Confirm Payment that the Above Request details are okay and I am ready to Pay
                    </li>
                </ol>
            </div>

            @php
                $message = "Proceed to Confirm Request and Pay the sume of N".number_format(convertToNaira($amount),2);
            @endphp

            {!! Form::submit('Proceed to Confirm Request and Pay', ['class'=> 'form-control btn btn-danger']) !!}

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
