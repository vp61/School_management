<?php

namespace App\Http\Controllers;
use DB;
use App\Models\GeneralSetting;
use App\Models\PaymentSetting;
use App\Models\SmsSetting;

use App\Traits\AccountingScope;
use App\Traits\CommonScope;
use App\Traits\DateTimeScope;
use App\Traits\ExaminationScope;
use App\Traits\FacultySemesterScope;
use App\Traits\StaffScope;
use App\Traits\StudentScopes;
use App\Traits\TransportScope;
use App\Traits\UploadScope;

use View; use App\Branch; 
use App\AssignFee;
use AppHelper, Image;
use Session;
use Carbon\Carbon;
use App\Models\Student;
class CollegeBaseController extends Controller
{
    public $pagination_limit = 10;
    protected $message_success = 'message_success';
    protected $message_warning = 'message_warning';
    protected $message_danger = 'message_danger';
    public $internet_status = 'There is no Internet connection. Please Check the network cables, modem, and router.';

    /*Traits*/
    use CommonScope;
    use StudentScopes;
    use StaffScope;
    use DateTimeScope;
    use AccountingScope;
    use UploadScope;
    use ExaminationScope;
    use FacultySemesterScope;
    use TransportScope;

    protected function loadDataToView($view_path)
    {
        View::composer($view_path, function ($view) {
            $view->with('base_route', $this->base_route);
            $view->with('view_path', $this->view_path);
            $view->with('panel', $this->panel);
            $view->with('generalSetting', $this->getGeneralSetting());
            $view->with('paymentSetting', $this->getPaymentSetting());
            $view->with('smsSetting', $this->getSmsSetting());
            $view->with('folder_name', property_exists($this, 'folder_name')?$this->folder_name:'');

        });

        return $view_path;
    }

    /*check internet connection*/
    public function checkConnection()
    {
        $connected = @fsockopen("www.google.com", 80); //website, port  (try 80 or 443)
        if ($connected){
            return true;
        }
        return false;
    }

    protected function invalidRequest($message = 'Invalid request!!')
    {
        request()->session()->flash($this->message_warning, $message);
        return redirect()->route($this->base_route);
    }

    // protected function reciept_no($id){

    //     $recpt="SPSR"; $cnt=1; 
    //     $recpt .=10000+$id;
    //     return $recpt;
    
    // }    

    protected function reciept_no($id='',$receipt_ids=array()){
        $receiptPreFix = env('PREFIX_RCP');
        if(isset($receipt_ids[0]) && $receipt_ids[0] != ""){
            $currentAutoId = $receipt_ids[0];
            $ssn_tble=DB::table('collect_fee')->select('id', 'reciept_no')
            ->where('id','<', $currentAutoId)
            ->where('reciept_no','LIKE',$receiptPreFix."%")
            ->orderBy('reciept_no','desc')
            ->first();
            $current_session= isset($ssn_tble->reciept_no)?$ssn_tble->reciept_no : ''; 
            if($current_session == ''){
                $recpt = $receiptPreFix.'10001';
            }else{
                $lastReceiptNo = str_replace($receiptPreFix, "", $current_session);
                $recpt = $receiptPreFix.($lastReceiptNo+1);
            }
        }else{
            $recpt=$receiptPreFix; $cnt=1;
            $recpt .=10000+$id;
        }
        return $recpt;
    
    }

    protected function master_fee($student, $course){
        if($student){
            $indiv_fee = AssignFee::where('session_id', Session::get('activeSession'))
            ->where('branch_id', Session::get('activeBranch'))
            ->where('student_id', $student)
            ->sum('fee_amount');
        }else{ $indiv_fee=0; }
        
        $course_fee = AssignFee::where('session_id', Session::get('activeSession'))
        ->where('branch_id', Session::get('activeBranch'))
        ->where('course_id', $course)
        ->sum('fee_amount');
       return $indiv_fee + $course_fee;  
    }

