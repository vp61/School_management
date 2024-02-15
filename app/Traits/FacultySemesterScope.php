<?php
namespace App\Traits;

use App\Models\Faculty;
use App\Models\Semester;
use App\Session_Model;
use Auth;
use Session,DB;
use App\Models\TeacherCoordinator;

trait FacultySemesterScope{

    public function activeFaculties()
    {
         $org_id = Auth::user()->org_id;
        $branch_id = Session::get('activeBranch'); //Auth::user()->branch_id;
        /*--teacher access--*/
        $classTeacher= TeacherCoordinator::where('teacher_id',Auth::user()->hook_id)
        ->where('branch_id',$branch_id)
        ->where('record_status',1)
        ->where('session_id',Session::get('activeSession'))->pluck('faculty_id')->toArray();
        // dd($classTeacher);
        $ability = Auth::user()->ability('super-admin,account','show-all-classes');
        $faculty = Faculty::
         where('status',1)
        
        ->where('branch_id',$branch_id)
        ->where(function($q)use($ability,$classTeacher){
            if(!$ability){
                $q->whereIn('id',$classTeacher);
            }    
        })
        
        ->pluck('faculty','id')->toArray();
        // dd($org_id);
        $data = $faculty;
        if(!(count($classTeacher)>0)){
         $data = array_prepend($faculty,'Select '.env('course_label'),'0');
        }
         return $data;
         /*--teacher access--*/
    }
    public function getBranchTitle($id){
        $branch=DB::table('branches')->select('branch_title')->where('id',$id)->first();
        if($branch){
            return $branch->branch_title;
        }else{
            return "Unknown";
        }
    }

    public function getFacultyTitle($id)
    {
        $faculty = Faculty::find($id);
        if ($faculty) {
            return $faculty->faculty;
        }else{
            return "Unknown";
        }
    }

    public function getSemesterById($id)
    {
        $semester = Semester::find($id);
        if ($semester) {
            return $semester->semester;
        }else{
            return "";
        }
    }

    public function getSemesterTitle($id)
    {
        $semester = Semester::find($id);
        if ($semester) {
            return $semester->semester;
        }else{
            return "Unknown";
        }
    }
     public function getSessionById($id)
    {
        $session = Session_Model::find($id);
        
        if ($session) {
            return $session->session_name;
        }else{
            return "Unknown";
        }
    }
    
    public function getAbility($role='',$permission='show-all-classes'){
        if(!$role){
            $role = 'super-admin,account';
        }
        
        $ability = Auth::user()->ability($role,$permission);
        
        return $ability;
    }
    
    public function getClassTeacherCourse(){
        $classTeachercourse= TeacherCoordinator::where('teacher_id',Auth::user()->hook_id)
        ->where('branch_id',Session::get('activeBranch'))
        ->where('record_status',1)
        ->where('session_id',Session::get('activeSession'))->pluck('faculty_id')->toArray();
        
        return $classTeachercourse;
    }
    
    public function getClassTeacherSection(){
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
        $data['section'] = array_prepend($data['section'],'--Select Section/Sem.--','');
        return $data['section'];
    }

}