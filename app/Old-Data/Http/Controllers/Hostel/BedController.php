<?php
/**
 * Created by PhpStorm.
 * User: Umesh Kumar Yadav
 * Date: 03/03/2018
 * Time: 7:05 PM
 */
namespace App\Http\Controllers\Hostel;

use App\Http\Controllers\CollegeBaseController;
use App\Models\Bed;
use DB;
use App\Models\Room;
use Illuminate\Http\Request;
use App\Models\Hostel;


class BedController extends CollegeBaseController
{
    protected $base_route = 'hostel.bed';
    protected $view_path = 'hostel.bed';
    protected $panel = 'Bed';
    protected $filter_query = [];

    public function __construct()
    {

    }
    public function index($hostel_id){
        $id=$hostel_id;
        $data['hostels']=DB::table('hostels')->select('name','id')->where('id',$id)->first();
        $data['blocks']=DB::table('hostel_blocks')->select('id','title')
        ->where('hostel_id',$id)
        ->pluck('title','id')
        ->toArray();
        $data['blocks']=array_prepend($data['blocks'],'--Select Block--',"");
         $data['bed']=DB::table('beds')->select('beds.id','bed_number','rate','bed_statuses.title as status','hostel_blocks.title as block','hostel_floors.title as floor','room_number as room')
         ->leftjoin('rooms','rooms.id','=','beds.rooms_id')
         ->leftjoin('hostel_blocks','hostel_blocks.id','=','rooms.block_id')
         ->leftjoin('hostel_floors','hostel_floors.id','=','rooms.floor_id')
         ->leftjoin('bed_statuses','bed_statuses.id','=','beds.bed_status')
         ->where('beds.hostels_id',$id)
         ->get();
         
        return view(parent::loadDataToView($this->view_path.'.index'),compact('data','id'));
    }

    public function addBeds(Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');
        $hostelId = $request->get('hostelId');
        $roomId = $request->get('room');
        if($start == 0 or $end == 0){
            $request->session()->flash($this->message_warning, $this->panel. ' Attention!, Please Enter Start Value Greater Than 0');
            return back();
        }

        if($start > $end){
            $request->session()->flash($this->message_warning, $this->panel. ' Attention!, Yo have enter End Value is Less than Start. Correct It.');
            return back();
        }

        if($start == $end){
            $rooms = 1;
        }else{
            $rooms = ($end - $start) + 1;
        }

        if ($rooms > 0) {
            $i = 1;
            while ($i <= $rooms){
                $row = Bed::where([['hostels_id','=',$hostelId],['rooms_id','=',$roomId],['bed_number','=',$start]])->first();
                if($row){
                    $request->session()->flash($this->message_warning, 'Bed Number '.$row['bed_number'].' Already Exist Please Correct and Try Again.');
                    return back();
                }else{
                    Bed::create([
                        'hostels_id' => $hostelId,
                        'rooms_id' => $roomId,
                        'bed_number' => $start,
                        'created_by' => auth()->user()->id,
                        'rate'       =>$request->rate   
                    ]);
                }
                $start++;
                $i++;
            }
        }
        $request->session()->flash($this->message_success, $this->panel.' Add Successfully.');
        return back();
    }
     public function update(Request $request,$hostelId,$bedId)
    {
        $id=$hostelId;
         $data['hostel']=Hostel::FindOrFail($id);

        $data['bed']=DB::table('beds')->select('beds.id','bed_number','rate','bed_statuses.title as status','hostel_blocks.title as block','hostel_floors.title as floor','room_number as room')
         ->leftjoin('rooms','rooms.id','=','beds.rooms_id')
         ->leftjoin('hostel_blocks','hostel_blocks.id','=','rooms.block_id')
         ->leftjoin('hostel_floors','hostel_floors.id','=','rooms.floor_id')
         ->leftjoin('bed_statuses','bed_statuses.id','=','beds.bed_status')
         ->where('beds.hostels_id',$id)
         ->get();
        $data['block']=DB::table('hostel_blocks')->select('id','title')
        ->where('hostel_id',$id)
        ->pluck('title','id')
        ->toArray();
        $data['block']=array_prepend($data['block'],'--Select Block--',"");
        if(isset($request->block) && isset($request->floor) && isset($request->room) && isset($request->start)){
           
                $hostelId = $request->get('hostelId');
                $bed_number = $request->get('start');
                $room = $request->get('room');
                $bed = $request->get('start');
                $rate=$request->rate;
                if (!$row = Bed::where(['id'=>$bedId])->first()) return parent::invalidRequest();

                $request->request->add(['last_updated_by' => auth()->user()->id]);

                $row->update([
                    'hostels_id' => $hostelId,
                    'rooms_id' => $room,
                    'bed_number' => $bed_number,
                    'rate'      =>  $rate,
                    'created_by' => auth()->user()->id,
                ]);

                $request->session()->flash($this->message_success, $this->panel.' Updated Successfully.');
                return redirect(route('hostel.bed',$id));
            }
            else{                
                $data['row']=DB::table('beds')->select('beds.id','bed_number','rate','bed_statuses.title as status','hostel_blocks.id as block','hostel_floors.id as floor','rooms.id as room')
                 ->leftjoin('rooms','rooms.id','=','beds.rooms_id')
                 ->leftjoin('hostel_blocks','hostel_blocks.id','=','rooms.block_id')
                 ->leftjoin('hostel_floors','hostel_floors.id','=','rooms.floor_id')
                 ->leftjoin('bed_statuses','bed_statuses.id','=','beds.bed_status')
                 ->where('beds.id',$bedId)
                 ->first();
               return view(parent::loadDataToView($this->view_path.'.index'),compact('id','data','bedId'));
            }
    }        
    public function delete(Request $request, $id,$bedId)
    {
        if (!$row = Bed::find($bedId)) return parent::invalidRequest();

        $row->delete();

        $request->session()->flash($this->message_success, $this->panel.' Deleted Successfully.');
        return redirect()->back();
    }   

