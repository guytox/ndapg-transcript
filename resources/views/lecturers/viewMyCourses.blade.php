@extends('layouts.setup')

@section('content')

    @include('includes.messages')

    <h4>List of Courses Allocated to {{user()->name}} for {{$session_name}}, {{ucfirst($semester)}} Semester</h4>

    <div class="row">
        <div class="col-lg-12">

            <div class="card">
                <div class="card-body">
                    <div>
                        <a class="popup-form btn btn-primary" href="#test-form">Search Another Session/Semester</a>
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
                                    <th scope="col">Code</th>
                                    <th scope="col">Title</th>
                                    <th scope="col"> Credits</th>
                                    <th scope="col">Dept</th>
                                    <th scope="col">Grading</th>
                                    <th scope="col">Submitted</th>
                                    <th scope="col">Hod Confirm</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($courses as $key => $val)


                                    <tr>
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                {{$key+1}}
                                            </div>
                                        </td>

                                        <td>{{getCourseDetailsById($val->course_id,'code')}}</td>
                                        <td>{{getCourseDetailsById($val->course_id,'title')}}</td>
                                        <td>{{getCourseDetailsById($val->course_id,'credits')}}</td>
                                        <td>{{getDepartmentDetailById(getCourseDetailsById($val->course_id,'all')->department_id,'name')}}</td>
                                        <td>

                                            @if ($val->cfm_ca1 ==='0')
                                                <span title="Confirmed CA1" > &#10060;</span>
                                            @elseif ($val->cfm_ca1 ==='1')
                                                <span title="Confirmed CA1" > &#9989;</span>
                                            @endif

                                            @if ($val->cfm_ca2 ==='0')
                                                <span title="Confirmed CA2" > &#10060;</span>
                                            @elseif ($val->cfm_ca2 ==='1')
                                                <span title="Confirmed CA2" > &#9989;</span>
                                            @endif

                                            @if ($val->cfm_ca3 ==='0')
                                                <span title="Confirmed CA3" > &#10060;</span>
                                            @elseif ($val->cfm_ca3 ==='1')
                                                <span title="Confirmed CA3" > &#9989;</span>
                                            @endif

                                            @if ($val->cfm_ca4 ==='0')
                                                <span title="Confirmed CA4" > &#10060;</span>
                                            @elseif ($val->cfm_ca4 ==='1')
                                                <span title="Confirmed CA4" > &#9989;</span>
                                            @endif

                                            @if ($val->cfm_exam ==='0')
                                                <span title="Confirmed EXAM" > &#10060;</span>
                                            @elseif ($val->cfm_exam ==='1')
                                                <span title="Confirmed EXAM" > &#9989;</span>
                                            @endif

                                        </td>

                                        <td>
                                            @if ($val->submitted ==='2')
                                                <span title="Lecturer Submission" > &#10060;</span>
                                            @elseif ($val->submitted ==='1')
                                                <span title="Lecturer Submission" > &#9989;</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($val->accepted ==='2')
                                                <span title="HOD Acceptance" > &#10060;</span>
                                            @elseif ($val->accepted ==='1')
                                                <span title="HOD Acceptance" > &#9989;</span>
                                            @endif

                                        </td>
                                        <td>


                                            {!! Form::open(['route' => ['lecturer.grading.download', 'as'=>'ortesenKwagh','id'=>$val->uid] , 'method' => 'POST']) !!}
                                            @if ($val->graded === '2' && $val->can_grade ==='1')
                                                <a class="btn btn-success" href="{{route('lecturer.grading.start',['as'=>'ortesenKwagh', 'id'=>$val->uid])}}">Start Grading</a>

                                            @elseif ($val->grading_completed ==='1')

                                                <a class="btn btn-success" href="{{route('lecturer.grading.scoresheet',['as'=>'ortesenKwagh', 'id'=>$val->uid])}}">View Score Sheet</a>

                                            @elseif ($val->graded ==='1' && $val->cfm_ca1 ==='1' && $val->cfm_ca2=='1' && $val->cfm_ca3=='1' && $val->cfm_ca4=='1' && $val->cfm_exam=='1' && $val->submitted=='2')

                                            <a class="popup-form btn btn-primary" href="#submit-form{{$key+1}}">Submit to HOD</a>

                                            <a class="popup-form btn btn-dark" href="#unconfirm-form{{$key+1}}">Reverse Confirmation</a>

                                            <a class=" btn btn-success" href="{{route('lecturer.grading.scoresheet',['as'=>'ortesenKwagh', 'id'=>$val->uid])}}">View Score Sheet</a>


                                            @elseif ($val->graded ==='1' && $val->can_grade ==='1' && $val->submitted=='2')

                                                <a class="popup-form btn btn-success" href="#edit-form{{$key+1}}">Upload Scores</a>

                                                <a class="btn btn-primary" href="{{route('lecturer.grading.manualupload',['as'=>'ortesenKwagh', 'id'=>$val->uid])}}">Enter Grades</a>

                                                <a class=" btn btn-success" href="{{route('lecturer.grading.scoresheet',['as'=>'ortesenKwagh', 'id'=>$val->uid])}}">View Score Sheet</a>

                                                <a class="popup-form btn btn-warning" href="#confirm-form{{$key+1}}">Confirm Grades</a>

                                                <a class="popup-form btn btn-dark" href="#unconfirm-form{{$key+1}}">Reverse Confirmation</a>



                                            @elseif ($val->graded ==='1' && $val->submitted=='1')

                                            <a class="btn btn-primary" href="{{route('lecturer.grading.scoresheet',['as'=>'ortesenKwagh', 'id'=>$val->uid])}}">View Score Sheet</a>

                                            @endif


                                            {!! Form::submit('Download Registrants', ['class'=>'btn btn-danger']) !!}

                                            {!! Form::close() !!}

                                        </td>
                                    </tr>

                                    <div class="card mfp-hide mfp-popup-form mx-auto" id="edit-form{{$key+1}}">
                                        <div class="card-body">
                                            <h4 class="mt-0 mb-4">Upload Scores using Excel File ***(format .xlsx)</h4>
                                            {!! Form::open(['route' => ['lecturer.grading.upload', 'as'=>'ortesenKwagh']  , 'method' => 'POST', 'file'=>true, 'enctype'=>"multipart/form-data"]) !!}

                                                {!! Form::hidden('id', $val->uid, ['class'=>'form-control']) !!}

                                                <div class="form-group">
                                                    {!! Form::label('grading', 'Select what to grade') !!}
                                                    {!! Form::select('grading', ['' =>'N/A', 'ca1'=>'CA1','ca2'=>'CA2', 'ca3'=>'CA3', 'ca4'=>'CA4', 'exam'=>'EXAM', 'all'=>'ALL'], '',['class'=>'form-control', 'required' ]) !!}
                                                </div>

                                                <div class="form-group">
                                                    {!! Form::label('file', 'Select .xlsx file to upload ***(must contain the following headers: // matricno // ca1 // ca2 // ca3 // ca4 // exam )') !!}
                                                    {!! Form::file('file',  ['class' => 'form-control', 'required' ]) !!}
                                                </div>

                                            {!! Form::submit('Upload Scores',['class'=>'btn btn-success']) !!}

                                            {!! Form::close() !!}
                                        </div>
                                    </div>

                                    <div class="card mfp-hide mfp-popup-form mx-auto" id="confirm-form{{$key+1}}">
                                        <div class="card-body">
                                            <h4 class="mt-0 mb-4">Grade Confirmation (Choose what to confirm and submit)</h4>
                                            {!! Form::open(['route' => ['lecturer.grading.confirm', 'as'=>'ortesenKwagh']  , 'method' => 'POST', 'file'=>true, 'enctype'=>"multipart/form-data"]) !!}

                                                {!! Form::hidden('id', $val->uid, ['class'=>'form-control']) !!}

                                                <div class="form-group">
                                                    {!! Form::label('grading', 'Select what to confirm') !!}
                                                    {!! Form::select('grading', ['' =>'N/A', 'ca1'=>'CA1','ca2'=>'CA2', 'ca3'=>'CA3', 'ca4'=>'CA4', 'exam'=>'EXAM', 'all'=>'ALL'], '',['class'=>'form-control', 'required' ]) !!}
                                                </div>

                                                <div class="form-group">
                                                    {!! Form::label('confirm', 'I confirm that I will not be able to grade this after this confirmation', ['class'=>'text-danger']) !!}
                                                    {!! Form::checkbox('confirm','mlumun' , false ,  [ 'required' ]) !!}
                                                </div>

                                            {!! Form::submit('Confirm Grades',['class'=>'btn btn-success']) !!}

                                            {!! Form::close() !!}
                                        </div>
                                    </div>

                                    <div class="card mfp-hide mfp-popup-form mx-auto" id="unconfirm-form{{$key+1}}">
                                        <div class="card-body">
                                            <h4 class="mt-0 mb-4 text-danger">Grade De -Confirmation (Choose what to deconfirm and submit)</h4>
                                            {!! Form::open(['route' => ['lecturer.grading.deconfirm', 'as'=>'ortesenKwagh']  , 'method' => 'POST', 'file'=>true, 'enctype'=>"multipart/form-data"]) !!}

                                                {!! Form::hidden('id', $val->uid, ['class'=>'form-control']) !!}

                                                <div class="form-group">
                                                    {!! Form::label('grading', 'Select what to deconfirm') !!}
                                                    {!! Form::select('grading', ['' =>'N/A', 'ca1'=>'CA1','ca2'=>'CA2', 'ca3'=>'CA3', 'ca4'=>'CA4', 'exam'=>'EXAM', 'all'=>'ALL'], '',['class'=>'form-control', 'required' ]) !!}
                                                </div>

                                                <div class="form-group">
                                                    {!! Form::label('confirm', 'I confirm that I want to reverse my earlier confimation so I can be able to effect changes to grades again', ['class'=>'text-danger']) !!}
                                                    {!! Form::checkbox('confirm','mvenda' , false ,  [ 'required' ]) !!}
                                                </div>

                                            {!! Form::submit('Confirm Grades',['class'=>'btn btn-success']) !!}

                                            {!! Form::close() !!}
                                        </div>
                                    </div>


                                    <div class="card mfp-hide mfp-popup-form mx-auto" id="submit-form{{$key+1}}">
                                        <div class="card-body">
                                            <h4 class="mt-0 mb-4 text-danger">Submit Grades to HOD</h4>
                                            {!! Form::open(['route' => ['lecturer.grading.submit', 'as'=>'ortesenKwagh']  , 'method' => 'POST', 'file'=>true, 'enctype'=>"multipart/form-data"]) !!}

                                                {!! Form::hidden('id', $val->uid, ['class'=>'form-control']) !!}

                                                <div class="form-group">
                                                    {!! Form::label('confirm', 'I confirm that I have completed grading, I do not have further changes and I want to submit these grades to the Head of Department', ['class'=>'text-danger']) !!}
                                                    {!! Form::checkbox('confirm','mna' , false ,  [ 'required' ]) !!}
                                                </div>

                                            {!! Form::submit('Confirm Grades',['class'=>'btn btn-success']) !!}

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
                                    <a class="popup-form btn btn-primary" href="#test-form">Search Another Session/Semester</a>
                                </div>

                                <div class="card mfp-hide mfp-popup-form mx-auto" id="test-form">
                                    <div class="card-body">
                                        <h4 class="mt-0 mb-4">Specify Details of Session and Semester</h4>
                                        {!! Form::open(['route' => ['lecturer.grading.previous','as'=>'ortesenKwagh'], 'method' => 'POST']) !!}
                                        {{-- {!! Form::hidden('uid',uniqid('dp_'), ['class'=>'form-control']) !!} --}}

                                        <div class="form-group">
                                            {!! Form::label('session_id', 'Select Session') !!}
                                            {!! Form::select('session_id', getSessionsDropdown(), null, ['class' => 'form-control' ]) !!}
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('semester_id', 'Select Semester') !!}
                                            {!! Form::select('semester_id',[''=>'N/A',1=>'First', 2=>'Second'] ,'',['class'=>'form-control', 'required' ]) !!}
                                        </div>



                                        {!! Form::submit('Search Courses') !!}

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
