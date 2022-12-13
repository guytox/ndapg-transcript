@extends('layouts.setup')
@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">

                <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                    @endif

                    @if (session('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('error') }}
                    </div>
                    @endif
                    <h2 class="header-title">Add Confidential Referee</h2>



                    <form method="post" action="{{ route('applicant.referee.store') }}" class="mt-5">
                        @csrf
                        <div class="form-group">
                            <label for="">Referee Name</label>
                            <input type="text" name="referee_name" value="" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="referee_email">Referee Email</label>
                            <input type="email" name="referee_email" value="" class="form-control">
                        </div>

                        <button type="submit" class="btn btn-primary">Submit Confidential Referee</button>
                    </form>


                </div>
            </div>

            <hr>

            <div class="card">

                <div class="card-body">

                    <h2 class="header-title">List of Nominated Referees</h2>

                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>s/n</th>
                                <th>Name</th>
                                <th>email</th>
                                <th>gsm</th>
                                <th>Respnse Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($referees)
                            @php
                                $sn = 1;
                            @endphp
                                @foreach ($referees as $v)

                                <tr>
                                    <td> {{$sn}}</td>
                                    <td>{{$v->name}}</td>
                                    <td>{{$v->email}}</td>
                                    <td>{{$v->phone}}</td>
                                    @if ($v->is_filled ==1)
                                        <td class="text text-success">Referee has Responded</td>
                                    @else
                                        <td class="text text-danger">Not Responded <a href="{{route('delete.referee',['uid'=>$v->uid])}}" class="btn btn-danger"> Remove</a></td>
                                    @endif

                                </tr>

                                @php
                                    $sn++
                                @endphp

                                @endforeach
                            @else
                            <tr>
                                <td colspan="5"> No Referees Nominated Yet</td>
                            </tr>

                            @endif


                        </tbody>
                    </table>


                </div>
            </div>

        </div>

    </div>
@endsection
