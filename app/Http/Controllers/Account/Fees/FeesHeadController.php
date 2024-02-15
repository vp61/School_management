<?php

namespace App\Http\Controllers\Account\Fees;

use App\Http\Controllers\CollegeBaseController;
use App\Http\Requests\Account\FeeHead\AddValidation;
use App\Http\Requests\Account\FeeHead\EditValidation;
use App\Models\FeeHead;
use Illuminate\Http\Request;
class FeesHeadController extends CollegeBaseController
{
    protected $base_route = 'account.fees.head';
    protected $view_path = 'account.fees.head';
    protected $panel = 'Fees Head';
    protected $filter_query = [];

    public function __construct()
    {

    }

    public function index(Request $request)
    {
        $data = [];
        $data['fees_head'] = FeeHead::select('id', 'fee_head_title', 'status')->orderBy('fee_head_title','asc')->get();
        $data['parent']=FeeHead::select('id','fee_head_title as title')->where([
            ['status','=',1],
            ['parent_id','=',0]
        ])->pluck('title','id')->toArray();
        $data['parent']=array_prepend($data['parent'],"--Select Parent Head--",'');
        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }
    
    public function dashboard()
    {
        $fees['assign']= DB::table('faculties as f')->select('f.id as course_id','sds.id','sds.student_id',DB::raw('count(sds.id) as total_stu'),'f.faculty')
        ->where('f.branch_id',session::get('activeBranch'))
        ->where('sds.session_id',session::get('activeSession'))
        ->leftjoin('student_detail_sessionwise as sds','sds.course_id','=','f.id')
       ->leftjoin('students as st','st.id','=','sds.student_id')
       ->where('st.status',1)
        
        ->groupBy('f.id')
        ->get();
        foreach($fees['assign'] as $key =>$value)
        {

            
            $student_count[$value->course_id]=$value->total_stu;
            
        }
        
        $fees['show']=DB::table('assign_fee as af')->select('af.id','course_id','fee_amount','m.faculty','af.student_id','fee_heads.fee_head_title')
        ->leftjoin('faculties as m','m.id','=','af.course_id')
        ->leftjoin('fee_heads','fee_heads.id','=','af.fee_head_id')
        ->where('af.branch_id',session::get('activeBranch'))
        ->where('af.session_id',session::get('activeSession'))
        //->where('course_id',1)
        ->orderby('af.course_id')
        // ->groupBy('af.course_id')
        ->get();
        //dd($fees['show']);
       foreach($fees['show'] as $key =>$value){
        //dd($value);
            if(isset($paid[$value->id])){
                $paid[$value->id] = 0;
            }
                //dd($value);
            $paid[$value->id] = DB::table('collect_fee')->select()
            ->where('assign_fee_id',$value->id)
            
            ->sum('amount_paid');
            if(!isset($fees['temp'][$value->course_id]['paid'])){
                $fees['temp'][$value->course_id]['paid'] = 0;
            }

                if($paid[$value->id])
                {
                    $fees['temp'][$value->course_id]['paid'] =$fees['temp'][$value->course_id]['paid'] + $paid[$value->id];
                     
                }
                //dd($fees['temp']);
                $fees['show'][$key]->paid =$fees['temp'][$value->course_id]['paid'];
                      
        }
        $fees['disp']=[];
        foreach($fees['assign'] as $val){
            
            foreach($fees['show'] as $key =>$value)
                {
                
                    if($value->course_id == $val->course_id){
                        if(!isset($arr[$value->course_id])){
                        $arr[$value->course_id]=0;
                        }
                        if(!isset($student_count[$value->course_id])){
                            $student_count[$value->course_id] = 0;
                        }
                       
                        if($value->student_id == 0)
                        {
                            $arr[$value->course_id] = $arr[$value->course_id] + ($value->fee_amount * $student_count[$value->course_id]);  
                        }else
                        { 
                            $arr[$value->course_id]=($arr[$value->course_id]) + ($value->fee_amount);
                        }

                       
                        $fees['disp'][$value->course_id] = $value;
                        
                        $fees['disp'][$value->course_id]->totalcoll=$arr[$value->course_id];
                    }else{

                            if(!isset($fees['disp'][$val->course_id])){
                                $fees['disp'][$val->course_id] = $val;
                                
                            $fees['disp'][$val->course_id]->totalcoll=0;    
                            }   
                        }
                        
                }
                
               
        }
        //dd($fees['disp']);
        $fees['show'] = $fees['disp'];
        foreach($fees['show'] as $key =>$value){
            if(!isset($fees['temp'][$value->course_id]['paid'])){
                $fees['temp'][$value->course_id]['paid'] = 0;
            }

            $fees['show'][$key]->paid=$fees['temp'][$value->course_id]['paid'];
        }
        //dd($fees['show']);
        foreach($fees['show'] as $key =>$value){
            
            if(isset($todayfee[$value->course_id])){
                $todayfee[$value->course_id] =0;
               
            }
            $todayfee[$value->course_id] = DB::table('assign_fee')->select()
            
             
            ->where('course_id',$value->course_id)
            ->whereDate('created_at','=',Carbon::today()->toDateString())

            ->sum('fee_amount');
           
            $fees['show'][$key]->today =  $todayfee[$value->course_id];
            
        }

        $head['assign'] = DB::table('assign_fee as af')->select('*')
        ->where('af.branch_id',session::get('activeBranch'))
        ->where('af.session_id',session::get('activeSession'))
        ->where('af.status','=',1)
        ->leftjoin('fee_heads as fh','fh.id','=','af.fee_head_id')
        ->groupBy('af.fee_head_id')
        ->get();
          $feehead=[];
        foreach($head['assign'] as $key=>$value){
            //dd($value);
            $count_stu =DB::table('student_detail_sessionwise as sds')->select()
            ->where('course_id',$value->course_id)
            ->count('sds.id');

            //dd($count_stu);
            if(!isset($feehead[$value->fee_head_id])){
                $feehead[$value->fee_head_id]['assign']=0;
            }
            if($value->student_id==0){
                //$feehead[$value->fee_head_id]= ['assign'];

                $feehead[$value->fee_head_id]['assign']=($feehead[$value->fee_head_id]['assign'])+($value->fee_amount * $count_stu);
                //
            }
            else{
                $feehead[$value->fee_head_id]['assign']=($feehead[$value->fee_head_id]['assign']) + ($value->fee_amount);

            }

            $temp = $feehead[$value->fee_head_id]['assign'];
            $value->assign = $temp;
            $feehead[$value->fee_head_id]=$value;
            //dd($feehead);

            $sam[$value->id]=DB::table('collect_fee')
            ->where('assign_fee_id',$value->id)
            ->sum('amount_paid');
           // ->groupBy('collect_fee.assign_fee_id');
            //dd($sam);

            
            if(!isset($feehead['demo'][$value->fee_head_id])){
                $feehead['demo'][$value->fee_head_id]['collect']=0;
                
            }
            //dd($feehead[$value->fee_head_id]['collect']);
            if($sam[$value->id])
            {
                $feehead['demo'][$value->fee_head_id]['collect']= ($feehead['demo'][$value->fee_head_id]['collect']) + $sam[$value->id];
            }
            $head['assign'][$key]->collect=$feehead['demo'][$value->fee_head_id]['collect'];
            //dd($feehead);
            $today['today'][$value->fee_head_id] = DB::table('assign_fee')->select()
            ->where('fee_head_id',$value->fee_head_id)
            ->whereDate('created_at','=',Carbon::today()->toDateString())

            ->sum('fee_amount');
            //dd($today);


            if(!isset($today['today'][$value->fee_head_id])){
                $today['today'][$value->fee_head_id]=0;
                
            }
            $head['assign'][$key]->today =$today['today'][$value->fee_head_id];


        }
        //dd($head['assign']);
       //dd($feehead);
        $month['month_id']=DB::table('months')->select('id as due_month','title')
        ->orderBy('id')
        ->get();
        //dd($month);
        $month['assign']=DB::table('assign_fee as af')->select('*')
        ->where('af.branch_id',session::get('activeBranch'))
        ->where('af.session_id',session::get('activeSession'))
        ->where('af.status','=',1)
        ->leftjoin('months as m','m.id','=','af.due_month')
        ->groupBy('af.due_month')
        ->get();
        //dd($month['assign']);
        foreach($month['assign'] as $key=> $value){

            $no_stu=DB::table('student_detail_sessionwise as sds')->select()
            ->where('course_id','=',$value->course_id)
            ->count('id');


            if(!isset($arr[$value->due_month])){
                $arr[$value->due_month]=0;
            }

            if($value->student_id==0){

                $arr[$value->due_month]=$arr[$value->due_month] +($value->fee_amount * $no_stu);
            }
            else{
                $arr[$value->due_month]=$arr[$value->due_month] + $value->fee_amount;
            }
            $monthwise[$value->due_month]=$value;
            $monthwise[$value->due_month]->assign =$arr[$value->due_month];


            $collect[$value->id]=DB::table('assign_fee as af')
                ->leftjoin('collect_fee as cf','cf.assign_fee_id','=','af.id')
                ->where('af.branch_id',session::get('activeBranch'))
                ->where('af.session_id',session::get('activeSession'))
                ->where('af.due_month',$value->due_month)
                ->groupBy('af.id')
                ->sum('cf.amount_paid');
                $month['assign'][$key]->collect =$collect[$value->id];
        }    
        // dd($month);
        $total_collect = DB::table('collect_fee')
        ->selectRaw('MONTH(reciept_date) as month')
        ->selectRaw('SUM(amount_paid) as collected')
        ->leftjoin('assign_fee as af','af.id','=','collect_fee.assign_fee_id')
        ->where([
            ['af.session_id','=',Session::get('activeSession')],
            ['af.branch_id','=',Session::get('activeBranch')],
            ['af.status','=',1],
            ['collect_fee.status','=',1],
        ])
        ->groupBy(DB::raw('MONTH(reciept_date)'))
        ->get();
         //dd($total_collect);
        foreach($month['month_id'] as $ke=>$val){
            //dd($val);
            
            if(!isset($assing[$val->due_month]['assign'])){
                $assing[$val->due_month]['assign'] = 0;

            }
            if(!isset($assing[$val->due_month]['collect'])){
                $assing[$val->due_month]['collect'] = 0;
            }
            if(!isset($assing[$val->due_month]['total_collect'])){
                $assing[$val->due_month]['total_collect'] = 0;
            }
            foreach($month['assign'] as $k=>$v){
                //dd($v);
               if($val->due_month==$v->due_month){
                
                $assing[$val->due_month]['assign'] += $v->assign;
                
                $assing[$val->due_month]['collect'] += $v->collect;

                

                }


            }
            foreach($total_collect as $coll){
                //dd($total_collect);
                if($val->due_month == $coll->month){
                    //dd($val->due_month);
                    $assing[$val->due_month]['total_collect'] += $coll->collected;
                    
                }
            }
            //dd($val);
            if($val->title){
                //
                $assing[$val->due_month]['title'] = $val->title;

            }
           
        }
        return view(parent::loadDataToView($this->view_path.'.graph'),compact('fees','head','assing'));
    }

