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



    <h1>List of Registered Courses for {{ getCurriculaById($Monitors->curricula_id,"name") }}</h1>


    <div class="row">
        @include('includes.messages')
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <span class="text-default " title="Add/Drop Instructions">1. REGISTRATION UPDATE PROCEDURE:  Remove Courses, then add courses (NOTE: Adding courses before removing may give you errors)</span>
                    @if ($Monitors->message!='')
                    <p><span class="text-danger " title="Add/Drop Instructions"><b>*** Notice from Department****: </b>{{$Monitors->message}}</span></p>
                    @endif

                    <div>

                        <a class="btn btn-danger form-control" href="{{route('student.registration.submit',['id'=>$Monitors->id])}}">Click here Submit Registration ***(Note: You will no longer be able to make changes)</a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">

                            <thead>
                                <tr>
                                    <th scope="col">S/N</th>
                                    <th scope="col">Check</th>
                                    <th scope="col">Code</th>
                                    <th scope="col">Title</th>
                                    <th scope="col">Credits</th>
                                    <th scope="col">category</th>
                                    <th scope="col">Action</th>

                                </tr>
                            </thead>
                            {!! Form::open(['route' => ['coursereg.store'], 'method' => 'POST']) !!}
                            <div class="form-group">
                                {!! Form::hidden('curricula_id', $Monitors->curricula_id, ['class'=>'form-control']) !!}

                            </div>
                            <tbody>

                                @php
                                    $totalCredits =0;
                                    $totalCore=0;
                                    $totalElective=0;


                                    $sn=1;

                                @endphp


                            @if ($Monitors->num_of_courses>=1)



                                @if (count($Monitors->RegMonitorItems)>=1)
                                        <tr>
                                            <td colspan="7" align="center" >List of Registered Courses</td>
                                        </tr>


                                    @foreach ($Monitors->RegMonitorItems as $key => $item)
                                        <tr>
                                            <td>
                                                <div class="custom-control custom-checkbox">
                                                    {{$sn}}
                                                </div>
                                            </td>
                                            <td>{!! Form::checkbox('registered[]', $item->course_id, true,['readonly', 'onclick'=>"return false;"]) !!}</td>
                                            <td>{{ getCourseDetailsById($item->course_id,'code') }}</td>
                                            <td>{{ getCourseDetailsById($item->course_id,'title') }}</td>
                                            <td>{{ getCourseDetailsById($item->course_id,'credits') }}</td>
                                            <td>{{ $item->category }}</td>
                                            @if ($item->is_carryOver==1)
                                                <td></td>
                                            @else
                                                <td>
                                                    <a class="btn btn-info" href="{{ route('student.registration.removecourse', ['id'=>$item->id]) }}">Remove Course</a>
                                                </td>
                                            @endif

                                        </tr>

                                        @php
                                            $totalCredits += getCourseDetailsById($item->course_id,'credits');
                                            if ($item->category == 'core') {
                                                $totalCore += getCourseDetailsById($item->course_id,'credits');
                                            }elseif ($item->category == 'elective') {
                                                $totalElective += getCourseDetailsById($item->course_id,'credits');
                                            }
                                            $sn++;
                                        @endphp

                                    @endforeach
                                @endif





                            @endif

                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td>Total Core Credits</td>
                                        <td>{{ $totalCore }}</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td>Total Elective Credits</td>
                                        <td>{{ $totalElective }}</td>
                                        <td></td>
                                    </tr>

                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td>Total Credits</td>
                                        <td>{{ $totalCredits }}</td>
                                        <td></td>
                                    </tr>

                                {{-- //Core Carry Overs --}}

                                <tr>
                                    <td colspan="7" align="center" class="text-danger" >Unregistered Carry Over Courses</td>
                                </tr>
                                @if (count($CorecarryOvers)<=0 && count($ElectivecarryOvers)<=0)
                                    <tr>
                                        <td colspan="7" align="center" class="text-default" >You do not have Unregistered Carry Over Courses</td>
                                    </tr>
                                @endif


                                @if (count($CorecarryOvers)>0)



                                    @foreach ($CorecarryOvers as $key => $item)
                                            <tr>
                                                <td>
                                                    <div class="custom-control custom-checkbox">
                                                        {{$sn}}
                                                    </div>
                                                </td>
                                                <td>{!! Form::checkbox('carryOvers[]', $item->course_id, false,['readonly']) !!}</td>
                                                <td>{{ getCourseDetailsById($item->course_id,'code') }}</td>
                                                <td>{{ getCourseDetailsById($item->course_id,'title') }}</td>
                                                <td>{{ getCourseDetailsById($item->course_id,'credits') }}</td>
                                                <td>core</td>
                                                <td></td>

                                            </tr>

                                            @php

                                                $sn++;
                                            @endphp

                                    @endforeach
                                @endif

                                {{-- Elective CarryOvers --}}
                                @if (count($ElectivecarryOvers)>0)


                                @foreach ($ElectivecarryOvers as $key => $item)
                                        <tr>
                                            <td>
                                                <div class="custom-control custom-checkbox">
                                                    {{$sn}}
                                                </div>
                                            </td>
                                            <td>{!! Form::checkbox('carryOvers[]', $item->course_id, false,['readonly']) !!}</td>
                                            <td>{{ getCourseDetailsById($item->course_id,'code') }}</td>
                                            <td>{{ getCourseDetailsById($item->course_id,'title') }}</td>
                                            <td>{{ getCourseDetailsById($item->course_id,'credits') }}</td>
                                            <td>elective</td>
                                            <td></td>

                                        </tr>

                                        @php

                                            $sn++;
                                        @endphp

                                    @endforeach
                                @endif

                                <tr>
                                    <td colspan="7" align="center" class="text-danger" >Unregistered Dropped Courses</td>
                                </tr>

                                @if (count($droppedcores)<=0 && count($droppedelectives)<=0)
                                    <tr>
                                        <td colspan="7" align="center" class="text-default" >You do not have Unregistered Dropped Courses</td>
                                    </tr>
                                @endif

                                @if (count($droppedcores)>0)

                                @foreach ($droppedcores as $key => $item)
                                        <tr>
                                            <td>
                                                <div class="custom-control custom-checkbox">
                                                    {{$sn}}
                                                </div>
                                            </td>
                                            <td>{!! Form::checkbox('droppedCores[]', $item->course_id, false,['readonly']) !!}</td>
                                            <td>{{ getCourseDetailsById($item->course_id,'code') }}</td>
                                            <td>{{ getCourseDetailsById($item->course_id,'title') }}</td>
                                            <td>{{ getCourseDetailsById($item->course_id,'credits') }}</td>
                                            <td>core</td>
                                            <td></td>

                                        </tr>

                                        @php

                                            $sn++;
                                        @endphp

                                    @endforeach
                                @endif


                                @if (count($droppedelectives)>0)

                                @foreach ($droppedelectives as $key => $item)
                                        <tr>
                                            <td>
                                                <div class="custom-control custom-checkbox">
                                                    {{$sn}}
                                                </div>
                                            </td>
                                            <td>{!! Form::checkbox('droppedElectives[]', $item->course_id, false,['readonly']) !!}</td>
                                            <td>{{ getCourseDetailsById($item->course_id,'code') }}</td>
                                            <td>{{ getCourseDetailsById($item->course_id,'title') }}</td>
                                            <td>{{ getCourseDetailsById($item->course_id,'credits') }}</td>
                                            <td>elective</td>
                                            <td></td>

                                        </tr>

                                        @php

                                            $sn++;
                                        @endphp

                                    @endforeach
                                @endif




                            </tbody>

                        </table>

                        {!! Form::submit('Add Selected Courses', ['class'=>'btn btn-success']) !!}

                            {!! Form::close() !!}





                        <div class="card">
                            <div class="card-body">
                                <h4 class="header-title mb-4"></h4>
                                <div>

                                    <a class="btn btn-danger form-control" href="{{route('student.registration.submit',['id'=>$Monitors->id])}}">Click Here Submit Registration ***(Note: You will no longer be able to make changes)</a>
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
