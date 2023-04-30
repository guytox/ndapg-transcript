@extends('layouts.setup')

@section('css')
<!-- DataTables -->
<link href="{{ asset('admin/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
{{-- <link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" --}}
type="text/css" />
@endsection



@section('content')

    <h1>{{$title}}</h1>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-4"></h4>

                    @include('includes.messages')

                    <div>
                        {!! Form::open(['route' =>['effect.change.admission'] , 'method' => 'POST']) !!}

                        {!! Form::hidden('id', $applicant->uid, []) !!}


                    </div>

                    <div class="table-responsive">
                        <table id="datatable"  class="table table-striped table-centered table-nowrap mb-0" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th scope="col">App No</th>
                                    <td>{{ $applicant->form_number}}</td>                                    
                                </tr>
                                <tr>
                                    <th scope="col">Name</th>
                                    <td>{{ getUserById($applicant->user_id)->name}}</td>
                                </tr>
                                <tr>
                                    <th scope="col">Session</th>
                                    <td>{{getSessionById($applicant->session_id)->name}}</td>
                                </tr>
                                <tr>
                                    <th scope="col">Old Programme</th>
                                    <td>{{getProgramNameById($applicant->program_id)}} </td>
                                </tr>
                               
                                <tr>
                                    <td>
                                        <a class="btn btn-danger form-control" href="{{ route('preview.submitted.application', ['id'=>$applicant->user_id]) }}" target="_blank">view Submitted Application Form</a>
                                    </td>
                                </tr>
                                
                            </thead>
                            <tbody>
                                {{-- <tr>
                                    <th scope="col">New Programme</th>

                                    <td>
                                        <livewire:search-programs>
                                    </td>
                                </tr> --}}
                                <tr>
                                    <th>Select New Program</th>
                                    <td>
                                    {!! Form::select('progId', getAppliableProgrammeDropdown(), '', ['class'=>'form-control']) !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        {!! Form::submit('Change Programme and Admit', ['class'=>'form-control btn btn-success']) !!}
                                    </td>
                                </tr>
                            </tbody>



                        </table>

                        {!! Form::close() !!}


                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end row -->



@endsection


@section('js')
<!-- Required datatable js -->
<script src="{{ asset('admin/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>


<!-- Datatable init js -->
<script src="{{ asset('admin/assets/js/pages/datatables.init.js') }}"></script>


@endsection
