@extends('layouts.setup')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h3>{{ __('Password update for all users') }}</h3></div>

                @include('includes.messages')

                <div class="card-body">
                    {!! Form::open(['route' =>['userpass-update'] , 'method' => 'POST']) !!}

                                <div class="form-group">
                                    {!! Form::label('matric', 'Enter Username') !!}
                                    {!! Form::text('matric', '',['class'=>'form-control', 'placeholder' => "Enter username" ]) !!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('newpass', 'Enter New Password') !!}
                                    {!! Form::text('newpass', '',['class'=>'form-control', 'placeholder' => "Enter New Password" ]) !!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('confirmpass', 'Confirm New Password') !!}
                                    {!! Form::text('confirmpass', '',['class'=>'form-control', 'placeholder' => "Confirm New Password" ]) !!}
                                </div>


                                {!! Form::submit('Update Password', ['class'=>'form-control btn btn-success']) !!}

                                {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>
</div>
@endsection




