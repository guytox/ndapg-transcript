@extends('layouts.setup')
@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            @include('includes.messages')
            <div class="card-body">
                <h2 class="header-title">Programme Details</h2>


                {!! Form::open(['route' => 'applicant.add_programme.store', 'method' => 'POST']) !!}

                <div class="form-group">
                    {!! Form::label('programme', 'Select the Programme you want to apply for') !!}
                    {!! Form::select('programme', getAppliableProgrammeDropdown(), user()->profile->applicant_program, ['class' => 'form-control', 'required']) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('service_status', 'Are you a Serving Officer') !!}
                    {!! Form::select('service_status', [''=>'----', '1'=>'Yes', '0'=>'No'], user()->profile->is_serving_officer, ['class' => 'form-control', 'required']) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('service_number', 'If you are a serving Officer, Enter your service number here') !!}
                    {!! Form::text('service_number', user()->profile->service_number, ['class' => 'form-control', 'placeholder'=>'123456', ]) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('service_rank', 'Enter Present Service Rank(If applicable)') !!}
                    {!! Form::text('service_rank', user()->profile->service_rank, ['class' => 'form-control', 'placeholder'=>'Enter Present Rank', ]) !!}
                </div>

                {!! Form::submit('Save Programme Details Details', ['class'=>'form-control btn btn-primary']) !!}



                {!! Form::close() !!}




            </div>
        </div>
    </div>

</div>
@endsection
