@extends('layouts.setup')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h3>{{ __('Upload Student Account Balances (file must be excel)') }}</h3></div>

                <div class="card-body">
                    <form action="{{ route('import.balances') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <label for="">File:</label>
                        <input type="file" name="file" id="">
                        <br><br>
                        <button type="submit">Upload Student Balances</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection




