{!!Form::open(['route'=>'bulkUpdateCollection','method'=>'POST', 'class' => 'form-horizontal','id' => 'validation-form', "enctype" => "multipart/form-data"])!!} 


 @if(isset($collection) && count($collection)>0)
 <h4 class="header large lighter blue"> 
                               
     <i class="fa fa-search bigger-110"></i>Edit Student Collection
    

</h4>
 @foreach($search_criteria as $k=>$v)
     <center><p>{{$k}} : {{$v}}</p></center>
     @endforeach
<div class="table-responsive">
    <table id="dynamic-tableeeeeeeee" class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
            <th class="center">
                <label class="pos-rel">
                    <input type="checkbox" class="ace" />
                    <span class="lbl"></span>
                </label>
            </th> 
            <th>Student Name</th>
            <th>Assign Amount</th>
            <th>collected Amount</th>
          
            
            
         </tr>
        </thead>
        <tbody class="content_table_body">
            <input type="hidden" name="assign_fee_id" value="{{$assignId}}">
            <input type="hidden" name="faculty_id" value="{{$faculty_id}}">
	            @foreach($collection as $key=>$value)

	        	<tr>
	        		 <td class="center first-child">
	                    <label>
	                        <input type="checkbox" name="student_id[{{ $value->student_id }}]" value="{{ $value->student_id }}" class="ace" />
	                        <span class="lbl"></span>
	                    </label>
	                 </td>
			        <td>{{$value->name}}</td>
			        <td>{{$value->fee_amount}}
                        <input type="hidden" name="fee_amount[{{$value->student_id}}]" value="{{$value->fee_amount}}">

			        </td>
			        <td>
                        @php  $paid= $value->amount_paid+ $value->discount; @endphp

                        {{$paid}}
			            <input type="hidden" name="amount_paid[{{$value->student_id}}]" value="{{$paid}}">
			        </td>
	        	</tr>
	        	@endforeach
           
       
        </tbody>
                   
        </table>
        <div class="form-group">
        	 {!! Form::label('Discount', 'Discount Type', ['class' => 'col-sm-2 control-label']) !!}
        	 <div class="col-sm-3">
        	 	{!! Form::select('discount_type',['1'=>'percetage Type','2'=>'Amount Type'], null, ['class' => 'form-control']) !!}
        	 </div>
        	 {!! Form::label('Discount', 'Discount', ['class' => 'col-sm-1 control-label']) !!}
        	 <div class="col-sm-3">
        	 	{!! Form::number('discount', null, ['class' => 'form-control']) !!}
        	 </div>
        	 <div class="col-sm-2">
        	 	<button class="btn btn-info btn-sm" type="submit">
                    <i class="icon-ok"></i>
                            Update
                </button>
        	 </div>
        </div>
</div>
 @endif

{!!Form::close()!!}