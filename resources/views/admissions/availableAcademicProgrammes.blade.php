@extends('app-index')

@section('content')

    @include('includes.messages')

    <legend dir="ltr" style='font-variant: small-caps; font-size: larger;'>List of Available Academic Programmes</legend>
    <div style='font-size: medium'>
        @if ($faculties)
            @foreach ($faculties as $f )
                    <ol class="fac">
                        <li>
                            {{strtoUpper($f->name)}}
                            <ol class="dept">
                                @foreach ($f->department as $d )
                                    @if ($depts->contains($d->id))

                                        <li>Department of {{$d->name}}
                                            <ol class="prog">
                                                @foreach ($d->programs as $p)

                                                        @if ($p->category =='academic' && $p->is_advertised ==1)
                                                            <li>{{$p->name}}</li>
                                                        @endif

                                                @endforeach
                                            </ol>
                                        </li>

                                    @endif
                                @endforeach
                            </ol>
                        </li>
                    </ol>

            @endforeach

        @endif





    </div>

@endsection

