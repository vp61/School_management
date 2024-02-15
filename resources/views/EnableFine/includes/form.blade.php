<div class="form-group">
    {!! Form::label('title', 'Due Month', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::select('due_month_id',$dropdown['due-month'], null, [ "class" => "form-control border-form getHead","required",'id'=>'month_id']) !!}
    </div>
     {!! Form::label('title',env('course_label'), ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::select('faculty_id',$dropdown['faculty'], null, [ "class" => "form-control border-form getHead","required",'id'=>'faculty_id']) !!}
    </div>
    {!! Form::label('title','Fee Head', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::select('fee_head_id',[''=>'Select'], null, [ "class" => "form-control border-form","required",'id'=>'head_id']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('title', 'Start From', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::date('start_date', null, [ "class" => "form-control border-form","required"]) !!}
    </div>
     {!! Form::label('title','Daily Fine', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::number('daily_fine', null, [ "class" => "form-control border-form ","required",'min'=>1]) !!}
    </div>
    {!! Form::label('title','Monthly Fine', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::number('monthly_fine', null, [ "class" => "form-control border-form ","required",'min'=>1]) !!}
    </div>
</div>
<div class="form-group">
    
     {!! Form::label('title','On Minimum Due of', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::number('on_minimum_due', 1, [ "class" => "form-control border-form ","required",'min'=>1]) !!}
    </div>
   
</div>
@if(isset($data['row']))
<div class="clearfix form-actions">
    <div class="align-right">            &nbsp; &nbsp; &nbsp;
        <button class="btn btn-info submitButton" type="submit" id="filter-btn">
           
                Update
        </button>
    </div>
</div>
@else
<div class="clearfix form-actions">
        <div class="align-right">            &nbsp; &nbsp; &nbsp;
                <button class="btn btn-info submitButton" type="submit" id="filter-btn">
                     <i class="fa fa-plus bigger-110"></i>
                        Add 
                </button>
        </div>
</div>
@endif