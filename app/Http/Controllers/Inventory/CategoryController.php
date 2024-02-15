<?php

namespace App\Http\Controllers\Inventory;
use App\Http\Controllers\CollegeBaseController;
use Illuminate\Http\Request;
use App\Models\InventoryCategory;
use Carbon\Carbon;

class CategoryController extends CollegeBaseController
{
	protected $base_route = 'inventory.category';
    protected $view_path = 'inventory.category';
    protected $panel = 'Category';
    protected $filter_query = [];

    public function __construct(){

    }
    public function index(){
        $data['parent']=InventoryCategory::select('id','title')->where([
            ['record_status','=',1],
            ['parent_id','=',0]
        ])->pluck('title','id')->toArray();
        $data['parent']=array_prepend($data['parent'],"--Select Parent--",'0');
        $data['category']=InventoryCategory::select('inventory_categories.*','parent.title as parent')
        ->leftjoin('inventory_categories as parent',function($query){
            $query->on('parent.id','=','inventory_categories.parent_id')
            ->where('parent.record_status',1);
           
        })
        ->where([
            ['inventory_categories.record_status','=',1],
           
        ])
        ->get();
        return view(parent::loadDataToView($this->view_path.'.index'),compact('data'));
    }

    public function store(Request $request)
    {
        $msg=[
            'title.required'=>"Please Enter Title"
        ];
        $rules=[
            'title'=>'required'
        ];
        $this->validate($request,$rules,$msg);
        $request->request->add(['created_at'=>Carbon::now()]);
        $request->request->add(['created_by'=>auth()->user()->id]);
        $request->request->add(['record_status'=>1]);
        InventoryCategory::create($request->all()); 
         $request->session()->flash($this->message_success, $this->panel.' Added Successfully.');
        return back();
    }
    public function edit(Request $request,$id){

        $data['row']=InventoryCategory::find($id);
        $data['parent']=InventoryCategory::select('id','title')->where([
            ['record_status','=',1],
            ['parent_id','=',0]
        ])->pluck('title','id')->toArray();
        $data['parent']=array_prepend($data['parent'],"--Select Parent--",'0');

        if(!$data['row']){
            parent::invalidRequest();
        }
       
        if($request->title){
              $msg=[
            'title.required'=>"Please Enter Title"
            ];
            $rules=[
                'title'=>'required'
            ];
            $this->validate($request,$rules,$msg);
            $request->request->add(['updated_at'=>Carbon::now()]);
            $request->request->add(['updated_by'=>auth()->user()->id]);
            $data['row']->update($request->all());
            return redirect()->route($this->base_route)->with('message_success', $this->panel.' Updated Successfully');
        }
         $data['category']=InventoryCategory::where('record_status',1)->select('*')->get();
        return view(parent::loadDataToView($this->view_path.'.index'),compact('data','id'));
    }          
    public function delete(Request $request, $id)
    {
       $data['row']=InventoryCategory::find($id);
        if(!$data['row']){
            parent::invalidRequest();
        }
        $request->request->add(['record_status'=>0]);
            $data['row']->update($request->all());
        return redirect()->route($this->base_route)->with('message_success', $this->panel.' Deleted Successfully');    
    }
}
