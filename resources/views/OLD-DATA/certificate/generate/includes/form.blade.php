<h4 class="header large lighter blue"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;Generate</h4>
<div class="form-group">
	{!!Form::label('course','Course',['class'=>'col-sm-1 control-label'])!!}
	<div class="col-sm-3">
		{!!Form::select('course',$data['course'],null,['class'=>'form-control','onChange'=>'loadStudent()','id'=>'course'])!!}
	</div>
	{!!Form::label('section','Section',['class'=>'col-sm-1 control-label'])!!}
	<div class="col-sm-3">
		{!!Form::select('section',$data['section'],null,['class'=>'form-control','onChange'=>'loadStudent()','id'=>'section'])!!}
	</div>
	{!!Form::label('student','Student',['class'=>'col-sm-1 control-label'])!!}
	<div class="col-sm-3">
		{!!Form::select('student',[''=>'Select Student'],null,['class'=>'form-control','id'=>'student','required'=>'required'])!!}
	</div>
</div>
<div class="form-group">
	{!!Form::label('certificate_list','Certificate',['class'=>'col-sm-1 control-label'])!!}
	<div class="col-sm-3">
		{!!Form::select('certificate',$data['certificate'],null,['class'=>'form-control',
		'required'=>'required'])!!}
	</div>
	<div class="col-sm-1 pull-right">
		
	          
        <button class="btn btn-info" type="submit" id="filter-btn">
               Generate
        </button>
      
	</div>
</div>