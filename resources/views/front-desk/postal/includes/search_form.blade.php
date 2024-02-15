<div class="row">
	<div class="col-sm-12">
		<h4 class="header large lighter blue"><i class="fa fa-search"></i>
			Search Postal
		</h4>
	</div>
	<div class="col-sm-12">
		{!!Form::open(['route'=>$base_route,'method'=>'GET','class'=>'form-horizontal','id' => 'validation-form',"enctype"=>"miltipart/form-data"])!!}

			<div class="form-group">
				{!!Form::label('from_title','From Title',['class'=>'col-sm-2 control-label'])!!}
				<div class="col-sm-2">
					{!!Form::text('from_title',null,['class'=>'form-control','placeholder'=>'Enter From Title'])!!}
				</div>
				{!!Form::label('to_title','To Title',['class'=>'col-sm-2 control-label'])!!}
				<div class="col-sm-2">
					{!!Form::text('to_title',null,['class'=>'form-control','placeholder'=>'Enter To Title'])!!}
				</div>
				{!!Form::label('ref','Reference No.',['class'=>'col-sm-2 control-label'])!!}
				<div class="col-sm-2">
					{!!Form::text('reference_no',null,['class'=>'form-control','placeholder'=>'Enter Reference Number'])!!}
				</div>
			</div>
			<div class="form-group">
				{!!Form::label('note','Postal Type',['class'=>'col-sm-2 control-label'])!!}
				<div class="col-sm-2">
					{!!Form::select('type',[''=>"--Select Type--",'1'=>"Dispatched",'2'=>"Received"],null,['class'=>'form-control'])!!}
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

			
				<div class="clearfix ">
			        <div class="align-right">            &nbsp; &nbsp; &nbsp;
			                <button class="btn btn-info" type="submit" id="filter-btn">
			                     <i class="fa fa-search bigger-110"></i>
			                        Search
			                </button>
			        </div>
				</div> 
			
		{!!Form::close()!!}
	</div>
</div>