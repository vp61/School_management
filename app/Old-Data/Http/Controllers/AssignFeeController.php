<?php
namespace App\Http\Controllers;
use App\Http\Controllers\CollegeBaseController;
use App\Fee_model; 
use App\Session_Model; 
use App\Models\FeeHead;
use Session; 
use App\Models\Faculty;
use App\AssignFee; use DB;
use Illuminate\Http\Request;  
use App\Collection;
use Illuminate\Support\Facades\Validator;
use Auth; use App\category_model;
use App\Branch; use App\Models\StudentDetailSessionwise;
use App\StudentPromotion;
use Carbon\Carbon;


class AssignFeeController extends CollegeBaseController{
	
	public function index(Request $req){
		
		if(Session::has('activeBranch')){
		    $branch_ids = Session::get('activeBranch');
		}else{ $branch_ids = Auth::user()->branch_id; }
		
		$assign_list=Fee_model::select('assign_fee.*','faculties.faculty', 'fee_heads.fee_head_title', 'session.session_name', 'users.name')->leftJoin('fee_heads', function($join){
				$join->on('fee_heads.id', '=', 'assign_fee.fee_head_id');
		})->leftJoin('session', function($join){
			$join->on('session.id', '=', 'assign_fee.session_id');
		
		})->leftJoin('users', function($join){
				$join->on('users.id', '=', 'assign_fee.created_by');
		})->leftJoin('faculties', function($join){
				$join->on('faculties.id', '=', 'assign_fee.course_id');
		})->where('assign_fee.session_id', Session::get('activeSession'))->where('assign_fee.branch_id', $branch_ids)->orderBy('assign_fee.id','desc')->paginate(10);

		$head_list=FeeHead::get();
		$session_list=Session_Model::select('id', 'session_name')->pluck('session_name', 'id')->toArray();
		$session_list=array_prepend($session_list,'Select Session',"");
		$ssn_tble=DB::table('session')->select('id', 'session_name')->where('active_status', '1')->get();
		$current_session=Session::get('activeSession');	//$ssn_tble[0]->id;
		//die("-->".$ssn_tble[0]->id);
		$course_list=Faculty::select('id', 'faculty')->where('branch_id', $branch_ids)->pluck('faculty', 'id')->toArray();
		$course_list=array_prepend($course_list, "Select Course", "");
		//get();
		$panel='Fees Assign';
		$subj_list = DB::table('subjects')->select('id', 'title')->pluck('title', 'id')->toArray();
		$subj_list = array_prepend($subj_list, "Select Subject", "");

		$student_list = DB::table('students')->select('id', 'first_name')->pluck('first_name', 'id')->toArray();
		$student_list = array_prepend($student_list, "select Student", "");
		
		$branch_list = Branch::select('branch_name','branch_address','id','org_id')->where("id",$branch_ids)->get();
		
	//	$branch_list = array_prepend($branch_list, "Select Branch", "");

		return view('Fee.assign', compact('assign_list', 'panel', 'head_list', 'session_list', 'course_list', 'subj_list', 'student_list', 'branch_list', 'branch_ids', 'current_session'));
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
			//foreach($head_arr as $key=>$val){
			foreach($fee_amount_arr as $fee_amount){
				$cnt=0;
				if($times_arr[$indx] == "Yearly"){ $k=1; }else{ $k=2; }
				for($i=0; $i<$k; $i++){
					$fee_id = $head_id_arr[$indx];  $cnt+=1;
					//$fee_amount = $fee_amount_arr[$indx];
					if($fee_id && $fee_amount){
						$times=($times_arr[$indx] == "Yearly") ? $times_arr[$indx] : "Semester ".$cnt;
						$assiGn=new AssignFee;
						$assiGn->branch_id = $branch_id;
						$assiGn->fee_head_id = $fee_id;
						$assiGn->course_id = $course;
						$assiGn->session_id = $session;	
						$assiGn->subject_id = $subject;
						$user_id = Auth::user()->id;
						$assiGn->created_by=$user_id;
						$assiGn->student_id = ($student!="") ? $student:0;	
						$assiGn->fee_amount = $fee_amount;
						$assiGn->times = $times;
						$assiGn->save();
						
					}//else{ Session::flash('msG', 'Fees Not Assigned. Try Again'); }
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
		$sessn=($req->selected_session) ? $req->selected_session : session('activeSession');
		$branch=($req->branch) ? $req->branch:session('activeBranch');
		$result = DB::table('students')->select('students.id', 'first_name', 'reg_no')
		->leftJoin('student_detail_sessionwise', function($join){
			$join->on('student_detail_sessionwise.student_id', '=', 'students.id');
		})
		->where('student_detail_sessionwise.course_id', $req->course)
		->where('branch_id', $branch)
		->where('student_detail_sessionwise.session_id', $sessn)
		->where(function($query) use ($req){
if($req->semester){ $query->where('student_detail_sessionwise.Semester', $req->semester); }
		})->get();

		if($result->count()){
			foreach($result as $val){
$retval.="<option value=\"".$val->id."\">".$val->first_name." (".$val->reg_no.")</option>";
			}
		}else{ $retval="<option value=\"\">----No Students----</option>"; }	return response($retval);
	}

	public function student_fee(Request $req){
		$branch=$req->branch; 
		$session=$req->ssn;
		$course=$req->course;
		$fee_result=AssignFee::Select('assign_fee.*', 'fee_heads.fee_head_title')
		->leftJoin('fee_heads', function($join){
			$join->on('fee_heads.id', '=', 'assign_fee.fee_head_id');
		})->where('assign_fee.branch_id', $branch)
		->where('assign_fee.session_id', $session)
		->where('assign_fee.course_id', $course)
		->Where('assign_fee.student_id', '0')
		->orWhere(function($q) use ($req){
			if($req->student){
				$q->orWhere('assign_fee.student_id', $req->student)
				->where('assign_fee.branch_id', $req->branch)
				->where('assign_fee.session_id', $req->ssn)
				->where('assign_fee.course_id', $req->course);
			}
		})->groupBy('assign_fee.id')->get();
		$ret_val=""; $i=0;



if(count($fee_result) && $req->student){
	$ret_val.="<div class=\"row\" style=\" text-align:left; background:#F89406; padding:10px 15px; color:#ffffff;\">
			<div class=\"col-sm-2\"><b>Fee Head</b></div>
			<div class=\"col-sm-1\"></div>
			<div class=\"col-sm-1\"><b>Fees</b></div>
			<div class=\"col-sm-1\"><b>Paid</b></div>
			<div class=\"col-sm-2\"><b>Amount</b></div>
			
			<div class=\"col-sm-1\"><b>Due</b></div>
			<div class=\"col-sm-1\"><b>Discount</b></div>
			<div class=\"col-sm-3\" style=\"text-align:center\"><b>Remarks</b></div>
			</div>";
		foreach($fee_result as $fee){
			$paid_result=DB::table('collect_fee')->where('assign_fee_id', $fee->id)->where('student_id', $req->student)
			->Where('status' , 1)->sum('amount_paid');
			$due = $fee->fee_amount - $paid_result;
			$disabled = ($due == 0) ? " style=\"display:none;\"":"";
			$ret_val.="<div class=\"row\" style=\"margin:5px 0px; text-align:left;\">
			<input type=\"hidden\" name=\"assign_id[]\" value=\"".$fee->id."\">
			
			<div class=\"col-sm-2\">
 
<b>".++$i."): ".$fee->fee_head_title."</div><div class=\"col-sm-1\">
<input type=\"hidden\" value=\"".$fee->fee_head_id."\" name=\"fee_head_id[]\">
<b>".$fee->times." </b></div>
			<div class=\"col-sm-1\"><b><i class=\"fa fa-inr\"></i> ".$fee->fee_amount."</b></div>
			<div class=\"col-sm-1\"><b><i class=\"fa fa-inr\"></i> ".$paid_result."</b></div>
			<div class=\"col-sm-2\"><b>";
			if($due){
				$ret_val.="<input type=\"number\" placeholder=\"Enter Amount\" name=\"amount[]\" max=\"".$due."\" required>";
			}else{
			$ret_val.="<input $disabled placeholder=\"Enter Amount\" type=\"number\" name=\"amount[]\" value=\"\">";
			}

			$ret_val.="</b></div>
			<div class=\"col-sm-1\"><b><i class=\"fa fa-inr\"></i> $due
<input type=\"hidden\" name=\"due_amount[]\" value=\"".$due."\">
			</b></div>
			<div class=\"col-sm-1\"><input type=\"text\" name=\"discount[]\" $disabled placeholder=\"Enter Discount\" style=\"display:none;\"/></div>
			<div class=\"col-sm-3\">
				<input type=\"text\" $disabled placeholder=\"Enter Remarks\" name=\"remark[]\" class=\"pull-right\">
			</div>

			</div>";
		}
		
}else{ $ret_val="<div class=\"alert alert-danger\"><h3>No Fees Assigned</h3></div>"; }
		return response($ret_val);
	}

	public function student_fee_history(Request $req)

	{
		$branch=$req->branch; 
		$session=$req->ssn; 
		$course=$req->course;

		$feeHistory_data = DB::table('collect_fee')->Select('collect_fee.id','collect_fee.status','collect_fee.student_id','collect_fee.discount', 'collect_fee.reciept_date','collect_fee.assign_fee_id','collect_fee.amount_paid','collect_fee.reciept_no','collect_fee.payment_type','collect_fee.status','sd.first_name','sd.branch_id','sd.reg_no', 'sd.reg_date', 'sd.university_reg','sd.date_of_birth', 'sd.gender','asf.fee_amount','asf.course_id','asf.fee_head_id','fd.fee_head_title','br.branch_name','br.branch_logo','br.branch_mobile','br.branch_email','br.branch_address', 'fac.faculty')
		        ->where('collect_fee.student_id','=',$req->student)
		        ->where('collect_fee.status','=',1)
		        //->Where('asf.student_id', $req->student)
                ->where('asf.branch_id', $branch)
				->where('asf.session_id', $session)
        ->join('students as sd', 'sd.id', '=', 'collect_fee.student_id')
        ->join('assign_fee as asf', 'asf.id', '=', 'collect_fee.assign_fee_id')
        ->join('fee_heads as fd', 'fd.id', '=', 'asf.fee_head_id')
        ->join('branches as br', 'br.id', '=', 'sd.branch_id')
        ->join('faculties as fac', 'asf.course_id', '=', 'fac.id')
        ->get();
        $data_val="";$i=0;

		$data_val.="<thead><tr>
                    <th class=\"col-sm-1\">S.N.</th>
                    <th class=\"col-sm-1\">Reciep No</th>
                    <th class=\"col-sm-1\">Fee-Head</th>
                    <th class=\"col-sm-1\">Paid</th>
                    <th class=\"col-sm-1\"> Mode</th>
                    <th class=\"col-sm-1\">Date</th>
                    <th class=\"col-sm-1\">status</th>
                    <th class=\"col-sm-1\">Print</th>
                    
                   
                    
                </tr></thead ><tbody>";

        foreach ($feeHistory_data as $feeHistory) {
        	$collDate = date("d-m-Y",strtotime($feeHistory->reciept_date));
        	$data_val.="<tr>
                    <td class=\"col-sm-1\">".++$i."</td>
                    <td class=\"col-sm-1\">".$feeHistory->reciept_no."</td>
                    <td class=\"col-sm-1\">".$feeHistory->fee_head_title."</td>
                    <td class=\"col-sm-1\">".$feeHistory->amount_paid."</td>
                    <td class=\"col-sm-1\"> ".$feeHistory->payment_type."</td>
                    <td class=\"col-sm-1\">".$collDate."</td>";
                    if($feeHistory->status==1){
                    $data_val.= "<td class=\"col-sm-1\">PAID</td>";
                    }
                    else
                    {
                    $data_val.= "<td class=\"col-sm-1\">Failed</td>";
                    }
                    
                    $PRINT_URL =  route('feeReceipt', ['receipt_no' => $feeHistory->reciept_no]); 
                    $data_val.=" <td>";
                   if($feeHistory->status== 1){
                    $data_val.="
                        <a class=\"btn btn-xs btn-primary\" href=\"".$PRINT_URL."\" target=\"_blank\">print
                            <i class=\"fa fa-print\"></i>
                              </a>";
                    
                                
                        }      
                                     
                       $data_val.="</td>";
                    
              $data_val.="</tr>";
        	
        }
        $data_val.="</tbody>";

		return response($data_val);
		
	}

	public function collect(Request $req){
		$session_list=Session_Model::select('id', 'session_name')->pluck('session_name', 'id')->toArray();
		$session_list=array_prepend($session_list,'Select Session',"");
		$panel='Fees Collect';


		$ssn_tble=DB::table('session')->select('id', 'session_name')->where('active_status', '1')->get();
		$current_session=$ssn_tble[0]->id;


		if(Session::has('activeBranch')){
		    $branch_ids = Session::get('activeBranch');
		}else{$branch_ids = Auth::user()->branch_id; }

		$branch_list = DB::table('branches')->select('id', 'branch_name')->pluck('branch_name', 'id')->toArray();
		//$branch_list = array_prepend($branch_list, "Select Branch", "");
        $branch_list = Branch::select('branch_name','branch_address','id','org_id')->where("id",$branch_ids)->get();
		$course_list=Faculty::select('id', 'faculty')->where('branch_id', $branch_ids)->pluck('faculty', 'id')->toArray();
		$course_list=array_prepend($course_list, "Select Course", "");

		$pay_type_list = DB::table('payment_type')->select('type_name')->where('status', '1')->pluck('type_name','type_name')->toArray(); 
		//dd($pay_type_list);//, 'id'
		$pay_type_list = array_prepend($pay_type_list, "----Select Payment Mode----", "");
		$student_list = array(); //DB::table('students')->select('id', 'first_name')->where('branch_id', $branch_ids)->pluck('first_name', 'id')->toArray();
		$student_list = array_prepend($student_list, "select Student", "");
		if($req->has('submit')){

			$amount_paid=$req->amount; $l=0; $assign_id=$req->assign_id; $fee_head_id_arr=$req->fee_head_id;
			$branch=$req->branch; $session=$req->ssn; 
			$course=$req->course;
			$disc=$req->discount; $remarks=$req->remark;
			// $reciept_no=$this->reciept_no();
			
			$checkStatus = 0;
			foreach($amount_paid as $amnt){
				$data=array();
				$data['student_id']=$req->student;
				//$data['session']=$req->session;	
				$data['assign_fee_id']=$assign_id[$l];
                if($assign_id[$l] && $amnt>0 && $amnt !="'" && $req->student && $req->session){
                    
                    $checkStatus = 1;
                	$data['amount_paid']=$amnt;	$data['discount']=$disc[$l];
                	$data['remarks']=$remarks[$l];
                	$data['payment_type']=$req->payment_type;
                	$data['created_at']=date('Y-m-d');
                	// $data['reciept_no']=$reciept_no;
                	$data['created_by']=Auth::user()->id;
                	$data['reference']=($req->reference) ? $req->reference : "-";
                	//$data['branch_id']=$branch_ids;
                	//$data['course_id']=$req->course;
                	//$data['fee_head_id']=$fee_head_id_arr[$l];
                	$data['reciept_date']=$req->reciept_date;
                	$receipt_id[]=DB::table('collect_fee')->insertGetId($data);
                }	
				$l++;
			}
			
			$payment_count=count($receipt_id);
            $collect_id=$receipt_id[$payment_count-1];
            $receipt_no=$this->reciept_no($collect_id);
            foreach ($receipt_id as $key => $value) {
               DB::table('collect_fee')->where('id',$value)->update([
                    'reciept_no'=>$receipt_no
               ]);
            }
			if($checkStatus == 1){
			    Session::flash('msG', 'Fees Collected successfully.');
			    return redirect('studentfeeReceipt/'.$receipt_no); //route('collect_fee');
			}else{
			    
			    Session::flash('msG', 'Invalid paid amount, Please try again.');
			    
			    return redirect('collect_fee');
			    /*
			    return view('Fee.collect', compact('panel', 'session_list', 'course_list', 'student_list', 'branch_list', 'branch_ids', 'pay_type_list', 'current_session'));
			    */
			}
			
		}else{
			return view('Fee.collect', compact('panel', 'session_list', 'course_list', 'student_list', 'branch_list', 'branch_ids', 'pay_type_list', 'current_session'));
		}
	}


	public function collection_List(Request $req){
		if(Session::has('activeBranch')){
		    $branch_ids = Session::get('activeBranch');
		}else{ $branch_ids = Auth::user()->branch_id; }

		$current_session=Session::get('activeSession');

		$collection_list=Collection::select('collect_fee.*', 'session.session_name', 'students.first_name', 'students.reg_no', 'students.category_id', 'users.name', 'fee_heads.fee_head_title', 'faculties.faculty', 'assign_fee.fee_head_id', 'fee_heads.fee_head_title')
		->leftJoin('assign_fee', function($join){
		    $join->on('collect_fee.assign_fee_id', '=', 'assign_fee.id');
		})->leftJoin('session', function($join){
			$join->on('session.id', '=', 'assign_fee.session_id');
		})->leftJoin('students', function($join){
				$join->on('students.id', '=', 'collect_fee.student_id');
		})->leftJoin('faculties', function($join){
				$join->on('faculties.id', '=', 'assign_fee.course_id');
		})->leftJoin('fee_heads', function($join){
				$join->on('fee_heads.id', '=', 'assign_fee.fee_head_id');
		})->leftJoin('users', function($join){
				$join->on('users.id', '=', 'collect_fee.created_by');
		})
		->where('assign_fee.branch_id', $branch_ids)
		->where('collect_fee.status',1)
		->where('assign_fee.session_id', $current_session)

		->where(function($qry) use ($req){
			if($req->faculty){
				$qry->where('assign_fee.course_id', $req->faculty);
			}
			if ($req->reg_start_date && $req->reg_end_date) {
            	$qry->whereBetween('collect_fee.reciept_date', [$req->get('reg_start_date')." 00:00:00", $req->get('reg_end_date')." 23:59:00"]);
        	}else{
        		if(isset($_GET['reg_start_date'])){ 
          		}else{
        			$start_date=date("Y-m-d")." 00:00:00";
                    $end_date=date("Y-m-d")." 23:59:00";
                    $qry->whereBetween('collect_fee.reciept_date', [$start_date, $end_date]);
                }
        	}
        	if($req->category){
        		$qry->where('students.category_id', $req->category);
        	}
        	if($req->name){
        		$qry->Where('students.first_name', 'like', '%'.$req->name.'%');
        		$qry->orWhere('students.reg_no', 'like', '%'.$req->name.'%');
        	}
        	
        	if($req->payment_type){
        		$qry->where('collect_fee.payment_type', 'like', '%'.$req->payment_type.'%'); 
        	}
        	if($req->ref_no){
        		$qry->where('collect_fee.reference', 'like', '%'.$req->ref_no.'%'); 
        	}
        	if($req->fee_head){
        		$qry->where('assign_fee.fee_head_id', 'like', '%'.$req->fee_head.'%'); 
        	}
		})
		->where('collect_fee.status', '=', '1')
		->orderBy('collect_fee.id','desc')->get();
		
		//->paginate(10);
		$ssn_tble=DB::table('session')->select('id', 'session_name')->where('active_status', '1')->get();
		$current_session=$ssn_tble[0]->id;
		//die("-->".$ssn_tble[0]->id);
		$course_list=Faculty::select('id', 'faculty')->where('branch_id', $branch_ids)->pluck('faculty', 'id')->toArray();
		$course_list=array_prepend($course_list, "Select Course", "");
		$category_list=category_model::select('id', 'category_name')->pluck('category_name', 'id')->toArray();
		$category_list=array_prepend($category_list, "Select Category", "");
		$panel='Collection';
		
		$pay_type_list = DB::table('payment_type')->select('type_name')->where('status', '1')->pluck('type_name','type_name')->toArray(); 
		//dd($pay_type_list);//, 'id'
		$pay_type_list = array_prepend($pay_type_list, "Payment Mode", "");
		$feeHead = FeeHead::select('id', 'fee_head_title')->where('status', 1)->pluck('fee_head_title', 'id')->toArray(); 
		
		$feeHead = array_prepend($feeHead, "Select Fee Head", "");

		return view('Fee.collectionList', compact('collection_list', 'panel', 'course_list', 'student_list', 'category_list', 'branch_ids', 'current_session','pay_type_list','feeHead'));
	}

	public function dueReport(Request $req){
		$data=$student_tbl=[];
		$data['course_list']=Faculty::where('status', '1')->where('branch_id', session('activeBranch'))->pluck('id', 'faculty');
		$data['student_list']=DB::table('students')->where('status', '1')->select('id', 'first_name')->get();
		$data['semester_list']=DB::table('semesters')->select('id', 'semester')->get();

		// Get Course & Section Title
			$courseNameArr = json_decode(json_encode($data['course_list']), true);
			$courseNameArr = array_flip($courseNameArr);
			$courseID  = (isset($req->course) && $req->course!=0)? $req->course : "";

		// End

		if($req->all()){

			$info['Error_Message'] = "";
			if($req->course==0 OR $req->semester==0)
			{
				Session::flash('msG', 'Faculty & Semester field is required!');
				return view('Fee.dueReport', compact('data'));
			}
			
			if($courseID!=""){
				$info['courseName'] 	= $courseNameArr[$req->course];
			}else{
				$info['courseName'] 	= "-";
			}

			$printed_data=array();	//dd($req->all());
			if($req->admission_no){
				$student_session=StudentDetailSessionwise::select('students.branch_id', 'students.first_name', 'student_detail_sessionwise.*')
				->leftjoin('students', function($join){
					$join->on('student_detail_sessionwise.student_id', '=', 'students.id');
				})->where(function($query) use ($req){
					$query->where('students.branch_id', session('activeBranch'));
					$query->where('student_detail_sessionwise.session_id', session('activeSession'));
					$query->where('students.reg_no', $req->admission_no);
				})->where('students.status','=',1)
				->orderBy('students.first_name','asc')
				->get();
			}else{
				$course=$req->course; $semester=$req->semester;
				$student=$req->student;
				$student_session=StudentDetailSessionwise::select('students.branch_id', 'students.first_name', 'student_detail_sessionwise.*')
				->leftjoin('students', function($join){
					$join->on('student_detail_sessionwise.student_id', '=', 'students.id');
				})->where(function($query) use ($req){
					$query->where('students.branch_id', session('activeBranch'));
					$query->where('student_detail_sessionwise.session_id', session('activeSession'));
					if($req->course){ $query->where('student_detail_sessionwise.course_id', $req->course); }
					if($req->student){ $query->where('student_detail_sessionwise.student_id', $req->student); }
					if($req->semester){ $query->where('student_detail_sessionwise.Semester', $req->semester); }
				})->where('students.status','=',1)
				->orderBy('students.first_name','asc')
				->get();
			}
			// dd($student_session[]);
			$printed_data=$due_tbl=array();
			$printed_data['branch']=Branch::where('id', session('activeBranch'))->get()->toArray(); $k=1;
			foreach($student_session as $stud){

				//,DB::raw("SUM(assign_fee.fee_amount) as amount")
				$assign_qry=AssignFee::select('assign_fee.*',DB::raw('GROUP_CONCAT(assign_fee.id) as assignId'),DB::raw("SUM(assign_fee.fee_amount) as amount"),'fee_heads.fee_head_title')->leftJoin('fee_heads', function($join){
				$join->on('assign_fee.fee_head_id', '=', 'fee_heads.id');
				})->where([['course_id', $stud->course_id], ['session_id', $stud->session_id]])->whereIn('assign_fee.student_id', ['0', $stud->student_id])->get()->toArray();
				//dd( $stud->student_id,'---',$assign_qry);

					$ids = explode(',',$assign_qry[0]['assignId']);
					$collect[$stud->student_id]["paid"]=0;
					$collect[$stud->student_id]["due"]=0;
					for ($i=0; $i <count($ids) ; $i++) { 
						$collected=DB::table('collect_fee')->select('st.id',DB::raw("SUM(collect_fee.amount_paid) as amount_paid"),'st.first_name','st.reg_no','pd.father_first_name')
						->leftjoin('students as st','st.id','=','collect_fee.student_id')
						->leftjoin('parent_details as pd','pd.students_id','=','collect_fee.student_id')
						->where('student_id',$stud->student_id)
						->where('collect_fee.status','=',1)
						->whereIn('assign_fee_id', [$ids[$i]])
						->get()->toArray();	
						$collect[$stud->student_id]["paid"]=$collected[0]->amount_paid+$collect[$stud->student_id]["paid"];
					}
					$stdata[$stud->student_id]=DB::table('students')->select('first_name','reg_no','pd.father_first_name')
					->leftjoin('parent_details as pd','pd.students_id','=','students.id')
					->where('students.id',$stud->student_id)
					->get();
					$collect[$stud->student_id]["total"]=$assign_qry[0]['amount'];
					$collect[$stud->student_id]["due"]=$assign_qry[0]['amount'] - $collect[$stud->student_id]["paid"];
					$studId=$stud->student_id;
					$due_tbl[$studId]=$assign_qry[0];
					$student_tbl[$studId]=$stud->first_name;	
			}	
			return view('Fee.duePrint', compact('printed_data', 'due_tbl','collect', 'stdata','student_tbl','info'));
		}else{
			return view('Fee.dueReport', compact('data'));
		}
	}


	public function feeReport(Request $req){
		
		try{
		$data=$student_tbl=[];
		$data['course_list']=Faculty::where('status', '1')->where('branch_id', session('activeBranch'))->pluck('id', 'faculty');
		$data['fee_list']=DB::table('fee_heads')->where('status', '1')->select('id', 'fee_head_title')->get();
		$data['semester_list']=DB::table('semesters')->select('id', 'semester')->get();

		// Get Course & Section Title
			$courseNameArr = json_decode(json_encode($data['course_list']), true);
			$courseNameArr = array_flip($courseNameArr);
			
			$courseID  = (isset($req->course) && $req->course!=0)? $req->course : "";
		// End
		if($req->all()){

			$info['Error_Message'] = "";
			if($req->course==0 OR $req->semester==0)
			{
				Session::flash('msG', 'Faculty & Semester field is required!');
				return view('Fee.feeReport', compact('data'));
			}
			if($courseID!=""){
				$info['courseName'] 	= $courseNameArr[$req->course];
			}else{
				$info['courseName'] 	= "-";
			}

			$printed_data=array();
			if($req->admission_no){
				$student_session=StudentDetailSessionwise::select('students.branch_id', 'students.first_name', 'student_detail_sessionwise.*')
				->leftjoin('students', function($join){
					$join->on('student_detail_sessionwise.student_id', '=', 'students.id');
				})->where(function($query) use ($req){
				$query->where('students.branch_id', session('activeBranch'));
				$query->where('student_detail_sessionwise.session_id', session('activeSession'));
				$query->where('students.reg_no', $req->admission_no);
				})->where('students.status','=',1)
				->orderBy('students.first_name','asc')
				->get();
			}else{
				$course=$req->course; $semester=$req->semester;
				$fee_type=$req->fee_type;
				$student_session=StudentDetailSessionwise::select('students.branch_id', 'students.first_name', 'student_detail_sessionwise.*')
				->leftjoin('students', function($join){
					$join->on('student_detail_sessionwise.student_id', '=', 'students.id');
				})->where(function($query) use ($req){
					if($req->course){ $query->where('student_detail_sessionwise.course_id', $req->course); }
					if($req->semester){ $query->where('student_detail_sessionwise.Semester', $req->semester); }
					$query->where('students.branch_id', session('activeBranch'));
					$query->where('student_detail_sessionwise.session_id', session('activeSession'));
				})
				->where('students.status','=',1)
				->orderBy('students.first_name','asc')
				->get();
			}
			$printed_data=$due_tbl=array();
			$printed_data['branch']=Branch::where('id', session('activeBranch'))->get()->toArray(); $k=1;
			foreach($student_session as $stud){

				$assign_qry=AssignFee::select('assign_fee.*', 'collect_fee.reciept_no', 'collect_fee.amount_paid', 'collect_fee.discount', 'collect_fee.fine','collect_fee.reciept_date','payment_type','reference','collect_fee.created_by as receipt_by','users.name','collect_fee.reciept_date', 'fee_heads.fee_head_title','parent_details.father_first_name','students.reg_no')->leftjoin('collect_fee', function($join){
				$join->on('assign_fee.id', '=', 'collect_fee.assign_fee_id');
				})->leftJoin('fee_heads', function($join){
				$join->on('assign_fee.fee_head_id', '=', 'fee_heads.id');
				})->leftJoin('users', function($join){
				$join->on('users.id', '=', 'collect_fee.created_by');
				})->leftJoin('parent_details', function($join){
				$join->on('parent_details.students_id', '=', 'collect_fee.student_id');
				})->leftJoin('students', function($join){
				$join->on('students.id', '=', 'collect_fee.student_id');
				})->where(function($query) use ($req){
				if($req->fee_type){ $query->where('assign_fee.fee_head_id', $req->fee_type); }
				if($req->from && $req->to){
				$query->whereBetween('collect_fee.reciept_date', array($req->from, $req->to));
				}
				})->where([['collect_fee.amount_paid','!=', 0],['collect_fee.status','=',1],['course_id', $stud->course_id], ['assign_fee.session_id', $stud->session_id], ['collect_fee.student_id', $stud->student_id]])->whereIn('assign_fee.student_id', ['0', $stud->student_id])->get()->toArray();
				foreach($assign_qry as $assign_data){
					$studId=$stud->student_id;
					$due_tbl[$studId][]=$assign_data;
					$student_tbl[$studId]=$stud->first_name;
				}
			}
			return view('Fee.feePrint', compact('printed_data', 'due_tbl', 'student_tbl','info'));
		}else{
			return view('Fee.feeReport', compact('data'));
		}
} catch (\Exception $e) { dd($e); }
	}	

public function noDues(Request $req){
	try{
		$data=$student_tbl=$fee_arr=[];
		$data['course_list']= Faculty::where('status', '1')
								->where('branch_id', session('activeBranch'))
								->pluck('id', 'faculty');

		$data['fee_list']=	DB::table('fee_heads')
							->where('status', '1')
							->select('id', 'fee_head_title')
							->get();

		$data['semester_list']=DB::table('semesters')
								->select('id', 'semester')
								->get();
		if($req->all()){

			$printed_data=array();

			$course 	=	$req->course; 
			$semester 	=	$req->semester;
			$fee_type 	=	$req->fee_type;
			
			// Get Course & Section Title

			$info['Error_Message'] = "";
			if($req->course==0 OR $req->semester==0)
			{
				Session::flash('msG', 'Faculty & Semester field is required!');
				return view('Fee.headwiseReport', compact('data'));
			}

			$courseNameArr = json_decode(json_encode($data['course_list']), true);
			$courseNameArr = array_flip($courseNameArr);
			$info['courseName'] 	= $courseNameArr[$req->course];

			// End

			$student_session=StudentDetailSessionwise::select('students.branch_id', 'students.first_name', 'students.reg_no', 'student_detail_sessionwise.*','pd.father_first_name as fatherName')
			->leftjoin('students', function($join){
				$join->on('student_detail_sessionwise.student_id', '=', 'students.id');
			})->where(function($query) use ($req){
				if($req->course){ $query->where('student_detail_sessionwise.course_id', $req->course); }
				if($req->semester){ $query->where('student_detail_sessionwise.Semester', $req->semester); }
				$query->where('students.branch_id', session('activeBranch'));
				$query->where('student_detail_sessionwise.session_id', session('activeSession'));
			})
			->where('students.status','=',1)
			->join('parent_details as pd', 'pd.students_id', '=', 'students.id')
			->orderBy('students.first_name','asc')
			->get();
			 
			$printed_data=$due_tbl=array();
			$printed_data['branch']=Branch::where('id', session('activeBranch'))->get()->toArray(); $k=1;
			
			foreach($student_session as $stud){

				$assign_qry=AssignFee::select('assign_fee.*', 'fee_heads.fee_head_title')->leftJoin('fee_heads', function($join){
						$join->on('assign_fee.fee_head_id', '=', 'fee_heads.id');
				})->where(function($query) use ($req){
				if($req->fee_type){
					$query->where('assign_fee.fee_head_id', $req->fee_type);
				}
				})->where([['course_id', $stud->course_id], ['session_id', $stud->session_id]])->whereIn('assign_fee.student_id', ['0', $stud->student_id])->get()->toArray();

				foreach($assign_qry as $assign_data){
					
					$studId=$stud->student_id;
					$fee_name=$assign_data['fee_head_title'];
					$fee_arr[$fee_name]=$fee_name;
					$collect_qry=Collection::where([['student_id', $studId],['status','=',1], ['assign_fee_id', $assign_data['id']]])->get();
					/*
					foreach($collect_qry as $collect){
						$due_tbl[$studId][$fee_name]['to_pay']=$assign_data['fee_amount'];
						$due_tbl[$studId][$fee_name]['paid'][]=$collect->amount_paid;
						$due_tbl[$studId][$fee_name]['disc'][]=$collect->discount;
						$due_tbl[$studId][$fee_name]['fine'][]=$collect->fine;
						$due_tbl[$studId][$fee_name]['student']=$stud->first_name;
						$due_tbl[$studId][$fee_name]['admission_no']=$stud->reg_no;
						$due_tbl[$studId][$fee_name]['fatherName']=$stud->fatherName;
					}
					*/
					
					if(count($collect_qry)>0){
							foreach($collect_qry as $collect){
								$due_tbl[$studId][$fee_name]['to_pay']=$assign_data['fee_amount'];
								$due_tbl[$studId][$fee_name]['paid'][]=$collect->amount_paid;
								$due_tbl[$studId][$fee_name]['disc'][]=$collect->discount;
								$due_tbl[$studId][$fee_name]['fine'][]=$collect->fine;
								$due_tbl[$studId][$fee_name]['student']=$stud->first_name;
								$due_tbl[$studId][$fee_name]['admission_no']=$stud->reg_no;
								$due_tbl[$studId][$fee_name]['fatherName']=$stud->fatherName;
								
							}
					}else{
								$due_tbl[$studId][$fee_name]['to_pay']=$assign_data['fee_amount'];
								$due_tbl[$studId][$fee_name]['paid'][]=0;
								$due_tbl[$studId][$fee_name]['disc'][]=0;
								$due_tbl[$studId][$fee_name]['fine'][]=0;
								$due_tbl[$studId][$fee_name]['student']=$stud->first_name;
								$due_tbl[$studId][$fee_name]['admission_no']=$stud->reg_no;
								$due_tbl[$studId][$fee_name]['fatherName']=$stud->fatherName;
					}
					$student_tbl[$studId]=$stud->first_name;
				}
			} 
			return view('Fee.headwisePrint', compact('printed_data', 'due_tbl', 'student_tbl', 'fee_arr','info'));
		}else{
			return view('Fee.headwiseReport', compact('data'));
		}
	} catch (\Exception $e) { dd($e); }
}

	public function edit_collection($id,Request $request){

		if($request->all()){
			$arr['log_status']=1;
			$status=$this->insert_in_log($id,$arr);
			if($status){
				$update=DB::table('collect_fee')->where('id',$id)->update([
				'updated_at'=>Carbon::now(),
				'amount_paid'=>$request->amount,
				'assign_fee_id'=>$request->assign_fee_id,
				'reference'=>$request->ref_no,
				'payment_type'=>$request->pay_mode,
				'reciept_date'=>$request->date
			]);
				if($update){
					return redirect('/collection_List')->with('message_success','Updated Successfully');
				}
			}
			else{
				return redirect('/collection_List')->with('message_warning','Something Went Wrong');
			}
		}
		$data=DB::table('collect_fee')->select('collect_fee.*','assign_fee.fee_head_id','course_id','session_id')->where('collect_fee.id',$id)
		->leftjoin('assign_fee','collect_fee.assign_fee_id','=','assign_fee.id')
		->first();
		$fee_head=DB::table('assign_fee')->select('assign_fee.id as id','fee_heads.fee_head_title as title')->where([
			['course_id','=',$data->course_id],
			['assign_fee.status','=',1],
			['session_id','=',$data->session_id],
			['branch_id','=',Session::get('activeBranch')]
		])->where(function($query)use($data){
			$query->where('student_id',0);
			$query->orWhere('student_id',$data->student_id);
		})
		->leftjoin('fee_heads','fee_heads.id','=','assign_fee.fee_head_id')
		->pluck('title','id')->toArray();
		$fee_head=array_prepend($fee_head,'--Select Fee Head--','');
		$pay_type = DB::table('payment_type')->select('type_name')->where('status', '1')->pluck('type_name','type_name')->toArray(); 	
		$pay_type = array_prepend($pay_type, "----Select Payment Mode----", "");

		return view('Fee.collect_fee_edit',compact('data','fee_head','id','pay_type'));
	}
	public function insert_in_log($id,$arr){
		$date=Carbon::now()->format('Y-m-d H:i:s');
		$data=DB::table('collect_fee')->select('*')->where('id',$id)->first();
		$data->log_created_at=$date;
		$data->log_created_by=auth()->user()->id;
		
		foreach ($data as $key => $value) {
			if($key=='id'){
				$arr['collect_fee_id']=$value;
			}else{
					$arr[$key]=$value;
			}
		
		}
		$insert=DB::table('collect_fee_log')->insert($arr);
		return $insert;
	}
	public function delete_collection($id){
		$arr['log_status']=2;
		$status=$this->insert_in_log($id,$arr);
		if($status){
			$data=DB::table('collect_fee')->where('id',$id)->update([
				'status'=>0
			]);
			if($data){
						return redirect('/collection_List')->with('message_success','Record Deleted');
				}
		}
			else{
				return redirect('/collection_List')->with('message_warning','Something Went Wrong');
			}
	}

}

