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
			<table class="table table-hover  table-bordered">
				<thead>
					<th colspan="4" class="tbl_heading">Confirm Details</th>
				</thead>
				<tr>
					<th class="col-sm-2">Student's Name:</th>
					<td class="col-sm-4">{{$form_data['first_name']}}</td>
					<th class="col-sm-2">Father's Name:</th>
					<td class="col-sm-4">{{$form_data['father_first_name']}}</td>
				</tr>
				<tr>
					<th class="col-sm-2 ">DOB:</th>
					<td class="col-sm-4">{{$form_data['date_of_birth']}}</td>
					<th class="col-sm-2">Gender:</th>
					<td class="col-sm-4">{{$form_data['gender']}}</td>
				</tr>
				<tr>
					<th class="col-sm-2">Mobile:</th>
					<td class="col-sm-4">{{$form_data['mobile_1']}}</td>
					<th class="col-sm-2">E-mail:</th>
					<td class="col-sm-4">{{$form_data['email']}}</td>
				</tr>
				<tr>
					<th class="col-sm-2 ">Aadhaar No:</th>
					<td class="col-sm-4">{{$form_data['aadhar_no']}}</td>
					<th class="col-sm-2">Indose No:</th>
					<td class="col-sm-4">{{$form_data['indose_number']}}</td>
				</tr>
				<tr>
					<th class="col-sm-2">Address:</th>
					<td class="col-sm-4">{{$form_data['address']}}</td>
					<th class="col-sm-2">State:</th>
					<td class="col-sm-4">{{$form_data['state']}}</td>
				</tr>
				<tr>
					<th class="col-sm-2 ">Country:</th>
					<td class="col-sm-4">{{$form_data['country']}}</td>
					<th class="col-sm-2">ZIP:</th>
					<td class="col-sm-4">{{$form_data['zip']}}</td>
				</tr>
				<tr>
					<th class="col-sm-2 tbl_heading" colspan="4">Selected Courses:</th>
				</tr>
				<?php $total_fee=0; ?>
				@for($i=1;$i<@count($form_data['course']);$i++)
					<?php
					//dd($student_inserted_data,$form_data);
					$values=App\Http\Controllers\OnlineAdmission\OnlineAdmissionController::get_course_batch_details($form_data['course'][$i]);
					foreach($student_inserted_data as $k=>$val){
						if(Session::get('isCourseBatch')){
							if(($val->faculty==$values['name']->faculty_id) && ($val->batch_id==$values['batch']->batch_id)){
									$total_fee=$total_fee+$values['fee'];
									?>
									<tr>
										<th class="col-sm-2">Course Name:</th>
										<td class="col-sm-4">{{$values['name']->faculty}}</td>
										<th class="col-sm-2">Fee:</th>
										<td class="col-sm-4">Rs- <b class="fee_amount">{{$values['fee']}}</b></td>
									</tr>
									<tr>
										<th class="col-sm-2 ">Batch:</th>
										<td class="col-sm-4">{{$values['batch']->course_batch}}</td>
										
									</tr>
								<?php 
							}
						}else{
							if(($val->faculty==$values['name']->faculty_id)){
									$total_fee=$total_fee+$values['fee'];
									?>
									<tr>
										<th class="col-sm-2">Course Name:</th>
										<td class="col-sm-4">{{$values['name']->faculty}}</td>
										<th class="col-sm-2">Fee:</th>
										<td class="col-sm-4">Rs- <b class="fee_amount">{{$values['fee']}}</b></td>
									</tr>
								<?php 
							}
						}	
				}
						?>	
				@endfor
				<?php 
					/*Static Min Pay Amount ,Remove this for dynamicity */
						if($total_fee < 50000){
				            $min_pay_amount = 50;
				        }else{
				            $min_pay_amount = 10;
				        }

					/*  END */

					$min_amount=round(($min_pay_amount/100)*$total_fee); 
					$txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
					?>
				<thead>
					<th colspan="4" class="tbl_heading">Payment Details </th>
				</thead>
				<form action="OnlineAdmission/ConfirmPayment" method="GET" class="form-horizontal">
					<tr>
						<div class="form-group">
                                <input type="hidden" name="key" value="{{$merchant->Merchant_Key}}" />
                                <input type="hidden" name="hash" value="{{$merchant->Merchant_Salt}}"/>
                                <input type="hidden" name="txnid" value="<?php echo $txnid ?>" />
                                <input type="hidden" name="email" value="{{$form_data['email']}}" />
                                <input type="hidden" name="phone" value="{{$form_data['mobile_1']}}" />
                                <input type="hidden" name="firstname" value="{{$form_data['first_name']}}" />
                                <input type="hidden" name="productinfo" value="Tution Fee" />
                                <input type="hidden" name="feehead" value="Tution Fee" />
                                <input type="hidden" name="branch_id" value="{{$form_data['branch']}}" />
                                <input type="hidden" name="surl" value="OnlineAdmission/PaymentStatus" />
                                <input type="hidden" name="unmappedstatus"  value= "Pending" class="input-sm form-control border-form" />
                                <input type="hidden" name="total_fee" value="{{$total_fee}}" />
                               @for($i=1;$i<@count($form_data['course']);$i++)
                               	<input type="hidden" name="course[]" value="{{$form_data['course'][$i]}}" />
                               @endfor
                            </div>
						<th class="col-sm-2">Fee Type:</th>
						<td class="col-sm-4">Tution Fee</td>
						<th class="col-sm-2">Total Amount:</th>
						<td class="col-sm-4"><input type="number" value="{{$total_fee}}" name="amount" id="total_fee" min="{{$min_amount}}" max="{{$total_fee}}" required class="form-control"><i>Pay minimum &#8377; {{$min_amount}} amount.</i></td>
					</tr>
					
			</table>
			<div class="row">
				<div class="col-12 col-sm-12">
					<button type="submit" class="btn btn-success pull-right" name="Confirm">Confirm</button>
				</div>
			</div>
				</form>
		</div>	
	</div>
	
</body>
	<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
	<script src="{{ asset('assets/js/jquery-2.1.4.min.js') }}"></script>
	<script src="{{ asset('assets/js/toastr.min.js') }}"></script>


</html>