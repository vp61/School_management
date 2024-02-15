<?php

namespace App\Http\Controllers\EnableFine;

use App\Http\Controllers\CollegeBaseController;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Month;
use App\Models\Faculty;
use App\Models\FineSettings;
use Session,DB;
class FineController extends CollegeBaseController
{
    protected $base_route = 'enableFine';
    protected $view_path = 'EnableFine';
    protected $panel = 'Fine Settings';
    protected $filter_query = [];

    public function __construct()
    {

    }

    public function index(Request $request)
    {
        $dropdown = $this->get_table_and_dropdown();
        
        return view(parent::loadDataToView($this->view_path.'.index'), compact('dropdown'));
    }
    public function store(Request $request)
    {
        // dd($request->all());
        $msg=[
            'due_month_id.required'=>"Please Select Month",
            'faculty_id.required'=>"Please Select Course/Class",
            'fee_head_id.required'=>"Please Select Head",
            'start_date.required'=>"Please Enter Start Date ",
            'daily_fine.required'=>"Please Enter Daily Fine",
            'monthly_fine.required'=>"Please Enter Monthly Fine ",
            'on_minimum_due.required'=>"Please Enter On Minumum Due Amount",
        ];
        $rules=[
            'due_month_id'=>'required',
            'faculty_id'=>'required',
            'fee_head_id'=>'required',
            'start_date'=>'required',
            'daily_fine'=>'required',
            'monthly_fine'=>'required',
            'on_minimum_due'=>'required',
        ];
        $this->validate($request,$rules,$msg);
        $request->request->add(['created_at'=>Carbon::now()]);
        $request->request->add(['created_by'=>auth()->user()->id]);
        $request->request->add(['branch_id'=>Session::get('activeBranch')]);
        $request->request->add(['session_id'=>Session::get('activeSession')]);
        $request->request->add(['record_status'=>1]);
        FineSettings::create($request->all()); 
         $request->session()->flash($this->message_success, $this->panel.' Added Successfully.');
        return back();
    }
    // public function edit(Request $request,$id){
    //     $data['row']=Exam_type_Master::find($id);
    //     if(!$data['row']){
    //         parent::invalidRequest();
    //     }
    //     if($request->all() ){
    //         $msg=[
    //         'title.required'=>"Please Enter Title",
    //         ];
    //         $rules=[
    //             'title'=>'required',
    //         ];
    //         $this->validate($request,$rules,$msg);
    //         $request->request->add(['updated_at'=>Carbon::now()]);
    //         $request->request->add(['updated_by'=>auth()->user()->id]);
    //         $data['row']->update($request->all());

            

    //         return redirect()->route($this->base_route)->with('message_success', $this->panel.' Updated Successfully');
    //     }
    //     $dropdown = $this->get_table_and_dropdown();
    //     return view(parent::loadDataToView($this->view_path.'.index'),compact('data','id','dropdown'));
    // }          
    public function delete(Request $request, $id)
    {
       $data['row']=FineSettings::find($id);
        if(!$data['row']){
            parent::invalidRequest();
        }
        $request->request->add(['record_status'=>0]);
            $data['row']->update($request->all());
        return redirect()->route($this->base_route)->with('message_success', $this->panel.' Deleted Successfully');    
    }
    public function get_table_and_dropdown(){

        $data['due-month']=Month::where([
            ['status','=',1],
           
        ])->select('id','title')->pluck('title','id')->toArray();

        $data['due-month'] = array_prepend($data['due-month'],'--Select Due Month--','');

        $data['faculty'] = Faculty::where([
            ['status','=',1],
            ['branch_id','=',Session::get('activeBranch')],
        ])->select('id','faculty as title')->orderBy('title','asc')->pluck('title','id')->toArray();
        $data['faculty'] = array_prepend($data['faculty'],'--Select '.env('course_label').'--','');

        $data['list'] = FineSettings::select('fine_settings.id','start_date','daily_fine','monthly_fine','on_minimum_due','months.title as month','fac.faculty','fh.fee_head_title','ur.name as entered_by','fine_settings.created_at')
            ->leftJoin('months','months.id','=','fine_settings.due_month_id')
            ->leftJoin('faculties as fac','fac.id','=','fine_settings.faculty_id')
            ->leftJoin('fee_heads as fh','fh.id','=','fine_settings.fee_head_id')
            ->leftJoin('users as ur','ur.id','=','fine_settings.created_by')
            ->where([
                ['fine_settings.record_status','=',1],
                ['fine_settings.branch_id','=',Session::get('activeBranch')],
                ['fine_settings.session_id','=',Session::get('activeSession')],

            ])
            ->get();
        
        // ->get();
        return $data; 
    }

    public function getHead(Request $request){
        $data['error'] = true;
        $data['data'] = [];

        if($request->month_id && $request->faculty_id && $request->session_id && $request->branch_id ){
            $ret = DB::table('assign_fee')->select('fh.id','fh.fee_head_title')
                   ->where([
                        ['due_month','=',$request->month_id],
                        ['course_id','=',$request->faculty_id],
                        ['session_id','=',$request->session_id],
                        ['branch_id','=',$request->branch_id],
                        ['assign_fee.status','=',1],
                   ])
                   ->leftJoin('fee_heads as fh','fh.id','=','assign_fee.fee_head_id')
                   ->groupBy('fh.id')
                   ->pluck('fee_head_title','id')->toArray();

                   if(count($ret)>0){
                        $data['error'] = false;
                        $data['data'] = $ret;
                   }

        }

        return response()->json(json_encode($data));
    }

}