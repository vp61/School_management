<?php
namespace App\Traits;

use App\Models\Faculty;
use App\Models\Semester;
use App\Session_Model;
use Auth;
use Session,DB;
trait FacultySemesterScope{

    public function activeFaculties()
    {
        $org_id = Auth::user()->org_id;
        $branch_id = Session::get('activeBranch'); //Auth::user()->branch_id;
        $faculty = Faculty::
        where('status',1)
        ->where('org_id',$org_id)
        ->where('branch_id',$branch_id)
        ->pluck('faculty','id')->toArray();
         return array_prepend($faculty,'Select Course','0');
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

}