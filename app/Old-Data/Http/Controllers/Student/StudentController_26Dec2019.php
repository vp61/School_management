<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\CollegeBaseController;
use App\Http\Requests\Student\Registration\AddValidation;
use App\Http\Requests\Student\Registration\EditValidation;

use App\Models\AcademicInfo;
use App\Models\Addressinfo;
use App\Models\AlertSetting;
use App\Models\Attendance;
use App\Models\Attendence;
use App\Models\Document;
use App\Models\Faculty;
use App\Models\GuardianDetail; use App\category_model;
use App\Models\LibraryMember;
use App\Models\Note;
use App\Models\ParentDetail;
use App\Models\ResidentHistory;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentAddressinfo;
use App\Models\StudentGuardian;
use App\Models\StudentParent;
use App\Models\StudentStatus;
use App\Admission;
use App\Collection;

use App\Models\TransportHistory;
use App\Traits\SmsEmailScope;
use App\Traits\UserScope;
use App\User;
use Auth;
use App\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Image, URL;
use ViewHelper;
use Session;
use DB;
use Carbon\Carbon;
use App\models\FeeCollection; 
use App\StudentPromotion;
use App\AssignFee;

class StudentController extends CollegeBaseController
{
    protected $base_route = 'student';
    protected $view_path = 'student';
    protected $panel = 'Student';
    protected $folder_path;
    protected $folder_name = 'studentProfile';
    protected $filter_query = [];

    use SmsEmailScope;
    use UserScope;

    public function __construct()
    {
        $this->folder_path = public_path().DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.$this->folder_name.DIRECTORY_SEPARATOR;
    }

    public function index(Request $request)
    {

        $org_id     = Auth::user()->org_id;
        $branch_id  = Session::get('activeBranch');
        $current_session_id=Session::get('activeSession');
        $data = []; //dd($current_session_id);
        /*->leftJoin('faculties', function($q){
        $q->on('faculties.id', '=', 'student_detail_sessionwise.course_id');
        })*/

        $data['student'] = StudentPromotion::select('students.id'
, 'students.reg_no', 'students.reg_date'
, 'student_detail_sessionwise.course_id as faculty'
, 'student_detail_sessionwise.semester', 'students.academic_status', 'students.first_name', 'students.middle_name', 'students.last_name', 'students.status', 'ai.mobile_1','pd.father_first_name as father_name', 'student_detail_sessionwise.session_id','students.indose_number')
            ->leftjoin('addressinfos as ai', 'student_detail_sessionwise.student_id', '=', 'ai.students_id')
            ->leftjoin('parent_details as pd', 'student_detail_sessionwise.student_id', '=', 'pd.students_id')
            ->leftJoin('students', function($a){
                $a->on('student_detail_sessionwise.student_id', '=', 'students.id');
            })->where(function ($query) use ($request) {

                if ($request->reg_no) {
                    $query->where('students.reg_no', 'like', '%' . $request->reg_no . '%');
                    $this->filter_query['students.reg_no'] = $request->reg_no;
                }

                if ($request->reg_start_date && $request->reg_end_date) {
                    $start_date=date("Y-m-d", strtotime($request->reg_start_date))." 00:00:00";
                    $end_date=date("Y-m-d", strtotime($request->reg_end_date))." 23:59:00";
                    $query->whereBetween('students.reg_date', [$start_date, $end_date]);
                    $this->filter_query['reg_start_date'] = $request->get('reg_start_date');
                    $this->filter_query['reg_end_date'] = $request->get('reg_end_date');
                } elseif ($request->reg_start_date) {
                    
                    $query->where('students.reg_date', '>=', $request->get('reg_start_date'));
                    $this->filter_query['reg_start_date'] = $request->get('reg_start_date');
                } elseif ($request->reg_end_date) {
                    $query->where('students.reg_date', '<=', $request->get('reg_end_date'));
                    $this->filter_query['reg-end-date'] = $request->get('reg-end-date');
                }else{
                    if(isset($_GET['reg_start_date'])){ 
                    }else{
                        $start_date=date("Y-m-d")." 00:00:00";
                        $end_date=date("Y-m-d")." 23:59:00";
                        $query->whereBetween('students.reg_date', [$start_date, $end_date]);
                        $this->filter_query['reg_start_date'] = $request->get('reg_start_date');
                        $this->filter_query['reg_end_date'] = $request->get('reg_end_date');
                    }
                }

                if ($request->faculty) {
                    $query->where('students.faculty', '=', $request->faculty);
                    $this->filter_query['students.faculty'] = $request->faculty;
                
                }

                if ($request->category) {
                    $query->where('students.category_id', $request->category);
                    $this->filter_query['category'] = $request->category; 
                }
                if ($request->name) {
                    $query->where('students.first_name', 'like', '' . $request->name . '%')
                    
                    ->orWhere('students.first_name', 'like', '%' . $request->name . '%')
                    ->orWhere('students.first_name', 'like', '%' . $request->name . '');
                    $this->filter_query['name'] = $request->name;
                }

                if ($request->semester) {
                    $query->where('students.semester', 'like', '%' . $request->semester . '%');
                    $this->filter_query['students.semester'] = $request->semester;
                }

                if ($request->status && $request->status!="all") {
                    $query->where('students.status', $request->status == 'active' ? 1 : 0);
                    $this->filter_query['students.status'] = $request->get('status');
                }

                if ($request->mobile) {
                    $query->where('ai.mobile_1', $request->mobile)->orWhere('ai.mobile_1', 'like', '%'.$request->mobile.'%');
                    $this->filter_query['mobile'] = $request->mobile;
                }

                if ($request->reg_no) {
                    $query->where('students.reg_no', $request->reg_no);
                    $this->filter_query['reg_no'] = $request->reg_no;
                }
            })
            //->where('students.org_id',$org_id)
            ->where('student_detail_sessionwise.session_id', $current_session_id)
            ->where('students.branch_id',$branch_id)
            ->groupBy('student_detail_sessionwise.id')
            ->orderBy('id','desc')
            ->get();

        

        $data['category_name'] = category_model::get();
        $data['faculties'] = $this->activeFaculties();

        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;
        $panel="Student";
        return view(parent::loadDataToView($this->view_path.'.index'), compact('data', 'panel'));
    }
    
    
    public function registration($length = 4)
    {
        $admId = (isset($_GET['admId']) && $_GET['admId']!="")? $_GET['admId'] : "";
        $admData = "";
        if($admId){
            $admData = Admission::where('id',$admId)->get();
        }
        
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    
    $randomString = $this->reG_no();
    
         if(Session::has('activeBranch'))
        {
            $branch_id = Session::get('activeBranch');
        }
        else{ $branch_id = Auth::user()->branch_id; }
                    
        $org_id = Auth::user()->org_id;
        $branch = Branch:: select('branch_name','branch_title','id','org_id')->where('id', $branch_id)->get();   //
        $data = []; //dd($branch);
        
        $data['blank_ins'] = new Student();
        
    $Semester = Semester:: select('semester','id')->get(); 
    $sessions = DB::table('session')->select('id', 'session_name')->pluck('session_name', 'id')->toArray(); //get();
    $sessions=array_prepend($sessions, '----Select Session----', '');

    $pay_type_list = DB::table('payment_type')->select('type_name')->where('status', '1')->pluck('type_name','type_name')->toArray(); 
        //dd($pay_type_list);//, 'id'
        $pay_type_list = array_prepend($pay_type_list, "--Payment Mode--", "");
        
    $faculties = Faculty::select('id', 'faculty')
      ->where('org_id',$org_id)
      ->where('branch_id',$branch_id)
      ->orderBy('faculty')
      ->Active()->pluck('faculty','id')->toArray();
      
      $courses = Faculty::select('id', 'faculty', 'status')
            ->where('branch_id' , $branch_id) 
            ->orderBy('faculty')
            ->get();
      
      
      $category = category_model::select('id', 'category_name')->pluck('category_name','id')->toArray();
      $category= array_prepend($category,'SelectCategory',0);

        $data['faculties'] = array_prepend($faculties,'Select Course',0);
        $academicStatus = StudentStatus::select('id', 'title')->Active()->pluck('title','id')->toArray();
        $data['academic_status'] = array_prepend($academicStatus,'Select Status',0);

        return view(parent::loadDataToView($this->view_path.'.registration.register'), compact('data','branch','category','Semester','sessions','randomString','admData','pay_type_list','courses', 'admId'));
    }



    public function addregistration($id)
    {


        $registration = Admission::where('id', $id)->get();
        $org_id = Auth::user()->org_id;
        
        if(Session::has('activeBranch')){
            $branch_id = Session::get('activeBranch');
        }else{  $branch_id = Auth::user()->branch_id;  }
        // $branches = Branch:: select('branch_name','branch_title','id','org_id')->where('org_id', $org_id)->get();
        $data = [];
        $data['blank_ins'] = new Student();
        $faculties = Faculty::select('id', 'faculty')
      ->where('org_id',$org_id)
      ->where('branch_id',$branch_id)
      ->Active()->pluck('faculty','id')->toArray();
        $data['faculties'] = array_prepend($faculties,'Select Course',0);
            
        $academicStatus = StudentStatus::select('id', 'title')->Active()->pluck('title','id')->toArray();
        $data['academic_status'] = array_prepend($academicStatus,'Select Status',0);
        $data['selected_branch'] = Branch:: select('branch_name','branch_title','id','org_id')->where('id', $branch_id)->get(); 
 // return $registration;
 // DD();
        return view(parent::loadDataToView($this->view_path.'.registration.register'), compact('data','registration'));
    }

