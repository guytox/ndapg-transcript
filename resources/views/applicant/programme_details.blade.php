@extends('layouts.setup')
@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            @include('includes.messages')
            <div class="card-body">
                <h2 class="header-title">Programme Details</h2>


                {!! Form::open(['route' => 'applicant.add_programme.store', 'method' => 'POST']) !!}

                <div class="form-group">
                    {!! Form::label('programme', 'Select the Programme you want to apply for') !!}
                    {!! Form::select('programme', getAppliableProgrammeDropdown(), user()->profile->applicant_program ?? '', ['class' => 'form-control', 'required']) !!}
                </div>

                <div class="form-group">
                <label>Select Faculty </label>
                <select name="faculty" id="category_select" class="form-control" onchange="showSelectedOption(this.value)">
                <option> Select Faculty </option>
                    @foreach($faculties as $faculty)
                        <option value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                    @endforeach
                </select>
                </div>

                <br>
                <label>Department</label>
                <div id="selector" >
            
                                        <select name="department" id="departments" class="form-control @error('departments') is-invalid @enderror">
                                            <option value="">select department</option>
                                        </select>
                                    </div> <br>
                <label>Program</label>
                
                
                <div id="program_selector" >
            
                                        <select name="program" id="programs" class="form-control @error('program') is-invalid @enderror">
                                            <option value="">select program</option>
                                        </select>
                                    </div>

                <div class="form-group">
                    {!! Form::label('service_status', 'Are you a Serving Officer') !!}
                    {!! Form::select('service_status', [''=>'----', '1'=>'Yes', '0'=>'No'], user()->profile->is_serving_officer ??'', ['class' => 'form-control', 'required']) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('service_number', 'If you are a serving Officer, Enter your service number here') !!}
                    {!! Form::text('service_number', user()->profile->service_number ?? '', ['class' => 'form-control', 'placeholder'=>'123456', ]) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('service_rank', 'Enter Present Service Rank(If applicable)') !!}
                    {!! Form::text('service_rank', user()->profile->service_rank ?? '', ['class' => 'form-control', 'placeholder'=>'Enter Present Rank', ]) !!}
                </div>

                {!! Form::submit('Save Programme Details Details', ['class'=>'form-control btn btn-primary']) !!}



                {!! Form::close() !!}




            </div>
        </div>
    </div>

</div>
@endsection

@section('js')
<script type="text/javascript">
    let url  = window.location.protocol + '//' + window.location.host + '/departments-get/';

    function showSelectedOption(value)
    {
        console.log(url + value)
        let response = fetch(url + value).then(response => response.json()).then(data => insertDepartmentOptions(data))
        console.log(value)
    }

    function addDepartmentOptions(data, option, subCategory)
    {
        // var data = data;

        let select = "<select class='form-control @error('department') is-invalid @enderror' name='department' onchange='showSelectedProgramOption(this.value)'>";
        select += '<option value="">select department</option>';

        for (let i in data) {
            select += '<option value="' + data[i].id + '">' + data[i].name + '</option>';
        }
        select += '</select>';
        $("#selector").html(select);
    }

    function insertDepartmentOptions(data)
    {
        let  option = document.createElement("option");
        let subCategories = document.getElementById("departments");
        addDepartmentOptions(data, option, subCategories)
    }

    function showSelectedProgramOption(value)
    {
        url =  window.location.protocol + '//' + window.location.host + '/programmes-get/';
        console.log(url + value)
        let response = fetch(url + value).then(response => response.json()).then(data => insertProgramOptions(data))
        console.log(value)
    }

    function addProgramOptions(data, option, subCategory)
    {
        // var data = data;

        let select = "<select class='form-control @error('programme') is-invalid @enderror' name='programme'>";
        select += '<option value="">select programme</option>';

        for (let i in data) {
            select += '<option value="' + data[i].id + '">' + data[i].name + '</option>';
        }
        select += '</select>';
        $("#program_selector").html(select);
    }

    function insertProgramOptions(data)
    {
        let  option = document.createElement("option");
        let subCategories = document.getElementById("program");
        addProgramOptions(data, option, subCategories)
    }

</script>
@endsection