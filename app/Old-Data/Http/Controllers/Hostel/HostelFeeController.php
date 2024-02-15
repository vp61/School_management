<?php

namespace App\Http\Controllers\Hostel;
use App\Http\Controllers\CollegeBaseController;
use Illuminate\Http\Request;
use App\Http\Requests\Transport\User\AddValidation;
use App\Http\Requests\Transport\User\EditValidation;
use App\Models\Faculty;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Resident;
use URL,DB,Session;
use Carbon\Carbon;

class HostelFeeController extends CollegeBaseController
{
    protected $base_route = 'hostel.fee';
    protected $view_path = 'hostel.fee';
    protected $panel = 'Hostel Fees';
    protected $filter_query = [];
    public function __construct()
    {

    }

    public function index(Request $request)
    {
       $data['course']= Faculty::select('id','faculty')->where('branch_id',Session::get('activeBranch'))->pluck('faculty','id')->toArray();
        $data['course'] = array_prepend($data['course'],'--Select Course--'," ");

       $data['section']=Faculty::select('semesters.id','semesters.semester')
        ->where('faculties.branch_id',Session::get('activeBranch'))
        ->distinct('semesters.id')
        ->leftjoin('faculty_semester','faculty_semester.faculty_id','=','faculties.id')
        ->leftjoin('semesters','semesters.id','=','faculty_semester.semester_id')
        ->orderBy('semester','ASC')
        ->pluck('semesters.semester','semesters.id')
        ->toArray();
        $data['section']=array_prepend($data['section'],"--Select Section--","");
        $data['staff']=Staff::select('id',DB::raw("CONCAT(first_name,' ',last_name) as name"))->where('branch_id',Session::get('activeBranch'))->pluck('name','id')->toArray();
         $data['staff']=array_prepend($data['staff'],"--Select Staff--","");

        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;
        $data['session']=Session::get('activeSession');
        $data['branch']=Session::get('activeBranch');
         $data['pay_type'] = DB::table('payment_type')->select('type_name')->where('status', '1')->pluck('type_name','type_name')->toArray(); 
      
        $data['pay_type'] = array_prepend($data['pay_type'], "--Payment Mode--", "");

     return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }
    public function store(Request $request){
       $memType=$request->member_type;
       $memId=$request->memberId;
       $cdate=Carbon::now();

       foreach ($request->amount as $key => $value) {
          foreach ($request->remark as $rkey => $rvalue) {
             if($rkey==$key){
                if($value>0){
                   $incId=DB::table('hostel_collect_fees')->insertGetId([
                    'member_id'           => $memId,
                    'member_type'         => $memType,
                    'resident_id'         => $key,
                    'amount_paid'         => $value,
                    'receipt_date'          => $request->reciept_date,
                    'remark'              => $rvalue,
                    'pay_mode'            => $request->pay_mode,
                    'session_id'          => Session::get('activeSession'),
                    'branch_id'           => Session::get('activeBranch'),
                    'receipt_by'          => auth()->user()->id, 
                    'ref_no'              => $request->ref_no,   
                ]);
                   $getno=1000+$incId;
                  $rcp='AESHS'.$getno;
                 
                 $data['update']=DB::table('hostel_collect_fees')
                 ->where('id',$incId)
                 ->update(['receipt_no'=>$rcp]);
               }
                 $request->session()->flash($this->message_success, $this->panel. ' Collected Successfully.');
                
             }
          }
       }
       return redirect('hostel/print/'.$rcp);
    }
     public function collectReceipt($id=""){
        $data=[];
        $type=DB::table('hostel_collect_fees')->select('member_type','member_id','resident_id')->where('receipt_no',$id)
        ->get();
        $tuId=$type[0]->resident_id;
       // dd($type[0]->transport_user_id);
       if($type[0]->member_type==1){ 
            $data=DB::table('hostel_collect_fees')->select('br.branch_name','br.branch_logo','br.branch_mobile','br.branch_email','br.branch_address','receipt_no','hostel_collect_fees.receipt_date','pay_mode','amount_paid','sd.first_name','sd.reg_no','users.name','rd.rent','ref_no')
            ->where([
                ['hostel_collect_fees.receipt_no','=',$id],
            ])
            ->leftjoin('students as sd','sd.id','=','hostel_collect_fees.member_id')
            ->leftJoin('branches as br', 'br.id', '=', 'hostel_collect_fees.branch_id')
            ->leftjoin('users','users.id','=','hostel_collect_fees.receipt_by')
            ->leftjoin('residents as rd','rd.id','=','hostel_collect_fees.resident_id')
            ->get();
            $data['paid']=DB::select(DB::raw("SELECT sum(hostel_collect_fees.amount_paid) as total_paid FROM  hostel_collect_fees where resident_id=$tuId"));
       
        }
        if($type[0]->member_type==2){ 
            $data=DB::table('hostel_collect_fees')->select('br.branch_name','br.branch_logo','br.branch_mobile','br.branch_email','br.branch_address','receipt_no','hostel_collect_fees.receipt_date','pay_mode','amount_paid',DB::raw("CONCAT(sf.first_name,' ',sf.last_name)as first_name"),'sf.reg_no','users.name','rd.rent','ref_no')
            ->where([
                ['hostel_collect_fees.receipt_no','=',$id],
            ])
            ->leftjoin('staff as sf','sf.id','=','hostel_collect_fees.member_id')
            ->leftJoin('branches as br', 'br.id', '=', 'hostel_collect_fees.branch_id')
            ->leftjoin('users','users.id','=','hostel_collect_fees.receipt_by')
            ->leftjoin('residents as rd','rd.id','=','hostel_collect_fees.resident_id')
            ->get();
            $data['paid']=DB::select(DB::raw("SELECT sum(hostel_collect_fees.amount_paid) as total_paid FROM  hostel_collect_fees where resident_id=$tuId"));
          
        }
         return view(parent::loadDataToView($this->view_path.'.includes.collectReceipt'), compact('data'));
    }
     public function loadStudent(Request $request){
        $response=[];
        $response['error']=true;
        if($request->has('course') && $request->has('section')){
            $student= Student::select('id','first_name as name')
            ->where([
                ['faculty','=',$request->get('course')],
                ['semester','=',$request->get('section')]
            ])
            ->orderBy('first_name','ASC')
            ->get();
            if($student){
                $response['student']=$student;
                $response['error'] = false;
                $response['success'] = 'Student Found';  
            }
        }
        else{
            $response['message']='invalid Request';
        }
        return response()->json(json_encode($response));
    }
   
