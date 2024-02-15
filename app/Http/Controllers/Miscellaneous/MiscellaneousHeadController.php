<?php

namespace App\Http\Controllers\Miscellaneous;

use App\Http\Controllers\CollegeBaseController;
use App\Models\Miscellaneous\MiscellaneousHead;
use Illuminate\Http\Request;
class MiscellaneousHeadController extends CollegeBaseController
{
    protected $base_route = 'miscellaneous.head';
    protected $view_path = 'miscellaneous.head';
    protected $panel = 'Miscellaneous Head';
    protected $filter_query = [];

    public function __construct()
    {

    }

    public function index(Request $request)
    {
        $data = [];
        $data['fees_head'] = MiscellaneousHead::select('id', 'fee_head_title', 'status')->orderBy('fee_head_title','asc')->get();
        $data['parent']    = MiscellaneousHead::select('id','fee_head_title as title')->where([
            ['status','=',1],
            ['parent_id','=',0]
        ])->pluck('title','id')->toArray();
        $data['parent']=array_prepend($data['parent'],"--Select Parent Head--",'');
        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    public function store(Request $request)
    {
        if($request->parent_id){
            $parent_head=MiscellaneousHead::findOrFail($request->parent_id);
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

        $faculty = MiscellaneousHead::create($request->all());

        $request->session()->flash($this->message_success, $this->panel. ' Created Successfully.');
        return redirect()->route($this->base_route);
    }

    public function edit(Request $request, $id)
    {
        $data = [];
        if (!$data['row'] = MiscellaneousHead::find($id))
            return parent::invalidRequest();

        $data['fees_head'] = MiscellaneousHead::select('id', 'fee_head_title', 'status')->orderBy('fee_head_title','asc')->get();
        $data['parent']=MiscellaneousHead::select('id','fee_head_title as title')->where([
            ['status','=',1],
            ['parent_id','=',0]
        ])->pluck('title','id')->toArray();
        $data['parent']=array_prepend($data['parent'],"--Select Parent Head--",'');
        $data['base_route'] = $this->base_route;
        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    public function update(Request $request, $id)
    {
        if (!$row = MiscellaneousHead::find($id)) return parent::invalidRequest();
        if($request->parent_id){
            $parent_head=MiscellaneousHead::findOrFail($request->parent_id);
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
        if (!$row = MiscellaneousHead::find($id)) return parent::invalidRequest();
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
                            $row = MiscellaneousHead::find($row_id);
                            if ($row) {
                                $row->status = $request->get('bulk_action') == '1'?'1':'0';
                                $row->save();
                            }
                            break;
                        case 'delete':
                            $row = MiscellaneousHead::find($row_id);
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
        if (!$row = MiscellaneousHead::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => '1']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $row->faculty.' '.$this->panel.' Active Successfully.');
        return redirect()->route($this->base_route);
    }

    public function inActive(request $request, $id)
    {
        if (!$row = MiscellaneousHead::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => '0']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $row->faculty.' '.$this->panel.' In-Active Successfully.');
        return redirect()->route($this->base_route);
    }
}
