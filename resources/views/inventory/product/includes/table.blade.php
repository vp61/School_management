@include('includes.data_table_header')
<div class="table-responsive">
	<table id="dynamic-table" class="table table-striped">
	<thead>
		<tr>
			<th>S.No.</th>
			<th>Product Name</th>
			<th>Brand</th>
			<th>Category</th>
			<th>Sub Category</th>
			<th>Unit</th>
			<th>SKU</th>
			<th>ISBN</th>
			<th>Alert Quantity</th>
			<th>Price</th>
			<th>GST</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		@if(isset($data['product']))
			@php($i=1)
			@foreach($data['product'] as $key=>$val)
				<tr>
					<td>{{$i++}}</td>
					<td>{{$val->title}}</td>
					<td>{{$val->brand}}</td>
					<td>{{$val->category}}</td>
					<td>{{$val->sub_cat}}</td>
					<td>{{$val->unit}}</td>
					<td>{{$val->sku}}</td>
					<td>{{$val->isbn}}</td>
					<td>{{$val->alert_quantity}}</td>
					<td>{{$val->price}}</td>
					<td>{{$val->gst!=null?$val->gst.'%':''}}</td>
					<td>
					<a href="{{route('inventory.product.edit',[$val->id])}}" class="btn btn-success btn-minier" title="Edit"><i class="fa fa-pencil"></i></a> 
					@ability('super-admin','super-admin')
					<a href="{{route('inventory.product.delete',[$val->id])}}" class="btn btn-danger btn-minier bootbox-confirm" title="delete"><i class="fa fa-trash"></i></a>
					</td>
					@endability
				</tr>
			@endforeach
		@endif
	</tbody>
</table>
</div>
