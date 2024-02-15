  
	<div class="form-group">
		{!!Form::label('course',env('course_label'),['class'=>'col-sm-6 control-label'])!!}
		<div class=" col-md-6">
			<!-- load section code -->
			{{ Form::select('course', $data['course'], '', ['class'=>'form-control', 'required'=>'required','onchange'=>'loadSemesters(this)'])}}
			<!-- load section code -->
		</div>
	</div>
	<div class="form-group">
		{!!Form::label('section','Section',['class'=>'col-sm-6 control-label'])!!}
		<div class="col-md-6">
			<!-- assign subject to class/course -->
			{!!Form::select('section[]',$data['section'],null,['class'=>'form-control selectpicker semester_select','required'=>'required','data-live-search'=>'true','multiple'=>'multiple'])!!}
			<!-- assign subject to class/course -->
		</div>
	</div> 
	<!-- assign subject to class/course --> 
		{!!Form::label('subject','Subject',['class'=>'col-sm-6 control-label'])!!}
	<div class="form-group">
		<div class="col-md-6">
			
				<table class="table" >
				@foreach( $data['allsubject'] as $k=>$v)

				<tr>
					<td  style="border:none;">
					<label class="custom-control-label nowrap" for="customCheck{{$v->id}}">
					<input type="checkbox" class="custom-control-input " id="customCheck{{$v->id}}" name="subject[]" value="{{$v->id}}">
                     {{$v->title}}</label></td>
                      <!-- subjectPriority -->
                     <td style="border:none;">
                     	<input type="number" name="sub_priority[{{$v->id}}]" class="form-control">
                     	
                     </td>
                     <!-- subjectPriority -->
                 </tr>

				@endforeach
			  </table>
			
			
		</div>
	</div>
	<!-- assign subject to class/course -->
	


	 

	<div class="align-right">
	            <button type="submit" class="btn btn-sm " >
	                <i class="fa fa-plus bigger-120"></i> Add Subject
	            </button>
	        </div>

