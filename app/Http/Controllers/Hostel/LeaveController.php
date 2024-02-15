<?php
/**
 * Created by PhpStorm.
 * User: Umesh Kumar Yadav
 * Date: 03/03/2018
 * Time: 7:05 PM
 */
namespace App\Http\Controllers\Hostel;

use App\Http\Controllers\CollegeBaseController;
use App\Http\Requests\Hostel\Hostel\AddValidation;
use App\Http\Requests\Hostel\Hostel\EditValidation;
use App\Models\HostelLeave;
use App\Models\Resident;
use App\Models\ResidentHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use DB;
use Carbon\Carbon;
use Session;

class LeaveController extends CollegeBaseController
{
    protected $base_route = 'hostel.Leave';
    protected $view_path = 'hostel.Leave';
    protected $panel = 'Leave';
    protected $filter_query = [];

    public function __construct()
    {

    }
     public function index(Request $request)
    {
        $data = [];
        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;
        $data['resident'] = Resident::select('residents.id', 'residents.user_type', 'residents.member_id','s.first_name as student','st.first_name  as staff',DB::raw("IF(user_type=1,CONCAT(COALESCE(s.first_name,''),' - ','Student'),CONCAT(COALESCE(st.first_name,''),' - ','Staff')) as name"))
       ->leftjoin('students as s',function($j){
                        $j->on('s.id','=','residents.member_id')
                        ->where('residents.user_type',1);
                    })
       ->leftjoin('staff as st',function($k){
                        $k->on('st.id','=','residents.member_id')
                        ->where('residents.user_type',2);
                    })
        ->where('residents.branch_id','=',Session::get('activeBranch'))
        ->where('residents.session_id','=',Session::get('activeSession'))
        ->where('residents.status',1)
        ->pluck('name','id')->toArray();
        $data['resident']= array_prepend($data['resident'],'--select Resident--','');
        $data['leave']=HostelLeave::select('hostel_leave.*','residents.user_type','residents.member_id')
        ->leftjoin('residents','residents.id','=','hostel_leave.resident_id')
        ->where(function ($query) use ($request) {
                 if($request->leave_from && $request->leave_from!=''){
                    $query->where('hostel_leave.leave_from','>=',$request->leave_from);
                 }
                 if($request->leave_to && $request->leave_to!=''){
                    $query->where('hostel_leave.leave_to','<=',$request->leave_to);
                 } 
                 if($request->resident_id && $request->resident_id!=''){
                    $query->where('hostel_leave.resident_id',$request->resident_id);
                 }

            })
        ->where('hostel_leave.branch_id',session::get('activeBranch'))
        ->where('hostel_leave.session_id',session::get('activeSession'))
        ->where('record_status',1)->get();
       //dd($data);
        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    public function add(Request $request)
    {
        $data = [];
        $data['resident'] = Resident::select('residents.id', 'residents.user_type', 'residents.member_id','s.first_name as student','st.first_name  as staff',DB::raw("IF(user_type=1,CONCAT(COALESCE(s.first_name,''),' - ','Student'),CONCAT(COALESCE(st.first_name,''),' - ','Staff')) as name"))
       ->leftjoin('students as s',function($j){
                        $j->on('s.id','=','residents.member_id')
                        ->where('residents.user_type',1);
                    })
       ->leftjoin('staff as st',function($k){
                        $k->on('st.id','=','residents.member_id')
                        ->where('residents.user_type',2);
                    })
        ->where('residents.branch_id','=',Session::get('activeBranch'))
        ->where('residents.session_id','=',Session::get('activeSession'))
        ->where('residents.status',1)
        ->pluck('name','id')->toArray();
        $data['resident']= array_prepend($data['resident'],'--select Resident--','');
        $data['leave']=HostelLeave::select('hostel_leave.*','residents.user_type','residents.member_id')
        ->leftjoin('residents','residents.id','=','hostel_leave.resident_id')
        ->where('hostel_leave.branch_id',session::get('activeBranch'))
        ->where('hostel_leave.session_id',session::get('activeSession'))
        ->where('record_status',1)->get();
       //dd($data);
        return view(parent::loadDataToView($this->view_path.'.add'), compact('data'));
    }

   

    public function store(Request $request)
    {
        $rules=[
            'resident_id'=>'required',
            'leave_from'=>'required',  
            'leave_to'=>'required',  
            'reason'=>'required',  
            'return_date'=>'required',  
        ];

      
        
        $msg=[
            'resident_id.required'=>'Please select  one Resident',
            'leave_from.required'=>'Please select  Leave From',    
            'leave_to.required'=>'Please select Leave To',    
            'reason.required'=>'Please select Leave Reason',    
            'return_date.required'=>'Please select Return date',    
        ];
        $this->validate($request,$rules,$msg);
        if($request->leave_from > $request->leave_to){
            $request->session()->flash($this->message_danger, $this->panel. 'From should be less than To date.');
            return redirect()->route($this->base_route);
        }

        $request->request->remove('_token');
        $request->request->add(['created_by' => auth()->user()->id]);
        $request->request->add(['created_at' => Carbon::now()]);
        $request->request->add(['branch_id' => session::get('activeBranch')]);
        $request->request->add(['session_id' =>session::get('activeSession')]);
      
        $insert= HostelLeave::create($request->all());
        $request->session()->flash($this->message_success, $this->panel. ' Added Successfully.');
        return redirect()->route($this->base_route);
    }

    public function edit(Request $request, $id)
    {
        $data = [];
        if (!$data['row'] = HostelLeave::find($id))
            return parent::invalidRequest();
        $data['resident'] = Resident::select('residents.id', 'residents.user_type', 'residents.member_id','s.first_name as student','st.first_name  as staff',DB::raw("IF(user_type=1,CONCAT(COALESCE(s.first_name,''),' - ','Student'),CONCAT(COALESCE(st.first_name,''),' - ','Staff')) as name"))
       ->leftjoin('students as s',function($j){
                        $j->on('s.id','=','residents.member_id')
                        ->where('residents.user_type',1);
                    })
       ->leftjoin('staff as st',function($k){
                        $k->on('st.id','=','residents.member_id')
                        ->where('residents.user_type',2);
                    })
        ->where('residents.branch_id','=',Session::get('activeBranch'))
        ->where('residents.session_id','=',Session::get('activeSession'))
        ->where('residents.status',1)
        ->pluck('name','id')->toArray();
        $data['resident']= array_prepend($data['resident'],'--select Resident--','');
        $data['leave']=HostelLeave::select('hostel_leave.*','residents.user_type','residents.member_id')
        ->leftjoin('residents','residents.id','=','hostel_leave.resident_id')
        ->where('hostel_leave.branch_id',session::get('activeBranch'))
        ->where('hostel_leave.session_id',session::get('activeSession'))->where('record_status',1)->get();
        if($request->all()){
            $rules=[
                'resident_id'=>'required',
                'leave_from'=>'required',  
                'leave_to'=>'required',  
                'reason'=>'required',  
                'return_date'=>'required',  
            ];

      
        
            $msg=[
                'resident_id.required'=>'Please select  one Resident',
                'leave_from.required'=>'Please select  Leave From',    
                'leave_to.required'=>'Please select Leave To',    
                'reason.required'=>'Please select Leave Reason',    
                'return_date.required'=>'Please select Return date',    
            ];
            $this->validate($request,$rules,$msg);
            $request->request->remove('_token');
            $request->request->add(['updated_by' => auth()->user()->id]);
            $request->request->add(['updated_at' => Carbon::now()]);
            $data['row']->update($request->all());
            $request->session()->flash($this->message_success, $this->panel.' Updated Successfully.');
            return redirect()->route($this->base_route);
        }
      

        return view(parent::loadDataToView($this->view_path.'.add'), compact('data'));
    }

    

    public function delete(Request $request, $id)
    {
        if (!$row = HostelLeave::find($id)) return parent::invalidRequest();

        $row->update(['record_status'=>0,'updated_at'=>carbon::now(),'updated_by'=>auth()->user()->id]);

        $request->session()->flash($this->message_success, $this->panel.' Deleted Successfully.');
        return redirect()->route($this->base_route);
    }

   
}