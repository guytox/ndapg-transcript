@extends('layouts.setup')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h3>{{ __('Select Migration Session') }}</h3></div>

                @include('includes.messages')

                <div class="card-body">
                    {!! Form::open(['route' =>['studentMigration.store'] , 'method' => 'POST']) !!}


                                <div class="form-group">
                                    {!! Form::label('school_session', 'Select Session **') !!}
                                    {!! Form::select('school_session', [''=>'N/A',$schoolSession], null, ['class' => 'form-control', 'required']) !!}
                                </div>


                                <div class="form-group">
                                    {!! Form::label('student_id', 'Enter Student Matric No ***(For single Migration)***') !!}
                                    {!! Form::text('student_id', '',['class'=>'form-control', 'placeholder' => "Leave Blank to Bill all Students in Search Criteria" ]) !!}
                                </div>


                                {!! Form::submit('Begin Migration Now', ['class'=>'form-control btn btn-success']) !!}

                                {!! Form::close() !!}
                                <br>
                                <p align='center'>Note *** You will be able to preview before completing migration</p>
                                <p align='center'><a href="{{ route('get.migration') }}" class="btn btn-danger form-control">View/Approve Proposed Migrations</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection




