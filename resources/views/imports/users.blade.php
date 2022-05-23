@extends('layouts.setup')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h3>{{ __('Upload List of Students (must be excel file)git ') }}</h3></div>

                <div class="card-body">
                    <form action="{{ route('import.users') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <label for="">File:</label>
                        <input type="file" name="file" id="">
                        <br><br>
                        <button type="submit">Upload Student List</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection




