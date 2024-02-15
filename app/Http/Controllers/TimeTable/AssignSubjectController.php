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
      $data['course']=array_prepend($data['course'],"--Select ".env("course_label")."--","");  

      $data['section']=Faculty::select('semesters.id','semesters.semester')
      ->where('faculties.branch_id',Session::get('activeBranch'))
      ->distinct('semesters.id')
      ->leftjoin('faculty_semester','faculty_semester.faculty_id','=','faculties.id')
      ->leftjoin('semesters','semesters.id','=','faculty_semester.semester_id')
      ->orderBy('semester','ASC')
      ->pluck('semesters.semester','semesters.id')
      ->toArray();
      $data['section']=array_prepend($data['section'],"--Select Section--","");

      $data['subject']=DB::table('timetable_subjects')->select('timetable_subjects.id',DB::RAW("CONCAT(sm.title,' ( ',f.faculty,'-',sem.semester,' ) ') as title"))
      /*assign subject to teacher*/
      ->leftjoin('subject_master as sm','sm.id','=','timetable_subjects.subject_master_id')
      ->leftjoin('faculties as f','f.id','=','timetable_subjects.course_id')
      ->leftjoin('semesters as sem','sem.id','=','timetable_subjects.section_id')
      /*assign subject to teacher*/
      ->where([
        ['timetable_subjects.branch_id','=',Session::get('activeBranch')],
        ['timetable_subjects.session_id','=',Session::get('activeSession')],
        ['timetable_subjects.status','=',1]
      ])
      ->orderBy('title','ASC')
      ->pluck('title','timetable_subjects.id')
      ->toArray();

      $data['assigned']=DB::table('timetable_assign_subject')->select('timetable_assign_subject.*','sm.title as subject',DB::raw("CONCAT(st.first_name,' ',st.last_name,'(',st.reg_no,')') as teacher"))
      ->leftjoin('timetable_subjects as sbj','sbj.id','=','timetable_assign_subject.timetable_subject_id')
      ->leftjoin('staff as st','st.id','=','timetable_assign_subject.staff_id')
       /*assign subject to teacher*/
      ->leftjoin('subject_master as sm','sm.id','=','sbj.subject_master_id')
       /*assign subject to teacher*/
      ->where([
        ['sbj.status','=',1],
        ['timetable_assign_subject.status','=',1],
        ['timetable_assign_subject.branch_id','=',Session::get('activeBranch')],
        ['timetable_assign_subject.session_id','=',Session::get('activeSession')]
        ])
      ->orderBy('subject','ASC')
      ->groupBy('timetable_assign_subject.staff_id')
      ->get();


      $data['teacher']=DB::table('staff')->select('id',DB::raw("CONCAT(first_name,' ',last_name,'(',reg_no,')') as title"))
      ->where([
        ['status','=',1],
        ['branch_id','=',Session::get('activeBranch')],
        ['designation','=',env('TEACHER_DESIGNATION')]
      ])
      ->orderBy('title','ASC')
      ->pluck('title','id')
      ->toArray();
      $data['teacher']=array_prepend($data['teacher'],"--Select Teacher--","");
      return view(parent::loadDataToView($this->view_path.'.index'),compact('data'));
    }
   
    public function store(Request $request)
    {
       /*assign subject to teacher*/
      $subject= $request->subject;
     // dd($request->all());
      foreach ($subject as $key => $value) {
       $data['subject']=DB::table('timetable_assign_subject')->insertGetId([
        'created_by'=>auth()->user()->id,
        'staff_id'=>$request->teacher,
        'timetable_subject_id'=>$value,
        'session_id'=>Session::get('activeSession'),
        'branch_id'=>Session::get('activeBranch'),
        'status'=>1
        ]);
      }
       /*assign subject to teacher*/

       return redirect('timetable/assign')->with('message_success','Subject assigned to selected teacher');
    }
     public function edit(Request $request,$id){
      $data['teacher']=DB::table('staff')->select('id',DB::raw("CONCAT(first_name,' ',last_name,'(',reg_no,')') as title"))
      ->where([
        ['status','=',1],
        ['branch_id','=',Session::get('activeBranch')],
        ['designation','=',env('TEACHER_DESIGNATION')]
      ])
      ->orderBy('title','ASC')
      ->pluck('title','id')
      ->toArray();
      /*assign subject to teacher*/
      $data['teacher']=array_prepend($data['teacher'],"--Select Teacher--","");
      $data['assigned']=DB::table('timetable_assign_subject')->select('timetable_assign_subject.*',DB::RAW("CONCAT(sm.title,' ( ',f.faculty,'-',sem.semester,' ) ') as subject"),DB::raw("CONCAT(st.first_name,' ',st.last_name,'(',st.reg_no,')') as teacher"))
      ->leftjoin('timetable_subjects as sbj','sbj.id','=','timetable_assign_subject.timetable_subject_id')
      ->leftjoin('subject_master as sm','sm.id','sbj.subject_master_id')
      ->leftjoin('staff as st','st.id','=','timetable_assign_subject.staff_id')
      ->leftjoin('faculties as f','f.id','=','sbj.course_id')
      ->leftjoin('semesters as sem','sem.id','=','sbj.section_id')
      ->where([
        ['timetable_assign_subject.staff_id','=',$id]
        ])
      ->first();

      $data['assigned_sub']=DB::table('timetable_assign_subject')->select('timetable_assign_subject.timetable_subject_id',DB::RAW("CONCAT(sm.title,' ( ',f.faculty,'-',sem.semester,' ) ') as title"))
      ->leftjoin('timetable_subjects as sbj','sbj.id','=','timetable_assign_subject.timetable_subject_id')
      ->leftjoin('subject_master as sm','sm.id','sbj.subject_master_id')
      ->leftjoin('staff as st','st.id','=','timetable_assign_subject.staff_id')
      ->leftjoin('faculties as f','f.id','=','sbj.course_id')
      ->leftjoin('semesters as sem','sem.id','=','sbj.section_id')
      ->where([
        ['timetable_assign_subject.staff_id','=',$id]
        ])
      ->get();
     /*assign subject to teacher*/
     
    
      $data['subject']=DB::table('timetable_subjects')->select('timetable_subjects.id',DB::RAW("CONCAT(sm.title,' ( ',f.faculty,'-',sem.semester,' ) ') as title"))
      /*assign subject to teacher*/
      ->leftjoin('subject_master as sm','sm.id','=','timetable_subjects.subject_master_id')
      ->leftjoin('faculties as f','f.id','=','timetable_subjects.course_id')
      ->leftjoin('semesters as sem','sem.id','=','timetable_subjects.section_id')
      /*assign subject to teacher*/
      ->where([
        ['timetable_subjects.branch_id','=',Session::get('activeBranch')],
        ['timetable_subjects.session_id','=',Session::get('activeSession')],
        ['timetable_subjects.status','=',1]
      ])
      ->orderBy('title','ASC')
      ->pluck('title','timetable_subjects.id')
      ->toArray();
        /*assign subject to teacher*/
      if($request->teacher && $request->subject){
        $subject= $request->subject;
        $exists_subjects = DB::table('timetable_assign_subject')->select('timetable_subject_id','id')
          ->where([
                  ['staff_id','=',$request->teacher],
                  ['branch_id','=',Session::get('activeBranch')],
                  ['session_id'   ,'=',Session::get('activeSession')],
          ])->pluck('timetable_subject_id','id')->toArray();
          //dd($exists_subjects,$subject);
        foreach($exists_subjects as $tas_id => $t_sub_id ){
          if(!in_array($t_sub_id, $request->subject)){
            DB::table('timetable_assign_subject')->where('id',$tas_id)->delete();
          }
        }
        foreach ($request->subject as $key=>$value ) { 
          $data['update']=  DB::table('timetable_assign_subject')->updateOrInsert(
              [
                'staff_id'=>$request->teacher,
                'branch_id'=>Session::get('activeBranch'),
                'session_id'   =>Session::get('activeSession'),
                'timetable_subject_id'=>$value,
              ],

              [
                 'updated_at'=>Carbon::now(),
                 'updated_by'=>auth()->user()->id
             ]);


       }
       /*assign subject to teacher*/
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
