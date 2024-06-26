@extends('layouts.auth')

@include('includes.messages')

@section('content')

<div class="text-danger" ><marquee behavior="" direction="left"> <b> <font size="6">{{ getSiteNotification()}}</font> </b> </marquee></div>


<h5 class="mb-5 text-center">Sign in to your account.</h5>
<form class="form-horizontal" method="POST" action="{{ route('login') }}">
    @csrf
    <div class="row">
        <div class="col-md-12">
            <div class="form-group mb-4">
                <label for="username">Username</label>
                <input type="text" class="form-control @error('username') is-invalid @enderror" id="email" placeholder="Email Address" name="username" value="{{ old('username') }}">
                @error('username')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group mb-4">
                <label for="password">Password</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" placeholder="Enter your password" name="password">
                @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="customControlInline">
                        <label class="custom-control-label" for="customControlInline">Remember me</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="text-md-right mt-3 mt-md-0">
                        <a href="{{ route('password.email')}}" class="text-muted"><i class="mdi mdi-lock"></i> Forgot your
                            password?</a>
                    </div>
                </div>
            </div>
            <br>

                <div class="mt-4">
                    <button class="btn btn-success btn-block waves-effect waves-light" type="submit">Log In (For All Users and Returning Applicants) </button>
                </div>


            <hr>

            <div class="row">
            <div class="col-4">
                    <a href="{{ asset('assets/NDA_SPGS_OnlineApplicationGuide.pdf') }}" target="_blank" class="btn btn-secondary  waves-effect waves-light" type="submit">Transcript Request Guidelines </a>
            </div>
            <div class="col-8">
                <a href="{{ route('register') }}" class="btn btn-danger "><i class="mdi mdi-account-circle mr-1"></i>
                    Begin New Transcript Request (For New Applicants Only)</a>
            </div>
            </div>
        </div>
    </div>
</form>
@endsection
