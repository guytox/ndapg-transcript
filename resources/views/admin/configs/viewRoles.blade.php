@extends('layouts.setup')

@section('content')

    <h1>List of Roles</h1>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-4"></h4>

                    @error('')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $error }}</strong>
                        </span>
                    @enderror

                    @include('includes.messages')

                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">S/N</th>
                                    <th scope="col">Role Name</th>
                                    <th scope="col">Role Description</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($staffRoles as $key => $faculty)


                                    <tr>
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                {{$key+1}}
                                            </div>
                                        </td>

                                        <td>

                                            <h5 class="font-size-15 mb-0">{{$faculty->name}}</h5>
                                        </td>
                                        <td></td>
                                        <td>


                                            {!! Form::open(['route' => ['rolemanagement.destroy', 'rolemanagement'=> $faculty->id] , 'method' => 'DELETE']) !!}

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
                                    <a class="popup-form btn btn-primary" href="#test-form">Create New Role</a>
                                </div>

                                <div class="card mfp-hide mfp-popup-form mx-auto" id="test-form">
                                    <div class="card-body">
                                        <h4 class="mt-0 mb-4">Specify Details of New Role Below</h4>
                                        {!! Form::open(['route' => 'rolemanagement.store', 'method' => 'POST']) !!}

                                        <div class="form-group">
                                            {!! Form::label('name', 'Enter Enter Role Name ') !!}
                                            {!! Form::text('name', '',['class'=>'form-control', 'required' ]) !!}
                                        </div>

                                        {!! Form::submit('Create New Role') !!}

                                        {!! Form::close() !!}
                                    </div>
                                </div>

                            </div>
                        </div>


                                <div class="card" >
                                    <div class="card-body">
                                        <h4 class="header-title mb-4"></h4>
                                    @foreach ($staffRoles as $key => $val2)
                                        <div class="card mfp-hide mfp-popup-form mx-auto" id="edit-form{{$key+1}}">
                                            <div class="card-body">
                                                <h4 class="mt-0 mb-4">Edit Role Details</h4>
                                                {!! Form::open(['route' =>['rolemanagement.update', 'rolemanagement'=>$val2->id] , 'method' => 'PUT']) !!}

                                                {!! Form::hidden('id', $val2->id, ['class'=>'form-control']) !!}


                                                <div class="form-group">
                                                    {!! Form::label('name', 'Enter Enter Role Name') !!}
                                                    {!! Form::text('name', $val2->name, ['class'=>'form-control', 'required' ]) !!}
                                                </div>


                                                {!! Form::submit('Edit Faculty Details',['class'=>'btn btn-success']) !!}

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

    <script>
        @if(session()->has('error'))
          alert('{{session()->get('error')}}')
        @endif
    </script>

@endsection
