<h4 class="header large lighter blue"><i class="fa fa-list"></i> {{$panel}} List</h4>
<table id="dynamic-table" class="table table-striped table-hover">
	<thead>
		<th>S.No.</th>
		<th>Vehicle</th>
		<th>Problem</th>
		<th>Date</th>
		<th>work Perform</th>
		<th>Perform By</th>
		<th>Cost</th>
		<th>Edit / Delete</th>
	</thead>
	
	@php($i=1)
		@foreach($data['maintenance'] as $key=>$val)

		
		
			<tr>
				<td>{{$i}}</td>
				<td>{{$val->number}}</td>
				<td>{{$val->problem}}</td>
				<td>{{$val->maintenance_date}} </td>
				<td>{{$val->work_performed}} </td>
				<td>{{$val->performed_by}} </td>
				<td>&#8377; {{$val->maintenance_charge}}</td>
			
				<td><a href="{{route($base_route.'.edit',[$val->id])}}" title="Edit"><i class="fa fa-pencil" style="color: green;"></i></a>
				@ability('super-admin','super-admin')	
				  <a href="{{route($base_route.'.delete',[$val->id])}}" title="Delete"><i class="fa fa-trash-o" style="color: red;"></i></a>
				@endability  
				</td>
			</tr>
			@php($i++)
		@endforeach
	
</table>