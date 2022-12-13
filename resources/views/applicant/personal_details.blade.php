@extends('layouts.setup')
@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            @include('includes.messages')
            <div class="card-body">
                <h2 class="header-title">Personal Details</h2>

                <form method="post" action="{{ route('applicant.profile.store') }}" class="mt-5">
                    @csrf
                    <div class="form-group">
                        <label for="">Full Name</label>
                        <input type="text" name="name" value="{{ user()->name }}" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="">Nationality</label>
                        <input type="text" name="nationality" value="{{ user()->profile->nationality ?? '' }}" class="form-control">
                    </div>


                    <div class="form-group">
                        <label for="status">Marital Status</label>
                        <select name="marital_status" id="status" class="form-control">
                            @if(isset(user()->profile->marital_status))
                            <option value="{{ user()->profile->marital_status }}" selected>{{ user()->profile->marital_status}} (current)</option>
                            @endif
                            <option value="married">Married</option>
                            <option value="single">Single</option>
                            <option value="divorced">Divorced</option>

                        </select>
                    </div>


                    <div class="form-group">
                        <label for="state">State</label>
                        <select name="state_of_origin" id="state" class="form-control">
                            @if(isset(user()->profile->state_id))
                            <option value="{{ user()->profile->state_id}}" selected>{{ getStateNameById(user()->profile->state_id)  }} (current) </option>
                            @endif
                            <option value="38">Not a Nigerian State</option>
                            @foreach ($states as $state)
                            <option value="{{ $state->id }}">{{ $state->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="local_government">Local Government</label>
                        <input type="text" name="local_government" value="{{ user()->profile->local_government ?? '' }}" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="town">Town</label>
                        <input type="text" name="town" value="{{ user()->profile->town ?? '' }}" class="form-control">
                    </div>


                    <button type="submit" class="btn btn-primary">Save Personal Details</button>
                </form>


            </div>
        </div>
    </div>

</div>
@endsection
