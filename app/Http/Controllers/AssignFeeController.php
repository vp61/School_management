<?php
namespace App\Http\Controllers;
use App\Http\Controllers\CollegeBaseController;
use App\Fee_model; 
use App\Session_Model; 
use App\Models\FeeHead;
use Session; 
use App\Models\Faculty;
use App\AssignFee; use DB,Log;
use Illuminate\Http\Request;  
use App\Collection;
use Illuminate\Support\Facades\Validator;
use Auth,URL; use App\category_model;
use App\Branch; use App\Models\StudentDetailSessionwise;
use App\StudentPromotion;
use Carbon\Carbon;
use App\Models\FeeStructure;
use App\Models\Month;
use App\User;

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
		$course_list=array_prepend($course_list, "Select ".env("course_label"), "");
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
	public function new_index(Request $req){
		
		if(Session::has('activeBranch')){
		    $branch_ids = Session::get('activeBranch');
		}else{ $branch_ids = Auth::user()->branch_id; }
		
		$assign_list=Fee_model::select('assign_fee.*','faculties.faculty', 'fee_heads.fee_head_title','session.session_name', 'users.name','cb.title as batch_title','cb.start_date','cb.end_date','st.first_name','pd.father_first_name as father_name')
		->leftJoin('fee_heads', function($join){
				$join->on('fee_heads.id', '=', 'assign_fee.fee_head_id');
		})
		->leftJoin('session', function($join){
			$join->on('session.id', '=', 'assign_fee.session_id');
		
		})->leftJoin('users', function($join){
				$join->on('users.id', '=', 'assign_fee.created_by');
		})->leftJoin('faculties', function($join){
				$join->on('faculties.id', '=', 'assign_fee.course_id');
		})->leftjoin('course_batches as cb',function($j){
			$j->on('cb.id','=','assign_fee.batch_id');
		})
		->leftJoin('students as st','st.id','=','assign_fee.student_id')
		->leftjoin('parent_details as pd','pd.students_id','=','st.id')
		// ->leftjoin('fee_heads as fee','fee.id','=','assign_fee.fee_head_id')
		// ->leftjoin('fee_heads as sub_head','sub_head.id','=','fee.parent_id')
		->where([
			['assign_fee.session_id','=', Session::get('activeSession')],
			['assign_fee.branch_id','=', $branch_ids],
			['assign_fee.status','=',1]
		])->orderBy('assign_fee.course_id','asc')->get();
		// ->paginate(10);
		$head_list=FeeHead::where([
			['status','=',1], 
			['parent_id','=',0]
		])->get();

		foreach ($head_list as $key => $value) {
			$sub_heads[$value->id]=FeeHead::where([
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
		// $months_list=array_prepend($months_list,'--Select Due Month--','');
		$branch_list = Branch::select('branch_name','branch_address','id','org_id')->where("id",$branch_ids)->get();
		
	//	$branch_list = array_prepend($branch_list, "Select Branch", "");

		return view('Fee.new_assign', compact('assign_list', 'panel', 'head_list', 'session_list', 'course_list', 'subj_list', 'student_list', 'branch_list', 'branch_ids', 'current_session','sub_heads','months_list'));
	}
    	public function assign_fee_list(Request $req){
		
		if(Session::has('activeBranch')){
		    $branch_ids = Session::get('activeBranch');
		}else{ $branch_ids = Auth::user()->branch_id; }
		
		
		$student_list=[];
		$assign_list=[];
		
		if($req->all()){
		    
		    $assign_list=Fee_model::select('assign_fee.*','faculties.faculty', 'fee_heads.fee_head_title','session.session_name', 'users.name','cb.title as batch_title','cb.start_date','cb.end_date','st.first_name','pd.father_first_name as father_name')
    		->leftJoin('fee_heads', function($join){
    				$join->on('fee_heads.id', '=', 'assign_fee.fee_head_id');
    		})
    		->leftJoin('session', function($join){
    			$join->on('session.id', '=', 'assign_fee.session_id');
    		
    		})->leftJoin('users', function($join){
    				$join->on('users.id', '=', 'assign_fee.created_by');
    		})->leftJoin('faculties', function($join){
    				$join->on('faculties.id', '=', 'assign_fee.course_id');
    		})->leftjoin('course_batches as cb',function($j){
    			$j->on('cb.id','=','assign_fee.batch_id');
    		})
    		->leftJoin('students as st','st.id','=','assign_fee.student_id')
    		->leftjoin('parent_details as pd','pd.students_id','=','st.id')
    		->where([
    			['assign_fee.session_id','=', Session::get('activeSession')],
    			['assign_fee.branch_id','=', $branch_ids],
    			['assign_fee.status','=',1]
    		])
    		->where(function($q)use($req){
    		    if($req->course){
    		        $q->where('assign_fee.course_id',$req->course);
    		    }
    		    if($req->student_id){
    		        $q->where('assign_fee.student_id',$req->student_id);
    		    }
    		})
    		->orderBy('assign_fee.course_id','asc')->get();
    		
    // 		$req->course=null;
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
		
		$student_list = array_prepend($student_list, "select Student", "");
		$months_list=DB::table('months')->select('title','id')->where('status',1)->pluck('title','id')->toArray();
		// $months_list=array_prepend($months_list,'--Select Due Month--','');
		$branch_list = Branch::select('branch_name','branch_address','id','org_id')->where("id",$branch_ids)->get();
		
	//	$branch_list = array_prepend($branch_list, "Select Branch", "");

		return view('Fee.assign_fee_list', compact('assign_list', 'panel', 'head_list', 'session_list', 'course_list', 'subj_list', 'student_list', 'branch_list', 'branch_ids', 'current_session','sub_heads','months_list'));
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
	public function new_assigned(Request $req){
	//dd($req->all());
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
			
			
			foreach($month_id as $key_head=>$value_head){
				$parent_head_name= FeeHead::where([
						['id','=',$key_head], 
					])->select('fee_head_title')->first();
                   $sub_heads=FeeHead::where([
				    ['status','=',1],
				   ['parent_id','=',$key_head]
			        ])
                  
                   ->pluck('id','fee_head_title')->toArray();
                  //dd($sub_heads);
                 foreach ($value_head as $key_month => $value_month) {
                 	$monthname= $this->getMonthName($value_month);
                 	$m=Carbon::parse($monthname)->format('M');
                 	$mon = $parent_head_name->fee_head_title." ( ".strtoupper($m)." )";

                 	if(array_key_exists($mon,$sub_heads)){

                 	 $sub_head_id= $sub_heads[$mon];
                 	 
                 	}
                 	else{
                 		$sub_head_id= DB::table('fee_heads')->insertGetId([
                          'parent_id'      		 => $key_head,
                          'fee_head_title' 		 => $mon,
                          'slug'           		 => $mon,
                          'created_at'           => $mon,
                          'created_by'           => Auth::user()->id,
                 		]);
                 	}
                 if($sub_head_id && $fee_amount_arr[$key_head]){
                 	$assiGn=new AssignFee;
                 	$assiGn->branch_id = $branch_id;
					$assiGn->fee_head_id = $sub_head_id;
					$assiGn->course_id = $course;
					$assiGn->session_id = $session;	
					$assiGn->batch_id = $batch_id;
					$user_id = Auth::user()->id;
					$assiGn->created_by=$user_id;
					$assiGn->student_id = ($student!="") ? $student:0;	
					$assiGn->fee_amount =$fee_amount_arr[$key_head];
					$assiGn->due_month = $value_month;
					$assiGn->save();
				}
             }
				
					
				

				
			}
			Session::flash('msG', 'Fees Assigned Successfully.');
			return redirect()->route('newAssignList'); 
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
			$assign_list = Fee_model::select('assign_fee.*', 'fee_heads.fee_head_title', 'session.session_name')
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
				Log::debug($assign_list);
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
	public function student_fee_bkp_10Feb2022(Request $req){
		$branch=$req->branch; 
		$session=$req->ssn;
		$course=$req->course;
		$month=[];
		$month=$req->due_month;
		// Log::debug(isset($month));
		$fee_result=AssignFee::Select('assign_fee.*', 'fee_heads.fee_head_title','sub_head.fee_head_title as sub_head')
		->leftJoin('fee_heads', function($join){
			$join->on('fee_heads.id', '=', 'assign_fee.fee_head_id');
		})
		->leftjoin('fee_heads as fee','fee.id','=','assign_fee.fee_head_id')
		->leftjoin('fee_heads as sub_head','sub_head.id','=','fee.parent_id')
		->where('assign_fee.branch_id', $branch)
		->where('assign_fee.session_id', $session)
		->where('assign_fee.course_id', $course)
		->where('assign_fee.status',1)
		->where(function($query)use($month,$req){
			if(isset($month)){
				foreach ($month as $key => $value) {
					$query->orWhere('assign_fee.due_month',$value);
				}
			}
			if($req->batch){
				$query->Where('assign_fee.batch_id',$req->batch);
			}
		})
		->Where('assign_fee.student_id', '0')
		->orWhere(function($q) use ($req,$month){
			if($req->student){
				$q->orWhere('assign_fee.student_id', $req->student)
				->where('assign_fee.branch_id', $req->branch)
				->where('assign_fee.session_id', $req->ssn)
				->where('assign_fee.status',1)
				->where(function($query)use($month,$req){
					if(isset($month)){
						foreach ($month as $key => $value) {
							$query->orWhere('assign_fee.due_month',$value);
						}
					}
					if($req->batch){
						$query->Where('assign_fee.batch_id',$req->batch);
					}	
					})
				->where('assign_fee.course_id', $req->course);
			}
		})->groupBy('assign_fee.id')->orderByRaw("FIELD(assign_fee.due_month, '4','5','6','7','8','9','10','11','12','1','2','3') ASC")->get();
		$ret_val=""; $i=0;
		
if(count($fee_result) && $req->student){
	$ret_val.="<div class=\"row\" style=\" text-align:left; background:#F89406; padding:10px 15px; color:#ffffff;\">
			<div class=\"col-sm-2\"><b>Fee Head</b></div>
			<div class=\"col-sm-1\"></div>
			<div class=\"col-sm-1\"><b>Fees</b></div>
			<div class=\"col-sm-1\" title=\"Total Paid + Total Discount\"><b>Paid</b> <i class=\"fa fa-info-circle \" ></i> </div>
			<div class=\"col-sm-2\"><b>Amount</b></div>
			
			<div class=\"col-sm-1\"><b>Due</b></div>
			<div class=\"col-sm-1\"><b>Discount</b></div>
			<div class=\"col-sm-1\"><b>Type</b></div>
			<div class=\"col-sm-2\" style=\"text-align:center\"><b>Remarks</b></div>
			</div>";
			$total_paid=$total_due=$to_pay=0;
		foreach($fee_result as $fee){
			$paid_result=DB::table('collect_fee')->where('assign_fee_id', $fee->id)->where('student_id', $req->student)
			->Where('status' , 1)->sum('amount_paid');
			Log::debug($req->student);
			$discount=DB::table('collect_fee')->where('assign_fee_id', $fee->id)->where('student_id', $req->student)
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
			<div class=\"col-sm-1\"><input type=\"number\" name=\"discount[]\" $disabled class=\"discount\" placeholder=\"Enter Discount\" style=\"max-width:100%\" onkeyup=\"sum_discount()\" value=\"0\" min=\"0\" step=\"0.01\"/></div>
			<div class=\"col-sm-1\">
			<select name=\"discount_type[]\" $disabled><option value=\"1\">Percent</option><option value=\"2\">Amount</option></select>
				
			</div>
			<div class=\"col-sm-2\">
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
	
	public function student_fee(Request $req){
		$branch=$req->branch; 
		$session=$req->ssn;
		$course=$req->course;
		$month=[];
		$month=$req->due_month;
		
		  
            $student_old_course= DB::table('student_detail_sessionwise as sds')
                ->where('student_id',$req->student)->where('active_status',1)->where('session_id','!=',$session)->pluck('course_id','course_id')->toArray();
           

            
			$old_fee_result=AssignFee::Select('assign_fee.*', 'fee_heads.fee_head_title','sub_head.fee_head_title as sub_head','session.session_name','faculties.faculty')
			->leftJoin('fee_heads', function($join){
				$join->on('fee_heads.id', '=', 'assign_fee.fee_head_id');
			})
			->leftJoin('collect_fee', function($join) use($req){
				$join->on('collect_fee.assign_fee_id', '=', 'assign_fee.id');
				$join->where('collect_fee.status',1);
				$join->where('collect_fee.student_id',$req->student);
			})
		->leftjoin('fee_heads as fee','fee.id','=','assign_fee.fee_head_id')
		->leftjoin('fee_heads as sub_head','sub_head.id','=','fee.parent_id')
		->leftjoin('session','session.id','=','assign_fee.session_id')
		->leftjoin('faculties','faculties.id','=','assign_fee.course_id')
		->where('assign_fee.branch_id', $branch)
		->where('assign_fee.session_id','!=' ,$session)
		->whereIn('assign_fee.course_id', $student_old_course)
		->where('assign_fee.status',1)
		->where(function($query)use($month,$req){
			if(isset($month)){
				foreach ($month as $key => $value) {
					$query->orWhere('assign_fee.due_month',$value);
				}
			}
			if($req->batch){
				$query->Where('assign_fee.batch_id',$req->batch);
			}
		})
		->Where('assign_fee.student_id', '0')
		->selectRAW("sum(collect_fee.amount_paid) as amount_paid")
		->selectRAW("sum(collect_fee.discount) as discount")
		->orWhere(function($q) use ($req,$month,$student_old_course,$session){
			if($req->student){
				$q->orWhere('assign_fee.student_id', $req->student)
				->where('assign_fee.branch_id', $req->branch)
				->where('assign_fee.session_id','!=', $session)
				->where('assign_fee.status',1)
				->where(function($query)use($month,$req){
					if(isset($month)){
						foreach ($month as $key => $value) {
							$query->orWhere('assign_fee.due_month',$value);
						}
					}
					if($req->batch){
						$query->Where('assign_fee.batch_id',$req->batch);
					}	
					})
				->whereIn('assign_fee.course_id', $student_old_course);
			}
		})->groupBy('assign_fee.id')->orderByRaw("FIELD(assign_fee.due_month, '4','5','6','7','8','9','10','11','12','1','2','3') ASC")->get();

        /* To Append Fine End */
		$fee_result=AssignFee::Select('assign_fee.*', 'fee_heads.fee_head_title','sub_head.fee_head_title as sub_head','session.session_name','faculties.faculty','fs.start_date','fs.daily_fine','fs.monthly_fine','fs.on_minimum_due')
		->leftJoin('fee_heads', function($join){
			$join->on('fee_heads.id', '=', 'assign_fee.fee_head_id');
		})
		->leftjoin('fee_heads as fee','fee.id','=','assign_fee.fee_head_id')
		->leftjoin('fee_heads as sub_head','sub_head.id','=','fee.parent_id')
		->leftjoin('session','session.id','=','assign_fee.session_id')
		->leftjoin('faculties','faculties.id','=','assign_fee.course_id')
		->leftjoin('fine_settings as fs',function($j){
			$j->on('fs.due_month_id','=','assign_fee.due_month')
			->whereRaw("fs.fee_head_id = assign_fee.fee_head_id AND fs.faculty_id = assign_fee.course_id AND fs.branch_id = assign_fee.branch_id AND fs.session_id = assign_fee.session_id AND fs.record_status = 1")
			
			;
		})
		->where('assign_fee.branch_id', $branch)
		->where('assign_fee.session_id', $session)
		->where('assign_fee.course_id', $course)
		->where('assign_fee.status',1)
		->where(function($query)use($month,$req){
			if(isset($month)){
				foreach ($month as $key => $value) {
					$query->orWhere('assign_fee.due_month',$value);
				}
			}
			if($req->batch){
				$query->Where('assign_fee.batch_id',$req->batch);
			}
		})
		->Where('assign_fee.student_id', '0')
		->orWhere(function($q) use ($req,$month){
			if($req->student){
				$q->orWhere('assign_fee.student_id', $req->student)
				->where('assign_fee.branch_id', $req->branch)
				->where('assign_fee.session_id', $req->ssn)
				->where('assign_fee.status',1)
				->where(function($query)use($month,$req){
					if(isset($month)){
						foreach ($month as $key => $value) {
							$query->orWhere('assign_fee.due_month',$value);
						}
					}
					if($req->batch){
						$query->Where('assign_fee.batch_id',$req->batch);
					}	
					})
				->where('assign_fee.course_id', $req->course);
			}
		})->groupBy('assign_fee.id')->orderByRaw("FIELD(assign_fee.due_month, '4','5','6','7','8','9','10','11','12','1','2','3') ASC")->get();
   	

		foreach($fee_result as $fee){
			$paid_result=DB::table('collect_fee')->where('assign_fee_id', $fee->id)->where('student_id', $req->student)
					->Where('status' , 1)->sum('amount_paid');
					
					$discount=DB::table('collect_fee')->where('assign_fee_id', $fee->id)->where('student_id', $req->student)
					->Where('status' , 1)->sum('discount');
					$paid_result=$paid_result + $discount;
					$due = $fee->fee_amount - $paid_result;
					
					
					$total_data = 0;
					
					if($fee->on_minimum_due){
					    
						if($due >= $fee->on_minimum_due){
							$total_data = $this->CalculateFine($fee->start_date,$fee->daily_fine,$fee->monthly_fine,$fee->on_minimum_due);
						}
					}
					// dd($fee);

					if($total_data){
						$fine_assigned = DB::table('assign_fee')->updateOrInsert([
							'assign_ref' => $fee->id,
							'student_id' => $req->student,
							'status' => 1,
						],
						[
							'branch_id' =>$fee->branch_id,
							'session_id' =>$fee->session_id,
							'course_id' =>$fee->course_id,
							'fee_head_id' =>env('Late_fee'),
							'fee_amount' =>$total_data,
							'created_at' =>Carbon::now(),
							'due_month' =>$fee->due_month,
							'created_by' =>auth()->user()->id,
						]
						);
						
						
					}
		}
		/* To Append Fine */
		$fee_result=AssignFee::Select('assign_fee.*', 'fee_heads.fee_head_title','sub_head.fee_head_title as sub_head','session.session_name','faculties.faculty')
		->leftJoin('fee_heads', function($join){
			$join->on('fee_heads.id', '=', 'assign_fee.fee_head_id');
		})
		->leftjoin('fee_heads as fee','fee.id','=','assign_fee.fee_head_id')
		->leftjoin('fee_heads as sub_head','sub_head.id','=','fee.parent_id')
		->leftjoin('session','session.id','=','assign_fee.session_id')
		->leftjoin('faculties','faculties.id','=','assign_fee.course_id')
		->where('assign_fee.branch_id', $branch)
		->where('assign_fee.session_id', $session)
		->where('assign_fee.course_id', $course)
		->where('assign_fee.status',1)
		->where(function($query)use($month,$req){
			if(isset($month)){
				foreach ($month as $key => $value) {
					$query->orWhere('assign_fee.due_month',$value);
				}
			}
			if($req->batch){
				$query->Where('assign_fee.batch_id',$req->batch);
			}
		})
		->Where('assign_fee.student_id', '0')
		->orWhere(function($q) use ($req,$month){
			if($req->student){
				$q->orWhere('assign_fee.student_id', $req->student)
				->where('assign_fee.branch_id', $req->branch)
				->where('assign_fee.session_id', $req->ssn)
				->where('assign_fee.status',1)
				->where(function($query)use($month,$req){
					if(isset($month)){
						foreach ($month as $key => $value) {
							$query->orWhere('assign_fee.due_month',$value);
						}
					}
					if($req->batch){
						$query->Where('assign_fee.batch_id',$req->batch);
					}	
					})
				->where('assign_fee.course_id', $req->course);
			}
		})->groupBy('assign_fee.id')->orderByRaw("FIELD(assign_fee.due_month, '4','5','6','7','8','9','10','11','12','1','2','3') ASC")->get();
		
		$ret_val=""; $i=0;
	
		if( (count($fee_result) && $req->student) || (count($old_fee_result) && $req->student) ){
			$ret_val.="<div class=\"row\" style=\" text-align:left; background:#F89406; padding:10px 15px; color:#ffffff;\">
				<div class=\"col-sm-2\"><b>Fee Head</b></div>
				<div class=\"col-sm-1\"></div>
				<div class=\"col-sm-1\"><b>Fees</b></div>
				<div class=\"col-sm-1\" title=\"Total Paid + Total Discount\"><b>Paid</b> <i class=\"fa fa-info-circle \" ></i> </div>
				<div class=\"col-sm-2\"><b>Amount</b></div>
				
				<div class=\"col-sm-1\"><b>Due</b></div>
				<div class=\"col-sm-1\"><b>Discount</b></div>
				<div class=\"col-sm-1\"><b>Type</b></div>
				<div class=\"col-sm-2\" style=\"text-align:center\"><b>Remarks</b></div>
				</div>";
				$total_paid=$total_due=$to_pay=0; $old_to_pay=0;$total_old_due=0;$old_total_paid=0;

				foreach($old_fee_result as $old_fee){
					$old_paid= $old_fee->amount_paid+ $old_fee->discount;
					$old_due = $old_fee->fee_amount - $old_paid;
					
					if($old_due>0){
	                    $old_total_paid+=$old_paid;
		                $old_to_pay+=$old_fee->fee_amount;
		                $total_old_due+= $old_due;
						$ret_val.="<div class=\"row\" style=\"margin:5px 0px; text-align:left;border-bottom:1px dashed lightgrey;padding-bottom:5px;background: #ffd6c7fc;\">
							<input type=\"hidden\" name=\"assign_id[]\" value=\"".$old_fee->id."\">
							
							<div class=\"col-sm-2\">
							<b>".++$i."): ".$old_fee->fee_head_title.' <br>'.$old_fee->faculty.' / '.$old_fee->session_name."</div><div class=\"col-sm-1\">
							<input type=\"hidden\" value=\"".$old_fee->fee_head_id."\" name=\"fee_head_id[]\">
							<b>".$old_fee->times." </b></div>
							<div class=\"col-sm-1\"><b><i class=\"fa fa-inr\"></i> ".$old_fee->fee_amount."</b></div>
							<div class=\"col-sm-1\"><b><i class=\"fa fa-inr\"></i> ".$old_paid."</b></div>
							<div class=\"col-sm-2\"><b>";
							$ret_val.="<input type=\"number\" class=\"amount\" placeholder=\"Enter Amount\" name=\"amount[]\" max=\"".$old_due."\" value=\"0\" onkeyup=\"sum_amount()\" required>";
						$ret_val.="</b></div>
							<div class=\"col-sm-1\"><b><i class=\"fa fa-inr\"></i> $old_due
							<input type=\"hidden\" name=\"due_amount[]\" value=\"".$old_due."\">
							</b></div>
							<div class=\"col-sm-1\"><input type=\"number\" name=\"discount[]\"  class=\"discount\" placeholder=\"Enter Discount\" style=\"max-width:100%\" onkeyup=\"sum_discount()\" value=\"0\" min=\"0\" step=\"0.01\"/></div>
							<div class=\"col-sm-1\">
							<select name=\"discount_type[]\" id=\"discount_type\"><option value=\"1\">Percent</option><option value=\"2\">Amount</option></select>
								
							</div>
							<div class=\"col-sm-2\">
								<input type=\"text\" placeholder=\"Enter Remarks\" name=\"remark[]\" class=\"pull-right\">
							</div>

							</div>";
					}
				}
				
				foreach($fee_result as $fee){
					$paid_result=DB::table('collect_fee')->where('assign_fee_id', $fee->id)->where('student_id', $req->student)
					->Where('status' , 1)->sum('amount_paid');
					
					$discount=DB::table('collect_fee')->where('assign_fee_id', $fee->id)->where('student_id', $req->student)
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
		 
					<b>".++$i."): ".$fee->fee_head_title.'<br>'.$fee->faculty.' / '.$fee->session_name."</div><div class=\"col-sm-1\">
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
					<div class=\"col-sm-1\"><input type=\"number\" name=\"discount[]\" $disabled class=\"discount\" placeholder=\"Enter Discount\" style=\"max-width:100%\" onkeyup=\"sum_discount()\" value=\"0\" min=\"0\" step=\"0.01\"/></div>
					<div class=\"col-sm-1\">
					<select name=\"discount_type[]\"  class=\"discount_type\"  $disabled><option value=\"1\">Percent</option><option value=\"2\">Amount</option></select>
						
					</div>
					<div class=\"col-sm-2\">
						<input type=\"text\" $disabled placeholder=\"Enter Remarks\" name=\"remark[]\" class=\"pull-right\">
					</div>

					</div>";
				}
					$overall_to_pay= $old_to_pay+$to_pay;
					$overall_due= $total_due+$total_old_due;
					$overall_paid= $old_total_paid+$total_paid;
					$ret_val.="<div class=\"row\" style=\" text-align:left; background:#f4ddbf; padding:10px 15px; color:#000;\" ><div class=\"col-sm-3\">
						 <b>Total</b>
						</div>
				 		<div class=\"col-sm-1\">
				 			<b><i class=\"fa fa-inr\"></i> ".$overall_to_pay."</b>
				 		</div>
				 		<div class=\"col-sm-1\">
				 			<b><i class=\"fa fa-inr\"></i> ".$overall_paid."</b>
				 		</div>
				 		<div class=\"col-sm-2\"><b><i class=\"fa fa-inr\"></i> <b id=\"total_amount\">0</b></b>
				 		</div>
				 		<div class=\"col-sm-1\">
				 			<b><i class=\"fa fa-inr\"></i> ".$overall_due."</b>
				 		</div>
				 		<div class=\"col-sm-2\"><b><i class=\"fa fa-inr\"></i> <b id=\"total_discount\">0</b></b>
				 		</div></div>";
			
		}else{ 
			$ret_val="<div class=\"alert alert-danger\"><h3>No Fees Assigned</h3></div>"; 
		}
		return response($ret_val);
	}
    
    public function CalculateFine($start_date,$daily_fine,$monthly_fine,$on_minimum_due){
    	// $current_date = new Carbon('2023-03-03');
    	$current_date = Carbon::now();
    	$temp_start_day = Carbon::parse($start_date)->format('d');
    	$first_fine_date = (new Carbon($start_date))->addDays(1);

    	
    	$d_fine = 0;
    	$m_fine = 0;
    	$last_month = new Carbon($first_fine_date);
    	$i = 1;
    	while($first_fine_date <= $current_date){
    		


    		if($last_month->diffInDays($first_fine_date) >= $last_month->daysInMonth){
    			$d_fine = 0;
    			$m_fine += $monthly_fine;
    			$last_month = new Carbon($first_fine_date);
    		}else{
    			$d_fine += $daily_fine;
    		}
    		$first_fine_date->addDays(1);
    	}
    	
    	$total_fine = $d_fine + $m_fine;
    	
    	return $total_fine;

    }
	public function student_fee_history(Request $req)

	{
		$branch=$req->branch; 
		$session=$req->ssn; 
		$course=$req->course;
		$feeHistory_data = DB::table('collect_fee')->Select('collect_fee.id','collect_fee.status','collect_fee.student_id','collect_fee.discount', 'collect_fee.reciept_date','collect_fee.assign_fee_id','collect_fee.amount_paid','collect_fee.reciept_no','collect_fee.payment_type','collect_fee.status','collect_fee.discount','sd.first_name','sd.branch_id','sd.reg_no', 'sd.reg_date', 'sd.university_reg','sd.date_of_birth', 'sd.gender','asf.fee_amount','asf.course_id','asf.fee_head_id','fd.fee_head_title','br.branch_name','br.branch_logo','br.branch_mobile','br.branch_email','br.branch_address', 'fac.faculty')
		        ->where('collect_fee.student_id','=',$req->student)
		        ->where('collect_fee.status','=',1)
		        //->Where('asf.student_id', $req->student)
                ->where('asf.branch_id', $branch)
				->where('asf.session_id', $session)
				->where(function($q)use($req){
					if($req->batch){
						$q->where('asf.batch_id',$req->batch);
					}
				})
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
		// dd($course_list);//, 'id'
		$pay_type_list = array_prepend($pay_type_list, "----Select Payment Mode----", "");
		$student_list = array(); //DB::table('students')->select('id', 'first_name')->where('branch_id', $branch_ids)->pluck('first_name', 'id')->toArray();
		$student_list = array_prepend($student_list, "select Student", "");
		if($req->has('submit')){
			
			$amount_paid=$req->amount; $l=0; $assign_id=$req->assign_id; $fee_head_id_arr=$req->fee_head_id;
			$branch=$req->branch; $session=$req->ssn; 
			$course=$req->course;
			$disc=$req->discount; $remarks=$req->remark;


			$disc_type = $req->discount_type;
			// $reciept_no=$this->reciept_no();
			// dd($req->all());

			$checkStatus = 0;
			foreach($amount_paid as $amnt){
				$data=array();
				$data['student_id']=$req->student;
				//$data['session']=$req->session;	
				$data['assign_fee_id']=$assign_id[$l];

                if($assign_id[$l] && ($amnt>0 || $disc[$l]>0) && $amnt !="'" && $req->student && $req->session){
                    $disc_data['discount'] = $disc[$l];
					$disc_data['discount_type'] = 2;
					$disc_data['discount_value'] = $disc[$l];
                	if(isset($disc[$l])){
                		if($disc[$l] > 0){
                			if(isset($disc_type[$l])){
                				// if($disc_type[$l] == 1){
                				$assigned_data = DB::table('assign_fee')->select('fee_amount')->where('id',$assign_id[$l])->first();
                				if($assigned_data){

                					$disc_data = $this->getDiscountAmount($disc_type[$l],$assigned_data->fee_amount,$disc[$l]);
                				}
                				// }
                			}
                		}
                	}
                	// dd($disc_data);
                    $checkStatus = 1;
                	$data['amount_paid']=$amnt;
                	$data['discount']=$disc_data['discount'];
                	$data['discount_type']=$disc_data['discount_type'];
                	$data['discount_value']=$disc_data['discount_value'];
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

                	// dd($disc_data,$data);
                	$receipt_id[]=DB::table('collect_fee')->insertGetId($data);
                }	
				$l++;
			}
			if(!isset($receipt_id)){
				return redirect('collect_fee')->with('message_danger','Please enter Amount/Discount more than 0.');	
			}
			$payment_count=count($receipt_id);
            $collect_id=$receipt_id[$payment_count-1];
            // $receipt_no=$this->reciept_no($collect_id);
            $receipt_no=$this->reciept_no($collect_id,$receipt_id);
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
			return view('Fee.collect', compact('panel', 'session_list', 'course_list', 'student_list', 'branch_list', 'branch_ids', 'pay_type_list', 'current_session','months_list'));
		}
	}
	public function getDiscountAmount($disc_type,$amount,$disc){
		$data['discount'] = 0;
		$data['discount_type'] = 0;
		$data['discount_value'] = 0;

		if($disc_type == 1){
			$data['discount'] =	($amount/100) * $disc;
			$data['discount_type'] =	$disc_type;
			$data['discount_value'] =	$disc;
		}else{
			$data['discount'] =	$disc;
			$data['discount_type'] =	$disc_type;
			$data['discount_value'] =	$disc;
		}


		return $data;
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
		$collection_list=Collection::select('collect_fee.*','pd.father_first_name as father_name', 'session.session_name', 'students.first_name', 'students.reg_no', 'students.category_id', 'users.name', 'fee_heads.fee_head_title', 'faculties.faculty', 'assign_fee.fee_head_id', 'fee_heads.fee_head_title')
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
		->leftJoin('parent_details as pd','pd.students_id','=','collect_fee.student_id')
		// ->leftjoin('fee_heads as fee','fee.id','=','assign_fee.fee_head_id')
		// ->leftjoin('fee_heads as sub_head','sub_head.id','=','fee.parent_id')
		->where('assign_fee.branch_id', $branch_ids)
		->where('collect_fee.status',1)
		->where('students.status',1)
// 		->where('assign_fee.session_id', $current_session)

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
        		$qry->orWhere('collect_fee.reciept_no','like','%'.$req->ref_no.'%');
        	}
        	if($req->fee_head){
        		$qry->where('assign_fee.fee_head_id', 'like', '%'.$req->fee_head.'%'); 
        	}
        	if($req->session_id){
        		$qry->where('assign_fee.session_id', '=',$req->session_id); 
        	}
		})
		->where('collect_fee.status', '=', '1')
		->selectRAW("sum(amount_paid) as amount_paid")
		->selectRAW("sum(discount) as discount")
		->groupBy('collect_fee.reciept_no')
		->orderBy('collect_fee.id','desc')->get();
		
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
		$feeHead = FeeHead::select('id', 'fee_head_title')->where('status', 1)->pluck('fee_head_title', 'id')->toArray(); 
		
		$feeHead = array_prepend($feeHead, "Select Fee Head", "");
        
        $session_list = DB::table('session')->select('id','session_name')->where('status',1)->pluck('session_name','id')->toArray();
		$session_list = array_prepend($session_list, "--Select Session--", "");
		 
		return view('Fee.collectionList', compact('collection_list','session_list', 'panel', 'course_list', 'student_list', 'category_list', 'branch_ids', 'current_session','pay_type_list','feeHead','user'));
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
		$data['collection_list']=Collection::select('collect_fee.*', 'session.session_name', 'students.first_name', 'students.reg_no', 'students.category_id', 'users.name', 'fee_heads.fee_head_title', 'faculties.faculty', 'assign_fee.fee_head_id', 'fee_heads.fee_head_title')
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
		$data['feeHead'] = FeeHead::select('id', 'fee_head_title')->where('status', 1)->pluck('fee_head_title', 'id')->toArray(); 
		
		$data['feeHead'] = array_prepend($data['feeHead'], "Select Fee Head", "");

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
				//->where('assign_fee.status',1)
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
					$stdata[$stud->student_id]=DB::table('students')->select('first_name','reg_no','pd.father_first_name','fac.faculty as course_name','sem.semester')
					->leftjoin('parent_details as pd','pd.students_id','=','students.id')
					->leftJoin('student_detail_sessionwise as sts','sts.student_id','=','students.id')
					->leftJoin('faculties as fac','fac.id','=','sts.course_id')
					->leftJoin('semesters as sem','sem.id','=','sts.Semester')
					->where('students.id',$stud->student_id)
					->where('sts.session_id',Session::get('activeSession'))
					->where('sts.active_status',1)
					->get();
					$collect[$stud->student_id]["total"]=$assign_qry[0]['amount'];
					$collect[$stud->student_id]["due"]=$assign_qry[0]['amount'] - ($collect[$stud->student_id]["paid"] + $collect[$stud->student_id]["discount"]);
					$studId=$stud->student_id;
					$due_tbl[$studId]=$assign_qry[0];
					$student_tbl[$studId]=$stud->first_name;	
			}	
			
			$type = $req->report_type;
			return view('Fee.duePrint', compact('printed_data', 'due_tbl','collect', 'stdata','student_tbl','info','search_criteria','type'));
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
		
		$data['months_list']=DB::table('months')->select('title','id')->where('status',1)->pluck('title','id')->toArray();
		$data['student_list'] = array();
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
			if($req->student){
				$search_criteria['Student']=DB::table('students')->select('first_name as title')->where('id',$req->student)->first();
				$search_criteria['Student']=$search_criteria['Student']->title;
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
					
					if($req->student){
						$query->where('students.id', $req->student);
					}
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
				if($req->student){
					$query->where('collect_fee.student_id', $req->student);
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
			 //dd($due_tbl);
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
					$assign_qry=FeeHead::select('child.*', 'fee_heads.fee_head_title','fac.faculty as class')
					->where('child.status',1)
				
					->leftjoin('assign_fee as child','child.fee_head_id','=','fee_heads.id')
					->leftjoin('faculties as fac','fac.id','=','child.course_id')
					->where(function($query) use ($req){
					if($req->fee_type){ 
						if(isset($req->fee_type)){
							foreach ($req->fee_type as $key => $value) {
								$query->orWhere('child.fee_head_id',$value);
							}
						}
					}
					if($req->due_month){ 
						$due_month=$req->due_month;
						if(isset($due_month)){
							foreach ($due_month as $key => $value) {
								$query->orWhere('child.due_month',$value);
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

					$collect_qry=Collection::where([['student_id', $studId],['status','=',1], ['assign_fee_id', $assign_data['id']]])->get();
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
			$list = DB::table('collect_fee')
			->where('reciept_no',$id)
			->get();

			foreach($list as $k => $v){

				$status=$this->insert_in_log($v->id,$arr);
			}

			if($status){
				$update=DB::table('collect_fee')->where('reciept_no',$id)->update([
				'updated_at'=>Carbon::now(),
				// 'amount_paid'=>$request->amount,
				// 'assign_fee_id'=>$request->assign_fee_id,
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
		$data=DB::table('collect_fee')->select('collect_fee.*','assign_fee.fee_head_id','course_id','session_id')->where('collect_fee.reciept_no',$id)
		->leftjoin('assign_fee','collect_fee.assign_fee_id','=','assign_fee.id')
		->first();
		// dd($data);

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
		
		$data = DB::table('collect_fee')->select('id')->where('reciept_no',$id)->get();
		$chk = 0;
		foreach($data as $v){
		    $status=$this->insert_in_log($v->id,$arr);
		    $ret = DB::table('collect_fee')->where('id',$v->id)->update([
				'status'=>0
			]);
			$chk = 1;
		}
		
		if($chk){
			return redirect('/collection_List')->with('message_success','Record Deleted');
		}
		else{
			return redirect('/collection_List')->with('message_warning','Something Went Wrong');
		}
	}
	public function cancel_receipt($id){
		$arr['log_status']=5;
// 		$receipt_no=DB::table('collect_fee')->select('reciept_no')->where('id',$id)->first();
// 		dd($receipt_no);
		$collections=DB::table('collect_fee')->select('id')->where('reciept_no',$id)->get();
		$data=[];
		foreach ($collections as $key => $value) {
			$status=$this->insert_in_log($value->id,$arr);
			if($status){
				$data[]=DB::table('collect_fee')->where('id',$value->id)->update([
				'status'=>5,
				'reference'=>'Cancelled'
			]);
			}
		}
		
		if(count($data)>0){
				return redirect('/collection_List')->with('message_success','Receipt '.$id.' cancelled');
			
		}
		else{
			return redirect('/collection_List')->with('message_warning','Something went wrong');
		}
	}
	public function deleteAssignFee($id){
		$data['row']=AssignFee::find($id);
		$data['collect']=DB::table('collect_fee')->select('*')->where([['assign_fee_id','=',$id],['status','=',1]])->get();
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
// 			dd($info['courseName']);

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

					$collect_qry=Collection::where([['student_id', $studId],['status','=',1], ['assign_fee_id', $assign_data['id']]])->get();
				// 	$info['courseName'] 	= $courseNameArr[$stud->course_id];
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
// 			dd($search_criteria);
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
			$data=DB::table('assign_fee')->select('assign_fee.id','assign_fee.branch_id','assign_fee.session_id','assign_fee.course_id','assign_fee.student_id','fee_head_id','due_month','fee_amount','collect_fee.id as collect_id','fac.faculty as course','fh.fee_head_title as fee_head','std.first_name as std_name','std.reg_no','mnt.title as month','users.name as created_by',DB::raw("date_format(assign_fee.created_at,'%d-%M-%Y') as created_at"))
			->leftjoin('faculties as fac','fac.id','=','assign_fee.course_id')
			->leftjoin('users','users.id','=','assign_fee.created_by')
			->leftjoin('fee_heads as fh','fh.id','=','assign_fee.fee_head_id')
			->leftjoin('months as mnt',function($q){
				$q->on('mnt.id','=','assign_fee.due_month');
			})
			->leftjoin('students as std',function($q){
				$q->on('std.id','=','assign_fee.student_id');
			})
			->leftjoin('collect_fee',function($q){
				$q->on('collect_fee.assign_fee_id','=','assign_fee.id')
				->where('collect_fee.status',1);
			})
			->where([
				['assign_fee.id','=',$request->id]
			])->get();
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
		$data=AssignFee::where('id',$request->id)->update([
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
		
			$data['receipts']=DB::table('collect_fee_log')->select('collect_fee_log.*','st.first_name as student_name','fac.faculty as course','sem.semester as sem','st.reg_no','users.name as cancel_by')
			->leftjoin('assign_fee as af','af.id','=','collect_fee_log.assign_fee_id')
			->leftjoin('students as st','st.id','=','collect_fee_log.student_id')
			->leftjoin('student_detail_sessionwise as stssn',function($j){
				$j->on('stssn.student_id','=','collect_fee_log.student_id')
				->where([
					['stssn.session_id','=',Session::get('activeSession')]
				]);
			})
			->leftjoin('users','users.id','=','collect_fee_log.log_created_by')
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
		return view('Fee.cancelled_receipt',compact('data','panel'));
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
					->selectRaw('(COALESCE(sum(cf.amount_paid),0)) as fee_sum')
					->selectRaw('(COALESCE(sum(cf.discount),0)) as discount')
				// 	->selectRaw('sum(cf.amount_paid) as fee_sum')
					
					->orderBy('assign_fee_id','ASC')
					->get();
					
					if(count($collection)>0){
						foreach ($collection as $key => $value) {
							$count=DB::table('student_detail_sessionwise')
							->leftjoin('students as std','std.id','=','student_detail_sessionwise.student_id')
							->Where([
								['student_detail_sessionwise.session_id','=',Session::get('activeSession')],
								['student_detail_sessionwise.course_id','=',$value->course_id],
								['std.status','=',1],
								['student_detail_sessionwise.active_status','=',1],
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
// 			dd($students);
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

					$collect_qry=Collection::where([['student_id', $studId],['status','=',1], ['assign_fee_id', $assign_data['id']]])->get();
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

	/*  start bulk collect fee code */

   public function bulk_student_fee_10Feb2022(Request $req){
   	     
		$branch=$req->branch; 
		$session=$req->ssn;
		$course=$req->course;
		$month=[];
		$month=$req->due_month;
		// Log::debug(isset($month));
		$fee_result=AssignFee::Select('assign_fee.*', 'fee_heads.fee_head_title','sub_head.fee_head_title as sub_head','assign_fee.id as assign_fee_id')
		->leftJoin('fee_heads', function($join){
			$join->on('fee_heads.id', '=', 'assign_fee.fee_head_id');
		})
		->leftjoin('fee_heads as fee','fee.id','=','assign_fee.fee_head_id')
		->leftjoin('fee_heads as sub_head','sub_head.id','=','fee.parent_id')
		->where('assign_fee.branch_id', $branch)
		->where('assign_fee.session_id', $session)
		->where('assign_fee.course_id', $course)
		->where('assign_fee.status',1)
		->where(function($query)use($month,$req){
			if(isset($month)){
				foreach ($month as $key => $value) {
					$query->orWhere('assign_fee.due_month',$value);
				}
			}
			if($req->batch){
				$query->Where('assign_fee.batch_id',$req->batch);
			}
		})
		->Where('assign_fee.student_id', '0')
		->orWhere(function($q) use ($req,$month){
			if($req->student){
				$q->orWhere('assign_fee.student_id', $req->student)
				->where('assign_fee.branch_id', $req->branch)
				->where('assign_fee.session_id', $req->ssn)
				->where('assign_fee.status',1)
				->where(function($query)use($month,$req){
					if(isset($month)){
						foreach ($month as $key => $value) {
							$query->orWhere('assign_fee.due_month',$value);
						}
					}
					if($req->batch){
						$query->Where('assign_fee.batch_id',$req->batch);
					}	
					})
				->where('assign_fee.course_id', $req->course);
			}
		})->groupBy('assign_fee.id')->orderByRaw("FIELD(assign_fee.due_month, '4','5','6','7','8','9','10','11','12','1','2','3') ASC")->get();
		$ret_val=""; $i=0;
		
    if(count($fee_result) && $req->student){
	$ret_val.="<div class=\"row\" style=\" text-align:left; background:#F89406; padding:10px 15px; color:#ffffff;\">
			<div class=\"col-sm-2\"><input type=\"checkbox\" id=\"checkAll\"></div>
			<div class=\"col-sm-4\"><b>Fee Head</b></div>
			<div class=\"col-sm-2\"><b>Fees</b></div>
			<div class=\"col-sm-2\" title=\"Total Paid + Total Discount\"><b>Paid</b> <i class=\"fa fa-info-circle \" ></i> </div>
			
			
			<div class=\"col-sm-2\"><b>Due</b></div>
			
			
			</div>";
			$total_paid=$total_due=$to_pay=0;
		foreach($fee_result as $fee){
			// dd($fee);
			$paid_result=DB::table('collect_fee')->where('assign_fee_id', $fee->id)->where('student_id', $req->student)
			->Where('status' , 1)->sum('amount_paid');
			
			$discount=DB::table('collect_fee')->where('assign_fee_id', $fee->id)->where('student_id', $req->student)
			->Where('status' , 1)->sum('discount');
			$paid_result=$paid_result + $discount;
			$due = $fee->fee_amount - $paid_result;
			$total_paid=$total_paid+$paid_result;
			$total_due=$total_due+$due;
			$to_pay=$to_pay+$fee->fee_amount;
			$disabled = ($due == 0) ? " style=\"display:none;\"":"";
			$ret_val.="<div class=\"row\" style=\"margin:5px 0px; text-align:left;border-bottom:1px dashed lightgrey;padding-bottom:5px;\">
			<div class=\"col-sm-2\">
			 <input type=\"checkbox\" name=\"assign_id[".$fee->assign_fee_id."]\" value=\"".$fee->id."\" checked></div>
			
			<div class=\"col-sm-4\">
 
           <b>".$fee->fee_head_title."
             <input type=\"hidden\" value=\"".$fee->fee_head_id."\" name=\"fee_head_id[".$fee->assign_fee_id."]\">
            <b>".$fee->times." </b></div>
			<div class=\"col-sm-2\"><b><i class=\"fa fa-inr\"></i> ".$fee->fee_amount."</b></div>
			<div class=\"col-sm-2\"><b><i class=\"fa fa-inr\"></i> ".$paid_result."</b></div>
			
			<div class=\"col-sm-2\"><b><i class=\"fa fa-inr\"></i> $due
             <input type=\"hidden\" name=\"due_amount[".$fee->assign_fee_id."]\" value=\"".$due."\">
			</b></div>
			
			

			</div>";
		}
		$ret_val.="<div class=\"row\" style=\" text-align:left; background:#f4ddbf; padding:10px 15px; color:#000;\" ><div class=\"col-sm-2 col-sm-offset-4\">
					 <b>Total</b>
					</div>
			 		<div class=\"col-sm-2\">
			 			<b><i class=\"fa fa-inr\"></i> ".$to_pay."</b>
			 		</div>
			 		<div class=\"col-sm-2\">
			 			<b><i class=\"fa fa-inr\"></i> ".$total_paid."</b>
			 		</div>
			 		
			 		<div class=\"col-sm-2\">
			 			<b><i class=\"fa fa-inr\"></i> ".$total_due."</b>
			 		</div>
			 		</div>";
		
                }else{ $ret_val="<div class=\"alert alert-danger\"><h3>No Fees Assigned</h3></div>"; }
		        return response($ret_val);
	}
    public function bulk_student_fee(Request $req){
   	     
		$branch=$req->branch; 
		$session=$req->ssn;
		$course=$req->course;
		$month=[];
		$month=$req->due_month;
		// Log::debug(isset($month));
		 $student_old_course= DB::table('student_detail_sessionwise as sds')
                ->where('student_id',$req->student)->where('active_status',1)->where('session_id','!=',$session)->pluck('course_id','course_id')->toArray();
            // dd($student_old_course,$course);
			$old_fee_result=AssignFee::Select('assign_fee.*', 'fee_heads.fee_head_title','sub_head.fee_head_title as sub_head','session.session_name','faculties.faculty')
			->leftJoin('fee_heads', function($join){
				$join->on('fee_heads.id', '=', 'assign_fee.fee_head_id');
			})
			->leftJoin('collect_fee', function($join) use($req){
				$join->on('collect_fee.assign_fee_id', '=', 'assign_fee.id');
				$join->where('collect_fee.status',1);
				$join->where('collect_fee.student_id',$req->student);
			})
		->leftjoin('fee_heads as fee','fee.id','=','assign_fee.fee_head_id')
		->leftjoin('fee_heads as sub_head','sub_head.id','=','fee.parent_id')
		->leftjoin('session','session.id','=','assign_fee.session_id')
		->leftjoin('faculties','faculties.id','=','assign_fee.course_id')
		->where('assign_fee.branch_id', $branch)
		->where('assign_fee.session_id','!=' ,$session)
		->whereIn('assign_fee.course_id', $student_old_course)
		->where('assign_fee.status',1)
		->where(function($query)use($month,$req){
			if(isset($month)){
				foreach ($month as $key => $value) {
					$query->orWhere('assign_fee.due_month',$value);
				}
			}
			if($req->batch){
				$query->Where('assign_fee.batch_id',$req->batch);
			}
		})
		->Where('assign_fee.student_id', '0')
		->selectRAW("sum(collect_fee.amount_paid) as amount_paid")
		->selectRAW("sum(collect_fee.discount) as discount")
		->orWhere(function($q) use ($req,$month,$student_old_course,$session){
			if($req->student){
				$q->orWhere('assign_fee.student_id', $req->student)
				->where('assign_fee.branch_id', $req->branch)
				->where('assign_fee.session_id','!=', $session)
				->where('assign_fee.status',1)
				->where(function($query)use($month,$req){
					if(isset($month)){
						foreach ($month as $key => $value) {
							$query->orWhere('assign_fee.due_month',$value);
						}
					}
					if($req->batch){
						$query->Where('assign_fee.batch_id',$req->batch);
					}	
					})
				->whereIn('assign_fee.course_id', $student_old_course);
			}
		})->groupBy('assign_fee.id')->orderByRaw("FIELD(assign_fee.due_month, '4','5','6','7','8','9','10','11','12','1','2','3') ASC")->get();

		$fee_result=AssignFee::Select('assign_fee.*', 'fee_heads.fee_head_title','sub_head.fee_head_title as sub_head','assign_fee.id as assign_fee_id','session.session_name','faculties.faculty')
		->leftJoin('fee_heads', function($join){
			$join->on('fee_heads.id', '=', 'assign_fee.fee_head_id');
		})
		->leftjoin('fee_heads as fee','fee.id','=','assign_fee.fee_head_id')
		->leftjoin('fee_heads as sub_head','sub_head.id','=','fee.parent_id')
		->leftjoin('session','session.id','=','assign_fee.session_id')
		->leftjoin('faculties','faculties.id','=','assign_fee.course_id')
		->where('assign_fee.branch_id', $branch)
		->where('assign_fee.session_id', $session)
		->where('assign_fee.course_id', $course)
		->where('assign_fee.status',1)
		->where(function($query)use($month,$req){
			if(isset($month)){
				foreach ($month as $key => $value) {
					$query->orWhere('assign_fee.due_month',$value);
				}
			}
			if($req->batch){
				$query->Where('assign_fee.batch_id',$req->batch);
			}
		})
		->Where('assign_fee.student_id', '0')
		->orWhere(function($q) use ($req,$month){
			if($req->student){
				$q->orWhere('assign_fee.student_id', $req->student)
				->where('assign_fee.branch_id', $req->branch)
				->where('assign_fee.session_id', $req->ssn)
				->where('assign_fee.status',1)
				->where(function($query)use($month,$req){
					if(isset($month)){
						foreach ($month as $key => $value) {
							$query->orWhere('assign_fee.due_month',$value);
						}
					}
					if($req->batch){
						$query->Where('assign_fee.batch_id',$req->batch);
					}	
					})
				->where('assign_fee.course_id', $req->course);
			}
		})->groupBy('assign_fee.id')->orderByRaw("FIELD(assign_fee.due_month, '4','5','6','7','8','9','10','11','12','1','2','3') ASC")->get();
		$ret_val=""; $i=0;
// 	 	dd($fee_result,$old_fee_result);
	    if( (count($fee_result) && $req->student) || (count($old_fee_result) && $req->student) ){
			$ret_val.="<div class=\"row\" style=\" text-align:left; background:#F89406; padding:10px 15px; color:#ffffff;\">
			<div class=\"col-sm-2\"><input type=\"checkbox\" id=\"checkAll\"></div>
			<div class=\"col-sm-4\"><b>Fee Head</b></div>
			<div class=\"col-sm-2\"><b>Fees</b></div>
			<div class=\"col-sm-2\" title=\"Total Paid + Total Discount\"><b>Paid</b> <i class=\"fa fa-info-circle \" ></i> </div>
			
			
			<div class=\"col-sm-2\"><b>Due</b></div>
			
			
			</div>";
			$total_paid=$total_due=$to_pay=0; $old_to_pay=0;$total_old_due=0;$old_total_paid=0;
		    foreach($old_fee_result as $old_fee){
				$old_paid= $old_fee->amount_paid+ $old_fee->discount;
				$old_due = $old_fee->fee_amount - $old_paid;
				
				if($old_due>0){
                    $old_total_paid+=$old_paid;
	                $old_to_pay+=$old_fee->fee_amount;
	                $total_old_due+= $old_due;
					$ret_val.="<div class=\"row\" style=\"margin:5px 0px; text-align:left;border-bottom:1px dashed lightgrey;padding-bottom:5px;background: #ffd6c7fc;\">
					<div class=\"col-sm-2\">
					 <input type=\"checkbox\" name=\"assign_id[".$old_fee->assign_fee_id."]\" value=\"".$old_fee->id."\" checked></div>
					
					<div class=\"col-sm-4\">
		 
		           <b>".$old_fee->fee_head_title.' <br>'.$old_fee->faculty.' / '.$old_fee->session_name."
		             <input type=\"hidden\" value=\"".$old_fee->fee_head_id."\" name=\"fee_head_id[".$old_fee->assign_fee_id."]\">
		            <b>".$old_fee->times." </b></div>
					<div class=\"col-sm-2\"><b><i class=\"fa fa-inr\"></i> ".$old_fee->fee_amount."</b></div>
					<div class=\"col-sm-2\"><b><i class=\"fa fa-inr\"></i> ".$old_paid."</b></div>
					
					<div class=\"col-sm-2\"><b><i class=\"fa fa-inr\"></i> $old_due
		             <input type=\"hidden\" name=\"due_amount[".$old_fee->assign_fee_id."]\" value=\"".$old_due."\">
					</b></div>
					
					

					</div>";
					
					
				}
			}
			foreach($fee_result as $fee){
				// dd($fee);
				$paid_result=DB::table('collect_fee')->where('assign_fee_id', $fee->id)->where('student_id', $req->student)
				->Where('status' , 1)->sum('amount_paid');
				
				$discount=DB::table('collect_fee')->where('assign_fee_id', $fee->id)->where('student_id', $req->student)
				->Where('status' , 1)->sum('discount');
				$paid_result=$paid_result + $discount;
				$due = $fee->fee_amount - $paid_result;
				$total_paid=$total_paid+$paid_result;
				$total_due=$total_due+$due;
				$to_pay=$to_pay+$fee->fee_amount;
				$disabled = ($due == 0) ? " style=\"display:none;\"":"";
				$ret_val.="<div class=\"row\" style=\"margin:5px 0px; text-align:left;border-bottom:1px dashed lightgrey;padding-bottom:5px;\">
				<div class=\"col-sm-2\">
				 <input type=\"checkbox\" name=\"assign_id[".$fee->assign_fee_id."]\" value=\"".$fee->id."\" checked></div>
				
				<div class=\"col-sm-4\">
	 
	           <b>".$fee->fee_head_title.' <br>'.$fee->faculty.' / '.$fee->session_name."
	             <input type=\"hidden\" value=\"".$fee->fee_head_id."\" name=\"fee_head_id[".$fee->assign_fee_id."]\">
	            <b>".$fee->times." </b></div>
				<div class=\"col-sm-2\"><b><i class=\"fa fa-inr\"></i> ".$fee->fee_amount."</b></div>
				<div class=\"col-sm-2\"><b><i class=\"fa fa-inr\"></i> ".$paid_result."</b></div>
				
				<div class=\"col-sm-2\"><b><i class=\"fa fa-inr\"></i> $due
	             <input type=\"hidden\" name=\"due_amount[".$fee->assign_fee_id."]\" value=\"".$due."\">
				</b></div>
				
				

				</div>";
			}
			$overall_to_pay= $old_to_pay+$to_pay;
			$overall_due= $total_due+$total_old_due;
			$overall_paid= $old_total_paid+$total_paid;
			$ret_val.="<div class=\"row\" style=\" text-align:left; background:#f4ddbf; padding:10px 15px; color:#000;\" ><div class=\"col-sm-2 col-sm-offset-4\">
					 <b>Total</b>
					</div>
			 		<div class=\"col-sm-2\">
			 			<b><i class=\"fa fa-inr\"></i> ".$overall_to_pay."</b>
			 		</div>
			 		<div class=\"col-sm-2\">
			 			<b><i class=\"fa fa-inr\"></i> ".$overall_paid."</b>
			 		</div>
			 		
			 		<div class=\"col-sm-2\">
			 			<b><i class=\"fa fa-inr\"></i> ".$overall_due."</b>
			 		</div>
			 		</div>";
		
         }else{ 
         	$ret_val="<div class=\"alert alert-danger\"><h3>No Fees Assigned</h3></div>"; 
        }
		return response($ret_val);
	}
	public function BulkcollectFee(Request $req)
	{
		// dd($req->all());
		$session_list=Session_Model::select('id', 'session_name')->pluck('session_name', 'id')->toArray();
		$session_list=array_prepend($session_list,'Select Session',"");
		$panel=' Bulk Fees Collect';


		$ssn_tble=DB::table('session')->select('id', 'session_name')->where('active_status', '1')->get();
		$current_session=Session::get('activeSession');

 
		if(Session::has('activeBranch')){
		    $branch_ids = Session::get('activeBranch');
		}else{$branch_ids = Auth::user()->branch_id; }

		$branch_list = DB::table('branches')->select('id', 'branch_name')->pluck('branch_name', 'id')->toArray();
		
        $branch_list = Branch::select('branch_name','branch_address','id','org_id')->where("id",$branch_ids)->get();
		$course_list=Faculty::select('id', 'faculty')->where('branch_id', $branch_ids)->pluck('faculty', 'id')->toArray();
		$course_list=array_prepend($course_list, "Select ".env("course_label"), "");

		$pay_type_list = DB::table('payment_type')->select('type_name')->where('status', '1')->pluck('type_name','type_name')->toArray(); 
		$months_list=DB::table('months')->select('title','id')->where('status',1)->pluck('title','id')->toArray();
		$pay_type_list = array_prepend($pay_type_list, "----Select Payment Mode----", "");

		$student_list = array(); 
		$student_list = array_prepend($student_list, "select Student", "");
		if($req->all()){
			// dd($req->all());
		   $amount_paid=$req->amount;
		    $l=0;
		    $amount_left=$amount_paid;
		   $assign_id=$req->assign_id;
		   $fee_head_id= $req->fee_head_id;
		   $totaldue=0;
		   $receipt_id = [];
		   foreach ($assign_id as $key => $value) {
		   	    $due_amount= $req->due_amount[$key];

		   	    $totaldue= $totaldue+$due_amount;
		   }
		   if($amount_paid>$totaldue){
		   	 
		   	  Session::flash('message_danger', 'Paid amount Must be less than or Equal to due amount.'); 
		   	   return redirect('bulk_collect_fee');
		   }
		    else{
		        foreach ($assign_id as $key => $value) {
	   	         $due_amount= $req->due_amount[$key]; 
	   	        // dd($due_amount);
		   	     $data['remarks']=$req->remark;
                 $data['payment_type']=$req->payment_type;
                 $data['created_at']=date('Y-m-d');
                 $data['created_by']=Auth::user()->id;
                 $data['reference']=($req->reference) ? $req->reference : "-";
                 $data['reciept_date']=$req->reciept_date;
                 $data['assign_fee_id']=$value;
                 $data['student_id']=$req->student;
                 if($amount_paid>=$due_amount){
                 $data['amount_paid']=$due_amount;
                 $amount_left= $amount_paid-$due_amount;
                 $amount_paid= $amount_left;
                 }
                 elseif($amount_paid<$due_amount){
	                 $data['amount_paid']=$amount_paid;
	                 $amount_paid=$amount_paid - $amount_paid;
                 }
                
		   	    if($data['amount_paid']>0){
		   	    	$receipt_id[]=DB::table('collect_fee')->insertGetId($data);
		   	    }
		   	    else{
		   	    	// dd($amount_paid);
		   	    }

                     
		   }
		     if(count($receipt_id)>0 && $req->amount){
		     	$payment_count=count($receipt_id);
                 $collect_id=$receipt_id[$payment_count-1];
                $receipt_no=$this->reciept_no($collect_id,$receipt_id);
	            foreach ($receipt_id as $key => $value) {
	               DB::table('collect_fee')->where('id',$value)->update([
	                    'reciept_no'=>$receipt_no
	               ]);
	            }
                Session::flash('msG', 'Fees Collected successfully.');
			    return redirect('studentfeeReceipt/'.$receipt_no); //route('collect_fee');
		     }
		     else{
		     	 return redirect('bulk_collect_fee')->with('message_warning','Something went wrong.');
		     }
         
	     }


		}
		else{

		return view('Fee.bulkcollectfee', compact('panel', 'session_list', 'course_list', 'student_list', 'branch_list', 'branch_ids', 'pay_type_list', 'current_session','months_list'));
		}
	}
	
	/* end bulk collect fee code */

	public function getMonthName($id)
    {
        $month = [
                        1 => 'January',
                        2 => 'February',
                        3 => 'March',
                        4 => 'April',
                        5 => 'May',
                        6 => 'June',
                        7 => 'July',
                        8 => 'August',
                        9 => 'September',
                        10 => 'October',
                        11 => 'November',
                        12 => 'December',
                        
                    ];

        if ($month) {
            return $month[$id];
        }else{
            return "unknown";
        }
    }
    
    
    
    // Ankur Codes
    
   public function report_card(Request $req)
	{
		$data['session_list']=Session_Model::select('id', 'session_name')->pluck('session_name', 'id')->toArray();
		$data['session_list']=array_prepend($data['session_list'],'Select Session',"");
		$panel='Fees Collect';
		$data['ssn_tble']=DB::table('session')->select('id', 'session_name')->where('active_status', '1')->get();
		$data['current_session']=Session::get('activeSession');
		if(Session::has('activeBranch')){
		    $data['branch_ids'] = Session::get('activeBranch');
		}else{$data['branch_ids'] = Auth::user()->branch_id; }
		$data['branch_list'] = DB::table('branches')->select('id', 'branch_name')->pluck('branch_name', 'id')->toArray();
        $data['branch_list'] = Branch::select('branch_name','branch_address','id','org_id')->where("id",$data['branch_ids'])->get();
		$data['course_list']=Faculty::select('id', 'faculty')->where('branch_id', $data['branch_ids'])->pluck('faculty', 'id')->toArray();
		$data['course_list']=array_prepend($data['course_list'], "Select ".env("course_label"), "");
		$data['section_list']=DB::table('semesters')->select('id','semester')->where('status',1)->pluck('semester','id')->toArray();
		$data['section_list']=array_prepend($data['section_list'], "Select ".env("section_label"), "");
		$data['months_list']=DB::table('months')->select('title','id')->where('status',1)->pluck('title','id')->toArray();
		$data['student_list'] = array(); 
		$data['student_list'] = array_prepend($data['student_list'], "select Student", "");
		if($req->all()){
			$search_criteria=[];

			if($req->course){
				$search_criteria[env('course_label')]=Faculty::select('faculty as title')->where('id',$req->course)->first();
				$search_criteria[env('course_label')]=$search_criteria[env('course_label')]->title;
			}
			if($req->semester){
				$search_criteria[env('section_label')]=DB::table('semesters')->select('semester as title')->where('id',$req->semester)->first();
				$search_criteria[env('section_label')]=$search_criteria[env('section_label')]->title;
			}
			if($req->student){
				$search_criteria['Student']=DB::table('students')->select('first_name as title')->where('id',$req->student)->first();
				$search_criteria['Student']=$search_criteria['Student']->title;
			}
			if($req->due_month){
				//dd($req->due_month);
				foreach ($req->due_month as $key => $value) {
					$temp=DB::table('months')->select('title')->where('id',$value)->first();
					if(isset($search_criteria[env('Due Month')])){
						$search_criteria[env('Due Month')]=$search_criteria[env('Due Month')].','.$temp->title;
					}else{
						$search_criteria[env('Due Month')]=$temp->title;
					}
				}	
			}
			$assign['fee']=DB::table('assign_fee as af')->select('af.*','m.title','fee_amount','sds.Semester')
			->leftjoin('faculties as f','f.id','=','af.course_id')
			->leftjoin('months as m','m.id','=','af.due_month')
			->leftjoin('student_detail_sessionwise as sds','sds.student_id','=','af.student_id')
			->where('af.branch_id',session::get('activeBranch'))
			->where('af.session_id',session::get('activeSession'))
			->where(function($query) use ($req){
					if($req->course){
						
					    $query->where('af.course_id', $req->course);
					  }
					
					
					if($req->months){
					

						$query->whereIn('af.due_month', $req->months);
					}		
			})
			->where('af.status','=',1)
			->groupBy('af.id')
			->orderByRaw("FIELD(af.due_month, '4','5','6','7','8','9','10','11','12','1','2','3') ASC")
			->get();
			$student_head=[];$feehead=[];$master=[];
			foreach($assign['fee'] as $key =>$value){
				if($value->student_id==0){
					$feehead[$value->due_month][$value->course_id][]=$value;
				}
				else{
					$student_head[$value->due_month][$value->student_id][]=$value;

				}
			}			
			foreach($feehead as $key =>$value){
				foreach($value as $k=>$v){
					if(isset($std[$k])){
						$std_data=$std[$k];
					}
					else{
						
						$std[$k]=DB::table('student_detail_sessionwise as sds')->select('course_id','students.id','students.reg_no','students.first_name','students.first_name','students.first_name','faculties.faculty','sem.semester','pd.father_first_name as fatherName')
						->leftjoin('faculties','faculties.id','=','sds.course_id')
						->leftjoin('semesters as sem','sem.id','=','sds.Semester')
						->leftjoin('students','sds.student_id', '=', 'students.id')
						->leftjoin('parent_details as pd', 'pd.students_id', '=', 'students.id')
						->where('students.status','=',1)
						->where('course_id','=',$k)
						->where('students.branch_id', session('activeBranch'))
						->where('sds.session_id', session('activeSession'))
						->where(function($query) use ($req){
							if($req->section){
								//dd($req->section);
							
						    	$query->where('sds.semester', $req->section);
						  	}
						  	if($req->student){
						
        					    $query->where('sds.student_id', $req->student);
        					 }
						})
						->get();
						$std_data=$std[$k];	
					}
					
					
					foreach($std_data as $key=>$valu){
						foreach($v as $ke=>$vl){
							$collect_data=DB::table('collect_fee')->select('amount_paid')
							->where('student_id',$valu->id)
							->where('assign_fee_id',$vl->id)
							->where('collect_fee.status',1)
							->selectRaw('SUM(amount_paid) as total_paid,SUM(discount) as disc')
							->first();
							if(!isset($master[$vl->due_month][$vl->course_id][$valu->id]['stdata'])){
								$master[$vl->due_month][$vl->course_id][$valu->id]['stdata'] = $valu;
							}
							if(!isset($master[$vl->due_month][$vl->course_id][$valu->id]['assign'])){
								$master[$vl->due_month][$vl->course_id][$valu->id]['assign'] = 0;
							}
							if(!isset($master[$vl->due_month][$vl->course_id][$valu->id]['due'])){
								$master[$vl->due_month][$vl->course_id][$valu->id]['due'] = 0;
							}
							if(!isset($master[$vl->due_month][$vl->course_id][$valu->id]['collect'])){
								$master[$vl->due_month][$vl->course_id][$valu->id]['collect'] = 0;
							}
							if(!isset($master[$vl->due_month][$vl->course_id][$valu->id]['discount'])){
								$master[$vl->due_month][$vl->course_id][$valu->id]['discount'] = 0;
							}
							if(!isset($master[$vl->due_month][$vl->course_id][$valu->id]['month'])){
								$master[$vl->due_month][$vl->course_id][$valu->id]['month'] = $vl->title;
							}
							if(!isset($master[$vl->due_month][$vl->course_id][$valu->id]['month_id'])){
								$master[$vl->due_month][$vl->course_id][$valu->id]['month_id'] = $vl->due_month;
							}
							$master[$vl->due_month][$vl->course_id][$valu->id]['assign'] = $master[$vl->due_month][$vl->course_id][$valu->id]['assign'] + $vl->fee_amount;
							if($collect_data){
								$master[$vl->due_month][$vl->course_id][$valu->id]['collect'] += $collect_data->total_paid;

								$master[$vl->due_month][$vl->course_id][$valu->id]['discount'] += $collect_data->disc;
							}
							$master[$vl->due_month][$vl->course_id][$valu->id]['due']=$master[$vl->due_month][$vl->course_id][$valu->id]['assign']-($master[$vl->due_month][$vl->course_id][$valu->id]['collect']+$master[$vl->due_month][$vl->course_id][$valu->id]['discount']);	
						}
					}
				}
			}
            // dd($master);
			foreach($student_head as $ka=>$val){
				//dd($student_head);
				foreach($val as $k=>$v){
					//dd($val);
					$data=DB::table('student_detail_sessionwise as sds')->select('student_id','students.id','students.reg_no','students.first_name','students.first_name','students.first_name','faculties.faculty','sem.semester','pd.father_first_name as fatherName')
						->leftjoin('faculties','faculties.id','=','sds.course_id')
						->leftjoin('semesters as sem','sem.id','=','sds.Semester')
						->leftjoin('students','sds.student_id','=','students.id')
						->leftjoin('parent_details as pd', 'pd.students_id', '=', 'students.id')
						->where(function($query) use ($req){
							if($req->section){
							
						    	$query->where('sds.semester', $req->section);
						  	}
						})
						->where('students.status','=',1)
						->where('students.id','=',$k)
						->where('students.branch_id', session('activeBranch'))
						->where('sds.session_id', session('activeSession'))
						->get();
						foreach($data as $key=>$value){
							//dd($value);
							foreach($v as $ko=>$val){
								//dd($val);
								$collect_data=DB::table('collect_fee')->select('amount_paid')
									->where('student_id',$value->id)
									->where('assign_fee_id',$val->id)
									->where('collect_fee.status',1)
									->selectRaw('SUM(amount_paid) as total_paid,SUM(discount) as disc')
									->first();	
									if(!isset($master[$val->due_month][$val->course_id][$val->student_id]['stdata'])){
											$master[$val->due_month][$val->course_id][$val->student_id]['stdata'] = $value;
										}
									if(!isset($master[$val->due_month][$val->course_id][$val->student_id]['assign'])){
											$master[$val->due_month][$val->course_id][$val->student_id]['assign'] = 0;
										}
									if(!isset($master[$val->due_month][$val->course_id][$val->student_id]['due'])){
											$master[$val->due_month][$val->course_id][$val->student_id]['due'] = 0;
										}
									if(!isset($master[$val->due_month][$val->course_id][$val->student_id]['collect'])){
											$master[$val->due_month][$val->course_id][$val->student_id]['collect'] = 0;
										}
									if(!isset($master[$val->due_month][$val->course_id][$val->student_id]['discount'])){
											$master[$val->due_month][$val->course_id][$val->student_id]['discount'] = 0;
										}
									if(!isset($master[$val->due_month][$val->course_id][$val->student_id]['month'])){
											$master[$val->due_month][$val->course_id][$val->student_id]['month'] = $val->title;
										}
									if(!isset($master[$val->due_month][$val->course_id][$val->student_id]['month_id'])){
											$master[$val->due_month][$val->course_id][$val->student_id]['month_id'] = $val->due_month;
										}
									$master[$val->due_month][$val->course_id][$val->student_id]['assign'] = $master[$val->due_month][$val->course_id][$val->student_id]['assign'] + $val->fee_amount;
									if($collect_data){
										$master[$val->due_month][$val->course_id][$val->student_id]['collect'] += $collect_data->total_paid;
										$master[$val->due_month][$val->course_id][$val->student_id]['discount'] += $collect_data->disc;
									}
									$master[$val->due_month][$val->course_id][$val->student_id]['due']=$master[$val->due_month][$val->course_id][$val->student_id]['assign']-($master[$val->due_month][$val->course_id][$val->student_id]['collect']+$master[$val->due_month][$val->course_id][$val->student_id]['discount']);
							}
						}
				}	
			}
			
			

			$branch=Branch::where('id', session('activeBranch'))->first();
			
			 $type = $req->report_type;
			
			return view('Fee.report_card',compact('branch','master','search_criteria','type'));
		}else{
			return view('Fee.report_card',compact('data'));
			}
	}

	public function report_head(Request $req){
		//dd("hdfhh");
		$data['session_list']=Session_Model::select('id', 'session_name')->pluck('session_name', 'id')->toArray();
		$data['session_list']=array_prepend($data['session_list'],'Select Session',"");
		$panel='Fees Collect';
		$data['ssn_tble']=DB::table('session')->select('id', 'session_name')->where('active_status', '1')->get();
		$data['current_session']=Session::get('activeSession');
		if(Session::has('activeBranch')){
		    $data['branch_ids'] = Session::get('activeBranch');
		}else{$data['branch_ids'] = Auth::user()->branch_id; }
		$data['branch_list'] = DB::table('branches')->select('id', 'branch_name')->pluck('branch_name', 'id')->toArray();
        $data['branch_list'] = Branch::select('branch_name','branch_address','id','org_id')->where("id",$data['branch_ids'])->get();
		$data['course_list']=Faculty::select('id', 'faculty')->where('branch_id', $data['branch_ids'])->pluck('faculty', 'id')->toArray();
		$data['course_list']=array_prepend($data['course_list'], "Select ".env("course_label"), "");
		$data['section_list']=DB::table('semesters')->select('id','semester')->where('status',1)->pluck('semester','id')->toArray();
		$data['section_list']=array_prepend($data['section_list'], "Select ".env("section_label"), "");
		$data['months_list']=DB::table('months')->select('title','id')->where('status',1)->pluck('title','id')->toArray();
		//dd($data);
		if($req->all()){
			$search_criteria=[];
			if($req->course){
			//dd($req->course);
				$search_criteria[env('course_label')]=Faculty::select('faculty as title')->where('id',$req->course)->first();
				$search_criteria[env('course_label')]=$search_criteria[env('course_label')]->title;
			}
			if($req->semester){
				//dd($req->semester);
				$search_criteria[env('section_label')]=DB::table('semesters')->select('semester as title')->where('id',$req->semester)->first();
				$search_criteria[env('section_label')]=$search_criteria[env('section_label')]->title;
			}
			
			if($req->due_month){
				//dd($req->due_month);
				foreach ($req->due_month as $key => $value) {
					$temp=DB::table('months')->select('title')->where('id',$value)->first();
					if(isset($search_criteria[env('Due Month')])){
						$search_criteria[env('Due Month')]=$search_criteria[env('Due Month')].','.$temp->title;
					}else{
						$search_criteria[env('Due Month')]=$temp->title;
					}
				}	
			}

			$assign['fee']=DB::table('assign_fee as af')->select('af.*','m.title','fee_amount','sds.Semester','fh.fee_head_title')
			->leftjoin('faculties as f','f.id','=','af.course_id')
			->leftjoin('months as m','m.id','=','af.due_month')
			->leftjoin('student_detail_sessionwise as sds','sds.student_id','=','af.student_id')
			->where('af.branch_id',session::get('activeBranch'))
			->where('af.session_id',session::get('activeSession'))
			->where('af.status','=',1)
			
			->leftJoin('collect_fee as cf',function($q){
						$q->on('af.id','=','cf.assign_fee_id')
							->Where('cf.status',1);
					})
			->leftjoin('fee_heads as fh','fh.id','=','af.fee_head_id')
			->where(function($query) use ($req){
					if($req->course){
						
					    $query->where('af.course_id', $req->course);
					  }
					
					if($req->months){
					

						$query->whereIn('af.due_month', $req->months);
					}		
			})
			->groupBy('af.id')
			->orderBy('af.due_month','asc')
			->get();
			
			//dd($assign);
			$student_head=[];$feehead=[];$master=[];
			foreach($assign['fee'] as $key =>$value){
				if($value->student_id==0){
					$feehead[$value->due_month][$value->course_id][]=$value;
				}
				else{
					$student_head[$value->due_month][$value->student_id][]=$value;

				}

			}
			//dd($feehead,$student_head);
			foreach($feehead as $key =>$value){
				foreach($value as $k=>$v){
					
					if(isset($std[$k])){
						$std_data=$std[$k];
					}
					else{
						
						$std[$k]=DB::table('student_detail_sessionwise as sds')->select('course_id','students.id','students.reg_no','students.first_name','students.first_name','students.first_name','faculties.faculty','sem.semester','pd.father_first_name as fatherName',DB::raw('count(*) as total_reg'))
						->leftjoin('faculties','faculties.id','=','sds.course_id')
						->leftjoin('semesters as sem','sem.id','=','sds.Semester')
						->leftjoin('students','sds.student_id', '=', 'students.id')
						->leftjoin('parent_details as pd', 'pd.students_id', '=', 'students.id')
						->where('students.status','=',1)
						->where('course_id','=',$k)
						->where('students.branch_id', session('activeBranch'))
						->where('sds.session_id', session('activeSession'))
						->where(function($query) use ($req){
							if($req->section){
								//dd($req->section);
							
						    	$query->where('sds.semester', $req->section);
						  	}
						})
						//->groupBy('students.status')
						->get();

						$std_data=$std[$k];	
					}
					//dd($std_data);
					
					
					foreach($std_data as $key=>$valu){
						//dd($valu);
						foreach($v as $ke=>$vl){
							//dd($v);
							$collect_data=DB::table('collect_fee')->select('amount_paid')
							->where('student_id',$valu->id)
							->where('assign_fee_id',$vl->id)
							->selectRaw('SUM(amount_paid) as total_paid,SUM(discount) as disc')
							->first();
							//dd($collect_data);
							if(!isset($master[$vl->due_month][$vl->course_id][$ke]['stdata'])){
								$master[$vl->due_month][$vl->course_id][$ke]['stdata'] = $valu;
							}
							if(!isset($master[$vl->due_month][$vl->course_id][$ke]['assign'])){
								$master[$vl->due_month][$vl->course_id][$ke]['assign'] = 0;
							}
							if(!isset($master[$vl->due_month][$vl->course_id][$ke]['due'])){
								$master[$vl->due_month][$vl->course_id][$ke]['due'] = 0;
							}
							if(!isset($master[$vl->due_month][$vl->course_id][$ke]['collect'])){
								$master[$vl->due_month][$vl->course_id][$ke]['collect'] = 0;
							}
							if(!isset($master[$vl->due_month][$vl->course_id][$ke]['discount'])){
								$master[$vl->due_month][$vl->course_id][$ke]['discount'] = 0;
							}
							if(!isset($master[$vl->due_month][$vl->course_id][$ke]['month'])){
								$master[$vl->due_month][$vl->course_id][$ke]['month'] = $vl->title;
							}
							if(!isset($master[$vl->due_month][$vl->course_id][$ke]['month_id'])){
								$master[$vl->due_month][$vl->course_id][$ke]['month_id'] = $vl->due_month;
							}
							if(!isset($master[$vl->due_month][$vl->course_id][$ke]['title'])){
								$master[$vl->due_month][$vl->course_id][$ke]['title'] = $vl->fee_head_title;
							}
							$master[$vl->due_month][$vl->course_id][$ke]['assign'] = $master[$vl->due_month][$vl->course_id][$ke]['assign'] +($vl->fee_amount * $valu->total_reg);
							if($collect_data){
								$master[$vl->due_month][$vl->course_id][$ke]['collect'] +=( $collect_data->total_paid * $valu->total_reg);

								$master[$vl->due_month][$vl->course_id][$ke]['discount'] += ($collect_data->disc * $valu->total_reg);
							}
							$master[$vl->due_month][$vl->course_id][$ke]['due'] =($master[$vl->due_month][$vl->course_id][$ke]['assign'] - ($master[$vl->due_month][$vl->course_id][$ke]['collect'] +$master[$vl->due_month][$vl->course_id][$ke]['discount']));
						}
					}
					
				}
				
			}
			//dd($master);
			foreach($student_head as $ka=>$val){
				//dd($val);
				foreach($val as $k=>$v){
					//dd($v,$val);
					$data=DB::table('student_detail_sessionwise as sds')->select('sds.student_id','students.id','students.reg_no','students.first_name','faculties.faculty','sem.semester',DB::raw('count(*) as total_reg'))
						->leftjoin('faculties','faculties.id','=','sds.course_id')
						->leftjoin('semesters as sem','sem.id','=','sds.Semester')
						->leftjoin('students','sds.student_id','=','students.id')

						
						->where(function($query) use ($req){
							if($req->section){
							
						    	$query->where('sds.semester', $req->section);
						  	}
						})
						//->where('students.status','=',1)
						->where('students.id','=',$k)
						->where('students.branch_id', session('activeBranch'))
						->where('sds.session_id', session('activeSession'))
						->groupBy('faculties.id')
						->get();
						//dd($data);
						foreach($data as $key=>$value){
							//dd($value);
							foreach($v as $ko=>$val){
								//dd($val);
								$collect_data=DB::table('collect_fee')->select('amount_paid')
									->where('student_id',$value->id)
									->where('assign_fee_id',$val->student_id)
									->selectRaw('SUM(amount_paid) as total_paid,SUM(discount) as disc')
									->first();	
									if(!isset($master[$val->due_month][$val->fee_head_id]['1']['stdata'])){
											$master[$val->due_month][$val->fee_head_id]['1']['stdata'] = $value;
										}
									if(!isset($master[$val->due_month][$val->fee_head_id]['1']['assign'])){
											$master[$val->due_month][$val->fee_head_id]['1']['assign'] = 0;
										}
									if(!isset($master[$val->due_month][$val->fee_head_id]['1']['due'])){
											$master[$val->due_month][$val->fee_head_id]['1']['due'] = 0;
										}
									if(!isset($master[$val->due_month][$val->fee_head_id]['1']['collect'])){
											$master[$val->due_month][$val->fee_head_id]['1']['collect'] = 0;
										}
									if(!isset($master[$val->due_month][$val->fee_head_id]['1']['discount'])){
											$master[$val->due_month][$val->fee_head_id]['1']['discount'] = 0;
										}
									if(!isset($master[$val->due_month][$val->fee_head_id]['1']['month'])){
											$master[$val->due_month][$val->fee_head_id]['1']['month'] = $val->title;
										}
									if(!isset($master[$val->due_month][$val->fee_head_id]['1']['month_id'])){
											$master[$val->due_month][$val->fee_head_id]['1']['month_id'] = $val->due_month;
										}
									if(!isset($master[$val->due_month][$val->fee_head_id]['1']['title'])){
											$master[$val->due_month][$val->fee_head_id]['1']['title'] = $val->fee_head_title;
										}
									$master[$val->due_month][$val->fee_head_id]['1']['assign'] = $master[$val->due_month][$val->fee_head_id]['1']['assign'] + $val->fee_amount;
									if($collect_data){
										$master[$val->due_month][$val->fee_head_id]['1']['collect'] += ($collect_data->total_paid * $value->total_reg);
										$master[$val->due_month][$val->fee_head_id]['1']['discount'] += ($collect_data->disc * $value->total_reg);
									}
									$master[$val->due_month][$val->fee_head_id]['1']['due']=$master[$val->due_month][$val->fee_head_id]['1']['assign']-($master[$val->due_month][$val->fee_head_id]['1']['collect'] +$master[$val->due_month][$val->fee_head_id]['1']['discount']);

							}
						}
				}	
			}
			//dd($master);
			ksort($master);

			$branch=Branch::where('id', session('activeBranch'))->first();
			return view('Fee.report_head',compact('branch','master','search_criteria'));
		}else{
			return view('Fee.report_head',compact('data'));
			}

	}
	
	public function report_class(Request $req){
		
		$data['session_list']=Session_Model::select('id', 'session_name')->pluck('session_name', 'id')->toArray();
		$data['session_list']=array_prepend($data['session_list'],'Select Session',"");
		$panel='Fees Collect';
		$data['ssn_tble']=DB::table('session')->select('id', 'session_name')->where('active_status', '1')->get();
		$data['current_session']=Session::get('activeSession');
		if(Session::has('activeBranch')){
		    $data['branch_ids'] = Session::get('activeBranch');
		}else{$data['branch_ids'] = Auth::user()->branch_id; }
		$data['branch_list'] = DB::table('branches')->select('id', 'branch_name')->pluck('branch_name', 'id')->toArray();
        $data['branch_list'] = Branch::select('branch_name','branch_address','id','org_id')->where("id",$data['branch_ids'])->get();
		$data['course_list']=Faculty::select('id', 'faculty')->where('branch_id', $data['branch_ids'])->pluck('faculty', 'id')->toArray();
		$data['course_list']=array_prepend($data['course_list'], "Select ".env("course_label"), "");
		$data['section_list']=DB::table('semesters')->select('id','semester')->where('status',1)->pluck('semester','id')->toArray();
		$data['section_list']=array_prepend($data['section_list'], "Select ".env("section_label"), "");
		$data['months_list']=DB::table('months')->select('title','id')->where('status',1)->pluck('title','id')->toArray();
		$data['student_list'] = array(); 
		$data['student_list'] = array_prepend($data['student_list'], "select Student", "");
		
		if($req->all()){
		  //  dd($req->all());
			$search_criteria=[];

			if($req->course){
				$search_criteria[env('course_label')]=Faculty::select('faculty as title')->where('id',$req->course)->first();
				$search_criteria[env('course_label')]=$search_criteria[env('course_label')]->title;
			}
			if($req->semester){
				$search_criteria[env('section_label')]=DB::table('semesters')->select('semester as title')->where('id',$req->semester)->first();
				$search_criteria[env('section_label')]=$search_criteria[env('section_label')]->title;
			}
			if($req->student){
				$search_criteria['Student']=DB::table('students')->select('first_name as title')->where('id',$req->student)->first();
				$search_criteria['Student']=$search_criteria['Student']->title;
			}
			if($req->due_month){
				//dd($req->due_month);
				foreach ($req->due_month as $key => $value) {
					$temp=DB::table('months')->select('title')->where('id',$value)->first();
					if(isset($search_criteria[env('Due Month')])){
						$search_criteria[env('Due Month')]=$search_criteria[env('Due Month')].','.$temp->title;
					}else{
						$search_criteria[env('Due Month')]=$temp->title;
					}
				}	
			}

			$assign['fee']=DB::table('assign_fee as af')->select('af.*','m.title','fee_amount',DB::raw('count(*) as total_course'),'f.faculty')
			->leftjoin('faculties as f','f.id','=','af.course_id')
			->leftjoin('months as m','m.id','=','af.due_month')
			
			->where('af.branch_id',session::get('activeBranch'))
			->where('af.session_id',session::get('activeSession'))
			->where('af.status','=',1)
			->groupBy('af.id')
			->where(function($query) use ($req){
					if($req->course){
						
					    $query->where('af.course_id', $req->course);
					  }
					
					if($req->student){
						
					    $query->where('af.student_id', $req->student)
					       ->orWhere('af.student_id',0);
					    ;
					  }
					if($req->months){
					

						$query->whereIn('af.due_month', $req->months);
					}		
			})
			->orderBy('f.id')
			->get();
		
            $student_head=[];$feehead=[];$master=[];
			foreach($assign['fee'] as $key =>$value){
				if($value->student_id == 0){
					$feehead[$value->course_id][]=$value;
				}
				else{
					$student_head[$value->student_id][]=$value;

				}
			}
			
			foreach($feehead as $key =>$value){
			    foreach($value as $k => $vl){
			        if(isset($std[$key])){
							$std_data=count($std[$key]);
					}else{	
    					$std[$key]=DB::table('student_detail_sessionwise as sds')->select('students.id')
    					->leftjoin('students','sds.student_id', '=', 'students.id')
    					->where('students.status','=',1)
    					->where('course_id','=',$key)
    					->where('students.branch_id', session('activeBranch'))
    					->where('sds.session_id', session('activeSession'))
    					->where(function($query) use ($req){
        					if($req->section){
        				    	$query->where('sds.Semester', $req->section);
        				  	}
    					})
    					->pluck('id')->toArray();
    					$std_data=count($std[$key]);	
    				}
    					$collect_data=DB::table('collect_fee')->select('amount_paid')
						->where('assign_fee_id',$vl->id)
						->selectRaw('SUM(amount_paid) as total_paid,SUM(discount) as disc')
						->where('collect_fee.status',1)
						->where(function($q)use($std,$key){
						    if(count($std)>0){
						        $q->whereIn('collect_fee.student_id',$std[$key]);
						    }
						})
						->first();
					if(!isset($master[$key]['assign'])){
					    $master[$key]['assign'] = 0;
					    
					}
					if(!isset($master[$key]['collect'])){
					    $master[$key]['collect'] = 0;
					    
					}
					if(!isset($master[$key]['discount'])){
					  
					    $master[$key]['discount'] = 0;
					   
					    
					}
					if(!isset($master[$key]['title'])){
					     $master[$key]['title'] = '';
					    
					}
					
					$master[$key]['assign'] += ($vl->fee_amount * $std_data);
					$master[$key]['collect'] += ($collect_data->total_paid);
					$master[$key]['discount'] += ($collect_data->disc);
					$master[$key]['title'] = ($vl->faculty);
			    }		
			}    
          $master2=[];
            foreach($student_head as $key=>$value){
                foreach($value as $v1){
                    $check = DB::table('student_detail_sessionwise as sds')
                    ->leftJoin('students as st','st.id','=','sds.student_id')
                    ->where(function($q)use($req,$v1){
                        if($req->section){
                            $q->where('sds.Semester',$req->section);
                            
                        }
                        $q->where('student_id',$v1->student_id)
                        ->where('active_status',1)
                        ->where('sds.session_id',Session::get('activeSession'))
                        ->where('st.status',1);
                    })
                    ->first();
                    
   
					if(!isset($master[$v1->course_id]['assign'])){
					    $master[$v1->course_id]['assign'] = 0;
					    
					}
					if(!isset($master[$v1->course_id]['collect'])){
					    $master[$v1->course_id]['collect'] = 0;
					    
					}
					if(!isset($master[$v1->course_id]['discount'])){
					  
					    $master[$v1->course_id]['discount'] = 0;
					   
					    
					}
					if(!isset($master[$v1->course_id]['title'])){
					     $master[$v1->course_id]['title'] = '';
					    
					}
					if($check){
					    $collect_data=DB::table('collect_fee')->select('amount_paid')
						->where('assign_fee_id',$v1->id)
						->where('collect_fee.student_id',$v1->student_id)
						->selectRaw('SUM(amount_paid) as total_paid,SUM(discount) as disc')
						->where('collect_fee.status',1)
						->first();
					    $master[$v1->course_id]['assign'] += $v1->fee_amount;    
					    $master[$v1->course_id]['collect'] += $collect_data->total_paid;    
					    $master[$v1->course_id]['discount'] += $collect_data->disc;    
					    $master[$v1->course_id]['title'] = $v1->faculty;    
					}
					
                }
            }
            
            
            
			$branch=Branch::where('id', session('activeBranch'))->first();
			return view('Fee.report_class',compact('branch','master','search_criteria'));
		}else{

		return view('Fee.report_class',compact('data'));
		}
	}
	
	
	/*public function report_class(Request $req){
		//dd('mhadev');
		$data['session_list']=Session_Model::select('id', 'session_name')->pluck('session_name', 'id')->toArray();
		$data['session_list']=array_prepend($data['session_list'],'Select Session',"");
		$panel='Fees Collect';
		$data['ssn_tble']=DB::table('session')->select('id', 'session_name')->where('active_status', '1')->get();
		$data['current_session']=Session::get('activeSession');
		if(Session::has('activeBranch')){
		    $data['branch_ids'] = Session::get('activeBranch');
		}else{$data['branch_ids'] = Auth::user()->branch_id; }
		$data['branch_list'] = DB::table('branches')->select('id', 'branch_name')->pluck('branch_name', 'id')->toArray();
        $data['branch_list'] = Branch::select('branch_name','branch_address','id','org_id')->where("id",$data['branch_ids'])->get();
		$data['course_list']=Faculty::select('id', 'faculty')->where('branch_id', $data['branch_ids'])->pluck('faculty', 'id')->toArray();
		$data['course_list']=array_prepend($data['course_list'], "Select ".env("course_label"), "");
		$data['section_list']=DB::table('semesters')->select('id','semester')->where('status',1)->pluck('semester','id')->toArray();
		$data['section_list']=array_prepend($data['section_list'], "Select ".env("section_label"), "");
		$data['months_list']=DB::table('months')->select('title','id')->where('status',1)->pluck('title','id')->toArray();
		$data['student_list'] = array(); 
		$data['student_list'] = array_prepend($data['student_list'], "select Student", "");
		//dd($data);
		if($req->all()){
			$search_criteria=[];

			if($req->course){
				$search_criteria[env('course_label')]=Faculty::select('faculty as title')->where('id',$req->course)->first();
				$search_criteria[env('course_label')]=$search_criteria[env('course_label')]->title;
			}
			if($req->semester){
				$search_criteria[env('section_label')]=DB::table('semesters')->select('semester as title')->where('id',$req->semester)->first();
				$search_criteria[env('section_label')]=$search_criteria[env('section_label')]->title;
			}
			if($req->student){
				$search_criteria['Student']=DB::table('students')->select('first_name as title')->where('id',$req->student)->first();
				$search_criteria['Student']=$search_criteria['Student']->title;
			}
			if($req->due_month){
				//dd($req->due_month);
				foreach ($req->due_month as $key => $value) {
					$temp=DB::table('months')->select('title')->where('id',$value)->first();
					if(isset($search_criteria[env('Due Month')])){
						$search_criteria[env('Due Month')]=$search_criteria[env('Due Month')].','.$temp->title;
					}else{
						$search_criteria[env('Due Month')]=$temp->title;
					}
				}	
			}

			$assign['fee']=DB::table('assign_fee as af')->select('af.*','m.title','fee_amount','sds.Semester',DB::raw('count(*) as total_course'))
			->leftjoin('faculties as f','f.id','=','af.course_id')
			->leftjoin('months as m','m.id','=','af.due_month')
			->leftjoin('student_detail_sessionwise as sds','sds.student_id','=','af.student_id')
			->where('af.branch_id',session::get('activeBranch'))
			->where('af.session_id',session::get('activeSession'))
			->where('af.status','=',1)
			->groupBy('af.id')
			->where(function($query) use ($req){
					if($req->course){
						
					    $query->where('af.course_id', $req->course);
					  }
					
					if($req->student){
						
					    $query->where('af.student_id', $req->student);
					  }
					if($req->months){
					

						$query->whereIn('af.due_month', $req->months);
					}		
			})
			->get();
			//dd($assign);
			$student_head=[];$feehead=[];$master=[];
			foreach($assign['fee'] as $key =>$value){
				if($value->student_id==0){
					$feehead[$value->course_id][$value->due_month][$value->course_id][]=$value;
				}
				else{
					$student_head[$value->course_id][$value->due_month][$value->student_id][]=$value;

				}
			}			
			//dd($feehead,$student_head);
			foreach($feehead as $key =>$value){
				foreach($value as $ka=>$va){
					foreach($va as $k=>$v){

						if(isset($std[$k])){
							$std_data=$std[$k];
						}
						else{
							
							$std[$k]=DB::table('student_detail_sessionwise as sds')->select('course_id','students.id','students.reg_no','students.first_name','students.first_name','students.first_name','faculties.faculty','sem.semester','pd.father_first_name as fatherName',DB::raw('count(*) as total_reg'))
							->leftjoin('faculties','faculties.id','=','sds.course_id')
							->leftjoin('semesters as sem','sem.id','=','sds.Semester')
							->leftjoin('students','sds.student_id', '=', 'students.id')
							->leftjoin('parent_details as pd', 'pd.students_id', '=', 'students.id')
							->where('students.status','=',1)
							->where('course_id','=',$k)
							->where('students.branch_id', session('activeBranch'))
							->where('sds.session_id', session('activeSession'))
							->where(function($query) use ($req){
							if($req->section){
								//dd($req->section);
							
						    	$query->where('sds.semester', $req->section);
						  	}
							})
							->get();
							$std_data=$std[$k];	
						}
						//dd($std_data);
						
						foreach($std_data as $key=>$valu){
							//dd($valu);
							foreach($v as $ke=>$vl){
								//dd($vl);
								$collect_data=DB::table('collect_fee')->select('amount_paid')
								->where('student_id',$valu->id)
								->where('assign_fee_id',$vl->id)
								->selectRaw('SUM(amount_paid) as total_paid,SUM(discount) as disc')
								->first();
								if(!isset($master[$vl->course_id][$vl->due_month][$vl->course_id][$valu->id]['stdata'])){
									$master[$vl->course_id][$vl->due_month][$vl->course_id][$valu->id]['stdata'] = $valu;
								}
								if(!isset($master[$vl->course_id][$vl->due_month][$vl->course_id][$valu->id]['assign'])){
									$master[$vl->course_id][$vl->due_month][$vl->course_id][$valu->id]['assign'] = 0;
								}
								if(!isset($master[$vl->course_id][$vl->due_month][$vl->course_id][$valu->id]['due'])){
									$master[$vl->course_id][$vl->due_month][$vl->course_id][$valu->id]['due'] = 0;
								}
								if(!isset($master[$vl->course_id][$vl->due_month][$vl->course_id][$valu->id]['collect'])){
									$master[$vl->course_id][$vl->due_month][$vl->course_id][$valu->id]['collect'] = 0;
								}
								if(!isset($master[$vl->course_id][$vl->due_month][$vl->course_id][$valu->id]['discount'])){
									$master[$vl->course_id][$vl->due_month][$vl->course_id][$valu->id]['discount'] = 0;
								}
								if(!isset($master[$vl->course_id][$vl->due_month][$vl->course_id][$valu->id]['month'])){
									$master[$vl->course_id][$vl->due_month][$vl->course_id][$valu->id]['month'] = $vl->title;
								}
								if(!isset($master[$vl->course_id][$vl->due_month][$vl->course_id][$valu->id]['month_id'])){
									$master[$vl->course_id][$vl->due_month][$vl->course_id][$valu->id]['month_id'] = $vl->due_month;
								}
								$master[$vl->course_id][$vl->due_month][$vl->course_id][$valu->id]['assign'] = $master[$vl->course_id][$vl->due_month][$vl->course_id][$valu->id]['assign'] + $vl->fee_amount * $valu->total_reg;
								if($collect_data){
									$master[$vl->course_id][$vl->due_month][$vl->course_id][$valu->id]['collect'] += $collect_data->total_paid * $valu->total_reg;

									$master[$vl->course_id][$vl->due_month][$vl->course_id][$valu->id]['discount'] += $collect_data->disc * $valu->total_reg;
								}
								$master[$vl->course_id][$vl->due_month][$vl->course_id][$valu->id]['due']=
								$master[$vl->course_id][$vl->due_month][$vl->course_id][$valu->id]['assign'] -($master[$vl->course_id][$vl->due_month][$vl->course_id][$valu->id]['collect']+$master[$vl->course_id][$vl->due_month][$vl->course_id][$valu->id]['discount']);	
							}
						}
					}
				}
			}
			dd($master,$student_head);
			foreach($student_head as $key=>$value){
				foreach($value as $ke=>$val){

					foreach($val as $k=>$v){
					
					$data=DB::table('student_detail_sessionwise as sds')->select('student_id','students.id','students.reg_no','students.first_name','students.first_name','students.first_name','faculties.faculty','sem.semester','pd.father_first_name as fatherName',DB::raw('count(*) as total_reg'))
						->leftjoin('faculties','faculties.id','=','sds.course_id')
						->leftjoin('semesters as sem','sem.id','=','sds.Semester')
						->leftjoin('students','sds.student_id','=','students.id')
						->leftjoin('parent_details as pd', 'pd.students_id', '=', 'students.id')
						
						->where('students.status','=',1)
						->where('students.id','=',$k)
						->where('students.branch_id', session('activeBranch'))
						->where('sds.session_id', session('activeSession'))
						->where(function($query) use ($req){
							if($req->section){
								//dd($req->section);
							
						    	$query->where('sds.semester', $req->section);
						  	}
						})
						->get();
						
						foreach($data as $key=>$value){
							
							foreach($v as $ko=>$val){
								
								$collect_data=DB::table('collect_fee')->select('amount_paid')
									->where('student_id',$value->id)
									->where('assign_fee_id',$val->id)
									->selectRaw('SUM(amount_paid) as total_paid,SUM(discount) as disc')
									->first();	
									if(!isset($master[$val->course_id][$val->due_month][$val->course_id][$val->student_id]['stdata'])){
											$master[$val->course_id][$val->due_month][$val->course_id][$val->student_id]['stdata'] = $value;
										}
									if(!isset($master[$val->course_id][$val->due_month][$val->course_id][$val->student_id]['assign'])){
											$master[$val->course_id][$val->due_month][$val->course_id][$val->student_id]['assign'] = 0;
										}
									if(!isset($master[$val->course_id][$val->due_month][$val->course_id][$val->student_id]['due'])){
											$master[$val->course_id][$val->due_month][$val->course_id][$val->student_id]['due'] = 0;
										}
									if(!isset($master[$val->course_id][$val->due_month][$val->course_id][$val->student_id]['collect'])){
											$master[$val->course_id][$val->due_month][$val->course_id][$val->student_id]['collect'] = 0;
										}
									if(!isset($master[$val->course_id][$val->due_month][$val->course_id][$val->student_id]['discount'])){
											$master[$val->course_id][$val->due_month][$val->course_id][$val->student_id]['discount'] = 0;
										}
									if(!isset($master[$val->course_id][$val->due_month][$val->course_id][$val->student_id]['month'])){
											$master[$val->course_id][$val->due_month][$val->course_id][$val->student_id]['month'] = $val->title;
										}
									if(!isset($master[$val->course_id][$val->due_month][$val->course_id][$val->student_id]['month_id'])){
											$master[$val->course_id][$val->due_month][$val->course_id][$val->student_id]['month_id'] = $val->due_month;
										}
									$master[$val->course_id][$val->due_month][$val->course_id][$val->student_id]['assign'] = $master[$val->course_id][$val->due_month][$val->course_id][$val->student_id]['assign'] + $val->fee_amount;
									if($collect_data){
										$master[$val->course_id][$val->due_month][$val->course_id][$val->student_id]['collect'] += $collect_data->total_paid;
										$master[$val->course_id][$val->due_month][$val->course_id][$val->student_id]['discount'] += $collect_data->disc;
									}
									$master[$val->course_id][$val->due_month][$val->course_id][$val->student_id]['due']=$master[$val->course_id][$val->due_month][$val->course_id][$val->student_id]['assign']-(										$master[$val->course_id][$val->due_month][$val->course_id][$val->student_id]['collect']+$master[$val->course_id][$val->due_month][$val->course_id][$val->student_id]['discount']);
							}
						}
				}
				}

			}
			//dd($master);
			$branch=Branch::where('id', session('activeBranch'))->first();
			return view('Fee.report_class',compact('branch','master','search_criteria'));
		}else{

		return view('Fee.report_class',compact('data'));
		}
	}*/
	
	
	public function student_collection_report_10Feb2022(Request $req){
		$data['course_list']=Faculty::where('status', '1')->where('branch_id', session('activeBranch'))->pluck('id', 'faculty');
		$data['fee_list']=DB::table('fee_heads')->where('status', '1')->select('id', 'fee_head_title')->get();
		
		$data['months_list']=DB::table('months')->select('title','id')->where('status',1)->pluck('title','id')->toArray();
		$data['student_list'] = array();
		$data['semester_list']=DB::table('semesters')->select('id', 'semester')->get();
		$branch= DB::table('branches')->select('*')->where('id',Session::get('activeBranch'))->first();
          if($req->all()){
             //dd($req->all(),session::get('activeBranch'));
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

			if($req->from){
				$search_criteria['From']=Carbon::parse($req->from)->format('d-m-Y'); 
			}
			if($req->to ){
				$search_criteria['To']=Carbon::parse($req->to)->format('d-m-Y'); 
			}
           // dd($search_criteria);
          	
            $collect= DB::table('collect_fee')->select('collect_fee.*','s.first_name','s.middle_name','s.last_name','s.reg_no','pd.father_first_name as father_name')
            ->leftjoin('students as s','s.id','=','collect_fee.student_id')
            ->leftjoin('parent_details as pd','pd.students_id','=','collect_fee.student_id')
            ->leftjoin('student_detail_sessionwise as sds','sds.student_id','=','collect_fee.student_id')
           ->leftjoin('assign_fee as af','af.id','=','collect_fee.assign_fee_id')
           //->where('af.course_id',$req->course)
           //->where('sds.Semester',$req->semester)
          	
           ->where('af.session_id',session::get('activeSession'))
           ->where('sds.session_id',session::get('activeSession'))
           ->where('af.branch_id',session::get('activeBranch'))
           ->where(function($q) use($req){
           	 if($req->student !=''){
           	 	 $q->where('collect_fee.student_id','=',$req->student);
           	 }
           	 if($req->course !=0){
           	 	 $q->where('af.course_id','=',$req->course);
           	 }
           	  if($req->semester !=0){
           	 	 $q->where('sds.Semester','=',$req->semester);
           	 }

           	 if($req->from !='' && $req->to !=''){
           	 	//dd("both");
           	 	 $q->whereBetween('collect_fee.reciept_date',[$req->from.' 00:00:00',$req->to.' 23:59:59']);
           	 }
           	  
           })
           ->where('collect_fee.status','=',1)
           ->where('sds.active_status','=',1)
           ->where('af.status','=',1)
          ->selectRAW('SUM(collect_fee.amount_paid) as paid')
           ->groupBy('collect_fee.reciept_no')
           ->get();
           //dd($collect);
           $head=[];
            foreach($collect as $reciept_k=>$receipt_v){
	           $head[$receipt_v->reciept_no]= DB::table('collect_fee')->select('collect_fee.id','fee_heads.fee_head_title','collect_fee.amount_paid','fee_heads.id')
	         
			 	->leftjoin('assign_fee as af','af.id','collect_fee.assign_fee_id')
			 	->leftjoin('fee_heads','fee_heads.id','=','af.fee_head_id')
			 	 ->leftjoin('student_detail_sessionwise as sds',function($j){
			 	     $j->on('sds.student_id','=','collect_fee.student_id')
			 	     ->where('sds.session_id',Session::get('activeSession'));
			 	 })
			 	 ->where(function($q) use($req){
			 	    if($req->from !='' && $req->to !=''){
           	 
           	 	 		$q->whereBetween('collect_fee.reciept_date',[$req->from.' 00:00:00',$req->to.' 23:59:59']);
           			 }
           			 if($req->student !=''){
		           	 	 $q->where('collect_fee.student_id','=',$req->student);
		           	 }
		           	 if($req->course !=0){
		           	 	 $q->where('af.course_id','=',$req->course);
		           	 }
		           	  if($req->semester !=0){
		           	 	 $q->where('sds.Semester','=',$req->semester);
		           	 }
			 	  })
			 	 ->where('af.session_id',session::get('activeSession'))
                 ->where('af.branch_id',session::get('activeBranch'))
			 	->where('reciept_no',$receipt_v->reciept_no)
			 	->where('af.status',1)
			 	->where('fee_heads.status',1)
			 	->selectRAW('SUM(collect_fee.amount_paid) as headpaid')
                ->groupBy('af.fee_head_id')
			 	->get();
			 
                                    
            }
             	$head_master=[]; 

			 	foreach($head as $receipt_no =>$receipt_collection){
			 		foreach($receipt_collection as $head_k=>$head_v){
                        
                          $head_master[$head_v->id]['paid']= !isset( $head_master[$head_v->id]['paid'])?0: $head_master[$head_v->id]['paid'];
			 		    
			 			 $head_master[$head_v->id]['head']=   $head_v->fee_head_title;
			 			 $head_master[$head_v->id]['paid']+=   $head_v->headpaid;
			 			 
			 			 
			 			 /*$head_master[$head_v->id]['paid']=$head_master[$head_v->id]['paid']+$head_v->headpaid; */
			 		}
			 	}
			 	//dd($head_master);
             return view('Fee.student_collection_print', compact('collect','branch','search_criteria','head','head_master'));
			}
			else{
				return view('Fee.student_collection_report', compact('data','search_criteria'));
		    }
	}
	public function student_collection_report(Request $req)
	{
		$data['course_list']=Faculty::where('status', '1')->where('branch_id', session('activeBranch'))->pluck('id', 'faculty');
		$data['fee_list']=DB::table('fee_heads')->where('status', '1')->select('id', 'fee_head_title')->get();
		
		$data['months_list']=DB::table('months')->select('title','id')->where('status',1)->pluck('title','id')->toArray();
		$data['student_list'] = array();
		$data['semester_list']=DB::table('semesters')->select('id', 'semester')->get();
		$branch= DB::table('branches')->select('*')->where('id',Session::get('activeBranch'))->first();
          if($req->all()){
             //dd($req->all(),session::get('activeBranch'));
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

			if($req->from){
				$search_criteria['From']=Carbon::parse($req->from)->format('d-m-Y'); 
			}
			if($req->to ){
				$search_criteria['To']=Carbon::parse($req->to)->format('d-m-Y'); 
			}
           // dd($search_criteria);
          	$session= DB::table('session')->orderBy('id','desc')->pluck('session_name','id')
          	->toArray();
            $collect= DB::table('collect_fee')->select('collect_fee.*','s.first_name','s.middle_name','s.last_name','s.reg_no','pd.father_first_name as father_name')
            ->leftjoin('students as s','s.id','=','collect_fee.student_id')
            ->leftjoin('parent_details as pd','pd.students_id','=','collect_fee.student_id')
            ->leftjoin('student_detail_sessionwise as sds',function($j){
			 	 	$j->on('sds.student_id','=','collect_fee.student_id')
			 	 	->where('sds.active_status','=',1)
			 	 	->where('sds.session_id',Session::get('activeSession'));
			 	 })
           ->leftjoin('assign_fee as af','af.id','=','collect_fee.assign_fee_id')
           //->where('af.course_id',$req->course)
           //->where('sds.Semester',$req->semester)
          	
           //->where('af.session_id',session::get('activeSession'))
           // ->where('sds.session_id',session::get('activeSession'))
           ->where('af.branch_id',session::get('activeBranch'))
           ->where(function($q) use($req){
           	 if($req->student !=''){
           	 	 $q->where('collect_fee.student_id','=',$req->student);
           	 }
           	 if($req->course !=0){
           	 	 $q->where('sds.course_id','=',$req->course);
           	 }
           	  if($req->semester !=0){
           	 	 $q->where('sds.Semester','=',$req->semester);
           	 }

           	 if($req->from !='' && $req->to !=''){
           	 	//dd("both");
           	 	 $q->whereBetween('collect_fee.reciept_date',[$req->from.' 00:00:00',$req->to.' 23:59:59']);
           	 }
           	  
           })
           ->where('collect_fee.status','=',1)
           
           ->where('af.status','=',1)
          ->selectRAW('SUM(collect_fee.amount_paid) as paid')
           ->groupBy('collect_fee.reciept_no')
           ->get();
           // dd($collect);
           $head=[];
            foreach($collect as $reciept_k=>$receipt_v){
	           $head[$receipt_v->reciept_no]= DB::table('collect_fee')->select('collect_fee.id','fee_heads.fee_head_title','collect_fee.amount_paid','fee_heads.id','af.session_id')
	         
			 	->leftjoin('assign_fee as af','af.id','collect_fee.assign_fee_id')
			 	->leftjoin('fee_heads','fee_heads.id','=','af.fee_head_id')
			 	 ->leftjoin('student_detail_sessionwise as sds',function($j){
			 	 	$j->on('sds.student_id','=','collect_fee.student_id')
			 	 	->where('sds.session_id',Session::get('activeSession'));
			 	 })
			 	 ->where(function($q) use($req){
			 	    if($req->from !='' && $req->to !=''){
           	 
           	 	 		$q->whereBetween('collect_fee.reciept_date',[$req->from.' 00:00:00',$req->to.' 23:59:59']);
           			 }
           			 if($req->student !=''){
		           	 	 $q->where('collect_fee.student_id','=',$req->student);
		           	 }
		           	 if($req->course !=0){
		           	 	 $q->where('sds.course_id','=',$req->course);
		           	 }
		           	  if($req->semester !=0){
		           	 	 $q->where('sds.Semester','=',$req->semester);
		           	 }
			 	  })
			 	 //->where('af.session_id',session::get('activeSession'))
                 ->where('af.branch_id',session::get('activeBranch'))
			 	->where('reciept_no',$receipt_v->reciept_no)
			 	->where('af.status',1)
			 	->where('collect_fee.status',1)
			 	->where('fee_heads.status',1)
			 	->selectRAW('SUM(collect_fee.amount_paid) as headpaid')
                ->groupBy('af.fee_head_id')
			 	->get();
			 
                                    
            }
            // dd($collect,$head);
            
             	$head_master=[]; 

			 	foreach($head as $receipt_no =>$receipt_collection){
			 		foreach($receipt_collection as $head_k=>$head_v){
                        
                          $head_master[$head_v->id][$head_v->session_id]['paid']= !isset( $head_master[$head_v->id][$head_v->session_id]['paid'])?0: $head_master[$head_v->id][$head_v->session_id]['paid'];
			 		    
			 			 $head_master[$head_v->id][$head_v->session_id]['head']=   $head_v->fee_head_title;
			 			 $head_master[$head_v->id][$head_v->session_id]['paid']+=   $head_v->headpaid;
			 			 
			 			 
			 			 /*$head_master[$head_v->id]['paid']=$head_master[$head_v->id]['paid']+$head_v->headpaid; */
			 		}
			 	}
			 	
             return view('Fee.student_collection_print', compact('collect','branch','search_criteria','head','head_master','session'));
			}
			else{
				return view('Fee.student_collection_report', compact('data','search_criteria'));
		    }
	}
	
	public function bulkEditCollection(Request $request)
	{
		$branch_id= session::get('activeBranch');
		$session_id= session::get('activeSession');
		$data=[];
		$data['faculty']=Faculty::select('id', 'faculty')->where('branch_id', $branch_id)->pluck('faculty', 'id')->toArray();
		$data['faculty']=array_prepend($data['faculty'], "Select ".env("course_label"), "");
		if($request->all()){

		$assignId= $request->assign_fee_id; 
		$faculty_id= $request->faculty; 
		 $search_criteria=[];
		 $head= AssignFee::select('fee_heads.fee_head_title')
		 ->leftjoin('fee_heads','fee_heads.id','=','assign_fee.fee_head_id')
		 ->where('assign_fee.id',$request->assign_fee_id)->first();
		 $search_criteria['Fee Head']= $head->fee_head_title;
          $collection= DB::table('collect_fee')->select('collect_fee.*','s.first_name as name','af.fee_amount')
          ->leftjoin('students as s','s.id','=','collect_fee.student_id')
          ->leftjoin('assign_fee as af','af.id','=','collect_fee.assign_fee_id')
          ->where('collect_fee.assign_fee_id',$request->assign_fee_id)
          ->where('collect_fee.status',1)
          ->where('s.status',1)
          ->orderBy('collect_fee.reciept_no','Desc')
          ->groupby('collect_fee.student_id')
         ->selectRAW("sum(amount_paid) as amount_paid")
		 ->selectRAW("sum(discount) as discount")
		 ->groupBy('collect_fee.student_id')
		  ->orderBy('collect_fee.id','desc')->get();
         
          if(count($collection)>0){

          	return view('Fee.Bulk-Edit-Collection.index', compact('collection','data','search_criteria','assignId','faculty_id')); 
          }
          else{ return redirect()->route('bulkEditCollection')->with('message_danger'," No collection Found. Try Again'."); 

            
          }
          //dd($collection);
           
		}
		else{

		   return view('Fee.Bulk-Edit-Collection.index', compact('data'));
		}

	}
	public function LoadCourseFeeHeads(Request $request)
	{
        $data = [];
        $data['error'] = true;
        if($request->faculty_id  && $request->session_id && $request->branch_id){ 
            $feeheads = AssignFee::Select('assign_fee.*', 'fee_heads.fee_head_title','sub_head.fee_head_title as sub_head','assign_fee.id as assign_fee_id')
		->leftJoin('fee_heads', function($join){
			$join->on('fee_heads.id', '=', 'assign_fee.fee_head_id');
		})
		->leftjoin('fee_heads as fee','fee.id','=','assign_fee.fee_head_id')
		->leftjoin('fee_heads as sub_head','sub_head.id','=','fee.parent_id')
		->where('assign_fee.branch_id', $request->branch_id)
		->where('assign_fee.session_id', $request->session_id)
		->where('assign_fee.course_id', $request->faculty_id)
		->where('assign_fee.status',1)
	    ->groupBy('assign_fee.id')->orderByRaw("FIELD(assign_fee.due_month, '4','5','6','7','8','9','10','11','12','1','2','3') ASC")->get();
            if(count($feeheads)>0){
                $data['error'] = false;
                $data['msg'] = 'Fee Assign Found!!!';
                $data['fees'] = $feeheads;
            
            }else{
                $data['msg'] = 'No Fees Assigned';
            }
        }else{
            $data['msg'] = 'Invalid Request!';
        }

        return response()->JSON(json_encode($data));
    
	}

	public function bulkUpdateCollection(Request $request)
	{
		//dd($request->all());
		$branch_id= session::get('activeBranch');
		$session_id= session::get('activeSession');
		$course_id= $request->faculty_id;
		if($request->student_id){
			$student= $request->student_id;
			 
            foreach($student As $key=>$value){
            	$fee= $request->fee_amount[$key];
	            if($request->discount_type==1){
	            	
	              	
	              	$disc_amount= ($request->discount*$fee)/100;
	            }
	            else{
	            	
	            	$disc_amount=$request->discount;
	            }
	         
	            $collection=$request->amount_paid[$key]+$disc_amount;
	          
	            if($collection>$fee){
                 $collection_data= DB::table('collect_fee')->select('*')
                 ->where('student_id',$key)
                 ->where('assign_fee_id',$request->assign_fee_id)
                 ->orderBy('reciept_no','Desc')
                 ->where('status',1)->get();
                 //dd($collection_data);
	                if($collection_data){
	                	foreach ($collection_data as $coll_key => $coll_value) {
	                		$d=!empty($coll_value->discount)?$coll_value->discount:0;
	                		$ap=!empty($coll_value->amount_paid)?$coll_value->amount_paid:0;
                            
	                		$coll_paid= $ap+$d;
	                		if($coll_paid>$disc_amount){
		                         $update_collection= DB::table('collect_fee')->where('id',$coll_value->id)->update([
		                         'amount_paid'   =>$coll_value->amount_paid-$disc_amount,
		                         'discount'      =>$disc_amount,
		                         'discount_type' =>$request->discount_type,
			                    ]);
		                        if($update_collection && $disc_amount>0){
			                        $excess_amount= $disc_amount;
				                    $assign_ids=$this->GetStudentAssignFee($key,$course_id,$session_id,$branch_id);
				                    foreach ($assign_ids as $assign_key => $assign_value) {
				               	     $paid_result=DB::table('collect_fee')->where('assign_fee_id', $assign_value->id)->where('student_id', $key)
									  ->Where('status' , 1)->sum('amount_paid');
									
									 $discount_result=DB::table('collect_fee')->where('assign_fee_id', $assign_value->id)->where('student_id', $key)
									 ->Where('status' , 1)->sum('discount');
									 $paid_result=$paid_result + $discount_result;
									 $due = $assign_value->fee_amount - $paid_result;
						                $paid[$assign_value->id]= $paid_result;
						                $fee[$assign_value->id]= $assign_value->fee_amount;
						                $duearr[$assign_value->id]= $due;
					                	if($due>0){
					                	 $latest_collection_data= DB::table('collect_fee')->select('*')
					                	 ->where('student_id',$key)
					                	 ->orderby('reciept_no','Desc')
					                	 ->where('status',1)
					                	 ->first();
										// dd($due,$amount_paid);
											if($latest_collection_data){
												$data['remarks']=$latest_collection_data->remarks;
							                  	$data['payment_type']=$latest_collection_data->payment_type;
							                  	$data['created_at']=date('Y-m-d');
							                  	$data['created_by']=Auth::user()->id;
							                  	$data['reference']=($latest_collection_data->reference) ? $latest_collection_data->reference : "-";
							                 	$data['reciept_date']=$latest_collection_data->reciept_date;
							                  	$data['assign_fee_id']=$assign_value->id;
							                  	$data['student_id']=$key;
							                  	$data['reciept_no']=$latest_collection_data->reciept_no;
							                    if($disc_amount>=$due){

								                 	$data['amount_paid']=$due;
								                 	$excess_amount= $disc_amount-$due;
							             			$disc_amount= $excess_amount;
								                }
								                elseif($disc_amount<$due){
								                	
									                 $data['amount_paid']=$disc_amount;
									                 $disc_amount=$disc_amount - $disc_amount;
								                }
								                if($data['amount_paid']>0){
									   	    	    $receipt_id[]=DB::table('collect_fee')->insertGetId($data);
										   	    }
											}   
										}
				                    }
				                }
	                		}
	                		
	                	}
	                }
	                
			    
	            }
	            else{
	            	$col_id=DB::table('collect_fee')->where('student_id',$key)->select('id')
	                 ->where('assign_fee_id',$request->assign_fee_id)
	                 ->orderBy('reciept_no','Desc')
	                 ->where('status',1)->first();
	                    if($col_id){
	                 	 $update_collection= DB::table('collect_fee')->where('id',$col_id->id)->update([
                           'discount'      =>$disc_amount,
                          'discount_type' =>$request->discount_type,
                        ]);
	                 }
	            	
	            }
	           

                
            }
			 
		return redirect()->route('bulkEditCollection')->with('message_success',"Collection Updated Successfully!!!"); 
		}
		else{
			return redirect()->route('bulkEditCollection')->with('message_danger',"please Select at Least One student !!!");
		}
	}
	public function GetStudentAssignFee($studentid,$courseid,$sessionId,$branchId){
		$fee_result=AssignFee::Select('assign_fee.*', 'fee_heads.fee_head_title','sub_head.fee_head_title as sub_head','assign_fee.id as assign_fee_id')
		->leftJoin('fee_heads', function($join){
			$join->on('fee_heads.id', '=', 'assign_fee.fee_head_id');
		})
		->leftjoin('fee_heads as fee','fee.id','=','assign_fee.fee_head_id')
		->leftjoin('fee_heads as sub_head','sub_head.id','=','fee.parent_id')
		->where('assign_fee.branch_id',$branchId)
		->where('assign_fee.session_id', $sessionId)
		->where('assign_fee.course_id', $courseid)
		->where('assign_fee.status',1)
		->Where('assign_fee.student_id', '0')
		//->whereNotIn('assign_fee.id',$assign_id)
		->orWhere(function($q) use ($studentid,$courseid,$sessionId){
			if($studentid){
				$q->orWhere('assign_fee.student_id', $studentid)
				->where('assign_fee.session_id', $sessionId)
				->where('assign_fee.status',1)
				->where('assign_fee.course_id', $courseid);
			}
		})->groupBy('assign_fee.id')->orderByRaw("FIELD(assign_fee.due_month, '4','5','6','7','8','9','10','11','12','1','2','3') ASC")
		->get();

		return $fee_result;
	}
}

