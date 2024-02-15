<?php

namespace App\Http\Controllers\FrontDesk;
use App\Http\Controllers\CollegeBaseController;
use Illuminate\Http\Request;
use App\Http\Requests\Transport\User\AddValidation;
use App\Http\Requests\Transport\User\EditValidation;
use URL,DB,Session;
use Carbon\Carbon;
use App\Models\Postal;

class PostalController extends CollegeBaseController
{
    protected $base_route = 'frontdesk.postal';
    protected $view_path = 'front-desk.postal';
    protected $panel = 'Postal';
    protected $filter_query = [];

    public function index(Request $request)
    {
      $data['postal']=Postal::select('*')
      ->where(function($query)use($request){
        if($request->name){
          $query->where('to_title',$request->to_title);
        }
        if($request->from_title){
           $query->where('from_title',$request->from_title); 
        }
        if($request->reg_start_date && $request->reg_end_date){
          $query->whereBetween('date',[$request->reg_start_date,$request->reg_end_date]);
        }else{
          if(!isset($_GET['reg_start_date'])){
            $query->where('date',Carbon::now()->format('Y-m-d'));
          }
        }
        if($request->reference_no){
          $query->where('reference_no',$request->reference_no); 
        }
        if($request->note){
          $query->where('note',$request->note); 
        }
         if($request->type){
          $query->where('type',$request->type); 
        }
      })->where([['record_status','=',1],['branch_id','=',Session::get('activeBranch')],['session_id','=',Session::get('activeSession')]])
      ->get();
        return view(parent::loadDataToView($this->view_path.'.index'),compact('data'));
    }
    public function postaldispatch(Request $request){
      if($request->all()){
        $msg=[
          'to_title.required'=>"Please Enter To Title",
          'from_title.required'=>"Please Enter From Title",
          'date.required'=>"Please Enter To date",
          'mobile.required'=>"Please Enter To Mobile",
          'mobile.min'=>"Mobile must be atleast 10 digits",
          'address.required'=>"Please Enter To Address"
        ];
        $rules=[
          'to_title'=>'required',
          'from_title'=>'required',
          'mobile'=>'required|min:10',
          'date'=>'required',
          'address'=>'required'
        ];
        $this->validate($request,$rules,$msg);
        $request->request->add(['created_by'=>auth()->user()->id]);
        $request->request->add(['created_at'=>Carbon::now()]);
        $request->request->add(['branch_id'=>Session::get('activeBranch')]);
        $request->request->add(['session_id'=>Session::get('activeSession')]);
        $request->request->add(['record_status'=>1]);
        Postal::create($request->all());
        return redirect()->back()->with('message_success','Dispatch Postal Added');
      }
      return view(parent::loadDataToView($this->view_path.'.dispatch.add'),compact('data'));
    }
    public function postalreceive(Request $request){

       if($request->all()){
        $msg=[
          'to_title.required'=>"Please Enter To Title",
          'from_title.required'=>"Please Enter From Title",
          'date.required'=>"Please Enter To date",
          'mobile.required'=>"Please Enter To Mobile",
          'mobile.min'=>"Mobile must be atleast 10 digits",
          'address.required'=>"Please Enter To Address"
        ];
        $rules=[
          'to_title'=>'required',
          'from_title'=>'required',
          'mobile'=>'required|min:10',
          'date'=>'required',
          'address'=>'required'
        ];
        $this->validate($request,$rules,$msg);
        $request->request->add(['created_by'=>auth()->user()->id]);
        $request->request->add(['created_at'=>Carbon::now()]);
        $request->request->add(['branch_id'=>Session::get('activeBranch')]);
        $request->request->add(['session_id'=>Session::get('activeSession')]);
        $request->request->add(['record_status'=>1]);
        Postal::create($request->all());
        return redirect()->back()->with('message_success','Receive Postal Added');
    }
      return view(parent::loadDataToView($this->view_path.'.receive.add'),compact('data'));
    }
    public function edit($id ,Request $request)
    {
      $data['row']=Postal::find($id);
      if(!$data['row']){
        return parent::invalidRequest();
     }
     if($request->all()){
      $msg=[
          'to_title.required'=>"Please Enter To Title",
          'from_title.required'=>"Please Enter From Title",
          'date.required'=>"Please Enter To date",
          'mobile.required'=>"Please Enter To Mobile",
          'mobile.min'=>"Mobile must be atleast 10 digits",
          'address.required'=>"Please Enter To Address"
        ];
        $rules=[
          'to_title'=>'required',
          'from_title'=>'required',
          'mobile'=>'required|min:10',
          'date'=>'required',
          'address'=>'required'
        ];
        $this->validate($request,$rules,$msg);
         $request->request->add(['updated_by'=>auth()->user()->id]);
        $request->request->add(['updated_at'=>Carbon::now()]);
         $data['row']->update($request->all());
         return redirect()->route($this->base_route)->with('message_success','Postal Updated');
     }
     if($data['row']->type==1){
      return view(parent::loadDataToView($this->view_path.'.dispatch.add'),compact('data'));
     }
     if($data['row']->type==2){
      return view(parent::loadDataToView($this->view_path.'.receive.add'),compact('data'));
     }
    }
   
   
    public function delete($id,Request $request){
       $data['row']=Postal::find($id);
       if(!$data['row']){
        return parent::invalidRequest();
       }

      Postal::where('id',$id)->update(['record_status'=>0]);
      return redirect()->back()->with('message_warning','Postal Deleted');
    }

}
