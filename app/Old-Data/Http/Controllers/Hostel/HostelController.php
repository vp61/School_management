<?php
/**
 * Created by PhpStorm.
 * User: Umesh Kumar Yadav
 * Date: 03/03/2018
 * Time: 7:05 PM
 */
namespace App\Http\Controllers\Hostel;

use App\Http\Controllers\CollegeBaseController;
use App\Http\Requests\Hostel\Hostel\AddValidation;
use App\Http\Requests\Hostel\Hostel\EditValidation;
use App\Models\Bed;
use App\Models\Hostel;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use DB;
use Carbon\Carbon;
use Session;

class HostelController extends CollegeBaseController
{
    protected $base_route = 'hostel';
    protected $view_path = 'hostel.hostel';
    protected $panel = 'Hostel';
    protected $filter_query = [];

    public function __construct()
    {

    }

    public function index(Request $request)
    {
        $data = [];
        $data['hostel'] = Hostel::select('id', 'name', 'status')->where('branch_id','=',Session::get('activeBranch'))->get();

        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;
        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    public function add(Request $request)
    {
        $data = [];
        // $roomTypes = RoomType::select('id','title')->get();
        // $data['room_type'] = array_pluck($roomTypes,'title','id');

        return view(parent::loadDataToView($this->view_path.'.add'), compact('data'));
    }

    public function store(AddValidation $request)
    {
        $session=Session::get('activeBranch');
        $request->request->add(['created_by' => auth()->user()->id]);

        $row = Hostel::create([
            'created_by'        => auth()->user()->id,
            'name'              => $request->name,
            'type'              => $request->type,
            'address'           => $request->address,
            'contact_detail'    =>$request->contact_detail,
            'warden'            =>$request->warden,
            'warden_contact'    =>$request->warden_contact,
            'description'       =>$request->description,
            'status'            =>$request->status,
            'branch_id'            =>$session

        ]);

        // if ($row && $request->has('rooms')) {
        //     $i = 1;
        //     while ($i <= $request->get('rooms')){
        //         Room::create([
        //             'hostels_id' => $row->id,
        //             'room_number' => $i,
        //             'room_type' => $request->get('room_type'),
        //             'created_by' => auth()->user()->id,
        //         ]);
        //         $i++;
        //     }
        // }

        $request->session()->flash($this->message_success, $this->panel. ' Created Successfully.');
        return redirect()->route($this->base_route);
    }

    public function edit(Request $request, $id)
    {
        $data = [];
        if (!$data['row'] = Hostel::find($id))
            return parent::invalidRequest();

        $roomTypes = RoomType::select('id','title')->get();
        $data['room_type'] = array_pluck($roomTypes,'title','id');

        return view(parent::loadDataToView($this->view_path.'.edit'), compact('data'));
    }

    public function update(EditValidation $request, $id)
    {

        if (!$row = Hostel::find($id)) return parent::invalidRequest();

        $request->request->add(['last_updated_by' => auth()->user()->id]);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $this->panel.' Updated Successfully.');
        return redirect()->route($this->base_route);
    }

    public function delete(Request $request, $id)
    {
        if (!$row = Hostel::find($id)) return parent::invalidRequest();

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
                            $row = Hostel::find($row_id);
                            if ($row) {
                                $row->status = $request->get('bulk_action') == 'active'?'active':'in-active';
                                $row->save();
                            }
                            break;
                        case 'delete':
                            $row = Hostel::find($row_id);
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
        if (!$row = Hostel::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $row->semester.' '.$this->panel.' Active Successfully.');
        return redirect()->route($this->base_route);
    }