    public function register(AddValidation $request)
    {
        //return $request;

        //dd($request->date_of_birth);

  
        if ($request->hasFile('student_main_image')){
            $student_image = $request->file('student_main_image');
            $student_image_name = $request->reg_no.'.'.$student_image->getClientOriginalExtension();
            $student_image->move(public_path().DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'studentProfile'.DIRECTORY_SEPARATOR, $student_image_name);
        }else{
            $student_image_name = "";
        }

        $request->request->add(['reg_date' => $request->get('reg_date')]);
        $request->request->add(['created_by' => auth()->user()->id]);
        $request->request->add(['semester' => $request->get('semester')]);
        $request->request->add(['student_image' => $student_image_name]);
        $regNo  = $this->reG_no();
        if(!$request->email){
            $emails = "AES".$regNo."@asha.ac.in";
        }else{ $emails = $request->email; }

        $request->request->add(['reg_no' => $regNo]);
        $request->request->add(['email' => $emails]);
        $request->request->add(['first_name' => trim($request->first_name)]);
        
        $student = Student::create($request->all());

        //entry of student_detail sessionwise

         DB::table('student_detail_sessionwise')->Insert(['student_id'=>$student->id
                    , 'course_id'=>$request->faculty
                    , 'session_id'=>$request->session_id
                    , 'Semester'=>$request->semester
                    , 'created_by'=>auth()->user()->id]); 

        $parential_image_path = public_path().DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'parents'.DIRECTORY_SEPARATOR;

        if ($request->hasFile('father_main_image')){
            $father_image = $request->file('father_main_image');
            $father_image_name = $student->reg_no.'_father.'.$father_image->getClientOriginalExtension();
            $father_image->move($parential_image_path, $father_image_name);
        }else{
            $father_image_name = "";
        }

        if ($request->hasFile('mother_main_image')){
            $mother_image = $request->file('mother_main_image');
            $mother_image_name = $student->reg_no.'_mother.'.$mother_image->getClientOriginalExtension();
            $mother_image->move($parential_image_path, $mother_image_name);
        }else{
            $mother_image_name = "";
        }

        if ($request->hasFile('guardian_main_image')){
            $guardian_image = $request->file('guardian_main_image');
            $guardian_image_name = $student->reg_no.'_guardian.'.$guardian_image->getClientOriginalExtension();
            $guardian_image->move($parential_image_path, $guardian_image_name);
        }else{
            $guardian_image_name = "";
        }

        $request->request->add(['father_image' => $father_image_name]);
        $request->request->add(['mother_image' => $mother_image_name]);
        $request->request->add(['guardian_image' => $guardian_image_name]);
        $request->request->add(['father_first_name' => trim($request->father_first_name)]);

        $request->request->add(['students_id' => $student->id]);
        $addressinfo = Addressinfo::create($request->all());
        $parentdetail = ParentDetail::create($request->all());

        if($request->guardian_link_id == null){
            $guardian = GuardianDetail::create($request->all());
            $studentGuardian = StudentGuardian::create([
                'students_id' => $student->id,
                'guardians_id' => $guardian->id,
            ]);
        }else{
            $studentGuardian = StudentGuardian::create([
                'students_id' => $student->id,
                'guardians_id' => $request->guardian_link_id,
            ]);
        }


        /*Academic Info Start*/
        if ($student && $request->has('institution')) {
            foreach ($request->get('institution') as $key => $institute) {
                AcademicInfo::create([
                    'students_id' => $student->id,
                    'institution' => $institute,
                    'board' => $request->get('board')[$key],
                    'pass_year' => $request->get('pass_year')[$key],
                    'symbol_no' => "Education",
                    //'symbol_no' => $request->get('symbol_no')[$key],
                    'percentage' => $request->get('percentage')[$key],
                    'division_grade' => $request->get('division_grade')[$key],
                    'major_subjects' => $request->get('major_subjects')[$key],
                    'remark' => $request->get('remark')[$key],
                    'created_by' => auth()->user()->id,
                ]);
            }
        }
        if( $student && $request->has('fee_masters_id'))
        { 
            
            $feeMastIdArr = $request->get('fee_masters_id'); 
            $feeAmountArr = $request->get('fee_amount');
            $date         = $request->get('reg_date'); 

            //$date = Carbon::now()->format('Y-m-d H:i:s');
            //$receipt_no = substr(hash('sha256', mt_rand() . microtime()), 0, 10);
            $receipt_no = $this->reciept_no();
            $ifPaymentDone = 0;
            foreach ($feeMastIdArr as $k => $v) {
               
               $FeeHeadIds = $v;
               $FeeHeadAmt = $feeAmountArr[$k];
               $rmrk=(isset($request->remark[$k])) ? $request->remark[$k] : "";
               if(Session::has('activeBranch')){
                    $branch_ids = Session::get('activeBranch');
                }else{ $branch_ids = Auth::user()->branch_id; }
                if($FeeHeadAmt > 0){
                    $ifPaymentDone = 1;
                    DB::Table('collect_fee')->Insert([
                        'created_by' =>auth()->user()->id,
                        'reciept_date' =>$date, 
                        'student_id' => $student->id,
                        'assign_fee_id' =>$FeeHeadIds,
                        'amount_paid' => $FeeHeadAmt,
                        'payment_type' =>$request->payment_type,
                        'reference' =>$request->ref_no,
                        'remarks' =>$rmrk,
                        'status' =>1,
                        'reciept_no' =>$receipt_no 
                        
                    ]);
                }
                
            } 
                
            }
  
        /*Academic Info End*/

        /*SMS & Email Alert*/
        $this->registrationConfirm($student->first_name,$student->reg_no,$addressinfo->mobile_1,$student->email);

        $request->session()->flash($this->message_success, $this->panel. ' Created Successfully.');
        if ($student && $request->has('fee_masters_id') && $ifPaymentDone == 1) {
        return redirect()->route('feeReceipt',$receipt_no);
        }
        return redirect()->route($this->base_route);
        
        // return view('student.registration.includes.studentfeereceipt', compact('receipt_no'));
    }




    public function feeReceipt($receipt_no)
    {
        
        $data = DB::table('collect_fee')->Select('collect_fee.id','collect_fee.student_id', 'collect_fee.reciept_date','collect_fee.assign_fee_id','collect_fee.amount_paid','collect_fee.reciept_no','collect_fee.payment_type','collect_fee.reference','collect_fee.remarks','collect_fee.created_by','sd.first_name','sd.branch_id','sd.reg_no', 'sd.reg_date', 'sd.university_reg','sd.date_of_birth', 'sd.gender','asf.fee_amount','asf.course_id','asf.fee_head_id','asf.session_id','fd.fee_head_title','br.branch_name','br.branch_logo','br.branch_mobile','br.branch_email','br.branch_address','ur.name', 'fac.faculty','sn.session_name')
        ->where('collect_fee.reciept_no','=', $receipt_no)
        ->where('collect_fee.status',1)
        ->leftJoin('students as sd', 'sd.id', '=', 'collect_fee.student_id')
        ->leftJoin('users as ur', 'ur.id', '=', 'collect_fee.created_by')
        ->leftJoin('assign_fee as asf', 'asf.id', '=', 'collect_fee.assign_fee_id')
        ->leftJoin('fee_heads as fd', 'fd.id', '=', 'asf.fee_head_id')
        ->leftJoin('branches as br', 'br.id', '=', 'sd.branch_id')
        ->leftJoin('session as sn', 'sn.id', '=', 'asf.session_id')
        ->leftJoin('faculties as fac', 'asf.course_id', '=', 'fac.id')->get();
        //return $data;
        return view('student.registration.includes.studentfeereceipt',compact('data'));
    }

