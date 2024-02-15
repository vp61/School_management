@include('includes.validation_error_messages')
<div class="form-group">
	{!!Form::label('head','Head',['class'=>'col-sm-1 control-label'])!!}
	<div class="col-sm-3">
		{!!Form::select('tr_head_id',$data['head'],null,['class'=>'form-control select_live_search','required'=>'required'])!!}
	</div>
	{!!Form::label('type','Type',['class'=>'col-sm-1 control-label'])!!}
	<div class="col-sm-3">
		{!!Form::select('type',[''=>'--Select Type--','Credit'=>'Credit','Debit'=>'Debit'],null,['class'=>'form-control','required'=>'required'])!!}
	</div>
	{!!Form::label('date','Date',['class'=>'col-sm-1 control-label'])!!}
	<div class="col-sm-3">
		{!!Form::date('date',$date,['class'=>'form-control date-picker','required'=>'required'])!!}
	</div>
</div>
<div class="form-group">
	{!!Form::label('amount','Amount',['class'=>'col-sm-1 control-label'])!!}
	<div class="col-sm-3">
		{!!Form::number('amount',null,['class'=>'form-control','placeholder'=>'Enter Amount','required'=>'required'])!!}
	</div>
	{!!Form::label('dsc','Description',['class'=>'col-sm-1 control-label'])!!}
	<div class="col-sm-3">
		{!!Form::text('description',null,['class'=>'form-control','placeholder'=>'Enter Description'])!!}
	</div>
	{!!Form::label('note','Note',['class'=>'col-sm-1 control-label'])!!}
	<div class="col-sm-3">
		{!!Form::text('note',null,['class'=>'form-control','placeholder'=>'Enter Note'])!!}
	</div>
</div>
<div class="form-group">
	{!!Form::label('pay','Pay Mode',['class'=>'col-sm-1 control-label'])!!}
	<div class="col-sm-3">
		{!!Form::select('pay_mode',$data['pay_mode'],null,['class'=>'form-control','required'=>'required'])!!}
	</div>
	{!!Form::label('ref','Ref. No.',['class'=>'col-sm-1 control-label'])!!}
	<div class="col-sm-3">
		{!!Form::text('reference',null,['class'=>'form-control'])!!}
	</div>
	{!!Form::label('branch_id','Branch',['class'=>'col-sm-1 control-label'])!!}
	<div class="col-sm-3">
		{!!Form::select('branch_id',$data['branches'],null,['class'=>'form-control','required'=>'required'])!!}
	</div>	
</div>
<div class="col-sm-12">
		<button type="submit" class="btn btn-info pull-right">
			 
			@if(isset($data['row']))
			 	Update
			@else
				<i class="fa fa-plus"></i> Add
			@endif		
		</button>
</div>
