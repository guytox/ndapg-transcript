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
                            <label for="exam_body">Exam Body </label>
                            <select class="form-control show-tick" name="exam_body">
                                <option value="">-- Select Examination body --</option>
                                <option value="WAEC">WAEC</option>
                                <option value="WASCCE">WASCCE</option>
                                <option value="NECO">NECO</option>
                                <option value="NABTEB">NABTEB</option>
                                <option value="IGCE">IGCE</option>

                            </select>

                        </div>

                        <div class="form-group">
                            <label for="exam_type">Exam Type </label>
                            <select class="form-control show-tick" name="exam_type">
                                <option value="">-- Select Examination type --</option>
                                <option value="JAN/FEB">JAN/FEB</option>
                                <option value="MAY/JUNE">MAY/JUNE</option>
                                <option value="NOV/DEC">NOV/DEC</option>

                            </select>

                        </div>

                        <div class="form-group">
                            <label for="">Exam Year </label>
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
