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
use App\Models\BedStatus;
use App\Models\Room;
use App\Models\Hostel;
use Illuminate\Http\Request;
use DB;

class RoomController extends CollegeBaseController
{
    protected $base_route = 'hostel';
    protected $view_path = 'hostel.room';
    protected $panel = 'Room';
    protected $filter_query = [];

    public function __construct()
    {

    }
    public function index(Request $request,$hostel_id){
        $id=$hostel_id;
        $data['hostel']=Hostel::FindOrFail($id);
        $data['block']=DB::table('hostel_blocks')->select('id','title')->where('hostel_id',$id)->pluck('title','id')->toArray();
        $data['block']=array_prepend($data['block'],"--Select Block--","");
        $data['room_type']=DB::table('room_types')->select('id','title')->where('status',1)->pluck('title','id')->toArray();
         $data['room_type']=array_prepend($data['room_type'],"--Select Type--","");
         $data['room']=DB::table('rooms')->select('rooms.id','room_number','room_types.title as type','hostel_blocks.title as block','hostel_floors.title as floor')
         ->leftjoin('room_types','room_types.id','=','rooms.room_type')
         ->leftjoin('hostel_blocks','hostel_blocks.id','=','rooms.block_id')
         ->leftjoin('hostel_floors','hostel_floors.id','=','rooms.floor_id')
         ->where('rooms.hostels_id',$id)
         ->get();
        return view(parent::loadDataToView($this->view_path.'.index'),compact('id','data'));
    }

    public function add(Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');
        if(!empty($roomId)){
                $success= DB::table('rooms')->where('id',$roomId)->delete();
                return redirect()->back()->with('message_warning','Selected Room Deleted');
        }
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
                $row = Room::where([['room_number','=',$start],['hostels_id','=',$request->get('hostelId')],['block_id','=',$request->block]])->first();

                if($row){
                    $request->session()->flash($this->message_warning,' Room No '.$row['room_number'].' Already Exists, Please Enter Start Room No Which Do Not Exists');
                     return back();
                }else{
                    Room::create([
                        'hostels_id' => $request->get('hostelId'),
                        'room_number' => $start,
                        'room_type' => $request->get('room_type'),
                        'created_by' => auth()->user()->id,
                        'block_id'   => $request->block,
                        'floor_id'   =>$request->floor      
                    ]);
                     $request->session()->flash($this->message_success,'Room Added Successfully.');
                }
                $start++;
                $i++;
            }
        }
        return back();

    }

    public function bulkAction(Request $request)
    {

        if ($request->has('bulk_action') && in_array($request->get('bulk_action'), ['active', 'in-active', 'delete'])) {

            if ($request->has('chkIds')) {
                foreach ($request->get('chkIds') as $row_id) {
                    switch ($request->get('bulk_action')) {
                        case 'active':
                        case 'in-active':
                            $row = Room::find($row_id);
                            if ($row) {
                                $row->status = $request->get('bulk_action') == 'active'?'active':'in-active';
                                $row->save();
                            }
                            break;
                        case 'delete':
                            $row = Room::find($row_id);
                            $row->delete();
                            break;
                    }
                }

                if ($request->get('bulk_action') == 'active' || $request->get('bulk_action') == 'in-active')
                    $request->session()->flash($this->message_success, $request->get('bulk_action'). ' Action Successfully.');
                else
                    $request->session()->flash($this->message_success, 'Deleted successfully.');

                return redirect()->back();

            } else {
                $request->session()->flash($this->message_warning, 'Please, Check at least one row.');
                return redirect()->back();
            }

        } else return parent::invalidRequest();
    }
    public function update(Request $request,$hostelId,$roomId)
    {
        $id=$hostelId;
         $data['hostel']=Hostel::FindOrFail($id);

         $data['room_type']=DB::table('room_types')->select('id','title')->where('status',1)->pluck('title','id')->toArray();
         $data['room_type']=array_prepend($data['room_type'],"--Select Type--","");
         $data['room']=DB::table('rooms')->select('rooms.id','room_number','room_types.title as type','hostel_blocks.title as block','hostel_floors.title as floor')
         ->leftjoin('room_types','room_types.id','=','rooms.room_type')
         ->leftjoin('hostel_blocks','hostel_blocks.id','=','rooms.block_id')
         ->leftjoin('hostel_floors','hostel_floors.id','=','rooms.floor_id')
         ->where('rooms.hostels_id',$id)
         ->get();
        if(isset($request->block) && isset($request->floor) && isset($request->room_type) && isset($request->start)){
           
                $hostelId = $request->get('hostelId');
                $room_number = $request->get('start');
                $room_type = $request->get('room_type');
                $floorId=$request->floor;
                $blockId=$request->block;
                if (!$row = Room::where(['id'=>$roomId])->first()) return parent::invalidRequest();

                $request->request->add(['last_updated_by' => auth()->user()->id]);

                $row->update([
                    'hostels_id' => $hostelId,
                    'room_number' => $room_number,
                    'room_type' => $room_type,
                    'block_id'  => $blockId,
                    'floor_id'   => $floorId,
                    'created_by' => auth()->user()->id,
                ]);

                $request->session()->flash($this->message_success, $this->panel.' Updated Successfully.');
                return redirect(route('hostel.room.add',$id));
            }
            else{
                $data['block']=DB::table('hostel_blocks')->select('id','title')->where('hostel_id',$id)->pluck('title','id')->toArray();
                $data['block']=array_prepend($data['block'],"--Select Block--","");
                $data['floor']=DB::table('rooms')->select('hostel_floors.id as fid','hostel_floors.title as ftitle')->where('rooms.id',$roomId)->leftjoin('hostel_floors','hostel_floors.id','=','rooms.floor_id')
                ->pluck('ftitle','fid')->toArray();
                $data['block']=array_prepend($data['block'],"--Select Block--","");
                $data['row'] = Room::where(['id'=>$roomId])->first();
               return view(parent::loadDataToView($this->view_path.'.index'),compact('id','data','roomId'));
            }
                
    }
    public function delete(Request $request, $id,$roomId)
    {
        if (!$row = Room::find($roomId)) return parent::invalidRequest();

        $row->delete();

        $request->session()->flash($this->message_success, ' Room Deleted Successfully.');
        return redirect()->back();
    }

    public function Active(request $request, $id)
    {
        if (!$row = Room::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, ' Room Active Successfully.');
        return redirect()->back();
    }

    public function InActive(request $request, $id)
    {
        if (!$row = Room::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'in-active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success,' Room In-Active Successfully.');
        return redirect()->back();
    }

    public function view(Request $request, $id)
    {
        $data = [];
        $data['rooms'] = Room::select('id', 'hostels_id','room_type','room_number', 'rate_perbed', 'description', 'status')
            ->where('id','=',$id)
            ->orderBy('room_number','asc')
            ->first();

        $data['beds'] = Bed::where('rooms_id','=',$data['rooms']->id )
            ->get();

        $data['beds_status'] = BedStatus::select('id', 'title', 'display_class')->get();

        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    public function loadFloor(Request $request){
        $response=[];
        $response['error']=true;
        if($request->has('block')){
            $floor=DB::table('hostel_floors')->select('id','title')
            ->where('block_id',$request->get('block'))
            ->get();
            if($floor){
                $response['floor']=$floor;
                $response['error']=false;
                $response['success']="Floor Found";
            }else{
                $response['error']='No floor found';
            }
        }else{
            $response['message']="Invalid Request";
        }
         return response()->json(json_encode($response));
    }

}