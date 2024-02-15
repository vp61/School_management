 @include('includes.validation_error_messages')
<div class="form-group">
	{!!Form::label('complain','Complain By',['class'=>'col-sm-2 control-label'])!!}
	<div class="col-sm-2">
		{!!Form::text('complain_by',null,['class'=>'form-control','placeholder'=>'Enter Name','required'=>'required'])!!}
	</div>
	{!!Form::label('phone','Mobile',['class'=>'col-sm-2 control-label'])!!}
	<div class="col-sm-2">
		{!!Form::number('mobile',null,['class'=>'form-control','placeholder'=>'Enter phone','required'=>'required'])!!}
	</div>
	{!!Form::label('email','E-mail',['class'=>'col-sm-2 control-label'])!!}
	<div class="col-sm-2">
		{!!Form::email('email',null,['class'=>'form-control','placeholder'=>'Enter E-mail'])!!}
	</div>
</div>
<div class="form-group">
	{!!Form::label('type','Complain Type',['class'=>'col-sm-2 control-label'])!!}
	<div class="col-sm-2">
		{!!Form::select('complain_type',$data['complain_type'],null,['class'=>'form-control','required'=>'required'])!!}
	</div>
	{!!Form::label('source','Source',['class'=>'col-sm-2 control-label'])!!}
	<div class="col-sm-2">
		{!!Form::select('source_id',$data['source'],null,['class'=>'form-control','required'=>'required'])!!}
	</div>
	{!!Form::label('date','Date',['class'=>'col-sm-2 control-label'])!!}
	<div class="col-sm-2">
		{!!Form::date('date',null,['class'=>'form-control date-picker','required'=>'required'])!!}
	</div>
</div>	
<div class="form-group">
	{!!Form::label('assigned','Assigned',['class'=>'col-sm-2 control-label'])!!}
	<div class="col-sm-2">
		{!!Form::text('assigned',null,['class'=>'form-control','placeholder'=>'Enter Name'])!!}
	</div>
	{!!Form::label('action','Action Taken',['class'=>'col-sm-2 control-label'])!!}
	<div class="col-sm-2">
		{!!Form::text('action_taken',null,['class'=>'form-control'])!!}
	</div>
	{!!Form::label('note','Note',['class'=>'col-sm-2 control-label'])!!}
	<div class="col-sm-2">
		{!!Form::text('note',null,['class'=>'form-control','placeholder'=>'Enter Note'])!!}
	</div>
</div>	
<div class="form-group">
	
	 {!!Form::label('desc','Description',['class'=>'col-sm-2 control-label'])!!}
	<div class="col-sm-4">
		{!!Form::textarea('description',null,['class'=>'form-control','placeholder'=>"Enter Description",'rows'=>5])!!}
	</div>
</div>
