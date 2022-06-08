@extends('layouts.setup')
@section('content')
    <div class="row">
        <div class="col-xl-4">
            <div class="card">
                @include('includes.messages')
                <div class="card-body">
                    <h2 class="header-title">Schools' Qualifications Submission</h2>

                    <form method="post" action="{{ route('applicant.qualifications.store') }}" class="mt-5">
                        @csrf
                        <div class="form-group">
                            <label for="">Name / Certificate Type</label>
                            <input type="text" name="certificate_type" placeholder="e.g SSCE" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="">Awarding Institution</label>
                            <input type="text" name="awarding_institution" value="" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="">Qualification Obtained</label>
                            <input type="text" name="qualification_obtained" value="" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="">Class</label>
                            <input type="text" name="class" value="" class="form-control">
                        </div>
                        <input type="hidden" name="type" value="school">
                        <div class="form-group">
                            <label for="">Year Obtained</label>
                            <input type="date" name="year_obtained" value="" class="form-control">
                        </div>



                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>


                </div>
            </div>
        </div>

        {{-- table for professional qualifications --}}
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <h2 class="header-title">Submitted Schools' Qualifications</h2>

                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                        <tr>
                            <th>Cert Type</th>
                            <th>Awarding Institution</th>
                            <th>Qualification Obtained</th>
                            <th>Class</th>
                            <th>Year Obtained</th>
                            <th>Edit</th>
                        </tr>
                        </thead>


                        <tbody>

                        @foreach($qualifications as $qualification)
                            <tr>
                                <td>{{ $qualification->certificate_type  }}</td>
                                <td>{{ $qualification->awarding_institution  }}</td>
                                <td>{{ $qualification->qualification_obtained  }}</td>
                                <td>{{ $qualification->qualification_obtained ?? 'N/A'  }}</td>
                                <td>{{ \Carbon\Carbon::parse($qualification->year_obtained)->year  }}</td>
                                <td><button class="btn btn-success btn-sm">edit</button></td>

                            </tr>
                        @endforeach

                        </tbody>
                    </table>

                </div>
            </div>
        </div>

    </div>
@endsection
