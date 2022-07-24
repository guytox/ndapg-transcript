@extends('layouts.setup')

@section('content')

    <h1>List of Registration Outlines</h1>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-4"></h4>

                    @include('includes.messages')

                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead>
                                <tr>

                                    <th scope="col">Title</th>
                                    <th scope="col">Min. Credits</th>
                                    <th scope="col">Max. Credits</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>

                                    <tr>
                                        <td>{{$currentOutline->title}}</td>
                                        <td>{{$currentOutline->minRegCredits}}</td>
                                        <td>{{$currentOutline->maxRegCredits}}</td>
                                        <td>
                                            <a class="btn btn-primary" href="{{ route('coursereg.show', ['coursereg'=>$currentOutline->id]) }}">Register</a>
                                        </td>
                                    </tr>
                            </tbody>
                        </table>



                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end row -->

    <script>
        @if(session()->has('error'))
          alert('{{session()->get('error')}}')
        @endif
    </script>

@endsection
