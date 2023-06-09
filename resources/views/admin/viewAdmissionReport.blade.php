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
                                    <th scope="col">Prog</th>
                                    <th scope="col">Admitted</th>
                                    <th scope="col">Paid Accept.</th>
                                    <th scope="col">Screened</th>
                                    <th scope="col">Paid Tuition</th>
                                    <th scope="col">File Issued</th>
                                </tr>
                            </thead>



                            <tbody>
                                @php
                                    $sn =1;
                                    $adm =0;
                                    $acc=0;
                                    $scr=0;
                                    $tui=0;
                                    $cfil=0;

                                @endphp
                                @foreach ($report as $v)

                                    <tr>
                                        <td>{{$sn}}</td>
                                        <td>{{$v->name}}</td>
                                        <td>{{$v->admitted}}</td>
                                        <td>{{$v->paidAcceptance}}</td>
                                        <td>{{$v->Screened}}</td>
                                        <td>{{$v->paidTuition}}</td>
                                        <td>{{$v->collectedFile}}</td>


                                    </tr>

                                    @php
                                        $sn++;
                                        $adm= $adm+$v->admitted;
                                        $acc= $acc+$v->paidAcceptance;
                                        $scr= $scr+$v->Screened;
                                        $tui= $tui+$v->paidTuition;
                                        $cfil= $cfil+$v->collectedFile;
                                    @endphp

                                @endforeach

                                    <tr>
                                        <td></td>
                                        <td>Total</td>
                                        <td>{{$adm}}</td>
                                        <td>{{$acc}}</td>
                                        <td>{{$scr}}</td>
                                        <td>{{$tui}}</td>
                                        <td>{{$cfil}}</td>
                                    </tr>



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
