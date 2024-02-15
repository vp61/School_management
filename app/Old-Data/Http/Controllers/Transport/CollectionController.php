<?php

namespace App\Http\Controllers\Transport;
use App\Http\Controllers\CollegeBaseController;
use Illuminate\Http\Request;
use App\Http\Requests\Transport\User\AddValidation;
use App\Http\Requests\Transport\User\EditValidation;
use App\Models\TransportUser;
use App\Models\TransportHistory;
use App\Models\RouteVehicle;
use App\Models\Route;
use App\Models\Staff;
use App\Models\Student;
use URL,DB,Session;
use Carbon\Carbon;

class CollectionController extends CollegeBaseController
{
    protected $base_route = 'transport.collect';
    protected $view_path = 'transport.feecollect';
    protected $panel = 'Transport Fees';
    protected $filter_query = [];

    public function index(Request $request)
    {
        $routes = Route::select('id','title')->get();
        $map_routes = array_pluck($routes,'title','id');
        $data['routes'] = array_prepend($map_routes,'Select Route...'," ");

        /*Active Route For Shift List*/
        /*Route List*/
        $routes = Route::select('id','title')->Active()->get();
        $map_routes = array_pluck($routes,'title','id');
        $data['active_routes'] = array_prepend($map_routes,'Select Route...','0');

        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;
        $data['session']=Session::get('activeSession');
        $data['branch']=Session::get('activeBranch');
         $data['pay_type'] = DB::table('payment_type')->select('type_name')->where('status', '1')->pluck('type_name','type_name')->toArray(); 
      
        $data['pay_type'] = array_prepend($data['pay_type'], "--Payment Mode--", "");

     return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function loadTravellers(Request $request)
    {
       $response = [];
        $response['error'] = true;
        
        if($request->route_id){
           if($request->type==1){
            $response['type']=$request->type; 
            $travellers=TransportUser::select('st.first_name','st.id','st.reg_no')
            ->where([
                ['user_type','=',$request->type],
                ['routes_id','=',$request->route_id]
            ])
            ->leftjoin('students as st','st.id','=','transport_users.member_id')
            ->distinct('st.reg_no')
            ->get();
           }
            elseif($request->type==2){
            $response['type']=$request->type; 
            $travellers=TransportUser::select(DB::raw("CONCAT(st.first_name,' ',st.last_name) as first_name"),'st.id','st.reg_no')
            ->where([
                ['user_type','=',$request->type],
                ['routes_id','=',$request->route_id]
            ])
            ->leftjoin('staff as st','st.id','=','transport_users.member_id')
            ->get();
           }
           if($travellers){
            $response['traveller']=$travellers;
            $response['error']=false;
            $response['success'] = 'Travellers Available For This Route.';
            } else {
                $response['error'] = 'No Travellers.';
            }
        }else {
            $response['message'] = 'Invalid request!!';
        }
        return response()->json(json_encode($response));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       $memType=$request->member_type;
       $memId=$request->travelId;
       $routeId=$request->route;
       $cdate=Carbon::now();

       foreach ($request->amount as $key => $value) {
          foreach ($request->remark as $rkey => $rvalue) {
             if($rkey==$key){
                if($value>0){
                   $incId=DB::table('transport_collect_fees')->insertGetId([
                    'member_id'           => $memId,
                    'member_type'         => $memType,
                    'transport_user_id'   => $key,
                    'amount_paid'         => $value,
                    'created_at'          => $request->reciept_date,
                    'remark'              => $rvalue,
                    'pay_mode'            => $request->pay_mode,
                    'session_id'             => Session::get('activeSession'),
                    'branch_id'              => Session::get('activeBranch'),
                    'receipt_by'          => auth()->user()->id, 
                    'ref_no'              => $request->ref_no,   
                ]);
                   $getno=10000+$incId;
                  $rcp='AES'.$getno;
                 
                 $data['update']=DB::table('transport_collect_fees')
                 ->where('id',$incId)
                 ->update(['receipt_no'=>$rcp]);
               }
                 $request->session()->flash($this->message_success, $this->panel. ' Collected Successfully.');
                
             }
          }
       }
       return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
  public function showFee(Request $request)
    {
       $type=$request->type;
       $member_id=$request->member_id;
       $route_id=$request->route;
       $sessionId=$request->sId;
       $branchId=$request->bId;
        $response = [];
        $response['error'] = true;

       $data=[];
       $pay=[];
        if($request->type && $request->member_id && $request->sId && $request->bId){       
               $data=DB::table('transport_users')->select('transport_users.id','total_rent','rt.title','vh.number','duration','st.first_name','from_date','to_date')
               ->where([
                ['transport_users.user_type','=',$type],
                ['transport_users.member_id','=',$member_id],
                ['transport_users.session','=',$sessionId],
                ['transport_users.branch','=',$branchId],
               ])
               ->leftjoin('routes as rt','rt.id','=','transport_users.routes_id')
               ->leftjoin('vehicles as vh','vh.id','=','transport_users.vehicles_id')
               ->leftjoin('students as st','st.id','=','transport_users.member_id')
               ->orderBy('transport_users.id','asc')
               ->get();

               $pay=DB::table('transport_collect_fees')->select('amount_paid','pay_mode','receipt_no',DB::raw("DATE_FORMAT(transport_collect_fees.created_at,'%d-%m-%Y') as created_at"),'ref_no','transport_users.duration')
               ->where([
                  ['transport_collect_fees.member_id','=',$member_id],
                  ['transport_collect_fees.member_type','=',$type],
                  ['branch_id','=',$branchId],
                  ['session_id','=',$sessionId]
               ])
               ->leftjoin('transport_users','transport_users.id','=','transport_collect_fees.transport_user_id')
               ->orderBy('transport_collect_fees.created_at','asc')
               ->get();
               $paid=[];
               foreach ($data as $id) {
                  $paid[$id->id]=DB::select(DB::raw("SELECT sum(amount_paid) as total_paid,amount_paid,transport_user_id from transport_collect_fees where transport_user_id='$id->id'")); 
               }
            
            if($data){
            $response['user']=$data;
            $response['paid']=$paid;
            $response['history']=$pay;
            $response['error']=false;
            $response['success'] = 'Travellers Available For This Route.';
            } else {
                $response['error'] = 'No Travellers.';
            }
        }else {
            $response['message'] = 'Invalid request!!';
        }
        return response()->json(json_encode($response));
      
    }

public function showFeeOld(Request $request)
    {
       return response()->json(json_encode($request));
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
