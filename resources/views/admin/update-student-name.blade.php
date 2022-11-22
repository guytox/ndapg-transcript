@extends('layouts.setup')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h3>{{ __('Change of Name Form') }}</h3></div>

                @include('includes.messages')

                <div class="card-body">
                    {!! Form::open(['route' =>['username.update.search'] , 'method' => 'POST']) !!}

                                <div class="form-group">
                                    {!! Form::label('studentmatric', 'Enter Matric Number') !!}
                                    {!! Form::text('studentmatric', '',['class'=>'form-control', 'placeholder' => "Enter Matric Number" ]) !!}
                                </div>

                                {!! Form::submit('Search Student Information', ['class'=>'form-control btn btn-danger']) !!}

                                {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>
</div>
@endsection




