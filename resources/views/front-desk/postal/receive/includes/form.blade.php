 @include('includes.validation_error_messages')
<div class="form-group">
	{!!Form::hidden('type','2')!!}
	{!!Form::label('to','From Title',['class'=>'col-sm-1 control-label'])!!}
	<div class="col-sm-3">
		{!!Form::text('from_title',null,['class'=>'form-control','placeholder'=>'Enter From','required'=>'required'])!!}
	</div>
	{!!Form::label('ref','Ref No',['class'=>'col-sm-1 control-label'])!!}
	<div class="col-sm-3">
		{!!Form::text('reference_no',null,['class'=>'form-control','placeholder'=>'Enter Ref No'])!!}
	</div>
	{!!Form::label('phone','Mobile',['class'=>'col-sm-1 control-label'])!!}
	<div class="col-sm-3">
		{!!Form::number('mobile',null,['class'=>'form-control','placeholder'=>'Enter phone','required'=>'required'])!!}
	</div>
</div>
<div class="form-group">
	
	{!!Form::label('to','To Title',['class'=>'col-sm-1 control-label'])!!}
	<div class="col-sm-3">
	
		{!!Form::text('to_title',null,['class'=>'form-control','placeholder'=>'Enter From','required'=>'required'])!!}
	</div>
	{!!Form::label('date','Date',['class'=>'col-sm-1 control-label'])!!}
	<div class="col-sm-3">
		{!!Form::date('date',null,['class'=>'form-control date-picker','required'=>'required'])!!}
	</div>
	
	{!!Form::label('ad','Note',['class'=>'col-sm-1 control-label'])!!}
	<div class="col-sm-3">
		{!!Form::text('note',null,['class'=>'form-control','placeholder'=>'Enter Note'])!!}
	</div>
	
</div>
<div class="form-group">
	{!!Form::label('ad','Address',['class'=>'col-sm-1 control-label'])!!}
	<div class="col-sm-3">
		{!!Form::textarea('address',null,['class'=>'form-control','placeholder'=>'Enter Address','required'=>'required','rows'=>4])!!}
	</div>
</div>


