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
/*--teacher access--*/
use Session,DB,Auth;
use App\Models\TeacherCoordinator;
/*--teacher access--*/

class ExamReportController extends CollegeBaseController
{
    protected $base_route = 'exam.report';
    protected $view_path = 'exam.report';
    protected $panel = 'Exam Report';
    protected $filter_query = [];

    public function __construct()
    {

    }

    public function index(Request $request)
    {
        $dropdowns = $this->get_table_and_dropdown(); 
         
         unset($dropdowns['term']['']);
        $dropdowns['term']= array_prepend($dropdowns['term'],'Combined','');


        return view(parent::loadDataToView($this->view_path.'.index'), compact('dropdowns'));
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
        $request->request->add(['branch_id'=>Session::get('activeBranch')]);
        $request->request->add(['session_id'=>Session::get('activeSession')]);
        ExamPaper::create($request->all()); 
         $request->session()->flash($this->message_success, $this->panel.' Added Successfully.');
        return back();
    }
    public function edit(Request $request,$id){
        $data['row']=ExamPaper::find($id);
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
         $data['exam-paper'] = ExamPaper::where([
            ['record_status','=',1],
            ['branch_id','=',Session::get('activeBranch')],
            ['session_id','=',Session::get('activeSession')],
        ])->select('*')->get();
        return view(parent::loadDataToView($this->view_path.'.index'),compact('data','id'));
    }          
    public function delete(Request $request, $id)
    {
       $data['row']=ExamPaper::find($id);
        if(!$data['row']){
            parent::invalidRequest();
        }
        $request->request->add(['record_status'=>0]);
            $data['row']->update($request->all());
        return redirect()->route($this->base_route)->with('message_success', $this->panel.' Deleted Successfully');    
    }
    /* GENERATE REPORT CARD BACKUP
    public function generate(Request $request){
        
        $branch = DB::table('branches')->select('*')->where('id',Session::get('activeBranch'))->first();
        $student = DB::table('student_detail_sessionwise')->select('std.first_name','std.reg_no','std.date_of_birth','std.gender','fac.faculty as course','sem.semester','pd.father_first_name','std.student_image')
        ->leftjoin('students as std','std.id','=','student_detail_sessionwise.student_id')
        ->leftjoin('faculties as fac','fac.id','=','student_detail_sessionwise.course_id')
        ->leftjoin('semesters as sem','sem.id','=','student_detail_sessionwise.Semester')
        ->leftjoin('parent_details as pd','pd.students_id','=','student_detail_sessionwise.student_id')
        ->where([
            ['student_detail_sessionwise.course_id','=',$request->course],
            ['student_detail_sessionwise.Semester','=',$request->section],
            ['student_detail_sessionwise.session_id','=',Session::get('activeSession')],
            ['student_detail_sessionwise.active_status','=',1],
            ['student_detail_sessionwise.student_id','=',$request->student]
        ])->first();
   
            $exams = ExamCreate::select('exam_create.id','exam_create.title','exam_create.max_mark','exam_create.pass_mark','et.title as exam_term','em.mark as obtained_mark','em.attendance','type.title as exam_type','ts.title as subject')->where([
                ['exam_create.faculty_id','=',$request->course],
                ['exam_create.section_id','=',$request->section],
                ['exam_create.term_id','=',$request->term],
                ['exam_create.type_id','=',$request->type_id],
                ['exam_create.session_id','=',Session::get('activeSession')],
                ['exam_create.branch_id','=',Session::get('activeBranch')],
                ['exam_create.result_status','=',1],
            ])
            ->leftjoin('exam_terms as et','et.id','=','exam_create.term_id')
            ->leftjoin('exam_type as type','type.id','=','exam_create.type_id')
            ->leftjoin('timetable_subjects as ts','ts.id','=','exam_create.subject_id')
            ->leftjoin('exam_mark as em',function($j)use($request){
                $j->on('em.exam_id','=','exam_create.id')
                ->where('em.student_id',$request->student);
            })
            ->get();
             
            foreach ($exams as $key => $value) {
               $data[$value->exam_term][$value->exam_type][$value->id]=$value;
            }
            $cnt =  0;
           
       return view(parent::loadDataToView($this->view_path.'.generate'),compact('data','branch','student'));  
    }
    */
    public function generate(Request $request){
        // dd($request->all());
        try{


        if($request->student && $request->course && $request->section && $request->report_type){
            $report_type = $request->report_type;
            $term=[];
            $subject = [];
            $optional_subject = [];
            $disc = [];
            $branch = DB::table('branches')->select('*')->where('id',Session::get('activeBranch'))->first();
            $session = DB::table('session')->select('*')->where('id',Session::get('activeSession'))->first();
            // dd($request->all());
            $student = DB::table('student_detail_sessionwise')->select('std.first_name','std.reg_no','student_detail_sessionwise.roll_no','std.date_of_birth','std.gender','fac.faculty as course','sem.semester','pd.father_first_name','std.student_image','pd.mother_first_name')
                ->leftjoin('students as std','std.id','=','student_detail_sessionwise.student_id')
                ->leftjoin('faculties as fac','fac.id','=','student_detail_sessionwise.course_id')
                ->leftjoin('semesters as sem','sem.id','=','student_detail_sessionwise.Semester')
                ->leftjoin('parent_details as pd','pd.students_id','=','student_detail_sessionwise.student_id')
                ->where([
                    ['student_detail_sessionwise.course_id','=',$request->course],
                    ['student_detail_sessionwise.Semester','=',$request->section],
                    ['student_detail_sessionwise.session_id','=',Session::get('activeSession')],
                    ['student_detail_sessionwise.active_status','=',1],
                    ['student_detail_sessionwise.student_id','=',$request->student],
                ])
            ->first();
            // mapped subject
            $exams = ExamCreate::select('exam_create.id','exam_create.title','exam_create.max_mark','exam_create.pass_mark','et.title as exam_term','em.mark as obtained_mark','em.attendance','type.title as exam_type','ts.title as subject','exam_create.subject_id','exam_create.type_id','exam_create.term_id','sm.is_main_subject as subject_type','em.grade','exam_create.grading_type','exam_create.paper_type','ep.title as paper_type_name')
            // mapped subject end
                ->where([
                    ['exam_create.faculty_id','=',$request->course],
                    ['exam_create.section_id','=',$request->section],
                    ['exam_create.session_id','=',Session::get('activeSession')],
                    ['exam_create.branch_id','=',Session::get('activeBranch')],
                    ['exam_create.result_status','=',1],
                ])
                ->where(function($q)use($request){
                    if($request->term_id){
                        $q->where('exam_create.term_id',$request->term_id);
                    }
                    if($request->type_id){
                        $q->where('exam_create.type_id',$request->type_id);
                    }
                })
                ->leftjoin('exam_terms as et','et.id','=','exam_create.term_id')
                ->leftjoin('exam_papers as ep','ep.id','=','exam_create.paper_type')
                ->leftjoin('exam_type as type','type.id','=','exam_create.type_id')
                ->rightjoin('timetable_subjects as ts','ts.id','=','exam_create.subject_id')
                ->leftjoin('subject_master as sm','sm.id','=','ts.subject_master_id')
                ->leftjoin('exam_mark as em',function($j)use($request){
                    $j->on('em.exam_id','=','exam_create.id')
                    ->where('em.student_id',$request->student);
                })
               
                ->where([
                    ['exam_create.record_status','=',1]
                ])
                /*marksheet change*/
                ->orderBy('ts.sub_priority','ASC')
                // ->orderBy('type.priority','ASC')
                /*marksheet change*/
            ->get();
            // mapped subject
            $mapped_subject =[];

            foreach ($exams as $k => $v) {
                if($v->mapped_subject_name){
                    $mapped_subject[$v->subject_id] = $v->mapped_subject_name;
                }
            }
            
            // mapped subject end
            $commerce_marksheet =[];
             if($report_type==4){
               $commerce_marksheet= $this->CommerceMarsheet($exams);
              //dd($commerce_marksheet);
               
             }
             $science_marksheet =[];
             if($report_type==5 || $report_type == 6){
               $science_marksheet= $this->ScienceMarsheet($exams);
               foreach ($exams as $key => $value){

                        $term[$value->term_id.'-'.$value->exam_term][$value->type_id] = $value->exam_type.'=='.$value->max_mark;

                        if($value->subject_type == 0){
                            $op_subject[$value->subject_id.'-'.$value->subject][$value->term_id.'-'.$value->exam_term][$value->type_id] = $value;
                        }else{
                           
                           $subject[$value->subject_id.'-'.$value->subject][$value->term_id.'-'.$value->exam_term][$value->type_id] = $value;
                        }
                    }
              
             }
             else{
                 

            // dd($exams);
              /*marksheet change*/
              $disciplin= DB::table('exam_student_remark as esr')->select('edm.title as disciplin_name','esr.disciplin_grade','esr.remark','esr.term_id','esr.disciplin_id','exam_terms.title as term_name','edmp.title as disciplin_parent','edm.exam_disciplin_master_parent_id')
              ->leftjoin('exam_discipline_master as edm','edm.id','=','esr.disciplin_id')
              ->leftjoin('exam_disciplin_master_parent as edmp','edmp.id','=','edm.exam_disciplin_master_parent_id')
              ->leftjoin('exam_terms','exam_terms.id','=','esr.term_id')
              ->where('esr.branch_id',Session::get('activeBranch'))
              ->where('esr.session_id',Session::get('activeSession'))
              ->where('esr.student_id',$request->student)
               ->where(function($q)use($request){
                    if($request->term_id){
                        $q->where('esr.term_id',$request->term_id);
                    }
                    
                })
              ->where('esr.record_status',1)
              ->where('edm.status',1)
              ->orderBy('edm.priority','ASC')
              ->get();
             
                  // dd($disciplin);
              /*marksheet change*/
                if(count($exams)>0){
                    foreach ($exams as $key => $value){
                        if($value->subject_type == 1){
                            $term[$value->exam_term.'-'.$value->term_id][$value->type_id] = $value->exam_type.'=='.$value->max_mark;
                        }
                        if($value->subject_type == 0){
                            $op_subject[$value->subject_id.'-'.$value->subject][$value->exam_term.'-'.$value->term_id][$value->type_id] = $value;
                        }else{
                           
                           $subject[$value->subject_id.'-'.$value->subject][$value->exam_term.'-'.$value->term_id][$value->type_id] = $value;
                        }
                    }
                    
                    
                    
                    foreach($term as $k => $typ_arr){
                        foreach($typ_arr as $typ_k => $typ_val){
                            if(isset($op_subject)){
                                if(is_array($op_subject)){
                                    foreach ($op_subject as $key => $op_subject_term) {
                                        foreach($op_subject_term as $op_subject_term_name => $op_subject_term_value){
                                            foreach ($op_subject_term_value as $op_subject_type_key => $op_subject_type_value) {
                                                if(!isset($op_subject[$key][$k][$typ_k])){
                                                    $op_subject[$key][$k][$typ_k] = '';
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    // LOOP 1 TO GET ALL TERMS IN ARRAY
                    foreach ($subject as $sub_1 => $term_1) {
                        foreach ($term_1 as $term_1_id => $type_1) {

                            foreach ($subject as $sub_2 => $term_2) {
                                foreach ($term_2 as $term_2_id => $type_2) {
                                   if($sub_1 != $sub_2){
                                        
                                        foreach ($type_1 as $type_1_id => $sub_1_val) {

                                            foreach ($type_2 as $type_2_id => $sub_2_val) {
                                                
                                                if(!isset($subject[$sub_1][$term_2_id])){
                                                    $subject[$sub_1][$term_2_id][$type_2_id] ='';
                                                }
                                                

                                            }       
                           
                                        }
                                   }
                                }
                            }
                            
                        }
                    }
                    // END LOOP 1
                    // LOOP 2 TO GET ALL QUARTERS IN TERM ARRAY ARRAY
                    foreach ($subject as $sub_1 => $term_1) {
                        foreach ($term_1 as $term_1_id => $type_1) {

                            foreach ($subject as $sub_2 => $term_2) {
                                foreach ($term_2 as $term_2_id => $type_2) {
                                   if($sub_1 != $sub_2){
                                        // if(!isset($subject[$sub_1][$term_2_id])){
                                        //     $subject[$sub_1][$term_2_id][] ='';
                                        // }
                                        if($term_1_id == $term_2_id){
                                            foreach ($type_1 as $type_1_id => $sub_1_val) {

                                                foreach ($type_2 as $type_2_id => $sub_2_val) {
                                                    
                                                    if(!isset($subject[$sub_2][$term_2_id][$type_1_id])){
                                                        $subject[$sub_2][$term_2_id][$type_1_id]='';

                                                    }
                                                    

                                                }        
                               
                                            }

                                        }

                                        
                                   }
                                }
                            }
                            
                        }
                    }
                     $disc=[];
                    foreach ($disciplin as $key => $value) {
                        $disc[$value->exam_disciplin_master_parent_id.'='.$value->disciplin_parent][$value->disciplin_name][$value->term_name.'-'.$value->term_id]= $value;

                    }
                   
                  
                    // ksort($term);
                    // /* disciplin key set term wise */
                        foreach($disc as $discparent=>$discarr){
                            foreach($term as $term_id=>$term_name){
                                 foreach($discarr as $disciplin_key=>$disc_term_arr){

                                     if(!isset($disc[$discparent][$disciplin_key][$term_id])){
                                        $disc[$discparent][$disciplin_key][$term_id]='';
                                     }
                                      ksort($disc[$discparent][$disciplin_key]);
                                 }

                            }
                        }
                    // /*key set */
                   //dd($disc,$disciplin,$subject);
                  
                    ksort($term);
                    // dd($term);
                    foreach ($term as $term_name => $value) {
                        
                       ksort($term[$term_name]);
                    }
                    
                   
                   foreach ($subject as $sub_name => $temp_term) {
                       foreach ($temp_term as $t1 => $type) {
                           ksort($subject[$sub_name][$t1]);
                       }
                       ksort($subject[$sub_name]);
                   }
                   
                //   dd($term,$subject);
                  
                   if(isset($term) && isset($op_subject)){
                    $optional_subject = $this->get_optional_subject($op_subject,$term);
                    
                   }

                }else{

                    return redirect()->back()->with('message_warning','No Exam Found');
                }
            // mapped subject
                // dd($term,$optional_subject,$mapped_subject,$commerce_marksheet);
             }
            return view(parent::loadDataToView($this->view_path.'.generate'),compact('branch','student','term','subject','report_type','session','optional_subject','disc','science_marksheet','commerce_marksheet'));   
            // mapped subject end
        }
        else{
            return redirect()->back()->with('message_warning','Invalid Request');
        }  
        }catch(\Throwable $e){
            dd($e);
            return redirect()->back()->with('message_danger','Something went wrong or Incomplete Entries');
        }
    }
    public function generate_olg(Request $request){
        // dd($request->all());
        if($request->student && $request->course && $request->section && $request->report_type){
            $report_type = $request->report_type;
            $branch = DB::table('branches')->select('*')->where('id',Session::get('activeBranch'))->first();
            $session = DB::table('session')->select('*')->where('id',Session::get('activeSession'))->first();

            $student = DB::table('student_detail_sessionwise')->select('std.first_name','std.reg_no','std.date_of_birth','std.gender','fac.faculty as course','sem.semester','pd.father_first_name','std.student_image','pd.mother_first_name')
                ->leftjoin('students as std','std.id','=','student_detail_sessionwise.student_id')
                ->leftjoin('faculties as fac','fac.id','=','student_detail_sessionwise.course_id')
                ->leftjoin('semesters as sem','sem.id','=','student_detail_sessionwise.Semester')
                ->leftjoin('parent_details as pd','pd.students_id','=','student_detail_sessionwise.student_id')
                ->where([
                    ['student_detail_sessionwise.course_id','=',$request->course],
                    ['student_detail_sessionwise.Semester','=',$request->section],
                    ['student_detail_sessionwise.session_id','=',Session::get('activeSession')],
                    ['student_detail_sessionwise.active_status','=',1],
                    ['student_detail_sessionwise.student_id','=',$request->student],
                ])
            ->first();

            $exams = ExamCreate::select('exam_create.id','exam_create.title','exam_create.max_mark','exam_create.pass_mark','et.title as exam_term','em.mark as obtained_mark','em.attendance','type.title as exam_type','ts.title as subject','exam_create.subject_id','exam_create.type_id','exam_create.term_id','sm.is_main_subject as subject_type','em.grade')
                ->where([
                    ['exam_create.faculty_id','=',$request->course],
                    ['exam_create.section_id','=',$request->section],
                    ['exam_create.session_id','=',Session::get('activeSession')],
                    ['exam_create.branch_id','=',Session::get('activeBranch')],
                    ['exam_create.result_status','=',1],
                ])
                ->leftjoin('exam_terms as et','et.id','=','exam_create.term_id')
                ->leftjoin('exam_type as type','type.id','=','exam_create.type_id')
                ->rightjoin('timetable_subjects as ts','ts.id','=','exam_create.subject_id')
                ->leftjoin('subject_master as sm','sm.id','=','ts.subject_master_id')
                ->leftjoin('exam_mark as em',function($j)use($request){
                    $j->on('em.exam_id','=','exam_create.id')
                    ->where('em.student_id',$request->student);
                })
                ->where([
                    ['exam_create.record_status','=',1]
                ])
                /*marksheet change*/
                ->orderBy('ts.sub_priority','ASC')
                /*marksheet change*/
            ->get();
            // dd($exams);
              /*marksheet change*/
              $disciplin= DB::table('exam_student_remark as esr')->select('edm.title as disciplin_name','esr.disciplin_grade','esr.remark','esr.term_id','esr.disciplin_id','exam_terms.title as term_name','edmp.title as disciplin_parent','edm.exam_disciplin_master_parent_id')
              ->leftjoin('exam_discipline_master as edm','edm.id','=','esr.disciplin_id')
              ->leftjoin('exam_disciplin_master_parent as edmp','edmp.id','=','edm.exam_disciplin_master_parent_id')
              ->leftjoin('exam_terms','exam_terms.id','=','esr.term_id')
              ->where('esr.branch_id',Session::get('activeBranch'))
              ->where('esr.session_id',Session::get('activeSession'))
              ->where('esr.student_id',$request->student)
              ->where('esr.record_status',1)
              ->where('edm.status',1)
              ->orderBy('edm.priority','ASC')
              ->get();
             

              /*marksheet change*/
                if(count($exams)>0){
                    foreach ($exams as $key => $value){

                        $term[$value->term_id.'-'.$value->exam_term][$value->type_id] = $value->exam_type.'=='.$value->max_mark;

                        if($value->subject_type == 0){
                            $op_subject[$value->subject_id.'-'.$value->subject][$value->term_id.'-'.$value->exam_term][$value->type_id] = $value;
                        }else{
                           
                           $subject[$value->subject_id.'-'.$value->subject][$value->term_id.'-'.$value->exam_term][$value->type_id] = $value;
                        }
                    }
                    foreach($term as $k => $typ_arr){
                        foreach($typ_arr as $typ_k => $typ_val){
                            if(isset($op_subject)){
                                if(is_array($op_subject)){
                                    foreach ($op_subject as $key => $op_subject_term) {
                                        foreach($op_subject_term as $op_subject_term_name => $op_subject_term_value){
                                            foreach ($op_subject_term_value as $op_subject_type_key => $op_subject_type_value) {
                                                if(!isset($op_subject[$key][$k][$typ_k])){
                                                    $op_subject[$key][$k][$typ_k] = '';
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    // LOOP 1 TO GET ALL TERMS IN ARRAY
                    foreach ($subject as $sub_1 => $term_1) {
                        foreach ($term_1 as $term_1_id => $type_1) {

                            foreach ($subject as $sub_2 => $term_2) {
                                foreach ($term_2 as $term_2_id => $type_2) {
                                   if($sub_1 != $sub_2){
                                        
                                        foreach ($type_1 as $type_1_id => $sub_1_val) {

                                            foreach ($type_2 as $type_2_id => $sub_2_val) {
                                                
                                                if(!isset($subject[$sub_1][$term_2_id])){
                                                    $subject[$sub_1][$term_2_id][$type_2_id] ='';
                                                }
                                                

                                            }       
                           
                                        }
                                   }
                                }
                            }
                            
                        }
                    }
                    // END LOOP 1
                    // LOOP 2 TO GET ALL QUARTERS IN TERM ARRAY ARRAY
                    foreach ($subject as $sub_1 => $term_1) {
                        foreach ($term_1 as $term_1_id => $type_1) {

                            foreach ($subject as $sub_2 => $term_2) {
                                foreach ($term_2 as $term_2_id => $type_2) {
                                   if($sub_1 != $sub_2){
                                        // if(!isset($subject[$sub_1][$term_2_id])){
                                        //     $subject[$sub_1][$term_2_id][] ='';
                                        // }
                                        if($term_1_id == $term_2_id){
                                            foreach ($type_1 as $type_1_id => $sub_1_val) {

                                                foreach ($type_2 as $type_2_id => $sub_2_val) {
                                                    
                                                    if(!isset($subject[$sub_2][$term_2_id][$type_1_id])){
                                                        $subject[$sub_2][$term_2_id][$type_1_id]='';

                                                    }
                                                    

                                                }        
                               
                                            }

                                        }

                                        
                                   }
                                }
                            }
                            
                        }
                    }
                    $disc=[];
                    foreach ($disciplin as $key => $value) {
                        $disc[$value->exam_disciplin_master_parent_id.'='.$value->disciplin_parent][$value->disciplin_name][$value->term_id.'-'.$value->term_name]= $value;

                    }
                   
                  
                    // ksort($term);
                    // /* disciplin key set term wise */
                        foreach($disc as $discparent=>$discarr){
                            foreach($term as $term_id=>$term_name){
                                 foreach($discarr as $disciplin_key=>$disc_term_arr){

                                     if(!isset($disc[$discparent][$disciplin_key][$term_id])){
                                        $disc[$discparent][$disciplin_key][$term_id]='';
                                     }
                                     // ksort($disc[$discparent][$disciplin_key]);
                                 }

                            }
                        }
                    // /*key set */
                    // dd($term);
                     // dd($disc);
                    foreach ($term as $term_name => $value) {

                       ksort($term[$term_name]);
                    }
                    ksort($term);
                   
                   foreach ($subject as $sub_name => $temp_term) {
                       foreach ($temp_term as $t1 => $type) {
                           ksort($subject[$sub_name][$t1]);

                       }
                       ksort($subject[$sub_name]);
                   }
                  
                   if(isset($term) && isset($op_subject)){
                    $optional_subject = $this->get_optional_subject($op_subject,$term);
                    
                   }

                }else{

                    return redirect()->back()->with('message_warning','No Exam Found');
                }
                dd($subject,$term,$disc,$optional_subject);
            return view(parent::loadDataToView($this->view_path.'.generate'),compact('branch','student','term','subject','report_type','session','optional_subject','disc'));   
        }
        else{
            return redirect()->back()->with('message_warning','Invalid Request');
        }   
    }

    public function get_table_and_dropdown(){
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

      $data['faculty'] = $this->activeFaculties();

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
        $data['section']= array_prepend($data['section'],'--Select Section--','');
        /*teacher access*/
        //  $data['faculty'] = Faculty::where([
        //     ['status','=',1],
        //     ['branch_id','=',Session::get('activeBranch')],
        // ])->select('id','faculty as title')->orderBy('title','asc')->pluck('title','id')->toArray();
        // $data['faculty'] = array_prepend($data['faculty'],'--Select '.env('course_label').'--','');
         $data['exam'] = ExamCreate::where([
            ['exam_create.record_status','=',1],
            ['exam_create.branch_id','=',Session::get('activeBranch')],
            ['exam_create.session_id','=',Session::get('activeSession')],
        ])->select('exam_create.id','exam_create.title as exam_title','exam_create.description as exam_description','term.title as term','type.title as type','fac.faculty as faculty','sem.semester as section','mode.title as mode','paper.title as paper','sub.title as subject','max_mark','pass_mark')
         ->leftjoin('exam_terms as term','term.id','=','exam_create.term_id')
         ->leftjoin('exam_type as type','type.id','=','exam_create.type_id')
         ->leftjoin('faculties as fac','fac.id','=','exam_create.faculty_id')
         ->leftjoin('semesters as sem','sem.id','=','exam_create.section_id')
         ->leftjoin('exam_modes as mode','mode.id','=','exam_create.mode_id')
         ->leftjoin('exam_papers as paper','paper.id','=','exam_create.paper_type')
         ->leftjoin('timetable_subjects as sub','sub.id','=','exam_create.subject_id')
         ->get();
        return $data;
    }
    public function assessment(){
        $dropdowns = $this->get_table_and_dropdown(); 
        return view(parent::loadDataToView($this->view_path.'.assessment'),compact('dropdowns'));
    }
    public function maxMarkByExamId($exam_id){
         
         $exam_data= DB::table('exam_create')
          ->select('id','faculty_id','section_id','subject_id','term_id','type_id')
          ->where('id',$exam_id)
          ->first();
          //dd($exam_data);
          $exam= DB::table('exam_create')
          ->where('term_id',$exam_data->term_id)
          ->where('type_id',$exam_data->type_id)
          ->where('faculty_id',$exam_data->faculty_id)
          ->where('subject_id',$exam_data->subject_id)->pluck('id')->toArray();
          
	        $max_mark = DB::table('exam_mark')->selectRaw('MAX(mark) as max_mark')->whereIN('exam_id',$exam)->first();
	        //dd($max_mark);

          return $max_mark->max_mark?$max_mark->max_mark:'-';
    }
    public function getGrade($exam_id,$percentage){
        $data = DB::table('exam_create')->select('gs.name as grade')
            ->leftjoin('grading_types as gt',function($j){
                $j->on('gt.id','=','exam_create.grading_type');
            })
            ->leftjoin('grading_scales as gs',function($j)use($percentage){
                $j->on('gs.gradingType_id','=','gt.id')
                ;
            })
            ->where('gs.percentage_from','<=',$percentage)
                ->Where('gs.percentage_to','>=',$percentage)
            ->where('exam_create.id',$exam_id)
            ->first();
        if(isset($data->grade)){
            return $data->grade;    
        }else{
            return '-';
        }    
        // return $data->grade;
        // return 'a';
    }
    
    public function get_optional_subjectbkp($subject,$term){
        $op_subject = [];
        
        // LOOP 1 TO GET ALL TERMS IN ARRAY
        foreach ($subject as $sub_1 => $term_1) {
            foreach ($term_1 as $term_1_id => $type_1) {

                foreach ($subject as $sub_2 => $term_2) {
                    foreach ($term_2 as $term_2_id => $type_2) {
                       if($sub_1 != $sub_2){
                            
                            foreach ($type_1 as $type_1_id => $sub_1_val) {

                                foreach ($type_2 as $type_2_id => $sub_2_val) {
                                    
                                    if(!isset($subject[$sub_1][$term_2_id])){
                                        $subject[$sub_1][$term_2_id][$type_2_id] ='';
                                    }
                                    

                                }       
               
                            }
                       }
                    }
                }
                
            }
        }
        // END LOOP 1
        // LOOP 2 TO GET ALL QUARTERS IN TERM ARRAY ARRAY
        foreach ($subject as $sub_1 => $term_1) {
            foreach ($term_1 as $term_1_id => $type_1) {

                foreach ($subject as $sub_2 => $term_2) {
                    foreach ($term_2 as $term_2_id => $type_2) {
                       if($sub_1 != $sub_2){
                            // if(!isset($subject[$sub_1][$term_2_id])){
                            //     $subject[$sub_1][$term_2_id][] ='';
                            // }
                            if($term_1_id == $term_2_id){
                                foreach ($type_1 as $type_1_id => $sub_1_val) {

                                    foreach ($type_2 as $type_2_id => $sub_2_val) {
                                        
                                        if(!isset($subject[$sub_2][$term_2_id][$type_1_id])){
                                            $subject[$sub_2][$term_2_id][$type_1_id]='';

                                        }
                                        

                                    }       
                   
                                }

                            }

                            
                       }
                    }
                }
                
            }
        }
        foreach ($term as $term_name => $value) {
           ksort($term[$term_name]);
        }
       foreach ($subject as $sub_name => $temp_term) {
           foreach ($temp_term as $t1 => $type) {
               ksort($subject[$sub_name][$t1]);
           }
       }
        if(isset($term)){
            // $data['op_term'] = $term;
        }

        if(isset($subject)){
            $op_subject = $subject;
        }

        return $op_subject;
    }
    public function get_optional_subject($subject,$term){
        $op_subject = [];
        
        // LOOP 1 TO GET ALL TERMS IN ARRAY
        foreach ($subject as $sub_1 => $term_1) {
            foreach ($term_1 as $term_1_id => $type_1) {

                foreach ($subject as $sub_2 => $term_2) {
                    foreach ($term_2 as $term_2_id => $type_2) {
                       if($sub_1 != $sub_2){
                            
                            foreach ($type_1 as $type_1_id => $sub_1_val) {

                                foreach ($type_2 as $type_2_id => $sub_2_val) {
                                    
                                    if(!isset($subject[$sub_1][$term_2_id])){
                                        $subject[$sub_1][$term_2_id][$type_2_id] ='';
                                    }
                                    

                                }       
               
                            }
                       }
                    }
                }
                
            }
        }
        // END LOOP 1
        // LOOP 2 TO GET ALL QUARTERS IN TERM ARRAY ARRAY
        foreach ($subject as $sub_1 => $term_1) {
            foreach ($term_1 as $term_1_id => $type_1) {

                foreach ($subject as $sub_2 => $term_2) {
                    foreach ($term_2 as $term_2_id => $type_2) {
                       if($sub_1 != $sub_2){
                            // if(!isset($subject[$sub_1][$term_2_id])){
                            //     $subject[$sub_1][$term_2_id][] ='';
                            // }
                            if($term_1_id == $term_2_id){
                                foreach ($type_1 as $type_1_id => $sub_1_val) {

                                    foreach ($type_2 as $type_2_id => $sub_2_val) {
                                        
                                        if(!isset($subject[$sub_2][$term_2_id][$type_1_id])){
                                            $subject[$sub_2][$term_2_id][$type_1_id]='';

                                        }
                                        

                                    }       
                   
                                }

                            }

                            
                       }
                    }
                }
                
            }
        }
        foreach ($term as $term_name => $value) {
           ksort($term[$term_name]);
        }
       foreach ($subject as $sub_name => $temp_term) {
           foreach ($temp_term as $t1 => $type) {
               ksort($subject[$sub_name][$t1]);
           }
           ksort($subject[$sub_name]);
       }
        if(isset($term)){
            // $data['op_term'] = $term;
        }

        if(isset($subject)){
            $op_subject = $subject;
        }

        return $op_subject;
    }
    
    
    public function studentMarkExcel(Request $request)
    {
        $dropdowns = $this->get_table_and_dropdown();
         if($request->all()){
             $class= DB::table('faculties')->select('faculty')->where('id',$request->faculty)->first();
              $section= DB::table('semesters')->select('semester')->where('id',$request->section)->first();
              $branch= DB::table('branches')->select('branch_name')->where('id',session::get('activeBranch'))->first();
            $student = DB::table('student_detail_sessionwise')->select('std.id','std.first_name','std.reg_no','std.date_of_birth','std.gender','fac.faculty as course','sem.semester','pd.father_first_name','std.admission_condition')
                ->leftjoin('students as std','std.id','=','student_detail_sessionwise.student_id')
                ->leftjoin('faculties as fac','fac.id','=','student_detail_sessionwise.course_id')
                ->leftjoin('semesters as sem','sem.id','=','student_detail_sessionwise.Semester')
                ->leftjoin('parent_details as pd','pd.students_id','=','student_detail_sessionwise.student_id')
                ->where([
                    ['student_detail_sessionwise.course_id','=',$request->faculty],
                    ['student_detail_sessionwise.Semester','=',$request->section],
                    ['student_detail_sessionwise.session_id','=',Session::get('activeSession')],
                    ['std.branch_id','=',Session::get('activeBranch')],
                    ['student_detail_sessionwise.active_status','=',1],
                    ['std.status','=',1],
                ])
            //->limit(1)
                ->orderBy('first_name','asc')
             ->get();
             //dd($student);
             $resulttype= DB::table('exam_result_type')->select('result_type_id')
                  ->where('course_id',$request->faculty)
                  ->where('branch_id',Session::get('activeBranch'))
                  ->where('session_id',Session::get('activeSession'))->first();

            $disciplin_master= DB::table('exam_discipline_master')
                   ->where('exam_discipline_master.result_type_id',$resulttype->result_type_id)->get(); 
                   //dd($disciplin_master);
             $exams=[];
             foreach ($student as $std_k => $std_val) {
                $exams = ExamCreate::select('exam_create.id','exam_create.title','exam_create.max_mark','exam_create.pass_mark','et.title as exam_term','em.mark as obtained_mark','em.attendance','type.title as exam_type','ts.title as subject','exam_create.subject_id','exam_create.type_id','exam_create.term_id','sm.is_main_subject as subject_type','em.grade')
                ->where([
                    ['exam_create.faculty_id','=',$request->faculty],
                    ['exam_create.section_id','=',$request->section],
                    ['exam_create.session_id','=',Session::get('activeSession')],
                    ['exam_create.branch_id','=',Session::get('activeBranch')],
                    ['exam_create.result_status','=',1],
                ])
                ->leftjoin('exam_terms as et','et.id','=','exam_create.term_id')
                ->leftjoin('exam_type as type','type.id','=','exam_create.type_id')
                ->leftjoin('timetable_subjects as ts','ts.id','=','exam_create.subject_id')
                ->leftjoin('subject_master as sm','sm.id','=','ts.subject_master_id')
                ->leftjoin('exam_mark as em',function($j)use($std_val){
                    $j->on('em.exam_id','=','exam_create.id')
                    ->where('em.student_id',$std_val->id);
                })
                ->where([
                    ['exam_create.record_status','=',1]
                ])
                /*marksheet change*/
                ->orderBy('ts.sub_priority','ASC')
                /*marksheet change*/
              
               ->get();
                //dd($exams);
                
                
                if($resulttype){
                 $disciplin= DB::table('exam_student_remark as esr')->select('edm.title as disciplin_name','esr.disciplin_grade','esr.remark','esr.term_id','esr.disciplin_id','exam_terms.title as term_name')
                  ->leftjoin('exam_discipline_master as edm','edm.id','=','esr.disciplin_id')
                  ->leftjoin('exam_terms','exam_terms.id','=','esr.term_id')
                  ->where('esr.branch_id',Session::get('activeBranch'))
                  ->where('esr.session_id',Session::get('activeSession'))
                  ->where('esr.student_id',$std_val->id)
                  ->where('esr.record_status',1)
                  ->where('edm.result_type_id',$resulttype->result_type_id)
                  ->orderBy('edm.priority','ASC')
                  ->orderBy('esr.term_id','ASC')
                  ->get();

                
                }
                

                foreach ($exams as $key => $value){
                        if($value->subject_type == 0){
                            $op_subject_master[$std_val->id][$value->subject_id.'=='.$value->subject][$value->term_id.'-'.$value->exam_term][$value->type_id] = $value;
                            $op_sub_master[$value->subject_id.'-'.$value->subject][$value->term_id.'-'.$value->exam_term][$value->type_id] = $value->exam_type.'=='.$value->max_mark;
                        }else{
                           $subject[$std_val->id][$value->subject_id.'-'.$value->subject][$value->term_id.'-'.$value->exam_term][$value->type_id] = $value;

                           $main_sub_master[$value->subject_id.'-'.$value->subject][$value->term_id.'-'.$value->exam_term][$value->type_id] = $value->exam_type.'=='.$value->max_mark;
                        }
                    }

                    /*disciplin array*/
                    foreach ($disciplin as $key => $value) {
                     $disc[$std_val->id][$value->disciplin_name][]= $value;
                     }


             }
             
            // dd($main_sub_master,$subject);
            foreach ($main_sub_master as $main_sub_name => $main_sub_value) {
                foreach ($main_sub_value as $key => $value) {
                    ksort($main_sub_master[$main_sub_name][$key]);
                }
               ksort($main_sub_master[$main_sub_name]);
            }
           foreach($subject as $st_id => $st_val){
                foreach ($st_val as $sub_name => $temp_term) {

                   foreach ($temp_term as $t1 => $type) {
                       ksort($subject[$st_id][$sub_name][$t1]);
                   }
                   ksort($subject[$st_id][$sub_name]);
                }   
           }

           foreach ($op_sub_master as $main_sub_name => $main_sub_value) {
                foreach ($main_sub_value as $key => $value) {
                    ksort($op_sub_master[$main_sub_name][$key]);
                }
               ksort($op_sub_master[$main_sub_name]);
            }

            foreach($op_subject_master as $st_id => $st_val){
                foreach ($st_val as $sub_name => $temp_term) {

                   foreach ($temp_term as $t1 => $type) {
                       ksort($op_subject_master[$st_id][$sub_name][$t1]);
                   }
                   ksort($op_subject_master[$st_id][$sub_name]);
                }   
           }
         


           // Adjusting Array if subject/exam not created according to main_sub_maste
           foreach ($main_sub_master as $sub_name => $term_arr) {
               foreach ($term_arr as $term_name => $type_arr) {
                   foreach ($type_arr as $type_key => $value) {
                      if(isset($subject)){
                                if(is_array($subject)){
                                    foreach ($subject as $std_id => $subject_arr) {
                                        foreach($subject_arr as $subject_name => $subject_term_arr){
                                            foreach ($subject_term_arr as $subject_term_name => $subject_type_arr) {
                                                foreach ($subject_type_arr as $subject_type_name => $subject_type_val) {
                                                    if(!isset($subject[$std_id][$subject_name][$subject_term_name][$subject_type_name])){
                                                        $subject[$std_id][$subject_name][$subject_term_name][$subject_type_name] = '';
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                   }
               }
           }


          

            
            foreach ($op_sub_master as $sub_name => $term_arr) {
               foreach ($term_arr as $term_name => $type_arr) {
                   foreach ($type_arr as $type_key => $value) {
                      if(isset($op_subject_master)){
                                if(is_array($op_subject_master)){
                                    foreach ($op_subject_master as $std_id => $subject_arr) {
                                        foreach($subject_arr as $subject_name => $subject_term_arr){
                                            foreach ($subject_term_arr as $subject_term_name => $subject_type_arr) {
                                                foreach ($subject_type_arr as $subject_type_name => $subject_type_val) {
                                                    if(!isset($op_subject_master[$std_id][$subject_name][$subject_term_name][$subject_type_name])){
                                                        $op_subject_master[$std_id][$subject_name][$subject_term_name][$subject_type_name] = '';
                                                    }
                                                }
                                            }    
                                        }
                                    }
                                }
                            }
                   }
               }
           }

            
         
           
          
            return view(parent::loadDataToView($this->view_path.'.mark-excel.includes.printexcel'),compact('dropdowns','student','subject','op_subject_master','main_sub_master','op_sub_master','disc','disciplin_master','class','section','branch'));

         } 
        return view(parent::loadDataToView($this->view_path.'.mark-excel.index'),compact('dropdowns'));
    }
}