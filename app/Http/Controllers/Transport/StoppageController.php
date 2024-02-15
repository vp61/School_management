<?php
namespace App\Http\Controllers\Transport;

use App\Http\Controllers\CollegeBaseController;
use App\Http\Requests\Transport\Route\AddValidation;
use App\Http\Requests\Transport\Route\EditValidation;
use App\Models\Route;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use App\Models\Stoppage;
use Carbon\Carbon;
class StoppageController extends CollegeBaseController
{
    protected $base_route = 'transport.stoppage';
    protected $view_path = 'transport.stoppage';
    protected $panel = 'Stoppage';
    protected $filter_query = [];

    public function __construct()
    {

    }

    public function index(Request $request)
    {
        $data = [];
        $data['route'] = Route::select('id','title')->where('status',1)->pluck('title','id')->toArray();
        $data['route']=array_prepend($data['route'],'--Select Route--','');
        $data['stoppage']=Stoppage::select('routes.title as route','stoppages.*')->leftJoin('routes','routes.id','=','stoppages.route_id')
        ->where('stoppages.record_status',1)->get();
        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    public function store(Request $request)
    {
        $msg=[
            'route_id.required'=>"Please select route",
            'title.required'=>"Please enter stoppage name",
            'distance.required'=>"Please enter distance",
            'fee_amount'=>"Please enter fee amount"
        ];
        $rules=[
            'route_id'=>'required',
            'title'=>'required',
            'distance'=>'required',
            'fee_amount'=>'required'
        ];
        $this->validate($request,$rules,$msg);
        $request->request->add(['created_by' => auth()->user()->id]);
        $request->request->add(['created_at' => Carbon::now()]);
        $request->request->add(['active_status' => 1]);
        $request->request->add(['record_status' => 1]);
        $route =  Stoppage::create($request->all());

        $request->session()->flash($this->message_success, $this->panel. ' Created Successfully.');
        return redirect()->route($this->base_route);
    }

    public function edit(Request $request, $id)
    {
        $data = [];
        if (!$data['row'] = Stoppage::find($id))
            return parent::invalidRequest();
        if($request->all()){
            $msg=[
            'route_id.required'=>"Please select route",
            'title.required'=>"Please enter stoppage name",
            'distance.required'=>"Please enter distance",
            'fee_amount'=>"Please enter fee amount"
        ];
        $rules=[
            'route_id'=>'required',
            'title'=>'required',
            'distance'=>'required',
            'fee_amount'=>'required'
        ];
        $this->validate($request,$rules,$msg);
        $request->request->add(['updated_by' => auth()->user()->id]);
        $request->request->add(['updated_at' => auth()->user()->id]);
        $data['row']->update($request->all());

        $request->session()->flash($this->message_success, $this->panel.' Updated Successfully.');
        return redirect()->route($this->base_route); 
        }

       $data['route'] = Route::select('id','title')->where('status',1)->pluck('title','id')->toArray();
        $data['route']=array_prepend($data['route'],'--Select Route--','');
        $data['stoppage']=Stoppage::select('routes.title as route','stoppages.*')->leftJoin('routes','routes.id','=','stoppages.route_id')
        ->where('stoppages.record_status',1)->get();

        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    
    public function delete(Request $request, $id)
    {
        if (!$row = Stoppage::find($id)) return parent::invalidRequest();
        $row->update([
            'record_status'=>0
        ]);    
        $request->session()->flash($this->message_success, $this->panel.' Deleted Successfully.');
        return redirect()->route($this->base_route);
    }
    public function changeStatus(Request $request,$id,$status){
         if (!$row = Stoppage::find($id)) return parent::invalidRequest();
         $row->update([
            'active_status'=>$status
         ]);
         $request->session()->flash($this->message_success, $this->panel.'  Status Changed Successfully.');
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
                            $row = Route::find($row_id);
                            if ($row) {
                                $row->status = $request->get('bulk_action') == 'active'?'active':'in-active';
                                $row->save();
                            }
                            break;
                        case 'delete':
                            $row = Route::find($row_id);
                            $row->vehicle()->detach();
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
        if (!$row = Route::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $row->semester.' '.$this->panel.' Active Successfully.');
        return redirect()->route($this->base_route);
    }

    public function inActive(request $request, $id)
    {
        if (!$row = Route::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'in-active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $row->semester.' '.$this->panel.' In-Active Successfully.');
        return redirect()->route($this->base_route);
    }

    public function vehicleHtmlRow(Request $request)
    {
        $response = [];
        $response['error'] = true;

        if ($request->has('id')) {
            $vehicle = Vehicle::select('id','number', 'type', 'model')->find($request->get('id'));
            if ($vehicle) {
                $response['error'] = false;
                $response['html'] = view($this->view_path.'.includes.vehicle_tr', [ 'vehicle' => $vehicle ])->render();
                $response['message'] = 'Operation successful.';

            } else{
                $response['message'] = 'Invalid request!!';
            }
        } else{
            $response['message'] = 'Invalid request!!';
        }

        return response()->json(json_encode($response));
    }

    public function vehicleAutocomplete(Request $request)
    {
        if ($request->has('q')) {
            $param = $request->get('q');

            $vehicles = Vehicle::select('id','number', 'type', 'model')
                ->where(function ($query) use($param){
                    $query->where('number', 'like', '%'.$param.'%')
                        ->orwhere('type', 'like', '%'.$param.'%')
                        ->orwhere('model', 'like', '%'.$param.'%');
                })
                ->get();

            $response = [];
            foreach ($vehicles as $vehicle) {
                $response[] = ['id' => $vehicle->id, 'text' => $vehicle->number.' | '.$vehicle->model .' | '.$vehicle->type];
            }

            return json_encode($response);
        }

        abort(501);
    }
}