<h4 class="header large lighter blue"><i class="fa fa-list"></i> Subjects List</h4>
 <div class="row"> 
 	<div class="col-md-12">
 		<table id="dynamic-table" class="table table-striped table-bordered table-hover">
				<thead>
					<th>S.No.</th>
					<th>Course</th>
					<th>Section</th>
					<th>Subject</th>
					<th>Edit /Delete </th>
				</thead>
				@php($i=1)
				@if(isset($data['subject']) && count($data['subject'])>0)
					@foreach($data['subject'] as $values)
						<tr>
							<td>{{$i}}</td>
							<td>{{$values->course}}</td>
							<td>{{$values->section}}</td>
							<td>{{$values->title}}</td>
							<td><a href="/timetable/subject/edit/{{$values->id}}"><i class="fa fa-pencil green" title="Edit"></i></a> <a href="/timetable/subject/delete/{{$values->id}}"><i class="fa fa-trash-o red" title="Delete"></i></a></td>
						</tr>
						@php($i++)
					@endforeach
				@endif
		</table>
 	</div>
 </div>