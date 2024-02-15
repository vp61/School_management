<div class="row">
	@include('includes.data_table_header')
	<div class="col-sm-12 table-responsive">
		<table  id="dynamic-table" class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th>S.No.</th>
					<th>Name</th>
					<th>Purpose</th>
					<th>E-mail</th>
					<th>Mobile</th>
					<th>ID</th>
					<th>No. of people</th>
					<th>In Time</th>
					<th>Out Time</th>
					<th>Note</th>
					<th>Edit/Delete</th>
				</tr>
			</thead>
			<tbody>
				@php($i=1)
			@foreach($data['visitor'] as $data)
				<tr>
					<td>{{$i}}</td>
					<td>{{$data->name}}</td>
					<td>{{$data->purpose}}</td>
					<td>{{$data->email}}</td>
					<td>{{$data->contact}}</td>
					<td>{{$data->id_proof}}</td>
					<td>{{$data->no_of_people}}</td>
					<td>{{$data->in_time}}</td>
					<td>{{$data->out_time}}</td>
					<td>{{$data->note}}</td>
					<td>
						<a href="{{route($base_route.'.edit',[$data->id])}}" title="Edit">
							<i class="fa fa-pencil green"> </i>
						</a>
					@ability('super-admin','super-admin')	
						|
						<a href="{{route($base_route.'.delete',[$data->id])}}" title="Delete" class="bootbox-confirm">
							<i class="fa fa-trash red"></i>
						</a>
					@endability		
					</td>
					@php($i++)
				</tr>
			@endforeach
			</tbody>
		</table>
	</div>
</div>