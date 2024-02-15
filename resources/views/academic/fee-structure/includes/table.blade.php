<h4 class="header large lighter blue"><i class="fa fa-list"></i> {{$panel}} List</h4>
<div class="row">
	<div class="col-sm-12">
		{!!Form::open(['route'=>$base_route,'method'=>'GET','class'=>'from-horizontal'])!!}
			<div class="form-group">
				{!!Form::label('class','Class',['class'=>'control-label col-sm-2'])!!}
				<div class="col-sm-5">
					{!!Form::select('faculty',$data['faculty'],null,['class'=>'form-control','required'=>'required'])!!}
				</div>
				<div class="col-sm-5">
					 <div class="align-right"> 
				            <button class="btn btn-info" type="submit" id="filter-btn">
				                 
				                   Search
				            </button>
				    </div>
				</div>
			</div>
		{!!Form::close()!!}
	</div>
</div>
<br>
<table id="dynamic-table" class="table table-striped table-hover">
	<thead>
		<th>S.No.</th>
		<th>Faculty</th>
		<th>Month(From - To)</th>
		
		<th>Edit / Delete</th>
	</thead>
	
	@php($i=1)
		@foreach($data['feeStructure'] as $key=>$val)
		
			<tr>
				<td>{{$i}}</td>
				
				<td>{{$val->faculty}}</td>
				<td>{{$val->from_month}} - {{$val->to_month}}</td>
				<td><a href="{{route($base_route.'.edit',[$val->id])}}" title="Edit"><i class="fa fa-pencil" style="color: green;"></i></a>
				@ability('super-admin','super-admin')	
				  <a href="{{route($base_route.'.delete',[$val->id])}}" title="Delete"><i class="fa fa-trash-o" style="color: red;"></i></a>
				@endability  
				</td>
			</tr>
			@php($i++)
		@endforeach
	
</table>