<div class="form-group">
    {!! Form::label('College', 'College', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        <select name="branch_id" id="department" class="form-control" required>
            <option value="{{Session::get('activeBranch')}}"> 
                <?php
                    $branch_id = Session::get('activeBranch');; //Auth::user()->branch_id;
                    $selectedbranch= App\Branch:: select
                    ('branch_name','branch_title','id','org_id')
                    ->where('id', $branch_id)->get();
                    ?>
                    {{$selectedbranch[0]->branch_name}}

            </option>
           
            </select>
    </div>
<div>
    
    <input type="hidden" name="org_id" value="{{Auth::user()->org_id}}">
</div>
    
</div>
<div class="form-group">
    {!! Form::label('faculty', 'Faculty/Course', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::text('faculty', null, ["placeholder" => "e.g. BBA/Class1", "class" => "form-control border-form upper","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'faculty'])
    </div>

</div>

<div class="form-group">
    {!! Form::label('Form Fees', 'Form Fees', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::text('form_fees', null, ["placeholder" => "", "class" => "form-control border-form upper","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'form_fees'])
    </div>

</div>



@if(isset($data['semester']) && $data['semester']->count() > 0)
    <div class="form-group">
        <label class="col-sm-12 control-label align-left" for="status"> Semester/Section &nbsp;&nbsp;&nbsp;</label>
        @foreach($data['semester'] as $semester)
            <div class="col-sm-4">
                <div class="control-group">
                    <div class="checkbox">
                        <label>
                            @if (!isset($data['row']))
                                {!! Form::checkbox('semester[]', $semester->id, false, ['class' => 'ace']) !!}
                            @else
                                {!! Form::checkbox('semester[]', $semester->id, array_key_exists($semester->id, $data['active_semester']), ['class' => 'ace']) !!}
                            @endif
                            <span class="lbl"> {{ $semester->semester }} </span>
                        </label>
                    </div>
                </div>
            </div>
        @endforeach
        @include('includes.form_fields_validation_message', ['name' => 'semester[]'])
    </div>
@endif
