<h4 class="header large lighter blue"><i class="fa fa-list"></i> {{$panel}} List</h4>
<table id="dynamic-table" class="table table-striped table-hover">
	<thead>
		<th>S.No.</th>
		<th>Name</th>
		<th>Email</th>
		<th>Mobile</th>
		<th>Alt. Number</th>
		<th>GSTIN</th>
		<th>Address</th>
		<th>Edit / Delete</th>
	</thead>
	
	@php($i=1)
		@foreach($data['supplier'] as $key=>$val)
		
			<tr>
				<td>{{$i}}</td>
				<td>{{$val->name}}</td>
				<td>{{$val->email}}</td>
				<td>{{$val->mobile}}</td>
				<td>{{$val->alternate_mobile}}</td>
				<td>{{$val->gstin}}</td>
				<td>{{$val->address}}</td>
				
				<td><a href="{{route($base_route.'.edit',[$val->id])}}" title="Edit" class="btn-minier btn-success"><i class="fa fa-pencil" ></i></a>
				@ability('super-admin','super-admin')	
				  <a href="{{route($base_route.'.delete',[$val->id])}}" title="Delete" class="bootbox-confirm btn-minier btn-danger"><i class="fa fa-trash-o" ></i></a>
				@endability  
				</td>
			</tr>
			@php($i++)
		@endforeach
	
</table>