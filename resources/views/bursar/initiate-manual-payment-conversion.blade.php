@extends('layouts.setup')

@section('content')

@include('includes.messages')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h3>{{ __('Manual Payment Conversion Module') }}</h3></div>

                <div class="card-body">
                    {!! Form::open(['route' => ['manual-payment-processing.store'], 'method' => 'POST', 'file'=>true, 'enctype'=>"multipart/form-data"]) !!}


                                <div class="form-group">
                                    {!! Form::label('school_session', 'Select Session **') !!}
                                    {!! Form::select('school_session', [''=>'N/A',$schoolSession], null, ['class' => 'form-control', 'required']) !!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('studentList', 'Select Excel File to upload ****(Must be type .xlxs) Format: //sno//studentno//amount') !!}
                                    {!! Form::file('studentList', ['class'=>'form-control']) !!}
                                </div>


                                {!! Form::submit('Submit Payment for Conversion', ['class'=>'form-control btn btn-success']) !!}

                                {!! Form::close() !!}
                                <br>
                                <p align='center'>Note *** You will be able to preview submission after billing</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection




