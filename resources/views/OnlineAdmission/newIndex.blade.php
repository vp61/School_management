<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/font-awesome/4.5.0/css/font-awesome.min.css') }}" />
    <link href="{{ asset('assets/css/toastr.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />
	<title>Online Admission</title>
	<style >
		.main_div{
			border: 1px solid #0d1e73;
			padding: 30px;
			background: #f4f5f6d9;
			
		}
		hr{
		    margin-top: 20px;
		    margin-bottom: 20px;
		    border-top: 1px solid #fff;
		}
		body{
			background-image: url('{{$data['image']}}');
			background-size: cover;
			background-repeat: no-repeat;
			background-position: fixed;
			min-height: 100vh;		
		}
		.heading{
			color: #0d1e73;
			font-weight: 600;
		}
		.form_label{
			color: black;
		    background: #f0ad4e;
		    font-weight: 500;
		    font-size: 15px;
		    padding: 5px 12px;
		    border-left: 4px #fdc06b solid;
		    border-right: 1px #fdc06b solid
		}
		.bb{
			border-bottom: 1px solid; 
		}
		.nopad [class*='col-md']{
			padding-right: 0px;
		}
		.req{
			color: red;
		}
	</style>
</head>
<body >
	<div class="container">
		<div class="row">
			<div class="col-sm-12 col-12 text-center">
				<div class="pull-left">
					<img src="{{asset('assets/images/marine_bg/AEG_LOGO.png')}}" height="100px">
				</div>
				<div class="">
					<h2 class="heading">ASHA INTERNATIONAL INSTITUTE OF MARINE TECHNOLOGY<h2>
					<h2 class="heading">ONLINE ADMISSION</h2>
				</div>
			</div>
		</div>
	{!!Form::open(['route'=>'OnlineAdmission','method'=>'POST', 'class' => 'form-horizontal',
                                        'id' => 'validation-form', "enctype" => "multipart/form-data"])!!}
		<div class="row main_div">
			@include('includes.validation_error_messages')
			@include('includes.flash_messages')
			<!-- <div class="col-md-12 col-xs-12">
				<div class="row">
					<div class="col-md-12 col-xs-12">
						<h4 class="heading bb">Online Admision Form</h4>
					</div>
				</div>
			</div>	 -->
			
			
		<div class="label label-warning arrowed-in arrowed-right arrowed">Select Type</div>
		<div class="col-md-12 col-xs-12">
			<div class="row">
				<div class="form-group">
					<label class=' col-md-2 col-xs-3 control-label'>Please Select Type<b class="req"> *</b></label>
					<div class="col-md-9 col-xs-9">
						{!!Form::select('student_type',[""=>'--Select Type--',"1"=>'Pre-Sea','2'=>'Post-Sea'],"",['class'=>'form-control','id'=>'std_type','onChange'=>'getBranchBySeaType(this)','required'=>'required'])!!}
					</div>
				</div>
			</div>
		</div>	

		<hr class="hr-8">
		<!-- COURSE DETAILS --Branch-->
		<h4 class="label label-warning arrowed-in arrowed-right arrowed" >Course Detail
		</h4>
			<div class="col-md-12 col-xs-12">
				<div class="row">
					<div class="form-group">
						<label class=' col-md-2 col-xs-3 control-label'>Select Branch<b class="req"> *</b></label>
					<div class="col-md-9 col-xs-9">
						{!!form::select('branch',[''=>'Select Branch'],null,['class'=>'form-control','onchange'=>'loadCourse(this)','required','id'=>'branch'])!!}
					</div>
					</div>
					
				</div>
			</div>
		<!-- div class="row">
			<div class="col-md-12 col-xs-12">
				<button class="btn btn-primary pull-right" id="new_button" type="button" onclick="addNewRow()"><i class="fa fa-plus"></i> Add New Course</button>
			</div>
			
		</div -->
			<!-- <div class="row">
				<div class="form-group">
					{!!Form::label('branch',env('course_label'),['class'=>'col-md-3 col-xs-3 form_label control-label text-center'])!!}
					<div class="col-md-9 col-xs-9">
						{!!form::select('branch',$data['branch'],null,['class'=>'form-control','onchange'=>'loadCourseType(this)'])!!}
					</div>					
				</div>
			</div> -->
			<br>
			
			<div style="display: none" id="course_batch_hidden_row">
				<div class="col-md-12 col-xs-12 input_row " id="1">
					<div class="row">
						<div class="form-group">
							<label class=' col-md-2 col-xs-3 control-label text-center'>Select {{env('course_label')}}<b class="req"> *</b></label>
							<div class="col-md-9 col-xs-7">
								{!!form::select('course[]',[""=>'Select Course'],null,['class'=>'form-control courses add_require'])!!}
							</div>	
							<div class="col-md-1 col-xs-2"><i class="fa fa-trash pull-right delete_row btn btn-danger btn-minier" onclick="closest('.input_row').remove();"></i>
								</div>				
						</div>
					</div>
					
				</div>
			</div>
			<div  id="course_batch_visible_row">
				<div class="col-md-12 col-xs-12 input_row" id="1">
					<div class="row">
						<div class="form-group">
							<label class=' col-md-2 col-xs-3 control-label text-center'>Select {{env('course_label')}}<b class="req"> *</b></label>
							
							<div class="col-md-9 col-xs-7">
								{!!form::select('course[]',[""=>'Select Course'],null,['class'=>'form-control courses','required'=>'required'])!!}
							</div>				
							<div class="col-md-1 col-xs-2">
							    <button class="btn btn-primary pull-right" id="new_button" type="button" onclick="addNewRow()"><i class="fa fa-plus"></i> Add</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		<hr class="hr-8">
		
			<!-- PERSONAL DETAILS	 -->
			<div class="label label-warning arrowed-in arrowed-right arrowed">Student Details</div>
			<hr class="hr-8">
			<div class="row">
				<div class="form-group">

				    <label class=' col-md-2 col-xs-3 control-label'>Student's Name<b class="req"> *</b></label>
				    <div class="col-md-4 col-xs-9">
				        {!! Form::text('first_name',null, ["class" => "form-control border-form upper" ,"required"]) !!}
				        @include('includes.form_fields_validation_message', ['name' => 'first_name'])
				    </div>
				    
				    {!! Form::label('father_name', "Father's Name", ['class' => 'col-md-2 col-xs-3 control-label']) !!}
				    <div class="col-md-4 col-xs-9">
				        {!! Form::text('father_first_name',null, [ "class" => "form-control border-form upper"]) !!}
				        @include('includes.form_fields_validation_message', ['name' => 'father_first_name'])
				    </div>
				</div>
			</div>
			<div class="row">
				<div class="form-group">
					<label class=' col-md-2 col-xs-3 control-label'>DOB<b class="req"> *</b></label>
				    <div class="col-md-4 col-xs-9">
				     {!! Form::date('date_of_birth',null, ["class" => "form-control border-form upper",'required','max'=>$data['max_age']]) !!}
				        
				    </div>
				    <label class=' col-md-2 col-xs-3 control-label'>Gender<b class="req"> *</b></label>
				    <div class="col-md-4 col-xs-9">
				        {!! Form::select('gender', ['' => 'Select Gender','MALE' => 'MALE', 'FEMALE' => 'FEMALE', 'OTHER' => 'OTHER'],null, ['class'=>'form-control border-form','required']); !!}
				    </div>
				</div>
			</div>
			<div class="label label-warning arrowed-in arrowed-right arrowed">Contact Details</div>
			<hr class="hr-8">
			<div class="row">
				<div class="form-group">
					<label class=' col-md-2 col-xs-3 control-label'>Mobile<b class="req"> *</b></label>
				    <div class="col-md-4 col-xs-9"> 
				        {!! Form::number('mobile_1',null, ["class" => "form-control border-form input-mask-mobile mobileKValidationCheck" ,"required","maxlength"=>"10",'min'=>0]) !!}
				    </div>
					<label class=' col-md-2 col-xs-3 control-label'>E-mail<b class="req"> *</b></label>
				    <div class="col-md-4 col-xs-9">
				        {!! Form::email('email',null, ["class" => "form-control border-form",'required']) !!}
				        
				    </div>  
				</div>
				<div class="form-group">  
					 <label class=' col-md-2 col-xs-3 control-label'>Aadhaar No.</label>
				    <div class="col-md-4 col-xs-9">
				        {!! Form::number('aadhar_no',null, ["class" => "form-control border-form","min"=>0]) !!}
				       
				    </div> 
				    <div id="indos">
					    <label class=' col-md-2 col-xs-3 control-label post_icon'>Indos No.<b class="req ">*</b></label>
					    <label class=' col-md-2 col-xs-3 control-label pre_icon' style="display: none;">Indos No.</label>
					    <div class="col-md-4 col-xs-9">
					        {!! Form::text('indose_number',null, ["class" => "form-control border-form post_req",'required','pattern'=>'[A-Za-z0-9]+','title'=>'Alpha Numeric Only']) !!}
					        <i class="post_icon" style="display: none;">Please contact to AIIMT for further details</i>
					    </div>
				    </div>  
				</div>
			</div>
		<h4 class="label label-warning arrowed-in arrowed-right arrowed" >Address Details
		</h4>
		<hr class="hr-8">
		<div class="form-group">
			<label class=' col-md-2 col-xs-3 control-label'>Address<b class="req"> *</b></label>
		    <div class="col-md-4 col-xs-9">
		        {!! Form::text('address',null, ["class" => "form-control border-form upper"]) !!}
		    </div>

		    {!! Form::label('state', 'State', ['class' => 'col-md-2 col-xs-3 control-label']) !!}
		    <div class="col-md-4 col-xs-9">
		        {!! Form::text('state', null, ["class" => "form-control border-form upper"]) !!}
		    </div>
		</div>
		<div class="form-group">
		    {!! Form::label('country', 'Country', ['class' => 'col-md-2 col-xs-3 control-label']) !!}
		    <div class="col-md-4 col-xs-9">
		       {!! Form::text('country',null, ['class'=>'form-control border-form']); !!}
		    </div>
		    {!! Form::label('zip', 'Zip', ['class' => 'col-md-2 col-xs-3 control-label']) !!}
		    <div class="col-md-4 col-xs-9">
		        {!! Form::number('zip',null, ["class" => "form-control border-form upper"]) !!}
		    </div>
		</div>
		<h4 class="label label-warning arrowed-in arrowed-right arrowed" >Upload Documents
		</h4>
		<hr class="hr-8">
		<div class="row">
			<div class="form-group">
				<label class=' col-md-2 col-xs-3 control-label'>High School<b class="req"> *</b></label>
			 	 <div class="col-md-4 col-xs-9">
			        {!! Form::file('high_school', ["class" => "form-control border-form upper",'required']) !!}
			    </div>
			    <label class=' col-md-2 col-xs-3 control-label'>Intermediate<b class="req"> *</b></label>
			 	 <div class="col-md-4 col-xs-9">
			        {!! Form::file('intermediate', ["class" => "form-control border-form upper",'required']) !!}
			    </div>
			</div>
		</div>
		<div class="row">
			<div class="form-group">
				<label class=' col-md-2 col-xs-3 control-label'>Aadhaar</label>
			 	 <div class="col-md-4 col-xs-9">
			        {!! Form::file('aadhaar', ["class" => "form-control border-form upper"]) !!}
			    </div>
			    <label class='col-md-2 col-xs-3 control-label post_icon'>Passport Image<b class="req"> *</b></label>
			    <label class='col-md-2 col-xs-3 control-label pre_icon' style="display: none;">Passport Image</label>
			 	 <div class="col-md-4 col-xs-9">
			        {!! Form::file('pass_port', ["class" => "form-control border-form upper post_req",'required']) !!}
			    </div>
			</div>
		</div>
		
		
		
		<div class="row">
			<div class="form-group">
				<div class="col-sm-12 col-xs-12">
					<br>
					<br>
					<button class="btn btn-success pull-right">Proceed</button>
				</div>
			</div>
		</div>
	</div>		
			
