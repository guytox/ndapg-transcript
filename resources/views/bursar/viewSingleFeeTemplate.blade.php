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



    <h1>Break down of {{ $templateDetails->narration }}</h1>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-4"></h4>

                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mb-4"></h4>
                            <div>
                                <a class="popup-form btn btn-primary" href="#new-fee-template-item">Add New Fee Template Item</a>

                                <a class="btn btn-warning" href="{{route('fee-templates.index')}}">Return Back</a>
                            </div>

                            <div class="card mfp-hide mfp-popup-form mx-auto" id="new-fee-template-item">
                                <div class="card-body">
                                    <h4 class="mt-0 mb-4">Provide New Template Item Details</h4>
                                    {!! Form::open(['url' => 'bursary/add-template-item', 'method' => 'POST']) !!}

                                    <div class="form-group">
                                        {!! Form::hidden('fee_template_id', $templateDetails->id, ['class'=>'form-control']) !!}
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('fee_item_id', 'Fee Template Item ') !!}
                                        {!! Form::select('fee_item_id', $feeTemplateItems, null, ['class' => 'form-control', 'required']) !!}
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('item_amount', 'Enter Fee Item Amount') !!}
                                        {!! Form::text('item_amount', '',['class'=>'form-control', 'required' ]) !!}
                                    </div>


                                    {!! Form::submit('Add New Fee Template Item') !!}

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
                                    <th scope="col">Item</th>
                                    <th scope="col">Total Amount</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>

                                @php
                                    $totalAmount =0;
                                @endphp

                                @foreach ($templateDetails->feeTemplateItems as $key => $item)


                                    <tr>
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                {{$key+1}}
                                            </div>
                                        </td>
                                        <td>{{ getFeeItemName($item->fee_item_id) }}</td>
                                        <td >{{number_format(convertToNaira($item->item_amount),2)}}</td>
                                        <td>


                                            {!! Form::open(['route' => ['delete.template.item', $item->id] , 'method' => 'POST']) !!}

                                            <a class="popup-form btn btn-primary" href="#edit-form{{$key+1}}">Edit Fee Item</a>

                                            {!! Form::submit('Delete', ['class'=>'btn btn-danger']) !!}

                                            {!! Form::close() !!}

                                        </td>
                                    </tr>

                                    @php
                                        $totalAmount += convertToNaira($item->item_amount);
                                    @endphp
                                @endforeach

                                    <tr>
                                        <td></td>
                                        <td>Total Amount</td>
                                        <td>{{ number_format($totalAmount,2) }}</td>
                                        <td></td>

                                    </tr>
                            </tbody>
                        </table>





                        <div class="card">
                            <div class="card-body">
                                <h4 class="header-title mb-4"></h4>
                                <div>
                                    <a class="popup-form btn btn-primary" href="#new-fee-template-item">Add New Fee Template Item</a>

                                    <a class="btn btn-warning" href="{{route('fee-templates.index')}}">Return Back</a>
                                </div>



                            </div>
                        </div>


                                <div class="card" >
                                    <div class="card-body">
                                        <h4 class="header-title mb-4"></h4>
                                    @foreach ($templateDetails->feeTemplateItems as $key => $val2)
                                        <div class="card mfp-hide mfp-popup-form mx-auto" id="edit-form{{$key+1}}">
                                            <div class="card-body">
                                                <h4 class="mt-0 mb-4">Edit Fee Template Item</h4>
                                                {!! Form::open(['url' => 'bursary/edit-template-item/'.$val2->id , 'method' => 'POST']) !!}

                                                {!! Form::hidden('id', $val2->id, ['class'=>'form-control']) !!}
                                                {!! Form::hidden('fee_template_id', $val2->fee_template_id, ['class'=>'form-control']) !!}
                                                {!! Form::hidden('fee_item_id', $val2->fee_item_id, ['class'=>'form-control']) !!}

                                                <div class="form-group">
                                                    {!! Form::label('name', 'Fee Item Name') !!}
                                                    {!! Form::text('name', getFeeItemName($val2->fee_item_id), ['class'=>'form-control', 'disabled' ]) !!}
                                                </div>

                                                <div class="form-group">
                                                    {!! Form::label('item_amount', 'Enter Enter a New Amount') !!}
                                                    {!! Form::text('item_amount', convertToNaira($val2->item_amount), ['class'=>'form-control', 'required' ]) !!}
                                                </div>

                                                {!! Form::submit('Edit Fee Type',['class'=>'btn btn-success']) !!}

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
