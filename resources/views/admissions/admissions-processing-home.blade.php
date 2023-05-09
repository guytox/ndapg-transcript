@extends('layouts.setup')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="text-danger" ><marquee behavior="" direction="left"> <b> <font size="6">{{ getSiteNotification()}}</font> </b> </marquee></div>

            <div class="card">
                <div class="card-header">{{ __('Admissions Processing Home') }}</div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif

                    @include('includes.messages')

                    {{ __('This Page is for Admission Processing (Enter the Applicant Form Number to Continue)') }}


                    @role('admin|registry|bursary')

                    <hr>
                    <h2>Applicant Clearance Form</h2>

                    {!! Form::open(['route'=>'admission.processing.details','method'=>'POST']) !!}

                    <div class="form-group">
                        {!! Form::label('form_number', 'Enter the Form Number to Process Admission') !!}
                        {!! Form::text('form_number', '', ['class'=>"form-control",'placeholder'=>"Enter Form Number",'required']) !!}
                    </div>

                    {{-- <div class="form-group">
                        {!! Form::label('form_action', 'Select What you want to do') !!}
                        {!! Form::select('form_action', [''=>'N/A', '1'=>'Candidate Screening','2'=>'Issue File', '3'=>"Screen For Registration"], '', ['class'=>'form-control','required']) !!}
                    </div> --}}

                    <hr>

                    {!! Form::submit('Search Applicant Record', ['class'=>'form-control btn btn-success']) !!}

                    {!! Form::close() !!}

                    @endrole




                </div>
            </div>
        </div>
    </div>
</div>
@endsection
