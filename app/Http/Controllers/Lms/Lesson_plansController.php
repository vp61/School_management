<?php

namespace App\Http\Controllers\Lms;

use Illuminate\Http\Request;
//use App\Http\Controllers\Controller;
use App\Http\Controllers\CollegeBaseController;

use App\Session_Model;
use App\Models\Lesson_plans;
use Session;
use Illuminate\Support\Facades\Crypt;
use App\Models\Faculty;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Subject; 
use DB,Log;
use Auth,URL; 
use App\category_model;
use App\Branch; 
use Carbon\Carbon;
use App\User;
use App\Models\GeneralSetting;



class Lesson_plansController extends CollegeBaseController
{
    //

    protected $base_route = 'Lms.Lesson_plans';
    protected $view_path = 'Lms.Lesson_plans';
    protected $panel = 'Lesson plans';
    protected $folder_path;
    protected $folder_name = 'Lesson_plans';
    protected $filter_query = [];

    public function __construct()
    {
        $this->folder_path = public_path().DIRECTORY_SEPARATOR.$this->folder_name.DIRECTORY_SEPARATOR;

    }


    public function index(Request $request){
        //dd(session('activeSession'));
        $data['Lesson_plans']=Lesson_plans::select('lesson_plans.id','lesson_plans.months_id','lesson_plans.faculty','lesson_plans.semesters_id','subjects_id','chapter_no_id','faculties.faculty','semesters.semester','sm.title as subject','chapter_no.title','lesson_plans.publish_date',DB::raw("CONCAT(staff.first_name,' ',staff.last_name) as created_by"),'lesson_plans.end_date','lesson_plans.status as st','lesson_plans.file','lesson_plans.topic')
        ->leftjoin('faculties','lesson_plans.faculty','=','faculties.id')
        ->leftjoin('semesters','lesson_plans.semesters_id','=','semesters.id')
        ->leftjoin('timetable_subjects','lesson_plans.subjects_id','=','timetable_subjects.id')
        ->leftjoin('subject_master as sm','sm.id','=','timetable_subjects.subject_master_id')
        ->leftjoin('chapter_no','lesson_plans.chapter_no_id','=','chapter_no.id')
        ->leftjoin('staff','staff.id','=','lesson_plans.created_by')
        ->where('lesson_plans.branch_id', session('activeBranch'))
        ->where('lesson_plans.session_id', session('activeSession'))
        ->where(function($query)use($request){

            if($request->faculty){
              $query->where('lesson_plans.faculty',$request->faculty);
            }
            if($request->semesters_id){
              $query->where('lesson_plans.semesters_id',$request->semesters_id);
            }
            if($request->months_id){
              $query->where('lesson_plans.months_id',$request->months_id);
            }
            if($request->subjects_id){
              $query->where('lesson_plans.subjects_id',$request->subjects_id);
            }
            if($request->teacher_id){
              $query->where('lesson_plans.created_by',$request->teacher_id);
            }
           if ($request->publish_date_start != "" && $request->publish_date_end != ""){
                $query->whereBetween('lesson_plans.publish_date', [$request->publish_date_start, $request->publish_date_end]);
                    $this->filter_query['publish_date_start'] = $request->publish_date_start;
                    $this->filter_query['publish_date_end'] = $request->publish_date_end;
                    } elseif ($request->publish_date_start != "") {
                        //$query->where('lesson_plans.publish_date', '>=', $request->publish_date_start);
                        $this->filter_query['publish_date_start'] = $request->publish_date_start;
                    }
        })
        ->get();
        //dd($data['Lesson_plans']);
        $data['status']= ['0'=>'inactive','1'=>'active'];
        //dd($data['E_content']);
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

        $data['month']=DB::table('months')->select('id','title')
        ->where('status',1)
        ->orderBy('id','asc')
        ->pluck('title','id')->toArray();
       
        $data['month'] = array_prepend($data['month'],"-- Select months --",'');

        $data['staff']=DB::table('staff')->select('id','first_name','designation')

        ->where('branch_id',Session::get('activeBranch'))
        ->where('designation',env('TEACHER_DESIGNATION'))
        ->where('status',1)
        ->orderBy('first_name','asc')
        ->pluck('first_name','id')->toArray();
        //dd($data['staff']);
        $data['staff'] = array_prepend($data['staff'],"-- Select --",'');
        //dd($data['semester']);
        //$data['subject']=DB::table('timetable_subjects')->select('id','title')
        $data['subjects']= [''=>'--select--'];
        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;
        
        return view(parent::loadDataToView($this->view_path.'.index'),compact('data'));

    }
    public function add(Request $request){
        //  dd('jdfhjd');
        $data['faculties'] = DB::table('faculties')->select('id','faculty')
        ->where('branch_id',Session::get('activeBranch'))
        ->where('status',1)
        ->orderBy('faculty','asc')
        ->pluck('faculty','id')->toArray();
        //dd($data['faculties']);
        $data['faculties'] = array_prepend($data['faculties'],"-- Select ".env('course_label').' --','');

        $data['semester'] = DB::table('semesters')->select('semesters.id','semesters.semester')
        ->rightJoin('faculty_semester as fs','fs.semester_id','=','semesters.id')
        ->rightJoin('faculties as fac','fac.id','=','fs.faculty_id')
        ->where('fac.branch_id',Session::get('activeBranch'))
        ->where('fac.status',1)
        ->orderBy('semester','asc')
        ->pluck('semester','id')->toArray();
       
        $data['semester'] = array_prepend($data['semester'],"-- Select Section --",'');

        $data['month']=DB::table('months')->select('id','title')
        ->where('status',1)
        ->orderBy('id','asc')
        ->pluck('title','id')->toArray();
        //dd($data['month']);
        $data['month'] = array_prepend($data['month'],"-- Select months --",'');

        $data['subjects']= [''=>'--select--'];

       
        $data['chapter']=[''=>'--chapter--'];
        $data['url'] = URL::current();
        return view(parent::loadDataToView($this->view_path.'.add'),compact('data'));
    }
    public function store(Request $request){
        //dd($request->all());
        $msg=['publish_date.required'=>'Date field is required',
                'end_date.required'=>'Date field is required',
                // 'months_id.required'=>'Months fields is requerd',
                'faculty.required'=>'class field is requerd',
                'semesters_id.required'=>'semester field is required',
                'subjects_id.required'=>'subject field is requierd',
                'type.required'=>'type field is required',
                'chapter_no_id.required'=>'chapter field is required',
                'topic.required'=>'topic field is required',
                // 'unit.required'=>'unit field is required',
                'no_h/w.required'=>'h/w field is required',
                // 'detail.required'=>'detail field is required',


        ];
        $rules=[
                'publish_date'  =>'required',
                'end_date'  =>'required',
                // 'months_id' =>'required',
                'faculty' =>'required',
                'semesters_id' =>'required',
                'subjects_id' =>'required',
                'type' =>'required',
                'chapter_no_id' =>'required',
                'topic' =>'required',
                // 'unit' =>'required',
                'no_h/w' =>'required',
                // 'detail' =>'required',

            ];
        $this->validate($request,$rules,$msg);
        $name = str_slug($request->get('detail'));
        if ($request->hasFile('attach_file')){
            $file = $request->file('attach_file');
            $file_name = rand(4585, 9857).'_'.$name.'.'.$file->getClientOriginalExtension();

            $file->move($this->folder_path, $file_name);
            //dd($file_name,$file);
            
            $request->request->add(['file'=>$file_name]);
            
        }
        $request->request->add(['branch_id'=>session('activeBranch')]);
            $request->request->add(['session_id'=>session('activeSession')]);
            $request->request->add(['created_by'=>auth()->user()->hook_id]);
        Lesson_plans::create($request->all());
        //return redirect($this->base_route.'/head');
        return redirect('Lms/Lesson_plans')->with('message_success',' Added Successfully');
    }
    public function edit(Request $request,$id){
        //dd($request->all());

        $data['row']=Lesson_plans::find($id);
        //dd($data['row']);
        if(!$data['row']) return parent::invalidRequest();
        if($request->all()){
            $request->request->add(['updated_at'=>Carbon::now()]);
            $request->request->add(['updated_by'=>auth()->user()->id]);
            $name = str_slug($request->get('detail'));
            if($request->hasFile('attach_file')){
                $file = $request->file('attach_file');
                $file_name = rand(4585, 9857).'_'.$name.'.'.$file->getClientOriginalExtension();
                $file->move($this->folder_path, $file_name);

                if ($data['row']->file && file_exists($this->folder_path.$data['row']->file)) {
                    @unlink($this->folder_path.$data['row']->file);
                }
            }
            $data['row']->update($request->all());
            return redirect()->route('Lms.Lesson_plans')->with('message_success','Recoard Updated');
        }
        $data['Lesson_plans']=Lesson_plans::select('lesson_plans.id','lesson_plans.months_id','lesson_plans.faculty','lesson_plans.semesters_id','subjects_id','chapter_no_id','faculties.faculty','semesters.semester','timetable_subjects.title as subject','chapter_no.title')
        ->leftjoin('faculties','lesson_plans.faculty','=','faculties.id')
        ->leftjoin('semesters','lesson_plans.semesters_id','=','semesters.id')
        ->leftjoin('timetable_subjects','lesson_plans.subjects_id','=','timetable_subjects.id')
        ->leftjoin('chapter_no','lesson_plans.chapter_no_id','=','chapter_no.id')
        ->get();

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

        $data['month']=DB::table('months')->select('id','title')
        ->where('status',1)
        ->orderBy('title','asc')
        ->pluck('title','id')->toArray();
       
        $data['month'] = array_prepend($data['month'],"-- Select months --",'');

       
       $data['subjects']=DB::table('timetable_subjects')->select('timetable_subjects.id','sm.title')
        ->leftjoin('subject_master as sm','sm.id','=','timetable_subjects.subject_master_id')
        ->where('branch_id',Session::get('activeBranch'))
        ->where('session_id',Session::get('activeSession'))
        ->where('course_id',$data['row']->faculty)
        ->where('section_id',$data['row']->semesters_id)
        ->where('timetable_subjects.status',1)
        ->where('sm.record_status',1)
        ->pluck('title','id')->toArray();
        
       $data['chapter']=DB::table('chapter_no')->select('id','title')
        ->where('status',1)
        ->orderBy('title','asc')
         ->where('faculty',$data['row']->faculty)
        ->where('semesters_id',$data['row']->semesters_id)
        ->where('timetable_subjects_id',$data['row']->subjects_id)
        ->pluck('title','id')->toArray();
        $data['chapter']=array_prepend($data['chapter'],"-- select chapter--",'');


          return view(parent::loadDataToView($this->view_path.'.add'),compact('data','id'));

    }
    public function delete(Request $request,$id){
        
       $data['row']=DB::table('lesson_plans')->where('id',$id)->delete();
        if(!$data['row']){
            parent::invalidRequest();
        }
        return redirect()->route($this->base_route)->with('message_success', $this->panel.' Deleted Successfully');

    }
    public function findSemester(Request $request)
    {
      //  dd($request->all());
        $response = [];
        $response['error'] = true;

        if ($request->has('faculty_id')) {
            $faculty = Faculty::select('faculties.id','faculties.faculty', 'faculties.slug', 'faculties.status','fs.semester_id','fs.faculty_id')
                ->where('faculties.id','=',$request->faculty_id)
                ->join('faculty_semester as fs', 'faculties.id', '=', 'fs.faculty_id')
                ->join('semesters as s', 'fs.semester_id', '=', 's.id')
                ->first();

            if ($faculty) {

                $response['semester'] = $faculty->semester()->select('semesters.id', 'semesters.semester')->get();
                //dd($response['semester']);
                $response['error'] = false;
                $response['success'] = 'Semester/Sec. Available For This Faculty/Class.';
            } else {
                $response['error'] = 'No Any Semester Assign on This Faculty/Class.';
            }

        } else {
            $response['message'] = 'Invalid request!!';
        }
        return response()->json(json_encode($response));
    }
    public function findSubject(Request $request){
            
        // $subjects= DB::table('timetable_subjects')->select('id','title')
        // ->where('course_id',$request->faculty_id)
        // ->where('section_id',$request->semester_id)
        // ->where('branch_id',$request->branch_id)
        // ->where('session_id',$request->session_id)
        // ->where('status',1)
        // ->get();
        
        $subjects = DB::table('timetable_subjects as sub')->select('sub.id as id','sm.title')
            ->where([
            ['sub.branch_id','=',$request->branch_id],
            ['sub.session_id','=',$request->session_id],
            ['sub.course_id','=',$request->faculty_id],
            ['sub.section_id','=',$request->semester_id],
            ['sub.status','=',1]
            ])

            ->leftjoin('subject_master as sm','sm.id','=','sub.subject_master_id')
            ->wherein('sm.is_main_subject',[0,1])
            ->orderBy('sm.title','asc')
            ->get();

         
        if (count($subjects)>0) {
            $response['subjects'] = $subjects;
            $response['success'] = 'Subjects Found, Select Subject and Manage Question.';
        }else {
            $response['error'] = 'No Any Subject Found. Please Contact Your Administrator.';
        }

        return response()->json(json_encode($response));
    }
    public function findChapter(Request $request){
        //dd($request->all());
        $chapter=DB::table('chapter_no')->select('id','title')
        ->where('faculty',$request->faculty_id)
        ->where('semesters_id',$request->semester_id)
        ->where('timetable_subjects_id',$request->subject_id)
        /*->where('branch_id',$request->branch_id)
        ->where('session_id',$request->session_id)*/
        ->where('status',1)
        ->get();
        //dd($chapter);

        if (count($chapter)>0) {
            $response['chapter'] = $chapter;
            $response['success'] = 'lession Found, Select lession and Manage Question.';
        }else {
            $response['error'] = 'No Any lession Found. Please Contact Your Administrator.';
        }
        //dd($chapter);
        return response()->json(json_encode($response));
    }
    public function Statuss(request $request){
        $data=Lesson_plans::where('id',$request->id)->update(['status'=>$request->status]);
         if ($data) {
            
            $response['msg'] = 'Status Updated Successfully';
        }else {
            $response['msg'] = 'Something Wrong!!!';
        }

        return response()->json(json_encode($response)); 

    }
   

}
