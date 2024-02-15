<h4 class="header large lighter blue"><i class="fa fa-list"></i> {{$panel}} List</h4>
<table id="dynamic-table" class="table table-striped table-hover">
	<thead>
		<th>S.No.</th>
		<th>Group</th>
		<th>Name</th>
		<th>Display Name</th>
		<th>Parent</th>
		<th>Route</th>
		<th>Description</th>
		<th>Edit</th>
	</thead>
	
	@php($i=1)
		@foreach($data['menu'] as $key=>$val)
		
			<tr>
				<td>{{$i}}</td>
				<td>{{$val->group}}</td>
				<td>{{$val->name}}</td>
				<td>{{$val->display_name}}</td>
				<td>{{$val->parent_name}}</td>
				<td>{{$val->route}}</td>
				<td>{{$val->description}}</td>
				
				<td><a href="{{route($base_route.'.edit',[$val->id])}}" title="Edit" class="btn-minier btn-success"><i class="fa fa-pencil" ></i></a> 
				</td>
			</tr>
			@php($i++)
		@endforeach
	
</table>