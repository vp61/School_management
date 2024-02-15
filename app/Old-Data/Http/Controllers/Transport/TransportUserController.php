<?php

namespace App\Http\Controllers\Transport;

use App\Http\Controllers\CollegeBaseController;
use App\Http\Requests\Transport\User\AddValidation;
use App\Http\Requests\Transport\User\EditValidation;
use App\Models\TransportUser;
use App\Models\TransportHistory;
use App\Models\RouteVehicle;
use App\Models\Route;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Year;
use Carbon\Carbon;
use App\Models\transport_collect_fee;
use DB;
use Session;
use Illuminate\Http\Request;
use URL;

class TransportUserController extends CollegeBaseController
{
    protected $base_route = 'transport.user';
    protected $view_path = 'transport.user';
    protected $panel = 'Transport Registration';
    protected $filter_query = [];

    public function __construct()
    {
    }

    public function index(Request $request)
    {
        $data = [];
        $data['user'] = TransportUser::select('id', 'routes_id', 'vehicles_id', 'user_type', 'member_id', 'status')
            ->where(function ($query) use ($request) {
                if ($request->get('user_type') !== '' & $request->get('user_type') > 0) {
                    $query->where('user_type', '=', $request->get('user_type'));
                    $this->filter_query['user_type'] = $request->get('user_type');
                }

                if ($request->reg_no != null) {
                    if($request->get('user_type') !== '' & $request->get('user_type') > 0){
                        if($request->has('user_type') == 1){
                            $studentId = $this->getStudentIdByReg($request->reg_no);
                            $query->where('member_id', '=', $studentId);
                            $this->filter_query['member_id'] = $studentId;
                        }
                        if($request->has('user_type') == 2){
                            $staffId = $this->getStaffByReg($request->reg_no);
                            $query->where('member_id', '=', $studentId);
                            $this->filter_query['member_id'] = $staffId;
                        }
                    }

                }

                if ($request->get('route') !== '' & $request->get('route') > 0) {
                    $query->where('routes_id', '=', $request->get('route'));
                    $this->filter_query['routes_id'] = $request->get('route');
                }

                if ($request->get('vehicle_select') !== '' & $request->get('vehicle_select') > 0) {
                    $query->where('vehicles_id', '=', $request->get('vehicle_select'));
                    $this->filter_query['vehicles_id'] = $request->get('vehicle_select');
                }


                if ($request->get('status') !== '' & $request->get('status') > 0) {
                    $query->where('status', $request->get('status') == '1' ? 1 : 0);
                    $this->filter_query['status'] = $request->get('status');
                }
            })
            ->get();

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

        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    public function add(Request $request)
    {
        $data = [];
        /*Hostel List*/
        $routes = Route::select('id','title')->Active()->get();
        $map_routes = array_pluck($routes,'title','id');
        $data['routes'] = array_prepend($map_routes,'Select Route...','');

        $data['reg_no'] ='';
        $data['pay_type'] = DB::table('payment_type')->select('type_name')->where('status', '1')->pluck('type_name','type_name')->toArray(); 
      
        $data['pay_type'] = array_prepend($data['pay_type'], "--Payment Mode--", "");
        return view(parent::loadDataToView($this->view_path.'.add'), compact('data'));
    }

    public function store(AddValidation $request)
    {
        $userType = $request->get('user_type');
        $regNo = $request->get('reg_no');
        $status = $request->get('status');
        $route = $request->get('route');
        $vehicle = $request->get('vehicle_select');
        $duration= $request->get('duration');
        $from=$request->get('from_date');
        $to=$request->get('to_date');
        $rent=$request->get('rent');
        $total_fare=$request->get('total_fare');
        $date=Carbon::now();
        $year = Year::where('active_status','=',1)->first();
        $ses=Session::get('activeSession');
        $branch=Session::get('activeBranch');
        if(!$year){
            $request->session()->flash($this->message_warning,' Active Year Not Found.Please, Set Year For History Record.');
            return back();
        }

        /*User Type and User Verification. only valid student or staff will get membership*/
        if($userType && $regNo){
            switch ($userType){
                case 1:
                    $data = Student::where('reg_no','=',$regNo)->first();
                    break;
                case 2:
                    $data = Staff::where('reg_no','=',$regNo)->first();
                    break;
                default:
                    return parent::invalidRequest();
            }
        }else{
            $request->session()->flash($this->message_warning,' Registration Number or User Type is not Valid.');
            return back();
        }

        if(isset($data)){
            $request->request->add(['routes_id' => $route]);
            $request->request->add(['vehicles_id' => $vehicle]);
            $request->request->add(['user_type' => $userType]);
            $request->request->add(['member_id' => $data->id]);
            $request->request->add(['status' => $status]);
            $request->request->add(['created_by' => auth()->user()->id]);
            $request->request->add(['duration' => $duration]);
            $request->request->add(['from_date' => $from]);
            $request->request->add(['to_date' => $to]);
            $request->request->add(['rent' => $rent]);
            $request->request->add(['total_rent' => $total_fare]);
            $request->request->add(['session' => $ses ]);
            $request->request->add(['branch' => $branch]);
            /*Check Member Alreday Register or not*/
            $UserStatus = TransportUser::where(['user_type' => $request->user_type, 'member_id' => $data->id])->orderBy('id','desc')->first();

                $request->session()->flash($this->message_success, $this->panel. ' Already Registered. Please Edit This TransportUser');
           
                $TransportUserRegister = TransportUser::create($request->all());
            //Transport fee payment  
           if(!empty($request->amount_paid) && ($request->amount_paid)>0){ 
              $incId=DB::table('transport_collect_fees')->insertGetId([
             'amount_paid'=>$request->amount_paid,
             'member_id'=>$data->id,
             'transport_user_id'=>$TransportUserRegister->id,
             'created_at'=>$date,
             'ref_no'=>$request->ref_no,
             'pay_mode'=>$request->pay_mode,
             'member_type'=>$userType,
             'session_id'=>Session::get('activeSession'),
             'branch_id'=>Session::get('activeBranch'),
             'receipt_by'=>auth()->user()->id,
            ]);
              $getno=10000+$incId;
              $rcp='AES'.$getno;
             
             $data['update']=DB::table('transport_collect_fees')
             ->where('id',$incId)
             ->update(['receipt_no'=>$rcp]);
            } 
           
                /*check TransportUser Register and add on histroy table*/
                if($TransportUserRegister){
                    $CreateHistory = TransportHistory::create([
                        'years_id' => $year->id,
                        'routes_id' => $route,
                        'vehicles_id' => $vehicle,
                        'travellers_id' => $TransportUserRegister->id,
                        'history_type' => "Registration",
                        'created_by' => auth()->user()->id,
                    ]);

                }
                $request->session()->flash($this->message_success, $this->panel. ' Created Successfully.');
            
        }else{
            $request->session()->flash($this->message_warning,' Registration Number or User Type is not Valid.');
        }

       return back();
    }

    public function edit(Request $request, $id)
    {
        $data = [];
        if (!$data['row'] = TransportUser::find($id))
            return parent::invalidRequest();


        if($data['row']->user_type == 1){
            $data['reg_no'] = Student::find($data['row']->member_id)->reg_no;
        }

        if($data['row']->user_type == 2){
            $data['reg_no'] = Staff::find($data['row']->member_id)->reg_no;
        }

        $data['base_route'] = $this->base_route;
        return view(parent::loadDataToView($this->view_path.'.edit'), compact('data'));
    }

    public function update(EditValidation $request, $id)
    {

        if (!$row = TransportUser::find($id)) return parent::invalidRequest();

        /*User Type and User Verification. only valid student or staff will get membership*/
        $userType =$request->get('user_type');
        $regNo =$request->get('reg_no');

        if($userType && $regNo){
            switch ($userType){
                case 1:
                    $data = Student::where('reg_no','=',$regNo)->first();
                    break;
                case 2:
                    $data = Staff::where('reg_no','=',$regNo)->first();
                    break;
                default:
                    return parent::invalidRequest();
            }
        }else{
            $request->session()->flash($this->message_warning,' Registration Number or User Type is not Valid.');
            return back();
        }

        if($data){
            $request->request->add(['user_type' => $request->get('user_type')]);
            $request->request->add(['member_id' => $data->id]);
            $request->request->add(['status' => $request->get('status')]);
            $request->request->add(['last_updated_by' => auth()->user()->id]);
            /*Check Member Alreday Register or not*/
            $UserStatus = TransportUser::where(['user_type' => $request->user_type, 'member_id' => $data->id])->orderBy('id','desc')->first();

            if($UserStatus->count() > 0){
                $row->update($request->all());
                $request->session()->flash($this->message_success, $this->panel.' Updated Successfully.');
            }else{
                $request->session()->flash($this->message_warning, $this->panel. ' Already Registered or Duplicate Registration. Please, Find on TransportUser List and Edit');
            }
        }else{
            $request->session()->flash($this->message_warning,' Registration Number or User Type is not Valid.');
        }

        return redirect()->route($this->base_route);
    }

    public function delete(Request $request, $id)
    {
        if (!$row = TransportUser::find($id)) return parent::invalidRequest();

        /*Delete History*/
        TransportHistory::where('travellers_id','=',$row->id)->delete();
        /*Delete TransportUser*/
        $row->delete();

        $request->session()->flash($this->message_success, $this->panel.' Deleted Successfully.');
        return redirect()->route($this->base_route);
    }

    public function bulkAction(Request $request)
    {
        if ($request->has('bulk_action') && in_array($request->get('bulk_action'), ['Active', 'Shift', 'Leave', 'Delete'])) {
            /*Assign request values*/
            $route = $request->get('route_bulk');
            $vehicle = $request->get('vehicle_bulk');
            $year = Year::where('active_status','=',1)->first();

            if ($request->has('chkIds')) {
                foreach ($request->get('chkIds') as $row_id) {
                    $row = TransportUser::find($row_id);
                    if($row) {
                        switch ($request->get('bulk_action')) {
                            case 'Active':
                                if($route && $vehicle) {
                                    /*TransportUser New Hostel, Vehicle & Bed Assign*/
                                    $active = $row->update([
                                        'routes_id' => $route,
                                        'vehicles_id' => $vehicle,
                                        'status' => 'active'
                                    ]);

                                    if ($active) {
                                        /*Create History for Transfer Future Record*/
                                        TransportHistory::create([
                                            'years_id' => $year->id,
                                            'routes_id' => $route,
                                            'vehicles_id' => $vehicle,
                                            'travellers_id' => $row->id,
                                            'history_type' => "Shift",
                                            'created_by' => auth()->user()->id
                                        ]);
                                    }

                                    $request->session()->flash($this->message_success, $this->panel . ' Re-Active Successfully.');
                                }else{
                                    $request->session()->flash($this->message_warning, 'Please Select Route & Vehicle for Active.');
                                }

                                break;
                            case 'Shift':
                                if($route && $vehicle) {
                                    /*TransportUser New Hostel, Vehicle & Bed Assign*/
                                    $shift = $row->update([
                                        'routes_id' => $route,
                                        'vehicles_id' => $vehicle,
                                    ]);

                                    if ($shift) {
                                        /*Create History for Transfer Future Record*/
                                        TransportHistory::create([
                                            'years_id' => $year->id,
                                            'routes_id' => $route,
                                            'vehicles_id' => $vehicle,
                                            'travellers_id' => $row->id,
                                            'history_type' => "Shift",
                                            'created_by' => auth()->user()->id
                                        ]);
                                    }

                                    $request->session()->flash($this->message_success, $this->panel . ' Shifted Successfully.');
                                }else{
                                    $request->session()->flash($this->message_warning, 'Please Select Route & Vehicle for Shifting.');
                                }
                                break;
                            case 'Leave':

                                    /*Create History for Leave TransportUser Future Record*/
                                    $CreateHistory = TransportHistory::create([
                                        'years_id' => $year->id,
                                        'routes_id' => $row->routes_id,
                                        'vehicles_id' => $row->vehicles_id,
                                        'travellers_id' => $row->id,
                                        'history_type' => "Leave",
                                        'created_by' => auth()->user()->id
                                    ]);

                                    /*update TransportUser*/
                                    $request->request->add(['routes_id' => null]);
                                    $request->request->add(['vehicles_id' => null]);
                                    $request->request->add(['status' => 'in-active']);
                                    $request->request->add(['last_updated_by' => auth()->user()->id]);
                                    $row->update($request->all());
                                    $request->session()->flash($this->message_success, $this->panel . ' TransportUsers Leave Successfully.');

                                break;
                            case 'Delete':
                                /*Delete History*/
                                TransportHistory::where('travellers_id', '=', $row->id)->delete();
                                /*Delete TransportUser*/
                                $row->delete();
                                $request->session()->flash($this->message_success, $this->panel . ' Deleted With History Successfully.');
                                break;
                        }
                    }
                }
                return redirect()->back();
            } else {
                $request->session()->flash($this->message_warning, 'Please, Check at least one row.');
                return redirect()->back();
            }
        } else return parent::invalidRequest();
    }

    public function renew(request $request)
    {
        $id = $request->get('userId');
        $route = $request->get('route_assign');
        $vehicle = $request->get('vehicle_assign');
        $year = Year::where('active_status','=',1)->first();

        if (!$row = TransportUser::find($id)) return parent::invalidRequest();

        $renewTransportUser = $row->update([
                            'routes_id' => $route,
                            'vehicles_id' => $vehicle,
                            'status' => 'active'
                        ]);

        if($renewTransportUser){
            /*Create Renew History*/
            $CreateHistory = TransportHistory::create([
                'years_id' => $year->id,
                'routes_id' => $route,
                'vehicles_id' => $vehicle,
                'travellers_id' => $id,
                'history_type' => "Renew",
                'created_by' => auth()->user()->id,
            ]);

            $request->session()->flash($this->message_success, $this->panel.' Re-Active Successfully.');
        }else{
            $request->session()->flash($this->message_warning, 'Not A Valid TransportUsetu.');
        }

        return redirect()->back();
    }

    public function leave(request $request, $id)
    {
        if (!$row = TransportUser::where('id',$id)->Active()->first()) return parent::invalidRequest();

        $route = $row->routes_id;
        $vehicle = $row->vehicles_id;

        /*update TransportUser*/
        $request->request->add(['routes_id' => null]);
        $request->request->add(['vehicles_id' => null]);
        $request->request->add(['status' => 'in-active']);
        $request->request->add(['last_updated_by' => auth()->user()->id]);
        $user = $row->update($request->all());

        $year = Year::where('active_status','=',1)->first();

        if($user) {
            /*Create History for Leave TransportUser Future Record*/
            $CreateHistory = TransportHistory::create([
                'years_id' => $year->id,
                'routes_id' => $route,
                'vehicles_id' => $vehicle,
                'travellers_id' => $row->id,
                'history_type' => "Leave",
                'created_by' => auth()->user()->id
            ]);

            $request->session()->flash($this->message_success, $this->panel. ' Leave Successfully.');
        }

        return redirect()->route($this->base_route);
    }

    public function shift(request $request)
    {
        /*Get Request values on Variables */
        $id = $request->get('userId');
        $route = $request->get('route_shift');
        $vehicle = $request->get('vehicle_shift');
        $year = Year::where('active_status','=',1)->first();

        if($route > 0 && $vehicle > 0 ) {
            $user = TransportUser::where('id', $id)->Active()->first();

            if ($user) {

                /*TransportUser New Hostel, Vehicle & Bed Assign*/
                $shift = $user->update([
                    'routes_id' => $route,
                    'vehicles_id' => $vehicle
                ]);

                if ($shift) {
                    /*Create History for Transfer Future Record*/
                    $CreateHistory = TransportHistory::create([
                        'years_id' => $year->id,
                        'routes_id' => $route,
                        'vehicles_id' => $vehicle,
                        'travellers_id' => $user->id,
                        'history_type' => "Shift",
                        'created_by' => auth()->user()->id
                    ]);
                }

                $request->session()->flash($this->message_success, $this->panel.' Shifted Successfully.');
            } else {
                $request->session()->flash($this->message_warning, 'TransportUser Not Select or Not Active, Please Active First.');
            }
        }else{
            $request->session()->flash($this->message_warning, 'Please, Select Route, Vehicle and Bed First.');
        }
        return redirect()->route($this->base_route);
    }

    /*History*/
    public function history(Request $request)
    {
        $data = [];
        //dd($request->all());
        if($request->all()) {
            $data['history'] = TransportHistory::select('transport_histories.id', 'transport_histories.years_id',
                'transport_histories.routes_id', 'transport_histories.vehicles_id',  'transport_histories.history_type',
                'transport_histories.created_at','tu.member_id','tu.user_type')
                ->where(function ($query) use ($request) {

                    if ($request->get('year') !== '' & $request->get('year') > 0) {
                        $query->where('transport_histories.years_id', '=', $request->get('year'));
                        $this->filter_query['transport_histories.years_id'] = $request->get('year');
                    }

                    if ($request->get('route') !== '' & $request->get('route') > 0) {
                        $query->where('transport_histories.routes_id', '=', $request->get('route'));
                        $this->filter_query['transport_histories.routes_id'] = $request->get('route');
                    }

                    if ($request->get('vehicle_select') !== '' & $request->get('vehicle_select') > 0) {
                        $query->where('transport_histories.vehicles_id', '=', $request->get('vehicle_select'));
                        $this->filter_query['transport_histories.vehicles_id'] = $request->get('vehicle_select');
                    }

                    if ($request->history_type <> '0'){
                        $query->where('transport_histories.history_type', '=', $request->get('history_type'));
                        $this->filter_query['transport_histories.history_type'] = $request->get('history_type');
                    }


                    if ($request->get('user_type') !== '' & $request->get('user_type') > 0) {
                        $query->where('tu.user_type', '=', $request->get('user_type'));
                        $this->filter_query['tu.user_type'] = $request->get('user_type');
                    }

                    if ($request->reg_no != null) {
                        if($request->get('user_type') !== '' & $request->get('user_type') > 0){
                            if($request->has('user_type') == 1){
                                $studentId = $this->getStudentIdByReg($request->reg_no);
                                $query->where('member_id', '=', $studentId);
                                $this->filter_query['member_id'] = $studentId;
                            }
                            if($request->has('user_type') == 2){
                                $staffId = $this->getStaffByReg($request->reg_no);
                                $query->where('member_id', '=', $studentId);
                                $this->filter_query['member_id'] = $staffId;
                            }
                        }

                    }


                })
                ->join('transport_users as tu','tu.id','=','transport_histories.travellers_id')
                ->orderBy('transport_histories.created_at')
                ->get();
        }

        /*Year*/
        $routes = Year::select('id','title')->Active()->get();
        $map_years = array_pluck($routes,'title','id');
        $data['years'] = array_prepend($map_years,'Select Year...','0');

        /*Hostel List*/
        $routes = Route::select('id','title')->get();
        $map_routes = array_pluck($routes,'title','id');
        $data['routes'] = array_prepend($map_routes,'Select Route...','0');

        $data['url'] = URL::current();

        return view(parent::loadDataToView($this->view_path.'.history.index'), compact('data'));
    }

    /*All Vehicle & Bed available or not*/
    public function findVehicles(Request $request)
    {
        $response = [];
        $response['error'] = true;

        if ($request->has('route_id')) {
            $routes = RouteVehicle::select('route_vehicles.id','route_vehicles.vehicles_id', 'v.number','v.type','v.description','routes.rent')
                ->where('route_vehicles.routes_id','=', $request->get('route_id'))
                ->join('vehicles as v','v.id','=','route_vehicles.vehicles_id')
                 ->join('routes','routes.id','=','route_vehicles.routes_id')
                ->get();

            //$routes = array_pluck($routes,'number','vehicles_id');

            if ($routes) {
                $response['vehicles'] = $routes;
                $response['rent']=$routes[0]->rent;
                $response['error'] = false;
                $response['success'] = 'Vehicles Available For This Route.';
            } else {
                $response['error'] = 'No Any Vehicles Assign on This Route.';
            }

        } else {
            $response['message'] = 'Invalid request!!';
        }
        return response()->json(json_encode($response));
    }

}