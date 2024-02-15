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
use DB;
use URL;
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
}
