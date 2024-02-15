<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/font-awesome/4.5.0/css/font-awesome.min.css') }}" />
	<title>Online Admission</title>
	<style >
		.main_div{
			border: 1px solid #0d1e73;
			padding: 30px;
			background: #f4f5f6d9;
			
		}
		body{
			background-image: url('{{$data['image']}}');
			background-size: cover;
			background-repeat: no-repeat;
			background-position: fixed;
			height: 100vh;		
		}
		.heading{
			color: #0d1e73;
			font-weight: 600;
		}
		.form_label{
			color: white;
		    background: #3853d6;
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
					<h1 class="heading">ASHA EDUCATIONAL SOCIETY<h1>
					<h3 class="heading">ONLINE ADMISSION</h3>
				</div>
			</div>
		</div>
		<div class="row main_div">
			<div class="col-md-12 col-xs-12">
				<div class="row">
					<div class="col-md-12 col-xs-12">
						<h4 class="heading bb">Personal Details</h4>
					</div>
				</div>
			</div>	
			<!-- PERSONAL DETAILS	 -->
			<div class="col-md-12 col-xs-12">
				<!-- Full NameROW -->
				<div class="row">
					<div class="col-md-4 col-xs-12">
							<div class="row">
								{!!Form::label('indos','INDoS No.*',['class'=>'col-md-4 col-xs-3 form_label'])!!}
								<div class="col-md-8 col-xs-9">
									{!!Form::text('indose_number',null,['class'=>' form-control','placeholder'=>'Enter INDOS NO.','required'=>'required'])!!}
								</div>
							</div>
					</div>
					<div class="col-md-4 col-xs-12">
						<div class="row">
							{!!Form::label('name','Full Name*',['class'=>'col-md-4 col-xs-3 form_label full-left'])!!}
							<div class="col-md-8 col-xs-9">
								{!!Form::text('first_name',null,['class'=>'form-control','placeholder'=>'Enter Full Name','required'=>'required'])!!}
							</div>
						</div>
					</div>
					<div class="col-md-4 col-xs-12">
						<div class="row">
							{!!Form::label('dob','DOB*',['class'=>'col-md-4 col-xs-3 form_label'])!!}
							<div class="col-md-8 col-xs-9">
								{!!Form::date('date_of_birth',null,['class'=>' form-control','placeholder'=>'Enter Date Of Birth','min'=>$data['min_age'],'max'=>$data['max_age'],'required'=>'required'])!!}
							</div>
						</div>
					</div>
				</div>	
				<!-- MOBILE NO ROW	 -->
				<div class="row">
					<div class="col-md-4 col-xs-12">
						<div class="row">
							{!!Form::label('mobile','Mobile No.*',['class'=>'col-md-4 col-xs-3 form_label'])!!}
							<div class="col-md-8 col-xs-9">
								{!!Form::number('mobile',null,['class'=>'form-control','placeholder'=>'Enter Mobile No.','required'=>'required'])!!}
							</div>
						</div>
					</div>
					<div class="col-md-4 col-xs-12">
						<div class="row">
							{!!Form::label('email','E-mail*',['class'=>'col-md-4 col-xs-3 form_label'])!!}
							<div class="col-md-8 col-xs-9">
								{!!Form::email('email',null,['class'=>'form-control','placeholder'=>'Enter E-mail','required'=>'required'])!!}
								
							</div>
						</div>
					</div>
				</div>
			</div>		
			<br>
			<div class="col-md-12 col-xs-12">
				<div class="row">
					<div class="col-md-12 col-xs-12">
						<h4 class="heading bb">{{ env('course_label') }} Details</h4>
					</div>
				</div>
			</div>
			<!-- COURSE DETAILS --Branch-->
			<div class="col-md-12 col-xs-12">
				<div class="row">
					{!!Form::label('branch','Select Branch',['class'=>'col-md-3 col-xs-3 form_label'])!!}
					<div class="col-md-9 col-xs-9">
						{!!form::select('branch',$data['branch'],null,['class'=>'form-control','onchange'=>'loadCourseType(this)'])!!}
					</div>
				</div>
			</div>
			<div class="col-md-12 col-xs-12">
				<div class="row">
					<div class="col-md-12 col-xs-12">
						<h4 class="heading">Select {{ env('course_label') }}/Batch <button class="btn btn-primary pull-right" id="new_button">Add New</button></h4>
					</div>
				</div>
			</div>
			<!-- SELECT COURSE -->
			<!-- <div class="col-md-12 col-xs-12">
				<div class="row">
					<div class="col-md-3 col-xs-12">
						<div class="row">
							{!!Form::label('course_type','Type.*',['class'=>'col-md-4 col-xs-3 form_label'])!!}
							<div class="col-md-8 col-xs-9">
								{!!Form::select('course_type',[''=>''],null,['class'=>' form-control','required'=>'required'])!!}
							</div>
						</div>
					</div>
					<div class="col-md-3 col-xs-12">
						<div class="row">
							{!!Form::label('course','Course*',['class'=>'col-md-4 col-xs-3 form_label'])!!}
							<div class="col-md-8 col-xs-9">
								{!!Form::select('course',[''=>''],null,['class'=>' form-control','required'=>'required'])!!}
							</div>
						</div>
					</div>
					<div class="col-md-3 col-xs-12">
						<div class="row">
							{!!Form::label('batch','Batch*',['class'=>'col-md-4 col-xs-3 form_label'])!!}
							<div class="col-md-8 col-xs-9">
								{!!Form::select('batch',[''=>''],null,['class'=>'form-control','required'=>'required'])!!}
							</div>
						</div>
					</div>
					<div class="col-md-3 col-xs-12">
						<div class="row">
							{!!Form::label('batch','Fee*',['class'=>'col-md-4 col-xs-3 form_label'])!!}
							<div class="col-md-8 col-xs-9">
								{!!Form::text('fee',null,['class'=>'form-control','disabled'=>'disabled'])!!}
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-12 col-xs-12">
				<div class="row">
					<div class="col-md-3 col-xs-12">
						<div class="row">
							{!!Form::label('course_type','Type.*',['class'=>'col-md-4 col-xs-3 form_label'])!!}
							<div class="col-md-8 col-xs-9">
								{!!Form::select('course_type',[''=>''],null,['class'=>' form-control','required'=>'required'])!!}
							</div>
						</div>
					</div>
					<div class="col-md-3 col-xs-12">
						<div class="row">
							{!!Form::label('course','Course*',['class'=>'col-md-4 col-xs-3 form_label'])!!}
							<div class="col-md-8 col-xs-9">
								{!!Form::select('course',[''=>''],null,['class'=>' form-control','required'=>'required'])!!}
							</div>
						</div>
					</div>
					<div class="col-md-3 col-xs-12">
						<div class="row">
							{!!Form::label('batch','Batch*',['class'=>'col-md-4 col-xs-3 form_label'])!!}
							<div class="col-md-8 col-xs-9">
								{!!Form::select('batch',[''=>''],null,['class'=>'form-control','required'=>'required'])!!}
							</div>
						</div>
					</div>
					<div class="col-md-3 col-xs-12">
						<div class="row">
							{!!Form::label('batch','Fee*',['class'=>'col-md-4 col-xs-3 form_label'])!!}
							<div class="col-md-8 col-xs-9">
								{!!Form::text('fee',null,['class'=>'form-control','disabled'=>'disabled'])!!}
							</div>
						</div>
					</div>
				</div>
			</div> -->
			<!-- SELECT COURSE TYPE HEADINGS -->
			<div class="col-md-12 col-xs-12">
				<div class="row">
					<div class="col-md-3 col-xs-3">
						<div class="row">
							{!!Form::label('branch',env('course_label').' Type',['class'=>'col-md-12 col-xs-12 form_label'])!!}
						</div>
					</div>
					<div class="col-md-3 col-xs-3">
						<div class="row">
							{!!Form::label('branch','Course',['class'=>'col-md-12 col-xs-12 form_label'])!!}
						</div>
					</div><div class="col-md-3 col-xs-3">
						<div class="row">
							{!!Form::label('branch','Batch',['class'=>'col-md-12 col-xs-12 form_label'])!!}
						</div>
					</div><div class="col-md-3 col-xs-3">
						<div class="row">
							{!!Form::label('branch','Fee',['class'=>'col-md-12 col-xs-12 form_label'])!!}
						</div>
					</div>									
				</div>
			</div>
			<!-- INPUT FIELDS -->
			<div id="input_div">
				<div class="col-md-12 col-xs-12 input_row" id="1">
					<div class="row">
						<div class="col-md-3 col-xs-3">
							<div class="row">
								<div class="col-md-12 col-xs-12">
									{!!Form::select('course_type[]',['abc'=>'aa'],null,['class'=>'form-control','id'=>'type'])!!}
								</div>
							</div>
						</div>
						<div class="col-md-3 col-xs-3">
							<div class="row">
								<div class="col-md-12 col-xs-12">
									{!!form::select('course[]',[''=>''],null,['class'=>'form-control'])!!}
								</div>
							</div>
						</div>
						<div class="col-md-3 col-xs-3">
							<div class="row">
								<div class="col-md-12 col-xs-12">
									{!!form::select('batch[]',[''=>''],null,['class'=>'form-control'])!!}
								</div>
							</div>
						</div>
						<div class="col-md-3 col-xs-3">
							<div class="row">
								<div class="col-md-12 col-xs-12">
									{!!form::select('fee[]',[''=>''],null,['class'=>'form-control'])!!}
								</div>
							</div>
						</div>									
					</div>
				</div>
			</div>
			<div class="col-md-12 col-xs-12">
				<div class="row">
					<div class="col-md-3 col-xs-3">
						<div class="row">
							<div class="col-md-12 col-xs-12">
								{!!form::select('branch',[''=>''],null,['class'=>'form-control'])!!}
							</div>
						</div>
					</div>
					<div class="col-md-3 col-xs-3">
						<div class="row">
							<div class="col-md-12 col-xs-12">
								{!!form::select('branch',[''=>''],null,['class'=>'form-control'])!!}
							</div>
						</div>
					</div>
					<div class="col-md-3 col-xs-3">
						<div class="row">
							<div class="col-md-12 col-xs-12">
								{!!form::select('branch',[''=>''],null,['class'=>'form-control'])!!}
							</div>
						</div>
					</div>
					<div class="col-md-3 col-xs-3">
						<div class="row">
							<div class="col-md-12 col-xs-12">
								{!!form::select('branch',[''=>''],null,['class'=>'form-control'])!!}
							</div>
						</div>
					</div>									
				</div>
			</div>
		</div>
	</div>
	<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
	<script src="{{ asset('assets/js/jquery-2.1.4.min.js') }}"></script>
<script>
	function loadCourseType($this){ 
		$.ajax({
			type: 'POST',
			url: "{{route('OnlineAdmission.loadCourseType')}}",
			data:{
				_token: "{{csrf_token()}}",
				branch_id : $this.value
			},
			success:function(response){
				var data= $.parseJSON(response);
			}
		});
	}
	$('#new_button').click(function(){
		var branch_id=document.getElementById('branch').value;
		var a=$("#input_div:last #1");
		alert(a:last select);
	});
</script>
</body>

</html>