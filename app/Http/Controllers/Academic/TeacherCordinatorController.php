<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\CollegeBaseController;
use Session;
use Illuminate\Http\Request;
use Auth,DB;
use Response;
use Carbon\Carbon;
use App\Models\TeacherCoordinator;
use App\Models\Faculty;
use App\Models\Staff;
class TeacherCordinatorController extends CollegeBaseController
{
    protected $base_route = 'add_teacher_cordinator';
    protected $view_path = 'academic.teacher-coordinator';
    protected $panel = 'Add Teacher/Co-ordinator';
    protected $filter_query = [];

    public function __construct()
    {

    }

    public function index()
    {

       $data=[];
       $data['teacher']=DB::table('staff')->select('id',DB::raw("CONCAT(first_name,' ',last_name,'(',reg_no,')') as title"))
      ->where([
        ['status','=',1],
        ['branch_id','=',Session::get('activeBranch')],
      ])
      ->orderBy('title','ASC')
      ->pluck('title','id')
      ->toArray();
       $data['teacher']=array_prepend($data['teacher'],"--Select Teacher--","");
       
        $data['course']= Faculty::select('id','faculty')->where('branch_id',Session::get('activeBranch'))->pluck('faculty','id')->toArray();

        $data['course'] = array_prepend($data['course'],'--Select '.env("course_label").'--'," ");
        $data['section']=Faculty::select('semesters.id','semesters.semester')
      ->where('faculties.branch_id',Session::get('activeBranch'))
      ->distinct('semesters.id')
      ->leftjoin('faculty_semester','faculty_semester.faculty_id','=','faculties.id')
      ->leftjoin('semesters','semesters.id','=','faculty_semester.semester_id')
      ->orderBy('semester','ASC')
      ->pluck('semesters.semester','semesters.id')
      ->toArray();

        $data['teacher_coordinator']= TeacherCoordinator::select('teacher_coordinator.*','staff.first_name','staff.last_name','faculties.faculty','semesters.semester')
        ->leftjoin('staff','teacher_coordinator.teacher_id','=','staff.id')
        ->leftjoin('faculties','teacher_coordinator.faculty_id','=','faculties.id')
        ->leftjoin('semesters','teacher_coordinator.section_id','=','semesters.id')
        ->where('record_status',1)
        ->orderBy('faculty_id','ASC')->get();

       return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

public function store(Request $request)
    {
       //dd($request->all());
        $section= $request->section_id;
        if($request->type==2){
            foreach($section as $k=>$v){
             $insert= TeacherCoordinator::UpdateOrinsert(
              [
                 'teacher_id'=>$request->teacher_id,
                 'faculty_id'=>$request->faculty_id,
                 'section_id'=>$v,
                 'branch_id'=>session::get('activeBranch'),
                 'session_id'=>session::get('activeSession'),
                 'record_status'=>1,
                 

              ],
              [
                 'type'=>$request->type,
                 'created_at'=>Carbon::now(),
                 'created_by'=>auth()->user()->id,
              ]);
            
          }
        }
        if($request->type==1){
          $chkexist= TeacherCoordinator::select('id')
          ->where('teacher_id',$request->teacher_id)
          ->where('record_status',1)
          ->first();
           if(!$chkexist){
             $insert= TeacherCoordinator::insert([
                 'teacher_id'=>$request->teacher_id,
                 'faculty_id'=>$request->faculty_id,
                 'section_id'=>$section['0'],
                 'branch_id'=>session::get('activeBranch'),
                 'session_id'=>session::get('activeSession'),
                 'type'=>$request->type,
                 'created_at'=>Carbon::now(),
                 'created_by'=>auth()->user()->id,
              ]);
           }
           else{
             return redirect()->route($this->base_route)->with('message_danger',' Already Assigned Please Edit');
           }
         
        }
       
       
         $request->session()->flash($this->message_success, $this->panel.' Added Successfully.');
        return back();
    }
    public function edit(Request $request,$id){
        $data['row']=TeacherCoordinator::find($id);
        if(!$data['row']){
            parent::invalidRequest();
        }
       
        if($request->all()){
           $section= $request->section_id;
            
           if($request->type==2){
              foreach($section as $k=>$v){
                $update= TeacherCoordinator::UpdateOrinsert(
                [
                   'teacher_id'=>$request->teacher_id,
                   'faculty_id'=>$request->faculty_id,
                   'section_id'=>$v,
                   'branch_id'=>session::get('activeBranch'),
                   'session_id'=>session::get('activeSession'),
                    'type'=>$request->type,
               

               ],
               [
                  
                   'updated_at'=>Carbon::now(),
                   'last_updated_by'=>auth()->user()->id,
              ]);
            }
           }
           if($request->type==1){
            $update= TeacherCoordinator::where('id',$id)->Update([
                 'faculty_id'=>$request->faculty_id,
                 'teacher_id'=>$request->teacher_id,
                 'section_id'=>$section['0'],
                 'branch_id'=>session::get('activeBranch'),
                 'session_id'=>session::get('activeSession'),
                 'type'=>$request->type,
                 'updated_at'=>Carbon::now(),
                 'last_updated_by'=>auth()->user()->id,

            ]);
           }
            return redirect()->route($this->base_route)->with('message_success', $this->panel.' Updated Successfully');
        }
         $data['teacher']=DB::table('staff')->select('id',DB::raw("CONCAT(first_name,' ',last_name,'(',reg_no,')') as title"))
      ->where([
        ['status','=',1],
        ['branch_id','=',Session::get('activeBranch')],
      ])
      ->orderBy('title','ASC')
      ->pluck('title','id')
      ->toArray();
       $data['teacher']=array_prepend($data['teacher'],"--Select Teacher--","");
       
        $data['course']= Faculty::select('id','faculty')->where('branch_id',Session::get('activeBranch'))->pluck('faculty','id')->toArray();

        $data['course'] = array_prepend($data['course'],'--Select '.env("course_label").'--'," ");
        $data['section']=Faculty::select('semesters.id','semesters.semester')
      ->where('faculties.branch_id',Session::get('activeBranch'))
      ->distinct('semesters.id')
      ->leftjoin('faculty_semester','faculty_semester.faculty_id','=','faculties.id')
      ->leftjoin('semesters','semesters.id','=','faculty_semester.semester_id')
      ->orderBy('semester','ASC')
      ->pluck('semesters.semester','semesters.id')
      ->toArray();

        $data['teacher_coordinator']= TeacherCoordinator::select('teacher_coordinator.*','staff.first_name','staff.last_name','faculties.faculty','semesters.semester')
        ->leftjoin('staff','teacher_coordinator.teacher_id','=','staff.id')
        ->leftjoin('faculties','teacher_coordinator.faculty_id','=','faculties.id')
        ->leftjoin('semesters','teacher_coordinator.section_id','=','semesters.id')
        ->where('record_status',1)
         ->orderBy('faculty_id','ASC')->get();
        return view(parent::loadDataToView($this->view_path.'.index'),compact('data','id'));
    }          
    public function delete(Request $request, $id)
    {
       $data['row']=TeacherCoordinator::find($id);
        if(!$data['row']){
            parent::invalidRequest();
        }
        $request->request->add(['record_status'=>0]);
            $data['row']->update($request->all());
        return redirect()->route($this->base_route)->with('message_success', $this->panel.' Deleted Successfully');    
    }            

 

   
}
