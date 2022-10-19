@extends('layouts.setup')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h3>{{ __('Matric Number Update') }}</h3></div>

                @include('includes.messages')

                <div class="card-body">
                    {!! Form::open(['route' =>['matric-update'] , 'method' => 'POST']) !!}

                                <div class="form-group">
                                    {!! Form::label('oldmatric', 'Enter Old Matric Number') !!}
                                    {!! Form::text('oldmatric', '',['class'=>'form-control', 'placeholder' => "Enter Old Matric Number" ]) !!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('newmatric', 'Enter New Matric Number') !!}
                                    {!! Form::text('newmatric', '',['class'=>'form-control', 'placeholder' => "Enter New Matric Number" ]) !!}
                                </div>


                                {!! Form::submit('Convert Matric Number', ['class'=>'form-control btn btn-success']) !!}

                                {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>
</div>
@endsection




