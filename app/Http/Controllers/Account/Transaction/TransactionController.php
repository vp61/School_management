<?php
namespace App\Http\Controllers\Account\Transaction;
use App\Http\Controllers\CollegeBaseController;
use App\Models\Transaction;
use App\Models\TransactionHead;
use Illuminate\Http\Request;
use App\Branch; use URL, Session,DB;
use Carbon\Carbon;

class TransactionController extends CollegeBaseController
{
    protected $base_route = 'account.transaction';
    protected $view_path = 'account.transaction';
    protected $panel = 'Transaction';
    protected $filter_query = [];

    public function __construct(){

    }

    public function index(Request $request)
    {
        $data = [];
         
        $data['transaction'] = Transaction::select('transactions.id', 'transactions.date', 'transactions.tr_head_id', 'transactions.amount','transactions.type','transactions.note', 'transactions.description','transactions.status', 'branches.branch_name')
            ->leftJoin('branches', function($join){
                $join->on('transactions.branch_id', '=', 'branches.id');
            })->where([
                ['transactions.session_id','=',Session::get('activeSession')]
            ])
            ->where(function ($query) use ($request) {
                if($request->branch_type){
                    if($request->branch_type==2){
                        $query->where('transactions.branch_id','=',Session::get('activeBranch'));
                    }
                }
                // $query->where('transactions.branch_id', '=', Session::get('activeBranch'));
                // $query->orWhere('transactions.branchs_id', '=', NULL);

                // $query->where('transactions.session_id', '=', Session::get('activeSession'));
                // $query->orWhere('transactions.session_id', '=', NULL);
                if ($request->tr_head) {
                    $query->where('transactions.tr_head_id', 'like', $request->tr_head);
                    $this->filter_query['tr_head_id'] = $request->tr_head;
                }

                if ($request->tr_date_start && $request->hastr_date_end) {
                    $query->whereBetween('date', [$request->get('tr_date_start'), $request->get('tr_date_end')]);
                    $this->filter_query['tr_date_start'] = $request->get('tr_date_start');
                    $this->filter_query['tr_date_end'] = $request->get('tr_date_end');
                }
                else{
                    if(isset($_GET['tr_date_start'])){

                    }else{
                         $query->where('date',Carbon::now()->format('Y-m-d'));
                    $this->filter_query['tr_date_start'] = $request->get('tr_date_start');
                    }
                    
                }
                if($request->type){
                    $query->where('type',$request->type);
                }
                if($request->rcp_no){
                    $query->where('transactions.id',str_replace("AES","",$request->rcp_no));
                }
                
            })
            ->orderBy('transactions.id', 'Desc')
            ->where('transactions.status',1)
            ->get();
        $data['tr_heads'] = [];
        $data['tr_heads']=TransactionHead::select('id','tr_head')->where([
           ['status','=',1]
        ])->pluck('tr_head','id')->toArray();
        $data['tr_heads']=array_prepend($data['tr_heads'],'--Select Head--','');
        // foreach (TransactionHead::select('id', 'tr_head')->get() as $tr_head) {
        //     $data['tr_heads'][$tr_head->id] = $tr_head->tr_head;
        // }

        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;

        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    public function add(Request $request)
    {
        $data = [];
        /*$data['tr_heads'] = [];
        $data['tr_heads'][0] = 'Select Transaction Head';
        foreach (TransactionHead::select('id', 'tr_head')->get() as $tr_head) {
            $data['tr_heads'][$tr_head->id] = $tr_head->tr_head;
        }*/

        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;

        return view(parent::loadDataToView($this->view_path.'.add'), compact('data'));
    }
   

    public function store(Request $request)
    {
        if ($request->has('tr_head')) {
            foreach ($request->get('tr_head') as $key => $tr_head) {
                $transaction_head = TransactionHead::where('id','=', $request->get('tr_head')[$key])->first();

                if($transaction_head->type == 'income'){
                    $dr_amount = $request->get('amount')[$key];
                    $cr_amount = null;
                }else{
                    $cr_amount = $request->get('amount')[$key];
                    $dr_amount = null;
                }

                Transaction::create([
                    'date' => $request->get('tr_date'),
                    'tr_head_id' =>  $request->get('tr_head')[$key],
                    'dr_amount' => $dr_amount,
                    'cr_amount' => $cr_amount,
                    'description' => $request->get('description')[$key],
                    'session_id'=>Session::get('activeSession'), 
                    'branch_id'=>Session::get('activeBranch'), 
                    'created_by' => auth()->user()->id,
                ]);
            }
        }else {
            $request->session()->flash($this->message_warning, 'You have no any transaction add in this time. Please, Do At Lease One Transaction.');
            return redirect()->route($this->base_route);
        }

        $request->session()->flash($this->message_success, $this->panel. ' Add Successfully.');
        return redirect()->route($this->base_route);

    }
    public function new_add(Request $request){
        $data['head']=TransactionHead::select('id','tr_head as title')->where([
            ['status','=',1]
            // ['branch_id','=',Session::get('activeBranch')],
            // ['session_id','=',Session::get('activeSession')]
        ])->pluck('title','id')->toArray();
        $data['head']=array_prepend($data['head'],'--Select Head--','');
        $data['pay_mode']=DB::table('payment_type')->select('id','type_name as title')->where('status',1)->pluck('title','title')->toArray();
        $data['pay_mode']=array_prepend($data['pay_mode'],'--Select Mode--','');
        $data['branches']=DB::table('branches')->select('branch_name','id')->where('record_status',1)->pluck('branch_name','id')->toArray();
        $data['branches']=array_prepend($data['branches'],'--Select Branch--','');
        return view(parent::loadDataToView($this->view_path.'.new_add'),compact('data'));
    }
    public function new_store(Request $request){
        $rule=[
            'tr_head_id'=>'required',
            'type'=>'required',
            'date'=>'required',
            'amount'=>'required',
            'pay_mode'=>'required',
            'branch_id'=>'required'
        ];
        $msg=[
            'tr_head_id.required'=>"Please select Head",
            'type.required'=>"Please select Type",
            'type.date'=>"Please enter date",
            'amount.required'=>"Please enter amount",
            'pay_mode.required'=>"Please select Pay Mode",
            'branch_id.required'=>"Please select branch"
        ];
        $this->validate($request,$rule,$msg);
        if($request->all()){
            // $request->request->add(['branch_id'=>Session::get('activeBranch')]);
            $request->request->add(['session_id'=>Session::get('activeSession')]);
            $request->request->add(['created_by'=>auth()->user()->id]);
            $request->request->add(['created_at'=>Carbon::now()]);
            Transaction::create($request->all());
            return redirect()->route($this->base_route)->with('message_success','Transaction Add Successfully');
        }else{
            return redirect()->back()->with('message_warning','Something went wrong');
        }
    }
    public function new_edit(Request $request,$id){
        $data['row']=Transaction::find($id);
        if(!$data['row']){
            parent::invalidRequest();
        }
        $data['head']=TransactionHead::select('id','tr_head as title')->where([
            ['status','=',1]
            // ['branch_id','=',Session::get('activeBranch')],
            // ['session_id','=',Session::get('activeSession')]
        ])->pluck('title','id')->toArray();
        $data['head']=array_prepend($data['head'],'--Select Head--','');
        $data['pay_mode']=DB::table('payment_type')->select('id','type_name as title')->where('status',1)->pluck('title','title')->toArray();
        $data['pay_mode']=array_prepend($data['pay_mode'],'--Select Mode--','');
        $data['branches']=DB::table('branches')->select('branch_name','id')->where('record_status',1)->pluck('branch_name','id')->toArray();
        $data['branches']=array_prepend($data['branches'],'--Select Branch--','');
        if($request->all()){
            $rule=[
                'tr_head_id'=>'required',
                'type'=>'required',
                'date'=>'required',
                'amount'=>'required',
                'pay_mode'=>'required',
                'branch_id'=>'required'
            ];
            $msg=[
                'tr_head_id.required'=>"Please select Head",
                'type.required'=>"Please select Type",
                'type.date'=>"Please enter date",
                'amount.required'=>"Please enter amount",
                'pay_mode.required'=>"Please select Pay Mode",
                'branch_id.required'=>"Please select branch"
            ];
            $this->validate($request,$rule,$msg);
            $request->request->add(['updated_at'=>Carbon::now()]);
            $request->request->add(['last_updated_by'=>auth()->user()->id]);
            $data['row']->update($request->all());
            return redirect()->route($this->base_route)->with('message_success','Transaction updated successfully');

        }
        return view(parent::loadDataToView($this->view_path.'.new_add'),compact('data'));
    }
    public function new_delete(Request $request,$id){
      $data['row']=Transaction::find($id);
        if(!$data['row']){
            parent::invalidRequest();
        }
        $request->request->add(['status'=>0]);
        $aa=Transaction::where('id',$data['row']->id)->update([
            'status'=>0
        ]);  
        return redirect()->back()->with('message_success','Transaction Deleted');
    }
    public function edit(Request $request, $id)
    {

        $data = [];

        $data['row'] = Transaction::select('transactions.id', 'transactions.date', 'transactions.tr_head_id', 'th.tr_head as transaction_title', 'transactions.branch_id', 
            'transactions.dr_amount','transactions.cr_amount', 'transactions.description','transactions.status')
            ->where('transactions.id','=',$id)
            ->join('transaction_heads as th','th.id','=','transactions.tr_head_id')
            ->first();
        $branches=Branch::get()->pluck('branch_name', 'id');
        if (!$data['row'])
            return parent::invalidRequest();

        //$data['tr_heads'] = TransactionHead::select('id', 'tr_head')->where()->first();
        /*foreach (TransactionHead::select('id', 'tr_head')->get() as $tr_head) {
            $data['tr_heads'][$tr_head->id] = $tr_head->tr_head;
        }*/

        $data['url'] = URL::current();
        $data['base_route'] = $this->base_route;
        return view(parent::loadDataToView($this->view_path.'.add'), compact('data', 'branches'));
    }

    
    public function update(Request $request, $id){
        if (!$row = Transaction::find($id)) return parent::invalidRequest();

        $row->update([
            'date' => $request->get('date'),
            'dr_amount' => $request->get('dr_amount'),
            'cr_amount' => $request->get('cr_amount'),
            'description' => $request->get('description'),
            'branch_id' => Session::get('activeBranch'), 
            'session_id' => Session::get('activeSession'), 
            'last_updated_by' => auth()->user()->id,
        ]);
        $request->session()->flash($this->message_success, $this->panel.' Updated Successfully.');
        return redirect()->route($this->base_route);
    }

    public function delete(Request $request, $id)
    {
        if (!$row = Transaction::find($id)) return parent::invalidRequest();

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
                            $row = Transaction::find($row_id);
                            if ($row) {
                                $row->status = $request->get('bulk_action') == 'active'?'active':'in-active';
                                $row->save();
                            }
                            break;
                        case 'delete':
                            $row = Transaction::find($row_id);
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

    public function trHtmlRow()
    {
        $tr_heads = [];
        $tr_heads[0] = 'Select Transaction Head';
        foreach (TransactionHead::select('id', 'tr_head')->orderBy('tr_head')->get() as $tr_head) {
            $tr_heads[$tr_head->id] = $tr_head->tr_head;
        }

        $response['html'] = view($this->view_path.'.includes.transaction_tr', [
            'tr_heads' => $tr_heads,
        ])->render();
        return response()->json(json_encode($response));

    }


    public function printed($id){
        //echo"inside"; exit();
        $data = [];
        $transaction = Transaction::select('transactions.id', 'transactions.date', 'transactions.tr_head_id', 'transactions.dr_amount','transactions.cr_amount', 'transactions.description','transactions.status', 'branches.branch_name', 'branches.branch_logo','branches.branch_mobile','branches.branch_email','branches.branch_address', 'users.name', 'transaction_heads.tr_head','transactions.type','transactions.amount','transactions.pay_mode','transactions.reference','transactions.note')
            ->leftJoin('branches', function($join){
                $join->on('transactions.branch_id', '=', 'branches.id');
            })->leftJoin('users', function($join){
                $join->on('transactions.created_by', '=', 'users.id');
            })->leftJoin('transaction_heads', function($join){
                $join->on('transactions.tr_head_id', '=', 'transaction_heads.id');
            })->where('transactions.id', '=', $id)->get()->toArray();


        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;
        //print_r($data);
        //die($this->view_path.'.printed');
        return view($this->view_path.'.printed', compact('data', 'transaction'));
    }
    public function Update_tr(){
        $data=DB::table('transactions')->select('dr_amount','cr_amount','id')->where([['dr_amount','>',0]])
        ->orWhere([['cr_amount','>',0]])
        ->get();
       
        foreach ($data as $key => $value) {
            if(!empty($value->dr_amount)){
                DB::table('transactions')->where('id',$value->id)->update([
                    'type'=>'Debit',
                    'amount'=>$value->dr_amount
                ]);
            }
            if(!empty($value->cr_amount)){
                DB::table('transactions')->where('id',$value->id)->update([
                    'type'=>'Credit',
                    'amount'=>$value->cr_amount
                ]);
            }
        }
       dd('done');
    }

}
