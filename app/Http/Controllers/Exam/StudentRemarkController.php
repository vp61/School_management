<?php

namespace App\Http\Controllers\Exam;

use App\Http\Controllers\CollegeBaseController;
use App\Http\Controllers\Api\ServicesController;
use Illuminate\Http\Request;
use App\Models\Exam\ExamTerm;
use App\Models\Faculty;
use Carbon\Carbon;
use URL,Session,DB;
use App\Models\Exam\ResultType;

/*--teacher access--*/
use Auth;
use App\Models\TeacherCoordinator;
/*--teacher access--*/

class StudentRemarkController extends CollegeBaseController
{
    protected $base_route = 'exam.studentRemark';
    protected $view_path = 'exam.studentRemark';
    protected $panel = 'Exam Remark';
    protected $filter_query = [];

    public function __construct()
    {

    }

    public function index(Request $request)
    {
          $data['url'] = URL::current();
          $data['filter_query'] = $this->filter_query;
          $data['faculties']= $this->activeFaculties();
          $data['term'] = ExamTerm::where([
            ['record_status','=',1],
            ['branch_id','=',Session::get('activeBranch')],
            ['session_id','=',Session::get('activeSession')],
          ])->select('id','title')->pluck('title','id')->toArray();
          $data['term'] = array_prepend($data['term'],'--Select Term--','');
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
        // $data['section']= array_prepend($data['section'],'--Select Section--','');
        /*teacher access*/
        if($request->all()){
          // dd($request->all());
           $term= $request->term;
           $section= $request->semester;
          $data['section'] = DB::table('faculty_semester')
          ->where(function($q)use($request){
              if($request->faculty!=''){
                  $q->where('faculty_id',$request->faculty);
              }
          })
         ->select('sem.id','semester')
         ->leftjoin('semesters as sem','sem.id','=','faculty_semester.semester_id')
         ->pluck('semester','id')->toArray();
          

           $resultType= ResultType::select('result_type_id')
           ->where('course_id',$request->faculty)
           ->where('branch_id',session::get('activeBranch'))
           ->where('session_id',session::get('activeSession'))
           ->first();
           // dd($resultType,session::get('activeBranch'),session::get('activeSession'));
           if($resultType){
            $result_type_id= $resultType->result_type_id;
           }
           else{
            $result_type_id='';
           }
         // dd($result_type_id,$request->all());
          $data['student']=DB::table('student_detail_sessionwise as sds')->select('students.id','students.first_name','students.middle_name','students.last_name','reg_no')
          ->leftjoin('students','students.id','=','sds.student_id')
          ->where('students.branch_id',session::get('activeBranch'))   
          ->where('sds.session_id',session::get('activeSession'))
          ->where('sds.course_id',$request->faculty)
          ->where('sds.Semester',$request->semester)
          ->where('sds.active_status',1)
          ->where('students.status',1)
          ->get();
          $data['disciplin']= DB::Table('exam_discipline_master')->select('*')
          ->where('status',1)
          ->where('result_type_id',$result_type_id)
          ->get();
          // dd($data);
        
          $data['Remark']= DB::table('exam_student_remark')->select('*')
          ->where('branch_id',session::get('activeBranch'))
          ->where('session_id',session::get('activeSession'))
          ->where('term_id',$term)->get();
          
          
          $data['disciplin_remark']= DB::table('exam_disciplin_mark')->pluck('title','title')->toArray();
           $data['disciplin_remark'] = array_prepend($data['disciplin_remark'],'--Select--','');
          return view(parent::loadDataToView($this->view_path.'.index'),compact('data','term','section'));
            
        }
        return view(parent::loadDataToView($this->view_path.'.index'),compact('data'));
    }
    public function store(Request $request)
    {
         
         // dd($request->all());
        $grade= $request->grade;
        foreach ($grade as $key => $value) {
          foreach ($value as $k => $v) {

             $insert= DB::table('exam_student_remark')->UpdateOrInsert([
              'term_id'=>$request->term_id,
              'student_id'=>$key,
              'disciplin_id'=>$k,

             ],
             [
              'disciplin_grade'=>$v,
              'remark'=>$request->remark[$key],
              'created_at'=>Carbon::now(),
              'created_by'=>auth()->user()->id,
              'branch_id'=>session::get('activeBranch'),
              'session_id'=>session::get('activeSession'),

             ]);
          }
        }

        $request->session()->flash($this->message_success, $this->panel. ' Added Successfully.');
        return redirect()->route($this->base_route);
       
    }
    
    
   
            
   
}