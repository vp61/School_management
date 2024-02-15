<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\CollegeBaseController; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Models\Faculty;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Year;
use App\Fee_model;
use App\AssignFee;
use App\Collection;
use App\StudentPromotion;
use App\User;
use Auth, DB;
use Log;
use Session;
use Carbon;
use Response;
use ViewHelper;
use App\Models\AcademicInfo;
use App\Models\AssignmentAnswer;
use App\Models\Addressinfo;
use App\Models\AlertSetting;
use App\Models\Attendance;
use App\Models\Attendence;
use App\Models\Document;
use App\Models\GuardianDetail;
use App\category_model;
use App\Models\LibraryMember;
use App\Models\Note;
use App\Models\ParentDetail;
use App\Models\ResidentHistory;
use App\Models\LiveClass;
use Illuminate\Support\Str; 
use App\Models\TeacherCoordinator;
use App\Models\Exam\ExamCreate;

class ServicesController extends CollegeBaseController
{
    public function __construct()
    {

    }

    public function return_data_in_json($data,$error_msg=""){
        $error_msg= ($error_msg!="") ? $error_msg : "Please try again.";
        if (!$data) 
        { 
            return Response::json( [
                'status' =>[0],  
                'data' =>[ ]
            ], 404);
        }else{
            return Response::json([
                 'status' =>[1],
                 'data' => $data
            ],200);
        }
    }

   public function asha_login(Request $request)
    {  
        Log::useDailyFiles(storage_path().'/logs/login_log.log');
        Log::info('Login Data--');
        Log::info($request->all());
        Log::info('Login Data END---');
        $data   = [];
        $email  = isset($request->email)? $request->email : "";
        $pass   = isset($request->pass)? $request->pass : "";
        $session = isset($request->session_id)? $request->session_id : "";
        $branch = isset($request->branch_id)? $request->branch_id : "";
        if($email != "" && $pass !="")
        {
            $user   = User::where([

                ['email','=',$email],
                ['status','=',1],
            ])->first();
            
            // dd($user);
            $stdId  = trim($user->hook_id);
            $rollId = trim($user->role_id);
            
            $status = Hash::check($pass, $user->password);
            // $rollId = 6 means student
            if($status && $rollId==6){
                // Get User info
                $userType   = 'Student';
                if($stdId != ""){
                    $data= $this->get_dash_board_info($email="",$stdId,$userType,$rollId,$session,$branch);
                }else{
                    $data= $this->get_dash_board_info($email,$stdId="",$userType,$rollId,$session,$branch);
                }
            }else if($status && $rollId!=6 && $rollId!=1 && $rollId!=2 && $rollId!=3){
                // Staff profile details
                $userType   = 'Staff';
                $data= $this->get_Staff_Details($stdId,$userType,$rollId);
            }
             else if($status && ($rollId==1 || $rollId==2 || $rollId==3)){
                // Admin profile details
                $userType   = 'Admin';
                $data= $this->get_Staff_Details($stdId,$userType,$rollId);
                
            }
        }
        return $data;
    }

    public function get_dash_board_info($emailId="",$uIds ="",$userType="",$rollId="",$session="",$branch="")
    {   
        // Get data by Email or Id
        $data = [];
        $data['userType']   = $userType;
        $data['rollId']     = $rollId;
        $emailGet=(isset($request->email) && $request->email!="")? $request->email : $emailId;
        
        $email = ($emailId != "")? $emailId : $emailGet;
        $uIds=(isset($request->uid) && $request->uid!="")? $request->uid : $uIds;

        $fileLink = asset('images'.DIRECTORY_SEPARATOR.'studentProfile');
        $fileLink = addslashes($fileLink).'/';

        if($uIds!="")
        {            
            
            $data['info']   = Student::select('students.id','students.created_at','reg_no','reg_date','faculty','sts.course_id as faculty','students.semester','sts.Semester as semester','academic_status','first_name','date_of_birth','gender','blood_group','nationality','mother_tongue','students.email','extra_info',DB::raw("CONCAT('$fileLink',student_image) as student_image"),'students.status','students.branch_id','students.org_id','category_id','sts.session_id','zip','indose_number','passport_no','Merchant_Key as passkey','ai.mobile_1','users.id as user_id')
                ->leftJoin('student_detail_sessionwise as sts','sts.student_id','=','students.id')
                ->leftJoin('branches','students.branch_id', '=', 'branches.id')
                ->leftjoin('users',function($j){
                    $j->on('users.hook_id','=','students.id')
                    ->where('users.role_id',6);
                })
                ->leftjoin('addressinfos as ai', 'students.id', '=', 'ai.students_id')
                ->where('sts.session_id',$session)
                ->where('students.id',$uIds)
                ->where('students.branch_id',$branch)
                
                ->first(); 
        } 
        else
        { 
            $data['info']   = Student::select('students.id','students.created_at','reg_no','reg_date','faculty','sts.course_id as faculty','students.semester','sts.Semester as semester','academic_status','first_name','date_of_birth','gender','blood_group','nationality','mother_tongue','students.email','extra_info',DB::raw("CONCAT('$fileLink',student_image) as student_image"),'students.status','students.branch_id','students.org_id','category_id','sts.session_id','zip','indose_number','passport_no','Merchant_Key as passkey','ai.mobile_1','users.id as user_id')
                ->leftJoin('student_detail_sessionwise as sts','sts.student_id','=','students.id')
                ->leftJoin('branches','students.branch_id', '=', 'branches.id')
                ->leftjoin('users',function($j){
                    $j->on('users.hook_id','=','students.id')
                    ->where('users.role_id',6);
                })
                ->leftjoin('addressinfos as ai', 'students.id', '=', 'ai.students_id')
                ->where('email',$email)
                ->where('students.branch_id',$branch)
                ->first(); 
        }   

        return $this->return_data_in_json($data,$error_msg="");   
    }

    public function get_student_profile(Request $request,$stdId="")
    {
        $data = [];
        $uIds=(isset($_GET['uid']) && $_GET['uid']!="")? $_GET['uid'] : $stdId;
        
        if($uIds==""){
            return $data;
        }

        $fileLink = asset('images'.DIRECTORY_SEPARATOR.'studentProfile');
        $fileLink = addslashes($fileLink).'/';
        $data['student'] = StudentPromotion::select('students.id','students.reg_no', 'students.reg_date', DB::raw("DATE_FORMAT(students.reg_date,'%d-%m-%Y') as reg_date"),
            'students.branch_id','branches.branch_name',
            'students.category_id','category.category_name','religions.title as religion',
            'students.session_id','session.session_name',
            'student_detail_sessionwise.course_id','faculties.faculty as course_name','student_detail_sessionwise.semester','semesters.semester as section_semester', 
            'students.academic_status', 'students.first_name', 'students.date_of_birth',DB::raw("DATE_FORMAT(students.date_of_birth,'%d-%m-%Y') as date_of_birth"), 'students.gender', 'students.blood_group', 'students.nationality',
            'students.mother_tongue', 'students.email', 'students.extra_info', DB::raw("CONCAT('$fileLink',students.student_image) as student_image"), 'students.status','pd.father_first_name', 'pd.father_middle_name',
            'pd.father_last_name', 'pd.father_eligibility', 'pd.father_occupation', 'pd.father_mobile_1', 'pd.father_email', 'pd.mother_first_name','pd.mother_middle_name', 'pd.mother_last_name', 'pd.mother_eligibility', 'pd.mother_occupation','pd.mother_mobile_1', 'pd.mother_email',
            'ai.address', 'ai.state', 'ai.country','ai.home_phone',
            'ai.mobile_1', 'ai.mobile_2', 'gd.id as guardian_id', 'gd.guardian_email','gd.guardian_first_name', 'gd.guardian_middle_name', 'gd.guardian_last_name',
            'gd.guardian_eligibility', 'gd.guardian_occupation',
            'gd.guardian_mobile_1', 'gd.guardian_mobile_2', 'gd.guardian_email', 'gd.guardian_relation', 'gd.guardian_address')

            //->where('student_detail_sessionwise.session_id', $current_session_id)
            ->where('students.id','=',$uIds)
            ->where(function($q)use($request){
                if($request->session_id){
                    $q->where('student_detail_sessionwise.session_id',$request->session_id);
                }
            })
            ->leftJoin('students','student_detail_sessionwise.student_id', '=', 'students.id')
            ->leftjoin('parent_details as pd', 'pd.students_id', '=', 'students.id')
            ->leftjoin('addressinfos as ai', 'ai.students_id', '=', 'students.id')
            ->leftjoin('student_guardians as sg', 'sg.students_id','=','students.id')
            ->leftjoin('guardian_details as gd', 'gd.id', '=', 'sg.guardians_id')
            ->leftJoin('faculties','student_detail_sessionwise.course_id', '=', 'faculties.id')
            ->leftJoin('semesters','student_detail_sessionwise.semester', '=', 'semesters.id')
            ->leftJoin('session','student_detail_sessionwise.session_id', '=', 'session.id')
            ->leftJoin('category','students.category_id', '=', 'category.id')
            ->leftJoin('branches','students.branch_id', '=', 'branches.id')
            ->leftJoin('religions','religions.id','=','students.religion_id')
            ->first();
        return $this->return_data_in_json($data,$error_msg="");
    }

    public function get_student_academic($stdId="")
    {
        $data = [];
        $uIds=(isset($_GET['uid']) && $_GET['uid']!="")? $_GET['uid'] : $stdId;
        
        if($uIds==""){
            return $data;
        }
        $data['academicInfos'] =AcademicInfo::select('*')->Where('students_id', $uIds)->orderBy('sorting_order','asc')->get();

        return $this->return_data_in_json($data,$error_msg="");
    }

    public function get_student_note($stdId="")
    {
        $data = [];
        $uIds=(isset($_GET['uid']) && $_GET['uid']!="")? $_GET['uid'] : $stdId;
        
        if($uIds==""){
            return $data;
        }

        $data['note'] = Note::select('created_at', 'id', 'member_type','member_id','subject', 'note', 'status')
            ->where('member_type','=','student')
            ->where('member_id','=', $uIds)
            ->orderBy('created_at','desc')
            ->get();
        return $this->return_data_in_json($data,$error_msg="");
    }
    
    public function get_student_document($stdId="")
    {
        $data = [];
        $uIds=(isset($_GET['uid']) && $_GET['uid']!="")? $_GET['uid'] : $stdId;
        
        if($uIds==""){
            return $data;
        }
       $fileLink = asset('documents'.DIRECTORY_SEPARATOR.'student'.DIRECTORY_SEPARATOR.ViewHelper::getStudentById( $uIds ).DIRECTORY_SEPARATOR);
       $fileLink = addslashes($fileLink)."/";
    //   dd($fileLink);

        $data['document'] = Document::select('id', 'member_type','member_id', 'title',DB::raw("CONCAT('$fileLink',file) as file_url"),'description', 'status')
            ->where('member_type','=','student')
            ->where('member_id','=',$uIds)
            ->orderBy('created_by','desc')
            ->get();
        return $this->return_data_in_json($data,$error_msg="");
    }

    public function get_student_session_list($stdId="",$ssId="")
    {
        $data = [];
        $uIds=(isset($_GET['uid']) && $_GET['uid']!="")? $_GET['uid'] : $stdId;
        $ssId=(isset($_GET['ssId']) && $_GET['ssId']!="")? $_GET['ssId'] : $ssId;
        
        if($uIds==""){ return $data; }
        $data['student_session'] = StudentPromotion::select('student_detail_sessionwise.course_id','faculties.faculty as course',
            'student_detail_sessionwise.semester','semesters.semester',
            'student_detail_sessionwise.session_id','session.session_name',
            'student_detail_sessionwise.Status' 
            )
            ->leftJoin('students','student_detail_sessionwise.student_id', '=', 'students.id')
            ->leftJoin('faculties','student_detail_sessionwise.course_id', '=', 'faculties.id')
            ->leftJoin('semesters','student_detail_sessionwise.semester', '=', 'semesters.id')
            ->leftJoin('session','student_detail_sessionwise.session_id', '=', 'session.id')
            // ->where('student_detail_sessionwise.session_id', $ssId)
            ->where('students.id','=',$uIds)
            ->orderBy('student_detail_sessionwise.id','desc')
            ->get();

        return $this->return_data_in_json($data,$error_msg="");
    }

    public function get_student_fee_head_list($stdId="",$ssId="",$courseId="",Request $request)
    {
        
        $data = [];
        $uIds=(isset($_GET['uid']) && $_GET['uid']!="")? $_GET['uid'] : $stdId;
        $ssId=(isset($_GET['ssId']) && $_GET['ssId']!="")? $_GET['ssId'] : $ssId;
        $courseId=(isset($_GET['courseId']) && $_GET['courseId']!="")? $_GET['courseId'] : $courseId;
        
        if($uIds==""){
            return $data;
        }
        
        $fee_result=AssignFee::Select('assign_fee.*', 'fee_heads.fee_head_title',DB::raw("COALESCE(m.title,'0') as month_name"))
        ->leftJoin('fee_heads', function($join){
            $join->on('fee_heads.id', '=', 'assign_fee.fee_head_id');
        })
        ->leftJoin('months as m','m.id','=','assign_fee.due_month')
        ->where('assign_fee.session_id', $ssId)
        ->where('assign_fee.course_id', $courseId)
        ->Where('assign_fee.student_id', '0')
        ->Where('assign_fee.status', 1)
        ->orWhere(function($q) use ($uIds,$ssId,$courseId){
            if($uIds){
                $q->orWhere('assign_fee.student_id', $uIds)
                
                ->where('assign_fee.session_id', $ssId)
                ->where('assign_fee.course_id', $courseId);
            }
        })
        ->orderByRaw("FIELD(assign_fee.due_month, '4','5','6','7','8','9','10','11','12','1','2','3') ASC")
        ->groupBy('assign_fee.id')->get();

        $ret_val=""; $i=0; 

        $arrFeeMaster = [];
        
        if(count($fee_result) && $uIds){$k=0;
            foreach($fee_result as $fee){ $k++;
                $paid_result=DB::table('collect_fee')->where('assign_fee_id', $fee->id)->where('student_id', $uIds)
                ->Where('status' , 1)->sum('amount_paid');
                 $discount_result=DB::table('collect_fee')->where('assign_fee_id', $fee->id)->where('student_id', $uIds)
                ->Where('status' , 1)->sum('discount');
                $due = $fee->fee_amount - ($paid_result + $discount_result);
                $disabled = ($due == 0) ? " style=\"display:none;\"":"";
                $arrFeeMaster[$k]['assignId'] = $fee->id;
                $arrFeeMaster[$k]['Fee Head'] = $fee->fee_head_title;//rtrim(substr($fee->fee_head_title, 0, strpos($fee->fee_head_title, '(')),' ');
                $arrFeeMaster[$k]['fee_head_id'] = $fee->fee_head_id;
                $arrFeeMaster[$k]['Fees'] = $fee->times;
                $arrFeeMaster[$k]['amount'] = $fee->fee_amount;
                $arrFeeMaster[$k]['paid'] = $paid_result;
                $arrFeeMaster[$k]['discount'] = $discount_result;
                $arrFeeMaster[$k]['due'] = $due;
                $arrFeeMaster[$k]['month_name'] = $fee->month_name;
            }
        }
        if(count($arrFeeMaster)==0){
            $arrFeeMaster = (object) $arrFeeMaster;
        }
        // dd($arrFeeMaster);
        return $this->return_data_in_json($arrFeeMaster,$error_msg="");
    }

    public function get_student_fee_receipt_list($stdId="",$ssId="",$courseId="",Request $request)
   {
       Log::debug($request->all());
        $data = [];
        $uIds=(isset($_GET['uid']) && $_GET['uid']!="")? $_GET['uid'] : $stdId;
        $ssId=(isset($_GET['ssId']) && $_GET['ssId']!="")? $_GET['ssId'] : $ssId;
        $courseId=(isset($_GET['courseId']) && $_GET['courseId']!="")? $_GET['courseId'] : $courseId;
        
        if($uIds==""){
            return $data;
        }

        $feeHistory_data = DB::table('collect_fee')->Select('collect_fee.id','collect_fee.status','collect_fee.student_id','collect_fee.discount', 'collect_fee.reciept_date','collect_fee.assign_fee_id','collect_fee.amount_paid','collect_fee.reciept_no','collect_fee.payment_type','collect_fee.status','sd.first_name','sd.branch_id','sd.reg_no', 'sd.reg_date', 'sd.university_reg','sd.date_of_birth', 'sd.gender','asf.fee_amount','asf.course_id','asf.fee_head_id','fd.fee_head_title','br.branch_name','br.branch_logo','br.branch_mobile','br.branch_email','br.branch_address', 'fac.faculty')
            ->where('collect_fee.student_id','=',$uIds)
                //->Where('asf.student_id', $req->student)
                //->where('asf.branch_id', $branch)
            ->where('asf.session_id', $ssId)
        ->leftjoin('students as sd', 'sd.id', '=', 'collect_fee.student_id')
        ->leftjoin('assign_fee as asf', 'asf.id', '=', 'collect_fee.assign_fee_id')
        ->leftjoin('fee_heads as fd', 'fd.id', '=', 'asf.fee_head_id')
        ->leftjoin('branches as br', 'br.id', '=', 'sd.branch_id')
        ->leftjoin('faculties as fac', 'asf.course_id', '=', 'fac.id')
        ->get();
        $data_val="";$i=0;

        $arrFeeMaster = [];
        $k=0;
        foreach ($feeHistory_data as $feeHistory) { $k++;
            $due=$feeHistory->fee_amount-($feeHistory->amount_paid +$feeHistory->discount);
            $collDate = date("d-m-Y",strtotime($feeHistory->reciept_date));
            $arrFeeMaster[$k]['reciept_no']     = $feeHistory->reciept_no;
            $arrFeeMaster[$k]['fee_head_title'] = $feeHistory->fee_head_title;
            $arrFeeMaster[$k]['amount_paid']    = $feeHistory->amount_paid;
            $arrFeeMaster[$k]['discount']    = $feeHistory->discount;
            $arrFeeMaster[$k]['payment_mode']   = $feeHistory->payment_type;
            $arrFeeMaster[$k]['due']   = $due;
            $arrFeeMaster[$k]['payment_date']       = $collDate; 
            $PRINT_URL =  route('feeReceipt', ['receipt_no' => $feeHistory->reciept_no]);
            $receipt_print_url  = ($feeHistory->status== 1)? $PRINT_URL : "";

            //$arrFeeMaster[$k]['PRINT_URL'] = $receipt_print_url;
            $arrFeeMaster[$k]['status'] = ($feeHistory->status==1)? 'PAID' : 'Failed';
        }
         if(count($arrFeeMaster)==0){
            $arrFeeMaster = (object) $arrFeeMaster;
        }
        $resp = [];
        foreach ($arrFeeMaster as $key => $value) {

            if(!isset($resp[$value['reciept_no']])){
    
    
                   $resp[$value['reciept_no']]=$value; 
    
             }
    
             else{
    
                 $resp[$value['reciept_no']]['fee_head_title'] .= ','.$value['fee_head_title'];
                 $resp[$value['reciept_no']]['amount_paid'] += $value['amount_paid'];
                 $resp[$value['reciept_no']]['discount'] += $value['discount'];
                 $resp[$value['reciept_no']]['due'] += $value['due'];
    
    
    
             }
    
    
        }
        if(count($resp)==0){
            $resp = (object) $resp;
        }
        // Log::debug($arrFeeMaster);
        return $this->return_data_in_json($resp,$error_msg="");
    }

    public function getcourseapi($cid)
    {
        $data = [];
        $data = Faculty::select('id', 'faculty', 'status')
        ->where('branch_id' , $cid)
        ->orderBy('faculty')
        ->get();
        return $this->return_data_in_json($data,$error_msg="");        
   }

    public function feebycourseapi($cid,$sessionId="")
    {
        $assign_list=Fee_model::select('assign_fee.*', 'fee_heads.fee_head_title', 'session.session_name')
        ->leftJoin('fee_heads', function($join){
                $join->on('fee_heads.id', '=', 'assign_fee.fee_head_id');
        })
        ->leftJoin('session', function($join){
            $join->on('session.id', '=', 'assign_fee.session_id');
        }) 
        ->where('course_id' , $cid)
        ->where(function($q) use ($sessionId){
            if($sessionId!=""){
                $q->where('assign_fee.session_id',$sessionId);
            }
        })
        ->where('student_id', '0')
        ->get();
       return $this->return_data_in_json($assign_list,$error_msg=""); 
   }


    public function getcoursebycollege()
    {
        $cid = Session::get('activeBranch');
        $data = [];
        $data = Faculty::select('id', 'faculty', 'status','form_fees')
        ->where('branch_id' , $cid)
        ->orderBy('faculty')
        ->get();

        return $this->return_data_in_json($data,$error_msg=""); 
}


  public function getfeebycourse($id)
    {  
        $data = [];
        $data = Faculty::select( 'form_fees')
        ->where('id' , $id)
        ->get();
        return $this->return_data_in_json($data,$error_msg=""); 
    }   

    public function checkPostHash(Request $request)
    {
        $data = array();
        if(isset($_GET['key']) && $_GET['key'] != ""){ 
            $data = $_GET;
        }
        
        if(isset($_POST['key']) && $_POST['key'] != ""){ 
            $data = $_POST;
        }
        
        $retHashSeq= "";
        
        // hash=sha512(key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5||||||SALT)
        if(isset($data['key']) && $data['key'] != ""){
            $key        = $this->checkBlackVal($data['key']);
            $amount     = $this->checkBlackVal($data['amount']);
            $txnid      = $this->checkBlackVal($data['txnid']);
            $email      = $this->checkBlackVal($data['email']);
            $productinfo= $this->checkBlackVal($data['productinfo']);
            $firstname  = $this->checkBlackVal($data['firstname']);
            $udf1        = $this->checkBlackVal($data['udf1']);
            $udf2        = $this->checkBlackVal($data['udf2']);
            $udf3        = $this->checkBlackVal($data['udf3']);
            $udf4        = $this->checkBlackVal($data['udf4']);
            $udf5        = $this->checkBlackVal($data['udf5']);
            $salt        = $this->checkBlackVal($data['salt']);
            
            $retHashSeq = $key.'|'.$txnid.'|'.$amount.'|'.$productinfo.'|'.$firstname.'|'.$email.'|'.$udf1.'|'.$udf2.'|'.$udf3.'|'.$udf4.'|'.$udf5.'||||||'.$salt;
        } 
        return $hash = hash("sha512", $retHashSeq);
    }
   
   
    public function checkRevershashVal($data)
    {
        $retHashSeq = "";
        //$salt       = "pjVQAWpA"; //Get salt from DB where key

        $branchId=DB::table('payu_payments')->Select('branch_id')
        ->Where('txnid', $data["txnid"])
        ->get();

        $merchantSalt=DB::table('branches')->Select('Merchant_Salt') 
        ->Where('Merchant_Key', $data["key"])
        // ->Where('id', $branchId[0]->branch_id)
        ->first();

        $salt = trim($merchantSalt->Merchant_Salt);

        //  sha512(SALT|status||||||udf5|udf4|udf3|udf2|udf1|email|firstname|productinfo|amount|txnid|key)
        if (isset($data["additional_charges"]) && $data["additional_charges"] != null) { 
            $reversehash_string = $data["additional_charges"] . "|" . $salt . "|" . $data["status"]  . "||||||" . $data["udf5"] . "|" . $data["udf4"] . "|" . $data["udf3"] . "|" . $data["udf2"] . "|" . $data["udf1"] . "|" .
            $data["email"] . "|" . $data["firstname"] . "|" . $data["productinfo"] . "|" . $data["amount"] . "|" . $data["txnid"] . "|" . $data["key"] ; 
      }
      else{
            $reversehash_string =  $salt . "|" . $data["status"]  . "||||||" . $data["udf5"] . "|" . $data["udf4"] . "|" . $data["udf3"] . "|" . $data["udf2"] . "|" . $data["udf1"] . "|" .$data["email"] . "|" . $data["firstname"] . "|" . $data["productinfo"] . "|" . $data["amount"] . "|" . $data["txnid"] . "|" . $data["key"] ; 
      }

      $reverseHash = strtolower(hash("sha512", $reversehash_string));
      return $reverseHash;
    }
   
    public function checkBlackVal($val="")
    {
        return (isset($val) && $val !="") ? $val : "";
    }
    

    public function getBranchInformation($stdId="")
    {
        $data = [];
        $uIds=(isset($_GET['uid']) && $_GET['uid']!="")? $_GET['uid'] : $stdId;
        
        if($uIds==""){
            return $data;
        }
        
        $fileLink = asset('images'.'/'.'logo/');
        $fileLink = addslashes($fileLink)."/";

        $fee_result=DB::table('branches')->Select('branch_name','branch_title','branch_address','branch_email','branch_mobile',DB::raw("CONCAT('$fileLink',branch_logo) as branch_logo"),'branch_website')
        ->join('users as ur', 'ur.branch_id', '=', 'branches.id')
        ->Where('ur.id', $uIds)
        ->get();

        $arrFeeMaster = $fee_result;
        return $this->return_data_in_json($arrFeeMaster,$error_msg="");
    }

    public function fee_payment_process()
    {   
        $reqData        = json_encode($_POST);
        
        /*
        $reqData        = '{"AssignId":"49","Amount":"125.0","udf5":"","mobile":"1234567123","Product":"FINE","emailId":"creataum@creataum.in","udf3":"","udf4":"","udf1":"","udf2":"","PaidAmount":"125.0","firstNamne":"Creataum Test User","branch_id":"1","UserId":"441","TrasationId":"1574768354326","Key":"7rnFly","hash":"480f2ef83d26fae971069dd4d570ff35c50cdf686083dfb8bffb1fdb3d4ad33d25359e3d72a1afb3b887be251c2c48c9521932b115068085d9626f258ff406af"}';
        */
        $reqDataDecod   = json_decode($reqData);
        $postData       = $reqDataDecod; 
        $arrFeeMaster['Status'] = $this->save_fee_payment_post_data($postData);
        
        return $this->return_data_in_json($arrFeeMaster,$error_msg="");
    }

    public function save_fee_payment_post_data($data)
    {
        if(!empty($data))
        {
            $insStr = 'insert into payu_payments (account,txnid,firstname,email,phone,amount,data,status,feeamount,feehead,fee_masters_id,student_id,branch_id,org_id) values ("payu","'.$data->TrasationId.'","'.$data->firstNamne.'","'.$data->emailId.'","'.$data->mobile.'","'.$data->Amount.'","'.$data->Product.'","pending","'.$data->Amount.'","'.$data->Product.'","'.$data->AssignId.'","'.$data->UserId.'","'.$data->branch_id.'","1")';
            $saveRec = DB::insert($insStr);
            if($saveRec){
                return "success";
            }else{
                return "fail";
            }
        }
        else{
                return "fail";
            }
    }

    public function fee_payment_online_save()
    {   
        $reqData        = json_encode($_POST); 
        /*
        $reqData        = '{"Data":"mihpayid=403993715520166339&amp;mode=CC&amp;status=success&amp;unmappedstatus=captured&amp;key=7rnFly&amp;txnid=1574768354326&amp;amount=125.00&amp;cardCategory=domestic&amp;discount=0.00&amp;net_amount_debit=125&amp;addedon=2019-11-26+17%3A09%3A18&amp;productinfo=FINE&amp;firstname=Creataum+Test+User&amp;lastname=&amp;address1=&amp;address2=&amp;city=&amp;state=&amp;country=&amp;zipcode=&amp;email=creataum%40creataum.in&amp;phone=1234567123&amp;udf1=&amp;udf2=&amp;udf3=&amp;udf4=&amp;udf5=&amp;udf6=&amp;udf7=&amp;udf8=&amp;udf9=&amp;udf10=&amp;hash=e0b0c08b76b0629c92b38146e48841b136a74f17e47e6e664796d8f9061f426e7c369ff700c564ffb70b927efe5aeee04d2322ace3c575fed59e666ca9bcd090&amp;field1=933010001106&amp;field2=000000&amp;field3=201933065671736&amp;field4=700201933034313521&amp;field5=05&amp;field6=&amp;field7=AUTHPOSITIVE&amp;field8=&amp;field9=&amp;payment_source=payu&amp;PG_TYPE=HDFCPG&amp;bank_ref_num=201933065671736&amp;bankcode=CC&amp;error=E000&amp;error_Message=No+Error&amp;name_on_card=gaurav.+singh881&amp;cardnum=401200XXXXXX1112&amp;cardhash=This+field+is+no+longer+supported+in+postback+params."}'; 
        */
        $reqData        = str_replace('&amp;', '&', $reqData);
        $reqData        = str_replace('{"Data":"', '', $reqData);
        $reqData        = str_replace('"}', '', $reqData);
        
        $reqDataDecod   = trim($reqData);
        $reqDataDecod   = parse_str($reqDataDecod, $get_array);;

        $responseData   = $get_array;
        $arrFeeMaster['PaymentResponse'] = $responseData;
        $arrFeeMaster['FeePaymentStatus'] = $this->save_fee_payment_response($responseData);

        return $this->return_data_in_json($arrFeeMaster,$error_msg="");
    }

    public function save_fee_payment_response($data)
    {
        $resArray       = array();
        if(!empty($data))
        {
            $resposneReverseHash    = $data['hash'];
            $genReverseHash         = $this->checkRevershashVal($data);

            if($resposneReverseHash != $genReverseHash){
                $resArray['temStatus'] = "Invalid transaction - transaction is tempered";
                $resArray['paymentStatus'] = 'Failed';
            }else{
                $resArray['temStatus']      = "Transaction Success";
                $resArray['paymentStatus']  = $data['status'];
            }

            $status = $resArray['paymentStatus']; //$data['status'] ;
            $unmappedstatus = $data['unmappedstatus'];

            $dataResp = DB::table('payu_payments')->where('txnid', $data['txnid'])
                    ->update([
                        "account"       =>$data['payment_source'], 
                        'txnid'         =>$data['txnid'],
                        'firstname'     =>$data['firstname'],
                        'email'         =>$data['email'],
                        'phone'         =>$data['phone'],
                        'amount'        =>$data['amount'], 
                        'discount'      =>$data['discount'], 
                        'net_amount_debit' =>$data['net_amount_debit'],
                        'data'          =>$data['productinfo'],
                        'status'        =>$data['status'],
                        'unmappedstatus'=>$data['unmappedstatus'],
                        'mode'          =>$data['mode'],
                        'bank_ref_num'  =>$data['bank_ref_num'],
                        'mihpayid'      =>$data['mihpayid'],
                        'updated_status'=>$resArray['temStatus'],
                        'cardnum'       =>$data['cardnum']
                    ]);

            $feeheads=DB::table('payu_payments')->select('feehead', 'feeamount','student_id','fee_masters_id','id')->where('txnid' ,$data['txnid'])->Get();

            $user_id        = $feeheads[0]->student_id;
            $date           = Carbon\Carbon::now();
            // $randomString   = $this->reciept_no();
            // $resArray['receiptNumber']  = $randomString;
            if ($data['status']=='Failed' || $data['status']=='failure' || $resArray['paymentStatus'] == 'Failed') 
            {
                $paymentdata       = DB::table('collect_fee')->insertGetId([
                    'created_by'    => $user_id,
                    // 'reciept_no'    => $randomString,
                    'created_by'    => $user_id,
                    'student_id'    => $feeheads[0]->student_id, 
                    'assign_fee_id' => $feeheads[0]->fee_masters_id, 
                    'created_at'    => $date, 
                    'reciept_date'  => $date,
                    'amount_paid'   => $data['amount'],  
                    'payment_type'  => $data['mode'],
                    'reference'     => "Transaction No. ".$data['txnid'],
                    'status'        => '0'
                ]);
            }
            elseif ($status=='success') 
            {
                $paymentdata =DB::table('collect_fee')->insertGetId([
                    'created_by'    => $user_id,
                    'reciept_date'  => $date,
                    // 'reciept_no'    => $randomString,
                    'created_by'    => $user_id,
                    'student_id'    => $feeheads[0]->student_id, 
                    'assign_fee_id' => $feeheads[0]->fee_masters_id, 
                    'created_at'    => $date, 
                    'amount_paid'   => $data['amount'],  
                    'payment_type'  => $data['mode'], 
                    'reference'     => "Transaction No. ".$data['txnid'],
                    'status'        => '1' 
                ]); 
            }
            
            $randomString=$this->reciept_no($paymentdata); 
            $resArray['receiptNumber']  = $randomString;
            
            DB::table('collect_fee')->where('id',$paymentdata)->update([
                        'reciept_no'=>$randomString ]);
                        
            return $resArray;
        }
    }

