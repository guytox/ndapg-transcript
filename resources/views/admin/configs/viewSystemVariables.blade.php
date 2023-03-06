@extends('layouts.setup')

@section('content')

    <h1>List of System Variables (***Modify with caution***)</h1>

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
                                    <th scope="col">Variable Name</th>
                                    <th scope="col">Value</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($systemVariables as $key => $v)


                                    <tr>
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                {{$key+1}}
                                            </div>
                                        </td>

                                        <td>

                                            <h5 class="font-size-15 mb-0">{{$v->name}}</h5>
                                        </td>
                                        <td>{{$v->value}}</td>
                                        <td>{{$v->description}}</td>
                                        <td>


                                            {!! Form::open(['route' => ['systemvariables.destroy', 'systemvariable'=> $v->id] , 'method' => 'DELETE']) !!}

                                            <a class="popup-form btn btn-primary" href="#edit-form{{$key+1}}">Change Value</a>

                                            {!! Form::submit('Delete', ['class'=>'btn btn-danger']) !!}

                                            {!! Form::close() !!}

                                        </td>
                                    </tr>



                                    <div class="card mfp-hide mfp-popup-form mx-auto" id="edit-form{{$key+1}}">
                                        <div class="card-body">
                                            <h4 class="mt-0 mb-4">Edit System Variable </h4>
                                            {!! Form::open(['route' =>['systemvariables.update', 'systemvariable'=>$v->id] , 'method' => 'PUT']) !!}

                                            {!! Form::hidden('id', $v->id, ['class'=>'form-control']) !!}
                                            {!! Form::hidden('uid', uniqid('sv_'), ['class'=>'form-control']) !!}

                                            <div class="form-group">
                                                {!! Form::label('name', 'Enter Enter Faculty Name (Don\'t use the Prefix "Faculty of")') !!}
                                                {!! Form::text('name', $v->name, ['class'=>'form-control', 'readonly', 'required' ]) !!}
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('value', 'Enter a Value for the Variable') !!}
                                                {!! Form::text('value', $v->value,['class'=>'form-control', 'required' ]) !!}
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('description', 'Enter a Faculty Description') !!}
                                                {!! Form::text('description', $v->description,['class'=>'form-control', 'readonly',  'required'  ]) !!}
                                            </div>

                                            {!! Form::submit('Change System Variable',['class'=>'btn btn-success']) !!}

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
                                    <a class="popup-form btn btn-danger" href="#test-form">Create New System Variable</a>
                                </div>

                                <div class="card mfp-hide mfp-popup-form mx-auto" id="test-form">
                                    <div class="card-body">
                                        <h4 class="mt-0 mb-4">Specify Details of New Faculty Below</h4>
                                        {!! Form::open(['route' => 'systemvariables.store', 'method' => 'POST']) !!}

                                        {!! Form::hidden('uid',uniqid('sv_'), ['class'=>'form-control']) !!}

                                        <div class="form-group">
                                            {!! Form::label('name', 'Enter Variable Name') !!}
                                            {!! Form::text('name', '',['class'=>'form-control', 'required' ]) !!}
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('value', 'Enter a Value for the Variable') !!}
                                            {!! Form::text('value', '',['class'=>'form-control', 'required' ]) !!}
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('description', 'Enter a Description for the Variable (Possible Values)') !!}
                                            {!! Form::text('description', '',['class'=>'form-control', 'required' ]) !!}
                                        </div>



                                        {!! Form::submit('Create New System Variable') !!}

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
