@extends('layouts.setup')

@section('content')

    <h1>List of Semester Courses</h1>

    <div class="row">
        <div class="col-lg-12">

            <div class="card">
                <div class="card-body">
                    <div>
                        <a class="popup-form btn btn-primary" href="#test-form">Create New Semester Course</a>
                    </div>
                    <h4 class="header-title mb-4"></h4>
                    @include('includes.messages')
                    @error('error')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $error }}</strong>
                        </span>
                    @enderror


                    <div class="table-responsive">
                        <table id="datatable-buttons" class="table table-bordered dt-responsive nowrap mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">S/N</th>
                                    <th scope="col">Code</th>
                                    <th scope="col">Title</th>
                                    <th scope="col">credits</th>
                                    <th scope="col"> Department</th>
                                    <th scope="col"> Synopsis</th>
                                    <th scope="col"> Max. CA</th>
                                    <th scope="col"> Max. Exam</th>
                                    <th scope="col"> Status</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($courses as $key => $department)


                                    <tr>
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                {{$key+1}}
                                            </div>
                                        </td>

                                        <td>
                                            <h5 class="font-size-15 mb-0">{{$department->courseCode}}</h5>
                                        </td>
                                        <td>{{$department->courseTitle}}</td>
                                        <td>{{$department->creditUnits}}</td>
                                        <td>{{$department->deptName}}</td>
                                        <td>{{$department->courseDescription}}</td>
                                        <td>{{$department->max_ca}}</td>
                                        <td>{{$department->max_exam}}</td>
                                        @if ($department->activeStatus===1)
                                        <td>{{'Active'}}</td>
                                        <td>

                                            {!! Form::open(['route' => ['semestercourses.destroy', 'semestercourse'=>$department->id] , 'method' => 'DELETE']) !!}
                                            <a class="btn btn-warning" href="{{ route('semestercourses.edit',['semestercourse'=>$department->id, 'action'=>'Deactivate']) }}">Deactivate</a>
                                        @else
                                        <td>{{ 'Inactive'}}</td>

                                        <td>
                                            {!! Form::open(['route' => ['semestercourses.destroy', 'semestercourse'=>$department->id] , 'method' => 'DELETE']) !!}
                                            <a class="btn btn-primary" href="{{ route('semestercourses.edit',['semestercourse'=>$department->id, 'action'=>'Activate']) }}">Activate</a>
                                        @endif

                                            <a class="popup-form btn btn-primary" href="#edit-form{{$key+1}}">Edit Details</a>

                                            {!! Form::submit('Delete', ['class'=>'btn btn-danger']) !!}

                                            {!! Form::close() !!}

                                        </td>
                                    </tr>

                                    <div class="card mfp-hide mfp-popup-form mx-auto" id="edit-form{{$key+1}}">
                                        <div class="card-body">
                                            <h4 class="mt-0 mb-4">Edit Department Details</h4>
                                            {!! Form::open(['route' => ['semestercourses.update', 'semestercourse'=>$department->id]  , 'method' => 'PUT']) !!}

                                            {!! Form::hidden('id', $department->id, ['class'=>'form-control']) !!}
                                            {!! Form::hidden('uid',uniqid('sc_'), ['class'=>'form-control']) !!}

                                            <div class="form-group">
                                                {!! Form::label('department_id', 'Select Associate Department') !!}
                                                {!! Form::select('department_id', $departments, $department->department_id, ['class' => 'form-control' ]) !!}
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('courseTitle', 'Enter the course Title e.g. "General Physics II"') !!}
                                                {!! Form::text('courseTitle', $department->courseTitle,['class'=>'form-control', 'required' ]) !!}
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('courseCode', 'Enter the CourseCode e.g. CMP121') !!}
                                                {!! Form::text('courseCode', $department->courseCode,['class'=>'form-control', 'required' ]) !!}
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('creditUnits', 'Enter the Credit Unit **(must be a figure) ') !!}
                                                {!! Form::text('creditUnits', $department->creditUnits,['class'=>'form-control', 'required' ]) !!}
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('courseDescription', 'Enter the course Synopsis') !!}
                                                {!! Form::text('courseDescription', $department->courseDescription,['class'=>'form-control', 'required' ]) !!}
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('max_ca', 'Enter the Max CA Scores') !!}
                                                {!! Form::text('max_ca', $department->max_ca,['class'=>'form-control', 'required' ]) !!}
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('max_exam', 'Enter the Max Exam Scores') !!}
                                                {!! Form::text('max_exam', $department->max_exam,['class'=>'form-control', 'required' ]) !!}
                                            </div>

                                            {!! Form::submit('Edit Semester Courses Details',['class'=>'btn btn-success']) !!}

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
                                    <a class="popup-form btn btn-primary" href="#test-form">Create New Semester Course</a>
                                </div>

                                <div class="card mfp-hide mfp-popup-form mx-auto" id="test-form">
                                    <div class="card-body">
                                        <h4 class="mt-0 mb-4">Specify Details of New Semester Course Below</h4>
                                        {!! Form::open(['route' => 'semestercourses.store', 'method' => 'POST']) !!}

                                        {!! Form::hidden('uid',uniqid('sc_'), ['class'=>'form-control']) !!}


                                        <div class="form-group">
                                            {!! Form::label('department_id', 'Select Associate Department') !!}
                                            {!! Form::select('department_id', $departments, null, ['class' => 'form-control' ]) !!}
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('courseTitle', 'Enter the course Title e.g. "General Physics II"') !!}
                                            {!! Form::text('courseTitle', '',['class'=>'form-control', 'required' ]) !!}
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('creditUnits', 'Enter the Credit Unit **(must be a number) ') !!}
                                            {!! Form::text('creditUnits', '',['class'=>'form-control', 'required' ]) !!}
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('courseCode', 'Enter the CourseCode e.g. CMP121') !!}
                                            {!! Form::text('courseCode', '',['class'=>'form-control', 'required' ]) !!}
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('courseDescription', 'Enter the course Synopsis') !!}
                                            {!! Form::text('courseDescription', '',['class'=>'form-control', 'required' ]) !!}
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('max_ca', 'Enter the Max CA Scores') !!}
                                            {!! Form::text('max_ca', '',['class'=>'form-control', 'required' ]) !!}
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('max_exam', 'Enter the Max Exam Scores') !!}
                                            {!! Form::text('max_exam', '',['class'=>'form-control', 'required' ]) !!}
                                        </div>

                                        {!! Form::submit('Create New Semester Course') !!}

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
