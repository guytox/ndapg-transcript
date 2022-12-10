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
                    <input type="number" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="" placeholder="09088877651">
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
                        <input type="number" class="form-control @error('candidate_referee_relationship_years') is-invalid @enderror" id="years_knowing" name="candidate_referee_relationship_years" value="{{ 5 }}">
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
                    <input type="text" class="form-control @error('candidate_relationship') is-invalid @enderror" id="relationship" placeholder="Brother" name="candidate_relationship" value="">
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
                        <select name="candidate_morally_upright" id="morally_upright" class="form-control @error('candidate_morally_upright') is-invalid @enderror">
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
                <textarea name="general_comment" id="general_comments" cols="30" rows="5" class="form-control @error('general_comment') is-invalid @enderror"></textarea>
                @error('reason_for_rejecting_candidate_for_research')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <!-- <div class="mt-4">
                <button class="btn btn-success btn-block waves-effect waves-light" type="submit">Submit Confidential Information</button>
            </div> -->

            <button type="button" class="btn btn-primary btn-sm waves-effect waves-light" data-toggle="modal" data-target="#exampleModalScrollable">Preview Submission</button>
                                                        
                                                        <div class="modal fade bs-example-modal-lg" id="exampleModalScrollable" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                                                            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h2 class="modal-title mt-0 text-center" id="exampleModalScrollableTitle">Application Details Preview</h2>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p>Name: <span id="name_pr">Julius Idowu</span></p>
                                                                        <p>Phone:  <span id="phone_pr">20130204243</span></p>
                                                                        <p>Years of Knowing Candidate:  <span id="years_knowing_pr">NIL</span></p>
                                                                        <p>Relationship With Candidate: <span id="relationship_pr">NIL</span></p>
                                                                        <hr style="border-top:1px solid rgb(0, 0, 0)">
                                                                        <p>Intellectual ability: <span id="intellectual_ability_pr">NIL</span></p>
                                                                        <p>Academic study capacity:  <span id="academic_study_pr">NIL</span></p>
                                                                        <p>Independent study capacity:  <span id="independent_study_pr">NIL</span></p>
                                                                        <p>Imaginative thought capacity: <span id="ability_for_imaginative_pr">NIL</span></p>
                                                                        <hr style="border-top:1px solid rgb(0, 0, 0)">
                                                                        <p>Oral Expression: <span id="ability_for_oral_expression_in_english_pr">NIL</span></p>
                                                                        <p>Written expression in English:  <span id="ability_for_written_expression_in_english_pr">NIL</span></p>
                                                                        <p>Academic rank over 5 years (1-100):  <span id="candidate_rank_academically_among_students_in_last_five_years_pr">NIL</span></p>
                                                                        <hr style="border-top:1px solid rgb(0, 0, 0)">
                                                                        <p>Morally Upright: <span id="morally_upright_pr">NIL</span></p>
                                                                        <p>Emotionally Stable :  <span id="candidate_emotionally_stable_pr">NIL</span></p>
                                                                        <p>Physically Fit:  <span id="candidate_physically_fit_pr">NIL</span></p>
                                                                        <p>Can you Accept Candidate as a researcher:  <span id="accept_candidate_for_research_pr">NIL</span></p>
                                                                        <hr style="border-top:1px solid rgb(0, 0, 0)">
                                                                        <p>General comments: <span id="general_comments_pr">NIL</span></p>

                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                        <button class="btn btn-success" type="submit">Submit Confidential Information</button>

                                                                        </div>
                                                                </div><!-- /.modal-content -->
                                                            </div><!-- /.modal-dialog -->
                                                        </div><!-- /.modal -->

    </div>
</div>


        </div>
    </div>
</form>

<!--  Modal content for the above example -->
<!-- Small modal -->
<style>
    .modal-dialog {
  width: 98%;
  height: 92%;
  padding: 0;
}
</style>

<!-- end Account pages -->

<!-- JAVASCRIPT -->
<script src="{{ asset('admin/assets/libs/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/metismenu/metisMenu.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/node-waves/waves.min.js') }}"></script>

<script src="{{ asset('admin/assets/js/app.js') }}"></script>

<script type="text/javascript">
 document.getElementById('name_pr').innerHTML = document.getElementById('name').value;
 document.getElementById('phone_pr').innerHTML = document.getElementById('phone').value;
 document.getElementById('years_knowing_pr').innerHTML = document.getElementById('years_knowing').value;
 document.getElementById('relationship_pr').innerHTML = document.getElementById('relationship').value;
 document.getElementById('intellectual_ability_pr').innerHTML = document.getElementById('intellectual_ability').value;
 document.getElementById('academic_study_pr').innerHTML = document.getElementById('capacity_for_persistent_academic_study').value;
 document.getElementById('independent_study_pr').innerHTML = document.getElementById('capacity_for_independent_academic_study').value;
 document.getElementById('ability_for_imaginative_pr').innerHTML = document.getElementById('ability_for_imaginative_thought').value;
 document.getElementById('ability_for_oral_expression_in_english_pr').innerHTML = document.getElementById('ability_for_oral_expression_in_english').value;
 document.getElementById('ability_for_written_expression_in_english_pr').innerHTML = document.getElementById('ability_for_written_expression_in_english').value;
 document.getElementById('candidate_rank_academically_among_students_in_last_five_years_pr').innerHTML = document.getElementById('candidate_rank_academically_among_students_in_last_five_years').value;
 document.getElementById('morally_upright_pr').innerHTML = document.getElementById('morally_upright').value;
 document.getElementById('candidate_emotionally_stable_pr').innerHTML = document.getElementById('candidate_emotionally_stable').value;
 document.getElementById('candidate_physically_fit_pr').innerHTML = document.getElementById('candidate_physically_fit').value;
 document.getElementById('accept_candidate_for_research_pr').innerHTML = document.getElementById('accept_candidate_for_research').value;
 document.getElementById('general_comments_pr').innerHTML = document.getElementById('general_comments').value;
</script>

</body>

</html>
