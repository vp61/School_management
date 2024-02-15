<div class="row">
	@include('includes.data_table_header')
	<div class="col-sm-12 table-responsive">
		<table  id="dynamic-table" class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th>S.No.</th>
					<th>Type</th>
					<th>From</th>
					<th>To</th>
					<th>Date</th>
					<th>Mobile</th>
					<th>Address</th>
					<th>Note</th>
					
					<th>Edit/Delete</th>
				</tr>
			</thead>
			<tbody>
				@php($i=1)
			@foreach($data['postal'] as $data)
				<tr>
					<td>{{$i}}</td>
					<td>@if($data->type==1)
						<b class="btn btn-minier btn-info">Dispatched</b>
						@elseif($data->type==2)
						<b class="btn btn-minier btn-success">Received</b>
						@endif
					</td>
					<td>{{$data->from_title}}</td>
					<td>{{$data->to_title}}</td>
					<td>{{\Carbon\Carbon::parse($data->date)->format('d-M-Y')}}</td>
					<td>{{$data->mobile}}</td>
					
					<td>{{$data->address}}</td>
					<td>{{$data->note}}</td>
					<td>
						<a href="{{route($base_route.'.edit',[$data->id])}}" title="Delete" class="btn btn-primary btn-minier btn-success">
							<i class="fa fa-pencil"></i>
						</a>
					@ability('super-admin','super-admin')	
						<a href="{{route($base_route.'.delete',[$data->id])}}" title="Delete" class="btn btn-primary btn-minier btn-danger bootbox-confirm">
							<i class="fa fa-trash"></i>
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