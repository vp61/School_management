
	<div class="form-group">
		{!!Form::label('course',env('course_label'),['class'=>'col-sm-2 control-label'])!!}
		<div class=" col-md-10">
			{{ Form::select('course', $data['course'], '', ['class'=>'form-control', 'required'=>'required'])}}
		</div>
	</div>
	<div class="form-group">
		{!!Form::label('section','Section',['class'=>'col-sm-2 control-label'])!!}
		<div class="col-md-10">
			{!!Form::select('section',$data['section'],null,['class'=>'form-control','required'=>'required'])!!}
		</div>
	</div>
	<div class="form-group">
		{!!Form::label('subject','Subject',['class'=>'col-sm-2 control-label'])!!}
		<div class="col-md-10">
			{!!Form::text('subject',null,['class'=>'form-control','placeholder'=>'Enter Subject Name','required'=>'required'])!!}
		</div>
	</div>
	<div class="align-right">
	            <button type="submit" class="btn btn-sm " >
	                <i class="fa fa-plus bigger-120"></i> Add Subject
	            </button>
	        </div>

