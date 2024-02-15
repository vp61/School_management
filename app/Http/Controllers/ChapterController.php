<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session, Auth,DB; use App\Branch; use App\PaymentType;
use  Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\BranchBatch;
use App\Models\Faculty;
use App\Models\Staff;
use App\Models\chapter;

class ChapterController extends CollegeBaseController
{
	protected $base_route = 'chapter_master';
    protected $view_path ='chapter';
    protected $panel = 'chapter Type';
    protected $filter_query = [];

    public function __construct()
    {

    }
    //

    public function index(Request $request){
    	$data=[];
    	$role = DB::table('role_user')->where('user_id',auth()->user()->id)->first();
    	$data['list'] = [];
    	if($request->all()){
    	      $data['list']=chapter::select('chapter_no.id','faculties.faculty','semesters.semester','sm.title as subject','chapter_no.title')
            ->leftjoin('faculties','chapter_no.faculty','=','faculties.id')
            ->leftjoin('semesters','chapter_no.semesters_id','=','semesters.id')
            ->leftjoin('timetable_subjects','chapter_no.timetable_subjects_id','=','timetable_subjects.id')
            ->leftJoin('subject_master as sm','sm.id','=','timetable_subjects.subject_master_id')
            ->where('chapter_no.branch_id', session('activeBranch'))
            ->where('chapter_no.session_id', session('activeSession'))
            ->where('chapter_no.status', 1)
            ->where(function ($query) use ($request,$role){
                if ($request->faculty) {
                    $query->where('chapter_no.faculty', '=', $request->faculty);
                }
                if ($request->semesters_id) {
                    $query->where('chapter_no.semesters_id', '=', $request->semesters_id);
                }
                if($role){
                    if(!in_array($role->role_id,[1,2,3])){
                        
                    }
                }
            })
            ->get();
    	}
      
        
        $data['faculties'] = DB::table('faculties')->select('id','faculty')
        ->where('branch_id',Session::get('activeBranch'))
        ->where('status',1)
        ->orderBy('faculty','asc')
        ->pluck('faculty','id')->toArray();
        
        $data['faculties'] = array_prepend($data['faculties'],"-- Select ".env('course_label').' --','');
        $data['semester'] = DB::table('semesters')->select('semesters.id','semesters.semester')
        ->rightJoin('faculty_semester as fs','fs.semester_id','=','semesters.id')
        ->rightJoin('faculties as fac','fac.id','=','fs.faculty_id')
        ->where('fac.branch_id',Session::get('activeBranch'))
        ->where('fac.status',1)
        ->orderBy('semester','asc')
        ->pluck('semester','id')->toArray();
        $data['semester'] = array_prepend($data['semester'],"-- Select Section --",'');
        $data['subjects']= [''=>'--select--'];
    	return view(parent::loadDataToView($this->view_path.'.index'),compact('data'));
    }
    public function add(Request $request){

         $data['faculties'] = DB::table('faculties')->select('id','faculty')
        ->where('branch_id',Session::get('activeBranch'))
        ->where('status',1)
        ->orderBy('faculty','asc')
        ->pluck('faculty','id')->toArray();
        
        $data['faculties'] = array_prepend($data['faculties'],"-- Select ".env('course_label').' --','');
        
        //
        $data['semester'] = DB::table('semesters')->select('semesters.id','semesters.semester')
        ->rightJoin('faculty_semester as fs','fs.semester_id','=','semesters.id')
        ->rightJoin('faculties as fac','fac.id','=','fs.faculty_id')
        ->where('fac.branch_id',Session::get('activeBranch'))
        ->where('fac.status',1)
        ->orderBy('semester','asc')
        ->pluck('semester','id')->toArray();
        
        $data['semester'] = array_prepend($data['semester'],"-- Select Section --",'');
        
        $data['subjects']= ['--select--'];
        
        return view(parent::loadDataToView($this->view_path.'.add'),compact('data'));
    }
        public function store(Request $request){
        //dd($request->all());
        $request->request->add(['branch_id'=>session('activeBranch')]);
        $request->request->add(['session_id'=>session('activeSession')]);
        $request->request->add(['created_by'=>auth()->user()->id]);
        chapter::create($request->all());
        //dd($data['lession']);
        //return back();
        return redirect()->back()->with('message_success',' Added Successfully');
    }
    public function edit(Request $request,$id){

        $data['row']=chapter::find($id);
         if(!$data['row']){
          return parent::invalidRequest();
         }
          if($request->all()){
            $request->request->add(['updated_at'=>Carbon::now()]);
            $request->request->add(['updated_by'=>auth()->user()->id]);
            $data['row']->update($request->all());
            return redirect()->route('chapter_master')->with('message_success','Chapter Updated');
          }
          $data['faculties'] = DB::table('faculties')->select('id','faculty')
        ->where('branch_id',Session::get('activeBranch'))
        ->where('status',1)
        ->orderBy('faculty','asc')
        ->pluck('faculty','id')->toArray();
        
        $data['faculties'] = array_prepend($data['faculties'],"-- Select ".env('course_label').' --','');
        
        $data['semester'] = DB::table('semesters')->select('semesters.id','semesters.semester')
        ->rightJoin('faculty_semester as fs','fs.semester_id','=','semesters.id')
        ->rightJoin('faculties as fac','fac.id','=','fs.faculty_id')
        ->where('fac.branch_id',Session::get('activeBranch'))
        ->where('fs.faculty_id',$data['row']->faculty)
        ->where('fac.status',1)
        ->orderBy('semester','asc')
        ->pluck('semester','id')->toArray();
        
        $data['semester'] = array_prepend($data['semester'],"-- Select Section --",'');
         
         $data['list']=chapter::select('chapter_no.id','faculties.faculty','semesters.semester','timetable_subjects.title as subject','chapter_no.title')
        ->where('chapter_no.branch_id',Session::get('activeBranch'))
        ->where('chapter_no.session_id',Session::get('activeSession'))
        ->leftjoin('faculties','chapter_no.faculty','=','faculties.id')
        ->leftjoin('semesters','chapter_no.semesters_id','=','semesters.id')
        ->leftjoin('timetable_subjects','chapter_no.timetable_subjects_id','=','timetable_subjects.id')
        ->get();
        /*subject list*/
        $data['subjects'] = DB::table('timetable_subjects')->select('timetable_subjects.id','sm.title')
        ->leftJoin('subject_master as sm','sm.id','=','timetable_subjects.subject_master_id')
        ->where('timetable_subjects.status',1)
        ->where('timetable_subjects.course_id',$data['row']->faculty)
        ->where('timetable_subjects.section_id',$data['row']->semesters_id)
        ->where('timetable_subjects.branch_id',session::get('activeBranch'))
        ->where('timetable_subjects.session_id',session::get('activeSession'))
        ->pluck('title','id')->toArray();

          return view(parent::loadDataToView($this->view_path.'.add'),compact('data','id'));

    }

    public function delete(Request $request, $id){
    	//$data['row']=worksheet::find($id);
        $data['row']=DB::table('chapter_no')->where('id',$id)->update([
                'updated_at'=>Carbon::now(),
                'updated_by' =>auth()->user()->id,
                'status'=>0
            ]);
    	if(!$data['row']){
            parent::invalidRequest();
        }
    	return redirect()->route($this->base_route)->with('message_success', $this->panel.' Deleted Successfully');
    }
}
