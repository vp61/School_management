<?php

namespace App\Http\Controllers\Lms;

use Illuminate\Http\Request;
use App\Http\Controllers\CollegeBaseController;

use App\Session_Model;
use App\Models\E_content;
use Session;
use App\Models\Faculty;
use App\Models\HomeWork;
use App\Models\Semester;
use App\Models\Subject; 
use DB,Log;
use Auth,URL;
use App\Branch;
use Carbon\Carbon;
use App\User;
use App\Models\GeneralSetting;

class EContentController extends CollegeBaseController
{
    //

    protected $base_route = 'Lms.Econtent';
    protected $view_path = 'Lms.Econtent';
    protected $panel = 'Econtent';
    protected $folder_path;
    protected $folder_name = 'Econtent';
    protected $filter_query = [];

    public function __construct()
    {
        $this->folder_path = public_path().DIRECTORY_SEPARATOR.$this->folder_name.DIRECTORY_SEPARATOR;

    }
   public function index(Request $request){
        //dd($request->all());
        $data['chapter']=[];
        $data['status']= ['0'=>'inactive','1'=>'active'];
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
        $data['subjects']= ['--select--'];
        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;
        if($request->all()){
                $fac= $request->faculty;
                $sem= $request->semesters_id;
                $sub=$request->subjects_id;
                $data['E_content']=E_content::select('e_content.id','e_content.faculty as faculty_id','e_content.semesters_id','subjects_id','chapter_no_id','faculties.faculty','semesters.semester','sm.title as subject','chapter_no.title','e_content.publish_date','e_content.end_date','e_content.status as st','e_content.file')
                ->leftjoin('faculties','e_content.faculty','=','faculties.id')
                ->leftjoin('semesters','e_content.semesters_id','=','semesters.id')
                ->leftjoin('timetable_subjects','e_content.subjects_id','=','timetable_subjects.id')
                 ->leftjoin('subject_master as sm','sm.id','=','timetable_subjects.subject_master_id')
                ->leftjoin('chapter_no','e_content.chapter_no_id','=','chapter_no.id')
                ->where('e_content.branch_id', session('activeBranch'))
                ->where('e_content.session_id', session('activeSession'))
                 ->where('e_content.status',1)
                ->where(function($query)use($request){

                if($request->faculty){
                  $query->where('e_content.faculty',$request->faculty);
                }
                if($request->semesters_id){
                  $query->where('e_content.semesters_id',$request->semesters_id);
                }
                if($request->subjects_id){
                  $query->where('e_content.subjects_id',$request->subjects_id);
                }
                if($request->teacher_id){
                  $query->where('e_content.created_by',$request->teacher_id);
                }
                })

               
                   

                ->get();
                foreach($data['E_content'] as $k=>$v){
                    $data['chapter'][$v->subjects_id]= $v;
                }
               

               return view(parent::loadDataToView($this->view_path.'.result'),compact('data','fac','sem','sub'));
        }else{
        return view(parent::loadDataToView($this->view_path.'.index'),compact('data'));
        }
    }
    public function add(Request $request){
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

        $data['subjects']= ['--select--'];

        $data['chapter']=DB::table('chapter_no')->select('id','title')
        
        ->where('chapter_no.branch_id',Session::get('activeBranch'))
        ->where('chapter_no.session_id',Session::get('activeSession'))
        ->where('status',1)
        ->orderBy('title','asc')
        ->pluck('title','id')->toArray();
        $data['chapter']=array_prepend($data['chapter'],"-- select chapter--",'');

        $data['worksheet']=DB::table('assin_book_type')->select('id','title')
        ->where('status',1)
        ->pluck('title','id')->toArray();
        $data['worksheet'] = array_prepend($data['worksheet'],"-- Select file --",'');
        $data['url'] = URL::current();
        return view(parent::loadDataToView($this->view_path.'.add'),compact('data'));
    }

