<?php

namespace App\Http\Controllers\FrontDesk;
use App\Http\Controllers\CollegeBaseController;
use Illuminate\Http\Request;
use App\Http\Requests\Transport\User\AddValidation;
use App\Http\Requests\Transport\User\EditValidation;
use URL,DB,Session,Log;
use App\Models\CallLog;
use App\Models\Visitor;
use Carbon\Carbon;
use App\Models\FollowUpHistory;

class CallLogController extends CollegeBaseController
{
    protected $base_route = 'frontdesk.callLog';
    protected $view_path = 'front-desk.call-log';
    protected $panel = 'Call Log';
    protected $filter_query = [];

    public function index(Request $request)
    {
      
      $data['calllog']=CallLog::select('call_logs.*','fwh.next_follow_up','fwh.date as last_call_date','fwh.response','fwh.call_duration as last_call_duration')
      ->leftjoin('follow_up_history as fwh','fwh.call_log_id','=','call_logs.id')
           
      ->where(function($query)use($request){
        if($request->name){
          $query->where('call_logs.name',$request->name);
        }
        if($request->reg_start_date && $request->reg_end_date){
          $query->whereBetween('call_logs.date',[$request->reg_start_date,$request->reg_end_date]);
        }else{
          if(isset($_GET['reg_start_date'])){
            
          }else{
            $query->where('call_logs.date',Carbon::now()->format('Y-m-d'));
          }
        }
        if($request->mobile){
          $query->where('call_logs.contact',$request->mobile); 
        }
        if($request->note){
          $query->where('call_logs.note',$request->note); 
        }
        if($request->call_type){
          $query->where('call_logs.call_type',$request->call_type);
        }
        if($request->start_follow_up && $request->end_follow_up){
          $query->WhereBetween('fwh.next_follow_up',[$request->start_follow_up,$request->end_follow_up]);
        }
      })->where('fwh.follow_up_status',1)
      ->where('call_logs.record_status',1)
      ->where('branch_id',Session::get('activeBranch'))
      ->where('session_id',Session::get('activeSession'))
      
     
      ->get();
     
        return view(parent::loadDataToView($this->view_path.'.index'),compact('data','log'));
    }
    public function add(Request $request)
    {
        return view(parent::loadDataToView($this->view_path.'.add'),compact('data'));
    }
   
    public function store(Request $request)
    {
      $msg=
        ['name.required'=>'Please Enter Name',
        'contact.required'=>'Mobile field is required',
        'contact.min'=>'Mobile must be at least 10 digits',
        'call_type.required'=>'Call Type field is required',
        'call_duration.required'=>'Duration field is required',
        'date.required'=>'Date field is required',
      ]
      ;
     $rules=    
        [
          'name'=>'required',
          'contact'=>'required|min:10',
          'call_type'=>'required',
          'call_duration'=>'required',
          'date'  =>'required'
        ];
     $this->validate($request,$rules,$msg);
       $request->request->add(['created_at'=>Carbon::now()]);
       $request->request->add(['created_by'=>auth()->user()->id]);
        $request->request->add(['branch_id'=>Session::get('activeBranch')]);
         $request->request->add(['session_id'=>Session::get('activeSession')]);
       $request->request->add(['record_status'=>1]);
      
       $dd=CallLog::create($request->all());
       if($request->date){
        $row=FollowUpHistory::insert([
            'created_at'=> $request->created_at,
            'created_by'=>$request->created_by,
            'call_duration'=>$request->call_duration,
            'date'=>$request->date,
            'next_follow_up'=>$request->follow_up_date,
            'call_log_id'=>$dd->id,
            'record_status'=>1,
            'follow_up_status'=>1,
            'note'=>'FIRST CALL'
        ]);
      }
       return redirect('frontdesk/callLog')->with('message_success','Log Added Successfully');
    }
     public function edit(Request $request,$id){

      $data['row']=CallLog::find($id);
     if(!$data['row']){
      return parent::invalidRequest();
     }
      if($request->all()){
        $request->request->add(['updated_at'=>Carbon::now()]);
        $request->request->add(['updated_by'=>auth()->user()->id]);
        $data['row']->update($request->all());
        return redirect()->route('frontdesk.callLog')->with('message_success','Call Log Updated');
      }

      return view(parent::loadDataToView($this->view_path.'.add'),compact('data','id'));

    }
    public function delete($id,Request $request){
       $data['row']=CallLog::find($id);
       if(!$data['row']){
        return parent::invalidRequest();
       }
      CallLog::where('id',$id)->update(['record_status'=>0]);
      return redirect()->back()->with('message_warning','Call log Deleted');
    }
    public function view($id){
      $data['row']=CallLog::find($id);
      if(!$data['row']){
        return parent::invalidRequest();
      }
      $data['followup']=FollowUpHistory::where([['call_log_id','=',$id],['record_status','=',1]])->select('*')->orderBy('next_follow_up','desc')->get();
      $log_id=$id;
      return view(parent::loadDataToView($this->view_path.'.view'),compact('data','log_id'));
    }
    public function addFollowUp($id,Request $request){
      $msg=[
          'date.required'=>'Please Enter Date',
          'response.required'=>"Please Enter Response"
      ];
      $rule=[
        'date'=>'required',
        'response'=>'required'
      ];
      $this->validate($request,$rule,$msg);
      $request->request->add(['created_at'=>Carbon::now()]);
      $request->request->add(['created_by'=>auth()->user()->id]);
      $request->request->add(['record_status'=>1]);
      $request->request->add(['call_log_id'=>$id]);
      $request->request->add(['follow_up_status'=>1]);
      $row=FollowUpHistory::create($request->all());
        return redirect()->back()->with('message_success','Next Follow Up Date Added Successfully');
    }
    public function editFollowUp($id,Request $request){
      $row=FollowUpHistory::find($id);

      if(!$row){
        return parent::invalidRequest();
      }
       $data['followup']=FollowUpHistory::where([['call_log_id','=',$row->call_log_id],['record_status','=',1]])->select('*')->orderBy('next_follow_up','desc')->get();
      $data['row']=CallLog::find($row->call_log_id);
      if($request->all()){
        $msg=[
          'date.required'=>'Please Enter Date',
        'response.required'=>"Please Enter Response"
        ];
        $rule=[
          'date'=>'required',
          'response'=>'required'
        ];
        $this->validate($request,$rule,$msg);
        $request->request->add(['updated_at'=>Carbon::now()]);
        $request->request->add(['updated_by'=>auth()->user()->id]);
        $row->update($request->all());
        return redirect()->route($this->base_route.'.view',[$row->call_log_id])->with('message_success','Follow Up History Updated');
      }
      $log_id=$row->call_log_id;
    return view(parent::loadDataToView($this->view_path.'.view'),compact('data','row','log_id'));
    }
    public function deleteFollowUp($id){
       $row=FollowUpHistory::find($id);
        if(!$row){
          return parent::invalidRequest();
        }
        FollowUpHistory::where('id',$id)->update(['record_status'=>0]);
      return redirect()->back()->with('message_warning','Call log Deleted');
    }
    public function changeFollowUpStatus($log,$id,$status){
      $row=FollowUpHistory::find($id);
      if(!$row){
        return parent::invalidRequest();
      }
      FollowUpHistory::where('id',$id)->update(['follow_up_status'=>$status]);
      return redirect()->route($this->base_route.'.view',$log)->with('message_success','Status Changed');
    }
}
