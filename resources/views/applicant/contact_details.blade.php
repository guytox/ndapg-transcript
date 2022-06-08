@extends('layouts.setup')
@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                @include('includes.messages')
                <div class="card-body">
                    <h2 class="header-title">Contact Details</h2>

                    <form method="post" action="{{ route('applicant.profile.store') }}" class="mt-5">
                        @csrf
                        <div class="form-group">
                            <label for="">Contact Address</label>
                            <input type="text" name="contact_address" value="{{ user()->profile->contact_address ?? '' }}" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="">Permanent Address</label>
                            <input type="text" name="permanent_home_address" value="{{ user()->profile->permanent_home_address ?? '' }}" class="form-control">
                        </div>

                        <button type="submit" class="btn btn-primary">Save Contact Details</button>
                    </form>


                </div>
            </div>
        </div>

    </div>
@endsection
