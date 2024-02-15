<?php
namespace App\Http\Controllers;
use App\Fee_model; use App\Session_model; use App\Models\FeeHead;
use Session; use App\Models\Faculty; use App\AssignFee; use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;




class AssignFeeController extends Controller{

	public function index(Request $req){
		$assign_list=Fee_model::select('assign_fee.*', 'fee_heads.fee_head_title', 'session.session_name')->leftJoin('fee_heads', function($join){
				$join->on('fee_heads.id', '=', 'assign_fee.fee_head_id');
		})->leftJoin('session', function($join){
			$join->on('session.id', '=', 'assign_fee.session_id');
		
		})->paginate(1);

		$branch_ids = Session::get('activeBranch');

		$head_list=FeeHead::get();
		$session_list=Session_model::select('id', 'session_name')->pluck('session_name', 'id')->toArray();
		$session_list=array_prepend($session_list,'Select Session',"");

		$course_list=Faculty::select('id', 'faculty')->where('branch_id', $branch_ids)->pluck('faculty', 'id')->toArray();
		$course_list=array_prepend($course_list, "Select Course", "");
		//get();
		$panel='Fees Assign';
		$subj_list = DB::table('subjects')->select('id', 'title')->pluck('title', 'id')->toArray();
		$subj_list = array_prepend($subj_list, "Select Subject", "");

		$student_list = DB::table('students')->select('id', 'first_name')->pluck('first_name', 'id')->toArray();
		$student_list = array_prepend($student_list, "select Student", "");
		
		$branch_list = DB::table('branches')->select('id', 'branch_name')->pluck('branch_name', 'id')->toArray();
		$branch_list = array_prepend($branch_list, "Select Branch", "");

		return view('Fee.assign', compact('assign_list', 'panel', 'head_list', 'session_list', 'course_list', 'subj_list', 'student_list', 'branch_list', 'branch_ids'));
	}

	public function assigned(Request $req){
		$rules=['heads'=>'required', 'session'=>'required|numeric', 'course'=>'required'];
		
		$valid=Validator::make($req->all(), ['heads'=>'required', 'session'=>'required|numeric', 'course'=>'required', 'branch_id'=>'required']);
		if($valid->fails()){
		  return redirect()->back()->withErrors($valid)->withInput();
		}else{
			$session = $req->session;	$branch_id=$req->branch_id;
			$course=$req->course; 		$subject=$req->subject;
			$student = $req->student;
			
			$head_arr=$req->heads; //dd($req);
			$head_id_arr=$req->fee_hd; $fee_amount_arr = $req->fee_amnt;
			$times_arr=$req->times;
			$indx=0;
			foreach($head_arr as $key=>$val){
				$cnt=0;
				if($times_arr[$indx] == "Yearly"){ $k=1; }else{ $k=2; }
				for($i=0; $i<$k; $i++){
					$fee_id = $head_id_arr[$indx];  $cnt+=1;
					$fee_amount = $fee_amount_arr[$indx];
					if($fee_id && $fee_amount){
						$times=($times_arr[$indx] == "Yearly") ? $times_arr[$indx] : "Semester ".$cnt;
						$assiGn=new AssignFee;
						$assiGn->branch_id = $branch_id;
						$assiGn->fee_head_id = $fee_id;
						$assiGn->course_id = $course;
						$assiGn->session_id = $session;	
						$assiGn->subject_id = $subject;
						$assiGn->student_id = $student;	
						$assiGn->fee_amount = $fee_amount;
						$assiGn->times = $times;
						$assiGn->save();
					}//else{ echo"outside"; }
				}
				$indx++;
			}
			Session::flash('msG', 'Fees Assigned Successfully.');
			return redirect()->route('assignList'); 
		}
	}

	public function branch_select(Request $req){
		$retval="<option value=\"\">----Select Courses----</option>";
		$result = DB::table('faculties')->where('branch_id', $req->vl)->select('id', 'faculty')->get();
		if($result->count()){
			foreach($result as $val){
$retval.="<option value=\"".$val->id."\">".$val->faculty."</option>";
			}
		}else{ $retval="<option value=\"\">----No Courses----</option>"; }	return response($retval);
	}

	public function student_select(Request $req){
		$retval="<option value=\"\">----Select Student----</option>";
		$result = DB::table('students')->select('id', 'first_name', 'reg_no')->where('branch_id', $req->branch)->where('faculty', $req->course)->get();

		if($result->count()){
			foreach($result as $val){
$retval.="<option value=\"".$val->id."\">".$val->first_name." (".$val->reg_no.")</option>";
			}
		}else{ $retval="<option value=\"\">----No Students----</option>"; }	return response($retval);
	}

