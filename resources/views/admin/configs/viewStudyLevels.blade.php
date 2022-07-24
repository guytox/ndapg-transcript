@extends('layouts.setup')

@section('content')

    <h1>List of Colleges</h1>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div>
                        <a class="popup-form btn btn-primary" href="#test-form">Create Study Level</a>
                    </div>
                    <h4 class="header-title mb-4"></h4>

                    @error('')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $error }}</strong>
                        </span>
                    @enderror



                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">S/N</th>
                                    <th scope="col">Study Level</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($studyLevels as $key => $level)


                                    <tr>
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                {{$key+1}}
                                            </div>
                                        </td>

                                        <td>

                                            <h5 class="font-size-15 mb-0">{{$level->level}}</h5>
                                        </td>
                                        <td>{{$level->description}}</td>
                                        <td>


                                            {!! Form::open(['route' => ['studylevels.destroy', 'studylevel'=> $level->id] , 'method' => 'DELETE']) !!}

                                            <a class="popup-form btn btn-primary" href="#edit-form{{$key+1}}">Edit Details</a>

                                            {!! Form::submit('Delete', ['class'=>'btn btn-danger']) !!}

                                            {!! Form::close() !!}

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>





                        <div class="card">
                            <div class="card-body">
                                <h4 class="header-title mb-4"></h4>
                                <div>
                                    <a class="popup-form btn btn-primary" href="#test-form">Create Study Level</a>
                                </div>

                                <div class="card mfp-hide mfp-popup-form mx-auto" id="test-form">
                                    <div class="card-body">
                                        <h4 class="mt-0 mb-4">Specify Details of New Study Level Below</h4>
                                        {!! Form::open(['route' => 'studylevels.store', 'method' => 'POST']) !!}

                                        {!! Form::hidden('uid',uniqid('sl_'), ['class'=>'form-control']) !!}

                                        <div class="form-group">
                                            {!! Form::label('level', 'Enter Study Level eg. 800")') !!}
                                            {!! Form::text('level', '', ['class'=>'form-control', 'required' ]) !!}
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('description', 'Enter a Study Level Description') !!}
                                            {!! Form::text('description', '',['class'=>'form-control', 'required' ]) !!}
                                        </div>

                                        {!! Form::submit('Create New Study Level',['class'=>'btn btn-success']) !!}

                                        {!! Form::close() !!}
                                    </div>
                                </div>

                            </div>
                        </div>


                                <div class="card" >
                                    <div class="card-body">
                                        <h4 class="header-title mb-4"></h4>
                                    @foreach ($studyLevels as $key => $val2)
                                        <div class="card mfp-hide mfp-popup-form mx-auto" id="edit-form{{$key+1}}">
                                            <div class="card-body">
                                                <h4 class="mt-0 mb-4">Edit Study Level Details</h4>
                                                {!! Form::open(['route' =>['studylevels.update', 'studylevel'=>$val2->id] , 'method' => 'PUT']) !!}

                                                {!! Form::hidden('id', $val2->id, ['class'=>'form-control']) !!}
                                                {!! Form::hidden('uid', uniqid('sl_'), ['class'=>'form-control']) !!}

                                                <div class="form-group">
                                                    {!! Form::label('level', 'Enter Study Level eg. 800")') !!}
                                                    {!! Form::text('level', $val2->level, ['class'=>'form-control', 'required' ]) !!}
                                                </div>

                                                <div class="form-group">
                                                    {!! Form::label('description', 'Enter a level Description') !!}
                                                    {!! Form::text('description', $val2->description,['class'=>'form-control', 'required'  ]) !!}
                                                </div>

                                                {!! Form::submit('Edit Study Level Details',['class'=>'btn btn-success']) !!}

                                                {!! Form::close() !!}
                                            </div>
                                        </div>
                                    @endforeach

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
