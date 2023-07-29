<div class="form-group">
    {!! Form::label('staffId2', 'Select Lecturer') !!}
    <input wire:model="query" type="text" class="form-control" placeholder="Type any three letters">
    @if (strlen($query) >2)

        @if ($results->count() >0)
            <select  name="staffId"  class="form-control" >
                <option value="">---Select Lecturer---</option>
                @foreach ($results as $l)
                <option value="{{$l->id}}">{{$l->full_name}}</option>

                @endforeach

            </select>
        @else
        No Lecturers found to match {{$query}}
        @endif

    @endif

</div>
