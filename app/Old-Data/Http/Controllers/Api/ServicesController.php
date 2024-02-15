<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\CollegeBaseController; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Faculty;
use App\Models\Semester;
use App\Models\Student;
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

    public function asha_login()
    {   
        $data   = [];
        $email  = isset($_GET['email'])? $_GET['email'] : "";
        $pass   = isset($_GET['pass'])? $_GET['pass'] : "";
        if($email != "" && $pass !="")
        {
            $user   = User::where('email',$email)->first();
            $stdId  = trim($user->hook_id);
            $rollId = trim($user->role_id);
            
            $status = Hash::check($pass, $user->password);
            // $rollId = 6 means student
            if($status && $rollId==6){
                // Get User info
                $userType   = 'Student';
                if($stdId != ""){
                    $data= $this->get_dash_board_info($email="",$stdId,$userType,$rollId);
                }else{
                    $data= $this->get_dash_board_info($email,$stdId="",$userType,$rollId);
                }
            }else if($status && $rollId!=6){
                // Staff profile details
                $userType   = 'Staff';
                $data= $this->get_Staff_Details($stdId,$userType,$rollId);
            }
        }

        return $data;
    }

    public function get_dash_board_info($emailId="",$uIds ="",$userType="",$rollId="")
    {   
        // Get data by Email or Id
        $data = [];
        $data['userType']   = $userType;
        $data['rollId']     = $rollId;
        $emailGet=(isset($_GET['email']) && $_GET['email']!="")? $_GET['email'] : $emailId;
        
        $email = ($emailId != "")? $emailId : $emailGet;
        $uIds=(isset($_GET['uid']) && $_GET['uid']!="")? $_GET['uid'] : $uIds;

        $fileLink = asset('images'.DIRECTORY_SEPARATOR.'studentProfile');
        $fileLink = addslashes($fileLink).'/';

        if($uIds!="")
        {            
            //$data   = Student::where('id',$uIds)->first(); 
            $data['info']   = Student::select('students.id','students.created_at','reg_no','reg_date','faculty','semester','academic_status','first_name','date_of_birth','gender','blood_group','nationality','mother_tongue','email','extra_info',DB::raw("CONCAT('$fileLink',student_image) as student_image"),'students.status','branch_id','students.org_id','category_id','session_id','zip','indose_number','passport_no','Merchant_Key as passkey','ai.mobile_1')
                ->leftJoin('branches','students.branch_id', '=', 'branches.id')
                ->leftjoin('addressinfos as ai', 'students.id', '=', 'ai.students_id')
                ->where('students.id',$uIds)->first(); 
        } 
        else
        { 
            $data['info']   = Student::select('students.id','students.created_at','reg_no','reg_date','faculty','semester','academic_status','first_name','date_of_birth','gender','blood_group','nationality','mother_tongue','email','extra_info',DB::raw("CONCAT('$fileLink',student_image) as student_image"),'students.status','branch_id','students.org_id','category_id','session_id','zip','indose_number','passport_no','Merchant_Key as passkey','ai.mobile_1')

                ->leftJoin('branches','students.branch_id', '=', 'branches.id')
                ->leftjoin('addressinfos as ai', 'students.id', '=', 'ai.students_id')
                ->where('email',$email)->first(); 
        }   

        return $this->return_data_in_json($data,$error_msg="");   
    }

    public function get_student_profile($stdId="")
    {
        $data = [];
        $uIds=(isset($_GET['uid']) && $_GET['uid']!="")? $_GET['uid'] : $stdId;
        
        if($uIds==""){
            return $data;
        }

        $fileLink = asset('images'.DIRECTORY_SEPARATOR.'studentProfile');
        $fileLink = addslashes($fileLink).'/';
        $data['student'] = StudentPromotion::select('students.id','students.reg_no', 'students.reg_date', 
            'students.branch_id','branches.branch_name',
            'students.category_id','category.category_name',
            'students.session_id','session.session_name',
            'student_detail_sessionwise.course_id','faculties.faculty as course_name','student_detail_sessionwise.semester','semesters.semester as section_semester', 
            'students.academic_status', 'students.first_name', 'students.date_of_birth', 'students.gender', 'students.blood_group', 'students.nationality',
            'students.mother_tongue', 'students.email', 'students.extra_info', DB::raw("CONCAT('$fileLink',students.student_image) as student_image"), 'students.status','pd.father_first_name', 'pd.father_middle_name',
            'pd.father_last_name', 'pd.father_eligibility', 'pd.father_occupation', 'pd.father_mobile_1', 'pd.father_email', 'pd.mother_first_name','pd.mother_middle_name', 'pd.mother_last_name', 'pd.mother_eligibility', 'pd.mother_occupation','pd.mother_mobile_1', 'pd.mother_email',
            'ai.address', 'ai.state', 'ai.country','ai.home_phone',
            'ai.mobile_1', 'ai.mobile_2', 'gd.id as guardian_id', 'gd.guardian_email','gd.guardian_first_name', 'gd.guardian_middle_name', 'gd.guardian_last_name',
            'gd.guardian_eligibility', 'gd.guardian_occupation',
            'gd.guardian_mobile_1', 'gd.guardian_mobile_2', 'gd.guardian_email', 'gd.guardian_relation', 'gd.guardian_address')

            //->where('student_detail_sessionwise.session_id', $current_session_id)
            ->where('students.id','=',$uIds)
            ->leftJoin('students','student_detail_sessionwise.student_id', '=', 'students.id')
            ->join('parent_details as pd', 'pd.students_id', '=', 'students.id')
            ->join('addressinfos as ai', 'ai.students_id', '=', 'students.id')
            ->join('student_guardians as sg', 'sg.students_id','=','students.id')
            ->join('guardian_details as gd', 'gd.id', '=', 'sg.guardians_id')
            ->leftJoin('faculties','student_detail_sessionwise.course_id', '=', 'faculties.id')
            ->leftJoin('semesters','student_detail_sessionwise.semester', '=', 'semesters.id')
            ->leftJoin('session','student_detail_sessionwise.session_id', '=', 'session.id')
            ->leftJoin('category','students.category_id', '=', 'category.id')
            ->leftJoin('branches','students.branch_id', '=', 'branches.id')
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

    public function get_student_fee_head_list($stdId="",$ssId="",$courseId="")
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
        //->where('assign_fee.branch_id', $branch)
        ->where('assign_fee.session_id', $ssId)
        ->where('assign_fee.course_id', $courseId)
        ->Where('assign_fee.student_id', '0')
        ->orWhere(function($q) use ($uIds,$ssId,$courseId){
            if($uIds){
                $q->orWhere('assign_fee.student_id', $uIds)
                //->where('assign_fee.branch_id', $req->branch)
                ->where('assign_fee.session_id', $ssId)
                ->where('assign_fee.course_id', $courseId);
            }
        })->groupBy('assign_fee.id')->get();

        $ret_val=""; $i=0; 

        $arrFeeMaster = [];
        if(count($fee_result) && $uIds){$k=0;
            foreach($fee_result as $fee){ $k++;
                $paid_result=DB::table('collect_fee')->where('assign_fee_id', $fee->id)->where('student_id', $uIds)
                ->Where('status' , 1)->sum('amount_paid');
                $due = $fee->fee_amount - $paid_result;
                $disabled = ($due == 0) ? " style=\"display:none;\"":"";
                $arrFeeMaster[$k]['assignId'] = $fee->id;
                $arrFeeMaster[$k]['Fee Head'] = $fee->fee_head_title;
                $arrFeeMaster[$k]['fee_head_id'] = $fee->fee_head_id;
                $arrFeeMaster[$k]['Fees'] = $fee->times;
                $arrFeeMaster[$k]['amount'] = $fee->fee_amount;
                $arrFeeMaster[$k]['paid'] = $paid_result;
                $arrFeeMaster[$k]['due'] = $due;
            }
        }
        return $this->return_data_in_json($arrFeeMaster,$error_msg="");
    }

    public function get_student_fee_receipt_list($stdId="",$ssId="",$courseId="")
    {
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
        ->join('students as sd', 'sd.id', '=', 'collect_fee.student_id')
        ->join('assign_fee as asf', 'asf.id', '=', 'collect_fee.assign_fee_id')
        ->join('fee_heads as fd', 'fd.id', '=', 'asf.fee_head_id')
        ->join('branches as br', 'br.id', '=', 'sd.branch_id')
        ->join('faculties as fac', 'asf.course_id', '=', 'fac.id')
        ->get();
        $data_val="";$i=0;

        $arrFeeMaster = [];
        $k=0;
        foreach ($feeHistory_data as $feeHistory) { $k++;
            $collDate = date("d-m-Y",strtotime($feeHistory->reciept_date));
            $arrFeeMaster[$k]['reciept_no']     = $feeHistory->reciept_no;
            $arrFeeMaster[$k]['fee_head_title'] = $feeHistory->fee_head_title;
            $arrFeeMaster[$k]['amount_paid']    = $feeHistory->amount_paid;
            $arrFeeMaster[$k]['payment_mode']   = $feeHistory->payment_type;
            $arrFeeMaster[$k]['payment_date']       = $collDate; 
            $PRINT_URL =  route('feeReceipt', ['receipt_no' => $feeHistory->reciept_no]);
            $receipt_print_url  = ($feeHistory->status== 1)? $PRINT_URL : "";

            //$arrFeeMaster[$k]['PRINT_URL'] = $receipt_print_url;
            $arrFeeMaster[$k]['status'] = ($feeHistory->status==1)? 'PAID' : 'Failed';
        }

        return $this->return_data_in_json($arrFeeMaster,$error_msg="");
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

    public function feebycourseapi($cid)
    {
        $assign_list=Fee_model::select('assign_fee.*', 'fee_heads.fee_head_title', 'session.session_name')
        ->leftJoin('fee_heads', function($join){
                $join->on('fee_heads.id', '=', 'assign_fee.fee_head_id');
        })
        ->leftJoin('session', function($join){
            $join->on('session.id', '=', 'assign_fee.session_id');
        }) 
        ->where('course_id' , $cid)
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

        $fee_result=DB::table('branches')->Select('branch_name','branch_title','branch_address','branch_email','branch_mobile',DB::raw("CONCAT('$fileLink',branch_logo) as branch_logo"))
        ->join('students as sd', 'sd.branch_id', '=', 'branches.id')
        ->Where('sd.id', $uIds)
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
        Log::debug($reqData);
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
        Log::debug($reqData);
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
            $randomString   = $this->reciept_no();
            $resArray['receiptNumber']  = $randomString;
            if ($data['status']=='Failed' || $data['status']=='failure' || $resArray['paymentStatus'] == 'Failed') 
            {
                $paymentdata       = DB::table('collect_fee')->insert([
                    'created_by'    => $user_id,
                    'reciept_no'    => $randomString,
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
                $paymentdata =DB::table('collect_fee')->insert([
                    'created_by'    => $user_id,
                    'reciept_date'  => $date,
                    'reciept_no'    => $randomString,
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
    

        $data['info']=DB::table('staff')->select('staff.id as staff_id','staff.reg_no','staff.join_date','staff.designation','staff.first_name','staff.middle_name','staff.last_name','staff.date_of_birth','staff.gender','staff.nationality','staff.address','staff.blood_group','staff.mother_tongue','staff.address','staff.state','staff.country','staff.temp_state','staff.temp_country','staff.home_phone','staff.mobile_1','staff.email','staff.qualification','staff.experience','staff.experience_info','staff_designations.title as designation','branches.branch_title',DB::raw("CONCAT('$fileLink',staff.staff_image) as staff_image"))
        ->where([
            ['staff.id','=',$staffId] 
        ]) 
        ->join('staff_designations','staff.designation','=','staff_designations.id')
        ->leftjoin('branches','branches.id','=','staff.branch_id')
        ->first();
        return $this->return_data_in_json($data,$error_msg="");
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
             ->get();
            foreach ($years as $key => $value) {
                $data['years'][$value->id]=$value->title;
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
        $staff_id   = $request->staff_id;
        $Session    = $request->Session;
        $Branch     = $request->Branch;
        $Course     = $request->Course; 
        $Section    = $request->Section;

       $stdAtnd    = $request->Student_attendance_list; 

    $atdArr = json_decode($stdAtnd);
        $date=explode('/', $date);
        $da=$date[0]; 
        $day="day_".$da;   
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
    public function get_assignment_list(Request $request){ 
        $data=[]; 
        $faculty=(isset($request->facultyId))?$request->facultyId:null;
        $session=(isset($request->sessionId))?$request->sessionId:null;
        $branchId=(isset($request->branchId))?$request->branchId:null;
        if($faculty==null || $session==null || $branchId==null){
        
            return $data;
        }
        $fileLink = asset('assignments'.DIRECTORY_SEPARATOR.'questions');
      
        $data['assignment']=DB::table('assignments')->select('assignments.id as assignments_id','assignments.created_by as staff_id',DB::raw("CONCAT(staff.first_name,' ',staff.last_name)as staff_name"),'faculties.faculty','assignments.session_id','session.session_name as session','assignments.semesters_id','semesters.semester','assignments.subjects_id','subjects.title as subject_name','assignments.publish_date','assignments.end_date','assignments.title','assignments.description',DB::raw("CONCAT('$fileLink',file) as assignment")) 
            ->Where([
                ['assignments.faculty','=',$faculty],
                ['session_id','=',$session],
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
    public function add_assignment(Request $request){
        $staffId=(isset($request->staffId))?$request->staffId:null;
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
    public function get_transport_details($memId="",$memType=""){
            $data=[];
            if($memId==""){
                return $data;
            }
            else{
                    if($memType==1){
                         $data['transport']=DB::table('transport_users')->select('students.first_name','vehicles.number as vehicles_number','vehicles.type as vehical_type','routes.title as route','routes.rent as rent','from_date','to_date','transport_users.status')
                         ->where([
                            ['transport_users.member_id','=',$memId],
                            ['transport_users.user_type','=',1]
                         ])
                         ->leftjoin('students','students.id','=','transport_users.member_id')
                         ->leftjoin('routes','routes.id','=','transport_users.routes_id')
                         ->leftjoin('vehicles','vehicles.id','=','transport_users.vehicles_id')
                         ->orderBy('transport_users.created_at','Desc')
                         ->get();
                    }
                    if($memType==2){
                         $data['transport']=DB::table('transport_users')->select('staff.first_name','staff.middle_name','staff.last_name','vehicles.number','vehicles.type as vehicles_type','routes.title as route','routes.rent as rent','from_date','to_date','transport_users.status')
                         ->where([
                            ['transport_users.member_id','=',$memId],
                            ['transport_users.user_type','=',2]
                         ])
                         ->leftjoin('staff','staff.id','=','transport_users.member_id')
                         ->leftjoin('routes','routes.id','=','transport_users.routes_id')
                         ->leftjoin('vehicles','vehicles.id','=','transport_users.vehicles_id')
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
            $data['answerList']=DB::table('assignment_answers')->select('assignment_answers.*','st.first_name','st.reg_no')
            ->leftjoin('students as st','st.id','=','assignment_answers.students_id')
            ->where(function($query) use ($request){
                if(!empty($request->StudentId)){
                    $query->where('students_id','=',$request->StudentId);
                    $this->filter_query['students_id'] = $request->StudentId;
                }
                $query->where('assignments_id','=',$request->assignmentId);
                $this->filter_query['id']=$request->assignmentId;   
                })
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
        $branch_list=DB::table('branches')->select('id','branch_title')
         ->get();
         $i=0;
        foreach ($branch_list as $key => $value) {
            $data[$i]['id']=$value->id;
            $data[$i]['branch_title']=$value->branch_title;
            $i++;
         }
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
        return $this->return_data_in_json($data,$error_msg="");
   }
   public function get_course_branch($branch_id){
        $cources=DB::table('faculties')->select('id','faculty as course_name')->where([
            ['status','=',1],
            ['branch_id','=',$branch_id]
        ])
        ->orderBy('course_name','asc')
        ->get();
        $k =0;
        foreach ($cources as $val) {
            $data['course'][$k]['id']=$val->id;
            $data['course'][$k]['course_name']=$val->course_name;
            $k++;
         }

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
                $data['subject']=DB::table('timetable_assign_subject')->select('sbj.id','sbj.title as subject')
                ->leftjoin('timetable_subjects as sbj','sbj.id','=','timetable_assign_subject.timetable_subject_id')
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
            $schedule=DB::table('timetable')->select('timetable.id','day_id',DB::raw("date_format(time_from,'%H:%i') as time_from "),DB::raw("date_format(time_to,'%H:%i') as time_to "),'sbj.title','timetable.staff_id',DB::raw("CONCAT(st.first_name,' ',st.last_name) as staff"),'room_no','subject_type')
            ->leftjoin('staff as st','st.id','=','timetable.staff_id')
            ->leftjoin('timetable_subjects as sbj','sbj.id','=','timetable.timetable_subject_id')
            // ,DB::raw("CONCAT(altstf.first_name,' ',altstf.last_name) as altTeacher")
            // ->leftjoin('timetable_alt_teacher as alt','alt.timetable_id','=','timetable.id')
            // ->leftjoin('staff as altstf','altstf.id','=','alt.staff_id')
            
            ->where([
                ['sbj.status','=',1],
                ['timetable.course_id','=',$request->course],
                ['timetable.section_id','=',$request->section],
                // ['alt.dates','=',$currentDate]
            ])
            ->orderBy('time_from','ASC')
            ->get();
            Log::debug($schedule);
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
              ->distinct('semesters.id')
              ->leftjoin('faculty_semester','faculty_semester.faculty_id','=','faculties.id')
              ->leftjoin('semesters','semesters.id','=','faculty_semester.semester_id')
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
           Log::debug($data['atten']);
            return response()->json(json_encode($data['atten']));
        }
    }
}