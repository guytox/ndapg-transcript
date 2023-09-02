@extends('layouts.setup')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="text-danger" ><marquee behavior="" direction="left"> <b> <font size="6">{{ getSiteNotification()}}</font> </b> </marquee></div>

            <div class="card">
                <div class="card-header">{{ __('Student ID Card') }}</div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif

                    @include('includes.messages')

                    {{ __('Student Details!!!') }}



                    <table class="table-control table-bordered" width="100%">
                        <tr>
                            <th>Matric Number</th>
                            <td colspan="2"><h2 class="text-success">{{$std->matric}}</h2></td>
                            <td rowspan="8"> <img src="{{ asset($std->user->passport)}}" alt="Applicant Passport" height="200" width="150"> </td>
                        </tr>
                        <tr>
                            <th>Student Name</th>
                            <td>{{$std->user->name}}</td>

                        </tr>
                        <tr>
                            <th>Program</th>
                            <td>{{getProgramNameById($std->program_id)}}</td>
                        </tr>

                        {{-- <tr>
                            <th>Admission Status</th>
                            <td>
                                @if ($std->is_admitted == 1)
                                    ADMITTED
                                @else
                                    NOT ADMITTED
                                @endif
                            </td>
                        </tr> --}}

                        <tr>
                            <th>GSM</th>
                            <td>{{$std->user->phone_number}} </td>
                        </tr>

                        <tr>
                            <th>Email</th>
                            <td>{{$std->user->email}} </td>
                        </tr>

                        <tr>
                            <th>Gender</th>
                            <td>{{$std->user->profile->gender}}</td>
                        </tr>

                        <tr>
                            <th>Date of Birth</th>
                            <td>{{$std->user->profile->dob}}</td>
                        </tr>

                        <tr>
                            <th>Application No:</th>
                            @if ($std->applicant)
                                <td>{{$std->user->applicant->form_number}} </td>
                            @else
                                <td> N/A </td>
                            @endif

                        </tr>

                    </table>

                    <hr>

                    <div>
                        <a href="{{route('idCard.index')}}" class="btn btn-danger form-control">Close</a>
                    </div>



                </div>
            </div>
        </div>
    </div>
</div>
@endsection
