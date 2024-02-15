<?php
namespace App\Http\Controllers\Academic;

use App\Http\Controllers\CollegeBaseController;
use Illuminate\Http\Request;
use App\Http\Requests\Transport\User\AddValidation;
use App\Http\Requests\Transport\User\EditValidation;
use DB;
use Carbon\Carbon;
use Session,URL;
use App\Models\Source;
use App\Models\Faculty;
use App\Models\Month;
use App\Models\FeeStructure;


class FeeStructureController extends CollegeBaseController
{
    protected $base_route = 'feeStructure';
    protected $view_path = 'academic.fee-structure';
    protected $panel = 'Fee Structure';
    protected $filter_query = [];

    public function __construct()
    {

    }
    public function index(Request $request){
        $data['faculty']=Faculty::where([
            ['branch_id','=',Session::get('activeBranch')],
            ['status','=',1]
        ])->select('id','faculty')->pluck('faculty','id')->toArray();
        $data['faculty']=array_prepend($data['faculty'],'--Select Class--','');
        $data['month']=Month::where([
            ['status','=',1]
        ])->select('id','title')->pluck('title','id')->toArray();
        $data['month']=array_prepend($data['month'],'--Select Month--','');
        $data['feeStructure']=[];
        if($request->faculty){
            $data['feeStructure']=FeeStructure::select('fee_structures.*','from.title as from_month','to.title as to_month','fac.faculty')
            ->leftJoin('months as from','fee_structures.from_month','=','from.id')
            ->leftJoin('months as to','fee_structures.to_month','=','to.id')
            ->leftJoin('faculties as fac','fac.id','=','fee_structures.faculty_id')
            ->where([
                ['fee_structures.record_status','=',1],
                ['fee_structures.faculty_id','=',$request->faculty],
                ['fee_structures.branch_id','=',Session::get('activeBranch')],
                ['fee_structures.session_id','=',Session::get('activeSession')]
            ])->get();
        }
        $data['source']=Source::where('record_status',1)->select('*')->get();
        return view(parent::loadDataToView($this->view_path.'.index'),compact('data'));
    }

    public function store(Request $request)
    {
        $msg=[
            'faculty_id.required'=>"Please Enter Title",
            'from_month.required'=>"Please Enter Description",
            'to_month.required'=>"Please Enter Description"
        ];
        $rules=[
            'faculty_id'=>'required',
            'from_month'=>"required",
            'to_month'=>"required"
        ];
        $this->validate($request,$rules,$msg);
        $request->request->add(['created_at'=>Carbon::now()]);
        $request->request->add(['created_by'=>auth()->user()->id]);
        $request->request->add(['branch_id'=>Session::get('activeBranch')]);
        $request->request->add(['session_id'=>Session::get('activeSession')]);
        $request->request->add(['record_status'=>1]);
        $dd=FeeStructure::create($request->all());
         $request->session()->flash($this->message_success, $this->panel.' Added Successfully.');
        return redirect()->back();
    }
    public function edit(Request $request,$id){
        $data['row']=FeeStructure::find($id);
        if(!$data['row']){
            parent::invalidRequest();
        }
        $data['feeStructure']=[];

        $data['faculty']=Faculty::where([
            ['branch_id','=',Session::get('activeBranch')],
            ['status','=',1]
        ])->select('id','faculty')->pluck('faculty','id')->toArray();
        $data['faculty']=array_prepend($data['faculty'],'--Select Class--','');
        $data['month']=Month::where([
            ['status','=',1]
        ])->select('id','title')->pluck('title','id')->toArray();
        $data['month']=array_prepend($data['month'],'--Select Month--','');
       
        if($request->faculty_id && $request->from_month){
            $msg=[
                'faculty_id.required'=>"Please Enter Title",
                'from_month.required'=>"Please Enter Description",
                'to_month.required'=>"Please Enter Description"
            ];
            $rules=[
                'faculty_id'=>'required',
                'from_month'=>"required",
                'to_month'=>"required"
            ];
            $this->validate($request,$rules,$msg);
            $request->request->add(['updated_at'=>Carbon::now()]);
            $request->request->add(['updated_by'=>auth()->user()->id]);
            $data['row']->update($request->all());
            return redirect()->route($this->base_route)->with('message_success', $this->panel.' Updated Successfully');
        }
        return view(parent::loadDataToView($this->view_path.'.index'),compact('data','id'));
    }          
    public function delete(Request $request, $id)
    {
       $data['row']=FeeStructure::find($id);
        if(!$data['row']){
            parent::invalidRequest();
        }
        $request->request->add(['record_status'=>0]);
            $data['row']->update($request->all());
        return redirect()->route($this->base_route)->with('message_success', $this->panel.' Deleted Successfully');    
    }   

}