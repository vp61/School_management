<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/font-awesome/4.5.0/css/font-awesome.min.css') }}" />
    <link href="{{ asset('assets/css/toastr.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-select.min.css') }}" />
	<title>CAREER</title>
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
			/*background-image: url('{{$data['image']}}');*/
			background-size: cover;
			background-repeat: no-repeat;
			background-position: fixed;
			min-height: 100vh;
			background: #213442;
		}
		.heading{
			color: #ffffff;
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
				   
					<img src="{{asset('images/logo/')}}/{{$data['branch']->branch_logo}}" height="100px">
				</div>
				<div class="">
					<h2 class="heading">{{ env('APPLICATION_TITLE') }}<h2>
					<h2 class="heading">{{$data['branch']->branch_address}}</h2>
				</div>
			</div>
		</div>
	{!!Form::open(['route'=>'career','method'=>'POST', 'class' => 'form-horizontal',
                                        'id' => 'validation-form', "enctype" => "multipart/form-data"])!!}


		<div class="row main_div">
			@include('includes.validation_error_messages')
			@include('includes.flash_messages')
		
		   <!-- COURSE DETAILS --Branch-->
			<h3 class="heading"><center>SCHOOL RECRUITMENT FORM (TEACHING)</center></h3><hr>
			<div class="label label-warning arrowed-in arrowed-right arrowed">Personal Information</div>
			<div class="row">
				<div class="form-group">

				    <label class=' col-md-2 col-xs-3 control-label'>Candidate Name<b class="req"> *</b></label>
				    <div class="col-md-4 col-xs-9">
				    	<input type="hidden" name="type" value="{{$data['type']}}">
				        {!! Form::text('candidate_name',null, ["class" => "form-control border-form upper" ,"required"]) !!}
				        @include('includes.form_fields_validation_message', ['name' => 'candidate_name'])
				    </div>
				     <label class=' col-md-2 col-xs-3 control-label'>Father/Husband Name<b class="req"> *</b></label>
				    <div class="col-md-4 col-xs-9">
				        {!! Form::text('father_name',null, [ "class" => "form-control border-form upper"]) !!}
				        @include('includes.form_fields_validation_message', ['name' => 'father_name'])
				    </div>
		       </div>
			</div>
			<div class="row">
				<div class="form-group">

				    <label class=' col-md-2 col-xs-3 control-label'> Email<b class="req"> *</b></label>
				    <div class="col-md-4 col-xs-9">
				        {!! Form::email('email',null, ["class" => "form-control border-form upper" ,"required"]) !!}
				        @include('includes.form_fields_validation_message', ['name' => 'email'])
				    </div>
				     <label class=' col-md-2 col-xs-3 control-label'> Gender<b class="req"> *</b></label>
				    <div class="col-md-4 col-xs-9">
				        {!! Form::select('gender',['' => 'Select Gender','Female'=>'Female','Male'=>'Male','Other'=>'Other'],null, [ "class" => "form-control border-form upper"]) !!}
				        @include('includes.form_fields_validation_message', ['name' => 'gender'])
				    </div>
		       </div>
			</div>
			<div class="row">
				<div class="form-group">

				    <label class=' col-md-2 col-xs-3 control-label'> Date Of Birth<b class="req"> *</b></label>
				    <div class="col-md-4 col-xs-9">
				        {!! Form::date('dob',null, ["class" => "form-control border-form upper" ,"required"]) !!}
				        @include('includes.form_fields_validation_message', ['name' => 'dob'])
				    </div>
				     <label class=' col-md-2 col-xs-3 control-label'>Contact No.<b class="req"> *</b></label>
				    <div class="col-md-4 col-xs-9">
				      {!! Form::number('mobile',null, ["class" => "form-control border-form input-mask-mobile mobileKValidationCheck max10Legth onlyNumber" ,"required",'min'=>0,'autocomplete'=>"false"]) !!}
				        @include('includes.form_fields_validation_message', ['name' => 'mobile'])
				    </div>
		       </div>
			</div>
			<div class="row">
				<div class="form-group">
				    <label class=' col-md-2 col-xs-3 control-label'>Permanent Address<b class="req"> *</b></label>
				    <div class="col-md-10 col-xs-9">
				        {!! Form::textarea('per_add',null, ["class" => "form-control border-form upper" ,"required",'rows'=>'3']) !!}
				        @include('includes.form_fields_validation_message', ['name' => 'per_add'])
				    </div>
		       </div>
			</div>
			<div class="label label-warning arrowed-in arrowed-right arrowed">Post Information</div>
			<div class="row">
				<div class="form-group">
			        <label class=' col-md-2 col-xs-3 control-label'>Mother Teacher<b class="req"> *</b></label>
				    <div class="col-md-4 col-xs-9">
				        {!! Form::select('mother_teacher',[''=>'-- Select --','Yes'=>'Yes','No'=>'No'],null, [ "class" => "form-control border-form",'required'=>'required']) !!}
				        @include('includes.form_fields_validation_message', ['name' => 'mother_teacher'])
				    </div>
				     <label class=' col-md-2 col-xs-3 control-label'>Experience<b class="req"> *</b></label>
				    <div class="col-md-4 col-xs-9">
				        {!! Form::select('experience',[''=>'-- Select --','1 Year'=>'1 Year','2 Year'=>'2 Year','3 Years & above'=>'3 Years & above'],null, [ "class" => "form-control border-form",'required'=>'required']) !!}
				        @include('includes.form_fields_validation_message', ['name' => 'experience'])
				    </div>
		        </div>
			</div>
			<div class="row">
				<div class="form-group">
			        <label class=' col-md-2 col-xs-3 control-label'>PRT (3 years teaching experience with B.Ed.)</label>
				    <div class="col-md-4 col-xs-9">
				        {!! Form::select('prt',[''=>'-- Select --','Hindi'=>'Hindi','English'=>'English','Math'=>'Math','Science'=>'Science'],null, [ "class" => "form-control border-form"]) !!}
				        @include('includes.form_fields_validation_message', ['name' => 'prt'])
				    </div>
				     <label class=' col-md-2 col-xs-3 control-label'>TGT (3 years teaching experience with B.Ed.)</label>
				    <div class="col-md-4 col-xs-9">
				        {!! Form::select('tgt',[''=>'-- Select --','Hindi'=>'Hindi','English'=>'English','Math'=>'Math','Science'=>'Science','Social Science'=>'Social Science','Computer Science'=>'Computer Science'],null, [ "class" => "form-control border-form"]) !!}
				        @include('includes.form_fields_validation_message', ['name' => 'tgt'])
				    </div>
		        </div>
			</div>
			<div class="row">
				<div class="form-group">
			        <label class=' col-md-2 col-xs-3 control-label'>PGT (3 years teaching experience with B.Ed.)</label>
				    <div class="col-md-4 col-xs-9">
				        {!! Form::select('pgt',[''=>'-- Select --','Physics'=>'Physics','Chemistry'=>'Chemistry','Biology'=>'Biology','Mathematics'=>'Mathematics','History'=>'History','Geography'=>'Geography','Political Science'=>'Political Science','Psychology cum School counsellor'=>'Psychology cum School counsellor','Accountancy'=>'Accountancy','Business Studies'=>'Business Studies','Economics'=>'Economics','Computer Science'=>'Computer Science'],null, [ "class" => "form-control border-form"]) !!}
				        @include('includes.form_fields_validation_message', ['name' => 'pgt'])
				    </div>
			</div>
			<div class="label label-warning arrowed-in arrowed-right arrowed">Educational Qualification</div>

			<div class="row">
				<div class="form-group">
			        <label class=' col-md-2 col-xs-3 control-label'>NTT<b class="req"> *</b></label>
				    <div class="col-md-4 col-xs-9">
				        {!! Form::select('ntt',[''=>'-- Select --','Yes'=>'Yes','No'=>'No'],null, [ "class" => "form-control border-form",'required'=>'required']) !!}
				        @include('includes.form_fields_validation_message', ['name' => 'ntt'])
				    </div>
		        </div>
			</div>
			<div class="row">
				<div class="form-group">
				      <label class=' col-md-2 col-xs-3 control-label'>Graduation<b class="req"> *</b></label>
				    <div class="col-md-10 col-xs-9">
				        {!! Form::select('graduation',[''=>'-- Select --','B.Sc.'=>'B.Sc.','B.Com'=>'B.Com','B.A.'=>'B.A.','B.C.A.'=>'B.C.A.','B.Lib'=>'B.Lib','B.Ped'=>'B.Ped'],null, [ "class" => "form-control border-form",'required'=>'required']) !!}
				        @include('includes.form_fields_validation_message', ['name' => 'graduation'])
				    </div>
		        </div>
			</div>
			<div class="row">
				<div class="form-group">
				    <label class=' col-md-2 col-xs-3 control-label'>Graduation Subject<b class="req"> *</b></label>
				    <div class="col-md-4 col-xs-9">
				        {!! Form::text('graduation_subject',null, ["class" => "form-control border-form upper",'required'=>'required']) !!}
				        @include('includes.form_fields_validation_message', ['name' => 'graduation_subject'])
				    </div>
				     <label class=' col-md-2 col-xs-3 control-label'>Graduation Percentage (Rounded)<b class="req"> *</b></label>
				    <div class="col-md-4 col-xs-9">
				        {!! Form::number('graduation_percentage',null, [ "class" => "form-control onlyNumber border-form upper",'min'=>'0','required'=>'required']) !!}
				        @include('includes.form_fields_validation_message', ['name' => 'graduation_percentage'])
				    </div>
		       </div>
			</div>

			<div class="row">
				<div class="form-group">
				     <label class=' col-md-2 col-xs-3 control-label'>Post Graduation</label>
				    <div class="col-md-10 col-xs-9">
				        {!! Form::select('post_graduation',[''=>'-- Select --','M.Sc.'=>'M.Sc.','M.Com'=>'M.Com','M.A.'=>'M.A.','M.C.A.'=>'M.C.A.','M.Lib'=>'M.Lib','M.Ped'=>'M.Ped'],null, [ "class" => "form-control border-form"]) !!}
				        @include('includes.form_fields_validation_message', ['name' => 'post_graduation'])
				    </div>
		        </div>
			</div>
			
			<div class="row">
				<div class="form-group">
				    <label class=' col-md-2 col-xs-3 control-label'>PG Pursuing Year</label>
				    <div class="col-md-4 col-xs-9">
				        {!! Form::select('post_graduation_pursuing_year',$data['year_list'],null, ["class" => "form-control border-form upper"]) !!}
				        @include('includes.form_fields_validation_message', ['name' => 'post_graduation_pursuing_year'])
				    </div>
				     <label class=' col-md-2 col-xs-3 control-label'>Is Completed?</label>
				    <div class="col-md-4 col-xs-9">
				        {!! Form::select('is_pg_completed',[''=>'-- Select --','Yes'=>'Yes','No'=>'No'],null, [ "class" => "form-control border-form upper"]) !!}
				        @include('includes.form_fields_validation_message', ['name' => 'is_pg_completed'])
				    </div>
		       </div>
			</div>

			<div class="row">
				<div class="form-group">
				    <label class=' col-md-2 col-xs-3 control-label'>PG Subject</label>
				    <div class="col-md-4 col-xs-9">
				        {!! Form::text('post_graduation_subject',null, ["class" => "form-control border-form upper"]) !!}
				        @include('includes.form_fields_validation_message', ['name' => 'graduation_subject'])
				    </div>
				     <label class=' col-md-2 col-xs-3 control-label'>PG Percentage (Rounded)</label>
				    <div class="col-md-4 col-xs-9">
				        {!! Form::number('post_graduation_percentage',null, [ "class" => "form-control onlyNumber border-form upper",'min'=>'0']) !!}
				        @include('includes.form_fields_validation_message', ['name' => 'graduation_percentage'])
				    </div>
		       </div>
			</div>

			<div class="row">
				<div class="form-group">
				    <label class=' col-md-2 col-xs-3 control-label'>B.Ed. Pursuing Year</label>
				    <div class="col-md-4 col-xs-9">
				        {!! Form::select('b_ed_pursuing_year',$data['year_list'],null, ["class" => "form-control border-form upper"]) !!}
				        @include('includes.form_fields_validation_message', ['name' => 'b_ed_pursuing_year'])
				    </div>
				     <label class=' col-md-2 col-xs-3 control-label'>Is Completed?</label>
				    <div class="col-md-4 col-xs-9">
				        {!! Form::select('is_b_ed_completed',[''=>'-- Select --','Yes'=>'Yes','No'=>'No'],null, [ "class" => "form-control border-form upper"]) !!}
				        @include('includes.form_fields_validation_message', ['name' => 'is_b_ed_completed'])
				    </div>
		       </div>
			</div>
			<div class="row">
				<div class="form-group">
				    <label class=' col-md-2 col-xs-3 control-label'>M.Ed. Pursuing Year</label>
				    <div class="col-md-4 col-xs-9">
				        {!! Form::select('m_ed_pursuing_year',$data['year_list'],null, ["class" => "form-control border-form upper"]) !!}
				        @include('includes.form_fields_validation_message', ['name' => 'm_ed_pursuing_year'])
				    </div>
				     <label class=' col-md-2 col-xs-3 control-label'>Is Completed?</label>
				    <div class="col-md-4 col-xs-9">
				        {!! Form::select('is_m_ed_completed',[''=>'-- Select --','Yes'=>'Yes','No'=>'No'],null, [ "class" => "form-control border-form upper"]) !!}
				        @include('includes.form_fields_validation_message', ['name' => 'is_m_ed_completed'])
				    </div>
		       </div>
			</div>


			<div class="row">
				<div class="form-group">
				    <label class=' col-md-2 col-xs-3 control-label'>Class 12th Stream<b class="req"> *</b></label>
				    <div class="col-md-4 col-xs-9">
				        {!! Form::select('12_stream',[''=>'-- Select Stream--','Science'=>'Science','Commerce'=>'Commerce','Arts'=>'Arts'],null, ["class" => "form-control border-form upper",'required'=>'required']) !!}
				        @include('includes.form_fields_validation_message', ['name' => '12_stream'])
				    </div>
				    <label class=' col-md-2 col-xs-3 control-label'>Class 12th Percentage (Rounded)<b class="req"> *</b></label>
				    <div class="col-md-4 col-xs-9">
				        {!! Form::number('12_percentage',null, [ "class" => "form-control onlyNumber border-form upper",'min'=>'0','required'=>'required']) !!}
				        @include('includes.form_fields_validation_message', ['name' => '12_percentage'])
				    </div>
		       </div>
			</div>
			<div class="row">
				<div class="form-group">
				    
				    <label class=' col-md-2 col-xs-3 control-label'>Class 10th Percentage (Rounded)<b class="req"> *</b></label>
				    <div class="col-md-4 col-xs-9">
				        {!! Form::number('10_percentage',null, [ "class" => "form-control onlyNumber border-form upper",'min'=>'0','required'=>'required']) !!}
				        @include('includes.form_fields_validation_message', ['name' => '10_percentage'])
				    </div>
				    <label class=' col-md-2 col-xs-3 control-label'>Board<b class="req"> *</b></label>
				    <div class="col-md-4 col-xs-9">
				        {!! Form::select('board',[''=>'-- Select Board--','U.P. Board'=>'U.P. Board','CBSE'=>'CBSE','ICSE'=>'ICSE','NIOS'=>'NIOS','Other State Board'=>'Other State Board'],null, ["class" => "form-control border-form upper",'required'=>'required']) !!}
				        @include('includes.form_fields_validation_message', ['name' => 'board'])
				    </div>
		       </div>
			</div>
		<div class="label label-warning arrowed-in arrowed-right arrowed">Other Details</div>
			<div class="row">
				<div class="form-group">
				    <label class=' col-md-2 col-xs-3 control-label'>Years of Teaching Experience<b class="req"> *</b></label>
				    <div class="col-md-4 col-xs-9">
				        {!! Form::text('year_of_experience',null, ["class" => "form-control border-form upper" ,"required"]) !!}
				        @include('includes.form_fields_validation_message', ['name' => 'year_of_experience'])
				    </div>
				     <label class=' col-md-2 col-xs-3 control-label'>Name of Present Organization and Place<b class="req"> *</b></label>
				    <div class="col-md-4 col-xs-9">
				        {!! Form::text('pesent_organization',null, [ "class" => "form-control border-form upper",'required'=>'required']) !!}
				        @include('includes.form_fields_validation_message', ['name' => 'pesent_organization'])
				    </div>
		       </div>
			</div>
			<div class="row">
				<div class="form-group">
				    <label class=' col-md-2 col-xs-3 control-label'>Which Subjects and Classes Presently Teaching<b class="req"> *</b></label>
				    <div class="col-md-4 col-xs-9">
				        {!! Form::text('classes_presently_teaching',null, ["class" => "form-control border-form upper" ,"required"]) !!}
				        @include('includes.form_fields_validation_message', ['name' => 'classes_presently_teaching'])
				    </div>
				     <label class=' col-md-2 col-xs-3 control-label'>Languages Known<b class="req"> *</b></label>
				    <div class="col-md-4 col-xs-9">
				        {!! Form::select('languages_known[]',['English'=>'English','Hindi'=>'Hindi','Other'=>'Other'],null, [ "class" => "form-control border-form upper selectpicker",'required'=>'required','multiple'=>'multiple']) !!}
				        @include('includes.form_fields_validation_message', ['name' => 'languages_known'])
				    </div>
		       </div>
			</div>
			<div class="row">
				<div class="form-group">
				     <label class=' col-md-2 col-xs-3 control-label'>Current Salary<b class="req"> *</b></label>
				    <div class="col-md-4 col-xs-9">
				        {!! Form::number('current_salary',null, ["class" => "form-control border-form upper" ,"required",'min'=>'0']) !!}
				        @include('includes.form_fields_validation_message', ['name' => 'current_salary'])
				    </div>
		        </div>
			</div>
			<div class="row">
				<div class="form-group">
				     
				     <label class=' col-md-2 col-xs-3 control-label'>Reason For Leaving<b class="req"> *</b></label>
				    <div class="col-md-4 col-xs-9">
				        {!! Form::text('leaving_reason',null, ["class" => "form-control border-form upper" ,"required"]) !!}
				        @include('includes.form_fields_validation_message', ['name' => 'leaving_reason'])
				    </div>
				    <label class=' col-md-2 col-xs-3 control-label'>How Soon Can You Join<b class="req"> *</b></label>
				    <div class="col-md-4 col-xs-9">
				        {!! Form::select('join_day',$data['join'],null, [ "class" => "form-control border-form upper"]) !!}
				        @include('includes.form_fields_validation_message', ['name' => 'join_day'])
				    </div>
		        </div>
			</div>
			<div class="row">
				<div class="col-md-2 col-md-offset-10">
				     
				     	<button class="btn btn-success pull-right form-control proceed-btn" style="height: 50px;">Proceed</button>
					
					     <button class="buttonload btn-danger form-control proceed-btn-2" style="display:none;height: 50px;">
                            <i class="fa fa-circle-o-notch fa-spin"></i> Saving..
                         </button>
					
			
				 </div>
			</div>
			
	    </div>		
			
</div>
	<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
	<script src="{{ asset('assets/js/jquery-2.1.4.min.js') }}"></script>
   <script type="text/javascript">
    if('ontouchstart' in document.documentElement) document.write("<script src='{{ asset('assets/js/jquery.mobile.custom.min.js') }}'>"+"<"+"/script>");
    </script>

<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
	
<script src="{{ asset('assets/js/toastr.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap-select.min.js') }}"></script>

<script type="text/javascript">
	     $('.selectpicker').selectpicker('refresh');
	  $("#std_type").prop("selectedIndex",'');
	
	    $("#validation-form").on("submit",function(){
	    $(".proceed-btn").hide();
	    $(".proceed-btn-2").show();
	    
	})
	    $(document).on('keypress','.max10Legth',function(e){
       		var l = $(this).val().length;
       		if(parseInt(l)>=10){
       			
       			return false;
       		}
       });
       $(document).on('keypress','.max12Legth',function(e){
       		var l = $(this).val().length;
       		if(parseInt(l)>=12){
       			
       			return false;
       		}
       });

$(document).on('keypress','.onlyNumber',function (e) {
         //if the letter is not digit then display error and don't type anything
         if (e.which != 8 && e.which != 0 && e.which < 48 || e.which > 57) {
            
            alert("Only Numeric Value Allowed.");
                   return false;
        }
        });
</script>
</body>

</html>