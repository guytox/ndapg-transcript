@extends('layouts.setup')
@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
        
                <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                    @endif

                    @if (session('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('error') }}
                    </div>
                    @endif
                    <h2 class="header-title">Add Confidential Referee</h2>



                    <form method="post" action="{{ route('applicant.referee.store') }}" class="mt-5">
                        @csrf
                        <div class="form-group">
                            <label for="">Referee Name</label>
                            <input type="text" name="referee_name" value="" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="referee_email">Referee Email</label>
                            <input type="email" name="referee_email" value="" class="form-control">
                        </div>

                        <button type="submit" class="btn btn-primary">Submit Confidential Referee</button>
                    </form>


                </div>
            </div>
        </div>

    </div>
@endsection
