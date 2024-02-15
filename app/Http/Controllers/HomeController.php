<?php

namespace App\Http\Controllers;
use App\AssignFee, App\Collection;
use App\Charts\FeeCompareChart;
use App\Charts\FeesChart;
use App\Charts\TransactionChart;

use App\Models\AttendanceMaster;
use App\Models\Attendence;
use App\Models\AttendenceMaster;
use App\Models\Bed;
use App\Models\Book;
use App\Models\BookIssue;
use App\Models\ExamSchedule;
use App\Models\FeeCollection;
use App\Models\FeeMaster;
use App\Models\Notice;
use App\Models\SalaryPay;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Transaction;
use App\Models\Vehicle;
use App\Models\Year;
use App\Role;
use App\Traits\StudentScopes;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use ViewHelper;
use App\Branch;  use App\StudentPromotion;
use Auth, Session; use Illuminate\Http\Request; 
class HomeController extends CollegeBaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    use StudentScopes;

    protected $base_route = 'home';
    protected $view_path = 'dashboard';
    protected $panel = 'Dashboard';



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
        $org_id = Auth::user()->org_id;
        $current_session=Session::get('activeSession');
        $current_branch=Session::get('activeBranch');
        /*check user role & provide dash*/
        if(auth()->user()->hasRole('student'))
            return redirect()->route('user-student');

        if(auth()->user()->hasRole('guardian'))
            return redirect()->route('user-guardian');

        if(auth()->user()->hasRole('staff'))
            return redirect()->route('user-staff');

        /* Setup dashboard for super-admin, admin, account, library-*/
        $data = [];
        $year = Year::where('active_status','=',1)->first();
        if($year){
            $activeYear = $year->title;
        }else{
            $activeYear = '0000';
            request()->session()->flash($this->message_success, 'Please, Create Year and Active');
        }


        $userRoleId = auth()->user()->roles()->first()->id;
        /*Notice*/
        $now = date('Y-m-d');
        $data['notice_disaplay'] = Notice::select('last_updated_by', 'title', 'message',  'publish_date', 'end_date',
            'display_group', 'status')
            ->where('display_group','like','%'.$userRoleId.'%')
            ->where('publish_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->latest()
            ->get();

        /*Indicators*/
        $data['studentIndicator'] = Student::count();
        $data['staffIndicator'] = Staff::count();
        $data['feeCollectionIndicator'] = FeeCollection::sum('paid_amount');
        $data['salaryPayIndicator'] = SalaryPay::sum('paid_amount');

        /*Accounting*/
        $data['recent_fees_collection'] = FeeCollection::select('fee_collections.date','fee_collections.students_id', 'fee_collections.fee_masters_id',
            'fee_collections.paid_amount', 's.reg_no', 'fh.fee_head_title')
            ->join('students as s','s.id','=','fee_collections.students_id')
            ->join('fee_masters as fm','fm.id','=','fee_collections.fee_masters_id')
            ->join('fee_heads as fh', 'fh.id', '=','fm.fee_head')
            ->latest('fee_collections.created_at')
            ->limit(10)
            ->get();

        $data['recent_payroll_pay'] = SalaryPay::select('salary_pays.date','salary_pays.staff_id',
            'salary_pays.salary_masters_id', 'salary_pays.paid_amount', 's.reg_no', 'ph.title')
            ->join('staff as s','s.id','=','salary_pays.staff_id')
            ->join('payroll_masters as pm','pm.id','=','salary_pays.salary_masters_id')
            ->join('payroll_heads as ph', 'ph.id', '=','pm.payroll_head')
            ->latest('salary_pays.created_at')
            ->where('salary_pays.status',1)
            ->limit(10)
            ->get();

        $data['recent_transaction'] = Transaction::select('transactions.date', 'transactions.tr_head_id',
            'transactions.dr_amount','transactions.cr_amount', 'th.tr_head')
            ->join('transaction_heads as th','th.id','=','transactions.tr_head_id')
            ->latest('transactions.created_at')
            ->limit(10)
            ->get();

        /*Library*/
        $data['book_issued'] = BookIssue::select('book_issues.member_id','book_issues.issued_on', 'book_issues.due_date',
            'b.book_code', 'bm.id as bookmaster_id','bm.title', 'lm.member_id as lib_id','lm.user_type')
            ->where('book_issues.return_date',null)
            ->latest('book_issues.created_at')
            ->join('books as b','b.id','=','book_issues.book_id')
            ->join('book_masters as bm','bm.id','=','b.book_masters_id')
            ->join('library_members as lm','lm.id','=','book_issues.member_id')
            ->get();

        $data['book_return_over'] = BookIssue::select('book_issues.member_id','book_issues.issued_on', 'book_issues.due_date',
            'b.book_code', 'bm.id as bookmaster_id','bm.title', 'lm.member_id as lib_id', 'lm.user_type')
            ->where('book_issues.status','=',1)
            ->where('book_issues.due_date',"<", Carbon::now())
            ->join('books as b','b.id','=','book_issues.book_id')
            ->join('book_masters as bm','bm.id','=','b.book_masters_id')
            ->join('library_members as lm','lm.id','=','book_issues.member_id')
            ->get();

        /*Attendence*/
        $data['attendance_booklet'] = AttendanceMaster::select('id', 'year', 'month', 'day_in_month','holiday','open','status')
            ->limit(12)
            ->orderBy('year','desc')
            ->orderBy('month', 'asc')
            ->get();

        /*FOR Summary Right WIDGET*/
        $data['student_active_status'] = Student::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        /*$data['academic_status_count'] = Student::select('academic_status', DB::raw('count(*) as total'))
            ->groupBy('academic_status')
            ->get();*/
        $data['academic_status_count']=StudentPromotion::select('students.branch_id'
            , 'student_detail_sessionwise.course_id'
            , 'student_detail_sessionwise.student_id'
            , 'student_detail_sessionwise.session_id'
            , 'student_detail_sessionwise.id')
            ->leftjoin('students', function($q){
    $q->on('student_detail_sessionwise.student_id', '=', 'students.id');
                })
            ->where('students.branch_id', Session::get('activeBranch'))
            ->where('student_detail_sessionwise.session_id', $current_session)->groupBy('student_detail_sessionwise.id')
            ->where('students.status',1)->get();

        $data['staff_status'] = Staff::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        $data['books_status'] = Book::select('book_status', DB::raw('count(*) as total'))
            ->groupBy('book_status')
            ->get();

        $data['bed_status'] = Bed::select('bed_status', DB::raw('count(*) as total'))
            ->groupBy('bed_status')
            ->get();

        $data['transport_status'] = Vehicle::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        $data['exams_status'] = ExamSchedule::select('years_id', 'months_id', 'exams_id', 'faculty_id', 'semesters_id', 'status')
            ->groupBy('years_id', 'months_id', 'exams_id', 'faculty_id', 'semesters_id', 'status')
            ->orderBy('years_id', 'desc')
            ->orderBy('months_id', 'asc')
            ->limit (10)
            ->get()
            ->count();

        
        /*Fees chart*/
        $monthBase = [0,0,0,0,0,0,0,0,0,0,0,0];
        $feeMonthly = DB::table('collect_fee')
            ->select(DB::raw('MONTH(collect_fee.reciept_date) as month'),DB::raw('YEAR(collect_fee.reciept_date) as year'), DB::raw('sum(amount_paid) as total'))
            ->leftjoin('students as st','st.id','=','collect_fee.student_id')
            ->leftjoin('assign_fee', function($q){
                $q->on('collect_fee.assign_fee_id', '=', 'assign_fee.id');
            })->where('assign_fee.branch_id', Session::get('activeBranch'))
            ->where('collect_fee.status', '1')
            ->where('assign_fee.status','1')
            ->where('st.status',1)
            ->where('assign_fee.session_id', Session::get('activeSession')) 
            // ->whereYear('collect_fee.reciept_date', '=', $activeYear) 
            ->groupBy(DB::raw('YEAR(collect_fee.reciept_date)') , DB::raw('MONTH(collect_fee.reciept_date)') )
            ->get();
            $tt = 0;
        foreach ($feeMonthly as $value){
            $tt = $tt + $value->total;
            $monthBase = data_set($monthBase,$value->month-1,$value->total);
        }
       
        $feeMonthly = $monthBase;
       
        /*Salary Chart*/
        $monthBase = [0,0,0,0,0,0,0,0,0,0,0,0];
        $salaryMonthly = DB::table('salary_pays')
            ->select(DB::raw('MONTH(date) as month'), DB::raw('sum(paid_amount) as total'))
            ->whereYear('date', '=', $activeYear)
            ->groupBy(DB::raw('MONTH(date)') )
            ->get();

        foreach ($salaryMonthly as $value){
            $monthBase = data_set($monthBase,$value->month-1,$value->total);
        }
        $salaryMonthly = $monthBase;

        /*fee salary chart*/
        $data['feeSalaryChart'] = new FeesChart();
        $data['feeSalaryChart']->dataset('Fee Collection', 'bar', $feeMonthly)
            ->options(['borderColor' => '#46b8da', 'backgroundColor'=>'#46b8da' ]);

        $data['feeSalaryChart']->dataset('Salary', 'bar', $salaryMonthly)
            ->options(['borderColor' => '#FF6384', 'backgroundColor'=>'#FF6384']);


/////total fees collection and due compare start////////
    //         $student_session=StudentPromotion::select('students.branch_id'
    //         , 'student_detail_sessionwise.course_id'
    //         , 'student_detail_sessionwise.student_id'
    //         , 'student_detail_sessionwise.session_id'
    //         , 'student_detail_sessionwise.id')
    //         ->leftjoin('students', function($q){
    // $q->on('student_detail_sessionwise.student_id', '=', 'students.id');
    //             })
    //         ->where('students.branch_id', Session::get('activeBranch'))
    //         ->where('student_detail_sessionwise.session_id', $current_session)->groupBy('student_detail_sessionwise.id')->get();
    //         $feeMaster = 0;
            
    //         foreach($student_session as $stu_ssn){
    //             $feeMaster += $this->master_fee($stu_ssn->student_id, $stu_ssn->course_id);
    //         }
    //         $feeCollection = Collection::select('collect_fee.amount_paid', 'assign_fee.session_id', 'assign_fee.branch_id', 'collect_fee.status')
    //         ->leftjoin('assign_fee', function($q){
    //             $q->on('collect_fee.assign_fee_id', '=', 'assign_fee.id');
    //         })->where('assign_fee.branch_id', Session::get('activeBranch'))
    //         ->where('collect_fee.status', '1')
    //         ->where('assign_fee.session_id', $current_session)
    //         ->sum('amount_paid');
    //         //die($feeMaster." - ".$feeCollection);
    //         $dueFee = $feeMaster - $feeCollection;
           //my fees collection code 
            $branchId=Session::get('activeBranch');
            $sessionId=Session::get('activeSession');
           $data['assigned']=DB::table('assign_fee')->select('fee_amount','course_id','student_id','id')
           ->where([
            ['branch_id','=',$branchId],
            ['session_id','=',$sessionId],
            ['status','=','1']
            ]) 
           ->get();
          
           $fee=0;
           $paid=0;
           foreach ($data['assigned'] as $key => $value) {
             if($value->student_id==0){
                 $count=DB::table('students')->select(DB::raw('COUNT(students.id) as ct'))
                 ->leftjoin('student_detail_sessionwise as st','st.student_id','=','students.id')
                 ->where([
                    ['students.branch_id','=',$branchId],
                    ['st.session_id','=',$sessionId],
                    ['st.course_id','=',$value->course_id],
                    ['students.status','=',1]
                 ])
                 ->get();
              $ct=$count[0]->ct;
              $fee=$fee+($value->fee_amount * $ct);
             }
             else{
                $fee=$fee+$value->fee_amount;
             }
             $assign=$value->id;
                 $collectAmt=DB::table('collect_fee')
                 ->leftjoin('students as st','st.id','=','collect_fee.student_id')
                 ->where([
                    ['assign_fee_id','=',$assign],
                    ['collect_fee.status','=',1],
                    ['st.status','=',1]
                 ])
                 ->sum('amount_paid');
                 $paid=$paid+$collectAmt;
                 
           }
           $feeMaster=$fee;
           $feeCollection=$paid;
            $dueFee = $feeMaster - $feeCollection;
           

            /*chart*/
            $data['feeCompare'] = new FeeCompareChart();
            $data['feeCompare']->dataset('Income', 'doughnut',[$feeCollection, $dueFee])
                ->options(['borderColor' => '#46b8da', 'backgroundColor'=>['#46b8da','#FF6384'] ]);
/////total fees collection and due compare end////////

        /*Transaction Chart*/
        $monthBase = [0,0,0,0,0,0,0,0,0,0,0,0];
        $drTransaction = DB::table('transactions')
            ->select(DB::raw('MONTH(date) as month'), DB::raw('sum(dr_amount) as total'))
            ->whereYear('date', '=', $activeYear)
            ->groupBy(DB::raw('MONTH(date)') )
            ->get();

        foreach ($drTransaction as $value){
            $monthBase = data_set($monthBase,$value->month-1,$value->total);
        }
        $drTransaction = $monthBase;

        /*cr*/
        $monthBase = [0,0,0,0,0,0,0,0,0,0,0,0];
        $crTransaction = DB::table('transactions')
            ->select(DB::raw('MONTH(date) as month'), DB::raw('sum(cr_amount) as total'))
            ->whereYear('date', '=', $activeYear)
            ->groupBy(DB::raw('MONTH(date)') )
            ->get();
        foreach ($crTransaction as $value){
            $monthBase = data_set($monthBase,$value->month-1,$value->total);
        }
        $crTransaction = $monthBase;

        $data['transactionChart'] = new TransactionChart();
        $data['transactionChart']->dataset('Income', 'line',$drTransaction)
            ->options(['borderColor' => '#46b8da', 'backgroundColor'=>'transparent' ]);
        $data['transactionChart']->dataset('Expenses', 'line',$crTransaction)
            ->options(['borderColor' => '#FF6384', 'backgroundColor'=>'transparent' ]);
        //$branches= Branch:: select('branch_name','branch_title','id','org_id')->where('org_id', $org_id)->get()->toArray();
            //return $branches;

       return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));

    }


    public function switchbranch(Request $request,$id)
    {
        $org_id = Auth::user()->org_id;
        $branch = DB::table('branches')->where('id',$id)->first();
        Session::put('activeBranch', $id);
        $isCourseBatch = DB::table('sessionwise_branch_batch')->select('is_course_batch')->where([
            ['session_id','=',Session::get('activeSession')],
            ['branch_id','=',$id]
        ])->first();
        if(isset($isCourseBatch)){
            if($isCourseBatch->is_course_batch){
                Session::put('isCourseBatch','1');
            }else{
                Session::put('isCourseBatch','0');
            }
        }
        else{
            Session::put('isCourseBatch','0');
        }
        //$branch_id = Auth::user()->branch_id;
        //return $branch_id;
        //return $branch_id;
        //$user = User::select('branch_id','org_id')->where('branch_id',);
        /*$user= DB::table('users')
             ->where("users.branch_id", '=', $branch_id)
            ->update(['users.branch_id'=> $id]);
    */
        return redirect()->route('home')->with('branchmsg', 'College changed');
    }
    
    public function switch_session(Request $req, $id){
        
        Session::put('activeSession', $id);
        $isCourseBatch = DB::table('sessionwise_branch_batch')->select('is_course_batch')->where([
            ['session_id','=',$id],
            ['branch_id','=',Session::get('activeBranch')]
        ])->first();
        if(isset($isCourseBatch)){
            if($isCourseBatch->is_course_batch){
                Session::put('isCourseBatch','1');
            }else{
                Session::put('isCourseBatch','0');
            }
        }
        else{
            Session::put('isCourseBatch','0');
        }
        return redirect()->route('home')->with('branchmsg', 'Session Changed Successfully.');
    }
}
