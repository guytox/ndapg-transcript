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



    <h1>Details of {{ $curriculum->title }}</h1>

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
                                <a class="popup-form btn btn-primary" href="#new-fee-template-item">Add New Curriculum Course</a>

                                <a class="btn btn-warning" href="{{redirect(back())}}">Return Back</a>
                            </div>

                            <div class="card mfp-hide mfp-popup-form mx-auto" id="new-fee-template-item">
                                <div class="card-body">
                                    <h4 class="mt-0 mb-4">Provide New Curriculum Details</h4>
                                    {!! Form::open(['route' => ['curriculaitems.store'], 'method' => 'POST']) !!}

                                    <div class="form-group">
                                        {!! Form::hidden('curricula_id', $curriculum->id, ['class'=>'form-control']) !!}
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('semester_courses_id', 'Select Semester Course') !!}
                                        {!! Form::select('semester_courses_id', $courses, null, ['class' => 'form-control', 'required']) !!}
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('alternative', 'Select Alternative Semester Course **(only if you select Core/Optional category)') !!}
                                        {!! Form::select('alternative', [''=>'N/A',$courses], null, ['class' => 'form-control']) !!}
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('category', 'Select Semester Course') !!}
                                        {!! Form::select('category', array('core' => 'Core', 'elective' => 'Elective','core/optional'=>'Core/Optional'), null, ['class' => 'form-control', 'required']) !!}
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
                                    <th scope="col">Credits</th>
                                    <th scope="col">category</th>
                                    <th scope="col">Alternative</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>

                                @php
                                    $totalCredits =0;
                                    $totalCore=0;
                                    $totalElective=0;
                                    $totalAlternatives=0;
                                @endphp

                                @if ($curriculum->numOfCourses>=1)



                                @foreach ($curriculum->curriculumItems as $key => $item)


                                    <tr>
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                {{$key+1}}
                                            </div>
                                        </td>
                                        <td>{{ getCourseDetailsById($item->semester_courses_id,'code') }}</td>
                                        <td>{{ getCourseDetailsById($item->semester_courses_id,'title') }}</td>
                                        <td>{{ getCourseDetailsById($item->semester_courses_id,'credits') }}</td>
                                        <td>{{ $item->category }}</td>
                                        @if ($item->alternative == '')
                                            <td>N/A</td>
                                        @else
                                            <td>{{ getCourseDetailsById($item->alternative,'code') }}</td>
                                        @endif



                                        <td>


                                            {!! Form::open(['route' => ['curriculaitems.destroy', 'curriculaitem'=>$item->id] , 'method' => 'DELETE']) !!}

                                            <a class="popup-form btn btn-primary" href="#edit-form{{$key+1}}">Edit Curriculum Course</a>

                                            {!! Form::submit('Delete Course', ['class'=>'btn btn-danger']) !!}

                                            {!! Form::close() !!}

                                        </td>
                                    </tr>

                                    @php
                                        $totalCredits += getCourseDetailsById($item->semester_courses_id,'credits');
                                        if ($item->category == 'core') {
                                            $totalCore += getCourseDetailsById($item->semester_courses_id,'credits');
                                        }elseif ($item->category == 'elective') {
                                            $totalElective += getCourseDetailsById($item->semester_courses_id,'credits');
                                        }elseif ($item->category == 'core/optional') {
                                            $totalCore += getCourseDetailsById($item->semester_courses_id,'credits');
                                            $totalAlternatives += getCourseDetailsById($item->semester_courses_id,'credits');
                                        }


                                    @endphp


                                        <div class="card mfp-hide mfp-popup-form mx-auto" id="edit-form{{$key+1}}">
                                            <div class="card-body">
                                                <h4 class="mt-0 mb-4">Edit Curriculum Course</h4>
                                                {!! Form::open(['route' => ['curriculaitems.update',$item->id] , 'method' => 'PUT']) !!}


                                                    {!! Form::hidden('curricula_id', $curriculum->id, ['class'=>'form-control']) !!}


                                                <div class="form-group">
                                                    {!! Form::label('semester_courses_id', 'Select Semester Course') !!}
                                                    {!! Form::select('semester_courses_id', $courses, $item->semester_courses_id, ['class' => 'form-control', 'required']) !!}
                                                </div>

                                                <div class="form-group">
                                                    {!! Form::label('alternative', 'Select Alternative Semester Course **(only if you select Core/Optional category)') !!}
                                                    {!! Form::select('alternative', [''=>'N/A',$courses], $item->alternatives, ['class' => 'form-control']) !!}
                                                </div>

                                                <div class="form-group">
                                                    {!! Form::label('category', 'Select Semester Course') !!}
                                                    {!! Form::select('category', array('core' => 'Core', 'elective' => 'Elective','core/optional'=>'Core/Optional'), $item['category'], ['class' => 'form-control', 'required']) !!}
                                                </div>

                                                {!! Form::submit('Edit Curriculum Course',['class'=>'btn btn-success']) !!}

                                                {!! Form::close() !!}
                                            </div>
                                        </div>
                                @endforeach

                                @endif

                                    <tr>
                                        <td></td>
                                        <td>Total Core Credits</td>
                                        <td>{{ $totalCore }}</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>Total Elective Credits</td>
                                        <td>{{ $totalElective }}</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>Total Optional Credits</td>
                                        <td>{{ $totalAlternatives }}</td>
                                        <td></td>
                                    </tr>

                                    <tr>
                                        <td></td>
                                        <td>Total Credits</td>
                                        <td>{{ $totalCredits.'/'.$totalAlternatives }}</td>
                                        <td></td>
                                    </tr>
                            </tbody>
                        </table>





                        <div class="card">
                            <div class="card-body">
                                <h4 class="header-title mb-4"></h4>
                                <div>
                                    <a class="popup-form btn btn-primary" href="#new-fee-template-item">Add New Fee Template Item</a>

                                    <a class="btn btn-warning" href="{{redirect(back())}}">Return Back</a>
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
