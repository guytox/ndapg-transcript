@extends('layouts.setup')

@section('content')

@include('includes.messages')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h3>{{ __('Select Billing Criteria') }}</h3></div>

                <div class="card-body">
                    {!! Form::open(['route' => ['schorlarship-processing.store'], 'method' => 'POST', 'file'=>true, 'enctype'=>"multipart/form-data"]) !!}

                                <div class="form-group">
                                    {!! Form::label('category_id', 'Select Fee Select Scholarship Type **') !!}
                                    {!! Form::select('category_id', [''=>'N/A',$categories], null, ['class' => 'form-control', 'required']) !!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('school_session', 'Select Session **') !!}
                                    {!! Form::select('school_session', [''=>'N/A',$schoolSession], null, ['class' => 'form-control', 'required']) !!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('studentList', 'Select Excel File to upload ****(Must be type .xlxs) Format: //sno//matricno//amount') !!}
                                    {!! Form::file('studentList', ['class'=>'form-control']) !!}
                                </div>



                                {{-- <div class="form-group">
                                    {!! Form::label('semester', 'Select Semester **') !!}
                                    {!! Form::select('semester', [''=>'N/A',getAllSemesters()], null, ['class' => 'form-control', 'required']) !!}
                                </div> --}}

                                <div class="form-group">
                                    {!! Form::label('user_id', 'Enter Student Matric No') !!}
                                    {!! Form::text('user_id', '',['class'=>'form-control', 'placeholder' => "Leave Blank to Bill all Students in Search Criteria" ]) !!}
                                </div>


                                {!! Form::submit('Submit Scholarship Proposal Now', ['class'=>'form-control btn btn-success']) !!}

                                {!! Form::close() !!}
                                <br>
                                <p align='center'>Note *** You will be able to preview after billing</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection




