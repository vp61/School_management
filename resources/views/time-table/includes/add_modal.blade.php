<div class="modal fade " id="addModal" role="dialog">
	<div class="modal-dialog modal-lg" style="width: 75% !important;">
		
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title blue">Add Schedule</h4>
			</div>
			{!!Form::open(['route'=>$base_route.'.add','method'=>'POST','class'=> 'form-horizontal','id'=>'validaton-form',"enctype"=>"multipart/form-data"])!!}
			<div class="modal-body" >
				<div class="form-group">
					 {!!Form::label('course','Select '.env('course_label'),['class'=>'col-sm-2 control-label'])!!}
      				  <div class="col-sm-4">
      				  	{!! Form::select('course', $data['course'], null, ['class' => 'form-control','id'=>'course','onChange'=>'loadSectionForModal(this)','required'=>'required','style'=>'width:100%;']) !!}
      				  </div>
      				 {!!Form::label('section','Select Section',['class'=>'col-sm-2 control-label'])!!}
      				  <div class="col-sm-4">
      				  	{!! Form::select('section', $data['section'], null, ['class' => 'form-control','id'=>'section','required'=>'required']) !!}
      				  </div>
				</div>
				<div class="form-group">
					{!!Form::label('day','Select Day',					[	'class'=>'col-sm-2 control-label'])!!}
					<div class="col-sm-4">
						{!!Form::select('day',$data['day'],null,['class'=>' form-control','required'=>'required','id'=>'day'])!!}
					</div>
					{!!Form::label('time','Select time',					[	'class'=>'col-sm-2 control-label'])!!}
					<div class="col-sm-2">
						<div class="input-group clockpicker" data-placement="left" data-align="top" data-autoclose="true">
							<span class="input-group-addon">
							  <span class="">From</span>
							</span>
							<input type="time" class="form-control" value="" name="from" id="from">	
						</div>

					</div>
					<div class="col-sm-2">
						<div class="input-group clockpicker" data-placement="left" data-align="top" data-autoclose="true">
							<span class="input-group-addon">
							  <span class="">To</span>
							</span>
							<input type="time" class="form-control" value="" name="to" id="to">	
						</div>

					</div>
					
				</div>
				<div class="form-group">
					{!!Form::label('is_break','Is Break',					[	'class'=>'col-sm-2 control-label'])!!}
					<div class="col-sm-4">
						<label class="radio-inline">	{!!Form::radio('break','1',null,['required'=>'required','onchange'=>'is_break(this)'])!!}Yes</label>
							<label class="radio-inline">
								{!!Form::radio('break','0',null,['onchange'=>'is_break(this)'])!!}No
							</label>
					</div>
					
				</div>
				<div id="on_break">
						<div class="form-group">
							{!!Form::label('teacher','Select Teacher',['class'=>'col-sm-2 control-label'])!!}
							<div class="col-sm-4">
								{!!Form::select('teacher',$data['teacher'],null,['class'=>' form-control','required'=>'required','onchange'=>'checkTeacher()','id'=>'staff'])!!}
							</div>
							<div class="col-sm-1"  id="teacher-loader" style="color: blue;display: none">
		        				<div class="loader" id="loader-4">
							          <span>Checking..</span>		
						        </div>
						    </div>

						    <div class="col-sm-1"  id="available" style="color:green;display: none">
		        				<span>Available</span>
						    </div>
						    <div class="col-sm-3"  id="select-time" style="color:red;display: none">
		        				<span>Please Select Time</span>
						    </div>
						     <div class="col-sm-2"  id="notavailable" style="color:red;display: none">
		        				<span>Not Available</span>
						    </div>
						</div>
				
					
						<div class="form-group">
							{!!Form::label('subject','Select Subject',					[	'class'=>'col-sm-2 control-label'])!!}
							<div class="col-sm-4">
								{!!Form::select('subject',[""=>'Select'],null,['class'=>' form-control','required'=>'required','id'=>'subject'])!!}
							</div>
							{!!Form::label('sub_type','Subject Type',['class'=>'col-sm-2 control-label'])!!}
							<div class="col-sm-4">
								
									<label class="radio-inline">	{!!Form::radio('type','Theory',null,['required'=>'required','id'=>'type'])!!}Theory</label>
									<label class="radio-inline">
										{!!Form::radio('type','Practical')!!}Practical
									</label>		
							</div>
						</div>
						<div class="form-group">
							{!!Form::label('secondary','Secn. Teacher',					[	'class'=>'col-sm-2 control-label'])!!}
							<div class="col-sm-4">
								{!!Form::select('secondary_teacher',$data['teacher'],null,['class'=>' form-control','id'=>'secondary_staff'])!!}
							</div>
							{!!Form::label('from_date','Schedule From',					[	'class'=>'col-sm-2 control-label'])!!}
							<div class="col-sm-4">
								{!!Form::date('s_staff_from',null,['class'=>' form-control','id'=>'s_staff_from'])!!}
							</div>
						</div>
						<div class="form-group">
							{!!Form::label('to_date','Schedule To',					[	'class'=>'col-sm-2 control-label'])!!}
							<div class="col-sm-4">
								{!!Form::date('s_staff_to',null,['class'=>' form-control','id'=>'s_staff_to'])!!}
							</div>
							{!!Form::label('room','Room No.',					[	'class'=>'col-sm-2 control-label'])!!}
							<div class="col-sm-4">
								{!!Form::text('room',null,['class'=>' form-control','required'=>'required','id'=>'room'])!!}
							</div>
						</div>
					 </div>	
				</div>
			<div class="modal-footer">
				<button type="reset" class="btn">RESET</button>
				<button type="submit" class="btn btn-primary" id="submit">SAVE</button>
			</div>
			{!!Form::close()!!}
		</div>
	</div>
	
</div>