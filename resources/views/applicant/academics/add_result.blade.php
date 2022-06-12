@extends('layouts.setup')
@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                @include('includes.messages')
                <div class="card-body">
                    <h2 class="header-title">  Add Olevel Resukt</h2>
                    <p><strong>NB: If you add a sitting of a result that has been previously added it would be replaced by the new result entry</strong></p>
                    <br><br>

                    <form action="{{ route('applicant.add_result.store') }}" method="post">
                        {{ csrf_field() }}
                        <div class="clearfix row">
                            <div class="col-sm-4">
                                <select class="form-control show-tick" name="exam_type">
                                    <option value="">-- Select Examination type --</option>
                                    <option value="WASCCE JAN/FEB">WASSCE JAN/FEB</option>
                                    <option value="WASCCE MAY/JUNE">WASSCE MAY/JUNE</option>
                                    <option value="WASCCE GCE NOV/DEC">WASSCE GCE NOV/DEC</option>
                                    <option value="IGCE">IGCE</option>

                                </select>

                            </div>

                            <div class="col-sm-4">
                                <select class="form-control show-tick" name="exam_type">
                                    <option value="">-- Select Examination type --</option>
                                    <option value="WASCCE JAN/FEB">WASSCE JAN/FEB</option>
                                    <option value="WASCCE MAY/JUNE">WASSCE MAY/JUNE</option>
                                    <option value="WASCCE GCE NOV/DEC">WASSCE GCE NOV/DEC</option>
                                    <option value="IGCE">IGCE</option>

                                </select>

                            </div>

                            <div class="col-sm-4">
                                <select class="form-control show-tick" name="exam_year">
                                    <option value="">Select Your Examination year</option>

                                    <option value="2001">2001</option>
                                    <option value="2002">2002</option>
                                    <option value="2003">2003</option>
                                    <option value="2004">2004</option>
                                    <option value="2005">2005</option>
                                    <option value="2006">2006</option>
                                    <option value="2007">2007</option>
                                    <option value="2008">2008</option>
                                    <option value="2009">2009</option>
                                    <option value="2010">2010</option>
                                    <option value="2011">2011</option>
                                    <option value="2012">2012</option>
                                    <option value="2013">2013</option>
                                    <option value="2014">2014</option>
                                    <option value="2015">2015</option>
                                    <option value="2016">2016</option>
                                    <option value="2017">2017</option>
                                    <option value="2018">2018</option>
                                    <option value="2019">2019</option>


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
