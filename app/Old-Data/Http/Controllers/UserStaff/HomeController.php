<?php

namespace App\Http\Controllers\UserStaff;

use App\Http\Controllers\CollegeBaseController;
use App\Models\AlertSetting;
use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\AttendanceStatus;
use App\Models\Document;
use App\Models\Faculty;
use App\Models\LibraryMember;
use App\Models\Month;
use App\Models\Note;
use App\Models\Notice;
use App\Models\ResidentHistory;
use App\Models\Semester;
use App\Models\Staff;
use Session;
use DB;
use App\Models\Student;
use App\Models\Subject;
use App\Models\TransportHistory;
use App\Models\Year;
use App\Traits\AcademicScope;
use App\Traits\SmsEmailScope;
use App\Traits\StudentScopes;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use ViewHelper, URL;

class HomeController extends CollegeBaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    protected $base_route = 'dashboard';
    protected $view_path = 'user-staff';
    protected $panel = 'Dashboard';
    protected $filter_query = [];

    use StudentScopes;
    use AcademicScope;
    use SmsEmailScope;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $id = auth()->user()->hook_id;
        $userRoleId = auth()->user()->roles()->first()->id;
        $data = [];
        $data['staff'] = Staff::select('id','reg_no', 'join_date', 'first_name',  'middle_name', 'last_name',
            'father_name', 'mother_name', 'date_of_birth', 'gender', 'blood_group', 'nationality','mother_tongue', 'address', 'state', 'country',
            'temp_address', 'temp_state', 'temp_country', 'home_phone', 'mobile_1', 'mobile_2', 'email', 'qualification',
            'experience', 'experience_info', 'other_info','staff_image', 'status')
            ->where('id','=',$id)
            ->first();

        if (!$data['staff']){
            request()->session()->flash($this->message_warning, "Not a Valid Staff");
            return redirect()->route($this->base_route);
        }

        $data['note'] = Note::select('created_at', 'id', 'member_type','member_id','subject', 'note', 'status')
            ->where('member_type','=','staff')
            ->where('member_id','=', $data['staff']->id)
            ->orderBy('created_at','desc')
            ->get();

        $data['document'] = Document::select('id', 'member_type','member_id', 'title', 'file','description', 'status')
            ->where('member_type','=','staff')
            ->where('member_id','=',$data['staff']->id)
            ->orderBy('created_by','desc')
            ->get();

        /*Notice*/
        $now = date('Y-m-d');
        $data['notice_disaplay'] = Notice::select('last_updated_by', 'title', 'message',  'publish_date', 'end_date',
            'display_group', 'status')
            ->where('display_group','like','%'.$userRoleId.'%')
            ->where('publish_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->latest()
            ->get();

        $data['staff_login'] = User::where([['role_id',5],['hook_id',$id]])->first();

        return view(parent::loadDataToView($this->view_path.'.dashboard.index'), compact('data'));
    }


    public function password(Request $request, $id)
    {
        if (!$row = User::find($id)) return parent::invalidRequest();

        if ($request->get('password') === $request->get('confirmPassword')){
            $new_password= bcrypt($request->get('password'));
        }else{
            $request->session()->flash($this->message_warning, 'Password & Confirm Password Not Match.');
            return redirect()->back();
        }

        $request->request->add(['password' => isset($new_password)?$new_password:$row->password]);

        $row->update($request->all());

        $request->session()->flash($this->message_success, 'Login Detail Updated Successfully.');
        return redirect()->back();
    }

    public function payroll()
    {
        $this->panel = 'Payroll | Staff User';
        $id = auth()->user()->hook_id;
        $data = [];
        $data['staff'] = Staff::select('id','reg_no', 'join_date', 'first_name',  'middle_name', 'last_name',
            'father_name', 'mother_name', 'date_of_birth', 'gender', 'blood_group', 'nationality','mother_tongue', 'address', 'state', 'country',
            'temp_address', 'temp_state', 'temp_country', 'home_phone', 'mobile_1', 'mobile_2', 'email', 'qualification',
            'experience', 'experience_info', 'other_info','staff_image', 'status')
            ->where('id','=',$id)
            ->first();

        if (!$data['staff']){
            request()->session()->flash($this->message_warning, "Not a Valid Staff");
            return redirect()->route($this->base_route);
        }

        $data['payroll_master'] = $data['staff']->payrollMaster()->orderBy('due_date','desc')->get();
        $data['pay_salary'] = $data['staff']->paySalary()->get();

        return view(parent::loadDataToView($this->view_path.'.payroll.index'), compact('data'));
    }

    public function library()
    {
        $this->panel = 'Library | Staff User';
        $id = auth()->user()->hook_id;
        $data['lib_member'] = LibraryMember::where(['library_members.user_type' => 2, 'library_members.member_id' => $id])
            ->first();

        if($data['lib_member'] != null){
            $data['books_history'] = $data['lib_member']->libBookIssue()->select('book_issues.id', 'book_issues.member_id',
                'book_issues.book_id',  'book_issues.issued_on', 'book_issues.due_date','book_issues.return_date', 'b.book_masters_id',
                'b.book_code', 'bm.title','bm.categories')
                ->join('books as b','b.id','=','book_issues.book_id')
                ->join('book_masters as bm','bm.id','=','b.book_masters_id')
                ->orderBy('book_issues.issued_on', 'desc')
                ->get();

            $data['circulation'] = $data['lib_member']->libCirculation()->first();
        }

        return view(parent::loadDataToView($this->view_path.'.library.index'), compact('data'));
    }


    public function hostel()
    {
        $this->panel = 'Hostel | Staff User';
        $id = auth()->user()->hook_id;
        $data = [];

        $data['history'] = ResidentHistory::select('resident_histories.years_id', 'resident_histories.hostels_id',
            'resident_histories.rooms_id', 'resident_histories.beds_id',
            'resident_histories.history_type','resident_histories.created_at')
            ->where(['r.user_type' => 2, 'r.member_id' => $id])
            ->join('residents as r', 'r.id', '=', 'resident_histories.residents_id')
            ->join('beds as b', 'b.id', '=', 'resident_histories.beds_id')
            ->orderBy('resident_histories.created_at')
            ->get();

        return view(parent::loadDataToView($this->view_path.'.hostel.index'), compact('data'));
    }


    // public function oldTransport()
    // {
    //     $this->panel = 'Transport | Staff User';
    //     $id = auth()->user()->hook_id;
    //     $reg_no = $this->getStaffById($id);
    //     $data = [];

    //     /*Transport History*/
    //     $data['transport_history'] = TransportHistory::select('transport_histories.id', 'transport_histories.years_id',
    //         'transport_histories.routes_id', 'transport_histories.vehicles_id',  'transport_histories.history_type',
    //         'transport_histories.created_at','tu.member_id','tu.user_type')
    //         ->where(['tu.user_type' => 2, 'tu.member_id' => $id])
    //         ->join('transport_users as tu','tu.id','=','transport_histories.travellers_id')
    //         ->latest()
    //         ->get();

    //     return view(parent::loadDataToView($this->view_path.'.transport.index'), compact('data'));
    // }
    public function transport(){
        $this->panel = "Transport | Student User";
        $id = auth()->user()->hook_id;
        $sessionId=Session::get('activeSession');
        $branchId=Session::get('activeBranch');
        $data=[];

         $data['user']=DB::table('transport_users')->select('transport_users.id','total_rent','rt.title','vh.number','duration',DB::raw("CONCAT(st.first_name,' ',st.middle_name,' ',st.last_name) as first_name"),'from_date','to_date')
               ->where([
                ['transport_users.user_type','=',2],
                ['transport_users.member_id','=',$id],
                ['transport_users.session','=',$sessionId],
                ['transport_users.branch','=',$branchId],
               ])
               ->leftjoin('routes as rt','rt.id','=','transport_users.routes_id')
               ->leftjoin('vehicles as vh','vh.id','=','transport_users.vehicles_id')
               ->leftjoin('staff as st','st.id','=','transport_users.member_id')
               ->orderBy('transport_users.id','asc')
               ->get();

               $paid=DB::table('transport_collect_fees')->select('amount_paid','pay_mode','receipt_no',DB::raw("DATE_FORMAT(transport_collect_fees.created_at,'%d-%m-%Y') as created_at"),'ref_no','transport_users.duration')
               ->where([
                  ['transport_collect_fees.member_id','=',$id],
                  ['transport_collect_fees.member_type','=',2],
                  ['branch_id','=',$branchId],
                  ['session_id','=',$sessionId]
               ])
               ->leftjoin('transport_users','transport_users.id','=','transport_collect_fees.transport_user_id')
               ->orderBy('transport_collect_fees.created_at','asc')
               ->get();
               $total_paid=[];
               foreach ($data['user'] as $id) {
                  $total_paid[$id->id]=DB::select(DB::raw("SELECT sum(amount_paid) as total_paid,transport_user_id from transport_collect_fees where transport_user_id='$id->id'")); 
               }
             
               return view(parent::loadDataToView($this->view_path.'.transport.index'), compact('data','paid','total_paid'));
    }
    public function transportDetail($id){
        $data['user']=DB::table('transport_users')->select('transport_users.id','total_rent','rt.title','vh.number','duration',DB::raw("CONCAT(st.first_name,' ',st.middle_name,' ',st.last_name) as first_name"),'from_date','to_date')
        ->where([
            ['transport_users.id','=',$id],
            ['user_type','=',2]
        ])
        ->leftjoin('routes as rt','rt.id','=','transport_users.routes_id')
               ->leftjoin('vehicles as vh','vh.id','=','transport_users.vehicles_id')
               ->leftjoin('staff as st','st.id','=','transport_users.member_id')
               ->orderBy('transport_users.id','asc')
        ->get();
         $paid=DB::table('transport_collect_fees')->select('amount_paid','pay_mode','receipt_no',DB::raw("DATE_FORMAT(transport_collect_fees.created_at,'%d-%m-%Y') as created_at"),'ref_no','transport_users.duration')
              ->where([
            ['transport_user_id','=',$id],
            ['member_type','=',2]
             ])
               ->leftjoin('transport_users','transport_users.id','=','transport_collect_fees.transport_user_id')
               ->orderBy('transport_collect_fees.created_at','asc')
               ->get();
         $total_paid=[];
               foreach ($data['user'] as $id) {
                  $total_paid=DB::select(DB::raw("SELECT sum(amount_paid) as total_paid,transport_user_id from transport_collect_fees where transport_user_id='$id->id'")); 
               }
        return view(parent::loadDataToView($this->view_path.'.transport.view'),compact('data','total_paid','paid'));
    }


    public function subject()
    {
        $this->panel = 'Course | Staff User';
        $id = auth()->user()->hook_id;
        $data = [];
        $data['subject'] = Subject::where('staff_id',$id)->orderBy('title')->get();

        return view(parent::loadDataToView($this->view_path.'.subject.index'), compact('data'));
    }

    public function notice()
    {
        $this->panel = 'Notice | Staff User';
        $data = [];
        $userRoleId = auth()->user()->roles()->first()->id;
        $data['rows'] = Notice::select('id', 'title', 'message', 'publish_date', 'end_date', 'display_group','status')
            ->where('display_group','like','%'.$userRoleId.'%')
            ->latest()
            ->get();

        return view(parent::loadDataToView($this->view_path.'.notice.index'), compact('data'));
    }


    public function attendance()
    {
        $this->panel = 'Attendance | Staff User';
        $id = auth()->user()->hook_id;
        $data = [];
        $data['attendance'] = Attendance::select('attendances.id', 'attendances.attendees_type', 'attendances.link_id',
            'attendances.years_id', 'attendances.months_id', 'attendances.day_1', 'attendances.day_2', 'attendances.day_3',
            'attendances.day_4', 'attendances.day_5', 'attendances.day_6', 'attendances.day_7', 'attendances.day_8',
            'attendances.day_9', 'attendances.day_10', 'attendances.day_11', 'attendances.day_12', 'attendances.day_13',
            'attendances.day_14', 'attendances.day_15', 'attendances.day_16', 'attendances.day_17', 'attendances.day_18',
            'attendances.day_19', 'attendances.day_20', 'attendances.day_21', 'attendances.day_22', 'attendances.day_23',
            'attendances.day_24', 'attendances.day_25', 'attendances.day_26', 'attendances.day_27', 'attendances.day_28',
            'attendances.day_29', 'attendances.day_30', 'attendances.day_31', 's.id as staff_id', 's.reg_no',
            's.first_name', 's.middle_name', 's.last_name', 's.designation')
            ->where('attendances.status', 1)
            ->where('attendances.attendees_type', 2)
            ->where('attendances.link_id',$id)
            ->join('staff as s', 's.id', '=', 'attendances.link_id')
            ->orderBy('s.id','asc')
            ->orderBy('attendances.years_id','asc')
            ->orderBy('attendances.months_id','asc')
            ->get();

        return view(parent::loadDataToView($this->view_path.'.attendance.index'), compact('data'));
    }

    /*student attendance*/

    public function addStudentAttendance(Request $request)
    {
        $this->panel = 'Student Attendance | Staff User';
        $data = [];
        $data['faculties'] = [];
        $data['faculties'][0] = 'Select Faculty';
        foreach (Faculty::select('id', 'faculty')->get() as $faculty) {
            $data['faculties'][$faculty->id] = $faculty->faculty;
        }



        return view(parent::loadDataToView($this->view_path.'.attendance.student.add'), compact('data'));
    }

    public function storeStudentAttendance(Request $request)
    {
        $response = [];
        $response['error'] = true;
        $date = Carbon::parse($request->get('date'));
        $month = Carbon::createFromFormat('Y-m-d H:i:s', $date)->month;
        $day = "day_".Carbon::createFromFormat('Y-m-d H:i:s', $date)->day;
        $yearTitle = Carbon::createFromFormat('Y-m-d H:i:s', $date)->year;
        $year = Year::where('title',$yearTitle)->first()->id;

        $attendanceStatus = AttendanceStatus::get();

        $faculty = $request->get('faculty');
        $semester = $request->get('semester_select');

        if($request->has('students_id')) {
            foreach ($request->get('students_id') as $student) {

                $attendanceExist = Attendance::select('attendances.id','attendances.attendees_type','attendances.link_id',
                    'attendances.years_id','attendances.months_id','attendances.'.$day,
                    's.id as students_id','s.reg_no','s.first_name','s.middle_name','s.last_name','s.student_image')
                    ->where('attendances.attendees_type',1)
                    ->where('attendances.years_id',$year)
                    ->where('attendances.months_id',$month)
                    ->where([['s.id', '=' , $student],['s.faculty', '=' , $faculty], ['s.semester', '=' , $semester]])
                    ->join('students as s','s.id','=','attendances.link_id')
                    ->first();

                /*get ledger exist student id*/

                if ($attendanceExist) {
                    /*Update Already Register Mark Ledger*/
                    $Update = [
                        'attendees_type' => 1,
                        'link_id' => $student,
                        'years_id' => $year,
                        'months_id' => $month,
                        $day => $request->get($student),
                        'last_updated_by' => auth()->user()->id
                    ];

                    $attendanceExist->update($Update);
                }else{
                    /*Schedule When Not Scheduled Yet*/
                    //dd($day);
                    Attendance::create([
                        'attendees_type' => 1,
                        'link_id' => $student,
                        'years_id' => $year,
                        'months_id' => $month,
                        $day => $request->get($student),
                        'created_by' => auth()->user()->id,
                    ]);

                }
            }

            /*confirmation*/
            if($request->has('send_alert') && $request->get('send_alert') ==1){
                $this->attendanceConfirm($semester,$date);
            }

            $request->session()->flash($this->message_success, $this->panel. ' Manage Successfully.');
            return redirect()->back();
        }else{
            $request->session()->flash($this->message_warning, 'You Have No Any Student to Manage Attendance. ');
            return back();
        }

    }

    public function studentHtmlRow(Request $request)
    {
        $id = auth()->user()->hook_id;
        $faculty = $request->get('faculty_id');
        $semester = $request->get('semester_id');
        $availableSemester = Semester::where('id', $semester)->where('staff_id',$id)->get();
        $availableSemesterId = array_pluck($availableSemester,'staff_id');

        if(!$availableSemesterId){
          $response['error'] = 'Student not found or you have not the right to manage attendance.';
          return response()->json(json_encode($response));
        }

        $response = [];
        $response['error'] = true;
        $date = Carbon::parse($request->get('date'));
        $month = Carbon::createFromFormat('Y-m-d H:i:s', $date)->month;
        $day = "day_".Carbon::createFromFormat('Y-m-d H:i:s', $date)->day;
        $yearTitle = Carbon::createFromFormat('Y-m-d H:i:s', $date)->year;
        $year = Year::where('title',$yearTitle)->first()->id;

        $attendanceStatus = AttendanceStatus::get();

        /*For Student List*/
        $studentCondition = [['faculty', '=' , $faculty], ['semester', '=' , $semester] ];

        $attendanceExist = Attendance::select('attendances.attendees_type','attendances.link_id',
            'attendances.years_id','attendances.months_id','attendances.'.$day,
            's.id as students_id','s.reg_no','s.first_name','s.middle_name','s.last_name','s.student_image')
            ->where('attendances.attendees_type',1)
            ->where('attendances.years_id',$year)
            ->where('attendances.months_id',$month)
            ->where($day,'<>',0)
            ->where('s.faculty', $faculty)
            ->where('s.semester',$availableSemesterId)
            ->join('students as s','s.id','=','attendances.link_id')
            ->get();

        /*get ledger exist student id*/
        $dayStatus  = array_pluck($attendanceExist, $day);
        $existStudentId  = array_pluck($attendanceExist, 'students_id');

        //Get Active Student For Related Faculty and Semester
        $activeStudent = Student::select('id','reg_no','first_name','middle_name','last_name','student_image')
            ->where($studentCondition)
            ->whereIn('semester',$availableSemesterId)
            ->whereNotIn('id',$existStudentId)
            ->Active()
            ->orderBy('id','asc')
            ->get();


        if($activeStudent->count() > 0 || $attendanceExist->count() > 0) {
            if($attendanceExist){
                $response['error'] = false;

                $response['exist'] = view($this->view_path.'.attendance.student.includes.student_tr_rows', [
                    'exist' => $attendanceExist,
                    'attendanceStatus' => $attendanceStatus,
                    'dayStatus' => $dayStatus,
                    'day' => $day,
                ])->render();

                $response['students'] = view($this->view_path.'.attendance.student.includes.student_tr', [
                    'students' => $activeStudent,
                    'attendanceStatus' => $attendanceStatus
                ])->render();

                $response['message'] = 'Active Students Found. Please, Manage Attendence.';
            }else{
                $response['error'] = false;

                $response['students'] = view($this->view_path.'.attendance.student.includes.student_tr', [
                    'students' => $activeStudent
                ])->render();

                $response['message'] = 'Active Students Found. Please, Manage Mark.';
            }
        }else{
            $response['error'] = 'Student not found or you have not the right to manage attendance.';
        }

        return response()->json(json_encode($response));
    }


    /*Send Attendance Alert*/
    public function attendanceConfirm($semester, $date)
    {
        $month = Carbon::createFromFormat('Y-m-d H:i:s', $date)->month;
        $day = "day_".Carbon::createFromFormat('Y-m-d H:i:s', $date)->day;
        $yearTitle = Carbon::createFromFormat('Y-m-d H:i:s', $date)->year;
        $year = Year::where('title',$yearTitle)->first()->id;


        $alert = AlertSetting::select('sms','email','subject','template')->where('event','=','AttendanceConfirmation')->first();
        if(!$alert)
            return back()->with($this->message_warning, "Alert Setting not Setup. Contact Admin For More Detail.");

        $sms = false;
        $email = false;

        $attendanceExist = Attendance::select('attendances.id','attendances.attendees_type','attendances.link_id',
            'attendances.years_id','attendances.months_id','attendances.'.$day,
            's.id as students_id','s.first_name as student_name','gd.guardian_first_name','gd.guardian_mobile_1','gd.guardian_email')
            ->where('attendances.attendees_type',1)
            ->where('attendances.years_id',$year)
            ->where('attendances.months_id',$month)
            ->where('s.semester', $semester)
            ->join('students as s','s.id','=','attendances.link_id')
            ->join('student_guardians as sg', 'sg.students_id','=','s.id')
            ->join('guardian_details as gd', 'gd.id', '=', 'sg.guardians_id')
            ->get();

        if($attendanceExist && $attendanceExist->count() > 0) {
            foreach ($attendanceExist as $attendance) {
                $subject = $alert->subject;
                $guardianName = $attendance->guardian_first_name;
                $guardianContact = $attendance->guardian_mobile_1;
                $guardianEmail = $attendance->guardian_email;
                $message = str_replace('{{first_name}}', $guardianName, $alert->template);
                $message = str_replace('{{status}}', $this->getAttendanceFullStatus($attendance->$day), $message);
                $message = str_replace('{{date}}', Carbon::parse($date)->format('M d , Y'), $message);

                /*Now Send SMS On First Mobile Number*/
                if ($alert->sms == 1 && isset($guardianContact)) {
                    $contactNumbers = array($guardianContact);
                    $smssuccess = $this->sendSMS($contactNumbers, $message);
                    $sms = true;
                }

                if ($alert->email == 1 && isset($guardianEmail)) {
                    $emailIds = array($guardianEmail);
                    $emailIds = $this->emailFilter($emailIds);
                    $emailSuccess = $this->sendEmail($emailIds, $subject, $message);
                    $email = true;
                }

            }

        }
    }

    /*assignment*/
    public function assignment(Request $request)
    {
        $this->panel = 'Assignment | Staff User';
        $id = auth()->user()->id;
        //dd($id);
        $data = [];
        if($request->all()) {
            $data['assignment'] = Assignment::select('id', 'created_by', 'last_updated_by', 'years_id', 'semesters_id', 'subjects_id', 'publish_date',
                'end_date', 'title', 'description', 'file', 'status')
                ->where('created_by',$id)
                ->where(function ($query) use ($request) {
                    if ($request->year && $request->year > 0) {
                        $query->where('years_id', '=', $request->year);
                        $this->filter_query['years_id'] = $request->year;
                    }

                    if ($request->semesters_id && $request->semesters_id != "" && $request->semesters_id > 0) {
                        $query->where('semesters_id', '=', $request->semesters_id);
                        $this->filter_query['semesters_id'] = $request->semesters_id;
                    }

                    if ($request->subjects_id && $request->subjects_id != "" && $request->subjects_id > 0) {
                        $query->where('subjects_id', '=', $request->subjects_id);
                        $this->filter_query['subjects_id'] = $request->subjects_id;
                    }

                    if ($request->publish_date_start != "" && $request->publish_date_end != "") {
                        $query->whereBetween('publish_date', [$request->publish_date_start, $request->publish_date_end]);
                        $this->filter_query['publish_date_start'] = $request->publish_date_start;
                        $this->filter_query['publish_date_end'] = $request->publish_date_end;
                    } elseif ($request->publish_date_start != "") {
                        $query->where('publish_date', '>=', $request->publish_date_start);
                        $this->filter_query['publish_date_start'] = $request->publish_date_start;
                    }
                })
                ->latest()
                ->get();
        }else{
            $data['assignment'] = Assignment::select('id', 'created_by', 'last_updated_by', 'years_id', 'semesters_id', 'subjects_id', 'publish_date',
                'end_date', 'title', 'description', 'file', 'status')
                ->where('created_by',$id)
                ->latest()
                ->limit(50)
                ->get();
        }


        $data['faculties'] = [];
        $data['faculties'][0] = 'Select Faculty';
        foreach (Faculty::select('id', 'faculty')->get() as $faculty) {
            $data['faculties'][$faculty->id] = $faculty->faculty;
        }

        $data['years'] = [];
        $data['years'][0] = 'Select Year';
        foreach (Year::Active()->get() as $year) {
            $data['years'][$year->id] = $year->title;
        }


        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;

        return view(parent::loadDataToView($this->view_path.'.assignment.index'), compact('data'));
    }
    public function viewTimetable(){
        $currentDate=Carbon::now()->format('Y-m-d');
        $weekday=DB::select(DB::raw("SELECT dayofweek('$currentDate') as day"));
        $weekday=$weekday[0]->day;

        $date=Carbon::now()->format('Y-m-d');
            $date=explode('-', $date);
            $month=$date[1];
            if($month[0]==0){
              $month=$month[1];
            }
            $day=$date[2];
            if($day[0]==0){
              $day=$day[1];
            }
           
        $data['timetable']=DB::table('timetable')->select('timetable.id','fcl.faculty as course',"att.day_$day as present",'timetable.course_id','timetable.section_id','sem.semester as section','sub.title as subject','timetable_subject_id as subject_id','subject_type as type',DB::raw("date_format(time_from,'%H:%i') as time_from "),DB::raw("date_format(time_to,'%H:%i') as time_to "),'room_no',DB::raw("CONCAT(st.first_name,' ',st.last_name) as staff"),'timetable.staff_id',DB::raw("CONCAT(altstf.first_name,' ',altstf.last_name) as altTeacher"),'alt.staff_id as alternate')
    ->leftjoin('attendances as att','att.link_id','=','timetable.staff_id')
    ->leftjoin('years','years.id','=','att.years_id')
    ->leftjoin('faculties as fcl','fcl.id','=','timetable.course_id')
    ->leftjoin('semesters as sem','sem.id','=','timetable.section_id')
    ->leftjoin('staff as st','st.id','=','timetable.staff_id')
    ->leftjoin('timetable_subjects as sub','sub.id','=','timetable.timetable_subject_id')
    ->leftjoin('timetable_alt_teacher as alt','alt.timetable_id','=','timetable.id')
    ->leftjoin('staff as altstf','altstf.id','=','alt.staff_id')
    
    ->where([
      ['timetable.day_id','=',$weekday],
      ['timetable.branch_id','=',Session::get('activeBranch')],
      ['timetable.session_id','=',Session::get('activeSession')],
      ['timetable.status','=',1],
      ['timetable.staff_id','=',auth()->user()->hook_id],
      ['att.attendees_type','=',2],
      ['att.months_id','=',$month],
      ['years.title','=',$date[0]],
      ['is_break','=',0],
    ])
    ->orderBy('course','Asc')
    ->orderBy('timetable.time_from','Asc')
    ->get();
    $data['alt']=DB::table('timetable_alt_teacher')->select('timetable_alt_teacher.timetable_id','tb.subject_type as type','fac.faculty as course','sem.semester as section','sub.title as subject',DB::raw("date_format(time_from,'%H:%i') as time_from "),DB::raw("date_format(time_to,'%H:%i') as time_to "),'tb.room_no')
   ->leftjoin('timetable as tb','tb.id','=','timetable_alt_teacher.timetable_id')
   ->leftjoin('faculties as fac','fac.id','=','tb.course_id')
   ->leftjoin('semesters as sem','sem.id','=','tb.section_id')
   ->leftjoin('timetable_subjects as sub','sub.id','=','tb.timetable_subject_id')
    ->where([
        ['timetable_alt_teacher.date','=',$currentDate],
        ['timetable_alt_teacher.staff_id','=',auth()->user()->hook_id]
    ])
    ->orderBy('time_from','ASC')
    ->get();
    return view(parent::loadDataToView($this->view_path.'.timetable.index'),compact('data'));
   
    }
}
