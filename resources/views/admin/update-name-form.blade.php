@extends('layouts.setup')

@section('content')

@if ($action =='update')

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header"><h3>{{ __('Update Student Name Now') }}</h3></div>

                        @include('includes.messages')

                        <div class="card-body">
                            {!! Form::open(['route' =>['userNameChange.update', 'userNameChange'=> $studentDetail->id] , 'method' => 'PUT']) !!}

                                        <h1>{{$studentDetail->name}}</h1>

                                        <div class="form-group">
                                            {!! Form::label('newname', 'EnterNew Name') !!}
                                            {!! Form::text('newname', '',['class'=>'form-control', 'placeholder' => "Enter New Name" ]) !!}
                                        </div>

                                        {!! Form::submit('Update Student Name', ['class'=>'form-control btn btn-success']) !!}

                                        {!! Form::close() !!}

                        </div>
                    </div>
                </div>
            </div>
        </div>

@else

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header"><h3>{{ __('Change of Name Form') }}</h3></div>

                        @include('includes.messages')

                        <div class="card-body">
                            {!! Form::open(['route' =>['userNameChange.store'] , 'method' => 'POST']) !!}

                                        <div class="form-group">
                                            {!! Form::label('matric', 'Enter Mtric Number') !!}
                                            {!! Form::text('matric', '',['class'=>'form-control', 'placeholder' => "Enter Matric Number" ]) !!}
                                        </div>

                                        {!! Form::submit('Search Student Record', ['class'=>'form-control btn btn-success']) !!}

                                        {!! Form::close() !!}

                        </div>
                    </div>
                </div>
            </div>
        </div>

@endif



@endsection