    /****** [START: No Due ]***********/
    public function noDueReceipt($student)
    {

        $sessiondata=DB::table('student_detail_sessionwise')->select('student_detail_sessionwise.student_id','student_detail_sessionwise.course_id','student_detail_sessionwise.session_id','session.session_name','faculties.faculty','students.first_name','students.reg_no')
         ->where('student_id','=',$student)
         ->join('session','student_detail_sessionwise.session_id','=','session.id')
         ->join('students','students.id','=','student_detail_sessionwise.student_id')
         ->join('faculties','faculties.id','=','student_detail_sessionwise.course_id')
        ->get();
     
          
           foreach ($sessiondata as $key => $value) {
                $feepaid[]=DB::table('assign_fee')->select('session_id','assign_fee.id','fee_heads.fee_head_title','assign_fee.fee_head_id','assign_fee.times','assign_fee.fee_amount','session.session_name','collect_fee.amount_paid','faculties.faculty')
                        ->where([
                            ['session_id','=',$value->session_id],
                            ['course_id','=',$value->course_id],
                            ['collect_fee.student_id','=',$student]
                        ])
                        ->join('fee_heads','assign_fee.fee_head_id','=','fee_heads.id')
                        ->join('session','session.id','=','assign_fee.session_id')
                        ->join('collect_fee','collect_fee.assign_fee_id','=','assign_fee.id')
                        ->join('faculties','assign_fee.course_id','=','faculties.id')
                       
                        ->get();
                 $assignfee[]=DB::table('assign_fee')->select('session_id','assign_fee.id','assign_fee.fee_head_id','assign_fee.times','assign_fee.fee_amount','fee_head_title','session.session_name')       
                    ->where([
                            ['session_id','=',$value->session_id],
                            ['course_id','=',$value->course_id]
                           ])
                     ->join('fee_heads','assign_fee.fee_head_id','=','fee_heads.id')
                     ->join('session','assign_fee.session_id','=','session.id')
                    ->get();   
            } 
 
      
            foreach ($feepaid as $k => $value) {
            if(count($value)==0){
                
              $feepaid[$k]=$assignfee[$k]; 
           }
        }
           
            $gt=0;
            $tdue=0;
            foreach ($assignfee as $key => $value) {
                foreach ($value as $key => $val) {
                  $sum=0; 
                  $discount=0;
                  $disc=0;
                  $due=0;
                    foreach ($feepaid as $key => $data) {
                        foreach ($data as $key => $v) {
                            if($val->session_id==$v->session_id)
                            {
                                if($val->id==$v->id){
                                 if(isset($v->amount_paid)){  
                                      $sum=$sum+$v->amount_paid;
                                      if(!empty($v->discount)){
                                                $disc=$v->discount;
                                            }
                                            else{
                                                $disc=0;
                                            }
                                            $discount=$discount+$disc;  
                                    }
                                }    
                           
                            }    
                        }
                    }
                     $gt=$sum+$gt;
                      foreach ($feepaid as $key => $data) { 
                        
                        foreach ($data as $key => $v) {
                            if($val->session_id==$v->session_id)
                            {
                                if($val->id==$v->id){

                                       $due=$val->fee_amount-$discount-$sum;
                                    }
                                       if($due==0){ 
                                       
                                        if($val->fee_amount!=!empty($sum)){
                                            $due=$val->fee_amount;
                                           
                                      }  
                                       
                                }

                                $fee1[$val->fee_head_title][$val->times][$v->session_name]=array('paid'=>$sum , 'discount'=>$discount,'due'=>$due,'fee_head'=>$val->fee_head_title);


                             }
                            
                       } 
                                  
                    }   
                       
                            $tdue=$due+$tdue;
               }
            }
         
           foreach ($assignfee as $ke => $va) {
                 foreach ($va as $key => $v) {
                      foreach ($fee1 as $key => $value) {
                         foreach ($value as $k => $val) {
                               $x= array_key_exists($v->session_name, $val);
                            if(array_key_exists($v->session_name, $val)){
                              
                            }
                           else{
                            foreach ($val as $key => $vl) {
                         
                             $fee1[$vl['fee_head']][$k][$v->session_name]=array('paid'=>0 , 'discount'=>0,'due'=>0,'fee_head'=>$vl['fee_head']);
                          }
                      }
                    }
                 }            

                }
            } 
            
          return view('asha/FeeView',compact('sessiondata','assignfeeid','fee1','gt','tdue'));
    }

    /****** [END: No Due ]***********/
    public function view($id)
    {

        $current_session_id=Session::get('activeSession');
        $data = [];
         $data['student'] = StudentPromotion::select('students.id as id','students.reg_no', 'students.reg_date', 'students.university_reg',
            'student_detail_sessionwise.course_id as faculty','student_detail_sessionwise.semester', 'students.academic_status', 'students.first_name', 'students.middle_name',
            'students.last_name', 'students.date_of_birth', 'students.gender', 'students.blood_group', 'students.nationality',
            'students.mother_tongue', 'students.email', 'students.extra_info', 'students.student_image', 'students.status', 'pd.grandfather_first_name','students.branch_id',
            'pd.grandfather_middle_name', 'pd.grandfather_last_name', 'pd.father_first_name', 'pd.father_middle_name',
            'pd.father_last_name', 'pd.father_eligibility', 'pd.father_occupation', 'pd.father_office', 'pd.father_office_number',
            'pd.father_residence_number', 'pd.father_mobile_1', 'pd.father_mobile_2', 'pd.father_email', 'pd.mother_first_name',
            'pd.mother_middle_name', 'pd.mother_last_name', 'pd.mother_eligibility', 'pd.mother_occupation', 'pd.mother_office',
            'pd.mother_office_number', 'pd.mother_residence_number', 'pd.mother_mobile_1', 'pd.mother_mobile_2', 'pd.mother_email',
            'ai.address', 'ai.state', 'ai.country', 'ai.temp_address', 'ai.temp_state', 'ai.temp_country', 'ai.home_phone',
            'ai.mobile_1', 'ai.mobile_2', 'gd.id as guardian_id', 'gd.guardian_email','gd.guardian_first_name', 'gd.guardian_middle_name', 'gd.guardian_last_name',
            'gd.guardian_eligibility', 'gd.guardian_occupation', 'gd.guardian_office', 'gd.guardian_office_number', 'gd.guardian_residence_number',
            'gd.guardian_mobile_1', 'gd.guardian_mobile_2', 'gd.guardian_email', 'gd.guardian_relation', 'gd.guardian_address')

            ->where('student_detail_sessionwise.session_id', $current_session_id)
            ->where('students.id','=',$id)
            ->leftJoin('students','student_detail_sessionwise.student_id', '=', 'students.id')
            ->join('parent_details as pd', 'pd.students_id', '=', 'students.id')
            ->join('addressinfos as ai', 'ai.students_id', '=', 'students.id')
            ->join('student_guardians as sg', 'sg.students_id','=','students.id')
            ->join('guardian_details as gd', 'gd.id', '=', 'sg.guardians_id')
            ->first();

        if (!$data['student']){
            request()->session()->flash($this->message_warning, "Not a Valid Student");
            return redirect()->route($this->base_route);
        }

          $data['fee_master'] = DB::table('collect_fee')->Select('collect_fee.id','collect_fee.status','collect_fee.student_id','collect_fee.discount', 'collect_fee.reciept_date','collect_fee.assign_fee_id','collect_fee.amount_paid','collect_fee.reciept_no','collect_fee.payment_type','sd.first_name','sd.branch_id','sd.reg_no', 'sd.reg_date', 'sd.university_reg','sd.date_of_birth', 'sd.gender','asf.fee_amount','asf.course_id','asf.fee_head_id','fd.fee_head_title','br.branch_name','br.branch_logo','br.branch_mobile','br.branch_email','br.branch_address', 'fac.faculty')
        ->where('collect_fee.student_id','=',$data['student']->id)
        ->join('students as sd', 'sd.id', '=', 'collect_fee.student_id')
        ->join('assign_fee as asf', 'asf.id', '=', 'collect_fee.assign_fee_id')
        ->join('fee_heads as fd', 'fd.id', '=', 'asf.fee_head_id')
        ->join('branches as br', 'br.id', '=', 'sd.branch_id')
        ->join('faculties as fac', 'asf.course_id', '=', 'fac.id')
        ->get();


        $branch=$data['student']->branch_id;
        $session=Session::get('activeSession');
        $data['session_id'] =$session;
         //return $session ; 
        $course=$data['student']->faculty;
        //return $data['student'] ; 
        $student = $data['student']->id;
        $fee_result=AssignFee::Select('assign_fee.*', 'fee_heads.fee_head_title')
        ->leftJoin('fee_heads', function($join){
            $join->on('fee_heads.id', '=', 'assign_fee.fee_head_id');
        })->where('assign_fee.branch_id', $branch)
        ->where('assign_fee.session_id', $session)
        ->where('assign_fee.course_id', $course)
        ->Where('assign_fee.student_id', '0')
        ->orWhere(function($q) use ($data){
            if($data['student']->id){
                $q->orWhere('assign_fee.student_id', $data['student']->id)
                ->where('assign_fee.branch_id', $data['student']->branch_id)
                ->where('assign_fee.session_id', $data['student']->session_id)
                ->where('assign_fee.course_id', $data['student']->faculty);
            }
        })->groupBy('assign_fee.id')->get();
        // return  $fee_result;
        // dd();
        $due = array();
        $paid_result_arr=array();
        $totalSumVal = $totalSumDisc = $totalSumPaid = $totalSumDue =0;
        if(count($fee_result) && $data['student']->id)
        {
            foreach($fee_result as $fee){
            $totalPay = $totalDisc = $totalDisc = $totalDueAmt =  0;
            $feeHeadAmount = $fee->fee_amount;

            $paid_result=DB::table('collect_fee')->select('amount_paid','discount')
            ->where('assign_fee_id', $fee->id)
            ->where('status', 1)
            ->where('student_id', $data['student']->id)->get();
             
            foreach($paid_result as $v){  
                $totalPay = $totalPay + $v->amount_paid;
                $totalDisc = $totalDisc + $v->discount; 
                $totalDueAmt =(int)$totalPay+(int)$totalDisc;
            }  

            $totalDueAmt =(int)$feeHeadAmount - (int)$totalDueAmt;
            
           $totalSumVal = $totalSumVal+$fee->fee_amount;
           $totalSumPaid = $totalSumPaid+$totalPay;
           $totalSumDisc = $totalSumDisc+$totalDisc;
           $totalSumDue = $totalSumDue + $totalDueAmt; 
            
            $due[] = $totalDueAmt;
            $paid_result_arr[]= $totalPay; 
            $disc_result_arr[]= $totalDisc; 
             }

          
         }

        
       

        /*total Calculation on Table Foot*/
        $data['student']->fee_amount    = $totalSumVal; 
        $data['student']->paid_amount   = $totalSumPaid;  
        $data['student']->discount      = $totalSumDisc;  
        $data['student']->due           = $totalSumDue;  
        // $data['student']->fine = $data['student']->feeCollect()->sum('fine');
        //$data['student']->paid_amount = $data['fee_master']->sum('amount_paid');
       $data['student']->balance =$totalSumDue;  
       // return $data['student'];
       // dd();
        // $data['fee_master'] = $data['student']->feeMaster()->orderBy('fee_due_date','desc')->get();
        // $data['fee_collection'] = $data['student']->feeCollect()->get();

        // /*total Calculation on Table Foot*/
        // $data['student']->fee_amount = $data['student']->feeMaster()->sum('fee_amount');
        // $data['student']->discount = $data['student']->feeCollect()->sum('discount');
        // $data['student']->fine = $data['student']->feeCollect()->sum('fine');
        // $data['student']->paid_amount = $data['student']->feeCollect()->sum('paid_amount');
        // $data['student']->balance =
        //     ($data['student']->fee_amount - ($data['student']->paid_amount + $data['student']->discount))+ $data['student']->fine;

        $data['document'] = Document::select('id', 'member_type','member_id', 'title', 'file','description', 'status')
            ->where('member_type','=','student')
            ->where('member_id','=',$data['student']->id)
            ->orderBy('created_by','desc')
            ->get();

        $data['attendance'] = Attendance::select('attendances.id', 'attendances.attendees_type', 'attendances.link_id',
            'attendances.years_id', 'attendances.months_id', 'attendances.day_1', 'attendances.day_2', 'attendances.day_3',
            'attendances.day_4', 'attendances.day_5', 'attendances.day_6', 'attendances.day_7', 'attendances.day_8',
            'attendances.day_9', 'attendances.day_10', 'attendances.day_11', 'attendances.day_12', 'attendances.day_13',
            'attendances.day_14', 'attendances.day_15', 'attendances.day_16', 'attendances.day_17', 'attendances.day_18',
            'attendances.day_19', 'attendances.day_20', 'attendances.day_21', 'attendances.day_22', 'attendances.day_23',
            'attendances.day_24', 'attendances.day_25', 'attendances.day_26', 'attendances.day_27', 'attendances.day_28',
            'attendances.day_29', 'attendances.day_30', 'attendances.day_31')
            ->where('attendances.attendees_type', 1)
            ->where('attendances.link_id',$data['student']->id)
            ->join('students as s', 's.id', '=', 'attendances.link_id')
            ->orderBy('attendances.years_id','asc')
            ->orderBy('attendances.months_id','asc')
            ->get();

        $data['lib_member'] = LibraryMember::where([
            'library_members.user_type' => 1,
            'library_members.member_id' => $data['student']->id,
        ])
            ->first();

        if($data['lib_member'] != null){
            $data['books_history'] = $data['lib_member']->libBookIssue()->select('book_issues.id', 'book_issues.member_id',
                'book_issues.book_id',  'book_issues.issued_on', 'book_issues.due_date','book_issues.return_date', 'b.book_masters_id',
                'b.book_code', 'bm.title','bm.categories')
                ->join('books as b','b.id','=','book_issues.book_id')
                ->join('book_masters as bm','bm.id','=','b.book_masters_id')
                ->orderBy('book_issues.issued_on', 'desc')
                ->get();
        }

        $data['note'] = Note::select('created_at', 'id', 'member_type','member_id','subject', 'note', 'status')
            ->where('member_type','=','student')
            ->where('member_id','=', $data['student']->id)
            ->orderBy('created_at','desc')
            ->get();

        $data['hostel_history'] = ResidentHistory::select('resident_histories.years_id', 'resident_histories.hostels_id',
            'resident_histories.rooms_id', 'resident_histories.beds_id',
            'resident_histories.history_type','resident_histories.created_at')
            ->where([['r.user_type','=', 1],['r.member_id','=',$data['student']->id]])
            ->join('residents as r', 'r.id', '=', 'resident_histories.residents_id')
            ->join('beds as b', 'b.id', '=', 'resident_histories.beds_id')
            ->orderBy('resident_histories.created_at')
            ->get();

        //$data['academicInfos'] = $data['student']->academicInfo()->orderBy('sorting_order','asc')->get();
    $data['academicInfos'] =AcademicInfo::select('*')->Where('students_id', $data['student']->id)->orderBy('sorting_order','asc')->get();
     /*Exam Score*/
        /*filter student with schedule subject markledger*/
       // $subject = $data['student']->markLedger()
            //->select( 'exam_schedule_id',  'obtain_mark_theory', 'obtain_mark_practical','absent')
           // ->get();

        //filter subject and joint mark from schedules;
            // $filteredSubject  = $subject->filter(function ($subject, $key) {
            //     $joinSub = $subject->examSchedule()
            //         ->first();

            //     if($joinSub){
            //         $subject->subjects_id = $joinSub->subjects_id;
            //         $subject->full_mark_theory = $joinSub->full_mark_theory;
            //         $subject->pass_mark_theory = $joinSub->pass_mark_theory;
            //         $subject->full_mark_practical = $joinSub->full_mark_practical;
            //         $subject->pass_mark_practical = $joinSub->pass_mark_practical;

            //         /*attach exam detail*/
            //         $subject->years_id = $joinSub->years_id;
            //         $subject->months_id = $joinSub->months_id;
            //         $subject->exams_id = $joinSub->exams_id;
            //         $subject->faculty_id = $joinSub->faculty_id;
            //         $subject->semesters_id = $joinSub->semesters_id;
            //         return $subject;
            //     }
            // });

        //$data['student']->markLedger->subjects = $filteredSubject;
        // $data['examScore'] = $data['student']->markLedger->subjects->groupBY('months_id');
         $data['examScore'] =[];


        /*Transport History*/
        $data['transport_history'] = TransportHistory::select('transport_histories.id', 'transport_histories.years_id',
            'transport_histories.routes_id', 'transport_histories.vehicles_id',  'transport_histories.history_type',
            'transport_histories.created_at','tu.member_id','tu.user_type')
            ->where([['tu.user_type','=', 1],['tu.member_id','=',$data['student']->id]])
            ->join('transport_users as tu','tu.id','=','transport_histories.travellers_id')
            ->orderBy('transport_histories.created_at')
            ->get();


        //login credential
        $data['student_login'] = User::where([['role_id',6],['hook_id',$data['student']->id]])->first();
        $data['guardian_login'] = User::where([['role_id',7],['hook_id',$data['student']->guardian_id]])->first();

        $data['url'] = URL::current();
        // return $fee_result;
        // dd();
        return view(parent::loadDataToView($this->view_path.'.detail.index'),compact('data','fee_result','paid_result_arr','disc_result_arr','due','feelist','failedlist'));
    }


