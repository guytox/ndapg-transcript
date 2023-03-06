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



    <h1>List of Payment Categories</h1>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-4"></h4>

                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mb-4"></h4>
                            <div>
                                <a class="popup-form btn btn-primary" href="#new-fee-category">Create New Fee Category</a>
                            </div>

                            <div class="card mfp-hide mfp-popup-form mx-auto" id="new-fee-category">
                                <div class="card-body">
                                    <h4 class="mt-0 mb-4">Provide Fee Category Details</h4>
                                    {!! Form::open(['url' => 'bursary/fee-categories', 'method' => 'POST']) !!}

                                    <div class="form-group">
                                        {!! Form::label('category_name', 'Enter Fee Category Name') !!}
                                        {!! Form::text('category_name', '',['class'=>'form-control', 'required' ]) !!}
                                    </div>


                                    {!! Form::submit('Create New Fee Category') !!}

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
                                    <th scope="col">Category Name</th>
                                    <th scope="col">Descripton</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($feeCategories as $key => $item)


                                    <tr>
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                {{$key+1}}
                                            </div>
                                        </td>
                                        <td>{{$item->category_name}}</td>
                                        <td>{{$item->description}}</td>
                                        <td>


                                            {!! Form::open(['url' => 'bursary/fee-categories/'.$item->id , 'method' => 'DELETE']) !!}

                                            <a class="popup-form btn btn-primary" href="#edit-form{{$key+1}}">Edit Fee Category</a>

                                            {!! Form::submit('Delete', ['class'=>'btn btn-danger']) !!}

                                            {!! Form::close() !!}

                                        </td>
                                    </tr>

                                    <div class="card mfp-hide mfp-popup-form mx-auto" id="edit-form{{$key+1}}">
                                        <div class="card-body">
                                            <h4 class="mt-0 mb-4">Edit Fee Category Details</h4>
                                            {!! Form::open(['url' => 'bursary/fee-categories/'.$item->id , 'method' => 'PUT']) !!}

                                            {!! Form::hidden('id', $item->id, ['class'=>'form-control']) !!}

                                            <div class="form-group">

                                                {!! Form::label('category_name', 'Enter Fee Category Name') !!}
                                                {!! Form::text('category_name', $item->category_name,['class'=>'form-control', 'required' ]) !!}

                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('description', 'Enter Enter a New Description (*** Must be in slug format)') !!}
                                                {!! Form::text('description', $item->description, ['class'=>'form-control', 'required' ]) !!}
                                            </div>

                                            {!! Form::submit('Edit Fee Category',['class'=>'btn btn-success']) !!}

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
                                    <a class="popup-form btn btn-primary" href="#new-fee-category">Create New Fee Category</a>
                                </div>
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
