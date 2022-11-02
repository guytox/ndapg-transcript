@extends('layouts.setup')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h3>{{ __('Change of Programme Form') }}</h3></div>

                @include('includes.messages')

                <div class="card-body">
                    {!! Form::open(['route' =>['programme-update'] , 'method' => 'POST']) !!}

                                <div class="form-group">
                                    {!! Form::label('studentmatric', 'Enter Matric Number') !!}
                                    {!! Form::text('studentmatric', '',['class'=>'form-control', 'placeholder' => "Enter Matric Number" ]) !!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('newprogramme', 'Select New Programme') !!}
                                    {!! Form::select('newprogramme', getAllProgramsDropdown(), null, ['class'=>'form-control']) !!}
                                </div>

                                {!! Form::submit('Change Student Programme', ['class'=>'form-control btn btn-danger']) !!}

                                {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>
</div>
@endsection




