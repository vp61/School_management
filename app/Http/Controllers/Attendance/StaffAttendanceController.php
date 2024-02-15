<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\CollegeBaseController;
use App\Models\Attendance;
use App\Models\AttendanceMaster;
use App\Models\AttendanceStatus;
use App\Models\Month;
use App\Models\Staff;
use App\Models\StaffDesignation;
use App\Models\Year;
use Carbon\Carbon;
use Illuminate\Http\Request;
use View, URL,DB;  use Session;

class StaffAttendanceController extends CollegeBaseController
{
    protected $base_route = 'attendance.staff';
    protected $view_path = 'attendance.staff';
    protected $panel = 'Staff Attendance';
    protected $filter_query = [];

    public function __construct()
    {

    }

    public function index(Request $request)
    {
        $data = [];
        if($request->all()) {
            $data['staff'] = Attendance::select('attendances.id', 'attendances.attendees_type', 'attendances.link_id',
                'attendances.years_id', 'attendances.months_id', 'attendances.day_1', 'attendances.day_2', 'attendances.day_3',
                'attendances.day_4', 'attendances.day_5', 'attendances.day_6', 'attendances.day_7', 'attendances.day_8',
                'attendances.day_9', 'attendances.day_10', 'attendances.day_11', 'attendances.day_12', 'attendances.day_13',
                'attendances.day_14', 'attendances.day_15', 'attendances.day_16', 'attendances.day_17', 'attendances.day_18',
                'attendances.day_19', 'attendances.day_20', 'attendances.day_21', 'attendances.day_22', 'attendances.day_23',
                'attendances.day_24', 'attendances.day_25', 'attendances.day_26', 'attendances.day_27', 'attendances.day_28',
                'attendances.day_29', 'attendances.day_30', 'attendances.day_31', 's.id as staff_id', 's.reg_no',
                's.first_name', 's.middle_name', 's.last_name', 's.designation')
                ->where('attendances.attendees_type', 2)
                ->where(function ($query) use ($request) {
                    if ($request->has('year') && $request->get('year') != 0) {
                        $query->where('attendances.years_id', '=', $request->year);
                        $this->filter_query['attendances.years_id'] = $request->year;
                    }

                    if ($request->has('month') && $request->get('month') != 0) {
                        $query->where('attendances.months_id', '=', $request->month);
                        $this->filter_query['attendances.months_id'] = $request->month;
                    }

                    if ($request->has('reg_no') && $request->get('reg_no') != null) {
                        $query->where('s.reg_no', $request->reg_no);
                        $this->filter_query['s.reg_no'] = $request->reg_no;
                    }

                    if ($request->has('designation')) {
                        $query->where('s.designation', '=', $request->designation);
                        $this->filter_query['s.designation'] = $request->designation;
                    }

                })
                ->join('staff as s', 's.id', '=', 'attendances.link_id')
                ->orderBy('s.id','asc')
                ->orderBy('attendances.years_id','asc')
                ->orderBy('attendances.months_id','asc')
                ->get();

            //dd($data['staff']->toArray());
        }

        $data['years'] = [];
        $data['years'][0] = 'Select Year';
        foreach (Year::select('id', 'title')->get() as $year) {
            $data['years'][$year->id] = $year->title;
        }

        $data['months'] = [];
        $data['months'][0] = 'Select Month';
        foreach (Month::select('id', 'title')->orderBy('id')->get() as $month) {
            $data['months'][$month->id] = $month->title;
        }

        $data['designation'] = [];
        $data['designation'][0] = 'Select Designation';
      
        foreach (StaffDesignation::select('id', 'title')->get() as $designation) {
            $data['designation'][$designation->id] = $designation->title;
        }

        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;

        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    public function add(Request $request)
    { 
        $data = [];
        $data['designation'] = [];
        $data['designation'][0] = 'Select Designation';
        foreach (StaffDesignation::select('id', 'title')->get() as $designation) {
            $data['designation'][$designation->id] = $designation->title;
        }

        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;

        return view(parent::loadDataToView($this->view_path.'.add'), compact('data'));
    }

    public function store(Request $request)
    {
        $response = [];
        $response['error'] = true;
        $date = Carbon::parse($request->get('date'));
        $month = Carbon::createFromFormat('Y-m-d H:i:s', $date)->month;
        $day = "day_".Carbon::createFromFormat('Y-m-d H:i:s', $date)->day;
        $yearTitle = Carbon::createFromFormat('Y-m-d H:i:s', $date)->year;
        $year = Year::where('title',$yearTitle)->first()->id;

        $attendanceStatus = AttendanceStatus::get();

        $designation = $request->get('designation');
        $semester = $request->get('semester_select');

        if($request->has('staffs_id')) {
            foreach ($request->get('staffs_id') as $staff) {

                $attendanceExist = Attendance::select('attendances.id','attendances.attendees_type','attendances.link_id',
                    'attendances.years_id','attendances.months_id','attendances.'.$day,
                    's.id as staff_id','s.reg_no','s.first_name','s.middle_name','s.last_name','s.staff_image')
                    ->where('attendances.attendees_type',2)
                    ->where('attendances.years_id',$year)
                    ->where('attendances.months_id',$month)
                    ->where([['s.id', '=' , $staff],['s.designation', '=' , $designation]])
                    ->join('staff as s','s.id','=','attendances.link_id')
                    ->first();

                /*get ledger exist staff id*/

                if ($attendanceExist) {
                    /*Update Already Register Mark Ledger*/
                    $Update = [
                        'attendees_type' => 2,
                        'link_id' => $staff,
                        'years_id' => $year,
                        'months_id' => $month,
                        $day => $request->get($staff),
                        'last_updated_by' => auth()->user()->id
                    ];

                    $attendanceExist->update($Update);
                }else{
                    /*Schedule When Not Scheduled Yet*/
                    Attendance::create([
                        'attendees_type' => 2,
                        'link_id' => $staff,
                        'years_id' => $year,
                        'months_id' => $month,
                        $day => $request->get($staff),
                        'created_by' => auth()->user()->id,
                    ]);

                }
            }
            $request->session()->flash($this->message_success, $this->panel. ' Manage Successfully.');
            return redirect()->back();
        }else{
            $request->session()->flash($this->message_warning, 'You Have No Any Staff to Manage Attendance. ');
            return back();
        }


        return redirect()->route($this->base_route);
    }

    public function delete(Request $request, $id)
    {
        if (!$row = Attendance::find($id)) return parent::invalidRequest();

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
                            $row = Attendance::find($row_id);
                            if ($row) {
                                $row->status = $request->get('bulk_action') == 'active'?'active':'in-active';
                                $row->save();
                            }
                            break;
                        case 'delete':
                            $row = Attendance::find($row_id);
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


    public function staffHtmlRow(Request $request)
    {
        $response = [];
        $response['error'] = true;
        $date = Carbon::parse($request->get('date'));
        $month = Carbon::createFromFormat('Y-m-d H:i:s', $date)->month;
        $day = "day_".Carbon::createFromFormat('Y-m-d H:i:s', $date)->day;
        $yearTitle = Carbon::createFromFormat('Y-m-d H:i:s', $date)->year;
        $year = Year::where('title',$yearTitle)->first()->id;

        $attendanceStatus = AttendanceStatus::get();

        $designation = $request->get('designation_id');

        /*For Staff List*/
        $attendanceExist = Attendance::select('attendances.attendees_type','attendances.link_id',
            'attendances.years_id','attendances.months_id','attendances.'.$day,
            's.id as staff_id','s.reg_no','s.first_name','s.middle_name','s.last_name','s.staff_image')
            ->where('attendances.attendees_type',2)
            ->where('attendances.years_id',$year)
            ->where('attendances.months_id',$month)
            ->where($day,'<>',0)
            ->where('s.designation', $designation)
            ->join('staff as s','s.id','=','attendances.link_id')
            ->get();

        /*get ledger exist staff id*/
        $dayStatus  = array_pluck($attendanceExist, $day);
        $existStaffId  = array_pluck($attendanceExist, 'staff_id');

        //Get Active Staff For Related Faculty and Semester
        $activeStaff = Staff::select('id','reg_no','first_name','middle_name','last_name','staff_image')
            ->where('designation', $designation)
            ->where('branch_id', session('activeBranch'))
            ->whereNotIn('id',$existStaffId)
            ->Active()
            ->orderBy('id','asc')
            ->get();

        if($activeStaff) {
            if($attendanceExist){
                $response['error'] = false;

                $response['exist'] = view($this->view_path.'.includes.staff_tr_rows', [
                    'exist' => $attendanceExist,
                    'attendanceStatus' => $attendanceStatus,
                    'dayStatus' => $dayStatus,
                    'day' => $day,
                ])->render();

                $response['staffs'] = view($this->view_path.'.includes.staff_tr', [
                    'staffs' => $activeStaff,
                    'attendanceStatus' => $attendanceStatus
                ])->render();

                $response['message'] = 'Active Staff Found. Please, Manage Attendance.';
            }else{
                $response['error'] = false;

                $response['staffs'] = view($this->view_path.'.includes.staff_tr', [
                    'staffs' => $activeStaff
                ])->render();

                $response['message'] = 'Active Staff Found. Please, Manage Mark.';
            }
        }else{
            $response['error'] = 'No Any Active Staffs Found.';
        }

        return response()->json(json_encode($response));
    }

    // By ANkur
    
    public function graph(Request $request)
    {
       $response = [];
        $response['error'] = true;
        $date = Carbon::now();
        //dd($date);
        $month = Carbon::createFromFormat('Y-m-d H:i:s', $date)->month;
        $day = "day_".Carbon::createFromFormat('Y-m-d H:i:s', $date)->day;
        $yearTitle = Carbon::createFromFormat('Y-m-d H:i:s', $date)->year;
        $year = Year::where('title',$yearTitle)->first()->id;
        //$branch_id = Auth::user()->branch_id;

        $data['sem'] = DB::table('faculty_semester')->select('semesters.id','faculty_id as course_id','semester_id as Semester','semesters.semester','faculties.faculty')
            ->leftJoin('semesters','semesters.id','=','faculty_semester.semester_id')
            ->leftjoin('faculties','faculties.id','=','faculty_semester.faculty_id')
            ->where('faculties.branch_id',session::get('activeBranch'))
            ->orderBy('faculty_id')
            ->orderBy('semester_id')
            ->get();
            //dd($data['sem']);
            $no_stu=[];
            foreach($data['sem'] as $key=>$value){
                //dd($value);

                
                $no_stu[]=DB::table('student_detail_sessionwise as sds')->select('student_id','sds.course_id','sds.Semester','semesters.semester','faculties.faculty',DB::raw('count(student_id) as total_stu'))
                ->leftJoin('semesters','semesters.id','=','sds.Semester')
                ->leftjoin('faculties','faculties.id','=','sds.course_id')
                ->where('course_id','=',$value->course_id)
                ->where('sds.session_id',session::get('activeSession'))
                //->where('faculties.branch_id',session::get('activeBranch'))
                ->where('sds.Semester','=',$value->Semester)
                ->groupBy('sds.student_id')
                ->get();


               
                
            }
            $attendanceStatus=[];
            foreach($no_stu as $ke=>$val){
              

                foreach($val as $v){

                    if(!isset($attendanceStatus[$v->course_id][$v->Semester]['total'])){
                        $attendanceStatus[$v->course_id][$v->Semester]['total'] = 0;
                    }
                    $attendanceStatus[$v->course_id][$v->Semester]['total']=$attendanceStatus[$v->course_id][$v->Semester]['total'] +1;
                    if(!isset($attendanceStatus[$v->course_id][$v->Semester]['total_prst'])){
                        $attendanceStatus[$v->course_id][$v->Semester]['total_prst']=0;
                     }
                     
                     if(!isset($attendanceStatus[$v->course_id][$v->Semester]['total_abst'])){
                        $attendanceStatus[$v->course_id][$v->Semester]['total_abst']=0;
                     }


                    $temp=DB::table('attendances')->select('attendances.id','attendances.attendees_type','attendances.link_id',
                        'attendances.years_id','attendances.months_id','attendances.'.$day,DB::raw('count(attendances.day_17) as tokan'))
                    ->leftjoin('students as s','s.id','=','attendances.link_id')
                    
                    ->where('attendances.link_id','=',$v->student_id)
                    ->where('attendances.attendees_type',1)
                    ->where('attendances.years_id',$year)
                    ->where('attendances.months_id',$month)
                    ->where($day,'=',1)         
                    ->get();
                    if($temp[0]->$day==1){
                       $attendanceStatus[$v->course_id][$v->Semester]['total_prst']=$attendanceStatus[$v->course_id][$v->Semester]['total_prst'] + 1;
                    }
                    else{
                        
                        $attendanceStatus[$v->course_id][$v->Semester]['total_abst']=$attendanceStatus[$v->course_id][$v->Semester]['total_abst'] + 1;  
                    }

                    $attendanceStatus[$v->course_id][$v->Semester]['semester']= $v->semester;
                    $attendanceStatus[$v->course_id][$v->Semester]['course_id']= $v->faculty;
                }

                //$attendanceStatus[$v->course_id][$v->Semester]['course_id']= $v->faculty;
            }
            $data['staff']=DB::table('staff')->select('staff.id','staff.designation','sd.title','sd.id as designation_id')
            ->leftjoin('staff_designations as sd','sd.id','=','staff.designation')
            //->where('branch_id',$branch_id)
           ->groupBy('staff.id')

            ->get();
            foreach($data['staff'] as $key=>$value){
                if(!isset($attendanceStaff[$value->designation]['total'])){
                        $attendanceStaff[$value->designation]['total'] = 0;
                        
                    }
                    $attendanceStaff[$value->designation]['total']=$attendanceStaff[$value->designation]['total'] +1;

                    if(!isset($attendanceStaff[$value->designation]['total_prst'])){
                        $attendanceStaff[$value->designation]['total_prst'] = 0;
                        
                    }if(!isset($attendanceStaff[$value->designation]['total_abst'])){
                        $attendanceStaff[$value->designation]['total_abst'] = 0;
                        
                    }
                
                $temp=DB::table('attendances')->select('attendances.id','attendances.attendees_type','attendances.link_id','attendances.years_id','attendances.months_id','attendances.'.$day,DB::raw('count(attendances.day_22) as tokan'))
                ->leftjoin('staff as s','s.id','=','attendances.link_id') 
                ->where('attendances.attendees_type',2)
                ->where('attendances.years_id',$year)
                ->where('attendances.months_id',$month)
                ->where('attendances.link_id', $value->id)
              
                ->where($day,'=',1)
                   
                ->get();
                if($temp[0]->$day==1){
                    $attendanceStaff[$value->designation]['total_prst']=$attendanceStaff[$value->designation]['total_prst'] + 1;
                }else{
                     $attendanceStaff[$value->designation]['total_abst']=$attendanceStaff[$value->designation]['total_abst'] + 1;

                }
                $attendanceStaff[$value->designation]['designation']=$value->title;
            }
           
        return view(parent::loadDataToView($this->view_path.'.graph'), compact('data','attendanceStatus','attendanceStaff'));
    }
}