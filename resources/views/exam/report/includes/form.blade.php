<div class="form-group">
    {!! Form::label('course', env('course_label'), ['class' => ' cors col-sm-1 control-label']) !!}
    <div class="col-sm-3">
        {!! Form::select('course',$dropdowns['faculty'],null, ["class" => "form-control border-form upper","required",'onchange'=>'loadSection(this)','id'=>'course_drop']) !!}
        <input type="hidden" name="" class="branch_drop" value="{{Session::get('activeBranch')}}">
        <input type="hidden" name="" class="sesn" value="{{Session::get('activeSession')}}">
    </div>
    {!! Form::label('sec', 'Section', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-3">
        {!! Form::select('section',$dropdowns['section'],null, ["class" => "form-control border-form semester","required",'id'=>'section']) !!}
    </div>
    
    {!! Form::label('mode','Student', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-3">
        {!! Form::select('student',[''=>'--Select Student--','1'=>'Demo 1','2'=>'Demo 2'],null, ["class" => "form-control border-form student selectpicker","required",'data-live-search'=>'true']) !!}
    </div>
</div>
<div class="form-group">
     {!! Form::label('report','Report Type', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-3">
        {!! Form::select('report_type',['2'=>'Type 2 (Class 1-10)'],null, ["class" => "form-control border-form","required"]) !!}
    </div>
     {!! Form::label('mode','Term', ['class' => 'col-sm-1 control-label']) !!}
  <div class="col-sm-3">
      {!! Form::select('term_id',$dropdowns['term'],null, ["class" => "form-control border-form upper",'id'=>'term']) !!}
  </div>
</div>
<?php /*
<div class="form-group">  
   {!! Form::label('mode','Term', ['class' => 'col-sm-1 control-label']) !!}
  <div class="col-sm-3">
      {!! Form::select('term',$dropdowns['term'],null, ["class" => "form-control border-form upper","required",'id'=>'term']) !!}
  </div>
   {!! Form::label('type', 'Exam Type', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-3">
        {!! Form::select('type_id',[''=>'Select Type'],null, ["class" => "form-control border-form upper","required",'id'=>'exam_type']) !!}
    </div> 
</div>
*/ ?>
<div class="clearfix form-actions">
    <div class="align-right">            &nbsp; &nbsp; &nbsp;
        <button class="btn btn-info" type="submit" id="filter-btn">
            <i class="fa fa-print bigger-110"></i>
                Print Report Card 
        </button>
    </div>
</div>