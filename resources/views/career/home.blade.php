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
	{!!Form::open(['route'=>'career','method'=>'GET', 'class' => 'form-horizontal',
                                        'id' => 'validation-form', "enctype" => "multipart/form-data"])!!}


		<div class="row main_div">
			@include('includes.validation_error_messages')
			@include('includes.flash_messages')
		
		   <!-- COURSE DETAILS --Branch-->
			<h3 class="heading"><center>SCHOOL RECRUITMENT FORM</center></h3><hr>
			<div class="row">
				<div class="form-group">
				    <label class=' col-md-2 col-xs-3 control-label'>Job-Type<b class="req"> *</b></label>
				    <div class="col-md-9 col-xs-9">
				        {!! Form::select('type',['' => '--Select--','1'=>'Teaching','2'=>'Non-Teaching'],null, [ "class" => "form-control border-form upper",'required'=>'required']) !!}
				        @include('includes.form_fields_validation_message', ['name' => 'type'])
				    </div>
		       </div>
			</div>
			<div class="row">
				<div class="col-md-2 col-md-offset-10">
				     
				     	<button class="btn btn-success pull-right form-control proceed-btn" style="height: 50px;">NEXT >></button>
					
					     <button class="buttonload btn-danger form-control proceed-btn-2" style="display:none;height: 50px;">
                            <i class="fa fa-circle-o-notch fa-spin"></i> Please Wait..
                         </button>
					
			
				 </div>
			</div>
			
	    </div>
	    {!!Form::close()!!}		
			
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