@extends('layouts.setup')

@section('content')

    <h1>List of Departments</h1>

    <div class="row">
        <div class="col-lg-12">

            <div class="card">
                <div class="card-body">
                    <div>
                        <a class="popup-form btn btn-primary" href="#test-form">Create New Department</a>
                    </div>
                    <h4 class="header-title mb-4"></h4>

                    @error('error')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $error }}</strong>
                        </span>
                    @enderror


                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">S/N</th>
                                    <th scope="col">Faculty</th>
                                    <th scope="col">Department</th>
                                    <th scope="col"> Description</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($departments as $key => $department)


                                    <tr>
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                {{$key+1}}
                                            </div>
                                        </td>

                                        <td>{{getFacultyDetailsById($department->faculty_id, 'name')}}</td>
                                        <td>

                                            <h5 class="font-size-15 mb-0">{{$department->name}}</h5>
                                        </td>

                                        <td>{{$department->description}}</td>
                                        <td>


                                            {!! Form::open(['route' => ['departments.destroy', 'department'=>$department->id] , 'method' => 'DELETE']) !!}

                                            <a class="popup-form btn btn-primary" href="#edit-form{{$key+1}}">Edit Details</a>

                                            {!! Form::submit('Delete', ['class'=>'btn btn-danger']) !!}

                                            {!! Form::close() !!}

                                        </td>
                                    </tr>

                                    <div class="card mfp-hide mfp-popup-form mx-auto" id="edit-form{{$key+1}}">
                                        <div class="card-body">
                                            <h4 class="mt-0 mb-4">Edit Department Details</h4>
                                            {!! Form::open(['route' => ['departments.update', 'department'=>$department->id]  , 'method' => 'PUT']) !!}

                                            {!! Form::hidden('id', $department->id, ['class'=>'form-control']) !!}
                                            {!! Form::hidden('uid',uniqid('dp_'), ['class'=>'form-control']) !!}

                                            <div class="form-group">
                                                {!! Form::label('faculty_id', 'Select Associate College') !!}
                                                {!! Form::select('faculty_id',  $faculties, $department->faculty_id,  ['class' => 'form-control' ]) !!}
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('name', 'Enter Enter Department Name (Don\'t use the Prefix "Department of")') !!}
                                                {!! Form::text('name', $department->name, ['class'=>'form-control', 'required' ]) !!}
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('description', 'Enter Department Description') !!}
                                                {!! Form::text('description', $department->description,['class'=>'form-control', 'required'  ]) !!}
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('academic', 'Is it Academic or Non-Academic ') !!}
                                                {!! Form::select('academic',[1=>'Academic', 2=>'Non-Academic'] ,$department->academic,['class'=>'form-control', 'required' ]) !!}
                                            </div>

                                            {!! Form::submit('Edit Department Details',['class'=>'btn btn-success']) !!}

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
                                    <a class="popup-form btn btn-primary" href="#test-form">Create New Department</a>
                                </div>

                                <div class="card mfp-hide mfp-popup-form mx-auto" id="test-form">
                                    <div class="card-body">
                                        <h4 class="mt-0 mb-4">Specify Details of New Department Below</h4>
                                        {!! Form::open(['route' => 'departments.store', 'method' => 'POST']) !!}
                                        {!! Form::hidden('uid',uniqid('dp_'), ['class'=>'form-control']) !!}


                                        <div class="form-group">
                                            {!! Form::label('faculty_id', 'Select Associate College') !!}
                                            {!! Form::select('faculty_id', $faculties, null, ['class' => 'form-control' ]) !!}
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('name', 'Enter Enter Department Name (Don\'t use the Prefix "Department of")') !!}
                                            {!! Form::text('name', '',['class'=>'form-control', 'required' ]) !!}
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('description', 'Enter a Shortcode for the Department') !!}
                                            {!! Form::text('description', '',['class'=>'form-control', 'required' ]) !!}
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('academic', 'Is it Academic or Non-Academic ') !!}
                                            {!! Form::select('academic',[1=>'Academic', 2=>'Non-Academic'] ,'1',['class'=>'form-control', 'required' ]) !!}
                                        </div>

                                        {!! Form::submit('Create New Department') !!}

                                        {!! Form::close() !!}
                                    </div>
                                </div>

                            </div>
                        </div>


                                <div class="card" >
                                    <div class="card-body">
                                        <h4 class="header-title mb-4"></h4>


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
