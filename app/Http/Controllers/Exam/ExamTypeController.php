<?php

namespace App\Http\Controllers\Exam;

use App\Http\Controllers\CollegeBaseController;
use Illuminate\Http\Request;
use App\Models\Exam\ExamType;
use App\Models\Exam\ExamTerm;
use Carbon\Carbon;
use Session,DB;
class ExamTypeController extends CollegeBaseController
{
    protected $base_route = 'exam.setup.exam-type';
    protected $view_path = 'exam.setup.exam-type';
    protected $panel = 'Exam Type';
    protected $filter_query = [];

    public function __construct()
    {

    }

    public function index(Request $request)
    {
        

        $dropdown = $this->get_table_and_dropdown();
        return view(parent::loadDataToView($this->view_path.'.index'), compact('dropdown'));
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
        $request->request->add(['branch_id'=>Session::get('activeBranch')]);
        $request->request->add(['session_id'=>Session::get('activeSession')]);
        $request->request->add(['record_status'=>1]);
        ExamType::create($request->all()); 
         $request->session()->flash($this->message_success, $this->panel.' Added Successfully.');
        return back();
    }
    public function edit(Request $request,$id){
        $data['row']=ExamType::find($id);
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
        $dropdown = $this->get_table_and_dropdown();
        return view(parent::loadDataToView($this->view_path.'.index'),compact('data','id','dropdown'));
    }          
    public function delete(Request $request, $id)
    {
       $data['row']=ExamType::find($id);
        if(!$data['row']){
            parent::invalidRequest();
        }
        $request->request->add(['record_status'=>0]);
            $data['row']->update($request->all());
        return redirect()->route($this->base_route)->with('message_success', $this->panel.' Deleted Successfully');    
    }
    public function get_table_and_dropdown(){

        $data['exam-type']=ExamType::where([
            ['exam_type.record_status','=',1],
            ['exam_type.branch_id','=',Session::get('activeBranch')],
            ['exam_type.session_id','=',Session::get('activeSession')],
        ])->select('exam_type.*','et.title as term')
        ->leftjoin('exam_terms as et','et.id','=','exam_type.term_id')->get();

        $data['term'] = ExamTerm::where([
            ['record_status','=',1],
            ['branch_id','=',Session::get('activeBranch')],
            ['session_id','=',Session::get('activeSession')],
        ])->select('id','title')->pluck('title','id')->toArray();
        $data['term'] = array_prepend($data['term'],'--Select Term--','');

        return $data; 
    }

}