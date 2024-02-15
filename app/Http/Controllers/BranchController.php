<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Session, Auth,DB; use App\Branch; use App\PaymentType;
use  Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\BranchBatch;

class BranchController extends CollegeBaseController{
	
    protected $panel = 'Branch';
    
    public function __contstruct(){
		$this->midleware('auth');
	}

/* (branch_descriptiojn) not defined by me(Grav), didnt made chanGes as it may after effect the entire app */


	public function index(Request $req, $edt=""){
		$panel = $this->panel;	$db=[];
		$flds=['id'=>'', 'branch_name'=>'Branch Name', 'branch_title'=>'Title', 'branch_manager'=>'Branch Manager', 'branch_email'=>'Email-Id', 'branch_mobile'=> 'Contact', 'branch_address'=>'Address', 'branch_descriptiojn'=>'Description','is_course_batch'=>'Is Course Batch'];
		$data['tbl']=Branch::orderBy('id', 'desc')->get();
		if($edt){
			$db=Branch::where('id', $edt)->get()->first()->toArray();
		}
		return view('branch', compact('view_path', 'panel','flds', 'data', 'edt', 'db'));
	}

	public function branch_ops(Request $req){
		$rules=[
			'branch_title'=>'required'
			, 'branch_email'=>'required|email'
			, 'branch_mobile'=>'required|min:10'
			, 'branch_address'=>'required'
			, 'is_course_batch' => 'required'

		];
		if(!$req->id){
			$rules['branch_name']='required|unique:branches|max:111';
		}
		$valid = Validator::make($req->all(), $rules);
		if($valid->fails()){
			return back()->withErrors($valid)->withInput();
		}else{
			if($req->id){
				$qry=Branch::where('id', $req->id)->update($req->except(['_token', 'submit', 'edt']));
				$msg=$this->panel." Record Updated Successfully.";
			}else{
				$qry=Branch::create($req->all());
				$msg=$this->panel." Record Inserted Successfully.";
			}
			if($qry){ Session::flash('message_success', $msg); return redirect('branch'); }
		}
	}

	public function payment_type(Request $req, $edt=""){
		$panel = "Payment Type";	$db=[];
		$flds=['id'=>'', 'type_name'=>'Payment Name'];
		$data['tbl']=PaymentType::orderBy('id', 'desc')->get();
		if($edt){
			$db=PaymentType::where('id', $edt)->get()->first()->toArray();
		}
		return view('pay_type', compact('flds', 'data', 'db', 'panel'));
	}

	public function payment_ops(Request $req){
		$this->panel="Payment Type";
		if(!$req->id){
			$rules=['type_name'=>'required|unique:payment_type|max:33'];
		}else{ $rules=['type_name'=>'required|max:33']; }
		
		$valid = Validator::make($req->all(), $rules);
		if($valid->fails()){
			return back()->withErrors($valid)->withInput();
		}else{
			if($req->id){
				$qry=PaymentType::where('id', $req->id)->update($req->except(['_token', 'submit', 'edt']));
				$msg=$this->panel." Record Updated Successfully.";
			}else{
				$qry=PaymentType::create($req->all());
				$msg=$this->panel." Record Inserted Successfully.";
			}
			if($qry){
				Session::flash('message_success', $msg);
				return redirect('payment_type');
			}
		}
	}
	public function batch_index(Request $request,$id = ""){
		$panel = 'Is Course Batch';
		if($id){
			$data['row']=BranchBatch::Find($id);
			if(!$data['row']){
				return redirect()->route('branch_batchwise')->with('message_warning','Invalid Request');
			}
		}
		$data['records'] = BranchBatch::select('sessionwise_branch_batch.id','sessionwise_branch_batch.is_course_batch','session.session_name','branches.branch_name')
			->leftjoin('session','session.id','=','sessionwise_branch_batch.session_id')
			->leftjoin('branches','branches.id','=','sessionwise_branch_batch.branch_id')
			->get();
		$data['session'] = DB::table('session')->select('session_name','id')->where('status',1)->pluck('session_name','id')->toArray();
		$data['session'] = array_prepend($data['session'],'--Select Session--','');

		$data['branch'] = DB::table('branches')->select('branch_name','id')->where('record_status',1)->pluck('branch_name','id')->toArray();
		$data['branch'] = array_prepend($data['branch'],'--Select Branch--','');
		if($request->all()){
			$rules = [
				'branch_id' => 'required',
				'session_id' => 'required' ,
				'is_course_batch'	=> 'required' ,
			];
			$msg = [
				'branch_id.required' => 'Please Select Branch',
				'session_id.required' => 'Please Select Session' ,
				'is_course_batch.required'	=> 'Please Select Is Course Batch' ,
			];
			$this->Validate($request,$rules,$msg);
				$exist = BranchBatch::where([
					['branch_id','=',$request->branch_id],
					['session_id','=',$request->session_id]
				])->first();
				$insert = BranchBatch::updateOrInsert(
			    [
			        'session_id' => $request->session_id,
			        'branch_id' => $request->branch_id,
			        
			    ],
			    [
			        'session_id' => $request->session_id,
			        'branch_id' => $request->branch_id,
			        'is_course_batch' => $request->is_course_batch,
			        'created_at'	=>Carbon::now(),
			        'created_by'	=> auth()->user()->id
			    ]
				);
			if($insert){
					return redirect()->route('branch_batchwise')->with('message_success','Record Added');
			}		
			else{	
				return redirect()->route('branch_batchwise')->with('message_warning','Somenthing Went Wrong');
			}
		}
		return view('batchwise_branch',compact('data','panel','id'));
	}
	

}
?>