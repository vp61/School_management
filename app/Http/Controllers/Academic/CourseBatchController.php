<?php
namespace App\Http\Controllers\Academic;

use App\Http\Controllers\CollegeBaseController;
use DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Session,Log;
use App\Models\CourseBatch;
use App\Models\Faculty;


class CourseBatchController extends CollegeBaseController
{
    protected $base_route = 'courseBatch';
    protected $view_path = 'academic.course-batch';
    protected $panel = ' Batch';
    protected $filter_query = [];

    public function __construct()
    {
        $this->panel=env("course_label").' '.$this->panel;
    }
    public function index(){
        $data['type']=DB::table('course_type')->select('id','title')->where([
            ['branch_id','=',Session::get('activeBranch')],
            ['status','=',1]
        ])->pluck('title','id')
        ->toArray();
        $data['type']=array_prepend($data['type'],"--Select Type--",'');
        $data['batch']=CourseBatch::select('course_batches.*','fac.faculty as course','ct.title as course_type')
        ->where([
            ['course_batches.status','=',1],
            ['course_batches.session_id','=',Session::get('activeSession')],
            ['course_batches.branch_id','=',Session::get('activeBranch')]
        ])
        ->leftjoin('faculties as fac','fac.id','=','course_batches.course_id')
        ->leftjoin('course_type as ct','ct.id','=','course_batches.course_type')
        ->get();
        $data['faculty'][''] = 'Select '.env('course_label');
        return view(parent::loadDataToView($this->view_path.'.index'),compact('data'));
    }

    public function store(Request $request)
    {
        $request->request->add(['created_by'=>auth()->user()->id]);
        $request->request->add(['session_id'=>Session::get('activeSession')]);
        $request->request->add(['status'=>1]);
        $request->request->add(['branch_id'=>Session::get('activeBranch')]);
        $data=CourseBatch::create($request->all());
        $request->session()->flash($this->message_success, $this->panel.' Add Successfully.');
        return back();
    }
    public function edit(Request $request,$id){
         
          if (!$data['row'] = CourseBatch::find($id))
            return parent::invalidRequest();
        if($request->all()){
           $request->request->add(['updated_at'=>Carbon::now()]);
           $request->request->add(['updated_by'=>auth()->user()->id]);
          $data['row']->update($request->all());
           return redirect('/courseBatch')->with('message_success','Updated Successfully');
        }
                
        $data['batch']=CourseBatch::select('course_batches.*','fac.faculty as course','ct.title as course_type')
        ->where([
            ['course_batches.status','=',1],
            ['course_batches.session_id','=',Session::get('activeSession')],
            ['course_batches.branch_id','=',Session::get('activeBranch')]
        ])
        ->leftjoin('faculties as fac','fac.id','=','course_batches.course_id')
        ->leftjoin('course_type as ct','ct.id','=','course_batches.course_type')
        ->get();
         $data['type']=DB::table('course_type')->select('id','title')->where([
            ['branch_id','=',Session::get('activeBranch')],
            ['status','=',1]
        ])->pluck('title','id')
        ->toArray();
        $data['type']=array_prepend($data['type'],"--Select Type--",'');
        $data['faculty'] = Faculty::where([
            ['course_type','=',$data['row']->course_type],
            ['branch_id','=',$data['row']->branch_id],
            ['status','=',1]
        ])->select('id','faculty')->pluck('faculty','id')->toArray();
        // dd($data['type']);
        // $request->session()->flash('message_warning','Please'.env("course_label").' Type To Load '.env("course_label"));
        return view(parent::loadDataToView($this->view_path.'.edit'),compact('data','id'));
    }          
    public function delete($id)
    {
       $delete=CourseBatch::where('id',$id)->update([
            'status'=>0
       ]);

        session()->flash($this->message_success, $this->panel.' Deleted Successfully.');
        return redirect('courseBatch');
    }  
    public function loadCourseByType(Request $request){
        $response=[];
        $response['error']=true;
        if($request->courseType && $request->branch_id){
            $data=Faculty::select('faculty','id')->where([
                ['course_type','=',$request->courseType],
                ['status','=',1],
                ['branch_id','=',$request->branch_id]
            ])->get();
            if($data){
                $response['data']=$data;
                $response['error']=false;
                $response['success']=env("course_label")." Found";
            }else{
                $response['error']="No ".env("course_label")." Found";
            }
        }else{
            $response['message']="Invalid Request";
        }
        return response()->json(json_encode($response));
    } 

}