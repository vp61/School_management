<?php

namespace App\Http\Controllers\Setting;
use App\Http\Controllers\CollegeBaseController;

use App\Models\MenuPermission;
use Illuminate\Http\Request;
use App\Session_Model;
use Carbon\Carbon;

class MenuSettingController extends CollegeBaseController
{
    protected $base_route = 'setting.menu';
    protected $view_path = 'setting.menu';
    protected $panel = 'Menu Setting';
    protected $folder_path;
    protected $folder_name = 'menu';
    protected $filter_query = [];

    public function __construct()
    {
        $this->folder_path = public_path().DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'setting'.DIRECTORY_SEPARATOR.$this->folder_name.DIRECTORY_SEPARATOR;
    }

    public function index(Request $request)
    {
            $data['menu'] = [];
            $data['menu'] = MenuPermission::select('permissions.*','per.display_name as parent_name')
            ->leftjoin('permissions as per','per.id','=','permissions.parent_id')->get();

            $data['parent'] = MenuPermission::select('group','id')->groupBy('group')->pluck('group','group')->toArray();


            $data['parent'] = array_prepend($data['parent'],'--Select Parent--','');
            return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    public function store(Request $request)
    {
        $request->request->add(['created_at'=>Carbon::now()]);
        $request->request->add(['created_by'=>auth()->user()->id]);
        if($request->parent_group){
            $request->request->add(['group'=>$request->parent_group]);
        }
        MenuPermission::create($request->all()); 
        $request->session()->flash($this->message_success, $this->panel.' Added Successfully.');
        return redirect()->route('setting.menu');
    }
    public function edit(Request $request,$id){
        $data['row']=MenuPermission::find($id);
        if(!$data['row']){
            parent::invalidRequest();
        }
       $data['menu'] = MenuPermission::select('permissions.*','per.display_name as parent_name')
            ->leftjoin('permissions as per','per.id','=','permissions.parent_id')->get();
            $data['parent'] = MenuPermission::select('group','id')->groupBy('group')->pluck('group','group')->toArray();
            $data['parent'] = array_prepend($data['parent'],'--Select Parent--','');
        if($request->all()){
            $request->request->add(['updated_at'=>Carbon::now()]);
            $request->request->add(['updated_by'=>auth()->user()->id]);
            if($request->parent_group){
                $request->request->add(['group'=>$request->parent_group]);
            }
            $data['row']->update($request->all());
            return redirect()->route($this->base_route)->with('message_success', $this->panel.' Updated Successfully');
        }
        return view(parent::loadDataToView($this->view_path.'.index'),compact('data','id'));
    }

}