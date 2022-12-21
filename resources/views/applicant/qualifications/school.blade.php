@extends('layouts.setup')
@section('content')
    <div class="row">
        <div class="col-xl-4">
            <div class="card">
                @include('includes.messages')
                <div class="card-body">
                    <h2 class="header-title">Academic Qualifications Submission</h2>

                        {!! Form::open(['route' => 'applicant.qualifications.store', 'method' => 'POST', 'file'=>true, 'enctype'=>"multipart/form-data", 'class'=>"mt-5"]) !!}

                        {!! Form::hidden('action', 'school', []) !!}

                        <div class="form-group">
                            {!! Form::label('certificate_type', 'Name / Certificate Type') !!}
                            {!! Form::text('certificate_type', null, ['class'=>'form-control', 'required',  'placeholder'=>"e.g HND, B.Sc., M.Sc."]) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('awarding_institution', 'Awarding Institution') !!}
                            {!! Form::text('awarding_institution', null, ['class'=>'form-control', 'required',  'placeholder'=>""]) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('qualification_obtained', 'Qualification Obtained') !!}
                            {!! Form::text('qualification_obtained', null, ['class'=>'form-control', 'required',  'placeholder'=>""]) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('class', 'Certificate Class') !!}
                            {!! Form::text('class', null, ['class'=>'form-control', 'required',  'placeholder'=>""]) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('year_obtained', 'Year Obtained') !!}
                            {!! Form::date('year_obtained', null, ['class'=>'form-control','required']) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('file', 'Upload Certificate here, Note***(Must be jpeg or png of not more than 100kb)') !!}
                            {!! Form::file('file', ['class'=> 'form-control']) !!}
                        </div>

                        {!! Form::submit('Submit Academic Qualification',['class'=>'form-control btn btn-success']) !!}

                        {!! Form::close() !!}


                    </form>


                </div>
            </div>
        </div>

        {{-- table for professional qualifications --}}
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <h2 class="header-title">Submitted Academic Qualifications</h2>

                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                        <tr>
                            <th>Cert Type</th>
                            <th>Awarding Institution</th>
                            <th>Qualification Obtained</th>
                            <th>Class</th>
                            <th>Year Obtained</th>
                            <th>Edit</th>
                        </tr>
                        </thead>


                        <tbody>

                        @foreach($qualifications as $qualification)
                            <tr>
                                <td>{{ $qualification->certificate_type  }}</td>
                                <td>{{ $qualification->awarding_institution  }}</td>
                                <td>{{ $qualification->qualification_obtained  }}</td>
                                <td>{{ $qualification->class ?? 'N/A'  }}</td>
                                <td>{{ \Carbon\Carbon::parse($qualification->year_obtained)->year  }}</td>
                                <td><a class="btn btn-success btn-sm" href="{{route('applicant.delete.qualification',['id'=>$qualification->uid])}}">Remove</a>
                                {{-- <a href="{{$qualification->uid}}" target="_blank" class="btn btn-danger btn-sm" target="_blank">View Certificate</a> --}}
                                <img class="rounded-circle header-profile-user" src="{{ asset($qualification->path) }}" alt="No Upload Yet">
                                <a href="{{asset($qualification->path)}}" target="_blank" class="btn btn-warning btn-sm">Show Certificate</a>
                                </td>

                            </tr>
                        @endforeach

                        </tbody>
                    </table>

                </div>
            </div>
        </div>

    </div>
@endsection
