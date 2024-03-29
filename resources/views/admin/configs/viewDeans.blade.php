@extends('layouts.setup')

@section('content')

    <h1>List of Faculties</h1>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-4"></h4>

                    @include('includes.messages')

                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">S/N</th>
                                    <th scope="col">Faculty Name</th>
                                    <th scope="col">Dean</th>
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
                                        @if ($faculty->dean_id !='')
                                            <td>{{getUser($faculty->dean_id,'name')}}</td>
                                        @else
                                            <td>{{$faculty->dean_id}}</td>
                                        @endif

                                        <td>


                                            {!! Form::open(['route' => ['appointments.revoke.deans', 'faultyid'=> $faculty->id] , 'method' => 'POST']) !!}

                                            <a class="popup-form btn btn-primary" href="#edit-form{{$key+1}}">Appoint New Dean</a>

                                            {!! Form::submit('Revoke Appointment', ['class'=>'btn btn-danger']) !!}

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
                                                {!! Form::open(['route' =>['appointments.assign.deans', 'faultyid'=>$val2->id] , 'method' => 'POST']) !!}

                                                {!! Form::hidden('id', $val2->id, ['class'=>'form-control']) !!}
                                                {!! Form::hidden('uid', uniqid('fc_'), ['class'=>'form-control']) !!}



                                                <div class="form-group">
                                                    {!! Form::label('dean_id', 'Select New Staff to Appoint as Dean') !!}
                                                    {!! Form::select('dean_id',  $staffList, $val2->dean_id,  ['class' => 'form-control' ]) !!}
                                                </div>



                                                {!! Form::submit('Assign as Dean',['class'=>'btn btn-success']) !!}

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
