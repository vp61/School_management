<?php
/**
 * Created by PhpStorm.
 * User: Umesh Kumar Yadav
 * Date: 03/03/2018
 * Time: 7:05 PM
 */
namespace App\Http\Controllers\Transport;

use App\Http\Controllers\CollegeBaseController;
use App\Http\Requests\Transport\Vehicle\AddValidation;
use App\Http\Requests\Transport\Vehicle\EditValidation;
use Session;
use App\Models\Vehicle;
use App\Models\VehicleMaintenance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VehicleMaintenanceController extends CollegeBaseController
{
    protected $base_route = 'transport.maintenance';
    protected $view_path = 'transport.maintenance';
    protected $panel = 'Vehicle Maintenance';
    protected $filter_query = [];

    public function __construct()
    {

    }

    public function index(Request $request)
    {
       
        $data = [];
        $data['vehicle']= Vehicle::select('id','number')->where('status',1)->pluck('number','id')->toarray();
        $data['vehicle']= array_prepend($data['vehicle'],'--select vehicle--','0');

        $data['maintenance'] = VehicleMaintenance::select('vehicle_maintenance.*','vehicles.number')
        ->leftjoin('vehicles','vehicles.id','=','vehicle_maintenance.vehicle_id')
         ->where('vehicle_maintenance.branch_id',Session::get('activeBranch'))
         ->where('vehicle_maintenance.session_id',Session::get('activeSession'))
         ->where('vehicle_maintenance.record_status',1)
         ->get();
        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    public function store(Request $request)
    {
           $rules=[
            'vehicle_id'=>'required',
            'problem'=>'required',
            'maintenance_date'=>'required',   
            'work_performed'=>'required',
            'performed_by'=>'required',
            'maintenance_charge'=>'required|numeric',   
        ];

      
        
        $msg=[
            'vehicle_id.required'=>'Please select  on evehicle',
            'problem.required'=>'Please Vehicle problem',
            'maintenance_date.required'=>'Please Enter Date',   
            'work_performed.required'=>'Please Enter Work performed ',
            'performed_by.required'=>'Please Enter Performed By',
            'maintenance_charge.required'=>' Please Enter Cost ',
            
        ];
          $this->validate($request,$rules,$msg);
          $request->request->remove('_token');
          $request->request->add(['created_by' => auth()->user()->id]);
          $request->request->add(['created_at' => Carbon::now()]);
          $request->request->add(['branch_id' =>  session::get('activeBranch')]);
          $request->request->add(['session_id' => session::get('activeSession')]);
         //dd($request->all());
        $maintenance =  VehicleMaintenance::create($request->all());
        $request->session()->flash($this->message_success, $this->panel. ' Added Successfully.');
        return redirect()->route($this->base_route);
    }

    public function edit(Request $request, $id)
    {
        $data = [];
        if (!$data['row'] = VehicleMaintenance::find($id))
            return parent::invalidRequest();

       

         $data['vehicle']= Vehicle::select('id','number')->where('status',1)->pluck('number','id')->toarray();
         $data['maintenance'] = VehicleMaintenance::select('vehicle_maintenance.*','vehicles.number')
        ->leftjoin('vehicles','vehicles.id','=','vehicle_maintenance.vehicle_id')
         ->where('vehicle_maintenance.branch_id',Session::get('activeBranch'))
         ->where('vehicle_maintenance.session_id',Session::get('activeSession'))
         ->where('vehicle_maintenance.record_status',1)
         ->get();

         if($request->all()){
            
            $request->request->add(['updated_by' => auth()->user()->id]); 
            $request->request->add(['updated_at' => Carbon::now()]);

            $data['row']->update($request->all());
            $request->session()->flash($this->message_success, $this->panel.' Updated Successfully.');
             return redirect()->route($this->base_route);
         }
        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

   

    public function delete(Request $request, $id)
    {
        if (!$row = VehicleMaintenance::find($id)) return parent::invalidRequest();

          $row->update(['record_status'=>0,'updated_at'=>carbon::now(),'updated_by' => auth()->user()->id]);

        $request->session()->flash($this->message_success, $this->panel.' Deleted Successfully.');
        return redirect()->route($this->base_route);
    }

   
}