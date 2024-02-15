<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\CollegeBaseController;
use App\Http\Requests\Student\Document\AddValidation;
use App\Http\Requests\Student\Document\EditValidation;
use App\Models\Document;
use App\Models\Student;
use Illuminate\Http\Request;
use App\StudentPromotion;
use ViewHelper;
use view;
use DB,Session,Log;
use App\Models\Faculty; 
use App\Models\AcademicInfo;
use App\Models\Addressinfo;
use App\Models\ParentDetail;
use App\Models\StudentDetailSessionwise;
use App\Models\GuardianDetail;
use Carbon\Carbon;

class TransferStudentController extends CollegeBaseController
{
    protected $base_route = 'student';
    protected $view_path = 'student';
    protected $panel = 'Student';

    public function __construct()
    {
       
    }

    public function index(Request $request)
    {
        $data = [];
        if($request->all()) {
            $data['student'] = StudentPromotion::select('students.id', 'students.reg_no', 'students.reg_date', 'students.first_name', 'student_detail_sessionwise.course_id as faculty', 'students.semester', 'students.status', 'student_detail_sessionwise.session_id as requested_session', 'student_detail_sessionwise.course_id', 'student_detail_sessionwise.Semester','pd.father_first_name as father_name')
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
        $data['branches']=DB::table('branches')->select('id','branch_title as branch')->pluck('branch','id')->toArray();
        $data['branches']=array_prepend($data['branches'],'--Select Branch--','');

        $ssn_list=DB::table('session')->select('id', 'session_name')
        // ->where('id',Session::get('activeSession'))
        ->pluck('session_name', 'id')->toArray();
        $data['semester'] =DB::table('semesters')->select('semester','id')->pluck('semester','id')->toArray(); 
        $data['semester']=array_prepend($data['semester'],'--Select Section--','');
        return view(parent::loadDataToView($this->view_path.'.student-transfer.index'),compact('data','ssn_list'));
    }
    public function transferStudent($id,$session){
        $data['student'] = StudentPromotion::select('students.id', 'students.reg_no', 'students.reg_date', 'students.first_name', 'student_detail_sessionwise.course_id as faculty', 'students.semester', 'students.status', 'student_detail_sessionwise.session_id as requested_session', 'student_detail_sessionwise.course_id', 'student_detail_sessionwise.Semester','students.branch_id')
            ->leftJoin('students','student_detail_sessionwise.student_id', '=', 'students.id')
            ->where('students.id',$id)
            ->where('student_detail_sessionwise.session_id',$session)
            ->get();
            $data['faculties'] = $this->activeFaculties();
            $data['branches']=DB::table('branches')->select('id','branch_title as branch')->pluck('branch','id')->toArray();
            $data['branches']=array_prepend($data['branches'],'--Select Branch--','');

        $ssn_list=DB::table('session')->select('id', 'session_name')->pluck('session_name', 'id')->toArray();
        $ssn_list=array_prepend($ssn_list,"--Select Session--","");
        $old_assign_fee=DB::table('assign_fee')->select('assign_fee.*','fee_heads.fee_head_title')->where([
            ['session_id','=',$data['student'][0]['requested_session']],
            ['branch_id','=',$data['student'][0]['branch_id']],
            ['course_id','=',$data['student'][0]['course_id']],
            ['branch_id','=',$data['student'][0]['branch_id']],
            ['student_id','=',0]
        ])->leftjoin('fee_heads','fee_heads.id','=','assign_fee.fee_head_id')
        ->get();
        
        foreach ($old_assign_fee as $key => $value) {
          $old_collect_fee[$value->id]=DB::table('collect_fee')->select('*')
          ->where([
            ['student_id','=',$id],
            ['assign_fee_id','=',$value->id]
            ])->get();
        }
            return view(parent::loadDataToView($this->view_path.'.student-transfer.transfer'),compact('data','ssn_list','old_collect_fee','old_assign_fee','id','session'));
    }
    public function findCourse(Request $request){
        $response=[];
        $response['error']=true;
        if($request->branch_id && $request->session_id){
            $data=Faculty::select('id','faculty')->where([
                ['branch_id',$request->branch_id]
            ])->get();
            if($data){
                $response['course']=$data;
                $response['error']=false;
                $response['success'] = env("course_label").' Found';
            }else{
                $response['error']='NO '.env("course_label").'  Found';
            }
        }
        else{
            $response['message']='Please Select Branch And session';
        }
        return response()->json(json_encode($response));
    }
    public function getAssignedFee(Request $request){
        $response=[];
        $response['error']=true;
        if($request->branch_id && $request->session_id && $request->course_id && $request->section_id){
            $data=DB::table('assign_fee')->select('assign_fee.*','fee_heads.fee_head_title')->where([
                ['branch_id','=',$request->branch_id],
                ['session_id','=',$request->session_id],
                ['course_id','=',$request->course_id],
                ['assign_fee.status','=',1],
                ['student_id','=',0]
            ])->leftjoin('fee_heads','fee_heads.id','=','assign_fee.fee_head_id')
            ->get();
            
            if($data){
                $response['data']=$data;
                $response['error']=false;
                $response['success']="Assign Fee Found";
            }else{
                $response['error']='NO '.env("course_label").' Found';
            }
        }else{
            $response['message']='Invalid Request!';
        }
         return response()->json(json_encode($response));
    }
    public function transfer(Request $request){
        // dd();
        $id=$request->student;
        $toBranch=$request->branch;
        $toSession=$request->session;
        $toCourse=$request->course;
        $toSemester=$request->semester;

        $student=Student::findOrFail($id);
        $academic=AcademicInfo::where('students_id',$id)->first();
        $address=Addressinfo::where('students_id',$id)->first();
        $parent=ParentDetail::where('students_id',$id)->first();
        $session_std=StudentDetailSessionwise::where([
            ['student_id','=',$id],
            ['session_id','=',$request->student_session]
        ])->first();
        
        $guardian=DB::table('student_guardians')->select('gd.guardian_first_name','gd.id','gd.guardian_middle_name','guardian_last_name','guardian_eligibility','guardian_occupation','guardian_office','guardian_office_number','guardian_residence_number','guardian_mobile_1','guardian_mobile_2','guardian_email','guardian_relation','guardian_address','guardian_image','status')
        ->leftjoin('guardian_details as gd','gd.id','=','student_guardians.guardians_id')
        ->where('student_guardians.students_id',$id)
        ->first();

                
        if($student){
            $data=DB::table('students')->where('id',$student->id)->update([
                'reg_no'=>$student->reg_no.'/'.Carbon::now()->format('Y-m-d')
            ]);

            $inserted_id=Student::insertGetId([
                'created_at'=>Carbon::now(),
                'created_by'=>auth()->user()->id,
                'reg_no'    =>$student->reg_no,
                'reg_date'  =>$student->reg_date,
                'university_reg'=>$student->university_reg,
                'faculty'   =>$toCourse,
                'semester'=>$toSemester,
                'academic_status'=>$student->academic_status,
                'first_name'=>$student->first_name,
                'middle_name'=>$student->middle_name,
                'last_name'=>$student->last_name,
                'date_of_birth'=>$student->date_of_birth,
                'gender'=>$student->gender,
                'blood_group'=>$student->blood_group,
                'nationality'=>$student->nationality,
                'mother_tongue'=>$student->mother_tongue,
                'email'     =>$student->email,
                'extra_info'=>$student->extra_info,
                'student_image'=>$student->student_image,
                'status'=>1,
                'branch_id'=>$toBranch,
                'org_id'=>$student->org_id,
                'category_id'=>$student->category_id,
                'session_id'=>$toSession,
                'zip'   =>$student->zip,
                'indose_number'=>$student->indose_number,
                'passport_no'=>$student->passport_no
            ]);
           
            if(isset($inserted_id)){
                    Student::where('id',$id)->update([
                    'status'=>4
                ]);
                $st_ssn=StudentDetailSessionwise::insert([
                    'course_id'=>$toCourse,
                    'student_id'=>$inserted_id,
                    'session_id'=>$toSession,
                    'Semester'  =>$toSemester,
                    'created_by'=>auth()->user()->id,
                    'created_at'=>Carbon::now()
                ]);
                    if($session_std){
                        StudentDetailSessionwise::where('id',$session_std['id'])->update([
                            'active_status'=>4
                        ]);
                    }
                if($academic){
                    $ac=AcademicInfo::insert([
                        'created_at'=>Carbon::now(),
                        'created_by'=>auth()->user()->id,
                        'students_id'=>$inserted_id,
                        'institution'=>$academic->institution,
                        'board' =>$academic->board,
                        'pass_year'=>$academic->pass_year,
                        'symbol_no' =>$academic->symbol_no,
                        'percentage'=>$academic->percentage,
                        'division_grade'=>$academic->division_grade,
                        'major_subjects'=>$academic->major_subjects,
                        'remark'    =>$academic->remark,
                        'sorting_order'=>$academic->sorting_order,
                        'status'=>$academic->status

                    ]);
                    AcademicInfo::where('students_id',$id)->update([
                        'status'=>4
                    ]);
                }
                if($address){
                    $ad=Addressinfo::insert([
                        'created_at'=>Carbon::now(),
                        'created_by'=>auth()->user()->id,
                        'students_id'=>$inserted_id,
                        'address'=>$address->address,
                        'state'=>$address->state,
                        'country'=>$address->country,
                        'temp_address'=>$address->temp_address,
                        'temp_state'    =>$address->temp_state,
                        'temp_country'=>$address->temp_country,
                        'home_phone'    =>$address->home_phone,
                        'mobile_1'  =>$address->mobile_1,
                        'mobile_2'  =>$address->mobile_2,
                        'status'    =>$address->status,
                    ]);
                    Addressinfo::where('students_id',$id)->update(['status'=>4]);
                }
                if($parent){
                    $par=ParentDetail::insert([
                        'created_at'=>Carbon::now(),
                        'created_by'=>auth()->user()->id,
                        'students_id'=>$inserted_id,
                        'grandfather_first_name'=>$parent->grandfather_first_name,
                        'grandfather_middle_name'=>$parent->grandfather_middle_name,
                        'grandfather_last_name'=>$parent->grandfather_last_name,
                        'father_first_name'=>$parent->father_first_name,
                        'father_middle_name'=>$parent->father_middle_name,
                        'father_last_name'=>$parent->father_last_name,
                        'father_eligibility'=>$parent->father_eligibility,
                        'father_occupation'=>$parent->father_occupation,
                        'father_office'=>$parent->father_office,
                        'father_office_number'=>$parent->father_office_number,
                        'father_residence_number'=>$parent->father_residence_number,
                        'father_mobile_1'=>$parent->father_mobile_1,
                        'father_mobile_2'=>$parent->father_mobile_2,
                        'father_email'  =>$parent->father_email,
                        'mother_first_name'=>$parent->mother_first_name,
                        'mother_middle_name'=>$parent->mother_middle_name,
                        'mother_last_name'=>$parent->mother_last_name,
                        'mother_eligibility'=>$parent->mother_eligibility,
                        'mother_occupation'=>$parent->mother_occupation,
                        'mother_office'=>$parent->mother_office,
                        'mother_office_number'=>$parent->mother_office_number,
                        'mother_residence_number'=>$parent->mother_residence_number,
                        'mother_mobile_1'=>$parent->mother_mobile_1,
                        'mother_mobile_2'=>$parent->mother_mobile_2,
                        'mother_email'=>$parent->mother_email,
                        'father_image'=>$parent->father_image,
                        'mother_image'=>$parent->mother_image,
                        'status'=>$parent->status
                    ]);
                    ParentDetail::where('students_id',$id)->update(['status'=>4]);

                }
                if($guardian){
                    $guar=GuardianDetail::insertGetId([
                        'created_at'=>Carbon::now(),
                        'created_by'=>auth()->user()->id,
                        'guardian_first_name'=>$guardian->guardian_first_name,
                        'guardian_middle_name'=>$guardian->guardian_middle_name,
                        'guardian_last_name'=>$guardian->guardian_last_name,
                        'guardian_eligibility'=>$guardian->guardian_eligibility,
                        'guardian_occupation'=>$guardian->guardian_occupation,
                        'guardian_office'=>$guardian->guardian_office,
                        'guardian_office_number'=>$guardian->guardian_office_number,
                        'guardian_residence_number'=>$guardian->guardian_residence_number,
                        'guardian_mobile_1'=>$guardian->guardian_mobile_1,
                        'guardian_mobile_2'=>$guardian->guardian_mobile_2,
                        'guardian_email'=>$guardian->guardian_email,
                        'guardian_relation'=>$guardian->guardian_relation,
                        'guardian_address'=>$guardian->guardian_address,
                        'guardian_image'=>$guardian->guardian_image,
                        'status'=>$guardian->status
                    ]);
                    GuardianDetail::where('id',$guardian->id)->update([
                        'status'=>4
                    ]);
                    $data=DB::table('student_guardians')->insert([
                        'created_at'=>Carbon::now(),
                        'students_id'=>$inserted_id,
                        'guardians_id'=>$guar,
                    ]);
                }
                
                   
                if($request->assign){
                   
                   foreach ($request->assign as $key => $value) {
                        // dd($key,$request->assign,$value,$id);
                       $assign_faculty_fee[$value][]=DB::table('collect_fee')->select('*')->where([
                        ['assign_fee_id','=',$key],
                        ['student_id','=',$id]
                    ])->get();
                   }
                   
                   if(isset($assign_faculty_fee)){

                        foreach ($assign_faculty_fee as $key => $value) {
                          
                          foreach ($value as $keyval => $v) {
                            foreach ($v as $k => $val) {
                               
                              $col= DB::table('collect_fee')->insert([
                                    'reciept_no'=>$val->reciept_no,
                                    'student_id'=>$inserted_id,
                                    'assign_fee_id'=>$key,
                                    'amount_paid'=>$val->amount_paid,
                                    'discount'=>$val->discount,
                                    'fine'     =>$val->fine,
                                    'remarks'   =>$val->remarks,
                                    'created_at'=>Carbon::now(),
                                    'reciept_date'=>$val->reciept_date,
                                    'payment_type'=>$val->payment_type,
                                    'reference' =>$val->reference,
                                    'created_by'=>auth()->user()->id,
                                    'status'=>$val->status
                               ]);
                              if($col){
                                DB::table('collect_fee')->where('id',$val->id)->update([
                                    'status'=>4,
                                    'remarks'=>'Transferred/'.$inserted_id.' '.Carbon::now()->format('Y-m-d')
                                ]);
                              }
                           }
                          }
                        }
                   }

                }
              
                    $assign_on_student=DB::table('assign_fee')->select('id','fee_head_id','fee_amount','times','status')->where([
                            ['session_id','=',$session_std['session_id']],
                            ['course_id','=',$session_std['course_id']],
                            ['branch_id','=',$student->branch_id],
                            ['student_id','=',$id]
                        ])
                    ->get();
                if(isset($assign_on_student)) {
                    foreach ($assign_on_student as $key => $value) {
                        // dd($value);
                        $student_new_assign_id=DB::table('assign_fee')->insertGetId([
                            'branch_id'=>$toBranch,
                            'session_id'=>$toSession,
                            'course_id'=>$toCourse,
                            'student_id'=>$inserted_id,
                            'fee_head_id'=>$value->fee_head_id,
                            'fee_amount'=>$value->fee_amount,
                            'created_at'=>Carbon::now(),
                            'times'     =>$value->times,
                            'status'    =>$value->status,
                            'created_by'=>auth()->user()->id
                        ]);
                       $collect_on_student=DB::table('collect_fee')->select('*')->where([
                                ['assign_fee_id','=',$value->id],
                                ['student_id','=',$id]
                            ])->get();
                        if(isset($collect_on_student)){
                            foreach ($collect_on_student as $key => $val) {
                               $data=DB::table('collect_fee')->insert([
                                    'reciept_no'=>$val->reciept_no,
                                    'student_id'=>$inserted_id,
                                    'assign_fee_id'=>$student_new_assign_id,
                                    'amount_paid'=>$val->amount_paid,
                                    'discount'  =>$val->discount,
                                    'fine'      =>$val->fine,
                                    'created_at'=>Carbon::now(),
                                    'reciept_date'=>$val->reciept_date,
                                    'payment_type'=>$val->payment_type,
                                    'reference'=>$val->reference,
                                    'created_by'=>auth()->user()->id,
                                    'status'   =>$val->status
                               ]);
                               if($data){
                                DB::table('collect_fee')->where('id',$val->id)->update([
                                    'status'=>4,
                                    'remarks'   =>'Transferred/'.Carbon::now()->format('Y-m-d'),
                                ]);
                               }
                            }
                        }
                       
                    }
                   
                }     
            }
        }
        return redirect()->route('student.transfer-student')->with('message_success','Student Transferred');
    }
   

    
}