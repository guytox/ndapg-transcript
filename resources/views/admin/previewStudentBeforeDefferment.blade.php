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

                    <div class="">
                        {!! Form::open(['route' =>['defermentMgt.store'] , 'method' => 'POST']) !!}

                        <table id=""  class="table table-striped table-centered table-nowrap mb-0" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th colspan="2">Name</th>
                                    <td colspan="5">{{getUserById($std->user_id)->name}}</td>

                                </tr>

                                <tr>
                                    <th colspan="2">Programme</th>
                                    <td colspan="5"> {{getProgrammeNameById($std->program_id)}} </td>
                                </tr>

                                <tr>
                                    <th colspan="2">Matric No</th>
                                    <td colspan="5">{{$std->matric}} </td>
                                </tr>

                                <tr>
                                    <th colspan="2">Begin Session</th>
                                    <td colspan="5" class="text-danger"><b> {{getSessionById($beginSess)->name}}</b> </td>
                                </tr>

                                <tr>
                                    <th colspan="2">Return Session</th>
                                    <td colspan="5"> {!! Form::text('r_sess', $returnSess, ['class'=>'form-control text-danger', 'style'=>'font-weight: bold']) !!} </td>
                                </tr>

                                <tr>
                                    <th colspan="2">Amount to Pay on Return</th>
                                    <td colspan="5"> <strong> {!! Form::number('amt', $amt, ['class'=>'form-control text-danger', 'style'=>'font-weight: bold']) !!} </strong></td>
                                </tr>

                                <tr>
                                    <th colspan="7" class="text-center">Course Registrtion Records to be deleted (if found)</th>
                                </tr>

                                <tr>
                                    <th scope="col">Sno</th>
                                    <th scope="col">Chk</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Session</th>
                                    <th scope="col">Semester</th>
                                    <th scope="col">#Credits</th>
                                    <th scope="col">#courses</th>
                                    <th scope="col">status</th>
                                </tr>

                                
                            </thead>


                                {!! Form::hidden('d_sess', $beginSess, ['class'=>'form-control']) !!}
                                {!! Form::hidden('std_id', $std->id, ['class'=>'form-control']) !!}


                            <tbody>
                                @php
                                    $sn =1;
                                @endphp
                                @foreach ($thisSessionReg as $v)

                                    <tr>
                                        <td>{{$sn}}</td>
                                        <td>{!! Form::checkbox('regMonitor[]', $v->uid, true,['readonly'=>'readonly']) !!}</td>
                                        <td>{{ $v->title}}</td>
                                        <td>{{getSessionById($v->session_id)->name}}</td>
                                        <td>{{getSemesterById($v->session_id)->name}}</td>
                                        <td>{{$v->total_credits}}</td>
                                        <td>{{$v->num_of_courses}}</td>
                                        <td>{{$v->status}}</td>
                                       
                                    </tr>

                                    @php
                                        $sn++;
                                    @endphp

                                @endforeach


                                <tr>
                                    <th colspan="7" class="text-center">Note**** Any Registration Records listed above will be deleted during deferment processing</th>
                                </tr>



                            </tbody>

                        </table>
                        
                        <table>
                            <hr>
                            <thead>
                                <td></td>
                                <td></td>
                            </thead>
                            <tbody>
                                

                                <tr>

                                    <td>
                                        {!! Form::label('approveAs', 'Select Action (Proceed or Cancel)') !!}
                                        {!! Form::select('action', [''=>'N/A','1'=>"Proceed", '2'=>'Cancel'], '', ['class'=>'form-control','required']) !!}
                                    </td>
                                </tr>
                               

                                <tr>

                                    <td>
                                        {!! Form::submit('Proceed with Defferment', ['class'=>'btn btn-success','required']) !!}
                                    </td>

                                </tr>
                            </tbody>

                            {!! Form::close() !!}
                        </table>



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


@section('js')
<!-- Required datatable js -->
<script src="{{ asset('admin/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>


<!-- Datatable init js -->
<script src="{{ asset('admin/assets/js/pages/datatables.init.js') }}"></script>


@endsection
