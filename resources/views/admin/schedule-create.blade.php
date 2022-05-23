@extends('layouts.setup')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Schedule Presentation</h4>
                    {{--                    <p class="card-title-desc">Fill this form to submit your zakat asset.--}}
                    {{--                    </p>--}}
                    @if ($errors->any())

                    @endif

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

                    <br>


                    <form class="needs-validation" action="{{ route('admin.schedule-store') }}" method="POST"
                          enctype="multipart/form-data">
                        @csrf

                        <div id="assetFormFields">
                            <div class="row">

                                <div class="col-md-4 mb-3">
                                    <label>Presentation Title</label>
                                    <select name="presentation" id="" class="form-control @error('presentation') is-invalid @enderror">
                                        <option value="">Selected Option</option>
                                        @foreach($presentations as $presentation)
                                        <option value="{{ $presentation->id }}">{{ $presentation->title }}</option>
                                        @endforeach
                                    </select>
                                    @error('presentation')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label>Date</label>
                                    <input type="date" name="schedule_date" id="" class="form-control @error('schedule_date') is-invalid @enderror">
                                    @error('schedule_date')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label>Time</label>
                                    <input type="time" name="schedule_time" id="" class="form-control @error('schedule_time') is-invalid @enderror">
                                    @error('schedule_time')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label>Location</label>
                                    <input type="text" name="location" id="" class="form-control @error('location') is-invalid @enderror" value="UMM">
                                    @error('location')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label>Virtual Meet Link</label>
                                    <input type="url" name="virtual_link" id="" class="form-control @error('virtual_link') is-invalid @enderror" placeholder="https://zoom.us/meet/umm">
                                    @error('virtual_link')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                            </div>
                        </div>

                        <div class="d-flex justify-content-left mb-3">

                            <button class="btn  btn-md btn-info" type="submit"> Schedule Presentation

                            </button>


                        </div>
                </div>

                </form>
            </div>
        </div>
    </div>
    </div>
    <!-- end row -->
@endsection


@section('js')


    <!-- form mask -->
    <script src="{{ asset('admin/assets/libs/inputmask/min/jquery.inputmask.bundle.min.js') }}"></script>

    <!-- form mask init -->
    <script src="{{ asset('admin/assets/js/pages/form-mask.init.js') }}"></script>
@endsection