    //APIs
    public function get_Staff_Details($staffId="",$userType="",$rollId="")
    {
        
        $data = []; 
        $data['userType']   = $userType;
        $data['rollId']     = $rollId;
        $fileLink = asset('images'.DIRECTORY_SEPARATOR.'staff');
        $fileLink = addslashes($fileLink).'/';
        if($staffId==""){
            return $data;
        }
    

        $data['info']=DB::table('staff')->select('staff.id as staff_id','staff.reg_no','staff.join_date','staff.designation as designation_id','staff.first_name','staff.middle_name','staff.last_name','staff.date_of_birth','staff.gender','staff.nationality','staff.address','staff.blood_group','staff.mother_tongue','staff.address','staff.state','staff.country','staff.temp_state','staff.temp_country','staff.home_phone','staff.mobile_1','staff.email','staff.qualification','staff.experience','staff.experience_info','staff_designations.title as designation','branches.branch_title','zoom_api_key','zoom_secret_key','zoom_email','zoom_password','zoom_jwt','observer',DB::raw("CONCAT('$fileLink',staff.staff_image) as staff_image"),'users.id as user_id','notificationadmin')
        ->where([
            ['staff.id','=',$staffId] 
        ]) 
        ->join('staff_designations','staff.designation','=','staff_designations.id')
        ->leftjoin('users',function($j){
            $j->on('users.hook_id','=','staff.id')
            ->where('users.role_id','!=',6);
        })
        ->leftjoin('branches','branches.id','=','staff.branch_id')
        ->first();
        $data['general_setting'] = DB::table('general_settings')->select('live_class_scheduling')->first();
        return $this->return_data_in_json($data,$error_msg="");
    }
    public function staff_details(Request $request){
        $data = $this->get_Staff_Details($request->staff_id);
        return $this->return_data_in_json($data);
    }
    public function get_Staff_Attendance($staffId="",$yearId="",$monthId="")
    {
         $data = [];

         if($staffId=="" || $yearId=="" ){
            return $data;
        }

        if(!empty($monthId)){
            $data['staffatt']=DB::table('attendances')->select('attendances.years_id','years.title as year_name','attendances.months_id','months.title as month_name','attendances.day_1','attendances.day_2','attendances.day_3','attendances.day_4','attendances.day_5','attendances.day_6','attendances.day_7','attendances.day_8','attendances.day_9','attendances.day_10','attendances.day_11','attendances.day_12','attendances.day_13','attendances.day_14','attendances.day_15','attendances.day_16','attendances.day_17','attendances.day_18','attendances.day_19','attendances.day_20','attendances.day_21','attendances.day_22','attendances.day_23','attendances.day_24','attendances.day_25','attendances.day_26','attendances.day_27','attendances.day_28','attendances.day_29','attendances.day_30','attendances.day_31')
            ->where([
                ['attendees_type','!=','1'],
                ['link_id','=',$staffId], 
                ['months_id','=',$monthId],
                ['years_id','=',$yearId]
            ])
            ->join('years','attendances.years_id','=','years.id')
            ->join('months','attendances.months_id','=','months.id')
            ->get();
            // $data['TYPE']['PRESENT']   = [];
            // $data['TYPE']['ABSENT']    = [];
            // $data['TYPE']['LATE']      = [];
            // $data['TYPE']['LEAVE']     = [];
            // $data['TYPE']['HOLIDAY']   = [];
            foreach ($data['staffatt'] as $vals) {
                # code...
                for($i=1;$i<=31;$i++)
                {
                    $key ='day_'.$i;
                    $val = $vals->$key;
                    if($val==1){
                        $data['TYPE']['PRESENT']['data'][]   = $i; 
                    }
                    elseif($val==2){ 
                        $data['TYPE']['ABSENT']['data'][]   = $i; 
                    }
                    elseif($val==3){ 
                        $data['TYPE']['LATE']['data'][]   = $i; 
                    }
                    elseif($val==4){
                        $data['TYPE']['LEAVE']['data'][]   = $i; 
                    }
                    elseif($val==5){
                        $data['TYPE']['HOLIDAY']['data'][]   = $i; 
                    }
                }
            }
        } 
        else{
            $data['staffatt']=DB::table('attendances')->select('attendances.years_id','years.title as year_name','attendances.months_id','months.title as month_name','attendances.day_1','attendances.day_2','attendances.day_3','attendances.day_4','attendances.day_5','attendances.day_6','attendances.day_7','attendances.day_8','attendances.day_9','attendances.day_10','attendances.day_11','attendances.day_12','attendances.day_13','attendances.day_14','attendances.day_15','attendances.day_16','attendances.day_17','attendances.day_18','attendances.day_19','attendances.day_20','attendances.day_21','attendances.day_22','attendances.day_23','attendances.day_24','attendances.day_25','attendances.day_26','attendances.day_27','attendances.day_28','attendances.day_29','attendances.day_30','attendances.day_31')
            ->where([
                ['attendees_type','!=','1'],
                ['link_id','=',$staffId], 
                ['years_id','=',$yearId]
            ])
            ->join('years','attendances.years_id','=','years.id')
            ->join('months','attendances.months_id','=','months.id')
            ->get();
            // $data['TYPE']['PRESENT']   = [];
            // $data['TYPE']['ABSENT']    = [];
            // $data['TYPE']['LATE']      = [];
            // $data['TYPE']['LEAVE']     = [];
            // $data['TYPE']['HOLIDAY']   = [];
            foreach ($data['staffatt'] as $vals) {
                # code...
                for($i=1;$i<=31;$i++)
                {
                    $key ='day_'.$i;
                    $val = $vals->$key;
                    if($val==1){
                        $data['TYPE']['PRESENT']['data'][]   = $i; 
                    }
                    elseif($val==2){ 
                        $data['TYPE']['ABSENT']['data'][]   = $i; 
                    }
                    elseif($val==3){ 
                        $data['TYPE']['LATE']['data'][]   = $i; 
                    }
                    elseif($val==4){
                        $data['TYPE']['LEAVE']['data'][]   = $i; 
                    }
                    elseif($val==5){
                        $data['TYPE']['HOLIDAY']['data'][]   = $i; 
                    }
                }
            }
        }
            $title=DB::table('attendance_statuses')->select('title','id')
            ->get();
            foreach ($title as $key => $value) {
                $data['attstatus'][$value->id]=$value->title;
            }
            return $this->return_data_in_json($data,$error_msg="");

    }
    public function get_Student_Attendance_Status($stdId="",$yearId="",$monthId="")
    {
         $data = [];

         if($stdId=="" || $yearId=="" ){
            return $data;
        }

        if(!empty($monthId)){
            $data['studentAttendance']=DB::table('attendances')->select('attendances.years_id','years.title as year_name','attendances.months_id','months.title as month_name','attendances.day_1','attendances.day_2','attendances.day_3','attendances.day_4','attendances.day_5','attendances.day_6','attendances.day_7','attendances.day_8','attendances.day_9','attendances.day_10','attendances.day_11','attendances.day_12','attendances.day_13','attendances.day_14','attendances.day_15','attendances.day_16','attendances.day_17','attendances.day_18','attendances.day_19','attendances.day_20','attendances.day_21','attendances.day_22','attendances.day_23','attendances.day_24','attendances.day_25','attendances.day_26','attendances.day_27','attendances.day_28','attendances.day_29','attendances.day_30','attendances.day_31')
            ->where([
                ['attendees_type','=','1'],
                ['link_id','=',$stdId], 
                ['months_id','=',$monthId],
                ['years_id','=',$yearId]
            ])
            ->leftjoin('years','attendances.years_id','=','years.id')
            ->leftjoin('months','attendances.months_id','=','months.id')
            ->get();
            // $data['TYPE']['PRESENT']   = [];
            // $data['TYPE']['ABSENT']    = [];
            // $data['TYPE']['LATE']      = [];
            // $data['TYPE']['LEAVE']     = [];
            // $data['TYPE']['HOLIDAY']   = [];
            foreach ($data['studentAttendance'] as $vals) {
                # code...
                //dd($vals->day_1);
                for($i=1;$i<=31;$i++)
                {
                    $key ='day_'.$i;
                    $val = $vals->$key;
                    if($val==1){
                        $data['TYPE']['PRESENT']['data'][]   = $i; 
                    }
                    elseif($val==2){ 
                        $data['TYPE']['ABSENT']['data'][]   = $i; 
                    }
                    elseif($val==3){ 
                        $data['TYPE']['LATE']['data'][]   = $i; 
                    }
                    elseif($val==4){
                        $data['TYPE']['LEAVE']['data'][]   = $i; 
                    }
                    elseif($val==5){
                        $data['TYPE']['HOLIDAY']['data'][]   = $i; 
                    }
                }
            }
        } 
        else{
            $data['studentAttendance']=DB::table('attendances')->select('attendances.years_id','years.title as year_name','attendances.months_id','months.title as month_name','attendances.day_1','attendances.day_2','attendances.day_3','attendances.day_4','attendances.day_5','attendances.day_6','attendances.day_7','attendances.day_8','attendances.day_9','attendances.day_10','attendances.day_11','attendances.day_12','attendances.day_13','attendances.day_14','attendances.day_15','attendances.day_16','attendances.day_17','attendances.day_18','attendances.day_19','attendances.day_20','attendances.day_21','attendances.day_22','attendances.day_23','attendances.day_24','attendances.day_25','attendances.day_26','attendances.day_27','attendances.day_28','attendances.day_29','attendances.day_30','attendances.day_31')
            ->where([
                ['attendees_type','=','1'],
                ['link_id','=',$stdId], 
                ['years_id','=',$yearId]
            ])
            ->leftjoin('years','attendances.years_id','=','years.id')
            ->leftjoin('months','attendances.months_id','=','months.id')
            ->get();

            // $data['TYPE']['PRESENT']   = [];
            // $data['TYPE']['ABSENT']    = [];
            // $data['TYPE']['LATE']      = [];
            // $data['TYPE']['LEAVE']     = [];
            // $data['TYPE']['HOLIDAY']   = [];
            foreach ($data['studentAttendance'] as $vals) {
                # code...
                //dd($vals->day_1);
                for($i=1;$i<=31;$i++)
                {
                    $key ='day_'.$i;
                    $val = $vals->$key;
                    if($val==1){
                        $data['TYPE']['PRESENT']['data'][]   = $i; 
                    }
                    elseif($val==2){ 
                        $data['TYPE']['ABSENT']['data'][]   = $i; 
                    }
                    elseif($val==3){ 
                        $data['TYPE']['LATE']['data'][]   = $i; 
                    }
                    elseif($val==4){
                        $data['TYPE']['LEAVE']['data'][]   = $i; 
                    }
                    elseif($val==5){
                        $data['TYPE']['HOLIDAY']['data'][]   = $i; 
                    }
                }

            }

        }
            $title=DB::table('attendance_statuses')->select('title','id')
            ->get();
            foreach ($title as $key => $value) {
                $data['attstatus'][$value->id]=$value->title;
            }
            return $this->return_data_in_json($data,$error_msg="");

    }

    public function get_Year_List(){
            $years=DB::table('years')->select('id','title')
            ->orderBy('active_status','desc')   
             ->get();
            foreach ($years as $key => $value) {
                $temp['year']=$value->title;
                $temp['id']=$value->id;
                $data[] = $temp;
             }
        return $this->return_data_in_json($data,$error_msg="");
       }

    public function get_Month_List(){
             $years=DB::table('months')->select('id','title')
             ->get();
            foreach ($years as $key => $value) {
             $data['months'][$value->id]=$value->title;
             }
        return $this->return_data_in_json($data,$error_msg="");
    }

    public function get_Attendance_Title(){
            $title=DB::table('attendance_statuses')->select('title','id')
            ->get();
            foreach ($title as $key => $value) {
                $data['attstatus'][$value->id]=$value->title;
            }
            return $this->return_data_in_json($data,$error_msg="");
    }

    public function get_Staff_Document($staffId=""){
                $data = [];
        if($staffId==""){
            return $data;
        }        
        $reg_no=DB::table('staff')->select('reg_no')
        ->where([
            ['id','=',$staffId]
        ])
        ->get();
        
        $fileLink = asset('documents'.DIRECTORY_SEPARATOR.'staff'.DIRECTORY_SEPARATOR.$reg_no[0]->reg_no);
        $fileLink = addslashes($fileLink).'/';
        
        $data['staffdoc']=DB::table('documents')->select('member_type','member_id','title',DB::raw("CONCAT('$fileLink',file) as staff_document"),'description')
        ->where([
            ['member_type','!=','student'],
            ['member_id','=',$staffId]
        ])
        ->get();
         return $this->return_data_in_json($data,$error_msg="");
    }

    public function get_Staff_Note($staffId=""){
        $data = [];
        if($staffId==""){
            return $data;
        } 
        
        $data['staffnote']=DB::table('notes')->select('member_type','member_id','subject','note','created_at')
        ->where([
            ['member_type','!=','student'],
            ['member_id','=',$staffId]
        ])
        ->get();
        return $this->return_data_in_json($data,$error_msg="");

    }

    public function get_Staff_Payroll($staffId=""){
        $data = [];
        if($staffId==""){
            return $data;
        }

        $data['assigned'] = DB::select(DB::raw("SELECT payroll_masters.id,payroll_masters.staff_id,payroll_masters.tag_line,payroll_masters.due_date,payroll_masters.created_at,payroll_masters.payroll_head,payroll_masters.amount,sum(salary_pays.paid_amount) as total_paid FROM `payroll_masters` left join salary_pays on salary_pays.salary_masters_id = payroll_masters.id where payroll_masters.staff_id=$staffId group by payroll_masters.id "));

        $data['reciept']=DB::table('salary_pays')->select('paid_amount','date','allowance','fine','payroll_masters.tag_line','payroll_masters.amount')
            ->where([
                        ['salary_pays.staff_id','=',$staffId]
                    ])
            ->leftjoin('payroll_masters','payroll_masters.id','=','salary_pays.salary_masters_id')
            ->orderBy('payroll_masters.tag_line','ASC')
            ->get();
        
        return $this->return_data_in_json($data,$error_msg="");  
       } 
    public function get_student_attendance($session_id,$branch_id,$course_id,$sec_id){
        $data['student']=DB::select(DB::raw("SELECT students.first_name as student_name,students.reg_no,students.id as student_id,parent_details.father_first_name father_name,student_detail_sessionwise.course_id FROM students 
            join student_detail_sessionwise on student_detail_sessionwise.student_id = students.id 
            left join parent_details on parent_details.students_id=students.id
            where(students.branch_id=$branch_id and student_detail_sessionwise.course_id=$course_id and student_detail_sessionwise.Semester=$sec_id and students.status=1 and student_detail_sessionwise.session_id=$session_id) order By student_name  asc 
            "));

         return $this->return_data_in_json($data,$error_msg=""); 
    }
    public function update_student_attendance(Request $request){
        
        $date       = $request->date; 
        $staff_id   = $request->user_id;
        $Session    = $request->Session;
        $Branch     = $request->Branch;
        $Course     = $request->Course; 
        $Section    = $request->Section;

       $stdAtnd    = $request->Student_attendance_list; 

    $atdArr = json_decode($stdAtnd);
        $date=explode('/', $date);
        $da=$date[0]; 
        $day="day_".ltrim($da,0);   
        $month=$date[1];
        $year=$date[2];
        $year_id=DB::select(DB::raw("SELECT id from years where title=$year"));
        $year_id=$year_id[0]->id;
        $cdate=Carbon\Carbon::now();
        $attStatus =1;
        foreach ($atdArr as $key => $value) {
            if($value->istakemarkp==true){
                $resp= DB::table('attendances')
                    ->updateOrInsert(
                    ['attendees_type' => 1, 'link_id' => $value->reg_no,'years_id'=>$year_id,'months_id'=>$month],
                    [$day => 1,'created_by'=>$staff_id,'created_at'=>$cdate->toDateTimeString(),'updated_at'=>$cdate->toDateTimeString()]
                    );
            }
            elseif($value->istakemarkp==false){
                $resp= DB::table('attendances')
                    ->updateOrInsert(
                    ['attendees_type' => 1, 'link_id' => $value->reg_no,'years_id'=>$year_id,'months_id'=>$month],
                    [$day => 2,'created_by'=>$staff_id,'created_at'=>$cdate->toDateTimeString(),'updated_at'=>$cdate->toDateTimeString()]
                    );
            }

            if($resp==0){
                $attStatus = 0;
            }              
        }  

        return $status = "{'Status':'".$attStatus."'}";
           
    }
    /*
    public function get_assignment_list(Request $request){ 
        $data=[]; 
        
        $faculty=$request->facultyId?$request->facultyId:null;
        $session=$request->sessionId?$request->sessionId:null;
        $branchId=$request->branchId?$request->branchId:null;
        if($faculty==null || $session==null || $branchId==null){
        
            return $this->return_data_in_json($data,$error_msg="");
        }
        $fileLink = asset('assignments'.DIRECTORY_SEPARATOR.'questions');
      
        $data['assignment']=DB::table('assignments')->select('assignments.id as assignments_id','assignments.created_by as staff_id',DB::raw("CONCAT(staff.first_name,' ',staff.last_name)as staff_name"),'faculties.faculty','assignments.session_id','session.session_name as session','assignments.semesters_id','semesters.semester','assignments.subjects_id','subjects.title as subject_name','assignments.publish_date','assignments.end_date','assignments.title','assignments.description',DB::raw("CONCAT('$fileLink','/',file) as assignment")) 
            ->Where([
                ['assignments.faculty','=',$faculty],
                ['session_id','=',$session],
                ['assignments.status','=',1],
                ['assignments.branch_id','=',$branchId]
            ])
            ->leftjoin('faculties','faculties.id','=','assignments.faculty')
            ->leftjoin('staff','assignments.created_by','=','staff.id')
            ->leftjoin('session','assignments.session_id','=','session.id')
            ->leftjoin('semesters','semesters.id','=','assignments.semesters_id')
            ->leftjoin('subjects','subjects.id','=','assignments.subjects_id')
            ->get();

        return $this->return_data_in_json($data,$error_msg="");
    } 
    */
    
    public function get_assignment_list(Request $request){
        Log::debug($request->all());
        $data=[];
        $faculty=(isset($request->facultyId))?$request->facultyId:null;
        $session=(isset($request->sessionId))?$request->sessionId:null;
        $branchId=(isset($request->branchId))?$request->branchId:null;
        if($faculty==null || $session==null || $branchId==null){
        
                return $data;
            }
            $fileLink = asset('assignments'.DIRECTORY_SEPARATOR.'questions');
            $data['assignment'] = [];
            if($request->staff_id){
                $data['assignment']=DB::table('assignments')->select('assignments.id as assignments_id','staff.id as staff_id',DB::raw("CONCAT(staff.first_name,' ',staff.last_name)as staff_name"),'faculties.faculty','assignments.session_id','session.session_name as session','assignments.semesters_id','semesters.semester','assignments.subjects_id','timetable_subjects.title as subject_name','assignments.publish_date','assignments.end_date','assignments.title','assignments.description',DB::raw("CONCAT('$fileLink','/',assignments.file) as assignment")) 
                ->Where([
                    ['assignments.faculty','=',$faculty],
                    ['assignments.session_id','=',$session],
                    ['assignments.status','=',1],
                    ['assignments.branch_id','=',$branchId]
                ])
                ->where(function($q)use($request){
                    if($request->section_id){
                        $q->where('semesters_id',$request->section_id);
                    }
                })
                ->leftjoin('faculties','faculties.id','=','assignments.faculty')
                ->leftJoin('users as ur','ur.id','=','assignments.created_by')
                ->leftjoin('staff','ur.hook_id','=','staff.id')
                ->leftjoin('session','assignments.session_id','=','session.id')
                ->leftjoin('semesters','semesters.id','=','assignments.semesters_id')
                ->leftjoin('timetable_subjects','timetable_subjects.id','=','assignments.subjects_id')
                ->rightJoin('timetable_assign_subject as tas','timetable_subjects.id','=','tas.timetable_subject_id')
                ->leftjoin('assignment_answers as a_ans',function($j){
                    $j->on('a_ans.assignments_id','=','assignments.id')
                    ->where(function($q){
                        $q->where('approve_status','!=',1)
                        ->orWhere('approve_status','!=',2);
                    });
                })
                ->where('tas.status',1)
                 ->where('tas.staff_id',$request->staff_id)
                ->selectRaw('COUNT(a_ans.id) as submitted_answers')
                ->groupBy('assignments.id')
                ->get();
            }
            
        
            if(!(count($data['assignment']) > 0)){
                $data['assignment']=DB::table('assignments')->select('assignments.id as assignments_id','staff.id as staff_id',DB::raw("CONCAT(staff.first_name,' ',staff.last_name)as staff_name"),'faculties.faculty','assignments.session_id','session.session_name as session','assignments.semesters_id','semesters.semester','assignments.subjects_id','timetable_subjects.title as subject_name','assignments.publish_date','assignments.end_date','assignments.title','assignments.description',DB::raw("CONCAT('$fileLink','/',assignments.file) as assignment")) 
                ->Where([
                    ['assignments.faculty','=',$faculty],
                    ['assignments.session_id','=',$session],
                    ['assignments.status','=',1],
                    ['assignments.branch_id','=',$branchId]
                ])
                ->where(function($q)use($request){
                    if($request->section_id){
                        $q->where('semesters_id',$request->section_id);
                    }
                })
                ->leftjoin('faculties','faculties.id','=','assignments.faculty')
                ->leftJoin('users as ur','ur.id','=','assignments.created_by')
                ->leftjoin('staff','ur.hook_id','=','staff.id')
                ->leftjoin('session','assignments.session_id','=','session.id')
                ->leftjoin('semesters','semesters.id','=','assignments.semesters_id')
                ->leftjoin('timetable_subjects','timetable_subjects.id','=','assignments.subjects_id')
                ->leftjoin('assignment_answers as a_ans',function($j){
                    $j->on('a_ans.assignments_id','=','assignments.id')
                    ->where(function($q){
                        $q->where('approve_status','!=',1)
                        ->orWhere('approve_status','!=',2);
                    });
                })
                ->selectRaw('COUNT(a_ans.id) as submitted_answers')
                ->groupBy('assignments.id')
                ->get();
            }
    
        return $this->return_data_in_json($data,$error_msg="");
    } 
     public function add_assignment(Request $request){
        Log::debug("add_assignment");
        Log::debug($request->all());
        $staffId=(isset($request->user_id))?$request->user_id:null;
        $year=(isset($request->yearId))?$request->yearId:null;
        $sem=(isset($request->semester))?$request->semester:null;
        $subject=(isset($request->subjectId))?$request->subjectId:null;
        $pub_date=(isset($request->pubDate))?$request->pubDate:null;
        $end_date=(isset($request->endDate))?$request->endDate:null;
        $title=(isset($request->assignmentTitle))?$request->assignmentTitle:null;
        $desc=(isset($request->assignmentDescription))?$request->assignmentDescription:null;
        $status=1;

        $session=(isset($request->sessionId))?$request->sessionId:null;
        $faculty=(isset($request->faculty))?$request->faculty:null;
        $branch_id=(isset($request->branchId))?$request->branchId:null;

        $date=Carbon\Carbon::now();
         $id = DB::table('assignments')->insertGetId(
            ['created_by' => $staffId, 'years_id' => $year, 'semesters_id' => $sem,'subjects_id'=>$subject,'publish_date'=>$pub_date,'end_date'=>$end_date,'title' => $title, 'description' => $desc, 'status' => $status,'session_id'=>$session,'faculty'=>$faculty,'branch_id'=>$branch_id,'created_at'=>$date->toDateTimeString(),'updated_at'=>$date->toDateTimeString()]
        );
         $data['insertdeId']=$id;
         
        if($id){
            $this->sendAssignmentNotification($id);
        }

        return $this->return_data_in_json($data,$error_msg="");
    }
    public function add_assignment_file(Request $request){
        
        $id=$request->id;
        if ($request->hasFile('attach_file')){
            $date=Carbon\Carbon::now();
            $name = str_slug($date->toDateTimeString());
            $attach_file = $request->file('attach_file');
            $file_name = rand(4585, 9857).'_'.$name.'.'.$attach_file->getClientOriginalExtension();
            $path=public_path().DIRECTORY_SEPARATOR.'assignments'.DIRECTORY_SEPARATOR.'questions'.DIRECTORY_SEPARATOR;
            $attach_file->move($path,$file_name);
        }else{
            $file_name = "";
        }
           $d= DB::table('assignments')
            ->where('id', $id)
            ->update(['file' => $file_name]);
        if($d==1){
            $sta['message']="Assignment Added";
            $sta['status']=1;
        }
        else{
            $sta['message']="Something went wrong"; 
            $sta['status']=0;
        }
        return $this->return_data_in_json($sta,$error_msg="");
    }
    
    
    
     /*NEW COPIED FROM SHEAT */
        /*Student Assignment Answer */
       public function post_student_assignment_answer(Request $request){
            $data=[];
            $status='';
            $assignmentId=(isset($request->assignmentId))?$request->assignmentId:null;
            $studentId=(isset($request->studentId))?$request->studentId:null;
            $answerText=(isset($request->answerText))?$request->answerText:' ';
            $userId=DB::table('users')->select('id')->where('hook_id',$studentId)->first();
            $date=Carbon\Carbon::now();
           
            if ($request->hasFile('attach_file')){
                if($request->attach_file){
                    //  dd($request->all());
                    $date=Carbon\Carbon::now();
                    $name = str_slug($date->toDateTimeString());
                    $attach_file = $request->file('attach_file');
                    $file_name = rand(4585, 9857).'_'.$name.'.'.$attach_file->getClientOriginalExtension();
                    $path=public_path().DIRECTORY_SEPARATOR.'assignments'.DIRECTORY_SEPARATOR.'answers'.DIRECTORY_SEPARATOR;
                    $attach_file->move($path,$file_name);
                }
                
            }else{
                $file_name = "";
            }
            if($userId!=null){
                if($file_name){
                    $status= DB::table('assignment_answers')->updateOrInsert(
                    [
                        'students_id'=>$studentId,
                        'assignments_id'=>$assignmentId,
                    ],
                    
                    [
                    'created_by'=>$userId->id,
                    
                    // 'answer_text'=>$answerText,
                    'created_at'=>$date->toDateTimeString(),
                    'status'=>1,
                    'file' => $file_name
                    ]);  
                }else{
                    $status= DB::table('assignment_answers')->updateOrInsert(
                    [
                        'students_id'=>$studentId,
                        'assignments_id'=>$assignmentId,
                    ],
                    
                    [
                    'created_by'=>$userId->id,
                    
                    'answer_text'=>$answerText,
                    'created_at'=>$date->toDateTimeString(),
                    'status'=>1,
                    // 'file' => $file_name
                    ]);  
                }
              
            }
            if($status==1){
                $data['message']="Assignment Added";
                $data['status']=1;
            }
            else{
                $data['message']="Something went wrong"; 
                $data['status']=0;
            }
            return $this->return_data_in_json($data,$error_msg="");
        }
        /*Student Assignment Answer END*/
        
    /*NEW COPIED FROM SHEAT END */
    
    
    public function get_transport_details($memId="",$memType=""){
            $data=[];
            if($memId==""){
                return $data;
            }
            else{
                    if($memType==1){
                         $data['transport']=DB::table('transport_users')->select('students.first_name','vehicles.number as vehicles_number','vehicles.type as vehical_type','routes.title as route','routes.rent as rent','from_date','to_date','transport_users.status','stoppages.title as stoppage')
                         ->where([
                            ['transport_users.member_id','=',$memId],
                            ['transport_users.user_type','=',1]
                         ])
                         ->leftjoin('students','students.id','=','transport_users.member_id')
                         ->leftjoin('routes','routes.id','=','transport_users.routes_id')
                         ->leftjoin('vehicles','vehicles.id','=','transport_users.vehicles_id')
                         ->leftjoin('stoppages','stoppages.id','=','transport_users.stoppage_id')
                         ->orderBy('transport_users.created_at','Desc')
                         ->get();
                    }
                    if($memType==2){
                         $data['transport']=DB::table('transport_users')->select('staff.first_name','staff.middle_name','staff.last_name','vehicles.number','vehicles.type as vehicles_type','routes.title as route','routes.rent as rent','from_date','to_date','transport_users.status','stoppages.title as stoppage')
                         ->where([
                            ['transport_users.member_id','=',$memId],
                            ['transport_users.user_type','=',2]
                         ])
                         ->leftjoin('staff','staff.id','=','transport_users.member_id')
                         ->leftjoin('routes','routes.id','=','transport_users.routes_id')
                         ->leftjoin('vehicles','vehicles.id','=','transport_users.vehicles_id')
                         ->leftjoin('stoppages','stoppages.id','=','transport_users.stoppage_id')
                         ->orderBy('transport_users.created_at','Desc')
                         ->get();
                    }

            }

         return $this->return_data_in_json($data,$error_msg="");   
    }
    public function assignment_submitted(Request $request){
        $data=[];
        $assignmentId=(isset($request->aId))?$request->aId:null;
        if($assignmentId==null){
            return $data;
        }   
        $fileLinkAssignment = asset('assignments'.DIRECTORY_SEPARATOR.'answers');
        $fileLinkAssignment = addslashes($fileLinkAssignment).'/';
        $fileLinkQuestion = asset('assignments'.DIRECTORY_SEPARATOR.'questions');
        $fileLinkQuestion = addslashes($fileLinkQuestion).'/';
        $data['assignment']=DB::table('assignments')->select('assignments.created_by as staff_id',DB::raw("CONCAT(staff.first_name,' ',staff.middle_name,' ',staff.last_name)as staff_name"),'faculties.faculty','assignments.session_id','session.session_name as session','assignments.semesters_id','semesters.semester','assignments.subjects_id','subjects.title as subject_name','assignments.publish_date','assignments.end_date','assignments.title','assignments.description',DB::raw("CONCAT('$fileLinkQuestion',file) as assignment"))
            ->Where([
                ['assignments.id','=',$assignmentId],
            ])
            ->leftjoin('faculties','faculties.id','=','assignments.faculty')
            ->leftjoin('staff','assignments.created_by','=','staff.id')
            ->leftjoin('session','assignments.session_id','=','session.id')
            ->leftjoin('semesters','semesters.id','=','assignments.semesters_id')
            ->leftjoin('subjects','subjects.id','=','assignments.subjects_id')
            ->get();

        $data['submitted_assignment']=DB::table('assignment_answers')->select('students.reg_no','students.first_name','answer_text',DB::raw("CONCAT('$fileLinkAssignment',assignment_answers.file) as submitted_file"),'approve_status','assignments.title','assignment_answers.created_at','faculties.faculty','students.date_of_birth','students.gender','students.reg_date')
            ->where([
                ['assignments_id','=',$assignmentId]
            ])

            ->leftjoin('students','students.id','=','assignment_answers.students_id')
            ->leftjoin('assignments','assignments.id','=','assignment_answers.assignments_id')
            ->join('faculties','students.faculty','=','faculties.id')
            ->get();
        return $this->return_data_in_json($data,$error_msg="");
    }
    public function post_Assignment_comment_student(Request $request){
        $data=[];
        $assignmentId=(isset($request->assignmentId))?$request->assignmentId:null;
        $answerId=(isset($request->answerId))?$request->answerId:null;
        $stdId=(isset($request->stdId))?$request->stdId:null;
        $comment=(isset($request->comment))?$request->comment:null;
        $type=(isset($request->MemberType))?$request->MemberType:null;
        $name=(isset($request->name))?$request->name:null;
        if(empty($comment)){
            return $data;
        }
        if($request->MemberType==1){
            $data['comment']=DB::table('assignment_comments')->insert([
                    'comment'=>$comment,
                    'assignment_id'=>$assignmentId,
                    'answer_id'=>$answerId,
                    'member_id'=>$stdId,
                    'member_name'=>$name,
                    'member_type'=>1
                ]);
        }
        elseif($request->MemberType==2){
             $data['comment']=DB::table('assignment_comments')->insert([
                    'comment'=>$comment,
                    'assignment_id'=>$assignmentId,
                    'answer_id'=>$answerId,
                    'member_id'=>$stdId,
                    'member_name'=>$name,
                    'member_type'=>2
                ]);
        }
        
        return $this->return_data_in_json($data,$error_msg="");
    }
    public function post_Assignment_submit(Request $request){
        $data=[];
        $assignmentId=(isset($request->assignmentId))?$request->assignmentId:null;
        if(!empty($assignmentId)){
            $path=asset('assignments'.DIRECTORY_SEPARATOR.'answers'.DIRECTORY_SEPARATOR);
            $data['answerList']=DB::table('assignment_answers')->select('assignment_answers.*','st.first_name','st.reg_no',DB::raw("CONCAT('$path','/',file) as file"))
            ->leftjoin('students as st','st.id','=','assignment_answers.students_id')
            ->where(function($query) use ($request){
                if(!empty($request->StudentId)){
                    $query->where('students_id','=',$request->StudentId);
                    $this->filter_query['students_id'] = $request->StudentId;
                }
                $query->where('assignments_id','=',$request->assignmentId);
                $this->filter_query['id']=$request->assignmentId;   
                })
                ->where('assignment_answers.status',1)
            ->get(); 
        }
        else{
            return $data;
        }
         return $this->return_data_in_json($data,$error_msg="");
    } 
    public function view_assignment_answer(Request $request){
        $assignmentId=(isset($request->assignmentId))?$request->assignmentId:null;
        $answerId=(isset($request->answerId))?$request->answerId:null;
        $data=[];
        if(!empty($assignmentId) && !empty($answerId)){
            $data['answer']=DB::table('assignment_answers')->select('*')
            ->where('id',$answerId)
            ->get(); 
            $data['question']=DB::table('assignments')->select('*')
            ->where('id',$assignmentId)
            ->get();
            $data['comment']=DB::table('assignment_comments')->select('*')
            ->where('assignment_id',$assignmentId)
            ->where('answer_id',$answerId)
            ->get(); 
        }
        else{
            return $data;
        }
         return $this->return_data_in_json($data,$error_msg="");
    }
    public function post_assignment_status(Request $request){
        $data=[];
        $answerId=(isset($request->answerId))?$request->answerId:null;
        $status=(isset($request->status))?$request->status:null;
        $assignmentId=(isset($request->assignmentId))?$request->assignmentId:null;
        $staffId=(isset($request->staffId))?$request->staffId:null;
        $comment=(isset($request->comment))?$request->comment:null;
        $name=(isset($request->staffName))?$request->staffName:null;
        $type=(isset($request->staffType))?$request->staffType:null;
        if (!$row = AssignmentAnswer::find($answerId)){
            return $data;
        }
        else{
            if(!empty($status)){
                $request->request->add(['approve_status' => $status]);
                $data['status']=$row->update($request->all());
            }
             else{
                $data['status']=false;
             }   

            if(!empty($comment && $answerId && $assignmentId && $staffId && $type )){
                 $data['comment']=DB::table('assignment_comments')->insert([
                    'comment'=>$comment,
                    'assignment_id'=>$assignmentId,
                    'answer_id'=>$answerId,
                    'member_id'=>$staffId,
                    'member_name'=>$name,
                    'member_type'=>$type
                ]);
            }
             else{
                $data['comment']=false;
             }   

        }  
        return $this->return_data_in_json($data,$error_msg="");
        
    }

    public function get_branch_list(){
        $branch_list=DB::table('branches')->select('id','branch_title','branch_name','is_iimt','api_url')
        ->where('record_status','!=','3')
         ->get();
            
         $i=0;
        foreach ($branch_list as $key => $value) {
            
            $data[$i]['id']=$value->id;
            $data[$i]['is_iimt']=$value->is_iimt;
            $data[$i]['api_url']=$value->api_url;
            $data[$i]['branch_title']=$value->branch_name;
            $i++;
        }

        $prep = ['branch_title'=>'-Branch-','id'=>'0','is_iimt'=>'','api_url'=>''];
        array_unshift($data,$prep);

        return $this->return_data_in_json($data,$error_msg="");
    }

    public function get_faculty_course_list($branch_id=""){
        $data = [];
        if($branch_id==""){
            $faculty=DB::table('faculties')->select('id','faculty')
            ->get();
        }else{
            $faculty=DB::table('faculties')->select('id','faculty')
            ->where('branch_id',$branch_id)
            ->get();
        }
        $i=0;
        foreach ($faculty as $key => $value) {
            $data[$i]['course']=$value->faculty;
            $data[$i]['id']=$value->id;
            $i++;

        }
        $prep = ['course'=>'-'.env('course_label').'-','id'=>'0'];
        array_unshift($data,$prep);
        return $this->return_data_in_json($data,$error_msg="");
   }

   public function get_session_batch_list(){
    
        $sessionList=DB::table('session')->select('id','session_name')
         ->get();
         $i=0;
        foreach ($sessionList as $key => $value) {
            $data['session'][$i]['id']=$value->id;
            $data['session'][$i]['session_name']=$value->session_name;
            $i++;
         }
         $prep = ['session_name'=>'-Session-','id'=>'0'];
        array_unshift($data['session'],$prep);
        return $this->return_data_in_json($data,$error_msg="");
   }
    public function get_course_branch($branch_id,Request $request){
        
        $classTeachercourse =[];
        
        $role = DB::table('role_user')->select('role_user.role_id')
        ->rightJoin('permission_role','permission_role.role_id','=','role_user.role_id')
        ->where('user_id',$request->user_id)
        ->where('permission_id',583)
        ->whereNotIn('role_user.role_id',[1,2,3])
        ->get();
        
        if(count($role) > 0){
            
        }else{
            if($request->staff_id && $request->session_id){
            
                $classTeachercourse= TeacherCoordinator::where('teacher_id',$request->staff_id)
                ->where('branch_id',$branch_id)
                ->where('record_status',1)
                //->where('session_id',$request->session_id)
                ->pluck('faculty_id')->toArray();
            
            }
        }
        
        
        
        $cources=DB::table('faculties')->select('id','faculty as course_name')->where([
            ['status','=',1],
            ['branch_id','=',$branch_id]
        ])
        ->where(function($q)use($classTeachercourse){
            if(count($classTeachercourse)>0){
                $q->whereIn('id',$classTeachercourse);
            }
        })
        ->orderBy('course_name','asc')
        ->get();
       
        $k =0;
        foreach ($cources as $val) {
            $data['course'][$k]['id']=$val->id;
            $data['course'][$k]['course_name']=$val->course_name;
            $k++;
         }
        //  $prep = ['course_name'=>'-'.env('course_label').'-','id'=>'0'];
        // array_unshift($data['course'],$prep);
        $section=DB::table('semesters')->select('id','semester as section')->where([
            ['status','=',1]
        ])
        ->get();
        $k=0;
        foreach ($section as $val) {
            $data['section'][$k]['id']=$val->id;
            $data['section'][$k]['course_name']=$val->section;
            $k++;
         }
        //  $prep = ['course_name'=>'-Section/Sem-','id'=>'0'];
        // array_unshift($data['section'],$prep);
        

         return $this->return_data_in_json($data,$error_msg="");
   }
   public function get_section_by_course($course_id,Request $request){
       
       $classTeacherSection =[];
        if($request->staff_id && $request->session_id){
            
             $classTeacherSection= TeacherCoordinator::where('teacher_id',$request->staff_id)
            ->where('faculty_id',$course_id)
            ->where('record_status',1)
            ->where('session_id',$request->session_id)->pluck('section_id')->toArray();
        
        }
      
       
       
       
        $section=DB::table('faculty_semester')->select('sem.id','sem.semester as section')
        ->leftjoin('semesters as sem',function($j){
            $j->on('sem.id','=','faculty_semester.semester_id')
            ->where('sem.status',1);
        })
        ->where([
            ['faculty_semester.faculty_id','=',$course_id]
        ])
        ->where(function($q)use($classTeacherSection){
            if(count($classTeacherSection)>0){
                $q->whereIn('sem.id',$classTeacherSection);
            }
        })
        ->get();
        $k=0;
        foreach ($section as $val) {
            $data['section'][$k]['id']=$val->id;
            $data['section'][$k]['course_name']=$val->section;
            $k++;
         }
        //  $prep = ['course_name'=>'-Section/Sem-','id'=>'0'];
        // array_unshift($data['section'],$prep);
        

         return $this->return_data_in_json($data,$error_msg="");
   }
    public function checkTeacher(Request $request,$from="",$to="",$day="",$staff_id="")
    {
      
        $response = [];
        $response['error'] = true;
        if($request->staff_id){
           if($request->from && $request->to){
             $from=Carbon\Carbon::parse($request->from)->addMinute()->format('H:i:s');
             $to=Carbon\Carbon::parse($request->to)->subMinute()->format('H:i:s');
                             
               $data['check']=DB::table('timetable')->select('*')
                ->where([
                  ['day_id','=',$request->day],
                  ['staff_id','=',$request->staff_id],
                  ['is_break','=',0]
                ])
                ->where(function($query) use ($from,$to){ 
                        $query->where([
                        ['time_from','<=',$from],
                        ['time_to','>=',$from]
                                ])
                     ->orWhere([
                        ['time_from','<=',$to],
                        ['time_to','>=',$to]
                     ]);        
                })
                ->get();
            if($data['check']){
                $response['teacher']= $data['check'];
                $response['error']=false;
                $response['success'] = 'Data Checked.';
            } else {
                $response['error'] = 'Not Checked';
            }
           }
                $data['subject']=DB::table('timetable_assign_subject')->select('sbj.id','sbj.title as subject',DB::raw("CONCAT(COALESCE(fac.faculty,''),' - ',sbj.title) as subject"))
                ->leftjoin('timetable_subjects as sbj','sbj.id','=','timetable_assign_subject.timetable_subject_id')
                ->leftJoin('faculties as fac','fac.id','=','sbj.course_id')
                ->where([
                    ['staff_id',$request->staff_id],
                    ['sbj.status',1],
                    ['timetable_assign_subject.status',1]
                ])
                ->orderBy('subject','ASC')->get();
                if($data['subject']){
                    $response['subject']=$data['subject'];
                    $response['error']=false;
                    $response['success'] = 'Subject Fount';
            } else {
                $response['error'] = 'No Subject.';
            }
           
           
        }else {
            $response['message'] = 'Invalid request!!';
        }
        return response()->json(json_encode($response));
       
    }
    public function loadSchedule(Request $request){
        $response=[];
        $response['error']=true;
        if($request->course && $request->section){
            $currentDate=Carbon\Carbon::now()->format('Y-m-d');
            $weekday=DB::select(DB::raw("SELECT dayofweek('$currentDate') as day"));
            $weekday=$weekday[0]->day;
            $schedule=DB::table('timetable')->select('timetable.id','day_id',DB::raw("date_format(time_from,'%H:%i') as time_from "),DB::raw("date_format(time_to,'%H:%i') as time_to "),'sbj.title','timetable.staff_id',DB::raw("CONCAT(st.first_name,' ',st.last_name) as staff"),'room_no','subject_type',DB::raw("CONCAT(altstf.first_name,' ',altstf.last_name) as altTeacher"))
            ->leftjoin('staff as st','st.id','=','timetable.staff_id')
            ->leftjoin('timetable_subjects as sbj','sbj.id','=','timetable.timetable_subject_id')
            
            ->leftjoin('timetable_alt_teacher as alt',function($q){
                $q->on('alt.timetable_id','=','timetable.id')
                    ->where('alt.date',Carbon\Carbon::now()->format('Y-m-d'));
            })
            ->leftjoin('staff as altstf','altstf.id','=','alt.staff_id')
            
            ->where([
                ['sbj.status','=',1],
                ['timetable.course_id','=',$request->course],
                ['timetable.section_id','=',$request->section],
                // ['alt.dates','=',$currentDate]
            ])
            ->orderBy('time_from','ASC')
            ->get();
             $day=DB::table('days')->select('id','title')->orderBy('id','ASC')->get();
             $count=DB::table('timetable')->select(DB::raw('count(day_id) as times'))
             ->groupBy('day_id')
             ->orderBy('times','DESC')
             ->limit(1)
             ->get();
             
            foreach ($day as $key => $val) {
                foreach ($schedule as $key => $value) {
                    if($value->day_id==$val->id){
                        $data[$value->day_id][]=$value;            
                    }
                }
            }
            foreach ($data as $key => $value) {
                foreach ($value as $k => $val) {
                    for($i=1;$i<=7;$i++){
                        $disp[$k][$i][$val->day_id]=$val;  
                    }
                }
            }
            
            if($schedule){
                
                $response['schedule']=$disp;
                $response['day']=$day;
                $response['weekday']=$weekday;
                $response['error']=false;
                $response['success']='Schedule Search Successful';
            }else {
                $response['error'] = 'No Schedule.';
            }
        }   else {
            $response['message']='Invalid Request';
        }
        return response()->json(json_encode($response));
        // return $this->return_data_in_json($response,$error_msg="");

    }
    public function loadSection(Request $request){
        $response=[];
        $response['error']=true;
        if($request->course){
             $section=Faculty::select('semesters.id','semesters.semester')
              ->where('faculties.id',$request->course)
            //   ->distinct('semesters.id')
              ->leftjoin('faculty_semester','faculty_semester.faculty_id','=','faculties.id')
              ->leftjoin('semesters','semesters.id','=','faculty_semester.semester_id')
              ->groupBy('semesters.id')
              ->orderBy('semester','ASC')
              ->get();
         if($section){
                $response['section']=$section;
                $response['error']=false;
                $response['success']='Section Found';
            }else {
                $response['error'] = 'No Section.';
            }
        }   else {
            $response['message']='Invalid Request';
        }
        return response()->json(json_encode($response));

    }
    public function deleteSchedule(Request $request){
        $response=[];
        $response['error']=true;
        if($request->id){

            $id=explode('/', $request->id);
            $id=$id[1];
            $data=DB::table('timetable')->where('id',$id)->delete();
            if($data){
                $response['delete']=$data;
                $response['error']=false;
                $response['success']='Deleted';
            }
            else{
                $response['error']="Not Deleted";
            }
        }
        else{
                $response['message']='Invalid Request';
            }
            return response()->json(json_encode($response));
    }
   
    public function autoAssign(Request $request){
        $response=[];
        $response['error']=true;
        $currentDate=Carbon\Carbon::now()->format('Y-m-d');
        $weekday=DB::select(DB::raw("SELECT dayofweek('$currentDate') as day"));
        $weekday=$weekday[0]->day;

        $date=Carbon\Carbon::now()->format('Y-m-d');
        $date=explode('-', $date);
        $month=$date[1];
        if($month[0]==0){
            $month=$month[1];
        }
        $day=$date[2];
        if($day[0]==0){
            $day=$day[1];
        }
       $data['timetable']=DB::table('timetable')->select('timetable.id','fcl.faculty as course',"att.day_$day as present",'timetable.course_id','timetable.section_id','sem.semester as section','sub.title as subject','timetable_subject_id as subject_id','subject_type as type',DB::raw("date_format(time_from,'%H:%i') as time_from "),DB::raw("date_format(time_to,'%H:%i') as time_to "),'room_no',DB::raw("CONCAT(st.first_name,' ',st.last_name) as staff"),'timetable.staff_id')
        ->leftjoin('attendances as att','att.link_id','=','timetable.staff_id')
        ->leftjoin('years','years.id','=','att.years_id')
        ->leftjoin('faculties as fcl','fcl.id','=','timetable.course_id')
        ->leftjoin('semesters as sem','sem.id','=','timetable.section_id')
        ->leftjoin('staff as st','st.id','=','timetable.staff_id')
        ->leftjoin('timetable_subjects as sub','sub.id','=','timetable.timetable_subject_id')
        ->where([
          ['timetable.day_id','=',$weekday],
          ['timetable.branch_id','=',1],
          ['timetable.session_id','=',2],
          ['timetable.status','=',1],
          ['att.attendees_type','=',2],
          ['att.months_id','=',$month],
          ['years.title','=',$date[0]],
          
        ])
        ->get();
        
        foreach ($data['timetable'] as $key => $value) {
          $data['schedule'][$value->course_id][$value->section_id][]=$value;
          if($value->present!=1){
            $data['subject'][$value->id]=$value;
          }
        }
        if(isset($data['subject'])){
            foreach ($data['subject'] as $key => $value) {
          $data['available']=DB::table('timetable_assign_subject')->select('timetable_assign_subject.staff_id')
          ->leftjoin('staff as st','st.id','=','timetable_assign_subject.staff_id')
          ->leftjoin('attendances as att','st.id','=','att.link_id')
         
          ->where([
            ['timetable_assign_subject.staff_id','!=',$value->staff_id],
            ['timetable_assign_subject.timetable_subject_id','=',$value->subject_id],
            
          ])
          ->get();
            foreach ($data['available'] as $key => $available) {
          if(!empty($available->staff_id)){
                $present=DB::table('attendances')->select('attendances.id')
                 ->leftjoin('years','years.id','=','attendances.years_id')
                ->where([ 
                  ['link_id','=',$available->staff_id],
                  ['attendees_type','=',2],
                  ['months_id','=',$month],
                  ['title','=',$date[0]],
                  ['day_'.$day,'=','1']
                ])
                ->get();
                $testavb[][$available->staff_id][]=$present;
                if(count($present)>0){
                     $check[$value->id]=$this->checkInTimetable($value->time_from,$value->time_to,$weekday,$available->staff_id);
                  if(count($check[$value->id])==0){
                      $avl=$available;
                      $altchk[$value->id]=$this->CheckInAltTimetable($value->time_from,$value->time_to,$avl->staff_id);
                      if(count($altchk[$value->id])==0){
                          $assignCheck[$value->id]=DB::table('timetable_alt_teacher')->where([
                            ['timetable_id','=',$value->id],
                            ['date','=',$currentDate]
                          ])
                          ->get();
                          if(count($assignCheck[$value->id])==0){
                              $assignTeacher[]=DB::table('timetable_alt_teacher')->insertGetId([
                               'date'=>$currentDate,
                               'timetable_id'=> $value->id,
                               'staff_id'=>$avl->staff_id
                              ]);
                          }
                      }
                    }
                }    
          }
        }
          
        }
        }
        if(isset($assignTeacher)){
           $response['assign']=$assignTeacher;
           $response['error']=false;
           $response['success']='Available Teacher Assigned, Please Reload';
        }else{
            $response['message']='No teacher available to assign';
        }
        return response()->json(json_encode($response));
    }
    public function checkInTimetable($from,$to,$day,$staff_id){
         if($from && $to && $day && $staff_id){
            $from=Carbon\Carbon::parse($from)->addMinute()->format('H:i:s');
             $to=Carbon\Carbon::parse($to)->subMinute()->format('H:i:s');
            
             
               $data['check']=DB::table('timetable')->select('*')
                ->where([
                    ['day_id','=',$day],
                    ['staff_id','=',$staff_id],
                    ['is_break','=',0]
                  ])
                  ->where(function($query) use ($from,$to){
                        $query->where([
                        ['time_from','<=',$from],
                        ['time_to','>=',$from]
                                ])
                     ->orWhere([
                        ['time_from','<=',$to],
                        ['time_to','>=',$to]
                     ]);
                  })
                ->get();
         return  $data['check'];     

       }
       else{
        return $data['check']="Invalid Request";
       }
    }
     public function CheckInAltTimetable($from,$to,$staff_id){
      $date=Carbon\Carbon::now()->format('Y-m-d');
         if($from && $to && $staff_id){
            $from=Carbon\Carbon::parse($from)->addMinute()->format('H:i:s');
             $to=Carbon\Carbon::parse($to)->subMinute()->format('H:i:s');
               $data['check']=DB::table('timetable_alt_teacher')->select('timetable_alt_teacher.*')
                    ->leftjoin('timetable as tmb','tmb.id','=','timetable_alt_teacher.timetable_id')
                    ->where([
                        ['timetable_alt_teacher.staff_id','=',$staff_id],
                        ['date','=',$date],
                        ['is_break','=',0]
                    ])
                  ->where(function($query) use ($from,$to){
                        $query->where([
                        ['time_from','<=',$from],
                        ['time_to','>=',$from]
                                ])
                     ->orWhere([
                        ['time_from','<=',$to],
                        ['time_to','>=',$to]
                     ]);
                  })
                ->get();
         return  $data['check'];
       }
       else{
        return $data['check']="Invalid Request";
       }
    }
    public function studentTimetable(Request $request){
        $response=[];
        $response['error']=true;
        if($request->course && $request->section){
            $currentDate=Carbon\Carbon::now()->format('Y-m-d');
            $weekday=DB::select(DB::raw("SELECT dayofweek('$currentDate') as day"));
            $weekday=$weekday[0]->day;
            $date=Carbon\Carbon::now();
            $data['atten']=DB::table('timetable')->select('time_from','time_to','subject_type','room_no',DB::raw("CONCAT(st.first_name,' ',st.last_name) as staff"),'sub.title as subject',DB::raw("CONCAT(altT.first_name,' ',altT.last_name) as altTeacher"))
            ->leftjoin('staff as st','st.id','=','timetable.staff_id')
            ->leftjoin('timetable_alt_teacher as alt','alt.timetable_id','=','timetable.id')
            ->leftjoin('staff as altT','altT.id','=','alt.staff_id')
           ->where([
            ['course_id','=',$request->course],
            ['section_id','=',$request->section],
            ['day_id','=',$weekday],
            ['alt.date','=',$currentDate]
           ])
           ->get();
            return response()->json(json_encode($data['atten']));
        }
    }
    public function getSubjects($branchId="",$session=""){
        $data=[];
        $subject=DB::table('timetable_subjects')->select('*')->where([
            ['branch_id','=',$branchId],
            ['session_id','=',$session],
            ['status','=',1]
        ])->get();
        foreach ($subject as $key => $value) {
           $data[$value->course_id][$value->session_id][]=$value->title;
        }
        return $this->return_data_in_json($data,$error_msg="");
    }
    public function postWeeklyTimetable(Request $request){
        $data=[];
        $branchId=(isset($request->branchId))?$request->branchId:null;
        $sessionId=(isset($request->sessionId))?$request->sessionId:null;
        $courseId=(isset($request->courseId))?$request->courseId:null;
        $secId=(isset($request->secId))?$request->secId:null;
        if($branchId &&  $sessionId && $courseId && $secId){
            $sch=DB::table('timetable')->select('time_from','time_to','room_no','subject_type',DB::raw("CONCAT(stf.first_name,' ',stf.last_name) as teacher"),DB::raw("CONCAT(altsf.first_name,' ',altsf.last_name) as alt_teacher"),DB::RAW("CONCAT(sm.title,' ( ',f.faculty,'-',sem.semester,' ) ') as subject"),'days.title as day')
            ->leftjoin('timetable_alt_teacher as alt',function($q){
                $q->on('alt.timetable_id','=','timetable.id')
                    
                    ->where('date',Carbon\Carbon::now()->format('Y-m-d'));
            })->leftjoin('staff as stf','stf.id','=','timetable.staff_id')
            ->leftjoin('staff as altsf','altsf.id','=','alt.staff_id')
            ->leftjoin('timetable_subjects as ts','ts.id','=','timetable.timetable_subject_id')
             ->leftjoin('subject_master as sm','sm.id','=','ts.subject_master_id')
              ->leftjoin('faculties as f','f.id','=','ts.course_id')
              ->leftjoin('semesters as sem','sem.id','=','ts.section_id')
            ->leftjoin('days','days.id','=','timetable.day_id')
            ->where([
                ['timetable.branch_id','=',$branchId],
                ['timetable.session_id','=',$sessionId],
                ['timetable.course_id','=',$courseId],
                ['timetable.section_id','=',$secId]
            ])
            ->orderBy('day_id','ASC')
            ->orderBy('time_from','ASC')
            ->get();
            foreach ($sch as $key => $value) {
               $data[$value->day][]=$value;
           }
           
        }else{
            return $data['error']="Invalid Request";
        }
        return $this->return_data_in_json($data,$error_msg="");
    }
    public function postTeacherDailyTimetable(Request $request){
       $branchId=(isset($request->branchId))?$request->branchId:null;
        $sessionId=(isset($request->sessionId))?$request->sessionId:null;
        $courseId=(isset($request->courseId))?$request->courseId:null;
        $secId=(isset($request->secId))?$request->secId:null;
        $teacherId=(isset($request->teacherId))?$request->teacherId:null;
        $currentDate=(isset($request->date))?$request->date:(Carbon\Carbon::now()->format('Y-m-d'));
         
        if($branchId &&  $sessionId && $courseId && $secId && $teacherId){

        
        $weekday=DB::select(DB::raw("SELECT dayofweek('$currentDate') as day"));
        $weekday=$weekday[0]->day;

        $date=$currentDate;
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
          ['timetable.branch_id','=',$branchId],
          ['timetable.session_id','=',$sessionId],
          ['timetable.status','=',1],
          ['timetable.staff_id','=',$teacherId],
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
            ['timetable_alt_teacher.staff_id','=',$teacherId]
        ])
        ->orderBy('time_from','ASC')
        ->get();
        $sch['actual_schedule']=$data['timetable'];
        $sch['alternate_schedule']=$data['alt'];
        }else{
            return $data['error']="Invalid Request";
        }
        return $this->return_data_in_json($sch,$error_msg="");
    }
    public function postStudentDailyTimetable(Request $request){
        
        $branchId=(isset($request->branchId))?$request->branchId:null;
        $sessionId=(isset($request->sessionId))?$request->sessionId:null;
        $courseId=(isset($request->courseId))?$request->courseId:null;
        $secId=(isset($request->secId))?$request->secId:null;
        $studentId=(isset($request->studentId))?$request->studentId:null;
        $currentDate=(isset($request->date))?$request->date:(Carbon\Carbon::now()->format('Y-m-d'));
        $staff_id = (isset($request->staff_id))?$request->staff_id:null;
         $data = [];
        if($branchId &&  $sessionId && $courseId && $secId && $studentId){

            $weekday=DB::select(DB::raw("SELECT dayofweek('$currentDate') as day"));
            $weekday=$weekday[0]->day;
    
            $date=$currentDate;
    
                $date=explode('-', $date);
                $month=$date[1];
                if($month[0]==0){
                  $month=$month[1];
                }
                $day=$date[2];
                if($day[0]==0){
                  $day=$day[1];
                }
               $faculty=DB::table('student_detail_sessionwise')->select('course_id','Semester')->where([
                ['session_id','=',$sessionId],
                ['student_id','=',$studentId]
            ])
               ->get();
               $faculty[0]->course_id = $courseId;
               $faculty[0]->Semester = $secId;
               
    
            $data['timetable']=DB::table('timetable')->select('timetable.id','fcl.faculty as course',DB::raw("COALESCE(att.day_$day,'0') as present"),'timetable.course_id','timetable.section_id','sem.semester as section','sub.title as subject','timetable_subject_id as subject_id','subject_type as type',DB::raw("date_format(time_from,'%H:%i') as time_from "),DB::raw("date_format(time_to,'%H:%i') as time_to "),'room_no',DB::raw("CONCAT(st.first_name,' ',st.last_name) as staff"),'timetable.staff_id',DB::raw("CONCAT(altstf.first_name,' ',altstf.last_name) as altTeacher"),'alt.staff_id as alternate')
            ->leftjoin('attendances as att',function($j)use($month){
                $j->on('att.link_id','=','timetable.staff_id')
                ->where([
                         ['att.attendees_type','=',2],
                    ['att.months_id','=',$month],
                    ]);
            })
            ->leftjoin('years',function($j)use($date){
                $j->on('years.id','=','att.years_id')
                ->where([
                    ['years.title','=',$date[0]],
                    ]);
            })
            ->leftjoin('faculties as fcl','fcl.id','=','timetable.course_id')
            ->leftjoin('semesters as sem','sem.id','=','timetable.section_id')
            ->leftjoin('staff as st','st.id','=','timetable.staff_id')
            ->leftjoin('timetable_subjects as sub','sub.id','=','timetable.timetable_subject_id')
            ->leftjoin('timetable_alt_teacher as alt',function($j)use($currentDate){
                $j->on('alt.timetable_id','=','timetable.id')
                ->where('alt.date',$currentDate);
            })
            
            ->leftjoin('staff as altstf','altstf.id','=','alt.staff_id')
            
            ->where([
              ['timetable.day_id','=',$weekday],
              ['timetable.branch_id','=',$branchId],
              ['timetable.session_id','=',$sessionId],
              ['timetable.status','=',1],
              ['timetable.course_id','=',$faculty[0]->course_id],
              ['timetable.section_id','=',$faculty[0]->Semester],
             
              
              ['is_break','=',0]
            ])
            ->orderBy('course','Asc')
            ->orderBy('timetable.time_from','Asc')
            ->get();
            $sch=$data['timetable'];
   
        }elseif($branchId &&  $sessionId && $staff_id){
            $weekday=DB::select(DB::raw("SELECT dayofweek('$currentDate') as day"));
            $weekday=$weekday[0]->day;
    
            $date=$currentDate;

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
            ->leftjoin('timetable_alt_teacher as alt',function($j)use($currentDate){
                $j->on('alt.timetable_id','=','timetable.id')
                ->where('alt.date',$currentDate);
            })
            ->leftjoin('staff as altstf','altstf.id','=','alt.staff_id')
            
            ->where([
              ['timetable.day_id','=',$weekday],
              ['timetable.branch_id','=',$branchId],
              ['timetable.session_id','=',$sessionId],
              ['timetable.status','=',1],
              ['timetable.staff_id','=',$staff_id],
              ['att.attendees_type','=',2],
              ['att.months_id','=',$month],
              ['years.title','=',$date[0]],
              ['is_break','=',0],
            ])
            ->orderBy('course','Asc')
            ->orderBy('timetable.time_from','Asc')
            ->get();
            
            $sch=$data['timetable'];
        }else{
            return $sch['error']="Invalid Request";
        }
        return $this->return_data_in_json($sch,$error_msg="");
    }
    public function postStudentExam(Request $request){
        $data=[];
        $courseId=(isset($request->courseId))?$request->courseId:null;
        $secId=(isset($request->secId))?$request->secId:null;
        $yearId=(isset($request->yearId))?$request->yearId:null;
        if($courseId && $secId && $yearId){
            $data=DB::table('exam_schedules')->select('exam_schedules.id as exam_schedule_id','exams.title as exam_name','fac.faculty as faculty','sub.title as subject','mnt.title as month','years.title as year',DB::raw("date_format(date,'%d-%m-%Y') as date"),'start_time','end_time','exam_schedules.full_mark_theory','exam_schedules.pass_mark_theory','exam_schedules.full_mark_practical','exam_schedules.pass_mark_practical')
            ->leftjoin('faculties as fac','fac.id','=','exam_schedules.faculty_id')
            ->leftjoin('months as mnt','mnt.id','=','exam_schedules.months_id')
            ->leftjoin('years','years.id','=','exam_schedules.years_id')
            ->leftjoin('exams','exams.id','=','exam_schedules.exams_id')
            ->leftjoin('subjects as sub','sub.id','=','exam_schedules.subjects_id')
            ->where([
                ['exam_schedules.faculty_id','=',$courseId],
                ['exam_schedules.semesters_id','=',$secId],
                ['exam_schedules.years_id','=',$yearId],
                ['exam_schedules.status','=',1]
            ])->get();
        }else{
            return "Invalid request!";
        }
        return $this->return_data_in_json($data,$error_msg="");
    }
    public function getTeacher(Request $request){
        $data=[];
        $data=Semester::select(DB::raw("CONCAT(staff.first_name,' ',staff.last_name) as staff_name"),'semesters.semester as section','sub.title as subject','sub.code as subject_code','sub.sub_type as subject_type')
        ->leftjoin('semester_subject as semsub','semsub.semester_id','=','semesters.id')
        ->leftjoin('subjects as sub','sub.id','=','semsub.subject_id')
        ->leftjoin('staff','staff.id','=','sub.staff_id')
        ->where('semsub.status',1)
        ->get();
       return $this->return_data_in_json($data,$error_msg="");
    }
    public function getHostelResident($branchId="",$session="",$memberId="",$memberType=""){
        $data=[];
        $data=DB::table('residents')->select('st.first_name as student_name','ht.name as hostel_name','ht.address as hostel_address','ht.contact_detail as hostel_contact','ht.warden','ht.warden_contact','rooms.room_number','beds.bed_number','residents.rent','residents.register_date','residents.leave_date')
        ->leftjoin('students as st','st.id','=','residents.member_id')
        ->leftjoin('hostels as ht','ht.id','=','residents.hostels_id')
        ->leftjoin('rooms','rooms.id','=','residents.rooms_id')
        ->leftjoin('beds','beds.id','=','residents.beds_id')
        ->where([
            ['residents.status','=',1]
        ])->get();
        if(count($data)>0){
            return $this->return_data_in_json($data,$error_msg="");
        }else{
            return "No record found";
        }
    }
    public function getNotice($role_id=""){
        $data=array();
        $notice=DB::table('notices')->select('id','display_group')->where('status',1)->get();

        foreach ($notice as $key => $value) {
           $roles=explode(',',$value->display_group);
            if(in_array($role_id,$roles)){
                $abc=DB::table('notices')->select('*')->where('id',$value->id)->get();
                foreach ($abc as $key => $val) {
                    $data[]=$val;
                }
            }
        }
        if(!count($data)>0){
         $data['error']="No record Found!";   
        }
       return $this->return_data_in_json($data,$error_msg="");
    }
    public function postExamResult(Request $request){
        $examId=(isset($request->examId))?$request->examId:null;
        $yearId=(isset($request->yearId))?$request->yearId:null;
        $courseId=(isset($request->courseId))?$request->courseId:null;
        $semesterId=(isset($request->semesterId))?$request->semesterId:null;
        $stdId=(isset($request->stdId))?$request->stdId:null;
        $data=[];
        if($examId && $semesterId && $courseId && $yearId && $stdId){
           $data=DB::table('exam_schedules')->select('exam_schedules.full_mark_theory','exam_schedules.pass_mark_theory','obtain_mark_theory','absent_theory','exam_schedules.full_mark_practical','exam_schedules.pass_mark_practical','obtain_mark_practical','absent_practical','exams.title as exam','subjects.title as subject')
           ->leftjoin('exam_mark_ledgers as exam','exam.exam_schedule_id','=','exam_schedules.id')
           ->leftjoin('subjects','subjects.id','=','exam_schedules.subjects_id')
           ->leftjoin('exams','exams.id','=','exam_schedules.exams_id')
           ->where([
            ['exam.students_id','=',$stdId],
            ['exam_schedules.exams_id','=',$examId],
            ['exam_schedules.faculty_id','=',$courseId],
            ['exam_schedules.semesters_id','=',$semesterId],
            ['exam_schedules.years_id','=',$yearId],
            ['exam_schedules.publish_status','=',1],
            ['exam_schedules.status','=',1]
           ])->get();
        }else{
            $data['error']="Invalid Request";
        }
        return $this->return_data_in_json($data,$error_msg="");
    }
    public function yearList(){
        $data=DB::table('years')->select('id','title as year')->where('status',1)->get();
        return $this->return_data_in_json($data,$error_msg="");
    }
    
