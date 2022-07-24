<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Login | Apaxy - Responsive Bootstrap 4 Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesdesign" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- Bootstrap Css -->
    <link href="{{ asset('admin/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('admin/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('admin/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />

</head>

<body class="bg-primary bg-pattern">
<div class="home-btn d-none d-sm-block">
    <a href="index.html"><i class="mdi mdi-home-variant h2 text-white"></i></a>
</div>

<div class="account-pages my-5 pt-5">
    <div class="container">
{{--        <div class="row">--}}
{{--            <div class="col-lg-12">--}}
{{--                <div class="text-center mb-5">--}}
{{--                    <a href="index.html" class="logo"><img src="{{ asset('assets/images/logo-light.png') }}"--}}
{{--                                                           height="24" alt="logo"></a>--}}
{{--                    <h5 class="font-size-16 text-white-50 mb-4">Responsive Bootstrap 4 Admin Dashboard</h5>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
        <!-- end row -->

@include('includes.messages')

<div class="row justify-content-center">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body p-4">
                <div class="p-2">


<h5 class="mb-5 text-center">Referee Confidential Form.</h5>
<form class="form-horizontal" method="POST" action="{{ route('referee.update_details', $details->uid) }}">
    @csrf
    <div class="row">

        <div class="col-md-12">
            <div class="row col-md-12">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control @error('referee_name') is-invalid @enderror" id="name"  name="referee_name" value="{{ $details->name ?? '' }}">
                    @error('referee_name')
                    <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                    @enderror
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="username">Phone</label>
                    <input type="number" class="form-control @error('phone') is-invalid @enderror" id="email" name="phone" value="" placeholder="09088877651">
                    @error('phone')
                    <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                    @enderror
                </div>
            </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="username">Years of Knowing Candidate</label>
                        <input type="number" class="form-control @error('candidate_referee_relationship_years') is-invalid @enderror" id="email" name="candidate_referee_relationship_years" value="{{ 5 }}">
                        @error('candidate_referee_relationship_years')
                        <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                        @enderror
                    </div>
                </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="candidate_relationship">Relationship With Candidate</label>
                    <input type="text" class="form-control @error('candidate_relationship') is-invalid @enderror" id="email" placeholder="Brother" name="candidate_relationship" value="">
                    @error('candidate_relationship')
                    <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                    @enderror
                </div>
            </div>
            </div>

            <hr>

            <div class="row col-md-12">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="intellectual_ability">Intellectual Ability</label>
                        <input type="text" class="form-control @error('intellectual_ability') is-invalid @enderror" id="intellectual_ability"  name="intellectual_ability" placeholder="Good">
                        @error('intellectual_ability')
                        <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="capacity_for_persistent_academic_study">Academic study Capacity</label>
                        <input type="text" class="form-control @error('capacity_for_persistent_academic_study') is-invalid @enderror" id="capacity_for_persistent_academic_study" name="capacity_for_persistent_academic_study" value="" placeholder="Good">
                        @error('capacity_for_persistent_academic_study')
                        <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="capacity_for_independent_academic_study">Independent Study Capacity</label>
                        <input type="number" class="form-control @error('capacity_for_independent_academic_study') is-invalid @enderror" id="capacity_for_independent_academic_study" name="capacity_for_independent_academic_study" value="">
                        @error('capacity_for_independent_academic_study')
                        <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="ability_for_imaginative_thought">Imaginative thought ability</label>
                        <input type="text" class="form-control @error('ability_for_imaginative_thought') is-invalid @enderror" id="ability_for_imaginative_thought" placeholder="Good" name="ability_for_imaginative_thought" value="">
                        @error('ability_for_imaginative_thought')
                        <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                        @enderror
                    </div>
                </div>
            </div>

            <hr>

            <div class="row col-md-12">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="ability_for_oral_expression_in_english">Oral expression in English</label>
                        <input type="text" class="form-control @error('ability_for_oral_expression_in_english') is-invalid @enderror" id="ability_for_oral_expression_in_english"  name="ability_for_oral_expression_in_english" placeholder="Good">
                        @error('ability_for_oral_expression_in_english')
                        <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="ability_for_written_expression_in_english">Written expression in English</label>
                        <input type="text" class="form-control @error('ability_for_written_expression_in_english') is-invalid @enderror" id="ability_for_written_expression_in_english" name="ability_for_written_expression_in_english" value="" placeholder="Good">
                        @error('ability_for_written_expression_in_english')
                        <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="candidate_rank_academically_among_students_in_last_five_years">Academic rank over 5 years (1-100)</label>
                        <input type="number" class="form-control @error('candidate_rank_academically_among_students_in_last_five_years') is-invalid @enderror" id="candidate_rank_academically_among_students_in_last_five_years" name="candidate_rank_academically_among_students_in_last_five_years" value="">
                        @error('candidate_rank_academically_among_students_in_last_five_years')
                        <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                        @enderror
                    </div>
                </div>

            </div>

            <hr>

            <div class="row col-md-12">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="username">Morally Upright ?</label>
                        <select name="candidate_morally_upright" id="" class="form-control @error('candidate_morally_upright') is-invalid @enderror">
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                        @error('candidate_morally_upright')
                        <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="candidate_emotionally_stable">Emotionally stable ?</label>
                        <select name="candidate_emotionally_stable" id="candidate_emotionally_stable" class="form-control @error('candidate_emotionally_stable') is-invalid @enderror">
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                        @error('candidate_emotionally_stable')
                        <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="candidate_physically_fit">Physically  Fit ?</label>
                        <select name="candidate_physically_fit" id="candidate_physically_fit" class="form-control @error('candidate_physically_fit') is-invalid @enderror">
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                        @error('candidate_physically_fit')
                        <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="accept_candidate_for_research">Can you accept as a researcher ?</label>
                        <select name="accept_candidate_for_research" id="accept_candidate_for_research" class="form-control @error('accept_candidate_for_research') is-invalid @enderror">
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                        @error('capacity_for_independent_academic_study')
                        <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                        @enderror
                    </div>
                </div>

            </div>

            <div class="form-group ">
                <label for="reason_for_rejecting_candidate_for_research">If you are rejecting candidate for research state your reason</label>
                <input type="text" class="form-control @error('reason_for_rejecting_candidate_for_research') is-invalid @enderror" id="reason_for_rejecting_candidate_for_research" placeholder="Reason for rejecting candidate" name="reason_for_rejecting_candidate_for_research">
                @error('reason_for_rejecting_candidate_for_research')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group ">
                <label for="general_comment">General Comment for Candidate </label>
                <textarea name="general_comment" id="" cols="30" rows="5" class="form-control @error('general_comment') is-invalid @enderror"></textarea>
                @error('reason_for_rejecting_candidate_for_research')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="mt-4">
                <button class="btn btn-success btn-block waves-effect waves-light" type="submit">Submit Confidential Information</button>
            </div>

        </div>
    </div>
</form>

                </div>
            </div>
        </div>
    </div>
</div>

    </div>
</div>
<!-- end Account pages -->

<!-- JAVASCRIPT -->
<script src="{{ asset('admin/assets/libs/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/metismenu/metisMenu.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/node-waves/waves.min.js') }}"></script>

<script src="{{ asset('admin/assets/js/app.js') }}"></script>

</body>

</html>
