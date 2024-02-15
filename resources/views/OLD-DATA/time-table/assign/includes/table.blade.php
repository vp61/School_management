<h4 class="header large lighter blue"><i class="fa fa-list"></i> Assigned Subjects List</h4>
 <div class="row"> 
 	<div class="col-md-12">
 		<table id="dynamic-table" class="table table-striped table-bordered table-hover">
				<thead>
					<th>S.No.</th>
					<th>Teacher</th>
					<th>Subject</th>
					<th>Edit / Delete</th>
				</thead>
				@php($i=1)
				@if(isset($data['assigned']) && count($data['assigned'])>0)
					@foreach($data['assigned'] as $values)
						<tr>
							<td>{{$i}}</td>
							<td>{{$values->teacher}}</td>
							<td>{{$values->subject}}</td>
							<td><a href="/timetable/assign/edit/{{$values->id}}"><i class="fa fa-pencil green" title="Edit"></i></a> <a href="/timetable/assign/delete/{{$values->id}}"><i class="fa fa-trash-o red" title="Delete"></i></a></td>
						</tr>
						@php($i++)
					@endforeach
				@endif
		</table>
 	</div>
 </div>