    public function get_key_salt_by_key($key="")
    {
        $data = DB::table('branches')->select('Merchant_Salt')
            ->where('Merchant_Key','=',$key)
            ->get();
        return $this->return_data_in_json($data,$error_msg="");
    }
    /*public function store_zoom_meeting(Request $request){
        $data = [];
        $meeting_data = ' {
                    "Topic": "Class1",
                    "FromTime": "10:00:00",
                    "EndTime": "11:00:00",
                    "MeertingID": "175423566",
                    "Password": "12345",
                    "Class": "Class1",
                    "Section": "Sec1",
                    "EmailID": "xyz@gmail.com"
                }';
            // $meeting_data = (isset($request->meeting_data))?$request->meeting_data:null;  
        $ret = json_decode($meeting_data);
        if($ret){
            $insert = LiveClass::insert([
                'topic' => $ret['Topic'],
                'start_time' => $ret['FromTime'],
                'end_time' => $ret['EndTime'],
                'meeting_id' => $ret['MeertingID'],
                'faculty_id' => $ret['Class'],
                'section_id' => $ret['Section'],
                'email' => $ret['EmailID'],
            ]); 
            $data['msg']="Meeting Added";
        }else{
            $data['error']="Invalid Request";
        }                   
        return $this->return_data_in_json($data,$error_msg="");
    }*/
    /* ZOOM LIVE CLASS */
    public function create_zoom_meeting(Request $request){       
        $meeting['error'] = 1;
        foreach ($request->all() as $key => $value) {
          $post_data[$key] = $value;
        }
        $api_key = (isset($request->api_key))?$request->api_key : null;
        $secret_key = (isset($request->secret_key))?$request->secret_key : null;
        $client = new \GuzzleHttp\Client();
        /*Get Old Env Api/Secret Key*/
        $response = $client->request('GET', 'http://zoomapi.academicmitra.com/api/get_env');
        $body = $response->getBody()->getContents();
        $keys = json_decode($body);
         /*Change env if $api_key != $keys->api */ 

         $exist = LiveClass::select('*')->where([
            ['topic','=',$post_data['topic']],
            ['start_time','=',$post_data['start_time']],
            // ['duration','=',$post_data['duration']],
            ['faculty_id','=',$post_data['faculty_id']],
            ['section_id','=',$post_data['section_id']],
            ['session_id','=',$post_data['session_id']],
            ['branch_id','=',$post_data['branch_id']],
         ])->first();
        if(!$exist){
            if(!($api_key == $keys->api && $secret_key == $keys->secret)){
              $response = $client->request('POST', 'http://zoomapi.academicmitra.com/api/create_meeting',['query' => $post_data]);
            }
            $response = $client->request('POST', 'http://zoomapi.academicmitra.com/api/create_meeting',['query' => $post_data]);
            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            $data = json_decode($body);
            
            if(isset($data->id)){
            $start_time = Carbon\Carbon::parse($data->start_time)->addMinutes(330);
            $insert = LiveClass::insert([
                'created_at'    => Carbon\Carbon::now(),
                'created_by'    => $request->staff_id,
                'topic'         => $data->topic,
                'start_time'    => $start_time,
                'duration'      => $data->duration,
                'faculty_id'    => rtrim($request->faculty_id,','),
                'section_id'    => rtrim($request->section_id,','),
                'session_id'    => $request->session_id,
                'branch_id'     => $request->branch_id,
                'meeting_id'    => $data->id,
                'meeting_password'    => $data->password,
                'join_url'      => $data->join_url,
                'start_url'     => $data->start_url,
                'email'         => $request->email,
                'status'        => 1,
                'host_status'   => 1,
            ]);
            $meeting['error'] = 0;
            $meeting['start_url'] = $data->start_url;
            $meeting['join_url'] = $data->join_url;
            $meeting['meeting_id'] = $data->id;
            $meeting['meeting_password'] = $data->password;
            $meeting['start_time'] = $start_time;
            }else{
                $meeting['msg'] = 'Something Went Wrong';
            }
        }else{
            $meeting['msg'] = 'Live Class Already Exists With Same Details';
        }
        return $this->return_data_in_json($meeting,$error_msg="");
    }
    public function live_meeting_log(Request $request){
        $attendance_date = (isset($request->date))?$request->date:null;
        $student_id = (isset($request->student_id))?$request->student_id:null;
        $status = (isset($request->status))?$request->status:null;
        $meeting_id = (isset($request->meeting_id))?$request->meeting_id:null;
        DB::table('live_class_attendance')->Insert(
            [
            'attendance_date' => $attendance_date,
            'meeting_id'    => $meeting_id,
            'student_id'    => $student_id,
            'created_at' => Carbon\Carbon::now(),
            'attendance_status' => $status
        ]);
    }
    public function list_attendance(Request $request){
        $data=[];
        if($request->meeting_id){
            $live_class = LiveClass::find($request->meeting_id);
            if($live_class){
                $student = DB::table('live_class_attendance')->select('std.first_name as name','std.reg_no','fac.faculty as faculty','sem.semester as section','live_class_attendance.student_id','live_class_attendance.meeting_id')
                        ->leftjoin('student_detail_sessionwise as sts',function($j)use($live_class){
                            $j->on('sts.student_id','=','live_class_attendance.student_id')
                            ->where([
                                ['sts.active_status','=',1],
                               ['sts.session_id','=',$live_class->session_id],
                           ]);
                        })
                        ->leftjoin('students as std',function($j)use($live_class){
                            $j->on('std.id','=','sts.student_id')
                            ->where([
                               // ['std.status','=',1],
                               ['std.branch_id','=',$live_class->branch_id]
                           ]);
                        })
                        ->leftjoin('faculties as fac','fac.id','=','sts.course_id')
                        ->leftjoin('semesters as sem','sem.id','=','sts.Semester')
                        ->where('meeting_id',$live_class->meeting_id)
                        ->groupBy('live_class_attendance.student_id')
                        ->get();
                if(count($student)>0){
                    foreach ($student as $key => $value) {
                        $data[$value->student_id]['std'] = $value;
                        $data[$value->student_id]['data'] = DB::table('live_class_attendance')->select('attendance_status','created_at','device_id','device_name','manufacturer','login_ip')->where([
                            ['meeting_id','=',$value->meeting_id],
                            ['student_id','=',$value->student_id],
                        ])->get();
                    }
                }
            }
        }
        return $this->return_data_in_json($data);
    }
    public function zoom_class_list_staff(Request $request){
        $staff_id = (isset($request->staff_id))?$request->staff_id:null;
        $session_id = (isset($request->session_id))?$request->session_id:null;
        $branch_id = (isset($request->branch_id))?$request->branch_id:null;
        $from_date = (isset($request->from_date))?$request->from_date:null;
        $to_date = (isset($request->to_date))?$request->to_date:null;
        $data=[];
        if($staff_id && $session_id && $branch_id){
           $data = LiveClass::select('live_classes.*',DB::raw("CONCAT(staff.first_name,' ',staff.last_name) as teacher"))
           ->rightJoin('staff','staff.id','=','live_classes.created_by')
           ->where([
            ['live_classes.created_by','=',$staff_id],
            ['live_classes.session_id','=',$session_id],
            ['live_classes.branch_id','=',$branch_id],
            ['live_classes.status','=',1]
           ])
           ->where(function($q)use($from_date,$to_date){
                if($from_date && $to_date){
                    $q->whereBetween('start_time',[$from_date.' 00:00:00',$to_date.' 23:59:59']);
                }
           })
           ->orderBy('start_time','asc')
           ->get();
        }else{
            $data['error']="Invalid Request";
        }
        return $this->return_data_in_json($data,$error_msg="");
    }
    public function zoom_class_list_student(Request $request){
        $faculty_id = (isset($request->faculty_id))?$request->faculty_id:null;
        $section_id = (isset($request->section_id))?$request->section_id:null;
        $session_id = (isset($request->session_id))?$request->session_id:null;
        $branch_id = (isset($request->branch_id))?$request->branch_id:null;
        $from_date = (isset($request->from_date))?$request->from_date:null;
        $to_date = (isset($request->to_date))?$request->to_date:null;
        $student_id = (isset($request->student_id))?$request->student_id:null;
        $data=[];
         $general_setting =  DB::table('general_settings')->select('live_class_scheduling')->first();
        if($faculty_id && $section_id && $session_id && $branch_id){
            if($student_id){
                $is_locked = DB::table('users')->where([
                    ['hook_id','=',$student_id],
                    ['role_id','=',6],
                    ['status','=',1],
                ])->select('*')->first();
                if(!$is_locked){
                    $data['error'] = 'User Locked';
                    return $this->return_data_in_json($data,$error_msg="");
                }   
            } 
           $data = LiveClass::select('live_classes.*',DB::raw("CONCAT(staff.first_name,' ',staff.last_name) as teacher"))
           ->rightJoin('staff','staff.id','=','live_classes.created_by')
           ->where([
            // ['live_classes.section_id','=',$section_id],
            ['live_classes.session_id','=',$session_id],
            ['live_classes.branch_id','=',$branch_id],
            ['live_classes.status','=',1]
           ])
           ->where(function($q)use($from_date,$to_date,$student_id){
                if($from_date && $to_date){
                    $q->whereBetween('start_time',[$from_date.' 00:00:00',$to_date.' 23:59:59']);
                }
           })
           ->where(function($q)use($general_setting,$faculty_id,$section_id){
                if($general_setting->live_class_scheduling == '1'){
                    $q->whereRaw('FIND_IN_SET(?,live_classes.faculty_id)', $faculty_id);
                    $q->where('live_classes.section_id',$section_id);
                }else{
                    $q->whereRaw('FIND_IN_SET(?,live_classes.section_id)', $section_id);
                    $q->where('live_classes.faculty_id',$faculty_id);
                }
            })
            // ->whereRaw('FIND_IN_SET(?,live_classes.faculty_id)', $faculty_id)
           ->orderBy('start_time','asc')
           ->get();
        }else{
            $data['error']="Invalid Request";
        }
        return $this->return_data_in_json($data,$error_msg="");
    }
   
