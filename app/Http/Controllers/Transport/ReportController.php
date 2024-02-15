<?php

namespace App\Http\Controllers\Transport;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CollegeBaseController;
use App\Http\Requests\Transport\User\AddValidation;
use App\Http\Requests\Transport\User\EditValidation;
use App\Models\Route;
use App\Models\TransportUser;
use App\Models\transport_collect_fee;
use App\Models\Staff;
use App\Models\Student; 
use DB,Session;
use URL;
use Carbon;
use Log,Auth;
use App\Models\FeeHead;
use App\AssignFee;
class ReportController extends CollegeBaseController
{
    protected $base_route = 'transport.report';
    protected $view_path = 'transport.report';
    protected $panel = 'Transport Fee Report';
    protected $filter_query = [];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
 
         /*Route List*/
        $routes = Route::select('id','title')->get();
        $map_routes = array_pluck($routes,'title','id');
        $data['routes'] = array_prepend($map_routes,'Select Route...','');

        /*Active Route For Shift List*/
        /*Route List*/
        $routes = Route::select('id','title')->Active()->get();
        $map_routes = array_pluck($routes,'title','id');
        $data['active_routes'] = array_prepend($map_routes,'Select Route...','0');

        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;
        $data['pay_type'] = DB::table('payment_type')->select('type_name')->where('status', '1')->pluck('type_name','type_name')->toArray(); 
      
