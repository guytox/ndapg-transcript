@extends('layouts.setup')
@section('css')
<!-- DataTables -->
<link href="{{ asset('admin/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
{{-- <link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" --}}
type="text/css" />
@endsection

@section('content')

@include('includes.messages')

@if (!isset($applicant))
    <div class="row">
        <div class="card ">
            <div class="card-body">
                <h4 class="mt-0 mb-4">Admitted Student Payment Code Confirmation Form</h4>
                {!! Form::open(['route' => 'select.paycode.upload', 'method' => 'POST', 'file'=>true, 'enctype'=>"multipart/form-data"]) !!}

                <div class="form-group">
                    {!! Form::label('applicant', 'Enter the Student form number and click search') !!}
                    {!! Form::text('applicant', null, ['class'=>'form-control', 'required']) !!}
                </div>


                {!! Form::submit('Search Student Details', ['class'=>'form-control btn btn-danger']) !!}

                {!! Form::close() !!}

            </div>
        </div>
    </div>

@endif


@if (isset($applicant))


    <div class="row">
        <div class="card ">
            <div class="card-body">
                <h4 class="mt-0 mb-4">Biodata</h4>
                <div class="table-control">
                    <table class="table">
                        <tr>
                            <th>Name :</th>
                            <td>{{$applicant->surname}} {{$applicant->other_names}}</td>
                        </tr>
                        <tr>
                            <th>Gender :</th>
                            <td>{{$applicant->gender}}</td>
                        </tr>
                        <tr>
                            <th>State :</th>
                            <td>{{$applicant->state}}</td>
                        </tr>
                        <tr>
                            <th>Country :</th>
                            <td>{{$applicant->country}}</td>
                        </tr>
                    </table>

                </div>

            </div>
        </div>

        <div class="card ">
            <div class="card-body">
                <h4 class="mt-0 mb-4">Admission Details</h4>
                <div class="table-control">
                    <table class="table">
                        <tr>
                            <th>Programme :</th>
                            <td>{{$applicant->programme}}</td>
                        </tr>
                        <tr>
                            <th>Department :</th>
                            <td>{{$applicant->department}}</td>
                        </tr>

                        <tr>
                            <th>Faculty :</th>
                            <td>{{$applicant->faculty}}</td>
                        </tr>

                        <tr>
                            <th>Categoty :</th>
                            <td>{{$applicant->category}}</td>
                        </tr>
                    </table>

                </div>


            </div>
        </div>

        <div class="card ">
            <div class="card-body">
                <h4 class="mt-0 mb-4">Student Details</h4>
                <div class="table-control">
                    <table class="table">
                        <tr>
                            <th>MatricNo :</th>
                            <td>{{$applicant->matric_number}}</td>
                        </tr>
                        <tr>
                            <th>Form Number : </th>
                            <td>{{$applicant->form_number}}</td>
                        </tr>
                        <tr>
                            <th>Payment Code :</th>
                            <td>{{$applicant->payment_code}}</td>
                        </tr>
                    </table>

                </div>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="card">
            <table>
                <tr>
                    <th>Qualifications :</th>
                    <td>{{$applicant->qualifications}}</td>
                </tr>
            </table>
        </div>

    </div>
    <div class="row">
        <div class="card ">
            <div class="card-body">
                <h4 class="mt-0 mb-4">Confirm Student Payment</h4>
                {!! Form::open(['route' => 'activate.student.account', 'method' => 'POST', 'file'=>true, 'enctype'=>"multipart/form-data"]) !!}

                {!! Form::hidden('formnumber', $applicant->id, []) !!}

                <div class="form-group">
                    {!! Form::label('amount', 'Enter the Amount paid by student') !!}
                    {!! Form::text('amount', null, ['class'=>'form-control', 'required']) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('clearedfor', 'Select Semesters to allow Registration', []) !!}
                    {!! Form::select('clearedfor', ['1'=>'First Semester', '2'=>'Second Semester','3'=>'Both Semesters'], '3', ['class'=>'form-control']) !!}
                </div>


                {!! Form::submit('Confirm Student Payment',['class'=>'form-control btn btn-success']) !!}

                {!! Form::close() !!}

            </div>
        </div>
    </div>

    @if (isset($lasttenapplicants))
    <hr>
        <div class="row">
            <div class="card">
                <h4 class="mt-0 mb-4">last ten (10) Confirmations</h4>
                <div class="card-body">
                    <table class="table">

                        <tr>
                            <th>S/N</th>
                            <th>Matric No</th>
                            <th>Form No</th>
                            <th>Name</th>
                            <th>PayCode</th>
                            <th>Programme</th>
                            <th>cleared By</th>
                            <th>At</th>
                            <th>Amount Pid</th>
                        </tr>
            @foreach ($lasttenapplicants as $key => $v)

                <tr>
                    <td>{{$key +1}}</td>
                    <td>{{$v->matric_number}}</td>
                    <td>{{$v->form_number}}</td>
                    <td>{{$v->surname}} {{$v->other_names}}</td>
                    <td>{{$v->payment_code}}</td>
                    <td>{{getProgrammeDetailById($v->programme_id, 'name')}}</td>
                    <td>{{getUserById($v->cleared_by)->name}}</td>
                    <td>{{$v->cleared_at}}</td>
                    <td>NGN{{number_format(convertToNaira($v->amount_paid),2)}}</td>
                </tr>

            @endforeach


                    </table>
                </div>
            </div>

        </div>
    @endif
@endif



@endsection
@section('js')
<!-- Required datatable js -->
<script src="{{ asset('admin/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>


<!-- Datatable init js -->
<script src="{{ asset('admin/assets/js/pages/datatables.init.js') }}"></script>


@endsection
