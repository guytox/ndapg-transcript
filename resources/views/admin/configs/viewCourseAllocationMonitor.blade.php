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

                        <h4 class="header-title">Your List of Course Allocations</h4>
                        <p class="card-title-desc"> You can create an allocation for this semester by clicking on "Initiate New Allocation"
                        </p>

                    </div>
                    <div>
                        <a class="popup-form btn btn-primary" href="#new-fee-template">Initiate New Allocation </a>


                        <div class="card mfp-hide mfp-popup-form mx-auto" id="new-fee-template">
                            <div class="card-body">
                                <h4 class="mt-0 mb-4">Provide New Curriculum Details</h4>

                                {!! Form::open(['route' => 'course-allocation.store', 'method' => 'POST']) !!}

                                {!! Form::hidden('uid',uniqid('cam_'), ['class'=>'form-control']) !!}

                                <div class="form-group">
                                    {!! Form::label('department_id', 'Select Department') !!}
                                    {!! Form::select('department_id', $deptsDropdown, null, ['class' => 'form-control', 'required']) !!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('session_id', 'Select Academic Session') !!}
                                    {!! Form::select('session_id', $sessions, null, ['class' => 'form-control', 'required']) !!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('semester', 'Select Semester') !!}
                                    {!! Form::select('semester', $semesters, null, ['class' => 'form-control', 'required']) !!}
                                </div>

                                {!! Form::submit('Proceed to Allocation', ['class'=>'form-control btn btn-success']) !!}

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
                                    <th>Department</th>
                                    <th>Session</th>
                                    <th>Semester</th>
                                    <th>HOD</th>
                                    <th>View</th>
                                    <th>Action</th>
                                </tr>
                            </thead>


                            <tbody>


                            @if ($previousAllocations)


                                @foreach( $previousAllocations as $key => $v )
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ getDepartmentDetailById($v['department_id'],'name') }}</td>
                                    <td>{{ getsessionById($v['session_id'])->name }}</td>
                                    <td>{{ getSemesterDetailsById($v['semester_id']) }}</td>
                                    <td>{{ getUser($v['created_by'],'name') }}</td>
                                    <td>
                                        <a href="{{ route('course-allocation.show', $v['uid']) }}" class="btn btn-primary">View Details</a>
                                    </td>
                                    @if ($v['session_id'] < activeSession()->id)
                                    <td></td>
                                    @else
                                    <td>

                                        {!! Form::open(['route' => ['course-allocation.destroy', $v['uid']] , 'method' => 'DELETE']) !!}

                                        <a class="popup-form btn btn-success" href="#edit-fee-template{{$key+1}}">Change Semester </a>

                                        {!! Form::submit('Delete', ['class'=>'btn btn-danger']) !!}

                                        {!! Form::close() !!}</td>

                                    @endif





                                    <div class="card mfp-hide mfp-popup-form mx-auto" id="edit-fee-template{{$key+1}}">
                                        <div class="card-body">
                                            <h4 class="mt-0 mb-4">Edit Curricula</h4>
                                            {!! Form::open(['route' => ['course-allocation.update', $v->uid]  , 'method' => 'PUT']) !!}

                                            {!! Form::hidden('id', $v->uid, ['class'=>'form-control']) !!}

                                            

                                            <div class="form-group">
                                                {!! Form::label('department_id', 'Select Department') !!}
                                                {!! Form::select('department_id', $deptsDropdown, $v['department_id'], ['class' => 'form-control', 'required', 'disabled']) !!}
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('session_id', 'Select Academic Session') !!}
                                                {!! Form::select('session_id', $sessions, $v['session_id'], ['class' => 'form-control', 'required','disabled']) !!}
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('semester', 'Select Semester') !!}
                                                {!! Form::select('semester', $semesters, $v['semester_id'], ['class' => 'form-control', 'required']) !!}
                                            </div>


                                            {!! Form::submit('Edit Course Allocation',['class'=>'btn btn-success']) !!}

                                            {!! Form::close() !!}
                                        </div>
                                    </div>


                                </tr>


                                @endforeach

                            @endif

                            </tbody>
                        </table>

                        <div>
                            <a class="popup-form btn btn-primary" href="#new-fee-template">Initiate New Allocation</a>
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
