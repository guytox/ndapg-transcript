@extends('layouts.setup')
@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                @include('includes.messages')
                <div class="card-body">
                    <h2 class="header-title">  Add O-level Result</h2>
                    <p><strong>NB: If you add a sitting of a result that has been previously added it would be replaced by the new result entry</strong></p>
                    <br><br>

                    <form action="{{ route('applicant.add_result.store') }}" method="post">
                        {{ csrf_field() }}
                        <div class="clearfix row">
                            <div class="col-sm-4">
                                <select class="form-control show-tick" name="exam_body">
                                    <option value="">-- Select Examination body --</option>
                                    <option value="WAEC">WAEC</option>
                                    <option value="WASCCE">WASCCE</option>
                                    <option value="NECO">NECO</option>
                                    <option value="NABTEB">NABTEB</option>
                                    <option value="IGCE">IGCE</option>

                                </select>

                            </div>

                            <div class="col-sm-4">
                                <select class="form-control show-tick" name="exam_type">
                                    <option value="">-- Select Examination type --</option>
                                    <option value="JAN/FEB">JAN/FEB</option>
                                    <option value="MAY/JUNE">MAY/JUNE</option>
                                    <option value="NOV/DEC">NOV/DEC</option>

                                </select>

                            </div>

                            <div class="col-sm-4">
                                <select class="form-control show-tick" name="exam_year">
                                    <option value="">Select Your Examination year</option>
                                    @php
                                        $year = 1950;
                                    @endphp

                                    @while ($year <=2022)

                                    <option value="{{$year}}">{{$year}}</option>

                                    @php
                                        $year ++;
                                    @endphp

                                    @endwhile




                                </select>
                            </div>

                        </div>

                        <div class="body mt-4">

                            <div class="form-group">
                                <div>
                                    <h5 class="font-size-14 mb-3">Choose Sitting</h5>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="sitting" id="sitting1" value="first" >
                                        <label class="form-check-label" for="sitting1">
                                            First Sitting
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="sitting" id="sitting2" value="second">
                                        <label class="form-check-label" for="sitting2">
                                            Second Sitting
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <table class="table table-hover table-bordered">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Subject</th>
                                    <th>Grade</th>
                                </tr>
                                </thead>
                                <tbody>

                                <tr>
                                    <td>1</td>
                                    <td><strong><i class="zmdi zmdi-check"></i> English Language</strong></td>
                                    <td>
                                        <select class="form-control show-tick ms select2" name="english">
                                            <option value="">-- Select Grade --</option>
                                            <option value="A1">A1</option>
                                            <option value="B2">B2</option>
                                            <option value="B3">B3</option>
                                            <option value="C4">C4</option>
                                            <option value="C5">C5</option>
                                            <option value="C6">C6</option>
                                            <option value="D7">D7</option>
                                            <option value="E8">E8</option>
                                            <option value="F9">F9</option>
                                            <option value="AR">AR</option>
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <td>2</td>
                                    <td><strong><i class="zmdi zmdi-check"></i> Mathematics</strong></td>
                                    <td>
                                        <select class="form-control show-tick ms select2" name="mathematics">
                                            <option value="">-- Select Grade --</option>
                                            <option value="A1">A1</option>
                                            <option value="B2">B2</option>
                                            <option value="B3">B3</option>
                                            <option value="C4">C4</option>
                                            <option value="C5">C5</option>
                                            <option value="C6">C6</option>
                                            <option value="D7">D7</option>
                                            <option value="E8">E8</option>
                                            <option value="F9">F9</option>
                                            <option value="AR">AR</option>
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <td>3</td>
                                    <td>
                                        <select class="form-control show-tick ms select2" name="subject_3">
                                            <option value="">-- Select Subject --</option>
                                            <option value="CHEMISTRY">CHEMISTRY</option>
                                            <option value="PHYSICS">PHYSICS</option>
                                            <option value="BIOLOGY">BIOLOGY</option>
                                            <option value="COMMERCE">COMMERCE</option>
                                            <option value="AGRICULTURE SCIENCE">AGRICULTURE SCIENCE</option>
                                            <option value="ECONOMICS">ECONOMICS</option>
                                            <option value="FINANCIAL ACCOUNTING">FINANCIAL ACCOUNTING</option>
                                            <option value="LITERATURE IN ENGLISH">LITERATURE IN ENGLISH</option>
                                            <option value="FURTHER MATHEMATICS">FURTHER MATHEMATICS</option>
                                            <option value="COMPUTER STUDIES">COMPUTER STUDIES</option>
                                            <option value="MARKETING">MARKETING</option>
                                            <option value="CIVIC EDUCATION">CIVIC EDUCATION</option>
                                            <option value="HISTORY">HISTORY</option>
                                            <option value="GOVERNMENT ">GOVERNMENT </option>
                                            <option value="GEOGRAPHY">GEOGRAPHY</option>
                                            <option value="YORUBA">YORUBA</option>
                                            <option value="CHRISTAIN RELIGIOUS STUDIES">CHRISTAIN RELIGIOUS STUDIES</option>
                                            <option value="ISLAMIC RELIGIOUS STUDIES">ISLAMIC RELIGIOUS STUDIES</option>
                                            <option value="DATA PROCESSING">DATA PROCESSING</option>
                                            <option value="HOME MANAGEMENT">HOME MANAGEMENT</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control show-tick ms select2" data-placeholder="Select" name="subject_3_grade">
                                            <option value="">-- Select Grade --</option>
                                            <option value="A1">A1</option>
                                            <option value="B2">B2</option>
                                            <option value="B3">B3</option>
                                            <option value="C4">C4</option>
                                            <option value="C5">C5</option>
                                            <option value="C6">C6</option>
                                            <option value="D7">D7</option>
                                            <option value="E8">E8</option>
                                            <option value="F9">F9</option>
                                            <option value="AR">AR</option>
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <td>4</td>
                                    <td><select class="form-control show-tick ms select2" name="subject_4">
                                            <option value="">-- Select Subject --</option>
                                            <option value="CHEMISTRY">CHEMISTRY</option>
                                            <option value="PHYSICS">PHYSICS</option>
                                            <option value="BIOLOGY">BIOLOGY</option>
                                            <option value="COMMERCE">COMMERCE</option>
                                            <option value="AGRICULTURE SCIENCE">AGRICULTURE SCIENCE</option>
                                            <option value="ECONOMICS">ECONOMICS</option>
                                            <option value="FINANCIAL ACCOUNTING">FINANCIAL ACCOUNTING</option>
                                            <option value="LITERATURE IN ENGLISH">LITERATURE IN ENGLISH</option>
                                            <option value="FURTHER MATHEMATICS">FURTHER MATHEMATICS</option>
                                            <option value="COMPUTER STUDIES">COMPUTER STUDIES</option>
                                            <option value="MARKETING">MARKETING</option>
                                            <option value="CIVIC EDUCATION">CIVIC EDUCATION</option>
                                            <option value="HISTORY">HISTORY</option>
                                            <option value="GOVERNMENT ">GOVERNMENT </option>
                                            <option value="GEOGRAPHY">GEOGRAPHY</option>
                                            <option value="YORUBA">YORUBA</option>
                                            <option value="CHRISTAIN RELIGIOUS STUDIES">CHRISTAIN RELIGIOUS STUDIES</option>
                                            <option value="ISLAMIC RELIGIOUS STUDIES">ISLAMIC RELIGIOUS STUDIES</option>
                                            <option value="DATA PROCESSING">DATA PROCESSING</option>
                                            <option value="HOME MANAGEMENT">HOME MANAGEMENT</option>
                                        </select></td>
                                    <td>
                                        <select class="form-control show-tick ms select2" name="subject_4_grade">
                                            <option value="">-- Select Grade --</option>
                                            <option value="A1">A1</option>
                                            <option value="B2">B2</option>
                                            <option value="B3">B3</option>
                                            <option value="C4">C4</option>
                                            <option value="C5">C5</option>
                                            <option value="C6">C6</option>
                                            <option value="D7">D7</option>
                                            <option value="E8">E8</option>
                                            <option value="F9">F9</option>
                                            <option value="AR">AR</option>
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <td>5</td>
                                    <td>
                                        <select class="form-control show-tick ms select2" name="subject_5">
                                            <option value="">-- Select Subject --</option>
                                            <option value="CHEMISTRY">CHEMISTRY</option>
                                            <option value="PHYSICS">PHYSICS</option>
                                            <option value="BIOLOGY">BIOLOGY</option>
                                            <option value="COMMERCE">COMMERCE</option>
                                            <option value="AGRICULTURE SCIENCE">AGRICULTURE SCIENCE</option>
                                            <option value="ECONOMICS">ECONOMICS</option>
                                            <option value="FINANCIAL ACCOUNTING">FINANCIAL ACCOUNTING</option>
                                            <option value="LITERATURE IN ENGLISH">LITERATURE IN ENGLISH</option>
                                            <option value="FURTHER MATHEMATICS">FURTHER MATHEMATICS</option>
                                            <option value="COMPUTER STUDIES">COMPUTER STUDIES</option>
                                            <option value="MARKETING">MARKETING</option>
                                            <option value="CIVIC EDUCATION">CIVIC EDUCATION</option>
                                            <option value="HISTORY">HISTORY</option>
                                            <option value="GOVERNMENT ">GOVERNMENT </option>
                                            <option value="GEOGRAPHY">GEOGRAPHY</option>
                                            <option value="YORUBA">YORUBA</option>
                                            <option value="CHRISTAIN RELIGIOUS STUDIES">CHRISTAIN RELIGIOUS STUDIES</option>
                                            <option value="ISLAMIC RELIGIOUS STUDIES">ISLAMIC RELIGIOUS STUDIES</option>
                                            <option value="DATA PROCESSING">DATA PROCESSING</option>
                                            <option value="HOME MANAGEMENT">HOME MANAGEMENT</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control show-tick ms select2" name="subject_5_grade">
                                            <option value="">-- Select Grade --</option>
                                            <option value="A1">A1</option>
                                            <option value="B2">B2</option>
                                            <option value="B3">B3</option>
                                            <option value="C4">C4</option>
                                            <option value="C5">C5</option>
                                            <option value="C6">C6</option>
                                            <option value="D7">D7</option>
                                            <option value="E8">E8</option>
                                            <option value="F9">F9</option>
                                            <option value="AR">AR</option>
                                        </select>
                                    </td>
                                </tr>

                                </tbody>
                            </table>
                        </div>
                        <div class="text-center">
                            <br><br>
                            <button class="btn btn-round btn-primary btn-block">Submit O level Result</button>
                        </div>
                    </form>


                </div>
            </div>
        </div>

    </div>
@endsection
