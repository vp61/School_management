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

class AssignSubjectController extends CollegeBaseController
{
    protected $base_route = 'timetable.assign';
    protected $view_path = 'time-table.assign';
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

      $data['subject']=DB::table('timetable_subjects')->select('id','title')
      ->where([
        ['timetable_subjects.branch_id','=',Session::get('activeBranch')],
        ['timetable_subjects.session_id','=',Session::get('activeSession')],
        ['timetable_subjects.status','=',1]
      ])
      ->orderBy('title','ASC')
      ->pluck('title','id')
      ->toArray();
      $data['subject']=array_prepend($data['subject'],"--Select Subject--","");

      $data['assigned']=DB::table('timetable_assign_subject')->select('timetable_assign_subject.*','sbj.title as subject',DB::raw("CONCAT(st.first_name,' ',st.last_name,'(',st.reg_no,')') as teacher"))
      ->leftjoin('timetable_subjects as sbj','sbj.id','=','timetable_assign_subject.timetable_subject_id')
      ->leftjoin('staff as st','st.id','=','timetable_assign_subject.staff_id')
      ->where([
        ['sbj.status','=',1],
        ['timetable_assign_subject.status','=',1],
        ['timetable_assign_subject.branch_id','=',Session::get('activeBranch')],
        ['timetable_assign_subject.session_id','=',Session::get('activeSession')]
        ])
      ->orderBy('subject','ASC')
      ->get();

      $data['teacher']=DB::table('staff')->select('id',DB::raw("CONCAT(first_name,' ',last_name,'(',reg_no,')') as title"))
      ->where([
        ['status','=',1],
        ['branch_id','=',Session::get('activeBranch')],
        ['designation','=',6]
      ])
      ->orderBy('title','ASC')
      ->pluck('title','id')
      ->toArray();
      $data['teacher']=array_prepend($data['teacher'],"--Select Teacher--","");
      return view(parent::loadDataToView($this->view_path.'.index'),compact('data'));
    }
   
    public function store(Request $request)
    {
      $data['subject']=DB::table('timetable_assign_subject')->insertGetId([
        'created_by'=>auth()->user()->id,
        'staff_id'=>$request->teacher,
        'timetable_subject_id'=>$request->subject,
        'session_id'=>Session::get('activeSession'),
        'branch_id'=>Session::get('activeBranch'),
        'status'=>1
      ]);

       return redirect('timetable/assign')->with('message_success','Subject assigned to selected teacher');
    }
     public function edit(Request $request,$id){
      $data['teacher']=DB::table('staff')->select('id',DB::raw("CONCAT(first_name,' ',last_name,'(',reg_no,')') as title"))
      ->where([
        ['status','=',1],
        ['branch_id','=',Session::get('activeBranch')],
        ['designation','=',6]
      ])
      ->orderBy('title','ASC')
      ->pluck('title','id')
      ->toArray();
       $data['teacher']=array_prepend($data['teacher'],"--Select Teacher--","");
      $data['assigned']=DB::table('timetable_assign_subject')->select('timetable_assign_subject.*','sbj.title as subject',DB::raw("CONCAT(st.first_name,' ',st.last_name,'(',st.reg_no,')') as teacher"))
      ->leftjoin('timetable_subjects as sbj','sbj.id','=','timetable_assign_subject.timetable_subject_id')
      ->leftjoin('staff as st','st.id','=','timetable_assign_subject.staff_id')
      ->where([
        ['timetable_assign_subject.id','=',$id]
        ])
      ->first();

      $data['subject']=DB::table('timetable_subjects')->select('id','title')
      ->where([
        ['timetable_subjects.branch_id','=',Session::get('activeBranch')],
        ['timetable_subjects.session_id','=',Session::get('activeSession')],
        ['timetable_subjects.status','=',1]
      ])
      ->orderBy('title','ASC')
      ->pluck('title','id')
      ->toArray();
      $data['subject']=array_prepend($data['subject'],"--Select Subject--","");
      if($request->teacher && $request->subject){
        $data['update']=DB::table('timetable_assign_subject')->where('id',$id)->update([
          'timetable_subject_id'=>$request->subject,
          'staff_id'=>$request->teacher,
          'updated_at'=>Carbon::now(),
          'updated_by'=>auth()->user()->id
        ]);
        if($data['update']){
          return redirect('/timetable/assign')->with('message_success','Updated Successfully');
        }
        else
           return redirect('/timetable/assign')->with('message_warning','Updation failed. Please try again.');
      }
      return view(parent::loadDataToView($this->view_path.'.edit'),compact('data','id'));

    }
    public function delete($id){
       $data['update']=DB::table('timetable_assign_subject')->where('id',$id)->update([
        'status'=>0,
        'updated_at'=>Carbon::now(),
        'updated_by'=>auth()->user()->id
       ]);
        if($data['update']){
          return redirect('/timetable/assign')->with('message_success',' Deleted');
        }
        else
           return redirect('/timetable/assign')->with('message_warning','Deletion failed. Please try again.');
    }

}
