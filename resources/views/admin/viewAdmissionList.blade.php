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

                    <div class="table-responsive">
                        <table id="datatable"  class="table table-striped table-centered table-nowrap mb-0" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>


                                    <th scope="col">S/N</th>
                                    <th scope="col">App No</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Session</th>
                                    <th scope="col">Programme</th>
                                    <th scope="col">Details</th>
                                </tr>
                            </thead>

                                {!! Form::open(['route' =>['recommend.selected.applicants'] , 'method' => 'POST']) !!}


                            <tbody>
                                @php
                                    $sn =1;
                                @endphp
                                @foreach ($applicants as $v)

                                    <tr>
                                        <td>{{$sn}}</td>
                                        <td>{{ $v->form_number}}</td>
                                        <td>{{ getUserById($v->user_id)->name}}</td>
                                        <td>{{getSessionById($v->session_id)->name}}</td>
                                        <td>{{getProgramNameById($v->program_id)}} </td>
                                        <td>
                                            @if ($v->is_submitted ==1 )

                                                <a class="btn btn-primary" href="{{ route('preview.submitted.application', ['id'=>$v->user_id]) }}" target="_blank">view Details</a>

                                            @else


                                            @endif

                                        </td>
                                    </tr>

                                    @php
                                        $sn++;
                                    @endphp

                                @endforeach



                            </tbody>



                        </table>




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
