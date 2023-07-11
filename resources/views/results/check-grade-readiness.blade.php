@extends('layouts.setup')

@section('content')

    @include('includes.messages')

    <h4>List of Allocated Courses for {{ucfirst($semester)}} Semester,  {{$session_name}} Academic Session</h4>

    <div class="row">
        <div class="col-lg-12">

            <div class="card">
                <div class="card-body">
                    <div>
                        <a class="popup-form btn btn-primary" href="#test-form">Search Another Session/Semester</a>
                    </div>
                    <h4 class="header-title mb-4"></h4>

                    @include('includes.messages')

                    @error('error')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $error }}</strong>
                        </span>
                    @enderror


                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">S/N</th>
                                    <th scope="col">Code</th>
                                    <th scope="col">Title</th>
                                    <th scope="col"> Credits</th>
                                    <th scope="col">StaffId</th>
                                    <th scope="col">StaffName</th>
                                    <th scope="col">gsm</th>
                                    <th scope="col">Grading</th>
                                    <th scope="col">Submitted</th>
                                    <th scope="col">Hod Confirm</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($courses as $key => $val)


                                    <tr>
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                {{$key+1}}
                                            </div>
                                        </td>

                                        <td>{{getCourseDetailsById($val->course_id,'code')}}</td>
                                        <td>{{getCourseDetailsById($val->course_id,'title')}}</td>
                                        <td>{{getCourseDetailsById($val->course_id,'credits')}}</td>
                                        <td>{{getUserById($val->staff_id)->username}}</td>
                                        <td>{{getUserById($val->staff_id)->name}}</td>
                                        <td>{{getUserById($val->staff_id)->phone_number}}</td>
                                        <td>

                                            @if ($val->cfm_ca1 ==='0')
                                                <span title="Confirmed CA1" > &#10060;</span>
                                            @elseif ($val->cfm_ca1 ==='1')
                                                <span title="Confirmed CA1" > &#9989;</span>
                                            @endif

                                            @if ($val->cfm_ca2 ==='0')
                                                <span title="Confirmed CA2" > &#10060;</span>
                                            @elseif ($val->cfm_ca2 ==='1')
                                                <span title="Confirmed CA2" > &#9989;</span>
                                            @endif

                                            @if ($val->cfm_ca3 ==='0')
                                                <span title="Confirmed CA3" > &#10060;</span>
                                            @elseif ($val->cfm_ca3 ==='1')
                                                <span title="Confirmed CA3" > &#9989;</span>
                                            @endif

                                            @if ($val->cfm_ca4 ==='0')
                                                <span title="Confirmed CA4" > &#10060;</span>
                                            @elseif ($val->cfm_ca4 ==='1')
                                                <span title="Confirmed CA4" > &#9989;</span>
                                            @endif

                                            @if ($val->cfm_exam ==='0')
                                                <span title="Confirmed EXAM" > &#10060;</span>
                                            @elseif ($val->cfm_exam ==='1')
                                                <span title="Confirmed EXAM" > &#9989;</span>
                                            @endif

                                        </td>

                                        <td>
                                            @if ($val->submitted ==='2')
                                                <span title="Lecturer Submission" > &#10060;</span>
                                            @elseif ($val->submitted ==='1')
                                                <span title="Lecturer Submission" > &#9989;</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($val->accepted ==='2')
                                                <span title="HOD Acceptance" > &#10060;</span>
                                            @elseif ($val->accepted ==='1')
                                                <span title="HOD Acceptance" > &#9989;</span>
                                            @endif

                                        </td>

                                        <td>

                                            @if ($val->accepted ==='2')

                                            Not Ready

                                            @else

                                            All Set

                                            @endif


                                            @role('hod')

                                                @if ($val->submitted ==='1' && $val->accepted ==='2' )

                                                    <a class="popup-form btn btn-primary" href="#approve-form{{$key+1}}">Approve/Reject</a>

                                                    <a class="btn btn-success" href="{{route('lecturer.grading.scoresheet',['as'=>'ortesenKwagh', 'id'=>$val->uid])}}">View Score Sheet</a>

                                                @elseif ($val->submitted ==='2')

                                                Lecturer yet to Submit

                                                @elseif ($val->grading_completed ==='1' )
                                                    Ready for Result Computation <br>
                                                    <a class="btn btn-success" href="{{route('lecturer.grading.scoresheet',['as'=>'ortesenKwagh', 'id'=>$val->uid])}}">View Score Sheet</a>
                                                @endif


                                            @endrole

                                            @role('dean')

                                            @if ($val->grading_completed ==='1' )

                                                    <a class="popup-form btn btn-primary" href="#deanunconfirm-form{{$key+1}}">Approve/RejectSubmission</a>

                                                @elseif ($val->grading_completed ==='2')

                                                    No Action Required

                                                @endif


                                            @endrole
                                        </td>



                                        </td>
                                    </tr>


                                    <div class="card mfp-hide mfp-popup-form mx-auto" id="deanunconfirm-form{{$key+1}}">
                                        <div class="card-body">
                                            <h4 class="mt-0 mb-4">Grade Confirmation (Choose what to confirm and submit)</h4>
                                            {!! Form::open(['route' => ['lecturer.grading.confirm', 'as'=>'ityoughKiVesen']  , 'method' => 'POST']) !!}

                                                {!! Form::hidden('id', $val->uid, ['class'=>'form-control']) !!}

                                                <div class="form-group">
                                                    {!! Form::label('action', 'Select what to confirm') !!}
                                                    {!! Form::select('action', ['' =>'N/A', 'approve'=>'Accept Grades', 'disapprove'=>'Reject Grades'], '',['class'=>'form-control', 'required' ]) !!}
                                                </div>

                                                <div class="form-group">
                                                    {!! Form::label('confirm', 'I confirm that as Dean of Faculty, I wish to make this course availble for grading after it has been confirmed, I affirm that all affected results will be recomputed', ['class'=>'text-danger']) !!}
                                                    {!! Form::checkbox('confirm','mlumun' , false ,  [ 'required' ]) !!}
                                                </div>

                                            {!! Form::submit('Confirm Grades',['class'=>'btn btn-success']) !!}

                                            {!! Form::close() !!}
                                        </div>
                                    </div>



                                @endforeach

                                <tr>
                                    <td>Analytics</td>
                                </tr>
                                <tr>
                                    <td colspan="2">Un-Allocated Courses:</td>
                                    <td colspan="5">
                                        @if ($unAllocated == 1)

                                            @foreach ($notAllocated as $q)

                                                <b>{{$q['courseCode']}},</b>

                                            @endforeach

                                        @elseif ($unAllocated == 0)

                                            All Courses taken by students in this category have been allocated with grading rights (No Action Required)

                                        @endif
                                    </td>
                                </tr>


                                <tr>
                                    <td colspan="2">Grading Not Completed:</td>
                                    <td colspan="5" class="text-danger">
                                        @if ($gradingIncomplete == 1)

                                            @foreach ($gradingNotCompleted as $p)

                                            <b>{{$p['courseCode']}},</b>

                                            @endforeach

                                        @elseif ($gradingIncomplete == 0)

                                        All Courses taken by students in this category have been allocated successfully

                                        @endif
                                    </td>
                                </tr>

                                @if ($toCompute == 1)

                                    <tr>
                                        <td colspan="11">

                                            {!! Form::open(['route' => ['compute-selected-result','as'=>'ityoughKiKyaren'], 'method' => 'POST']) !!}
                                                {!! Form::hidden('studylevel', $level_id, ['class'=>'form-control']) !!}
                                                {!! Form::hidden('programme', $program_id, ['class'=>'form-control']) !!}
                                                {!! Form::hidden('schsession', $session_id, ['class'=>'form-control']) !!}
                                                {!! Form::hidden('schsemester', $semester_id, ['class'=>'form-control']) !!}
                                                {!! Form::hidden('command', 25, ['class'=>'form-control']) !!}

                                                {!! Form::submit("Comupute Result", ['class'=>'form-control btn btn-success']) !!}
                                            {!! Form::close() !!}
                                        </td>
                                    </tr>

                                @else

                                    <tr>
                                        <td colspan="2" class="text-danger">Compute Status</td>
                                        @if ($noCourseAllocation == 1)

                                            <td colspan="9">Error!!! You cannot compute result, No Semester Allocaton found for this semester</td>

                                        @else
                                            <td colspan="9">Note!!! You Cannot Compute Result Either because you have un-allocated courses or course-grading is not complete, Check above to resolve (Action Required: Contact Lecturers or HOD)</td>
                                        @endif

                                    </tr>

                                @endif




                            </tbody>
                        </table>


                                <div class="card" >
                                    <div class="card-body">
                                        <h4 class="header-title mb-4"></h4>


                                    </div>
                                </div>




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
