<!-- <h4 class="header strong">Product Details</h4> -->
<div class="form-group">
	<div class="col-sm-4">
		{!!Form::label('name','Product Name:',['class'=>'strong'])!!}
		{!!Form::text('title',null,['class'=>'form-control','placeholder'=>'Enter Product Name','required'=>'required'])!!}
	</div>
	<div class="col-sm-4">
		{!!Form::label('brand','Brand:',['class'=>'strong'])!!}
		{!!Form::select('brand_id',$data['brand'],null,['class'=>'form-control select_live_search'])!!}
	</div>
	<div class="col-sm-4">
		{!!Form::label('unit','Unit:',['class'=>'strong'])!!}
		{!!Form::select('unit_id',$data['unit'],null,['class'=>'form-control select_live_search','required'=>'required'])!!}
	</div>
</div>
<div class="form-group">
	<div class="col-sm-4">
		{!!Form::label('category','Category:',['class'=>'strong'])!!}
		{!!Form::select('category_id',$data['category'],null,['class'=>'form-control','onchange'=>'load_subcategory(this)'])!!}
	</div>
	<div class="col-sm-4">
		{!!Form::label('sub_category','Sub Category:',['class'=>'strong'])!!}
		{!!Form::select('sub_category',[''=>'Sub Category'],null,['class'=>'form-control'])!!}
	</div>
	<div class="col-sm-4">
		{!!Form::label('sku','SKU:',['class'=>'strong'])!!}
		{!!Form::text('sku',null,['class'=>'form-control','placeholder'=>'Enter SKU'])!!}
	</div>
</div>
<div class="form-group">
	<div class="col-sm-4">
		{!!Form::label('isbn','ISBN:',['class'=>'strong'])!!}
		{!!Form::text('isbn',null,['class'=>'form-control','placeholder'=>'Enter ISBN Number'])!!}
	</div>
	<div class="col-sm-4">
		{!!Form::label('alert','Alert Quantity:',['class'=>'strong'])!!}
		{!!Form::text('alert_quantity',null,['class'=>'form-control','placeholder'=>'Enter Alert Quantity','required'=>'required'])!!}
	</div>
	<div class="col-sm-4">
		{!!Form::label('image','Image:',['class'=>'strong'])!!} <i class="fa fa-info-circle" title="Try selecting more than one image when browsing for images."></i>
		<input type="file" name="file[]" multiple="multiple" class="form-control">
		
	</div>
</div>
@if(isset($data['images']))
	<div class="form-group">
		@foreach($data['images'] as $key=>$val)
			<div class="col-sm-2 image_div" >
				<div class="row">
					<div class="col-sm-12">
						<div style="width: 100%;height: 100px">
							<img src="{{ URL::asset($val->image_path) }}" height="100%" width="100%">
						</div>
					</div>
					<div class="col-sm-12">
						<button type="button" class="btn btn-danger btn-minier pull-right" id="remove_img_{{$val->id}}" onclick="remove_inv_img({{$val->id}})">Remove Image</button>
					</div>
				</div>
			</div>
		@endforeach
	</div>
@endif
<h4 class="header strong">Variation </h4>
<table class="table table-hover table-striped" id="variation_tbl">
	<tr>
		<th>Label</th>
		<th>Value</th>
		<th>Action <i class="fa fa-plus new_row pull-right btn btn-info btn-minier" title="Click to add new variation row"> Add New</i></th>
	</tr>
	<tr id="head_tbl" class="" style="display: none;">
		<td class="col-sm-2">
			{!!Form::select('variation_label[]',$data['label'],null,['class'=>'form-control'])!!}
		</td>
		<td class="col-sm-2">
			{!!Form::text('variation_value[]',null,['class'=>'form-control','placeholder'=>'Enter Value'])!!}
		</td>
		<td class="col-sm-2"><i class="fa fa-trash delete_row btn btn-danger btn-minier" onclick="closest('tr').remove();"></i></td>
	</tr>
	@if(isset($data['variations']))
		@foreach($data['variations'] as $key=>$val)
			@if(!empty($val->label_id))
				<tr class="">
					<td class="col-sm-2">
						{!!Form::select('old_label['.$val->variation_id.']',$data['label'],$val->label_id,['class'=>'form-control'])!!}
					</td>
					<td class="col-sm-2">
						{!!Form::text('old_value['.$val->variation_id.']',$val->value,['class'=>'form-control','placeholder'=>'Enter Value'])!!}
					</td>
					<td class="col-sm-2"><i class="fa fa-trash delete_row btn btn-danger btn-minier" onclick="closest('tr').remove();"></i></td>
				</tr>
			@endif
		@endforeach
	@endif	
</table>
<h4 class="header strong">Tax / G.S.T.</h4>
<div class="form-group">
	<div class="col-sm-4">
		{!!Form::label('purchase','Purchase Price',['class'=>'strong'])!!}
		{!!Form::number('price',null,['class'=>'form-control','placeholder'=>'Enter Purchase Price', 'id'=>'price' ,'onkeyup'=>'amount_calculate()'])!!}
	</div>
	<div class="col-sm-4">
		{!!Form::label('gst','G.S.T.',['class'=>'strong'])!!}
		{!!Form::select('gst',$data['gst'],null,['class'=>'form-control','onchange'=>'amount_calculate()','id'=>'gst'])!!}
	</div>
	<div class="col-sm-4">
		{!!Form::label('gst','Amount',['class'=>'strong'])!!}
		{!!Form::text('amount',null,['class'=>'form-control','disabled','id'=>'amt'])!!}
	</div>
</div>
<div class="form-group">
	<div class="clearfix form-actions">
		<button class="btn btn-info pull-right">Save</button>
	</div>
</div>
