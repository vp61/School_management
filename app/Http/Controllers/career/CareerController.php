<?php

namespace App\Http\Controllers\career;

use Illuminate\Http\Request;
use App\Http\Controllers\CollegeBaseController;
use App\Branch;
use DB,Log,Session,File;
use Carbon\Carbon;
use Tzsk\Payu\Facade\Payment;
use App\Models\GeneralSetting;
use App\Models\Student;
use App\Models\career;
use Illuminate\Support\Facades\Crypt;
use Intervention\Image\ImageManagerStatic as Image;
use App\Models\CareerStatus;
use App\Models\CareerFollowup;


class CareerController extends CollegeBaseController
{
    protected $base_route = 'career';
    protected $view_path = 'career';
    protected $panel = ' career Form';
    protected $filter_query = [];


    public function __construct()
    {
        
    }
    public function index(Request $request){

             $data['branch']= DB::table('branches')->select('*')->where('id',1)->where('record_status',1)->first();
             $data['image']=asset('assets/images/marine_bg/marine_college_bg.jpg');
            if($request->has('type')){
                if(empty($request->type)){
                    return redirect()->back()->with('message_warning','Invalid Request! Please Select Type.');
                }
                $data['qualification']=['Graduation'=>'Graduation','Post Graduation'=>'Post Graduation','10th'=>'10th','12th'=>'12th'];

                $data['post']= [''=>'Select Post Applied For','Admission Counsellor'=>'Admission Counsellor','Accounting Manager'=>'Accounting Manager','Computer Operator'=>'Computer Operator','Typist'=>'Typist','Back Office'=>'Back Office','Front Office Executive'=>'Front Office Executive','Care Taker'=>'Care Taker','Transport-in-charge'=>'Transport-in-charge','Personal Assistant'=>'Personal Assistant','PRO'=>'PRO','House Keeping'=>'House Keeping','Store Incharge'=>'Store Incharge','Lab Incharge'=>'Lab Incharge'];
                $data['join']= [''=>'Select when you join','15 days'=>'15 days','30 days'=>'30 days','45 days'=>'45 days'];
                
                $data['type'] =Crypt::encrypt($request->type);
                $year_list = range(2000,2021);
                foreach($year_list as $k => $v){
                    $data['year_list'][$v]= $v;
                }
                $data['year_list'] = array_prepend($data['year_list'],'--Select Year--','');
                if($request->type == 1){

                 return view($this->view_path.'.index',compact('data'));   
             }elseif($request->type == 2){
                return view($this->view_path.'.non-teaching',compact('data'));   
             }else{
                return redirect()->back()->with('message_warning','Invalid Request! Please Select Type.');
             }
                
            }else{
                return view($this->view_path.'.home',compact('data'));  
            }

          
        
    }
    public function store(Request $request){
        try{
            $type = Crypt::decrypt($request->type);
        }catch(\Throwable $e){
            return redirect()->back()->with('message_warning','Incorrect Job Type. Please Try Again.');
        }
          $date= Carbon::now();
        
          // dd($request->all());
        if($type == 1){
            $rules=[
                'candidate_name'=>'required',
                'father_name'=>'required',
                'email'=>'required|email',   
                'gender'=>'required',
                'dob'=>'required',
                'mobile'=>'required|numeric',
                // 'aadhar_no'=>'required|numeric',   
                // 'indose_number'=>'required|numeric',   
                'per_add'=>'required',   
                // 'post_applied_for'=>'required',   
                'current_salary'=>'required|numeric',   
                //'expected_salary'=>'required|numeric',   
                'leaving_reason'=>'required',   
                'join_day'=>'required',    
                'mother_teacher'=>'required',    
                'experience'=>'required',    
                'ntt'=>'required',    
                'graduation'=>'required',    
                'graduation_subject'=>'required',    
                'graduation_percentage'=>'required',    
                '12_stream'=>'required',    
                '12_percentage'=>'required',    
                '10_percentage'=>'required',    
                'board'=>'required',    
                'year_of_experience'=>'required',    
                'pesent_organization'=>'required',    
                'classes_presently_teaching'=>'required',    
                'languages_known'=>'required',    
            ];

          
            
            $msg=[
                'candidate_name.required'=>'Please Enter Candidate Name',
                'father_name.required'=>'Please Enter Father/Husband Name',
                'email.required'=>'Please Enter Email',   
                'gender.required'=>'Please Select Gender ',
                'dob.required'=>'Please Enter Date of birth',
                'mobile.required'=>'Please Enter Mobile Number',
               
                'per_add.required'=>'Please Enter Address',   
                'post_applied_for.required'=>'Please Enter Post Apply For',   
                'current_salary.required'=>'Please Enter Current salary',   
                //'expected_salary.required'=>'Please Enter Expected salary',   
                'leaving_reason.required'=>'Please  Enter Reason For leaving ',   
                'join_day.required'=>'Please Select Join day',    
                'mother_teacher.required'=>'Please Select Mother Teacher',    
                'experience.required'=>'Please Select Experience',    
                'ntt.required'=>'Please Select NTT',    
                'graduation.required'=>'Please Select Graduation',    
                'graduation_subject.required'=>'Please Enter Graduation Subjects',
                'graduation_percentage.required'=>'Please Enter Graduation Percentage',
                '12_stream.required'=>'Please Enter Class 12th Stream',
                '12_percentage.required'=>'Please Enter Class 12th Percentage',
                '10_percentage.required'=>'Please Enter Class 10th Percentage',
                'board.required'=>'Please Enter Board',
                'year_of_experience.required'=>'Please Enter Years of Teaching Experience',
                'pesent_organization.required'=>'Please Enter Name of Present Organization and Place',
                'classes_presently_teaching.required'=>'Please Enter Which Subjects and Classes Presently Teaching',
                'languages_known.required'=>'Please Select Languages Known',
                
            ];
        }elseif($type == 2){
            $rules=[
                'candidate_name'=>'required',
                'father_name'=>'required',
                'email'=>'required|email',   
                'gender'=>'required',
                'dob'=>'required',
                'mobile'=>'required|numeric',
                // 'aadhar_no'=>'required|numeric',   
                // 'indose_number'=>'required|numeric',   
                'per_add'=>'required',   
                'post_applied_for'=>'required',   
                'qualification'=>'required',   
                'current_salary'=>'required|numeric',   
                   
                'leaving_reason'=>'required',   
                'join_day'=>'required',    
                'board'=>'required',    
                'year_of_experience'=>'required',    
                'pesent_organization'=>'required',    
                'pesent_organization'=>'required',    
                'current_salary'=>'required',    
                'languages_known'=>'required',    
                'leaving_reason'=>'required',    
                // 'post_applied_for'=>'required',    
            ];

          
            
            $msg=[
                'candidate_name.required'=>'Please Enter Candidate Name',
                'father_name.required'=>'Please Enter Father/Husband Name',
                'email.required'=>'Please Enter Email',   
                'gender.required'=>'Please Select Gender ',
                'dob.required'=>'Please Enter Date of birth',
                'mobile.required'=>'Please Enter Mobile Number',
               
                'per_add.required'=>'Please Enter Address',   
                'post_applied_for.required'=>'Please Select Post Apply For',   
                'qualification.required'=>'Please Select Qualification',   
                'current_salary.required'=>'Please Enter Current salary',   
                
                'leaving_reason.required'=>'Please  Enter Reason For leaving ',   
                'join_day.required'=>'Please Select Join day',    
                'board.required'=>'Please Enter Board',    
                'year_of_experience.required'=>'Please Enter Working Experience',    
                'pesent_organization.required'=>'Please Enter Name of Present Organization and Place',    
                'current_salary.required'=>'Please Enter Current Salary',    
                'languages_known.required'=>'Please Select Languages Known',   
                'leaving_reason.required'=>'Please Enter Leaving Reason',    
                
            ];
        }else{
            return redirect()->back()->with('message_warning','Incorrect Job Type. Please Try Again.');
        }

        
          $this->validate($request,$rules,$msg);
          $request->request->remove('_token');
          $request->request->add(['created_at' => $date]); 
          $request->request->add(['type' => $type]); 
         if($request->qualification){
            $qualification= implode(',',$request->qualification);
            $request->request->add(['qualification' => $qualification]);
         }
         if($request->languages_known){
            $languages_known= implode(',',$request->languages_known);
            $request->request->add(['languages_known' => $languages_known]);
         }
        career::create($request->all());
        $request->session()->flash($this->message_success,'Form Submitted Successfully.We will contact you shortly.');
        return redirect()->route($this->base_route);
        
       
       
    }
    