    public function Active(request $request, $id)
    {
        if (!$row = Bed::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $this->panel.'  Active Successfully.');
        return redirect()->back();
    }

    public function InActive(request $request, $id)
    {
        if (!$row = Bed::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'in-active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success,$this->panel.' In-Active Successfully.');
        return redirect()->back();
    }


    public function bulkAction(Request $request)
    {

        if ($request->has('bulk_action') && in_array($request->get('bulk_action'), ['active', 'in-active', 'delete'])) {

            if ($request->has('chkIds')) {
                foreach ($request->get('chkIds') as $row_id) {
                    switch ($request->get('bulk_action')) {
                        case 'active':
                        case 'in-active':
                            $row = Bed::find($row_id);
                            if ($row) {
                                $row->status = $request->get('bulk_action') == 'active'?'active':'in-active';
                                $row->save();
                            }
                            break;
                        case 'delete':
                            $row = Bed::find($row_id);
                            $row->delete();
                            break;
                    }
                }

                if ($request->get('bulk_action') == 'active' || $request->get('bulk_action') == 'in-active')
                    $request->session()->flash($this->message_success, $request->get('bulk_action'). ' Action Successfully.');
                else
                    $request->session()->flash($this->message_success, $this->panel . ' Deleted successfully.');

                return redirect()->back();

            } else {
                $request->session()->flash($this->message_warning, 'Please, Check at least one row.');
                return redirect()->back();
            }

        } else return parent::invalidRequest();




    }

    public function bedStatus(request $request, $id, $status)
    {
        if (!$row = Bed::find($id)) return parent::invalidRequest();

        $request->request->add(['bed_status' => $status]);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $this->panel.' Status Change Successfully.');
        return back();
    }
    public function findBeds(Request $request)
    {
        $response = [];
        $response['error'] = true;

        if ($request->has('room_id')) {
            $beds = Bed::select('id','bed_number')
                ->where('rooms_id','=', $request->get('room_id'))
                ->get();

            if ($beds) {
                $response['beds'] = $beds;
                $response['error'] = false;
                $response['success'] = 'Rooms Available For This Hostel.';
            } else {
                $response['error'] = 'No Any Rooms Assign on This Hostel.';
            }

        }else{
            $response['message'] = 'Invalid request!!';
        }
        return response()->json(json_encode($response));
    }
    public function findRooms(Request $request)
    {
        $response = [];
        $response['error'] = true;

        if ($request->has('hostel_id')) {
            $hostels = Room::select('id','room_number')
                ->where('hostels_id','=', $request->get('hostel_id'))
                ->orderBy('room_number','ASC')
                ->get();

            if ($hostels) {
                $response['rooms'] = $hostels;
                $response['error'] = false;
                $response['success'] = 'Rooms Available For This Hostel.';
            } 
        }
            elseif ($request->has('block') && $request->has('floor')) {
                $hostels = Room::select('id','room_number')
                ->where([['block_id','=', $request->get('block')],['floor_id','=', $request->get('floor')]])
                ->get();

                if ($hostels) {
                    $response['rooms'] = $hostels;
                    $response['error'] = false;
                    $response['success'] = 'Rooms Available For This Hostel.';
                }
                else {
                $response['error'] = 'No Rooms Assign on This Hostel.';
                } 
        }  
        else {
            $response['message'] = 'Invalid request!!';
        }
        return response()->json(json_encode($response));
    }


}