@extends('layouts.setup')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Update Profile </div>

                <div class="card-body">

                    @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">

                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        @foreach ($errors->all() as $error)
                        <div class="">{{ $error }}</div>
                        @endforeach
                    </div>
                    @endif

                    @if(session()->has('success'))
                        <div class="alert alert-success">
                            {{ session()->get('success') }}
                        </div>
                    @endif

                    @if(session()->has('notice'))
                        <div class="alert alert-warning">
                            {{ session()->get('notice') }}
                        </div>
                    @endif

                    @if(session()->has('failure'))
                        <div class="alert alert-danger">
                            {{ session()->get('failure') }}
                        </div>
                    @endif


                    <form action="{{ route('update.student.profile') }}" method="post">
                        @csrf
                        <div class="form-group mb-4">
                            <label for="email">email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" placeholder="Email" name="email" value="{{ user()->email ?? ''}}">
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>

                            </span>
                            @enderror

                            <label for="email">Phone</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="name" placeholder="Phone" name="phone" value="{{ user()->phone_number ?? ''}}">
                            @error('phone')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror

                            <button type="submit" class="btn btn-primary mt-3">Update Profile</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