    public function store(AddValidation $request)
    {
        if($request->parent_id){
            $parent_head=FeeHead::findOrFail($request->parent_id);
            $title=$parent_head->fee_head_title." ( ".$request->fee_head_title." )";
            $parent_id=$request->parent_id;
        }else{
             $title=$request->fee_head_title;
             $parent_id=0;
        }
        $request->request->add(['created_by' => auth()->user()->id]);
        $request->request->add(['fee_head_title' => $title]);
        $request->request->add(['slug' => $title]);
        $request->request->add(['parent_id' => $parent_id]);

        $faculty = FeeHead::create($request->all());

        $request->session()->flash($this->message_success, $this->panel. ' Created Successfully.');
        return redirect()->route($this->base_route);
    }

    public function edit(Request $request, $id)
    {
        $data = [];
        if (!$data['row'] = FeeHead::find($id))
            return parent::invalidRequest();

        $data['fees_head'] = FeeHead::select('id', 'fee_head_title', 'status')->orderBy('fee_head_title','asc')->get();
        $data['parent']=FeeHead::select('id','fee_head_title as title')->where([
            ['status','=',1],
            ['parent_id','=',0]
        ])->pluck('title','id')->toArray();
        $data['parent']=array_prepend($data['parent'],"--Select Parent Head--",'');
        $data['base_route'] = $this->base_route;
        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    public function update(EditValidation $request, $id)
    {
        if (!$row = FeeHead::find($id)) return parent::invalidRequest();
        if($request->parent_id){
            $parent_head=FeeHead::findOrFail($request->parent_id);
            $title=$parent_head->fee_head_title." ( ".$request->fee_head_title." )";
            $parent_id=$request->parent_id;
        }else{
             $title=$request->fee_head_title;
             $parent_id=0;
        }
        $request->request->add(['fee_head_title' => $title]);
        $request->request->add(['slug' => $title]);
        $request->request->add(['parent_id' => $parent_id]);

        $request->request->add(['last_updated_by' => auth()->user()->id]);
        $row->update($request->all());

        $request->session()->flash($this->message_success, $this->panel.' Updated Successfully.');
        return redirect()->route($this->base_route);
    }

    public function delete(Request $request, $id)
    {
        if (!$row = FeeHead::find($id)) return parent::invalidRequest();
        $row->delete();
        $request->session()->flash($this->message_success, $this->panel.' Deleted Successfully.');
        return redirect()->route($this->base_route);
    }

    public function bulkAction(Request $request)
    {
        if ($request->has('bulk_action') && in_array($request->get('bulk_action'), ['active', 'in-active', 'delete'])) {

            if ($request->has('chkIds')) {
                foreach ($request->get('chkIds') as $row_id) {
                    switch ($request->get('bulk_action')) {
                        case 'active':
                        case 'in-active':
                            $row = FeeHead::find($row_id);
                            if ($row) {
                                $row->status = $request->get('bulk_action') == 'active'?'active':'in-active';
                                $row->save();
                            }
                            break;
                        case 'delete':
                            $row = FeeHead::find($row_id);
                            $row->delete();
                            break;
                    }
                }

                if ($request->get('bulk_action') == 'active' || $request->get('bulk_action') == 'in-active')
                    $request->session()->flash($this->message_success, $request->get('bulk_action'). ' Action Successfully.');
                else
                    $request->session()->flash($this->message_success, 'Deleted successfully.');

                return redirect()->route($this->base_route);

            } else {
                $request->session()->flash($this->message_warning, 'Please, Check at least one row.');
                return redirect()->route($this->base_route);
            }

        } else return parent::invalidRequest();

    }

    public function active(request $request, $id)
    {
        if (!$row = FeeHead::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $row->faculty.' '.$this->panel.' Active Successfully.');
        return redirect()->route($this->base_route);
    }

    public function inActive(request $request, $id)
    {
        if (!$row = FeeHead::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'in-active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $row->faculty.' '.$this->panel.' In-Active Successfully.');
        return redirect()->route($this->base_route);
    }
}
