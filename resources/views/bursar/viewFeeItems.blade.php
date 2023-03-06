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



    <h1>List of Payment Items</h1>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-4"></h4>

                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mb-4"></h4>
                            <div>
                                <a class="popup-form btn btn-primary" href="#new-fee-item">Create New Fee Item</a>
                            </div>

                            <div class="card mfp-hide mfp-popup-form mx-auto" id="new-fee-item">
                                <div class="card-body">
                                    <h4 class="mt-0 mb-4">Provide User Details</h4>
                                    {!! Form::open(['url' => 'bursary/fee-items', 'method' => 'POST']) !!}

                                    <div class="form-group">
                                        {!! Form::label('name', 'Enter Fee Item Name') !!}
                                        {!! Form::text('name', '',['class'=>'form-control', 'required' ]) !!}
                                    </div>


                                    {!! Form::submit('Create New Fee Item') !!}

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
                                    <th scope="col">Item Name</th>
                                    <th scope="col">Descripton</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($feeItems as $key => $item)


                                    <tr>
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                {{$key+1}}
                                            </div>
                                        </td>
                                        <td>{{$item->name}}</td>
                                        <td>{{$item->description}}</td>
                                        <td>


                                            {!! Form::open(['url' => 'bursary/fee-items/'.$item->id , 'method' => 'DELETE']) !!}

                                            <a class="popup-form btn btn-primary" href="#edit-form{{$key+1}}">Edit Fee Item</a>

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
                                    <a class="popup-form btn btn-primary" href="#test-form">Create New Fee Item</a>
                                </div>

                                <div class="card mfp-hide mfp-popup-form mx-auto" id="test-form">
                                    <div class="card-body">
                                        <h4 class="mt-0 mb-4">Provide User Details</h4>
                                        {!! Form::open(['url' => 'bursary/fee-items', 'method' => 'POST']) !!}

                                        <div class="form-group">
                                            {!! Form::label('name', 'Enter Fee Item Name') !!}
                                            {!! Form::text('name', '',['class'=>'form-control', 'required' ]) !!}
                                        </div>


                                        {!! Form::submit('Create New Fee Item') !!}

                                        {!! Form::close() !!}
                                    </div>
                                </div>

                            </div>
                        </div>


                                <div class="card" >
                                    <div class="card-body">
                                        <h4 class="header-title mb-4"></h4>
                                    @foreach ($feeItems as $key => $val2)
                                        <div class="card mfp-hide mfp-popup-form mx-auto" id="edit-form{{$key+1}}">
                                            <div class="card-body">
                                                <h4 class="mt-0 mb-4">Edit User Details</h4>
                                                {!! Form::open(['url' => 'bursary/fee-items/'.$val2->id , 'method' => 'PUT']) !!}

                                                {!! Form::hidden('id', $val2->id, ['class'=>'form-control']) !!}

                                                <div class="form-group">
                                                    {!! Form::label('name', 'Enter New Item Name') !!}
                                                    {!! Form::text('name', $val2->name, ['class'=>'form-control', 'required' ]) !!}
                                                </div>

                                                <div class="form-group">
                                                    {!! Form::label('description', 'Enter Enter a New Description (*** Must be in slug format)') !!}
                                                    {!! Form::text('description', $val2->description, ['class'=>'form-control', 'required' ]) !!}
                                                </div>

                                                {!! Form::submit('Edit Fee Item',['class'=>'btn btn-success']) !!}

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

@endsection



@section('js')

 <!-- Magnific Popup-->
 <script src="{{asset('admin/assets/libs/magnific-popup/jquery.magnific-popup.min.js')}} "></script>

 <!-- Tour init js-->
 <script src="{{ asset('admin/assets/js/pages/lightbox.init.js') }} "></script>

 <script src="{{asset('admin/assets/js/app.js')}}"></script>

@endsection