    public function store(Request $request){
        $msg=['publish_date.required'=>'Date field is required',
                'end_date.required'=>'Date field is required',
                'faculty.required'=>'class field is requerd',
                'semesters_id.required'=>'semester field is required',
                'subjects_id.required'=>'subject field is requierd',
                'chapter_no_id.required'=>'chapter field is required',
                //'assin_book_type_id.required'=>'worksheet is required',
                'detail.required'=>'detail field is required',

        ];
        $rules=[
            // 'publish_date'  =>'required',
            //     'end_date'  =>'required',
                'faculty' =>'required',
                'semesters_id' =>'required',
                'subjects_id' =>'required',
                'chapter_no_id' =>'required',
                //'assin_book_type_id' =>'required',
                'detail' =>'required',

            ];
        $this->validate($request,$rules,$msg);
        $name = str_slug($request->get('detail'));
        //dd($name);
        if ($request->hasFile('attach_file')){
            $file = $request->file('attach_file');
            $file_name = rand(4585, 9857).'_'.$name.'.'.$file->getClientOriginalExtension();
            $file->move($this->folder_path, $file_name);
            
            $request->request->add(['file'=>$file_name]);
            
            
        }
        $request->request->add(['branch_id'=>session('activeBranch')]);
            $request->request->add(['session_id'=>session('activeSession')]);
            $request->request->add(['created_by'=>auth()->user()->id]);
            E_content::create($request->all()); 
        
        return redirect('Lms/Econtent')->with('message_success',' Added Successfully');
    }
    public function edit(Request $request,$id){
        //dd('fjdhfjd');
        $data['row']=E_content::find($id);
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

                if ($data['row']->file && file_exists($this->folder_path.$data['row']->file)){
                    @unlink($this->folder_path.$data['row']->file);
                }
            }
            $data['row']->update($request->all());
            return redirect()->route('Lms.Econtent')->with('message_success','Recoard Updated');
        }
        $data['E_content']=E_content::select('e_content.id','e_content.faculty','e_content.semesters_id','subjects_id','chapter_no_id','faculties.faculty','semesters.semester','timetable_subjects.title as subject','chapter_no.title')
        ->leftjoin('faculties','e_content.faculty','=','faculties.id')
        ->leftjoin('semesters','e_content.semesters_id','=','semesters.id')
        ->leftjoin('timetable_subjects','e_content.subjects_id','=','timetable_subjects.id')
        ->leftjoin('chapter_no','e_content.chapter_no_id','=','chapter_no.id')
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

        $data['staff']=DB::table('staff')->select('id','first_name','designation')
        ->where('branch_id',Session::get('activeBranch'))
        ->where('designation',env('TEACHER_DESIGNATION'))
        ->where('status',1)
        ->orderBy('first_name','asc')
        ->pluck('first_name','id')->toArray();
        //dd($data['staff']);
        $data['staff'] = array_prepend($data['staff'],"-- Select --",'');
        
        //dd($data['semester']);
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
        ->pluck('title','id')->toArray();
        $data['chapter']=array_prepend($data['chapter'],"-- select chapter--",'');

        $data['worksheet']=DB::table('assin_book_type')->select('id','title')
        ->where('status',1)
        ->pluck('title','id')->toArray();
        $data['worksheet'] = array_prepend($data['worksheet'],"-- Select file --",'');
        //dd($data);

        return view(parent::loadDataToView($this->view_path.'.add'),compact('data','id'));

    }
    public function delete(Request $request,$id){
        //dd('fdbfbd');
        $data['row']=E_content::where('id',$request->id)->update([
            'status'=>0,
            'updated_at'=>Carbon::now(),
            'last_updated_by' =>auth()->user()->id,
            ]);
        //dd($data['row']);
        if(!$data['row']){
            parent::invalidRequest();
        }

         
        return redirect()->route($this->base_route)->with('message_success', $this->panel.' Deleted Successfully');

    }
    public function Statuss(request $request){
       
        $data=E_content::where('id',$request->id)->update(['status'=>$request->status]);
         if ($data) {
            
            $response['msg'] = 'Status Updated Successfully';
        }else {
            $response['msg'] = 'Something Wrong!!!';
        }

        return response()->json(json_encode($response)); 

    }
    public function findChapter(Request $request){
        //dd($request->all());
        $chapter=DB::table('chapter_no')->select('chapter_no.id','chapter_no.title','lesson_plans.topic')
        ->leftjoin('lesson_plans','chapter_no.id','=','lesson_plans.chapter_no_id')
        ->where('chapter_no.faculty',$request->faculty_id)
        ->where('chapter_no.semesters_id',$request->semester_id)
        ->where('chapter_no.timetable_subjects_id',$request->subject_id)
        /*->where('branch_id',$request->branch_id)
        ->where('session_id',$request->session_id)*/
        ->where('chapter_no.status',1)
        ->get();
        
        if (count($chapter)>0) {
            $response['chapter'] = $chapter;
            $response['success'] = 'lession Found, Select lession and Manage Question.';
        }else {
            $response['error'] = 'No Any lession Found. Please Contact Your Administrator.';
        }
        //dd($chapter);
        return response()->json(json_encode($response));
    }
    public function chapter(Request $request){
       
        $chapter=DB::table('chapter_no')->select('id','title')
        //->where('timetable_subjects_id',$request->id)
        /*->where('branch_id',$request->branch_id)
        ->where('session_id',$request->session_id)*/
        ->where('status',1)
        ->get();
        //dd($chapter);
        if ($chapter) {
            $response['chapter'] = $chapter;
            $response['success'] = 'lession Found, Select lession and Manage Question.';
        }else {
            $response['error'] = 'No Any lession Found. Please Contact Your Administrator.';
        }
        //dd($chapter);
    return response()->json(json_encode($response));


    }
      public function topic(Request $request){
        // dd($request->all());
        $topic=DB::table('e_content')->select('e_content.id','e_content.detail','e_content.file','e_content.status','e_content.chapter_no_id','e_content.detail as topic')
           // ->leftjoin('chapter_no','e_content.chapter_no_id','=','chapter_no.id')
         // ->leftJoin('lesson_plans','e_content.chapter_no_id','=','lesson_plans.chapter_no_id')
        ->where('e_content.faculty',$request->faculty_id)
        ->where('e_content.semesters_id',$request->semester_id)
        ->where('e_content.subjects_id',$request->subject_id)
        ->where('e_content.chapter_no_id',$request->chapter_no_id)
        ->where('e_content.branch_id',Session::get('activeBranch'))
        ->where('e_content.session_id',Session::get('activeSession'))
        ->where('e_content.status',1)
        ->get();

        //  dd($topic,$request->all());
        if($topic){
            $response['topic'] = $topic;
            $response['success'] ='this is worked,';
        }else{
            $response['error'] ='this side is wrong output';
        }
        return response()->json(json_encode($response));
    }
}
