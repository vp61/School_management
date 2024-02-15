<div class="form-group">
    {!! Form::label('Course', env('course_label'), ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
         @if(isset($data['row']))
             {!! Form::select('course_id',$data['course'],null, ["class" => "form-control border-form upper","required",'onChange'=>'loadSection(this)','id'=>'faculty']) !!}
         @else
            {!! Form::select('course_id[]',$data['course'],null, ["class" => "form-control border-form upper selectpicker","required",'onChange'=>'loadSection(this)','id'=>'faculty','multiple'=>'multiple']) !!}
        @endif
    </div>
</div>
<!-- <div class="form-group">
    {!! Form::label('Section', 'Section', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        @if(isset($data['row']))
         {!! Form::select('section_id',$data['section'],null, [ "class" => "form-control border-form upper","required",'id'=>'section']) !!}
        @else
       {!! Form::select('section_id[]',$data['section'],null, [ "class" => "form-control selectpicker border-form upper","required",'id'=>'section','multiple'=>'multiple']) !!}
        @endif
    </div>
</div> -->
<div class="form-group">
    {!! Form::label('Result Type', 'Result Type', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
         {!! Form::select('result_type_id',[''=>"--Result Type--",'1'=>'class 1- 3','2'=>'class 4- 8'],null, [ "class" => "form-control border-form upper","required"]) !!}
        
    </div>
</div>
@if(isset($data['row']))
<div class="clearfix form-actions">
    <div class="align-right">            &nbsp; &nbsp; &nbsp;
        <button class="btn btn-info" type="submit" id="filter-btn">
           
                Update
        </button>
    </div>
</div>
@else
<div class="clearfix form-actions">
        <div class="align-right">            &nbsp; &nbsp; &nbsp;
                <button class="btn btn-info" type="submit" id="filter-btn">
                     <i class="fa fa-plus bigger-110"></i>
                        Add 
                </button>
        </div>
</div>
@endif