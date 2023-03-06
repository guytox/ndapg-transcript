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

                        <h4 class="header-title">List of Fee Congigurations</h4>
                        <p class="card-title-desc"> Note *** All Payable Fees are to be configured here, contact ICT if in doubt of configuration process.

                        </p>

                    </div>
                    <div>
                        <a class="popup-form btn btn-primary" href="#new-fee-config">Add New Fee Configuration </a>


                        <div class="card mfp-hide mfp-popup-form mx-auto" id="new-fee-config">
                            <div class="card-body">
                                <h4 class="mt-0 mb-4">Provide New Fee Configuration Form</h4>
                                {!! Form::open(['url' => 'bursary/fee-configs', 'method' => 'POST']) !!}

                                <div class="form-group">
                                    {!! Form::label('fee_template_id', 'Select Fee Template') !!}
                                    {!! Form::select('fee_template_id', $feeTemplates, null, ['class' => 'form-control', 'required']) !!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('fee_category_id', 'Select Fee Category') !!}
                                    {!! Form::select('fee_category_id', $categories, null, ['class' => 'form-control', 'required']) !!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('account', 'Select Fee Account') !!}
                                    {!! Form::select('account', $accounts, null, ['class' => 'form-control', 'required']) !!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('narration', 'Enter a Description for this Configuration (See Examples)') !!}
                                    {!! Form::text('narration', '',['class'=>'form-control', 'required' ]) !!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('study_level', 'Select Study Level') !!}
                                    {!! Form::select('study_level', [''=>'N/A',getAllStudyLevels()], null, ['class' => 'form-control']) !!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('program_id', 'Select Programme') !!}
                                    {!! Form::select('program_id', [''=>'N/A',getAllProgrammes()], null, ['class' => 'form-control']) !!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('semester', 'Select Semester') !!}
                                    {!! Form::select('semester', [''=>'N/A',getAllSemesters()], null, ['class' => 'form-control']) !!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('in_state', 'In State? ') !!}
                                    {!! Form::select('in_state', [''=> 'N/A','0' => 'NO', '1' => 'YES'], '', ['class' => 'form-control']) !!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('user_id', 'Enter Student Matric No') !!}
                                    {!! Form::text('user_id', '',['class'=>'form-control' ]) !!}
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
                                    <th>Programme</th>
                                    <th>Level</th>
                                    <th>Semester</th>
                                    <th>In State</th>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>


                            <tbody>



                                @foreach( $feeConfigs as $key => $v )
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $v['narration'] }}</td>
                                    <td>{{ getProgrammeNameById($v['program_id']) }}</td>
                                    <td>{{ getStudyLevelNameById($v['study_level_id']) }}</td>
                                    <td>{{ getSemesterNameById($v['semester_id']) }}</td>
                                    <td>{{ getInStateValue($v['in_state']) }}</td>
                                    <td>{{ getFeeCategoryName($v['fee_category_id']) }}</td>
                                    <td>N{{ number_format(convertToNaira(getFeeTemplateAmount($v['fee_template_id'])),2) }}</td>

                                    <td>




                                            {!! Form::open(['route' => ['fee-configs.destroy', $v['id']] , 'method' => 'DELETE']) !!}

                                            <a class="popup-form btn btn-success" href="#edit-fee-template{{$key+1}}">Edit Config </a>
                                            <a href="{{ route('fee-templates.show', $v['fee_template_id']) }}" class="btn btn-primary">View Details</a>

                                            {!! Form::submit('Delete', ['class'=>'btn btn-danger']) !!}

                                            {!! Form::close() !!}
                                    </td>


                                    <div class="card mfp-hide mfp-popup-form mx-auto" id="edit-fee-template{{$key+1}}">
                                        <div class="card-body">
                                            <h4 class="mt-0 mb-4">Edit Fee Config</h4>
                                            {!! Form::open(['route' => ['fee-configs.update', $v->id]  , 'method' => 'PUT']) !!}

                                            {!! Form::hidden('id', $v->id, ['class'=>'form-control']) !!}

                                            <div class="form-group">
                                                {!! Form::label('fee_template_id', 'Select Fee Template') !!}
                                                {!! Form::select('fee_template_id', $feeTemplates, $v['fee_template_id'], ['class' => 'form-control', 'required']) !!}
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('fee_category_id', 'Select Fee Category') !!}
                                                {!! Form::select('fee_category_id', $categories, $v->fee_category_id, ['class' => 'form-control', 'required']) !!}
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('account', 'Select Fee Account') !!}
                                                {!! Form::select('account', $accounts, $v->account, ['class' => 'form-control', 'required']) !!}
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('narration', 'Enter a Description for this Configuration (See Examples)') !!}
                                                {!! Form::text('narration', $v->narration,['class'=>'form-control', 'required' ]) !!}
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('study_level', 'Select Study Level') !!}
                                                {!! Form::select('study_level', [''=>'N/A',getAllStudyLevels()], $v->study_level, ['class' => 'form-control']) !!}
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('program_id', 'Select Programme') !!}
                                                {!! Form::select('program_id', [''=>'N/A',getAllProgrammes()], $v->program_id, ['class' => 'form-control']) !!}
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('semester', 'Select Semester') !!}
                                                {!! Form::select('semester', [''=>'N/A',getAllSemesters()], $v->semester, ['class' => 'form-control']) !!}
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('in_state', 'In State? ') !!}
                                                {!! Form::select('in_state', [''=> 'N/A','0' => 'NO', '1' => 'YES'], $v->in_state, ['class' => 'form-control']) !!}
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('user_id', 'Enter Student Matric No') !!}
                                                {!! Form::text('user_id', $v->user_id,['class'=>'form-control' ]) !!}
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
