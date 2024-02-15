<?php
namespace App\Http\Controllers\Miscellaneous;
use App\Http\Controllers\CollegeBaseController;
use App\Session_Model; 
use App\Models\Miscellaneous\MiscellaneousHead;
use Session; 
use App\Models\Faculty;
use App\Models\Miscellaneous\MiscellaneousAssignFee; 
use DB,Log;
use Illuminate\Http\Request;  
use App\Models\Miscellaneous\MiscellaneousCollection;
use Illuminate\Support\Facades\Validator;
use Auth,URL; use App\category_model;
use App\Branch; use App\Models\StudentDetailSessionwise;
use App\StudentPromotion;
use Carbon\Carbon;
use App\Models\FeeStructure;
use App\User;
use App\Models\GeneralSetting;

class MiscellaneousAssignFeeController extends CollegeBaseController{
	
	public function new_index(Request $req){
		
		if(Session::has('activeBranch')){
		    $branch_ids = Session::get('activeBranch');
		}else{ $branch_ids = Auth::user()->branch_id; }
		
		$assign_list=MiscellaneousAssignFee::select('miscellaneous_assign_fee.*','faculties.faculty', 'fee_heads.fee_head_title','session.session_name', 'users.name','cb.title as batch_title','cb.start_date','cb.end_date')
		->leftJoin('fee_heads', function($join){
				$join->on('fee_heads.id', '=', 'miscellaneous_assign_fee.fee_head_id');
		})
		->leftJoin('session', function($join){
			$join->on('session.id', '=', 'miscellaneous_assign_fee.session_id');
		
		})->leftJoin('users', function($join){
				$join->on('users.id', '=', 'miscellaneous_assign_fee.created_by');
		})->leftJoin('faculties', function($join){
				$join->on('faculties.id', '=', 'miscellaneous_assign_fee.course_id');
		})->leftjoin('course_batches as cb',function($j){
			$j->on('cb.id','=','miscellaneous_assign_fee.batch_id');
		})
		// ->leftjoin('fee_heads as fee','fee.id','=','assign_fee.fee_head_id')
		// ->leftjoin('fee_heads as sub_head','sub_head.id','=','fee.parent_id')
		->where([
			['miscellaneous_assign_fee.session_id','=', Session::get('activeSession')],
			['miscellaneous_assign_fee.branch_id','=', $branch_ids],
			['miscellaneous_assign_fee.status','=',1]
		])->orderBy('miscellaneous_assign_fee.course_id','asc')->get();
		// ->paginate(10);
		$head_list=MiscellaneousHead::where([
			['status','=',1],
			['parent_id','=',0]
		])->get();

		foreach ($head_list as $key => $value) {
			$sub_heads[$value->id]=MiscellaneousHead::where([
				['status','=',1],
				['parent_id','=',$value->id]
			])->select('id','fee_head_title')->get();
		}
		$current_session=Session::get('activeSession');
		$session_list=Session_Model::select('id', 'session_name')->where('id',$current_session)->pluck('session_name', 'id')->toArray();
		// $session_list=array_prepend($session_list,'Select Session',"");
		$ssn_tble=DB::table('session')->select('id', 'session_name')->where('active_status', '1')->get();
		$current_session=Session::get('activeSession');	//$ssn_tble[0]->id;
		//die("-->".$ssn_tble[0]->id);
		$course_list=Faculty::select('id', 'faculty')->where('branch_id', $branch_ids)->pluck('faculty', 'id')->toArray();
		$course_list=array_prepend($course_list, "Select ".env("course_label"), "");
		//get();
		$panel='Fees Assign';
		$subj_list = DB::table('subjects')->select('id', 'title')->pluck('title', 'id')->toArray();
		$subj_list = array_prepend($subj_list, "Select Subject", "");

		// $student_list = DB::table('students')->select('id', 'first_name')->pluck('first_name', 'id')->toArray();
		$student_list=[];
		$student_list = array_prepend($student_list, "select Student", "");
		$months_list=DB::table('months')->select('title','id')->where('status',1)->pluck('title','id')->toArray();
		$months_list=array_prepend($months_list,'--Select Due Month--','');
		$branch_list = Branch::select('branch_name','branch_address','id','org_id')->where("id",$branch_ids)->get();
		
	//	$branch_list = array_prepend($branch_list, "Select Branch", "");

		return view('miscellaneous.fee.new_assign', compact('assign_list', 'panel', 'head_list', 'session_list', 'course_list', 'subj_list', 'student_list', 'branch_list', 'branch_ids', 'current_session','sub_heads','months_list'));
	}

	public function new_assigned(Request $req){
		// dd($req->all());
		$rules=['heads'=>'required', 'session'=>'required|numeric', 'course'=>'required', 'branch_id'=>'required'];
		if(Session::get('isCourseBatch')){
			$rules['batch']='required';
		}
		
		$valid=Validator::make($req->all(), $rules);
		if($valid->fails()){
			// dd($valid);
		  return redirect()->back()->withErrors($valid)->withInput();
		}else{
			$session = $req->session;	$branch_id=$req->branch_id;
			$course=$req->course; 		$subject=$req->subject;
			$student = $req->student;
			$batch_id = Session::get('isCourseBatch')?$req->batch:null;
			$head_arr=$req->heads; //dd($req);
			$head_id_arr=$req->fee_hd; $fee_amount_arr = $req->fee_amnt;
			$fee_structure=$req->fee_structure;
			$month_id=$req->due_month;
			$indx=0;
			//foreach($head_arr as $key=>$val){
			// dd($fee_amount_arr,$head_id_arr,$fee_structure);
			foreach($fee_amount_arr as $fee_amount){

				$cnt=0;
				// if($times_arr[$indx] == "Yearly"){ $k=1; }else{ $k=2; }
				// for($i=0; $i<$k; $i++){
					$fee_id = $head_id_arr[$indx];  $cnt+=1;
				// dd($fee_amount,$fee_id,$fee_structure);
					//$fee_amount = $fee_amount_arr[$indx];
					if($fee_id && $fee_amount){
									$assiGn=new MiscellaneousAssignFee;
									$assiGn->branch_id = $branch_id;
									$assiGn->fee_head_id = $fee_id;
									$assiGn->course_id = $course;
									$assiGn->session_id = $session;	
									$assiGn->batch_id = $batch_id;
									// $assiGn->subject_id = $subject;
									$user_id = Auth::user()->id;
									$assiGn->created_by=$user_id;
									$assiGn->student_id = ($student!="") ? $student:0;	
									$assiGn->fee_amount = $fee_amount;
									$assiGn->due_month = $month_id[$indx];
									$assiGn->save();
						
					}//else{ Session::flash('msG', 'Fees Not Assigned. Try Again'); }
				

				$indx++;
			}
			Session::flash('msG', 'Fees Assigned Successfully.');
			return redirect()->route('miscellaneous.newAssignList'); 
		}
	}

	public function branch_select(Request $req){
		$retval="<option value=\"\">----Select ".env("course_label")."----</option>";
		$result = DB::table('faculties')->where('branch_id', $req->vl)->select('id', 'faculty')->get();
		if($result->count()){
			foreach($result as $val){
$retval.="<option value=\"".$val->id."\">".$val->faculty."</option>";
			}
		}else{ $retval="<option value=\"\">----No ".env("course_label")."----</option>"; }	return response($retval);
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
		->where('students.status',1)
		->where(function($query) use ($req){
			if($req->semester){ 
				$query->where('student_detail_sessionwise.Semester', $req->semester); 
			}
		})->get();
		if($result->count()){
			foreach($result as $val){
$retval.="<option value=\"".$val->id."\">".$val->first_name." (".$val->reg_no.")</option>";
			}
		}else{ $retval="<option value=\"\">----No Students----</option>"; }
			return response($retval);
	}
	public function getFeeByBatch(Request $request){
		$data = [];
		$data['error'] = true;
		if($request->course && $request->session && $request->batch){
			$assign_list = MiscellaneousAssignFee::select('assign_fee.*', 'fee_heads.fee_head_title', 'session.session_name')
			    ->leftJoin('fee_heads', function($join){
			                $join->on('fee_heads.id', '=', 'assign_fee.fee_head_id');
			        })
			    ->leftJoin('session', function($join){
			            $join->on('session.id', '=', 'assign_fee.session_id');
			        }) 
			        ->where('course_id' , $request->course)
			        ->where('assign_fee.status','=',1)
			        ->where(function($q) use ($request){
			            if($request->session!=""){
			                $q->where('assign_fee.session_id',$request->session);
			            }
			        })
			        ->where(function($q)use($request){
			        	if($request->batch!=""){
			        		 $q->where('assign_fee.batch_id',$request->batch);
			        	}
			        })
			       ->where('student_id', '0')
			        ->get();
			if(count($assign_list)>0){
				$data['error'] = false;
				$data['msg'] = 'Assigned Fees found';
				$data['data'] = $assign_list;
			}else{
				$data['msg'] =	'No Fees Assigned';
			}        
		}else{
			$data['msg'] = 'Invaid Request';
		}
		return response()->json(json_encode($data));
	}
	public function getBatchByCourse(Request $request){
		$data = [];
		$data['error'] = true;
		if($request->course && $request->session_id){
			$batches = DB::table('course_batches')->select('id','title','start_date','end_date')
			->Where([
				['course_id','=',$request->course],
				['session_id','=',$request->session_id],
				['status','=',1]
			])->get();
			if(count($batches)>0){
				$data['error'] = false;
				$data['msg'] = 'Batches Found.';
				$data['batch'] = $batches;
			}else{
				$data['msg'] = 'No batch found , please create batch for selected '.env('course_label');
			}
		}else{
			$data['msg'] = 'Invald Request!';
		}
		return response()->json(json_encode($data));
	}
	public function getStudentByBatch(Request $request){
		$data = [];
		$data['error'] = true;
		$session=($request->selected_session) ? $request->selected_session : session('activeSession');
			$branch=($request->branch) ? $request->branch:session('activeBranch');
		if($request->course  &&  $request->batch){
			$student = DB::table('students')->select('students.id', 'first_name', 'reg_no')
			->leftJoin('student_detail_sessionwise', function($join){
				$join->on('student_detail_sessionwise.student_id', '=', 'students.id');
			})
			->where('student_detail_sessionwise.course_id', $request->course)
			->where('branch_id', $branch)
			->where('student_detail_sessionwise.session_id', $session)
			->where('students.status',1)
			->where(function($query) use ($request){
			if($request->batch){ 
				$query->where('students.batch_id', $request->batch); 
			}
		})->get();
			Log::debug($student);
			if(count($student)>0){
				$data['error'] = false;
				$data['msg'] = 'Student Found.';
				$data['student'] = $student;
			}else{
				$data['msg'] = 'No student found ';
			}
		}else{
			$data['msg'] = 'Invald Request!';
		}
		return response()->json(json_encode($data));
	}
	
	public function load_feeStructure(Request $request){
		$response="";
		// $response="<option value=\"\">--Select Month--</option>";
		if($request->session && $request->branch && $request->course){
			$data=FeeStructure::select('fee_structures.id','fee_structures.from_month as from_id','fee_structures.to_month as to_id','frm.title as from_month','to.title as to_month')
			->leftjoin('months as frm','frm.id','=','fee_structures.from_month')
			->leftjoin('months as to','to.id','=','fee_structures.to_month')
			->where([
				['faculty_id','=',$request->course],
				['session_id','=',$request->session],
				['branch_id','=',$request->branch],
				['record_status','=',1]
			])->get();
			if(count($data)>0){
				foreach ($data as $key => $value) {
					if($value->from_id==$value->to_id){
						$response.="<option value=".$value->id.">".$value->from_month."</option>";
					}else{
						$response.="<option value=".$value->id.">".$value->from_month." - ".$value->to_month."</option>";
					}
				}
			}else{
				$response="<option value=\"\">--No Structure Defined--</option>";
			}
		}
		return response($response);
	}

