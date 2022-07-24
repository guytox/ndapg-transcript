@extends('layouts.setup')
@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                @include('includes.messages')
                <div class="card-body">
                    <h2 class="header-title">  Olevel Results</h2>
                    <br>
                    <a href="{{ route('applicant.add_result') }}" class="btn btn-primary">Add New Result</a>
                    <br><br>

                    @foreach($olevels as $olevel)

                        <h4>Result For: {{ $olevel->exam_details['Exam_type'] }} {{ $olevel->exam_details['Exam_year'] }}  - {{ $olevel->sitting }}</h4>
                        <br>
                    <div class="body table-responsive mt-4">
                        <table class="table table-hover table-bordered">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Subject</th>
                                <th>Grade</th>
                            </tr>
                            </thead>
                            <tbody>

                            <tr>
                                <td>1</td>
                                <td>English Language</td>
                                <td>{{ $olevel->exam_details['English'] }}</td>
                            </tr>

                            <tr>
                                <td>2</td>
                                <td>Mathematics </td>
                                <td>{{ $olevel->exam_details['Mathematics'] }}</td>
                            </tr>

                            <tr>
                                <td>3</td>
                                <td>{{ $olevel->exam_details['subject_3']['subject_name'] }}</td>
                                <td>{{ $olevel->exam_details['subject_3']['grade'] }}</td>
                            </tr>

                            <tr>
                                <td>4</td>
                                <td>{{ $olevel->exam_details['subject_4']['subject_name'] }}</td>
                                <td>{{ $olevel->exam_details['subject_4']['grade'] }}</td>
                            </tr>

                            <tr>
                                <td>5</td>
                                <td>{{ $olevel->exam_details['subject_5']['subject_name'] }}</td>
                                <td>{{ $olevel->exam_details['subject_5']['grade'] }}</td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                    @endforeach



                </div>
            </div>
        </div>

    </div>
@endsection
