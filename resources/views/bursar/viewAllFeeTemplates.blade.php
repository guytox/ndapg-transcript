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

                        <h4 class="header-title">List of Configured Fee templates</h4>
                        <p class="card-title-desc"> Note *** After configuring a new fee template, ensure you add it to a fee configuration for it  to be active

                        </p>

                    </div>
                    <div>
                        <a class="popup-form btn btn-primary" href="#new-fee-template">Add New Fee Template </a>


                        <div class="card mfp-hide mfp-popup-form mx-auto" id="new-fee-template">
                            <div class="card-body">
                                <h4 class="mt-0 mb-4">Provide New Template Item Details</h4>
                                {!! Form::open(['url' => 'bursary/fee-templates', 'method' => 'POST']) !!}

                                <div class="form-group">
                                    {!! Form::label('fee_type_id', 'Fee Template Item ') !!}
                                    {!! Form::select('fee_type_id', $feeTypes, null, ['class' => 'form-control', 'required']) !!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('narration', 'Enter a Narration for the Template **(Must be Descriptive)') !!}
                                    {!! Form::text('narration', '',['class'=>'form-control', 'required' ]) !!}
                                </div>


                                {!! Form::submit('Add New Fee Template Item', ['class'=>'form-control btn btn-success']) !!}

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
                                    <th>Description</th>
                                    <th>Fee Type</th>
                                    <th>Total Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>


                            <tbody>



                                @foreach( $feeTemplates as $key => $v )
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $v['narration'] }}</td>
                                    <td>{{ getFeeTypeName($v['fee_type_id']) }}</td>
                                    <td>{{ number_format(convertToNaira($v['total_amount']),2) }}</td>

                                    <td>




                                            {!! Form::open(['route' => ['fee-templates.destroy', $v['id']] , 'method' => 'DELETE']) !!}

                                            <a class="popup-form btn btn-success" href="#edit-fee-template{{$key+1}}">Edit Template </a>
                                            <a href="{{ route('fee-templates.show', $v['id']) }}" class="btn btn-primary">View Details</a>

                                            {!! Form::submit('Delete', ['class'=>'btn btn-danger']) !!}

                                            {!! Form::close() !!}
                                    </td>


                                    <div class="card mfp-hide mfp-popup-form mx-auto" id="edit-fee-template{{$key+1}}">
                                        <div class="card-body">
                                            <h4 class="mt-0 mb-4">Edit Fee Template</h4>
                                            {!! Form::open(['route' => ['fee-templates.update', $v->id]  , 'method' => 'PUT']) !!}

                                            {!! Form::hidden('id', $v->id, ['class'=>'form-control']) !!}
                                            {!! Form::hidden('fee_type_id', $v->fee_type_id, ['class'=>'form-control']) !!}

                                            <div class="form-group">
                                                {!! Form::label('naration', 'Fee Item Name') !!}
                                                {!! Form::text('name', $v->narration, ['class'=>'form-control', 'required' ]) !!}
                                            </div>


                                            {!! Form::submit('Submit',['class'=>'btn btn-success']) !!}

                                            {!! Form::close() !!}
                                        </div>
                                    </div>


                                </tr>


                                @endforeach

                            </tbody>
                        </table>

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