	public function get_std_id(Request $request){
		$response=[];
		$response['error']=true;
		if($request->reg_no && $request->session && $request->branch){
			$data=DB::table('student_detail_sessionwise')->select('student_detail_sessionwise.course_id','student_detail_sessionwise.student_id')
			->leftjoin('students','students.id','=','student_detail_sessionwise.student_id')
			->where([
				['student_detail_sessionwise.session_id','=',$request->session],
				['student_detail_sessionwise.active_status','=',1],
				['students.reg_no','=',$request->reg_no],
				['students.branch_id','=',$request->branch]
			])->get();
			if(count($data)>0){
				$response['data']=$data[0];
				$response['error']=false;
			}else{
				$response['msg']='No Such Student';
			}
		}else{
			$response['msg']='Invalid Request!';
		}
		return response()->json(json_encode($response));
	}
	public function student_fee(Request $req){
		$branch=$req->branch; 
		$session=$req->ssn;
		$course=$req->course;
		$month=[];
		$month=$req->due_month;
		// Log::debug(isset($month));
		$fee_result=MiscellaneousAssignFee::Select('miscellaneous_assign_fee.*', 'miscellaneous_heads.fee_head_title','sub_head.fee_head_title as sub_head')
		->leftJoin('miscellaneous_heads', function($join){
			$join->on('miscellaneous_heads.id', '=', 'miscellaneous_assign_fee.fee_head_id');
		})
		->leftjoin('miscellaneous_heads as fee','fee.id','=','miscellaneous_assign_fee.fee_head_id')
		->leftjoin('miscellaneous_heads as sub_head','sub_head.id','=','fee.parent_id')
		->where('miscellaneous_assign_fee.branch_id', $branch)
		->where('miscellaneous_assign_fee.session_id', $session)
		->where('miscellaneous_assign_fee.course_id', $course)
		->where('miscellaneous_assign_fee.status',1)
		->where(function($query)use($month,$req){
			if(isset($month)){
				foreach ($month as $key => $value) {
					$query->orWhere('miscellaneous_assign_fee.due_month',$value);
				}
			}
			if($req->batch){
				$query->Where('miscellaneous_assign_fee.batch_id',$req->batch);
			}
		})
		->Where('miscellaneous_assign_fee.student_id', '0')
		->orWhere(function($q) use ($req,$month){
			if($req->student){
				$q->orWhere('miscellaneous_assign_fee.student_id', $req->student)
				->where('miscellaneous_assign_fee.branch_id', $req->branch)
				->where('miscellaneous_assign_fee.session_id', $req->ssn)
				->where('miscellaneous_assign_fee.status',1)
				->where(function($query)use($month,$req){
					if(isset($month)){
						foreach ($month as $key => $value) {
							$query->orWhere('miscellaneous_assign_fee.due_month',$value);
						}
					}
					if($req->batch){
						$query->Where('miscellaneous_assign_fee.batch_id',$req->batch);
					}	
					})
				->where('miscellaneous_assign_fee.course_id', $req->course);
			}
		})->groupBy('miscellaneous_assign_fee.id')->orderBy('miscellaneous_assign_fee.due_month','asc')->get();
		$ret_val=""; $i=0;
		Log::debug($fee_result);
if(count($fee_result) && $req->student){
	$ret_val.="<div class=\"row\" style=\" text-align:left; background:#F89406; padding:10px 15px; color:#ffffff;\">
			<div class=\"col-sm-2\"><b>Fee Head</b></div>
			<div class=\"col-sm-1\"></div>
			<div class=\"col-sm-1\"><b>Fees</b></div>
			<div class=\"col-sm-1\" title=\"Total Paid + Total Discount\"><b>Paid</b> <i class=\"fa fa-info-circle \" ></i> </div>
			<div class=\"col-sm-2\"><b>Amount</b></div>
			
			<div class=\"col-sm-1\"><b>Due</b></div>
			<div class=\"col-sm-1\"><b>Discount</b></div>
			<div class=\"col-sm-3\" style=\"text-align:center\"><b>Remarks</b></div>
			</div>";
			$total_paid=$total_due=$to_pay=0;
		foreach($fee_result as $fee){
			$paid_result=DB::table('miscellaneous_collect_fee')->where('assign_fee_id', $fee->id)->where('student_id', $req->student)
			->Where('status' , 1)->sum('amount_paid');
			Log::debug($req->student);
			$discount=DB::table('miscellaneous_collect_fee')->where('assign_fee_id', $fee->id)->where('student_id', $req->student)
			->Where('status' , 1)->sum('discount');
			$paid_result=$paid_result + $discount;
			$due = $fee->fee_amount - $paid_result;
			$total_paid=$total_paid+$paid_result;
			$total_due=$total_due+$due;
			$to_pay=$to_pay+$fee->fee_amount;
			$disabled = ($due == 0) ? " style=\"display:none;\"":"";
			$ret_val.="<div class=\"row\" style=\"margin:5px 0px; text-align:left;border-bottom:1px dashed lightgrey;padding-bottom:5px;\">
			<input type=\"hidden\" name=\"assign_id[]\" value=\"".$fee->id."\">
			
			<div class=\"col-sm-2\">
 
<b>".++$i."): ".$fee->fee_head_title."</div><div class=\"col-sm-1\">
<input type=\"hidden\" value=\"".$fee->fee_head_id."\" name=\"fee_head_id[]\">
<b>".$fee->times." </b></div>
			<div class=\"col-sm-1\"><b><i class=\"fa fa-inr\"></i> ".$fee->fee_amount."</b></div>
			<div class=\"col-sm-1\"><b><i class=\"fa fa-inr\"></i> ".$paid_result."</b></div>
			<div class=\"col-sm-2\"><b>";
			if($due){
				$ret_val.="<input type=\"number\" class=\"amount\" placeholder=\"Enter Amount\" name=\"amount[]\" max=\"".$due."\" value=\"0\" onkeyup=\"sum_amount()\" required>";
			}else{
			$ret_val.="<input $disabled placeholder=\"Enter Amount\" type=\"number\" name=\"amount[]\" value=\"\">";
			}

			$ret_val.="</b></div>
			<div class=\"col-sm-1\"><b><i class=\"fa fa-inr\"></i> $due
<input type=\"hidden\" name=\"due_amount[]\" value=\"".$due."\">
			</b></div>
			<div class=\"col-sm-1\"><input type=\"text\" name=\"discount[]\" $disabled class=\"discount\" placeholder=\"Enter Discount\" onkeyup=\"sum_discount()\" value=\"0\"/></div>
			<div class=\"col-sm-3\">
				<input type=\"text\" $disabled placeholder=\"Enter Remarks\" name=\"remark[]\" class=\"pull-right\">
			</div>

			</div>";
		}
		$ret_val.="<div class=\"row\" style=\" text-align:left; background:#f4ddbf; padding:10px 15px; color:#000;\" ><div class=\"col-sm-3\">
					 <b>Total</b>
					</div>
			 		<div class=\"col-sm-1\">
			 			<b><i class=\"fa fa-inr\"></i> ".$to_pay."</b>
			 		</div>
			 		<div class=\"col-sm-1\">
			 			<b><i class=\"fa fa-inr\"></i> ".$total_paid."</b>
			 		</div>
			 		<div class=\"col-sm-2\"><b><i class=\"fa fa-inr\"></i> <b id=\"total_amount\">0</b></b>
			 		</div>
			 		<div class=\"col-sm-1\">
			 			<b><i class=\"fa fa-inr\"></i> ".$total_due."</b>
			 		</div>
			 		<div class=\"col-sm-2\"><b><i class=\"fa fa-inr\"></i> <b id=\"total_discount\">0</b></b>
			 		</div></div>";
		
}else{ $ret_val="<div class=\"alert alert-danger\"><h3>No Fees Assigned</h3></div>"; }
		return response($ret_val);
	}

	public function student_fee_history(Request $req)

	{
		$branch=$req->branch; 
		$session=$req->ssn; 
		$course=$req->course;
		$feeHistory_data = DB::table('miscellaneous_collect_fee')->Select('miscellaneous_collect_fee.id','miscellaneous_collect_fee.status','miscellaneous_collect_fee.student_id','miscellaneous_collect_fee.discount', 'miscellaneous_collect_fee.reciept_date','miscellaneous_collect_fee.assign_fee_id','miscellaneous_collect_fee.amount_paid','miscellaneous_collect_fee.reciept_no','miscellaneous_collect_fee.payment_type','miscellaneous_collect_fee.status','miscellaneous_collect_fee.discount','sd.first_name','sd.branch_id','sd.reg_no', 'sd.reg_date', 'sd.university_reg','sd.date_of_birth', 'sd.gender','asf.fee_amount','asf.course_id','asf.fee_head_id','fd.fee_head_title','br.branch_name','br.branch_logo','br.branch_mobile','br.branch_email','br.branch_address', 'fac.faculty')
		        ->where('miscellaneous_collect_fee.student_id','=',$req->student)
		        ->where('miscellaneous_collect_fee.status','=',1)
		        //->Where('asf.student_id', $req->student)
                ->where('asf.branch_id', $branch)
				->where('asf.session_id', $session)
				->where(function($q)use($req){
					if($req->batch){
						$q->where('asf.batch_id',$req->batch);
					}
				})
        ->join('students as sd', 'sd.id', '=', 'miscellaneous_collect_fee.student_id')
        ->join('miscellaneous_assign_fee as asf', 'asf.id', '=', 'miscellaneous_collect_fee.assign_fee_id')
        ->join('miscellaneous_heads as fd', 'fd.id', '=', 'asf.fee_head_id')
        ->join('branches as br', 'br.id', '=', 'sd.branch_id')
        ->join('faculties as fac', 'asf.course_id', '=', 'fac.id')
        ->get();
        $data_val="";$i=0;

		$data_val.="<thead><tr>
                    <th class=\"col-sm-1\">S.N.</th>
                    <th class=\"col-sm-1\">Reciep No</th>
                    <th class=\"col-sm-1\">Fee-Head</th>
                    <th class=\"col-sm-1\">Paid</th>
                    <th class=\"col-sm-1\">Discount</th>
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
                    <td class=\"col-sm-1\"> ".($feeHistory->discount!=null?$feeHistory->discount:'0')."</td>
                    <td class=\"col-sm-1\"> ".$feeHistory->payment_type."</td>
                    <td class=\"col-sm-1\">".$collDate."</td>";
                    if($feeHistory->status==1){
                    $data_val.= "<td class=\"col-sm-1\">PAID</td>";
                    }
                    else
                    {
                    $data_val.= "<td class=\"col-sm-1\">Failed</td>";
                    }
                    
                    $PRINT_URL =  route('miscellaneous.studentfeeReceipt', ['receipt_no' => $feeHistory->reciept_no]); 
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
	public function feeReceipt($receipt_no){
        $generalSetting=GeneralSetting::first();
        $data = DB::table('miscellaneous_collect_fee')->Select('miscellaneous_collect_fee.id','miscellaneous_collect_fee.student_id', 'miscellaneous_collect_fee.reciept_date','miscellaneous_collect_fee.assign_fee_id','miscellaneous_collect_fee.amount_paid','miscellaneous_collect_fee.reciept_no','miscellaneous_collect_fee.payment_type','miscellaneous_collect_fee.reference','miscellaneous_collect_fee.remarks','miscellaneous_collect_fee.created_by','miscellaneous_collect_fee.discount','sd.first_name','sd.branch_id','sd.reg_no', 'sd.reg_date','sd.batch_id', 'sd.university_reg','sd.date_of_birth', 'sd.gender','asf.fee_amount','asf.course_id','asf.fee_head_id','asf.session_id','fd.fee_head_title','br.branch_name','br.branch_logo','br.branch_mobile','br.branch_email','br.branch_address','ur.name', 'fac.faculty','sn.session_name','pd.father_first_name as father_name','ai.mobile_1 as mobile','cb.start_date','cb.end_date','stf.subject')
        ->where('miscellaneous_collect_fee.reciept_no','=', $receipt_no)
        ->where('miscellaneous_collect_fee.status',1)
        ->leftJoin('students as sd', 'sd.id', '=', 'miscellaneous_collect_fee.student_id')
        ->leftJoin('users as ur', 'ur.id', '=', 'miscellaneous_collect_fee.created_by')
        ->leftJoin('miscellaneous_assign_fee as asf', 'asf.id', '=', 'miscellaneous_collect_fee.assign_fee_id')
        ->leftjoin('course_batches as cb',function($j){
            $j->on('cb.id','=','asf.batch_id');
        })
        ->leftJoin('miscellaneous_heads as fd', 'fd.id', '=', 'asf.fee_head_id')
        ->leftJoin('branches as br', 'br.id', '=', 'sd.branch_id')
        ->leftJoin('session as sn', 'sn.id', '=', 'asf.session_id')
        ->leftjoin('parent_details as pd','pd.students_id','=','miscellaneous_collect_fee.student_id')
        ->leftjoin('addressinfos as ai','ai.students_id','=','miscellaneous_collect_fee.student_id')
        
        ->leftJoin('faculties as fac', 'asf.course_id', '=', 'fac.id')
        ->leftjoin('student_detail_sessionwise as stf',function($j)use($receipt_no){
            $j->on('stf.student_id','=','miscellaneous_collect_fee.student_id');
            $j->where('stf.session_id','=',function($w)use($receipt_no){
                $w->selectRaw(" std_session.session_id from miscellaneous_collect_fee left join student_detail_sessionwise as std_session on std_session.student_id = miscellaneous_collect_fee.student_id where miscellaneous_collect_fee.reciept_no = '$receipt_no' LIMIT 1");
            });
        })
        ->get();

        $subjects = '';
        if($data['0']->subject){
            if(strpos($data['0']->subject, ',')){
                
                $sub = DB::table('timetable_subjects')->select('title as subject')
                ->whereRaw('FIND_IN_SET(id,?)', [$data['0']->subject])->get();
            }else{
                $sub = DB::table('timetable_subjects')->select('title as subject')->where('id','=',$data['0']->subject)->get();
            }
            foreach ($sub as $key => $value) {
                $subjects .= $value->subject.',';
            }
            $subjects = rtrim($subjects,',');
            
        }
        
       $collected = '';
       $count = count($data);
       $i = 1;
        foreach ($data as $key => $value) {
            if($i < $count){
                $collected .= $value->assign_fee_id.',';
            }else{
                $collected .= $value->assign_fee_id;
            }
            $i++;
        }
        $otherDues = DB::table('miscellaneous_assign_fee')->select('fee_amount','miscellaneous_assign_fee.id as assign_fee_id','cf.amount_paid','fee_head_title')
        ->whereRaw('miscellaneous_assign_fee.id not in ( '.$collected.')')
        ->where([
            ['course_id','=',$data['0']->course_id],
            ['session_id','=',$data['0']->session_id],
            ['batch_id','=',$data['0']->batch_id],
            ['miscellaneous_assign_fee.status','=',1],
        ])
        ->whereIn('miscellaneous_assign_fee.student_id',[$data['0']->student_id,'0'])
        ->leftjoin('miscellaneous_collect_fee as cf',function($q)use($data){
            $q->on('cf.assign_fee_id','=','miscellaneous_assign_fee.id')
            ->where('cf.student_id',$data['0']->student_id);
        })
        ->leftjoin('miscellaneous_heads as fh','fh.id','=','miscellaneous_assign_fee.fee_head_id')
        ->selectRaw('SUM(amount_paid) as total_paid,SUM(discount) as total_discount')
        ->groupBy('miscellaneous_assign_fee.id')
        ->get();
        return view('miscellaneous.fee.receipt',compact('data','generalSetting','otherDues','subjects'));
    }
	public function collect(Request $req){

		$session_list=Session_Model::select('id', 'session_name')->pluck('session_name', 'id')->toArray();
		$session_list=array_prepend($session_list,'Select Session',"");
		$panel='Miscellaneous Fees Collect';


		$ssn_tble=DB::table('session')->select('id', 'session_name')->where('active_status', '1')->get();
		//$current_session=$ssn_tble[0]->id;
		$current_session=Session::get('activeSession');


		if(Session::has('activeBranch')){
		    $branch_ids = Session::get('activeBranch');
		}else{$branch_ids = Auth::user()->branch_id; }

		$branch_list = DB::table('branches')->select('id', 'branch_name')->pluck('branch_name', 'id')->toArray();
		//$branch_list = array_prepend($branch_list, "Select Branch", "");
        $branch_list = Branch::select('branch_name','branch_address','id','org_id')->where("id",$branch_ids)->get();
		$course_list=Faculty::select('id', 'faculty')->where('branch_id', $branch_ids)->pluck('faculty', 'id')->toArray();
		$course_list=array_prepend($course_list, "Select ".env("course_label"), "");

		$pay_type_list = DB::table('payment_type')->select('type_name')->where('status', '1')->pluck('type_name','type_name')->toArray(); 
		$months_list=DB::table('months')->select('title','id')->where('status',1)->pluck('title','id')->toArray();
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
                if($assign_id[$l] && ($amnt>0 || $disc[$l]>0) && $amnt !="'" && $req->student && $req->session){
                    
                    $checkStatus = 1;
                	$data['amount_paid']=$amnt;
                	$data['discount']=$disc[$l];
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
                	$receipt_id[]=DB::table('miscellaneous_collect_fee')->insertGetId($data);
                }	
				$l++;
			}
			if(!isset($receipt_id)){
				return redirect('miscellaneous_collect_fee')->with('message_danger','Please enter Amount/Discount more than 0.');	
			}
			$payment_count=count($receipt_id);
            $collect_id=$receipt_id[$payment_count-1];
            // $receipt_no=$this->reciept_no($collect_id);
            $receipt_no=$this->misc_reciept_no($collect_id,$receipt_id);
            foreach ($receipt_id as $key => $value) {
               DB::table('miscellaneous_collect_fee')->where('id',$value)->update([
                    'reciept_no'=>$receipt_no
               ]);
            }
			if($checkStatus == 1){
			    Session::flash('msG', 'Fees Collected successfully.');
			    return redirect('miscellaneous/studentfeeReceipt/'.$receipt_no); //route('collect_fee');
			}else{
			    
			    Session::flash('msG', 'Invalid paid amount, Please try again.');
			    
			    return redirect('miscellaneous_collect_fee');
			    /*
			    return view('Fee.collect', compact('panel', 'session_list', 'course_list', 'student_list', 'branch_list', 'branch_ids', 'pay_type_list', 'current_session'));
			    */
			}
			
		}else{
			return view('miscellaneous.fee.collect', compact('panel', 'session_list', 'course_list', 'student_list', 'branch_list', 'branch_ids', 'pay_type_list', 'current_session','months_list'));
		}
	}
	protected function misc_reciept_no($id='',$receipt_ids=array()){
        if(isset($receipt_ids[0]) && $receipt_ids[0] != ""){
            $currentAutoId = $receipt_ids[0];
            $ssn_tble=DB::table('miscellaneous_collect_fee')->select('id', 'reciept_no')
            ->where('id','<', $currentAutoId)
            ->Where('reciept_no','LIKE',env('MISC_PREFIX_RCP').'%')
            ->orderBy('reciept_no','desc')
            ->first(); 
            $current_session= isset($ssn_tble->reciept_no)?$ssn_tble->reciept_no : '';
            if($current_session == ''){
                $recpt = env('MISC_PREFIX_RCP').'10001';
            }else{
                $lastReceiptNo = str_replace(env('MISC_PREFIX_RCP'), "", $current_session);
                $recpt = env('MISC_PREFIX_RCP').($lastReceiptNo+1);
            }
        }else{
            $recpt=env('MISC_PREFIX_RCP'); $cnt=1; 
            $recpt .=10000+$id;
        }
        return $recpt;
    
    }
	public static function getStudentFeeHeadDueAmout($stdId="",$feeAssignId="",$session="")
    {   
        $qry = "SELECT sum(amount_paid) as totalPaid, max(miscellaneous_assign_fee.fee_amount) as NeedToPay,sum(discount) as totalDiscount
            FROM miscellaneous_collect_fee 
            join miscellaneous_assign_fee on miscellaneous_assign_fee.id=miscellaneous_collect_fee.assign_fee_id
            where miscellaneous_collect_fee.student_id=$stdId and miscellaneous_collect_fee.status=1 and  miscellaneous_collect_fee.assign_fee_id=$feeAssignId";
            $results = DB::select($qry);
        return $results;
    }

	public function collection_List(Request $req){
		if(Session::has('activeBranch')){
		    $branch_ids = Session::get('activeBranch');
		}else{ $branch_ids = Auth::user()->branch_id; }

		$current_session=Session::get('activeSession');
		$user=User::select('users.id',DB::raw("CONCAT(users.name,' ( ',roles.display_name,' )') as name"))
      ->leftjoin('roles','users.role_id','=','roles.id')
        ->where(
          [
            ['users.branch_id','=',Session::get('activeBranch')],
            ['role_id','!=',6],
            ['role_id','!=',7]

        ])->pluck('name','id')->toArray();
        $user=array_prepend($user,'--Receipt By--','');
		$collection_list=MiscellaneousCollection::select('miscellaneous_collect_fee.*', 'session.session_name', 'students.first_name', 'students.reg_no', 'students.category_id', 'users.name', 'miscellaneous_heads.fee_head_title', 'faculties.faculty', 'miscellaneous_assign_fee.fee_head_id', 'miscellaneous_heads.fee_head_title','pd.father_first_name as father_name')
		->leftJoin('miscellaneous_assign_fee', function($join){
		    $join->on('miscellaneous_collect_fee.assign_fee_id', '=', 'miscellaneous_assign_fee.id');
		})->leftJoin('session', function($join){
			$join->on('session.id', '=', 'miscellaneous_assign_fee.session_id');
		})->leftJoin('students', function($join){
				$join->on('students.id', '=', 'miscellaneous_collect_fee.student_id');
		})->leftJoin('faculties', function($join){
				$join->on('faculties.id', '=', 'miscellaneous_assign_fee.course_id');
		})->leftJoin('miscellaneous_heads', function($join){
				$join->on('miscellaneous_heads.id', '=', 'miscellaneous_assign_fee.fee_head_id');
		})->leftJoin('users', function($join){
				$join->on('users.id', '=', 'miscellaneous_collect_fee.created_by');
		})->leftjoin('parent_details as pd','pd.students_id','=','students.id')
		// ->leftjoin('fee_heads as fee','fee.id','=','assign_fee.fee_head_id')
		// ->leftjoin('fee_heads as sub_head','sub_head.id','=','fee.parent_id')
		->where('miscellaneous_assign_fee.branch_id', $branch_ids)
		->where('miscellaneous_collect_fee.status',1)
		->where('students.status',1)
		->where('miscellaneous_assign_fee.session_id', $current_session)
		->where(function($qry) use ($req){
			if($req->faculty){
				$qry->where('miscellaneous_assign_fee.course_id', $req->faculty);
			}
			if ($req->reg_start_date && $req->reg_end_date) {
            	$qry->whereBetween('miscellaneous_collect_fee.reciept_date', [$req->get('reg_start_date')." 00:00:00", $req->get('reg_end_date')." 23:59:00"]);
        	}else{
        		if(isset($_GET['reg_start_date'])){ 
          		}else{
        			$start_date=date("Y-m-d")." 00:00:00";
                    $end_date=date("Y-m-d")." 23:59:00";
                    $qry->whereBetween('miscellaneous_collect_fee.reciept_date', [$start_date, $end_date]);
                }
        	}
        	if($req->category){
        		$qry->where('students.category_id', $req->category);
        	}
        	if($req->receipt_by){
        		$qry->where('miscellaneous_collect_fee.created_by',$req->receipt_by);
        	}
        	if($req->name){
        		$qry->Where('students.first_name', 'like', '%'.$req->name.'%');
        		$qry->orWhere('students.reg_no', 'like', '%'.$req->name.'%');
        	}
        	
        	if($req->payment_type){
        		$qry->where('miscellaneous_collect_fee.payment_type', 'like', '%'.$req->payment_type.'%'); 
        	}
        	if($req->ref_no){
        		$qry->where('miscellaneous_collect_fee.reference', 'like', '%'.$req->ref_no.'%');
        		$qry->orWhere('miscellaneous_collect_fee.reciept_no','like','%'.$req->ref_no.'%');
        	}
        	if($req->fee_head){
        		$qry->where('assign_fee.fee_head_id', 'like', '%'.$req->fee_head.'%'); 
        	}
		})
		->where('miscellaneous_collect_fee.status', '=', '1')
		->selectRAW("sum(amount_paid) as amount_paid")
		->selectRAW("sum(discount) as discount")
		->groupBy('miscellaneous_collect_fee.reciept_no')
		->orderBy('miscellaneous_collect_fee.id','desc')->get();
		
		//->paginate(10);
		$ssn_tble=DB::table('session')->select('id', 'session_name')->where('active_status', '1')->get();
		$current_session=$ssn_tble[0]->id;
		//die("-->".$ssn_tble[0]->id);
		$course_list=Faculty::select('id', 'faculty')->where('branch_id', $branch_ids)->pluck('faculty', 'id')->toArray();
		$course_list=array_prepend($course_list, "Select ".env("course_label"), "");
		$category_list=category_model::select('id', 'category_name')->pluck('category_name', 'id')->toArray();
		$category_list=array_prepend($category_list, "Select Category", "");
		$panel='Collection';
		
		$pay_type_list = DB::table('payment_type')->select('type_name')->where('status', '1')->pluck('type_name','type_name')->toArray(); 
		//dd($pay_type_list);//, 'id'
		$pay_type_list = array_prepend($pay_type_list, "Payment Mode", "");
		$feeHead = MiscellaneousHead::select('id', 'fee_head_title')->where('status', 1)->pluck('fee_head_title', 'id')->toArray(); 
		
		$feeHead = array_prepend($feeHead, "Select Fee Head", "");

		return view('miscellaneous.fee.collectionList', compact('collection_list', 'panel', 'course_list', 'student_list', 'category_list', 'branch_ids', 'current_session','pay_type_list','feeHead','user'));
	}


    public function discount_status(Request $req){
		if(Session::has('activeBranch')){
		    $branch_ids = Session::get('activeBranch');
		}else{ $branch_ids = Auth::user()->branch_id; }

		$current_session=Session::get('activeSession');
		$data['user']=User::select('users.id',DB::raw("CONCAT(users.name,' ( ',roles.display_name,' )') as name"))
      ->leftjoin('roles','users.role_id','=','roles.id')
        ->where(
          [
            ['users.branch_id','=',Session::get('activeBranch')],
            ['role_id','!=',6],
            ['role_id','!=',7]

        ])->pluck('name','id')->toArray();
        $data['user']=array_prepend($data['user'],'--Receipt By--','');
		$data['collection_list']=MiscellaneousCollection::select('collect_fee.*', 'session.session_name', 'students.first_name', 'students.reg_no', 'students.category_id', 'users.name', 'fee_heads.fee_head_title', 'faculties.faculty', 'assign_fee.fee_head_id', 'fee_heads.fee_head_title')
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
		// ->leftjoin('fee_heads as fee','fee.id','=','assign_fee.fee_head_id')
		// ->leftjoin('fee_heads as sub_head','sub_head.id','=','fee.parent_id')
		->where('assign_fee.branch_id', $branch_ids)
		->where('collect_fee.status',1)
		->where('students.status',1)
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
        	if($req->discount_status){
        		 $discount_status=$req->discount_status=='pending'?null:($req->discount_status=='rejected'?'0':($req->discount_status=='approved'?'1':null));
        		// $status=$req->discount_status==3?null:$req->discount_status;
        		$qry->where('collect_fee.discount_status', $discount_status);
        	}
        	if($req->receipt_by){
        		$qry->where('collect_fee.created_by',$req->receipt_by);
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
		->where('collect_fee.discount', '>', '0')
		->orderBy('collect_fee.id','desc')->get();
		
		//->paginate(10);
		$ssn_tble=DB::table('session')->select('id', 'session_name')->where('active_status', '1')->get();
		$current_session=$ssn_tble[0]->id;
		//die("-->".$ssn_tble[0]->id);
		$data['course_list']=Faculty::select('id', 'faculty')->where('branch_id', $branch_ids)->pluck('faculty', 'id')->toArray();
		$data['course_list']=array_prepend($data['course_list'], "Select ".env("course_label"), "");
		$data['category_list']=category_model::select('id', 'category_name')->pluck('category_name', 'id')->toArray();
		$data['category_list']=array_prepend($data['category_list'], "Select Category", "");
		$panel='Collection';
		
		$data['pay_type_list'] = DB::table('payment_type')->select('type_name')->where('status', '1')->pluck('type_name','type_name')->toArray(); 
		//dd($pay_type_list);//, 'id'
		$data['pay_type_list'] = array_prepend($data['pay_type_list'], "Payment Mode", "");
		$data['MiscellaneousHead'] = MiscellaneousHead::select('id', 'fee_head_title')->where('status', 1)->pluck('fee_head_title', 'id')->toArray(); 
		
		$data['MiscellaneousHead'] = array_prepend($data['MiscellaneousHead'], "Select Fee Head", "");

		return view('Fee.discount_status', compact('data', 'panel', 'student_list', 'branch_ids', 'current_session'));
	}
	public function change_discount_status(Request $request){
		$data=[];
		$data['error']=true;
		// Log::debug($request->all());
		if($request->collect_id ){
			$discount=DB::table('collect_fee')->select('discount')->where('id',$request->collect_id)->first();
			$disc_amount=$request->status==0?0:$discount->discount;
			$old_data='amount:'.$discount->discount;
			$comment=$request->comment?$request->comment:'';
			$status=DB::table('collect_fee')->where('id',$request->collect_id)
			->update([
				'discount'=>$disc_amount,
				'discount_status'=>$request->status,
				'approve_reject_at'=>Carbon::now(),
				'approve_reject_by'=>auth()->user()->id,
				'old_discount_data'=>$old_data,
				'discount_comment'=>$comment
			]);
			if ($status) {
				$data['amount'] = $disc_amount;
				$data['error'] = false;
				$data['msg'] = 'Record Updated Successfully';
			}
			else{
				$data['msg'] = 'Something went wrong, please try again.';
			}
		}else{
			$data['msg'] = 'Invalid Request !';
		}
		return response()->json(json_encode($data));
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
			// if(!$req->admission_no){
			// 	if($req->course==0 OR $req->semester==0)
			// 	{
			// 		Session::flash('msG', 'Class & Section field is required!');
			// 		return view('Fee.dueReport', compact('data'));
			// 	}
			// }
			if($courseID!=""){
				$info['courseName'] 	= $courseNameArr[$req->course];
			}else{
				$info['courseName'] 	= "-";
			}
			$search_criteria=[];
			if($req->course){
				$search_criteria[env('course_label')]=Faculty::select('faculty as title')->where('id',$req->course)->first();
				$search_criteria[env('course_label')]=$search_criteria[env('course_label')]->title;
			}
			if($req->semester){
				$search_criteria['Section']=DB::table('semesters')->select('semester as title')->where('id',$req->semester)->first();
				$search_criteria['Section']=$search_criteria['Section']->title;
			}
			if($req->student){
				$search_criteria['Student']=DB::table('students')->select('first_name as title')->where('id',$req->student)->first();
				$search_criteria['Student']=$search_criteria['Student']->title;
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
			if(count($student_session)<1){
				Session::flash('msG', 'No data found!');
					return view('Fee.dueReport', compact('data'));
			}
			// dd($student_session[]);
			$printed_data=$due_tbl=array();
			$printed_data['branch']=Branch::where('id', session('activeBranch'))->get()->toArray(); $k=1;
			foreach($student_session as $stud){

				//,DB::raw("SUM(assign_fee.fee_amount) as amount")
				$assign_qry=AssignFee::select('assign_fee.*',DB::raw('GROUP_CONCAT(assign_fee.id) as assignId'),DB::raw("SUM(assign_fee.fee_amount) as amount"),'fee_heads.fee_head_title')->leftJoin('fee_heads', function($join){
				$join->on('assign_fee.fee_head_id', '=', 'fee_heads.id');
				})
				->where([['course_id', $stud->course_id], ['session_id', $stud->session_id],['assign_fee.status','=',1]])
				->where(function($q)use($req){
					if($req->batch){
						$q->where('assign_fee.batch_id',$req->batch);
					}
				})
				->whereIn('assign_fee.student_id', ['0', $stud->student_id])->get()->toArray();
					$ids = explode(',',$assign_qry[0]['assignId']);
					$collect[$stud->student_id]["paid"]=0;
					$collect[$stud->student_id]["due"]=0;
					$collect[$stud->student_id]["discount"]=0;
					for ($i=0; $i <count($ids) ; $i++) { 
						$collected=DB::table('collect_fee')->select('st.id',DB::raw("SUM(collect_fee.amount_paid) as amount_paid"),DB::raw("SUM(collect_fee.discount) as discount"),'st.first_name','st.reg_no','pd.father_first_name')
						->leftjoin('students as st','st.id','=','collect_fee.student_id')
						->leftjoin('parent_details as pd','pd.students_id','=','collect_fee.student_id')
						->where('student_id',$stud->student_id)
						->where('collect_fee.status','=',1)
						->whereIn('assign_fee_id', [$ids[$i]])
						->get()->toArray();	
						$collect[$stud->student_id]["paid"]=$collected[0]->amount_paid+$collect[$stud->student_id]["paid"];
						$collect[$stud->student_id]["discount"]=$collected[0]->discount+$collect[$stud->student_id]["discount"];
					}
					$stdata[$stud->student_id]=DB::table('students')->select('first_name','reg_no','pd.father_first_name')
					->leftjoin('parent_details as pd','pd.students_id','=','students.id')
					->where('students.id',$stud->student_id)
					->get();
					$collect[$stud->student_id]["total"]=$assign_qry[0]['amount'];
					$collect[$stud->student_id]["due"]=$assign_qry[0]['amount'] - ($collect[$stud->student_id]["paid"] + $collect[$stud->student_id]["discount"]);
					$studId=$stud->student_id;
					$due_tbl[$studId]=$assign_qry[0];
					$student_tbl[$studId]=$stud->first_name;	
			}	
			return view('Fee.duePrint', compact('printed_data', 'due_tbl','collect', 'stdata','student_tbl','info','search_criteria'));
		}else{
			return view('Fee.dueReport', compact('data'));
		}
	}


	public function feeReport(Request $req){
		if(request()->is('feeReportMonthWise')){
			$MonthWise=true;
		}else{
			$MonthWise=false;
		}
		try{
		$data=$student_tbl=[];
		$data['course_list']=Faculty::where('status', '1')->where('branch_id', session('activeBranch'))->pluck('id', 'faculty');
		$data['fee_list']=DB::table('fee_heads')->where('status', '1')->select('id', 'fee_head_title')->get();
		$data['semester_list']=DB::table('semesters')->select('id', 'semester')->get();
		$data['month']=DB::table('months')->select('title','id')->where('status',1)->pluck('title','id')->toArray();
		// Get Course & Section Title
			$courseNameArr = json_decode(json_encode($data['course_list']), true);
			$courseNameArr = array_flip($courseNameArr);
			
			$courseID  = (isset($req->course) && $req->course!=0)? $req->course : "";
		// End
		if($req->all()){
			$search_criteria=[];
			if($req->course){
				$search_criteria[env('course_label')]=Faculty::select('faculty as title')->where('id',$req->course)->first();
				$search_criteria[env('course_label')]=$search_criteria[env('course_label')]->title;
			}
			if($req->semester){
				$search_criteria['Section']=DB::table('semesters')->select('semester as title')->where('id',$req->semester)->first();
				$search_criteria['Section']=$search_criteria['Section']->title;
			}
			if($req->batch){
				$search_criteria['Batch']=DB::table('course_batches')->select('title as title')->where('id',$req->batch)->first();
				$search_criteria['Batch']=$search_criteria['Batch']->title;
			}
			if($req->fee_type){
				foreach ($req->fee_type as $key => $value) {
					$temp=DB::table('fee_heads')->select('fee_head_title as title')->where('id',$value)->first();
					if(isset($search_criteria['Fee Type'])){
						$search_criteria['Fee Type']=$search_criteria['Fee Type'].','.$temp->title;
					}else{
						$search_criteria['Fee Type']=$temp->title;
					}
				}
				
			}
			if($req->due_month){
				foreach ($req->due_month as $key => $value) {
					$temp=DB::table('months')->select('title')->where('id',$value)->first();
					if(isset($search_criteria['Due Month'])){
						$search_criteria['Due Month']=$search_criteria['Due Month'].','.$temp->title;
					}else{
						$search_criteria['Due Month']=$temp->title;
					}
				}
				
			}
			if($req->admission_no){
				$search_criteria['Reg. no']=$req->admission_no;
			}
			if($req->from || $req->to){
				$search_criteria['From']=$req->from;
				$search_criteria['To']=$req->to;
			}
			$info['Error_Message'] = "";
			// if(!$req->admission_no){
			// 	if($req->course==0 OR $req->semester==0)
			// 	{
			// 		Session::flash('msG', 'Faculty & Semester field is required!');
			// 		return view('Fee.feeReport', compact('data'));
			// 	}	
			// }
			
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

				$assign_qry=AssignFee::select('assign_fee.*', 'collect_fee.reciept_no', 'collect_fee.amount_paid', 'collect_fee.discount', 'collect_fee.fine','collect_fee.reciept_date','payment_type','reference','collect_fee.created_by as receipt_by','users.name','collect_fee.reciept_date', 'fee_heads.fee_head_title','parent_details.father_first_name','students.reg_no','students.first_name as student_name','faculties.faculty')->leftjoin('collect_fee', function($join){
				$join->on('assign_fee.id', '=', 'collect_fee.assign_fee_id');
				})->leftJoin('fee_heads', function($join){
				$join->on('assign_fee.fee_head_id', '=', 'fee_heads.id');
				})->leftJoin('users', function($join){
				$join->on('users.id', '=', 'collect_fee.created_by');
				})->leftJoin('parent_details', function($join){
				$join->on('parent_details.students_id', '=', 'collect_fee.student_id');
				})->leftJoin('students', function($join){
				$join->on('students.id', '=', 'collect_fee.student_id');
				})->leftJoin('faculties', function($join){
				$join->on('faculties.id', '=', 'assign_fee.course_id');
				 })
				// ->leftjoin('fee_heads as fee','fee.id','=','assign_fee.fee_head_id')
				// ->leftjoin('fee_heads as sub_head','sub_head.id','=','fee.parent_id')
				->where(function($query) use ($req,$fee_type){
				if($req->fee_type){ 
					if(isset($fee_type)){
						foreach ($fee_type as $key => $value) {
							$query->orWhere('assign_fee.fee_head_id',$value);
						}
					}
				}
				if($req->due_month){ 
					$due_month=$req->due_month;
					if(isset($due_month)){
						foreach ($due_month as $key => $value) {
							$query->orWhere('assign_fee.due_month',$value);
						}
					}
				}
				if($req->from && $req->to){
				$query->whereBetween('collect_fee.reciept_date', array($req->from, $req->to));
				}
				})->where([['collect_fee.amount_paid','!=', 0],['collect_fee.status','=',1],['course_id', $stud->course_id], ['assign_fee.session_id', $stud->session_id], ['collect_fee.student_id', $stud->student_id]])->whereIn('assign_fee.student_id', ['0', $stud->student_id])->orderBy('payment_type','asc')
				->orderBy('reciept_date','asc')
				->where(function($q)use($req){
					if($req->batch){
						$q->where('assign_fee.batch_id',$req->batch);
					}
				})
				->get();

				foreach($assign_qry as $assign_data){
					$studId=$stud->student_id;
					// $studId
					$due_tbl[$assign_data['payment_type']][]=$assign_data;
					$student_tbl[$studId]=$stud->first_name;
				}
				
			}
			// dd($due_tbl);
			return view('Fee.feePrint', compact('printed_data', 'due_tbl', 'student_tbl','info','search_criteria'));
		}else{
			// if($MonthWise)
			// 	return view('Fee.feeReportMonthWise', compact('data'));
			// else	
				return view('Fee.feeReport', compact('data','MonthWise'));
		}
} catch (\Exception $e) { dd($e); }
	}		

