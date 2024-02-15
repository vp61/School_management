<h4 class="header large lighter blue"><i class="fa fa-list"></i> Batch List</h4>
<table id="dynamic-table" class="table table-striped table-hover">
	<thead>
		<th>S.No.</th>
		<th>{{env('course_label')}} Type</th>
		<th>{{env('course_label')}} Name</th>
		<th>Batch Name</th>
		<th>Start : End Date</th>
		<th>Capacity</th>
		<th>Edit / Delete</th>
	</thead>
	@if(count($data['type'])>0)
	@php($i=1)
		@foreach($data['batch'] as $key=>$val)
		
			<tr>
				<td>{{$i}}</td>
				<td>{{$val->course_type}}</td>
				<td>{{$val->course}}</td>
				<td>{{$val->title}}</td>
				<td>{{$val->start_date ? ( \Carbon\Carbon::parse($val->start_date)->format('d-M-Y') ).' : ' : ''}} {{ $val->end_date ? (\Carbon\Carbon::parse($val->end_date)->format('d-M-Y') ) : ''}}</td>
				<td>{{$val->capacity}}</td>
				<td><a href="/courseBatch/edit/{{$val->id}}" title="Edit"><i class="fa fa-pencil" style="color: green;"></i></a>  <a href="/courseBatch/delete/{{$val->id}}" title="Delete"><i class="fa fa-trash-o" style="color: red;"></i></a></td>
			</tr>
			@php($i++)
		@endforeach
	@endif
</table>