<!DOCTYPE html>
<html>
<head>
	<title>Fee Report</title>
	<meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
	<div class="container">
		<div class="row no-gutters">
			<div class="col-sm-12">
				<div class="row no-gutters"> 
					<div class="col-sm-2"  >
						
					</div>
					<div class="col-sm-8" >
						
						<h2  style="text-align: center;">Asha Educational Institute</h2>
	
					</div>


				</div>
				<div class="row no-gutters">
					<div class="col-sm-12">
						
							<div class="row no-gutters">
								<div class="col-sm-4">
							<!-- Name -->		
							
						
									<h5> Student Name : <b>{{$sessiondata[0]->first_name}}</b> </h5>
									<h5> Registration No. : <b>{{$sessiondata[0]->reg_no}}</b> </h5>

								</div>
							</div>

							<table border="1" class="table table-striped">
								
							
								<tr style="border-bottom: 2px solid;">
											<th colspan="2">Fee head</th>
									<!-- Session data-->

										@foreach($sessiondata as $key=>$value)
											<th>{{$value->faculty}}({{$value->session_name}})</th>
										@endforeach

											<th>Total</th>
								</tr>
								

								<!-- NUMBER OF FEE HEAD ROWS -->
								
			@foreach($fee1 as $fee=> $value)
				
					@foreach($value as $key=>$times)		
          		
				
								<tr>

										
											<td rowspan="3" style="border-bottom: 2px solid black;">
											<!--  FEE HEADS -->	
												{{$fee}}<br>{{$key}}
													
											</td>
												<td style="color: green">Total Paid</td>
												
											
						<?php $sum=0; ?>	
						@foreach($times as $k=>$paid)
							<?php $sum=$sum+$paid['paid']; ?>
							<td>{{$paid['paid']}}</td>	
						@endforeach
											<!-- SUM OF PAID -->
											

							<td>{{$sum}}</td>	
											
										
											
										
								</tr>	
								<!-- SESSIONWISE DISCOUNT -->
								<tr >		
										<td>Discount</td>
										
									<?php $discount=0; ?>	
									@foreach($times as $k=>$paid)
										<?php $discount=$discount+$paid['discount']; ?>
										<td>{{$paid['discount']}}</td>	
									@endforeach
									

									<!-- SUM OF DISCOUNT	-->
									<td>{{$discount}}</td>	
								</tr>
								<!-- SESSIONWISE DUE-->
								<tr style="color: red;border-bottom: 2px solid black;">	
										<td>Due</td>
										<?php $due=0; ?>	
									@foreach($times as $k=>$paid)
										<?php $due=$due+$paid['due']; ?>
										<td>{{$paid['due']}}</td>	
									@endforeach
									<!-- SUM OF DISCOUNT	-->
									<td>{{$due}}</td>

								</tr>
								
					@endforeach		
				@endforeach		
						</table>

			<h5>			
					<table border="2px" class="table">			
						<tr>
							<td colspan="2" style="text-align: center;font-weight: 600;"><b>Grand Total</b></td>
						</tr>
						<tr>	
							<td style="color: green">Total PAID <b style="float:  right;">{{$gt}}</b></td>
							<td style="color: red">Total DUE <b style="float:  right;">{{$tdue}}</b></td>
						</tr>			
						
					</table>
			</h5>		

					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>