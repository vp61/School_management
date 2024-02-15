<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\CollegeBaseController;
use App\Models\CareerStatus;
use Illuminate\Http\Request;
Use Carbon\Carbon;

class CareerStatusController extends CollegeBaseController
{
    protected $base_route = 'career-status';
    protected $view_path = 'academic.career-status';
    protected $panel = 'Career Status';
    protected $filter_query = [];

    public function __construct()
    {

    }

    public function index(Request $request)
    {

        $data = [];
        $data['career-status'] = CareerStatus::select('*')->get();
        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    public function store(Request $request)
    {
       $request->request->add(['created_by' => auth()->user()->id]);
       $request->request->add(['created_at' => Carbon::now()]);

       CareerStatus::create($request->all());

       $request->session()->flash($this->message_success, $this->panel. ' Created Successfully.');
       return redirect()->route($this->base_route);
    }

    public function edit(Request $request, $id)
    {
        $data = [];
        if (!$data['row'] = CareerStatus::find($id))
            return parent::invalidRequest();

        $data['career-status'] = CareerStatus::select('*')->get();
        if($request->all()){
         $request->request->add(['updated_by' => auth()->user()->id]);
          $request->request->add(['updated_at' => Carbon::now()]);
            $data['row']->update($request->all());

            $request->session()->flash($this->message_success, $this->panel.' Updated Successfully.');
            return redirect()->route($this->base_route);

        }

        $data['base_route'] = $this->base_route;
        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    

    public function delete(Request $request, $id)
    {
        if (!$row = CareerStatus::find($id)) return parent::invalidRequest();
        $row->delete();

        $request->session()->flash($this->message_success, $this->panel.' Deleted Successfully.');
        return redirect()->route($this->base_route);
    }

   
}