<div>

    <input wire:model="query" type="text" class="form-control" placeholder="Type any three letters">
    {!! Form::label('progId', 'Select Program') !!}
    @if (strlen($query) >2)

        @if ($results->count() >0)
        {!! Form::select('progId', $results, '', ['class'=>'form-control']) !!}
            {{-- <select  name="progId"  id="progId" class="form-control" >
                <option value="">---Select Programme---</option>
                @foreach ($results as $l)
                <option value="{{$l->id}}">{{$l->name}}</option>

                @endforeach

            </select> --}}
        @else
        No Programs found to match {{$query}}
        @endif

    @endif
</div>