</div>
	<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
	<script src="{{ asset('assets/js/jquery-2.1.4.min.js') }}"></script>

<script>
	$("#std_type").prop("selectedIndex",'');
	function loadCourse($this){ 
		var sea_type = $('#std_type').val();
		$.ajax({
			type: 'POST',
			url: "{{route('OnlineAdmission.loadCourse')}}",
			data:{
				_token: "{{csrf_token()}}",
				branch_id : $this.value,
				sea_type : sea_type,
				admission_session: "{{$data['admission_session']->online_admission_session}}",
			},
			success:function(response){
				$(".courses").html('').append("<option value=''>Select</option>");
				var data= $.parseJSON(response);
				if(data.error){
					toastr.warning(data.msg,"WARNING");
				}
				else{
					toastr.success(data.msg,"Success");
					$(".courses").html('').append('<option value="">--Select Course--</option>');
					$.each(data.data,function(key,v){
						// VALUES==COURSE_id,BATCH_ID,FEE_AMOUNT,FEE_HEAD_ID
						 if(data.session == 1) {
							$(".courses").append('<option value="'+v.course_id+','+v.course_batch_id+','+v.fee+','+data.session+'">'+v.course_title+' |  '+ ' Batch : '+v.course_batch_title+' | '+' Fee: Rs. '+v.fee+' | Available Seats : ('+v.available+')</option>');
						} else {	
							$(".courses").append('<option value="'+v.course_id+','+v.course_batch_id+','+v.fee+','+data.session+'">'+v.course_title+' | '+' Fee: Rs. '+v.fee+' </option>');
						 } 
					})

				}
			}
		});
	}
	function getBranchBySeaType($this){ 
		var value=$this.value;
		$(".courses").html('').append('<option value="">Select Course</option>')
		if(value==2){
			$(".post_icon").css({"display":"block"});
			$(".pre_icon").css({"display":"none"});
			$(".post_req").attr("required",true);
		}else if(value==1){
			$(".post_icon").css({"display":"none"});
			$(".pre_icon").css({"display":"block"});
			$(".post_req").attr("required",false);
		}
		$.ajax({
			type: 'POST',
			url: "{{route('OnlineAdmission.getBranchBySeaType')}}",
			data:{
				_token: "{{csrf_token()}}",
				sea_type : value
			},
			success:function(response){
				var data = $.parseJSON(response);
				$('#branch').html('').append('<option value=""> --Select Branch--</option>');
				if(data.error){
					toastr.warning(data.msg,"WARNING");
				}else{
					toastr.success(data.msg,"Success");
					$.each(data.data,function($k,$v){
						$('#branch').append('<option value="'+$v.id+'">'+$v.branch_name+'</option>');
					});
				}
				
			}
		});
	}
	function addNewRow(){
		var data=$("#course_batch_hidden_row").html();
		var replace=data.replace('add_require','required_attribute');
		$("#course_batch_visible_row").append(replace);
		$(".required_attribute").attr("required",true);
		
	}
</script>
<script src="{{ asset('assets/js/toastr.min.js') }}"></script>
</body>

</html>