    public function edit(Request $request, $id)
    {
        $org_id = Auth::user()->org_id;
        if(Session::has('activeBranch')){
            $branch_id = Session::get('activeBranch');
        }else{ $branch_id = Auth::user()->branch_id; }
        $branches = Branch:: select('branch_name','branch_title','id','org_id')->where('org_id', $org_id)->get();
        $branch = Branch:: select('branch_name','branch_title','id','org_id')->where('id', $branch_id)->get();
        $data = [];
        
        $sessions = DB::table('session')->select('id', 'session_name')->pluck('session_name', 'id')->toArray(); //get();
        $sessions=array_prepend($sessions, '----Select Session----', '');

        $data['row'] = Student::select('students.id','students.category_id','students.reg_no', 'students.reg_date', 'students.university_reg',
            'students.faculty','students.semester', 'students.academic_status', 'students.first_name', 'students.zip', 'students.middle_name',
            'students.last_name', 'students.session_id', 'students.date_of_birth', 'students.gender', 'students.blood_group', 'students.nationality',
            'students.mother_tongue', 'students.email', 'students.extra_info','students.student_image', 'students.status','students.indose_number','students.passport_no',
            'pd.grandfather_first_name',
            'pd.grandfather_middle_name', 'pd.grandfather_last_name', 'pd.father_first_name', 'pd.father_middle_name',
            'pd.father_last_name', 'pd.father_eligibility', 'pd.father_occupation', 'pd.father_office', 'pd.father_office_number',
            'pd.father_residence_number', 'pd.father_mobile_1', 'pd.father_mobile_2', 'pd.father_email', 'pd.mother_first_name',
            'pd.mother_middle_name', 'pd.mother_last_name', 'pd.mother_eligibility', 'pd.mother_occupation', 'pd.mother_office',
            'pd.mother_office_number', 'pd.mother_residence_number', 'pd.mother_mobile_1', 'pd.mother_mobile_2', 'pd.mother_email',
            'pd.father_image', 'pd.mother_image',
            'ai.address', 'ai.state', 'ai.country', 'ai.temp_address', 'ai.temp_state', 'ai.temp_country', 'ai.home_phone',
            'ai.mobile_1', 'ai.mobile_2', 'gd.id as guardians_id', 'gd.guardian_first_name', 'gd.guardian_middle_name', 'gd.guardian_last_name',
            'gd.guardian_eligibility', 'gd.guardian_occupation', 'gd.guardian_office', 'gd.guardian_office_number',
            'gd.guardian_residence_number', 'gd.guardian_mobile_1', 'gd.guardian_mobile_2', 'gd.guardian_email',
            'gd.guardian_relation', 'gd.guardian_address', 'gd.guardian_image')
            ->where('students.id','=',$id)
            ->join('parent_details as pd', 'pd.students_id', '=', 'students.id')
            ->join('addressinfos as ai', 'ai.students_id', '=', 'students.id')
            ->join('student_guardians as sg', 'sg.students_id','=','students.id')
            ->join('guardian_details as gd', 'gd.id', '=', 'sg.guardians_id')
            ->first();

        if (!$data['row']){ return parent::invalidRequest(); }else{ $admData[0] = $data['row']; }

        $data['faculties'] = $this->activeFaculties();


        $semester = Semester::select('id', 'semester')->where('id','=',$data['row']->semester)->Active()->pluck('semester','id')->toArray();
        $data['semester'] = array_prepend($semester,'Select Semester',0);


        $academicStatus = StudentStatus::select('id', 'title')->Active()->pluck('title','id')->toArray();
        $data['academic_status'] = array_prepend($academicStatus,'Select Status',0);

       $data['academicInfo'] =AcademicInfo::select('*')->Where('students_id', $id)->orderBy('sorting_order','asc')->get();
        $data['academicInfo-html'] = view($this->view_path.'.registration.includes.forms.academic_tr_edit', [
            'academicInfos' => $data['academicInfo']
        ])->render();
     
        $courses = DB::table('faculties')->select('id', 'faculty')->get();
        $Semester = Semester:: select('semester','id')->get();
        $category=category_model::Select('id', 'category_name')->pluck('category_name', 'id')->toArray();
        $category=array_prepend($category, '----Select Category----', '');
        return view(parent::loadDataToView($this->view_path.'.registration.edit'), compact('data','branches','branch', 'sessions', 'courses', 'Semester', 'admData', 'category'));
    }

