@extends('layouts.setup')
@section('content')
    <div class="row">
        <div class="col-xl-4">
            <div class="card">
                @include('includes.messages')
                <div class="card-body">
                    <h2 class="header-title">Professional Qualifications Submission</h2>

                    <form method="post" action="{{ route('applicant.qualifications.store') }}" class="mt-5">
                        @csrf
                        <div class="form-group">
                            <label for="">Name / Certificate Type</label>
                            <input type="text" name="certificate_type" placeholder="e.g MNSE, ANAN, ICAN" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="">Awarding Institution</label>
                            <input type="text" name="awarding_institution" value="" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="">Certification No</label>
                            <input type="text" name="certificate_no" value="" class="form-control">
                        </div>
                        <input type="hidden" name="type" value="professional">
                        <div class="form-group">
                            <label for="">Issue Date</label>
                            <input type="date" name="year_obtained" value="" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="">Expiry Date</label>
                            <input type="date" name="expiry_date" value="" class="form-control">
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
                    <h2 class="header-title">Submitted Professional Qualifications</h2>

                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Awarding Institution</th>
                            <th>Certification No</th>
                            <th>Issue Date</th>
                            <th>Expiry Date</th>
                            <th>Edit</th>
                        </tr>
                        </thead>


                        <tbody>

                        @foreach($qualifications as $qualification)
                            <tr>
                                <td>{{ $qualification->certificate_type  }}</td>
                                <td>{{ $qualification->awarding_institution  }}</td>
                                <td>{{ $qualification->certificate_no }}</td>
                                <td>{{ \Carbon\Carbon::parse($qualification->year_obtained)->year  }}</td>
                                <td>{{ \Carbon\Carbon::parse($qualification->expiry_date)->year  }}</td>
                                <td><a class="btn btn-success btn-sm" href="{{route('applicant.delete.qualification',['id'=>$qualification->uid])}}">Remove</a></td>

                            </tr>
                        @endforeach

                        </tbody>
                    </table>

                </div>
            </div>
        </div>

    </div>
@endsection
