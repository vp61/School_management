<?php

namespace App\Http\Controllers\Hostel;

use App\Http\Controllers\CollegeBaseController;
use App\Http\Requests\Hostel\Resident\AddValidation;
use App\Http\Requests\Hostel\Resident\EditValidation;
use App\Models\Bed;
use App\Models\Hostel;
use App\Models\Resident;
use App\Models\ResidentHistory;
use App\Models\Room;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Year;
use App\Models\Faculty;
use Carbon\Carbon;
use Illuminate\Http\Request;
use URL,DB;
use Session;
class ResidentController extends CollegeBaseController
{
    protected $base_route = 'hostel.resident';
    protected $view_path = 'hostel.resident';
    protected $panel = 'Resident';
    protected $filter_query = [];

    public function __construct()
    {
    }

    public function index(Request $request)
    {
        $data = [];

        $data['resident'] = Resident::select('residents.id', 'residents.hostels_id', 'residents.rooms_id', 'residents.beds_id', 'register_date', 'leave_date', 'user_type', 'member_id', 'residents.status','hostel_blocks.title as block','hostel_floors.title as floor')
            ->where(function ($query) use ($request) {
                if ($request->get('user_type') !== '' & $request->get('user_type') > 0) {
                    $query->where('user_type', '=', $request->user_type);
                    $this->filter_query['user_type'] = $request->user_type;
                }

                if ($request->reg_no != null) {
                    if($request->get('user_type') !== '' & $request->get('user_type') > 0){
                        if($request->has('user_type') == 1){
                            $studentId = $this->getStudentIdByReg($request->reg_no);
                            $query->where('member_id', '=', $studentId);
                            $this->filter_query['member_id'] = $studentId;
                        }
                        if($request->has('user_type') == 2){
                            $staffId = $this->getStaffByReg($request->reg_no);
                            $query->where('member_id', '=', $staffId);
                            $this->filter_query['member_id'] = $staffId;
                        }
                    }

                }

                if ($request->get('hostel') !== '' & $request->get('hostel') > 0) {
                    $query->where('hostels_id', '=', $request->get('hostel'));
                    $this->filter_query['hostels_id'] = $request->get('hostel');
                }

                if ($request->get('room_select') !== '' & $request->get('room_select') > 0) {
                    $query->where('rooms_id', '=', $request->get('room_select'));
                    $this->filter_query['rooms_id'] = $request->get('room_select');
                }

                if ($request->get('bed_select') !== '' & $request->get('bed_select') > 0) {
                    $query->where('beds_id', '=', $request->get('bed_select'));
                    $this->filter_query['beds_id'] = $request->get('bed_select');
                }

                if ($request->get('status') !== '' & $request->get('status') > 0) {
                    $query->where('residents.status', $request->get('status') == '1' ? 1 : 0);
                    $this->filter_query['status'] = $request->get('status');
                }
            })
            ->leftjoin('rooms','rooms.id','=','residents.rooms_id')
            ->leftjoin('hostel_blocks','hostel_blocks.id','=','rooms.block_id')
            ->leftjoin('hostel_floors','hostel_floors.id','=','rooms.floor_id')
            ->get();
        /*Hostel List*/
        $hostels = Hostel::select('id','name')->where('branch_id',Session::get('activeBranch'))->get();
        $map_hostels = array_pluck($hostels,'name','id');
        $data['hostels'] = array_prepend($map_hostels,'Select Hostel...','');

        /*Active Hostel For Shift List*/
        /*Hostel List*/
        $hostels = Hostel::select('id','name')->Active()->get();
        $map_hostels = array_pluck($hostels,'name','id');
        $data['active_hostels'] = array_prepend($map_hostels,'Select Hostel...','');

        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;

        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    public function add(Request $request)
    {
            $data = [];
        $data['date']=Carbon::now()->format('d-m-Y');
        /*Hostel List*/
        $hostels = Hostel::select('id','name')->where('branch_id',Session::get('activeBranch'))->Active()->get();
        $map_hostels = array_pluck($hostels,'name','id');
        $data['hostels'] = array_prepend($map_hostels,'--Select Hostel--',' ');
        $data['course']=Faculty::select('id','faculty')->where('branch_id',Session::get('activeBranch'))->orderBy('faculty','ASC')->pluck('faculty','id')->toArray();
        $data['course']=array_prepend($data['course'],"--Select Course--","");  
        $data['section']=Faculty::select('semesters.id','semesters.semester')
        ->where('faculties.branch_id',Session::get('activeBranch'))
        ->distinct('semesters.id')
        ->leftjoin('faculty_semester','faculty_semester.faculty_id','=','faculties.id')
        ->leftjoin('semesters','semesters.id','=','faculty_semester.semester_id')
        ->orderBy('semester','ASC')
        ->pluck('semesters.semester','semesters.id')
        ->toArray();
        $data['section']=array_prepend($data['section'],"--Select Section--","");
        $data['staff']=Staff::select('id',DB::raw("CONCAT(first_name,' ',last_name) as name"))->where('branch_id',Session::get('activeBranch'))->pluck('name','id')->toArray();
         $data['staff']=array_prepend($data['staff'],"--Select Staff--","");
        
       
        $data['reg_no'] ='';
        $data['mode']=DB::table('payment_type')->select('id','type_name')->pluck('type_name','type_name')->toArray();
        $data['mode']=array_prepend($data['mode'],'--Select Mode--',"");

        return view(parent::loadDataToView($this->view_path.'.add'), compact('data'));
    }

    public function store(AddValidation $request)
    {
        $userType = $request->get('user_type');
        $memId = $request->get('memberId');
        $hostel = $request->get('hostel');
        $room = $request->get('room');
        $bed = $request->get('bed');
        $rent = $request->get('rent');
        $year = Year::where('active_status','=',1)->first();        
        /*User Type and User Verification. only valid student or staff will get membership*/
        if($userType && $memId){
            switch ($userType){
                case 1:
                    $data = Student::where('id','=',$memId)->first();
                    break;
                case 2:
                    $data = Staff::where('id','=',$memId)->first();
                    break;
                default:
                    return parent::invalidRequest();
            }
        }else{
            $request->session()->flash($this->message_warning,' Registration Number or User Type is not Valid.');
        }

        if(isset($data)){
            $request->request->add(['hostels_id' => $hostel]);
            $request->request->add(['rooms_id' => $room]);
            $request->request->add(['beds_id' => $bed]);
            $request->request->add(['user_type' => $userType]);
            $request->request->add(['member_id' => $data->id]);
            $request->request->add(['rent' => $rent]);
            $request->request->add(['session_id' => Session::get('activeSession')]);
            $request->request->add(['branch_id' => Session::get('activeBranch')]);
            $request->request->add(['register_date' => Carbon::now()]);
            $request->request->add(['created_by' => auth()->user()->id]);

            /*Check Member Alreday Register or not*/
            $ResidentStatus = Resident::where(['user_type' => $request->user_type, 'member_id' => $data->id])->orderBy('id','desc')->first();
            if($ResidentStatus){
                $request->session()->flash($this->message_success, $this->panel. ' Already Registered. Please Edit This Resident');
                return back();
            }else{
                $ResidentRegister = Resident::create($request->all());
                /*check Resident Register and add on histroy table*/
                if($ResidentRegister){
                    $CreateHistory = ResidentHistory::create([
                            'years_id' => $year->id,
                            'hostels_id' => $hostel,
                            'rooms_id' => $room,
                            'beds_id' => $bed,
                            'residents_id' => $ResidentRegister->id,
                            'history_type' => "Registration",
                            'created_by' => auth()->user()->id,
                        ]);
                    /*if History Create/Assign Bed for Resident Bed Status Occupied*/
                    if($CreateHistory){
                        Bed::where('id','=',$bed)->update([
                            'bed_status' => 2
                        ]);
                    }
                }
                if($request->paid && $request->paid >0){
                    $no=1000+$ResidentRegister['id'];
                    $rcp='AESHS'.$no;
                    $hostelCollect=DB::table('hostel_collect_fees')->insert([
                        'member_id'     => $ResidentRegister['member_id'],
                        'resident_id'   => $ResidentRegister['id'],
                        'amount_paid'   => $request->paid,
                        'ref_no'        => $request->ref_no,
                        'pay_mode'      => $request->mode,
                        'status'        => 1,
                        'member_type'   => $ResidentRegister['user_type'],
                        'receipt_no'    => $rcp,
                        'receipt_date'    =>$request->reciept_date,
                        'receipt_by'    => auth()->user()->id,
                        'branch_id'     =>Session::get('activeBranch'),
                        'session_id'    =>Session::get('activeSession')
                    ]);
                    
                return redirect('hostel/print/'.$rcp);
                }
                
               
            }
        }else{
            $request->session()->flash($this->message_warning,' Registration Number or User Type is not Valid.');
        }
         $request->session()->flash($this->message_success, $this->panel. ' Created Successfully.');
       return redirect()->route($this->base_route);
    }

    public function edit(Request $request, $id)
    {
        $data = [];
        if (!$data['row'] = Resident::find($id))
            return parent::invalidRequest();

        if($data['row']->user_type == 1){
            $data['reg_no'] = Student::find($data['row']->member_id)->reg_no;
        }

        if($data['row']->user_type == 2){
            $data['reg_no'] = Staff::find($data['row']->member_id)->reg_no;
        }

        /*Hostel List*/
        $hostels = Hostel::select('id','name')->Active()->get();
        $map_hostels = array_pluck($hostels,'name','id');
        $data['hostels'] = array_prepend($map_hostels,'Select Hostel...','0');

        $data['base_route'] = $this->base_route;
        return view(parent::loadDataToView($this->view_path.'.edit'), compact('data'));
    }

    public function update(EditValidation $request, $id)
    {

        if (!$row = Resident::find($id)) return parent::invalidRequest();

        //dd($row);

        /*User Type and User Verification. only valid student or staff will get membership*/
        if($request->get('user_type') && $request->has('reg_no')){

            switch ($request->get('user_type')){
                case 1:
                    //$data = Student::find($row->member_id);
                    $data = Student::where('reg_no','=',$request->get('reg_no'))->first();
                    break;
                case 2:
                    $data = Staff::where('reg_no','=',$request->get('reg_no'))->first();
                    break;
                default:
                    return parent::invalidRequest();
            }
        }

        if($data){
            $request->request->add(['reg_no' => $request->get('reg_no')]);
            $request->request->add(['member_id' => $data->id]);
            $request->request->add(['last_updated_by' => auth()->user()->id]);
            $request->request->add(['created_by' => auth()->user()->id]);
            /*Check Member Alreday Register or not*/
            $ResidentStatus = Resident::where([['reg_no','=',$request->reg_no],['member_id','=', $data->id]])->get();

            if($ResidentStatus->count() > 0){
                $request->session()->flash($this->message_success, $this->panel. ' Already Registered or Duplicate Registration. Please, Find on Resident List and Edit');
            }else{
                $ResidentRegister = Resident::create($request->all());
                /*check Resident Register and add on histroy table*/
                if($ResidentRegister){
                    $row->update($request->all());

                    $request->session()->flash($this->message_success, $this->panel.' Updated Successfully.');
                }
            }
        }else{
            $request->session()->flash($this->message_warning,' Registration Number or User Type is not Valid.');
        }

        return redirect()->route($this->base_route);
    }

    public function delete(Request $request, $id)
    {
        if (!$row = Resident::find($id)) return parent::invalidRequest();

        /*Delete History*/
        ResidentHistory::where('residents_id','=',$row->id)->delete();
        /*Delete Resident*/
        $row->delete();

        $request->session()->flash($this->message_success, $this->panel.' Deleted Successfully.');
        return redirect()->route($this->base_route);
    }

    public function bulkAction(Request $request)
    {
        if ($request->has('bulk_action') && in_array($request->get('bulk_action'), ['Active', 'Shift', 'Leave', 'Delete'])) {
            /*Assign request values*/
            $hostel = $request->get('hostel_bulk');
            $room = $request->get('room_bulk');
            $bed = $request->get('bed_bulk');
            $year = Year::where('active_status','=',1)->first();

            if ($request->has('chkIds')) {
                foreach ($request->get('chkIds') as $row_id) {
                    $row = Resident::find($row_id);
                    if($row) {
                        switch ($request->get('bulk_action')) {
                            case 'Active':
                                if($hostel && $room && $bed){
                                    /*Create Bed Available When Resident Leave*/
                                    $oldBedStatus = Bed::where([
                                        ['hostels_id', '=', $row->hostels_id],
                                        ['rooms_id', '=', $row->rooms_id],
                                        ['id', '=', $row->beds_id]
                                    ])->first();

                                    if($oldBedStatus) $oldBedStatus->update(['bed_status' => 1]);

                                    /*New Bed Occupied*/
                                    $newBedStatus = Bed::where([
                                        ['hostels_id', '=', $hostel],
                                        ['rooms_id', '=', $room],
                                        ['id', '=', $bed]
                                    ])->first();

                                    if($newBedStatus) $newBedStatus->update(['bed_status' => 2]);

                                    /*Resident New Hostel, Room & Bed Assign*/
                                    $shift = $row->update([
                                        'hostels_id' => $hostel,
                                        'rooms_id' => $room,
                                        'beds_id' => $bed
                                    ]);

                                    if($shift) {
                                        /*Create History for Transfer Future Record*/
                                        ResidentHistory::create([
                                            'years_id' => $year->id,
                                            'hostels_id' => $hostel,
                                            'rooms_id' => $room,
                                            'beds_id' => $bed,
                                            'residents_id' => $row->id,
                                            'history_type' => "Shift",
                                            'created_by' => auth()->user()->id
                                        ]);
                                    }

                                    $request->session()->flash($this->message_success, 'Resident Shifted Successfully.');
                                }elseif($hostel && $room){
                                    /*Create Bed Available When Resident Leave*/
                                    $oldBedStatus = Bed::find($row->beds_id);
                                    /*Update Old Bed Status As Available*/
                                    if($oldBedStatus) $oldBedStatus->update(['bed_status' => 1]);

                                    /*Find Available Bed To Assign Resident Automatically*/
                                    $availableBed = Bed::select('id','hostels_id','rooms_id')
                                        ->where([['hostels_id','=',$hostel],['rooms_id','=',$room],['bed_status','=',1]])
                                        ->first();

                                    if($availableBed) {
                                        /*Resident New Hostel, Room & Bed Assign*/
                                        $shift = $row->update([
                                            'hostels_id' => $hostel,
                                            'rooms_id' => $room,
                                            'beds_id' => $availableBed->id
                                        ]);

                                        if ($shift) {
                                            /*Create History for Transfer Future Record*/
                                            ResidentHistory::create([
                                                'years_id' => $year->id,
                                                'hostels_id' => $hostel,
                                                'rooms_id' => $room,
                                                'beds_id' => $availableBed->id,
                                                'residents_id' => $row->id,
                                                'history_type' => "Shift",
                                                'created_by' => auth()->user()->id
                                            ]);
                                        }

                                        /*Update Bed Status As Occupied*/
                                        $availableBed->update(['bed_status' => 2]);
                                        $request->session()->flash($this->message_success, 'Resident Shifted Successfully.');
                                    }else{
                                        $request->session()->flash($this->message_success, 'No Any Bed Available in Your Target Hostel Room.');
                                    }
                                }elseif($hostel){
                                    /*Create Bed Available When Resident Leave*/
                                    $oldBedStatus = Bed::find($row->beds_id);

                                    /*Update Old Bed Status As Available*/
                                    if($oldBedStatus) $oldBedStatus->update(['bed_status' => 1]);

                                    /*Find Available Bed To Assign Resident Automatically*/
                                    $availableBed = Bed::select('id','hostels_id','rooms_id')
                                        ->where([['hostels_id','=',$hostel],['bed_status','=',1]])
                                        ->first();

                                    if($availableBed) {
                                        /*Resident New Hostel, Room & Bed Assign*/
                                        $shift = $row->update([
                                            'hostels_id' => $hostel,
                                            'rooms_id' => $availableBed->rooms_id,
                                            'beds_id' => $availableBed->id
                                        ]);

                                        if ($shift) {
                                            /*Create History for Transfer Future Record*/
                                            ResidentHistory::create([
                                                'years_id' => $year->id,
                                                'hostels_id' => $hostel,
                                                'rooms_id' => $availableBed->rooms_id,
                                                'beds_id' => $availableBed->id,
                                                'residents_id' => $row->id,
                                                'history_type' => "Shift",
                                                'created_by' => auth()->user()->id
                                            ]);
                                        }

                                        /*Update Bed Status As Occupied*/
                                        $availableBed->update(['bed_status' => 2]);
                                    }
                                    $request->session()->flash($this->message_success, 'Resident Shifted Successfully.');
                                }else{
                                    $request->session()->flash($this->message_warning, 'No Any Hostel Selected. Please, Select Hostel.');
                                }
                                break;
                            case 'Shift':
                                if($hostel && $room && $bed){
                                    /*Create Bed Available When Resident Leave*/
                                    $oldBedStatus = Bed::where([
                                        ['hostels_id', '=', $row->hostels_id],
                                        ['rooms_id', '=', $row->rooms_id],
                                        ['id', '=', $row->beds_id]
                                    ])->first();

                                    if($oldBedStatus) $oldBedStatus->update(['bed_status' => 1]);

                                    /*New Bed Occupied*/
                                    $newBedStatus = Bed::where([
                                        ['hostels_id', '=', $hostel],
                                        ['rooms_id', '=', $room],
                                        ['id', '=', $bed]
                                    ])->first();

                                    if($newBedStatus) $newBedStatus->update(['bed_status' => 2]);

                                    /*Resident New Hostel, Room & Bed Assign*/
                                    $shift = $row->update([
                                        'hostels_id' => $hostel,
                                        'rooms_id' => $room,
                                        'beds_id' => $bed
                                    ]);

                                    if($shift) {
                                        /*Create History for Transfer Future Record*/
                                        ResidentHistory::create([
                                            'years_id' => $year->id,
                                            'hostels_id' => $hostel,
                                            'rooms_id' => $room,
                                            'beds_id' => $bed,
                                            'residents_id' => $row->id,
                                            'history_type' => "Shift",
                                            'created_by' => auth()->user()->id
                                        ]);
                                    }

                                    $request->session()->flash($this->message_success, 'Resident Shifted Successfully.');
                                }elseif($hostel && $room){
                                    /*Create Bed Available When Resident Leave*/
                                    $oldBedStatus = Bed::find($row->beds_id);
                                    /*Update Old Bed Status As Available*/
                                    if($oldBedStatus) $oldBedStatus->update(['bed_status' => 1]);

                                    /*Find Available Bed To Assign Resident Automatically*/
                                    $availableBed = Bed::select('id','hostels_id','rooms_id')
                                        ->where([['hostels_id','=',$hostel],['rooms_id','=',$room],['bed_status','=',1]])
                                        ->first();

                                    if($availableBed) {
                                        /*Resident New Hostel, Room & Bed Assign*/
                                        $shift = $row->update([
                                            'hostels_id' => $hostel,
                                            'rooms_id' => $room,
                                            'beds_id' => $availableBed->id
                                        ]);

                                        if ($shift) {
                                            /*Create History for Transfer Future Record*/
                                            ResidentHistory::create([
                                                'years_id' => $year->id,
                                                'hostels_id' => $hostel,
                                                'rooms_id' => $room,
                                                'beds_id' => $availableBed->id,
                                                'residents_id' => $row->id,
                                                'history_type' => "Shift",
                                                'created_by' => auth()->user()->id
                                            ]);
                                        }

                                        /*Update Bed Status As Occupied*/
                                        $availableBed->update(['bed_status' => 2]);
                                        $request->session()->flash($this->message_success, 'Resident Shifted Successfully.');
                                    }else{
                                        $request->session()->flash($this->message_success, 'No Any Bed Available in Your Target Hostel Room.');
                                    }
                                }elseif($hostel){
                                    /*Create Bed Available When Resident Leave*/
                                    $oldBedStatus = Bed::find($row->beds_id);

                                    /*Update Old Bed Status As Available*/
                                    if($oldBedStatus) $oldBedStatus->update(['bed_status' => 1]);

                                    /*Find Available Bed To Assign Resident Automatically*/
                                    $availableBed = Bed::select('id','hostels_id','rooms_id')
                                        ->where([['hostels_id','=',$hostel],['bed_status','=',1]])
                                        ->first();

                                    if($availableBed) {
                                        /*Resident New Hostel, Room & Bed Assign*/
                                        $shift = $row->update([
                                            'hostels_id' => $hostel,
                                            'rooms_id' => $availableBed->rooms_id,
                                            'beds_id' => $availableBed->id
                                        ]);

                                        if ($shift) {
                                            /*Create History for Transfer Future Record*/
                                            ResidentHistory::create([
                                                'years_id' => $year->id,
                                                'hostels_id' => $hostel,
                                                'rooms_id' => $availableBed->rooms_id,
                                                'beds_id' => $availableBed->id,
                                                'residents_id' => $row->id,
                                                'history_type' => "Shift",
                                                'created_by' => auth()->user()->id
                                            ]);
                                        }

                                        /*Update Bed Status As Occupied*/
                                        $availableBed->update(['bed_status' => 2]);
                                    }
                                    $request->session()->flash($this->message_success, 'Resident Shifted Successfully.');
                                }else{
                                    $request->session()->flash($this->message_warning, 'No Any Hostel Selected. Please, Select Hostel.');
                                }
                                break;
                            case 'Leave':
                                    /*Create Bed Available When Resident Leave*/
                                    $bedStatus = Bed::where([
                                        ['hostels_id', '=', $row->hostels_id],
                                        ['rooms_id', '=', $row->rooms_id],
                                        ['id', '=', $row->beds_id]
                                    ])->first();

                                    if ($bedStatus) $bedStatus->update(['bed_status' => 1]);

                                    /*Create History for Leave Resident Future Record*/
                                    $CreateHistory = ResidentHistory::create([
                                        'years_id' => $year->id,
                                        'hostels_id' => $row->hostels_id,
                                        'rooms_id' => $row->rooms_id,
                                        'beds_id' => $row->beds_id,
                                        'residents_id' => $row->id,
                                        'history_type' => "Leave",
                                        'created_by' => auth()->user()->id
                                    ]);

                                    /*update Resident*/
                                    $request->request->add(['rooms_id' => null]);
                                    $request->request->add(['beds_id' => null]);
                                    $request->request->add(['status' => 'in-active']);
                                    $request->request->add(['last_updated_by' => auth()->user()->id]);
                                    $row->update($request->all());
                                    $request->session()->flash($this->message_success, ' Residents Leave Successfully.');

                                break;
                            case 'Delete':
                                $row = Resident::find($row_id);
                                /*Delete History*/
                                ResidentHistory::where('residents_id', '=', $row->id)->delete();
                                /*Delete Resident*/
                                $row->delete();
                                $request->session()->flash($this->message_success, 'Residents Deleted With History Successfully.');
                                break;
                        }
                    }
                }
                return redirect()->back();
            } else {
                $request->session()->flash($this->message_warning, 'Please, Check at least one row.');
                return redirect()->back();
            }
        } else return parent::invalidRequest();
    }

    public function renew(request $request)
    {
        $id = $request->get('residentId');
        $hostel = $request->get('hostel_assign');
        $room = $request->get('room_assign');
        $bed = $request->get('bed_assign');
        $year = Year::where('active_status','=',1)->first();

        if (!$row = Resident::find($id)) return parent::invalidRequest();

        $renewResident = $row->update([
                            'hostels_id' => $hostel,
                            'rooms_id' => $room,
                            'beds_id' => $bed,
                            'status' => 'active'
                        ]);

        if($renewResident){
            /*Create Renew History*/
            $CreateHistory = ResidentHistory::create([
                'years_id' => $year->id,
                'hostels_id' => $hostel,
                'rooms_id' => $room,
                'beds_id' => $bed,
                'residents_id' => $row->id,
                'history_type' => "Renew",
                'created_by' => auth()->user()->id,
            ]);
            /*if History Create/Assign Bed for Resident Bed Status Occupied*/
            if($CreateHistory){
                Bed::where('id','=',$bed)->update([
                    'bed_status' => 2
                ]);
            }

            $request->session()->flash($this->message_success, $this->panel.' Re-Active Successfully.');
        }else{
            $request->session()->flash($this->message_warning, 'Not A Valid Resident.');
        }

        return redirect()->back();
    }

    public function leave(request $request, $id)
    {
        if (!$row = Resident::where('id',$id)->Active()->first()) return parent::invalidRequest();
        $hostel = $row->hostels_id;
        $room = $row->rooms_id;
        $bed = $row->beds_id;
        /*Create Bed Available When Resident Leave*/
        $bedStatus = Bed::where([
            ['hostels_id','=', $row->hostels_id],
            ['rooms_id','=', $row->rooms_id],
            ['id','=', $row->beds_id]
        ])->first();

        if($bedStatus) $bedStatus->update(['bed_status' => 1 ]);

        /*update Resident*/
        $request->request->add(['rooms_id' => null]);
        $request->request->add(['beds_id' => null]);
        $request->request->add(['status' => 'in-active']);
        $request->request->add(['last_updated_by' => auth()->user()->id]);
        $resident = $row->update($request->all());

        $year = Year::where('active_status','=',1)->first();

        if($resident) {
            /*Create History for Leave Resident Future Record*/
            $CreateHistory = ResidentHistory::create([
                'years_id' => $year->id,
                'hostels_id' => $hostel,
                'rooms_id' => $room,
                'beds_id' => $bed,
                'residents_id' => $row->id,
                'history_type' => "Leave",
                'created_by' => auth()->user()->id
            ]);

            $request->session()->flash($this->message_success, 'Resident Leave Successfully.');
        }

        return redirect()->route($this->base_route);
    }

    public function shift(request $request)
    {
        /*Get Request values on Variables */
        $id = $request->get('residentId');
        $hostel = $request->get('hostel');
        $room = $request->get('room');
        $bed = $request->get('bed');
        $rate =$request->get('rent');
        $year = Year::where('active_status','=',1)->first();

        if($hostel > 0 && $room > 0 && $bed > 0) {
            $resident = Resident::where('id', $id)->Active()->first();

            if ($resident) {
                /*Create Bed Available When Resident Leave*/
                $oldBedStatus = Bed::where([
                    ['hostels_id', '=', $resident->hostels_id],
                    ['rooms_id', '=', $resident->rooms_id],
                    ['id', '=', $resident->beds_id]
                ])->first();

                if ($oldBedStatus)
                    $oldBedStatus->update(['bed_status' => 1]);

                /*New Bed Occupied*/
                $newBedStatus = Bed::where([
                    ['hostels_id', '=', $hostel],
                    ['rooms_id', '=', $room],
                    ['id', '=', $bed]
                ])->first();

                if ($newBedStatus)
                    $newBedStatus->update(['bed_status' => 2]);

                /*Resident New Hostel, Room & Bed Assign*/
                $shift = $resident->update([
                    'hostels_id' => $hostel,
                    'rooms_id' => $room,
                    'beds_id' => $bed,
                    'rent'    => $rate,
                    'branch_id'=>Session::get('activeBranch'),
                    'session_id'=>Session::get('activeSession')
                ]);

                if ($shift) {
                    /*Create History for Transfer Future Record*/
                    $CreateHistory = ResidentHistory::create([
                        'years_id' => $year->id,
                        'hostels_id' => $hostel,
                        'rooms_id' => $room,
                        'beds_id' => $bed,
                        'residents_id' => $resident->id,
                        'history_type' => "Shift",
                        'created_by' => auth()->user()->id
                    ]);
                }

                $request->session()->flash($this->message_success, 'Resident Shifted Successfully.');
            } else {
                $request->session()->flash($this->message_warning, 'Resident Not Select or Not Active, Please Active First.');
            }
        }else{
            $request->session()->flash($this->message_warning, 'Please, Select Hostel, Room and Bed First.');
        }
        return redirect()->route($this->base_route);
    }

    /*History*/
    public function history(Request $request)
    {
        $data = [];
        if($request->all()) {
            $data['history'] = ResidentHistory::select('resident_histories.id', 'resident_histories.years_id',
                'resident_histories.hostels_id', 'resident_histories.rooms_id', 'resident_histories.beds_id',
                'resident_histories.residents_id', 'resident_histories.history_type','resident_histories.created_at',
                'r.user_type', 'r.member_id', 'r.status')
                ->where(function ($query) use ($request) {

                    if ($request->get('user_type') !== '' & $request->get('user_type') > 0) {
                        $query->where('r.user_type', '=', $request->get('user_type'));
                        $this->filter_query['r.user_type'] = $request->get('user_type');
                    }

                    if ($request->reg_no != null) {
                        if($request->get('user_type') !== '' & $request->get('user_type') > 0){
                            if($request->has('user_type') == 1){
                                $studentId = $this->getStudentIdByReg($request->reg_no);
                                $query->where('member_id', '=', $studentId);
                                $this->filter_query['member_id'] = $studentId;
                            }
                            if($request->has('user_type') == 2){
                                $staffId = $this->getStaffByReg($request->reg_no);
                                $query->where('member_id', '=', $staffId);
                                $this->filter_query['member_id'] = $staffId;
                            }
                        }

                    }

                    if ($request->get('year') !== '' & $request->get('year') > 0) {
                        $query->where('resident_histories.years_id', '=', $request->get('year'));
                        $this->filter_query['resident_histories.years_id'] = $request->get('year');
                    }

                    if ($request->history_type <> '0'){
                        $query->where('resident_histories.history_type', '=', $request->get('history_type'));
                        $this->filter_query['resident_histories.history_type'] = $request->get('history_type');
                    }

                    if ($request->get('hostel') !== '' & $request->get('hostel') > 0) {
                        $query->where('resident_histories.hostels_id', '=', $request->get('hostel'));
                        $this->filter_query['resident_histories.hostels_id'] = $request->get('hostel');
                    }

                    if ($request->get('room_select') !== '' & $request->get('room_select') > 0) {
                        $query->where('resident_histories.rooms_id', '=', $request->get('room_select'));
                        $this->filter_query['resident_histories.rooms_id'] = $request->get('room_select');
                    }

                    if ($request->get('bed_select') !== '' & $request->get('bed_select') > 0) {
                        $query->where('resident_histories.beds_id', '=', $request->get('bed_select'));
                        $this->filter_query['resident_histories.beds_id'] = $request->get('bed_select');
                    }

                    /*if ($request->get('history_type') !== '') {
                        $query->where('resident_histories.history_type', '=', $request->get('history_type'));
                        $this->filter_query['resident_histories.history_type'] = $request->get('history_type');
                    }*/
                })
                ->join('residents as r', 'r.id', '=', 'resident_histories.residents_id')
                ->join('beds as b', 'b.id', '=', 'resident_histories.beds_id')
                ->orderBy('resident_histories.created_at')
                ->get();
        }

        /*Year*/
        $hostels = Year::select('id','title')->Active()->get();
        $map_years = array_pluck($hostels,'title','id');
        $data['years'] = array_prepend($map_years,'Select Year...','0');

        /*Hostel List*/
        $hostels = Hostel::select('id','name')->get();
        $map_hostels = array_pluck($hostels,'name','id');
        $data['hostels'] = array_prepend($map_hostels,'Select Hostel...','0');

        $data['url'] = URL::current();
        //$data['filter_query'] = $this->filter_query;

        return view(parent::loadDataToView($this->view_path.'.history.index'), compact('data'));
    }

    /*All Room & Bed available or not*/
  
    public function findBlock(Request $request){
        $response = [];
        $response['error'] = true;
        if($request->has('hostel_id')){
            $hostel=DB::table('hostel_blocks')->select('id','title')->where('hostel_id',$request->get('hostel_id'))
            ->get();
            if($hostel){
                $response['block']= $hostel;
                $response['error']= false;
                $response['success']='Block available';
            }
        }
        else{
            $response['message']='Invalid Request';
        }
        return response()->json(json_encode($response));
    }
    public function findFloor(Request $request){
        $response = [];
        $response['error'] = true;
        if($request->has('block_id')){
            $floor=DB::table('hostel_floors')->select('id','title')
            ->where([['block_id',$request->get('block_id')]])
            ->get();
            if($floor){
                $response['floor']= $floor;
                $response['error']= false;
                $response['success']='Floor available';
            }
        }
        else{
            $response['message']='Invalid Request';
        }
        return response()->json(json_encode($response));

    }
     public function findRooms(Request $request)
    {
        $response = [];
        $response['error'] = true;

        if ( $request->has('floor_id') ) {
            $hostels = Room::select('rooms.id','room_number')
                ->where([
                   
                    ['rooms.floor_id','=', $request->get('floor_id')]
                ])
                ->orderBy('room_number','ASC')
                ->get();

            if ($hostels) {
                $response['rooms'] = $hostels;
                $response['error'] = false;
                $response['success'] = 'Rooms Available For This Hostel.';
            } 
        }
        else {
            $response['message'] = 'Invalid request!!';
        }
        return response()->json(json_encode($response));
    } 
     public function findBed(Request $request)
    {
        $response = [];
        $response['error'] = true;

        if ( $request->has('room_id') ) {
            $hostels = Bed::select('id','bed_number')
                ->where([
                    ['rooms_id','=', $request->get('room_id')],
                    ['bed_status','=','1']
                ])
                ->orderBy('bed_number','ASC')
                ->get();

            if ($hostels) {
                $response['bed'] = $hostels;
                $response['error'] = false;
                $response['success'] = 'Rooms Available For This Hostel.';
            } 
        }
        else {
            $response['message'] = 'Invalid request!!';
        }
        return response()->json(json_encode($response));
    } 
    public function findRate(Request $request){
         $response = [];
        $response['error'] = true;

        if ( $request->has('bed_id') ) {
            $hostels = Bed::select('id','rate')
                ->where([
                    ['id','=', $request->get('bed_id')],
                ])
                ->orderBy('bed_number','ASC')
                ->get();

            if ($hostels) {
                $response['bed'] = $hostels;
                $response['error'] = false;
                $response['success'] = 'Rooms Available For This Hostel.';
            } 
        }
        else {
            $response['message'] = 'Invalid request!!';
        }
        return response()->json(json_encode($response));
    } 
    public function loadStudent(Request $request){
        $response=[];
        $response['error']=true;
        if($request->has('course') && $request->has('section')){
            $student= Student::select('id','first_name as name')
            ->where([
                ['faculty','=',$request->get('course')],
                ['semester','=',$request->get('section')]
            ])
            ->orderBy('first_name','ASC')
            ->get();
            if($student){
                $response['student']=$student;
                $response['error'] = false;
                $response['success'] = 'Student Found';  
            }
        }
        else{
            $response['message']='invalid Request';
        }
        return response()->json(json_encode($response));
    }
   

}