	public function student_fee(Request $req){
		$branch=$req->branch; $session=$req->ssn; 
		$course=$req->course;
		$fee_result=AssignFee::Select('assign_fee.*', 'fee_heads.fee_head_title')
		->leftJoin('fee_heads', function($join){
			$join->on('fee_heads.id', '=', 'assign_fee.fee_head_id');
		})->where('assign_fee.branch_id', $branch)
		->where('assign_fee.session_id', $session)
		->where('assign_fee.course_id', $course)
		
		->Where(function($q) use ($req){
			if($req->student){
				$q->orWhere('assign_fee.student_id', $req->student)->orWhere('assign_fee.student_id', '0');
			}
		})->groupBy('assign_fee.id')->get();
		$ret_val=""; $i=0;

if(count($fee_result)){
	$ret_val.="<div class=\"row\" style=\" text-align:left; background:#F89406; padding:10px 15px; color:#ffffff;\">
			<div class=\"col-sm-2\"><b>Fee Head</b></div>
			<div class=\"col-sm-1\"></div>
			<div class=\"col-sm-1\"><b>Fees</b></div>
			<div class=\"col-sm-1\"><b>Paid</b></div>
			<div class=\"col-sm-2\"><b>Amount</b></div>
			
			<div class=\"col-sm-1\"><b>Due</b></div>
			<div class=\"col-sm-1\"><b>Discount</b></div>
			<div class=\"col-sm-3\"><b>Remarks</b></div>
			</div>";
		foreach($fee_result as $fee){
			$paid_result=DB::table('collect_fee')->where('assign_fee_id', $fee->id)->where('student_id', $req->student)->sum('amount_paid');
			$due = $fee->fee_amount - $paid_result;
			$ret_val.="<div class=\"row\" style=\"margin:5px 0px; text-align:left;\">
			<input type=\"hidden\" name=\"assign_id[]\" value=\"".$fee->id."\">
			
			<div class=\"col-sm-2\"><b>".++$i."): ".$fee->fee_head_title."</div><div class=\"col-sm-1\"><b>".$fee->times." </b></div>
			<div class=\"col-sm-1\"><b><i class=\"fa fa-inr\"></i> ".$fee->fee_amount."</b></div>
			<div class=\"col-sm-1\"><b><i class=\"fa fa-inr\"></i> ".$paid_result."</b></div>
			<div class=\"col-sm-2\"><b>";
			if($due){
				$ret_val.="<input type=\"number\" placeholder=\"Enter Amount\" name=\"amount[]\" max=\"".$due."\">";
			}else{
			$ret_val.="<input placeholder=\"Enter Amount\" type=\"number\" name=\"amount[]\" value=\"\">";
			}

			$ret_val.="</b></div>
			<div class=\"col-sm-1\"><b><i class=\"fa fa-inr\"></i> $due
<input type=\"hidden\" name=\"due_amount[]\" value=\"".$due."\">
			</b></div>
			<div class=\"col-sm-1\"><input type=\"text\" name=\"discount[]\" placeholder=\"Enter Discount\" /></div>
			<div class=\"col-sm-3\">
				<input type=\"text\" placeholder=\"Enter Remarks\" name=\"remark[]\">
			</div>

			</div>";
		}
		
}else{ $ret_val="<div class=\"alert alert-danger\"><h3>No Fees Assigned</h3></div>"; }
		return response($ret_val);
	}

	public function collect(Request $req){
		$session_list=Session_model::select('id', 'session_name')->pluck('session_name', 'id')->toArray();
		$session_list=array_prepend($session_list,'Select Session',"");

		$course_list=Faculty::select('id', 'faculty')->pluck('faculty', 'id')->toArray();
		$course_list=array_prepend($course_list, "Select Course", "");
		//get();
		$panel='Fees Collect';
		
		$student_list = DB::table('students')->select('id', 'first_name')->pluck('first_name', 'id')->toArray();
		$student_list = array_prepend($student_list, "select Student", "");
		$branch_ids = Session::get('activeBranch');
		$branch_list = DB::table('branches')->select('id', 'branch_name')->pluck('branch_name', 'id')->toArray();
		$branch_list = array_prepend($branch_list, "Select Branch", "");

		if($req->has('submit')){
			$amount_paid=$req->amount; $l=0; $assign_id=$req->assign_id;
			$branch=$req->branch; $session=$req->ssn; 
			$course=$req->course;
			$disc=$req->discount; $remarks=$req->remark;
			$reciept_no = "ASH".$branch.$course.$session.time();
			
			while(DB::table('collect_fee')->where('reciept-no', $reciept_no)->count()){
				$reciept_no = "ASH".$branch.$course.$session.time();	
			}

			die("inside==>".$reciept_no);
			foreach($amount_paid as $amnt){
				$data=array();	$data['reciept-no']=$reciept_no;
				$data['student_id']=$req->student;
				$data['session']=$req->session;	
				$data['assign_fee_id']=$assign_id[$l];
if($assign_id[$l] && $amnt && $req->student && $req->session){
	$data['amount_paid']=$amnt;	$data['discount']=$disc[$l];
	$data['remarks']=$remarks[$l];
	$data['payment_type']=$req->payment_type;
	$data['created_at']=date('Y-m-d');	$data['reference']=$req->reference;
	DB::table('collect_fee')->insert($data);
}	
				$l++;
			}
			return redirect()->route('collect_fee');
		}else{
			return view('Fee.collect', compact('panel', 'session_list', 'course_list', 'student_list', 'branch_list', 'branch_ids'));
		}
	}
}
