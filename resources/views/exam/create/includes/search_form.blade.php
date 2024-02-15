<div class="form-group">
    {!! Form::label('faculty', env('course_label'), ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-3">
        {!! Form::select('faculty_id',$dropdowns['faculty'],null, [ "class" => "form-control border-form upper",'onChange'=>'loadSection(this)','id'=>'faculty']) !!}
    </div>
    {!! Form::label('section', 'Section', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-3">
        {!! Form::select('section_id',$dropdowns['section'],null, [ "class" => "form-control border-form upper",'id'=>'section']) !!}
    </div>
    {!! Form::label('subject', 'Subject', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-3">
        {!! Form::select('subject_id',$dropdowns['subject'],null, [ "class" => "form-control border-form upper",'id'=>'subject']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('date','Exam Date', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-3">
        {!! Form::date('date',null, [ "class" => "form-control border-form date-picker"]) !!}
    </div>
    <div class="col-sm-8">
    	<button type="submit" class="btn btn-info pull-right">Search</button>
    </div>
</div>