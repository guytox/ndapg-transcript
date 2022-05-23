@extends('layouts.setup')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Assign Presentation Reviewer</h4>
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


                    <form class="needs-validation" action="{{ route('admin.store.assign-reviewer', $presentation->id) }}" method="POST"
                          enctype="multipart/form-data">
                        @csrf

                        <div id="assetFormFields">
                            <div class="row">

                                <div class="col-md-4 mb-3">
                                    <label>Presentation Title</label>
                                    <br>
                                    <p>{{ $presentation->title }}</p>
                                    @error('presentation')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label>Presentation Details</label>
                                    <br>
                                    <p>{{ $presentation->description }}</p>
                                    @error('presentation')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label>Presentater</label>
                                    <br>
                                    <p>{{ $presentation->user->name  }} {{ '<' . $presentation->user->email . '>' }}</p>
                                    @error('presentation')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label>Date Added</label>
                                    <br>
                                    <p>{{ $presentation->created_at->diffForHumans() }}</p>
                                </div>



                                <div class="col-md-4 mb-3">
                                    <label>Sub Category</label>
                                    <br>
                                    <p>{{ $presentation->subCategory->name }}</p>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label>Category</label>
                                    <br>
                                    <p>{{ $presentation->subCategory->category->name }}</p>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label>Select Reviewer</label>
                                    <select name="reviewer" id="" class="form-control @error('reviewer') is-invalid @enderror">
                                        <option value="">Selected Option</option>
                                        @foreach($reviewers as $reviewer)
                                            <option value="{{ $reviewer->id }}">{{ $reviewer->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('reviewer')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                            </div>
                        </div>

                        <div class="d-flex justify-content-left mb-3">

                            <button class="btn  btn-md btn-info" type="submit"> Assign Reviewer

                            </button>


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
