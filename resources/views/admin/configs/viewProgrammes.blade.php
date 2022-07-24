@extends('layouts.setup')


@section('content')

    <h1>List of Programmes</h1>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div>
                        <a class="popup-form btn btn-primary" href="#test-form">Create New Programme</a>
                    </div>
                    <h4 class="header-title mb-4"></h4>

                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">S/N</th>
                                    <th scope="col">College Name</th>
                                    <th scope="col">Department Name</th>
                                    <th scope="col">Title</th>
                                    <th scope="col">Programme Name</th>
                                    <th scope="col">Study Level</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($programmes as $key => $programme)


                                    <tr>
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                {{$key+1}}
                                            </div>
                                        </td>

                                        <td>{{$programme->facultyName}}</td>
                                        <td>{{$programme->deptName}}</td>
                                        <td>{{$programme->degreeTitle}}</td>
                                        <td>

                                            <h5 class="font-size-15 mb-0">{{$programme->progName}}</h5>
                                        </td>

                                        <td>{{$programme->levelName}}</td>
                                        <td>


                                            {!! Form::open(['route' => ['programs.destroy', 'program'=>$programme->progId] , 'method' => 'DELETE']) !!}

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
                                    <a class="popup-form btn btn-primary" href="#test-form">Create New Programme</a>
                                </div>

                                <div class="card mfp-hide mfp-popup-form mx-auto" id="test-form">
                                    <div class="card-body">
                                        <h4 class="mt-0 mb-4">Specify Details of New Programme Below</h4>
                                        {!! Form::open(['route' => 'programs.store', 'method' => 'POST']) !!}

                                        {!! Form::hidden('uid',uniqid('pr_'), ['class'=>'form-control']) !!}


                                        <div class="form-group">
                                            {!! Form::label('department_id', 'Select Associate Department') !!}
                                            {!! Form::select('department_id', $departments, null, ['class' => 'form-control' ]) !!}
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('degree_title', 'Enter Enter Programm title eg M.Sc') !!}
                                            {!! Form::text('degree_title', '',['class'=>'form-control', 'required' ]) !!}
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('name', 'Enter Enter Programm Name (Start with Prefix e.g B.Eng. ') !!}
                                            {!! Form::text('name', '',['class'=>'form-control', 'required' ]) !!}
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('level_id', 'Select Studye Level') !!}
                                            {!! Form::select('level_id', $levels, null, ['class' => 'form-control' ]) !!}
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('description', 'Enter a description for the Programme') !!}
                                            {!! Form::text('description', '',['class'=>'form-control', 'required' ]) !!}
                                        </div>

                                        {!! Form::submit('Create New Programme', ['class'=>'btn btn-primary']) !!}

                                        <a class="btn btn-primary" href="{{route('programs.index')}}">Cancel</a>

                                        {!! Form::close() !!}
                                    </div>
                                </div>

                            </div>
                        </div>


                                <div class="card" >
                                    <div class="card-body">
                                        <h4 class="header-title mb-4"></h4>
                                    @foreach ($programmes as $key => $val2)
                                        <div class="card mfp-hide mfp-popup-form mx-auto" id="edit-form{{$key+1}}">
                                            <div class="card-body">
                                                <h4 class="mt-0 mb-4">Edit Programme Details</h4>
                                                {!! Form::open(['route' => ['programs.update', 'program'=>$val2->progId]  , 'method' => 'PUT']) !!}

                                                {!! Form::hidden('id', $val2->progId, ['class'=>'form-control']) !!}
                                                {!! Form::hidden('uid',uniqid('pr_'), ['class'=>'form-control']) !!}

                                                <div class="form-group">
                                                    {!! Form::label('department_id', 'Select Associate Departmentllege') !!}
                                                    {!! Form::select('department_id', $departments, $val2->department_id, ['class' => 'form-control' ]) !!}
                                                </div>

                                                <div class="form-group">
                                                    {!! Form::label('degree_title', 'Enter Enter Programm title eg M.Sc') !!}
                                                    {!! Form::text('degree_title', $val2->degreeTitle,['class'=>'form-control', 'required' ]) !!}
                                                </div>

                                                <div class="form-group">
                                                    {!! Form::label('name', 'Enter Enter Programm Name (Start with Prefix e.g B.Eng. ")') !!}
                                                    {!! Form::text('name', $val2->progName, ['class'=>'form-control', 'required' ]) !!}
                                                </div>

                                                <div class="form-group">
                                                    {!! Form::label('level_id', 'Select Studye Level') !!}
                                                    {!! Form::select('level_id', $levels, $val2->level_id, ['class' => 'form-control' ]) !!}
                                                </div>

                                                <div class="form-group">
                                                    {!! Form::label('description', 'Enter a description for the Programme') !!}
                                                    {!! Form::text('description', $val2->progDescription,['class'=>'form-control', 'required'  ]) !!}
                                                </div>

                                                {!! Form::submit('Edit Programme Details',['class'=>'btn btn-success']) !!}

                                                <a class="btn btn-primary" href="{{route('programs.index')}}">Cancel</a>

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

@endsection
