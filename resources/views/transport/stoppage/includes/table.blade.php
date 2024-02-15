<h4 class="header large lighter blue"><i class="fa fa-list"></i> {{$panel}} List</h4>
<table id="dynamic-table" class="table table-striped table-hover">
	<thead>
		<th>S.No.</th>
		<th>Route</th>
		<th>Stoppage</th>
		<th>Distance</th>
		<th>Fee Amount</th>
		<th>Status</th>
		<th>Edit / Delete</th>
	</thead>
	
	@php($i=1)
		@foreach($data['stoppage'] as $key=>$val)
		
			<tr>
				<td>{{$i}}</td>
				<td>{{$val->route}}</td>
				<td>{{$val->title}}</td>
				<td>{{$val->distance}} Km</td>
				<td>&#8377; {{$val->fee_amount}}</td>
				 <td class="hidden-480 ">
                                <div class="btn-group">
                                    <button data-toggle="dropdown" class="btn btn-primary btn-minier dropdown-toggle {{ $val->active_status == 1?"btn-info":"btn-warning" }}" >
                                        {{ $val->active_status == 1?"Active":"In Active" }}
                                        <span class="ace-icon fa fa-caret-down icon-on-right"></span>
                                    </button>

                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="{{ route('transport.stoppage.status', ['id' => $val->id,1]) }}"><i class="fa fa-check" aria-hidden="true"></i></a>
                                        </li>

                                        <li>
                                            <a href="{{ route('transport.stoppage.status', ['id' => $val->id,2]) }}"><i class="fa fa-remove" aria-hidden="true"></i></a>
                                        </li>
                                    </ul>
                                </div>

                            </td>
				<td><a href="{{route($base_route.'.edit',[$val->id])}}" title="Edit"><i class="fa fa-pencil" style="color: green;"></i></a>
				@ability('super-admin','super-admin')	
				  <a href="{{route($base_route.'.delete',[$val->id])}}" title="Delete"><i class="fa fa-trash-o" style="color: red;"></i></a>
				@endability  
				</td>
			</tr>
			@php($i++)
		@endforeach
	
</table>