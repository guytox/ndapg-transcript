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

                        <h4 class="header-title">List of NDA Staff</h4>
                        <p class="card-title-desc"> The List below shows the staff list on the Portal
                        </p>

                    </div>
                    <div>
                        <a class="popup-form btn btn-primary" href="#new-fee-template">Add New Staff</a>

                        <a class="popup-form btn btn-success" href="#upload-fee-template">Upload Lecturer List</a>


                        <div class="card mfp-hide mfp-popup-form mx-auto" id="new-fee-template">
                            <div class="card-body">
                                <h4 class="mt-0 mb-4">Provide New Staff Details</h4>

                                {!! Form::open(['route' => 'stafflist.store', 'method' => 'POST']) !!}


                                <div class="form-group">
                                    {!! Form::label('department_id', 'Select Department') !!}
                                    {!! Form::select('department_id', $deptsDropdown, null, ['class' => 'form-control', 'required']) !!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('name', 'Enter Staff Name') !!}
                                    {!! Form::text('name', null, ['class' => 'form-control', 'placeholder'=>'for example Torkuma Agber', 'required']) !!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('usrname', 'Enter Username') !!}
                                    {!! Form::text('usrname', null, ['class' => 'form-control', 'placeholder'=>'for example PS007', 'required']) !!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('email', 'Enter User Email') !!}
                                    {!! Form::email('email', null, ['class' => 'form-control', 'placeholder'=>'enter email here', 'required']) !!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('gsm_number', 'Enter GSM Number of User') !!}
                                    {!! Form::text('gsm_number', null, ['class' => 'form-control', 'placeholder'=>'for example 08012345678', 'required']) !!}
                                </div>


                                <div class="form-group">
                                    {!! Form::label('staff_roles[]', 'Select Roles ***(Hold down ctrl key to select multiple)') !!}
                                    {!! Form::select('staff_roles[]', $staffRoles, null, ['class' => 'form-control', 'multiple','required']) !!}
                                </div>

                                {!! Form::submit('Proceed to Allocation', ['class'=>'form-control btn btn-success']) !!}

                                {!! Form::close() !!}
                            </div>
                        </div>

                        <div class="card mfp-hide mfp-popup-form mx-auto" id="upload-fee-template">
                            <div class="card-body">
                                <h4 class="mt-0 mb-4">Lecturer List Bulk Upload</h4>

                                {!! Form::open(['route' => 'stafflist.upload', 'method' => 'POST', 'file'=>true, 'enctype'=>"multipart/form-data"]) !!}

                                <div>
                                    {!! Form::label('file', "select file to upoload, must be of type .xlsx FORMAT MUST CONTAIN: staffId// name// department") !!}
                                    {!! Form::file('file', ['class'=> 'form-control']) !!}
                                </div>

                                <br>


                                {!! Form::submit('Upload List', ['class'=>'form-control btn btn-success']) !!}

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
                                    <th>#</th>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>email</th>
                                    <th>GSM</th>
                                    <th>Dept</th>
                                    <th>Roles</th>
                                    <th>Action</th>
                                </tr>
                            </thead>


                            <tbody>


                            @if ($staffList)


                                @foreach( $staffList as $key => $v )
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $v['username'] }}</td>
                                        <td>{{ $v['name'] }}</td>
                                        <td>{{ $v['email'] }}</td>
                                        <td>{{ $v['phone_number'] }}</td>
                                        <td>
                                            @isset(getStaffProfileById($v->id)->id)
                                                {{getDepartmentDetailById(getStaffProfileById($v->id)->department_id, 'name')}}
                                            @endisset

                                        </td>
                                        <td>
                                            @foreach ($v->roles as $role)
                                            {{$role->name}},
                                            @endforeach
                                        </td>
                                        <td>
                                            {!! Form::open(['route' => ['stafflist.destroy', $v['id']] , 'method' => 'DELETE']) !!}

                                            <a class="popup-form btn btn-dark" href="#edit-fee-template{{$key+1}}">Edit Roles</a>
                                            <a class="popup-form btn btn-success" href="#edit-fee-template{{$key+1}}">Edit Profile</a>

                                            {!! Form::submit('Delete', ['class'=>'btn btn-danger']) !!}

                                            {!! Form::close() !!}

                                        </td>


                                        <div class="card mfp-hide mfp-popup-form mx-auto" id="edit-fee-template{{$key+1}}">
                                            <div class="card-body">
                                                <h4 class="mt-0 mb-4">Edit User Details</h4>
                                                {!! Form::open(['route' => ['stafflist.update', 'stafflist'=>$v->id]  , 'method' => 'PUT']) !!}

                                                {!! Form::hidden('userId', $v->id, ['class'=>'form-control']) !!}
                                                {!! Form::hidden('psswrd', '', ['class'=>'form-control']) !!}

                                                <div class="form-group">
                                                    {!! Form::label('department_id', 'Select Department') !!}
                                                    {!! Form::select('department_id', $deptsDropdown, $v->id, ['class' => 'form-control', 'required']) !!}
                                                </div>

                                                <div class="form-group">
                                                    {!! Form::label('name', 'Enter Staff Name') !!}
                                                    {!! Form::text('name', $v->name, ['class' => 'form-control', 'placeholder'=>'for example Torkuma Agber', 'required']) !!}
                                                </div>

                                                <div class="form-group">
                                                    {!! Form::label('usrname', 'Enter Username') !!}
                                                    {!! Form::text('usrname', $v->username, ['class' => 'form-control', 'placeholder'=>'for example PS007', 'required']) !!}
                                                </div>

                                                <div class="form-group">
                                                    {!! Form::label('email', 'Enter User Email') !!}
                                                    {!! Form::email('email', $v->email, ['class' => 'form-control', 'placeholder'=>'enter email here', 'required']) !!}
                                                </div>

                                                <div class="form-group">
                                                    {!! Form::label('gsm_number', 'Enter GSM Number of User') !!}
                                                    {!! Form::text('gsm_number', $v->phone_number, ['class' => 'form-control', 'placeholder'=>'for example 08012345678', 'required']) !!}
                                                </div>


                                                <div class="form-group">
                                                    {!! Form::label('staff_roles[]', 'Select Roles ***(1. Hold down ctrl key to select multiple. 2. Roles you don\'t select will be removed from the staff)') !!}
                                                    {!! Form::select('staff_roles[]', $staffRoles, null, ['class' => 'form-control', 'multiple','required']) !!}
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
                            <a class="popup-form btn btn-primary" href="#new-fee-template">Add New Staff</a>
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
