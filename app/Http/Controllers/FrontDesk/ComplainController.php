<?php
namespace App\Http\Controllers\FrontDesk;
use App\Http\Controllers\CollegeBaseController;
use Illuminate\Http\Request;
use App\Http\Requests\Transport\User\AddValidation;
use App\Http\Requests\Transport\User\EditValidation;
use App\Models\Complain;
use App\Models\Staff;
use App\Models\Source;
use App\Models\ComplainType;
use URL,DB,Session;
use Carbon\Carbon;

class ComplainController extends CollegeBaseController
{
    protected $base_route = 'frontdesk.complain';
    protected $view_path = 'front-desk.complain';
    protected $panel = 'complain';
    protected $filter_query = [];

    public function index(Request $request)
    {
      $data['complain']=Complain::select('complains.*','ct.title as complain','sou.title as source')
      ->leftjoin('complain_types as ct','complains.complain_type','=','ct.id')
      ->leftjoin('sources as sou','sou.id','=','complains.source_id')
      ->where(function($query)use($request){
        if($request->complain_by){
          $query->where('complain_by','like','%'.$request->name.'%');
        }
        if($request->purpose){
           $query->where('complaint_type',$request->complaint_type); 
        }
        if($request->reg_start_date && $request->reg_end_date){
          $query->whereBetween('date',[$request->reg_start_date,$request->reg_end_date]);
        }else{
          if(!isset($_GET['reg_start_date'])){
            $query->where('date',Carbon::now()->format('Y-m-d'));
          }
        }
        if($request->mobile){
          $query->where('mobile',$request->mobile); 
        }
        if($request->note){
          $query->where('note',$request->note); 
        }
        if($request->source){
          $query->where('source_id',$request->source); 
        }
        if($request->assigned){
          $query->where('assigned','like','%'.$request->assigned.'%'); 
        }
      })->where([['complains.record_status','=',1],['complains.branch_id','=',Session::get('activeBranch')],['complains.session_id','=',Session::get('activeSession')]])
      ->get();
      $data['complain_type']=ComplainType::where('record_status',1)->select('id','title')->pluck('title','id')->toArray();
        $data['complain_type']=array_prepend($data['complain_type'],'--Select Complain Type--','');
        $data['source']=Source::where('record_status',1)->select('id','title')->pluck('title','id')->toArray();
        $data['source']=array_prepend($data['source'],'--Select Source--','');
        return view(parent::loadDataToView($this->view_path.'.index'),compact('data'));
    }
    public function add(Request $request)
    {
        $data['complain_type']=ComplainType::where('record_status',1)->select('id','title')->pluck('title','id')->toArray();
        $data['complain_type']=array_prepend($data['complain_type'],'--Select Complain Type--','');
        $data['source']=Source::where('record_status',1)->select('id','title')->pluck('title','id')->toArray();
        $data['source']=array_prepend($data['source'],'--Select Source--','');
        return view(parent::loadDataToView($this->view_path.'.add'),compact('data'));
    }
   
    public function store(Request $request)
    {
      $msg=
        ['complain_by.required'=>'Please Enter Complain By',
        'mobile.required'=>'Please Enter Mobile',
        'mobile.min'=>'Mobile must be at least 10 digits',
        'complain_type.required'=>'Please Enter Complain Type',
        'date.required'=>'Please Enter Date',
        'source_id.required'=>'Please Enter Source',
      ];
     $rules=    
        [
          'complain_by'=>'required',
          'mobile'=>'required|min:10',
          'complain_type'=>'required',
          'date'=>'required',
          'source_id'  =>'required'
        ];
     $this->validate($request,$rules,$msg);
      $request->request->add(['record_status'=>1]);
      $request->request->add(['complain_status'=>1]);
       $request->request->add(['created_at'=>Carbon::now()]);
       $request->request->add(['created_by'=>auth()->user()->id]);
      
       $request->request->add(['branch_id'=>Session::get('activeBranch')]);
       $request->request->add(['session_id'=>Session::get('activeSession')]);
      Complain::create($request->all());
     
       return redirect()->route($this->base_route)->with('message_success','Complain Added Successfully');
    }
     public function edit(Request $request,$id){

      $data['row']=Complain::find($id);
     if(!$data['row']){
      return parent::invalidRequest();
     }
      if($request->all()){
        $request->request->add(['updated_at'=>Carbon::now()]);
        $request->request->add(['updated_by'=>auth()->user()->id]);
        $data['row']->update($request->all());
        return redirect()->route('frontdesk.complain')->with('message_success','Complain Updated');
      }
     $data['complain_type']=ComplainType::where('record_status',1)->select('id','title')->pluck('title','id')->toArray();
        $data['complain_type']=array_prepend($data['complain_type'],'--Select Complain Type--','');
        $data['source']=Source::where('record_status',1)->select('id','title')->pluck('title','id')->toArray();
        $data['source']=array_prepend($data['source'],'--Select Source--','');
      return view(parent::loadDataToView($this->view_path.'.add'),compact('data','id'));

    }
    public function delete($id,Request $request){
       $data['row']=Complain::find($id);
       if(!$data['row']){
        return parent::invalidRequest();
       }

      Complain::where('id',$id)->update(['record_status'=>0]);
      return redirect()->back()->with('message_warning','Complain Deleted');
    }
    public function changeStatus($id,$status,Request $request){
      $data['row']=Complain::find($id);
      if(!$data['row']){
        parent::invalidRequest();
      }
      $request->request->add(['updated_at'=>Carbon::now()]);
      $request->request->add(['updated_by'=>auth()->user()->id]);
      $request->request->add(['complain_status'=>$status]);
      $data['row']->update($request->all());
       return redirect()->back()->with('message_success','Status Updated');
    }

}