    public function list(Request $request){

        // dd($request->all());
        $status=CareerStatus::select('id','title')->pluck('title','id')->toarray();
        $list = career::select('careers.*','career_status.title as careerstatus')
          ->leftjoin('career_status','career_status.id','=','careers.status')
       
        ->where(function($q)use($request){
            if($request->has('from') && $request->has('to')){
                if($request->from && $request->to){
                    $q->whereBetween('careers.created_at',[$request->from.' 00:00:00',$request->to.' 23:59:59']);
                }
            }else{
                $crr_date = Carbon::now()->format('Y-m-d');
                $q->whereBetween('careers.created_at',[$crr_date.' 00:00:00',$crr_date.' 23:59:59']);
            }
            if($request->type){
                $q->where('careers.type',$request->type);
            }
             /*subject change*/
            if($request->subject){
                $q->where('careers.prt',$request->subject); 
                $q->orWhere('careers.tgt',$request->subject); 
                $q->orWhere('careers.pgt',$request->subject); 
            }
            /*subject change*/
        })
          ->where('careers.record_status',1)
        ->get();
     
        
        
        return view('career.list',compact('list','status'));
    }
    
    
    
    
    
    public function online_admission_session(){
        $val=GeneralSetting::select('online_admission_session')->first();
        return $val->online_admission_session;
    }
    public function min_pay_amount(){
        $min_pay_amount = DB::table('general_settings')->select('online_admission_min_pay')->first();
        if($min_pay_amount->online_admission_min_pay > 0){
            return $min_pay_amount->online_admission_min_pay;
        }else{
            return 1;
        }
    }
    public static function get_course_batch_details($val){
        $data=explode(',',$val);
        // dd($data);
        $selected_course['name']=DB::table('faculties')->select('faculty','id as faculty_id')->where('id',$data[0])->first();
        $selected_course['batch']=DB::table('course_batches')->select('title as course_batch',DB::raw("CONCAT(title,' (',start_date,' to ',end_date,')') as course_batch"),'id as batch_id')->where('id',$data[1])->first();
        $selected_course['fee'] = $data[2];
        $selected_course['isCourseBatch']=$data[3];
        // $selected_course['assign_fee_id']=$data[4];
        return $selected_course;
    }
    function OnlinePayment(Request $request){
        $login=[];
        /*Static use of min pay amount */
        if($request->total_fee < 50000){
            $min_pay_amount = 50;
        }else{
            $min_pay_amount = 10;
        }
        /*end*/

        /*Min PAYMENT Amount Form Database */
        // $min_pay_amount = $this->min_pay_amount();
        /* end */
       if($request->amount < round(($min_pay_amount/100)*$request->total_fee)){
         $student_inserted_data=Session::get('student_inserted_data');
            foreach ($student_inserted_data as $key => $value) {
                Student::where('id',$value->id)->delete();
                if(File::exists(public_path().$value->high_school_image)){
                   File::delete(public_path().$value->high_school_image);
                }
                if(File::exists(public_path().$value->intermediate_image)){
                    File::delete(public_path().$value->intermediate_image);
                }
                if(File::exists(public_path().$value->aadhaar_image)){
                    File::delete(public_path().$value->aadhaar_image);
                }
                 if(File::exists(public_path().$value->pass_port_image)){
                    File::delete(public_path().$value->pass_port_image);
                }
                DB::table('addressinfos')->where('students_id',$value->id)->delete();
                DB::table('parent_details')->where('students_id',$value->id)->delete();
                DB::table('academic_infos')->where('students_id',$value->id)->delete();
                DB::table('student_detail_sessionwise')->where('student_id',$value->id)->delete();
            }
        return redirect()->back()->with('message_warning','Total Amount must be greater than or equal to the '.$min_pay_amount.' % of full payable fee');
       }
        $courses=$request['course'];
        $org_id = 1;
            $attributes = [
            // 'key'=>  $request['key'],      
            // 'hash'=>  $request['hash'],      
            'txnid' => $request['txnid'], # Transaction ID.
            'amount' => $request['amount'], # Amount to be charged.
            'productinfo' =>$request['productinfo'],
            'firstname' => $request['firstname'], # Payee Name.
            'email' => $request['email'], # Payee Email Address.
            'phone' => $request['phone'], # Payee Phone Number.
            'feehead' => $request['feehead'], # Payee Phone Number.
            'branch_id' => $request['branch_id'], # Payee Phone Number.
            'feeamount' => $request['feeamount'], # Payee Phone Number.
        ];
        $payupayment = DB::table('online_admission_payments')->insert([
                "account"=>'payu',
                'txnid' =>$request['txnid'], 
                'mihpayid' =>$request['txnid'], 
                'firstname' =>$request['firstname'], 
                'email' =>$request['email'],  
                'phone' =>$request['phone'], 
                'amount' =>$request['amount'], 
                'data' =>$request['productinfo'], 
                'status' =>$request['unmappedstatus'], 
                'unmappedstatus' =>$request['unmappedstatus'], 
                'feehead' => $request['feehead'], # Payee Phone Number.
                'feeamount' => $request['total_fee'],
                 // 'fee_masters_id' => $request['fee_masters_id'],
                // 'student_id' => $request['student_id'],
                 'branch_id' => $request['branch_id'],
                 'org_id' => $org_id,     
        ]);
        return Payment::make($attributes, function ($then) {
            $then->redirectTo('OnlineAdmission/PaymentStatus');
            # OR...
            $then->redirectRoute('OnlineAdmission.PaymentStatus');
            # OR...
            $then->redirectAction('OnlineAdmission\OnlineAdmissionController@status');
        });
    }
    public function status(){
        $login=[];
      $payment=  Payment::capture();
      $return_data=json_decode($payment->data);
      
         $paySessData = $branch_id =Session::get('tzsk_payu_data.payment'); 
        // Get the payment status.
        $payment->isCaptured();

        if(Session::get('refreshStatus') == 1){
            return redirect()->route('OnlineAdmission');
        }

        // chECK FOR EXISTING RECORDS FOR SAME TXNiD AND mihpayid
        $recInfo = DB::table('online_admission_payments')
        ->where(function($q) use ($payment){
            if($payment->txnid){
                $q->orWhere('txnid', $payment->txnid)
                ->orWhere('mihpayid', $payment->mihpayid);
            }
        })->get();
        
        $countStatus = $recInfo->count();
        
        $paymentStatus = ($paySessData['checksumStatus'] == "Tampered")? 'Failed' : $payment->status;
        $temStatus = ($paySessData['checksumStatus'] == "Tampered")? 'Tampered / Failure' : $payment->status;

        //dd($temStatus);
           
        DB::table('online_admission_payments')->where('txnid', $payment->txnid)
        ->update([
            "account"=>$payment->account,
            'payable_type' =>$payment->payable_type, 
            'txnid' =>$payment->txnid, 
            'firstname' =>$payment->firstname, 
            'email' =>$payment->email, 
            'phone' =>$payment->phone, 
            'amount' =>$payment->amount, 
            'discount' =>$payment->discount, 
            'net_amount_debit' =>$payment->net_amount_debit, 
            'data' =>$payment->data, 
            'status' =>$paymentStatus, 
            'unmappedstatus' =>$payment->unmappedstatus, 
            'mode' =>$payment->mode, 
            'bank_ref_num' =>$payment->bank_ref_num, 
            'mihpayid' =>$payment->mihpayid, 
            'updated_status' =>$temStatus,
            'cardnum' =>$payment->cardnum
        ]);
        // dd($payment,$return_data);
        $feeheads=DB::table('online_admission_payments')->select('feehead', 'feeamount','student_id','fee_masters_id','id')->where('txnid' ,$payment->txnid)->Get();
        // $user_id = Auth::user()->id;
        $date =  Carbon::now();
        $data['image']=asset('assets/images/marine_bg/marine_college_bg.jpg');
        // $randomString = $this->reciept_no();
        $student_inserted_data=Session::get('student_inserted_data');
        // dd($student_inserted_data);
        if ($payment->status=='Failed') 
         {
            foreach ($student_inserted_data as $key => $value) {
                Student::where('id',$value->id)->delete();
                if(File::exists(public_path().$value->high_school_image)){
                   File::delete(public_path().$value->high_school_image);
                }
                if(File::exists(public_path().$value->intermediate_image)){
                    File::delete(public_path().$value->intermediate_image);
                }
                if(File::exists(public_path().$value->aadhaar_image)){
                    File::delete(public_path().$value->aadhaar_image);
                }
                 if(File::exists(public_path().$value->pass_port_image)){
                    File::delete(public_path().$value->pass_port_image);
                }
                DB::table('addressinfos')->where('students_id',$value->id)->delete();
                DB::table('parent_details')->where('students_id',$value->id)->delete();
                DB::table('academic_infos')->where('students_id',$value->id)->delete();
                DB::table('student_detail_sessionwise')->where('student_id',$value->id)->delete();
            }
        }
        elseif ($payment->status=='Completed' && $paySessData['checksumStatus'] != "Tampered"){
         
            $received=$total_fee_paid=$payment->net_amount_debit;
            foreach ($student_inserted_data as $key => $value) {
               Student::where('id',$value->id)->update([
                'status'=>1
               ]);
                DB::table('addressinfos')->where('students_id',$value->id)->update([
                'status'=>1
               ]);
                DB::table('parent_details')->where('students_id',$value->id)->update([
                'status'=>1
               ]);
                 DB::table('academic_infos')->where('students_id',$value->id)->update([
                'status'=>1
               ]);
                DB::table('student_detail_sessionwise')->where('student_id',$value->id)->update([
                'active_status'=>1
               ]);
                $isCourseBatch = Session::get('isCourseBatch');
                $assigned_fee=DB::table('assign_fee')->select('fee_amount','id')->where([
                    ['status','=',1],
                    ['course_id','=',$value->faculty],
                    ['branch_id','=',$value->branch_id],
                    ['session_id','=',$value->session_id]
                ])->where(function($j)use($isCourseBatch,$value){
                    if($isCourseBatch){
                        $j->where('batch_id',$value->batch_id);
                    }
                })
                ->orderBy('fee_head_id','asc')
                ->get();
                foreach ($assigned_fee as $k => $assigned) {
                    if($received>0){
                        $collect=($received-$assigned->fee_amount)>0?$assigned->fee_amount:$received;
                        $receipts[]=DB::table('collect_fee')->insertGetId([
                            'assign_fee_id'=>$assigned->id,
                            'amount_paid'=>$collect,
                            'reciept_date'=>Carbon::now(),
                            'student_id'=>$value->id,
                            'payment_type'=>$payment->mode,
                            'reference'=>'Online Admission'
                        ]);
                        $received=$received-$collect;
                    }    
                }
                $login_email=str_replace(' ','_',$value->first_name).$value->id.'@asha.ac.in';
                $pass=rand(10000,999999);
                $batch = isset($value->batch_id) ? $value->batch_id :'';
                $course=$this->get_course_batch_details($value->faculty.','.$batch.','.'123'.','.Session::get('isCourseBatch'));
                $course_name=$course['name']->faculty;
                $batch_name = isset($course['batch']->course_batch) ? $course['batch']->course_batch :'-';
                $batch = $batch_name;
                $login_details['email']= $login_email;
                $login_details['password']= $pass;
                $login_details['course']= $course_name;
                $login_details['batch']= $batch;
                $login[]=$login_details;
                $user_id=DB::table('users')->insertGetId([
                    'name'=>$value->first_name,
                    'email'=>$login_email,
                    'password'=>bcrypt($pass),
                    'role_id'=>6,
                    'hook_id'=>$value->id,
                    'status'=>1,
                    'org_id'=>1,
                    'branch_id'=>$value->branch_id,
                    'created_at'=>Carbon::now(),
                ]);
                DB::table('role_user')->insert([
                    'user_id'=>$user_id,
                    'role_id'=>6
                ]);
                Student::where('id',$value->id)->update([
                    'created_by'=>$user_id
                ]);
                DB::table('addressinfos')->where('students_id',$value->id)->update([
                'created_by'=>$user_id
               ]);
                DB::table('parent_details')->where('students_id',$value->id)->update([
                'created_by'=>$user_id
               ]);
                 DB::table('academic_infos')->where('students_id',$value->id)->update([
                'created_by'=>$user_id
               ]);
                DB::table('student_detail_sessionwise')->where('student_id',$value->id)->update([
                'created_by'=>$user_id
               ]);
               
            }
           
            if(isset($receipts)){
               $payment_count=count($receipts);
                $collect_id=$receipts[$payment_count-1];
                // $receipt_no=$this->reciept_no($collect_id);
                $receipt_no=$this->reciept_no($collect_id,$receipts);
                foreach ($receipts as $key => $value) {
                   DB::table('collect_fee')->where('id',$value)->update([
                        'reciept_no'=>$receipt_no
                   ]);
                } 
            }
        }
        return view($this->view_path.'.payment_status', compact('payment','login','data'));
    }
    public function loadCourse(Request $request){
        $response=[];
        $response['error']=true;
        $records=[];
        if($request->branch_id && $request->admission_session){
            $session=$request->admission_session;
            $icb = DB::table('sessionwise_branch_batch')->select('is_course_batch as icb')->where([
                ['session_id','=',$session],
                ['branch_id','=',$request->branch_id]
            ])->first();
            if(isset($icb->icb)){
                $isCourseBatch = $icb->icb ? $icb->icb : '0';
            }else{
               $isCourseBatch = 0;
            }
                if($isCourseBatch){
                   $data = DB::table('faculties')->select('cb.title as course_batch_title','cb.id as course_batch_id','cb.start_date','cb.end_date','faculties.id as course_id','faculties.faculty as course_title')
                        ->rightjoin('course_batches as cb',function($j)use($session){
                               $j->on('faculties.id','=','cb.course_id')
                                ->where([
                                    ['cb.status','=',1],
                                    ['cb.session_id','=',$session]
                                ]);
                        })
                        ->where([
                        ['faculties.branch_id','=',$request->branch_id],
                        ['faculties.sea_type','=',$request->sea_type],
                        ['faculties.status','=',1],
                        // ['course_type.session_id','=',$request->admission_session]
                    ])->get();
                        Log::debug($data);
                }else{
                    $data = DB::table('faculties')->select('faculties.id as course_id','faculties.faculty as course_title')
                            ->where([
                            ['faculties.branch_id','=',$request->branch_id],
                            ['faculties.status','=',1],
                            // ['course_type.session_id','=',$request->admission_session]
                        ])->get();
                }
            if (count($data)>0) {
               foreach ($data as $key => $value) {
                 $fee=DB::table('assign_fee')->where([
                    ['course_id','=',$value->course_id],
                    ['branch_id','=',$request->branch_id],
                    ['session_id','=',$request->admission_session],
                    ['status','=',1],
                    ['student_id','=',0]
                   ])
                 ->where(function($j)use($value,$isCourseBatch){
                    if($isCourseBatch){
                        $j->where('batch_id',$value->course_batch_id);
                    }
                 })
                 ->sum('fee_amount');
                 // log::debug($fee);
                if($isCourseBatch){
                    $available=$this->available_seats($value->course_id,$request->branch_id,$request->admission_session,$value->course_batch_id,$isCourseBatch);
                }else{
                    $available = 0;   
                }
                foreach ($value as $k => $val) {
                    if($fee>0){
                        $records[$key]['fee']=$fee;
                        $records[$key]['available']=$available;
                        $records[$key][$k]=$val;
                    }
                        
                }
                
               
               }
                // Log::debug($abc);
            }

            if(count($records)>0){
                $response['data']=$records;
                $response['error']=false;
                $response['session'] = $isCourseBatch;
                $response['msg']=env("course_label")." Type Found";
            }
            else{
                $response['msg']="No ".env("course_label")." Type Found";
            }
        }else{
            $response['msg']="Invalid Request!";
        }
        return response()->json(json_encode($response));
    }
    public function available_seats($course_id="",$branch_id="",$session_id="",$batch_id="",$isCourseBatch=""){
         $seats_occupied=DB::table('students')->where([
                    ['faculty','=',$course_id],
                    ['branch_id','=',$branch_id],
                    ['session_id','=',$session_id],
                    ['status','=',1]
                   ])
                    ->where(function($j)use($batch_id,$isCourseBatch){
                        if($isCourseBatch){
                            $j->where('batch_id',$batch_id);
                        }
                     })
                    ->count('id');
                    if($isCourseBatch){
                        $total_seat=DB::table('course_batches')->select('capacity')->where('id',$batch_id)->first();
                        $available = ($total_seat->capacity) - $seats_occupied;
                    }else{
                        $available = 0;
                    }
                return $available;
    }
    public function regNo($id='',$branch_id='',$session_id=''){
        if($id !=''){
            $std_reg_no=Student::find($id);
            if($std_reg_no->reg_no){
                $recpt=$std_reg_no->reg_no;
            }

            else{
                $data=Student::select('*','fac.short_name','fac.code')
                ->leftjoin('faculties as fac','fac.id','=','students.faculty')
                ->where([
                    ['students.faculty','=',$std_reg_no->faculty],
                    ['students.session_id','=',$session_id],
                    ['students.branch_id','=',$branch_id]
                ])->get();
                $count=str_pad(count($data),4,'0',STR_PAD_LEFT);
                $shortCode =(isset($data[0]->short_name))? $data[0]->short_name.'/' :'';
                $year=Session::get('activeSession'); //Carbon::now()->format('y');
                $sessionyrData = $this->getActiveYearSessionName($year);
                $ssTitle = (isset($sessionyrData[0]->session_name))? $sessionyrData[0]->session_name.'/' : '';
                $recpt=env('PREFIX_REG').'/'.$shortCode.$ssTitle.($count+1);
                
            }
            return $recpt;
        }
    }    
    public function getBranchBySeaType(Request $request){
        $response=[];
        $response['error']=true;
        $records=[];
        if($request->sea_type){
            $branches = DB::table('branches')->where(function($q)use($request){
                // $q->whereRaw("FIND_IN_SET('sea_type',$request->sea_type)");
                 $q->whereRaw('FIND_IN_SET(?,sea_type)', [$request->sea_type]);
                $q->where('record_status',1);
            })->select('id','branch_name')
            ->get();
            if(count($branches)>0){
                $response['data']=$branches;
                $response['error']=false;
                $response['msg']= "Branches  Found";
            }
            else{
                $response['msg']="No Branches Found";
            }
        }else{
            $response['msg']="Invalid Request!";
        }
        return response()->json(json_encode($response));
    }
    public function ChangeStatus(Request $request,$id,$status){
      
       $id= decrypt($id);
       $row= career::find($id);
      
       if(!$row){

        return parent::invalidRequest();
      }
      $update=$row->update(['status'=>$status,'updated_at'=>carbon::now()]);
      

      if($update){
         return redirect()->route('career.list')->with('message_success', ' Status Updated Successfully');
      }
      else{
        return redirect()->route('career.list')->with('message_danger', 'Something Wrong');
      }


    }
    public function CareerView($id)
    {

     $branch= DB::table('branches')->select('*')->where('id',session::get('activeBranch'))->first();
      $data['row'] = DB::table('careers')->select('careers.*','career_status.title as status')
      ->leftjoin('career_status','career_status.id','=','careers.status')
      ->where('careers.id',$id)->where('careers.record_status',1)->first();
      if(!$data['row']){

        return parent::invalidRequest();
      }
      return view(parent::loadDataToView($this->view_path.'.view'), compact('data','branch'));
    }

