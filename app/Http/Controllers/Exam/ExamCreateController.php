<?php

namespace App\Http\Controllers\Exam;

use App\Http\Controllers\CollegeBaseController;
use Illuminate\Http\Request;
use App\Models\Exam\ExamCreate;
use App\Models\Exam\ExamMode;
use App\Models\Exam\ExamType;
use App\Models\Exam\ExamTerm;
use App\Models\Exam\ExamPaper;
use App\Models\Faculty;
use Carbon\Carbon;
use Session,DB,Auth;
/*--teacher access--*/
use App\Models\TeacherCoordinator;

/*--teacher access--*/

class ExamCreateController extends CollegeBaseController
{
    protected $base_route = 'exam.create';
    protected $view_path = 'exam.create';
    protected $panel = 'Exams';
    protected $filter_query = [];

    public function __construct()
    {

    }

    public function index(Request $request)
    {
       
        $dropdowns = $this->get_table_and_dropdown();
        return view(parent::loadDataToView($this->view_path.'.add'), compact('dropdowns'));
    }

    public function test(Request $request)
    {
       
        return view();
    }
    public function list(Request $request)
    {
        
        $data['exam'] = ExamCreate::select('exam_create.*','exam_create.title as exam_title','exam_create.description as exam_description','term.title as term','type.title as type','fac.faculty as faculty','sem.semester as section','mode.title as mode','paper.title as paper','sub.title as subject','max_mark','pass_mark','publish_status')
        ->where([
            ['exam_create.record_status','=',1],
            ['exam_create.branch_id','=',Session::get('activeBranch')],
            ['exam_create.session_id','=',Session::get('activeSession')],
        ])
        ->where(function($q)use($request){
            if($request->faculty_id){
                $q->where('exam_create.faculty_id','=',$request->faculty_id);
            }
            if($request->section_id){
                 $q->where('exam_create.section_id','=',$request->section_id);
            }
            if($request->subject_id){
                $q->where('exam_create.subject_id','=',$request->subject_id);
            }
            if($request->has('date')){
                if($request->date){
                    $q->where('exam_create.date','=',$request->date);
                }
            }else{
                $q->where('exam_create.date','=',Carbon::now()->format('Y-m-d'));
            }
        })
         ->leftjoin('exam_terms as term','term.id','=','exam_create.term_id')
         ->leftjoin('exam_type as type','type.id','=','exam_create.type_id')
         ->leftjoin('faculties as fac','fac.id','=','exam_create.faculty_id')
         ->leftjoin('semesters as sem','sem.id','=','exam_create.section_id')
         ->leftjoin('exam_modes as mode','mode.id','=','exam_create.mode_id')
         ->leftjoin('exam_papers as paper','paper.id','=','exam_create.paper_type')
         ->rightjoin('timetable_subjects as sub','sub.id','=','exam_create.subject_id')
         ->get();

        $dropdowns = $this->get_table_and_dropdown();
        return view(parent::loadDataToView($this->view_path.'.index'), compact('dropdowns','data'));
    }
    public function store_exam(Request $request)
    {
         //dd($request->all());
        $msg=[
            'title.required'=>"Please Enter Title",
            'mode_id.required'=>"Please Select Exam Mode",
            'term_id.required'=>"Please Select Term",
            'type_id.required'=>"Please Select Exam Type",
            'paper_type.required'=>"Please Select Paper Type",
            'faculty_id.required'=>"Please Select ".env('course_label'),
            'section_id.required'=>"Please Select Section",
            'subject_id.required'=>"Please Select Subject",
        ];
         $rules=[
            'title'=>'required',
            'mode_id'=>'required',
            'term_id'=>'required',
            'type_id'=>'required',
            'paper_type'=>'required',
            'faculty_id'=>'required',
            'section_id'=>'required',
            'subject_id'=>'required',
            'title'=>'required',
         ];
        
          $this->validate($request,$rules,$msg);
        
        
        
        
         $request->request->add(['created_at'=>Carbon::now()]);
         $request->request->add(['created_by'=>auth()->user()->id]);
         $request->request->add(['record_status'=>1]);
         $request->request->add(['branch_id'=>Session::get('activeBranch')]);
         $request->request->add(['session_id'=>Session::get('activeSession')]);
          /*exam change*/
          $subject_id= DB::table('timetable_subjects')->select('subject_master_id','title')
         ->where('id',$request->subject_id)->where('status',1)
         ->first();
          $subject= $subject_id->subject_master_id;
         // dd($subjectId);
          $resp=[];
          foreach($request->section_id as $key=>$value){

            $timetable_subject_id= DB::table('timetable_subjects')->select('id')
            ->where('subject_master_id',$subject)
            ->where('course_id',$request->faculty_id)
            ->where('section_id',$value)
            ->where('branch_id',session::get('activeBranch'))
            ->where('session_id',session::get('activeSession'))
            ->where('status',1)
            ->first();
           // dd($timetable_subject_id);
            $course= DB::table('faculties')->select('faculty')->where('id',$request->faculty_id)->first();
            //dd($course,$request->faculty_id);
            $course_name= $course->faculty;
            $section= DB::table('semesters')->select('semester')->where('id',$value)->first();
            $sem_name= $section->semester;

             if(!$timetable_subject_id){
               
               $resp[]= $this->panel.' not created for '.  $course_name.'('.$sem_name.')'.'-'.$subject_id->title .'as subject is not assigned';

             }
             else{
                 $subjectId= $timetable_subject_id->id;
                 $request->request->add(['section_id'=>$value]);
                 $request->request->add(['subject_id'=>$subjectId]);
                 $chk = $this->checkDuplicateExam($request);    
               if($chk){
                $resp[]= $this->panel.' already exists for '.  $course_name.'('.$sem_name.')'.'-'.$subject_id->title;
               }
               else{
                  ExamCreate::create($request->all()); 
                  $resp[]= $this->panel.' created for '.  $course_name.'('.$sem_name.')'.'-'.$subject_id->title;
               }
                
             }
           
          }
          
         
        if(count($resp)>0){
            return back()->withErrors($resp);
        }
            return redirect()->back()->with('message_success','Exams created Successfully');
         /*exam change*/
    }
    public function edit_exam(Request $request,$id){
        
        $data['row']=ExamCreate::find($id);

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
            
            
            if(($request->term_id != $data['row']->term_id) ||($request->type_id != $data['row']->type_id) ||($request->faculty_id != $data['row']->faculty_id) ||($request->section_id != $data['row']->section_id) ||($request->subject_id != $data['row']->subject_id)){
                $chk = $this->checkDuplicateExam($request,$data['row']->id);
                if($chk){
                    return redirect()->back()->with('message_danger','Exam already exists for selected Subject of the Class/Course & Section');
                } 
            }
            
            
            
            $request->request->add(['updated_at'=>Carbon::now()]);
            $request->request->add(['updated_by'=>auth()->user()->id]);
            
            $data['row']->update($request->all());
            return redirect()->route($this->base_route)->with('message_success', $this->panel.' Updated Successfully');
        }
        $dropdowns = $this->get_table_and_dropdown($data['row']->term_id,$data['row']->faculty_id,$data['row']->section_id);
        return view(parent::loadDataToView($this->view_path.'.add'),compact('data','id','dropdowns'));
    }          
    public function delete_exam(Request $request, $id)
    {
       $data['row']=ExamCreate::find($id);
        if(!$data['row']){
            parent::invalidRequest();
        }
        $request->request->add(['record_status'=>0]);
        $request->request->add(['updated_at'=>Carbon::now()]);
            $request->request->add(['updated_by'=>auth()->user()->id]);
            $data['row']->update($request->all());
        return redirect()->route($this->base_route)->with('message_success', $this->panel.' Deleted Successfully');    
    }
    public function change_status(Request $request, $id,$status)
    {
       $data['row']=ExamCreate::find($id);
        if(!$data['row']){
            parent::invalidRequest();
        }
        if((!$data['row']->date || !$data['row']->start_time || !$data['row']->end_time)&& ($status ==1)){
             return redirect()->route($this->base_route.'.edit',['id'=>$id])->with('message_warning','Please Add Date, Start Time, End Time First');
           
        } $request->request->add(['publish_status'=>$status]);
            $data['row']->update($request->all());  
        return redirect()->back()->with('message_success', $this->panel.' Status Updated Successfully');    
    } 
    public function result_status(Request $request, $id,$status)
    {
       $data['row']=ExamCreate::find($id);
        if(!$data['row']){
            parent::invalidRequest();
        }
        $current_date = Carbon::now();
        $exam_end_date = new Carbon($data['row']->date.' '.$data['row']->end_time);

        if(($current_date < $exam_end_date)&& ($status ==1)){
            return redirect()->back()->with('message_warning','Exam not completed yet, Cannot publish result now.');
           
        } $request->request->add(['result_status'=>$status]);
            $data['row']->update($request->all());  
        return redirect()->back()->with('message_success', $this->panel.' Result Status Updated Successfully');    
    }
    public function get_table_and_dropdown($term_id="",$faculty_id="",$section_id=""){
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


        $data['type'] = ExamType::where([
            ['record_status','=',1],
            ['branch_id','=',Session::get('activeBranch')],
            ['session_id','=',Session::get('activeSession')],
        ])
        ->where(function($q)use($term_id){
            if($term_id){
                $q->where('term_id',$term_id);
            }
        })
        ->select('id','title')->pluck('title','id')->toArray();
        $data['type'] = array_prepend($data['type'],'--Select Type--','');


         /* --teacher access-- */
        $classTeachercourse= $this->getClassTeacherCourse();

         $classTeacher= TeacherCoordinator::where('teacher_id',Auth::user()->hook_id)
        ->where('branch_id',Session::get('activeBranch'))
        ->where('record_status',1)
        ->where('session_id',Session::get('activeSession'))->pluck('section_id')->toArray();
        
        $ability = $this->getAbility();
        
        $data['section'] = DB::table('faculty_semester')
        ->select('sem.id','semester')
        ->leftjoin('semesters as sem','sem.id','=','faculty_semester.semester_id')
        ->Where(function($q)use($classTeacher,$ability){
            if(count($classTeacher)>0 && (!$ability)){
                $q->whereIn('sem.id',$classTeacher);
            }
        })
        ->pluck('semester','id')->toArray();
        $data['section']= array_prepend($data['section'],'-- Select Section --','');
        /*teacher access*/


         /*exam change*/
        $data['subject'] = DB::table('timetable_subjects')
        ->where([
            ['status','=',1],
            ['branch_id','=',Session::get('activeBranch')],
            ['session_id','=',Session::get('activeSession')],
            ['course_id',$faculty_id],
            ['section_id',$section_id],
        ])
        
        ->select('id','title')
        ->pluck('title','id')->toArray();
        $data['subject'] = array_prepend($data['subject'],'--Select Subject--','');
        /*exam change*/



        
        $data['paper-type'] = ExamPaper::where([
            ['record_status','=',1],
            ['branch_id','=',Session::get('activeBranch')],
            ['session_id','=',Session::get('activeSession')],
        ])->select('id','title')->pluck('title','id')->toArray();
        $data['paper-type'] = array_prepend($data['paper-type'],'--Select Paper Type--','');

        // $data['faculty'] = Faculty::where([
        //     ['status','=',1],
        //     ['branch_id','=',Session::get('activeBranch')],
        // ])->select('id','faculty as title')->orderBy('title','asc')->pluck('title','id')->toArray();
        // $data['faculty'] = array_prepend($data['faculty'],'--Select '.env('course_label').'--','');
        
         $data['faculty'] = $this->activeFaculties();
        
        $data['exam'] = ExamCreate::select('exam_create.*','exam_create.title as exam_title','exam_create.description as exam_description','term.title as term','type.title as type','fac.faculty as faculty','sem.semester as section','mode.title as mode','paper.title as paper','sub.title as subject','max_mark','pass_mark','publish_status')
        ->where([
            ['exam_create.record_status','=',1],
            ['exam_create.branch_id','=',Session::get('activeBranch')],
            ['exam_create.session_id','=',Session::get('activeSession')],
        ])
         ->leftjoin('exam_terms as term','term.id','=','exam_create.term_id')
         ->leftjoin('exam_type as type','type.id','=','exam_create.type_id')
         ->leftjoin('faculties as fac','fac.id','=','exam_create.faculty_id')
         ->leftjoin('semesters as sem','sem.id','=','exam_create.section_id')
         ->leftjoin('exam_modes as mode','mode.id','=','exam_create.mode_id')
         ->leftjoin('exam_papers as paper','paper.id','=','exam_create.paper_type')
         ->leftjoin('timetable_subjects as sub','sub.id','=','exam_create.subject_id')
         ->get();
         
         $data['grading_type'] = DB::table('grading_types')->select('id','title')
         ->where([
            ['status','=',1]
         ])->pluck('title','id')->toArray();

         $data['grading_type'] = array_prepend($data['grading_type'],'--Select Grading Type--','');
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
                // ['section_id','=',$request->section],
                ['session_id','=',$request->session],
                ['branch_id','=',$request->branch],
            ])

            ->where(function($q)use($request){
                if(is_array($request->section)){
                    $q->whereIn('section_id',$request->section);
                }else{
                    $q->where('section_id',$request->section);
                }
            })
            ->groupBy('subject_master_id')
            ->get();
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
    
    
    
    public function checkDuplicateExam(Request $request,$row_id=''){
        // dd($request->all());
        
        $chk = ExamCreate::select('id')->where([
                ['term_id','=',$request->term_id],
                ['type_id','=',$request->type_id],
                ['faculty_id','=',$request->faculty_id],
                ['section_id','=',$request->section_id],
                ['subject_id','=',$request->subject_id],
                ['branch_id','=',Session::get('activeBranch')],
                ['session_id','=',Session::get('activeSession')],
                ['record_status','=',1],
            ])
            ->where(function($q)use($row_id){
                if($row_id){
                    $q->where('id','!=',$row_id);
                }
            })
            
            ->first();
            
        return $chk; 
    }
    
    /*bulk exam status*/
    public function BulkAction(Request $request)
    {
         //dd($request->all());
         $chkid= $request->chkIds;
         foreach ($chkid as $key => $value) {
            $row= ExamCreate::find($value);
            if($request->bulk_action== 'publish'){
             $row->update(['publish_status'=>'1']);
            }
            if($request->bulk_action== 'result'){
              $row->update(['result_status'=>'1']);
            }
         }
          return redirect()->back()->with('message_success',$request->bulk_action.' status updated Successfully');
    }
    /*bulk exam status*/

}