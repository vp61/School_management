<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\CollegeBaseController;
use Session;
use Illuminate\Http\Request;
use Auth,DB;
use Response;
use Carbon\Carbon;
use App\Models\subjectMaster;
class SubjectMasterController extends CollegeBaseController
{
    protected $base_route = 'addsubject';
    protected $view_path = 'academic.subject-master';
    protected $panel = 'Add Subject';
    protected $filter_query = [];

    public function __construct()
    {

    }

    public function index()
    {
       $data=[];
       $data['addsubject']=subjectMaster::where('record_status',1)->select('*')->get();
       return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

public function store(Request $request)
    {
      //dd($request->all());
        $msg=[
            'title.required'=>"Please Enter Subject",   
        ];
        $rules=[
            'title'=>'required',
        ];
        $this->validate($request,$rules,$msg);
        $request->request->add(['created_at'=>Carbon::now()]);
        $request->request->add(['branch_id'=>session::get('activeBranch')]);
        $request->request->add(['session_id'=>session::get('activeSession')]);
        $request->request->add(['created_by'=>auth()->user()->id]);
        $request->request->add(['record_status'=>1]);
        subjectMaster::create($request->all()); 
         $request->session()->flash($this->message_success, $this->panel.' Added Successfully.');
        return back();
    }
    public function edit(Request $request,$id){
        $data['row']=subjectMaster::find($id);
        if(!$data['row']){
            parent::invalidRequest();
        }
       
        if($request->title){
              $msg=[
            'title.required'=>"Please Enter Title"
            ];
            $rules=[
                'title'=>'required'
            ];
            $this->validate($request,$rules,$msg);
            $request->request->add(['updated_at'=>Carbon::now()]);
            $request->request->add(['updated_by'=>auth()->user()->id]);
            $data['row']->update($request->all());
            DB::table('timetable_subjects')->where('subject_master_id',$data['row']->id)->update([
                    'title' => $request->title
                ]);
            return redirect()->route($this->base_route)->with('message_success', $this->panel.' Updated Successfully');
        }
         $data['addsubject']=subjectMaster::where('record_status',1)->select('*')->get();
        return view(parent::loadDataToView($this->view_path.'.index'),compact('data','id'));
    }          
    public function delete(Request $request, $id)
    {
       $data['row']=subjectMaster::find($id);
        if(!$data['row']){
            parent::invalidRequest();
        }
        $request->request->add(['record_status'=>0]);
            $data['row']->update($request->all());
        return redirect()->route($this->base_route)->with('message_success', $this->panel.' Deleted Successfully');    
    }            

 

   
}
