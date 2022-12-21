@extends('layouts.auth')

@section('content')
<h5 class="mb-5 text-center">Begin Applicaition <span class="text text-success"><i>(Create and Verify Account)</i></span> </h5>


<p class="text text-danger">NOTE:****** You are required to create and account and verify your email, then login to complete the application process</p>
<form class="form-horizontal" method="POST" action="{{ route('register') }}">
    @csrf
    <div class="row">
        <div class="col-md-12">
            <div class="form-group mb-4">
                <label for="name">Full Name</label>
                <input type="name" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Full Name" name="name" value="{{ old('name') }}">
                @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group mb-4">
                <label for="email">Email Address</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" placeholder="Email Address" name="email" value="{{ old('email') }}">
                @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>


            <div class="form-group mb-4">
                <label for="password">Password</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror

            </div>

            <div class="form-group mb-4">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation">
                @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    {{-- <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="customControlInline">
                        <label class="custom-control-label" for="customControlInline">Remember me</label>
                    </div> --}}
                </div>
                <div class="col-md-6">
                    {{-- <div class="text-md-right mt-3 mt-md-0">
                        <a href="#" class="text-muted"><i class="mdi mdi-lock"></i> Forgot your
                            password?</a>
                    </div> --}}
                </div>
            </div>
            <div class="mt-4">
                <button class="btn btn-success btn-block waves-effect waves-light" type="submit">Create Account and Login to continue Application</button>
            </div>
            <hr>
            <a href="{{ asset('assets/img/logo/Logo_mobile_dark.png') }}" target="_blank" class="btn btn-secondary  waves-effect waves-light" type="submit">Application Guidelines </a>
            <hr>
            <div class="mt-4 text-center">
                <a href="{{ route('login') }}" class=" text-muted"><i class="mdi mdi-account-circle mr-1"></i>
                    Already have an account
                    ? Go Back to Login Page</a>
            </div>
        </div>
    </div>
</form>
@endsection
