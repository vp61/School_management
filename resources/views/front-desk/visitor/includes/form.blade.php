 @include('includes.validation_error_messages')
<div class="form-group">
	{!!Form::label('name','Name',['class'=>'col-sm-1 control-label'])!!}
	<div class="col-sm-2">
		{!!Form::text('name',null,['class'=>'form-control','placeholder'=>'Enter Name','required'=>'required'])!!}
	</div>
	{!!Form::label('phone','Mobile',['class'=>'col-sm-1 control-label'])!!}
	<div class="col-sm-2">
		{!!Form::number('contact',null,['class'=>'form-control','placeholder'=>'Enter phone','required'=>'required'])!!}
	</div>
	{!!Form::label('email','E-mail',['class'=>'col-sm-1 control-label'])!!}
	<div class="col-sm-2">
		{!!Form::email('email',null,['class'=>'form-control','placeholder'=>'Enter E-mail'])!!}
	</div>
	{!!Form::label('purpose','Purpose',['class'=>'col-sm-1 control-label'])!!}
	<div class="col-sm-2">
		{!!Form::select('purpose',$data['purpose'],null,['class'=>'form-control'])!!}
	</div>
</div>
<div class="form-group">
	{!!Form::label('person','Persons',['class'=>'col-sm-1 control-label'])!!}
	<div class="col-sm-2">
		{!!Form::number('no_of_people',null,['class'=>'form-control','placeholder'=>'Enter Number of Persons','required'=>'required','min'=>0])!!}
	</div>
	
	{!!Form::label('date','Date',['class'=>'col-sm-1 control-label'])!!}
	<div class="col-sm-2">
		{!!Form::date('date',null,['class'=>'form-control date-picker','required'=>'required'])!!}
	</div>
	{!!Form::label('in_time','Time',['class'=>'col-sm-1 control-label'])!!}
		<div class="col-sm-2">
			<div class="input-group clockpicker" data-placement="left" data-align="top" data-autoclose="true">
				<span class="input-group-addon">
				  <span class="">In</span>
				</span>
				<input type="time" class="form-control"  name="in_time" <?php if(isset($data['row']->in_time)){ ?>
					value="{{$data['row']->in_time}}"
			<?php	} ?> id="from">	
			</div>

		</div>
		<div class="col-sm-2">
			<div class="input-group clockpicker" data-placement="left" data-align="top" data-autoclose="true">
				<span class="input-group-addon">
				  <span class="">Out</span>
				</span>
				<input type="time" class="form-control" name="out_time" <?php if(isset($data['row']->out_time)){ ?>
					value="{{$data['row']->out_time}}"
			<?php	} ?> id="to">	
			</div>
		</div>
</div>
<div class="form-group">
	{!!Form::label('note','Note',['class'=>'col-sm-1 control-label'])!!}
	<div class="col-sm-2">
		{!!Form::text('note',null,['class'=>'form-control','placeholder'=>'Enter Note'])!!}
	</div>
	 {!!Form::label('id','ID Card',['class'=>'col-sm-1 control-label'])!!}
	<div class="col-sm-2">
		{!!Form::text('id_proof',null,['class'=>'form-control','placeholder'=>"Visitor's Card Number"])!!}
	</div>
</div>