    public function update(EditValidation $request, $id)
    {
        //return $request;
        if (!$row = Student::find($id))
            return parent::invalidRequest();

        if ($request->hasFile('student_main_image')) {
            // remove old image from folder
            if (file_exists($this->folder_path.$row->student_image))
                @unlink($this->folder_path.$row->student_image);

            /*upload new student image*/
            $student_image = $request->file('student_main_image');
            $student_image_name = $request->reg_no.'.'.$student_image->getClientOriginalExtension();
            $student_image->move($this->folder_path, $student_image_name);
        }

        $request->request->add(['updated_by' => auth()->user()->id]);
        $request->request->add(['student_image' => isset($student_image_name)?$student_image_name:$row->student_image]);

        $student = $row->update($request->all());

        /*Update Associate Address Info*/
        $row->address()->update([
           'address'    =>  $request->address,
           'state'      =>  $request->state,
           'country'    =>  $request->country,
           'temp_address' =>  $request->temp_address,
           'temp_state' =>  $request->temp_state,
           'temp_country' =>  $request->temp_country,
           'home_phone'   =>  $request->home_phone,
           'mobile_1'   =>  $request->mobile_1,
           'mobile_2'   =>  $request->mobile_2

       ]);

        /*Update Associate Parents Info with Images*/
        $parent = $row->parents()->first();
        $guardian = $row->guardian()->first();

        $parential_image_path = public_path().DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'parents'.DIRECTORY_SEPARATOR;
        if ($request->hasFile('father_main_image')){
            // remove old image from folder
            if (file_exists($parential_image_path.$parent->father_image))
                @unlink($parential_image_path.$parent->father_image);

            $father_image = $request->file('father_main_image');
            $father_image_name = $row->reg_no.'_father.'.$father_image->getClientOriginalExtension();
            $father_image->move($parential_image_path, $father_image_name);
        }

        if ($request->hasFile('mother_main_image')){
            // remove old image from folder
            if (file_exists($parential_image_path.$parent->mother_image))
                @unlink($parential_image_path.$parent->mother_image);

            $mother_image = $request->file('mother_main_image');
            $mother_image_name = $row->reg_no.'_mother.'.$mother_image->getClientOriginalExtension();
            $mother_image->move($parential_image_path, $mother_image_name);
        }


        if ($request->hasFile('guardian_main_image')){
            // remove old image from folder
            if (file_exists($parential_image_path.$guardian->guardian_image))
                @unlink($parential_image_path.$guardian->guardian_image);

            $guardian_image = $request->file('guardian_main_image');
            $guardian_image_name = $row->reg_no.'_guardian.'.$guardian_image->getClientOriginalExtension();
            $guardian_image->move($parential_image_path, $guardian_image_name);
        }


        $father_image_name = isset($father_image_name)?$father_image_name:$parent->father_image;
        $mother_image_name = isset($mother_image_name)?$mother_image_name:$parent->mother_image;
        $guardian_image_name = isset($guardian_image_name)?$guardian_image_name:$guardian->guardian_image;


        $row->parents()->update([
            'grandfather_first_name'    =>  $request->grandfather_first_name,
            'grandfather_middle_name'   =>  $request->grandfather_middle_name,
            'grandfather_last_name'     =>  $request->grandfather_last_name,
            'father_first_name'         =>  $request->father_first_name,
            'father_middle_name'        =>  $request->father_middle_name,
            'father_last_name'          =>  $request->father_last_name,
            'father_eligibility'        =>  $request->father_eligibility,
            'father_occupation'         =>  $request->father_occupation,
            'father_office'             =>  $request->father_office,
            'father_office_number'      =>  $request->father_office_number,
            'father_residence_number'   =>  $request->father_residence_number,
            'father_mobile_1'           =>  $request->father_mobile_1,
            'father_mobile_2'           =>  $request->father_mobile_2,
            'father_email'              =>  $request->father_email,
            'mother_first_name'         =>  $request->mother_first_name,
            'mother_middle_name'        =>  $request->mother_middle_name,
            'mother_last_name'          =>  $request->mother_last_name,
            'mother_eligibility'        =>  $request->mother_eligibility,
            'mother_occupation'         =>  $request->mother_occupation,
            'mother_office'             =>  $request->mother_office,
            'mother_office_number'      =>  $request->mother_office_number,
            'mother_residence_number'   =>  $request->mother_residence_number,
            'mother_mobile_1'           =>  $request->mother_mobile_1,
            'mother_mobile_2'           =>  $request->mother_mobile_2,
            'mother_email'              =>  $request->mother_email,
            'father_image'              =>  $father_image_name,
            'mother_image'              =>  $mother_image_name

        ]);

        //if guardian link modified or not condition
        if($request->guardian_link_id == null){
            $sgd = $row->guardian()->first();
            $guardiansInfo = [
                'guardian_first_name'         =>  $request->guardian_first_name,
                'guardian_middle_name'        =>  $request->guardian_middle_name,
                'guardian_last_name'          =>  $request->guardian_last_name,
                'guardian_eligibility'        =>  $request->guardian_eligibility,
                'guardian_occupation'         =>  $request->guardian_occupation,
                'guardian_office'             =>  $request->guardian_office,
                'guardian_office_number'      =>  $request->guardian_office_number,
                'guardian_residence_number'   =>  $request->guardian_residence_number,
                'guardian_mobile_1'           =>  $request->guardian_mobile_1,
                'guardian_mobile_2'           =>  $request->guardian_mobile_2,
                'guardian_email'              =>  $request->guardian_email,
                'guardian_relation'           =>  $request->guardian_relation,
                'guardian_address'            =>  $request->guardian_address,
                'guardian_image'              =>  $guardian_image_name

            ];
            GuardianDetail::where('id',$sgd->guardians_id)->update($guardiansInfo);
        }else{
            $studentGuardian = StudentGuardian::where('students_id', $row->id)->update([
                'students_id' => $row->id,
                'guardians_id' => $request->guardian_link_id,
            ]);
        }


        /*Academic Info Start*/
        if ($row && $request->has('institution')) {
            foreach ($request->get('institution') as $key => $institute) {
                $academicInfoExist = AcademicInfo::where([['students_id','=',$row->id],['institution','=',$institute]])->first();
                if($academicInfoExist){
                    $academicInfoUpdate = [
                        'students_id' => $row->id,
                        'institution' => $institute,
                        'board' => $request->get('board')[$key],
                        'pass_year' => $request->get('pass_year')[$key],
                        
                        //'symbol_no' => $request->get('symbol_no')[$key],
                        'percentage' => $request->get('percentage')[$key],
                        'division_grade' => $request->get('division_grade')[$key],
                        'major_subjects' => $request->get('major_subjects')[$key],
                        'remark' => $request->get('remark')[$key],
                        'sorting_order' => $key+1,
                        'last_updated_by' => auth()->user()->id
                    ];
                    $academicInfoExist->update($academicInfoUpdate);
                }else{
                    AcademicInfo::create([
                        'students_id' => $row->id,
                        'institution' => $institute,
                        'board' => $request->get('board')[$key],
                        'pass_year' => $request->get('pass_year')[$key],
                        //'symbol_no' => $request->get('symbol_no')[$key],
                        'percentage' => $request->get('percentage')[$key],
                        'division_grade' => $request->get('division_grade')[$key],
                        'major_subjects' => $request->get('major_subjects')[$key],
                        'remark' => $request->get('remark')[$key],
                        'sorting_order' => $key+1,
                        'created_by' => auth()->user()->id,
                    ]);
                }

            }
        }
        /*Academic Info End*/

        $request->session()->flash($this->message_success, $this->panel. ' Updated Successfully.');
        return redirect()->route($this->base_route);

    }

    public function delete(Request $request, $id)
    {
        if (!$row = Student::find($id)) return parent::invalidRequest();

        $row->delete();

        $request->session()->flash($this->message_success, $this->panel.' Deleted Successfully.');
        return redirect()->route($this->base_route);
    }

    public function active(request $request, $id)
    {

        if (!$row = Student::find($id)) return parent::invalidRequest();
            

        $request->request->add(['status' => 'active']);

        $row->update($request->all());
       

        $request->session()->flash($this->message_success, $row->reg_no.' '.$this->panel.' Active Successfully.');
        return redirect()->route($this->base_route);
    }

    public function inActive(request $request, $id)
    {
        if (!$row = Student::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'in-active']);
        $row->update($request->all());

