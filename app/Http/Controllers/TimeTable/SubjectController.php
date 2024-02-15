<?php

namespace App\Http\Controllers\TimeTable;
use App\Http\Controllers\CollegeBaseController;
use Illuminate\Http\Request;
use App\Http\Requests\Transport\User\AddValidation;
use App\Http\Requests\Transport\User\EditValidation;
use App\Models\Faculty;
use App\Models\Staff;
use App\Models\Student;
use URL,DB,Session;
use Carbon\Carbon;
/*assign subject to class/course*/
use App\Models\subjectMaster;

class SubjectController extends CollegeBaseController
{
    protected $base_route = 'timetable.subject';
    protected $view_path = 'time-table.subject';
    protected $panel = 'Time Table';
    protected $filter_query = [];

    public function index(Request $request)
    {
     $branch_id=Session::get('activeBranch');
     $data['course']=Faculty::select('id','faculty')->where('branch_id',Session::get('activeBranch'))->orderBy('faculty','ASC')->pluck('faculty','id')->toArray();
      $data['course']=array_prepend($data['course'],"--Select ".env("course_label")."--","");  
      $data['section']=Faculty::select('semesters.id','semesters.semester')
      ->where('faculties.branch_id',Session::get('activeBranch'))
      ->distinct('semesters.id')
      ->leftjoin('faculty_semester','faculty_semester.faculty_id','=','faculties.id')
      ->leftjoin('semesters','semesters.id','=','faculty_semester.semester_id')
      ->orderBy('semester','ASC')
      ->pluck('semesters.semester','semesters.id')
      ->toArray();
  
      
      /*assign subject to class/course*/
      $data['subject']=DB::table('timetable_subjects')->select('timetable_subjects.*','faculties.faculty as course','semesters.semester as section')
      ->leftjoin('faculties','faculties.id','timetable_subjects.course_id')
      ->leftjoin('semesters','semesters.id','timetable_subjects.section_id')
      ->where([
        ['timetable_subjects.branch_id','=',Session::get('activeBranch')],
        ['timetable_subjects.session_id','=',Session::get('activeSession')],
        ['timetable_subjects.status','=',1]
      ])
      ->orderBy('course','ASC')
      ->orderBy('timetable_subjects.section_id','ASC')
      ->groupBy('timetable_subjects.course_id')
      ->groupBy('timetable_subjects.section_id')
      ->get();
  
      $data['allsubject']= subjectMaster::select('*')->where('record_status','=',1)
      ->get();
       /*end assign subject to class/course*/
      return view(parent::loadDataToView($this->view_path.'.index'),compact('data'));
    }
   
