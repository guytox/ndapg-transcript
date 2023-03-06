@extends('layouts.setup')

@section('content')

    <h2>List of Carry Overs for {{$stdName}} ({{$stdMatric}})</h2>
    <h4 class="text text-success">*** To Delete any carry over from this student, contact ICT ***</h4>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-4"></h4>

                    @include('includes.messages')

                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">S/N</th>
                                    <th scope="col">Session</th>
                                    <th scope="col">Semester</th>
                                    <th scope="col">Code</th>
                                    <th scope="col">Title</th>
                                    <th scope="col">Units</th>
                                    <th scope="col">Category</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($carryOverList as $key => $v)

                                    <tr>
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                {{$key+1}}
                                            </div>
                                        </td>
                                        <td>{{getsessionById($v->session_id)->name}}</td>
                                        <td>{{getSemesterNameById($v->semester_id)}}</td>
                                        <td>{{getCourseDetailsById($v->course_id,'code')}}</td>
                                        <td>{{getCourseDetailsById($v->course_id,'title')}}</td>
                                        <td>{{getCourseDetailsById($v->course_id,'credits')}}</td>
                                        <td>{{$v->category}}</td>
                                        <td>
                                            @role('admin|ict_admin')
                                            {!! Form::open(['route' => ['comgt.destroy', 'comgt'=> $v->id] , 'method' => 'DELETE']) !!}

                                            {!! Form::submit('Delete', ['class'=>'btn btn-danger']) !!}

                                            {!! Form::close() !!}
                                            @endrole


                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>





                        <div class="card">
                            <div class="card-body">
                                <h4 class="header-title mb-4"></h4>
                                <div>
                                    <a class="popup-form btn btn-primary" href="#test-form">Add Carry Over Course</a>

                                    <a class="btn btn-warning" href="{{route('comgt.index')}}">Back</a>
                                </div>

                                <div class="card mfp-hide mfp-popup-form mx-auto" id="test-form">
                                    <div class="card-body">
                                        <h4 class="mt-0 mb-4">Adding New Carry Over for {{$stdName}} ({{$stdMatric}})</h4>
                                        {!! Form::open(['route' => 'comgt.store', 'method' => 'POST']) !!}

                                        {!! Form::hidden('studentId', $stdId, ['class'=>'form-control']) !!}

                                        <div class="form-group">
                                            {!! Form::label('coursecode', 'Enter the course code ') !!}
                                            {!! Form::text('coursecode', '',['class'=>'form-control', 'required', 'placeholder'=>"e.g. CMM1301 ***(NO SPACE)" ]) !!}
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('schsession', 'Is it Academic or Non-Academic ') !!}
                                            {!! Form::select('schsession',getSessionsDropdown() ,'0',['class'=>'form-control', 'required' ]) !!}
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('semester', 'Select Semester') !!}
                                            {!! Form::select('semester',[''=>"N/A", 1=>'First', 2=>'Second'] ,'',['class'=>'form-control', 'required' ]) !!}
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('category', 'Select Carry Over Category') !!}
                                            {!! Form::select('category',[''=>"N/A", 'core'=>'Core', 'elective'=>'Elective'] ,'',['class'=>'form-control', 'required' ]) !!}
                                        </div>

                                        {!! Form::submit('Add New Carry Over Course') !!}

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
