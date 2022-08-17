@extends('layouts.setup')
@section('css')
 <!-- DataTables -->
 <link href="{{asset('admin/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />
 <link href="{{asset('admin/assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />

 <!-- Responsive datatable examples -->
 <link href="{{asset('admin/assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />

 <!-- Lightbox css -->
 <link href="{{ asset('admin/assets/libs/magnific-popup/magnific-popup.css') }}" rel="stylesheet" type="text/css" />

type="text/css" />
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @include('includes.messages')
                <div class="row">
                    <div class="col-sm-12">

                        <h4 class="header-title">List of Configured Curricula</h4>
                        <p class="card-title-desc"> Note *** After adding a curriculum, add courses to it to activate it
                        </p>

                    </div>
                    <div>
                        @role('admin')
                        <a class="popup-form btn btn-primary" href="#new-fee-template">Add New Curriculum </a>

                        @endrole


                        <div class="card mfp-hide mfp-popup-form mx-auto" id="new-fee-template">
                            <div class="card-body">
                                <h4 class="mt-0 mb-4">Provide New Curriculum Details</h4>
                                {!! Form::open(['route' => 'curricula.store', 'method' => 'POST']) !!}

                                {!! Form::hidden('uid',uniqid('cur_'), ['class'=>'form-control']) !!}

                                <div class="form-group">
                                    {!! Form::label('programs_id', 'Programme Name') !!}
                                    {!! Form::select('programs_id', $programs, null, ['class' => 'form-control', 'required']) !!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('semester', 'Semester') !!}
                                    {!! Form::select('semester', $semesters, null, ['class' => 'form-control', 'required']) !!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('studyLevel', 'Study Level') !!}
                                    {!! Form::select('studyLevel', $studyLevels, null, ['class' => 'form-control', 'required']) !!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('title', 'Enter a Narration for the Template **(Must be Descriptive)') !!}
                                    {!! Form::text('title', '',['class'=>'form-control', 'required' ]) !!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('minRegCredits', 'Enter the minimum Credits **(Must be a number)') !!}
                                    {!! Form::text('minRegCredits', '',['class'=>'form-control', 'required' ]) !!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('maxRegCredits', 'Enter the maximum Credits **(Must be a number)') !!}
                                    {!! Form::text('maxRegCredits', '',['class'=>'form-control', 'required' ]) !!}
                                </div>


                                {!! Form::submit('Add New Fee Template Item', ['class'=>'form-control btn btn-success']) !!}

                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>

                </div>

                <hr>

                <div>

                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Description</th>
                                    <th>Level</th>
                                    <th>Semester</th>
                                    <th>Program</th>
                                    <th>Min. Credits</th>
                                    <th>Max. Credits</th>
                                    <th># Courses</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>


                            <tbody>



                                @foreach( $curricula as $key => $v )
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $v['title'] }}</td>
                                    <td>{{ getStudyLevelDetailsById($v['studyLevel']) }}</td>
                                    <td>{{ getSemesterDetailsById($v['semester']) }}</td>
                                    <td>{{ getProgrammeDetailById($v['programs_id'],'name') }}</td>
                                    <td>{{ $v['minRegCredits'] }}</td>
                                    <td>{{ $v['maxRegCredits'] }}</td>
                                    <td>{{ $v['numOfCourses'] }}</td>
                                    @if ($v['active']===1)
                                    <td>{{ $v['active'] }}</td>
                                    @elseif ($v['active']===0)
                                    <td>{{ $v['inactive'] }}</td>
                                    @endif


                                    <td>

                                            {!! Form::open(['route' => ['curricula.destroy', $v['id']] , 'method' => 'DELETE']) !!}
                                            @role('admin')
                                            <a class="popup-form btn btn-success" href="#edit-fee-template{{$key+1}}">Edit Template </a>
                                            @endrole
                                            <a href="{{ route('curricula.show', $v['id']) }}" class="btn btn-primary">View Details</a>

                                            {!! Form::submit('Delete', ['class'=>'btn btn-danger']) !!}

                                            {!! Form::close() !!}
                                    </td>


                                    <div class="card mfp-hide mfp-popup-form mx-auto" id="edit-fee-template{{$key+1}}">
                                        <div class="card-body">
                                            <h4 class="mt-0 mb-4">Edit Curricula</h4>
                                            {!! Form::open(['route' => ['curricula.update', $v->id]  , 'method' => 'PUT']) !!}

                                            {!! Form::hidden('id', $v->id, ['class'=>'form-control']) !!}

                                            {!! Form::hidden('uid',uniqid('cur_'), ['class'=>'form-control']) !!}

                                            <div class="form-group">
                                                {!! Form::label('programs_id', 'Program Details') !!}
                                                {!! Form::select('programs_id', $programs, $v['programs_id'], ['class' => 'form-control', 'required']) !!}
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('semester', 'Fee Template Item ') !!}
                                                {!! Form::select('semester', $semesters, $v['semester'], ['class' => 'form-control', 'required']) !!}
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('studyLevel', 'Study Level') !!}
                                                {!! Form::select('studyLevel', $studyLevels, $v['studyLevel'], ['class' => 'form-control', 'required']) !!}
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('title', 'Enter a title for the Curriculum **(Must be Descriptive)') !!}
                                                {!! Form::text('title', $v['title'], ['class'=>'form-control', 'required' ]) !!}
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('minRegCredits', 'Enter the minimum credits *(must be a digit)') !!}
                                                {!! Form::text('minRegCredits', $v['minRegCredits'],['class'=>'form-control', 'required' ]) !!}
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('maxRegCredits', 'Enter maximum cretits *(must be a digit)') !!}
                                                {!! Form::text('maxRegCredits', $v['maxRegCredits'],['class'=>'form-control', 'required' ]) !!}
                                            </div>


                                            {!! Form::submit('Edit Curricula',['class'=>'btn btn-success']) !!}

                                            {!! Form::close() !!}
                                        </div>
                                    </div>


                                </tr>


                                @endforeach

                            </tbody>
                        </table>

                        <div>
                            @role('admin')
                            <a class="popup-form btn btn-primary" href="#new-fee-template">Add New Curriculum </a>
                            @endrole
                        </div>

                </div>
            </div>
        </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
    @endsection
    @section('js')
    <!-- Required datatable js -->
    <script src="{{ asset('admin/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('admin/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

    <script>
        $(document).ready(function () {
      $('#datatable').DataTable({
        "scrollX": true,
        //"scrollY": 800,
      });
      $('.dataTables_length').addClass('bs-select');
    });
    </script>



    <!-- Datatable init js -->
    <script src="{{ asset('admin/assets/js/pages/datatables.init.js') }}"></script>

    <!-- Magnific Popup-->
    <script src="{{asset('admin/assets/libs/magnific-popup/jquery.magnific-popup.min.js')}} "></script>

    <!-- Tour init js-->
    <script src="{{ asset('admin/assets/js/pages/lightbox.init.js') }} "></script>

    <script src="{{asset('admin/assets/js/app.js')}}"></script>


    @endsection
