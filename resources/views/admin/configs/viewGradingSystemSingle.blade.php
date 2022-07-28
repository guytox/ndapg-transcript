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



    <h1>Details of {{ $gradingSystemWithItems->name }}</h1>

    <div class="row">
        @include('includes.messages')
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-4"></h4>

                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mb-4"></h4>
                            <div>
                                <a class="popup-form btn btn-primary" href="#new-fee-template-item">Add New Grading System Item</a>

                                <a class="btn btn-warning" href="{{redirect(back())}}">Return Back</a>
                            </div>

                            <div class="card mfp-hide mfp-popup-form mx-auto" id="new-fee-template-item">
                                <div class="card-body">
                                    <h4 class="mt-0 mb-4">Provide New Curriculum Details</h4>
                                    {!! Form::open(['route' => ['add.grading.item'], 'method' => 'POST']) !!}

                                    <div class="form-group">
                                        {!! Form::hidden('grading_system_id', $gradingSystemWithItems->id, ['class'=>'form-control']) !!}
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('lower_boundary', 'Enter the Lower Boundary Mark') !!}
                                        {!! Form::number('lower_boundary', '', ['class'=>'form-control', 'required']) !!}
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('upper_boundary', 'Enter the Upper Boundary Mark') !!}
                                        {!! Form::number('upper_boundary', '', ['class'=>'form-control', 'required']) !!}
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('grade_letter', 'Enter the Grade Letter') !!}
                                        {!! Form::text('grade_letter', '', ['class'=>'form-control', 'requred']) !!}
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('weight_points', 'Enter the Graded Weight Points') !!}
                                        {!! Form::number('weight_points', '', ['class'=>'form-control', 'required']) !!}
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('credit_earned', 'Select if Credit is Earned (Yes/No)') !!}
                                        {!! Form::select('credit_earned', array(0 => 'No', 1 => 'Yes'), null, ['class' => 'form-control', 'required']) !!}
                                    </div>


                                    {!! Form::submit('Add New Grading Item') !!}

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
                                    <th scope="col">Lower Boundary</th>
                                    <th scope="col">Upper Boundary</th>
                                    <th scope="col">Grade Letter</th>
                                    <th scope="col">Graded Weight</th>
                                    <th scope="col">Earned</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>



                                @if ($gradingSystemWithItems->gradingSystemItems)



                                @foreach ($gradingSystemWithItems->gradingSystemItems as $key => $item)


                                    <tr>
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                {{$key+1}}
                                            </div>
                                        </td>
                                        <td>{{ $item->lower_boundary }}</td>
                                        <td>{{ $item->upper_boundary }}</td>
                                        <td>{{ $item->grade_letter }}</td>
                                        <td>{{ $item->weight_points }}</td>
                                        @if ($item->credit_earned == 1)
                                            <td>YES</td>
                                        @elseif ($item->credit_earned == 0)
                                            <td>NO</td>
                                        @else
                                            <td>Not Found</td>
                                        @endif

                                        <td>


                                            {!! Form::open(['route' => ['delete.grading.item', 'id'=>$item->id] , 'method' => 'POST']) !!}

                                            <a class="popup-form btn btn-primary" href="#edit-form{{$key+1}}">Edit Grading Item</a>

                                            {!! Form::submit('Delete Item', ['class'=>'btn btn-danger']) !!}

                                            {!! Form::close() !!}

                                        </td>
                                    </tr>




                                        <div class="card mfp-hide mfp-popup-form mx-auto" id="edit-form{{$key+1}}">
                                            <div class="card-body">
                                                <h4 class="mt-0 mb-4">Edit Grading Item</h4>
                                                {!! Form::open(['route' => ['edit.grading.item'] , 'method' => 'POST']) !!}


                                                    {!! Form::hidden('grading_system_id', $gradingSystemWithItems->id, ['class'=>'form-control']) !!}

                                                    {!! Form::hidden('id', $item->id, ['class'=>'form-control']) !!}


                                                    <div class="form-group">
                                                        {!! Form::label('lower_boundary', 'Enter the Lower Boundary Mark') !!}
                                                        {!! Form::number('lower_boundary', $item->lower_boundary, ['class'=>'form-control', 'required']) !!}
                                                    </div>

                                                    <div class="form-group">
                                                        {!! Form::label('upper_boundary', 'Enter the Upper Boundary Mark') !!}
                                                        {!! Form::number('upper_boundary', $item->upper_boundary, ['class'=>'form-control', 'required']) !!}
                                                    </div>

                                                    <div class="form-group">
                                                        {!! Form::label('grade_letter', 'Enter the Grade Letter') !!}
                                                        {!! Form::text('grade_letter', $item->grade_letter, ['class'=>'form-control', 'requred']) !!}
                                                    </div>

                                                    <div class="form-group">
                                                        {!! Form::label('weight_points', 'Enter the Graded Weight Points') !!}
                                                        {!! Form::number('weight_points', $item->weight_points, ['class'=>'form-control', 'required']) !!}
                                                    </div>

                                                    <div class="form-group">
                                                        {!! Form::label('credit_earned', 'Select if Credit is Earned (Yes/No)') !!}
                                                        {!! Form::select('credit_earned', array(0 => 'No', 1 => 'Yes'), $item->credit_earned, ['class' => 'form-control', 'required']) !!}
                                                    </div>

                                                {!! Form::submit('Edit Grading Item',['class'=>'btn btn-success']) !!}

                                                {!! Form::close() !!}
                                            </div>
                                        </div>
                                @endforeach

                                @endif

                            </tbody>
                        </table>





                        <div class="card">
                            <div class="card-body">
                                <h4 class="header-title mb-4"></h4>
                                <div>
                                    <a class="popup-form btn btn-primary" href="#new-fee-template-item">Add New Grading Item</a>

                                    <a class="btn btn-warning" href="{{redirect(back())}}">Return Back</a>
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