    public function inActive(request $request, $id)
    {
        if (!$row = Hostel::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'in-active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $row->semester.' '.$this->panel.' In-Active Successfully.');
        return redirect()->route($this->base_route);
    }

    public function view(Request $request, $id)
    {
        $data = [];
        $data['hostel'] = Hostel::select('id', 'name', 'type', 'address', 'contact_detail', 'warden',
            'warden_contact','description', 'status')
            ->where('id','=',$id)
            ->orderBy('name','asc')
            ->first();

        $data['rooms'] = Room::where('hostels_id','=',$data['hostel']->id )
            ->get();

        $roomTypes = RoomType::select('id','title')->get();
        $data['room_type'] = array_pluck($roomTypes,'title','id');

        return view(parent::loadDataToView($this->view_path.'.detail.index'), compact('data','id'));
    }
    public function add_block(Request $request,$hostel_id,$block_id="")
    {   
        $id=$hostel_id;
        $data['blocks']=DB::table('hostel_blocks')->select('title','hostel_blocks.id','hostels.name as hostel')
        ->where('hostel_id',$id)
        ->leftjoin('hostels','hostels.id','=','hostel_blocks.hostel_id')
        ->get();
       
        if(!empty($block_id)){
                $success= DB::table('hostel_blocks')->where('id',$block_id)->delete();
                return redirect()->back()->with('message_warning','Selected Block Deleted');
        }
        else{
            if(!empty($request->name)){
               $success= DB::table('hostel_blocks')->insert([
                    'created_by'        =>auth()->user()->id,
                    'hostel_id'         =>$request->hostel,
                    'title'             =>$request->name
                ]);
             if($success){
             
                 $request->session()->flash($this->message_success,"Block Added Successfully");
                 return redirect()->back();
             }
             else{
                $request->session()->flash($this->message_warning,"Something Went Wrong");
                 return redirect()->back();
             }
           }
        }
        
        $data['hostels']=DB::table('hostels')->select('name')->where('id',$id)->first();
        return view(parent::loadDataToView($this->view_path.'.detail.addBlock.index'), compact('data','id'));
    }
    public function edit_block(Request $request,$hostel_id,$block_id="")
    {   
        $id=$hostel_id;
        $bid=$block_id;
        $data['row']=DB::table('hostel_blocks')->select('title','hostel_blocks.id')
        ->where('id',$block_id)
        ->first();

            if(isset($request->name)){
               $success=DB::table('hostel_blocks')->where('id','=',$block_id)->update([
                    'title' => $request->name
                ]);
               $data['row']=null;
                $request->session()->flash($this->message_success,"Block Updated Successfully");
               return redirect()->route('hostel.block',$id);
           }
       
        $data['blocks']=DB::table('hostel_blocks')->select('title','hostel_blocks.id','hostels.name as hostel')
        ->where('hostel_id',$id)
        ->leftjoin('hostels','hostels.id','=','hostel_blocks.hostel_id')
        ->get();
        $data['hostels']=DB::table('hostels')->select('name')->where('id',$id)->first();
        return view(parent::loadDataToView($this->view_path.'.detail.addBlock.index'), compact('data','id','block_id'));
    }
    public function add_floor(Request $request,$hostel_id,$floor_id="")
    {   

        $id=$hostel_id;
       
       
        if(!empty($floor_id)){
                $success= DB::table('hostel_floors')->where('id',$floor_id)->delete();
                return redirect()->back()->with('message_warning','Selected Floor Deleted');
        }
        else{
            if(!empty($request->block && $request->floor)){
               $success= DB::table('hostel_floors')->insert([
                    'created_by'        =>auth()->user()->id,
                    'hostel_id'         =>$request->hostel,
                    'title'             =>$request->floor,
                    'block_id'          =>$request->block
                ]);
             if($success){
             
                 $request->session()->flash($this->message_success,"Floor Added Successfully");
                 return redirect()->back();
             }
             else{
                $request->session()->flash($this->message_warning,"Something Went Wrong");
                 return redirect()->back();
             }
           }
        }
        $data['floor']=DB::table('hostel_floors')->select('hostel_floors.id','hostel_floors.title as floor','hs.name as hostel','hb.title as block')
        ->where('hostel_floors.hostel_id',$id)
        ->leftjoin('hostels as hs','hs.id','=','hostel_floors.hostel_id')
        ->leftjoin('hostel_blocks as hb','hb.id','=','hostel_floors.block_id')
        ->get();
        $data['blocks']=DB::table('hostel_blocks')->select('id','title')
        ->where('hostel_id',$id)
        ->pluck('title','id')
        ->toArray();
        $data['blocks']=array_prepend($data['blocks'],'--Select Block--',"");
        $data['hostels']=DB::table('hostels')->select('name')->where('id',$id)->first();
        return view(parent::loadDataToView($this->view_path.'.detail.addFloor.index'), compact('data','id'));
    }
    public function edit_floor(Request $request,$hostel_id,$floor_id="")
    {   

        $id=$hostel_id;
        $fid=$floor_id;
        $data['row']=DB::table('hostel_floors')->select('title','hostel_floors.id')
        ->where('id',$fid)
        ->first();
            if(!empty($request->floor)){
               $success= DB::table('hostel_floors')->where('id',$fid)->update([
                    'title'             =>$request->floor,
                ]);
                $data['row']=null;
                $request->session()->flash($this->message_success,"Floor Updated Successfully");
               return redirect()->route('hostel.floor',$id);
           }
      
        $data['floor']=DB::table('hostel_floors')->select('hostel_floors.id','hostel_floors.title as floor','hs.name as hostel','hb.title as block')
        ->where('hostel_floors.hostel_id',$id)
        ->leftjoin('hostels as hs','hs.id','=','hostel_floors.hostel_id')
        ->leftjoin('hostel_blocks as hb','hb.id','=','hostel_floors.block_id')
        ->get();
        $data['blocks']=DB::table('hostel_blocks')->select('id','title')
        ->where('hostel_id',$id)
        ->pluck('title','id')
        ->toArray();
        $data['blocks']=array_prepend($data['blocks'],'--Select Block--',"");
        $data['hostels']=DB::table('hostels')->select('name')->where('id',$id)->first();
        return view(parent::loadDataToView($this->view_path.'.detail.addFloor.index'), compact('data','id','floor_id'));
    }
    public function findRooms(Request $request)
    {
        $response = [];
        $response['error'] = true;

        if ($request->has('hostel_id')) {
            $hostels = Room::select('id','room_number')
                ->where('hostels_id','=', $request->get('hostel_id'))
                ->Active()
                ->get();

            if ($hostels) {
                $response['rooms'] = $hostels;
                $response['error'] = false;
                $response['success'] = 'Rooms Available For This Hostel.';
            } else {
                $response['error'] = 'No Any Rooms Assign on This Hostel.';
            }

        } else {
            $response['message'] = 'Invalid request!!';
        }
        return response()->json(json_encode($response));
    }

    public function findBeds(Request $request)
    {
        $response = [];
        $response['error'] = true;

        if ($request->has('room_id')) {
            $beds = Bed::select('id','bed_number')
                ->where([['rooms_id','=', $request->get('room_id')],['bed_status','=', 1]])
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
}