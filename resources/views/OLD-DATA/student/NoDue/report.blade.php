<!DOCTYPE html>
<html>
<head>

	<?php  $x=count($sessiondata)-1;?>
	<title>{{$sessiondata[$x]->first_name}}:{{$sessiondata[$x]->session_name}}</title>
	<meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="{{asset('css\bootstrap.min.css')}}">
  <script src="{{asset('js/jquery.min.js')}}"></script>
  <script src="{{asset('js/popper.min.js')}}"></script>
  <script src="{{asset('js/bootstrap.min.js')}}"></script>

  <link rel="stylesheet" href="{{asset('assets/font-awesome/4.5.0/css/font-awesome.min.css')}}">
  <style type="text/css">
  	@media print {
    .hide {
      display: none;
    }
}
	#data{
		border-bottom: 1px dashed;
	}
	#datafield{

		font-size: 90%;
	}
	


  </style>
</head>
<body>

	<div class="container-fluid">
		<div class="row no-gutters">
			<div class="col-sm-12">
				<div class="row no-gutters"> 
					<div class="col-sm-2"  >
						<a href='' class="hide" onclick="print()"><i class="fa fa-print" aria-hidden="true"> Print</i></a>

					</div>
					<div class="col-sm-8" >
						
						<h2  style="text-align: center;">{{$sessiondata[$x]->branch_name}}</h2>
					@if(count($faculty)>0)	
						<h6 style="text-align: center;">
						@foreach($faculty as $key=>$val)
							
							<i class="fa fa-square-o" aria-hidden="true"> {{$val->faculty_name}} </i>
						@endforeach	

						</h6>
					@endif	
	
					</div>


				</div>
				<div class="row no-gutters">
					<div class="col-sm-4" style="text-align: center;">
						<b>Exam Fees/...</b>
					</div>
					<div class="col-sm-4" style="text-align: center;">
						<b ><u>No Dues Certificate - (Provisional)</u></b>
					</div>
				<!--	<div class="col-sm-2" style="text-align: center;">
						<b>Provisional</b>
					</div> -->
					<div class="col-sm-4" style="text-align: center;">
						<b>Date:{{date("d-m-y")}}</b>
					</div>
				</div>
				<div class="row no-gutters">
					<div class="col-sm-12">
						
							<div class="row no-gutters">
								<div class="col-sm-12">
							 <!-- Name -->		
							<b>1.0 <u>Basic Information:</u></b> <br>
						
									<div class="row no-gutters">
										<div class="col-sm-6">
											<div class="row no-gutters">
													<div class="col-sm-3" id="" >
														Student's Name:
													</div>
													<div class="col-sm-9" id="data" style="">
														{{$sessiondata[$x]->first_name}}
													</div>
											</div>
										</div>		
										<div class="col-sm-6">
											<div class="row no-gutters">			
													<div class="col-sm-3" id="">
														Father's Name:
													</div>
													<div class="col-sm-9" id="data">
														@if(!empty($sessiondata[$x]->father_first_name))
														{{$sessiondata[$x]->father_first_name}}
														@endif
													</div>
											</div>
										</div>
		
									</div>
									<div class="row no-gutters">
										<div class="col-sm-5">
											<div class="row no-gutters">
												<div class="col-sm-3" id="">
													Branch(Year):
												</div>
												<div class="col-sm-9" id="data">
														{{$sessiondata[$x]->faculty}}({{$sessiondata[$x]->session_name}})
												</div>
											</div>	
										</div>		
										<div class="col-sm-1" id="">
											Roll No:
										</div>
										<div class="col-sm-2" id="data">
											{{$sessiondata[$x]->reg_no}}
										</div>
										<div class="col-sm-1" id="">
											Mobile:
										</div>
										<div class="col-sm-3" id="data">
											@if(!empty($sessiondata[$x]->mobile_1))
											{{$sessiondata[$x]->mobile_1}}
											@endif
										</div>
									</div>
									<div class="row no-gutters">
										<div class="col-sm-1">
											Address:
										</div>
										<div class="col-sm-11" id="data">
											@if(!empty($sessiondata[$x]->address))
											{{$sessiondata[$x]->address}}
											@endif
										</div>
									</div>
											
							<div class="row no-gutters">
								<div class="col-sm-12">
									<b>2.0 <u>A/c Section</u></b>
								</div>
							</div>
							<div class="row no-gutters">

									<div class="col-sm-3" id="">
										<b> 2.1 Terms of Admission:</b>
									</div>
									<div class="col-sm-1">
										(i)Terms : 
									</div>
								<div class="col-sm-8" id="data">
									
								</div>
							</div>
							<div class="row no-gutters">
								<div class="col-sm-3">
									
								</div>
								<div class="col-sm-2">
									(ii)Total Fee Per Year :
								</div>
								<div class="col-sm-7" id="data">
									
								</div>
							</div>
									
								</div>
							</div>
						<b>2.2 Payment Status :</b>	

							<table border="1" class="table table-striped">
								
							
								<tr style="border-bottom: 2px solid;">
											<th colspan="2">Nature of Fee</th>
									<!-- Session data-->

										@foreach($sessiondata as $key=>$value)
											
											<th>{{$value->faculty}}({{$value->session_name}})</th>
										@endforeach

											<th>Total</th>
								</tr>
								

								<!-- NUMBER OF FEE HEAD ROWS -->
								
			@foreach($fee1 as $fee=> $value)
				
					@foreach($value as $key=>$times)		
          				<?php ksort($times,1); ?>

											
								<tr>

										
											<td rowspan="3" style="border-bottom: 2px solid black;font-size: 90%;">
											<!--  FEE HEADS -->	
												{{$fee}}<br>({{$key}})
													
											</td>
												<td style="color: green;padding-top:0%;padding-bottom:0%;">Paid</td>
												
											
						<?php $sum=0; $x=0;?>	
						@foreach($times as $k=>$paid)
							<?php $sum=$sum+$paid['paid'];?>
							<?php 
								if(isset($total[$k]['due'])){
									$total[$k]['due']=$total[$k]['due']+$paid['due'];
								}
								else{
									$total[$k]['due']=$paid['due'];
								}	
								if(isset($total[$k]['paid'])){
									$total[$k]['paid']=$total[$k]['paid']+$paid['paid'];
								}
								else{
									$total[$k]['paid']=$paid['paid'];
							}	
								if(isset($total[$k]['discount'])){
									$total[$k]['discount']=$total[$k]['discount']+$paid['discount'];
								}
								else{
									$total[$k]['discount']=$paid['discount'];
								}	
								
							?>




							<td style="padding-top:0%;padding-bottom:0%;color: green;">{{$paid['paid']}}</td>	
							

						@endforeach
											<!-- SUM OF PAID -->
									

							<td style="padding-top:0%;padding-bottom:0%;color: green;">{{$sum}}</td>	
											
										
											
										
								</tr>	
								<!-- SESSIONWISE DISCOUNT -->
								<tr >		
										<td style="padding-top:0%;padding-bottom:0%;">Concession</td>
										
									<?php $discount=0; ?>	
									@foreach($times as $k=>$paid)
										<?php $discount=$discount+$paid['discount']; ?>
										<td style="padding-top:0%;padding-bottom:0%;">{{$paid['discount']}}</td>	

									
									@endforeach
									

									<!-- SUM OF DISCOUNT	-->
									<td style="padding-top:0%;padding-bottom:0%;">{{$discount}}</td>	
								</tr>

								<!-- SESSIONWISE DUE-->
								<tr style="color: red;border-bottom: 2px solid black;">	
										<td style="padding-top:0%;padding-bottom:0%;">Balance</td>
										<?php $due=0; ?>	
									@foreach($times as $k=>$paid)
										<?php $due=$due+$paid['due']; ?>
										<td style="padding-top:0%;padding-bottom:0%;">{{$paid['due']}}</td>	
									@endforeach
									<!-- SUM OF DISCOUNT	-->
									<td style="padding-top:0%;padding-bottom:0%;">{{$due}}</td>

								</tr>
								
					@endforeach		
				@endforeach
			
					<tr style="font-weight: 600;" >
						<td rowspan="3" style="border-bottom: 2px solid black;">Grand Total</td>
						<td style="padding-top:0%;padding-bottom:0%;color: green">Paid</td>
						<?php $tpaid=0;$tdue=0;$tdiscount=0; ?>
						@foreach($total as $key=>$val)
							<?php $tpaid=$tpaid+$val['paid']; ?>
							<td style="color: green;padding-top:0%;padding-bottom:0%;">{{$val['paid']}}</td>
						@endforeach
							<td style="padding-top:0%;padding-bottom:0%;color: green;">{{$tpaid}}</td>
					</tr>
					<tr style="font-weight: 600;" >
						<td style="padding-top:0%;padding-bottom:0%;">Concession</td>
						@foreach($total as $key=>$val)
						<?php $tdis=$tdiscount+$val['discount']; ?>
							<td style="padding-top:0%;padding-bottom:0%;">{{$val['discount']}}</td>
						@endforeach
						<td style="padding-top:0%;padding-bottom:0%;">{{$tdis}}</td>
					</tr>
					<tr style=" color: red;border-bottom: 2px solid black;font-weight: 600;">
						<td style="padding-top:0%;padding-bottom:0%;">Balance</td>
						@foreach($total as $key=>$val)
						<?php $tdue=$tdue+$val['due']; ?>
							<td style="padding-top:0%;padding-bottom:0%;">{{$val['due']}}</td>
						@endforeach
						<td style="padding-top:0%;padding-bottom:0%;">{{$tdue}}</td>
					</tr>
				</table>		

					</div>
				</div>
						<div class="row no-gutters">
							<div class="col-sm-12">
								<b>2.3 Scholarship Status:</b>
								<table class=" table-striped" border="1px" style="width: 100%;">
									
									<tr>
										<th class="padding"><b>Status</b></th>
									@foreach($sessiondata as $key=>$val)	
										<th>{{$val->faculty}}({{$val->session_name}})</th>
									@endforeach
										<th>
											Remarks
										</th>
										
									</tr>
									<tr>
										<td><b>Applied</b></td>
									@foreach($sessiondata as $key=>$val)	
										<td></td>
										
									@endforeach
									<td></td>	
									</tr>
									<tr>
										<td><b>Sanction Received</b></td>
									@foreach($sessiondata as $key=>$val)	
										<td></td>
										
									@endforeach	
									<td></td>
									</tr>
									<tr>
										<td><b>Cheque Received</b></td>
									@foreach($sessiondata as $key=>$val)	
										<td></td>
										
									@endforeach	
									<td></td>
									</tr>
									<tr>
										<td><b>Money Received</b></td>
									@foreach($sessiondata as $key=>$val)	
										<td></td>
										
									@endforeach	
									<td></td>
									</tr>
									
								</table>
							</div>
						</div>
						<div class="row no-gutters">
							<div class="col-sm-12">
								<b>2.4  Special Remarks if any by A/c Dept:</b>
							</div>
							

						</div>
						<br>
						<div class="row no-gutters">
							<div class="col-sm-12" id="data">
								
							</div>
						</div>
						<br>	
						<div class="row no-gutters">
							<div class="col-sm-12" id="data">
								
							</div>
						</div>
						<div class="row no-gutters">
							<div class="col-sm-12" >
									<label style="float: right;margin-top:1%;font-weight: 600;"> Name & Signature Of Accountant</label>
							</div>
						</div>
						<b>3.0 <u>Book Bank:</u></b>
						<div class="row no-gutters">
							<div class="col-sm-6" >
								<div class="row no-gutters">
									<div class="col-sm-3">	
										Library Roll No:
									</div>
									<div class="col-sm-9" id="data">
										
									</div>
								</div>
							</div>		
							<div class="col-sm-1">
								Dues:
							</div>
							<div class="col-sm-5" id="data">
								
							</div>
						</div>
						<div class="row no-gutters">
							<div class="col-sm-12" >
									<label style="float: right;font-weight: 600;margin-top: 1.5%;"> Name & Signature Of Librarian</label>
							</div>
						</div>
						<b>4.0 <u>Lab & HOD:</u></b>
						<div class="row no-gutters">
							<div class="col-sm-1">
								Attendence:
							</div>
							<div class="col-sm-5" id="data">
								
							</div>
							<div class="col-sm-1">
								Other/Lab:
							</div>
							<div class="col-sm-5" id="data">
								
							</div>
						</div>
						<div class="row no-gutters">
							<div class="col-sm-12" >
									<label style="font-weight: 600;float: right;margin-top: 1.5%;"> Name & Signature Of HOD</label>
							</div>
						</div>
						<div class="row no-gutters">
							<div class="col-sm-12">
								<b>5.0  <u>Remarks by No Dues Approving Authority:</u></b>
							</div>
							

						</div>
						<br>
						<div class="row no-gutters">
							<div class="col-sm-12" id="data">
								
							</div>
						</div>
						<br>	
						<div class="row no-gutters">
							<div class="col-sm-12" id="data">
								
							</div>
						</div>
						<div class="row no-gutters">
							<div class="col-sm-12" >
									<label style="float: right;margin-top: 1%;font-weight: 600;"> Signature Of No Dues Approving Authority</label>
							</div>
						</div>

			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				
			</div>
		</div>
	</div>

</body>
<script type="text/javascript">
	public function print(){
		window.print();
	}
	
</script>
</html>