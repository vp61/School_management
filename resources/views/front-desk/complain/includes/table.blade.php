<div class="row">
	@include('includes.data_table_header')
	<div class="col-sm-12 table-responsive">
		<table  id="dynamic-table" class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th>S.No.</th>
					<th>Complain BY</th>
					<th>Mobile</th>
					<th>E-mail</th>
					<th>Complain Type</th>
					<th>Source</th>
					<th>Date</th>
					<th>Assigned</th>
					<th>Note</th>
					<th>Descripton</th>
					<th>Status</th>
					<th>Edit|Delete|Change Status</th>
				</tr>
			</thead>
			<tbody>
				@php($i=1)
			@foreach($data['complain'] as $data)
				<tr>
					<td>{{$i}}</td>
					<td>{{$data->complain_by}}</td>
					<td>{{$data->mobile}}</td>
					<td>{{$data->email}}</td>
					<td>{{$data->complain}}</td>
					<td>{{$data->source}}</td>
					<td>{{\Carbon\Carbon::parse($data->date)->format('d-M-Y')}}</td>
					<td>{{$data->assigned}}</td>
					<td>{{$data->note}}</td>
					<td>{{$data->description}}</td>
					<td><b class="btn btn-primary btn-minier {{ $data->complain_status==1?"btn-danger":"btn-info" }}" >{{ $data->complain_status==1?"Pending":"Completed" }}</b></td>
					<td>
						<a href="{{route($base_route.'.edit',[$data->id])}}" title="Edit">
							<i class="fa fa-pencil green"> </i>
						</a>|
					@ability('super-admin','super-admin')	
						<a href="{{route($base_route.'.delete',[$data->id])}}" title="Delete" class="bootbox-confirm">
							<i class="fa fa-trash red"></i>
						</a>|
					@endability	
						@if($data->complain_status==1)
							<a href="{{route($base_route.'.changeStatus',[$data->id,2])}}" class="btn btn-success btn-minier statusChange " title="Delete" id="delete">
								Complete
							</a>
						@else
							<a href="{{route($base_route.'.changeStatus',[$data->id,1])}}" class="btn btn-warning btn-minier statusChange " title="Delete" id="delete">
								Pending
							</a>
						@endif
					</td>
					@php($i++)
				</tr>
			@endforeach
			</tbody>
		</table>
	</div>
</div>