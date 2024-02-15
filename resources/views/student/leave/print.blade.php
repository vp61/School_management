<!DOCTYPE html>
<html>
<head>

	
	<title>{{env('APPLICATION_TITLE')}}</title>
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
	.dashed_border_bottom{
		border-bottom: 1px dashed;
	}
	#datafield{

		font-size: 90%;
	}
	

  </style>
</head>
<body>

	<div class="container">
		<div class="row no-gutters">
			<div class="col-sm-12">
				<a href='' class="hide" onclick="print()"><i class="fa fa-print" aria-hidden="true"> Print</i></a>
			</div>
		</div>
		<div class="col-sm-12" id="" >
			<div class="row no-gutters">
				<div class="col-sm-4" style="text-align: center;">
					<img id="ava"  src="{{asset('images/logo/main.png')}}" class="img-responsive" style="width: 150px;margin-top: -10px;">
				</div>
				<?php
				    $general_setting = DB::table('general_settings')->select('salogan')->first();
				    $branch = DB::table('branches')->select('branch_title','affiliation_no','school_code')->where('id',Session::get('activeBranch'))->first();
				
				?>
				
				
				<div class="col-sm-4" style="text-align: center;">
					<h2  style="text-align: center;">{{strtoupper($branch->branch_title)}}</h2>
					<h5 style="text-align: center;">{{strtoupper($general_setting->salogan)}}</h5>
				</div>
				<div class="col-sm-4" style="text-align: center;">
					
				</div>
			</div>
		</div>
	
		<br>
		<div class="row no-gutters">
			<div class="col-sm-6" style="text-align: center;">
				<b>Affiliation No: {{$branch->affiliation_no}}</b>
			</div>
			<div class="col-sm-6" style="text-align: center;">
				<b>School Code: {{$branch->school_code}}</b>
			</div>
		</div>
		<br>
		
		<div class="col-sm-12">
			<div class="row no-gutters">
				
					<div class="col-sm-3" style="text-align: center;">
						<div style="float: left;"><strong>Book No:</strong> </div><div class="dashed_border_bottom" style="overflow: auto;">{{($data['school_leaving_certificate']->book_no)}} </div>
					</div>

					<div class="col-sm-1" style="text-align: center;">
						
					</div>
					<div class="col-sm-3" style="text-align: center;">
						<div style="float: left;"> <strong>Sl. No:</strong></div><div class="dashed_border_bottom" style="overflow: auto;">{{($data['school_leaving_certificate']->si_no)}} </div>
					</div>
					<div class="col-sm-1" style="text-align: center;">
						
					</div>
					<div class="col-sm-4" style="text-align: center;">
						<div style="float: left;"> <strong>Admission No:</strong></div><div class="dashed_border_bottom" style="overflow: auto;">{{$data['school_leaving_certificate']->admi_no}} </div>
					</div>
				
			</div>
		</div>
		<br>
		<div class="col-sm-12">
			<div class="row no-gutters">
				<div class="col-sm-12" id="" >
					<div style="float: left;"><strong>1.</strong> Name of the Student : &nbsp; </div><div class="dashed_border_bottom" style="overflow: auto;">{{($data['school_leaving_certificate']->student_name)}} </div>
				</div>
				
			</div>
		</div>
		<br>
		<div class="col-sm-12">
			<div class="row no-gutters">
				<div class="col-sm-12" id="" >
					<div style="float: left;"><strong>2.</strong> (a) Father’s/Guardian’s Name : &nbsp;</div><div class="dashed_border_bottom" style="overflow: auto;">{{($data['school_leaving_certificate']->father_name)}} </div>
				</div>
				
			</div>
		</div>
		<br>
		<div class="col-sm-12">
			<div class="row no-gutters">
				<div class="col-sm-12" id="" >
					<div style="float: left;"> &nbsp;&nbsp;&nbsp;  (b) Mother’s Name : &nbsp;</div><div class="dashed_border_bottom" style="overflow: auto;">{{($data['school_leaving_certificate']->mother_name)}} </div>
				</div>
				
			</div>
		</div>
		<br>
		<div class="col-sm-12">
			<div class="row no-gutters">
				<div class="col-sm-12" id="" >
					<div style="float: left;"><strong>3.</strong> Nationality : &nbsp;</div><div class="dashed_border_bottom" style="overflow: auto;">{{($data['school_leaving_certificate']->nationality)}} </div>
				</div>
			</div>
		</div>
		<br>
		<div class="col-sm-12">
			<div class="row no-gutters">
				<div class="col-sm-12" id="" >
					<div style="float: left;"><strong>4.</strong> Whether the candidate belongs to Schedule Caste or Schedule Tribe : &nbsp;</div><div class="dashed_border_bottom" style="overflow: auto;">{{($data['school_leaving_certificate']->schedule_caste)}} </div>
				</div>
			</div>
		</div>
		<br>
		<div class="col-sm-12">
			<div class="row no-gutters">
				<div class="col-sm-12" id="" >
					<div style="float: left;"><strong>5.</strong> Date of first admission in the School with Class & Year : &nbsp;</div><div class="dashed_border_bottom" style="overflow: auto;">{{($data['school_leaving_certificate']->first_admission_date)}} </div>
				</div>
			</div>
		</div>
		<br>
		<div class="col-sm-12">
			<div class="row no-gutters">
				<div class="col-sm-12" id="" >
					<div style="float: left;"><strong>6.</strong> Date of Birth (in Christian era) according to Admission Register : &nbsp;</div><div class="dashed_border_bottom" style="overflow: auto;">{{$data['school_leaving_certificate']->date_of_birth?\Carbon\Carbon::parse($data['school_leaving_certificate']->date_of_birth)->format('d-M-Y'):''}} </div>
				</div>
			</div>
		</div>
		<br>
		<div class="col-sm-12">
			<div class="row no-gutters">
				<div class="col-sm-12" id="" >
					<div style="float: left;"><strong>7.</strong> Class in which the pupil last studied and percentage scored (in figures) : &nbsp;</div><div class="dashed_border_bottom" style="overflow: auto;">{{($data['school_leaving_certificate']->pupil_last_studied)}} </div>
				</div>
			</div>
		</div>
		<br>
		<div class="col-sm-12">
			<div class="row no-gutters">
				<div class="col-sm-12" id="" >
					<div style="float: left;"><strong>8.</strong> School/Boards Annual examination last taken with result : &nbsp;</div><div class="dashed_border_bottom" style="overflow: auto;">{{($data['school_leaving_certificate']->annual_examination_last)}}  {{($data['school_leaving_certificate']->last_percentage_scored)}} </div>
				</div>
			</div>
		</div>
		<br>
		<div class="col-sm-12">
			<div class="row no-gutters">
				<div class="col-sm-12" id="" >
					<div style="float: left;"><strong>9.</strong> Whether failed, if so once/twice in the same class : &nbsp;</div><div class="dashed_border_bottom" style="overflow: auto;">{{($data['school_leaving_certificate']->whether_failed)}} </div>
				</div>
			</div>
		</div>
		<br>
		<div class="col-sm-12">
			<div class="row no-gutters">
				<div class="col-sm-12" id="" >
					<div style="float: left;"><strong>10.</strong> Subjects Studied : &nbsp;</div><div class="dashed_border_bottom" style="overflow: auto;">{{($data['school_leaving_certificate']->subjects_name)}} </div>
				</div>
			</div>
		</div>
		<br>
		<div class="col-sm-12">
			<div class="row no-gutters">
				<div class="col-sm-12" id="" >
					<div style="float: left;"><strong>11.</strong> Whether qualified for promotion to the higher class : &nbsp;</div><div class="dashed_border_bottom" style="overflow: auto;">{{($data['school_leaving_certificate']->whether_qualified)}} </div>
				</div>
			</div>
		</div>
		<br>
		<div class="col-sm-12">
			<div class="row no-gutters">
				<div class="col-sm-12" id="" >
					<div style="float: left;"><strong>12.</strong> Month up to which the (pupil has paid) school dues paid : &nbsp;</div><div class="dashed_border_bottom" style="overflow: auto;">{{($data['school_leaving_certificate']->school_dues_paid)}} </div>
				</div>
			</div>
		</div>
		<div class="col-sm-12">
			<div class="row no-gutters">
				<div class="col-sm-12" id="" >
					<div style="float: left;"><strong>13.</strong> Any fee concession availed of: If so, the nature of such concession : &nbsp;</div><div class="dashed_border_bottom" style="overflow: auto;">{{($data['school_leaving_certificate']->fee_concession)}} </div>
				</div>
			</div>
		</div>
		<br>
		<div class="col-sm-12">
			<div class="row no-gutters">
				<div class="col-sm-12" id="" >
					<div style="float: left;"><strong>14.</strong> Total No. of working days in the academic session : &nbsp;</div><div class="dashed_border_bottom" style="overflow: auto;">{{($data['school_leaving_certificate']->academic_session)}} </div>
				</div>
			</div>
		</div>
		<br>
		<div class="col-sm-12">
			<div class="row no-gutters">
				<div class="col-sm-12" id="" >
					<div style="float: left;"><strong>15.</strong> Total No. of presence in the academic session : &nbsp;</div><div class="dashed_border_bottom" style="overflow: auto;">{{($data['school_leaving_certificate']->total_no_presence)}} </div>
				</div>
			</div>
		</div>
		<br>
		<div class="col-sm-12">
			<div class="row no-gutters">
				<div class="col-sm-12" id="" >
					<div style="float: left;"><strong>16.</strong> 	Whether NCC Cadet/Boy Scout/Girl Guide (details may be given) : &nbsp;</div><div class="dashed_border_bottom" style="overflow: auto;">{{($data['school_leaving_certificate']->whether_ncc_cadet)}} </div>
				</div>
			</div>
		</div>
		<br>
		<div class="col-sm-12">
			<div class="row no-gutters">
				<div class="col-sm-12" id="" >
					<div style="float: left;"><strong>17.</strong> Games played or extracurricular activities in which the pupil usually took part
						(Mention achievement level therein) 
					: &nbsp; </div><div class="dashed_border_bottom" style="overflow: auto;">{{($data['school_leaving_certificate']->games_played)}} </div>
				</div>
			</div>
		</div>
		<br>
		<div class="col-sm-12">
			<div class="row no-gutters">
				<div class="col-sm-12" id="" >
					<div style="float: left;"><strong>18.</strong> General conducts : &nbsp;</div><div class="dashed_border_bottom" style="overflow: auto;">{{($data['school_leaving_certificate']->general_conducts)}} </div>
				</div>
			</div>
		</div>
		<br>
		<div class="col-sm-12">
			<div class="row no-gutters">
				<div class="col-sm-12" id="" >
					<div style="float: left;"><strong>19.</strong> Date of application for certificate : &nbsp;</div><div class="dashed_border_bottom" style="overflow: auto;">{{$data['school_leaving_certificate']->date_of_application?\Carbon\Carbon::parse($data['school_leaving_certificate']->date_of_application)->format('d-M-Y'):''}} </div>
				</div>
			</div>
		</div>
		<br>
		<div class="col-sm-12">
			<div class="row no-gutters">
				<div class="col-sm-12" id="" >
					<div style="float: left;"><strong>20.</strong> Date on which pupils name was struck off the rolls of the school : &nbsp;</div><div class="dashed_border_bottom" style="overflow: auto;">{{$data['school_leaving_certificate']->struck_off?\Carbon\Carbon::parse($data['school_leaving_certificate']->struck_off)->format('d-M-Y'):''}} </div>
				</div>
			</div>
		</div>
		<br>
		<div class="col-sm-12">
			<div class="row no-gutters">
				<div class="col-sm-12" id="" >
					<div style="float: left;"><strong>21.</strong> Date of issue of certificate : &nbsp;</div><div class="dashed_border_bottom" style="overflow: auto;">{{$data['school_leaving_certificate']->date_issue_certificate?\Carbon\Carbon::parse($data['school_leaving_certificate']->date_issue_certificate)->format('d-M-Y'):''}} </div>
				</div>
			</div>
		</div>
		<br>
		<div class="col-sm-12">
			<div class="row no-gutters">
				<div class="col-sm-12" id="" >
					<div style="float: left;"><strong>22.</strong> Reasons for leaving the school: &nbsp;</div><div class="dashed_border_bottom" style="overflow: auto;">{{($data['school_leaving_certificate']->other_remark)}} </div>
				</div>
			</div>
		</div>
		<br>
		<div class="col-sm-12">
			<div class="row no-gutters">
				<div class="col-sm-12" id="" >
					<div style="float: left;"><strong>23.</strong> Any other remarks : &nbsp;</div><div class="dashed_border_bottom" style="overflow: auto;">{{($data['school_leaving_certificate']->detail)}} </div>
				</div>
			</di>
		</div>
		<br>
		<div class="col-sm-12">
			<div class="row no-gutters">
				<div class="col-sm-4" id="" >
					<div style="float: left;"> Date: &nbsp;</div><div class="dashed_border_bottom" style="overflow: auto;">&nbsp; </div>
				</div>
				<div class="col-sm-4" id="" >
					
				</div>
				<div class="col-sm-4" id="" >
					<div style="float: left;">Signature of the Principal : &nbsp;</div><div class="dashed_border_bottom" style="overflow: auto;">&nbsp; </div>
				</div>
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