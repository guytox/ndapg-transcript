@extends('layouts.setup')

@section('content')

    <h1>List of Scholarship Types</h1>

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
                                    <th scope="col">Scholarship Type</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Active</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($scholarships as $key => $v)


                                    <tr>
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                {{$key+1}}
                                            </div>
                                        </td>

                                        <td>

                                            <h5 class="font-size-15 mb-0">{{$v->type}}</h5>
                                        </td>
                                        <td>{{$v->description}}</td>
                                        @if ($v->active ==='1')
                                        <td>YES</td>

                                        @elseif ($v->active ==='0')

                                            <td>NO</td>
                                        @endif

                                        <td>


                                            {!! Form::open(['route' => ['scholarsips.destroy', 'scholarsip'=> $v->id] , 'method' => 'DELETE']) !!}

                                            <a class="popup-form btn btn-primary" href="#edit-form{{$key+1}}">Edit Details</a>

                                            {!! Form::submit('Delete', ['class'=>'btn btn-danger']) !!}

                                            {!! Form::close() !!}

                                        </td>
                                    </tr>


                                    <div class="card mfp-hide mfp-popup-form mx-auto" id="edit-form{{$key+1}}">
                                        <div class="card-body">
                                            <h4 class="mt-0 mb-4">Edit Faculty Details</h4>
                                            {!! Form::open(['route' =>['scholarsips.update', 'scholarsip'=>$v->id] , 'method' => 'PUT']) !!}

                                            {!! Form::hidden('id', $v->id, ['class'=>'form-control']) !!}
                                            {!! Form::hidden('uid', uniqid('sch_'), ['class'=>'form-control']) !!}

                                            <div class="form-group">
                                                {!! Form::label('type', 'Enter Enter Scholarship Type') !!}
                                                {!! Form::text('type', $v->type, ['class'=>'form-control', 'required' ]) !!}
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('description', 'Enter a Faculty Description') !!}
                                                {!! Form::text('description', $v->description,['class'=>'form-control', 'required'  ]) !!}
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('active', 'Enter a Faculty Description') !!}
                                                {!! Form::select('active',[0=>'In-Active', 1=>'Active'] ,$v->active,['class'=>'form-control', 'required' ]) !!}
                                            </div>

                                            {!! Form::submit('Edit Scholarship Type Details',['class'=>'btn btn-success']) !!}

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
                                    <a class="popup-form btn btn-primary" href="#test-form">Create New Scholarship Type</a>
                                </div>

                                <div class="card mfp-hide mfp-popup-form mx-auto" id="test-form">
                                    <div class="card-body">
                                        <h4 class="mt-0 mb-4">Specify Details of New Scholarhip Type Below</h4>
                                        {!! Form::open(['route' => 'scholarsips.store', 'method' => 'POST']) !!}

                                        {!! Form::hidden('uid',uniqid('sch_'), ['class'=>'form-control']) !!}

                                        <div class="form-group">
                                            {!! Form::label('type', 'Enter Enter Scholarhip type') !!}
                                            {!! Form::text('type', '',['class'=>'form-control', 'required' ]) !!}
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('description', 'Enter a Faculty Description') !!}
                                            {!! Form::text('description', '',['class'=>'form-control', 'required' ]) !!}
                                        </div>



                                        {!! Form::submit('Create New Scholarship Type') !!}

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
