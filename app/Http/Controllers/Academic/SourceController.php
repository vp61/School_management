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


class SourceController extends CollegeBaseController
{
    protected $base_route = 'source';
    protected $view_path = 'academic.source';
    protected $panel = 'Source';
    protected $filter_query = [];

    public function __construct()
    {

    }
    public function index(){
        $data['source']=Source::where('record_status',1)->select('*')->get();
        return view(parent::loadDataToView($this->view_path.'.index'),compact('data'));
    }

    public function store(Request $request)
    {
        $msg=[
            'title.required'=>"Please Enter Title",
            'description.required'=>"Pleaste Enter Description"
        ];
        $rules=[
            'title'=>'required',
            'description'=>'required'
        ];
        $this->validate($request,$rules,$msg);
        $request->request->add(['created_at'=>Carbon::now()]);
        $request->request->add(['created_by'=>auth()->user()->id]);
        $request->request->add(['record_status'=>1]);
        Source::create($request->all()); 
         $request->session()->flash($this->message_success, $this->panel.' Added Successfully.');
        return back();
    }
    public function edit(Request $request,$id){
        $data['row']=Source::find($id);
        if(!$data['row']){
            parent::invalidRequest();
        }
       
        if($request->title && $request->description){
              $msg=[
            'title.required'=>"Please Enter Title",
            'description.required'=>"Pleaste Enter Description"
            ];
            $rules=[
                'title'=>'required',
                'description'=>'required'
            ];
            $this->validate($request,$rules,$msg);
            $request->request->add(['updated_at'=>Carbon::now()]);
            $request->request->add(['updated_by'=>auth()->user()->id]);
            $data['row']->update($request->all());
            return redirect()->route($this->base_route)->with('message_success', $this->panel.' Updated Successfully');
        }
         $data['source']=Source::where('record_status',1)->select('*')->get();
        return view(parent::loadDataToView($this->view_path.'.index'),compact('data','id'));
    }          
    public function delete(Request $request, $id)
    {
       $data['row']=Source::find($id);
        if(!$data['row']){
            parent::invalidRequest();
        }
        $request->request->add(['record_status'=>0]);
            $data['row']->update($request->all());
        return redirect()->route($this->base_route)->with('message_success', $this->panel.' Deleted Successfully');    
    }   

}