   Public function loadFee(Request $request){
        $response=[];
        $response['error']=true;
         $userType=$request->type;
            $memberId=$request->userId;
            $session =$request->session;
            $branch =$request->branch;
        if($request->userId && $request->type){
           
            if($userType==1){
                    $fee=DB::table('residents')->select('rent','residents.id','member_id','user_type')->where([
                        ['member_id','=',$memberId],
                        ['user_type','=',$userType],
                        ['residents.branch_id','=',$branch],
                        ['residents.session_id','=',$session]
                    ])
                    ->leftjoin('students','students.id','=','residents.member_id')
                    ->get();   
            }    
            if($userType==2){
                $fee=DB::table('residents')->select('rent','residents.id','member_id','user_type')->where([
                        ['member_id','=',$memberId],
                        ['user_type','=',$userType],
                        ['residents.branch_id','=',$branch],
                        ['residents.session_id','=',$session]
                    ])
                    ->leftjoin('staff','staff.id','=','residents.member_id')
                    ->get();  
            }
            $pay=DB::table('hostel_collect_fees')->select('amount_paid','pay_mode','receipt_no',DB::raw("DATE_FORMAT(hostel_collect_fees.receipt_date,'%d-%m-%Y') as created_at"),'ref_no')
               ->where([
                  ['hostel_collect_fees.member_id','=',$memberId],
                  ['hostel_collect_fees.member_type','=',$userType],
                  ['hostel_collect_fees.branch_id','=',$branch],
                  ['hostel_collect_fees.session_id','=',$session]
               ])
               ->orderBy('hostel_collect_fees.created_at','asc')
               ->get();
               $paid=[];
                foreach ($fee as $id) {
                  $paid[$id->id]=DB::select(DB::raw("SELECT sum(amount_paid) as total_paid,amount_paid,resident_id from hostel_collect_fees where resident_id='$id->id'")); 
               }
            if($fee){
                $response['fee']=$fee;
                $response['pay']=$pay;
                $response['paid']=$paid;
                $response['error'] = false;
                $response['success'] = 'Student Found';  
            }
        }
        else{
            $response['message']='invalid Request';
        }
        return response()->json(json_encode($response));
    }
}