        $data['pay_type'] = array_prepend($data['pay_type'], "--Payment Mode--", "");
         return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    /*
    public function show(Request $request)
    {
         $data = [];

            $data['user'] = DB::table('transport_collect_fees')->select('transport_collect_fees.id','transport_users.routes_id','transport_users.vehicles_id','amount_paid','ref_no','pay_mode','transport_collect_fees.member_id','transport_collect_fees.member_type','receipt_no','transport_collect_fees.created_at','transport_collect_fees.ref_no','transport_users.total_rent')
            ->leftjoin('transport_users', 'transport_users.id', '=', 'transport_collect_fees.transport_user_id')
            ->orderBy('transport_collect_fees.id','desc')
            ->where(function ($query) use ($request) {
                if (!empty($request->member_type)) {
                    $query->where('member_type', '=', $request->get('member_type'));
                    $this->filter_query['member_type'] = $request->get('member_type');
                 }
                 if (!empty($request->route)) {
                    $query->where('transport_users.routes_id', '=', $request->get('route'));
                    $this->filter_query['route'] = $request->get('route');
                 }

                if (!empty($request->reg_no)) {
                    if($request->has('member_type') == 1){
                            $studentId = $this->getStudentIdByReg($request->reg_no);

                            $query->where('transport_collect_fees.member_id', '=', $studentId);
                            $this->filter_query['member_id'] = $studentId;
                        }
                        if($request->has('member_type') == 2){
                            $staffId = $this->getStaffByReg($request->reg_no);
                            $query->where('transport_collect_fees.member_id', '=', $staffId);
                            $this->filter_query['member_id'] = $staffId;
                        }
                }
               if(!empty($request->from_date) && !empty($request->to_date)){
                    $query->whereBetween('transport_collect_fees.created_at',[$request->from_date." 00:00:00",$request->to_date." 23:59:00"]);
                    $this->filter_query['date']=$request->created_at;
                }
               if(!empty($request->pay_mode)){
                $query->where('pay_mode',$request->pay_mode);
                $this->filter_query['pay_mode']=$request->pay_mode;
                } 
               if (!empty($request->ref_no)) {
                   $query->where('ref_no',$request->ref_no);
                 $this->filter_query['ref']=$request->ref_no;
                } 
               })

               ->get(); 
              
        $routes = Route::select('id','title')->get();
        $map_routes = array_pluck($routes,'title','id');
        $data['routes'] = array_prepend($map_routes,'Select Route...','');

        // Active Route For Shift List
        // Route List
        $routes = Route::select('id','title')->Active()->get();
        $map_routes = array_pluck($routes,'title','id');
        $data['active_routes'] = array_prepend($map_routes,'Select Route...','0');

        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;
        //pay Type
         $data['pay_type'] = DB::table('payment_type')->select('type_name')->where('status', '1')->pluck('type_name','type_name')->toArray(); 
      
        $data['pay_type'] = array_prepend($data['pay_type'], "--Payment Mode--", "");
         return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }
    */
    
    
    public function show(Request $request)
    {
         $data = [];
           $transport_master= env('Transport_Master');
           $branch_id= session::get('activeBranch');
           $session_id= session::get('activeSession');

           if($transport_master==1 && $request->member_type==1){
            $request->session()->flash($this->message_warning,' Student Collection Can Not be search here!!!.');
            return back();
           }
            $data['user'] = DB::table('transport_collect_fees')->select('transport_collect_fees.id','transport_users.routes_id','transport_users.vehicles_id','amount_paid','ref_no','pay_mode','transport_collect_fees.member_id','transport_collect_fees.member_type','receipt_no','transport_collect_fees.created_at','transport_collect_fees.ref_no','transport_users.total_rent')
            ->leftjoin('transport_users', 'transport_users.id', '=', 'transport_collect_fees.transport_user_id')
            ->orderBy('transport_collect_fees.id','desc')
            ->where(function ($query) use ($request,$transport_master) {
                if (!empty($request->member_type)) {
                    $query->where('member_type', '=', $request->get('member_type'));
                    $this->filter_query['member_type'] = $request->get('member_type');
                 }
                 if (!empty($request->route)) {
                    $query->where('transport_users.routes_id', '=', $request->get('route'));
                    $this->filter_query['route'] = $request->get('route');
                 }

                if (!empty($request->reg_no)) {
                    if($request->has('member_type') == 1 && $transport_master==2){
                            $studentId = $this->getStudentIdByReg($request->reg_no);

                            $query->where('transport_collect_fees.member_id', '=', $studentId);
                            $this->filter_query['member_id'] = $studentId;
                        }
                        if($request->has('member_type') == 2){
                            $staffId = $this->getStaffByReg($request->reg_no);
                            $query->where('transport_collect_fees.member_id', '=', $staffId);
                            $this->filter_query['member_id'] = $staffId;
                        }
                }
               if(!empty($request->from_date) && !empty($request->to_date)){
                    $query->whereBetween('transport_collect_fees.created_at',[$request->from_date." 00:00:00",$request->to_date." 23:59:00"]);
                    $this->filter_query['date']=$request->created_at;
                }
               if(!empty($request->pay_mode)){
                $query->where('pay_mode',$request->pay_mode);
                $this->filter_query['pay_mode']=$request->pay_mode;
                } 
               if (!empty($request->ref_no)) {
                   $query->where('ref_no',$request->ref_no);
                 $this->filter_query['ref']=$request->ref_no;
                } 
               })
             ->selectRaw('SUM(transport_collect_fees.amount_paid) as amount_paid')
             ->groupBY('transport_collect_fees.receipt_no')
            ->where('transport_users.branch',$branch_id)
            ->where('transport_users.session',$session_id)
            ->where('transport_users.status',1)
                    
               ->get(); 
            
              
        $routes = Route::select('id','title')->get();
        $map_routes = array_pluck($routes,'title','id');
        $data['routes'] = array_prepend($map_routes,'Select Route...','');

        /*Active Route For Shift List*/
        /*Route List*/
        $routes = Route::select('id','title')->Active()->get();
        $map_routes = array_pluck($routes,'title','id');
        $data['active_routes'] = array_prepend($map_routes,'Select Route...','0');

        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;
        //pay Type
         $data['pay_type'] = DB::table('payment_type')->select('type_name')->where('status', '1')->pluck('type_name','type_name')->toArray(); 
      
        $data['pay_type'] = array_prepend($data['pay_type'], "--Payment Mode--", "");
         return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }
    
    
    /*
    public function collectReceipt($id=""){
        $data=[];
        $type=DB::table('transport_collect_fees')->select('member_type','member_id','transport_user_id')->where('receipt_no',$id)
        ->get();
        $tuId=$type[0]->transport_user_id;
       // dd($type[0]->transport_user_id);
       if($type[0]->member_type==1){ 
            $data=DB::table('transport_collect_fees')->select('br.branch_name','br.branch_logo','br.branch_mobile','br.branch_email','br.branch_address','receipt_no','transport_collect_fees.created_at as receipt_date','pay_mode','amount_paid','sd.first_name','sd.reg_no','users.name','tu.total_rent','ref_no')
            ->where([
                ['transport_collect_fees.receipt_no','=',$id],
            ])
            ->leftjoin('students as sd','sd.id','=','transport_collect_fees.member_id')
            ->leftJoin('branches as br', 'br.id', '=', 'transport_collect_fees.branch_id')
            ->leftjoin('users','users.id','=','transport_collect_fees.receipt_by')
            ->leftjoin('transport_users as tu','tu.id','=','transport_collect_fees.transport_user_id')
            ->get();
            $data['paid']=DB::select(DB::raw("SELECT sum(transport_collect_fees.amount_paid) as total_paid FROM  transport_collect_fees where transport_user_id=$tuId"));
       
        }
        if($type[0]->member_type==2){ 
            $data=DB::table('transport_collect_fees')->select('br.branch_name','br.branch_logo','br.branch_mobile','br.branch_email','br.branch_address','receipt_no','transport_collect_fees.created_at as receipt_date','pay_mode','amount_paid',DB::raw("CONCAT(sf.first_name,' ',sf.middle_name,' ',sf.last_name)as first_name"),'sf.reg_no','users.name','tu.total_rent','ref_no')
            ->where([
                ['transport_collect_fees.receipt_no','=',$id],
            ])
            ->leftjoin('staff as sf','sf.id','=','transport_collect_fees.member_id')
            ->leftJoin('branches as br', 'br.id', '=', 'transport_collect_fees.branch_id')
            ->leftjoin('users','users.id','=','transport_collect_fees.receipt_by')
            ->leftjoin('transport_users as tu','tu.id','=','transport_collect_fees.transport_user_id')
            ->get();
            $data['paid']=DB::select(DB::raw("SELECT sum(transport_collect_fees.amount_paid) as total_paid FROM  transport_collect_fees where transport_user_id=$tuId"));
          
        }
         return view(parent::loadDataToView($this->view_path.'.includes.collectReceipt'), compact('data'));
    }
    */
    
    
    public function collectReceipt($id=""){
        $data=[];
        $receipt_data=[];
        $type=DB::table('transport_collect_fees')->select('member_type','member_id','transport_user_id')->where('receipt_no',$id)
        ->get();

      
        foreach ($type as $collect_key => $collect_value) {
            $tuId=$collect_value->transport_user_id;
             
            if($collect_value->member_type==1){ 
                $data[$tuId]=DB::table('transport_collect_fees')->select('transport_collect_fees.id','br.branch_name','br.branch_logo','br.branch_mobile','br.branch_email','br.branch_address','receipt_no','transport_collect_fees.created_at as receipt_date','pay_mode','amount_paid','sd.first_name','sd.reg_no','users.name','tu.total_rent','ref_no','tu.from_date','tu.to_date')
                ->where([
                    ['transport_collect_fees.receipt_no','=',$id],
                ])
                ->leftjoin('students as sd','sd.id','=','transport_collect_fees.member_id')
                ->leftJoin('branches as br', 'br.id', '=', 'transport_collect_fees.branch_id')
                ->leftjoin('users','users.id','=','transport_collect_fees.receipt_by')
                ->leftjoin('transport_users as tu','tu.id','=','transport_collect_fees.transport_user_id')
                ->selectRaw('SUM(amount_paid) as total_paid')
                ->where('transport_user_id',$tuId)
                ->where('transport_collect_fees.status',1)
                ->first();
                $receipt_data['first_name']=$data[$tuId]->first_name;          
                $receipt_data['reg_no']=$data[$tuId]->reg_no;          
                $receipt_data['receipt_no']=$data[$tuId]->receipt_no;          
                $receipt_data['pay_mode']=$data[$tuId]->pay_mode;          
                $receipt_data['branch_name']=$data[$tuId]->branch_name;          
                $receipt_data['branch_logo']=$data[$tuId]->branch_logo;          
                $receipt_data['branch_mobile']=$data[$tuId]->branch_mobile;          
                $receipt_data['branch_email']=$data[$tuId]->branch_email;          
                $receipt_data['branch_address']=$data[$tuId]->branch_address;
                $receipt_data['name']=$data[$tuId]->name;
                $receipt_data['receipt_date']=$data[$tuId]->receipt_date;                
                $receipt_data['ref_no']=$data[$tuId]->ref_no;                
       
           }
          if($collect_value->member_type==2){ 
            $data[$tuId]=DB::table('transport_collect_fees')->select('transport_collect_fees.id','br.branch_name','br.branch_logo','br.branch_mobile','br.branch_email','br.branch_address','receipt_no','transport_collect_fees.created_at as receipt_date','pay_mode','amount_paid',DB::raw("CONCAT(sf.first_name,' ',sf.middle_name,' ',sf.last_name)as first_name"),'sf.reg_no','users.name','tu.total_rent','ref_no','tu.from_date','tu.to_date')
            ->where([
                ['transport_collect_fees.receipt_no','=',$id],
            ])
            ->leftjoin('staff as sf','sf.id','=','transport_collect_fees.member_id')
            ->leftJoin('branches as br', 'br.id', '=', 'transport_collect_fees.branch_id')
            ->leftjoin('users','users.id','=','transport_collect_fees.receipt_by')
            ->leftjoin('transport_users as tu','tu.id','=','transport_collect_fees.transport_user_id')
            ->selectRaw('SUM(amount_paid) as total_paid')
            ->where('transport_user_id',$tuId)
            ->where('transport_collect_fees.status',1)
            ->first();  
            $receipt_data['first_name']=$data[$tuId]->first_name;          
            $receipt_data['reg_no']=$data[$tuId]->reg_no;          
            $receipt_data['receipt_no']=$data[$tuId]->receipt_no;          
            $receipt_data['pay_mode']=$data[$tuId]->pay_mode;
            $receipt_data['branch_name']=$data[$tuId]->branch_name;          
            $receipt_data['branch_logo']=$data[$tuId]->branch_logo;          
            $receipt_data['branch_mobile']=$data[$tuId]->branch_mobile;          
            $receipt_data['branch_email']=$data[$tuId]->branch_email;          
            $receipt_data['branch_address']=$data[$tuId]->branch_address;   
            $receipt_data['name']=$data[$tuId]->name;   
            $receipt_data['receipt_date']=$data[$tuId]->receipt_date;
            $receipt_data['ref_no']=$data[$tuId]->ref_no;      
       

          
           }
            
        }
       
            
         return view(parent::loadDataToView($this->view_path.'.includes.collectReceipt'), compact('data','receipt_data'));
    }
    
    
    public function dueReport(){
        $routes = Route::select('id','title')->get();
        $map_routes = array_pluck($routes,'title','id');
        $data['routes'] = array_prepend($map_routes,'Select Route...','');

        /*Active Route For Shift List*/
        /*Route List*/
        $routes = Route::select('id','title')->Active()->get();
        $map_routes = array_pluck($routes,'title','id');
        $data['active_routes'] = array_prepend($map_routes,'Select Route...','0');

        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;
        $data['pay_type'] = DB::table('payment_type')->select('type_name')->where('status', '1')->pluck('type_name','type_name')->toArray(); 
      
        $data['pay_type'] = array_prepend($data['pay_type'], "--Payment Mode--", "");
         return view(parent::loadDataToView($this->view_path.'.due'), compact('data'));
    }
    /*
    public function dueShow(Request $request){
        $userType = $request->member_type;
        $regNo = $request->reg_no;
        $route =$request->route;

        if(!empty($request->reg_no)){
            switch ($userType){
                case 1:
                    $id = Student::where('reg_no','=',$request->reg_no)->first();
                    break;
                case 2:
                    $id = Staff::where('reg_no','=',$request->reg_no)->first();
                    break;
                default:
                    $request->session()->flash($this->message_warning,' User Type is not Valid.');
                    return back();
            }
            if(empty($id)){
                $request->session()->flash($this->message_warning,' Registration number is not Valid.');
                    return back();
            }
        }

        $data = [];
        $data['user'] = DB::table('transport_users')->select('transport_users.id','user_type', 'member_id', 'status','total_rent','duration','br.branch_name','br.branch_logo','br.branch_mobile','br.branch_email','br.branch_address','from_date','to_date')
         ->leftJoin('branches as br', 'br.id', '=', 'transport_users.branch')
            ->where(function ($query) use ($request) {

                if (!empty($request->member_type)) {
                    $query->where('user_type', '=', $request->member_type);
                    $this->filter_query['user_type'] = $request->member_type;
                }
                if(!empty($request->route)){

                    $query->where('routes_id','=',$request->route);
                    $this->filter_query['routes_id'] = $request->route;
                }
                if(!empty($request->reg_no)){
                    if($request->member_type==1)
                        $Id = $this->getStudentIdByReg($request->reg_no);
                    else
                     $Id = $this->getStaffByReg($request->reg_no);
                    $query->where('member_id','=',$Id);
                    $this->filter_query['member_id'] = $Id;
                }
             })
             ->get(); 
             $paid=[];
              foreach ($data['user'] as $i) {
                  $paid[]=DB::select(DB::raw("SELECT sum(amount_paid) as total_paid,transport_users.id,transport_users.member_id,transport_users.user_type from transport_collect_fees left join transport_users on transport_users.id = transport_collect_fees.transport_user_id  where transport_users.id='$i->id'")); 
               }
               
               $rep=[];
               foreach ($data['user'] as $key => $value) {
                  
                  foreach ($paid as $pkay => $val) {
                    foreach ($val as $k => $v) {
                        if($v->id==$value->id){
                        if(!empty($v->total_paid)){
                             $rep[$value->id]['paid']=$v->total_paid;
                             $rep[$value->id]['due']= ($value->total_rent - $v->total_paid ) ;   
                        }
                        else{
                            $rep[$value->id]['paid']=0;
                            $rep[$value->id]['due']= $value->total_rent ;
                        }
                        }
                        
                    }
                  }
               }
       $routes = Route::select('id','title')->get();
        $map_routes = array_pluck($routes,'title','id');
        $data['routes'] = array_prepend($map_routes,'Select Route...','');         
     return view(parent::loadDataToView($this->view_path.'.includes.dueReport'), compact('data','rep'));         
    }
    
    */
    
    
    public function dueShow(Request $request){
        $userType = $request->member_type;
        $regNo = $request->reg_no;
        $route =$request->route;
        $report_type= $request->report_type;
        $branch_id= Session::get('activeBranch');
        $session_id= Session::get('activeSession');
        $branch=DB::table('branches')->select('*')->where('id',$branch_id)->first();
        $transport_master= env('Transport_Master');
        if($transport_master==1 && $request->member_type==1){
            $request->session()->flash($this->message_warning,' Student Due Can Not be search here!!!.');
            return back();
           }
        if(!empty($request->reg_no)){
            switch ($userType){
                case 1:
                    $id = Student::where('reg_no','=',$request->reg_no)->first();
                    break;
                case 2:
                    $id = Staff::where('reg_no','=',$request->reg_no)->first();
                    break;
                default:
                    $request->session()->flash($this->message_warning,' User Type is not Valid.');
                    return back();
            }
            if(empty($id)){
                $request->session()->flash($this->message_warning,' Registration number is not Valid.');
                    return back();
            }
        }

        $data = [];
        $data['user'] = DB::table('transport_users')->select('transport_users.id','user_type', 'member_id', 'status','total_rent','duration','br.branch_name','br.branch_logo','br.branch_mobile','br.branch_email','br.branch_address','from_date','to_date')
         ->leftJoin('branches as br', 'br.id', '=', 'transport_users.branch')
            ->where(function ($query) use ($request) {

                if (!empty($request->member_type)) {
                    $query->where('user_type', '=', $request->member_type);
                    $this->filter_query['user_type'] = $request->member_type;
                }
                if(!empty($request->route)){

                    $query->where('routes_id','=',$request->route);
                    $this->filter_query['routes_id'] = $request->route;
                }
                if(!empty($request->reg_no)){
                    if($request->member_type==1)
                        $Id = $this->getStudentIdByReg($request->reg_no);
                    else
                     $Id = $this->getStaffByReg($request->reg_no);
                    $query->where('member_id','=',$Id);
                    $this->filter_query['member_id'] = $Id;
                }
             })
            ->where('transport_users.branch',$branch_id)
            ->where('transport_users.session',$session_id)
            ->where('transport_users.status',1)
             ->get(); 

             $paid=[];
              foreach ($data['user'] as $i) {
                  $paid[]=DB::select(DB::raw("SELECT sum(amount_paid) as total_paid,transport_users.id,transport_users.member_id,transport_users.user_type from transport_collect_fees left join transport_users on transport_users.id = transport_collect_fees.transport_user_id  where transport_users.id='$i->id'")); 
               }
               
               $rep=[];

               foreach ($data['user'] as $key => $value) {
                  
                  foreach ($paid as $pkay => $val) {
                    foreach ($val as $k => $v) {
                        if($v->id==$value->id){
                          if(!empty($v->total_paid)){
                               $rep[$value->id]['paid']=$v->total_paid;
                               $rep[$value->id]['due']= ($value->total_rent - $v->total_paid ) ;   
                          }
                          else{
                              $rep[$value->id]['paid']=0;
                              $rep[$value->id]['due']= $value->total_rent ;
                          }
                        }
                        
                    }
                  }
               }
            
              
          
          
          

               if($report_type==2){
                   foreach($rep as $rep_key=>$rep_v){

                       if($rep_v['due']<=0){
                         unset($rep[$rep_key]);
                       }
                       else{

                       }
                   }
                   foreach ($data['user'] as $key => $value) {
                       if(!array_key_exists($value->id, $rep)){
                        unset($data['user'][$key]);
                       }
                       else{

                       }
                   }  
      
               }
               
             
      
         $routes = Route::select('id','title')->get();
        $map_routes = array_pluck($routes,'title','id');
        $data['routes'] = array_prepend($map_routes,'Select Route...','');         
     return view(parent::loadDataToView($this->view_path.'.includes.dueReport'), compact('data','rep','branch','transport_master'));         
    }
    
