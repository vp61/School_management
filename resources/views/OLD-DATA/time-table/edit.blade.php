@extends('layouts.master')

@section('css')
	 <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}" />
	  <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-clockpicker.min.css') }}" />
	   <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-loader.css') }}" />
	 <style type="text/css">
    .sch_box{
      padding: 5%;
      background: #96eba8d4;
      text-align: center;
      font-weight: 600;
           
    }

	 </style>
@endsection

@section('content')
	<div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                @include('layouts.includes.template_setting')
                <div class="page-header">
                    <h1>
                        @include($view_path.'.includes.breadcrumb-primary')
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                           Edit Schedule
                        </small>
                    </h1>
                </div><!-- /.page-header -->
                <br>
                <br>
                @include('includes.flash_messages')
	              <div class="row">
	              		<div class="col-md-12 col-xs-12">
	              				
	              			{!!Form::open(['route'=>[$base_route.'.update',$id],'method'=>'POST','class'=> 'form-horizontal','id'=>'validaton-form',"enctype"=>"multipart/form-data"])!!}
						<div class="form-group">
							{!!Form::label('day','Select Day',					[	'class'=>'col-sm-2 control-label'])!!}
							<div class="col-sm-4">
								{!!Form::select('day',$data['day'],$data['schedule']->day_id,['class'=>' form-control','required'=>'required','id'=>'day'])!!}
							</div>
							{!!Form::label('time','Select time',					[	'class'=>'col-sm-2 control-label'])!!}
							<div class="col-sm-2">
								<div class="input-group clockpicker" data-placement="left" data-align="top" data-autoclose="true">
									<span class="input-group-addon">
									  <span class="">From</span>
									</span>
									<input type="time" class="form-control" value="{{$data['schedule']->time_from}}" name="from" id="from">	
								</div>

							</div>
							<div class="col-sm-2">
								<div class="input-group clockpicker" data-placement="left" data-align="top" data-autoclose="true">
									<span class="input-group-addon">
									  <span class="">To</span>
									</span>
									<input type="time" class="form-control" value="{{$data['schedule']->time_to}}" name="to" id="to">	
								</div>

							</div>
							
						</div>
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
						     <div class="col-sm-1"  id="notavailable" style="color:red;display: none">
		        				<span>Not Available</span>
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
									{!!Form::text('room',$data['schedule']->room_no,['class'=>' form-control','required'=>'required','id'=>'room'])!!}
								</div>
								<div class="col-sm-5">
									
								</div>
							</div>
						</div>	
						<div class="clearfix form-actions">
			                <div class="align-right">            &nbsp; &nbsp; &nbsp;
			                        <button class="btn btn-info" type="submit" id="submit">
			                               Update
			                        </button>
			                </div>
			        </div>  			
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
@endsection

@section('js')
	
	<script src="{{ asset('assets/js/select2.min.js') }}"></script>
	<script src="{{ asset('assets/js/bootstrap-clockpicker.min.js') }}"></script>
	<script type="text/javascript">
		
		$(document).ready(function(){
			$("#course").select2();
			$('.clockpicker').clockpicker();
			$("#from").on('change',function(){
          $("#staff").prop('selectedIndex',"");

		      }); 
		       $("#to").on('change',function(){
		          $("#staff").prop('selectedIndex',"");
		      }); 
		});
		 function checkTeacher(){
		 	var day=$("#day").val();
		 	var from=$("#from").val();
		 	var to=$("#to").val();
     		var staff=$('#staff').val();
     		 if(from.length==0 || to.length==0){
		         $("#submit").prop('disabled',true);
		         $("#select-time").css('display','block');
		       }else{
		        $("#submit").prop('disabled',false);
		        $("#select-time").css('display','none');
		       }
		 	document.getElementById('teacher-loader').style.display="block";
		 	$("#available").css('display','none');
                    	$("#notavailable").css('display','none');
		 	$.ajax({
                type: 'POST',
                url: '/api/timetable/checkTeacher',
                data: {
                    _token: '{{ csrf_token() }}',
                    staff_id: staff,
                    from :from,
                    to : to,
                    day : day
                },
                success: function (response) {
                    var data = $.parseJSON(response);
                    if (data.error) {
                        toastr.warning(data.message, "warning");
                        $("#subject").html('').append('<option value="">Select</option>');
                        $("#teacher-loader").css('display','none');
                    } else {

                      	$("#teacher-loader").css('display','none');                  	
                        if(data.teacher){
                          if(data['teacher'].length==0){
                            $("#available").css('display','block');
                            $("#submit").prop('disabled',false);
                            
                          }
                          else{
                            $("#notavailable").css('display','block');
                            $("#submit").prop('disabled',true);
                          }
                        }
                        if(data.subject){
                           $("#subject").html('').append('<option value="">--Select Subject--</option>');
                            $.each(data.subject,function(key,val){
                                 $('#subject').append('<option value="'+val.id+'">'+val.subject+'</option>');
                            });
                        	
                          }
                      }
                }
            });
		 }
	function is_break($this){
	      var is_break=$this.value;
	      if(is_break==1){
	        $("#subject").prop("required",false);
	         $("#type").prop("required",false);
	          $("#room").prop("required",false);
	          $("#on_break").hide();
	      }
	      if(is_break==0){
	         $("#subject").prop("required",true);
	         $("#type").prop("required",true);
	          $("#room").prop("required",true);
	          $("#on_break").show();
	      }
    }
     

	</script>
@endsection