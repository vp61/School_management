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
use App\Models\VehicleDailyEntry;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VehicleDailyEntryController extends CollegeBaseController
{
    protected $base_route = 'transport.DailyEntry';
    protected $view_path = 'transport.DailyEntry';
    protected $panel = 'Vehicle Daily Entry';
    protected $filter_query = [];

    public function __construct()
    {

    }

    public function index(Request $request)
    {
       
        $data = [];
        $data['vehicle']= Vehicle::select('id','number')->where('status',1)->pluck('number','id')->toarray();
        $data['vehicle']= array_prepend($data['vehicle'],'--select vehicle--','0');

        $data['dailyentry'] = VehicleDailyEntry::select('transport_daily_entry.*','vehicles.number')
        ->leftjoin('vehicles','vehicles.id','=','transport_daily_entry.vehicle_id')
         ->where('transport_daily_entry.branch_id',Session::get('activeBranch'))
         ->where('transport_daily_entry.session_id',Session::get('activeSession'))
         ->where('transport_daily_entry.record_status',1)
         ->get();
        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    public function store(Request $request)
    {
       //dd($request->all());
           $rules=[
            'vehicle_id'=>'required',
            'date'=>'required',  
        ];

      
        
        $msg=[
            'vehicle_id.required'=>'Please select  one vehicle',
            'date.required'=>'Please select date',    
        ];
          $this->validate($request,$rules,$msg);
          $request->request->remove('_token');
          $request->request->add(['created_by' => auth()->user()->id]);
          $request->request->add(['created_at' => Carbon::now()]);
          $request->request->add(['branch_id' =>  session::get('activeBranch')]);
          $request->request->add(['session_id' => session::get('activeSession')]);

          if($request->distance!=null||($request->fuel!=null && $request->receipt_no!=null && $request->fuel_amount!=null)){
            // dd($request->all());
               $maintenance =  VehicleDailyEntry::create($request->all());
               $request->session()->flash($this->message_success, $this->panel. ' Added Successfully.');
               $request->session()->flash($this->message_danger, $this->panel.'something wrong!!!.');
               return redirect()->route($this->base_route);
          }
          
            $request->session()->flash($this->message_danger, 'something wrong.');
             return redirect()->route($this->base_route);
         
         
          
    }

    public function edit(Request $request, $id)
    {
        $data = [];
        if (!$data['row'] = VehicleDailyEntry::find($id))
            return parent::invalidRequest();

       

         $data['vehicle']= Vehicle::select('id','number')->where('status',1)->pluck('number','id')->toarray();
         $data['dailyentry'] = VehicleDailyEntry::select('transport_daily_entry.*','vehicles.number')
        ->leftjoin('vehicles','vehicles.id','=','transport_daily_entry.vehicle_id')
         ->where('transport_daily_entry.branch_id',Session::get('activeBranch'))
         ->where('transport_daily_entry.session_id',Session::get('activeSession'))
         ->where('transport_daily_entry.record_status',1)
         ->get();

         if($request->all()){
            
            $request->request->add(['updated_by' => auth()->user()->id]);
              $request->request->add(['updated_at' => Carbon::now()]);
            if($request->distance!=null||($request->fuel!=null && $request->receipt_no!=null && $request->fuel_amount!=null)){
            // dd($request->all());
               $data['row']->update($request->all());
               $request->session()->flash($this->message_success, $this->panel.' Updated Successfully.');
    
               return redirect()->route($this->base_route);
            }
          
            $request->session()->flash($this->message_danger, 'Data Not Updated. Validation error.');
             return redirect()->route($this->base_route);
        

         }
        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

   

    public function delete(Request $request, $id)
    {
        if (!$row = VehicleDailyEntry::find($id)) return parent::invalidRequest();

          $row->update(['record_status'=>0,'updated_at'=>carbon::now(),'updated_by' => auth()->user()->id]);

        $request->session()->flash($this->message_success, $this->panel.' Deleted Successfully.');
        return redirect()->route($this->base_route);
    }

   
}