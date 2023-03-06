@extends('layouts.setup')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h3>{{ __('Select Billing Criteria') }}</h3></div>

                <div class="card-body">
                    {!! Form::open(['url' => 'bursary/tuition-billing', 'method' => 'POST']) !!}


                                <div class="form-group">
                                    {!! Form::label('fee_category_id', 'Select Fee Category **') !!}
                                    {!! Form::select('fee_category_id', [''=>'N/A',$categories], null, ['class' => 'form-control', 'required']) !!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('fee_template_id', 'Select Fee') !!}
                                    {!! Form::select('fee_template_id', [''=>'N/A',$feeTemplate], null, ['class' => 'form-control']) !!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('program_id', 'Select Programme') !!}
                                    {!! Form::select('program_id', [''=>'All Programmes',getAllProgrammes()], null, ['class' => 'form-control']) !!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('study_level', 'Select Study Level') !!}
                                    {!! Form::select('study_level', [''=>'All Levels',getAllStudyLevels()], null, ['class' => 'form-control']) !!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('school_session', 'Select Session **') !!}
                                    {!! Form::select('school_session', [''=>'N/A',$schoolSession], null, ['class' => 'form-control', 'required']) !!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('semester', 'Select Semester **') !!}
                                    {!! Form::select('semester', [''=>'N/A',getAllSemesters()], null, ['class' => 'form-control', 'required']) !!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('user_id', 'Enter Student Matric No') !!}
                                    {!! Form::text('user_id', '',['class'=>'form-control', 'placeholder' => "Leave Blank to Bill all Students in Search Criteria" ]) !!}
                                </div>


                                {!! Form::submit('Bill Students Now', ['class'=>'form-control btn btn-success']) !!}

                                {!! Form::close() !!}
                                <br>
                                <p align='center'>Note *** You will be able to preview after billing</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection




