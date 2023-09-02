@extends('layouts.setup')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="text-danger" ><marquee behavior="" direction="left"> <b> <font size="6">{{ getSiteNotification()}}</font> </b> </marquee></div>

            <div class="card">
                <div class="card-header">{{ __('ID Card Processing Home') }}</div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif

                    @include('includes.messages')

                    {{ __('This Page is for ID Card Processing (Enter the Applicant Form Number to Continue)') }}


                    @role('admin|registry|bursary')

                    <hr>
                    <h2>Search Student for ID Card Production</h2>

                    {!! Form::open(['route'=>'idCard.store','method'=>'POST']) !!}

                    <div class="form-group">
                        {!! Form::label('matric', 'Enter the Matric Number') !!}
                        {!! Form::text('matric', '', ['class'=>"form-control",'placeholder'=>"Enter Matric Number",'required']) !!}
                    </div>

                    {{-- <div class="form-group">
                        {!! Form::label('form_action', 'Select What you want to do') !!}
                        {!! Form::select('form_action', [''=>'N/A', '1'=>'Candidate Screening','2'=>'Issue File', '3'=>"Screen For Registration"], '', ['class'=>'form-control','required']) !!}
                    </div> --}}

                    <hr>

                    {!! Form::submit('Search For Student Record', ['class'=>'form-control btn btn-success']) !!}

                    {!! Form::close() !!}

                    @endrole




                </div>
            </div>
        </div>
    </div>
</div>
@endsection
