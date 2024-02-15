<h4 class="header large lighter blue"><i class="fa fa-list"></i> {{$panel}} List</h4>
<table id="dynamic-table" class="table table-striped table-hover">
	<thead>
		<th>S.No.</th>
		<th>Resident Type</th>
		<th>Resident</th>
		<th>Leave From</th>
		<th>Leave To</th>
		<th>Leave Reason</th>
		<th>Return Date</th>
		<th>Remark</th>
		<th>Edit / Delete</th>
	</thead>
	
	@php($i=1)
		@foreach($data['leave'] as $key=>$val)
            
		
		
			<tr>
				<td>{{$i}}</td>
				<td>
					@if($val->user_type==1)
                     Student
                   @else
                    Staff
                   @endif
				</td>
				<td>
					@if($val->user_type==1)
                    <a href="{{ route('student.view', ['id' => $val->member_id]) }}">
                        {{ ViewHelper::getStudentNameById($val->member_id) }}
                    </a>
                   @else
                    <a href="{{ route('staff.view', ['id' => $val->member_id]) }}">
                        {{ ViewHelper::getStaffNameById($val->member_id) }}
                    </a>
                   @endif
				</td>
				<td>{{Carbon\carbon::parse($val->leave_from)->format('d-m-Y')}} </td>
				<td>{{Carbon\carbon::parse($val->leave_to)->format('d-m-Y')}} </td>
				<td>{{$val->reason}}</td>
				<td>{{Carbon\carbon::parse($val->return_date)->format('d-m-Y')}} </td>
				<td>{{$val->remark}} </td>
			
				<td><a href="{{route($base_route.'.edit',[$val->id])}}" title="Edit"><i class="fa fa-pencil" style="color: green;"></i></a>
				@ability('super-admin','super-admin')	
				  <a href="{{route($base_route.'.delete',[$val->id])}}" title="Delete"><i class="fa fa-trash-o" style="color: red;"></i></a>
				@endability  
				</td>
			</tr>
			@php($i++)
		@endforeach
	
</table>