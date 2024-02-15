<div class="form-group">
    {!! Form::label('mode', 'Exam Mode', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::select('mode_id',$dropdowns['mode'],null, ["class" => "form-control border-form upper","required"]) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('term', 'Term', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::select('term_id',$dropdowns['term'],null, ["class" => "form-control border-form upper","required",'id'=>'term']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('type', 'Exam Type', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::select('type_id',$dropdowns['type'],null, ["class" => "form-control border-form upper","required",'id'=>'exam_type']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('paper_type', 'Paper Type', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::select('paper_type',$dropdowns['paper-type'],null, [ "class" => "form-control border-form upper","required"]) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('faculty', env('course_label'), ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::select('faculty_id',$dropdowns['faculty'],null, [ "class" => "form-control border-form upper","required",'onChange'=>'loadSection(this)','id'=>'faculty']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('section', 'Section', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::select('section_id',$dropdowns['section'],null, [ "class" => "form-control border-form upper","required",'id'=>'section']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('subject', 'Subject', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::select('subject_id',$dropdowns['subject'],null, [ "class" => "form-control border-form upper","required",'id'=>'subject']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('exam', 'Exams', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::select('exam_id',[''=>'Select Exam'],null, [ "class" => "form-control border-form upper","required"]) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('date','Exam Date', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::date('date',null, [ "class" => "form-control border-form date-picker","required"]) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('start_time', 'Start Time', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::time('start_time',null, [ "class" => "form-control border-form","required"]) !!}
    </div>
</div>
<div class="form-group">
   {!! Form::label('end_time', 'End Time', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::time('end_time',null, [ "class" => "form-control border-form","required"]) !!}
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