    public function ImportCollection(){
        
        $transport_master= env('Transport_Master');
        if($transport_master==1){
         $transportUser=DB::table('transport_users')
         ->select('*')->where('user_type',1)->where('status',1)->where('transfer_status',1)->get();

                foreach($transportUser as $key_user=>$value_user){
                    $receipt_id=[];
                    $from= $value_user->from_date;
                    $to= $value_user->to_date;
                    $month_id=[]; $head_id= 17;  
                    $due_month=Carbon\Carbon::parse($from)->format('m');
                    if($value_user->duration=='monthly'){
                       $m=Carbon\Carbon::parse($from)->format('M'); 

                    }
                    else{
                        while($from<=$to){
                            $from= Carbon\Carbon::parse($from);

                            $month= Carbon\Carbon::parse($from)->format('M');
                            
                            if(!in_array($month, $month_id)){
                                $month_id[]= $month;
                            }
                            $from->addDays(1);
                        }
                        $m= implode('-', $month_id);
                    }

                    if($m && $head_id){
                        $parent_head_name= FeeHead::where([
                        ['id',$head_id], 
                        ])->select('fee_head_title')->first();
                        $mon = $parent_head_name->fee_head_title." ( ".strtoupper($m)." )"; 
                          
                        $sub_heads=FeeHead::where([
                        ['status','=',1],
                        ['parent_id','=',$head_id],
                        ['fee_head_title','=',$mon]
                        ])->first();
                        if($sub_heads){
                         $sub_head_id= $sub_heads->id;
                        } 
                        else{
                            
                            $sub_head_id= DB::table('fee_heads')->insertGetId([
                              'parent_id'            => $head_id,
                              'fee_head_title'       => $mon,
                              'slug'                 => $mon,
                              'created_at'           => $mon,
                              'created_by'           => Auth::user()->id,
                            ]);
                        }

                        $student_data= DB::table('student_detail_sessionwise')
                         ->select('course_id')
                         ->where('student_id',$value_user->member_id)
                         ->where('session_id',$value_user->session)->where('active_status',1)->first();

                        if($sub_head_id  && $student_data){
                             $chkexistassign= AssignFee::select('id')
                             ->where('branch_id',$value_user->branch)
                             ->where('session_id',$value_user->session)
                             ->where('course_id',$student_data->course_id)
                             ->where('student_id',$value_user->member_id)
                             ->where('fee_head_id',$sub_head_id)
                             ->where('status',1)->first();
                            
                            if(!$chkexistassign){
                                 $assign= AssignFee::insertGetId([
                                   'branch_id'=>$value_user->branch,
                                   'session_id'=>$value_user->session,
                                   'fee_head_id'=>$sub_head_id,
                                   'course_id'=>$student_data->course_id,
                                   'created_by'=>Auth::user()->id,
                                   'student_id'=>$value_user->member_id,
                                   'fee_amount'=>$value_user->total_rent,
                                   'due_month'=>$due_month
                                ]);
                                $update_tranport_user= DB::table('transport_users')->where('id',$value_user->id)->update(['transfer_status'=>0]);

                                 echo" assignid".$assign."--- subhead-".$mon."studentid".$value_user->member_id."tranport User id".$value_user->id."<br>";
                            }

                            $transport_collection= DB::table('transport_collect_fees')->select('*')
                            ->where('transport_user_id',$value_user->id)->where('status',1)->where('transfer_collect_status',1)->get();

                            foreach($transport_collection as $tc_key=>$tc_v){
                                 if(!empty($assign)){
                                     $receipt_id[]= DB::table('collect_fee')->insertGetId([
                                    'payment_type'=>$tc_v->pay_mode,
                                    'reciept_date'=>$tc_v->created_at,
                                    'reference'=>$tc_v->ref_no,
                                    'created_by'=>$tc_v->receipt_by,
                                    'created_at'=>$tc_v->created_at,
                                    'assign_fee_id'=>$assign,
                                    'student_id'=> $value_user->member_id,
                                    'amount_paid'=>$tc_v->amount_paid,

                                   ]);
                                 $update_tranport_collection= DB::table('transport_collect_fees')->where('id',$tc_v->id)->update(['transfer_collect_status'=>0]);
                                    $payment_count=count($receipt_id);
                                     $collect_id=$receipt_id[$payment_count-1];
                                     $receipt_no=$this->reciept_no($collect_id,$receipt_id);
                                    foreach ($receipt_id as $key => $value) {
                                       DB::table('collect_fee')->where('id',$value)->update([
                                            'reciept_no'=>$receipt_no
                                       ]);
                                    }
                                    echo "rec_no".$receipt_no."assignid".$assign."student".$value_user->member_id."tranport_collect_id".$tc_v->id."<br>";
                                 }

                                 

                               
                            }

                            
                        }
                    }
                }

                dd("done");
            
        }
    }
    
}
