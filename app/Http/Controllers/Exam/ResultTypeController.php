<?php

namespace App\Http\Controllers\Exam;

use App\Http\Controllers\CollegeBaseController;
use Illuminate\Http\Request;
use App\Models\Exam\ResultType;
use Carbon\Carbon;
use Session,DB;
use App\Models\Faculty;
class ResultTypeController extends CollegeBaseController
{
    protected $base_route = 'exam.setup.result-type';
    protected $view_path = 'exam.setup.result-type';
    protected $panel = 'Result Type';
    protected $filter_query = [];

    public function __construct()
    {

    }

    public function index(Request $request)
    {
        
        $data=[];
        $data['course']= Faculty::select('id','faculty')->where('branch_id',Session::get('activeBranch'))->pluck('faculty','id')->toArray();

        $data['course'] = array_prepend($data['course'],'--Select '.env("course_label").'--'," ");
         $data['section']= [];
         $data['result_type']=DB::table('exam_result_type')->select('exam_result_type.*','f.faculty as course','sem.semester as section')
         ->leftjoin('faculties as f','f.id','=','exam_result_type.course_id')
         ->leftjoin('semesters as sem','sem.id','=','exam_result_type.section_id')
         ->where('exam_result_type.branch_id',session::get('activeBranch'))
         ->where('exam_result_type.session_id',session::get('activeSession'))
         ->where('exam_result_type.record_status',1)->get();
        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }
    public function store(Request $request)
    {
         //dd($request->all());
        $msg=[
            'course_id.required'=>"Please Enter course",
            
        ];
        $rules=[
            'course_id'=>'required',
           
        ];
        $this->validate($request,$rules,$msg);
        $course= $request->course_id;
        foreach ($course as $key => $value) {
           $insert= ResultType::UpdateOrInsert([
              'branch_id'=>Session::get('activeBranch'),
              'session_id'=>Session::get('activeSession'),
              'course_id'=>$value,

           ],
           [
             'result_type_id'=>$request->result_type_id,
             'created_at'=>Carbon::now(),
             'created_by'=>auth()->user()->id,
             'record_status'=>1

           ]);
        }
        
         $request->session()->flash($this->message_success, $this->panel.' Added Successfully.');
        return back();
    }
    public function edit(Request $request,$id){
        $data['row']=ResultType::find($id);
          $data['course']= Faculty::select('id','faculty')->where('branch_id',Session::get('activeBranch'))->pluck('faculty','id')->toArray();

        $data['course'] = array_prepend($data['course'],'--Select '.env("course_label").'--'," ");
        $faculty_id= $data['row']->course_id;
        $data['section'] = DB::table('faculty_semester')
        ->where(function($q)use($faculty_id){
            if($faculty_id){
                $q->where('faculty_id',$faculty_id);
            }
        })
        ->select('sem.id','semester')
        ->leftjoin('semesters as sem','sem.id','=','faculty_semester.semester_id')
        ->pluck('semester','id')->toArray();
       
        $data['result_type']=ResultType::select('exam_result_type.*','f.faculty as course','sem.semester as section')
         ->leftjoin('faculties as f','f.id','=','exam_result_type.course_id')
         ->leftjoin('semesters as sem','sem.id','=','exam_result_type.section_id')
         ->where('exam_result_type.branch_id',session::get('activeBranch'))
         ->where('exam_result_type.session_id',session::get('activeSession'))
         ->where('exam_result_type.record_status',1)->get();
        if(!$data['row']){
            parent::invalidRequest();
        }
        if($request->all() ){
             $msg=[
            'course_id.required'=>"Please Enter course",
            ];
            $rules=[
                'course_id'=>'required',
               
            ];
            $this->validate($request,$rules,$msg);
            $request->request->add(['updated_at'=>Carbon::now()]);
            $request->request->add(['updated_by'=>auth()->user()->id]);
            $data['row']->update($request->all());

            

            return redirect()->route($this->base_route)->with('message_success', $this->panel.' Updated Successfully');
        }
        return view(parent::loadDataToView($this->view_path.'.index'),compact('data','id','section'));
    }          
    public function delete(Request $request, $id)
    {
       $data['row']=ResultType::find($id);
        if(!$data['row']){
            parent::invalidRequest();
        }
        $request->request->add(['record_status'=>0]);
            $data['row']->update($request->all());
        return redirect()->route($this->base_route)->with('message_success', $this->panel.' Deleted Successfully');    
    }
    

}