    protected function reG_no($id='',$faculty='',$ssn=''){
        $session = ($ssn!='')?$ssn : Session::get('activeSession');
        if($id !=''){
            $std_reg_no=Student::find($id);

            if($std_reg_no->reg_no){
                $recpt=$std_reg_no->reg_no;
            }
            else{
                $year=  $session; //Carbon::now()->format('y');
                $data=Student::select('*','fac.short_name','fac.code')
                ->leftjoin('faculties as fac','fac.id','=','students.faculty')
                ->where([
                    ['students.faculty','=',$std_reg_no->faculty],
                    ['students.session_id','=',$session],
                    ['students.branch_id','=',Session::get('activeBranch')]
                ])->get();
                $faculty = DB::table('faculties')->select('*')->where('id',$std_reg_no->faculty)->first();

                $count=str_pad(count($data),4,'0',STR_PAD_LEFT);
                $shortCode =(isset($faculty->short_name))? $faculty->short_name :'';
               
                $sessionyrData = $this->getActiveYearSessionName($year);
                $ssTitle = (isset($sessionyrData[0]->session_name))? $sessionyrData[0]->session_name.'/' : '';
                $recpt=env('PREFIX_REG').'/'.$ssTitle.$shortCode.'/'.($count+50);
            }
        }
        else{
            $data=Student::select('*','fac.short_name','fac.code')
            ->leftjoin('faculties as fac','fac.id','=','students.faculty')
            ->where([
                ['students.faculty','=',$faculty],
                ['students.session_id','=',$session],
                ['students.branch_id','=',Session::get('activeBranch')]
            ])->get();
            $faculty = DB::table('faculties')->select('*')->where('id',$faculty)->first();

            $count=str_pad(count($data),4,'0',STR_PAD_LEFT);
            $shortCode =(isset($faculty->short_name))? $faculty->short_name :'';
            $year = $session; //Carbon::now()->format('y');
            $sessionyrData = $this->getActiveYearSessionName($year);
            $ssTitle = (isset($sessionyrData[0]->session_name))? $sessionyrData[0]->session_name.'/' : '';
            $recpt=env('PREFIX_REG').'/'.$ssTitle.$shortCode.'/'.($count+50);
        }

        return $recpt;
    }
    
    protected function getActiveYearSessionName($id='')
    {
        if($id!=''){
            $branch= DB::table('session')->select('session_name')->where('id', $id)->get();
        }else{
            $branch= DB::table('session')->select('session_name')->get();    
        }
        
        return $branch;
    }

    protected function getGeneralSetting()
    {
        $data['general_setting'] = GeneralSetting::select('institute', 'salogan', 'address','phone','email', 'website', 'favicon', 'logo',
            'print_header', 'print_footer')->first();
        if(isset($data['general_setting']) && $data['general_setting']->count() > 0){
            return $data['general_setting'];
        }else{
            request()->session()->flash($this->message_warning, 'Please, Setup your institution detail or contact your system administrator');
            return redirect()->route('home');
        }
    }

    protected function getPaymentSetting()
    {
        $data['payment_setting'] = PaymentSetting::where('status',1)->get();
        if(isset($data['payment_setting']) && $data['payment_setting']->count() > 0){
            $d = json_decode($data['payment_setting'],true);
            $manageSetting = array_pluck($d,'config','identity');
            return $manageSetting;
        }
    }


    protected function getSmsSetting()
    {
        $data['sms_setting'] = SmsSetting::where('status',1)->get();
        if(isset($data['sms_setting']) && $data['sms_setting']->count() > 0){
            $d = json_decode($data['sms_setting'],true);
            $manageSetting = array_pluck($d,'config','identity');
            return $manageSetting;
        }
    }

    protected function course_drop(){
        $branch_id=Session::get('activeBranch');
        $course_list=DB::table('faculties')->select('id', 'faculty')->where('branch_id', $branch_id)->pluck('faculty', 'id')->toArray();
        return $course_list;
    }

    protected function branch_drop(){
        $branch_list = DB::table('branches')->select('id', 'branch_name')->pluck('branch_name', 'id')->toArray();
        return $branch_list;
    }

    public static function get_branch_name(){
        if(Session::has('activeBranch')){
            $branch_id = Session::get('activeBranch');
        }else{
            $branch_id = Auth::user()->branch_id; Session::put('activeBranch', $branch_id);
        }
        $branch= Branch::select('branch_name','branch_title','id','org_id')->where('id', $branch_id)->get();
        return $branch[0]->branch_name;
    }
    
}