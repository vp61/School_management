<?php

namespace App\Http\Controllers\Inventory;
use App\Http\Controllers\CollegeBaseController;
use Illuminate\Http\Request;
use App\Models\InventoryGst;
use Carbon\Carbon;

class GstController extends CollegeBaseController
{
	protected $base_route = 'inventory.gst';
    protected $view_path = 'inventory.gst';
    protected $panel = 'GST';
    protected $filter_query = [];

    public function __construct(){

    }
    public function index(){
        $data['gst']=InventoryGst::where('record_status',1)->select('*')->get();
        return view(parent::loadDataToView($this->view_path.'.index'),compact('data'));
    }

    public function store(Request $request)
    {
        $msg=[
            'title.required'=>"Please Enter Title",
            'value.required'=>"Pleaste Enter Value"
        ];
        $rules=[
            'title'=>'required',
            'value'=>'required | numeric'
        ];
        $this->validate($request,$rules,$msg);
        $request->request->add(['created_at'=>Carbon::now()]);
        $request->request->add(['created_by'=>auth()->user()->id]);
        $request->request->add(['record_status'=>1]);
        InventoryGst::create($request->all()); 
         $request->session()->flash($this->message_success, $this->panel.' Added Successfully.');
        return back();
    }
    public function edit(Request $request,$id){
        $data['row']=InventoryGst::find($id);
        if(!$data['row']){
            parent::invalidRequest();
        }
       
        if($request->title && $request->value){
              $msg=[
            'title.required'=>"Please Enter Title",
            'value.required'=>"Pleaste Enter Value"
            ];
            $rules=[
                'title'=>'required',
                'value'=>'required'
            ];
            $this->validate($request,$rules,$msg);
            $request->request->add(['updated_at'=>Carbon::now()]);
            $request->request->add(['updated_by'=>auth()->user()->id]);
            $data['row']->update($request->all());
            return redirect()->route($this->base_route)->with('message_success', $this->panel.' Updated Successfully');
        }
         $data['gst']=InventoryGst::where('record_status',1)->select('*')->get();
        return view(parent::loadDataToView($this->view_path.'.index'),compact('data','id'));
    }          
    public function delete(Request $request, $id)
    {
       $data['row']=InventoryGst::find($id);
        if(!$data['row']){
            parent::invalidRequest();
        }
        $request->request->add(['record_status'=>0]);
            $data['row']->update($request->all());
        return redirect()->route($this->base_route)->with('message_success', $this->panel.' Deleted Successfully');    
    }
}
