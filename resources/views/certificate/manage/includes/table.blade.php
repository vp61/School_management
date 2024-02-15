<div class="row">
	<div class="col-md-12 col-xs-12">
		 <table id="dynamic-table" class="table table-striped table-bordered table-hover">
			<thead>
				<th>S.No.</th>
				<th>Certificate Title</th>
				<th>Edit/Delete</th>
			</thead>
			@php($i=1)
			@foreach($data['values'] as $values)
				<tr>
					<td>{{$i}}</td>
					<td>{{$values->title}}</td>
					<td> 
						<a href="{{route('certificate.edit',[$values->id])}}">
                            <span class="green">
                            <i class="ace-icon fa fa-pencil-square-o bigger-120"> </i>
                             </span>
                          </a>
                       	<a href="{{route('certificate.delete',[$values->id])}}">
	                        <span class="red ">
	                             <i class="ace-icon fa fa-trash-o bigger-120"></i>
	                      	 </span>
                        </a>
                    </td>
				</tr>
				@php($i++)
			@endforeach
		</table>
	</div>
</div>