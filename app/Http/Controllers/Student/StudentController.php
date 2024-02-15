<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\CollegeBaseController;
use App\Http\Requests\Student\Registration\AddValidation;
use App\Http\Requests\Student\Registration\EditValidation;

use App\Models\AcademicInfo;
use App\Models\Addressinfo;
use App\Models\AlertSetting;
use App\Models\GeneralSetting;
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
use App\Models\school_leaving_certificate;
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
/*Transport info*/
use App\Models\Stoppage;
use App\Models\Route;
use App\Models\Month;
use App\Models\Year;
/*Transport info*/
/*--teacher access--*/
use App\Models\TeacherCoordinator;
/*--teacher access--*/


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
        $data = []; 
        
         /*--teacher access--*/
        $classTeacher= TeacherCoordinator::where('teacher_id',Auth::user()->hook_id)
        ->where('branch_id',$branch_id)
        ->where('record_status',1)
        ->where(function($q)use($request){
            if($request->faculty){
                $q->where('faculty_id',$request->faculty);
            }
        })
        ->where('session_id',Session::get('activeSession'))->pluck('section_id')->toArray();
        
        // dd($classTeacher);
        $classTeacherCourse = $this->getClassTeacherCourse();
        
        
        $ability = $this->getAbility();
        
          /*--teacher access--*/
          
        $data['student'] = StudentPromotion::select('students.id', 'students.reg_no','students.university_reg','students.aadhar_no','students.reg_date', 'student_detail_sessionwise.course_id as faculty', 'student_detail_sessionwise.semester', 'students.academic_status', 'students.first_name', 'students.date_of_birth as dob', 'students.middle_name', 'students.last_name', 'students.status', 'ai.mobile_1','pd.father_first_name as father_name', 'student_detail_sessionwise.session_id','students.indose_number','pd.mother_first_name as mother_name','ai.address','category.category_name','cb.title as batch_title','cb.start_date','cb.end_date','ur.email as login_email','sem.semester as sem_title','students.university_reg')
            ->leftjoin('addressinfos as ai', 'student_detail_sessionwise.student_id', '=', 'ai.students_id')
            ->leftjoin('parent_details as pd', 'student_detail_sessionwise.student_id', '=', 'pd.students_id')

            ->leftJoin('students', function($a){
                $a->on('student_detail_sessionwise.student_id', '=', 'students.id');
            })
            ->leftjoin('course_batches as cb',function($j){
                $j->on('cb.id','=','students.batch_id');
            })
            ->leftjoin('users as ur',function($j){
                $j->on('ur.hook_id','=','students.id')
                ->where('ur.role_id',6);
                
            })
            ->leftjoin('semesters as sem','sem.id','=','student_detail_sessionwise.Semester')
            ->leftjoin('category','category.id','=','students.category_id')
            /*--teacher access--*/
            ->where(function ($query) use ($request,$ability,$classTeacherCourse) {
            /*--teacher access--*/
                if ($request->reg_no) {
                    $query->where('students.reg_no', 'like', '%' . $request->reg_no . '%')
                            ->orWhere('students.university_reg', 'like', '%' . $request->reg_no . '%');
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
                    $query->where('student_detail_sessionwise.course_id', '=', $request->faculty);
                    $this->filter_query['students.faculty'] = $request->faculty;
                
                }
                /*--teacher access--*/
                else{
                    if(!$ability){
                        $query->whereIn('student_detail_sessionwise.course_id',$classTeacherCourse);
                    }
                }
                /*--teacher access--*/
                if ($request->section) {
                    $query->where('student_detail_sessionwise.semester', $request->section);
                    $this->filter_query['section'] = $request->section;
                }
                if ($request->category) {
                    $query->where('students.category_id', $request->category);
                    $this->filter_query['category'] = $request->category; 
                }
                if ($request->name) {
                    $query->where('students.first_name', 'like', '%' . $request->name . '%');
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
                if($request->reg_by){
                    $query->where('students.created_by','=',$request->reg_by);
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
            ->where(function($query){
                $query->where('students.status',1)
                ->orWhere('students.status',0);
            })
            
             /*--teacher access--*/
            ->Where(function($q)use($classTeacher,$ability){
                 if((count($classTeacher)>0) && (!$ability)){
                    $q->whereIn('student_detail_sessionwise.Semester',$classTeacher);
                 }
            })
             /*--teacher access--*/
            ->groupBy('student_detail_sessionwise.id')
            ->orderBy('id','desc')
            ->orderBy('cb.start_date','asc')
            ->get();
        // dd($data['student'][0]);
        $data['user']=User::select('users.id',DB::raw("CONCAT(users.name,' ( ',roles.display_name,' )') as name"))
      ->leftjoin('roles','users.role_id','=','roles.id')
        ->where(
          [
            ['users.branch_id','=',Session::get('activeBranch')],
            ['role_id','!=',6],
            ['role_id','!=',7]

        ])->pluck('name','id')->toArray();
        $data['user']=array_prepend($data['user'],'--Registration By--','');
        $data['category_name'] = category_model::get();
        $data['faculties'] = $this->activeFaculties();
        /*--teacher access--*/
        $data['semester'] = Semester::select('id','semester')->where([
            ['status','=',1]
        ])
        ->Where(function($q)use($classTeacher){
            if(count($classTeacher)>0){
                $q->whereIn('id',$classTeacher);
            }
        })
        ->pluck('semester','id')->toArray();
        $data['semester']=array_prepend($data['semester'],'--Select Section--','');
        /*--teacher access--*/
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
    
    $randomString = rand();
         if(Session::has('activeBranch'))
        {
            $branch_id = Session::get('activeBranch');
        }
        else{ $branch_id = Auth::user()->branch_id; }
                    
        $org_id = Auth::user()->org_id;
        $branch = Branch:: select('branch_name','branch_title','id','org_id')->where('id', $branch_id)->get();   //
        $data = []; //dd($branch);
        
        $data['blank_ins'] = new Student();
        
        /*--teacher access--*/
        $ability = $this->getAbility();
        
        $data['section']= [];
        $classTeacher= TeacherCoordinator::where('teacher_id',Auth::user()->hook_id)
            ->where('branch_id',$branch_id)
            ->where('record_status',1)
            ->where('session_id',Session::get('activeSession'))->pluck('section_id')->toArray();
        $Semester = Semester:: select('semester','id')
       ->Where(function($q)use($classTeacher,$ability){
            if(count($classTeacher)>0 && (!$ability)){
                $q->whereIn('id',$classTeacher);
            }
        })
       ->get(); 
       /*--teacher access--*/
    $sessions = DB::table('session')->select('id', 'session_name')->pluck('session_name', 'id')->toArray(); //get();
    $sessions=array_prepend($sessions, '----Select Session----', '');

    $pay_type_list = DB::table('payment_type')->select('type_name')->where('status', '1')->pluck('type_name','type_name')->toArray(); 
        //dd($pay_type_list);//, 'id'
        $pay_type_list = array_prepend($pay_type_list, "--Payment Mode--", "");
        
        /*--teacher access--*/
        $faculties = $this->activeFaculties();
       
      
        $classTeachercourse= $this->getClassTeacherCourse();
        
        $courses = Faculty::select('id', 'faculty', 'status')
            ->where('branch_id' , $branch_id) 
            ->where(function($q)use($ability,$classTeachercourse){
                if(!$ability){
                    $q->whereIn('id',$classTeachercourse);
                }
            })
            ->orderBy('faculty')
            ->get();
        
          /*--teacher access--*/
      
      $category = category_model::select('id', 'category_name')->pluck('category_name','id')->toArray();
      $category= array_prepend($category,'SelectCategory',0);

        $data['faculties'] = array_prepend($faculties,'Select '.env("course_label"),"");
        $academicStatus = StudentStatus::select('id', 'title')->Active()->pluck('title','id')->toArray();
        $data['academic_status'] = array_prepend($academicStatus,'Select Status',0);
     $religion=DB::table('religions')->select('id','title')->where('record_status',1)->pluck('title','id')->toArray();
    $religion=array_prepend($religion,"--Select Religion--",'');
    $handicap=DB::table('handicaps')->select('id','title')->where('record_status',1)->pluck('title','id')->toArray();
    $handicap=array_prepend($handicap,"--Select Handicap--",'');
    
    $data['student']=Student::select('students.id',DB::raw("CONCAT(students.first_name,'/  ',COALESCE(pd.father_first_name),' / ',COALESCE(f.faculty),'-', COALESCE( sem.semester )) as title"))

      ->leftjoin('student_detail_sessionwise as sds','sds.student_id','=','students.id')
      ->leftjoin('semesters as sem','sem.id','=','students.semester')
      ->leftjoin('parent_details as pd','pd.id','=','students.id')
      ->leftjoin('faculties as f','f.id','=','students.faculty')
       ->where('sds.session_id','=', session::get('activeSession'))
       ->where('students.branch_id','=', session::get('activeBranch'))
       ->where('students.status',1)
       ->where('sds.active_status',1)
     ->pluck('title','id')->toArray();
     $data['student']=array_prepend($data['student'],"--Select student--",'');

        return view(parent::loadDataToView($this->view_path.'.registration.register'), compact('data','branch','category','Semester','sessions','randomString','admData','pay_type_list','courses', 'admId','religion','handicap','faculties'));
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
        $data['faculties'] = array_prepend($faculties,'Select '.env("course_label"),"");
            
        $academicStatus = StudentStatus::select('id', 'title')->Active()->pluck('title','id')->toArray();
        $data['academic_status'] = array_prepend($academicStatus,'Select Status',0);
        $data['selected_branch'] = Branch:: select('branch_name','branch_title','id','org_id')->where('id', $branch_id)->get(); 
 // return $registration;
 // DD();
    $data['religion']=DB::table('religions')->select('id','title')->where('record_status',1)->pluck('title','id')->toArray();
    $data['religion']=array_prepend($data['religion'],"--Select Religion--",'');
    $data['handicap']=DB::table('handicaps')->select('id','title')->where('record_status',1)->pluck('title','id')->toArray();
    $data['handicap']=array_prepend($data['handicap'],"--Select Handicap--",'');
        return view(parent::loadDataToView($this->view_path.'.registration.register'), compact('data','registration'));
    }

    public function register(AddValidation $request)
    { 
         // $regNo  = $this->reG_no();
        $request->request->add(['reg_date' => $request->get('reg_date')]);
        $request->request->add(['created_by' => auth()->user()->id]);
        $request->request->add(['semester' => $request->get('semester')]);
        // $request->request->add(['semester' => $request->get('semester')]);
        $request->request->add(['first_name' => trim($request->first_name)]);
        // $request->request->add(['email' => $emails]);
        $subjects = '';
        if(isset($request->subjects)){
            if(count($request->subjects)>0){
            $subjects = implode(',',$request->subjects);
            }
        }

        
        $student = Student::create($request->all());
        if($request->faculty){
            $facultyId = $request->faculty;
        }else{
            $facultyId = "";
        }
            
        $regNo= $this->reG_no($student->id,$facultyId); 
        $imgName = rand(1111,9999).rand(1233,6666);
        
        if (isset($_POST['student_profile_image']) && $_POST['student_profile_image']!="") {
            $img            = $_POST['student_profile_image'];
            $folderPath     = $this->folder_path;

            $image_parts    = explode(";base64,", $img);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type     = $image_type_aux[1];

            $image_base64   = base64_decode($image_parts[1]);
            $fileName       = $imgName.'.'.uniqid() . '.png';
            $fileName       = $imgName. '.png';

            $file           = $folderPath . $fileName;
            file_put_contents($file, $image_base64);

            /*upload new student image*/
            $student_image      = $fileName;
            $student_image_name = $fileName; 
        }
        else{
  
            if ($request->hasFile('student_main_image')){
                $student_image = $request->file('student_main_image');
                $student_image_name = $imgName.'.'.$student_image->getClientOriginalExtension();
                $student_image->move(public_path().DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'studentProfile'.DIRECTORY_SEPARATOR, $student_image_name);
            }else{
                $student_image_name = "";
            }
        }

        // $request->request->add(['reg_date' => $request->get('reg_date')]);--
        // $request->request->add(['created_by' => auth()->user()->id]);--
        // $request->request->add(['semester' => $request->get('semester')]);--
        // $request->request->add(['student_image' => $student_image_name]);--
        if($student->email){
            $email=$request->email;
        }else{
            $email = $regNo.env('EMAIL_POST_FIX');
        }
        $std=Student::where('id',$student->id)->update([
            'reg_no'=>$regNo,
            'student_image'=>$student_image_name,
            'email'=>$email
        ]);
        
        // if(!$request->email){
        //     $emails = $regNo."@sheatpublicschool.com";
        // }else{ $emails = $request->email; }

        // $request->request->add(['reg_no' => $regNo]);
        // $request->request->add(['email' => $emails]);
        // $request->request->add(['first_name' => trim($request->first_name)]);--
        
        // $student = Student::create($request->all());--

        //entry of student_detail sessionwise
         
         DB::table('student_detail_sessionwise')->Insert([
            'student_id'=>$student->id,
            'course_id'=>$request->faculty,
            'session_id'=>$request->session_id,
            'Semester'=>$request->semester,
            'created_by'=>auth()->user()->id,
            'subject'=>$subjects,
            'is_hostlier'=>$request->is_hostlier
        ]); 
        //entry of student sibling details
           if($request->sibling_student_id){
             $siblings= $request->sibling_student_id;
             foreach ($siblings as $key_sibling => $value_sibling) {
                if($key_sibling>0){
                    DB::table('student_sibling')->Insert([
                        'student_id'=>$student->id,
                        'branch_id'=>session::get('activeBranch'),
                        'session_id'=>session::get('activeSession'),
                        'student_sibling_id'=>$value_sibling,
                        'created_by'=>auth()->user()->id,
                        'created_at'=>Carbon::now(),
                    ]); 
                }
             }
           }
            // end entry of student sibling details
        $parential_image_path = public_path().DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'parents'.DIRECTORY_SEPARATOR;

        if ($request->hasFile('father_main_image')){
            $father_image = $request->file('father_main_image');
            $father_image_name = $imgName.'_father.'.$father_image->getClientOriginalExtension();
            $father_image->move($parential_image_path, $father_image_name);
        }else{
            $father_image_name = "";
        }

        if ($request->hasFile('mother_main_image')){
            $mother_image = $request->file('mother_main_image');
            $mother_image_name = $imgName.'_mother.'.$mother_image->getClientOriginalExtension();
            $mother_image->move($parential_image_path, $mother_image_name);
        }else{
            $mother_image_name = "";
        }

        if ($request->hasFile('guardian_main_image')){
            $guardian_image = $request->file('guardian_main_image');
            $guardian_image_name = $imgName.'_guardian.'.$guardian_image->getClientOriginalExtension();
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
        // dd('add');
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

        // dd('academicStatus');
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
            // $receipt_no = $this->reciept_no();
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
                    $receipt_id[]=DB::Table('collect_fee')->insertGetId([
                        'created_by' =>auth()->user()->id,
                        'reciept_date' =>$date, 
                        'student_id' => $student->id,
                        'assign_fee_id' =>$FeeHeadIds,
                        'amount_paid' => $FeeHeadAmt,
                        'payment_type' =>$request->payment_type,
                        'reference' =>$request->ref_no,
                        'remarks' =>$rmrk,
                        'status' =>1,
                        // 'reciept_no' =>$receipt_no 
                        
                    ]);
                }
                
            } 
            if(isset($receipt_id)){
                    $payment_count=count($receipt_id);
                $collect_id=$receipt_id[$payment_count-1];
                $receipt_no=$this->reciept_no($collect_id,$receipt_id);
                foreach ($receipt_id as $key => $value) {
                   DB::table('collect_fee')->where('id',$value)->update([
                        'reciept_no'=>$receipt_no
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



    public function feeReceipt_10Feb2022($receipt_no){
        $generalSetting=GeneralSetting::first();
        $data = DB::table('collect_fee')->Select('collect_fee.id','collect_fee.student_id', 'collect_fee.reciept_date','collect_fee.assign_fee_id','collect_fee.amount_paid','collect_fee.reciept_no','collect_fee.payment_type','collect_fee.reference','collect_fee.remarks','collect_fee.created_by','collect_fee.discount','sd.first_name','sd.branch_id','sd.reg_no', 'sd.reg_date','sd.batch_id', 'sd.university_reg','sd.date_of_birth', 'sd.gender','asf.fee_amount','asf.course_id','asf.fee_head_id','asf.session_id','fd.fee_head_title','br.branch_name','br.branch_logo','br.branch_mobile','br.branch_email','br.branch_address','ur.name', 'fac.faculty','sn.session_name','pd.father_first_name as father_name','ai.mobile_1 as mobile','cb.start_date','cb.end_date','stf.subject')
        ->where('collect_fee.reciept_no','=', $receipt_no)
        ->where('collect_fee.status',1)
        ->leftJoin('students as sd', 'sd.id', '=', 'collect_fee.student_id')
        ->leftJoin('users as ur', 'ur.id', '=', 'collect_fee.created_by')
        ->leftJoin('assign_fee as asf', 'asf.id', '=', 'collect_fee.assign_fee_id')
        ->leftjoin('course_batches as cb',function($j){
            $j->on('cb.id','=','asf.batch_id');
        })
        ->leftJoin('fee_heads as fd', 'fd.id', '=', 'asf.fee_head_id')
        ->leftJoin('branches as br', 'br.id', '=', 'sd.branch_id')
        ->leftJoin('session as sn', 'sn.id', '=', 'asf.session_id')
        ->leftjoin('parent_details as pd','pd.students_id','=','collect_fee.student_id')
        ->leftjoin('addressinfos as ai','ai.students_id','=','collect_fee.student_id')
        
        ->leftJoin('faculties as fac', 'asf.course_id', '=', 'fac.id')
        ->leftjoin('student_detail_sessionwise as stf',function($j)use($receipt_no){
            $j->on('stf.student_id','=','collect_fee.student_id');
            $j->where('stf.session_id','=',function($w)use($receipt_no){
                $w->selectRaw(" std_session.session_id from collect_fee left join student_detail_sessionwise as std_session on std_session.student_id = collect_fee.student_id where collect_fee.reciept_no = '$receipt_no' LIMIT 1");
            });
        })
        ->groupBy('collect_fee.assign_fee_id')
        ->get();
        $subjects = '';
        if($data['0']->subject){
            if(strpos($data['0']->subject, ',')){
                
                $sub = DB::table('timetable_subjects')->select('title as subject')
                ->whereRaw('FIND_IN_SET(id,?)', [$data['0']->subject])->get();
            }else{
                $sub = DB::table('timetable_subjects')->select('title as subject')->where('id','=',$data['0']->subject)->get();
            }
            foreach ($sub as $key => $value) {
                $subjects .= $value->subject.',';
            }
            $subjects = rtrim($subjects,',');
            
        }
        
       $collected = '';
       $count = count($data);
       $i = 1;
        foreach ($data as $key => $value) {
            if($i < $count){
                $collected .= $value->assign_fee_id.',';
            }else{
                $collected .= $value->assign_fee_id;
            }
            $i++;
        }
        $otherDues = DB::table('assign_fee')->select('fee_amount','assign_fee.id as assign_fee_id','cf.amount_paid','fee_head_title')
        ->whereRaw('assign_fee.id not in ( '.$collected.')')
        ->where([
            ['course_id','=',$data['0']->course_id],
            ['session_id','=',$data['0']->session_id],
            ['batch_id','=',$data['0']->batch_id],
            ['assign_fee.status','=',1],
        ])
        ->whereIn('assign_fee.student_id',[$data['0']->student_id,'0'])
        ->leftjoin('collect_fee as cf',function($q)use($data){
            $q->on('cf.assign_fee_id','=','assign_fee.id')
            ->where('cf.student_id',$data['0']->student_id);
        })
        ->leftjoin('fee_heads as fh','fh.id','=','assign_fee.fee_head_id')
        ->selectRaw('SUM(amount_paid) as total_paid,SUM(discount) as total_discount')
        ->groupBy('assign_fee.id')
        ->get();
        // dd($otherDues,$data);
        return view('student.registration.includes.studentfeereceipt',compact('data','generalSetting','otherDues','subjects'));
    }
    
    
    
    public function feeReceipt($receipt_no){
        $generalSetting=GeneralSetting::first();
        $data = DB::table('collect_fee')->Select('collect_fee.id','collect_fee.student_id', 'collect_fee.reciept_date','collect_fee.assign_fee_id','collect_fee.amount_paid','collect_fee.reciept_no','collect_fee.payment_type','collect_fee.reference','collect_fee.remarks','collect_fee.created_by','collect_fee.discount','sd.first_name','sd.branch_id','sd.reg_no', 'sd.reg_date','sd.batch_id', 'sd.university_reg','sd.date_of_birth', 'sd.gender','asf.fee_amount','asf.course_id','asf.fee_head_id','asf.session_id','fd.fee_head_title','br.branch_name','br.branch_logo','br.branch_mobile','br.branch_email','br.branch_address','ur.name', 'fac.faculty','sn.session_name','pd.father_first_name as father_name','ai.mobile_1 as mobile','cb.start_date','cb.end_date','stf.subject','sn.session_name','fac.faculty')
        ->where('collect_fee.reciept_no','=', $receipt_no)
        ->where('collect_fee.status',1)
        ->leftJoin('students as sd', 'sd.id', '=', 'collect_fee.student_id')
        ->leftJoin('users as ur', 'ur.id', '=', 'collect_fee.created_by')
        ->leftJoin('assign_fee as asf', 'asf.id', '=', 'collect_fee.assign_fee_id')
        ->leftjoin('course_batches as cb',function($j){
            $j->on('cb.id','=','asf.batch_id');
        })
        ->leftJoin('fee_heads as fd', 'fd.id', '=', 'asf.fee_head_id')
        ->leftJoin('branches as br', 'br.id', '=', 'sd.branch_id')
        ->leftJoin('session as sn', 'sn.id', '=', 'asf.session_id')
        ->leftjoin('parent_details as pd','pd.students_id','=','collect_fee.student_id')
        ->leftjoin('addressinfos as ai','ai.students_id','=','collect_fee.student_id')
        
        ->leftJoin('faculties as fac', 'asf.course_id', '=', 'fac.id')
        ->leftjoin('student_detail_sessionwise as stf',function($j)use($receipt_no){
            $j->on('stf.student_id','=','collect_fee.student_id');
            $j->where('stf.session_id','=',function($w)use($receipt_no){
                $w->selectRaw(" std_session.session_id from collect_fee left join student_detail_sessionwise as std_session on std_session.student_id = collect_fee.student_id where collect_fee.reciept_no = '$receipt_no' LIMIT 1");
            });
        })
       
        ->groupBy('collect_fee.assign_fee_id')
        ->get();
        $subjects = '';
        if($data['0']->subject){
            if(strpos($data['0']->subject, ',')){
                
                $sub = DB::table('timetable_subjects')->select('title as subject')
                ->whereRaw('FIND_IN_SET(id,?)', [$data['0']->subject])->get();
            }else{
                $sub = DB::table('timetable_subjects')->select('title as subject')->where('id','=',$data['0']->subject)->get();
            }
            foreach ($sub as $key => $value) {
                $subjects .= $value->subject.',';
            }
            $subjects = rtrim($subjects,',');
            
        }
        
       $collected = '';
       $count = count($data);
       $i = 1;
        foreach ($data as $key => $value) {
            if($i < $count){
                $collected .= $value->assign_fee_id.',';
            }else{
                $collected .= $value->assign_fee_id;
            }
            $i++;
        }
        $otherDues = DB::table('assign_fee')->select('fee_amount','assign_fee.id as assign_fee_id','cf.amount_paid','fee_head_title')
        ->whereRaw('assign_fee.id not in ( '.$collected.')')
        ->where([
            ['course_id','=',$data['0']->course_id],
            ['session_id','=',$data['0']->session_id],
            ['batch_id','=',$data['0']->batch_id],
            ['assign_fee.status','=',1],
        ])
        ->whereIn('assign_fee.student_id',[$data['0']->student_id,'0'])
        ->leftjoin('collect_fee as cf',function($q)use($data){
            $q->on('cf.assign_fee_id','=','assign_fee.id')
            ->where('cf.student_id',$data['0']->student_id);
        })
        ->leftjoin('fee_heads as fh','fh.id','=','assign_fee.fee_head_id')
        ->selectRaw('SUM(amount_paid) as total_paid,SUM(discount) as total_discount')
        ->groupBy('assign_fee.id')
        ->get();
        // dd($otherDues,$data);
        return view('student.registration.includes.studentfeereceipt',compact('data','generalSetting','otherDues','subjects'));
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
        // dd($id);
        $current_session_id=Session::get('activeSession');
        $isCourseBatch = Session::get('isCourseBatch');
        $data = [];
         $data['student'] = StudentPromotion::select('students.id as id','students.reg_no', 'students.reg_date', 'students.university_reg',
            'student_detail_sessionwise.course_id as faculty','student_detail_sessionwise.semester', 'students.academic_status', 'students.first_name', 'students.middle_name',
            'students.last_name', 'students.date_of_birth', 'students.gender', 'students.blood_group', 'students.nationality',
            'students.mother_tongue', 'students.email', 'students.extra_info', 'students.student_image', 'students.status','pd.grandfather_first_name','students.branch_id','students.batch_id',
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
            ->leftjoin('parent_details as pd', 'pd.students_id', '=', 'students.id')
            ->leftjoin('addressinfos as ai', 'ai.students_id', '=', 'students.id')
            ->leftjoin('student_guardians as sg', 'sg.students_id','=','students.id')
            ->leftjoin('guardian_details as gd', 'gd.id', '=', 'sg.guardians_id')
            ->first();
        if (!$data['student']){
            request()->session()->flash($this->message_warning, "Not a Valid Student");
            return redirect()->route($this->base_route);
        }
        // dd($studen)
          $data['fee_master'] = DB::table('collect_fee')->Select('collect_fee.id','collect_fee.status','collect_fee.student_id','collect_fee.discount', 'collect_fee.reciept_date','collect_fee.assign_fee_id','collect_fee.amount_paid','collect_fee.reciept_no','collect_fee.payment_type','sd.first_name','sd.branch_id','sd.reg_no', 'sd.reg_date', 'sd.university_reg','sd.date_of_birth', 'sd.gender','asf.fee_amount','asf.course_id','asf.fee_head_id','fd.fee_head_title','br.branch_name','br.branch_logo','br.branch_mobile','br.branch_email','br.branch_address', 'fac.faculty')
        ->where('collect_fee.student_id','=',$data['student']->id)
        ->where('asf.session_id',Session::get('activeSession'))
        ->join('students as sd', 'sd.id', '=', 'collect_fee.student_id')
        ->join('assign_fee as asf', 'asf.id', '=', 'collect_fee.assign_fee_id')
        ->join('fee_heads as fd', 'fd.id', '=', 'asf.fee_head_id')
        ->join('branches as br', 'br.id', '=', 'sd.branch_id')
        ->join('faculties as fac', 'asf.course_id', '=', 'fac.id')
        ->get();

        // dd($data);
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
        ->where('assign_fee.status', 1)
        ->WhereIn('assign_fee.student_id', ['0',$data['student']->id])
        ->where(function($q)use($isCourseBatch,$data){
            if($isCourseBatch){
                $q->where('assign_fee.batch_id',$data['student']->batch_id);
            }
        })
        ->orWhere(function($q) use ($data,$isCourseBatch){
            if($data['student']->id){
                $q->orWhere('assign_fee.student_id', $data['student']->id)
                ->where('assign_fee.branch_id', $data['student']->branch_id)
                ->where('assign_fee.session_id', $data['student']->session_id)
                ->where('assign_fee.status', 1)
                ->where('assign_fee.course_id', $data['student']->faculty);
                if($isCourseBatch){
                    $q->where('assign_fee.batch_id',$data['student']->batch_id);
                }
            }
        })->groupBy('assign_fee.id')
        ->orderByRaw("FIELD(assign_fee.due_month, '4','5','6','7','8','9','10','11','12','1','2','3') ASC")
        ->get();
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
        
        
         /*siblings code*/
        $data['student']=Student::select('students.id',DB::raw("CONCAT(students.first_name,'/  ',COALESCE(pd.father_first_name),' / ',COALESCE(f.faculty),'-', COALESCE( sem.semester )) as title"))

      ->leftjoin('student_detail_sessionwise as sds','sds.student_id','=','students.id')
      ->leftjoin('semesters as sem','sem.id','=','students.semester')
      ->leftjoin('parent_details as pd','pd.id','=','students.id')
      ->leftjoin('faculties as f','f.id','=','students.faculty')
       ->where('sds.session_id','=', session::get('activeSession'))
       ->where('students.branch_id','=', session::get('activeBranch'))
       ->where('students.status',1)
       ->where('sds.active_status',1)
       ->where('students.id','!=',$id)
     ->pluck('title','id')->toArray();
      $data['student']=array_prepend($data['student'],"--Select student--",'');
      $data['siblings']= DB::table('student_sibling')->select('student_sibling_id','id')
      ->where('record_status',1)
      ->where('student_id',$id)
      ->pluck('student_sibling_id','id')->toArray();
       
        /*siblings code*/

        $data['row'] = Student::select('students.id','students.aadhar_no','students.category_id','students.reg_no', 'students.reg_date', 'students.university_reg','students.religion_id','students.handicap_id',
            'students.faculty','students.semester','sts.course_id as faculty','sts.Semester as semester', 'students.academic_status', 'students.first_name', 'students.zip', 'students.middle_name',
            'students.last_name', 'students.session_id', 'students.date_of_birth', 'students.gender', 'students.blood_group', 'students.nationality',
            'students.mother_tongue', 'students.email', 'students.extra_info','students.student_image', 'students.status','students.indose_number','students.passport_no','students.admission_condition',
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
            'gd.guardian_relation', 'gd.guardian_address', 'gd.guardian_image','cb.id as batch_id','sts.subject','sts.course_id as faculty','sts.Semester as semester','sts.is_hostlier','students.place_of_birth','students.previous_school','students.previous_class','pd.whatsapp_no','pd.mother_whatsapp_no','pd.father_annual_income','pd.father_aadhar_no','pd.mother_annual_income','pd.mother_aadhar_no','age_in_year_as_on_1april')
            ->where('students.id','=',$id)
            
            ->leftjoin('parent_details as pd', 'pd.students_id', '=', 'students.id')
            ->leftjoin('addressinfos as ai', 'ai.students_id', '=', 'students.id')
            ->leftjoin('student_guardians as sg', 'sg.students_id','=','students.id')
            ->leftjoin('guardian_details as gd', 'gd.id', '=', 'sg.guardians_id')
            ->leftjoin('course_batches as cb','cb.id','=','students.batch_id')
            ->leftjoin('student_detail_sessionwise as sts','sts.student_id','=','students.id')
            ->where('sts.session_id',Session::get('activeSession'))
            ->first();

        if (!$data['row']){ return parent::invalidRequest(); }
        else{ $admData[0] = $data['row']; 
        $std_subject =  DB::table('timetable_subjects')->select('id','title')
        ->where([
            ['section_id','=',$data['row']->semester],
            ['course_id','=',$data['row']->faculty],
            ['status','=',1],
            ['session_id','=',Session::get('activeSession')],
        ])
        ->pluck('title','id')->toArray();
        }

        $data['faculties'] = $this->activeFaculties();


        $semester = Semester::select('id', 'semester')->where('id','=',$data['row']->semester)->Active()->pluck('semester','id')->toArray();
        $data['semester'] = array_prepend($semester,'Select Semester',0);


        $academicStatus = StudentStatus::select('id', 'title')->Active()->pluck('title','id')->toArray();
        $data['academic_status'] = array_prepend($academicStatus,'Select Status',0);

       $data['academicInfo'] =AcademicInfo::select('*')->Where('students_id', $id)->orderBy('sorting_order','asc')->get();
        $data['academicInfo-html'] = view($this->view_path.'.registration.includes.forms.academic_tr_edit', [
            'academicInfos' => $data['academicInfo']
        ])->render();
     
        /*--teacher access--*/
        $classTeachercourse= $this->getClassTeacherCourse();
        // getClassTeacherCourse
        $classTeacher= TeacherCoordinator::where('teacher_id',Auth::user()->hook_id)
        ->where('branch_id',$branch_id)
        ->where('record_status',1)
        ->where('session_id',Session::get('activeSession'))->pluck('section_id')->toArray();
        
        $ability = $this->getAbility();
        $courses = Faculty::select('id', 'faculty', 'status')
            ->where('branch_id' , $branch_id) 
            ->where(function($q)use($ability,$classTeachercourse){
                if(!$ability){
                    $q->whereIn('id',$classTeachercourse);
                }
            })
            
            ->orderBy('faculty')
            ->get();
        $Semester = Semester:: select('semester','id')
         ->Where(function($q)use($classTeacher,$ability){
            if(count($classTeacher)>0 && (!$ability)){
                $q->whereIn('id',$classTeacher);
            }
        })->get();
          /*--teacher access--*/
        $category=category_model::Select('id', 'category_name')->pluck('category_name', 'id')->toArray();
        $category=array_prepend($category, '----Select Category----', '');
         $religion=DB::table('religions')->select('id','title')->where('record_status',1)->pluck('title','id')->toArray();
        $religion=array_prepend($religion,"--Select Religion--",'');
        $handicap=DB::table('handicaps')->select('id','title')->where('record_status',1)->pluck('title','id')->toArray();
        $handicap=array_prepend($handicap,"--Select Handicap--",'');
        $batches = DB::table('course_batches')->select('id','title as batch','capacity')
        ->where([
            ['course_id','=',$data['row']->faculty],
            ['session_id','=',Session::get('activeSession')],
            ['status','=',1]
        ])->get();
        $batch = [];
        if(count($batches)>0){
            foreach ($batches as $key => $value) {
               $batch[$value->id] = $value->batch.' ( Cap. :'.$value->capacity.' | Avl. :'.$this->available_seats($data['row']->faculty,Session::get('activeBranch'),Session::get('activeSession'),$value->id).' )';
            }
            $batch = array_prepend($batch,'Select Batch','');
        }
    // dd($batch);
        return view(parent::loadDataToView($this->view_path.'.registration.edit'), compact('data','branches','branch', 'sessions', 'courses', 'Semester', 'admData', 'category','religion','handicap','batch','std_subject'));
    }
    public function available_seats($course_id="",$branch_id="",$session_id="",$batch_id=""){
         $seats_occupied=DB::table('students')->where([
                    ['faculty','=',$course_id],
                    ['branch_id','=',$branch_id],
                    ['session_id','=',$session_id],
                    ['batch_id','=',$batch_id],
                    ['status','=',1]
                   ])->count('id');
                 $total_seat=DB::table('course_batches')->select('capacity')->where('id',$batch_id)->first();
                $available=$total_seat->capacity-$seats_occupied;
                return $available;
    }
    public function update(EditValidation $request, $id)
    {
        //return $request;
        if (!$row = Student::find($id))
            return parent::invalidRequest();

        $img_name = rand(1111,99999).rand(123,9998);
        if (isset($_POST['student_profile_image']) && $_POST['student_profile_image']!="") {
            $img            = $_POST['student_profile_image'];
            $folderPath     = $this->folder_path;

            $image_parts    = explode(";base64,", $img);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type     = $image_type_aux[1];

            $image_base64   = base64_decode($image_parts[1]);
            $fileName       = $img_name.'.'.uniqid() . '.png';
            $fileName       = $img_name. '.png';

            $file           = $folderPath . $fileName;
            file_put_contents($file, $image_base64);

            /*upload new student image*/
            $student_image      = $fileName;
            $student_image_name = $fileName;
            //$student_image->move($this->folder_path, $student_image_name);
        }
        else{
            if ($request->hasFile('student_main_image')) {
                // remove old image from folder
                if (file_exists($this->folder_path.$row->student_image))
                    @unlink($this->folder_path.$row->student_image);

                /*upload new student image*/
                $student_image = $request->file('student_main_image');
                $student_image_name = $img_name.'.'.$student_image->getClientOriginalExtension();
                $student_image->move($this->folder_path, $student_image_name);
            }
        }

        $request->request->add(['updated_by' => auth()->user()->id]);
        $request->request->add(['student_image' => isset($student_image_name)?$student_image_name:$row->student_image]);
        
        $faculty_id = $request->faculty;
        $semester_id = $request->semester;
        $request->request->remove('faculty');
        $request->request->remove('semester');
    // dd($request->all());
        $student = $row->update($request->all());
        if($request->subjects){
            $subjects = implode(',', $request->subjects);
            DB::table('student_detail_sessionwise')->where([
                ['student_id','=',$id],
                ['session_id','=',Session::get('activeSession')],
            ])->update([
                'subject' => $subjects
            ]);
        }
        if($request->is_hostlier){
            $is_hostlier= $request->is_hostlier;
            DB::table('student_detail_sessionwise')->where([
                ['student_id','=',$id],
                ['session_id','=',Session::get('activeSession')],
            ])->update([
                'is_hostlier' => $is_hostlier
            ]);
        }
        if($faculty_id && $semester_id){
            DB::table('student_detail_sessionwise')->where([
                ['student_id','=',$id],
                ['session_id','=',Session::get('activeSession')],
            ])->update([
                'course_id' => $faculty_id,
                'semester'  => $semester_id,
                'updated_at'  => Carbon::now()->format('Y-m-d'),
                'updated_by'  => auth()->user()->id
            ]);
        }
        
             // update entry of student sibling details
           if($request->sibling_student_id){
             $siblings= $request->sibling_student_id;
             $delete= DB::table('student_sibling')
             ->where('student_id',$id)
             ->whereNotIn('student_sibling_id',array($siblings))->delete();

             foreach ($siblings as $key_sibling => $value_sibling) {

                if($key_sibling>0){
                    DB::table('student_sibling')->updateOrInsert(
                        [
                        'student_id'=>$id,
                        'student_sibling_id'=>$value_sibling,
                        'branch_id'=>session::get('activeBranch'),
                        'session_id'=>session::get('activeSession'),
                        
                        ],
                        [
                        'created_by'=>auth()->user()->id,
                        'created_at'=>Carbon::now(),  
                        ]
                  ); 
                }
             }
           }
            // end entry of student sibling details
         
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
            $father_image_name = $img_name.'_father.'.$father_image->getClientOriginalExtension();
            $father_image->move($parential_image_path, $father_image_name);
        }

        if ($request->hasFile('mother_main_image')){
            // remove old image from folder
            if (file_exists($parential_image_path.$parent->mother_image))
                @unlink($parential_image_path.$parent->mother_image);

            $mother_image = $request->file('mother_main_image');
            $mother_image_name = $img_name.'_mother.'.$mother_image->getClientOriginalExtension();
            $mother_image->move($parential_image_path, $mother_image_name);
        }


        if ($request->hasFile('guardian_main_image')){
            // remove old image from folder
            if (file_exists($parential_image_path.$guardian->guardian_image))
                @unlink($parential_image_path.$guardian->guardian_image);

            $guardian_image = $request->file('guardian_main_image');
            $guardian_image_name = $img_name.'_guardian.'.$guardian_image->getClientOriginalExtension();
            $guardian_image->move($parential_image_path, $guardian_image_name);
        }


        $father_image_name = isset($father_image_name)?$father_image_name:$parent->father_image;
        $mother_image_name = isset($mother_image_name)?$mother_image_name:$parent->mother_image;
        if($guardian){
            $guardian_image_name = isset($guardian_image_name)?$guardian_image_name:$guardian->guardian_image;
        }else{
            $guardian_image_name = '';
        }


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
            'mother_image'              =>  $mother_image_name,
            'whatsapp_no'               =>   $request->whatsapp_no,
            'mother_whatsapp_no'        =>   $request->mother_whatsapp_no,
            'father_annual_income'      =>   $request->father_annual_income,
            'father_aadhar_no'          =>   $request->father_aadhar_no,
            'mother_annual_income'      =>   $request->mother_annual_income,
            'mother_aadhar_no'          =>   $request->mother_aadhar_no,

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
            if($guardian){
                GuardianDetail::where('id',$sgd->guardians_id)->update($guardiansInfo);
            }else{
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
                    'guardian_image'              =>  $guardian_image_name,
                    'created_by'                  =>  auth()->user()->id,
                    'updated_at'                  =>  Carbon::now(),
                    'last_updated_by'             => auth()->user()->id
    
                ];
                 $guard_details = GuardianDetail::insertGetId($guardiansInfo);
                 StudentGuardian::insert([
                     'students_id' => $request->id,
                     'guardians_id' => $guard_details,
                     'created_at'   => Carbon::now()
                     ]) ;
            }
            
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
    public function findStudents(Request $request)
    {
        //dd($request->all());
        $response = [];
        $response['error'] = true;

        if ($request->has('course_id')) {
            $student = StudentPromotion::select('students.id','students.first_name')
                //->where('students.status',1)
                ->where('student_detail_sessionwise.course_id','=',$request->course_id)
                ->where('student_detail_sessionwise.Semester','=',$request->section_id)
                ->leftJoin('students','student_detail_sessionwise.student_id', '=', 'students.id')
                ->where('students.branch_id', session('activeBranch'))
                ->where('student_detail_sessionwise.session_id', session('activeSession'))
                ->get();
                 if ($student) {

                $response['student'] = $student;

                $response['error'] = false;
                $response['success'] = 'Studets. Avaible For This section/Class.';
            } else {
                $response['error'] = 'No Any Semester Assign on This Faculty/Class.';
            }


        } else {
            $response['message'] = 'Invalid request!!';
        }
        //dd($response);
        return response()->json(json_encode($response));
    }
    public function transfer(Request $request)
    {
        $data = [];
        if($request->all()) {
            $data['student'] = StudentPromotion::select('students.id', 'students.reg_no', 'students.reg_date', 'students.first_name', 'student_detail_sessionwise.course_id as faculty', 'students.semester', 'students.status', 'student_detail_sessionwise.session_id as old_ssn', 'student_detail_sessionwise.course_id', 'student_detail_sessionwise.Semester','pd.father_first_name as father_name')
                ->where('students.status',1)
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
                })
                ->leftjoin('parent_details as pd','pd.students_id','=','student_detail_sessionwise.student_id')
                ->get();
        }

        $data['faculties'] = $this->activeFaculties();

        $academicStatus = StudentStatus::select('id', 'title')->Active()->pluck('title','id')->toArray();
        $data['student_status'] = array_prepend($academicStatus,'Select Status',0);

        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;

        $course_options=$this->course_drop();
        $course_options=array_prepend($course_options, '--Select '.env("course_label").'--', '');

        $ssn_list=DB::table('session')->select('id', 'session_name')
        // ->where('id',Session::get('activeSession'))
        ->pluck('session_name', 'id')->toArray();
        $ssn_list=array_prepend($ssn_list, 'Select Session', '');

        $semester_list=DB::table('semesters')->select('id', 'semester')->pluck('semester', 'id')->toArray();
        $semester_list=array_prepend($semester_list, '--Select Section--', '');
        
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
    
    
    public function transfer_bulk(Request $request){
        $data = [];
        if($request->all()) {
            $data['student'] = StudentPromotion::select('students.id', 'students.reg_no', 'students.reg_date', 'students.first_name', 'student_detail_sessionwise.course_id as faculty', 'students.semester', 'students.status', 'student_detail_sessionwise.session_id as old_ssn', 'student_detail_sessionwise.course_id', 'student_detail_sessionwise.Semester','pd.father_first_name as father_name','prom_ssn.session_name as promoted_session','prom_fac.faculty as promoted_course','student_detail_sessionwise.Status')
                ->where('students.status',1)
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

                    if ($request->session) {
                        $query->where('student_detail_sessionwise.session_id', '=', $request->session);
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
                })
                ->leftjoin('parent_details as pd','pd.students_id','=','student_detail_sessionwise.student_id')
                ->leftjoin('session as prom_ssn','prom_ssn.id','=','student_detail_sessionwise.promoted_session')
                ->leftjoin('faculties as prom_fac','prom_fac.id','=','student_detail_sessionwise.promoted_course')
                ->orderBy('students.first_name','asc')
                ->get();
        }

        $data['faculties'] = $this->activeFaculties();

        $academicStatus = StudentStatus::select('id', 'title')->Active()->pluck('title','id')->toArray();
        $data['student_status'] = array_prepend($academicStatus,'Select Status',0);

        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;

        $course_options=$this->course_drop();
        $course_options=array_prepend($course_options, '--Select '.env("course_label").'--','');

        $ssn_list=DB::table('session')->select('id', 'session_name')
        ->where('status',1)
        ->pluck('session_name', 'id')->toArray();
        // $ssn_list=array_prepend($ssn_list, 'Select Session', '');

        $semester_list=DB::table('semesters')->select('id', 'semester')->pluck('semester', 'id')->toArray();
        $semester_list=array_prepend($semester_list, '--Select Section--', '');
        
        return view(parent::loadDataToView($this->view_path.'.transfer.bulk_index'), compact('data', 'course_dropdown', 'ssn_dropdown','semester_dropdown', 'ssn_list', 'course_options', 'semester_list'));
    }
    public function transfer_bulk_store(Request $request){
        // dd($request->all());
        if($request->ids){
            if($request->course && $request->session && $request->semester && $request->status){
               foreach ($request->ids as $key => $value) {
                    $exist = StudentPromotion::where('student_id', $key)->where([
                        ['session_id','=',$request->session],
                        ['course_id','=',$request->course],
                        ['Semester','=',$request->semester],
                    ])->first();
                    if(!$exist){
                        StudentPromotion::where('student_id', $key)
                        ->where('session_id', $request->old_session)
                        ->update(['Status'=>$request->status, 'promoted_session'=>$request->session, 'promoted_course'=>$request->course]);
                        $exist_in_session=StudentPromotion::where(['student_id'=>$key, 'session_id'=>$request->session])->get();

                        if(!count($exist_in_session)){
                            $ret=StudentPromotion::insert(['course_id'=>$request->course, 'student_id'=>$key, 'session_id'=>$request->session, 'Semester'=>$request->semester, 'created_by'=>auth()->user()->id]);
                        }else{
                            $ret=StudentPromotion::where([['student_id','=',$key], ['session_id','=',$request->session]])->update(['course_id'=>$request->course, 'student_id'=>$key, 'session_id'=>$request->session, 'Semester'=>$request->semester, 'created_by'=>auth()->user()->id]);
                        }
                    }
                } 
                return redirect()->back()->with('message_success','Selected Students Promoted Successfully.');
            }
            return redirect()->back()->with('message_warning','Please Select '.env('course_label').', Session, Section, Status Properly.');
        }else{
            return redirect()->back()->with('message_warning','Please check atleast one record');
        }
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
        $branch_id = Session::get('activeBranch');
        $faculty = Faculty::where('branch_id',$branch_id) 
            ->get();
        return view(parent::loadDataToView($this->view_path.'.registration.import'),compact('faculty'));
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

        $facultyNamePost = $request->faculty; 
        $file = $request->file('file');
        $csvData = file_get_contents($file);

        $rows = array_map("str_getcsv", explode("\n", $csvData));
        $header = array_shift($rows);
        
        foreach ($rows as $row) {
            dd($row);
            if (count($header) != count($row)) {
                continue;
            }

            $row = array_combine($header, $row);

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
            $facultyNamePost = ($facultyNamePost=='')? $row['faculty'] : $facultyNamePost;
            $faculty = Faculty::where('faculty',$facultyNamePost)
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
            $regDate =  trim($row['reg_date']);
            
            //$regDate =  Carbon::createFromFormat('d/m/Y', $regDate)->format('Y-m-d'); 
            $reg_date       = date("Y-m-d", strtotime($regDate))." 00:00:00"; 
            
            $stdDOB =  trim($row['date_of_birth']);
            //$stdDOB =  Carbon::createFromFormat('d/m/Y', $stdDOB)->format('Y-m-d'); 
            $date_of_birth       = date("Y-m-d", strtotime($stdDOB))." 00:00:00"; 
            
            $randomString = $this->reG_no($stdIds='',$facultyId); //Std Reg No.

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
                "email"                 => $randomString.env('EMAIL_POST_FIX'),
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
                    /*
                        $qry = "SELECT * FROM assign_fee
                            where (student_id=157 OR student_id=0) AND session_id=$current_session and branch_id=$branch_id and course_id=$facultyId and fee_amount > 1500 limit 1";
                    */
                    $qry = "SELECT * FROM assign_fee
                            where (student_id=0) AND session_id=$current_session and branch_id=$branch_id and course_id=$facultyId and fee_amount > 1000 limit 1";
                    $results      = DB::select($qry); 
                    $feeAssiInfo  = $results[0]; 
                    
                    $feeMastIdArr = $feeAssiInfo->id; 
                    $FeeHeadIds   = $feeAssiInfo->fee_head_id;
                    
                    $date         = $reg_date;
                    $receipt_no   = $this->reciept_no($student->id,array()); 

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
    public function importAssignFee(Request $request){
        $status = [];
        if($request->has('file')){
            $branch_id  = Session::get('activeBranch');
            //file present or not validation
            $validator = Validator::make($request->all(), [
                'file' => 'required',
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
            $faculty = DB::table('faculties')->select('id','faculty as title')->where('branch_id',$branch_id)->pluck('id','title')->toArray();
            $session = DB::table('session')->select('id','session_name as title')->pluck('id','title')->toArray();
            $semester = DB::table('semesters')->select('id','semester')->pluck('id','semester')->toArray();
            $head     = DB::table('fee_heads')->select('fee_head_title as title','id')->pluck('id','title')->toArray();
            foreach ($rows as $row) {
                if (count($header) != count($row)) {
                    continue;
                }

                $row = array_combine($header, $row);
                
                //Staff validation
                $validator = Validator::make($row, [
                    'session'   => 'required',
                    'course'    => 'required',
                    'amount'    => 'required',
                    'fee_head'  => 'required',
                ]);
                $row['remark'] = '';
                $row['status'] ='Not Completed';
                if ($validator->fails()) {
                    $row['remark'] ='Vaidation Failed';
                    
                }else{
                    $fac = trim($row['course']);
                    $faculty_id = (isset($faculty[$fac] ))?$faculty[$fac]:null;
                    
                    if($faculty_id != null){

                        // $sem = (isset($semester[$row['section']])) ? $semester[$row['section']]:'2';
                        $ssn = trim($row['session']);
                        $sess = (isset($session[$ssn])) ? $session[$ssn] : null;
                        if($sess != null){
                            $fee_head = trim($row['fee_head']);
                            $head_id = (isset($head[$fee_head] ))?$head[$fee_head]:null;
                            if($head_id != null){
                                $amount = trim($row['amount']);
                                $exists = DB::table('assign_fee')->select('*')->where([
                                    'branch_id'     => $branch_id,
                                    'session_id'    => $sess,
                                    'course_id'     => $faculty_id,
                                    'fee_head_id'   => $head_id,
                                    'fee_amount'    => $amount,
                                ])->first();
                                if(!$exists){
                                    DB::table('assign_fee')->insertGetId([
                                        'branch_id'     => $branch_id,
                                        'session_id'    => $sess,
                                        'course_id'     => $faculty_id,
                                        'fee_head_id'   => $head_id,
                                        'fee_amount'    => $amount,
                                    ]);
                                    $row['status'] = 'Completed';
                                }else{
                                    $row['remark'] = 'Already Exists';
                                }
                                
                            }else{
                                $row['remark'] = 'Head Not Found,';
                            }
                        }else{
                            $row['remark'] = 'Session Not Found,';
                        }
                    }else{
                        $row['remark'] = 'Faculty Not Found,';
                    }
                }

                $status[] = $row;
            
            }
            $this->panel = 'Assign Fee Import';
            // dd($status);
            return view(parent::loadDataToView($this->view_path.'.import.import-student-status'),compact('status','panel'));
        }else{
            return view(parent::loadDataToView($this->view_path.'.import.import-fee')); 
        }
        
        
    }
    public function importStudentFee(Request $request){

        $status = [];
        if($request->has('file')){
            $branch_id  = Session::get('activeBranch');
            //file present or not validation
            $validator = Validator::make($request->all(), [
                'file' => 'required',
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
            $faculty = DB::table('faculties')->select('id','faculty as title')->where('branch_id',$branch_id)->pluck('id','title')->toArray();
            $student = Student::select('reg_no','id')->where('branch_id',$branch_id)->pluck('id','reg_no')->toArray();
            // ->leftjoin('faculties as fac','fac.id','=','assign_fee.course_id')
            $assign = DB::table('assign_fee')->select('fh.id as head','sess.id as session','fac.id as faculty','assign_fee.id')
            ->leftjoin('session as sess','sess.id','=','assign_fee.session_id')
            ->leftjoin('faculties as fac','fac.id','=','assign_fee.course_id')
            ->leftjoin('fee_heads as fh','fh.id','=','assign_fee.fee_head_id')
            ->get();
            $assign_data = [];
            if(count($assign)>0){
                foreach ($assign as $key => $value) {
                    $assign_data[$value->session][$value->faculty][$value->head] = $value->id;
                }
            }
            // dd($assign_data);
            $session = DB::table('session')->select('id','session_name as title')->pluck('id','title')->toArray();
            $semester = DB::table('semesters')->select('id','semester')->pluck('id','semester')->toArray();
            $head     = DB::table('fee_heads')->select('fee_head_title as title','id')->pluck('id','title')->toArray();
            $user = DB::table('users')->select('name','id')->where([
                ['role_id','!=',6]
            ])->pluck('id','name')->toArray();
            foreach ($rows as $row) {
                if (count($header) != count($row)) {
                    continue;
                }

                $row = array_combine($header, $row);
                
                //Staff validation
                $validator = Validator::make($row, [
                    'discount'      => 'required',
                    'created_by'    => 'required',
                    'payment_mode'   => 'required ', 
                    'payment_date'    => 'required',
                    'receipt_no'  => 'required',
                    'amount_paid'  => 'required',
                    // 'mobile_no'  => 'required',
                    // 'mother_name'  => 'required',
                    // 'date_of_birth'  => 'required',
                    // 'father_name'  => 'required',
                    // 'student_name'  => 'required',
                    'fee_head'  => 'required',
                    // 'amount'  => 'required',
                    // 'section'  => 'required',
                    'course'  => 'required',
                    'session'  => 'required',
                ]);
                $row['status'] ='Not Completed';
                $row['remark'] = '';
                if ($validator->fails()) {
                    
                    $row['remark'] ='Vaidation Failed';
                    // $status = $row;
                }else{
                    $fac = trim($row['course']);
                    $faculty_id = (isset($faculty[$fac] ))?$faculty[$fac]:null;
                    
                    if($faculty_id != null){
                        $sem = 2;
                        $ssn = trim($row['session']);
                        $sess = (isset($session[$ssn])) ? $session[$ssn] : null;
                        if($sess != null){
                            $fee_head = trim($row['fee_head']);
                            $head_id = (isset($head[$fee_head] ))?$head[$fee_head]:null;
                            if($head_id != null){
                                $exists_assign_fee = isset($assign_data[$sess][$faculty_id][$head_id])? $assign_data[$sess][$faculty_id][$head_id] : null;

                                if($exists_assign_fee){
                                    // $student_name = trim($row['student_name']);
                                    $exists_in_std = isset($student[trim($row['reg_no'])])? $student[trim($row['reg_no'])] : null;/*Student::select('students.*')
                                    ->leftjoin('student_detail_sessionwise as sts','sts.student_id','=','students.id')
                                     ->leftjoin('parent_details as pd','pd.students_id','=','sts.student_id')
                                    ->leftjoin('addressinfos as ad','ad.students_id','=','sts.student_id')
                                    ->where([
                                        ['sts.course_id','=',$faculty_id],
                                        ['sts.Semester','=',$sem],
                                        ['students.first_name','=',$student_name],
                                        ['students.date_of_birth','=',$date_of_birth],
                                        ['pd.father_first_name','=',$row['father_name']],
                                        ['pd.mother_first_name','=',$row['mother_name']],
                                        ['ad.mobile_1','=',$row['mobile_no']],
                                    ])->first();*/
                                    if($exists_in_std){
                                        $receipt_no = trim($row['receipt_no']);
                                       if($receipt_no != null &&  $receipt_no != 'NULL'){
                                        $amount = trim($row['amount_paid']);
                                             if($amount == null || $amount == 'NULL'){
                                                $amount_paid = 0;
                                             }else{
                                                   $amount_paid = $amount;
                                             }
                                             $receipt_date = Carbon::parse($row['payment_date'])->format('Y-m-d H:i:s');
                                             if(isset($user[trim($row['created_by'])])){
                                                $created_by = $user[trim($row['created_by'])];
                                             }else{
                                                $created_by = 1;
                                             }
                                             $exists_in_collect = DB::table('collect_fee')->where(
                                                   [
                                                      ['student_id'   ,'=', $exists_in_std],
                                                      ['assign_fee_id','=', $exists_assign_fee],
                                                      ['reciept_no'   ,'=', $receipt_no],
                                                      ['amount_paid'  ,'=', $amount_paid],
                                                      ['reciept_date' ,'=', $receipt_date],
                                                      ['payment_type' ,'=', $row['payment_mode']],
                                                   ])->first();
                                             if(!$exists_in_collect){
                                                DB::table('collect_fee')->insert(
                                                [
                                                   'student_id'        => $exists_in_std,
                                                   'assign_fee_id'     => $exists_assign_fee,
                                                   'reciept_no'        => $receipt_no,
                                                   'amount_paid'       => $amount_paid,
                                                   'reciept_date'      => $receipt_date,
                                                   'payment_type'      => $row['payment_mode'],
                                                   'discount'          => $row['discount'],
                                                   'reciept_date'      => $receipt_date,
                                                   'created_at'        => Carbon::now(),
                                                   'created_by'        => $created_by,
                                                   'remarks'            => trim($row['remark'])
                                                ]);
                                                $row['status'] = 'Completed';
                                             }else{
                                                $row['remark'] = 'Already Exists';
                                             }
                                       }else{
                                            $row['remark'] = 'Invalid Receipt';
                                       }
                                    }else{
                                        $row['remark'] = 'Student Not Found';
                                    }
                                    
                                }else{
                                    $row['remark'] = 'Assign Fee Not Found';
                                }
                                
                            }else{
                                $row['remark'] = 'Fee Head Not Found,';
                            }
                        }else{
                            $row['remark'] = 'Session Not Found,';
                        }
                    }else{
                        $row['remark'] = 'Faculty Not Found,';
                    }
                }

                $status[] = $row;
            
            }
            $this->panel = 'Student Fee';
            return view(parent::loadDataToView($this->view_path.'.import.import-student-status'),compact('status','panel'));
        }else{
            return view(parent::loadDataToView($this->view_path.'.import.import-fee')); 
        }
        
      
    }
    public function importFee(){
        return view(parent::loadDataToView($this->view_path.'.import.import-fee'));
    }
    public function importStudentNew(Request $request){
        $status = [];
        if($request->has('file')){
            $session_id = Session::get('activeSession');
            $branch_id  = Session::get('activeBranch');
            $org_id = Auth::user()->org_id;
            $branch = Branch:: select('branch_name','branch_title','id','org_id')->where('id', $branch_id)->get();   //
            $branchName = $branch[0]->branch_name;
            $branchId = $branch[0]->id;

            //file present or not validation
            $validator = Validator::make($request->all(), [
                'file' => 'required',
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
            $faculty = DB::table('faculties')->select('id','faculty as title')->where('branch_id',$branch_id)->pluck('id','title')->toArray();
            $category = category_model::select('id','category_name as title')->pluck('id','title')->toArray();
            $session = DB::table('session')->select('id','session_name as title')->pluck('id','title')->toArray();
            $religion = DB::table('religions')->select('title','id')->Where('record_status',1)->pluck('id','title')->toArray();
            $semester = DB::table('semesters')->select('id','semester')->pluck('id','semester')->toArray();
            $subject = DB::table('timetable_subjects')->select(DB::raw("CONCAT(branch_id,',',session_id,',',course_id,',',section_id,',',title) as title",'id'))->pluck('id','title')->toArray();
           $cnt = 1;
           // dd($header);
            foreach ($rows as $row) {
               // dd($row);
                
                if (count($header) != count($row)) {
                    continue;
                }else{
                    
                  
                }
                $cnt++;
               $row = array_combine($header, $row);
               // $row['std_count'] = $cnt;
                // $row['std_count'] = $cnt;
                // $row['std_n'] = $name;
                $validator = Validator::make($row, [
                // 'reg_no'                        => 'required',
                'student_name'                  => 'required',
                // 'board_admission_no'            => 'required ', 
                // 'date_of_admission'             => 'required',
                // 'father_name'                   => 'required',
                // 'mother_name'                   => 'required',
                // 'gender'                        => 'required',
                // 'date_of_birth'                 => 'required',
                // 'category'                      => 'required',
                // 'religion'                      => 'required',
                // 'address'                       => 'required',
                // 'admission_session'             => 'required',
                // 'mobile_no'                     => 'required',
                // 'email'                         => 'required',
                // 'student_applied_semester'      => 'required',
                // 'admission_remark'              => 'required',
                'admission_in_session'          => 'required',
                'admission_course'              => 'required',
                // 'date_of_admission'             => 'required',
                // 'subject'                       => 'required',
                // 'nationality'                   => 'required',
                // 'state'                         => 'required',
                // 'country'                       => 'required',
                ]);
                $row['status'] = 'Not Completed';
                $row['remark'] = '';
                if ($validator->fails()) {
                    
                    $row['remark'] ='Vaidation Failed';
                   
                }else{  
                     $mobileText     = $row['mobile_no'];
                     $mobAr          = explode(",",$mobileText);
                     $mobile_number_1  = (isset($mobAr[0]) && $mobAr[0]!="")? $mobAr[0] : "";
                     $mobile_number_2  = (isset($mobAr[1]) && $mobAr[1]!="")? $mobAr[1] : "";
                      
                      //Staff validation
                     
                     
                       $generate_reg_no = $request->reg_check;
                       $course = trim($row['admission_course']);
                       $faculty_id = (isset($faculty[$course]))?$faculty[$course]:null;
                       
                       if($faculty_id != null){
                           $sec = trim($row['section']);

                           $sem = (isset($semester[$sec]))?$semester[$sec]:null;
                            // dd($sem,$semester);
                           $ssn = trim($row['admission_in_session']);
                           $sess = (isset($session[$ssn])) ? $session[$ssn] : null;
                           if($sess != null){
                               $reg_date = '';
                               if($row['date_of_admission'] != ''){
                                   $regDate =  trim($row['date_of_admission']);
                                    // $rg = $regDate[0].$regDate[1].'-'.$regDate[2].$regDate[3].'-'.$regDate[4].$regDate[5].$regDate[6].$regDate[7];
                                   $reg_date   = date("Y-m-d", strtotime($regDate))." 00:00:00";
                                   // $reg_date   = date("Y-m-d", strtotime($regDate))." 00:00:00";
                                     // dd($reg_date);
                               }
                               $date_of_birth = '';
                               if($row['date_of_birth'] != ''){
                                   $stdDOB =  trim($row['date_of_birth']);

                                   $date_of_birth       = date("Y-m-d", strtotime($stdDOB))." 00:00:00";
                               }
                               if($generate_reg_no == '1'){
                                   $reg_no = $this->reG_no($stdIds='',$faculty_id,$sess);
                               }else{
                                   $reg_no = trim($row['reg_no']);
                               }
                               if($row['email'] != ''){
                                   $email = $row['email'];
                               }else{
                                   $email = rand().env('EMAIL_POST_FIX');
                               }
                               if(isset($category[$row['category']])){
                                  $catId =  $category[$row['category']];
                               }else{
                                   $catId = '';
                                   $row['status'] .= 'Category Not Found,';
                               }

                               $student_name = trim($row['student_name']);
                            //   $exists_in_std = Student::select('students.*')
                            //   ->leftjoin('parent_details as pd','pd.students_id','=','students.id')
                            //   ->leftjoin('addressinfos as ad','ad.students_id','=','students.id')
                            //   ->where([
                            //       // ['faculty','=',$faculty_id],
                            //       // ['semester','=',$sem],
                            //       ['first_name','=',$student_name],
                            //       ['date_of_birth','=',$date_of_birth],
                            //       ['pd.father_first_name','=',$row['father_name']],
                            //       ['pd.mother_first_name','=',$row['mother_name']],
                            //       ['ad.mobile_1','=',$row['mobile_no']],
                            //   ])->first();
                            $exists_in_std =0;
                               $subject_list = '';
                               if($row['subject'] != ''){
                                   $subjectArr = explode(",",$row['subject']);
                                   $sub = [];
                                   foreach ($subjectArr as $key => $value) {
                                       $sub_id = (isset($subject[$branch_id.','.$sess.','.$faculty_id.','.$sem.','.$value])) ? $subject[$branch_id.','.$sess.','.$faculty_id.','.$sem.','.$value] : null;
                                       if($sub_id != null){
                                           $sub[] =  $sub_id;
                                       }
                                      
                                   }
                                   $subject_list = implode(',',$sub);
                               } 
                               if(!$exists_in_std){
                                   $student = Student::create([
                                       "reg_no"                => $reg_no,
                                       "reg_date"              => $reg_date,
                                       "university_reg"        => $row['board_admission_no'],
                                       "faculty"               => $faculty_id,
                                       "semester"              => $sem,
                                       // "academic_status"       => $academicStatusId,
                                       "first_name"            => $student_name, 
                                       "date_of_birth"         => $date_of_birth,
                                       "gender"                => $row['gender'],
                                       "blood_group"           => $row['blood_group'],
                                       "nationality"           => $row['nationality'],
                                       "admission_condition"   => $row['admission_remark'],
                                       "email"                 => $email,
                                       "branch_id"             => $branch_id,
                                       "org_id"                => $org_id,
                                       "category_id"           => $catId,
                                       "session_id"            => $sess,
                                       "aadhar_no"             => trim($row['aadhaar_no']),
                                       // "status"                => 1,
                                       'created_by'            => auth()->user()->id
                                   ]);

                                   StudentPromotion::create([
                                       'course_id'=>$faculty_id,
                                       'student_id'=>$student->id,
                                       'session_id'=>$sess,
                                       'Semester'=>$sem,
                                       'subject'=>$subject_list,
                                       'created_by'=>auth()->user()->id
                                   ]);
                                   DB::table('student_activity')->insert([
                                    'student_id'            => $student->id,
                                    'session_id'            => $sess,
                                    'house'                 => trim($row['house']),
                                    'musical_instrument'    => trim($row['instrument']),
                                    'health_issue'          => trim($row['health_issue']),
                                    'activity'              => trim($row['activity']),
                                    'game'                  => trim($row['game']),
                                    'extra_activity'        => trim($row['extra_activity']),
                                    'third_language'        => trim($row['third_language']),
                                    'siblings'              => trim($row['sibling']),
                                    'bus_stop'              => (isset($row['bus_stop']))?trim($row['bus_stop']):null,
                                    'bus_route'              => (isset($row['bus_route']))?trim($row['bus_route']):null,
                                   ]);
                                   Addressinfo::create([
                                       "students_id"           => $student->id,
                                       "mobile_1"              => $mobile_number_1,
                                       "mobile_2"              => $mobile_number_2,
                                       "address"               => $row['address'],
                                       "state"                 => $row['state'],
                                       "country"               => $row['country'],
                                       'created_by'            => auth()->user()->id
                                   ]);
                                   ParentDetail::create([
                                       "students_id"               => $student->id,
                                       "father_first_name"         => $row['father_name'],
                                       "mother_first_name"         => $row['mother_name'],
                                       'created_by'                => auth()->user()->id
                                   ]);
                                   $row['status'] = 'Completed';
                                   $mail = $reg_no.env('EMAIL_POST_FIX');
                                   $pass = '123456';
                                   $this->createStdLogin($student_name,$mail,$pass,$role_id=6,$hook_id=$student->id,$branch_id);
                               }else{
                                   $exists_in_sts = Student::select('students.*')
                                       ->leftjoin('parent_details as pd','pd.students_id','=','students.id')
                                       ->leftjoin('addressinfos as ad','ad.students_id','=','students.id')
                                       ->leftjoin('student_detail_sessionwise as sts','sts.student_id','=','students.id')
                                       ->where([
                                           ['sts.course_id','=',$faculty_id],
                                           ['sts.Semester','=',$sem],
                                           ['sts.session_id','=',$sess],
                                           ['sts.student_id','=',$exists_in_std->id],
                                   ])->first();

                                   $row['remark'] .= 'Exists in Student,';

                                   if(!$exists_in_sts){
                                       StudentPromotion::create([
                                           'course_id'=>$faculty_id,
                                           'student_id'=>$exists_in_std->id,
                                           'session_id'=>$sess,
                                           'Semester'=>$sem,
                                           'subject'=>$subject_list,
                                           'created_by'=>auth()->user()->id
                                       ]);
                                       DB::table('student_activity')->insert([
                                            'student_id'            => $exists_in_std->id,
                                            'session_id'            => $sess,
                                            'house'                 => trim($row['house']),
                                            'musical_instrument'    => trim($row['instrument']),
                                            'health_issue'          => trim($row['health_issue']),
                                            'activity'              => trim($row['activity']),
                                            'game'                  => trim($row['game']),
                                            'extra_activity'        => trim($row['extra_activity']),
                                            'third_language'        => trim($row['third_language']),
                                            'siblings'              => trim($row['sibling']),
                                           ]);
                                       $row['status']  = 'Completed';
                                       $row['remark'] .= 'Inserted in Session Wise,';
                                   }else{
                                       $row['remark'] .= 'Exists in both,';
                                   }
                               }
                               
                           }else{
                               $row['remark'] = 'Session Not Found,';
                           }
                       }else{
                           $row['remark'] = 'Faculty Not Found,';
                       }
                }
                $status[] = $row;
                $this->panel = 'Student Import';
            
            }
            return view(parent::loadDataToView($this->view_path.'.import.import-student-status'),compact('status'));
        }else{
            return view(parent::loadDataToView($this->view_path.'.import.import-student')); 
        }
    }
    public function createStdLogin($name,$email,$pass,$role_id=6,$hook_id,$branch_id){

        $user_id=DB::table('users')->insertGetId([
                        'name'=>$name,
                        'email'=>$email,
                        'password'=>bcrypt($pass),
                        'role_id'=>$role_id,
                        'hook_id'=>$hook_id,
                        'status'=>1,
                        'org_id'=>1,
                        'branch_id'=>$branch_id,
                        'created_at'=>Carbon::now(),
                    ]);
                    DB::table('role_user')->insert([
                        'user_id'=>$user_id,
                        'role_id'=>$role_id
                    ]);
    }
    public function importCourse(Request $request){
        $status = [];
        if($request->has('file')){
            $branch_id  = Session::get('activeBranch');
            //file present or not validation
            $validator = Validator::make($request->all(), [
                'file' => 'required',
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
            $faculty = DB::table('faculties')->select('id','faculty as title')->where('branch_id',$branch_id)->pluck('id','title')->toArray();
            // dd($faculty);
            foreach ($rows as $row) {
                if (count($header) != count($row)) {
                    continue;
                }

                $row = array_combine($header, $row);
                
                //Staff validation
                $validator = Validator::make($row, [
                    // 'session'   => 'required',
                    'course'    => 'required',
                    // 'section'   => 'required ', 
                    // 'amount'    => 'required',
                    // 'fee_head'  => 'required',
                ]);
                $row['remark'] = '';
                $row['status'] ='Not Completed';
                if ($validator->fails()) {
                    $row['remark'] ='Vaidation Failed';
                    // dd('hiii');
                    // $status = $row;
                }else{
                    $fac = trim($row['course']);
                    $faculty_id = (isset($faculty[$fac] ))?$faculty[$fac]:null;
                    
                    if($faculty_id != null){
                      $row['remark'] = 'Already Exists';
                    }else{
                        if($fac != ''){
                            $fac_id = Faculty::insertGetId([
                           'faculty'      => $fac,
                           'created_by'   => auth()->user()->id,
                           'branch_id'    => $branch_id,
                           'org_id'       => 1
                            ]);
                            $sem = DB::table('faculty_semester')->updateOrInsert(
                                [
                                    'faculty_id'    =>  $fac_id,
                                    'semester_id'   =>  2
                                ],
                                [
                                    'created_at' => Carbon::now()
                                ]
                            );
                            $faculty = array_prepend($faculty,$fac_id,$fac);
                            // dd($faculty);
                            $row['status'] = 'Completed';
                        }else{
                            $row['remark']  = 'Empty Faculty Name';
                        }
                        
                    }
                }

                $status[] = $row;
            
            }
            $this->panel = env('course_label').' Import';
            // dd($status);
            return view(parent::loadDataToView($this->view_path.'.import.import-student-status'),compact('status','panel'));
        }else{
            return view(parent::loadDataToView($this->view_path.'.import.import-fee')); 
        }
        
        
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

    public static function getStudentFeeHeadDueAmout($stdId="",$feeAssignId="",$session="")
    {   
        $qry = "SELECT sum(amount_paid) as totalPaid, max(assign_fee.fee_amount) as NeedToPay,sum(discount) as totalDiscount
            FROM collect_fee 
            join assign_fee on assign_fee.id=collect_fee.assign_fee_id
            where collect_fee.student_id=$stdId and collect_fee.status=1 and  collect_fee.assign_fee_id=$feeAssignId";
            $results = DB::select($qry);
        return $results;
    }
    public function getSubject(Request $request){
        $data = [];
        $data['error'] = true;
        if($request->section && $request->course_id){
            $subject = DB::table('timetable_subjects')->select('id','title')
            ->where([
                ['branch_id','=',$request->branch_id],
                ['session_id','=',$request->session_id],
                ['course_id','=',$request->course_id],
                ['section_id','=',$request->section],
                ['status','=',1]
            ])
            ->orderBy('title','asc')
            ->get();
            if(count($subject)>0){
                $data['error'] = false;
                $data['msg']   = 'Subject found';
                $data['data'] = $subject;
            }else{
               $data['msg']   = 'No subject found'; 
            }
        }else{
            $data['msg']   = 'Select '.env('course_label').'/Section to load subject.';
        }
        return response()->json(json_encode($data));
    }

    /* ***********[Login code {START}]*********** */
    public function FacultyList()
    {
        /*get designation*/
        $facultys = Faculty::select('id','faculty')->orderBy('faculty')
        ->where(function($q){
                 $q->where('branch_id',Session::get('activeBranch'))
                        ->orWhere('branch_id',0)
                        ->orWhere('branch_id',Null);
            })->get();
        $facultys = array_pluck($facultys,'faculty','id');
        $facultys = array_prepend($facultys,'--Select '.env('course_label').'--','');

        /*designation represent as list*/
        return $facultys;
    }
    /* OLD CODE
    public function GeneratePassword(Request $request)
    {
       
        $data = [];
     
        if ($request->has('faculty')){
         $faculty = $request->get('faculty');
          $student = Student::select('students.id','first_name', 'email','branch_id','faculty')
          ->leftJoin('student_detail_sessionwise as st', function($join) {
             $join->on('st.student_id', '=', 'students.id');
              })
          ->wherein('st.course_id',$faculty)
          ->where(function($q){
                 $q->where('branch_id',Session::get('activeBranch'))
                        ->orWhere('branch_id',0)
                        ->orWhere('branch_id',Null);
            })
            ->get();
           
        
                
             $roleId = 6;
             foreach($student as $students){
                $userresult = User::select('id','name', 'email','branch_id','password','pass_Text')
                ->where("users.hook_id", '=', $students->id)
                ->where("users.role_id", '=', $roleId)
                ->first();

                // Check if email exist for other students
                $userEmailCheck = User::select('id','name', 'email','branch_id','password','pass_Text')
                ->where("users.hook_id", '!=', $students->id)
                ->where("users.role_id", '=', $roleId)
                ->where("users.email", '=', $students->email)
                ->first();
                if($userEmailCheck){
                    // Skip creating login as email is already exist ...............
                }else{
                
                    if($userresult)
                    {
                         DB::table('users')
                        ->where("users.hook_id", '=', $students->id)
                        ->where("users.role_id", '=', $roleId)
                        ->update(['users.password'=> bcrypt($request->password),
                             'users.pass_Text' => $request->password,
                         ]);
                         $request->session()->flash($this->message_success, $this->panel. ' Password Updated Successfully.');
                    }
                    else
                    { 
                        $user =  new User;
                        $user->name= $students->first_name;
                        $user->email= $students->email;
                        $user->password = bcrypt($request->password);
                        $user->pass_Text= $request->password;
                        $user->role_id= $roleId;
                        $user->status= true;
                        $user->hook_id= $students->id;
                        $user->branch_id= Session::get('activeBranch');
                        $user->save();
    
                        DB::table('role_user')->insert([
                            'user_id' => $user->id,
                            'role_id' => $roleId
                        ]);
    
                        $request->session()->flash($this->message_success, $this->panel. ' Password Created Successfully.');
                        
                    } 
                }
            } 
            
            return redirect()->back();
        }  

        $data['faculty'] = $this->FacultyList();
        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;

        return view(parent::loadDataToView($this->view_path.'.generatepassword'), compact('data'));
    }
    */
    public function GeneratePassword(Request $request)
    {
       
        $data = [];
     
        if ($request->has('faculty')){
           
           
         $faculty = $request->get('faculty');
            if(count($faculty)>1){
          
               $student = Student::select('students.id','university_reg','first_name', 'email','branch_id','faculty','students.Semester')
                ->leftJoin('student_detail_sessionwise as st', function($join) {
                    $join->on('st.student_id', '=', 'students.id');
                  })
                ->wherein('st.course_id',$faculty)
                ->where(function($q){
                    $q->where('branch_id',Session::get('activeBranch'))
                        ->orWhere('branch_id',0)
                        ->orWhere('branch_id',Null);
                })
               ->where('st.session_id',Session::get('activeSession'))
                ->groupBy('students.id')
                ->get();
            }
            else{

                $student = Student::select('students.id','university_reg','first_name', 'email','branch_id','faculty','students.Semester')
                ->leftJoin('student_detail_sessionwise as st', function($join) {
                    $join->on('st.student_id', '=', 'students.id');
                })
                ->wherein('st.course_id',$faculty)
                ->where(function($q)use($request){
                    if($request->section){
                      $q->where('st.Semester',$request->section);
                    }
                  
                })
              
                ->where(function($q){
                     $q->where('branch_id',Session::get('activeBranch'))
                            ->orWhere('branch_id',0)
                            ->orWhere('branch_id',Null);
                })
                ->where('st.session_id',Session::get('activeSession'))
                ->groupBy('students.id')
                ->get();
            }
          
          
          
                
             $roleId = 6;
             foreach($student as $students){
                if($students->university_reg){
                    
                
                    $userresult = User::select('id','name', 'email','branch_id','password','pass_Text')
                    ->where("users.hook_id", '=', $students->id)
                    ->where("users.role_id", '=', $roleId)
                    ->first();
    
                    // Check if email exist for other students
                    $userEmailCheck = User::select('id','name', 'email','branch_id','password','pass_Text')
                    ->where("users.hook_id", '!=', $students->id)
                    ->where("users.role_id", '=', $roleId)
                    ->where("users.email", '=', $students->email)
                    ->first();
    
                   
                    if($userEmailCheck){
                            $name= str_replace(' ', '', $students->first_name);
                            $newmail= $students->university_reg.env("EMAIL_POST_FIX");
                            DB::table('users')
                            ->where("users.hook_id", '=', $students->id)
                            ->where("users.role_id", '=', $roleId)
                            ->where("users.email", '=', $students->email)
                            ->update(['users.password'=> bcrypt($request->password),
                                   'users.pass_Text'  => $request->password,
                                   'users.email'      => $newmail,
                             ]);
                            $request->session()->flash($this->message_success, $this->panel. ' Password Updated Successfully.');
                    }else{
                    
                        if($userresult)
                        {
                             DB::table('users')
                            ->where("users.hook_id", '=', $students->id)
                            ->where("users.role_id", '=', $roleId)
                            ->update(['users.password'=> bcrypt($request->password),
                                 'users.pass_Text' => $request->password,
                             ]);
                             $request->session()->flash($this->message_success, $this->panel. ' Password Updated Successfully.');
                        }
                        else
                        { 
                             $name= str_replace(' ', '', $students->first_name);
                             $newmail= $students->university_reg.env("EMAIL_POST_FIX");
                            $user =  new User;
                            $user->name= $students->first_name;
                            $user->email= $newmail;
                            $user->password = bcrypt($request->password);
                            $user->pass_Text= $request->password;
                            $user->role_id= $roleId;
                            $user->status= true;
                            $user->hook_id= $students->id;
                            $user->branch_id= Session::get('activeBranch');
                            $user->save();
        
                            DB::table('role_user')->insert([
                                'user_id' => $user->id,
                                'role_id' => $roleId
                            ]);
        
                            $request->session()->flash($this->message_success, $this->panel. ' Password Created Successfully.');
                            
                        } 
                    }
                }    
            } 
            
            return redirect()->back();
        }  

        $data['faculty'] = $this->FacultyList();
        $data['semester'] = Semester::select('id','semester')->where([
            ['status','=',1]
        ])->pluck('semester','id')->toArray();
        $data['semester']=array_prepend($data['semester'],'--Select Section--','');
        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;

        return view(parent::loadDataToView($this->view_path.'.generatepassword'), compact('data'));
    }

    public function LoginDetailList(Request $request)
    {
         $data = [];
         $roleid=6;
         if($request->all())
         {
          $data['user'] = User::select('s.first_name','s.reg_no','users.id', 'users.name', 'users.email', 'users.status','hook_id','s.branch_id','pass_Text','role_id','f.faculty','p.father_first_name','a.mobile_1 as mobile','sem.semester')
            ->leftJoin('students as s', function($join) { $join->on('s.id', '=', 'users.hook_id'); })
            ->leftJoin('student_detail_sessionwise as st', function($join) { $join->on('st.student_id', '=', 'users.hook_id'); })
            ->leftJoin('semesters as sem','sem.id','=','st.Semester')
            ->leftJoin('faculties as f', function($join) { $join->on('st.course_id', '=', 'f.id'); })
            ->leftJoin('parent_details as p', function($join) { $join->on('s.id', '=', 'p.students_id'); })
            ->leftJoin('addressinfos as a', function($join) { $join->on('s.id', '=', 'a.students_id'); })
            ->where(function ($query) use ($request) {

                if ($request->faculty) {
                    $query->where('st.course_id', '=',$request->faculty);
                    // $this->filter_query['st.course_id'] =$request->faculty;
                }

                // if ($request->status) {
                //     if($request->status != ''){
                //     $query->where('users.status', $request->status == 'active'?1:0);
                //     $this->filter_query['users.status'] = $request->get('status');
                //      }
                // }
            })
            // ->where(function($q){
            //      $q->where('branch_id',Session::get('activeBranch'))
            //             ->orWhere('branch_id',0)
            //             ->orWhere('branch_id',Null);
            // })
            ->where('s.branch_id',Session::get('activeBranch'))
            ->where('st.session_id',Session::get('activeSession'))
             ->where('role_id','=',$roleid) 
             
             ->orderBy('st.course_id','asc') 
             ->orderBy('st.Semester','asc') 
             ->orderBy('s.first_name','asc') 
            ->get(); 
         }
        
        $data['faculty'] = $this->FacultyList();
        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;
       

        return view(parent::loadDataToView($this->view_path.'.logindetail'), compact('data'));
       
    }
    /* ***********[Login code {END}]*********** */
    
    
    /* Dashboard Graph By Ankur */
    public function graph()
    {   
        $branch_id  = Session::get('activeBranch');
        $session_id=Session::get('activeSession');  
        $data['gph'] = Student::select('status','reg_no', DB::raw('count(*) as total'),DB::raw('count(*) as total_reg'))
            ->groupBy('status')
            ->get();
        $data['course'] = Student::select('f.id','students.faculty','sds.session_id','f.faculty','reg_no','students.batch_id','reg_date', DB::raw('count(*) as total_course'),DB::raw('count(*) as total_reg'),DB::raw('count(reg_date) as todate'))
        ->leftjoin('faculties as f','f.id','=','students.faculty')
        ->leftjoin('student_detail_sessionwise as sds','sds.student_id','=','students.id')
        ->where('students.branch_id',$branch_id)
        ->where('sds.session_id',$session_id)
        //->where('reg_date','=',$data)
       // $yesterday = Carbon::yesterday();
        ->groupBy('students.faculty')
        ->get();
        //dd($data['course']);

        foreach($data['course'] as $key =>$value){

           
            //dd($value->faculty);
            $todayRegNo= DB::table('students')
            ->where('faculty',$value->id)
            ->whereDate('reg_date','=',Carbon::today()->toDateString())
            ->count();
           
            $data['course'][$key]->today = $todayRegNo;


        }

            $data['sem'] = DB::table('faculty_semester')->select('semesters.id','faculty_id as course_id','semester_id as Semester','semesters.semester','faculties.faculty')
            ->leftJoin('semesters','semesters.id','=','faculty_semester.semester_id')
            ->leftjoin('faculties','faculties.id','=','faculty_semester.faculty_id')
             ->where('faculties.branch_id',session::get('activeBranch'))
           
            ->get();
            foreach($data['sem'] as $key=>$value){
                //dd($value);
            $no_stu=DB::table('student_detail_sessionwise as sds')->select()
            ->where('course_id','=',$value->course_id)
           
            ->where('sds.session_id',session::get('activeSession'))
            ->where('Semester','=',$value->Semester)
            ->count('Semester');

            $data['sem'][$key]->total=$no_stu;
           
            }
        return view(parent::loadDataToView($this->view_path.'.student_dashboard.graph'), compact('data'));
    }
    
    /*bulk edit student*/
    public function BulkEditStudent(Request $request)
    {
        $data=[];
        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;
        $data['faculties'] = $this->activeFaculties();  
        
        $classTeacher= TeacherCoordinator::where('teacher_id',Auth::user()->hook_id)
        ->where('branch_id',session::get('activeBranch'))
        ->where('record_status',1)
        ->where(function($q)use($request){
            if($request->faculty){
                $q->where('faculty_id',$request->faculty);
            }
        })
        ->where('session_id',Session::get('activeSession'))->pluck('section_id')->toArray();
        $data['section']= Semester::select('id','semester')->where([
            ['status','=',1]
        ])
        ->Where(function($q)use($classTeacher){
            if(count($classTeacher)>0){
                $q->whereIn('id',$classTeacher);
            }
        })
        ->pluck('semester','id')->toArray();
        $data['section']=array_prepend($data['section'],'--Select Section--','');


        $edit= $request->edit;
        
        if($request->all()){
             $data['student']= DB::table('student_detail_sessionwise as sds')->select('sds.id','sds.course_id','sds.Semester','s.first_name','sds.roll_no','pd.father_first_name','s.reg_no')
             ->leftjoin('students as s','s.id','=','sds.student_id')
              ->leftjoin('parent_details as pd', 'sds.student_id', '=', 'pd.students_id')
             ->where('sds.course_id',$request->faculty)
             ->where('sds.Semester',$request->semester)
             ->where('s.branch_id',session::get('activeBranch'))
             ->where('sds.session_id',session::get('activeSession'))
             ->where('sds.active_status',1)
             ->where('s.status',1)
             
             ->get();
             
        }
        return view(parent::loadDataToView($this->view_path.'.bulk-student-edit.index'), compact('data','edit'));
        
    }

    public function Bulk_update_student_data(Request $request)
    {
      if($request->edit==1){
         $rollno=$request->roll_no;
           foreach ($rollno as $key => $value) {
             $update= DB::table('student_detail_sessionwise')->where('id',$key)->update([
              'roll_no'=> $value,
              'updated_at'=> Carbon::now(),
              'updated_by'=> auth()->user()->id,

             ]);
           }
           $request->session()->flash($this->message_success,'Record Updated Successfully');
           return redirect()->route($this->base_route.'.bulk_edit_student');
       }
       if($request->edit==2){
           $semester=$request->Semester;
           foreach ($semester as $key => $value) {
             $update= DB::table('student_detail_sessionwise')->where('id',$key)->update([
              'Semester'=> $value,
              'updated_at'=> Carbon::now(),
              'updated_by'=> auth()->user()->id,

             ]);
           }
           $request->session()->flash($this->message_success,'Record Updated Successfully');
           return redirect()->route($this->base_route.'.bulk_edit_student');
       }
        $request->session()->flash($this->message_danger,'invalidRequest');
        return redirect()->route($this->base_route.'.bulk_edit_student');
    }
    /*bulk edit student*/
    
    public function DeleteSibling(Request $request)
    {
        $delete= DB::table('student_sibling')->where('id',$request->id)->delete();

    }
    
    public function leave(Request $request, $id){
        $data['id']=$id;
        if($data['id']){
            //dd($id);
            $data['school_leaving_certificate']=school_leaving_certificate::select('*')
            ->where('student_id',$data['id'])
            ->first();

            if($data['school_leaving_certificate']){
                 //dd($data['school_leaving_certificate']);

                
                $data['res']=$data['school_leaving_certificate'];
                //dd($data);


               return view(parent::loadDataToView($this->view_path.'.leave.index'),compact('data'));

            }else{

                $data['student'] = Student::select('students.id as student_id','students.university_reg','students.religion_id','students.academic_status', 'students.first_name as student_name','students.session_id','students.date_of_birth as date_of_birth','students.gender','students.nationality as nationality', 'students.status','students.admission_condition','pd.father_first_name as father_name','pd.mother_first_name as mother_name','ai.address', 'ai.state','ai.temp_country','ai.mobile_1','cb.id as batch_id','sts.subject','students.previous_school','students.previous_class')
                    ->where('student_id','=',$data['id'])
                    ->leftjoin('parent_details as pd', 'pd.students_id', '=', 'students.id')
                    ->leftjoin('addressinfos as ai', 'ai.students_id', '=', 'students.id')
                    ->leftjoin('course_batches as cb','cb.id','=','students.batch_id')
                    ->leftjoin('student_detail_sessionwise as sts','sts.student_id','=','students.id')
                    ->where('sts.session_id',Session::get('activeSession'))
                    ->first();
                    $data['res']=$data['student'];
                        
            }
        }
        return view(parent::loadDataToView($this->view_path.'.leave.index'),compact('data'));
    }
    public function leave_store(Request $request,$id){
        $data['id']=$id;
        
        $arr=(array)$request->all();
        unset($arr['_token']);
        
        $request->request->add(['student_id' => $id]);
       
         if($data['id']){
            DB::table('school_leaving_certificate')->updateOrInsert(
                [
                'student_id'=>$data['id'],
                'branch_id'=>session::get('activeBranch'),
                'session_id'=>session::get('activeSession'),
                
                ],
                $arr
          ); 
        }

        $request->session()->flash($this->message_success, $this->panel. ' Add Successfully.');
        return redirect($this->base_route.'/'.$data['id'].'/'.'leave_print');
    }
    public function leave_print(Request $request,$id){
        $data['id']=$id;
        $data['school_leaving_certificate']=school_leaving_certificate::select('*')
        ->where('student_id',$data['id'])
        ->first();
        
        return view(parent::loadDataToView($this->view_path.'.leave.print'),compact('data'));
    }
    
    
    public function yearly_payment_report(Request $request, $id){
        
        $data['faculty']=Faculty::where('status', '1')->where('branch_id', session('activeBranch'))->pluck('faculty', 'id')->toArray();
        
        $branch=Branch::where('id', session('activeBranch'))->first();
        
        
        $data['session']=DB::table('session')->select('id','session_name')
        ->where('id',Session::get('activeSession'))
        ->first();
        
        
        $session_name = $data['session']->session_name;
        $data=explode('-',($data['session']->session_name));
        $from_date=$data[0];
        
        
        
        $to=($from_date+1).'-03-31';
        $from_date=$data[0].'-04-01';
         
         
        $data['collect']=DB::table('collect_fee')->select('collect_fee.id','collect_fee.reciept_no','collect_fee.student_id','collect_fee.amount_paid','collect_fee.discount','collect_fee.reciept_date','assign_fee.fee_head_id','fee_heads.fee_head_title')
        
        ->leftjoin('assign_fee','collect_fee.assign_fee_id','=','assign_fee.id')
        ->leftjoin('fee_heads','assign_fee.fee_head_id','=','fee_heads.id')
        
        ->where('collect_fee.student_id',$id)
        //  ->where(function ($query) use($to,$from_date){
        //     $query->wherebetween('reciept_date',[$from_date,$to]);
           
        // })
        ->where('assign_fee.session_id',Session::get('activeSession'))
        ->where([
                ['collect_fee.status','=',1]
            ])
            
        ->orderBy('collect_fee.reciept_date') 
        ->get(); 
         
        $data['student'] = DB::table('students')->select('students.reg_no','students.first_name','faculties.faculty','parent_details.father_first_name','addressinfos.mobile_1')
        
        ->leftjoin('student_detail_sessionwise as sts','sts.student_id','=','students.id')
        ->leftjoin('faculties','sts.course_id','=','faculties.id')
        
        ->leftjoin('parent_details','students.id','=','parent_details.students_id')
        ->leftjoin('addressinfos','students.id','=','addressinfos.students_id')
        ->where([
                ['sts.session_id','=',Session::get('activeSession')],
                ['sts.active_status','=',1],
                ['students.id','=',$id],
            ])
            ->groupBy('students.id')
            ->first();
            // dd($data);
        if(count($data['collect']) <= 0){
            return redirect()->back()->with('message_success','No Data Found');
        }
        
        
        return view(parent::loadDataToView($this->view_path.'.yearly_payment_report.report'),compact('data','branch','session_name'));
    }


}
