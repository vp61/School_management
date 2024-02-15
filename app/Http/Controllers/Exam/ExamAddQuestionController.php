<?php

namespace App\Http\Controllers\Exam;

use App\Http\Controllers\CollegeBaseController;
use Illuminate\Http\Request;
use App\Models\Exam\ExamCreate;
use App\Models\Exam\ExamMode;
use App\Models\Exam\ExamType;
use App\Models\Exam\ExamTerm;
use App\Models\Exam\ExamPaper;
use App\Models\Exam\ExamAddQuestion;
use App\Models\Exam\ExamQuestionType;
use App\Models\Faculty;
use Carbon\Carbon;
use Session,DB;

class ExamAddQuestionController extends CollegeBaseController
{
    protected $base_route = 'exam.add-question';
    protected $view_path = 'exam.add-question';
    protected $panel = 'Question';
    protected $filter_query = [];

    public function __construct()
    {

    }

    public function index(Request $request,$exam_id)
    {
       
        $dropdowns = $this->get_table_and_dropdown($exam_id);
        return view(parent::loadDataToView($this->view_path.'.index'), compact('dropdowns','exam_id'));
    }
    public function store_question(Request $request)
    {
        $data = $request->all();
        // dd($data['_token']['1']);
        $request->request->add(['created_at'=>Carbon::now()]);
        $request->request->add(['created_by'=>auth()->user()->id]);
        $request->request->add(['record_status'=>1]);
        $request->request->add(['exam_id'=>$request->exam_id]);

        foreach ($request->question_title as $key => $value) {

            if($key != 'question_id'){

                $option_1 =(isset($data['option_1'][$key]))?$data['option_1'][$key] :null;
                $option_2 =(isset($data['option_2'][$key]))?$data['option_2'][$key] :null;
                $option_3 =(isset($data['option_3'][$key]))?$data['option_3'][$key] :null;
                $option_4 =(isset($data['option_4'][$key]))?$data['option_4'][$key] :null;
                $option_5 =(isset($data['option_5'][$key]))?$data['option_5'][$key] :null;
                $option_6 =(isset($data['option_6'][$key]))?$data['option_6'][$key] :null;
                $correct_answer =(isset($data['correct_answer'][$key]))?$data['correct_answer'][$key] :null;
                $question_description =(isset($data['question_description'][$key]))?$data['question_description'][$key] :null;

                $request->request->add(['question_title'=>$value]);
                $request->request->add(['question_description'=>$question_description]);
                $request->request->add(['question_type'=>$data['question_type'][$key]]);
                $request->request->add(['mark'=>$data['question_mark'][$key]]);
                $request->request->add(['is_required'=>$data['is_required'][$key]]);
                $request->request->add(['option_1'=>$option_1]);
                $request->request->add(['option_2'=>$option_2]);
                $request->request->add(['option_3'=>$option_3]);
                $request->request->add(['option_4'=>$option_4]);
                $request->request->add(['option_5'=>$option_5]);
                $request->request->add(['option_6'=>$option_6]);
                $request->request->add(['correct_answer'=>$correct_answer]);
                ExamAddQuestion::create($request->all());
            }
        }
        $request->session()->flash($this->message_success, $this->panel.' Added Successfully.');
        return back();
    }
    public function edit_question(Request $request,$exam_id,$question_id){
        $data['row']=ExamAddQuestion::find($question_id);

        if(!$data['row']){
            parent::invalidRequest();
        }
        if($request->all()){
            $option_1 = (isset($request->option_1))?$request->option_1 :null;
                $option_2 = (isset($request->option_2))?$request->option_2 :null;
                $option_3 = (isset($request->option_3))?$request->option_3 :null;
                $option_4 = (isset($request->option_4))?$request->option_4 :null;
                $option_5 = (isset($request->option_5))?$request->option_5 :null;
                $option_6 =(isset($request->option_6))?$request->option_5 :null;
                $correct_answer =(isset($request->correct_answer))?$request->correct_answer :null;

                $request->request->add(['option_1'=>$option_1]);
                $request->request->add(['option_2'=>$option_2]);
                $request->request->add(['option_3'=>$option_3]);
                $request->request->add(['option_4'=>$option_4]);
                $request->request->add(['option_5'=>$option_5]);
                $request->request->add(['option_6'=>$option_6]);
                $request->request->add(['correct_answer'=>$correct_answer]);
            $request->request->add(['updated_at'=>Carbon::now()]);
            $request->request->add(['updated_by'=>auth()->user()->id]);
            $d=$data['row']->update($request->all());
                // dd($d);
            return redirect()->route($this->base_route,['exam_id'=>$exam_id])->with('message_success', $this->panel.' Updated Successfully');
        }
        $dropdowns = $this->get_table_and_dropdown($exam_id);
        return view(parent::loadDataToView($this->view_path.'.index'),compact('data','question_id','dropdowns','exam_id'));
    }          
    public function delete_question(Request $request,$exam_id, $question_id)
    {
       $data['row']=ExamAddQuestion::find($question_id);
        if(!$data['row']){
            parent::invalidRequest();
        }
        $request->request->add(['record_status'=>0]);
            $data['row']->update($request->all());
        return redirect()->route($this->base_route,['exam_id'=>$exam_id])->with('message_success', $this->panel.' Deleted Successfully');    
    }
    public function get_table_and_dropdown($exam_id=""){
        $data['mode'] = ExamMode::where([
            ['record_status','=',1],
        ])->select('id','title')->pluck('title','id')->toArray();
        $data['mode'] = array_prepend($data['mode'],'--Select Exam Mode--','');


        $data['term'] = ExamTerm::where([
            ['record_status','=',1],
            ['branch_id','=',Session::get('activeBranch')],
            ['session_id','=',Session::get('activeSession')],
        ])->select('id','title')->pluck('title','id')->toArray();
        $data['term'] = array_prepend($data['term'],'--Select Term--','');
        

        $data['paper-type'] = ExamPaper::where([
            ['record_status','=',1],
            ['branch_id','=',Session::get('activeBranch')],
            ['session_id','=',Session::get('activeSession')],
        ])->select('id','title')->pluck('title','id')->toArray();
        $data['paper-type'] = array_prepend($data['paper-type'],'--Select Paper Type--','');


        $data['question-type'] = ExamQuestionType::where([
            ['record_status','=',1],
            // ['branch_id','=',Session::get('activeBranch')],
            // ['session_id','=',Session::get('activeSession')],
        ])->select('id',DB::raw("CONCAT(title,'( ',description,' )') as title"))->pluck('title','id')->toArray();
        $data['question-type'] = array_prepend($data['question-type'],'--Select Question Type--','');


        $data['faculty'] = Faculty::where([
            ['status','=',1],
            ['branch_id','=',Session::get('activeBranch')],
        ])->select('id','faculty as title')->orderBy('title','asc')->pluck('title','id')->toArray();
        $data['faculty'] = array_prepend($data['faculty'],'--Select '.env('course_label').'--','');
         $data['exam'] = ExamCreate::where([
            ['exam_create.id','=',$exam_id],
        ])->select('exam_create.id','exam_create.title as exam_title','exam_create.description as exam_description','term.title as term','type.title as type','fac.faculty as faculty','sem.semester as section','mode.title as mode','paper.title as paper','sub.title as subject','max_mark','pass_mark')
         ->leftjoin('exam_terms as term','term.id','=','exam_create.term_id')
         ->leftjoin('exam_type as type','type.id','=','exam_create.type_id')
         ->leftjoin('faculties as fac','fac.id','=','exam_create.faculty_id')
         ->leftjoin('semesters as sem','sem.id','=','exam_create.section_id')
         ->leftjoin('exam_modes as mode','mode.id','=','exam_create.mode_id')
         ->leftjoin('exam_papers as paper','paper.id','=','exam_create.paper_type')
         ->leftjoin('timetable_subjects as sub','sub.id','=','exam_create.subject_id')
         ->first();

         $data['questions'] = ExamAddQuestion::where([
            ['exam_question.exam_id','=',$exam_id],
            ['exam_question.record_status','=',1]
         ])
         ->select('exam_question.*',DB::raw("CONCAT(qt.title) as question_type"))
         ->leftjoin('exam_question_types as qt','qt.id','=','exam_question.question_type')
         ->get();
        return $data;
    }
    public function getExamType(Request $request){
        $data = [];
        $data['error'] = true;
        if($request->term_id){ 
            $data['type'] = ExamType::select('*')->where([
                ['record_status','=',1],
                ['term_id','=',$request->term_id],
            ])->get();
            if(count($data['type'])>0){
                $data['error'] = false;
                $data['msg'] = 'Exam Type Found';
                $data['type'] = $data['type'];
            }else{
                $data['msg'] = 'No Exam Type Found';
            }
        }else{
            $data['msg'] = 'Invalid Request!';
        }
        return response()->JSON(json_encode($data));
    }
    public function getSubject(Request $request){
        $data = [];
        $data['error'] = true;
        if($request->section && $request->faculty && $request->session && $request->branch){ 
            $data['subject'] = DB::table('timetable_subjects')->select('*')->where([
                ['status','=',1],
                ['course_id','=',$request->faculty],
                ['section_id','=',$request->section],
                ['session_id','=',$request->session],
                ['branch_id','=',$request->branch],
            ])->get();
            if(count($data['subject'])>0){
                $data['error'] = false;
                $data['msg'] = 'Subjects Found';
                $data['type'] = $data['subject'];
            }else{
                $data['msg'] = 'No Subject Found';
            }
        }else{
            $data['msg'] = 'Invalid Request!';
        }
        return response()->JSON(json_encode($data));
    }
}