    public function store(Request $request)
    {
      
       /*assign subject to class/course*/
       $section= $request->section;
       $subject= $request->subject;
      foreach($section as $key=>$value){

         foreach($subject as $k=>$v){
          $subname= $this->Getsubject($v);
          
          /*subjectPriority*/
           $priority= isset($request->sub_priority[$v])?$request->sub_priority[$v]:'';
           /*subjectPriority*/
          //dd($subname);
                if($value){
                    $data['subject']=DB::table('timetable_subjects')->updateOrInsert(
                    [
                        'subject_master_id'=>$v, 
                        'branch_id'=>Session::get('activeBranch'),
                          'status'=>1,
                          'course_id'=>$request->course,
                          'section_id'=>$value,
                          'session_id'   =>Session::get('activeSession'),
                    ],
                    [
                      
                      'title'=>$subname,
                       /*subjectPriority*/
                      'sub_priority'=>$priority,
                      /*subjectPriority*/
                      'created_at'=>Carbon::now(),
                      'created_by'=>auth()->user()->id
                    ]);
                    
                    
                }
         }

      }
      /*assign subject to class/course*/
      

       return redirect('timetable/subject')->with('message_success','Subject Added');
    }
    public function edit(Request $request,$courseid,$sectionid){

      
      $data['course']=Faculty::select('id','faculty')->where('branch_id',Session::get('activeBranch'))->orderBy('faculty','ASC')->pluck('faculty','id')->toArray();
      $data['course']=array_prepend($data['course'],"--Select ".env("course_label")."--","");  
      $data['section']=Faculty::select('semesters.id','semesters.semester')
      ->where('faculties.branch_id',Session::get('activeBranch'))
      ->distinct('semesters.id')
      ->leftjoin('faculty_semester','faculty_semester.faculty_id','=','faculties.id')
      ->leftjoin('semesters','semesters.id','=','faculty_semester.semester_id')
      ->orderBy('semester','ASC')
      ->pluck('semesters.semester','semesters.id')
      ->toArray();
   /*assign subject to class/course*/
     /*subjectPriority*/
     $data['sub'] = subjectMaster::select('subject_master.*','ts.subject_master_id','ts.sub_priority')
      ->leftjoin('timetable_subjects as ts',function($j)use($courseid,$sectionid){
        $j->on('ts.subject_master_id','=','subject_master.id')
        ->where([
         ['ts.course_id','=',$courseid],
         ['ts.section_id','=',$sectionid]
        ])
        ->where('ts.status',1);
      })
      ->where([
        ['subject_master.record_status',1]

      ])
      ->get();
      /*subjectPriority*/
    
     
      if($request->course && $request->section && $request->subject){
        
        $exists_subjects = DB::table('timetable_subjects')->select('subject_master_id','id')
          ->where([
                  ['course_id','=',$request->course],
                  ['section_id','=',$request->section],
                  ['branch_id','=',Session::get('activeBranch')],
                  ['session_id'   ,'=',Session::get('activeSession')],
          ])->pluck('subject_master_id','id')->toArray();
        foreach($exists_subjects as $ts_id => $sub_mas_id ){
          if(!in_array($sub_mas_id, $request->subject)){
            DB::table('timetable_subjects')->where('id',$ts_id)->delete();
          }
        }
        foreach ($request->subject as $key=>$value ) { 
        $extrafee = isset($request->extra_fee[$key])?$request->extra_fee[$key]:null;  
        $subname= $this->Getsubject($value);
         /*subjectPriority*/
        $priority= isset($request->sub_priority[$value])?$request->sub_priority[$value]:'';
        /*subjectPriority*/
            $data['update']=  DB::table('timetable_subjects')->updateOrInsert(
              [
                'course_id'=>$request->course,
                'section_id'=>$request->section,
                'branch_id'=>Session::get('activeBranch'),
                'session_id'   =>Session::get('activeSession'),
                'subject_master_id'=>$value,
              ],

              [
                  
                  'title'=>$subname,
                  /*subjectPriority*/
                  'sub_priority'=>$priority,
                  /*subjectPriority*/
                 'updated_at'=>Carbon::now(),
                 'updated_by'=>auth()->user()->id
             ]);


       }
        if($data['update']){
          return redirect('/timetable/subject')->with('message_success','Subject Updated');
        }
        else
           return redirect('/timetable/subject')->with('message_warning','Subject updation failed. Please try again.');
      }
      return view(parent::loadDataToView($this->view_path.'.edit'),compact('data','courseid','sectionid'));
     /*assign subject to class/course*/
    }
    public function delete($id){
       $data['update']=DB::table('timetable_subjects')->where('id',$id)->update([
        'status'=>0,
        'updated_at'=>Carbon::now(),
        'updated_by'=>auth()->user()->id
       ]);
        if($data['update']){
          return redirect('/timetable/subject')->with('message_success','Subject Deleted');
        }
        else
           return redirect('/timetable/subject')->with('message_warning','Subject deletion failed. Please try again.');
    }

  /*assign subject to class/course*/
    public function Getsubject($subjectid)
    {
      
      $subject= subjectMaster::find($subjectid);
      
      if($subject){
        $subname= $subject->title;
      }
      else{
        $subname='';
      }

      return $subname;
    }
    /*assign subject to class/course*/

}
