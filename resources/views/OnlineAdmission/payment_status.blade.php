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
			background: white;
			
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
		.tbl_heading{
			background: #f4f5f6;
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
		@media print {
		  .print_hidden{
		    display: none;
		  }
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
					<h1 class="heading">ASHA EDUCATIONAL GROUP<h1>
					<h3 class="heading">ONLINE ADMISSION</h3>
				</div>
			</div>	
		</div>
		<div class="row main_div">
			@include('includes.validation_error_messages')
			@include('includes.flash_messages')
			@if($payment->status=='Failed')
					<div class="col-sm-12" style="padding: 5px;background: #d52929bf;color: #fff;font-weight: 600;font-size: 18px;text-align: center;">
				        Registration Failed, Please try again
				    </div>
			@elseif($payment->status=='Completed')
					<div class="col-sm-12" style="padding: 5px;background: #4bd529bf;color: #fff;font-weight: 600;font-size: 18px;text-align: center;">
				        Registration Successfull, Please note below details.
				    </div>
			@endif
			<br>
			<br>
			<table class="table table-hover  table-bordered">
				<thead>
					<th colspan="4" class="tbl_heading">Student Details <button class="btn btn-primary pull-right print_hidden" onclick="window.print()">Print</button></th>
				</thead>
				<tr>
					<th class="col-sm-2">Student's Name:</th>
					<td class="col-sm-4">{{$payment->firstname}}</td>
					<th class="col-sm-2">E-mail:</th>
					<td class="col-sm-4">{{$payment->email}}</td>
				</tr>
				<tr>
					<th class="col-sm-2 ">Contact:</th>
					<td class="col-sm-4">{{$payment->phone}}</td>
					<th class="col-sm-2">Fee Amount Paid:</th>
					<td class="col-sm-4">{{$payment->net_amount_debit}}</td>
				</tr>
				<tr>
					<th class="col-sm-2">Status:</th>
					@if($payment->status=='Failed')
					<td class="col-sm-4" style="color: red">FAILED</td>
					@elseif($payment->status=='Completed')
					<td class="col-sm-4" style="color: green">SUCCESS</td>
					@endif
					<th class="col-sm-2">Transaction Id:</th>
					<td class="col-sm-4">{{$payment->txnid}}</td>
				</tr>
				@if(isset($login))
					@if(count($login)>0)
						<tr>
							<th class="col-sm-2 tbl_heading" colspan="4">Login Details:</th>
						</tr>
						@foreach($login as $key=>$val)
							<tr>
								<th class="col-sm-2">Course:</th>
								<td class="col-sm-4">{{$val['course']}}</td>
								<th class="col-sm-2">Batch:</th>
								<td class="col-sm-4">{{$val['batch']}}</td>
							</tr>
							<tr>
								<th class="col-sm-2 ">Login E-mail:</th>
								<td class="col-sm-4">{{$val['email']}}</td>
								<th class="col-sm-2">Password:</th>
								<td class="col-sm-4">{{$val['password']}}</td>
							</tr>
						@endforeach
						<tr>
							<td colspan="4" class="print_hidden">Please note the Login E-mail and Password to login to your <a href="/" class="print_hidden">account</a>.</td>
						</tr>
					@endif	
				@endif
					<tr>
						<td colspan="4" style="text-align: center;"><a href="{{route('OnlineAdmission')}}">Go Back To Online Admission Form</a></td>
					</tr>
			</table>
		</div>	
	</div>
	<?php Session::put('refreshStatus','1'); ?>
</body>
	<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
	<script src="{{ asset('assets/js/jquery-2.1.4.min.js') }}"></script>
	<script src="{{ asset('assets/js/toastr.min.js') }}"></script>


</html>