@extends('layouts.setup')

@section('css')

 <!-- DataTables -->
 <link href="{{asset('admin/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />
 <link href="{{asset('admin/assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />

 <!-- Responsive datatable examples -->
 <link href="{{asset('admin/assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />

 <!-- Lightbox css -->
 <link href="{{ asset('admin/assets/libs/magnific-popup/magnific-popup.css') }}" rel="stylesheet" type="text/css" />



@endsection




@section('content')

    @if(session()->has('message'))
        <div class="alert alert-danger">
                {{ session()->get('message') }}
        </div>
    @endif



    <h4>{{ $title }} {{ getsessionById($allMonitor->session_id)->name}}, {{ ucfirst(getSemesterDetailsById($allMonitor->semester_id))}} Semester Course Allocation </h4>

    <div class="row">
        @include('includes.messages')
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-4"></h4>

                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mb-4"></h4>
                            <div>
                                <a class="popup-form btn btn-primary" href="#new-fee-template-item">Add New Course Allocation</a>

                                <a class="btn btn-warning" href="{{back()}}">Return Back</a>
                            </div>

                            <div class="card mfp-hide mfp-popup-form mx-auto" id="new-fee-template-item">
                                <div class="card-body">
                                    <h4 class="mt-0 mb-4">Provide New Curriculum Details</h4>
                                    {!! Form::open(['route' => ['add.allocation.staff'], 'method' => 'POST']) !!}

                                    <div class="form-group">
                                        {!! Form::hidden('MonitorId', $allMonitor->uid, ['class'=>'form-control']) !!}

                                        {!! Form::hidden('uid',uniqid('sca_'), ['class'=>'form-control']) !!}
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('semester_courses_id', 'Select Semester Course') !!}
                                        {!! Form::select('semester_courses_id', $curriculumCourses, null, ['class' => 'form-control', 'required']) !!}
                                    </div>

                                    {{-- <div class="form-group">
                                        {!! Form::label('staffId', 'Select Semester Course') !!}
                                        {!! Form::select('staffId', $lecturers, null, ['class' => 'form-control', 'required']) !!}
                                    </div> --}}

                                    <livewire:search-lecturer>

                                    <div class="form-group">
                                        {!! Form::label('gradingRights', 'Can this User grade (Yes/No)') !!}
                                        {!! Form::select('gradingRights', array(1 => 'Yes', 2 => 'No'), 2, ['class' => 'form-control', 'required']) !!}
                                    </div>


                                    {!! Form::submit('Add New Curriculum Course') !!}

                                    {!! Form::close() !!}
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">S/N</th>
                                    <th scope="col">Code</th>
                                    <th scope="col">Title</th>
                                    <th scope="col">staffId</th>
                                    <th scope="col">Lecturer Name</th>
                                    <th scope="col">GSM</th>
                                    <th scope="col">can Grade</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>



                            @if ($allMonitor)



                                @foreach ($allMonitor->allocationItems as $key => $item)


                                    <tr>
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                {{$key+1}}
                                            </div>
                                        </td>
                                        <td>{{ getCourseDetailsById($item->course_id,'code') }}</td>
                                        <td>{{ getCourseDetailsById($item->course_id,'title') }}</td>
                                        <td>{{ getUser($item->staff_id,'username') }}</td>
                                        <td>{{ getUser($item->staff_id,'name') }}</td>
                                        <td>{{ getUser($item->staff_id,'phone_number') }}</td>

                                        @if ($item->can_grade == 1)
                                            <td> YES </td>
                                        @elseif ($item->can_grade == 2)
                                            <td> NO </td>
                                        @endif



                                        <td>


                                            {!! Form::open(['route' => ['delete.allocation.staff', 'id'=>$item->id] , 'method' => 'POST']) !!}

                                            <a class="popup-form btn btn-primary" href="#edit-form{{$key+1}}">Change Grading Rights</a>

                                            {!! Form::submit('Delete Course Allocation', ['class'=>'btn btn-danger']) !!}

                                            {!! Form::close() !!}

                                        </td>
                                    </tr>




                                        <div class="card mfp-hide mfp-popup-form mx-auto" id="edit-form{{$key+1}}">
                                            <div class="card-body">
                                                <h4 class="mt-0 mb-4">Edit Curriculum Course</h4>
                                                {!! Form::open(['route' => ['course-allocation.update',$item->id] , 'method' => 'PUT']) !!}


                                                <div class="form-group">
                                                    {!! Form::hidden('MonitorId', $allMonitor->uid, ['class'=>'form-control']) !!}
                                                    {!! Form::hidden('uid',uniqid('sca_'), ['class'=>'form-control']) !!}
                                                </div>

                                                <div class="form-group">
                                                    {!! Form::label('semester_courses_id', 'Select Semester Course') !!}
                                                    {!! Form::select('semester_courses_id', $semesterCourses, $item->course_id, ['class' => 'form-control', 'required']) !!}
                                                </div>

                                                <div class="form-group">
                                                    {!! Form::label('staffId', 'Select Semester Course') !!}
                                                    {!! Form::select('staffId', $lecturers, null, ['class' => 'form-control', 'required']) !!}
                                                </div>

                                                <div class="form-group">
                                                    {!! Form::label('gradingRights', 'Can this User grade (Yes/No)') !!}
                                                    {!! Form::select('gradingRights', array('1' => 'Yes', '2' => 'No'), '2', ['class' => 'form-control', 'required']) !!}
                                                </div>

                                                {!! Form::submit('Edit Curriculum Course',['class'=>'btn btn-success']) !!}

                                                {!! Form::close() !!}
                                            </div>
                                        </div>
                                @endforeach

                            @endif

                                    <tr>
                                        <td></td>
                                        <td colspan="3"> <span class="text-default">Courses without Staff to Grade/Upload Scores</span> </td>
                                        <td colspan="4">
                                            @foreach ($unallocated as $course)
                                               <i class="text-danger" title="{{getCourseDetailsById($course, 'title')}}">{{getCourseDetailsById($course, 'code')}},</i>
                                            @endforeach

                                        </td>

                                    </tr>

                            </tbody>
                        </table>





                        <div class="card">
                            <div class="card-body">
                                <h4 class="header-title mb-4"></h4>
                                <div>
                                    <a class="popup-form btn btn-primary" href="#new-fee-template-item">Add New Course Allocation</a>

                                    <a class="btn btn-warning" href="{{redirect(back())}}">Return Back</a>

                                    <a class="btn btn-dark" href="#">Print Course Allocation</a>
                                </div>



                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end row -->

@endsection



@section('js')

 <!-- Magnific Popup-->
 <script src="{{asset('admin/assets/libs/magnific-popup/jquery.magnific-popup.min.js')}} "></script>

 <!-- Tour init js-->
 <script src="{{ asset('admin/assets/js/pages/lightbox.init.js') }} "></script>

 <script src="{{asset('admin/assets/js/app.js')}}"></script>

@endsection
