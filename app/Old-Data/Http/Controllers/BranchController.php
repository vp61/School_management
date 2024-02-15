<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Session, Auth; use App\Branch; use App\PaymentType;
use  Illuminate\Support\Facades\Validator;

class BranchController extends CollegeBaseController{
	
    protected $panel = 'Branch';
    
    public function __contstruct(){
		$this->midleware('auth');
	}

/* (branch_descriptiojn) not defined by me(Grav), didnt made chanGes as it may after effect the entire app */


	public function index(Request $req, $edt=""){
		$panel = $this->panel;	$db=[];
		$flds=['id'=>'', 'branch_name'=>'Branch Name', 'branch_title'=>'Title', 'branch_manager'=>'Branch Manager', 'branch_email'=>'Email-Id', 'branch_mobile'=> 'Contact', 'branch_address'=>'Address', 'branch_descriptiojn'=>'Description'];
		$data['tbl']=Branch::orderBy('id', 'desc')->get();
		if($edt){
			$db=Branch::where('id', $edt)->get()->first()->toArray();
		}
		return view('branch', compact('view_path', 'panel','flds', 'data', 'edt', 'db'));
	}

	public function branch_ops(Request $req){
		$rules=[
			'branch_title'=>'required'
			, 'branch_manager'=>'required'
			, 'branch_email'=>'required|email'
			, 'branch_mobile'=>'required|size:10'
			, 'branch_address'=>'required'
			, 'branch_descriptiojn'=>'required'
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

}
?>