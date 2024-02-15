<?php
namespace App\Http\Controllers\Academic;

use App\Http\Controllers\CollegeBaseController;
use DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Session;


class CourseTypeController extends CollegeBaseController
{
    protected $base_route = 'courseType';
    protected $view_path = 'academic.course-type';
    protected $panel = 'Class Type';
    protected $filter_query = [];

    public function __construct()
    {
        $this->panel=env("course_label")." Type";
    }
    public function index(){
        $data['type']=DB::table('course_type')->select('*')->where([
            ['branch_id','=',Session::get('activeBranch')],
            ['status','=',1]
        ])
        ->get();
        return view(parent::loadDataToView($this->view_path.'.index'),compact('data'));
    }

    public function store(Request $request)
    {
            $insert=DB::table('course_type')->insert([
                'title'=>$request->name,
                'created_at'=>Carbon::now(),
                'created_by'=>auth()->user()->id,
                'branch_id'=>Session::get('activeBranch'),
                'session_id'=>Session::get('activeSession'),
                'status'=>1,
                'org_id'=>$request->org_id
            ]);
         $request->session()->flash($this->message_success, $this->panel.' Add Successfully.');
        return back();
    }
    public function edit(Request $request,$id){
        if($request->name){
           $update=DB::table('course_type')->where('id',$id)->update(
            [
                'title'=>$request->name,
                'updated_at'=>Carbon::now(),
                'updated_by'=>auth()->user()->id,
            ]); 
           return redirect('/courseType')->with('message_success','Updated Successfully');
        }
        $data['edit']=DB::table('course_type')->select('title as name')
        ->where('id',$id)
        ->first();
        $data['type']=DB::table('course_type')->select('*')->where([
            ['branch_id','=',Session::get('activeBranch')],
            ['status','=',1]
        ])
        ->get();
        return view(parent::loadDataToView($this->view_path.'.edit'),compact('data','id'));
    }          
    public function delete(Request $request, $id)
    {
       $delete=DB::table('course_type')->where('id',$id)->update([
            'status'=>0
       ]);

        $request->session()->flash($this->message_success, $this->panel.' Deleted Successfully.');
        return redirect('courseType');
    }   

}