    function CareerAddFollowup( Request $request, $id)
    {   


       $this->panel= 'Career Follow Up';
       $id= $id;
       $data['career_status']=CareerStatus::select('id','title')->Pluck('title','id')->toarray();
       $data['career_status']= array_prepend( $data['career_status'],'-Select Status','');
       $data['followup']= CareerFollowup::select('career_followup.*','career_status.title as careerStatus')->where('career_followup.record_status',1)->leftjoin('career_status','career_status.id','=','career_followup.career_status')->where('career_id',$id)->where('career_followup.record_status',1)->get();
     
       $data['row']=DB::table('careers')->select('careers.*','career_status.title as status')
      ->leftjoin('career_status','career_status.id','=','careers.status')
      ->where('careers.id',$id)->where('careers.record_status',1)->first();
       
      return view(parent::loadDataToView($this->view_path.'.followup.index'),compact('data','id'));  
    }
    public function CareerFollowupStore(Request $request)
    {
         //dd($request->all());
         $request->request->add(['created_by' => auth()->user()->id]);
         $request->request->add(['created_at' => Carbon::now()]);
         $this->panel= 'Career Follow Up';
          $followup= CareerFollowup::create($request->all());
          if($followup){
            $update= career::where('id',$request->career_id)->update(['status'=>$request->career_status,
                'followup_date'=>$request->followup_date,
            ]);
          }
          if($followup){
             return redirect()->route('career.list')->with('message_success', ' follow up  Successfully');
          }
          else{
            return redirect()->route('career.list')->with('message_danger', 'Something Wrong');
          }
    }
    public function CareerFollowupDelete($id,Request $request)
    {

        if (!$row = CareerFollowup::find($id)) return parent::invalidRequest();
          $row->Update(['record_status'=>0]);
          $this->panel= 'Career Follow Up';
        $request->session()->flash($this->message_danger, $this->panel.' Deleted Successfully.');
        return redirect()->back();
    }
    public function CareerDelete($id,Request $request)
    {
        if (!$row = career::find($id)) return parent::invalidRequest();
          $row->Update(['record_status'=>0]);
        $request->session()->flash($this->message_danger, $this->panel.' Deleted Successfully.');
        return redirect()->back();
    }
}



