@extends('layouts.setup')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h3>{{ __('Change of Name Form') }}</h3></div>

                @include('includes.messages')

                <div class="card-body">

                    <h5>Old Name:  {{$oldName}}</h5>
                    {!! Form::open(['route' =>['username.update'] , 'method' => 'POST']) !!}

                                <div class="form-group">
                                    {!! Form::label('newname', 'Enter New Name') !!}
                                    {!! Form::text('newname', $oldName,['class'=>'form-control', 'placeholder' => "Enter New Name" ]) !!}
                                </div>
                                {!! Form::hidden('student_id', $studentId, []) !!}

                                {!! Form::submit('Change Student Name', ['class'=>'form-control btn btn-danger']) !!}

                                {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>
</div>
@endsection