        //in active student login detail
        $login_detail = User::where([['role_id',6],['hook_id',$row->id]])->first();
        $request->request->add(['status' => 'in-active']);
        // $login_detail->update($request->all());

        // in active guardian login detail
        //$login_detail = User::where([['role_id',7],['hook_id',$row->id]])->first();
        $request->request->add(['status' => 'in-active']);
        //$login_detail->update($request->all());

        $request->session()->flash($this->message_success, $row->reg_no.' '.$this->panel.' In-Active Successfully.');
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
                            $row = Student::find($row_id);
                            if ($row) {
                                $row->status = $request->get('bulk_action') == 'active'?'active':'in-active';
                                $row->save();
                            }
                            break;
                        case 'delete':
                            $row = Student::find($row_id);
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

    public function findSemester(Request $request)
    {
        $response = [];
        $response['error'] = true;

        if ($request->has('faculty_id')) {
            $faculty = Faculty::select('faculties.id','faculties.faculty', 'faculties.slug', 'faculties.status','fs.semester_id','fs.faculty_id')
                ->where('faculties.id','=',$request->faculty_id)
               ->join('faculty_semester as fs', 'faculties.id', '=', 'fs.faculty_id')
               ->join('semesters as s', 'fs.semester_id', '=', 's.id')
                ->first();

            if ($faculty) {

                $response['semester'] = $faculty->semester()->select('semesters.id', 'semesters.semester')->get();

                $response['error'] = false;
                $response['success'] = 'Semester/Sec. Available For This Faculty/Class.';
            } else {
                $response['error'] = 'No Any Semester Assign on This Faculty/Class.';
            }

        } else {
            $response['message'] = 'Invalid request!!';
        }
        return response()->json(json_encode($response));
    }

    public function transfer(Request $request)
    {
        $data = [];
        if($request->all()) {
            $data['student'] = StudentPromotion::select('students.id', 'students.reg_no', 'students.reg_date', 'students.first_name', 'student_detail_sessionwise.course_id as faculty', 'students.semester', 'students.status', 'student_detail_sessionwise.session_id as old_ssn', 'student_detail_sessionwise.course_id', 'student_detail_sessionwise.Semester')
                ->where(function ($query) use ($request) {

                    if ($request->has('reg_no') && $request->reg_no) {
                        $query->where('students.reg_no', 'like', '%' . $request->reg_no . '%');
                        $this->filter_query['students.reg_no'] = $request->reg_no;
                    }

if ($request->reg_start_date && $request->reg_end_date) {
    $start_date=date("Y-m-d", strtotime($request->get('reg_start_date')));
    $end_date=date("Y-m-d", strtotime($request->get('reg_end_date')));
    $query->whereBetween('students.reg_date', [$start_date, $end_date]);
    $this->filter_query['reg_start_date'] = $request->get('reg_start_date');
    $this->filter_query['reg_end_date'] = $request->get('reg_end_date');
} elseif ($request->reg_start_date) {
    $query->where('students.reg_date', '>=', $start_date);
    $this->filter_query['reg_start_date'] = $request->get('reg_start_date');
} elseif ($request->reg_end_date) {
    $query->where('students.reg_date', '<=', $end_date);
    $this->filter_query['reg_end_date'] = $request->get('reg_end_date');
}

                    if ($request->faculty) {
                        $query->where('student_detail_sessionwise.course_id', '=', $request->faculty);
                        $this->filter_query['students.faculty'] = $request->faculty;
                    }

                    if ($request->sesson) {
                        $query->where('student_detail_sessionwise.session_id', '=', $request->sesson);
                        $this->filter_query['students.session_id'] = $request->faculty;
                    }

                    if ($request->semester) {
                        $query->where('student_detail_sessionwise.semester', '=', $request->semester);
                        $this->filter_query['students.semester'] = $request->semester;
                    }

                    if ($request->status != "all") {
                        $query->where('students.status', $request->status == 'active' ? 1 : 0);
                        $this->filter_query['students.status'] = $request->get('status');
                    }

                })->leftJoin('students', function($q){
                    $q->on('student_detail_sessionwise.student_id', '=', 'students.id');
                })->get();
        }

        $data['faculties'] = $this->activeFaculties();

        $academicStatus = StudentStatus::select('id', 'title')->Active()->pluck('title','id')->toArray();
        $data['student_status'] = array_prepend($academicStatus,'Select Status',0);

        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;

        $course_options=$this->course_drop();
        $course_options=array_prepend($course_options, '--Select Course--', '');

        $ssn_list=DB::table('session')->select('id', 'session_name')->pluck('session_name', 'id')->toArray();
        $ssn_list=array_prepend($ssn_list, 'Select Session', '');

        $semester_list=DB::table('semesters')->select('id', 'semester')->pluck('semester', 'id')->toArray();
        $semester_list=array_prepend($semester_list, '--Select Semester--', '');
        
        return view(parent::loadDataToView($this->view_path.'.transfer.index'), compact('data', 'course_dropdown', 'ssn_dropdown','semester_dropdown', 'ssn_list', 'course_options', 'semester_list'));
    }

    public function create_promotion(Request $req){
        $session=$req->input('sessions');
        $old_sessions=$req->input('old_sessions');
        $status=$req->input('status');
        $course=$req->input('cours'); //die($status);
        $scholar=$req->input('scholar');
        $semester = $req->semester;
        $dop=date('Y-m-d h:m:s');
        $created_by = auth()->user()->id;
        $student_details=Student::where('id', $scholar)->first();
        $promobj = new StudentPromotion;
        $ret_old=$promobj->where('student_id', $scholar)->where('session_id', $old_sessions)->update(['Status'=>$status, 'created_by'=>$created_by, 'promoted_session'=>$session, 'promoted_course'=>$course]);
        $exsts=$promobj->where(['student_id'=>$scholar, 'session_id'=>$session])->get();
        if(!count($exsts)){
            $ret=$promobj->insert(['course_id'=>$course, 'student_id'=>$scholar, 'session_id'=>$session, 'Semester'=>$semester, 'created_by'=>$created_by]);
        }else{
            $ret=$promobj->update(['student_id'=>$scholar, 'session_id'=>$session], ['course_id'=>$course, 'student_id'=>$scholar, 'session_id'=>$session, 'Semester'=>$semester, 'created_by'=>$created_by]);
        }
        return $student_details->first_name." Has Been Promoted Successfully.";
    }

    public function transfering(Request $request)
    {
        if($request->faculty > 0 && $request->semester_select > 0){
             if ($request->has('chkIds')) {
                foreach ($request->get('chkIds') as $row_id) {
                    $row = Student::find($row_id);
                    if ($row) {
                        $row->faculty = $request->get('faculty');
                        $row->semester = $request->get('semester_select');
                        $row->academic_status = $request->get('student_status');
                        $row->save();
                    }
                }
             }else {
                 $request->session()->flash($this->message_warning, 'Please, Check at least one row.');
                 return redirect()->route($this->base_route.'.transfer');
             }

            $faculty_title = ViewHelper::getFacultyTitle( $request->faculty );
            $semester_title = ViewHelper::getSemesterTitle( $request->semester_select );
            $request->session()->flash($this->message_success, 'Students Transfer on : '.$faculty_title.' / '.$semester_title.' Successfully.');

        }else{
            $request->session()->flash($this->message_success, 'Please Choose Faculty and Semester Correctly.');
        }
        return redirect()->route($this->base_route.'.transfer');
    }

    public function academicInfoHtml()
    {
        $response['html'] = view($this->view_path.'.registration.includes.forms.academic_tr')->render();
        return response()->json(json_encode($response));
    }

    public function deleteAcademicInfo(Request $request, $id)
    {
        if (!$row = AcademicInfo::find($id)) return parent::invalidRequest();

        $row->delete();

        $request->session()->flash($this->message_success,'Academic Info Deleted Successfully.');
        return redirect()->back();
    }


    /*guardian's info link*/
    public function guardianNameAutocomplete(Request $request)
    {
        if ($request->has('q')) {

            $guardians = GuardianDetail::select('id','guardian_first_name',
                'guardian_middle_name', 'guardian_last_name', 'guardian_eligibility',
                'guardian_occupation', 'guardian_office', 'guardian_office_number',
                'guardian_residence_number', 'guardian_mobile_1', 'guardian_mobile_2',
                'guardian_email', 'guardian_relation', 'guardian_address','guardian_image')
                
                ->where('guardian_first_name', 'like', '%'.$request->get('q').'%')
                ->orWhere('guardian_middle_name', 'like', '%'.$request->get('q').'%')
                ->orWhere('guardian_last_name', 'like', '%'.$request->get('q').'%')
                ->orWhere('guardian_mobile_1', 'like', '%'.$request->get('q').'%')
                ->orWhere('guardian_mobile_2', 'like', '%'.$request->get('q').'%')
                ->orWhere('guardian_email', 'like', '%'.$request->get('q').'%')
                ->get();

            $response = [];
            foreach ($guardians as $guardian) {
                $response[] = ['id' => $guardian->id, 'text' => $guardian->guardian_first_name.' '.$guardian->guardian_middle_name.' '.$guardian->guardian_last_name];
            }

            return json_encode($response);
        }

        abort(501);
    }

    public function guardianInfo(Request $request)
    {
        $response = [];
        $response['error'] = true;
        if ($request->has('id')) {
            if ($guardianInfo = GuardianDetail::find($request->get('id'))) {
                $response['error'] = false;
                $response['html'] = view($this->view_path.'.registration..includes.forms.pull-guardian-info', [
                    'guardianInfo' => $guardianInfo,
                ])->render();
                $response['message'] = 'Operation successful.';

            } else{
                $response['message'] = $request->get('subject_id').'Invalid request!!';
            }
        } else{
            $response['message'] = $request->get('id').'Invalid request!!';
        }

        return response()->json(json_encode($response));
    }



