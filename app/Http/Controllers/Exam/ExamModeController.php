<?php

namespace App\Http\Controllers\Exam;

use App\Http\Controllers\CollegeBaseController;
use Illuminate\Http\Request;
use App\Models\Exam\ExamMode;
use Carbon\Carbon;
use Session,DB;

class ExamModeController extends CollegeBaseController
{
     protected $base_route = 'exam.setup.exam-mode';
    protected $view_path = 'exam.setup.exam-mode';
    protected $panel = 'Exam Mode';
    protected $filter_query = [];

    public function __construct()
    {

    }

    public function index(Request $request)
    {
        $data = [];
        $data['exam-mode'] = ExamMode::where([
            ['record_status','=',1],
        ])->select('*')->get();
        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }
    public function store(Request $request)
    {
        $msg=[
            'title.required'=>"Please Enter Title",
        ];
        $rules=[
            'title'=>'required',
        ];
        $this->validate($request,$rules,$msg);
        $request->request->add(['created_at'=>Carbon::now()]);
        $request->request->add(['created_by'=>auth()->user()->id]);
        $request->request->add(['record_status'=>1]);
        ExamMode::create($request->all()); 
         $request->session()->flash($this->message_success, $this->panel.' Added Successfully.');
        return back();
    }
    public function edit(Request $request,$id){
        $data['row']=ExamMode::find($id);
        if(!$data['row']){
            parent::invalidRequest();
        }
        if($request->all() ){
            $msg=[
            'title.required'=>"Please Enter Title",
            ];
            $rules=[
                'title'=>'required',
            ];
            $this->validate($request,$rules,$msg);
            $request->request->add(['updated_at'=>Carbon::now()]);
            $request->request->add(['updated_by'=>auth()->user()->id]);
            $data['row']->update($request->all());
            return redirect()->route($this->base_route)->with('message_success', $this->panel.' Updated Successfully');
        }
         $data['exam-mode'] = ExamMode::where([
            ['record_status','=',1],
        ])->select('*')->get();
        return view(parent::loadDataToView($this->view_path.'.index'),compact('data','id'));
    }          
    public function delete(Request $request, $id)
    {
       $data['row']=ExamMode::find($id);
        if(!$data['row']){
            parent::invalidRequest();
        }
        $request->request->add(['record_status'=>0]);
            $data['row']->update($request->all());
        return redirect()->route($this->base_route)->with('message_success', $this->panel.' Deleted Successfully');    
    }

}