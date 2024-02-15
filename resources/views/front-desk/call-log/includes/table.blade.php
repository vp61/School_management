<div class="row">
	@include('includes.data_table_header')
	<div class="col-sm-12 table-responsive">
		<table  id="dynamic-table" class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th>S.No.</th>
					<th>Name</th>
					<th>Mobile</th>
					<th>Date</th>
					<th>Call Type</th>
					<th>Duration</th>
					<th>Note</th>
					<th>Description</th>
					<th>Last Call Date</th>
					<th>Last Call Duration</th>
					<th>Response</th>

					<th>Next Follow Up Date</th>
					<th>Edit/Delete</th>
				</tr>
			</thead>
			<tbody>
				@php($i=1)
			@foreach($data['calllog'] as $data)
				<tr>
					<td>{{$i}}</td>
					<td>{{$data->name}}</td>
					<td>{{$data->contact}}</td>
					<td>{{\Carbon\Carbon::parse($data->date)->format('d-M-Y')}}</td>
					<td>@if($data->call_type==1)<i class="fa fa-arrow-right blue"> Incoming </i>
						@else <i class="fa fa-arrow-left green"> Outgoing</i> @endif</td>
					<td>{{$data->call_duration}}</td>
					<td>{{$data->note}}</td>
					<td>{{str_limit($data->description,40)}}</td>
					<!-- <td>{{\Carbon\Carbon::parse($data->follow_up_date)->format('d-M-Y')}}</td> -->
					<td>{{\Carbon\Carbon::parse($data->last_call_date)->format('d-M-Y')}}</td>
					<td>{{$data->last_call_duration}}</td>
					<td>{{$data->response}}</td>
					<td>
						@if(!empty($data->next_follow_up) ){{\Carbon\Carbon::parse($data->next_follow_up)->format('d-M-Y')}} @endif</td>
					<td>
						<a href="{{route($base_route.'.view',[$data->id])}}" title="View" class="btn btn-primary btn-minier btn-primary">
							<i class="ace-icon fa fa-eye bigger-130"></i>
						</a>
						<a href="{{route($base_route.'.edit',[$data->id])}}" title="Edit" class="btn btn-primary btn-minier btn-success">
							<i class="fa fa-pencil"> </i>
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