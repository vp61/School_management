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
      $data['course']=array_prepend($data['course'],"--Select Course--","");  
      $data['section']=Faculty::select('semesters.id','semesters.semester')
      ->where('faculties.branch_id',Session::get('activeBranch'))
      ->distinct('semesters.id')
      ->leftjoin('faculty_semester','faculty_semester.faculty_id','=','faculties.id')
      ->leftjoin('semesters','semesters.id','=','faculty_semester.semester_id')
      ->orderBy('semester','ASC')
      ->pluck('semesters.semester','semesters.id')
      ->toArray();
      $data['section']=array_prepend($data['section'],"--Select Section--","");
      $data['subject']=DB::table('timetable_subjects')->select('timetable_subjects.*','faculties.faculty as course','semesters.semester as section')
      ->leftjoin('faculties','faculties.id','timetable_subjects.course_id')
      ->leftjoin('semesters','semesters.id','timetable_subjects.section_id')
      ->where([
        ['timetable_subjects.branch_id','=',Session::get('activeBranch')],
        ['timetable_subjects.session_id','=',Session::get('activeSession')],
        ['timetable_subjects.status','=',1]
      ])
      ->orderBy('course','ASC')
      ->get();
      return view(parent::loadDataToView($this->view_path.'.index'),compact('data'));
    }
   
    public function store(Request $request)
    {
      // $data['check']=DB::table('timetable_subjects')->select('')
      $data['subject']=DB::table('timetable_subjects')->insertGetId([
        'title'=>$request->subject,
        'branch_id'=>Session::get('activeBranch'),
        'status'=>1,
        'course_id'=>$request->course,
        'section_id'=>$request->section,
        'session_id'   =>Session::get('activeSession'),
        'created_by'=>auth()->user()->id
      ]);

       return redirect('timetable/subject')->with('message_success','Subject Added');
    }
    public function edit(Request $request,$id){

      $data['course']=Faculty::select('id','faculty')->where('branch_id',Session::get('activeBranch'))->orderBy('faculty','ASC')->pluck('faculty','id')->toArray();
      $data['course']=array_prepend($data['course'],"--Select Course--","");  
      $data['section']=Faculty::select('semesters.id','semesters.semester')
      ->where('faculties.branch_id',Session::get('activeBranch'))
      ->distinct('semesters.id')
      ->leftjoin('faculty_semester','faculty_semester.faculty_id','=','faculties.id')
      ->leftjoin('semesters','semesters.id','=','faculty_semester.semester_id')
      ->orderBy('semester','ASC')
      ->pluck('semesters.semester','semesters.id')
      ->toArray();
      $data['section']=array_prepend($data['section'],"--Select Section--","");
      $data['subject']=DB::table('timetable_subjects')->select('timetable_subjects.*','faculties.faculty as course','semesters.semester as section')
      ->leftjoin('faculties','faculties.id','timetable_subjects.course_id')
      ->leftjoin('semesters','semesters.id','timetable_subjects.section_id')
      ->where([
        ['timetable_subjects.id','=',$id]
      ])
      ->first();
      if($request->course && $request->section && $request->subject){
        $data['update']=DB::table('timetable_subjects')->where('id',$id)->update([
          'course_id'=>$request->course,
          'section_id'=>$request->section,
          'title'=>$request->subject,
          'updated_at'=>Carbon::now(),
          'updated_by'=>auth()->user()->id
        ]);
        if($data['update']){
          return redirect('/timetable/subject')->with('message_success','Subject Updated');
        }
        else
           return redirect('/timetable/subject')->with('message_warning','Subject updation failed. Please try again.');
      }
      return view(parent::loadDataToView($this->view_path.'.edit'),compact('data','id'));

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

}
