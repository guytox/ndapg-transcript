@extends('layouts.setup')

@section('content')

    <h1>List of Academic Sessions</h1>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-4"></h4>

                    @error('')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $error }}</strong>
                        </span>
                    @enderror

                    @include('includes.messages')

                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">S/N</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Current <br> Semester</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">IsActive</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($academicSessions as $key => $v)


                                    <tr>
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                {{$key+1}}
                                            </div>
                                        </td>

                                        <td>

                                            <h5 class="font-size-15 mb-0">{{$v->name}}</h5>
                                        </td>
                                        <td>{{$v->currentSemester}}</td>
                                        <td>{{$v->description}}</td>
                                        @if ($v->status ==1)
                                            <td>Yes</td>

                                        @else

                                            <td>No</td>

                                        @endif

                                        <td>


                                            {!! Form::open(['route' => ['acadsessions.destroy', 'acadsession'=> $v->id] , 'method' => 'DELETE']) !!}

                                            <a class="popup-form btn btn-primary" href="#edit-form{{$key+1}}">Edit Details</a>

                                            {!! Form::submit('Delete', ['class'=>'btn btn-danger']) !!}

                                            {!! Form::close() !!}

                                        </td>
                                    </tr>


                                    <div class="card mfp-hide mfp-popup-form mx-auto" id="edit-form{{$key+1}}">
                                        <div class="card-body">
                                            <h4 class="mt-0 mb-4">Edit Academic Session Details</h4>
                                            {!! Form::open(['route' =>['acadsessions.update', 'acadsession'=>$v->id] , 'method' => 'PUT']) !!}

                                            {!! Form::hidden('id', $v->id, ['class'=>'form-control']) !!}
                                            {!! Form::hidden('uid',uniqid('as_'), ['class'=>'form-control']) !!}

                                            <div class="form-group">
                                                {!! Form::label('name', 'Enter Academic Session Name') !!}
                                                {!! Form::text('name', $v->name,['class'=>'form-control', 'required' ]) !!}
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('currentSemester', 'Select Current Semester') !!}
                                                {!! Form::select('currentSemester', ['first'=>'First', 'second'=>'Second'], $v->currentSemester, ['class'=>'form-control', 'required']) !!}
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('description', 'Enter Academic Session Description') !!}
                                                {!! Form::text('description', $v->description,['class'=>'form-control', 'required' ]) !!}
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('status', 'Select Current Semester') !!}
                                                {!! Form::select('status', [0=>'No', 1=>'Yes'], $v->status, ['class'=>'form-control', 'required', 'readonly']) !!}
                                            </div>

                                            {!! Form::submit('Edit Academic Session Details',['class'=>'btn btn-success']) !!}

                                            {!! Form::close() !!}
                                        </div>
                                    </div>



                                @endforeach
                            </tbody>
                        </table>





                        <div class="card">
                            <div class="card-body">
                                <h4 class="header-title mb-4"></h4>
                                <div>
                                    <a class="popup-form btn btn-primary" href="#test-form">Create New Academic Session</a>
                                </div>

                                <div class="card mfp-hide mfp-popup-form mx-auto" id="test-form">
                                    <div class="card-body">
                                        <h4 class="mt-0 mb-4">Specify Details of New Academic Session Below</h4>
                                        {!! Form::open(['route' => 'acadsessions.store', 'method' => 'POST']) !!}

                                        {!! Form::hidden('uid',uniqid('as_'), ['class'=>'form-control']) !!}

                                        <div class="form-group">
                                            {!! Form::label('name', 'Enter Academic Session Name') !!}
                                            {!! Form::text('name', '',['class'=>'form-control', 'required' ]) !!}
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('currentSemester', 'Select Current Semester') !!}
                                            {!! Form::select('currentSemester', ['first'=>'First', 'second'=>'Second'], 'first', ['class'=>'form-control', 'required']) !!}
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('description', 'Enter Academic Session Description') !!}
                                            {!! Form::text('description', '',['class'=>'form-control', 'required' ]) !!}
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('status', 'Is this the Current Session?') !!}
                                            {!! Form::select('status', [0=>'No', 1=>'Yes'], 0, ['class'=>'form-control', 'required']) !!}
                                        </div>

                                        {!! Form::submit('Create New Academic Session') !!}

                                        {!! Form::close() !!}
                                    </div>
                                </div>

                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end row -->

    <script>
        @if(session()->has('error'))
          alert('{{session()->get('error')}}')
        @endif
    </script>

@endsection
