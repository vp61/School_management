<div class="row">
	<div class="col-sm-12">
		<h4 class="header large lighter blue"><i class="fa fa-search"></i>
			Search Complain
		</h4>
	</div>
	<div class="col-sm-12">
		{!!Form::open(['route'=>$base_route,'method'=>'GET','class'=>'form-horizontal','id' => 'validation-form',"enctype"=>"miltipart/form-data"])!!}
			<div class="form-group">
				{!!Form::label('complain','Complain Type',['class'=>'col-sm-2 control-label'])!!}
				<div class="col-sm-3">
					{!!Form::select('complain_type',$data['complain_type'],null,['class'=>'form-control'])!!}
				</div>
				{!!Form::label('date','Date',['class'=>'col-sm-2 control-label'])!!}
				<div class=" col-sm-5">
	                <div class="input-group ">
	                    {!! Form::date('reg_start_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
	                    <span class="input-group-addon">
	                        <i class="fa fa-exchange"></i>
	                    </span>
	                    {!! Form::date('reg_end_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
	                    @include('includes.form_fields_validation_message', ['name' => 'reg_start_date'])
	                    @include('includes.form_fields_validation_message', ['name' => 'reg_end_date'])
	                </div>
	            </div>
			</div>
			<div class="form-group">
				{!!Form::label('name','Complain By',['class'=>'col-sm-2 control-label'])!!}
				<div class="col-sm-3">
					{!!Form::text('complain_by',null,['class'=>'form-control','placeholder'=>'Enter Name'])!!}
				</div>
				{!!Form::label('mobile','Mobile',['class'=>'col-sm-2 control-label'])!!}
				<div class="col-sm-2">
					{!!Form::number('mobile',null,['class'=>'form-control','placeholder'=>'Enter Mobile'])!!}
				</div>
				
				{!!Form::label('note','Note',['class'=>'col-sm-1 control-label'])!!}
				<div class="col-sm-2">
					{!!Form::text('note',null,['class'=>'form-control'])!!}
				</div>
			</div>
			<div class="form-group">
				{!!Form::label('source','Source',['class'=>'col-sm-2 control-label'])!!}
				<div class="col-sm-3">
					{!!Form::select('source',$data['source'],null,['class'=>'form-control'])!!}
				</div>
				{!!Form::label('assigned','Assigned',['class'=>'col-sm-2 control-label'])!!}
				<div class="col-sm-2">
					{!!Form::text('assigned',null,['class'=>'form-control'])!!}
				</div>
			</div>
			<div class="form-group">
				<div class="clearfix form-actions">
			        <div class="align-right">            &nbsp; &nbsp; &nbsp;
			                <button class="btn btn-info" type="submit" id="filter-btn">
			                     <i class="fa fa-search bigger-110"></i>
			                        Search
			                </button>
			        </div>
				</div> 
			</div>
		{!!Form::close()!!}
	</div>
</div>