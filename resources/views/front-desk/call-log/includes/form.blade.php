 @include('includes.validation_error_messages')
<div class="form-group">
	{!!Form::label('name','Name',['class'=>'col-sm-1 control-label'])!!}
	<div class="col-sm-3">
		{!!Form::text('name',null,['class'=>'form-control','placeholder'=>'Enter Name','required'=>'required'])!!}
	</div>
	{!!Form::label('phone','Mobile',['class'=>'col-sm-1 control-label'])!!}
	<div class="col-sm-3">
		{!!Form::number('contact',null,['class'=>'form-control','placeholder'=>'Enter phone','required'=>'required'])!!}
	</div>
	{!!Form::label('date','Date',['class'=>'col-sm-1 control-label'])!!}
	<div class="col-sm-3">
		{!!Form::date('date',$date,['class'=>'form-control date-picker','required'=>'required'])!!}
	</div>
</div>
<div class="form-group">
	{!!Form::label('type','Call Type',['class'=>'col-sm-1 control-label'])!!}
	<div class="col-sm-3">
		{!!Form::select('call_type',[''=>"--Select Type--",'1'=>"Incoming Call",'2'=>"Outgoing Call"],null,['class'=>'form-control','required'=>'required'])!!}
	</div>
	{!!Form::label('duration','Duration',['class'=>'col-sm-1 control-label'])!!}
	<div class="col-sm-3">
		{!!Form::text('call_duration',null,['class'=>'form-control','placeholder'=>'Enter Call Duration','required'=>'required'])!!}
	</div>
	@if(!isset($data['row']))
	{!!Form::label('follow_up_date','Follow Up',['class'=>'col-sm-1 control-label'])!!}
			<div class="col-sm-3">
				{!!Form::date('follow_up_date',null,['class'=>'form-control date-picker'])!!}
			</div>
	@else		
	{!!Form::label('note','Note',['class'=>'col-sm-1 control-label'])!!}
	<div class="col-sm-3">
		{!!Form::text('note',null,['class'=>'form-control','placeholder'=>'Enter Note'])!!}
	</div>
	@endif			
</div>
<div class="form-group">
	@if(!isset($data['row']))
	{!!Form::label('note','Note',['class'=>'col-sm-1 control-label'])!!}
	<div class="col-sm-3">
		{!!Form::text('note',null,['class'=>'form-control','placeholder'=>'Enter Note'])!!}
	</div>
	@else
	@endif
	 {!!Form::label('desc','Description',['class'=>'col-sm-1 control-label'])!!}
	<div class="col-sm-3">
		{!!Form::textarea('description',null,['class'=>'form-control','placeholder'=>"Please Enter Description",'rows'=>3])!!}
	</div>
</div>
