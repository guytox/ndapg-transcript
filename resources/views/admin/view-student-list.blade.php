@extends('layouts.setup')
@section('css')
<!-- DataTables -->
<link href="{{ asset('admin/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
{{-- <link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" --}}
type="text/css" />
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @include('includes.messages')
                <div class="row">
                    <div class="col-sm-6">

                        <h4 class="header-title">List of UMM Students</h4>
                        <p class="card-title-desc"> The List below shows the student list on the Portal

                        </p>

                    </div>

                    <div class="col-sn-6">
                    <a href="#upload-form" class="popup-form btn btn-primary">Upload Student List &nbsp; <i class="mdi mdi-file-document-edit-outline"></i></a>
                    </div>

                    <div class="card mfp-hide mfp-popup-form mx-auto" id="upload-form">
                        <div class="card-body">
                            <h4 class="mt-0 mb-4">Specify Details to Upload Student List</h4>
                            {!! Form::open(['route' => 'student.list.upload', 'method' => 'POST', 'file'=>true, 'enctype'=>"multipart/form-data"]) !!}

                            <div class="form-group">
                                {!! Form::label('program_id', 'Select Programme') !!}
                                {!! Form::select('program_id', $programlist,'',['class'=>'form-control', 'required' ]) !!}
                            </div>

                            <div class="form-group">
                                {!! Form::label('file', 'Select .xlsx File to upload (format: name// matricno // email// gsm // programme // department // faculty)') !!}
                                {!! Form::file('file', ['class'=> 'form-control']) !!}
                            </div>

                            {!! Form::submit('Upload Student List') !!}

                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>

                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;" >
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Matric.</th>
                                    <th>Name</th>
                                    <th>email</th>
                                    <th>GSM</th>
                                    <th>Program.</th>
                                    <th>Level</th>
                                    <th>Action</th>
                                </tr>
                            </thead>


                            <tbody>



                                @foreach( $studentList as $key => $v )
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $v['username'] }}</td>
                                    <td>{{ $v['name'] }}</td>
                                    <td>{{ $v['email'] }}</td>
                                    <td>{{ $v['phone_number'] }}</td>
                                    <td>{{getProgrammeDetailById(getStudentByUserId($v['id'])->program_id,'name')}}</td>
                                    <td>{{ getStudyLevelDetailsById($v['current_level']) }}Level</td>


                                    <td>
                                        @if($v['StudentMatricNo'] == null)



                                        @else

                                        <a href="{{ route('view.students.ledger', $v['id']) }}" class="btn btn-primary">View Ledger</a>
                                    </td>

                                    @endif
                                </tr>


                                @endforeach

                            </tbody>
                        </table>

                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
    @endsection
    @section('js')
    <!-- Required datatable js -->
    <script src="{{ asset('admin/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('admin/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>


    <!-- Datatable init js -->
    <script src="{{ asset('admin/assets/js/pages/datatables.init.js') }}"></script>


    @endsection
