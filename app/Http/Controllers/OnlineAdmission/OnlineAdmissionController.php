<?php

namespace App\Http\Controllers\OnlineAdmission;

use Illuminate\Http\Request;
use App\Http\Controllers\CollegeBaseController;
use App\Branch;
use DB,Log,Session,File;
use Carbon\Carbon;
use Tzsk\Payu\Facade\Payment;
use App\Models\GeneralSetting;
use App\Models\Student;
use Illuminate\Support\Facades\Crypt;
use Intervention\Image\ImageManagerStatic as Image;


class OnlineAdmissionController extends CollegeBaseController
{
    protected $base_route = 'OnlineAdmission';
    protected $view_path = 'OnlineAdmission';
    protected $panel = 'Online Admission';
    protected $filter_query = [];


    public function __construct()
    {
        
    }
    public function index(){
        if(Session::has('student_inserted_data')){
            Session::forget('student_inserted_data');
        }
    	$data['image']=asset('assets/images/marine_bg/marine_college_bg.jpg');
    	$data['branch']=Branch::select('id','branch_name as title')->where([
    		['record_status','=',1]
    	])->pluck('title','id')->toArray();
    	$data['branch']=array_prepend($data['branch'],"--Select Branch--",'');
    	$data['max_age']=Carbon::now()->subYear(14)->format('Y-m-d');
        // dd($data['max_age']);
    	$data['min_age']=Carbon::now()->subYear(25)->format('Y-m-d');
        $data['admission_session']=GeneralSetting::select('online_admission_session')->first();
    	return view($this->view_path.'.newIndex',compact('data'));
    }
    public function store(Request $request){
        if($request->student_type==1){
            $rules=[
            'first_name'=>'required',
            // 'father_first_name'=>'required',
            'date_of_birth'=>'required',
            'gender'=>'required',
            'mobile_1'=>'required|numeric',
            'email'=>'required|email',   
            // 'aadhar_no'=>'required|numeric',   
            // 'indose_number'=>'required|numeric',   
            'address'=>'required',   
            // 'state'=>'required',   
            // 'country'=>'required',   
            // 'zip'=>'required',   
            'branch'=>'required',   
            'course'=>'required',    
            'high_school'=>'image|mimes:jpeg,png,jpg,bmp|required|max:1024',
            'intermediate'=>'image|mimes:jpeg,png,jpg,bmp|required|max:1024',
            'aadhaar'=>'image|mimes:jpeg,png,jpg,bmp|max:1024'
        ];

        }elseif($request->student_type==1){
            $rules=[
            'first_name'=>'required',
            'date_of_birth'=>'required',
            'gender'=>'required',
            'mobile_1'=>'required|numeric',
            'email'=>'required|email',   
            'aadhar_no'=>'required|numeric',   
            'indose_number'=>'required|numeric',   
            // 'address'=>'required',      
            'branch'=>'required',   
            'course'=>'required',    
            'high_school'=>'image|mimes:jpeg,png,jpg,bmp|required|max:1024',
            'intermediate'=>'image|mimes:jpeg,png,jpg,bmp|required|max:1024',
            'aadhaar'=>'image|mimes:jpeg,png,jpg,bmp|max:1024',
            'pass_port'=>'image|mimes:jpeg,png,jpg,bmp|required|max:1024'
            ];
        }else{
             $rules=[
            'first_name'=>'required',
            'student_type'=>'required',
            'date_of_birth'=>'required',
            'gender'=>'required',
            'mobile_1'=>'required|numeric',
            'email'=>'required|email',   
            // 'aadhar_no'=>'required|numeric',   
            'indose_number'=>'required',   
            'address'=>'required',      
            'branch'=>'required',   
            'course'=>'required',    
            'high_school'=>'image|mimes:jpeg,png,jpg,bmp|required|max:1024',
            'intermediate'=>'image|mimes:jpeg,png,jpg,bmp|required|max:1024',
            'aadhaar'=>'image|mimes:jpeg,png,jpg,bmp|max:1024',
            'pass_port'=>'image|mimes:jpeg,png,jpg,bmp|required|max:1024'
            ];
        }
        
        $msg=[
            'first_name.required'=>'Please Enter Name',
            'student_type.required'=>'Please Select Student Type',
            // 'father_first_name.required'=>'Please Enter Father Name',
            'date_of_birth.required'=>'Please Enter Date of birth',
            'gender.required'=>'Please Select Gender ',
            'mobile_1.required'=>'Please Enter Mobile Number',
            'email.required'=>'Please Enter Email',   
            // 'aadhar_no.required'=>'Please Enter Aadhaar Number',   
            'indose_number.required'=>'Please Enter Indose Number',   
            // 'address.required'=>'Please Enter Address',   
            // 'state.required'=>'Please Enter State',   
            // 'country.required'=>'Please Enter Country',   
            // 'zip.required'=>'Please Enter Zip',   
            'branch.required'=>'Please Select Branch',   
            'course.required'=>'Please Select Course',    
            'high_school*.image'=>"Please choose high school image in format of jpeg, png, bmp, gif, svg, or webp.",
            'high_school*.required'=>"Please add high school image.",
            'intermediate*.image'=>"Please choose intermediate image in format of jpeg, png, bmp, gif, svg, or webp.",
            'intermediate*.required'=>"Please  add intermediate image.",
            'aadhaar*.image'=>"Please choose aadhaar image in format of jpeg, png, bmp, gif, svg, or webp.",
            // 'aadhaar*.required'=>"Please  add aadhaar image.",
            'pass_port*.image'=>"Please choose passport image in format of jpeg, png, bmp, gif, svg, or webp.",
            'pass_port*.required'=>"Please  add passport image.",
        ];
        
        $this->validate($request,$rules,$msg);
        $img_name=str_replace(' ','_',$request['first_name']).$request['mobile_1'];
        if($request->has('high_school')){
            $img=$request->file('high_school');
            $high_school_name=$img_name.'.'.$img->getClientOriginalExtension();
            $img = Image::make($img->getRealPath());              
            $img->resize(300, 300);
            $img->save(public_path('high_school/' .$high_school_name));
            // $aa=$img->move(public_path().'/high_school/',$high_school_name);
            $request->request->add(['high_school_image'=>'/high_school/'.$high_school_name]);
        }
        if($request->has('intermediate')){
            $img=$request->file('intermediate');
            $intermediate_name=$img_name.'.'.$img->getClientOriginalExtension();
            $img = Image::make($img->getRealPath());              
            $img->resize(300, 300);
            $img->save(public_path('intermediate/' .$intermediate_name));
            // $img->move(public_path().'/intermediate/',$intermediate_name);
            $request->request->add(['intermediate_image'=>'/intermediate_image/'.$intermediate_name]);
        }
        if($request->has('aadhaar')){
            $img=$request->file('aadhaar');
            $aadhaar_name=$img_name.'.'.$img->getClientOriginalExtension();
            $img = Image::make($img->getRealPath());              
            $img->resize(300, 300);
            $img->save(public_path('aadhaar/' .$aadhaar_name));
            // $img->move(public_path().'/aadhaar/',$aadhaar_name);
            $request->request->add(['aadhaar_image'=>'/aadhaar/'.$aadhaar_name]);            
        }
        if($request->has('pass_port')){
            $img=$request->file('pass_port');
            $pass_port_name=$img_name.'.'.$img->getClientOriginalExtension();
            $img = Image::make($img->getRealPath());              
            $img->resize(300, 300);
            $img->save(public_path('pass_port/' .$pass_port_name));
            // $img->move(public_path().'/pass_port/',$pass_port_name);
            $request->request->add(['pass_port_image'=>'/aadhaar/'.$pass_port_name]);            
        }
        $request->request->add(['branch_id'=>$request->branch]);
        for ($i=1; $i <count($request->course) ; $i++) { 
            $course_data=$this->get_course_batch_details($request->course[$i]);
            Session::put('isCourseBatch', $course_data['isCourseBatch']);
            $batch = isset($course_data['batch']->batch_id) ? $course_data['batch']->batch_id : null ;
            $available_seat=$this->available_seats($course_data['name']->faculty_id,$request->branch,$this->online_admission_session(),$batch);
            // if(Session::get('isCourseBatch')){
            //     if(!($available_seat>0)){
            //          Session::flash('message_danger',"Some of your registered course has been removed as NO SEAT AVAILABLE");
            //     }
            // }
                $request->request->add(['faculty'=>$course_data['name']->faculty_id]);
                $request->request->add(['semester'=>2]);
                $request->request->add(['org_id'=>1]);
                // $request->request->add(['course_type_id'=>$course_data['type']->course_type_id]);
                
                $request->request->add(['batch_id'=>$batch]);
                $request->request->add(['session_id'=>$this->online_admission_session()]);
                $request->request->add(['status'=>'5']);
                $data=Student::create($request->all());
                $reg_no=$this->regNo($data->id,$data->branch_id,$data->session_id);
                // dd($reg_no,$data);
                // dd($request->all(),$data,$data->id);
                if($data){
                    DB::table('addressinfos')->insert([
                        'students_id'=>$data->id,
                        'address'=>$request->address,
                        'state'=>$request->state,
                        'country'=>$request->country,
                        'mobile_1'=>$request->mobile_1,
                        'status'=>'5'
                    ]);
                    Student::where('id',$data->id)->update([
                        'reg_no'=>$reg_no,
                        'reg_date'=>Carbon::now(),
                        'status'=>'5'
                    ]);
                    DB::table('parent_details')->insert([
                        'students_id'=>$data->id,
                        'father_first_name'=>$request->father_first_name,
                        'status'=>'5',
                    ]);
                    DB::table('student_detail_sessionwise')->insert([
                        'course_id'=>$data->faculty,
                        'student_id'=>$data->id,
                        'session_id'=>$request->session_id,
                        'Semester'=>$request->semester,
                        'active_status'=>'5'
                    ]);
                    DB::table('academic_infos')->insert([
                        'created_at'=>Carbon::now(),
                        'students_id'=>$data->id,
                        'high_school_image'=>$request->high_school_image,
                        'intermediate_image'=>$request->intermediate_image,
                        'status'=>'5',
                        'sorting_order'=>'1',
                    ]);
                }    
               
               $student_inserted_data[]=$data;

        }
        Session::put('student_inserted_data',$student_inserted_data);
        // dd(Session::get('student_inserted_data'),$request->all());
        $form_data=$request->all();
        // dd($form_data);
        $data['image']=asset('assets/images/marine_bg/marine_college_bg.jpg');
        $merchant=DB::table('branches')->select('Merchant_Key','Merchant_Salt','branch_name')->where('id',$request->branch)->first();
        Session::put('Merchant_Key',$merchant->Merchant_Key);
        Session::put('Merchant_Salt',$merchant->Merchant_Salt);
        $min_pay_amount = $this->min_pay_amount();
        return view($this->view_path.'.confirm_details',compact('form_data','data','merchant','student_inserted_data','min_pay_amount'));
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
}