    /*public function live_class_attendance(Request $request){
        $attendance_date = (isset($request->date))?$request->date:null;
        $student_id = (isset($request->student_id))?$request->student_id:null;
        $status = (isset($request->status))?$request->status:null;
        Log::debug('---------join------');
        Log::debug($request->all());
        Log::debug('------join END-----');
        $meeting_id = (isset($request->meeting_id))?$request->meeting_id:null;
        $status = 0;
        if($attendance_date && $student_id && $status && $meeting_id){
            $date = Carbon\Carbon::parse($attendance_date);
            $month = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date)->month;
            $day = "day_".Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date)->day;
            $yearTitle = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date)->year;
            $year = Year::where('title',$yearTitle)->first()->id;
                $attendanceExist = Attendance::select('attendances.id','attendances.attendees_type','attendances.link_id',
                    'attendances.years_id','attendances.months_id','attendances.'.$day,
                    's.id as students_id','s.reg_no','s.first_name','s.middle_name','s.last_name','s.student_image')
                    ->where('attendances.attendees_type',1)
                    ->where('attendances.years_id',$year)
                    ->where('attendances.months_id',$month)
                    ->where([['s.id', '=' , $student_id]])
                    ->join('students as s','s.id','=','attendances.link_id')
                    ->first();
                if ($attendanceExist) {
                    $Update = [
                        'attendees_type' => 1,
                        'link_id' => $student_id,
                        'years_id' => $year,
                        'months_id' => $month,
                        $day => $status,
                        'last_updated_by' => $student_id
                    ];
                    $data = $attendanceExist->update($Update);
                }else{
                    $data = Attendance::create([
                        'attendees_type' => 1,
                        'link_id' => $student_id,
                        'years_id' => $year,
                        'months_id' => $month,
                        $day => $status,
                        'created_by' => $student_id,
                    ]);
                }
                   
                
                    DB::table('live_class_attendance')->Insert(
                        ['attendance_date' => $attendance_date,
                        'meeting_id'    => $meeting_id,
                        'student_id'    => $student_id,
                        'created_at' => Carbon\Carbon::now(),
                        'attendance_status' => $status
                    ]);
                
        }else{
            $data['msg'] = 'Invalid Request';
        }
        return $this->return_data_in_json($data,$error_msg="");
    }*/
    public function live_class_attendance(Request $request){
        $attendance_date = (isset($request->date))?$request->date:null;
        $student_id = (isset($request->student_id))?$request->student_id:null;
        $status = (isset($request->status))?$request->status:null;
        $meeting_id = (isset($request->meeting_id))?$request->meeting_id:null;
        $device_id = (isset($request->androidid))?$request->androidid:null;
        $data['status'] = 0;
       
        
        if($attendance_date && $student_id && $status && $meeting_id){
            $joined = 0;
           
            $std_exists = DB::table('live_class_attendance')->select('device_id','login_ip')
                ->where([
                    // ['device_id','=',$device_id],
                    ['meeting_id','=',$meeting_id],
                    ['student_id','=',$student_id],
                ])->first();
            if($std_exists){
                if($device_id){
                    //CHECK FOR ANDROID REQUEST
                    $exists = DB::table('live_class_attendance')->select('device_id')
                    ->where([
                        ['device_id','=',$device_id],
                        ['meeting_id','=',$meeting_id],
                        ['student_id','=',$student_id],
                    ])->first();

                    if($exists){
                        if($exists->device_id == $std_exists->device_id){
                            $joined = 0;
                            $data['check_msg'] = 'Same device id';
                        }else{
                            $joined = 1;
                            $data['check_msg'] = 'Device Id not matched';
                        }
                    }else{
                       if($device_id == $std_exists->device_id){
                            $joined = 0;
                            $data['check_msg'] = 'Null / Same device ID';
                        }else{
                            $joined = 1;
                            $data['check_msg'] = 'Device Id not matched';
                        } 
                    }
                }else{
                    $exists = DB::table('live_class_attendance')->select('login_ip')
                    ->where([
                        ['meeting_id','=',$meeting_id],
                        ['student_id','=',$student_id],
                        ['login_ip','=',$request->ip()],
                    ])->first();
                    if($exists){
                        if($exists->login_ip == $std_exists->login_ip){
                            $joined = 0;
                            $data['check_msg'] = 'Same device Ip';
                        }else{
                            $joined = 1;
                            $data['check_msg'] = 'IP not matched';
                        }
                    }else{
                       if($request->ip() == $std_exists->login_ip){
                            $joined = 0;
                            $data['check_msg'] = 'Same device IP';
                        }else{
                            $joined = 1;
                            $data['check_msg'] = 'IP not matched';
                        } 
                    }
                } 
            }
            if(!$joined){
                $meeting = DB::table('live_classes')->select('*')->where('meeting_id',$meeting_id)->first();
                if($meeting){
                    $meeting_time = new Carbon\Carbon($meeting->start_time);
                    $start_time = Carbon\Carbon::parse($meeting_time)->subMinutes(15);
                    $end_time   = Carbon\Carbon::parse($meeting_time)->addMinutes($meeting->duration);
                    $current_time = new Carbon\Carbon();
                    if(($current_time >= $start_time) && ($current_time <= $end_time)){
                        $date = Carbon\Carbon::parse($attendance_date);
                        $month = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date)->month;
                        $day = "day_".Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date)->day;
                        $yearTitle = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date)->year;
                        $year = Year::where('title',$yearTitle)->first()->id;
                        $attendanceExist = Attendance::select('attendances.id','attendances.attendees_type','attendances.link_id',
                            'attendances.years_id','attendances.months_id','attendances.'.$day,
                            's.id as students_id','s.reg_no','s.first_name','s.middle_name','s.last_name','s.student_image')
                            ->where('attendances.attendees_type',1)
                            ->where('attendances.years_id',$year)
                            ->where('attendances.months_id',$month)
                            ->where([['s.id', '=' , $student_id]])
                            ->join('students as s','s.id','=','attendances.link_id')
                            ->first();
                        if ($attendanceExist) {
                            $Update = [
                                'attendees_type' => 1,
                                'link_id' => $student_id,
                                'years_id' => $year,
                                'months_id' => $month,
                                $day => $status,
                                'last_updated_by' => $student_id
                            ];
                            $data['resp'] = $attendanceExist->update($Update);
                        }else{
                            $data['resp'] = Attendance::create([
                                'attendees_type' => 1,
                                'link_id' => $student_id,
                                'years_id' => $year,
                                'months_id' => $month,
                                $day => $status,
                                'created_by' => $student_id,
                            ]);
                        }

                    }
                    DB::table('live_class_attendance')->Insert(
                        [
                        'attendance_date'   => $attendance_date,
                        'meeting_id'        => $meeting_id,
                        'student_id'        => $student_id,
                        'created_at'        => Carbon\Carbon::now(),
                        'attendance_status' => $status,
                        'device_id'         => $device_id,
                        'login_ip'          => $request->ip(),
                        'device_name'       => $request->model,
                        'manufacturer'      => $request->manufacturer,
                        // 'manufacturer'      => $request->model,
                    ]);
                    $data['status'] = 1;
                    $data['msg']    = 'USER CAN JOIN THIS CLASS'; 
                }else{
                    $data['msg']    = 'INVALID MEETING'; 
                }                  
            }else{
                $data['msg']    = 'USER CANNOT JOIN THIS CLASS';
            }
        }else{
            $data['msg'] = 'Invalid Request';
        }

        return $this->return_data_in_json($data,$error_msg="");
    }
    public function liveClassDuration(Request $request){
        
        $data = DB::table('live_class_duration')->select('id','duration')
        ->where([
            ['record_status','=',1]
        ])->get();
        $i=1;
        foreach ($data as $key => $value) {
            $resp['duration'][$i]['id']=$value->duration;
            $resp['duration'][$i]['duration']=$value->duration;
            $i++;
        }
        $prep = ['duration'=>'-Duration-','id'=>'0'];
        array_unshift($resp['duration'],$prep);
        return $this->return_data_in_json($resp);
    }
    /* ZOOM LIVE CLASS END */


    public function user_password(Request $request){
       
    }
    public function delete_live_class($meeting_id,$user_id){
        $resp = '0';
        if(!empty($meeting_id)){
            $data = LiveClass::where('id',$meeting_id)->update([
                'status'=>'0',
                'updated_by' => $user_id
            ]);
            if($data){
                $resp = '1';
            }
        }
        return $this->return_data_in_json($resp,$error_msg='');
    }
    /*internal meeting*/
    public function meeting_joiner_type(){
        $data = DB::table('internal_meeting_joiners')->select('title','value')->where('record_status','1')
        ->get();
        foreach ($data as $key => $value) {
            $val[$key]=$value;
        }
        return $this->return_data_in_json($val);
    }
    public function create_internal_meeting(Request $request){       
        $meeting['error'] = 1;
        foreach ($request->all() as $key => $value) {
          $post_data[$key] = $value;
        }
        $api_key = (isset($request->api_key))?$request->api_key : null;
        $secret_key = (isset($request->secret_key))?$request->secret_key : null;
        $client = new \GuzzleHttp\Client();
        /*Get Old Env Api/Secret Key*/
        $response = $client->request('GET', 'http://zoomapi.academicmitra.com/api/get_env');
        $body = $response->getBody()->getContents();
        $keys = json_decode($body);
         /*Change env if $api_key != $keys->api */ 

         $exist = DB::table('internal_meetings')->select('*')->where([
            ['topic','=',$post_data['topic']],
            ['start_time','=',$post_data['start_time']],
            ['host_for','=',$post_data['host_for']],
            ['session_id','=',$post_data['session_id']],
            ['branch_id','=',$post_data['branch_id']],
         ])->first();
        if(!$exist){
            if(!($api_key == $keys->api && $secret_key == $keys->secret)){
              $response = $client->request('POST', 'http://zoomapi.academicmitra.com/api/create_meeting',['query' => $post_data]);
            }
            $response = $client->request('POST', 'http://zoomapi.academicmitra.com/api/create_meeting',['query' => $post_data]);
            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            $data = json_decode($body);
            
            if(isset($data->id)){
            $start_time = Carbon\Carbon::parse($data->start_time)->addMinutes(330);
            $insert = DB::table('internal_meetings')->insert([
                'created_at'    => Carbon\Carbon::now(),
                'created_by'    => $request->staff_id,
                'topic'         => $data->topic,
                'start_time'    => $start_time,
                'duration'      => $data->duration,
                'session_id'    => $request->session_id,
                'branch_id'     => $request->branch_id,
                'meeting_id'    => $data->id,
                'meeting_password'    => $data->password,
                'join_url'      => $data->join_url,
                'start_url'     => $data->start_url,
                'email'         => $request->email,
                'status'        => 1,
                'host_for'      => $request->host_for,
            ]);
            $meeting['error'] = 0;
            $meeting['start_url'] = $data->start_url;
            $meeting['join_url'] = $data->join_url;
            $meeting['meeting_id'] = $data->id;
            $meeting['meeting_password'] = $data->password;
            $meeting['start_time'] = $start_time;
            }else{
                $meeting['msg'] = 'Something Went Wrong';
            }
        }else{
            $meeting['msg'] = 'Meeting Already Exists With Same Details';
        }
        return $this->return_data_in_json($meeting,$error_msg="");
    }
    public function internal_meeting_list_staff(Request $request){
        $staff_id = (isset($request->staff_id))?$request->staff_id:null;
        $session_id = (isset($request->session_id))?$request->session_id:null;
        $branch_id = (isset($request->branch_id))?$request->branch_id:null;
        $from_date = (isset($request->from_date))?$request->from_date:null;
        $to_date = (isset($request->to_date))?$request->to_date:null;
        $data=[];
        if($staff_id && $session_id && $branch_id){
           $data = DB::table('internal_meetings')->select('internal_meetings.*',DB::raw("CONCAT(staff.first_name,' ',staff.last_name) as teacher"))
           ->rightJoin('staff','staff.id','=','internal_meetings.created_by')
           ->where([
            // ['live_classes.created_by','=',$staff_id],
            ['internal_meetings.session_id','=',$session_id],
            ['internal_meetings.branch_id','=',$branch_id],
            ['internal_meetings.status','=',1]
           ])
           ->where(function($q)use($from_date,$to_date){
                if($from_date && $to_date){
                    $q->whereBetween('start_time',[$from_date.' 00:00:00',$to_date.' 23:59:59']);
                }
           })
           ->where(function($q)use($staff_id){
            $q->where('internal_meetings.created_by',$staff_id);
            $q->orWhere('internal_meetings.host_for',0);
            $q->orwhere('internal_meetings.host_for',1);
          })
           ->orderBy('start_time','asc')
           ->get();
        }else{
            $data['error']="Invalid Request";
        }
        return $this->return_data_in_json($data,$error_msg="");
    }
    public function meeting_list_student(Request $request){
        $session_id = (isset($request->session_id))?$request->session_id:null;
        $branch_id = (isset($request->branch_id))?$request->branch_id:null;
        $from_date = (isset($request->from_date))?$request->from_date:null;
        $to_date = (isset($request->to_date))?$request->to_date:null;
        $data=[];
        if($session_id && $branch_id){
           $data = DB::table('internal_meetings')->select('internal_meetings.*',DB::raw("CONCAT(staff.first_name,' ',staff.last_name) as teacher"))
           ->rightJoin('staff','staff.id','=','internal_meetings.created_by')
           ->where([
            // ['live_classes.section_id','=',$section_id],
            ['internal_meetings.session_id','=',$session_id],
            ['internal_meetings.branch_id','=',$branch_id],
            ['internal_meetings.status','=',1]
           ])
           ->where(function($q)use($from_date,$to_date){
                if($from_date && $to_date){
                    $q->whereBetween('start_time',[$from_date.' 00:00:00',$to_date.' 23:59:59']);
                }
           })
           ->where(function($q){
                $q->where('internal_meetings.host_for',0); 
                $q->orwhere('internal_meetings.host_for',2); 
            })
           ->orderBy('start_time','asc')
           ->get();
        }else{
            $data['error']="Invalid Request";
        }
        return $this->return_data_in_json($data,$error_msg="");
    }
    public function delete_meeting($meeting_id,$user_id){
        $resp = '0';
        if(!empty($meeting_id)){
            $data = DB::table('internal_meetings')->where('id',$meeting_id)->update([
                'status'=>'0',
                'updated_by' => $user_id
            ]);
            if($data){
                $resp = '1';
            }
        }
        return $this->return_data_in_json($resp,$error_msg='');
    }
    public function getSubjectByCourse(Request $request){
        $data = 'Invalid Request';
        if($request->course_id && $request->section_id && $request->branch_id && $request->session_id){
            $data = DB::table('timetable_subjects')->select('id','title','course_id','section_id','branch_id','session_id')
            ->where([
                ['status','=',1],
                ['course_id','=',$request->course_id],
                ['section_id','=',$request->section_id],
                ['branch_id','=',$request->branch_id],
                ['session_id','=',$request->session_id],
            ])->get();
            $data = $data->all();
            $prep = ['title'=>'-Subject-','id'=>'0'];
            array_unshift($data,$prep);
        }
        return $this->return_data_in_json($data);
    }
    /*internal meeting end*/

    /*-----------------------------------------------------------------------*/
        /* EXAM */
    public function save_assessment(Request $request){
        $data['error'] = true;
        //Request---exam_id,mark[],attendance[],user_id
        if($request->exam_id && $request->mark && $request->attendance){
            $exam = DB::table('exam_create')->select('*')
                ->where('id',$request->exam_id)
                ->first();
            $mark = $request->mark;
            $attendance = $request->attendance;
            
            if($exam->date){
                $date = Carbon\Carbon::parse($exam->date);
                $month = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date)->month;
                $day = "day_".Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date)->day;
                $yearTitle = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date)->year;
                $year = Year::where('title',$yearTitle)->first()->id;
            }
            foreach ($mark as $key => $value) {
                $obtained_mark = $value? $value :'0';
                if($obtained_mark != ''){
                    $assessment_exists = DB::table('exam_mark')->select('id')->where([
                        ['exam_id','=',$exam->id],
                        ['student_id','=',$key]
                    ])->first();
                    $status = $attendance[$key]?$attendance[$key]:'2';
                    if($status != 1){
                        $obtained_mark = 0;
                    }
                    
                    
                    if($assessment_exists){
                        $add_mark = DB::table('exam_mark')->updateOrInsert(
                            [
                                'exam_id'=>$exam->id,
                                'student_id'=>$key,
                            ],
                            [
                                'mark'  => $obtained_mark,
                                /*assesmentGrade*/
                                'grade'  => $request->grade[$key],
                                /*assesmentGrade*/
                                'updated_at'  =>\Carbon\Carbon::now(),
                                'updated_by' => $request->user_id,
                                'attendance' => $status
                            ]
                        ); 
                    }else{
                        $add_mark = DB::table('exam_mark')->updateOrInsert(
                            [
                                'exam_id'=>$exam->id,
                                'student_id'=>$key,
                            ],
                            [
                                'mark'  => $obtained_mark,
                                /*assesmentGrade*/
                                'grade'  => $request->grade[$key],
                                /*assesmentGrade*/
                                'created_at' => \Carbon\Carbon::now(),
                                'created_by' => $request->user_id,
                                'attendance' => $status
                            ]
                        ); 

                    }
                    if($add_mark && $status != 4){
                        if(isset($date)){
                            
                            if($status == 3){
                                $status = 4;
                            }
                            $attendanceExist = Attendance::select('attendances.id','attendances.attendees_type','attendances.link_id',
                                'attendances.years_id','attendances.months_id','attendances.'.$day,
                                's.id as students_id','s.reg_no','s.first_name','s.middle_name','s.last_name','s.student_image')
                                ->where('attendances.attendees_type',1)
                                ->where('attendances.years_id',$year)
                                ->where('attendances.months_id',$month)
                                ->where([['s.id', '=' , $key]])
                                ->join('students as s','s.id','=','attendances.link_id')
                                ->first();
                            if ($attendanceExist) {
                                $Update = [
                                    'attendees_type' => 1,
                                    'link_id' => $key,
                                    'years_id' => $year,
                                    'months_id' => $month,
                                    $day => $status,
                                    'last_updated_by' => $request->user_id?$request->user_id:auth()->user()->id
                                ];
                                $update = $attendanceExist->update($Update);
                            }else{
                                $data = Attendance::create([
                                    'attendees_type' => 1,
                                    'link_id' => $key,
                                    'years_id' => $year,
                                    'months_id' => $month,
                                    $day => $status,
                                    'created_by' => $request->user_id?$request->user_id:auth()->user()->id,
                                ]);
                            }
                        }else{
                            $data['msg'] = 'Invalid Date!';
                        }    
                        $data['error']= false;
                    }else{
                        $data['msg'] = 'Mark Adding Failed!';
                    }
                }    
            }
        }else{
           $data['msg'] = 'Invalid Request!'; 
        }
        return $this->return_data_in_json($data,$error_msg="");
    }
    /* EXAM DROPDOWNS */
    public function get_exam_mode(){
        $data = DB::table('exam_modes')->select('title','description','id')->
        where('record_status',1)->get();
        $data = $data->all();
        $prep = ['title'=>'-Exam Mode-','id'=>'0'];
        array_unshift($data,$prep);
        // dd($data);
        return $this->return_data_in_json($data,$error_msg="");
    }
    public function get_exam_term(Request $request){
        $data=[];
        if($request->session_id && $request->branch_id){
            $data = DB::table('exam_terms')->select('title','description','id')->
            where([
                ['record_status','=',1],
                ['session_id','=',$request->session_id],
                ['branch_id','=',$request->branch_id],
            ])->get();   
            $data = $data->all();
            $prep = ['title'=>'-Exam Term-','id'=>'0'];
            array_unshift($data,$prep);
        }else{
            $data['msg']    =   'Invalid Request.';
        }
        return $this->return_data_in_json($data,$error_msg="");
    }
    public function get_exam_paper(Request $request){
        $data=[];
        if($request->session_id && $request->branch_id){
            $data = DB::table('exam_papers')->select('title','description','id')->
            where([
                ['record_status','=',1],
                ['session_id','=',$request->session_id],
                ['branch_id','=',$request->branch_id],
            ])->get();   
            $data = $data->all();
            $prep = ['title'=>'-Exam Paper-','id'=>'0'];
            array_unshift($data,$prep);
        }else{
            $data['msg']    =   'Invalid Request.';
        }
        return $this->return_data_in_json($data,$error_msg="");
    }
    
    public function get_exam_type(Request $request){
        $data=[];
        if($request->session_id && $request->branch_id && $request->term_id){
            $data = DB::table('exam_type')->select('title','description','id')->
            where([
                ['record_status','=',1],
                ['session_id','=',$request->session_id],
                ['branch_id','=',$request->branch_id],
                ['term_id','=',$request->term_id],
            ])->get();   
            $data = $data->all();
            $prep = ['title'=>'-Exam Type-','id'=>'0'];
            array_unshift($data,$prep);
        }else{
            $data['msg']    =   'Invalid Request.';
        }
        return $this->return_data_in_json($data,$error_msg="");
    }
    /*end dropdowns */
    
    
    public function get_exam_question_type(){
        $data=[];
        $data = DB::table('exam_question_types')->select('id','title','description')
        ->Where('record_status',1)->get();
            $data = $data->all();
            $prep = ['title'=>'-Question Type-','id'=>'0'];
            array_unshift($data,$prep);
        return $this->return_data_in_json($data,$error_msg="");
    }
    public function post_exam_question_answer(Request $request){
        
//          $resp = array (
//   'Answer_json_data' => '[{"answers":"Hh","exam_id":"3","id":0,"option":"option_1","qno":"2","student_id":"1125","type":"1","user_id":"1125"}]',
// )  ;
        $resp = $request->all();
        // Log::debug('----GAURAV SIR KA ANS---');
        
        if(isset($resp['Answer_json_data'])){
            $data = json_decode($resp['Answer_json_data']);
            
            if($data){
                foreach ($data as $key => $value) {
                    
                    $exam_id = $value->exam_id;
                    $std_id = $value->student_id;
                    $exists = DB::table('exam_mark')->select('*')->where([
                        ['exam_id','=', $exam_id],
                        ['student_id','=',$std_id]
                    ])->first();
                    
                    if($exists){
                        $resp['check_msg'] = 'Exam Already Submitted';
                         return $this->return_data_in_json($resp);
                    }
                    $exam_question_image = "";
                    if(isset($value->title_image) && $value->title_image!=""){
                        $exam_question_image = $this->examImageBase64UploadFile($value->title_image,$value->title_image_ext);
                    }
                    switch($value->type){
                        case 1:
                            
                            DB::table('exam_question_answer')->updateOrInsert(
                                [
                                    'exam_id'           => $value->exam_id,
                                    'student_id'        => $value->student_id,
                                    'exam_question_id'  => $value->qno,
                                ],
                                [
                                    'option_1_answer'   => $value->answers,
                                    'uploaded_image'   => $exam_question_image,
                                    'created_at'        => Carbon\Carbon::now(),
                                    'created_by'        => $value->user_id
                                ]
                            );
                            break; 
                        case 2:
                            DB::table('exam_question_answer')->updateOrInsert(
                                [
                                    'exam_id'           => $value->exam_id,
                                    'student_id'        => $value->student_id,
                                    'exam_question_id'  => $value->qno,
                                ],
                                [
                                    'option_1_answer'   => $value->option,
                                    'uploaded_image'   => $exam_question_image,
                                    'created_at'        => Carbon\Carbon::now(),
                                    'created_by'        => $value->user_id
                                ]
                            );
                            break;
                        case 3:
                            
                            $answers = explode(',', $value->option);
                            
                            foreach ($answers as $k => $val) {
                                if(!empty($val)){
                                    $ans['option_'.$val.'_answer'] = 'option_'.$val;
                                }
                            }
                            $ans['created_at'] = Carbon\Carbon::now();
                            $ans['uploaded_image'] = $exam_question_image;
                            $ans['created_by'] = $value->user_id;
                            DB::table('exam_question_answer')->updateOrInsert(
                                [
                                    'exam_id'           => $value->exam_id,
                                    'student_id'        => $value->student_id,
                                    'exam_question_id'  => $value->qno,
                                ],
                                $ans
                            );  
                            break;        
                        case 4:
                            DB::table('exam_question_answer')->updateOrInsert(
                                [
                                    'exam_id'           => $value->exam_id,
                                    'student_id'        => $value->student_id,
                                    'exam_question_id'  => $value->qno,
                                ],
                                [
                                    'option_1_answer'   => $value->option,
                                    'uploaded_image'   => $exam_question_image,
                                    'created_at'        => Carbon\Carbon::now(),
                                    'created_by'        => $value->user_id
                                ]
                            );
                            break;
                        case 5:
                            DB::table('exam_question_answer')->updateOrInsert(
                                [
                                    'exam_id'           => $value->exam_id,
                                    'student_id'        => $value->student_id,
                                    'exam_question_id'  => $value->qno,
                                ],
                                [
                                    'option_1_answer'   => $value->answers,
                                    'uploaded_image'   => $exam_question_image,
                                    'created_at'        => Carbon\Carbon::now(),
                                    'created_by'        => $value->user_id
                                ]
                            );
                            break;    
                        default :
                            break;
                    }
                }
                $correct_answer = DB::table('exam_question_answer')->select('question.correct_answer','question.mark as question_mark','exam_question_answer.id as answer_id')
                ->leftjoin('exam_question as question','question.id','=','exam_question_answer.exam_question_id')
                ->where([
                    ['student_id','=',$std_id],
                    ['exam_question_answer.exam_id','=',$exam_id],
                ])
                ->whereIn('question.question_type',[2,4])
                ->get();
                if(count($correct_answer)>0){
                    $total_mark = 0;
                    foreach ($correct_answer as $key => $value) {
                        $given_answer = DB::table('exam_question_answer')->select('option_1_answer as given_answer')
                        ->where([
                            ['id','=',$value->answer_id]
                        ])->first();
                        $obtained_mark = ($value->correct_answer == $given_answer->given_answer) ? $value->question_mark : 0;
                        $total_mark = $total_mark + $obtained_mark;
                        DB::table('exam_question_answer')->where('id',$value->answer_id)
                        ->update([
                            'obtained_mark'=>$obtained_mark
                        ]);
                    }
                    DB::table('exam_mark')->updateOrInsert(
                        [
                            'exam_id'    =>  $exam_id,
                            'student_id' =>  $std_id
                        ],
                        [
                            'mark'  =>  $total_mark,
                            'attendance' => 1,
                            'created_by'=> $std_id,
                            'latitude'  => $request->lat,
                            'longitude' => $request->long,
                            'mobile'    => $request->mobile,
                            'address'   => $request->addressss
                        ]
                    );
                }else{
                     DB::table('exam_mark')->updateOrInsert(
                        [
                            'exam_id'    =>  $exam_id,
                            'student_id' =>  $std_id
                        ],
                        [
                            'mark'  =>  0,
                            'attendance' => 1,
                            'created_by'=> $std_id,
                            'latitude'  => $request->lat,
                            'longitude' => $request->long,
                            'mobile'    => $request->mobile,
                            'address'   => $request->addressss
                        ]
                    );
                }
                $resp['msg'] = 'Exam Saved';
            }else{
                $resp['msg'] = 'Something Went Wrong';
            }
        }else{
            $resp['msg'] = 'Answer json data not found';
        }
        
        return $this->return_data_in_json($resp);
    }
    public function get_exam_by_term(Request $request){
        
        $data = [];
        if($request->mode_id && $request->branch_id && $request->session_id && $request->faculty_id && $request->section_id && $request->term_id && $request->type_id){
            $data = DB::table('exam_create')->select('exam_create.*',DB::raw("COALESCE(sm.is_main_subject,'1') as sub_type"))
                ->leftjoin('timetable_subjects as ts','ts.id','=','exam_create.subject_id')
                ->leftjoin('subject_master as sm','sm.id','=','ts.subject_master_id')
            ->where([
                ['exam_create.record_status','=',1],
                ['exam_create.term_id','=',$request->term_id],
                ['exam_create.type_id','=',$request->type_id],
                ['exam_create.faculty_id','=',$request->faculty_id],
                ['exam_create.section_id','=',$request->section_id],
                ['exam_create.branch_id','=',$request->branch_id],
                ['exam_create.session_id','=',$request->session_id],
                ['exam_create.mode_id','=',$request->mode_id],
            ])->get();
        }else{
            $data['msg']    =   'Invalid Request.';
        }
         return $this->return_data_in_json($data,$error_msg="");
    }
    public function get_student_by_exam(Request $request){
        if($request->exam_id){
             $exam = DB::table('exam_create')->select('exam_create.*','fac.faculty','sem.semester as section','subject.title as subject')
            ->leftjoin('faculties as fac','fac.id','=','exam_create.faculty_id')
            ->leftjoin('semesters as sem','sem.id','=','exam_create.section_id')
            ->leftjoin('timetable_subjects as subject','subject.id','=','exam_create.subject_id')
            ->where('exam_create.id',$request->exam_id)->first();


            $data['exam_detail'] = [
                'exam_title'            =>  $exam->title,
                'exam_description'      =>  $exam->description,
                'max_mark'              =>  $exam->max_mark,
                'pass_mark'             =>  $exam->pass_mark,
                'date'                  =>  $exam->date,
                'start_time'            =>  $exam->start_time,
                'end_time'              =>  $exam->end_time,
            ];


            $data['students'] = DB::table('student_detail_sessionwise')->select('std.first_name as name','std.id','std.reg_no','pd.father_first_name as father_name','em.mark as obtained','em.attendance')
            ->leftjoin('students as std','std.id','=','student_detail_sessionwise.student_id')
            ->leftjoin('parent_details as pd','pd.students_id','=','student_detail_sessionwise.student_id')
            ->leftjoin('exam_mark as em',function($join)use($exam){
                $join->on('em.student_id','=','student_detail_sessionwise.student_id')
                ->Where('em.exam_id',$exam->id);
            })
            ->where([
                ['student_detail_sessionwise.course_id','=',$exam->faculty_id],
                ['student_detail_sessionwise.Semester','=',$exam->section_id],
                ['student_detail_sessionwise.session_id','=',$exam->session_id],
                ['student_detail_sessionwise.active_status','=',1],
                ['std.branch_id','=',$exam->branch_id],
                ['std.status','=',1],
            ])
            ->orderBy('std.first_name','asc')
            ->get();
            
            
        }else{
            $data['msg']    =   'Invalid Request.';
        }
         return $this->return_data_in_json($data,$error_msg="");
    }
    public function examStudentList(Request $request){
        if($request->exam_id){
            $data = DB::table('exam_mark')->select('std.id as student_id','exam_mark.exam_id','std.first_name as student_name','std.reg_no','pd.father_first_name as father_name','exam_mark.mark as total_mark')
            ->leftjoin('students as std','std.id','=','exam_mark.student_id')
            ->leftjoin('parent_details as pd','pd.students_id','=','std.id')
            ->where([
                ['exam_mark.exam_id','=',$request->exam_id]
            ])->get();
        
            
        }else{
            $data['msg']    =   'Invalid Request.';
        }
         return $this->return_data_in_json($data,$error_msg="");
    }
    public function show_exam_result(Request $request){
        
        $data = [];
        if($request->student_id && $request->branch_id && $request->session_id && $request->term_id && $request->type_id){
            $exams = DB::table('exam_create')->select('exam_create.*','sub.title as subject')
            ->leftjoin('timetable_subjects as sub','sub.id','=','exam_create.subject_id')
            ->where([
                ['record_status','=',1],
                ['publish_status','=',1],
                ['result_status','=',1],
                ['exam_create.branch_id','=',$request->branch_id],
                ['exam_create.session_id','=',$request->session_id],
                ['term_id','=',$request->term_id],
                ['type_id','=',$request->type_id],
            ])->get();
            $exam = [];
            if(count($exams)>0){
                foreach ($exams as $key => $value) {
                    $result = DB::table('exam_mark')->select('*')->where([
                        ['exam_id','=',$value->id],
                        ['student_id','=',$request->student_id],
                    ])->first();
                    // dd($result);
                    if($result){
                        $exam['exam_title'] = $value->title;
                        $exam['subject'] = $value->subject;
                        $exam['max_mark'] = $value->max_mark;
                        $exam['pass_mark'] = $value->pass_mark;
                        $exam['obtained_mark'] = $result->mark;
                        $exam['attendance'] = $result->attendance;
                        $final_mark[] = $exam;
                    }
                    
                }
                if(count($final_mark)>0){
                    $data['mark'] = $final_mark;
                }else{
                    $data['msg'] = 'No Record Found';
                }
            }else{
                $data['msg'] = 'No Exam Found';    
            }
        }else{
            $data['msg'] = 'Invalid Request';
        }
        return $this->return_data_in_json($data,$error_msg='');
    }
    /*exam*/
        public function save_exam(Request $request){
            
            // $r=array (
            //   'date' => '2020-08-28',
            //   'session' => '5',
            //   'subject' => '3',
            //   'description' => 'test again',
            //   'endtime' => '18:06:00',
            //   'maxmark' => '100',
            //   'minmark' => '33',
            //   'section' => '2',
            //   'starttime' => '13:06:00',
            //   'title' => 'test again',
            //   'type' => '6',
            //   'branch' => '1',
            //   'mode' => '1',
            //   'staff_id' => '21',
            //   'publish' => '0',
            //   'course' => '10',
            //   'term' => '7',
            //   // 'hallno' => '555',
            //   // 'paper'  => '3'
            // )  ;
            $data       = new Request($request->all());
            $date       = $data->date?Carbon\Carbon::parse($data->date)->format('Y-m-d'):null;
            $start_time = $data->starttime ? $data->starttime:null;
            $end_time   = $data->endtime ? $data->endtime:null;

            if($data->branch && $data->session && $data->mode && $data->term && $data->type && $data->paper && $data->course && $data->section && $data->subject && $data->maxmark && $data->minmark && $data->title){
                $insert = DB::table('exam_create')->insert([
                    'branch_id'         =>  $data->branch,
                    'session_id'        =>  $data->session,
                    'mode_id'           =>  $data->mode,
                    'term_id'           =>  $data->term,
                    'type_id'           =>  $data->type,
                    'faculty_id'        =>  $data->course,
                    'section_id'        =>  $data->section,
                    'subject_id'        =>  $data->subject,
                    'paper_type'        =>  $data->paper,
                    'title'             =>  $data->title,
                    'description'       =>  $data->description,
                    'max_mark'          =>  $data->maxmark,
                    'pass_mark'         =>  $data->minmark,
                    'date'              =>  $date,
                    'start_time'        =>  $start_time,
                    'end_time'          =>  $end_time,
                    'publish_status'    =>  $data->publish,
                    'result_status'     =>  0,
                    'room_no'           =>  $data->hallno,
                    'record_status'     =>  1,
                    'created_at'        =>  Carbon\Carbon::now(),
                    'created_by'        =>  $data->user_id  
                ]);
                if($insert){
                    $resp['msg'] = 'Record Created';
                }else{
                    $resp['msg'] = 'Something went wrong';
                }
            }else{
                $resp['msg'] = 'Invalid Request';
            }
            return $this->return_data_in_json($resp);   
        }
        public function get_exam(Request $request){
            $data=[];
            
            if($request->session_id && $request->branch_id && (($request->section_id && $request->faculty_id && $request->term_id && $request->mode_id) || ($request->staff_id)) ){
                $data = DB::table('exam_create')->select('exam_create.id','exam_create.title as exam_title','exam_create.description as exam_description','max_mark','pass_mark','date','start_time','end_time','room_no','publish_status','result_status','et.title as term','etype.title as type','ts.title as subject')
                ->leftjoin('exam_terms as et','et.id','=','exam_create.term_id')
                ->leftjoin('exam_type as etype','etype.id','=','exam_create.type_id')
                ->leftjoin('timetable_subjects as ts','ts.id','=','exam_create.subject_id')
                ->Where(function($q)use($request){
                    if($request->section_id && $request->faculty_id && $request->term_id && $request->mode_id){
                        $q->where([
                            ['exam_create.term_id','=',$request->term_id],
                            ['exam_create.faculty_id','=',$request->faculty_id],
                            ['exam_create.section_id','=',$request->section_id],
                            ['exam_create.mode_id','=',$request->mode_id],                      
                        ]);
                    }if($request->staff_id){
                        $q->where([
                            // ['exam_create.created_by','=',$request->staff_id],
                        ]); 
                    }else{
                        $q->where([
                            ['exam_create.publish_status','=',1],
                        ]); 
                    }
                })
                ->where([
                    ['exam_create.session_id','=',$request->session_id],
                    ['exam_create.branch_id','=',$request->branch_id],
                    ['exam_create.record_status','=',1],
                ])->get();
            }else{
                $data['msg']    =   'Invalid Request.';
            }
            return $this->return_data_in_json($data,$error_msg="");
        }
        public function delete_exam(Request $request){
            $data=[];
            if($request->exam_id && $request->staff_id){
                $resp = DB::table('exam_create')->where('id',$request->exam_id)
                        ->update([
                            'record_status' => 0,
                            'updated_by'    => $request->staff_id,
                            'updated_at'    => Carbon\Carbon::now(),
                        ]);
                if($resp){
                    $data['msg'] = 'Exam Deleted';
                }else{
                    $data['msg'] = 'Something went wrong';
                }        
            }else{
                $data['msg'] = 'Invalid Request !';
            }
            return $this->return_data_in_json($data);
        }
        public function exam_publish_status(Request $request){
            $data=[];
            if($request->exam_id && $request->staff_id && $request->status){
                $resp = DB::table('exam_create')->where('id',$request->exam_id)
                        ->update([
                            'publish_status' => $request->status,
                            'updated_by'     => $request->staff_id,
                            'updated_at'     => Carbon\Carbon::now(),
                        ]);
                if($resp){
                    $data['msg'] = 'Exam Status Changed';
                }else{
                    $data['msg'] = 'Something went wrong';
                }        
            }else{
                $data['msg'] = 'Invalid Request !';
            }
            return $this->return_data_in_json($data);
        }
        public function exam_result_status(Request $request){
            $data=[];
            if($request->exam_id && $request->staff_id && $request->status){
                $resp = DB::table('exam_create')->where('id',$request->exam_id)
                        ->update([
                            'result_status' => $request->status,
                            'updated_by'     => $request->staff_id,
                            'updated_at'     => Carbon\Carbon::now(),
                        ]);
                if($resp){
                    $data['msg'] = 'Exam Result Status Changed';
                }else{
                    $data['msg'] = 'Something went wrong';
                }        
            }else{
                $data['msg'] = 'Invalid Request !';
            }
            return $this->return_data_in_json($data);
        }
        public function studentAppearingStatus(Request $request){
           
            if($request->student_id && $request->exam_id){
                $exists = DB::table('exam_mark')->select('*')->where([
                    ['exam_id','=',$request->exam_id],
                    ['student_id','=',$request->student_id],
                ])->first();
                if($exists){
                    $data['msg'] = 'Exam Already Given';
                    $data['status'] = 0;
                }else{
                    $data['msg'] = 'Exam not given';
                    $data['status'] = 1;
                }
            }else{
                $data['msg'] = 'Invalid Request!';
                // $data['status'] = 0;
            }
            return $this->return_data_in_json($data);
        }
        public function studentAssignmentSubmissionStatus(Request $request){
            if($request->assignment_id && $request->student_id){
                $exists = DB::table('assignment_answers')->select('*')->where([
                    ['assignments_id','=',$request->assignment_id],
                    ['students_id','=',$request->student_id],
                ])->first();
                if($exists){
                    $data['msg'] = 'Assignment Already Submitted';
                    $data['status'] = 0;
                }else{
                    $data['msg'] = 'Assignment Not Submitted';
                    $data['status'] = 1;
                }
            }else{
                $data['msg'] = 'Invalid Request!';
            }
            return $this->return_data_in_json($data);
        }
    /*exam end*/

    /* exam question */
        public function save_exam_question(Request $request){
            
            Log::debug('------------[START] Exam----------');
            Log::debug($request->all());
            Log::debug('------------[END] Exam----------');
            
            if($request->exam_id && $request->question_title && $request->question_mark && $request->question_type){
                
                $exam_question_image = ""; 
                $exam_option_1_image = ""; $exam_option_2_image = "";
                $exam_option_3_image = ""; $exam_option_4_image = "";
                $exam_option_5_image = ""; $exam_option_6_image = "";
                
                if(isset($request->title_image) && $request->title_image!=""){
                    $exam_question_image = $this->examImageBase64UploadFile($request->title_image,$request->title_image_ext);
                }
                
                if(isset($request->option_1_image) && $request->option_1_image!=""){
                    $exam_option_1_image = $this->examImageBase64UploadFile($request->option_1_image,$request->option_1_image_ext);
                }
                
                if(isset($request->option_2_image) && $request->option_2_image!=""){
                    $exam_option_2_image = $this->examImageBase64UploadFile($request->option_2_image,$request->option_2_image_ext);
                }
                
                if(isset($request->option_3_image) && $request->option_3_image!=""){
                    $exam_option_3_image = $this->examImageBase64UploadFile($request->option_3_image,$request->option_3_image_ext);
                }
                
                if(isset($request->option_4_image) && $request->option_4_image!=""){
                    $exam_option_4_image = $this->examImageBase64UploadFile($request->option_4_image,$request->option_4_image_ext);
                }
                
                if(isset($request->option_5_image) && $request->option_5_image!=""){
                    $exam_option_5_image = $this->examImageBase64UploadFile($request->option_5_image,$request->option_5_image_ext);
                }
                
                if(isset($request->option_6_image) && $request->option_6_image!=""){
                    $exam_option_6_image = $this->examImageBase64UploadFile($request->option_6_image,$request->option_6_image_ext);
                }
                
                
                $insert = DB::table('exam_question')->insert([
                    'exam_id'               => $request->exam_id,
                    'question_title'        => $request->question_title,
                    
                    'question_image'        => $exam_question_image,
                    
                    'question_description'  => $request->question_description,
                    'question_type'         => $request->question_type,
                    'mark'                  => $request->question_mark,
                    'option_1'              => $request->option_1,
                    'option_2'              => $request->option_2,
                    'option_3'              => $request->option_3,
                    'option_4'              => $request->option_4,
                    'option_5'              => $request->option_5,
                    'option_6'              => $request->option_6,
                    
                    'option_1_image'        => $exam_option_1_image,
                    'option_2_image'        => $exam_option_2_image,
                    'option_3_image'        => $exam_option_3_image,
                    'option_4_image'        => $exam_option_4_image,
                    'option_5_image'        => $exam_option_5_image,
                    'option_6_image'        => $exam_option_6_image,
                    
                    'correct_answer'        => $request->correct_option,
                    'require_file'          => $request->require_file,
                    'is_required'           => $request->is_required,
                    'created_at'            => Carbon\Carbon::now(),
                    'created_by'            => $request->staff_id,
                ]);
                if($insert){
                    $data['msg'] = 'Record Created';
                }else{
                    $data['msg'] = 'Something went wrong';
                }
            }else{
                $data['msg'] = 'Invalid Request!';
            }
            
            return $this->return_data_in_json($data);
        }
        
        /* ******** [SAVE BASE64 image in respective file] ********** */
        
        public function examImageBase64UploadFile($base64Image="",$image_ext = 'jpg'){
            Log::debug('------------[START] Exam Image----------');
            Log::debug($base64Image);
            Log::debug('------------[END] Exam Image----------');
            
            $image_64 = trim($base64Image);
            $imageName = Str::random(10).time().'.'.$image_ext;
            Storage::disk('exam')->put($imageName, base64_decode($image_64));   
            return $imageName;
        }
        
        public function examImageBase64Upload(Request $request){
            Log::debug('------------[START] Exam Image API----------');
            Log::debug($request);
            Log::debug('------------[END] Exam Image API----------');
            
            $image_ext = trim($request->file_extension); 
            $image_64 = trim($request->images_base64);  
            $imageName = Str::random(10).time().'.'.$image_ext; 
            Storage::disk('exam')->put($imageName, base64_decode($image_64));
            return $imageName;
        }
        
        public function get_exam_question(Request $request){
            $data = [];
            if($request->exam_id){
                $questions = DB::table('exam_question')->select('exam_question.*','eqt.title as exam_question_type')
                ->leftjoin('exam_question_types as eqt','eqt.id','=','exam_question.question_type')
                ->where([
                    ['exam_question.exam_id','=',$request->exam_id],
                    ['exam_question.record_status','=',1],
                ])->get();
                if($questions){
                    foreach ($questions as $key => $value) {
                        $i = 0;
                        if($value->option_1){
                            $i+=1;
                        }
                        if($value->option_2){
                            $i+=1;
                        }
                        if($value->option_3){
                            $i+=1;
                        }
                        if($value->option_4){
                            $i+=1;
                        }
                        if($value->option_5){
                            $i+=1;
                        }
                        if($value->option_6){
                            $i+=1;
                        }
                        foreach ($value as $k => $val) {
                           $quest[$k] = $val;
                        }
                        $quest['total_options'] = $i;
                        $data[$key] = $quest;
                    }
                }
            }else{
                $data['msg']    =   'Invalid Request.';
            }
             return $this->return_data_in_json($data,$error_msg="");
        }
        public function delete_exam_question(Request $request){
            $data=[];
            if($request->question_id ){
                $resp = DB::table('exam_question')->where('id',$request->question_id)
                        ->update([
                            'record_status' =>0,
                            'updated_by'    => $request->staff_id,
                            'updated_at'    => Carbon\Carbon::now(),
                        ]);
                if($resp){
                    $data['msg'] = 'Question Deleted';
                }else{
                    $data['msg'] = 'No such question';
                }        
            }else{
                $data['msg'] = 'Invalid Request !';
            }
            return $this->return_data_in_json($data);
        }
        public function questionwiseStudentAnswer(Request $request){
            Log::debug('--STUDENT VIEW ANSWER---');
            Log::debug($request->all());
            if($request->exam_id && $request->student_id){
                $data['exam'] = DB::table('exam_create')->select('exam_create.*','ts.title as subject','sem.semester as section','fac.faculty')
                ->leftjoin('semesters as sem','sem.id','=','exam_create.section_id')
                ->leftjoin('faculties as fac','fac.id','=','exam_create.faculty_id')
                ->leftjoin('timetable_subjects as ts','ts.id','=','exam_create.subject_id')
                ->where('exam_create.id',$request->exam_id)
                ->first();

                $data['answer'] = DB::table('exam_question_answer')->select('exam_question_answer.id as answer_id','exam_question_answer.exam_id','exam_question_answer.student_id','eq.question_title','eq.question_description','eq.question_image','eq.question_type','eqt.title as exam_question_type','eq.mark as question_mark','eq.question_image as title_image','option_1','option_2','option_3','option_4','option_5','option_6','correct_answer','is_required','exam_question_answer.obtained_mark as obtained_mark_on_answer','option_1_answer','option_2_answer','option_3_answer','option_4_answer','option_5_answer','option_6_answer','exam_question_answer.option_1_image as option_1_image_answer','exam_question_answer.option_2_image as option_2_image_answer','exam_question_answer.option_3_image as option_3_image_answer','exam_question_answer.option_4_image as option_4_image_answer','exam_question_answer.option_5_image as option_5_image_answer','exam_question_answer.option_6_image as option_6_image_answer','exam_question_answer.uploaded_image as uploaded_image')
                ->leftjoin('exam_question as eq',function($j){
                    $j->on('eq.id','=','exam_question_answer.exam_question_id')
                    ->where('eq.record_status',1);
                })
                ->leftjoin('exam_question_types as eqt','eqt.id','=','eq.question_type')
                // ->leftjoin('exam_mark','exam_mark','=','')
                ->where([
                    ['exam_question_answer.exam_id','=',$request->exam_id],
                    ['exam_question_answer.student_id','=',$request->student_id],
                ])->get();

                $data['total_mark'] = DB::table('exam_mark')->select('*')
                ->where([
                    ['exam_id','=',$request->exam_id],
                    ['student_id','=',$request->student_id],
                ])->first();
                // dd($data);
            }else{
                $data['msg'] = 'Invalid Request!';
            }
            return $this->return_data_in_json($data);
        }
        public function post_student_exam_mark(Request $request){
            Log::debug('post_student_exam_mark');
            Log::debug($request->all());
            $resp['error'] = 1;
            // $data = array (
            //   'Answer_json_data' => '[
            //   {"Correct_Option":"null","Descritption":"QUESTION 1 TEXT BOX QUESTION 1 TEXT BOX","Is_Required":"2","Optained_mark":"10","Option1":"null","Option2":"null","Option3":"null","Option4":"null","Option5":"null","Option6":"null","Option_answer1":"THIS IN MY ANSWER","Option_answer2":"null","Option_answer3":"null","Option_answer4":"null","Option_answer5":"null","Option_answer6":"null","Question_Type":"TEXT","Studnet_id":"257","answer_id":"1","exam_id":"1","max_mark":"10","title":"QUESTION 1 TEXT BOX"},{"Correct_Option":"option_3","Descritption":"QUESTION 2 RADIO QUESTION 2 RADIO","Is_Required":"2","Optained_mark":"10","Option1":"first","Option2":"second","Option3":"thrd","Option4":"four","Option5":"null","Option6":"null","Option_answer1":"option_3","Option_answer2":"null","Option_answer3":"null","Option_answer4":"null","Option_answer5":"null","Option_answer6":"null","Question_Type":"RADIO BUTTON","Studnet_id":"257","answer_id":"2","exam_id":"1","max_mark":"10","title":"QUESTION 2 RADIO"},{"Correct_Option":"null","Descritption":"QUESTION 3 CHECKBOX","Is_Required":"2","Optained_mark":"5","Option1":"1","Option2":"2","Option3":"3","Option4":"null","Option5":"null","Option6":"null","Option_answer1":"option_1","Option_answer2":"option_2","Option_answer3":"null","Option_answer4":"null","Option_answer5":"null","Option_answer6":"null","Question_Type":"CHECKBOX","Studnet_id":"257","answer_id":"3","exam_id":"1","max_mark":"10","title":"QUESTION 3 CHECKBOX"},{"Correct_Option":"option_2","Descritption":"QUESTION 4 DROPBOX QUESTION 4 DROPBOX","Is_Required":"2","Optained_mark":"0","Option1":"pehla","Option2":"dusra","Option3":"teesra","Option4":"null","Option5":"null","Option6":"null","Option_answer1":"option_1","Option_answer2":"null","Option_answer3":"null","Option_answer4":"null","Option_answer5":"null","Option_answer6":"null","Question_Type":"DROPBOX","Studnet_id":"257","answer_id":"4","exam_id":"1","max_mark":"10","title":"QUESTION 4 DROPBOX"},{"Correct_Option":"null","Descritption":"QUESTION 5 DATE","Is_Required":"1","Optained_mark":"10","Option1":"null","Option2":"null","Option3":"null","Option4":"null","Option5":"null","Option6":"null","Option_answer1":"2020-09-01","Option_answer2":"null","Option_answer3":"null","Option_answer4":"null","Option_answer5":"null","Option_answer6":"null","Question_Type":"DATE","Studnet_id":"257","answer_id":"5","exam_id":"1","max_mark":"10","title":"QUESTION 5 DATE"}
            //   ]',
            //   'Staff_id' => '21',
            // );

            $data = $request->all();
            
            if(isset($data['Answer_json_data'])){
                $staff_id = $data['user_id'];
                $answer = json_decode($data['Answer_json_data']);
                $total_mark = 0;
                $exam_id = $answer[0]->exam_id;
                $student_id = $answer[0]->Studnet_id;

                $exam = DB::table('exam_create')->select('*')->where('id',$exam_id)
                ->first();

                foreach ($answer as $key => $value) {
                    DB::table('exam_question_answer')
                    ->where('id',$value->answer_id)
                    ->update([
                        'obtained_mark' => $value->Optained_mark,
                        'updated_by'    => $staff_id,
                        'updated_at'    => Carbon\Carbon::now()
                    ]);

                    $total_mark = $total_mark + $value->Optained_mark;
                }
                $insert = DB::table('exam_mark')->updateOrInsert([
                    'exam_id'       => $exam_id,
                    'student_id'    => $student_id
                ],
                [   
                    'assessment_status' => 1,
                    'mark'              => $total_mark,
                    'updated_at'        => Carbon\Carbon::now(),
                    'updated_by'        => $staff_id
                ]);
                if($insert){
                    $date = Carbon\Carbon::parse($exam->date);
                    $month = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date)->month;
                    $day = "day_".Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date)->day;
                    $yearTitle =Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date)->year;
                    $year = Year::where('title',$yearTitle)->first()->id;
                    $attendanceExist = Attendance::select('attendances.id','attendances.attendees_type','attendances.link_id',
                                'attendances.years_id','attendances.months_id','attendances.'.$day,
                                's.id as students_id','s.reg_no','s.first_name','s.middle_name','s.last_name','s.student_image')
                                ->where('attendances.attendees_type',1)
                                ->where('attendances.years_id',$year)
                                ->where('attendances.months_id',$month)
                                ->where([['s.id', '=' , $student_id]])
                                ->join('students as s','s.id','=','attendances.link_id')
                                ->first();
                            if ($attendanceExist) {
                                $Update = [
                                    'attendees_type' => 1,
                                    'link_id' => $student_id,
                                    'years_id' => $year,
                                    'months_id' => $month,
                                    $day => 1,
                                    'last_updated_by' => $staff_id
                                ];
                                $update = $attendanceExist->update($Update);
                            }else{
                                $data = Attendance::create([
                                    'attendees_type' => 1,
                                    'link_id' => $student_id,
                                    'years_id' => $year,
                                    'months_id' => $month,
                                    $day => 1,
                                    'created_by' => $staff_id,
                                ]);
                            }
                }
                $resp['error'] = 0;
                $resp['msg'] = 'Mark Submitted';
            }else{
                
                $resp['msg'] = 'Answer json data not found';
            }
            // $answer = $data['Answer_json_data'];

            return $this->return_data_in_json($resp);
        }
    /*end exam question */
     /* EXAM END */

    public function user_status(Request $request){
        $user_id = $request->user_id;
        if($user_id){
            $status = DB::table('users')->select('status')->where('id',$user_id)->first();
            if($status){
                $data['status'] = $status->status;    
            }else{
              $data['msg'] = 'Invalid Id!';  
            }
            
        }else{
            $data['msg'] = 'Invalid Request!';
        }
        return $this->return_data_in_json($data);
    }
    
    public function attendance_coordinates(Request $request){
        $data['response'] = '';
        $data['msg'] = 'Success';
        if($request->staff_id){
            $branch_id = DB::table('staff')->select('branch_id')->where([
                            ['id','=',$request->staff_id]
                        ])->first();
            if($branch_id){
                
                $data['response'] = DB::table('branches')->select('longitude','latitude','radius')
                ->where([
                        ['id','=',$branch_id->branch_id]
                    ])->first();
            }else{
                $data['msg'] = 'Invalid Branch.';
            }          
        }else{
            $data['msg'] = 'Invalid Request';
        }
        // dd($data);
        return $this->return_data_in_json($data,$error_msg='');
    }
    public function getNoticeCount(Request $request){
         $data['response'] = '';
         $data['msg']   =   '';
        if($request->role_id){
            $currentDate = Carbon\Carbon::now()->format('Y-m-d');
            $count = DB::table('notices')->select('id')
                ->where([
                        ['notices.publish_date','<=',$currentDate],
                        ['notices.end_date','>=',$currentDate],
                        ['notices.status','>=',1],
                    ])
                 ->whereRaw('FIND_IN_SET(?,display_group)', $request->role_id)
                 ->get();
            $data['response'] = count($count);
                 
            $data['msg']   =   'Notice Found';
                 
                 
        }else{
            $data['msg']   =   'Invalid Request';
        }
        return $this->return_data_in_json($data,$error_msg='');
    }
    
    public function razorpay_before_send(Request $request){
        
        Log::useDailyFiles(storage_path().'/logs/razorpay_pg.log');
        Log::info('Razorpay before send--');
        Log::info($request->all());
        Log::info('Razorpay before send---');
//         $data =array (
//   'amount' => '2090.0',
//   'Branch_ID' => '1',
//   'student_id' => '2744',
//   'f_Name' => 'Test Demo Student',
//   'product_info' => 'Total Fee',
//   'user_id' => '3142',
//   'phone' => '1234567890',
//   'Pay_amount' => '2090.0',
//   'Pay_Paid' => '10',
//   'currency' => 'INR',
//   'receipt' => 'Order_id_recpr_18302',
//   'order_id' => 'order_HcLUV9nTCqgXv5',
//   'assignid' => '[{"ids":"2450,28","month":"6"}]',
//   'email' => 'test@gmail.com',
// )  ;
        
        $resp['msg'] = '';
        $resp['error'] = true;
        
        $data = $request->all();
        if(isset($data['amount']) && isset($data['order_id']) && isset($data['student_id'])){
            $assign_ids = '';
            if($data['assignid']){
                $data['assignid'] = json_decode($data['assignid'],true);

                $data['assignid'] = $this->month_wise_sort($data['assignid']);
                if(count($data['assignid'])){
                    foreach($data['assignid'] as $v){
                        if(isset($v['ids'])){
                          $assign_ids .= ','.$v['ids'];
                        }
                    }
                }
                $assign_ids = ltrim($assign_ids,',');
            }
            
            DB::table('payu_payments')
            ->insert([
                    'account' => 'Razorpay',
                    'txnid' => isset($data['order_id'])?$data['order_id']:'',
                    'mihpayid' => isset($data['order_id'])?$data['order_id']:'',
                    'firstname' => isset($data['f_Name'])?$data['f_Name']:'',
                    'email' => isset($data['email'])?$data['email']:'',
                    'phone' => isset($data['phone'])?$data['phone']:'',
                    'amount' => isset($data['amount'])?$data['amount']:'',
                    'data' => isset($data['product_info'])?$data['product_info']:'',
                    'status' => 'Pending',
                    'unmappedstatus' => 'Pending',
                    'fee_masters_id' => $assign_ids,
                    'student_id' => isset($data['student_id'])?$data['student_id']:'',
                    'feeamount' => isset($data['Pay_amount'])?$data['Pay_amount']:'',
                    'branch_id' => isset($data['Branch_ID'])?$data['Branch_ID']:'',
                    'created_at' => Carbon\Carbon::now(),
                    'login_user_id' =>isset($data['user_id'])?$data['user_id']:'',
                ]);
                $resp['msg'] = 'Record Inserted';
                $resp['error'] = false;
        }else{
            $resp['msg'] = 'Invalid Request!';
        }
        return $this->return_data_in_json($resp);
        
    }
    
    public function razorpay_response(Request $request){
      
        Log::useDailyFiles(storage_path().'/logs/razorpay_pg.log');
        Log::info('Razorpay Response--');
        Log::info($request->all());
        Log::info('Razorpay END--');
        
        $resp['error'] = true;
        $resp['msg'] ='';
        $data = $request->all();
        // $arFail =  array (
        //           'Payment_Id' => 'null',
        //           'Status' => '0',
        //           'Payment_signature' => 'null',
        //           'Payment_Order_Id' => 'order_GrXwSVYR8qtx6Y',
        //         );
                
        //  $data = array (
        //       'Payment_Id' => 'pay_HcJ0ss9u8lrsjf',
        //       'Status' => '1',
        //       'Payment_Data' => '{"razorpay_payment_id":"pay_HcJ0ss9u8lrsjf","razorpay_order_id":"order_HcJ0BhqtwDXU02","razorpay_signature":"0dea53b78f4ef31b644efdc5c7e18dab15fb2db08ec5a1566dc7e0dc91b97f18","org_logo":"","org_name":"Razorpay Software Private Ltd","checkout_logo":"https:\\/\\/cdn.razorpay.com\\/logo.png","custom_branding":false}',
        //       'Payment_signature' => '0dea53b78f4ef31b644efdc5c7e18dab15fb2db08ec5a1566dc7e0dc91b97f18',
        //       'Captured' => 'Payment Success',
        //       'Payment_Order_Id' => 'order_HcLc4RqaJaMCmx',
        //     );     
        if(isset($data['Payment_Order_Id']) && isset($data['Status']) && isset($data['Payment_Id']) && isset($data['Payment_signature'])){
            $exist_data = DB::table('payu_payments')->where([
                        ['txnid','LIKE',$data['Payment_Order_Id']]
                
                ])->first();
                if($exist_data){
                    if($data['Status'] == 0){
                        
                        DB::table('payu_payments')->where('id','=',$exist_data->id)
                        ->update([
                                'status' =>'Failed',
                                'unmappedstatus' =>isset($data['Captured'])?$data['Captured']:'Captured',
                                'txn_response_data' =>isset($data['Payment_Data'])?json_encode($data['Payment_Data']):json_encode($data),
                            ]);
                        
                    }else{
                         DB::table('payu_payments')->where('id','=',$exist_data->id)
                        ->update([
                                'status' =>'Success',
                                'unmappedstatus' =>isset($data['Captured'])?$data['Captured']:'Captured',
                                'net_amount_debit' =>$exist_data->amount,
                                'txn_response_data' =>isset($data['Payment_Data'])?json_encode($data['Payment_Data']):json_encode($data),
                            ]);
                            
                            if($exist_data->fee_masters_id){
                                $ids = explode(',',$exist_data->fee_masters_id);
                                $fee_data = DB::table('assign_fee')->select('assign_fee.id','fee_amount',DB::raw('(fee_amount - COALESCE(SUM(cf.amount_paid),0)) as due'))
                                ->leftjoin('collect_fee as cf',function($j)use($exist_data){
                                    $j->on('cf.assign_fee_id','=','assign_fee.id')
                                    ->where('cf.student_id',$exist_data->student_id)
                                    ->where('cf.status',1);
                                })
                                ->selectRaw('SUM(cf.amount_paid) as total_paid')
                                ->whereIn('assign_fee.id',$ids)
                                ->orderByRaw("FIELD(assign_fee.due_month, '4','5','6','7','8','9','10','11','12','1','2','3') ASC")
                                ->groupBy('assign_fee.id')
                                ->get();
                                
        
        
                                $paid_amount = $exist_data->amount;
                                foreach($fee_data as $v){
                                    if($paid_amount>0){
                                        if($paid_amount >= $v->due){
                                            $receipt_amount = $v->due;
                                            $paid_amount = $paid_amount - $v->due;
                                        }else{
                                            $receipt_amount = $paid_amount;
                                            $paid_amount = $paid_amount - $paid_amount;
                                        }
                                        
                                        
                                        
                                        $insert_id[] =   DB::table('collect_fee')->insertGetId([
                                        'student_id' => $exist_data->student_id,
                                        'assign_fee_id' => $v->id,
                                        'amount_paid' => $receipt_amount,
                                        'reciept_date' => Carbon\Carbon::now()->format('Y-m-d'),
                                        'payment_type' => 'Card',
                                        'discount' => 0,
                                        'created_at' => Carbon\Carbon::now(),
                                        'created_by' => $exist_data->login_user_id,
                                         ]);
                                    }
                                     
                                    
                               
                                
                            
                                }
                                if(isset($insert_id)){
                                    if(count($insert_id)>0){
                                        $receipt_no = $this->reciept_no('',$insert_id);   
                                         DB::table('collect_fee')->whereIn('id',$insert_id)->update([
                                                'reciept_no'=>$receipt_no
                                          ]);
                                    }
                                }
                                
                                
                            }     
                    }
                    $resp['msg'] ='Payment Captured';
                    $resp['error'] = false;
                }else{
                    $resp['msg'] ='No such order id!';
                }
               
        }else{
            $resp['msg'] ='Invalid Request!';
        }
        
        return $this->return_data_in_json($resp);
    }
    
    public function payment_gateway_credentials(Request $request){
        $data['data'] = [];
        $data['error'] = true;
        $data['msg'] = '';
        if($request->branch_id){
            $data['data'] = DB::table('payment_gateway_branch as pgb')->select('pg.gateway_name','pgb.gateway_url','merchant_key','secret','merchant_id','sub_merchant_id')
            ->leftjoin('payment_gateway as pg','pg.id','=','pgb.payment_gateway_id')
            ->where([
                    ['branch_id','=',$request->branch_id],
                    ['pgb.record_status','=',1],
                
                ])
            ->orderBy('pgb.id','desc')
            ->first();
            if($data['data']){
                $data['data'] = $data['data'];
                $data['error'] = false;
                $data['msg'] = 'Data Found';
            }else{
                $data['data'] = [];
                $data['error'] = true;
                $data['msg'] = 'No Data Found';
            }
            
            
        }else{
            $data['msg'] = 'Invalid Request!';
        }
        // Log::debug($data);
        return $this->return_data_in_json($data);
    }
    
    public function support(){
        $data['email'] = env('SUPPORT_EMAIL');
        $data['mobile'] = env('SUPPORT_MOBILE');
        return $this->return_data_in_json($data);
    }
    
    public function student_assign_fee($stdId="",$ssId="",$courseId="",Request $request)
    {
        
        $data = [];
        $uIds=(isset($_GET['uid']) && $_GET['uid']!="")? $_GET['uid'] : $stdId;
        $ssId=(isset($_GET['ssId']) && $_GET['ssId']!="")? $_GET['ssId'] : $ssId;
        $courseId=(isset($_GET['courseId']) && $_GET['courseId']!="")? $_GET['courseId'] : $courseId;
        
        if($uIds==""){
            return $data;
        }
        
        $fee_result=AssignFee::Select('assign_fee.*', 'fee_heads.fee_head_title')
        ->leftJoin('fee_heads', function($join){
            $join->on('fee_heads.id', '=', 'assign_fee.fee_head_id');
        })
        
        ->where('assign_fee.session_id', $ssId)
        ->where('assign_fee.course_id', $courseId)
        ->Where('assign_fee.student_id', '0')
        ->Where('assign_fee.status', 1)
        ->orWhere(function($q) use ($uIds,$ssId,$courseId){
            if($uIds){
                $q->orWhere('assign_fee.student_id', $uIds)
                
                ->where('assign_fee.session_id', $ssId)
                ->where('assign_fee.course_id', $courseId);
            }
        })
        ->orderByRaw("FIELD(assign_fee.due_month, '4','5','6','7','8','9','10','11','12','1','2','3') ASC")
        ->groupBy('assign_fee.id')->get();

        $ret_val=""; $i=0; 

        $arrFeeMaster = [];
        
        if(count($fee_result) && $uIds){$k=0;
            foreach($fee_result as $fee){ $k++;
                $paid_result=DB::table('collect_fee')->where('assign_fee_id', $fee->id)->where('student_id', $uIds)
                ->Where('status' , 1)->sum('amount_paid');
                 $discount_result=DB::table('collect_fee')->where('assign_fee_id', $fee->id)->where('student_id', $uIds)
                ->Where('status' , 1)->sum('discount');
                $due = $fee->fee_amount - ($paid_result + $discount_result);
                $disabled = ($due == 0) ? " style=\"display:none;\"":"";
                $arrFeeMaster[$k]['assignId'] = $fee->id;
                $arrFeeMaster[$k]['Fee Head'] = $fee->fee_head_title;
                $arrFeeMaster[$k]['fee_head_id'] = $fee->fee_head_id;
                $arrFeeMaster[$k]['Fees'] = $fee->times;
                $arrFeeMaster[$k]['amount'] = $fee->fee_amount;
                $arrFeeMaster[$k]['paid'] = $paid_result;
                $arrFeeMaster[$k]['discount'] = $discount_result;
                $arrFeeMaster[$k]['due'] = $due;
                $arrFeeMaster[$k]['month'] = $fee->due_month;
            }
        }
        $fee_arr = [];
        
        if(count($arrFeeMaster)>0){
            foreach($arrFeeMaster as $k => $v){
                if($v['amount']>0){
                    if(!isset($fee_arr[$v['month']])){
                        foreach($v as $key => $val){
                            $fee_arr[$v['month']][$key] = $val;
                        }
                    }else{
                        foreach($v as $key => $val){
                            if($key == 'amount' || $key == 'paid' || $key == 'discount' || $key == 'due'){
                                $fee_arr[$v['month']][$key] += $val;
                            }
                            
                            if($key == 'assignId' || $key == 'Fee Head' || $key == 'fee_head_id'){
                                $fee_arr[$v['month']][$key] .= ','.$val;
                            }
                        }
                    }
                }
            }
        }
        
        if(count($fee_arr)>0){
            $fee_arr = $this->month_wise_sort($fee_arr);
        }else{
            $fee_arr = (object) $fee_arr;
        }
        
        return $this->return_data_in_json($fee_arr,$error_msg="");
        if(count($arrFeeMaster)==0){
            $arrFeeMaster = (object) $arrFeeMaster;
        }
        // dd($arrFeeMaster);
        return $this->return_data_in_json($arrFeeMaster,$error_msg="");
    }
    
    public function month_wise_sort($arr){
        $order = array(4, 5, 6, 7,8,9,10,11,12,1,2,3);
            
            
            usort($arr, function ($a, $b) use ($order) {
                
                $pos_a = array_search($a['month'], $order);
                $pos_b = array_search($b['month'], $order);
                return $pos_a - $pos_b;
            });
        return $arr;    
    }
    
    public function GetReceiptByNo(Request $request)
    {
        $fileLink = asset('images'.DIRECTORY_SEPARATOR.'logo');
        $fileLink = addslashes($fileLink).'/';
        if ($request->receipt_no) {
        $receipt_detail= DB::table('collect_fee')->Select('sd.first_name','sd.reg_no', 'sd.reg_date', 'sd.university_reg','br.branch_name','collect_fee.status','collect_fee.discount', 'collect_fee.reciept_date','collect_fee.amount_paid','collect_fee.reciept_no','collect_fee.payment_type','collect_fee.status', 'asf.fee_amount','fd.fee_head_title')
        
           ->join('students as sd', 'sd.id', '=', 'collect_fee.student_id')
           ->join('assign_fee as asf', 'asf.id', '=', 'collect_fee.assign_fee_id')
           ->join('fee_heads as fd', 'fd.id', '=', 'asf.fee_head_id')
           ->join('branches as br', 'br.id', '=', 'sd.branch_id')
            ->join('faculties as fac', 'asf.course_id', '=', 'fac.id')
           ->where('collect_fee.reciept_no','=',$request->receipt_no)
           ->where('collect_fee.status',1)
        ->get();
        $data['branch_detail']= DB::table('collect_fee')->select('br.branch_name','br.branch_mobile','br.branch_email','br.branch_address',DB::raw("CONCAT('$fileLink',br.branch_logo) as branch_logo"))
              ->leftjoin('students as sd', 'sd.id', '=', 'collect_fee.student_id')
               ->join('branches as br', 'br.id', '=', 'sd.branch_id')
              ->where('collect_fee.reciept_no','=',$request->receipt_no)
              ->where('collect_fee.status',1)
              ->first();
        foreach($receipt_detail as $key=>$value){
            $value->status= 'PAID';
            $arr[]= $value;
        }
    
        $data['receipt_detail']= $arr;
       }
       else{
            $data['msg'] = 'Invalid Request!';
        }
    
        return $this->return_data_in_json($data);
    }
    
    public function student_assignment_detail(Request $request){
        $student_id = $request->student_id?$request->student_id:null;
        $assignment_id = $request->assignment_id?$request->assignment_id:null;
        $data['status'] = 0;
        $data['msg'] = 'Invalid Request!';
        if($student_id && $assignment_id){
            $path=asset('assignments'.DIRECTORY_SEPARATOR.'answers'.DIRECTORY_SEPARATOR);
            $data['data'] = DB::table('assignment_answers')->select('id','answer_text','file','approve_status',DB::raw("CONCAT('$path','/',file) as file"))
            ->where([
                    ['students_id','=',$student_id],
                    ['assignments_id','=',$assignment_id],
                    ['status','=',1],
            ])->first();
            if($data['data']){
                
                    
                
                $data['status'] = 1;
                $data['msg'] = 'Assignment Found';
            }else{
                $data['msg'] = 'No Assignment Found';
            }
        }
        return $this->return_data_in_json($data);
    }
    
    public function change_assignment_answer_status(Request $request){
        $answer_id = $request->answer_id?$request->answer_id:null;
        $status = $request->status?$request->status:null;
        $data['status'] = 0;
        $data['msg'] = 'Invalid Request!';
        if($answer_id && $status){
            if($status == 1 || $status == 2){
                if($status == 1){
                    $data['msg'] = 'Assignment Approved';
                }
                if($status == 2){
                    $data['msg'] = 'Assignment Rejected';
                }
                DB::table('assignment_answers')->where('id',$answer_id)
                ->update([
                        'approve_status' => $status
                    ]);
                    $data['status'] = 1;
            }elseif($status == 3){
                $row = DB::table('assignment_answers')->select('*')->where('id',$answer_id)->first();
                
                if($row->file){
                    $folder_path = public_path().DIRECTORY_SEPARATOR.'assignments'.DIRECTORY_SEPARATOR.'answers'.DIRECTORY_SEPARATOR;
                                if (file_exists($folder_path . $row->file))
                                    @unlink($folder_path . $row->file);
                }
                DB::table('assignment_answers')->where('id',$answer_id)->delete();
                $data['msg'] = 'Assignment Deleted';
                $data['status'] = 1;
            }else{
                $data['msg'] = 'Invalid Status';
            }
            
        }
        return $this->return_data_in_json($data);
        
    }
    public function delete_assignment(Request $request){
        $assignment_id = $request->assignment_id?$request->assignment_id:null;
        $user_id = $request->user_id?$request->user_id:null;
        
        $data['status'] = 0;
        $data['msg'] = 'Invalid Request!';
        if($assignment_id && $user_id){
            DB::table('assignments')->where('id',$assignment_id)
            ->update([
                    'updated_at' =>Carbon\Carbon::now(),
                    'last_updated_by' =>$user_id,
                    'status'=>0,
                ]);
            DB::table('assignment_answers')->where('assignments_id',$assignment_id)
            ->update([
                    'updated_at'=>Carbon\Carbon::now(),
                    'last_updated_by'=>$user_id,
                    'status'=>0,
                
                ]);
            $data['status'] = 1;  
            $data['msg'] = 'Assignment Deleted.';    
        }
        return $this->return_data_in_json($data);
    }
    
    public function student_dashboard_count(Request $request){
        $student_id = $request->student_id; 
        $course_id = $request->course_id; 
        $sec_id = $request->sec_id; 
        $session_id = $request->session_id; 
        $branch_id = $request->branch_id; 
        
        $carbon_date = Carbon\Carbon::now();
        $date = $carbon_date->format('Y-m-d');
        $day = $carbon_date->day;
        $month = $carbon_date->month;
        $year = $carbon_date->year;
        $year_id = 0;
        $year_data  = DB::table('years')->select('id')->where([
                ['title','=',$year]
            ])->first();
        if($year_data){
            $year_id = $year_data->id;
        }    
            $count = [
                'attendance' => 0,
                'time_table' => 0,
                'exam' => 0,
                'live_class' => 0,
                'web_minar' => 0,
                'homework'  => 0
            ];
      
        if($course_id && $sec_id && $session_id && $branch_id ){
            $class = DB::table('live_classes')
            ->where([
               
                ['live_classes.session_id','=',$session_id],
                ['live_classes.branch_id','=',$branch_id],
                ['live_classes.status','=',1],
            ])
            ->where(function($q)use($course_id,$sec_id){
                
                $q->whereRaw('FIND_IN_SET(?,live_classes.faculty_id)', $course_id);
                $q->whereRaw('FIND_IN_SET(?,live_classes.section_id)', $sec_id);
            })
            ->whereBetween("start_time",[$date.' 00:00:00',$date.' 23:59:59'])
            ->count('id');
        
        
            $meetings = DB::table('internal_meetings')
               ->where([
                ['internal_meetings.session_id','=',$session_id],
                ['internal_meetings.branch_id','=',$branch_id],
                ['internal_meetings.status','=',1]
               ])
               ->where(function($q){
                    $q->where('internal_meetings.host_for',0); 
                    $q->orwhere('internal_meetings.host_for',2); 
                })
               ->whereBetween("start_time",[$date.' 00:00:00',$date.' 23:59:59'])
               ->count('id');
            $count['live_class'] = $class;
            $count['web_minar'] = $meetings;
        } 
        if($day && $month && $year_id && $student_id){
            $att = 0;
            $attendance = DB::table('attendances')->where([
                    ['link_id','=',$student_id],  
                    ['attendees_type','=',1],  
                    ['years_id','=',$year_id],  
                    ['months_id','=',$month],  
                ])->first();
                
                if($attendance){
                    for($i=1;$i<=31;$i++){
                        $day_name = 'day_'.$i;
                        if($attendance->$day_name == 1){
                            $att++;  
                        }
                    }
                }
            $count['attendance']    = $att;
        }
        
        if($branch_id && $session_id && $course_id && $sec_id){
            $assignment_data = DB::table('assignments')
                ->where([
                    ['faculty','=',$course_id],
                    ['session_id','=',$session_id],
                    ['semesters_id','=',$sec_id],
                    ['branch_id','=',$branch_id],
                ])
                ->where('publish_date','<=',$carbon_date)
                ->where('end_date','>=',$carbon_date)
                ->count();
             $count['homework'] =     $assignment_data;
        }
            
        $data['data'] = $count;    
        return $this->return_data_in_json($data);    
    }
    public function staff_dashboard_count(Request $request){
        $staff_id = $request->staff_id; 
        $user_id = $request->user_id; 
        
        $session_id = $request->session_id; 
        $branch_id = $request->branch_id; 
        
        $carbon_date = Carbon\Carbon::now();
        $date = $carbon_date->format('Y-m-d');
        $day = $carbon_date->day;
        $month = $carbon_date->month;
        $year = $carbon_date->year;
        $year_id = 0;
        $year_data  = DB::table('years')->select('id')->where([
                ['title','=',$year]
            ])->first();
        if($year_data){
            $year_id = $year_data->id;
        }    
        $count = [
                'attendance' => 0,
                'time_table' => 0,
                'exam' => 0,
                'live_class' => 0,
                'web_minar' => 0,
                'homework'  => 0
            ];
     
        if($staff_id && $user_id && $session_id && $branch_id ){
            $class = DB::table('live_classes')
            ->where([
                ['live_classes.session_id','=',$session_id],
                ['live_classes.branch_id','=',$branch_id],
                ['live_classes.status','=',1],
                ['live_classes.created_by','=',$staff_id],
            ])
            ->whereBetween("start_time",[$date.' 00:00:00',$date.' 23:59:59'])
            ->count('id');
            
            $meetings = DB::table('internal_meetings')
               ->where([
                ['internal_meetings.session_id','=',$session_id],
                ['internal_meetings.branch_id','=',$branch_id],
                ['internal_meetings.status','=',1]
               ])
               ->where(function($q){
                    $q->where('internal_meetings.host_for',0); 
                    $q->orwhere('internal_meetings.host_for',1); 
                })
               ->whereBetween("start_time",[$date.' 00:00:00',$date.' 23:59:59'])
               ->count('id');
            $count['live_class'] = $class;
            $count['web_minar'] = $meetings;
            
            
            
            if($day && $month && $year_id && $staff_id){
                $att = 0;
                $attendance = DB::table('attendances')->where([
                        ['link_id','=',$staff_id],  
                        ['attendees_type','=',2],  
                        ['years_id','=',$year_id],  
                        ['months_id','=',$month],  
                    ])->first();
                    
                    if($attendance){
                        for($i=1;$i<=31;$i++){
                            $day_name = 'day_'.$i;
                            if($attendance->$day_name == 1){
                                $att++;  
                            }
                        }
                    }
                $count['attendance']    = $att;
            }
            
            
            $assignment_data = DB::table('assignments')
                ->where([
                    
                    ['session_id','=',$session_id],
                    ['created_by','=',$user_id],
                    ['branch_id','=',$branch_id],
                ])
                ->where('publish_date','<=',$carbon_date)
                ->where('end_date','>=',$carbon_date)
                ->count();
             $count['homework'] =     $assignment_data;
             
             
            $weekday=DB::select(DB::raw("SELECT dayofweek('$carbon_date') as day"));
            $weekday=$weekday[0]->day;
    
            $date=$carbon_date;

            $date=explode('-', $date);
            $month=$date[1];
            if($month[0]==0){
              $month=$month[1];
            }
            $day=$date[2];
            if($day[0]==0){
              $day=$day[1];
            }
            
             $timetable=DB::table('timetable')->select('id')
            ->leftjoin('attendances as att','att.link_id','=','timetable.staff_id')
            ->leftjoin('years','years.id','=','att.years_id')
            ->leftjoin('faculties as fcl','fcl.id','=','timetable.course_id')
            ->leftjoin('semesters as sem','sem.id','=','timetable.section_id')
            ->leftjoin('staff as st','st.id','=','timetable.staff_id')
            ->leftjoin('timetable_subjects as sub','sub.id','=','timetable.timetable_subject_id')
            ->leftjoin('timetable_alt_teacher as alt',function($j)use($carbon_date){
                $j->on('alt.timetable_id','=','timetable.id')
                ->where('alt.date',$carbon_date);
            })
            ->leftjoin('staff as altstf','altstf.id','=','alt.staff_id')
            
            ->where([
              ['timetable.day_id','=',$weekday],
              ['timetable.branch_id','=',$branch_id],
              ['timetable.session_id','=',$session_id],
              ['timetable.status','=',1],
              ['timetable.staff_id','=',$staff_id],
              ['att.attendees_type','=',2],
              ['att.months_id','=',$month],
              ['years.title','=',$date[0]],
              ['is_break','=',0],
            ])
            ->orderBy('course','Asc')
            ->orderBy('timetable.time_from','Asc')
            ->count();
             $count['time_table'] = $timetable;
             
             
             
             
             
        
        } 
            
        $data['data'] = $count;    
        return $this->return_data_in_json($data);    
    }
    /*
    public function CourseSubjectList(Request $request)
    {
        Log::debug($request->all());
        if($request->course_id && $request->section_id && $request->session_id){
          $data['subject_list']= DB::table('timetable_subjects')
          ->select('id','title')
          ->where('course_id',$request->course_id)
          ->where('section_id',$request->section_id)
          ->where('session_id',$request->session_id)
          ->where('status',1)
          ->get();
        }
        else{
           $data['msg']= 'Invalid Request. Message Validation Failed!!!!! ';   
        }
        return $this->return_data_in_json($data);  
    }
    
    */
    
     public function CourseSubjectList(Request $request)
    {
        
        
         Log::debug($request->all());
        if($request->course_id && $request->section_id && $request->session_id){


            $data['subject_list'] = [];
            if($request->staff_id){
                $data['subject_list']= DB::table('timetable_subjects')
                  ->select('timetable_subjects.id','timetable_subjects.title')
                  ->rightJoin('timetable_assign_subject as tas','timetable_subjects.id','=','tas.timetable_subject_id')
                  ->where('timetable_subjects.course_id',$request->course_id)
                  ->where('timetable_subjects.section_id',$request->section_id)
                  ->where('timetable_subjects.session_id',$request->session_id)
                  ->where('timetable_subjects.status',1)
                  ->where('tas.status',1)
                  ->where('tas.staff_id',$request->staff_id)
                  ->get();
                  
            }
            if(!count($data['subject_list'])>0){
                $data['subject_list']= DB::table('timetable_subjects')
                  ->select('id','title')
                  ->where('timetable_subjects.course_id',$request->course_id)
                  ->where('timetable_subjects.section_id',$request->section_id)
                  ->where('timetable_subjects.session_id',$request->session_id)
                  ->where('timetable_subjects.status',1)
                  ->get();
            }
            
          
        }
        else{
           $data['msg']= 'Invalid Request. Message Validation Failed!!!!! ';   
        }
        return $this->return_data_in_json($data);  
    }
    
    public function upload_staff_profile_image(Request $request){
        $data['msg'] = 'Something went wrong';
        if($request->staff_id && $request->image){
            $staff = DB::table('staff')->where('id',$request->staff_id)->first();
            if($staff){
                if($request->hasFile('image')){
                    $path = public_path().DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'staff'.DIRECTORY_SEPARATOR;
                    $image = $request->file('image');
                    $image_name = rand(1111, 9857).'.'.$image->getClientOriginalExtension();
            
                    $image->move($path, $image_name);  
                    DB::table('staff')->where('id',$staff->id)->update([
                            'staff_image' => $image_name
                        ]);
                    
                    
                    if (file_exists($path.$staff->staff_image))
                        @unlink($path.$staff->staff_image);
                    
                     $data['msg'] = 'Image Uploaded';
                    
                }else{
                    $data['msg'] = 'Invalid File';
                }
            }else{
                $data['msg'] = 'Staff Not Found';
            }
        }else{
            $data['msg'] = 'Invalid Request!';
        }
        
        return $this->return_data_in_json($data); 
    }
    
    public function offline_exam_students_list(Request $request){

        $data['error'] = true;
        $data['msg'] = '';
        $data['data'] = [];

        if($request->exam_id){
            $exam_id = $request->exam_id;
            $exam = ExamCreate::find($exam_id);
            
            if(!$exam){
                $data['msg'] = 'No Such Exam';
            }else{
                
                $students = DB::table('student_detail_sessionwise as sts')
                ->select('std.id as student_id','std.first_name as student_name','std.reg_no',DB::raw("COALESCE(mark,0) as mark"),'em.grade',DB::raw("COALESCE(attendance,1) as attendance"),'assessment_status',DB::raw("$exam->max_mark as max_mark"))
                ->rightJoin('students as std','std.id','=','sts.student_id')
                ->leftjoin('exam_mark as em',function($j)use($exam){
                    $j->on('em.student_id','=','sts.student_id')
                    ->where('em.exam_id',$exam->id);
                })
                ->where([
                    ['sts.session_id','=',$exam->session_id],
                    ['sts.course_id','=',$exam->faculty_id],
                    ['sts.Semester','=',$exam->section_id],
                    ['std.branch_id','=',$exam->branch_id],
                ])->get();

                if(count($students)>0){
                    $data['msg'] = 'Students Found';
                    $data['data'] = $students;
                    $data['error'] = false;
                }else{
                    $data['msg'] = 'No Student Found';
                }

                
            }
        }
        return $this->return_data_in_json($data); 
    }
    
    public function exam_attendance_status_list(){
        $data = [];
        $data[] = [
                'id' => '1',
                'value' => 'Present'
            ];
        $data[] = [
            'id' => '2',
            'value' => 'Absent'
        ]; $data[] = [
            'id' => '3',
            'value' => 'Medical'
        ];
            
            //  $data=
            // [
            //     '1' => 'Present',    
            //     '2' => 'Absent',    
            //     '3' => 'Medical',    
            // ];
       
        return $this->return_data_in_json($data);
    }
    
    public function save_offline_exam(Request $request){
        Log::debug('-- Save Offline Exam Start--');
        Log::debug($request->all());
        Log::debug('-- Save Offline Exam End--');
        $data = $request->all();
        $resp['error'] = true;
        $resp['msg'] = '';
        // $data = array (
        //   'user_id' => '1',
        //   'staff_id' => '1',
        //   'Json_Mark_List' => '[{"Obtain_Grade":"A","Obtain_Mark":"0.00","Student_Name":"Advik Periwal","attendance_opt":"1","reg_no":"TAV/2020-21/UKG/1","student_id":"1118"},{"Obtain_Grade":"B","Obtain_Mark":"0.00","Student_Name":"Anviti Pathak","attendance_opt":"1","reg_no":"TAV/2020-21/UKG/4","student_id":"1121"},{"Obtain_Grade":"C","Obtain_Mark":"0.00","Student_Name":"Aryan Maurya","attendance_opt":"1","reg_no":"TAV/2020-21/UKG/5","student_id":"1122"},{"Obtain_Grade":"null","Obtain_Mark":"0.00","Student_Name":"KUNWAR DEVANSH NAMAN","attendance_opt":"1","reg_no":"TAV/2020-21/UKG/11","student_id":"1128"},{"Obtain_Grade":"null","Obtain_Mark":"0.00","Student_Name":"PREETIKA SHAHI","attendance_opt":"1","reg_no":"TAV/2020-21/UKG/13","student_id":"1130"},{"Obtain_Grade":"null","Obtain_Mark":"0.00","Student_Name":"YATHARTH SINGH","attendance_opt":"1","reg_no":"TAV/2020-21/UKG/15","student_id":"1132"},{"Obtain_Grade":"null","Obtain_Mark":"0.00","Student_Name":"SHIVANSH YADAV","attendance_opt":"1","reg_no":"TAV/2020-21/UKG/17","student_id":"1134"},{"Obtain_Grade":"null","Obtain_Mark":"0.00","Student_Name":"TEJAS SINGH","attendance_opt":"1","reg_no":"TAV/2020-21/UKG/19","student_id":"1136"},{"Obtain_Grade":"null","Obtain_Mark":"0.00","Student_Name":"Anshuman Singh (STAFF WARD)","attendance_opt":"1","reg_no":"TAV/2020-21/UKG/22","student_id":"1139"},{"Obtain_Grade":"null","Obtain_Mark":"0.00","Student_Name":"ARNAV PAVIPANI","attendance_opt":"1","reg_no":"TAV/2020-21/UKG/24","student_id":"1141"},{"Obtain_Grade":"null","Obtain_Mark":"0.00","Student_Name":"PRAKHAR SINGH","attendance_opt":"1","reg_no":"TAV/2020-21/UKG/32","student_id":"1149"},{"Obtain_Grade":"null","Obtain_Mark":"0.00","Student_Name":"PRANAV KUMAR RAI","attendance_opt":"1","reg_no":"TAV/2020-21/UKG/33","student_id":"1150"},{"Obtain_Grade":"null","Obtain_Mark":"0.00","Student_Name":"PRATYUSH DUBEY","attendance_opt":"1","reg_no":"TAV/2020-21/UKG/35","student_id":"1152"},{"Obtain_Grade":"null","Obtain_Mark":"0.00","Student_Name":"SANSKRITI SINGH","attendance_opt":"1","reg_no":"TAV/2020-21/UKG/38","student_id":"1155"},{"Obtain_Grade":"null","Obtain_Mark":"0.00","Student_Name":"SARTHAK SINGH","attendance_opt":"1","reg_no":"TAV/2020-21/UKG/39","student_id":"1156"},{"Obtain_Grade":"null","Obtain_Mark":"0.00","Student_Name":"VIVAAN RAJPUT","attendance_opt":"1","reg_no":"TAV/2020-21/UKG/45","student_id":"1162"},{"Obtain_Grade":"null","Obtain_Mark":"0.00","Student_Name":"SHREYANSH DWIVEDI","attendance_opt":"1","reg_no":"TAV/2020-21/UKG/50","student_id":"1835"},{"Obtain_Grade":"null","Obtain_Mark":"0.00","Student_Name":"ADITYA YADAV","attendance_opt":"1","reg_no":"TAV/2020-21/UKG/56","student_id":"2268"},{"Obtain_Grade":"null","Obtain_Mark":"0.00","Student_Name":"AARAV SINGH","attendance_opt":"1","reg_no":"TAV/2020-21/UKG/23","student_id":"1140"},{"Obtain_Grade":"null","Obtain_Mark":"0.00","Student_Name":"SHREYANSH SINGH","attendance_opt":"1","reg_no":"TAV/2020-21/UKG/41","student_id":"1158"},{"Obtain_Grade":"null","Obtain_Mark":"0.00","Student_Name":"MANAS SINGH","attendance_opt":"1","reg_no":"TAV/2021-22/I/17","student_id":"3101"},{"Obtain_Grade":"null","Obtain_Mark":"0.00","Student_Name":"SARTHAK SINGH","attendance_opt":"1","reg_no":"TAV/2021-22/I/18","student_id":"3102"},{"Obtain_Grade":"null","Obtain_Mark":"0.00","Student_Name":"SANSKAR RAJ","attendance_opt":"1","reg_no":"TAV/2021-22/I/19","student_id":"3103"},{"Obtain_Grade":"null","Obtain_Mark":"0.00","Student_Name":"Aastha","attendance_opt":"1","reg_no":"TAV/2021-22/I/24","student_id":"3608"},{"Obtain_Grade":"null","Obtain_Mark":"0.00","Student_Name":"Siddharth Singh","attendance_opt":"1","reg_no":"TAV/2021-22/I/25","student_id":"3613"},{"Obtain_Grade":"null","Obtain_Mark":"0.00","Student_Name":"HARSHITA SINGH","attendance_opt":"1","reg_no":"TAV/2021-22/I/27","student_id":"3623"},{"Obtain_Grade":"null","Obtain_Mark":"0.00","Student_Name":"Aparaark Shreshtha","attendance_opt":"1","reg_no":"TAV/2021-22/I/28","student_id":"3642"}]',
        //   'exam_id' => '1420',
        // );
        
        if(isset($data['Json_Mark_List']) && isset($data['user_id']) && isset($data['staff_id']) && isset($data['exam_id'])){
                
            if($data['Json_Mark_List'] && $data['user_id'] && $data['exam_id']){
                $send_arr = [
                    'exam_id' => $data['exam_id'],
                    'user_id' => $data['user_id'],
                    'mark' => [],
                    'attendance' => [],
                    'grade' => [],
                ];
                
                
                $marks = json_decode($data['Json_Mark_List']);
                
                if(is_array($marks)){
                    foreach($marks as $k => $v){
                        $send_arr['mark'][$v->student_id] = $v->Obtain_Mark;
                        $send_arr['attendance'][$v->student_id] = $v->attendance_opt;
                        $send_arr['grade'][$v->student_id] = $v->Obtain_Grade;
                    }
                    
                    $req = new Request($send_arr);
                    $response = $this->save_assessment($req);
                    $response = json_decode($response);
                    if($response['data']['error']){
                        $resp['msg'] = 'Something went wrong !';
                    }else{
                        $resp['msg'] = 'Marks Uploaded.';
                        $resp['error'] = false;
                    }
                }
                
            }
        }else{
             $resp['msg'] = 'Invalid Request !';
        }
        return $this->return_data_in_json($resp);
    }
    
    
    public function show_result_to_student(){
        $gen = DB::table('general_settings')->select('show_result_to_student')
        ->first();
        
        return $this->return_data_in_json($gen);
        
    }
    
    public function student_result_link(Request $request){
        $data['error'] = true;
        $data['msg'] = '';
        $data['link'] = '';
        // Log::debug()
        if($request->all()){
            $data['error'] = false;
            $data['msg'] = 'Result Found';
            $data['link'] = 'https://rsworld.adminmitra.com/api/student_result';
        }
        
        return $this->return_data_in_json($data);
    }
    
    public function student_result(Request $request){
        return view('exam.test');
    }
    
    
       public function UpdatePassword(Request $request){
       $data['status']=0;
       if($request->user_id && $request->current_pass && $request->new_pass){
           $validate_user= DB::table('users')->select('id','password','pass_Text')
           ->where('id',$request->user_id)
           ->where('status',1)->first();
          
           if($validate_user){
             $check=Hash::check($request->current_pass,$validate_user->password);

             

             if($check){
                
                 $new_pass_hash= Hash::make($request->new_pass);
                 $update= DB::table('users')->where('id',$request->user_id)->update([
                  'password'=>$new_pass_hash,
                  'pass_Text'=>$request->new_pass

                 ]);
                 if($update){
                    $data['msg']= 'Password Updated Successfully!!';
                    $data['status']=1;
                 }
                 
             }
             else{
                
                 $data['msg']= 'Current passowrd Not match!!';
             }
            

           }
           else{
            $data['msg']= ' Invalid Credentials!!!';
           }  
        }
       else{
            $data['msg']= ' Invalid Request!!!';
       }
       return $this->return_data_in_json($data);
    }
 public function get_student_attendance_new(Request $request){
        $year_id=$month=$day=null;
        Log::debug("get_student_attendance_new");
        Log::debug($request->all());
        // $session_id,$branch_id,$course_id,$sec_id,$date
        $branch_id=$request->branch_id;
        $course_id=$request->course_id;
        $sec_id=$request->sec_id;
        $session_id=$request->session_id;
       
        $date=$request->date;
       
        if($date){
            $date=explode('/', $date);
            $da=$date[0]; 
            $day="day_".ltrim($da,0);   
            $month=$date[1];
            $year=$date[2];

            $year_id=DB::select(DB::raw("SELECT id from years where title=$year"));
            $year_id=$year_id[0]->id;
        }

        $data['student']=DB::table('students')->select('students.first_name as student_name','students.reg_no','students.id as student_id','parent_details.father_first_name as father_name','student_detail_sessionwise.course_id',"$day as status")
        ->leftjoin('student_detail_sessionwise','student_detail_sessionwise.student_id','=','students.id')
        ->leftjoin('parent_details','parent_details.students_id','=','students.id')
        ->whereRaw("students.branch_id=$branch_id and student_detail_sessionwise.course_id=$course_id and student_detail_sessionwise.Semester=$sec_id and students.status=1 and student_detail_sessionwise.session_id=$session_id")
        ->leftjoin('attendances as at',function($j)use($year_id,$month,$day){
            if($year_id && $month && $day){
                $j->on('at.link_id','=','students.id')
                ->where([
                    ['at.years_id','=',$year_id],
                    ['at.months_id','=',$month],
                    ['at.attendees_type','=',1],
                ]);
            }
        })
        // ->where(function($q)use($year,$month,$day){
        //     if($)
        // })
        ->orderBy('student_name','asc')
        ->get();
        // dd($data['student']);
        // $data['student']=DB::select(DB::raw("SELECT students.first_name as student_name,students.reg_no,students.id as student_id,parent_details.father_first_name father_name,student_detail_sessionwise.course_id FROM students 
        //     join student_detail_sessionwise on student_detail_sessionwise.student_id = students.id 
        //     left join parent_details on parent_details.students_id=students.id
        //     where(students.branch_id=$branch_id and student_detail_sessionwise.course_id=$course_id and student_detail_sessionwise.Semester=$sec_id and students.status=1 and student_detail_sessionwise.session_id=$session_id) order By student_name  asc 
        //     "));

         return $this->return_data_in_json($data,$error_msg=""); 
    }

    public function attendance_status_list(){
        // Made By Gaurav Singh
         $data['attendance_status']=DB::table('attendance_statuses')->select('id','title','status')
            ->where('status','!=','0')
            ->get();
            return $this->return_data_in_json($data,$error_msg="");
   
    }
