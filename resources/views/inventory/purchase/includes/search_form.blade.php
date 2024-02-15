<h4 class="header large lighter blue"><i class="fa fa-search"></i> Search {{$panel}} <i class="fa fa-angle-double-down pull-right search_show_icon" title="Click to view form" style="display: none;"></i><i class="fa fa-angle-double-up pull-right search_show_icon" title="Click to hide form" ></i></h4> 
<div class="hidden_search_form">
	<div class="row">
	<div class="col-sm-12">
		{!!Form::open(['route'=>$base_route,'method'=>'GET','class'=>'form-horizontal','id' => 'validation-form',"enctype"=>"miltipart/form-data"])!!}

			<div class="form-group">
				{!!Form::label('name','Product Name',['class'=>'col-sm-2 control-label'])!!}
				<div class="col-sm-3">
					{!!Form::select('product',$data['product'],null,['class'=>'form-control select_live_search'])!!}
				</div>
				{!!Form::label('date','Date',['class'=>'col-sm-2 control-label'])!!}
				<div class=" col-sm-5">
	                <div class="input-group ">
	                    {!! Form::date('start_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
	                    <span class="input-group-addon">
	                        <i class="fa fa-exchange"></i>
	                    </span>
	                    {!! Form::date('end_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
	                    @include('includes.form_fields_validation_message', ['name' => 'reg_start_date'])
	                    @include('includes.form_fields_validation_message', ['name' => 'reg_end_date'])
	                </div>
	            </div>
			</div>
			<div class="form-group">
				{!!Form::label('supplier','Supplier',['class'=>'col-sm-2 control-label'])!!}
				<div class="col-sm-3">
					{!!Form::select('supplier',$data['supplier'],null,['class'=>'form-control select_live_search'])!!}
				</div>
				
				{!!Form::label('reference','Reference',['class'=>'col-sm-2 control-label'])!!}
				<div class="col-sm-2">
					{!!Form::text('reference',null,['class'=>'form-control'])!!}
				</div>
				{!!Form::label('unit_price','Unit Price',['class'=>'col-sm-1 control-label'])!!}
				<div class="col-sm-2">
					{!!Form::text('unit_price',null,['class'=>'form-control'])!!}
				</div>
			</div>
			<div class="form-group">
				{!!Form::label('purchase_status','Purchase Status',['class'=>'col-sm-2 control-label'])!!}
				<div class="col-sm-3">
					{!!Form::select('purchase_status',$data['purchase_status'],null,['class'=>'form-control select_live_search'])!!}
				</div>
				<div class="col-sm-7">
					<div class="align-right">            &nbsp; &nbsp; &nbsp;
			                <button class="btn btn-info" type="submit" id="filter-btn">
			                     <i class="fa fa-search bigger-110"></i>
			                        Search
			                </button>
			        </div>
				</div>
			</div>
			<!-- <div class="form-group">
				<div class="clearfix form-actions">
			        
				</div> 
			</div> -->
		{!!Form::close()!!}
	</div>
</div>
</div>