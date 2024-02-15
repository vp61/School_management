<h4 class="header large lighter blue">{{ env('course_label') }} Type</h4>
<table id="dynamic-table" class="table table-striped table-hover">
	<thead>
		<th>S.No.</th>
		<th>Type</th>
		<th>Edit / Delete</th>
	</thead>
	@if(count($data['type'])>0)
	@php($i=1)
		@foreach($data['type'] as $key=>$val)
		
			<tr>
				<td>{{$i}}</td>
				<td>{{$val->title}}</td>
				<td><a href="/courseType/edit/{{$val->id}}" title="Edit"><i class="fa fa-pencil" style="color: green;"></i></a>  <a href="/courseType/delete/{{$val->id}}" title="Delete"><i class="fa fa-trash-o" style="color: red;"></i></a></td>
			</tr>
			@php($i++)
		@endforeach
	@endif
</table>