public function save_student_attendance(Request $request){
        Log::debug("save_student_attendance");
        Log::debug($request->all());
        $date       = $request->date; 
        $staff_id   = $request->user_id;
        $Session    = $request->Session;
        $Branch     = $request->Branch;
        $Course     = $request->Course; 
        $Section    = $request->Section;

       $stdAtnd    = $request->Student_attendance_list; 

        $atdArr = json_decode($stdAtnd);

        $date=explode('/', $date);
        $da=$date[0]; 
        $day="day_".ltrim($da,0);   
        $month=$date[1];
        $year=$date[2];
        $year_id=DB::select(DB::raw("SELECT id from years where title=$year"));
        $year_id=$year_id[0]->id;
        $cdate=Carbon\Carbon::now();
        $attStatus =1;
       
        foreach ($atdArr as $key => $value) {
            
             $resp= DB::table('attendances')
                    ->updateOrInsert(
                    ['attendees_type' => 1, 'link_id' => $value->reg_no,'years_id'=>$year_id,'months_id'=>$month],
                    [$day => $value->attendance_opt,'created_by'=>$staff_id,'created_at'=>$cdate->toDateTimeString(),'updated_at'=>$cdate->toDateTimeString()]
                    );
          
            if($resp){
                $student_data[$value->reg_no] = $value->reg_no;
                $attendance_data['student'][$value->reg_no] = $value->attendance_opt;
                $attendance_data['date'] = $request->date;
            }
            if($resp==0){
                $attStatus = 0;
            }              
        }  
        
        
        if(isset($student_data) && isset($attendance_data)){
            $student_token = $this->getStudentTokensById($student_data);

            
            if($student_token){
                if(count($student_token)>0){
                    $att_status = DB::table('attendance_statuses')->select('id','title')
                    ->pluck('title','id')->toArray();
                    
                    if(isset($attendance_data['student']) && $attendance_data['date']){
                        $att_date = $attendance_data['date'];
                        foreach($student_token as $val){
                            if(isset($attendance_data['student'][$val->id])){
                                if(isset($att_status[$attendance_data['student'][$val->id]])){
                                    $student_att_status = $att_status[$attendance_data['student'][$val->id]];

                                    $title = "RSWorld - Attendance - $student_att_status";
                                    $message = "$val->first_name has been marked $student_att_status on $att_date";

                                    $msg = [
                                        'title' => $title,
                                        'description' => $message,
                                        'image_url' => '',
                                    ];
                                    $send_data =[];
                                    $send_data[] = $val;
                                    
                                    $this->sendFirebaseNotification($send_data,$msg);
                                }
                            }
                        }
                    }
                }
            }
        }
        return $status = "{'Status':'".$attStatus."'}";
           
    }
  public function getStudentTokensById($ids){
        
        $student_data = DB::table('students')
        ->select('students.id','token.device_token','first_name')
        
        ->rightJoin('users as ur',function($j){
            $j->on('ur.hook_id','=','students.id')
            ->where('ur.role_id',6);
        })
        ->rightJoin('user_firebase_token as token','token.user_id','=','ur.id')
        ->where([
            // ['students.branch_id','=',$branch_id],
            // ['sts.session_id','=',$session_id],
            // ['sts.course_id','=',$course_id],
            // ['sts.Semester','=',$sec_id],
            // ['sts.active_status','=',1],
            ['students.status','=',1],
            ['token.login_status','=',1],
        ])
        ->whereIn('students.id',$ids)
        ->get();

        return $student_data;
    }
 public function store_firebase_token(Request $request){
       Log::debug("store_firebase_token");
       Log::debug($request->all());
        $data['error'] = true;
        $data['msg'] = 'Invalid Request!';
        if($request->user_id && $request->device_token){
            if($request->device_id){
                DB::table('user_firebase_token')->where('device_id',$request->device_id)
                // ->where('device_token','!=',$request->device_token)
                ->update([

                    'login_status' => 0
                ]);
            }


            DB::table('user_firebase_token')
            ->updateOrInsert(
                [
                'device_token' => $request->device_token,
                'user_id' => $request->user_id,
                ],
                [
                'device_id' => $request->device_id,
                'created_at' => Carbon\Carbon::now(),
                'device_manufacturer' => $request->device_manufacturer,
                'model_no' => $request->model_no,
                'login_status' =>1,
            ]);

            $data['error'] = false;
            $data['msg'] = 'Record Inserted';
        }

        return $this->return_data_in_json($data);
    }
    
    public function update_firebase_login_status(Request $request){
      
        $data['error'] = true;
        $data['msg'] = 'Invalid Request!';
        if($request->user_id && $request->device_id){
                if($request->push_notification == 1){
                    $data['error'] = false;
                    $data['msg'] = 'Push Notification Continues';
                }else{
                    DB::table('user_firebase_token')->where('device_id',$request->device_id)
                    ->where('user_id','=',$request->user_id)
                    ->update([
    
                        'login_status' => 0
                    ]);
                    
                    $data['error'] = false;
                    $data['msg'] = 'Record Inserted';
                }
                
            
            
        }

        return $this->return_data_in_json($data);
    }
    
     public function Staff_designation_List()
    {

        // Made By Gaurav Singh
         $data['staff_designation']=DB::table('staff_designations')->select('id','title')
          ->where('status','!=','0')
        ->get();
            
    
        return $this->return_data_in_json($data);
    }



  public function get_staff_attendance_list(Request $request){
        $year_id=$month=$day=null;
        Log::debug("get_staff_attendance_list");
        Log::debug($request->all());
        // $session_id,$branch_id,$course_id,$sec_id,$date
        $branch_id=$request->branch_id;
        $session_id=$request->session_id;
        $date=$request->date;
       
       if($request->designation_id)
       {
        if($date){
            $date=explode('/', $date);
            $da=$date[0]; 
            $day="day_".ltrim($da,0);   
            $month=$date[1];
            $year=$date[2];

            $year_id=DB::select(DB::raw("SELECT id from years where title=$year"));
            $year_id=$year_id[0]->id;
        }

        $data['staff']=DB::table('staff')->select('staff.first_name as staff_name','staff.reg_no','staff.id as staff_id',"$day as status")
        ->leftjoin('staff_designations','staff_designations.id','=','staff.designation')
        ->where('staff.branch_id',$branch_id)
         ->where('staff.status','!=','0')
         ->where('staff.designation',$request->designation_id)
        ->leftjoin('attendances as at',function($j)use($year_id,$month,$day){
            if($year_id && $month && $day){
                $j->on('at.link_id','=','staff.id')
                ->where([
                    ['at.years_id','=',$year_id],
                    ['at.months_id','=',$month],
                    ['at.attendees_type','=',2],
                ]);
            }
        })
     
        ->orderBy('staff_name','asc')
        ->get();

    }
    else
    {
            $data['msg']='Please Select Designation!!';
    }
         return $this->return_data_in_json($data,$error_msg=""); 
    
    }
    
    
     public function save_staff_attendance(Request $request){
        Log::debug("save_staff_attendance");
        Log::debug($request->all());
        $date       = $request->date; 
        $user_id   = $request->user_id;
        $Session    = $request->Session;
        $Branch     = $request->Branch;
        $staff_id     = $request->staff_id;
        $stdAtnd    = $request->staff_attendance_list; 
            
        $atdArr = json_decode($stdAtnd);

        $date=explode('/', $date);
        $da=$date[0]; 
        $day="day_".ltrim($da,0);   
        $month=$date[1];
        $year=$date[2];
        $year_id=DB::select(DB::raw("SELECT id from years where title=$year"));
        $year_id=$year_id[0]->id;
        $cdate=Carbon\Carbon::now();
        $attStatus =1;
        foreach ($atdArr as $key => $value) {
            // dd($value);
             $resp= DB::table('attendances')
                    ->updateOrInsert(
                    ['attendees_type' => 2, 'link_id' => $value->reg_no,'years_id'=>$year_id,'months_id'=>$month],
                    [$day => $value->attendance_opt,'created_by'=>$user_id,'created_at'=>$cdate->toDateTimeString(),'updated_at'=>$cdate->toDateTimeString()]
                    );
          

            if($resp==0){
                $attStatus = 0;
            }              
        }  

        return $status = "{'Status':'".$attStatus."'}";
           
    }
 
 
 
    public function notification_to_students_by_staff(Request $request){
        
        $resp['msg'] = 'Invalid Request!';
        $resp['error'] = true;

        if($request->branch_id && $request->session_id && $request->course_id && $request->section_id && $request->title && $request->description && $request->user_id){
            $course_id = $request->course_id;
            $sec_id = $request->section_id;
            $session_id = $request->session_id;
            $branch_id = $request->branch_id;
            $user_id = $request->user_id;

            $title = $request->title;
            $message = $request->description;
            $student_list = [];
            $student_ids = $request->student_id?rtrim($request->student_id,','):'';

            $student_list = explode(',',$student_ids);

            if(count($student_list) >0){
                $student_data = $this->getStudentTokensById($student_list);
            }else{
                $student_data = $this->getStudentTokens($course_id,$sec_id,$session_id,$branch_id);
            }

            if(count($student_data)>0){

                $msg = [
                    'title' => $title,
                    'description' => $message,
                    'image_url' => '',
                ];

                $this->sendFirebaseNotification($student_data,$msg);
                $insert = [
                    'user_type' => 1,
                    'course_id' => $course_id,
                    'section_id' => $sec_id,
                    'student_id' => $student_ids,
                    'session_id' => $session_id,
                    'branch_id' => $branch_id,
                    'title' => $title,
                    'description' => $message,
                    'image_url' => '',
                    'created_by' => $user_id
                ];
                $this->storeFirebaseNotification($insert);
                
                $resp['msg'] = 'Notification Sent';
                $resp['error'] = false;
            }else{
                $resp['msg'] = 'No Notification Sent';
                $resp['error'] = false;
            }
        }
        
        return $this->return_data_in_json($resp);
    }

  
     public function notification_to_staff(Request $request){
        $resp['msg'] = 'Invalid Request!';
        $resp['error'] = true;
        Log::debug("notification_to_staff");
        Log::debug($request->all());
        
        if($request->branch_id && $request->session_id && $request->title && $request->description && $request->user_id){

            $session_id = $request->session_id;
            $branch_id = $request->branch_id;
            $user_id = $request->user_id;
            $designation_id = $request->designation_id?$request->designation_id:0;

            $title = $request->title;
            $message = $request->description;


            $staff_data = $this->getStaffToken($branch_id,$session_id,$designation_id,$user_id);
            
            if(count($staff_data) > 0){
                $msg = [
                    'title' => $title,
                    'description' => $message,
                    'image_url' => '',
                ];

                $this->sendFirebaseNotification($staff_data,$msg);
                $insert = [
                    'user_type' => 2,
                    'course_id' => 0,
                    'section_id' => 0,
                    'student_id' => 0,
                    'session_id' => $session_id,
                    'branch_id' => $branch_id,
                    'staff_designation' => $designation_id,
                    'staff_id' => 0,
                    'title' => $title,
                    'description' => $message,
                    'image_url' => '',
                    'created_by' => $user_id
                ];
                $this->storeFirebaseNotification($insert);
                $resp['msg'] = 'Notification Sent';
                $resp['error'] = false;
            }else{
                $resp['msg'] = 'Unable To Send Notification - No Other Data Found';
                $resp['error'] = false;
            }

        }

        return $this->return_data_in_json($resp);
    }
    
    
    
       public function get_firebase_notification_list(Request $request){
        
        Log::debug("get_firebase_notification_list");
        Log::debug($request->all());
        
        $resp['msg']='Invalid Request!';
        $resp['error']=true;
        $resp['data'] = [];
         
        if($request->user_type == 2){
            $resp['data']=DB::table('firebase_notifications')->select('id','title','description','image_url','page_url',DB::raw("DATE(created_at) as date"))
            ->where('record_status',1)
            ->where('user_type',2)
            ->where('branch_id',$request->branch_id)
            ->where('session_id',$request->session_id)

            ->where(function($q)use($request){
                if($request->staff_id){
                    $q->where('staff_id',$request->staff_id)
                     ->orwhere('staff_id',0);
                }
            })
            ->where(function($q)use($request){
                if($request->designation_id){
                    $q->where('staff_designation',$request->designation_id);
                    
                    $q->where(function($q2)use($request){
                        $q2->where('staff_id',$request->staff_id)
                        ->orwhere('staff_id',0);
                        
                    });
                    
                }
            })
            ->limit(20)
            ->orderBy('id','desc')
            ->get();
            
            $resp['msg']='Data Found';
            $resp['error']=false;
        }elseif($request->user_type == 1){
            $std_id=0;
            if($request->user_id){
                $std = DB::table('users')->select('hook_id')
                ->where([
                    ['id','=',$request->user_id]
                ])->first();
                if($std){
                    $std_id = $std->hook_id;
                }
            }
            
            $resp['data']=DB::table('firebase_notifications')->select('id','title','description','image_url','page_url',DB::raw("DATE(created_at) as date"))
            ->where('record_status',1)
            ->where('user_type',1)
            ->where('branch_id',$request->branch_id)
            ->where('session_id',$request->session_id)

            ->where(function($q)use($request){
                
                    $q->where('course_id',$request->course_id)
                     ->where('section_id',$request->section_id);
                
            })
            ->where(function($q)use($request,$std_id){
                
                    // $q->where('staff_designation',$request->designation_id);
                    
                    $q->where(function($q2)use($request,$std_id){
                        
                        $q2->whereRaw("FIND_IN_SET(student_id,$std_id)")
                        ->orwhere('student_id',0);
                        
                    });
                    
                
            })
            ->limit(20)
            ->orderBy('id','desc')
            ->get();
            
            Log::debug( $resp['data']);
            $resp['msg']='Data Found';
            $resp['error']=false;
        }else{
            $resp['msg']='No Data Found';
            $resp['error']=false;
        }
        

       
        return $this->return_data_in_json($resp);
    }
    public function get_firebase_notification_count(Request $request){
      
      //  Log::debug("get_firebase_notification_count");
        Log::debug($request->all());
        // dd("hello");
       
        $resp['msg']='Invalid Request!';
        $resp['error']=true;
        $resp['data'] = 0;
         
        if($request->user_type == 2){
            $data=DB::table('firebase_notifications')->select('id','title','description','image_url','page_url',DB::raw("DATE(created_at) as date"))
            ->where('record_status',1)
            ->where('user_type',2)
            ->where('branch_id',$request->branch_id)
            ->where('session_id',$request->session_id)

            ->where(function($q)use($request){
                if($request->staff_id){
                    $q->where('staff_id',$request->staff_id)
                     ->orwhere('staff_id',0);
                }
            })
            ->where(function($q)use($request){
                if($request->designation_id){
                    $q->where('staff_designation',$request->designation_id);
                    
                    $q->where(function($q2)use($request){
                        $q2->where('staff_id',$request->staff_id)
                        ->orwhere('staff_id',0);
                        
                    });
                    
                }
            })
            
            
           
            ->limit(20)
            ->orderBy('id','desc')
            ->get();
            $resp['data'] = count($data);
            $resp['msg']='Data Found';
            $resp['error']=false;
        }elseif($request->user_type == 1){
            $std_id=0;
            if($request->user_id){
                $std = DB::table('users')->select('hook_id')
                ->where([
                    ['id','=',$request->user_id]
                ])->first();
                if($std){
                    $std_id = $std->hook_id;
                }
            }
            
            $data=DB::table('firebase_notifications')->select('id','title','description','image_url','page_url',DB::raw("DATE(created_at) as date"))
            ->where('record_status',1)
            ->where('user_type',1)
            ->where('branch_id',$request->branch_id)
            ->where('session_id',$request->session_id)

            ->where(function($q)use($request){
                
                    $q->where('course_id',$request->course_id)
                     ->where('section_id',$request->section_id);
                
            })
            ->where(function($q)use($request,$std_id){
                
                    // $q->where('staff_designation',$request->designation_id);
                    
                    $q->where(function($q2)use($request,$std_id){
                        
                        $q2->whereRaw("FIND_IN_SET(student_id,$std_id)")
                        ->orwhere('student_id',0);
                        
                    });
                    
                
            })
            ->limit(20)
            ->orderBy('id','desc')
            ->get();
            Log::debug($request->all());
            Log::debug($data);
            $resp['data'] = count($data);
            $resp['msg']='Data Found';
            $resp['error']=false;
        }else{
            $resp['msg']='No Data Found';
            $resp['error']=false;
        }
        

        $resp['msg']='Data Found';
        $resp['error']=false;
        return $this->return_data_in_json($resp);
    }
      public function get_student_list(Request $request){
        $resp['msg'] = 'Invalid Request!';
        $resp['error'] = true;
        $resp['data'] = [];
        if($request->branch_id && $request->session_id && $request->course_id && $request->section_id){
            $branch_id = $request->branch_id;
            $session_id =$request->session_id;
            $course_id =$request->course_id;
            $sec_id =$request->section_id;


            $student_data = DB::table('students')
            ->select('students.id','students.first_name',DB::raw("CONCAT(students.first_name,'-',pd.father_first_name) as student_name"))
            ->leftjoin('student_detail_sessionwise as sts','sts.student_id','=','students.id')
            ->leftjoin('parent_details as pd','pd.students_id','=','students.id')
            ->where([
                ['students.branch_id','=',$branch_id],
                ['sts.session_id','=',$session_id],
                ['sts.course_id','=',$course_id],
                ['sts.Semester','=',$sec_id],
                ['sts.active_status','=',1],
                ['students.status','=',1],
            ])
            ->get();
            if(count($student_data)>0){

                $resp['msg'] = 'Data Found';
            }else{

                $resp['msg'] = 'No Data Found';
            }
            $resp['data'] = $student_data; 
            $resp['error'] = false;
        }

        return $this->return_data_in_json($resp);
    }
    
    
      public function sendFirebaseNotification($token_data,$msg){
        if(count($token_data)>0 && isset($msg['title']) && isset($msg['description'])){
            // Set POST variables
            $url = 'https://fcm.googleapis.com/fcm/send';
    
            $headers = array(
                'Authorization: key=AAAArVzBoeM:APA91bGfP0LahqhRI1bzkeDXNeI22-C_AHWHj2izEiq2uCCWF7BHgCdk4NagKvZqsPxUeew9NHeyc5YmRW_NJ9H8SCZL1jh5BgSHO_KPnV_FgOOPmvx-zZ3SmfrIBClDbWv1nnU_6UFF',
                'Content-Type: application/json'
            );
            
    
            $temp_fields=array(
                "to"=>'',
                "notification"=>array(
                    "body"=>isset($msg['description'])?$msg['description']:'',
                    "title"=>isset($msg['title'])?$msg['title']:env('APPLICATION_TITLE'),
                    "icon"=>isset($msg['image_url'])?$msg['image_url']:'',
                    "click_action"=>isset($msg['page_url'])?$msg['page_url']:'',
                )
            );
            foreach($token_data  as $key =>$val){
                $temp_fields['to'] = $val->device_token;
                if(!empty($temp_fields['to'])){
                    // Open connection
                    $ch = curl_init();
            
                    // Set the url, number of POST vars, POST data
                    curl_setopt($ch, CURLOPT_URL, $url);
            
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
                    // Disabling SSL Certificate support temporarly
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($temp_fields));
                    $result = curl_exec($ch);
                    Log::debug("sendFirebaseNotification");
                    Log::debug($result);
                    
                    curl_close($ch);
                }
            }
            
        }
    }
    
     public function getStaffToken($branch_id,$session_id,$designation_id,$user_id=0){
        $staff_data = DB::table('staff')
        ->select('staff.id','token.device_token')
        ->rightJoin('users as ur',function($j){
            $j->on('ur.hook_id','=','staff.id')
            ->where('ur.role_id','!=',6);
        })
        ->rightJoin('user_firebase_token as token','token.user_id','=','ur.id')
        ->where([
            ['staff.branch_id','=',$branch_id],
            ['staff.status','=',1],
            ['ur.status','=',1],
            ['ur.id','!=',$user_id],
            ['token.login_status','=',1],
        ])
        ->where(function($q)use($designation_id){
            if($designation_id){
                $q->where('staff.designation',$designation_id);
            }
        })
        ->get();

        return $staff_data;
    }
    
    public function notification_to_students_by_principal(Request $request){
        $resp['msg'] = 'Invalid Request!';
        $resp['error'] = true;
        Log::debug("notification_to_students_by_principal");
       Log::debug($request->all());

        if($request->branch_id && $request->session_id && $request->course_id && $request->section_id && $request->title && $request->description && $request->user_id){
            $course_id = $request->course_id?rtrim($request->course_id,','):0;
            $sec_id = $request->section_id;
            $session_id = $request->session_id;
            $branch_id = $request->branch_id;
            $user_id = $request->user_id;

            $title = $request->title;
            $message = $request->description;
            
            $course_list =  explode(',',$course_id);

           

            if(count($course_list)>0){
                foreach($course_list as $k => $v){
                    $student_data = $this->getStudentTokens($v,$sec_id,$session_id,$branch_id);

                    if(count($student_data)>0){

                        $msg = [
                            'title' => $title,
                            'description' => $message,
                            'image_url' => '',
                        ];

                        $this->sendFirebaseNotification($student_data,$msg);
                        $insert = [
                            'user_type' => 1,
                            'course_id' => $v,
                            'section_id' => $sec_id,
                            'student_id' => 0,
                            'session_id' => $session_id,
                            'branch_id' => $branch_id,
                            'title' => $title,
                            'description' => $message,
                            'image_url' => '',
                            'created_by' => $user_id
                        ];
                        $this->storeFirebaseNotification($insert);
                    }
                }
                $resp['msg'] = 'Notification Sent!';
                $resp['error'] = false;
            }else{
                $resp['msg'] = 'No Data Found!';
                $resp['error'] = false;
            }

            
        }
        
        return $this->return_data_in_json($resp);
    }
    
    
     public function getStudentTokens($course_id,$sec_id,$session_id,$branch_id){
        $student_data = DB::table('students')
        ->select('students.id','token.device_token')
        ->leftjoin('student_detail_sessionwise as sts','sts.student_id','=','students.id')
        ->rightJoin('users as ur',function($j){
            $j->on('ur.hook_id','=','students.id')
            ->where('ur.role_id',6);
        })
        ->rightJoin('user_firebase_token as token','token.user_id','=','ur.id')
        ->where([
            ['students.branch_id','=',$branch_id],
            ['sts.session_id','=',$session_id],
            ['sts.course_id','=',$course_id],
            ['sts.Semester','=',$sec_id],
            ['sts.active_status','=',1],
            ['students.status','=',1],
            ['token.login_status','=',1],
        ])
        ->get();

        return $student_data;
    }
    
     public function storeFirebaseNotification($data){
        if(count($data) > 0 ){
            $data['created_at'] = Carbon\Carbon::now();
            $data['record_status'] = 1;
            DB::table('firebase_notifications')
            ->insert($data);

            return true;
        }

        return false;
    }
     
    public function sendAssignmentNotification($assignmentId){
        $data = DB::table('assignments')
        ->select('assignments.*','ts.title as subject_name')
        ->leftjoin('timetable_subjects as ts','ts.id','=','assignments.subjects_id')
        ->where('assignments.id',$assignmentId)
        ->first();
        if($data){
            if($data->faculty && $data->semesters_id && $data->session_id && $data->branch_id){

                $student_data = $this->getStudentTokens($data->faculty,$data->semesters_id,$data->session_id,$data->branch_id);
                

                $title = "RSWorld- New Home Work - $data->subject_name";
                $message = "One new home work of $data->subject_name";

                $msg = [
                    'title' => $title,
                    'description' => $message,
                    'image_url' => '',
                ];

                $this->sendFirebaseNotification($student_data,$msg);
                $insert = [
                    'user_type' => 1,
                    'course_id' => $data->faculty,
                    'section_id' => $data->semesters_id,
                    'student_id' => 0,
                    'session_id' => $data->session_id,
                    'branch_id' => $data->branch_id,
                    'title' => $title,
                    'description' => $message,
                    'image_url' => '',
                    'created_by' => $data->created_by
                ];
                $this->storeFirebaseNotification($insert);
            }
        }
    }

  public function Category(){
         $category=category_model::select('category_name','id')->get();
         $data = $category->all();
         $prep  = ['id'=>0,'category_name'=>'select category'];
         array_unshift($data,$prep);


         return $this->return_data_in_json($data);
    }
   
    public function Religion() {
        $religion=DB::table('religions')->select('id','title')->where('record_status',1)->get();
        $data = $religion->all();
        $prep  = ['id'=>0,'title'=>'select Religion'];
         array_unshift($data,$prep);
         return $this->return_data_in_json($data);
    }
    public function UpdateStudentProfile(request $request)
    {
        //dd($request);
       Log::debug('Update studnet end');
        Log::debug($request->all());
        $student_id=$request->student_id;
        $first_name=$request->first_name;
        $date_of_birth=$request->date_of_birth;
        $religion_id=$request->religion_id;
        $category_id=$request->category_id;
        $father_name=$request->father_name;
        $mother_name=$request->mother_name;
        $address=$request->address;
        $state=$request->state;
        $country=$request->country;
        $mobile_1=$request->mobile_1;               
         



        $env_setting_status=env('student_profile_update_status');

       // dd($env_setting_status);
        if($request->student_id){
             $data['status']=0;
             $student= DB::table('students')->select('students.id','students.first_name','students.date_of_birth','students.religion_id','students.category_id','pd.father_first_name','pd.mother_first_name','ai.address','ai.state','ai.country','ai.mobile_1')
                ->leftjoin('parent_details as pd', 'pd.students_id', '=', 'students.id')
                ->leftjoin('addressinfos as ai', 'ai.students_id', '=', 'students.id')
                ->where('students.id',$request->student_id)->where('students.status',1)->first();
            if($env_setting_status==1)
            {
            if($student){
               
              
                     $updateStudent= DB::table('students')->where('id',$student_id)->Update([
                    'first_name'=>$first_name,
                    'date_of_birth'=>$date_of_birth,
                    'religion_id'=>$religion_id,
                    'category_id'=>$category_id,
                    

                ]);
                $UpdateParentdetail= DB::table('parent_details')->where('students_id',$student_id)->update([
                'father_first_name'=>$father_name,
                'mother_first_name'=>$mother_name,
              
                ]);

                $UpdateAddress= DB::table('addressinfos')->where('students_id',$student_id)->update([
                'address'=>$address,
                'state'=>$state,
                'country'=>$country,
                'mobile_1'=>$mobile_1,
               
                ]);
               

                 $insert= DB::table('student_log')->insertGetId([
                'student_id'=>$student_id,
                'first_name'=>$first_name,
                'date_of_birth'=>$date_of_birth,
                'religion_id'=>$religion_id,
                'category_id'=>$category_id,
                'father_name'=>$father_name,
                'mother_name'=>$mother_name,
                'address'=>$address,
                'state'=>$state,
                'country'=>$country,
                'mobile_1'=>$mobile_1,
                'created_at'=>Carbon\Carbon::now(),
                'status'=>1,


                 ]);

                
                if($insert){
                   
                    $data['msg']= 'Student Updated successfully!!!';
                    $data['status']=1;
                }
                else{
                    $data['msg']= 'Student  Not Updated successfully!!';
                 }

            }
            else{
                $data['msg']= 'Invalid Student!!';
            }
         }
         else if($env_setting_status==2)
         {
       
             if($student){
        
                $insert= DB::table('student_log')->insertGetId([
                        'student_id'=>$student_id,
                        'first_name'=>$first_name,
                        'date_of_birth'=>$date_of_birth,
                        'religion_id'=>$religion_id,
                        'category_id'=>$category_id,
                        'father_name'=>$father_name,
                        'mother_name'=>$mother_name,
                        'address'=>$address,
                        'state'=>$state,
                        'country'=>$country,
                        'mobile_1'=>$mobile_1,
                        'created_by'=>$student_id,
                        'status'=>0,
                        


                 ]);
               
                if($insert){
                   
                    $data['msg']= 'Please Wait Admin Approve Your Request!!!';
                    $data['status']=1;
                }
               

            }
            else{
                $data['msg']= 'Invalid Student!!';
            }
        }
       } 
        return $this->return_data_in_json($data);
    }



    public function history_student_detail(Request $request)
    {
        
        $student_id=$request->student_id;
       // dd($student_id);
        $data['student_history']=0;
        if($student_id)
        {
               $data['student_history']= DB::table('student_log')->select('student_log.id','student_log.student_id','student_log.first_name','student_log.date_of_birth','student_log.father_name','student_log.mother_name','student_log.address','student_log.state','student_log.country','student_log.mobile_1','student_log.created_at','religions.title as religion_name','category.category_name','student_log.status')


                ->leftjoin('religions','religions.id','=','student_log.religion_id')
                ->leftjoin('category','category.id','=','student_log.category_id')
                ->where('student_log.student_id',$student_id)
                 ->get();
        }
        else
        {
             $data['msg']='Please Provide Student Id!!';
             $data['status']=0;

        }

          return $this->return_data_in_json($data);
    }

    public function approve_update_profile_status(Request $request)
    {

        Log::debug('Update staff end');
        Log::debug($request->all());
        $row_id=$request->row_id;
        $student_id=$request->student_id;
        $user_id=$request->user_id;
        $staff_id=$request->staff_id;
        $status=$request->status;
        $first_name=$request->first_name;
        $date_of_birth=$request->date_of_birth;
        $religion_id=$request->religion_id;
        $category_id=$request->category_id;
        $father_name=$request->father_name;
        $mother_name=$request->mother_name;
        $address=$request->address;
        $state=$request->state;
        $country=$request->country;
        $mobile_1=$request->mobile_1;
                

       

        
        if($row_id && $student_id && $user_id && $status)
        {
            $data['status']=0;
             $student= DB::table('students')->select('students.id','students.first_name','students.date_of_birth','students.religion_id','students.category_id','pd.father_first_name','pd.mother_first_name','ai.address','ai.state','ai.country','ai.mobile_1')
                ->leftjoin('parent_details as pd', 'pd.students_id', '=', 'students.id')
                ->leftjoin('addressinfos as ai', 'ai.students_id', '=', 'students.id')
                ->where('students.id',$request->student_id)->where('students.status',1)->first();
            

            //dd($student);

            if($student){
               
                $update= DB::table('student_log')->where('student_log.id',$row_id)->update([
                       
                        'updated_by'=>$request->user_id,
                        'updated_at'=>Carbon\Carbon::now(),
                        'status'=>$status,

                 ]);

                if($update)
                {
                     $updateStudent= DB::table('students')->where('id',$student_id)->Update([
                    'first_name'=>$first_name,
                    'date_of_birth'=>$date_of_birth,
                    'religion_id'=>$religion_id,
                    'category_id'=>$category_id,
                    'last_updated_by'=>$user_id,

                ]);
                $UpdateParentdetail= DB::table('parent_details')->where('students_id',$student_id)->update([
                'father_first_name'=>$father_name,
                'mother_first_name'=>$mother_name,
                'last_updated_by'=>$user_id,
                ]);

                $UpdateAddress= DB::table('addressinfos')->where('students_id',$student_id)->update([
                'address'=>$address,
                'state'=>$state,
                'country'=>$country,
                'mobile_1'=>$mobile_1,
                'last_updated_by'=>$request->user_id,
                ]);
                }



                $update_other= DB::table('student_log')
                ->where([
                    ['id','!=',$row_id],
                    ['student_id','=',$student_id],
                ])->update([
                       
                        'updated_by'=>$user_id,
                        'updated_at'=>Carbon\Carbon::now(),
                        'status'=>2,

                 ]);
              //  dd($update);
                if($update_other)
                {
                    $data['msg']= 'Student Updated successfully!!!';
                    $data['status']=1;
                }
                else if($update){
                   
                    $data['msg']= 'Student Updated successfully!!!';
                    $data['status']=1;
                }
                else{
                    $data['msg']= 'Student  Not Updated successfully!!';
                 }

            }
            else{
                $data['msg']= 'Invalid Student!!';
            }
        }
       else{
            $data['msg']= 'Invalid Request. Message Validation Failed!!!!';
       }
        return $this->return_data_in_json($data);


        }


    

   
     public function history_student_detail_staff_end(Request $request)
    {
       

       $student_id = $request->student_id;
       if($student_id)
       {
        $data['student_admin_history']=0;
        $data['student_admin_history']= DB::table('student_log')->select('student_log.id','student_log.student_id','student_log.first_name','student_log.date_of_birth','student_log.father_name','student_log.mother_name','student_log.address','student_log.state','student_log.country','student_log.mobile_1','student_log.created_at','religions.title as religion_name','category.category_name','student_log.father_name','student_log.status','student_log.religion_id','student_log.category_id')

                ->leftjoin('religions','religions.id','=','student_log.religion_id')
                ->leftjoin('category','category.id','=','student_log.category_id')
                 ->where('student_log.student_id',$student_id)
                ->get();
}
                 return $this->return_data_in_json($data);

    }



    // 1. Please Note That Auto Approve
    // 2. Please Note That Admin Approve
    public function update_profile_status_check(Request $request)
    {
         $data['msg']=0;
          $data['status']=0;
        //Log::debug($request->all());
        $env_setting_status=env('student_profile_update_status');
        
           $student=DB::table('student_log')->where('student_log.student_id',$request->student_id)->first();

        if($env_setting_status==1)
        {
            
            if($student)
            {

           $getstatus=DB::table('student_log')->where('student_log.status',1)->first();

           if($getstatus)
           {
          // dd($getstatus); 
             $data['msg']="Your Are Not Authorized Another Request!!";
             $data['status']=1;
          }

         
        }
        }   
        else if($env_setting_status==2)
        {
          
            if($student)
            {
             $getstatus=DB::table('student_log')->where('student_log.status',0)->first();

           if($getstatus)
           {

             $data['msg']="Please Wait First Admin Approve Your Previous Request!!";
             $data['status']=1;
              
           }
          
        }
        
        

        }
         return $this->return_data_in_json($data);
    }

    public function student_profile_show(Request $request)
    {
        $student_id = $request->student_id;
        $student_profile=0;
        $data['student_profile']= DB::table('students')->select('students.id','students.first_name',DB::raw("DATE_FORMAT(students.date_of_birth,'%Y-%m-%d') as dob "),'students.religion_id','students.category_id','pd.father_first_name','pd.mother_first_name','ai.address','ai.state','ai.country','ai.mobile_1','religions.title as religion_name','category.category_name')
                ->leftjoin('parent_details as pd', 'pd.students_id', '=', 'students.id')
                ->leftjoin('addressinfos as ai', 'ai.students_id', '=', 'students.id')
                ->leftjoin('religions','religions.id','=','students.religion_id')
                ->leftjoin('category','category.id','=','students.category_id')
                ->where('students.id',$student_id)
                ->where('students.status',1)
                ->first();
                 // $dob=Carbon\Carbon::parse( $student_profile->date_of_birth)->format('d-m-Y');
                 // $student_profile->date_of_birth= $dob;
                 // $data['student_profile_show']= $student_profile;

                 return $this->return_data_in_json($data);
    }


        public function student_list_profile(Request $request)
        {

 
                $session_id = $request->session_id;
                $branch_id = $request->branch_id;
                $course_id = $request->course_id;
                $sec_id = $request->sec_id;
            
                $data['student']=DB::table('students')->select('students.first_name as student_name', 'students.reg_no','students.id as student_id','parent_details.father_first_name as father_name','student_detail_sessionwise.course_id')   
                  ->leftJoin('student_detail_sessionwise','student_detail_sessionwise.student_id', '=' ,
                    'students.id')
                  ->leftJoin('parent_details' , 'parent_details.students_id','=','students.id')
                  ->where('students.branch_id','=',$branch_id)
                  ->where('student_detail_sessionwise.course_id','=',$course_id)
                  ->where('student_detail_sessionwise.Semester','=',$sec_id)
                  ->where('students.status','=','1')
                  ->where('student_detail_sessionwise.session_id','=',$session_id)
                  ->orderBy('student_name','asc')->get();

                  return $this->return_data_in_json($data);
        }
        
        
        
        //Student_Profile_List After Delete Check All Module
        
          public function get_student_profiles(Request $request,$stdId="")
    {
        // Log::debug($request->all().)
        $data = [];
        $uIds=(isset($_GET['uid']) && $_GET['uid']!="")? $_GET['uid'] : $stdId;
        
        if($uIds==""){
            return $data;
        }

        $fileLink = asset('images'.DIRECTORY_SEPARATOR.'studentProfile');
        $fileLink = addslashes($fileLink).'/';
        $data['student'] = StudentPromotion::select('students.id','students.reg_no', 'students.reg_date',DB::raw("DATE_FORMAT(students.reg_date,'%d-%M-%Y') as reg_date"),
            'students.branch_id','branches.branch_name',
            'students.category_id','category.category_name',
            'students.session_id','session.session_name',
            'student_detail_sessionwise.course_id','faculties.faculty as course_name','student_detail_sessionwise.semester','semesters.semester as section_semester', 
            'students.academic_status', 'students.first_name', 'students.date_of_birth', 'students.gender',DB::raw("DATE_FORMAT(students.date_of_birth,'%d-%M-%Y') as date_of_birth"), 'students.blood_group', 'students.nationality',
            'students.mother_tongue', 'students.email', 'students.extra_info', DB::raw("CONCAT('$fileLink',students.student_image) as student_image"), 'students.status','pd.father_first_name', 'pd.father_middle_name',
            'pd.father_last_name', 'pd.father_eligibility', 'pd.father_occupation', 'pd.father_mobile_1', 'pd.father_email', 'pd.mother_first_name','pd.mother_middle_name', 'pd.mother_last_name', 'pd.mother_eligibility', 'pd.mother_occupation','pd.mother_mobile_1', 'pd.mother_email',
            'ai.address', 'ai.state', 'ai.country','ai.home_phone',
            'ai.mobile_1', 'ai.mobile_2', 'gd.id as guardian_id', 'gd.guardian_email','gd.guardian_first_name', 'gd.guardian_middle_name', 'gd.guardian_last_name',
            'gd.guardian_eligibility', 'gd.guardian_occupation',
            'gd.guardian_mobile_1', 'gd.guardian_mobile_2', 'gd.guardian_email', 'gd.guardian_relation', 'gd.guardian_address','university_reg as admission_no','enroll_no','rel.title as religion','ur.email as user_email')

            //->where('student_detail_sessionwise.session_id', $current_session_id)
            ->where('students.id','=',$uIds)
            ->where(function($q)use($request){
                if($request->session_id){
                    $q->where('student_detail_sessionwise.session_id',$request->session_id);
                }
            })
            ->leftJoin('students','student_detail_sessionwise.student_id', '=', 'students.id')
            ->leftJoin('parent_details as pd', 'pd.students_id', '=', 'students.id')
            ->leftJoin('addressinfos as ai', 'ai.students_id', '=', 'students.id')
            ->leftJoin('student_guardians as sg', 'sg.students_id','=','students.id')
            ->leftJoin('guardian_details as gd', 'gd.id', '=', 'sg.guardians_id')
            ->leftJoin('faculties','student_detail_sessionwise.course_id', '=', 'faculties.id')
            ->leftJoin('semesters','student_detail_sessionwise.semester', '=', 'semesters.id')
            ->leftJoin('session','student_detail_sessionwise.session_id', '=', 'session.id')
            ->leftJoin('category','students.category_id', '=', 'category.id')
            ->leftJoin('branches','students.branch_id', '=', 'branches.id')
            ->leftJoin('religions as rel','rel.id','=','students.religion_id')
            ->leftJoin('users as ur',function($j){
                $j->on('ur.hook_id','=','students.id')
                ->where('ur.role_id',6);
            })
            ->first();
        return $this->return_data_in_json($data,$error_msg="");
    }
 //Admin DashBoard APi
    

     public function routes_list(){

            // Made By Gaurav Singh
         $data['routes_list']=DB::table('routes')->select('routes.id','routes.title','routes.description','vehicles.number','vehicles.model')
        ->leftJoin('route_vehicles','routes_id','=','routes.id')
        ->leftjoin('vehicles','vehicles.id','=','route_vehicles.vehicles_id')
        ->where('routes.id','!=','0')
        // ->where('routes.status',1)
        // ->where('vehicles.status',1)
         ->get();
            
        // $i=0;
        // foreach ($routes_list as $key => $value) {
            
        //     $data[$i]['id']=$value->id;
        //     $data[$i]['title']=$value->title;
        //     $data[$i]['description']=$value->description;
        //     $i++;
        // }

        // $prep = ['title'=>'-Routes-','id'=>'0','description'=>''];
        // array_unshift($data,$prep);

        return $this->return_data_in_json($data,$error_msg="");
   
    }
        public function routes_delete(Request $request){
        
        // Made By Gaurav Singh
            $data=[];
            if($request->route_id){
                $resp = DB::table('routes')->where('id',$request->route_id)
                        ->update([
                            'status' =>0,
                             'last_updated_by'     => $request->staff_id,
                            'updated_at'    => Carbon\Carbon::now(),
                        ]);
                if($resp){
                    $data['msg'] = 'Routes Deleted';
                }else{
                    $data['msg'] = 'No such Routes';
                }        
            }else{
                $data['msg'] = 'Invalid Request !';
            }
            return $this->return_data_in_json($data);
        
        }
        public function stoppage_list(Request $request)
        {
            // Made By Gaurav Singh
                    //Update
            if($request->route_id)
            {
                  $data['stoppage_list']= DB::table('stoppages')
                  ->select('stoppages.id as stoppage_id','stoppages.title as stoppages_title','stoppages.distance','stoppages.fee_amount','routes.title as route_name','routes.id as route_id')
                    ->leftJoin('routes','routes.id','=','stoppages.route_id')
                   // ->leftjoin('routes','routes.id','=','stoppages.routes_id')
                 ->where('stoppages.route_id','!=','0')
         
                ->where('stoppages.record_status',1)
               
                   ->where(function($q)use($request){
                        if($request->route_id){
                            $q->where('stoppages.route_id',$request->route_id);
                              }
                    })
                    ->get();
               }else{
                        $data['msg'] = 'Invalid Request !';
                    }
                return $this->return_data_in_json($data);  
        }

        public function stoppage_delete(Request $request)
        {
            // Made By Gaurav Singh
        
             $data=[];
                if($request->stoppage_id){
                    $resp = DB::table('stoppages')->where('id',$request->stoppage_id)
                            ->update([
                                'record_status' =>0,
                                'updated_by'     => $request->staff_id,
                                'updated_at'    => Carbon\Carbon::now(),
                            ]);
                    if($resp){
                        $data['msg'] = 'Stoppage Deleted';
                    }else{
                        $data['msg'] = 'No such Stoppage';
                    }        
                }else{
                    $data['msg'] = 'Invalid Request !';
                }
                return $this->return_data_in_json($data);
        }


        public function update_routes_status(Request $request){
            $data=[];
            // Made By Gaurav Singh
            if($request->route_id && $request->staff_id && $request->has('status')){
               
                $resp = DB::table('routes')->where('id',$request->route_id)
                        ->update([
                            'status' => $request->status,
                            'last_updated_by'     => $request->staff_id,
                            'updated_at'     => Carbon\Carbon::now(),
                        ]);
                if($resp){
                    $data['msg'] = 'Routes Status Changed';
                }else{
                    $data['msg'] = 'Something went wrong';
                }        
            }else{
                $data['msg'] = 'Invalid Request !';
            }
            return $this->return_data_in_json($data);
        }

   public function update_stoppage_status(Request $request){
            $data=[];
            // Made By Gaurav Singh
            if($request->stoppage_id && $request->staff_id && $request->has('status')){
               
                $resp = DB::table('stoppages')->where('id',$request->stoppage_id)
                        ->update([
                            'active_status' => $request->status,
                            'updated_by'     => $request->staff_id,
                            'updated_at'     => Carbon\Carbon::now(),
                        ]);
                if($resp){
                    $data['msg'] = 'Stoppage Status Changed';
                }else{
                    $data['msg'] = 'Something went wrong';
                }        
            }else{
                $data['msg'] = 'Invalid Request !';
            }
            return $this->return_data_in_json($data);
        }
        
        
        
        //New
        public function vehicle_list(Request $request)
        {
        
            // Made By Gaurav Singh
             $data['vehicles_list']=DB::table('vehicles')->select('vehicles.id','vehicles.number','vehicles.type','vehicles.model','vehicles.status','vehicles.description','staff.first_name','staff.mobile_1','staff.address')
            ->leftJoin('vehicle_staffs','vehicles_id','=','vehicles.id')
            ->leftjoin('staff','staff.id','=','vehicle_staffs.staffs_id')
            ->where('vehicles.id','!=','0')
            ->get();
                
            
            return $this->return_data_in_json($data,$error_msg="");
        }
        public function stoppage_add(Request $request)
        {
            $staffIds=(isset($request->staffId))?$request->staffId:null;
            $route_ids=(isset($request->route_id))?$request->route_id:null;
            $stoppage_titles=(isset($request->stoppage_title))?$request->stoppage_title:null;
            $distances=(isset($request->distance))?$request->distance:null;
            $fee_Amounts=(isset($request->fee_amount))?$request->fee_amount:null;
            $active_status=1;
            $record_status=1;
           
            $date=Carbon\Carbon::now();
             $id = DB::table('stoppages')->insertGetId(
                ['created_by' => $staffIds, 
                'stoppages.route_id' => $route_ids, 
                'stoppages.title' => $stoppage_titles,
                'stoppages.distance'=>$distances,
                'stoppages.fee_amount'=>$fee_Amounts,
                'stoppages.active_status'=>$active_status,
                'stoppages.record_status'=>$record_status,
                'stoppages.created_at'=>$date->toDateTimeString(),
                'stoppages.updated_at'=>$date->toDateTimeString()]
            );
             $data['insertdeId']=$id;
             $data['status']=false;
              $data['msg']="";
             if($id)
             {
                $data['status']=1;
                $data['msg']=" Data Inserted";
             }
             else
             {
                $data['status']=0;
                $data['msg']=" Data Not Inserted";
             }
            return $this->return_data_in_json($data);
        }



        public function stoppage_update(Request $request)
        {
    
       
            $stoppage_ids=(isset($request->stoppage_id))?$request->stoppage_id:null;
            $staffIds=(isset($request->staffId))?$request->staffId:null;
            $route_ids=(isset($request->route_id))?$request->route_id:null;
            $stoppage_titles=(isset($request->stoppage_title))?$request->stoppage_title:null;
            $distances=(isset($request->distance))?$request->distance:null;
            $fee_Amounts=(isset($request->fee_amount))?$request->fee_amount:null;
            $active_status=1;
            $record_status=1;
           
            $date=Carbon\Carbon::now();
             $id = DB::table('stoppages')->where('id',$stoppage_ids)->update(
                ['updated_by' => $staffIds, 
                'stoppages.route_id' => $route_ids, 
                'stoppages.title' => $stoppage_titles,
                'stoppages.distance'=>$distances,
                'stoppages.fee_amount'=>$fee_Amounts,
                'stoppages.active_status'=>$active_status,
                'stoppages.record_status'=>$record_status,
                'stoppages.updated_at'=>$date->toDateTimeString()]
    
            );
             $data['insertdeId']=$id;
             $data['status']=false;
              $data['msg']="";
             if($id)
             {
                $data['status']=1;
                $data['msg']=" Data Update";
             }
             else
             {
                $data['status']=0;
                $data['msg']=" Data Not Update";
             }
            return $this->return_data_in_json($data);
          
        }   
        
        public function staff_list(){

        // Made By Gaurav Singh
         $data['staff_list']=DB::table('staff')->select('staff.id','staff.reg_no','staff.join_date','staff.first_name','staff.designation','staff_designations.title as designation')
        ->leftJoin('staff_designations','staff_designations.id','=','staff.id')
        ->where('staff_designations.status','!=','0')
        ->get();
        return $this->return_data_in_json($data,$error_msg="");
   
        }

        public function vehicle_add(Request $request){
             
            $branch_id=$request->branch_id;
            $session_id=$request->session_id;
            $entire_json=json_decode($request->entire_json);   

            
            $vehicle_number = DB::table('vehicles')->where('number',$request->number)->first();
            
            if(!$vehicle_number)
            {
              $insert = DB::table('vehicles')->insertGetId([
                    'number'            =>  $request->number,
                    'type'              =>  $request->type,
                    'model'             =>  $request->model,
                    'description'       =>  $request->description,
                    'status'            =>  1,
                    'created_at'        =>  Carbon\Carbon::now(),
                    'created_by'        =>  $request->staff_id  
                        

                ]);
                
                foreach ($entire_json as $key => $value) {
                 
                      $data['list']= DB::table('vehicle_staffs')->insert([
                    'vehicles_id'            =>  $insert,
                    'staffs_id'              =>  $value->id,
                    'status'                 =>  1
                ]);
                       

                }

                if($data){
                    $resp['msg'] = 'Record Created';
                    $resp['status']=1;
                }else{
                    $resp['msg'] = 'Something went wrong';
                    $resp['status']=0;
                }
            // }else{
            //     $resp['msg'] = 'Invalid Request';
            // }
            }
            else
            {
               $resp['msg'] = 'Vehicle Number All Ready Available!!'; 
               $resp['status']=0;
            }
            Log::debug($resp); 
            return $this->return_data_in_json($resp);   
        }
        public function transport_detail(Request $request)
        {

            $user_type=$request->user_type;
            $route_id=$request->route_id;
            $Vehicle_id=$request->vehicle_id;
            $status=$request->status;
            if($request->user_type==1){

                $list=DB::table('transport_users')->select('transport_users.id','transport_users.routes_id','transport_users.vehicles_id','transport_users.member_id','transport_users.user_type','transport_users.stoppage_id','transport_users.status','students.first_name as name','students.reg_no','routes.title','routes.description','vehicles.number','vehicles.type','vehicles.model',DB::raw("'Student' as user_type"))
                    ->leftJoin('students','students.id','=','transport_users.member_id')
                    ->leftJoin('routes','routes.id','=','transport_users.routes_id')
                    ->leftJoin('vehicles','vehicles.id','=','transport_users.vehicles_id')
                   // ->where('transport_users.status','!=',0)
                    ->where('transport_users.user_type',1)
                    ->where(function($q)use($request){
                        if($request->route_id){
                            $q->where('transport_users.routes_id',$request->route_id);
                              }
                    })
                    ->where(function($q)use($request){
                        if($request->vehicle_id){
                            $q->where('transport_users.vehicles_id',$request->vehicle_id);
                              }
                    })
                     ->where(function($q)use($request){
                        if($request->status){
                            $q->where('transport_users.status',$request->status);
                              }
                    })

                    ->get();
            }
           elseif($request->user_type==2){
             $list=DB::table('transport_users')->select('transport_users.id','transport_users.routes_id','transport_users.vehicles_id','transport_users.member_id','transport_users.user_type','transport_users.stoppage_id','transport_users.status','staff.first_name as name','staff.reg_no','routes.title','routes.description','vehicles.number','vehicles.type','vehicles.model',DB::raw("'Staff' as user_type"))
                     ->leftJoin('staff','staff.id','=','transport_users.member_id')
                    ->leftJoin('routes','routes.id','=','transport_users.routes_id')
                    ->leftJoin('vehicles','vehicles.id','=','transport_users.vehicles_id')
                    //->where('transport_users.status','!=',0)
                    ->where('transport_users.user_type',2)

                    ->where(function($q)use($request){
                        if($request->route_id){
                            $q->where('transport_users.routes_id',$request->route_id);
                              }
                    })
                    ->where(function($q)use($request){
                        if($request->vehicle_id){
                            $q->where('transport_users.vehicles_id',$request->vehicle_id);
                              }
                    })
                     ->where(function($q)use($request){
                        if($request->status){
                            $q->where('transport_users.status',$request->status);
                              }
                    })
                    ->get();
           }
            else
            {
               $list= 'invalid request';
            }
                   
            $data['transport_detail']= $list;
        return $this->return_data_in_json($data,$error_msg="");

        }

        public function transport_detail_leave(Request $request)
        {
            Log::debug($request->all());
            $transport_detail_row_id=$request->transport_detail_id;
            $transport_detail_status=$request->transport_detail_status;
                        $staff_id =$request->staff_id;  
           
                $data['transport_detail_leave'] = DB::table('transport_users')->where('id',$transport_detail_row_id)
                        ->update([
                            'status'            =>  $transport_detail_status,
                            'updated_at'        =>  Carbon\Carbon::now(),
                            'last_updated_by'   =>  $staff_id
                        ]);
                    

        
            if($data){
                    $resp['msg'] = 'Record Created';
                    $resp['status']=1;
                }else{
                    $resp['msg'] = 'Something went wrong';
                    $resp['status']=0;
                }
             return $this->return_data_in_json($resp);

        }



        public function vehicle_route_list(Request $request)
        {

                    $data['vehicle_routes']=DB::table('routes')->select('routes.id','routes.title','routes.description','routes.status','vehicles.number','vehicles.type','vehicles.model','vehicles.description')
                    ->leftJoin('route_vehicles','route_vehicles.routes_id','=','routes.id')
                   ->leftJoin('vehicles','vehicles.id','=','route_vehicles.vehicles_id')
                    ->where(function($q)use($request){
                        if($request->route_id){
                            $q->where('route_vehicles.routes_id',$request->route_id);
                              }
                    })
                    ->where('routes.status','!=','0')
                    ->get();
                    return $this->return_data_in_json($data);
    

        }

    
     public function transport_user_shift(Request $request)
        {   
            Log::debug($request->all());
            $branch_id=$request->branch_id;
            $session_id=$request->session_id;
            $route_id=$request->route_id;
            $vehicle_id=$request->vehicle_id;
            $transport_detail_row_id=$request->transport_detail_row_id;
            $staff_id=$request->staff_id;


                 $update_data = DB::table('transport_users')->where('id',$transport_detail_row_id)
                        ->update([
                            'last_updated_by'     => $request->staff_id,
                            'updated_at'     => Carbon\Carbon::now(),
                            'routes_id'     => $route_id,
                            'vehicles_id'     =>$vehicle_id
                        ]);
                

                if($update_data)
                {
                $insert = DB::table('transport_histories')->insertGetId([
                        'years_id' => $session_id,
                        'routes_id' => $route_id,
                        'vehicles_id' => $vehicle_id,
                        'travellers_id' => $transport_detail_row_id,
                        'history_type' => "Shift",
                        'last_updated_by' =>$staff_id,
                        'updated_at' =>Carbon\Carbon::now()
                 ]);
                
               }

                if($insert){
                    $resp['msg'] = 'Record Created';
                    $resp['status']=1;
                }else{
                    $resp['msg'] = 'Something went wrong';
                    $resp['status']=0;
                }
            // }else{
            //     $resp['msg'] = 'Invalid Request';
            // }
            return $this->return_data_in_json($resp);   
        }
        
       public function transport_assign_user(Request $request)
        {
          //dd($request->all());
        $userType = $request->user_type;
        $regNo = $request->reg_no;
        $status = $request->status;
        $route = $request->route;
        $vehicle = $request->vehicle_id;
        $duration= $request->duration;
        $stoppage=$request->stoppage;
        $from=$request->from_date;
        $to=$request->to_date;
        $rent=$request->rent;
        $total_fare=$request->total_fare;
        $date=Carbon\Carbon::now();
        $year=$request->year_id;
        $session=$request->session_id;
        $branch_id=$request->branch_id;
        $staff_id=$request->staff_id;
        $paid=$request->paid;
        $payment_mode=$request->payment_mode;
        $ref_no=$request->ref_no;
        //dd($from,$to);
        Log::debug($request->all());
         if(!$year){
            
             $resp['msg'] = 'Active Year Not Found.Please, Set Year For History Record.';
        }

        if($userType && $regNo){
            switch ($userType){
                case 1:
                    $data= DB::table('students')->where('reg_no','=',$regNo)->first();
                     //dd($data);
                    break;
                case 2:
                    $data = DB::table('staff')->where('reg_no','=',$regNo)->first();
                    // dd($data->id);
                    break;
                default:
                    
            }
        }else{
           
             $resp['msg'] = 'Registration Number or User Type is not Valid.';
        }
        $UserStatus = DB::table('transport_users')->where(['user_type' => $userType, 'member_id' => $data->id])->orderBy('id','desc')->first();
            
            
            if($UserStatus)
            {
                $resp['msg'] = 'Already Registered. Please Edit This TransportUser.'; 
                $resp['status']=0;
                
            }
            else
            {
             if($data)
             {
           // dd($from,$to);
             $incId=DB::table('transport_users')->insertGetId([
             'member_id'=>$data->id,
             'created_at'=>Carbon\Carbon::now(),
             'created_by'=>$staff_id,
             'routes_id'=>$route,
             'vehicles_id'=>$vehicle,
             'stoppage_id'=>$stoppage,
             'user_type'=>$userType,
             'status'=>1,
             'duration'=>$duration,
             'from_date'=>Carbon\Carbon::parse($from)->format('Y-m-d'),
             'to_date'=>Carbon\Carbon::parse($to)->format('Y-m-d'),
             'rent'=>$rent,
             'total_rent'=>$total_fare,
             'session'=>$session,
             'branch'=>$branch_id,
             ]);

             if($incId){
                    $CreateHistory =DB::table('transport_histories')->insertGetId([
                        'years_id' => $year,
                        'routes_id' => $route,
                        'vehicles_id' => $vehicle,
                        'travellers_id' =>$incId,
                        'history_type' => "Registration",
                        'created_by' => $staff_id,
                    ]);

            }

            if($paid && $paid>0)
            { 
             $collect_fee=DB::table('transport_collect_fees')->insertGetId([
             'amount_paid'=>$paid,
             'member_id'=>$data->id,
             'transport_user_id'=>$incId,
             'created_at'=>$date,
             'ref_no'=>$ref_no,
             'pay_mode'=>$payment_mode,
             'member_type'=>$userType,
             'session_id'=>$session,
             'branch_id'=>$branch_id,
             'receipt_by'=>$staff_id,
            ]);
              $getno=10000+$incId;
              $rcp=env('PREFIX_REG').'T'.$getno;
             
             $update=DB::table('transport_collect_fees')
             ->where('id',$incId)
             ->update(['receipt_no'=>$rcp]);
            $resp['msg'] = 'Registered & Fee Collected Successfuly!!'; 
            $resp['status']=1;

             }
           
           $resp['msg'] = 'Registered  Successfuly!!';   
           $resp['status']=1;
       }
        else
       {
         $resp['msg'] = 'Something Wrong??!!';   
           $resp['status']=1;
       }
    }
            return $this->return_data_in_json($resp);
        }
   
    public function transport_collection_due_reoprt(Request $request)
        {
            $userType = $request->member_type;
            $regNo = $request->reg_no;
            $route =$request->route;
            if($regNo){
            switch ($userType){
                case 1:
                    $id = DB::table('students')->where('reg_no','=',$regNo)->first();
                    break;
                case 2:
                    $id = DB::table('staff')->where('reg_no','=',$regNo)->first();
                    break;
                default:
                    
                   
            }
            if($id){
                
                 $msg['msg'] = 'Invalid User!!';   
            }
        }

        $data = []; $rep=[]; $respo=[];
        if($userType!=0)
        {
        $user = DB::table('transport_users')->select('transport_users.id','user_type', 'member_id', 'status','total_rent','duration','br.branch_name','br.branch_logo','br.branch_mobile','br.branch_email','br.branch_address','from_date','to_date')
         ->leftJoin('branches as br', 'br.id', '=', 'transport_users.branch')
            ->where(function ($query) use ($request) {

                if (!empty($request->member_type)) {
                    $query->where('user_type', '=', $request->member_type);
                    $this->filter_query['user_type'] = $request->member_type;
                }
                if(!empty($request->route)){

                    $query->where('routes_id','=',$request->route);
                    $this->filter_query['routes_id'] = $request->route;
                }
                if(!empty($request->reg_no)){
                    if($request->member_type==1)
                        $Id = $this->getStudentIdByReg($request->reg_no);
                    else
                     $Id = $this->getStaffByReg($request->reg_no);
                    $query->where('member_id','=',$Id);
                    $this->filter_query['member_id'] = $Id;
                }
             })
             ->get(); 
             $paid=[];
              foreach ($user as $i) {
                  $paid[]=DB::select(DB::raw("SELECT sum(amount_paid) as total_paid,transport_users.id,transport_users.member_id,transport_users.user_type from transport_collect_fees left join transport_users on transport_users.id = transport_collect_fees.transport_user_id  where transport_users.id='$i->id'")); 
               }
               
              
               foreach ($user as $key => $value) {
                  
                  foreach ($paid as $pkay => $val) {
                    foreach ($val as $k => $v) {
                        if($v->id==$value->id){
                        if($v->total_paid){
                             $value->paid=$v->total_paid;
                             $value->due= ($value->total_rent - $v->total_paid ) ;
                             $respo[]= $value;   
                             
                        }
                        else{
                            $value->paid=0;
                             $value->due= $value->total_rent  ;
                             $respo[]= $value;   
                        }
                        }
                        
                    }
                  }
               }
        }
       //$routes = Route::select('id','title')->get();
        // $map_routes = array_pluck($routes,'title','id');
      //  $data['routes'] = $map_routes;         
      
       foreach ($respo as $key => $value) {
                   # code...
               // dd($key);
                if ($value->user_type==1) {
                    # code...

                    $list=DB::table('students')->select('students.first_name as name','students.reg_no',DB::raw("'Student' as user_type"))
                      ->where('students.id','=',$value->member_id)
                      ->first();
                      $value->name= $list->name;
                      $value->reg_no= $list->reg_no;
                      $value->user_type= $list->user_type;
                }
                else 
                {
                    $list=DB::table('staff')->select('staff.first_name as name','staff.reg_no',DB::raw("'Staff' as user_type"))
                      ->where('staff.id','=',$value->member_id)
                      ->first();
                       $value->name= $list->name;
                      $value->reg_no= $list->reg_no;
                      $value->user_type= $list->user_type;
                }
                $respo[]= $value;
               }
      return $this->return_data_in_json($respo,$error_msg="");
        }
     public function delete_vehicle(Request $request)
        {
              $data=[];
                if($request->vehicle_id){
                    $resp = DB::table('vehicles')->where('id',$request->vehicle_id)
                            ->update([
                                'status' =>0,
                                'last_updated_by'     => $request->staff_id,
                                'updated_at'    => Carbon\Carbon::now(),
                            ]);
                    if($resp){
                        $data['msg'] = 'Vehicle Deleted';
                    }else{
                        $data['msg'] = 'No such Stoppage';
                    }        
                }else{
                    $data['msg'] = 'Invalid Request !';
                }
                return $this->return_data_in_json($data);
        }
        
        
         public function update_vehicle_status(Request $request)
        {
              $data=[];
                if($request->vehicle_id){
                    $resp = DB::table('vehicles')->where('id',$request->vehicle_id)
                            ->update([
                                'status' =>$request->status,
                                'last_updated_by'     => $request->staff_id,
                                'updated_at'    => Carbon\Carbon::now(),
                            ]);
                    if($resp){
                        $data['msg'] = 'Vehicle Update Successfully';
                    }else{
                        $data['msg'] = 'No such Stoppage';
                    }        
                }else{
                    $data['msg'] = 'Invalid Request !';
                }
                return $this->return_data_in_json($data);
        }
        
        
            public function user_type()
        {
            $test = array(
         array(
        'type' => '-Select Type-',  
        'id' => '0' 
             ),
          array(
        'type' => 'Students', 
        'id' => '1'
            ),
            array(
        'type' => 'Staff', 
        'id' => '2'
         )
        );

             return $this->return_data_in_json($test);
        }
         public function status_list()
        {
            $test = array(
         array(
        'type' => '-Select Status-',  
        'id' => '0' 
             ),
          array(
        'type' => 'Active', 
        'id' => '1'
            ),
            array(
        'type' => 'InActive', 
        'id' => '2'
         )
        );

             return $this->return_data_in_json($test);
        }
        
        
        
            public function transport_duration()
        {
            $test = array(
         array(
        'type' => '-Select Duration-',  
        'value'=>'0'
             ),
          array(
        'type' => 'Monthly', 
       'value'=>'monthly'
            ),
            array(
        'type' => 'Quarterly', 
        'value'=>'quarterly'
         ),
              array(
        'type' => 'Half Yearly', 
        'value'=>'half_yearly'
         ),
                array(
        'type' => 'Yearly', 
        'value'=>'yearly'
         )
        );

             return $this->return_data_in_json($test);
        }
        
         public function payment_mode(Request $request)
        {

                    $data['payment_type']=DB::table('payment_type')->select('payment_type.id','payment_type.type_name','payment_type.status')
                  
                    ->where('payment_type.status','!=','0')
                    ->get();
                    return $this->return_data_in_json($data);
    

        }
        
        
       public function EnquiryFollowupList(Request $request)
        {
            
        Log::debug("Enquiry Follow Up List");
        Log::debug($request->all());
         if($request->id){
    
            $data['data']= DB::table('enquiry_followup')->select('followup_date','next_followup','response','note','enquiry_id','first_name as name','enq_date','enquiry_followup.id as followup_id')
            ->leftjoin('enquiries','enquiries.id','=','enquiry_followup.enquiry_id')
            ->where('enquiry_followup.record_status',1)
            ->where('enquiry_followup.enquiry_id',$request->id)
            ->get();
      
            }    
        else
            {
            $data['msg']= "Invalid Request Msg Validation Failed!!!";
            }
     return $this->return_data_in_json($data);
    }    
    
         public function transport_collection_report(Request $request)
        {
            $user_type=$request->user_type;
            $route_id=$request->route_id;
            $from_date=$request->from_date;
            $to_date=$request->to_date;
            $pay_mode=$request->pay_mode;
            $reference_id=$request->reference_id;
            $branch_id=$request->branch_id;
            $session_id=$request->session_id;
        
            
            if($request->user_type==1){
                $list=DB::table('transport_collect_fees')->select('transport_collect_fees.id','transport_collect_fees.amount_paid','transport_collect_fees.ref_no','transport_collect_fees.pay_mode','transport_collect_fees.member_type','transport_collect_fees.receipt_no','students.first_name as student_name','students.reg_no','routes.title','routes.description','vehicles.number','vehicles.type','vehicles.model','transport_users.total_rent',DB::raw("'Student' as user_type"))
                    ->leftJoin('transport_users','transport_users.id','=','transport_collect_fees.transport_user_id')
                    ->leftJoin('students','students.id','=','transport_collect_fees.member_id')
                    ->leftJoin('routes','routes.id','=','transport_users.routes_id')
                    ->leftJoin('vehicles','vehicles.id','=','transport_users.vehicles_id')
                    ->where('transport_collect_fees.status','!=',0)
                    ->where('transport_collect_fees.member_type',1)
                    ->where(function($q)use($request){
                        if($request->route_id){
                            $q->where('transport_users.routes_id',$request->route_id);
                              }
                    })
                    ->where(function($q)use($request){
                        if($request->branch_id){
                            $q->where('transport_collect_fees.branch_id',$request->branch_id);
                              }
                    })
                     ->where(function($q)use($request){
                        if($request->pay_mode){
                            $q->where('transport_collect_fees.pay_mode',$request->pay_mode);
                              }
                    })
                     ->where(function($q)use($request){
                                            if($request->session_id){
                                                $q->where('transport_collect_fees.session_id',$request->session_id);
                                                  }
                                        })
                     ->where(function($q)use($from_date,$to_date){
                          if($from_date && $to_date){
                          $q->whereBetween('transport_collect_fees.created_at',[$request->from_date.  " 00:00:00",$request->to_date." 23:59:00"]);
                         }
           })
                    ->get();
            }
           elseif($request->user_type==2){
             $list=DB::table('transport_collect_fees')->select('transport_collect_fees.id','transport_collect_fees.amount_paid','transport_collect_fees.ref_no','transport_collect_fees.pay_mode','transport_collect_fees.member_type','transport_collect_fees.receipt_no','staff.first_name as student_name','staff.reg_no','routes.title','routes.description','vehicles.number','vehicles.type','vehicles.model','transport_users.total_rent',DB::raw("'Staff' as user_type"))
                    ->leftJoin('transport_users','transport_users.id','=','transport_collect_fees.transport_user_id')
                    ->leftJoin('staff','staff.id','=','transport_collect_fees.member_id')
                    ->leftJoin('routes','routes.id','=','transport_users.routes_id')
                    ->leftJoin('vehicles','vehicles.id','=','transport_users.vehicles_id')
                    ->where('transport_collect_fees.status','!=',0)
                    ->where('transport_collect_fees.member_type',1)
                    ->where(function($q)use($request){
                        if($request->route_id){
                            $q->where('transport_users.routes_id',$request->route_id);
                              }
                    })
                    ->where(function($q)use($request){
                        if($request->branch_id){
                            $q->where('transport_collect_fees.branch_id',$request->branch_id);
                              }
                    })
                     ->where(function($q)use($request){
                        if($request->pay_mode){
                            $q->where('transport_collect_fees.pay_mode',$request->pay_mode);
                              }
                    })
                     ->where(function($q)use($request){
                                            if($request->session_id){
                                                $q->where('transport_collect_fees.session_id',$request->session_id);
                                                  }
                                        })
                     ->where(function($q)use($from_date,$to_date){
                          if($from_date && $to_date){
                          $q->whereBetween('transport_collect_fees.created_at',[$request->from_date.  " 00:00:00",$request->to_date." 23:59:00"]);
                         }
                    })
                    ->get();
           }
            else
            {
               $list= 'invalid request';
            }
                   
            $data['transport_collection_report']= $list;
        return $this->return_data_in_json($data,$error_msg="");
        }
    
    
    
    
      public function transport_user_history(Request $request)
        {
            $user_type=$request->user_type;
            $route_id=$request->route_id;
            $Vehicle_id=$request->vehicle_id;
            $year_id=$request->year_id;
            if($request->user_type==1){

                $list=DB::table('transport_histories')->select('transport_histories.id','transport_histories.routes_id','transport_histories.vehicles_id','transport_users.member_id','transport_users.user_type','transport_users.stoppage_id','students.first_name as student_name','students.reg_no','routes.title','routes.description','vehicles.number','vehicles.type','vehicles.model',DB::raw("'Student' as user_type"))
                    ->leftJoin('transport_users','transport_users.id','=','transport_histories.travellers_id')
                    ->leftJoin('students','students.id','=','transport_users.member_id')
                    ->leftJoin('routes','routes.id','=','transport_histories.routes_id')
                    ->leftJoin('vehicles','vehicles.id','=','transport_histories.vehicles_id')
                    ->where('transport_users.status','!=',0)
                    ->where('transport_users.user_type',1)
                    ->where(function($q)use($request){
                        if($request->route_id){
                            $q->where('transport_histories.routes_id',$request->route_id);
                              }
                    })
                    ->where(function($q)use($request){
                        if($request->vehicle_id){
                            $q->where('transport_histories.vehicles_id',$request->vehicle_id);
                              }
                    })
                     ->where(function($q)use($request){
                        if($request->year_id){
                            $q->where('transport_histories.years_id',$request->year_id);
                              }
                    })

                    ->get();
            }
           elseif($request->user_type==2){
             $list=DB::table('transport_histories')->select('transport_histories.id','transport_histories.routes_id','transport_histories.vehicles_id','transport_users.member_id','transport_users.user_type','transport_users.stoppage_id','staff.first_name as student_name','staff.reg_no','routes.title','routes.title','routes.description','vehicles.number','vehicles.type','vehicles.model',DB::raw("'Staff' as user_type"))
                   ->leftJoin('transport_users','transport_users.id','=','transport_histories.travellers_id')
                    ->leftJoin('staff','staff.id','=','transport_users.member_id')
                    ->leftJoin('routes','routes.id','=','transport_histories.routes_id')
                    ->leftJoin('vehicles','vehicles.id','=','transport_histories.vehicles_id')
                    ->where('transport_users.status','!=',0)
                    ->where('transport_users.user_type',2)
                    ->where(function($q)use($request){
                        if($request->route_id){
                            $q->where('transport_histories.routes_id',$request->route_id);
                              }
                    })
                    ->where(function($q)use($request){
                        if($request->vehicle_id){
                            $q->where('transport_histories.vehicles_id',$request->vehicle_id);
                              }
                    })
                     ->where(function($q)use($request){
                        if($request->year_id){
                            $q->where('transport_histories.years_id',$request->year_id);
                              }
                    })
                    ->get();
           }
            else
            {
               $list= 'invalid request';
            }
                   
            $data['transport_user_history']= $list;
        return $this->return_data_in_json($data,$error_msg="");
        }
        
        
        
        
          public function get_subject_type(){
        $data =[
        [
            'id'=>0,
            'title'=>'--Select Subject Type--',
        ],    
        [
            'id'=>2,
            'title'=>'Minor Subject',
        ],[
            'id'=>3,
            'title'=>'Vocational Subject',
        ],[
            'id'=>4,
            'title'=>'Co-Curriculum Subject',
        ]];


        return $this->return_data_in_json($data);
    }
  
   
    public function get_subject_by_type(Request $request){

        $data['msg'] = 'Invalid Request';
        $data['error'] = true;
        $data['data'] =[];
        $subject_type = '';

        if($request->subject_type){
            $subject_type=$request->subject_type;
        }
        if($subject_type){
            $subjects = DB::table('subject_master')->select('id','title')
                        ->where([
                            ['is_main_subject','=',$subject_type],
                            ['record_status','=',1],
                        ])->get();
            if(count($subjects)>0){
                $temp_arr[] = [
                        'id' => 0,
                        'title' =>'--Select Subject--'
                    ] ;
                    
                    foreach($subjects as $k => $v){
                        $temp_arr[] = $v;
                    }
                
                $data['data'] =$temp_arr;
                $data['error'] = false;
                $data['msg'] = 'Data Found';

            }else{
                $data['msg'] = 'No Data Found';
            }
        }

        return $this->return_data_in_json($data);
    }



    public function get_group_by_subject(Request $request){

        $data['msg'] = 'Invalid Request';
        $data['error'] = true;
        $data['data'] =[];

        $subject_master_id = '';
        if($request->subject_master_id){
            $subject_master_id = $request->subject_master_id;
        }
        $branch_id = $request->branch_id?$request->branch_id:''; 
        $session_id = $request->session_id?$request->session_id:''; 

        if($subject_master_id && $branch_id && $session_id){
            $groups = DB::table('student_subject_group')->select('id','group_name')
            ->where([
                ['subject_master_id','=',$subject_master_id],
                // ['branch_id','=',$branch_id],
                ['session_id','=',$session_id],
                ['record_status','=',1],

            ])->orderBy('group_name','ASC')
            ->get();

            if(count( $groups)>0){
                
                  $temp_arr[] = [
                        'id' => 0,
                        'group_name' =>'--Select Group--'
                    ] ;
                    
                    foreach($groups as $k => $v){
                        $temp_arr[] = $v;
                    }
                $data['data'] =$temp_arr;
                $data['error'] = false;
                $data['msg'] = 'Data Found';

            }else{
                $data['msg'] = 'No Data Found';
            }
        }

         return $this->return_data_in_json($data);
    }
        
    public function store_group_wise_class(Request $request){ 
        Log::debug('Group wise class');
        Log::debug($request->all());
        Log::debug('Group wise class end');
        $meeting['error'] = 1;
        foreach ($request->all() as $key => $value) {
          $post_data[$key] = $value;
        }
        $api_key = (isset($request->api_key))?$request->api_key : null;
        $secret_key = (isset($request->secret_key))?$request->secret_key : null;
        $client = new \GuzzleHttp\Client();
        /*Get Old Env Api/Secret Key*/
        $response = $client->request('GET', 'http://zoomapi.academicmitra.com/api/get_env');
        $body = $response->getBody()->getContents();
        $keys = json_decode($body);
         /*Change env if $api_key != $keys->api */ 

         $exist = LiveClass::select('*')->where([
            ['topic','=',$post_data['topic']],
            ['start_time','=',$post_data['start_time']],
            // ['duration','=',$post_data['duration']],
            ['class_type','=',2],
            ['subject_group_id','=',$post_data['group_name']],
            ['session_id','=',$post_data['session_id']],
            ['branch_id','=',$post_data['branch_id']],
         ])->first();
        if(!$exist){
            if(!($api_key == $keys->api && $secret_key == $keys->secret)){
              $response = $client->request('POST', 'http://zoomapi.academicmitra.com/api/create_meeting',['query' => $post_data]);
            }
            $response = $client->request('POST', 'http://zoomapi.academicmitra.com/api/create_meeting',['query' => $post_data]);
            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            $data = json_decode($body);
            
            if(isset($data->id)){
            $start_time = Carbon\Carbon::parse($data->start_time)->addMinutes(330);
            $insert = LiveClass::insert([
                'created_at'    => Carbon\Carbon::now(),
                'created_by'    => $request->staff_id,
                'topic'         => $data->topic,
                'start_time'    => $start_time,
                'duration'      => $data->duration,
                'class_type'    => 2,
                'subject_group_id'   => $request->group_name,
                'session_id'    => $request->session_id,
                'branch_id'     => 0,
                'meeting_id'    => $data->id,
                'meeting_password'    => $data->password,
                'join_url'      => $data->join_url,
                'start_url'     => $data->start_url,
                'email'         => $request->email,
                'status'        => 1,
                'host_status'   => 1,
            ]);
            $meeting['error'] = 0;
            $meeting['start_url'] = $data->start_url;
            $meeting['join_url'] = $data->join_url;
            $meeting['meeting_id'] = $data->id;
            $meeting['meeting_password'] = $data->password;
            $meeting['start_time'] = $start_time;
            
            if($insert){
                
                $student_list = DB::table('student_group')->select(DB::raw('GROUP_CONCAT(student_group.student_id) as student_ids'),'sts.branch_id','ssg.group_name','sm.title as subject_name','sds.course_id','sds.Semester as sec_id')
                ->leftJoin('students as sts',function($j){
                    $j->on('sts.id','=','student_group.student_id');
                })
                ->leftJoin('student_detail_sessionwise as sds','sds.student_id','=','student_group.student_id')
                ->leftJoin('student_subject_group as ssg','ssg.id','=','student_group.student_subject_group_id')
                ->leftJoin('subject_master as sm','sm.id','=','ssg.subject_master_id')
                ->where([
                        ['student_group.student_subject_group_id','=',$request->group_name],
                        ['ssg.session_id','=',$request->session_id],
                        ['sds.session_id','=',$request->session_id],
                    ])
                    ->groupBy('sds.course_id')
                    ->get();
                    Log::debug($student_list);
                    if(count($student_list) > 0){
                        foreach($student_list as $k => $v){
                            $student_ids = explode(',',$v->student_ids);
                            $student_tokens = $this->getStudentTokensById($student_ids);
                            
                            if(count($student_tokens)>0){
                                $title = $v->subject_name.'('.$v->group_name.')';
                                $message = 'New Live Class On Topic -'.$data->topic.' is scheduled for '.\Carbon\Carbon::parse($start_time)->format('d-M-Y g:i a');
                                $msg = [
                                    'title' =>$title,
                                    'description' => $message,
                                    'image_url' => '',
                                ];
                                
                                $this->sendFirebaseNotification($student_tokens,$msg);
                                $user_id = 0;
                                $user = DB::table('users')->where('hook_id',$request->staff_id)->where('role_id','!=',6)->select('id')->first();
                                if($user){
                                    $user_id = $user->id;
                                }
                                $insert = [
                                    'user_type' => 1,
                                    'course_id' => $v->course_id,
                                    'section_id' => $v->sec_id,
                                    'student_id' => $v->student_ids,
                                    'session_id' => $request->session_id,
                                    'branch_id' => $v->branch_id,
                                    'title' => $title,
                                    'description' => $message,
                                    'image_url' => '',
                                    'created_by' => $user_id
                                ];
                                
                                
                                $this->storeFirebaseNotification($insert);
                            }
                        }
                    }
               
            }
            }else{
                $meeting['msg'] = 'Something Went Wrong';
            }
        }else{
            $meeting['msg'] = 'Live Class Already Exists With Same Details';
        }
        return $this->return_data_in_json($meeting,$error_msg="");
    }
    
    
    /* IOS APP API */

    public function student_list(Request $request){
        
        $data['msg'] = 'Invalid Request';
        $data['error'] = true;
        $data['data'] =[];

        if($request->course_id && $request->section_id && $request->branch_id && $request->session_id){

            $student = DB::table('students')->select('students.id as student_id','students.first_name as student_name','pd.father_first_name as father_name','pd.mother_first_name as mother_name','university_reg as enroll_no','reg_no',DB::raw("DATE_FORMAT(date_of_birth,'%d-%m-%Y') as date_of_birth"),'gender','fac.faculty as course_name','sem.semester as section','sts.session_id','students.branch_id','sts.course_id','sts.Semester as section_id')
                        ->leftjoin('student_detail_sessionwise as sts','sts.student_id','=','students.id')
                        ->leftjoin('parent_details as pd','pd.students_id','=','students.id')
                        ->leftjoin('faculties as fac','fac.id','=','sts.course_id')
                        ->leftjoin('semesters as sem','sem.id','=','sts.Semester')
                        ->where([
                            ['sts.course_id','=',$request->course_id],
                            ['sts.Semester','=',$request->section_id],
                            ['students.branch_id','=',$request->branch_id],
                            ['sts.session_id','=',$request->session_id],
                            ['students.status','=',1],
                            ['sts.active_status','=',1],
                        ])
                        ->orderBy('sts.course_id','ASC')
                        ->orderBy('students.first_name','ASC')
                        ->get();
            $student_list = DB::table('students')
                        ->leftjoin('student_detail_sessionwise as sts','sts.student_id','=','students.id')
                                            ->where([
                            ['sts.course_id','=',$request->course_id],
                            ['sts.Semester','=',$request->section_id],
                            ['students.branch_id','=',$request->branch_id],
                            ['sts.session_id','=',$request->session_id],
                            ['students.status','=',1],
                            ['sts.active_status','=',1],
                        ])
                        ->count('students.id');
                        
                if(count($student) > 0){
                    $data['data']['student_count'] =$student_list;
                    $data['data']['list'] =$student;
                    $data['msg'] = 'Data Found';
                    $data['error'] = false;
                }else{
                    $data['msg'] = 'No Student Found';
                }    
        }

        return $this->return_data_in_json($data);
    }
    
    public function student_fee_headwise(Request $request){
        $data['msg'] = 'Invalid Request';
        $data['error'] = true;
        $data['data'] =[];

        if($request->course_id && $request->section_id && $request->branch_id && $request->session_id && $request->student_id){

            $fee = DB::table('assign_fee')->select('fh.fee_head_title as fee_head','m.title as month','assign_fee.fee_amount as assigned_fee',DB::raw("SUM(cf.amount_paid) as paid,SUM(cf.discount) as discount"),'m.id as month_id')
                    ->leftjoin('collect_fee as cf',function($j)use($request){
                        $j->on('cf.assign_fee_id','=','assign_fee.id')
                        ->where([
                            ['cf.student_id','=',$request->student_id],
                            ['cf.status','=',1],
                        ]);
                    })
                    ->leftjoin('fee_heads as fh','fh.id','=','assign_fee.fee_head_id')
                    ->leftjoin('months as m','m.id','=','assign_fee.due_month')
                    ->where(function($q)use($request){
                        $q->where('assign_fee.student_id',0)
                        ->orWhere('assign_fee.student_id',$request->student_id);
                    })
                    ->where([
                        ['assign_fee.course_id','=',$request->course_id],
                        ['assign_fee.branch_id','=',$request->branch_id],
                        ['assign_fee.session_id','=',$request->session_id],
                        ['assign_fee.status','=',1],
                    ])
                    ->groupBy('assign_fee.id')
                    ->orderByRaw($this->headOrderByRaw())
                    ->get();
            $fee_data = [];
            
            foreach($fee as $k => $v){
                if(!isset($fee_data[$v->month_id])){
                    $fee_data[$v->month_id] = [];
                }
                $due = $v->assigned_fee - ($v->paid + $v->discount);
                $v->due = $due;
                if($due <= 0){
                    $v->status = "Paid";
                }elseif($due >= $v->assigned_fee){
                    $v->status = "Due";
                }else{
                    $v->status = "Partial";
                }
                
                $fee_data[$v->month_id][] = $v;
            }

            if(count($fee) > 0){
                $data['data']=$fee_data;
                $data['msg'] = 'Data Found';
                $data['error'] = false;
            }else{
                $data['msg'] = 'No Fee Found';
            }    
        }

        return $this->return_data_in_json($data);
    }
    public function headOrderByRaw(){
        $data = "FIELD(assign_fee.due_month, '4','5','6','7','8','9','10','11','12','1','2','3') ASC";
        return $data;
    }
    
    public function student_count_coursewise(Request $request){
        $data['msg'] = 'Invalid Request';
        $data['error'] = true;
        $data['data'] =[];

        if($request->branch_id && $request->session_id){
            $student = DB::table('students')->select('fac.faculty as course_name')
                        ->leftjoin('student_detail_sessionwise as sts','sts.student_id','=','students.id')
                        ->leftjoin('faculties as fac','fac.id','=','sts.course_id')
                        ->leftjoin('semesters as sem','sem.id','=','sts.Semester')
                        ->where([
                            ['students.branch_id','=',$request->branch_id],
                            ['sts.session_id','=',$request->session_id],
                            ['students.status','=',1],
                            ['sts.active_status','=',1],
                        ])
                        ->selectRaw("COUNT(students.id) as count")
                        ->groupBy('sts.course_id')
                        ->orderBy('fac.faculty','ASC')
                        ->get();

            if(count($student) > 0){
                $data['data']=$student;
                $data['msg'] = 'Data Found';
                $data['error'] = false;
            }else{
                $data['msg'] = 'No Data Found';
            }    
        }

        return $this->return_data_in_json($data);
    }
}