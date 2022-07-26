@extends('layouts.setup')
@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                @include('includes.messages')
                <div class="card-body">

                    <h2 class="header-title">Submitted Card(s)</h2>
                    <div class="body table-responsive mt-4">
                        <table class="table table-hover table-bordered">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Card Pin</th>
                                <th>Card Serial No</th>
                                <th>Exam Type</th>
                                <th>Exam Year</th>
                                <th>Verification Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($cards as $card)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $card->card_pin }}</td>
                                <td>{{ $card->card_serial_no }}</td>
                                <td>{{ $card->exam_type }}</td>
                                <td>{{ $card->exam_year }}</td>
                                <td>{{ $card->verification_status }}</td>

                            </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                    <br>
                    <h2 class="header-title" id="addCard">Add Olevel Card Checking Details</h2>
                    <br>

                    <form method="post" action="{{ route('applicant.add_card.store') }}" class="mt-2">
                        @csrf
                        <div class="form-group">
                            <label for="">Exam Type </label>
                            <select class="form-control show-tick" name="exam_type">
                                <option value="">-- Select Examination type --</option>
                                <option value="WASCCE JAN/FEB">WASSCE JAN/FEB</option>
                                <option value="WASCCE MAY/JUNE">WASSCE MAY/JUNE</option>
                                <option value="WASCCE GCE NOV/DEC">WASSCE GCE NOV/DEC</option>
                                <option value="IGCE">IGCE</option>

                            </select>
                        </div>

                        <div class="form-group">
                            <label for="">Exam Year </label>
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
                        <div class="form-group">
                            <label for="">Card Pin</label>
                            <input type="text" name="card_pin" value="" class="form-control" placeholder="Card Pin">
                        </div>

                        <div class="form-group">
                            <label for="">Card Serial No</label>
                            <input type="text" name="card_serial_no" value="" class="form-control" placeholder="Card Serial No">
                        </div>

                        <div class="form-group">
                            <label for="referee_email">Select Submitted Olevel Sitting</label>
                            <select class="form-control show-tick" name="sitting">
                                <option value="">-------------</option>

                            @foreach($olevels as $olevel)
                                <option value="{{ $olevel->sitting }}">{{ $olevel->sitting . ' Sitting' }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Submit Verfication Card</button>
                    </form>


                </div>
            </div>
        </div>

    </div>
@endsection
