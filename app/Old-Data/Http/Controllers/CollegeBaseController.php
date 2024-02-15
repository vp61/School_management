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

    protected function reciept_no($id){

        $recpt="AESR"; $cnt=1;
        //$row = DB::table('collect_fee')->count();
        // $row = DB::table('collect_fee')->select('reciept_no')->orderby('id','desc')->first();

        // if(!empty($row)){
        //     $lastReceipt = trim($row->reciept_no);
        //     $maxVal = str_replace("AES","",$lastReceipt);
        //     $cnt = $maxVal+$cnt; 
        //     $recpt .= $cnt; //dd($recpt);
        // }else{ $recpt .= 10000+$cnt; }
        // $data=DB::table('collect_fee')->where('reciept_no','=',$recpt)->get();
        // if(count($data)){
        //     $this->reciept_no();
        // }else{ return $recpt; }
        $recpt .=10000+$id;
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

    protected function reG_no($id){
        $recpt="AES"; $cnt=1; 
        // $row = DB::table('students')->select('reg_no')->orderby('reg_no','desc')->first();
        // if(!empty($row)){
        //     $lastReceipt = trim($row->reg_no);
        //     $maxVal = str_replace("AES","",$lastReceipt);
        //     $cnt = $maxVal+$cnt; 
        //     $recpt .= $cnt; //dd($recpt);
        // }else{
        //     $recpt .= $cnt+10000;
        // }
        $recpt .=10000+$id;
        return $recpt;
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