    /*bulk import*/
    public function importStudent()
    {
        return view(parent::loadDataToView($this->view_path.'.registration.import'));
    }


    public function handleImportStudent(Request $request)
    {    
        $current_session=Session::get('activeSession');
        if(Session::has('activeBranch'))
        {
            $branch_id = Session::get('activeBranch');
        }
        else{ $branch_id = Auth::user()->branch_id; }
                    
        $org_id = Auth::user()->org_id;
        $branch = Branch:: select('branch_name','branch_title','id','org_id')->where('id', $branch_id)->get();   //
        $branchName = $branch[0]->branch_name;
        $branchId = $branch[0]->id;

        //file present or not validation
        $validator = Validator::make($request->all(), [
            'file' => 'required'
        ]);
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator);
        }

        $file = $request->file('file');
        $csvData = file_get_contents($file);
        $rows = array_map("str_getcsv", explode("\n", $csvData));
        $header = array_shift($rows);

        foreach ($rows as $row) {
        
            if (count($header) != count($row)) {
                continue;
            }

            $row = array_combine($header, $row);

            $randomString = $this->reG_no(); // Student Reg No.

            $mobileText     = $row['mobile_1'];
            $mobAr          = explode(",",$mobileText);
            $mobile_number_1  = (isset($mobAr[0]) && $mobAr[0]!="")? $mobAr[0] : "";
            $mobile_number_2  = (isset($mobAr[1]) && $mobAr[1]!="")? $mobAr[1] : "";

            //Staff validation
            $validator = Validator::make($row, [
                'reg_date'                      => 'required',
                'faculty'                       => 'required',
                'semester'                      => 'required',
                'first_name'                    => 'required ', 
                'date_of_birth'                 => 'required',
                'gender'                        => 'required',
                'nationality'                   => 'required ',
                'country'                       => 'required ',
            ]);

            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withErrors($validator);
            }

            //Faculty id
            $catArr = category_model::where('category_name',$row['category_name'])->first();
            if($catArr){
                $catId = $catArr->id;
            }else{
                $catId = "";
            }

            //Faculty id
            $faculty = Faculty::where('faculty',$row['faculty'])
            ->where('branch_id',$branch_id)
            ->first();
            if($faculty){
                $facultyId = $faculty->id;
            }else{
                $facultyId = "";
            }

            //Semester id
            $semester = Semester::where('semester',$row['semester'])->first();
            if($semester){
                $semesterId = $semester->id;
            }else{
                $semesterId = "";
            }

            //Academic Status
            $academicStatus = StudentStatus::where('title',$row['academic_status'])->first();
            if($academicStatus){
                $academicStatusId = $academicStatus->id;
            }else{
                $academicStatus = StaffDesignation::create([
                    'title' => strtoupper($row['academic_status']),
                    'created_by' => auth()->user()->id
                ]);

                $academicStatusId = $academicStatus->id;
            }
            
            $reg_date       = date("Y-m-d", strtotime($row['reg_date']))." 00:00:00";
            $date_of_birth       = date("Y-m-d", strtotime($row['date_of_birth']))." 00:00:00"; 

            //Student import
            $student = Student::create([
                "reg_no"                => $randomString,
                "reg_date"              => $reg_date,
                "university_reg"        => $row['university_reg'],
                "faculty"               => $facultyId,
                "semester"              => $semesterId,
                "academic_status"       => $academicStatusId,
                "first_name"            => $row['first_name'], 
                "date_of_birth"         => $date_of_birth,
                "gender"                => $row['gender'],
                "blood_group"           => $row['blood_group'],
                "nationality"           => $row['nationality'],
                "mother_tongue"         => $row['mother_tongue'],
                "email"                 => $randomString.'@asha.ac.in',
                "branch_id"             => $branch_id,
                "org_id"                => $org_id,
                "category_id"           => $catId,
                "session_id"            => $current_session,
                "status"                => 1,
                'created_by'            => auth()->user()->id
            ]);

            if($student){
                // Student detail session wise entry for current session
                $promobj = new StudentPromotion;
                $exsts   = $promobj->where(['student_id'=>$student->id, 'session_id'=>$current_session])->get();
                if(!count($exsts)){
                    $ret=$promobj->insert(['course_id'=>$facultyId, 'student_id'=>$student->id, 'session_id'=>$current_session, 'Semester'=>$semesterId, 'created_by'=>auth()->user()->id]);
                }

                //address info
                Addressinfo::create([
                    "students_id"           => $student->id,
                    "home_phone"            => $row['home_phone'],
                    "mobile_1"              => $mobile_number_1,
                    "mobile_2"              => $mobile_number_2,
                    "address"               => $row['address'],
                    "state"                 => $row['state'],
                    "country"               => $row['country'],
                    "temp_address"          => $row['temp_address'],
                    "temp_state"            => $row['temp_state'],
                    "temp_country"          => $row['temp_country'],
                    'created_by'            => auth()->user()->id
                ]);

                //parents detail
                ParentDetail::create([
                    "students_id"               => $student->id,
                    "home_phone"                => $row['home_phone'],
                    "grandfather_first_name"    => $row['grandfather_first_name'],
                    "grandfather_middle_name"   => $row['grandfather_middle_name'],
                    "grandfather_last_name"     => $row['grandfather_last_name'],
                    "father_first_name"         => $row['father_first_name'],
                    "father_middle_name"        => $row['father_middle_name'],
                    "father_last_name"          => $row['father_last_name'],
                    "father_eligibility"        => $row['father_eligibility'],
                    "father_occupation"         => $row['father_occupation'],
                    "father_office"             => $row['father_office'],
                    "father_office_number"      => $row['father_office_number'],
                    "father_residence_number"   => $row['father_residence_number'],
                    "father_mobile_1"           => $row['father_mobile_1'],
                    "father_mobile_2"           => $row['father_mobile_2'],
                    "father_email"              => $row['father_email'],
                    "mother_first_name"         => $row['mother_first_name'],
                    "mother_middle_name"        => $row['mother_middle_name'],
                    "mother_last_name"          => $row['mother_last_name'],
                    "mother_eligibility"        => $row['mother_eligibility'],
                    "mother_occupation"         => $row['mother_occupation'],
                    "mother_office"             => $row['mother_office'],
                    "mother_office_number"      => $row['mother_office_number'],
                    "mother_residence_number"   => $row['mother_residence_number'],
                    "mother_mobile_1"           => $row['mother_mobile_1'],
                    "mother_mobile_2"           => $row['mother_mobile_2'],
                    "mother_email"              => $row['mother_email'],
                    'created_by'                => auth()->user()->id
                ]);

                //Guardian detail
                $guardian = GuardianDetail::create([
                    "students_id"                 => $student->id,
                    "guardian_first_name"         => $row['guardian_first_name'],
                    "guardian_middle_name"        => $row['guardian_middle_name'],
                    "guardian_last_name"          => $row['guardian_last_name'],
                    "guardian_eligibility"        => $row['guardian_eligibility'],
                    "guardian_occupation"         => $row['guardian_occupation'],
                    "guardian_office"             => $row['guardian_office'],
                    "guardian_office_number"      => $row['guardian_office_number'],
                    "guardian_residence_number"   => $row['guardian_residence_number'],
                    "guardian_mobile_1"           => $row['guardian_mobile_1'],
                    "guardian_mobile_2"           => $row['guardian_mobile_2'],
                    "guardian_email"              => $row['guardian_email'],
                    "guardian_relation"           => $row['guardian_relation'],
                    "guardian_address"            => $row['guardian_address'],
                    'created_by'                  => auth()->user()->id
                ]);

                /*student guardian link*/

                if($guardian){
                   StudentGuardian::create([
                        'students_id' => $student->id,
                        'guardians_id' => $guardian->id,
                    ]);
                }

                // Update student status = 1 - Active
                $stdStatus = Student::where('id', $student->id)->update(['status' => 1,]);

                $feeAmtPaid = $row['fee_amount'];
                $feePaidBy  = $row['payment_mode'];
                $feeRefNo   = $row['payment_reference_no'];
                // Check for payment/fee and update/insert the same
                if($feeAmtPaid != "" && $feeAmtPaid > 0){
                    $qry = "SELECT * FROM assign_fee
                            where (student_id=157 OR student_id=0) AND session_id=$current_session and branch_id=$branch_id and course_id=$facultyId and fee_amount > 1000 limit 1";
                    $results      = DB::select($qry); 
                    $feeAssiInfo  = $results[0]; 
                    
                    $feeMastIdArr = $feeAssiInfo->id; 
                    $FeeHeadIds   = $feeAssiInfo->fee_head_id;
                    
                    $date         = $reg_date;
                    $receipt_no   = $this->reciept_no(); 

                    if($feeAmtPaid > 0){ 
                        DB::Table('collect_fee')->Insert([
                            'created_by' =>auth()->user()->id,
                            'reciept_date' =>$date, 
                            'student_id' => $student->id,
                            'assign_fee_id' =>$feeMastIdArr,
                            'amount_paid' => $feeAmtPaid,
                            'payment_type' =>$feePaidBy,
                            'reference' =>$feeRefNo,
                            'remarks' =>'',
                            'status' =>1,
                            'reciept_no' =>$receipt_no  
                        ]);
                    }
                }
                     
            }

        }

        $request->session()->flash($this->message_success,'Students imported Successfully');
        return redirect()->route($this->base_route);

    }

    public function handleImportStudent_OLD_BKP(Request $request)
    {
        //file present or not validation
        $validator = Validator::make($request->all(), [
            'file' => 'required'
        ]);
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator);
        }

        $file = $request->file('file');
        $csvData = file_get_contents($file);
        $rows = array_map("str_getcsv", explode("\n", $csvData));
        $header = array_shift($rows);

        foreach ($rows as $row) {
            if (count($header) != count($row)) {
                continue;
            }

            $row = array_combine($header, $row);


            //Staff validation
            $validator = Validator::make($row, [
                'reg_no'                        => 'required  | max:15 | unique:students,reg_no',
                'reg_date'                      => 'required',
                'faculty'                       => 'required',
                'semester'                      => 'required',
                'first_name'                    => 'required | max:15',
                'last_name'                     => 'required | max:15',
                'date_of_birth'                 => 'required',
                'gender'                        => 'required',
                'nationality'                   => 'required | max:15',
                'address'                       => 'required | max:100',
                'state'                         => 'required | max:25',
                'country'                       => 'required | max:25',
                'temp_address'                  => 'required | max:100',
                'temp_state'                    => 'required | max:25',
                'temp_country'                  => 'required | max:25',
                'email'                         => 'required | max:100 | unique:students,email',
                'extra_info'                    => 'max:100',
                'home_phone'                    => 'max:15',
                'mobile_1'                      => 'max:15',
                'mobile_2'                      => 'max:15',
                'grandfather_first_name'        => 'max:15',
                'grandfather_middle_name'       => 'max:15',
                'grandfather_last_name'         => 'max:15',
                'father_first_name'             => 'max:15',
                'father_middle_name'            => 'max:15',
                'father_last_name'              => 'max:15',
                'father_eligibility'            => 'max:50',
                'father_occupation'             => 'max:50',
                'father_office'                 => 'max:100',
                'father_office_number'          => 'max:15',
                'father_residence_number'       => 'max:15',
                'father_mobile_1'               => 'max:15',
                'father_mobile_2'               => 'max:15',
                'father_email'                  => 'max:100',
                'mother_first_name'             => 'max:15',
                'mother_middle_name'            => 'max:15',
                'mother_last_name'              => 'max:15',
                'mother_eligibility'            => 'max:50',
                'mother_occupation'             => 'max:50',
                'mother_office'                 => 'max:100',
                'mother_office_number'          => 'max:15',
                'mother_residence_number'       => 'max:15',
                'mother_mobile_1'               => 'max:15',
                'mother_mobile_2'               => 'max:15',
                'mother_email'                  => 'max:100',
            ]);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withErrors($validator);
            }


            //Faculty id
            $faculty = Faculty::where('faculty',$row['faculty'])->first();
            if($faculty){
                $facultyId = $faculty->id;
            }else{
                $facultyId = "";
            }

            //Semester id
            $semester = Semester::where('semester',$row['semester'])->first();
            if($semester){
                $semesterId = $semester->id;
            }else{
                $semesterId = "";
            }

            //Academic Status
            $academicStatus = StudentStatus::where('title',$row['academic_status'])->first();
            if($academicStatus){
                $academicStatusId = $academicStatus->id;
            }else{
                $academicStatus = StaffDesignation::create([
                    'title' => strtoupper($row['academic_status']),
                    'created_by' => auth()->user()->id
                ]);

                $academicStatusId = $academicStatus->id;
            }


            //Student import
            $student = Student::create([
                "reg_no"                => $row['reg_no'],
                "reg_date"              => $row['reg_date'],
                "university_reg"        => $row['university_reg'],
                "faculty"               => $facultyId,
                "semester"              => $semesterId,
                "academic_status"       => $academicStatusId,
                "first_name"            => $row['first_name'],
                "middle_name"           => $row['middle_name'],
                "last_name"             => $row['last_name'],
                "date_of_birth"         => $row['date_of_birth'],
                "gender"                => $row['gender'],
                "blood_group"           => $row['blood_group'],
                "nationality"           => $row['nationality'],
                "mother_tongue"         => $row['mother_tongue'],
                "email"                 => $row['email'],
                "extra_info"            => $row['extra_info'],
                'created_by'            => auth()->user()->id

            ]);


            if($student){
                //address info
                Addressinfo::create([
                    "students_id"           => $student->id,
                    "home_phone"            => $row['home_phone'],
                    "mobile_1"              => $row['mobile_1'],
                    "mobile_2"              => $row['mobile_2'],
                    "address"               => $row['address'],
                    "state"                 => $row['state'],
                    "country"               => $row['country'],
                    "temp_address"          => $row['temp_address'],
                    "temp_state"            => $row['temp_state'],
                    "temp_country"          => $row['temp_country'],
                    'created_by'            => auth()->user()->id
                ]);

                //parents detail
                ParentDetail::create([
                    "students_id"               => $student->id,
                    "home_phone"                => $row['home_phone'],
                    "grandfather_first_name"    => $row['grandfather_first_name'],
                    "grandfather_middle_name"   => $row['grandfather_middle_name'],
                    "grandfather_last_name"     => $row['grandfather_last_name'],
                    "father_first_name"         => $row['father_first_name'],
                    "father_middle_name"        => $row['father_middle_name'],
                    "father_last_name"          => $row['father_last_name'],
                    "father_eligibility"        => $row['father_eligibility'],
                    "father_occupation"         => $row['father_occupation'],
                    "father_office"             => $row['father_office'],
                    "father_office_number"      => $row['father_office_number'],
                    "father_residence_number"   => $row['father_residence_number'],
                    "father_mobile_1"           => $row['father_mobile_1'],
                    "father_mobile_2"           => $row['father_mobile_2'],
                    "father_email"              => $row['father_email'],
                    "mother_first_name"         => $row['mother_first_name'],
                    "mother_middle_name"        => $row['mother_middle_name'],
                    "mother_last_name"          => $row['mother_last_name'],
                    "mother_eligibility"        => $row['mother_eligibility'],
                    "mother_occupation"         => $row['mother_occupation'],
                    "mother_office"             => $row['mother_office'],
                    "mother_office_number"      => $row['mother_office_number'],
                    "mother_residence_number"   => $row['mother_residence_number'],
                    "mother_mobile_1"           => $row['mother_mobile_1'],
                    "mother_mobile_2"           => $row['mother_mobile_2'],
                    "mother_email"              => $row['mother_email'],
                    'created_by'                => auth()->user()->id
                ]);

                //Guardian detail
                $guardian = GuardianDetail::create([
                    "students_id"                 => $student->id,
                    "guardian_first_name"         => $row['guardian_first_name'],
                    "guardian_middle_name"        => $row['guardian_middle_name'],
                    "guardian_last_name"          => $row['guardian_last_name'],
                    "guardian_eligibility"        => $row['guardian_eligibility'],
                    "guardian_occupation"         => $row['guardian_occupation'],
                    "guardian_office"             => $row['guardian_office'],
                    "guardian_office_number"      => $row['guardian_office_number'],
                    "guardian_residence_number"   => $row['guardian_residence_number'],
                    "guardian_mobile_1"           => $row['guardian_mobile_1'],
                    "guardian_mobile_2"           => $row['guardian_mobile_2'],
                    "guardian_email"              => $row['guardian_email'],
                    "guardian_relation"           => $row['guardian_relation'],
                    "guardian_address"            => $row['guardian_address'],
                    'created_by'                  => auth()->user()->id
                ]);

                /*student guardian link*/

                if($guardian){
                   StudentGuardian::create([
                        'students_id' => $student->id,
                        'guardians_id' => $guardian->id,
                    ]);
                }
            }

        }

        $request->session()->flash($this->message_success,'Students imported Successfully');
        return redirect()->route($this->base_route);

    }


    /*Send Registration Alert*/
    public function registrationConfirm($first_name,$reg_no,$contactNumbers,$email)
    {
        $alert = AlertSetting::select('sms','email','subject','template')->where('event','=','StudentRegistration')->first();
        if(!$alert)
            return back()->with($this->message_warning, "Alert Setting not Setup. Contact Admin For More Detail.");

        $subject = $alert->subject;
        $message = str_replace('{first_name}',$first_name,$alert->template);
        $message = str_replace('{reg_no}',$reg_no, $message);

        $sms = false;
        $email = false;
        /*Now Send SMS On First Mobile Number*/
        if($alert->sms == 1){
            $contactNumbers = array($contactNumbers);
            $contactNumbers = $this->contactFilter($contactNumbers);
            $smssuccess = $this->sendSMS($contactNumbers,$message);
            $sms = true;
        }

        if($alert->email == 1){
            $emailIds = array($email);
            $emailIds = $this->emailFilter($emailIds);
            $emailSuccess = $this->sendEmail($emailIds, $subject, $message);
            $email = true;
        }

       /* if($sms == true || $email== true) {
            return true;
        }*/
    }

    public static function getStudentFeeHeadDueAmout($stdId="",$feeHeadId="",$session="")
    {   
        $qry = "SELECT sum(amount_paid) as totalPaid, max(assign_fee.fee_amount) as NeedToPay
            FROM collect_fee 
            join assign_fee on assign_fee.id=collect_fee.assign_fee_id
            where collect_fee.student_id=$stdId and collect_fee.status=1 and  assign_fee.fee_head_id=$feeHeadId";
            $results = DB::select($qry);
        return $results;
    }

    
}
