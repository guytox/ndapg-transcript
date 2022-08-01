@extends('layouts.setup')

@section('content')

    <h1>List of Faculties</h1>

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

                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">S/N</th>
                                    <th scope="col">Faculty Name</th>
                                    <th scope="col">Faculty Description</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($faculties as $key => $faculty)


                                    <tr>
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                {{$key+1}}
                                            </div>
                                        </td>

                                        <td>

                                            <h5 class="font-size-15 mb-0">{{$faculty->name}}</h5>
                                        </td>
                                        <td>{{$faculty->description}}</td>
                                        <td>


                                            {!! Form::open(['route' => ['faculties.destroy', 'faculty'=> $faculty->id] , 'method' => 'DELETE']) !!}

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
                                    <a class="popup-form btn btn-primary" href="#test-form">Create New Faculty</a>
                                </div>

                                <div class="card mfp-hide mfp-popup-form mx-auto" id="test-form">
                                    <div class="card-body">
                                        <h4 class="mt-0 mb-4">Specify Details of New Faculty Below</h4>
                                        {!! Form::open(['route' => 'faculties.store', 'method' => 'POST']) !!}

                                        {!! Form::hidden('uid',uniqid('fc_'), ['class'=>'form-control']) !!}

                                        <div class="form-group">
                                            {!! Form::label('name', 'Enter Enter Faculty Name (Don\'t use the Prefix "Faculty of")') !!}
                                            {!! Form::text('name', '',['class'=>'form-control', 'required' ]) !!}
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('description', 'Enter a Faculty Description') !!}
                                            {!! Form::text('description', '',['class'=>'form-control', 'required' ]) !!}
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('academic', 'Is it Academic or Non-Academic ') !!}
                                            {!! Form::select('academic',[1=>'Academic', 2=>'Non-Academic'] ,'1',['class'=>'form-control', 'required' ]) !!}
                                        </div>

                                        {!! Form::submit('Create New Faculty') !!}

                                        {!! Form::close() !!}
                                    </div>
                                </div>

                            </div>
                        </div>


                                <div class="card" >
                                    <div class="card-body">
                                        <h4 class="header-title mb-4"></h4>
                                    @foreach ($faculties as $key => $val2)
                                        <div class="card mfp-hide mfp-popup-form mx-auto" id="edit-form{{$key+1}}">
                                            <div class="card-body">
                                                <h4 class="mt-0 mb-4">Edit Faculty Details</h4>
                                                {!! Form::open(['route' =>['faculties.update', 'faculty'=>$val2->id] , 'method' => 'PUT']) !!}

                                                {!! Form::hidden('id', $val2->id, ['class'=>'form-control']) !!}
                                                {!! Form::hidden('uid', uniqid('fc_'), ['class'=>'form-control']) !!}

                                                <div class="form-group">
                                                    {!! Form::label('name', 'Enter Enter Faculty Name (Don\'t use the Prefix "Faculty of")') !!}
                                                    {!! Form::text('name', $val2->name, ['class'=>'form-control', 'required' ]) !!}
                                                </div>

                                                <div class="form-group">
                                                    {!! Form::label('description', 'Enter a Faculty Description') !!}
                                                    {!! Form::text('description', $val2->description,['class'=>'form-control', 'required'  ]) !!}
                                                </div>

                                                <div class="form-group">
                                                    {!! Form::label('academic', 'Enter a Faculty Description') !!}
                                                    {!! Form::select('academic',[1=>'Academic', 2=>'Non-Academic'] ,$val2->academic,['class'=>'form-control', 'required' ]) !!}
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
