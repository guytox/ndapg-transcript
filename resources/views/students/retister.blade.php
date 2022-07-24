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



    <h1>List of Courses for {{ $outline->title }}</h1>


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

                            </div>



                        </div>
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
                                    <th scope="col">Alternative</th>

                                </tr>
                            </thead>
                            {!! Form::open(['route' => ['coursereg.store'], 'method' => 'POST']) !!}
                            <div class="form-group">
                                {!! Form::hidden('curricula_id', $outline->id, ['class'=>'form-control']) !!}
                                {!! Form::hidden('uid',uniqid('crf_'), ['class'=>'form-control']) !!}
                            </div>
                            <tbody>

                                @php
                                    $totalCredits =0;
                                    $totalCore=0;
                                    $totalElective=0;
                                    $totalAlternatives=0;

                                    $sn=1;

                                @endphp

                            @if ($outline->numOfCourses>=1)

                                @if (count($carryOvers)>0)
                                        <tr>
                                            <td colspan="7" align="center" >Carry Over Courses</td>
                                        </tr>


                                    @foreach ($carryOvers as $key => $item)
                                        <tr>
                                            <td>
                                                <div class="custom-control custom-checkbox">
                                                    {{$sn}}
                                                </div>
                                            </td>
                                            <td>{!! Form::checkbox('carryOvers[]', $item->course_id, true,['readonly', 'onclick'=>"return false;"]) !!}</td>
                                            <td>{{ getCourseDetailsById($item->course_id,'code') }}</td>
                                            <td>{{ getCourseDetailsById($item->course_id,'title') }}</td>
                                            <td>{{ getCourseDetailsById($item->course_id,'credits') }}</td>
                                            <td>{{ $item->category }}</td>
                                            @if ($item->alternative == '')
                                                <td>N/A</td>
                                            @else
                                                <td>{{ getCourseDetailsById($item->alternative,'code') }}</td>
                                            @endif

                                        </tr>

                                        @php
                                            $totalCredits += getCourseDetailsById($item->course_id,'credits');
                                            if ($item->category == 'core') {
                                                $totalCore += getCourseDetailsById($item->course_id,'credits');
                                            }elseif ($item->category == 'elective') {
                                                $totalElective += getCourseDetailsById($item->course_id,'credits');
                                            }elseif ($item->category == 'core/optional') {
                                                $totalCore += getCourseDetailsById($item->course_id,'credits');
                                                $totalAlternatives += getCourseDetailsById($item->course_id,'credits');
                                            }
                                            $sn++;
                                        @endphp

                                    @endforeach
                                @endif

                                @if (count($droppedcores)>0)



                                <tr>
                                    <td colspan="7" align="center" >Dropped Core Courses</td>
                                </tr>
                                @foreach ($droppedcores as $key => $item)
                                    <tr>
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                {{$sn}}
                                            </div>
                                        </td>
                                        <td>{!! Form::checkbox('droppedCores[]', $item->course_id, true,['readonly', 'onclick'=>"return false;"]) !!}</td>
                                        <td>{{ getCourseDetailsById($item->course_id,'code') }}</td>
                                        <td>{{ getCourseDetailsById($item->course_id,'title') }}</td>
                                        <td>{{ getCourseDetailsById($item->course_id,'credits') }}</td>
                                        <td>{{ $item->category }}</td>
                                        @if ($item->alternative == '')
                                            <td></td>
                                        @else
                                            <td>{{ getCourseDetailsById($item->alternative,'code') }}</td>
                                        @endif

                                    </tr>

                                    @php
                                        $totalCredits += getCourseDetailsById($item->course_id,'credits');
                                        if ($item->category == 'core') {
                                            $totalCore += getCourseDetailsById($item->course_id,'credits');
                                        }elseif ($item->category == 'elective') {
                                            $totalElective += getCourseDetailsById($item->course_id,'credits');
                                        }elseif ($item->category == 'core/optional') {
                                            $totalCore += getCourseDetailsById($item->course_id,'credits');
                                            $totalAlternatives += getCourseDetailsById($item->course_id,'credits');
                                        }
                                        $sn++;
                                    @endphp
                                @endforeach
                                @endif


                                @if (count($droppedelectives)>0)


                                <tr>
                                    <td colspan="7" align="center" >Dropped Elective Courses</td>
                                </tr>
                                @foreach ($droppedelectives as $key => $item)
                                    <tr>
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                {{$sn}}
                                            </div>
                                        </td>
                                        <td>{!! Form::checkbox('droppedElectives[]', $item->course_id, false,['readonly'=>'readonly']) !!}</td>
                                        <td>{{ getCourseDetailsById($item->course_id,'code') }}</td>
                                        <td>{{ getCourseDetailsById($item->course_id,'title') }}</td>
                                        <td>{{ getCourseDetailsById($item->course_id,'credits') }}</td>
                                        <td>{{ $item->category }}</td>
                                        @if ($item->alternative == '')
                                            <td></td>
                                        @else
                                            <td>{{ getCourseDetailsById($item->alternative,'code') }}</td>
                                        @endif

                                    </tr>

                                    @php
                                        $totalCredits += getCourseDetailsById($item->course_id,'credits');
                                        if ($item->category == 'core') {
                                            $totalCore += getCourseDetailsById($item->course_id,'credits');
                                        }elseif ($item->category == 'elective') {
                                            $totalElective += getCourseDetailsById($item->course_id,'credits');
                                        }elseif ($item->category == 'core/optional') {
                                            $totalCore += getCourseDetailsById($item->course_id,'credits');
                                            $totalAlternatives += getCourseDetailsById($item->course_id,'credits');
                                        }
                                        $sn++;
                                    @endphp
                                @endforeach
                                @endif


                                @if (count($optionals)>0)


                                <tr>
                                    <td colspan="7" align="center" >Core/Optional Courses</td>
                                </tr>
                                @foreach ($optionals as $key => $item)
                                    <tr>
                                        <td colspan="7" align="center" class="text-danger" >*** Note: You have to choose <b>Only One</b> between {{ getCourseDetailsById($item->semester_courses_id,'code') }} and {{ getCourseDetailsById($item->alternative,'code') }}</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                {{$sn}}
                                            </div>
                                        </td>
                                        <td>{!! Form::checkbox('cores[]', $item->semester_courses_id, true,['readonly'=>'readonly']) !!}</td>
                                        <td>{{ getCourseDetailsById($item->semester_courses_id,'code') }}</td>
                                        <td>{{ getCourseDetailsById($item->semester_courses_id,'title') }}</td>
                                        <td>{{ getCourseDetailsById($item->semester_courses_id,'credits') }}</td>
                                        <td>{{ $item->category }}</td>
                                        @if ($item->alternative == '')
                                            <td>N/A</td>
                                        @else
                                            <td>{{ getCourseDetailsById($item->alternative,'code') }}</td>
                                        @endif

                                    </tr>

                                    <tr>
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                {{$sn}}
                                            </div>
                                        </td>
                                        <td>{!! Form::checkbox('cores[]', $item->alternative, false,['readonly'=>'readonly']) !!}</td>
                                        <td>{{ getCourseDetailsById($item->alternative,'code') }}</td>
                                        <td>{{ getCourseDetailsById($item->alternative,'title') }}</td>
                                        <td>{{ getCourseDetailsById($item->alternative,'credits') }}</td>
                                        <td>{{ $item->category }}</td>
                                        @if ($item->semester_courses_id == '')
                                            <td>N/A</td>
                                        @else
                                            <td>{{ getCourseDetailsById($item->semester_courses_id,'code') }}</td>
                                        @endif

                                    </tr>

                                    @php
                                        $totalCredits += getCourseDetailsById($item->semester_courses_id,'credits');
                                        if ($item->category == 'core') {
                                            $totalCore += getCourseDetailsById($item->semester_courses_id,'credits');
                                        }elseif ($item->category == 'elective') {
                                            $totalElective += getCourseDetailsById($item->semester_courses_id,'credits');
                                        }elseif ($item->category == 'core/optional') {
                                            $totalCore += getCourseDetailsById($item->semester_courses_id,'credits');
                                            $totalAlternatives += getCourseDetailsById($item->semester_courses_id,'credits');
                                        }
                                        $sn++;
                                    @endphp
                                @endforeach
                                @endif

                                {{-- Core courses --}}

                                @if (count($cores)>0)


                                <tr>
                                    <td colspan="7" align="center" >Core Courses</td>
                                </tr>
                                @foreach ($cores as $key => $item)
                                    <tr>
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                {{$sn}}
                                            </div>
                                        </td>
                                        <td>{!! Form::checkbox('cores[]', $item->semester_courses_id, true,['readonly'=>'readonly']) !!}</td>
                                        <td>{{ getCourseDetailsById($item->semester_courses_id,'code') }}</td>
                                        <td>{{ getCourseDetailsById($item->semester_courses_id,'title') }}</td>
                                        <td>{{ getCourseDetailsById($item->semester_courses_id,'credits') }}</td>
                                        <td>{{ $item->category }}</td>
                                        @if ($item->alternative == '')
                                            <td></td>
                                        @else
                                            <td>{{ getCourseDetailsById($item->alternative,'code') }}</td>
                                        @endif

                                    </tr>

                                    @php
                                        $totalCredits += getCourseDetailsById($item->semester_courses_id,'credits');
                                        if ($item->category == 'core') {
                                            $totalCore += getCourseDetailsById($item->semester_courses_id,'credits');
                                        }elseif ($item->category == 'elective') {
                                            $totalElective += getCourseDetailsById($item->semester_courses_id,'credits');
                                        }elseif ($item->category == 'core/optional') {
                                            $totalCore += getCourseDetailsById($item->semester_courses_id,'credits');
                                            $totalAlternatives += getCourseDetailsById($item->semester_courses_id,'credits');
                                        }
                                        $sn++;
                                    @endphp
                                @endforeach

                                @endif

                                @if (count($electives)>0)


                                <tr>
                                    <td colspan="7" align="center" >Elective Courses</td>
                                </tr>
                                @foreach ($electives as $key => $item)
                                    <tr>
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                {{$sn}}
                                            </div>
                                        </td>
                                        <td>{!! Form::checkbox('electives[]', $item->semester_courses_id, false,['readonly']) !!}</td>
                                        <td>{{ getCourseDetailsById($item->semester_courses_id,'code') }}</td>
                                        <td>{{ getCourseDetailsById($item->semester_courses_id,'title') }}</td>
                                        <td>{{ getCourseDetailsById($item->semester_courses_id,'credits') }}</td>
                                        <td>{{ $item->category }}</td>
                                        @if ($item->alternative == '')
                                            <td></td>
                                        @else
                                            <td>{{ getCourseDetailsById($item->alternative,'code') }}</td>
                                        @endif

                                    </tr>

                                    @php
                                        $totalCredits += getCourseDetailsById($item->semester_courses_id,'credits');
                                        if ($item->category == 'core') {
                                            $totalCore += getCourseDetailsById($item->semester_courses_id,'credits');
                                        }elseif ($item->category == 'elective') {
                                            $totalElective += getCourseDetailsById($item->semester_courses_id,'credits');
                                        }elseif ($item->category == 'core/optional') {
                                            $totalCore += getCourseDetailsById($item->semester_courses_id,'credits');
                                            $totalAlternatives += getCourseDetailsById($item->semester_courses_id,'credits');
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
                                        <td>Total Optional Credits</td>
                                        <td>{{ $totalAlternatives }}</td>
                                        <td></td>
                                    </tr>

                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td>Total Credits</td>
                                        <td>{{ $totalCredits.'/'.$totalAlternatives }}</td>
                                        <td></td>
                                    </tr>
                            </tbody>

                        </table>

                        {!! Form::submit('Register Courses', ['class'=>'btn btn-success']) !!}

                            {!! Form::close() !!}





                        <div class="card">
                            <div class="card-body">
                                <h4 class="header-title mb-4"></h4>
                                <div>

                                    <a class="btn btn-warning" href="#">Return Back</a>
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
