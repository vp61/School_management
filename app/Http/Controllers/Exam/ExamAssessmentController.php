<?php

namespace App\Http\Controllers\Exam;

use App\Http\Controllers\CollegeBaseController;
use App\Http\Controllers\Api\ServicesController;
use Illuminate\Http\Request;
use App\Models\Exam\ExamCreate;
use App\Models\Exam\ExamMode;
use App\Models\Exam\ExamType;
use App\Models\Exam\ExamTerm;
use App\Models\Exam\ExamPaper;
use App\Models\Exam\ExamAddQuestion;
use App\Models\Year;
use App\Models\Attendance;
use App\Models\Faculty;
use Carbon\Carbon;
/*--teacher access--*/
use Session,DB,Auth;

use App\Models\TeacherCoordinator;
/*--teacher access--*/

class ExamAssessmentController extends CollegeBaseController
{
    protected $base_route = 'exam.assessment';
    protected $view_path = 'exam.assessment';
    protected $panel = 'Exams';
    protected $filter_query = [];

    public function __construct()
    {

    }

    public function index(Request $request)
    {
        $dropdowns = $this->get_table_and_dropdown(); 
        return view(parent::loadDataToView($this->view_path.'.index'),compact('dropdowns'));
    }
    public function getExamByMode(Request $request){
        $data = [];
        $data['error'] = true;
        if($request->faculty && $request->section && $request->session && $request->branch && $request->term && $request->mode){
            $exams = ExamCreate::select('exam_create.id','exam_create.title','et.title as type')
            ->leftjoin('exam_type as et','et.id','=','exam_create.type_id')
            ->where([
                ['exam_create.faculty_id','=',$request->faculty],
                ['exam_create.section_id','=',$request->section],
                ['exam_create.term_id','=',$request->term],
                ['exam_create.session_id','=',$request->session],
                ['exam_create.branch_id','=',$request->branch],
                ['exam_create.mode_id','=',$request->mode],
                ['exam_create.record_status','=',1],
            ])->get();
            if(count($exams)>0){
                $data['error'] = false;
                $data['msg']   = 'Exams Found';
                $data['data']  = $exams; 
            }else{
                $data['msg']   = 'No Exam Found';
            }
        }elseif(!$request->section){
            $data['msg']   = 'Please Select Section!';
            $data['info']   = true;
        }elseif(!$request->term){
            $data['msg']   = 'Please Select Term!';
            $data['info']   = true;
        }elseif(!$request->mode){
            $data['msg']   = 'Please Select Mode!';
            $data['info']   = true;
        }
        else{
            $data['msg']   = 'Invalid Request!';
        }
        return response()->json(json_encode($data));
    }
    public function add_assessment(Request $request){
        if($request->exam){
            $exam = ExamCreate::select('exam_create.*','fac.faculty','sem.semester as section','subject.title as subject')
            ->leftjoin('faculties as fac','fac.id','=','exam_create.faculty_id')
            ->leftjoin('semesters as sem','sem.id','=','exam_create.section_id')
            ->leftjoin('timetable_subjects as subject','subject.id','=','exam_create.subject_id')
            ->where('exam_create.id',$request->exam)->first();
            
             $subject_type= $this->IsmainSub($exam->subject_id);
            $students = DB::table('student_detail_sessionwise')->select('std.first_name','std.id','std.reg_no','pd.father_first_name as father_name')
            ->leftjoin('students as std','std.id','=','student_detail_sessionwise.student_id')
            ->leftjoin('parent_details as pd','pd.students_id','=','student_detail_sessionwise.student_id')
            ->where([
                ['student_detail_sessionwise.course_id','=',$exam->faculty_id],
                ['student_detail_sessionwise.Semester','=',$exam->section_id],
                ['student_detail_sessionwise.session_id','=',$exam->session_id],
                ['student_detail_sessionwise.active_status','=',1],
                ['std.branch_id','=',$exam->branch_id],
                ['std.status','=',1],
            ])
            ->orderBy('std.first_name','asc')
            ->get();
            foreach ($students as $key => $value) {
                $marks[$value->id] = DB::table('exam_create')->select('max_mark','assessment_status','pass_mark','mark as obtained_mark','attendance','em.grade')
                /*assesmentGrade*/
                ->leftjoin('exam_mark as em',function($join)use($exam,$value){
                    $join->on('em.exam_id','=','exam_create.id')
                    ->where([
                        ['em.student_id','=',$value->id],
                    ]);
                })
                ->where([
                    ['exam_create.id','=',$exam->id],
                    ['exam_create.record_status','=',1]
                ])->first();
            }
            return view(parent::loadDataToView($this->view_path.'.add'),compact('marks','exam','students','subject_type'));
        }else{
            return redirect()->back()->with('message_warning','Invalid Request!');
        }
    }
    public function view_student_answer($exam_id,$student_id){
        $services = new ServicesController;
        $req = [
            'exam_id' => $exam_id,
            'student_id' => $student_id
        ];
        $request = new Request($req);
        $response =$services->questionwiseStudentAnswer($request);
        $content = $response->getContent();
        $data = json_decode($content);
        $data = $data->data;
        $exam = $data->exam;
        if(!$exam){
            return redirect()->route($this->base_route)->with('message_danger','Invalid Exam');
        }
        $answer = $data->answer;
        $student = DB::table('student_detail_sessionwise')->select('std.id as student_id','std.first_name as student_name','std.reg_no','pd.father_first_name as father_name')
        ->leftjoin('students as std','std.id','=','student_detail_sessionwise.student_id')
        ->leftjoin('parent_details as pd','pd.students_id','=','std.id')
        ->where([
            ['student_detail_sessionwise.student_id','=',$student_id],
            ['student_detail_sessionwise.session_id','=',Session::get('activeSession')],
        ])->first();

        return view(parent::loadDataToView($this->view_path.'.view'),compact('data','exam','answer','student'));
    }
    public function save_exam_mark(Request $request){
       if($request->exam_id && $request->student_id && $request->obtained_mark){
            $exam = ExamCreate::find($request->exam_id);
            if(!$exam){
                parent::invalidRequest();
            }
            $total_mark = 0;
            foreach ($request->obtained_mark as $key => $value) {
                DB::table('exam_question_answer')
                ->where('id',$key)
                ->update([
                    'obtained_mark' => $value,
                    'updated_by'    => auth()->user()->id,
                    'updated_at'    => Carbon::now()
                ]);
                $total_mark = $total_mark + $value;
            }
            $insert = DB::table('exam_mark')->updateOrInsert([
                'exam_id'       => $request->exam_id,
                'student_id'    => $request->student_id
            ],
            [   
                'assessment_status' => 1,
                'mark'              => $total_mark,
                'updated_at'        => Carbon::now(),
                'updated_by'        => auth()->user()->id
            ]);
            if($insert){
                $date = Carbon::parse($exam->date);
                $month = Carbon::createFromFormat('Y-m-d H:i:s', $date)->month;
                $day = "day_".Carbon::createFromFormat('Y-m-d H:i:s', $date)->day;
                $yearTitle = Carbon::createFromFormat('Y-m-d H:i:s', $date)->year;
                $year = Year::where('title',$yearTitle)->first()->id;
                $attendanceExist = Attendance::select('attendances.id','attendances.attendees_type','attendances.link_id',
                            'attendances.years_id','attendances.months_id','attendances.'.$day,
                            's.id as students_id','s.reg_no','s.first_name','s.middle_name','s.last_name','s.student_image')
                            ->where('attendances.attendees_type',1)
                            ->where('attendances.years_id',$year)
                            ->where('attendances.months_id',$month)
                            ->where([['s.id', '=' , $request->student_id]])
                            ->join('students as s','s.id','=','attendances.link_id')
                            ->first();
                        if ($attendanceExist) {
                            $Update = [
                                'attendees_type' => 1,
                                'link_id' => $request->student_id,
                                'years_id' => $year,
                                'months_id' => $month,
                                $day => 1,
                                'last_updated_by' => auth()->user()->id
                            ];
                            $update = $attendanceExist->update($Update);
                        }else{
                            $data = Attendance::create([
                                'attendees_type' => 1,
                                'link_id' => $request->student_id,
                                'years_id' => $year,
                                'months_id' => $month,
                                $day => 1,
                                'created_by' => auth()->user()->id,
                            ]);
                        }
            }
            return redirect()->route('exam.assessment.add',['faculty_id' => $exam->faculty_id,'section'=> $exam->section_id,'term'=>$exam->term_id,'exam_mode'=>$exam->mode_id,'exam'=>$exam->id])->with('message_success','Mark Updated Successfully');
       }
    }
    public function store_assessment(Request $request)
    {
       
        if($request->mark && $request->attendance){
            $data['mark'] = $request->mark;
            /*assesmentGrade*/
            $data['grade'] = $request->grade;
            /*assesmentGrade*/
            $data['attendance'] = $request->attendance;
            $data['exam_id']    = $request->exam_id;
            $data['user_id']    = auth()->user()->id;
            $api = new \App\Http\Controllers\Api\ServicesController();
            $req = new Request($data);
            $response = $api->save_assessment($req);
            $response = json_decode($response);
            if($response['data']['error']){
                return redirect()->route($this->base_route)->with('message_warning','Something went wrong');
            }else{
                return redirect()->route($this->base_route)->with('message_success','Assessment Successful');
            }
        }
        return redirect()->route($base_route)->with('message_warning','Invalid Request');
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
        ->where(function($q)use($faculty_id){
            if($faculty_id){
                $q->where('faculty_id',$faculty_id);
            }
        })
        ->select('sem.id','semester')
        ->leftjoin('semesters as sem','sem.id','=','faculty_semester.semester_id')
        ->Where(function($q)use($classTeacher,$ability){
            if(count($classTeacher)>0 && (!$ability)){
                $q->whereIn('sem.id',$classTeacher);
            }
        })
        ->pluck('semester','id')->toArray();

        /* --teacher access-- */
        
        
        
        
        // $data['section'] = DB::table('faculty_semester')
        // ->where(function($q)use($faculty_id){
        //     if($faculty_id){
        //         $q->where('faculty_id',$faculty_id);
        //     }
        // })
        // ->select('sem.id','semester')
        // ->leftjoin('semesters as sem','sem.id','=','faculty_semester.semester_id')
        // ->pluck('semester','id')->toArray();
        // $data['section'] = array_prepend($data['section'],'--Select Section--','');


        $data['subject'] = DB::table('timetable_subjects')
        ->where([
            ['status','=',1],
            ['branch_id','=',Session::get('activeBranch')],
            ['session_id','=',Session::get('activeSession')],
        ])
        ->where(function($q)use($faculty_id,$section_id){
            if($faculty_id){
                $q->where('course_id',$faculty_id);
                $q->where('section_id',$section_id);
            }
        })
        ->select('id','title')
        ->pluck('title','id')->toArray();
        $data['subject'] = array_prepend($data['subject'],'--Select Subject--','');


        
        $data['paper-type'] = ExamPaper::where([
            ['record_status','=',1],
            ['branch_id','=',Session::get('activeBranch')],
            ['session_id','=',Session::get('activeSession')],
        ])->select('id','title')->pluck('title','id')->toArray();
        $data['paper-type'] = array_prepend($data['paper-type'],'--Select Paper Type--','');

        /*teacher access*/
        $data['faculty'] = $this->activeFaculties();
        /*teacher access*/
        
        $data['exam'] = ExamCreate::where([
            ['exam_create.record_status','=',1],
            ['exam_create.branch_id','=',Session::get('activeBranch')],
            ['exam_create.session_id','=',Session::get('activeSession')],
        ])->select('exam_create.*','exam_create.title as exam_title','exam_create.description as exam_description','term.title as term','type.title as type','fac.faculty as faculty','sem.semester as section','mode.title as mode','paper.title as paper','sub.title as subject','max_mark','pass_mark','publish_status')
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
    
    public function IsmainSub($subjectid)
    {
       $subject= DB::table('timetable_subjects as ts')->select('sm.is_main_subject')->where('ts.id',$subjectid)
       ->leftjoin('subject_master as sm','sm.id','=','ts.subject_master_id')
       ->first();
       if($subject){
        $status= $subject->is_main_subject;
       }
       else{
        $status='';
       }
       return $status;
    }
}