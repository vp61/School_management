<h4 class="header large lighter blue"><i class="fa fa-list"></i> {{$panel}} List</h4>
<table id="dynamic-table" class="table table-striped table-hover">
	<thead>
		<th>S.No.</th>
		<th>Vehicle</th>
		<th>Date</th>
		<th>Todtal distance(/day)</th>
		<th>Fuel</th>
		<th>Receipt No</th>
		<th>Fuel amount</th>
		<th>Edit / Delete</th>
	</thead>
	
	@php($i=1)
		@foreach($data['dailyentry'] as $key=>$val)

		
		
			<tr>
				<td>{{$i}}</td>
				<td>{{$val->number}}</td>
				<td>{{Carbon\carbon::parse($val->date)->format('d-m-Y')}} </td>
				<td>{{$val->distance}}</td>
				<td>{{$val->fuel}} </td>
				<td>{{$val->receipt_no}} </td>
				<td>&#8377; {{$val->fuel_amount}}</td>
			
				<td><a href="{{route($base_route.'.edit',[$val->id])}}" title="Edit"><i class="fa fa-pencil" style="color: green;"></i></a>
				@ability('super-admin','super-admin')	
				  <a href="{{route($base_route.'.delete',[$val->id])}}" title="Delete"><i class="fa fa-trash-o" style="color: red;"></i></a>
				@endability  
				</td>
			</tr>
			@php($i++)
		@endforeach
	
</table>