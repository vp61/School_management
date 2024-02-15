<h4 class="header large lighter blue"><i class="fa fa-list"></i> {{$panel}} List</h4>
<table id="dynamic-table" class="table table-striped table-hover">
	<thead>
		<th>S.No.</th>
		<th>staff Name</th>
		<th>staff Type</th>
		<th>Course </th>
		<th>Section</th>
		<th>Edit / Delete</th>
	</thead>
	
	@php($i=1)
		@foreach($data['teacher_coordinator'] as $key=>$val)
		
			<tr>
				<td>{{$i}}</td>
				<td>{{$val->first_name}}</td>
				<td><?php
				   
				    
				    if($val->type==1){
				    	echo "Class Teacher";
				    }
				    if($val->type==2){
				    	echo "Co-Ordinator";
				    }

				 ?></td>
				 <td>{{$val->faculty}}</td>
				 <td>{{$val->semester}}</td>
				<td><a href="{{route($base_route.'.edit',[$val->id])}}" title="Edit"><i class="fa fa-pencil" style="color: green;"></i></a>
				@ability('super-admin','super-admin')	
				  <a href="{{route($base_route.'.delete',[$val->id])}}" title="Delete"><i class="fa fa-trash-o" style="color: red;"></i></a>
				@endability  
				</td>
			</tr>
			@php($i++)
		@endforeach
	
</table>