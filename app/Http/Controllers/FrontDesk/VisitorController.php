<?php

namespace App\Http\Controllers\FrontDesk;
use App\Http\Controllers\CollegeBaseController;
use Illuminate\Http\Request;
use App\Http\Requests\Transport\User\AddValidation;
use App\Http\Requests\Transport\User\EditValidation;
use App\Models\Visitor;
use App\Models\Staff;
use App\Models\Student;
use App\Models\StudentStatus;
use URL,DB,Session;
use Carbon\Carbon;

class VisitorController extends CollegeBaseController
{
    protected $base_route = 'frontdesk.visitor';
    protected $view_path = 'front-desk.visitor';
    protected $panel = 'Visitor';
    protected $filter_query = [];

    public function index(Request $request)
    {
      // dd($request->all());
      $data['visitor']=Visitor::select('visitors_book.*','sts.title as purpose')->leftjoin('student_statuses as sts','sts.id','=','visitors_book.purpose')
      ->where(function($query)use($request){
        if($request->name){
          $query->where('name',$request->name);
        }
        if($request->purpose){
           $query->where('purpose',$request->purpose); 
        }
        if($request->reg_start_date && $request->reg_end_date){
          $query->whereBetween('date',[$request->reg_start_date,$request->reg_end_date]);
        }else{
          if(!isset($_GET['reg_start_date'])){
            $query->where('date',Carbon::now()->format('Y-m-d'));
          }
        }
        if($request->mobile){
          $query->where('contact',$request->mobile); 
        }
        if($request->note){
          $query->where('note',$request->note); 
        }
      })->where([['visitors_book.record_status','=',1],['visitors_book.branch_id','=',Session::get('activeBranch')],['visitors_book.session_id','=',Session::get('activeSession')]])
      ->get();
      $data['purpose']=StudentStatus::where('status',1)->select('id','title')->pluck('title','id')->toArray();
        $data['purpose']=array_prepend($data['purpose'],'--Select Purpose--','');
        return view(parent::loadDataToView($this->view_path.'.index'),compact('data'));
    }
    public function add(Request $request)
    {
        $data['purpose']=StudentStatus::where('status',1)->select('id','title')->pluck('title','id')->toArray();
        $data['purpose']=array_prepend($data['purpose'],'--Select Purpose--','');
        return view(parent::loadDataToView($this->view_path.'.add'),compact('data'));
    }
   
    public function store(Request $request)
    {
      $msg=
        ['name.required'=>'Please Enter Name',
        'contact.required'=>'Please Enter Mobile',
        'contact.min'=>'Mobile must be at least 10 digits',
        'purpose.required'=>'Please Enter Purpose',
        'date.required'=>'Please Enter Date',
      ];
     $rules=    
        [
          'name'=>'required',
          'contact'=>'required|min:10',
          'purpose'=>'required',
          'no_of_people'=>'required',
          'date'  =>'required'
        ];
     $this->validate($request,$rules,$msg);
      $request->request->add(['record_status'=>1]);
       $request->request->add(['created_at'=>Carbon::now()]);
       $request->request->add(['created_by'=>auth()->user()->id]);
      
       $request->request->add(['branch_id'=>Session::get('activeBranch')]);
       $request->request->add(['session_id'=>Session::get('activeSession')]);
      Visitor::create($request->all());
     
       return redirect('frontdesk/visitor')->with('message_success','Visitor Added Successfully');
    }
     public function edit(Request $request,$id){

      $data['row']=Visitor::find($id);
     if(!$data['row']){
      return parent::invalidRequest();
     }
      if($request->all()){
        $request->request->add(['updated_at'=>Carbon::now()]);
        $request->request->add(['updated_by'=>auth()->user()->id]);
        $data['row']->update($request->all());
        return redirect()->route('frontdesk.visitor')->with('message_success','Visitor Updated');
      }
      $data['purpose']=StudentStatus::where('status',1)->select('id','title')->pluck('title','id')->toArray();
        $data['purpose']=array_prepend($data['purpose'],'--Select Purpose--','');

      return view(parent::loadDataToView($this->view_path.'.add'),compact('data','id'));

    }
    public function delete($id,Request $request){
       $data['row']=Visitor::find($id);
       if(!$data['row']){
        return parent::invalidRequest();
       }

      Visitor::where('id',$id)->update(['record_status'=>0]);
      return redirect()->back()->with('message_warning','Visitor Deleted');
    }

}