public function noDues(Request $req){
	try{
		if(request()->is('noDuesMonthWise')){
			$MonthWise=true;
		}else{
			$MonthWise=false;
		}
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
		$data['month']=DB::table('months')->select('title','id')->where('status',1)->pluck('title','id')->toArray();
		if($req->all()){
			if($req->con){
				$colums['con']=true;
			}if($req->paid){
				$colums['paid']=true;
			}if($req->total){
				$colums['to_pay']=true;
			}
			$printed_data=array();

			$course 	=	$req->course; 
			$semester 	=	$req->semester;
			$fee_type 	=	$req->fee_type;
			$search_criteria=[];
			if($req->course){
				$search_criteria[env('course_label')]=Faculty::select('faculty as title')->where('id',$req->course)->first();
				$search_criteria[env('course_label')]=$search_criteria[env('course_label')]->title;
			}
			if($req->semester){
				$search_criteria['Section']=DB::table('semesters')->select('semester as title')->where('id',$req->semester)->first();
				$search_criteria['Section']=$search_criteria['Section']->title;
			}
			if($req->fee_type){
				foreach ($req->fee_type as $key => $value) {
					$temp=DB::table('fee_heads')->select('fee_head_title as title')->where('id',$value)->first();
					if(isset($search_criteria['Fee Type'])){
						$search_criteria['Fee Type']=$search_criteria['Fee Type'].','.$temp->title;
					}else{
						$search_criteria['Fee Type']=$temp->title;
					}
				}
			}
			if($req->due_month){
				foreach ($req->due_month as $key => $value) {
					$temp=DB::table('months')->select('title')->where('id',$value)->first();
					if(isset($search_criteria['Due Month'])){
						$search_criteria['Due Month']=$search_criteria['Due Month'].','.$temp->title;
					}else{
						$search_criteria['Due Month']=$temp->title;
					}
				}	
			}
			// Get Course & Section Title

			$info['Error_Message'] = "";
			// if($req->course==0 OR $req->semester==0)
			// {
			// 	Session::flash('msG', env('course_label').' & Section field is required!');
			// 	return view('Fee.headwiseReport', compact('data'));
			// }

			$courseNameArr = json_decode(json_encode($data['course_list']), true);
			$courseNameArr = array_flip($courseNameArr);
			if(!empty($req->course))
			$info['courseName'] 	= $courseNameArr[$req->course];

			// End

			$student_session=StudentDetailSessionwise::select('students.branch_id', 'students.first_name', 'students.reg_no', 'student_detail_sessionwise.*','pd.father_first_name as fatherName','sem.semester as sem','ad.mobile_1 as mobile')
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
			->leftjoin('semesters as sem','sem.id','=','student_detail_sessionwise.Semester')
			->leftjoin('addressinfos as ad','ad.students_id','=','student_detail_sessionwise.student_id')
			->orderBy('students.first_name','asc')
			->get();
			 // dd($student_session);
			$printed_data=$due_tbl=array();
			$printed_data['branch']=Branch::where('id', session('activeBranch'))->get()->toArray(); $k=1;
			
			foreach($student_session as $stud){
				$assign_qry=AssignFee::select('assign_fee.*', 'fee_heads.fee_head_title','parent_head.fee_head_title as parent_head','fac.faculty as class')->leftJoin('fee_heads', function($join){
						$join->on('assign_fee.fee_head_id', '=', 'fee_heads.id');
				})->where('assign_fee.status',1)
				
				->leftjoin('fee_heads as parent_head','parent_head.id','=','fee_heads.parent_id')
				->leftjoin('faculties as fac','fac.id','=','assign_fee.course_id')
				->where(function($query) use ($req){
				if($req->fee_type){ 
					if(isset($req->fee_type)){
						foreach ($req->fee_type as $key => $value) {
							$query->orWhere('assign_fee.fee_head_id',$value);
						}
					}
				}
				if($req->due_month){ 
					$due_month=$req->due_month;
					if(isset($due_month)){
						foreach ($due_month as $key => $value) {
							$query->orWhere('assign_fee.due_month',$value);
						}
					}
				}
				})->where([
					['course_id', $stud->course_id],
					 ['session_id', $stud->session_id]])
				->where(function($q)use($req){
					if($req->batch){
						$q->where('assign_fee.batch_id',$req->batch);
					}
				})
				->whereIn('assign_fee.student_id', ['0', $stud->student_id])
				->orderBy('fee_head_title','asc')->get()->toArray();
				// dd($assign_qry);
				if(empty($assign_qry)){
					$assign_qry=MiscellaneousHead::select('child.*', 'fee_heads.fee_head_title','fac.faculty as class')
					->where('child.status',1)
				
					->leftjoin('assign_fee as child','child.fee_head_id','=','fee_heads.id')
					->leftjoin('faculties as fac','fac.id','=','child.course_id')
					->where(function($query) use ($req){
					if($req->fee_type){ 
						if(isset($req->fee_type)){
							foreach ($req->fee_type as $key => $value) {
								$query->orWhere('assign_fee.fee_head_id',$value);
							}
						}
					}
					if($req->due_month){ 
						$due_month=$req->due_month;
						if(isset($due_month)){
							foreach ($due_month as $key => $value) {
								$query->orWhere('assign_fee.due_month',$value);
							}
						}
					}
				})->where([['child.course_id', $stud->course_id], ['child.session_id', $stud->session_id]])->whereIn('child.student_id', ['0', $stud->student_id])->orderBy('fee_head_title','asc')->get()->toArray();
				// dd($assign_qry);
				}
				// dd($stud);
				foreach($assign_qry as $assign_data){
					// dd($assign_data);
					$studId=$stud->student_id;
					$assign_data['fee_head_title'];
					 $fee_name=(isset($assign_data['parent_head'])?$assign_data['parent_head']:$assign_data['fee_head_title']);
					$fee_arr[$fee_name]=$fee_name;

					$collect_qry=MiscellaneousCollection::where([['student_id', $studId],['status','=',1], ['assign_fee_id', $assign_data['id']]])->get();
					if(count($collect_qry)>0){

							foreach($collect_qry as $collect){

								$due_tbl[$studId][$assign_data['id']][$fee_name]['to_pay']=$assign_data['fee_amount'];
								$due_tbl[$studId][$assign_data['id']][$fee_name]['paid'][]=$collect->amount_paid;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['disc'][]=$collect->discount;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['fine'][]=$collect->fine;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['student']=$stud->first_name;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['admission_no']=$stud->reg_no;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['fatherName']=$stud->fatherName;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['class']=$assign_data['class'];
								$due_tbl[$studId][$assign_data['id']][$fee_name]['sem']=$stud->sem;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['mobile']=$stud->mobile;
								
							}

					}else{
								
								$due_tbl[$studId][$assign_data['id']][$fee_name]['to_pay']=$assign_data['fee_amount'];
								$due_tbl[$studId][$assign_data['id']][$fee_name]['paid'][]=0;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['disc'][]=0;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['fine'][]=0;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['student']=$stud->first_name;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['admission_no']=$stud->reg_no;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['fatherName']=$stud->fatherName;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['class']=$assign_data['class'];
								$due_tbl[$studId][$assign_data['id']][$fee_name]['sem']=$stud->sem;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['mobile']=$stud->mobile;
					}

					$student_tbl[$studId]=$stud->first_name;
				}

			} 
			$due=[];
			foreach ($due_tbl as $key => $assign_id_data) {
				foreach ($assign_id_data as $head_id => $assign_head_data) {

					foreach ($assign_head_data as $head_name => $value){

						if(isset($due[$key][$head_name]['to_pay'])){
							$due[$key][$head_name]['to_pay']=$due[$key][$head_name]['to_pay']+$value['to_pay'];
						}else{

							$due[$key][$head_name]['to_pay']=$value['to_pay'];
						}
						$paid=array_sum($value['paid']);
						if(isset($due[$key][$head_name]['paid'])){
							$due[$key][$head_name]['paid']=$due[$key][$head_name]['paid']+$paid;
						}else{
							$due[$key][$head_name]['paid']=$paid;
						}
						$disc=array_sum($value['disc']);
						if(isset($due[$key][$head_name]['disc'])){
							$due[$key][$head_name]['disc']=$due[$key][$head_name]['disc']+$disc;
						}else{
							$due[$key][$head_name]['disc']=$disc;
						}
						$fine=array_sum($value['fine']);
						if(isset($due[$key][$head_name]['fine'])){
							$due[$key][$head_name]['fine']=$due[$key][$head_name]['fine']+$fine;
						}else{
							$due[$key][$head_name]['fine']=$fine;
						}
						$due[$key][$head_name]['student']=$value['student'];
						$due[$key][$head_name]['admission_no']=$value['admission_no'];
						$due[$key][$head_name]['fatherName']=$value['fatherName'];
						$due[$key][$head_name]['class']=$value['class'];
						$due[$key][$head_name]['sem']=$value['sem'];
						$due[$key][$head_name]['mobile']=$value['mobile'];
					}
					
				}
			}

			$due_tbl=[];
			$due_tbl=$due;
			// dd($due);
			return view('Fee.headwisePrint', compact('printed_data', 'due_tbl', 'student_tbl', 'fee_arr','info','colums','search_criteria'));
		}else{
			return view('Fee.headwiseReport', compact('data','MonthWise'));
		}
	} catch (\Exception $e) { dd($e); }
}

	public function edit_collection($id,Request $request){

		if($request->all()){
			$arr['log_status']=1;
			$status=$this->insert_in_log($id,$arr);
			if($status){
				$update=DB::table('miscellaneous_collect_fee')->where('id',$id)->update([
				'updated_at'=>Carbon::now(),
				'amount_paid'=>$request->amount,
				'assign_fee_id'=>$request->assign_fee_id,
				'reference'=>$request->ref_no,
				'payment_type'=>$request->pay_mode,
				'reciept_date'=>$request->date
			]);
				if($update){
					return redirect('/miscellaneous/collection_List')->with('message_success','Updated Successfully');
				}
			}
			else{
				return redirect('/miscellaneous/collection_List')->with('message_warning','Something Went Wrong');
			}
		}
		$data=DB::table('miscellaneous_collect_fee')->select('miscellaneous_collect_fee.*','miscellaneous_assign_fee.fee_head_id','course_id','session_id')->where('miscellaneous_collect_fee.id',$id)
		->leftjoin('miscellaneous_assign_fee','miscellaneous_collect_fee.assign_fee_id','=','miscellaneous_assign_fee.id')
		->first();
		$fee_head=DB::table('miscellaneous_assign_fee')->select('miscellaneous_assign_fee.id as id','miscellaneous_heads.fee_head_title as title')->where([
			['course_id','=',$data->course_id],
			['miscellaneous_assign_fee.status','=',1],
			['session_id','=',$data->session_id],
			['branch_id','=',Session::get('activeBranch')]
		])->where(function($query)use($data){
			$query->where('student_id',0);
			$query->orWhere('student_id',$data->student_id);
		})
		->leftjoin('miscellaneous_heads','miscellaneous_heads.id','=','miscellaneous_assign_fee.fee_head_id')
		->pluck('title','id')->toArray();
		$fee_head=array_prepend($fee_head,'--Select Fee Head--','');
		$pay_type = DB::table('payment_type')->select('type_name')->where('status', '1')->pluck('type_name','type_name')->toArray(); 	
		$pay_type = array_prepend($pay_type, "----Select Payment Mode----", "");

		return view('miscellaneous.fee.collect_fee_edit',compact('data','fee_head','id','pay_type'));
	}
	public function insert_in_log($id,$arr){
		$date=Carbon::now()->format('Y-m-d H:i:s');
		$data=DB::table('miscellaneous_collect_fee')->select('*')->where('id',$id)->first();
		$data->log_created_at=$date;
		$data->log_created_by=auth()->user()->id;
		
		foreach ($data as $key => $value) {
			if($key=='id'){
				$arr['collect_fee_id']=$value;
			}else{
					$arr[$key]=$value;
			}
		
		}
		$insert=DB::table('miscellaneous_collect_fee_log')->insert($arr);
		return $insert;
	}
	public function delete_collection($id){
		$arr['log_status']=2;
		$status=$this->insert_in_log($id,$arr);
		if($status){
			$data=DB::table('miscellaneous_collect_fee')->where('id',$id)->update([
				'status'=>0
			]);
			if($data){
						return redirect('/miscellaneous/collection_List')->with('message_success','Record Deleted');
				}
		}
			else{
				return redirect('/miscellaneous/collection_List')->with('message_warning','Something Went Wrong');
			}
	}
	public function cancel_receipt($id){
		$arr['log_status']=5;
		$receipt_no=DB::table('miscellaneous_collect_fee')->select('reciept_no')->where('id',$id)->first();
		$collections=DB::table('miscellaneous_collect_fee')->select('id')->where('reciept_no',$receipt_no->reciept_no)->get();
		$data=[];
		foreach ($collections as $key => $value) {
			$status=$this->insert_in_log($value->id,$arr);
			if($status){
				$data[]=DB::table('miscellaneous_collect_fee')->where('id',$value->id)->update([
				'status'=>5,
				'reference'=>'Cancelled'
			]);
			}
		}
		
		if(count($data)>0){
				return redirect('/miscellaneous/collection_List')->with('message_success','Receipt '.$receipt_no->reciept_no.' cancelled');
			
		}
		else{
			return redirect('/miscellaneous/collection_List')->with('message_warning','Something went wrong');
		}
	}
	public function deleteAssignFee($id){
		$data['row']=MiscellaneousAssignFee::find($id);
		$data['collect']=DB::table('miscellaneous_collect_fee')->select('*')->where([['assign_fee_id','=',$id],['status','=',1]])->get();
		if(count($data['collect'])>0){
			return redirect()->back()->with('message_danger',"DELETION FAILD! Fees already collected on this head, please delete fee collection and try again.");
		}else{
			$data['row']->update([
				'deleted_at'=>Carbon::now(),
				'deleted_by'=>auth()->user()->id,
				'status'=>0
			]);
			return redirect()->back()->with('message_success',"Assigned Fees Head Deleted.");
		}
	}
	public function noDues_student(Request $req){
	try{
		if(request()->is('noDues_studentMonthWise')){
			$MonthWise=true;
		}else{
			$MonthWise=false;
		}
		$data=$student_tbl=$fee_arr=$due=[];
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
		$data['month']=DB::table('months')->select('title','id')->where('status',1)->pluck('title','id')->toArray();
		if($req->all()){
			if($req->con){
				$colums['con']=true;
			}if($req->paid){
				$colums['paid']=true;
			}if($req->total){
				$colums['to_pay']=true;
			}
			$printed_data=array();

			$course 	=	$req->course; 
			$semester 	=	$req->semester;
			$fee_type 	=	$req->fee_type;
			
			// Get Course & Section Title
			$courseNameArr = json_decode(json_encode($data['course_list']), true);
			$courseNameArr = array_flip($courseNameArr);
			
			$courseID  = (isset($req->course) && $req->course!=0)? $req->course : "";
			$info['Error_Message'] = "";
			// if(!$req->admission_no){
			// 	if($req->course==0 OR $req->semester==0)
			// 	{
			// 		Session::flash('msG', env('course_label').' & Section field is required!');
			// 		return view('Fee.noDue_student_head', compact('data'));
			// 	}
			
			// }
			// dd($req->all());
			$search_criteria=[];
			if($req->course){
				$search_criteria[env('course_label')]=Faculty::select('faculty as title')->where('id',$req->course)->first();
				$search_criteria[env('course_label')]=$search_criteria[env('course_label')]->title;
			}
			if($req->semester){
				$search_criteria['Section']=DB::table('semesters')->select('semester as title')->where('id',$req->semester)->first();
				$search_criteria['Section']=$search_criteria['Section']->title;
			}
			if($req->fee_type){
				foreach ($req->fee_type as $key => $value) {
					$temp_fee_type=DB::table('fee_heads')->select('fee_head_title as title')->where('id',$value)->first();
					if(isset($search_criteria['Fee Type'])){
						$search_criteria['Fee Type']=$search_criteria['Fee Type'].', '.$temp_fee_type->title;
					}else{
						$search_criteria['Fee Type']=$temp_fee_type->title;
						
					}
				}	
			}
			if($req->due_month){
				foreach ($req->due_month as $key => $value) {
					$temp=DB::table('months')->select('title')->where('id',$value)->first();
					if(isset($search_criteria['Due Month'])){
						$search_criteria['Due Month']=$search_criteria['Due Month'].','.$temp->title;
					}else{
						$search_criteria['Due Month']=$temp->title;
					}
				}	
			}
			if($req->admission_no){
				$search_criteria['Reg. no']=$req->admission_no;
			}
			// $courseNameArr = json_decode(json_encode($data['course_list']), true);
			// $courseNameArr = array_flip($courseNameArr);
			// $info['courseName'] 	= $courseNameArr[$req->course];
			if($courseID!=""){
				$info['courseName'] 	= $courseNameArr[$req->course];
			}else{
				$info['courseName'] 	= "-";
			}

			// End

			$student_session=StudentDetailSessionwise::select('students.branch_id', 'students.first_name', 'students.reg_no', 'student_detail_sessionwise.*','pd.father_first_name as fatherName','faculties.faculty','sem.semester','pd.father_mobile_1 as mobile','students.batch_id as batch')
			->leftjoin('faculties','faculties.id','=','student_detail_sessionwise.course_id')
			->leftjoin('semesters as sem','sem.id','=','student_detail_sessionwise.Semester')
			
			->leftjoin('students', function($join){
				$join->on('student_detail_sessionwise.student_id', '=', 'students.id');
				
			})->where(function($query) use ($req){
				if($req->course){ $query->where('student_detail_sessionwise.course_id', $req->course); }
				if($req->semester){ $query->where('student_detail_sessionwise.Semester', $req->semester); }
				if($req->admission_no){
					$query->where('students.reg_no',$req->admission_no);
				}
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
				if(Session::get('isCourseBatch')){
					if($req->admission_no){
						$req->request->add(['batch' => $stud->batch]);
					}
				}
				$assign_qry=AssignFee::select('assign_fee.*', 'fee_heads.fee_head_title')->leftJoin('fee_heads', function($join){
						$join->on('assign_fee.fee_head_id', '=', 'fee_heads.id');
				})->where('assign_fee.status',1)
				->where(function($query) use ($req){
					if($req->fee_type){
						$query->where('assign_fee.fee_head_id', $req->fee_type[0]);
							if(count($req->fee_type)>0){
								for ($i=1; $i <count($req->fee_type) ; $i++) { 			
									$query->orWhere('assign_fee.fee_head_id', $req->fee_type[$i]);
								}	
							}
					}
					if($req->due_month){ 
						$due_month=$req->due_month;
						if(isset($due_month)){
							foreach ($due_month as $key => $value) {
								$query->orWhere('assign_fee.due_month',$value);
							}
						}
					}
				
				})
				->where(function($q)use($req){
					if($req->batch){
						$q->Where('assign_fee.batch_id',$req->batch);
					}
				})
				->where([['course_id', $stud->course_id], ['session_id', $stud->session_id]])->whereIn('assign_fee.student_id', ['0', $stud->student_id])->get()->toArray();

				foreach($assign_qry as $assign_data){
					
					$studId=$stud->student_id;
					$assign_data['fee_head_title'];
					 $fee_name=$assign_data['fee_head_title'];
					$fee_arr[$fee_name]=$fee_name;

					$collect_qry=MiscellaneousCollection::where([['student_id', $studId],['status','=',1], ['assign_fee_id', $assign_data['id']]])->get();
					$info['courseName'] 	= $courseNameArr[$stud->course_id];
					if(count($collect_qry)>0){
							foreach($collect_qry as $collect){
								$due_tbl[$studId][$assign_data['id']][$fee_name]['to_pay']=$assign_data['fee_amount'];
								$due_tbl[$studId][$assign_data['id']][$fee_name]['paid'][]=$collect->amount_paid;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['disc'][]=$collect->discount;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['fine'][]=$collect->fine;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['student']=$stud->first_name;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['admission_no']=$stud->reg_no;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['fatherName']=$stud->fatherName;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['course']=$stud->faculty;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['mobile']=$stud->mobile;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['sec']=$stud->semester;
								
							}

					}else{
								$due_tbl[$studId][$assign_data['id']][$fee_name]['to_pay']=$assign_data['fee_amount'];
								$due_tbl[$studId][$assign_data['id']][$fee_name]['paid'][]=0;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['disc'][]=0;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['fine'][]=0;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['student']=$stud->first_name;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['admission_no']=$stud->reg_no;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['fatherName']=$stud->fatherName;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['course']=$stud->faculty;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['mobile']=$stud->mobile;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['sec']=$stud->semester;
					}

					$student_tbl[$studId]=$stud->first_name;
				}

			} 
			
			foreach ($due_tbl as $key => $assign_id_data) {
				foreach ($assign_id_data as $head_id => $assign_head_data) {

					foreach ($assign_head_data as $head_name => $value){

						if(isset($due[$key][$head_name]['to_pay'])){
							$due[$key][$head_name]['to_pay']=$due[$key][$head_name]['to_pay']+$value['to_pay'];
						}else{
							$due[$key][$head_name]['to_pay']=$value['to_pay'];
						}
						$paid=array_sum($value['paid']);
						if(isset($due[$key][$head_name]['paid'])){
							$due[$key][$head_name]['paid']=$due[$key][$head_name]['paid']+$paid;
						}else{
							$due[$key][$head_name]['paid']=$paid;
						}
						$disc=array_sum($value['disc']);
						if(isset($due[$key][$head_name]['disc'])){
							$due[$key][$head_name]['disc']=$due[$key][$head_name]['disc']+$disc;
						}else{
							$due[$key][$head_name]['disc']=$disc;
						}
						$fine=array_sum($value['fine']);
						if(isset($due[$key][$head_name]['fine'])){
							$due[$key][$head_name]['fine']=$due[$key][$head_name]['fine']+$fine;
						}else{
							$due[$key][$head_name]['fine']=$fine;
						}
						$due[$key][$head_name]['student']=$value['student'];
						$due[$key][$head_name]['admission_no']=$value['admission_no'];
						$due[$key][$head_name]['fatherName']=$value['fatherName'];
						$due[$key][$head_name]['course']=$value['course'];
						$due[$key][$head_name]['sec']=$value['sec'];
						$due[$key][$head_name]['mobile']=$value['mobile'];
					}
					
				}
			}
			$due_tbl=[];
			$due_tbl=$due;
			// dd($due_tbl);
			return view('Fee.student_headwisePrint', compact('printed_data', 'due_tbl', 'student_tbl', 'fee_arr','info','search_criteria','colums'));
		}else{
			return view('Fee.noDue_student_head', compact('data','MonthWise'));
		}
	} catch (\Exception $e) { dd($e); }
}
	public function loadAssignFees(Request $request){
		$response=[];
		$response['error']=true;
		if($request->id){
			$data=DB::table('miscellaneous_assign_fee')->select('miscellaneous_assign_fee.id','miscellaneous_assign_fee.branch_id','miscellaneous_assign_fee.session_id','miscellaneous_assign_fee.course_id','miscellaneous_assign_fee.student_id','fee_head_id','due_month','fee_amount','miscellaneous_collect_fee.id as collect_id','fac.faculty as course','fh.fee_head_title as fee_head','std.first_name as std_name','std.reg_no','mnt.title as month','users.name as created_by',DB::raw("date_format(miscellaneous_assign_fee.created_at,'%d-%M-%Y') as created_at"))
			->leftjoin('faculties as fac','fac.id','=','miscellaneous_assign_fee.course_id')
			->leftjoin('users','users.id','=','miscellaneous_assign_fee.created_by')
			->leftjoin('miscellaneous_heads as fh','fh.id','=','miscellaneous_assign_fee.fee_head_id')
			->leftjoin('months as mnt',function($q){
				$q->on('mnt.id','=','miscellaneous_assign_fee.due_month');
			})
			->leftjoin('students as std',function($q){
				$q->on('std.id','=','miscellaneous_assign_fee.student_id');
			})
			->leftjoin('miscellaneous_collect_fee',function($q){
				$q->on('miscellaneous_collect_fee.assign_fee_id','=','miscellaneous_assign_fee.id')
				->where('miscellaneous_collect_fee.status',1);
			})
			->where([
				['miscellaneous_assign_fee.id','=',$request->id]
			])->get();
			Log::debug($data);
			if(count($data)>0){
				if(empty($data[0]->collect_id)){
					$response['data']=$data;
					$response['error']=false;
				}else{
					$response['msg']='Fees already collected on this head please Delete/Cancel receipt and try again.';
				}
			}else{
				$response['msg']='No Such Assigned Fee.';
			}
		}else{
			$response['msg']='Invalid Request!';
		}
		return response()->json(json_encode($response));
	}
	public function editAssignFees(Request $request){

		$rules=[
			'course_id'=>'required',
			'amount'=>'required',
			'due_month'=>'required'
		];
		$msg=[
			'course_id.required'=>'Course is required.',
			'amount.required'=>'Fees amount is required.',
			'due_month.required'=>'Please select due month.'
		];
		if(Session::get('isCourseBatch')){
			$rules['batch']='required';
			$msg['batch.required']='Please select batch';
		}
		$this->validate($request,$rules,$msg);

		$student_id= ($request->student_id!=null?$request->student_id:0);
		$batch = $request->batch?$request->batch:null;
		$data=MiscellaneousAssignFee::where('id',$request->id)->update([
			'student_id'=>$student_id,
			'course_id'=>$request->course_id,
			'fee_amount'=>$request->amount,
			'updated_by'=>auth()->user()->id,
			'due_month'=>$request->due_month,
			'batch_id' => $batch
		]);
		if($data){
			return redirect()->back()->with('message_success','Assigned Fees Updated');
		}else{
			return redirect()->back()->with('message_warning','Something went wrong, Please try again.');
		}
	}
	public function cancelled_receipts(Request $request){
		$data['course']=Faculty::select('id', 'faculty')->where('branch_id',Session::get('activeBranch'))->pluck('faculty', 'id')->toArray();
		$data['course']=array_prepend($data['course'], "---Select ".env("course_label")."---", "");
		$data['user']=User::select('users.id',DB::raw("CONCAT(users.name,' ( ',roles.display_name,' )') as name"))
      ->leftjoin('roles','users.role_id','=','roles.id')
        ->where(
          [
            ['users.branch_id','=',Session::get('activeBranch')],
            ['role_id','!=',6],
            ['role_id','!=',7]

        ])->pluck('name','id')->toArray();
        $data['user']=array_prepend($data['user'],'---Cancelled By---','');
        $data['pay_type'] = DB::table('payment_type')->select('type_name')->where('status', '1')->pluck('type_name','type_name')->toArray(); 
		$data['pay_type'] = array_prepend($data['pay_type'], "---Select Payment Mode---", "");
		
			$data['receipts']=DB::table('miscellaneous_collect_fee_log')->select('miscellaneous_collect_fee_log.*','st.first_name as student_name','fac.faculty as course','sem.semester as sem','st.reg_no','users.name as cancel_by')
			->leftjoin('miscellaneous_assign_fee as af','af.id','=','miscellaneous_collect_fee_log.assign_fee_id')
			->leftjoin('students as st','st.id','=','miscellaneous_collect_fee_log.student_id')
			->leftjoin('student_detail_sessionwise as stssn',function($j){
				$j->on('stssn.student_id','=','miscellaneous_collect_fee_log.student_id')
				->where([
					['stssn.session_id','=',Session::get('activeSession')]
				]);
			})
			->leftjoin('users','users.id','=','miscellaneous_collect_fee_log.log_created_by')
			->leftjoin('faculties as fac','fac.id','=','stssn.course_id')
			->leftjoin('semesters as sem','sem.id','=','stssn.Semester')
			->where(function($q)use($request){
				if($request->faculty){
					$q->where('af.course_id','=',$request->faculty);
				}
				if($request->start_date && $request->end_date){
					$q->whereBetween('log_created_at',[$request->start_date.' 00:00:00',$request->end_date.' 23:59:59']);
				}else{
					
					if(!(isset($_GET['start_date']) || isset($_GET['end_date']))){
						$q->whereBetween('log_created_at',[Carbon::now()->format('Y-m-d').' 00:00:00',Carbon::now()->format('Y-m-d').' 23:59:59']);
					}
				}
				if($request->pay_type){
					$q->where('collect_fee_log.payment_type','=',$request->pay_type);
				}
				if($request->name){
					$q->where('st.first_name','LIKE','%'.$request->name.'%');
				}
				if($request->reg_no){
					$q->where('st.reg_no','=',$request->reg_no);
				}
				if($request->cancelled_by){
					$q->where('log_created_by','=',$request->cancelled_by);
				}
				if($request->receipt_no){
					$q->where('collect_fee_log.reciept_no','=',$request->receipt_no);
				}
			})
			->where([
				['af.branch_id','=',Session::get('activeBranch')],
				['af.session_id','=',Session::get('activeSession')],
				['log_status','=',5]
			])->get();
		$panel='Cancelled Receipts';
		return view('miscellaneous.fee.cancelled_receipt',compact('data','panel'));
	}
	
	
	public function headwiseTotalReport(Request $req){

		$data['faculty']=Faculty::where('status', '1')->where('branch_id', session('activeBranch'))->pluck('faculty', 'id')->toArray();
		$data['faculty']=array_prepend($data['faculty'],'--Select '.env('course_label').'--','');
		if($req->all()){
			$students=[];
			$branch=Branch::where('id', session('activeBranch'))->first();
			if(count($req->faculty)>0){
				foreach ($req->faculty as $k => $fac) {
					$collection=DB::table('assign_fee')->select('assign_fee.id','fee_amount','assign_fee.student_id','fh.fee_head_title','assign_fee.course_id','fac.faculty')
					->leftJoin('collect_fee as cf',function($q){
						$q->on('assign_fee.id','=','cf.assign_fee_id')
							->Where('cf.status',1);
					})
					->leftjoin('fee_heads as fh','fh.id','=','assign_fee.fee_head_id')
					->leftjoin('faculties as fac','fac.id','=','assign_fee.course_id')
					->where([
						// ['cf.status','=',1],
						['assign_fee.status','=',1],
						['assign_fee.branch_id','=',Session::get('activeBranch')],
						['assign_fee.course_id','=',$fac],
						['assign_fee.session_id','=',Session::get('activeSession')]
					])
					->where(function($q)use($req){
						if($req->batch){
							$q->where('assign_fee.batch_id',$req->batch);
						}
					})
					->groupBy('assign_fee.id')
					->selectRaw('sum(cf.amount_paid) as fee_sum')
					
					->orderBy('assign_fee_id','ASC')
					->get();
					if(count($collection)>0){
						foreach ($collection as $key => $value) {
							$count=DB::table('student_detail_sessionwise')
							->leftjoin('students as std','std.id','=','student_detail_sessionwise.student_id')
							->Where([
								['student_detail_sessionwise.session_id','=',Session::get('activeSession')],
								['student_detail_sessionwise.course_id','=',$value->course_id],
								['std.status','=',1]
							])->count('student_detail_sessionwise.id');
							foreach ($value as $key => $val) {
								$arr[$key]=$val;
							}
							$arr=array_prepend($arr,$count,'student_count');
							$students[$value->faculty][$value->fee_head_title][$value->id]=$arr;
						}	
					}
				}	
			}
			return view('Fee.headwiseTotalReport', compact('students','branch'));
		}else{
			return view('Fee.headwiseTotalReport', compact('data'));
		}
	}
	public function defaulter_list(Request $req){
		try{

		$data=$student_tbl=$fee_arr=$due=[];
		$data['faculty'] = Faculty::where('status', '1')->where('branch_id', session('activeBranch'))->pluck('faculty', 'id')->toArray();
		// $data['faculty'] = array_prepend($data['faculty'],'--Select '.env('course_label').'--','');

		$data['fee_list'] = DB::table('fee_heads')
							->where('status', '1')
							->select('id', 'fee_head_title')
							->pluck('fee_head_title', 'id')
							->toArray();
		// $data['fee_list'] = array_prepend($data['fee_list'],'--Select Head--','');

		$data['semester_list'] = DB::table('semesters')
								->select('id', 'semester')
								->pluck('semester', 'id')
								->toArray();
		// $data['semester_list'] = array_prepend($data['semester_list'],'--Select Semester --','');

		// $data['month']=DB::table('months')->select('title','id')->where('status',1)->pluck('title','id')->toArray();
		if($req->all()){
			$printed_data=array();

			$course 	=	$req->faculty; 
			$semester 	=	$req->semester;
			$fee_type 	=	$req->fee_head;
			
			//Get Course & Section Title
			$courseNameArr = json_decode(json_encode($data['faculty']), true);
			$courseNameArr = array_flip($courseNameArr);
			
			$courseID  = (isset($req->course) && $req->course!=0)? $req->course : "";
			$info['Error_Message'] = "";

			$student_session=StudentDetailSessionwise::select('students.branch_id', 'students.first_name', 'students.reg_no', 'student_detail_sessionwise.*','pd.father_first_name as fatherName','faculties.faculty','sem.semester','ad.mobile_1 as mobile')
			->leftjoin('faculties','faculties.id','=','student_detail_sessionwise.course_id')
			->leftjoin('semesters as sem','sem.id','=','student_detail_sessionwise.Semester')
			
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
			->leftjoin('addressinfos as ad', 'ad.students_id', '=', 'students.id')
			->orderBy('students.first_name','asc')
			->get();
			$printed_data=$due_tbl=array();
			$printed_data['branch']=Branch::where('id', session('activeBranch'))->get()->toArray(); $k=1;
			
			foreach($student_session as $stud){

				$assign_qry=AssignFee::select('assign_fee.*', 'fee_heads.fee_head_title')->leftJoin('fee_heads', function($join){
						$join->on('assign_fee.fee_head_id', '=', 'fee_heads.id');
				})->where('assign_fee.status',1)
				->where(function($query) use ($req){
					if($req->fee_type){
						$query->where('assign_fee.fee_head_id', $req->fee_type[0]);
							if(count($req->fee_type)>0){
								for ($i=1; $i <count($req->fee_type) ; $i++) { 			
									$query->orWhere('assign_fee.fee_head_id', $req->fee_type[$i]);
								}	
							}
					}
					if($req->due_month){ 
						$due_month=$req->due_month;
						if(isset($due_month)){
							foreach ($due_month as $key => $value) {
								$query->orWhere('assign_fee.due_month',$value);
							}
						}
					}
				
				})
				->where(function($q)use($req){
					if($req->batch){
						$q->where('assign_fee.batch_id',$req->batch);
					}
				})
				->where([['course_id', $stud->course_id], ['session_id', $stud->session_id]])->whereIn('assign_fee.student_id', ['0', $stud->student_id])->get()->toArray();

				foreach($assign_qry as $assign_data){
					
					$studId=$stud->student_id;
					$assign_data['fee_head_title'];
					 $fee_name=$assign_data['fee_head_title'];
					$fee_arr[$fee_name]=$fee_name;

					$collect_qry=MiscellaneousCollection::where([['student_id', $studId],['status','=',1], ['assign_fee_id', $assign_data['id']]])->get();
					// $info['courseName'] 	= $courseNameArr[$stud->course_id];
					if(count($collect_qry)>0){
							foreach($collect_qry as $collect){
								$due_tbl[$studId][$assign_data['id']][$fee_name]['to_pay']=$assign_data['fee_amount'];
								$due_tbl[$studId][$assign_data['id']][$fee_name]['paid'][]=$collect->amount_paid;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['disc'][]=$collect->discount;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['fine'][]=$collect->fine;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['student']=$stud->first_name;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['admission_no']=$stud->reg_no;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['fatherName']=$stud->fatherName;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['course']=$stud->faculty;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['mobile']=$stud->mobile;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['sec']=$stud->semester;
								
							}

					}else{
								$due_tbl[$studId][$assign_data['id']][$fee_name]['to_pay']=$assign_data['fee_amount'];
								$due_tbl[$studId][$assign_data['id']][$fee_name]['paid'][]=0;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['disc'][]=0;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['fine'][]=0;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['student']=$stud->first_name;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['admission_no']=$stud->reg_no;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['fatherName']=$stud->fatherName;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['course']=$stud->faculty;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['mobile']=$stud->mobile;
								$due_tbl[$studId][$assign_data['id']][$fee_name]['sec']=$stud->semester;
					}

					$student_tbl[$studId]=$stud->first_name;
				}

			} 
			
			foreach ($due_tbl as $key => $assign_id_data) {
				foreach ($assign_id_data as $head_id => $assign_head_data) {

					foreach ($assign_head_data as $head_name => $value){

						if(isset($due[$key][$head_name]['to_pay'])){
							$due[$key][$head_name]['to_pay']=$due[$key][$head_name]['to_pay']+$value['to_pay'];
						}else{
							$due[$key][$head_name]['to_pay']=$value['to_pay'];
						}
						$paid=array_sum($value['paid']);
						if(isset($due[$key][$head_name]['paid'])){
							$due[$key][$head_name]['paid']=$due[$key][$head_name]['paid']+$paid;
						}else{
							$due[$key][$head_name]['paid']=$paid;
						}
						$disc=array_sum($value['disc']);
						if(isset($due[$key][$head_name]['disc'])){
							$due[$key][$head_name]['disc']=$due[$key][$head_name]['disc']+$disc;
						}else{
							$due[$key][$head_name]['disc']=$disc;
						}
						$fine=array_sum($value['fine']);
						if(isset($due[$key][$head_name]['fine'])){
							$due[$key][$head_name]['fine']=$due[$key][$head_name]['fine']+$fine;
						}else{
							$due[$key][$head_name]['fine']=$fine;
						}
						$due[$key][$head_name]['student']=$value['student'];
						$due[$key][$head_name]['admission_no']=$value['admission_no'];
						$due[$key][$head_name]['fatherName']=$value['fatherName'];
						$due[$key][$head_name]['course']=$value['course'];
						$due[$key][$head_name]['sec']=$value['sec'];
						$due[$key][$head_name]['mobile']=$value['mobile'];
					}
					
				}
			}
			$due_tbl=[];
			$due_tbl=$due;
			$branch=Branch::where('id', session('activeBranch'))->first();
			$due_amount=$req->due_amount;
			return view('Fee.defaulter_list', compact('branch', 'due_tbl','fee_arr','due_amount'));
		}else{
			return view('Fee.defaulter_list', compact('data'));
		}
	} catch (\Exception $e) { dd($e); }
	}
	
	public function defaulter_notification(Request $req){
		if($req->chkIds){
			foreach ($req->chkIds as $key => $value) {
				$data=DB::table('students')->select('ad.mobile_1 as mobile')
				->leftjoin('addressinfos as ad','ad.students_id','=','students.id')
				->where('students.reg_no',$value)
				->first();
				if($data){
					if($req->sms){

					}
					if($req->email){

					}
				}
				
			}
			return redirect()->route('defaulter_list')->with('message_success','Notification Sent Successfully');		
		}else{
			return redirect()->route('defaulter_list')->with('message_danger','No Student Selected');
		}
		
		
	}
}

