<div class="form-group">
  
	{!!Form::label('course_type',env('course_label').' Type',['class'=>'col-sm-4 control-label'])!!}
	<div class="col-sm-8">
		{!!Form::select('course_type',$data['type'],null,['class'=>'form-control','required'=>'required','onchange'=>'loadCourse(this)'])!!}
	</div>
</div>
<div class="form-group">
    {!!Form::label('course','Class',['class'=>'col-sm-4 control-label'])!!}
    <div class="col-sm-8">
        {!!Form::select('course_id',$data['faculty'],null,['class'=>'form-control','required'=>'required','id'=>'course'])!!}
    </div>
</div>
<div class="form-group">
    {!!Form::label('title','Batch Name',['class'=>'col-sm-4 control-label'])!!}
    <div class="col-sm-8">
        {!!Form::text('title',null,['class'=>'form-control','required'=>'required'])!!}
    </div>
</div>
<div class="form-group">
    {!!Form::label('capacity','Capacity',['class'=>'col-sm-4 control-label'])!!}
    <div class="col-sm-8">
        {!!Form::text('capacity',null,['class'=>'form-control','required'=>'required'])!!}
    </div>
</div>
<div class="form-group">
    {!!Form::label('start_date','Start Date',['class'=>'col-sm-4 control-label'])!!}
    <div class="col-sm-8">
        {!!Form::date('start_date',null,['class'=>'form-control','date-picker'])!!}
    </div>
</div>
<div class="form-group">
    {!!Form::label('end_date','End Date',['class'=>'col-sm-4 control-label'])!!}
    <div class="col-sm-8">
        {!!Form::date('end_date',null,['class'=>'form-control','date-picker'])!!}
    </div>
</div>
