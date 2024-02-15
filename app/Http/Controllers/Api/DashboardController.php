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
use App\Models\StudentStatus;
use App\Models\Source;
use App\Models\ComplainType;

use App\Models\Resident;
use App\Models\TransportUser;

use App\Models\Book;
use App\Models\BookCategory;
use App\Models\BookMaster;
use App\Models\BookStatus;
use App\Models\LibraryCirculation;
use App\Models\BookIssue;
use App\Models\InventorySupplier;
use App\Models\Product;
use App\Models\Transaction;
/*new modals 04-09-2021*/
use App\Models\StudentDetailSessionwise;
class DashboardController extends CollegeBaseController
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

   

   




    public function enquiryList() 
    {
     $sessions =DB::table('session')->select('*')->Where('active_status','=',1)->first();
     $session= $sessions->id;
     
     $data['academicInfos'] =DB::table('enquiries')->select('branch_id',DB::raw('count(*) as total'))
    ->Where('session_id', $session)
    ->groupBy('branch_id')->get();
      return $this->return_data_in_json($data);
    }

    public function admission_form_sale()
    {

      $sessions =DB::table('session')->select('*')->Where('active_status','=',1)->first();
      $session= $sessions->id;
     
     $data['student'] =DB::table('admissions')->select('status','branch_id',DB::raw('count(*) as total'))
     ->Where('session_id', $session)
     ->groupBy('branch_id')
     ->groupBy('status')->get();
      return $this->return_data_in_json($data);


    }

     public function Total_Students()
    {
     $sessions =DB::table('session')->select('*')->Where('active_status','=',1)->first();
     $session= $sessions->id;
     
     $data['student'] =DB::table('students')->select('branch_id',DB::raw('count(*) as total'))
     ->Where('session_id', $session)
     ->groupBy('branch_id')->get();
      return $this->return_data_in_json($data);

    }

    public function Month_wise_fee()
    {

      $sessions =DB::table('session')->select('*')->Where('active_status','=',1)->first();
      $session= $sessions->id;
     
      $data['monthfee'] =DB::table('assign_fee')->select('due_month','branch_id',DB::raw('sum(fee_amount) as totalamount'))
     ->Where('session_id', $session)
     ->groupBy('branch_id')
     ->groupBy('due_month')->get();
      return $this->return_data_in_json($data);


    }
     public function Branch_wise_fee()
    {

      $sessions =DB::table('session')->select('*')->Where('active_status','=',1)->first();
      $session= $sessions->id;
      $monthfee =DB::table('assign_fee')->select('*')
     ->Where('session_id', $session)
     ->get();
        $arrFeeMaster = [];
        if(count($monthfee)){$k=0;
            foreach($monthfee as $fee){
                $k++;
                $paid_result=DB::table('collect_fee')->where('assign_fee_id', $fee->id)
                // ->Where('status' , 1)
                ->sum('amount_paid');
                // $arr[] = $paid_result;
                $assignfee_result=DB::table('assign_fee')->where('branch_id', $fee->branch_id)
                ->Where('status' , 1)->sum('fee_amount');
                $due = $assignfee_result - $paid_result;
                
                $arrFeeMaster[$fee->branch_id]['branch_id'] = $fee->branch_id;
                $arrFeeMaster[$fee->branch_id]['amount'] = $assignfee_result;
                 if(isset($arrFeeMaster[$fee->branch_id]['paid']))
                 {
                    $arrFeeMaster[$fee->branch_id]['paid'] = $paid_result+$arrFeeMaster[$fee->branch_id]['paid'];
                 }
                 else
                 {
                  $arrFeeMaster[$fee->branch_id]['paid'] = $paid_result;
                 }
                $arrFeeMaster[$fee->branch_id]['due'] = $due;
                
            }

        }


      return $this->return_data_in_json($arrFeeMaster);


    } 
     public function Due_Course_wise_fee()
    {

    //   $sessions =DB::table('session')->select('*')->Where('active_status','=',1)->first();
    //   $session= $sessions->id;
    //   $data['monthfee'] =DB::table('assign_fee')->select('branch_id','course_id',DB::raw("(sum(fee_amount)- sum(amount_paid)) as dueamount"))
    //  ->Where('session_id', $session)
    // ->leftjoin('collect_fee','assign_fee.id','=','collect_fee.assign_fee_id')
    //  ->groupBy('branch_id')
    //  ->groupby('course_id')
    //  ->get();
    // // dd($data['monthfee']);

    //   return $this->return_data_in_json($data);

      $sessions =DB::table('session')->select('*')->Where('active_status','=',1)->first();
      $session= $sessions->id;
      $monthfee =DB::table('assign_fee')->select('*')
     ->Where('session_id', $session)
     ->get();
     
        $arrFeeMaster = [];
        if(count($monthfee)){$k=0;
            foreach($monthfee as $fee){
                $k++;
                $paid_result=DB::table('collect_fee')->where('assign_fee_id', $fee->id)
                // ->Where('status' , 1)
                ->sum('amount_paid');
                 
                $assignfee_result=DB::table('assign_fee')->where('branch_id', $fee->branch_id)
                ->where('course_id', $fee->course_id)
                ->Where('status' , 1)->sum('fee_amount');
               
                $due = $assignfee_result - $paid_result; 
                $arrFeeMaster[$fee->branch_id][$fee->course_id]['branch_id'] = $fee->branch_id;
                $arrFeeMaster[$fee->branch_id][$fee->course_id]['course_id'] = $fee->course_id;
                $arrFeeMaster[$fee->branch_id][$fee->course_id]['due'] = $due;
               
                
            }

        }


      return $this->return_data_in_json($arrFeeMaster);




    }

    public function EnuiryListWithCondition(Request $request) 
    {
        $branch= $request->branch?$request->branch:'';
        $status= $request->status?$request->status:'';
        $daterange= $request->daterange?$request->daterange:'';
        $recordid= $request->recordid?$request->recordid:'';
        $month= $request->month?$request->month:'';
        $from_date= $request->from_date?$request->from_date:'';
        $to_date= $request->to_date?$request->to_date:'';
        $studid= $request->studid?$request->studid:'';


      $sessions =DB::table('session')->select('*')->Where('active_status','=',1)->first();
      $session= $sessions->id;
     $data['academicInfos'] =DB::table('enquiries')->select('enquiries.id','enquiries.branch_id','enquiries.first_name','enquiries.date_of_birth','enquiries.address','enquiries.country','enquiries.state','enquiries.city','enquiries.gender','enquiries.course','enquiries.academic_status','enquiries.email','enquiries.mobile','enquiries.extra_info','enquiries.next_follow_up','enquiries.no_of_child','enquiries.religion_id','enquiries.handicap_id','enquiries.enq_status','enquiries.responce','enquiries.reference','enquiries.refby','enquiries.enq_date','enquiries.status','enquiries.session_id','admissions.id','admissions.enquiry_id','students.id','students.admission_id')
        ->leftjoin('admissions','admissions.enquiry_id','=','enquiries.id')
        ->leftjoin('students','students.admission_id','=','admissions.id')
        ->Where('enquiries.session_id', $session)
        ->where(function($q) use ($branch){
            if($branch!=""){
                $q->where('enquiries.branch_id',$branch);
            }
        })
       ->where(function($q) use ($status){
            if($status!=""){
                $q->where('enquiries.status',$status);
            }
        })
       ->where(function($q) use ($month){
            if($month!=""){
                $q->whereRaw('Month(enquiries.enq_date)=?',[$month]);
            }
        })
       ->where(function($q) use ($recordid){
            if($recordid!=""){
                $q->where('enquiries.id',$recordid);
            }
        })
        ->where(function($q) use ($from_date,$to_date){
            if($from_date!="" & $to_date!= ""){
                 $q->wherebetween('enquiries.enq_date',array($from_date,$to_date));
            }
        })
         ->where(function($q) use ($studid){
            if($studid!=""){
                $q->where('students.id',$studid);
            }
        })
      ->get();       
      return $this->return_data_in_json($data);
    } 


    // student List 

    public function StudentListWithCondition(Request $request) 
    {
        // dd($request->all());
        $branch= $request->branch?$request->branch:'';
        $status= $request->status?$request->status:'';
        $regno= $request->regno?$request->regno:'';
        $enrollno= $request->enrollno?$request->enrollno:'';
        $month= $request->month?$request->month:'';
        $from_date= $request->from_date?$request->from_date:'';
        $to_date= $request->to_date?$request->to_date:'';
        $studid= $request->studid?$request->studid:'';
        $courseid= $request->courseid?$request->courseid:'';
        $sectionid= $request->sectionid?$request->sectionid:'';

        

      $sessions =DB::table('session')->select('*')->Where('active_status','=',1)->first();
      // $session= $sessions->id;
      $sessionId= $request->sessionId?$request->sessionId:$sessions->id;

     $data['studentlist'] =DB::table('student_detail_sessionwise as stsession')->select('stsession.*','stsession.student_id','stsession.id','stsession.session_id')
     ->leftjoin('students as std','std.id','=','stsession.student_id')
     ->Where('stsession.session_id', $sessionId)
     // ->orwhere('stsession.session_id', $session)

        ->where(function($q) use ($branch){
            if($branch!=""){
                $q->where('std.branch_id',$branch);
            }
        })
       ->where(function($q) use ($status){
            if($status!=""){
                $q->where('std.status',$status);
            }
        })
       ->where(function($q) use ($month){
            if($month!=""){
                
                $q->whereRaw('Month(std.reg_date)=?',[$month]);
            }
        })
       ->where(function($q) use ($regno){
            if($regno!=""){
                $q->where('std.reg_no',$regno);
            }
        })
        ->where(function($q) use ($from_date,$to_date){
            if($from_date!="" & $to_date!= ""){
                 $q->wherebetween('std.reg_date',array($from_date,$to_date));
            }
        })
         ->where(function($q) use ($studid){
            if($studid!=""){
                $q->where('std.id',$studid);
            }
        })
          ->where(function($q) use ($enrollno){
            if($enrollno!=""){
                $q->where('std.university_reg', "like", "%" . $enrollno . "%")
                   ->orwhere('std.admission_condition', "like", "%" . $enrollno . "%");
            }
        })
       ->where(function($q) use ($courseid){
        if($courseid!=""){
            $q->where('stsession.faculty',$courseid);
        }
        })
        ->where(function($q) use ($sectionid){
        if($sectionid!=""){
            $q->where('stsession.semester',$sectionid);
        }
        })
        ->get();
        
      return $this->return_data_in_json($data);
    } 
     public function DuefeeColection(Request $request)
    {
        $branch= $request->branch?$request->branch:'';
        $sessions =DB::table('session')->select('*')->Where('active_status','=',1)->first();
        $session= $sessions->id;
        $sessionId= $request->sessionId?$request->sessionId:$session;
        $monthfee =DB::table('assign_fee')->select('*')
        ->where('status','=',1)
        ->where(function($q) use ($branch){
        if($branch!=""){
        $q->where('branch_id',$branch);
        }
        })
        ->where(function($q) use ($sessionId){
        if($sessionId!=""){
        $q->where('session_id',$sessionId);
        }
        })->get();

          $fee=0;
           $paid=0;
         foreach ($monthfee as $key => $value) {
         if($value->student_id==0){
             $count=DB::table('students')->select(DB::raw('COUNT(students.id) as ct'))
             ->leftjoin('student_detail_sessionwise as st','st.student_id','=','students.id')
             ->where([
                ['students.branch_id','=',$value->branch_id],
                ['st.session_id','=',$value->session_id],
                ['st.course_id','=',$value->course_id],
                ['students.status','=',1]
             ])
             ->get();

             $arrFeeMaster = [];
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
             $arrFeeMaster['Feecollection'] = $paid;

       }

         $feeMaster=$fee;
         $feeCollection=$paid;
         $dueFee = $feeMaster - $feeCollection;
         $arrFeeMaster['Totaldue'] = $dueFee;


      return $this->return_data_in_json($arrFeeMaster);
}
    //total fee aasign

    public function Totalfeecollect(Request $request)
    {
       $branch= $request->branch?$request->branch:'';
       $sessions =DB::table('session')->select('*')->Where('active_status','=',1)->first();
       $session= $sessions->id;
       $sessionId= $request->sessionId?$request->sessionId:$session;
       $monthfee =DB::table('assign_fee')->select('*')
       ->where('status','=',1)
        ->where(function($q) use ($branch){
          if($branch!=""){
              $q->where('branch_id',$branch);
           }
         })
        ->where(function($q) use ($sessionId){
          if($sessionId!=""){
              $q->where('session_id',$sessionId);
           }
        })->get();
           $fee=0;
           $paid=0;
         foreach ($monthfee as $key => $value) {
         if($value->student_id==0){
             $count=DB::table('students')->select(DB::raw('COUNT(students.id) as ct'))
             ->leftjoin('student_detail_sessionwise as st','st.student_id','=','students.id')
             ->where([
                ['students.branch_id','=',$value->branch_id],
                ['st.session_id','=',$value->session_id],
                ['st.course_id','=',$value->course_id],
                ['students.status','=',1]
             ])
             ->get();

             $arrFeeMaster = [];
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
          $arrFeeMaster['Feecollection'] = $feeCollection;

      return $this->return_data_in_json($arrFeeMaster);
}

    public function StudentCount(Request $request)
    {
      $branch= $request->branch?$request->branch:'';
      $sessions =DB::table('session')->select('*')->Where('active_status','=',1)->first();
      $session= $sessions->id;
      $sessionId= $request->sessionId?$request->sessionId:$session;

      $data['student'] =DB::table('student_detail_sessionwise as st')->select('st.*')
      ->leftjoin('students as s','s.id','=','st.student_id')
      ->where('st.active_status','=',1)
      ->where('s.status','=',1)
     ->where(function($q) use ($branch){
        if($branch!=""){
            $q->where('s.branch_id',$branch);
         }
       })
      ->where(function($q) use ($sessionId){
        if($sessionId!=""){
            $q->where('st.session_id',$sessionId);
         }
      })
      ->count();
      return $this->return_data_in_json($data);
    } 
    
     public function DayList()
    {
        $day=DB::table('days')->select('id','title')->where('status',1)->get();
        $data['day'] = $day->all();
        $prep = ['id'=>0,'title'=>'select day'];
        array_unshift($data['day'],$prep);
        return $this->return_data_in_json($data);
    }
    
    
     public function StaffCount(Request $request)
    {
    if($request->branch_id){
    $data['staff_count'] =DB::table('staff')->select('id')
    ->where('status','=',1)
    ->where('branch_id',$request->branch_id)
    ->count();  
    }
    else{
        $data['msg']= "Invalid Request!!! Msg validation Failed!!!!";
    }



  return $this->return_data_in_json($data);
    }
    public function HostelBedCount(Request $request)
 {
  if($request->branch_id){
      $data['bed_count'] =DB::table('beds')->select('beds.id','beds.hostels_id')
      ->leftjoin('hostels','hostels.id','=','beds.hostels_id')
      ->where('hostels.status','=',1)
      ->where('hostels.branch_id',$request->branch_id)
      ->where(function($q) use ($request){
        if($request->hostel_id!=""){
            $q->where('beds.hostels_id',$request->hostel_id);
        }
       })
      ->count();
  }
  else{
      $data['msg']= "Invalid Request!!! Msg validation Failed!!!!";
  }

  return $this->return_data_in_json($data);
 }

 public function VehicleCount(Request $request)
  {
      $data['vehicle_count'] =DB::table('vehicles')->select('id')
      ->where('status','=',1)
      ->count();

  return $this->return_data_in_json($data);  
  }

  public function ExamCount(Request $request)
   {

     $today= Carbon\Carbon::now()->format('Y-m-d');

    if($request->branch_id && $request->session_id){
       $data['exam_count'] =DB::table('exam_create')->select('id')
      ->where('record_status','=',1)
      ->where('branch_id',$request->branch_id)
      ->where('session_id',$request->session_id)
      ->where('publish_status',1)
      ->where('date','>',$today)
      ->where(function($q) use ($request){
        if($request->faculty_id!=""){
            $q->where('faculty_id',$request->faculty_id);
        }
        if($request->section_id!=""){
            $q->where('section_id',$request->section_id);
        } 
        if($request->subject_id!=""){
            $q->where('subject_id',$request->subject_id);
        }
       })
      ->count();
    }
  else{
    $data['msg']= "Invalid Request!!! Msg validation Failed!!!!";
  }

  return $this->return_data_in_json($data);
   }
   public function BookCount(Request $request)
    {

       $data['book_count'] =DB::table('books')->select('id')
      ->where(function($q) use ($request){
        if($request->book_status!=""){
            $q->where('book_status',$request->book_status);
        }
       })
      ->count();
      return $this->return_data_in_json($data);
    }
       public function CallLogList(Request $request)
    {
      if($request->branch_id && $request->session_id){
        $data['call_log']= DB::table('call_logs as cl')->select('cl.id','cl.name','cl.contact','cl.date','cl.description','cl.follow_up_date','cl.call_duration','cl.note','b.branch_name','cl.session_id','cl.call_type','cl.branch_id','s.session_name')
        ->leftjoin('branches as b','b.id','=','cl.branch_id')
        ->leftjoin('session as s','s.id','=','cl.session_id')
         ->leftjoin('follow_up_history as fwh','fwh.call_log_id','=','cl.id')
        ->where('cl.branch_id',$request->branch_id)
        ->where('cl.session_id',$request->session_id)
        ->where(function($q) use ($request){
            if($request->from_date!="" & $request->to_date!=""){
                $q->wherebetween('cl.date',array($request->from_date,$request->to_date));
            }
            if($request->name){
              $query->where('cl.name',$request->name);
            }
            if($request->mobile){
              $query->where('cl.contact',$request->mobile); 
            }
            if($request->note){
              $query->where('cl.note',$request->note); 
            }
            if($request->call_type){
              $query->where('cl.call_type',$request->call_type);
            }
            if($request->start_follow_up && $request->end_follow_up){
              $query->WhereBetween('fwh.next_follow_up',[$request->start_follow_up,$request->end_follow_up]);
            }
        })
          


        ->where('fwh.follow_up_status',1)
 
      
        ->where('cl.record_status',1)->groupby('cl.id')->get();
      }
      else{
      $data['msg']= "Invalid Request!!! Msg validation Failed!!!!";
      }
  
        //dd($data);
      $resp['logs']=[];
            $call_log_type;
          foreach ($data['call_log'] as $k => $v) {
                 //dd($v);
           $call_log_type= $v->call_type;
         //  dd($call_log_type);
            
            if($call_log_type==1){
                
                $v->call_type_value= "Incoming";
                    //dd($v);
                       }
            else{
                $v->call_type_value= "Outgoing";
            }
            $resp['logs'][]= $v;
            //dd($data);
          }
      return $this->return_data_in_json($resp);
    }

        public function ComplaintList(Request $request)
        {
           if($request->branch_id && $request->session_id){
              $data['complaint_list']= DB::table('complains as c')->select('c.*','b.branch_name','s.session_name','sources.title as source_name','ct.title as complaint_type_name')
              ->leftjoin('sources','sources.id','=','c.source_id')
              ->leftjoin('branches as b','b.id','=','c.branch_id')
              ->leftjoin('session as s','s.id','=','c.session_id')
              ->leftjoin('complain_types as ct','ct.id','=','c.complain_type')
              ->where('c.record_status',1)
              ->where('c.branch_id',$request->branch_id)
              ->where('c.session_id',$request->session_id)
              ->where(function($q) use ($request){
                if($request->from_date!="" & $request->to_date!=""){
                    $q->wherebetween('c.date',array($request->from_date,$request->to_date));
                }
                if($request->complain_by){
                  $query->where('c.complain_by','like','%'.$request->name.'%');
                }
                if($request->purpose){
                   $query->where('c.complaint_type',$request->complaint_type); 
                }
                
                if($request->mobile){
                  $query->where('c.mobile',$request->mobile); 
                }
                if($request->note){
                  $query->where('c.note',$request->note); 
                }
                if($request->source){
                  $query->where('c.source_id',$request->source); 
                }
                if($request->assigned){
                  $query->where('c.assigned','like','%'.$request->assigned.'%'); 
                }
              })
             ->get();
           }
         else{
            $data['msg']= "Invalid Request!!! Msg validation Failed!!!!";
           }
      
          return $this->return_data_in_json($data);
        }

        public function PostalList(Request $request)
        {
           if($request->branch_id && $request->session_id){
              $data['postal_list']= DB::table('postals')->select('postals.*','b.branch_name','s.session_name')
              ->leftjoin('branches as b','b.id','=','postals.branch_id')
              ->leftjoin('session as s','s.id','=','postals.session_id')
             ->where('postals.record_status',1)
             ->where('postals.branch_id',$request->branch_id)
             ->where('postals.session_id',$request->session_id)
             ->where(function($q) use ($request){
                if($request->from_date!="" & $request->to_date!=""){
                    $q->wherebetween('postals.date',array($request->from_date,$request->to_date));
                }
            })
             ->get();
           }
         else{
            $data['msg']= "Invalid Request!!! Msg validation Failed!!!!";
           }
      
          return $this->return_data_in_json($data);
        }
        public function VisitorBook(Request $request)
        {
          if($request->branch_id && $request->session_id){
              $data['visitor_list']= DB::table('visitors_book as vb')->select('vb.*','ss.title as purpose','b.branch_name','s.session_name','vb.purpose as purpose_id')
              ->leftjoin('student_statuses as ss','ss.id','vb.purpose')
              ->leftjoin('branches as b','b.id','vb.branch_id')
              ->leftjoin('session as s','s.id','vb.session_id')
             ->where('vb.record_status',1)
             ->where('vb.branch_id',$request->branch_id)
             ->where('vb.session_id',$request->session_id)
             ->where(function($q) use ($request){
                if($request->from_date!="" & $request->to_date!=""){
                    $q->wherebetween('vb.date',array($request->from_date,$request->to_date));
                }
                if($request->name){
                  $query->where('vb.name',$request->name);
                }
                if($request->purpose){
                   $query->where('vb.purpose',$request->purpose); 
                }
                
                if($request->mobile){
                  $query->where('vb.contact',$request->mobile); 
                }
                if($request->note){
                  $query->where('vb.note',$request->note); 
                }
            })

             ->get();
           }
         else{
            $data['msg']= "Invalid Request!!! Msg validation Failed!!!!";
           }
      
          return $this->return_data_in_json($data);
        }

        public function EnquiryListing(Request $request)
        {
          if($request->branch_id && $request->session_id){
              $data['enquiry_list']= DB::table('enquiries as eq')->select('eq.*','f.faculty as course','b.branch_name','s.session_name','r.title as religion_name','handi.title as handicap_name','cat.category_name','ref.title as ref_by_name','sources.title as reference_name','eq.course as course_id')
              ->leftjoin('faculties as f','f.id','=','eq.course')
              ->leftjoin('branches as b','b.id','=','eq.branch_id')
              ->leftjoin('session as s','s.id','=','eq.session_id')
              ->leftjoin('religions as r','r.id','=','eq.religion_id')
              ->leftjoin('handicaps as handi','handi.id','=','eq.handicap_id')
              ->leftjoin('category as cat','cat.id','=','eq.category_id')
              ->leftjoin('reference as ref','ref.id','=','eq.refby')
              ->leftjoin('sources','sources.id','=','eq.reference')
             //->where('eq.status',1) 
             ->where('eq.branch_id',$request->branch_id)
             ->where('eq.session_id',$request->session_id)
             ->where(function($q) use ($request){
                
                if($request->from_date!="" & $request->to_date!=""){
                    $q->wherebetween('eq.enq_date',array($request->from_date,$request->to_date));
                }
                if($request->name) {
                    $q->where('eq.first_name', 'like', '%' . $request->name . '%');
                }

                if ($request->course_id) { 
                    $q->where('eq.course', '=', $request->course_id);
                }
                if ($request->mobile) {
                    $q->where('eq.mobile', $request->mobile)->orWhere('mobile', 'like', '%'.$request->mobile.'%');
                }

                if ($request->category) {
                    $q->where('eq.category_id', $request->category);
                }
               
               
             })
             ->get();

           }
         else{
            $data['msg']= "Invalid Request!!! Msg validation Failed!!!!";
           }
      
          return $this->return_data_in_json($data);
        }
        
        public function ProspectusList(Request $request)
        {
            //$today= Carbon\Carbon::now()->format('Y-m-d');
           if($request->branch_id && $request->session_id){
              $data['prospectus_list']= DB::table('admissions')->select('admissions.*','b.branch_name','s.session_name','cat.category_name','fac.faculty as course_name','handi.title as handicap_name','r.title as religion','reference.title as reference_name')
              ->leftjoin('branches as b','b.id','=','admissions.branch_id')
              ->leftjoin('session as s','s.id','=','admissions.session_id')
              ->leftjoin('category as cat','cat.id','=','admissions.category_id')
              ->leftjoin('faculties as fac','fac.id','=','admissions.course')
              ->leftjoin('handicaps as handi','handi.id','=','admissions.handicap_id')
              ->leftjoin('religions as r','r.id','=','admissions.religion_id')
              ->leftjoin('reference','reference.id','=','admissions.reference')
             ->where('admissions.branch_id',$request->branch_id)
             ->where('admissions.session_id',$request->session_id)
               //->where('status',1)
             ->where(function($q) use ($request){
                if($request->faculty_id!=""){
                    $q->where('course',$request->faculty_id);
                }
                 if($request->from_date!="" & $request->to_date!=""){

                    $q->wherebetween('admissions.admission_date',[$request->from_date,$request->to_date]);
                }
            })
             ->get();

           }
         else{
            $data['msg']= "Invalid Request!!! Msg validation Failed!!!!";
           }
      
          return $this->return_data_in_json($data);
        }
      
  public function BranchFeeCollectMonthWise(Request $request)
  {

       
      if($request->branch_id && $request->session_id){
        $month= DB::table('months')->select('id','title')->get();
         // $data['list'] = [];
         $data_arr =[];
          $assign_list=[];
         foreach($month as $m_key=>$m_value){
        
         $assign= DB::table('assign_fee')->select('*')
                ->where('branch_id',$request->branch_id)
               ->where('session_id',$request->session_id)
                ->where('due_month',$m_value->id)
               ->where('status',1)->get();
               foreach ($assign as $key => $value) {
                  $assign_list[$m_value->id][$value->course_id][]= $value;

         }

        }
        ksort($assign_list);
        $cnt=1;
         foreach ($assign_list as $key_month => $value_month) {
           
            foreach ($value_month as $key_course => $value_course) {
               foreach($value_course as $k=>$v){
                  $collect= DB::table('collect_fee')
                    ->where('assign_fee_id',$v->id)
                    ->where('status',1)
                    ->sum('amount_paid');

                    if($v->student_id==0){
                    $studentcnt= DB::table('student_detail_sessionwise as sds')
                    ->leftjoin('students','students.id','=','sds.student_id')
                    ->where('sds.course_id',$v->course_id)
                    ->where('sds.session_id',$v->session_id)
                    ->where('students.branch_id',$v->branch_id)
                    ->where('sds.active_status',1)->count();

                   
                    
                  
                     
                    if(!isset($data_arr[$key_month][$v->course_id]['assign_amount'])){
                        $data_arr[$key_month][$v->course_id]['assign_amount']=$studentcnt*$v->fee_amount;
                        

                    }
                    else{
                    $data_arr[$key_month][$v->course_id]['assign_amount']= $data_arr[$key_month][$v->course_id]['assign_amount']+$studentcnt*$v->fee_amount;
                      
                    }
                  }
                  else{
                     if(!isset($data_arr[$key_month][$v->course_id]['assign_amount'])){
                        $data_arr[$key_month][$v->course_id]['assign_amount']=$v->fee_amount;
                       
                    }
                    else{
                    $data_arr[$key_month][$v->course_id]['assign_amount']= $data_arr[$key_month][$v->course_id]['assign_amount']+$v->fee_amount; 
                    
                     }
                  }

                  if(!isset($data_arr[$key_month][$v->course_id]['collect_amount'])){
                    $data_arr[$key_month][$v->course_id]['collect_amount']=$collect;
                  }
                  else{
                    $data_arr[$key_month][$v->course_id]['collect_amount']=  $data_arr[$key_month][$v->course_id]['collect_amount']+ $collect;
                  }
               
               }

            }
                           

          $cnt++;
                                            
        }
       
        foreach ($data_arr as $key => $value) {
           
            $month= $this->Getmonth($key);
            $totalaasign= 0;
           foreach ($value as $k => $v) {
            $totalaasign= $totalaasign+$v['assign_amount'];
            if(!isset($data[$key]['total'])){
                $data[$key]['total']= $v['assign_amount'];
                $data[$key]['done']= $v['collect_amount'];
                $data[$key]['month']= $month;
            }
            else{
                $data[$key]['total']= $data[$key]['total']+$v['assign_amount'];
                $data[$key]['done']=  $data[$key]['done']+$v['collect_amount'];
                $data[$key]['month']= $month;
            }
           }
            # code...
        }
          
       
        
        $cnt=1;
        foreach ($data as $key => $value) {
          
            $value['due']= $value['total']-$value['done'];
            $temp['month_count']= $cnt;
            $temp['month_name'][]= $this->Getmonth($key);
            $temp['list'][] = (object) $value;
           $cnt++;
            // $temp[] = (object) $value;
        }
       
        // $temp[] = (object) $data;
      
        
        
         


      
      }
      else{
        $data['msg']= "Invalid Request!! Msg validation Failed!!!";
      }
      return $this->return_data_in_json($temp);
  }
  public function Getmonth($monthid)
  {

    $month= DB::table('months')->select('id','title')->where('id',$monthid)->first();
    if($month){
        $monthname= $month->title;
    }
    else{
        $monthname= 'unknown';
    }
    return $monthname;
  }

 

  public function BooksList(Request $request)
  {
      $books['value'] = BookMaster::select('id','code', 'title', 'image', 'categories', 'author', 'publisher', 'status')
            ->orderBy('title','asc')
            ->get();

            
        // foreach ($books as $key => $value) {
        //  $books_collection = Book::where('book_masters_id','=',$value->id )
        //     ->count();
        //     $value->book_count= $books_collection;
        //     $data['books'][]= $value;

            
        // }

             return $this->return_data_in_json($books);
  }

  
  
  public function SupplierList(Request $request)
  {
     
       $data['supplier_list']=InventorySupplier::where('record_status',1)->select('gstin','name','email','mobile','address','alternate_mobile')->get();
     
     
      return $this->return_data_in_json($data);
  }
  public function ProductList(Request $request)
  {
      $data['product']=Product::select('inventory_products.*','ib.title as brand','ic.title as category','iu.title as unit','sub_cat.title as sub_cat')
        ->leftjoin('inventory_brands as ib','ib.id','=','inventory_products.brand_id')
        ->leftjoin('inventory_categories as ic','ic.id','=','inventory_products.category_id')
        ->leftjoin('inventory_units as iu','iu.id','=','inventory_products.unit_id')
        ->leftjoin('inventory_categories as sub_cat','sub_cat.id','=','inventory_products.sub_category')
      ->where([
            ['inventory_products.record_status','=',1]
            ])->get();
       return $this->return_data_in_json($data);
  }

  public function StudentAttendanceList(Request $request)
  {
      if($request->branch_id && $request->session_id){
         $data['student'] = Attendance::select('attendances.id', 'attendances.attendees_type', 'attendances.day_1', 'attendances.day_2', 'attendances.day_3',
                'attendances.day_4', 'attendances.day_5', 'attendances.day_6', 'attendances.day_7', 'attendances.day_8',
                'attendances.day_9', 'attendances.day_10', 'attendances.day_11', 'attendances.day_12', 'attendances.day_13',
                'attendances.day_14', 'attendances.day_15', 'attendances.day_16', 'attendances.day_17', 'attendances.day_18',
                'attendances.day_19', 'attendances.day_20', 'attendances.day_21', 'attendances.day_22', 'attendances.day_23',
                'attendances.day_24', 'attendances.day_25', 'attendances.day_26', 'attendances.day_27', 'attendances.day_28',
                'attendances.day_29', 'attendances.day_30', 'attendances.day_31', 's.reg_no',
                's.first_name','f.faculty','m.title as month','y.title as year','s.branch_id','s.session_id')
                ->where('attendances.attendees_type', 1)
                ->join('students as s', 's.id', '=', 'attendances.link_id')
                ->leftjoin('faculties as f','f.id','=','s.faculty')
                ->leftjoin('months as m','m.id','=','attendances.months_id')
                ->leftjoin('years as y','y.id','=','attendances.years_id')
                ->orderBy('attendances.years_id','asc')
                ->orderBy('attendances.months_id','asc')
                //->where('s.branch_id',$request->branch_id)
                //->where('s.session_id',$request->sesion_id)
                ->get();
      }
      else{
        $data['msg']= "Invalid Request!! Msg validation Failed!!!"; 
      }
      return $this->return_data_in_json($data);
  }

  public function StaffAttendanceList(Request $request)
  {
      if($request->branch_id){
       $data['staff_attendance'] = Attendance::select('attendances.id', 'attendances.attendees_type', 'attendances.link_id',
                     'attendances.day_1', 'attendances.day_2', 'attendances.day_3',
                    'attendances.day_4', 'attendances.day_5', 'attendances.day_6', 'attendances.day_7', 'attendances.day_8',
                    'attendances.day_9', 'attendances.day_10', 'attendances.day_11', 'attendances.day_12', 'attendances.day_13',
                    'attendances.day_14', 'attendances.day_15', 'attendances.day_16', 'attendances.day_17', 'attendances.day_18',
                    'attendances.day_19', 'attendances.day_20', 'attendances.day_21', 'attendances.day_22', 'attendances.day_23',
                    'attendances.day_24', 'attendances.day_25', 'attendances.day_26', 'attendances.day_27', 'attendances.day_28',
                    'attendances.day_29', 'attendances.day_30', 'attendances.day_31', 's.id as staff_id', 's.reg_no',
                    's.first_name', 's.middle_name', 's.last_name', 's.designation','m.title as month','y.title as year','s.branch_id')
                    ->where('attendances.attendees_type', 2)
                    ->where('s.branch_id',$request->branch_id)
                    ->join('staff as s', 's.id', '=', 'attendances.link_id')
                    ->leftjoin('months as m','m.id','=','attendances.months_id')
                    ->leftjoin('years as y','y.id','=','attendances.years_id')
                    ->orderBy('s.id','asc')
                    ->orderBy('attendances.years_id','asc')
                    ->orderBy('attendances.months_id','asc')
                    // ->groupBy('attendances.link_id')
                    ->get();
      }else{
         $data['msg']= "Invalid Request!! Msg validation Failed!!!"; 
      }
      return $this->return_data_in_json($data);
  }
  public function CollectionList(Request $request)
  {
    if($request->branch_id && $request->session_id){
     $data['collection_list']=Collection::select('collect_fee.*', 'session.session_name', 'students.first_name', 'students.reg_no', 'students.category_id', 'users.name', 'fee_heads.fee_head_title', 'faculties.faculty', 'assign_fee.fee_head_id', 'fee_heads.fee_head_title')
        ->leftJoin('assign_fee', function($join){
            $join->on('collect_fee.assign_fee_id', '=', 'assign_fee.id');
        })->leftJoin('session', function($join){
            $join->on('session.id', '=', 'assign_fee.session_id');
        })->leftJoin('students', function($join){
                $join->on('students.id', '=', 'collect_fee.student_id');
        })->leftJoin('faculties', function($join){
                $join->on('faculties.id', '=', 'assign_fee.course_id');
        })->leftJoin('fee_heads', function($join){
                $join->on('fee_heads.id', '=', 'assign_fee.fee_head_id');
        })->leftJoin('users', function($join){
                $join->on('users.id', '=', 'collect_fee.created_by');
        })
        ->where('assign_fee.branch_id', $request->branch_id)
        ->where('collect_fee.status',1)
        ->where('students.status',1)
        ->where('assign_fee.session_id', $request->session_id)
        ->where('collect_fee.status', '=', '1')
        ->where(function($q) use ($request){
            if($request->from_date!="" && $request->to_date!=""){
                $q->whereBetween('collect_fee.reciept_date', [$request->from_date, $request->to_date]);
            }
            if($request->course_id){
                    $q->where('assign_fee.course_id', $request->course_id);
                }
        })
        ->selectRAW("sum(amount_paid) as amount_paid")
        ->selectRAW("sum(discount) as discount")
        ->groupBy('collect_fee.reciept_no')
        ->orderBy('collect_fee.id','desc')->get();
    }
    else{
        $data['msg']= "Invalid Request!! Msg validation Failed!!!";
      }
     return $this->return_data_in_json($data);
  }
  public function CancelReceiptList(Request $request)
  {
      if($request->branch_id && $request->session_id){
          $data['receipts']=DB::table('collect_fee_log')->select('collect_fee_log.*','st.first_name as student_name','fac.faculty as course','sem.semester as sem','st.reg_no','users.name as cancel_by','fee_heads.fee_head_title')
            ->leftjoin('assign_fee as af','af.id','=','collect_fee_log.assign_fee_id')
            ->leftjoin('students as st','st.id','=','collect_fee_log.student_id')
            ->leftjoin('student_detail_sessionwise as stssn',function($j){
                $j->on('stssn.student_id','=','collect_fee_log.student_id')
                ->where([
                    ['stssn.session_id','=',Session::get('activeSession')]
                ]);
            })
            ->leftjoin('users','users.id','=','collect_fee_log.log_created_by')
            ->leftjoin('faculties as fac','fac.id','=','af.course_id')
            ->leftjoin('semesters as sem','sem.id','=','stssn.Semester')
            ->leftJoin('fee_heads', 'fee_heads.id', '=', 'af.fee_head_id')
            ->where(function($q)use($request){
                
                if($request->from_date && $request->to_date){
                    $q->whereBetween('log_created_at',[$request->from_date.' 00:00:00',$request->to_date.' 23:59:59']);
                }
                if($request->course_id){
                    $q->where('af.course_id', $request->course_id);
                }

                
            })
            ->where([
                ['af.branch_id','=',$request->branch_id],
                ['af.session_id','=',$request->session_id],
                ['log_status','=',5]
            ])->get();
      }
      else{
        $data['msg']= "Invalid Request!! Msg validation Failed!!!";
      }
      return $this->return_data_in_json($data);
  }
  public function IncomeExpenseList(Request $request)
  {
      if($request->branch_id && $request->session_id && $request->type){
         $data['transaction'] = Transaction::select('transactions.id', 'transactions.date', 'transactions.tr_head_id', 'transactions.amount','transactions.type','transactions.note', 'transactions.description','transactions.status', 'users.name as receipt_by')
            ->leftJoin('users', function($join){
                $join->on('transactions.created_by', '=', 'users.id');
            })
            ->where([
                ['transactions.session_id','=',$request->session_id],
                ['transactions.branch_id','=',$request->branch_id]
            ])
            ->where(function ($query) use ($request) {
               
                if ($request->from_date && $request->to_date) {
                    $query->whereBetween('date', [$request->from_date, $request->to_date]);
                   
                }
                if($request->type){
                    $query->where('type',$request->type);
                }
                
            })
            ->orderBy('transactions.id', 'Desc')
            ->where('transactions.status',1)
           //->selectRAW("sum(amount) as total")
            ->get();
            $total=0;
            foreach ($data['transaction'] as $key => $value) {
               
               $total= $total+$value->amount;

            }
            $data['total_Transaction']= $total;
      }
      else{
        $data['msg']= "Invalid Request!! Msg validation Failed!!!";
      }
      return $this->return_data_in_json($data);
  }

  // public function DailyTableList(Request $request)
  // {
  //    if($request->branch_id && $request->session_id){
  //       $currentDate=Carbon\Carbon::now()->format('Y-m-d');
  //       $weekday=DB::select(DB::raw("SELECT dayofweek('$currentDate') as day"));
  //       $weekday=$weekday[0]->day;

  //      $date=carbon\Carbon::now()->format('Y-m-d');
  //       $date=explode('-', $date);
  //       $month=$date[1];
  //       if($month[0]==0){
  //         $month=$month[1];
  //       }
  //       $day=$date[2];
  //       if($day[0]==0){
  //         $day=$day[1];
  //       }
  //       $data['timetable']=DB::table('timetable')->select('timetable.id','fcl.faculty as course',"att.day_$day as present",'timetable.course_id','timetable.section_id','sem.semester as section','sub.title as subject','timetable_subject_id as subject_id','subject_type as type',DB::raw("date_format(time_from,'%H:%i') as time_from "),DB::raw("date_format(time_to,'%H:%i') as time_to "),'room_no',DB::raw("CONCAT(st.first_name,' ',st.last_name) as staff"),'timetable.staff_id',DB::raw("CONCAT(altstf.first_name,' ',altstf.last_name) as altTeacher"),'alt.staff_id as alternate','br.branch_name','branch_logo','branch_mobile','branch_email','branch_address','timetable.branch_id','timetable.session_id')
  //       ->leftjoin('attendances as att','att.link_id','=','timetable.staff_id')
  //       ->leftjoin('years','years.id','=','att.years_id')
  //       ->leftjoin('faculties as fcl','fcl.id','=','timetable.course_id')
  //       ->leftjoin('semesters as sem','sem.id','=','timetable.section_id')
  //       ->leftjoin('staff as st','st.id','=','timetable.staff_id')
  //       ->leftjoin('timetable_subjects as sub','sub.id','=','timetable.timetable_subject_id')
  //       ->leftjoin('timetable_alt_teacher as alt',function($q){
  //         $q->on('alt.timetable_id','=','timetable.id');
  //       })
  //       ->leftjoin('staff as altstf','altstf.id','=','alt.staff_id')
  //       ->leftjoin('branches as br','br.id','=','timetable.branch_id')
  //       ->where([
  //         ['timetable.day_id','=',$weekday],
  //         ['timetable.branch_id','=',$request->branch_id],
  //         ['timetable.session_id','=',$request->session_id],
  //         ['timetable.status','=',1],
  //         ['att.attendees_type','=',2],
  //         ['att.months_id','=',$month],
  //         ['years.title','=',$date[0]],
  //         ['is_break','=',0]

  //       ])
  //       ->orderBy('course','Asc')
  //       ->orderBy('timetable.time_from','Asc')
  //       ->get();
  //    }
  //    else{
  //       $data['msg']= "Invalid Request!! Msg validation Failed!!!";
  //     }
  //     return $this->return_data_in_json($data);
  // }

  public function AdminWeeklyTableList(Request $request)
  {
     $data=[];
     if($request->branch_id && $request->session_id &&$request->course_id && $request->section_id){
         $sch=DB::table('timetable')->select('timetable.id','time_from','time_to','room_no','subject_type',DB::raw("CONCAT(stf.first_name,' ',stf.last_name) as teacher"),DB::raw("CONCAT(altsf.first_name,' ',altsf.last_name) as alt_teacher"),'ts.title as subject','days.title as day')
            ->leftjoin('timetable_alt_teacher as alt',function($q){
                $q->on('alt.timetable_id','=','timetable.id')
                    
                    ->where('date',Carbon\Carbon::now()->format('Y-m-d'));
            })->leftjoin('staff as stf','stf.id','=','timetable.staff_id')
            ->leftjoin('staff as altsf','altsf.id','=','alt.staff_id')
            ->leftjoin('timetable_subjects as ts','ts.id','=','timetable.timetable_subject_id')
            ->leftjoin('days','days.id','=','timetable.day_id')
            ->where([
                ['timetable.branch_id','=',$request->branch_id],
                ['timetable.session_id','=',$request->session_id],
                ['timetable.course_id','=',$request->course_id],
                ['timetable.section_id','=',$request->section_id]
            ])
            ->orderBy('day_id','ASC')
            ->orderBy('time_from','ASC')
            ->get();
            foreach ($sch as $key => $value) {
               $data[$value->day][]=$value;
           }
          
     }
     else{
        $data['msg']= "Invalid Request!! Msg validation Failed!!!";
      }
      return $this->return_data_in_json($data);
  }
    public function AdminDailyTableList(Request $request){
    Log::debug("Daily TimeTable");
    Log::Debug($request->all());
        $branchId=(isset($request->branchId))?$request->branchId:null;
        $sessionId=(isset($request->sessionId))?$request->sessionId:null;
        $courseId=(isset($request->courseId))?$request->courseId:null;
        $secId=(isset($request->secId))?$request->secId:null;
        //$teacherId=(isset($request->teacherId))?$request->teacherId:null;
        $currentDate=(isset($request->date))?$request->date:(Carbon\Carbon::now()->format('Y-m-d'));
         
        if($branchId &&  $sessionId && $courseId && $secId){

        
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

    public function DiscountStatusList(Request $req)
    {
        if($req->branch_id && $req->session_id){
        $data['Discount_list']=Collection::select('collect_fee.*', 'session.session_name', 'students.first_name', 'students.reg_no', 'students.category_id', 'users.name', 'fee_heads.fee_head_title', 'faculties.faculty', 'assign_fee.fee_head_id', 'fee_heads.fee_head_title','collect_fee.discount_status','collect_fee.discount_comment','collect_fee.discount')
        ->leftJoin('assign_fee', function($join){
            $join->on('collect_fee.assign_fee_id', '=', 'assign_fee.id');
        })->leftJoin('session', function($join){
            $join->on('session.id', '=', 'assign_fee.session_id');
        })->leftJoin('students', function($join){
                $join->on('students.id', '=', 'collect_fee.student_id');
        })->leftJoin('faculties', function($join){
                $join->on('faculties.id', '=', 'assign_fee.course_id');
        })->leftJoin('fee_heads', function($join){
                $join->on('fee_heads.id', '=', 'assign_fee.fee_head_id');
        })->leftJoin('users', function($join){
                $join->on('users.id', '=', 'collect_fee.created_by');
        })
        ->where('assign_fee.branch_id', $req->branch_id)
        ->where('collect_fee.status',1)
        ->where('students.status',1)
        ->where('assign_fee.session_id', $req->session_id)

        ->where(function($qry) use ($req){
            if($req->course_id){
                $qry->where('assign_fee.course_id', $req->course_id);
            }
            if ($req->from_date && $req->to_date){
                $qry->whereBetween('collect_fee.reciept_date', [$req->from_date." 00:00:00", $req->to_date." 23:59:00"]);
            }
            // if($req->discount_status){
            //      $discount_status=$req->discount_status=='pending'?null:($req->discount_status=='rejected'?'0':($req->discount_status=='approved'?'1':null));
            //     // $status=$req->discount_status==3?null:$req->discount_status;
            //     $qry->where('collect_fee.discount_status', $discount_status);
            // }
            
        })
        ->where('collect_fee.status', '=', '1')
        ->where('collect_fee.discount', '>', '0')
        ->orderBy('collect_fee.id','desc')->get();
        }
        else{
        $data['msg']= "Invalid Request!! Msg validation Failed!!!";
      }
      return $this->return_data_in_json($data);
    }
    public function UserTypeList(Request $request)
    {
       $data['user_type']= DB::table('library_circulations')->select('id','user_type')
       ->orderBy('id','ASC')->get();
       return $this->return_data_in_json($data);
    }

    // 31-08-2021 
        // Updated 
            public function HostelDetailList(Request $request)
            {
              if($request->branch_id && $request->session_id && $request->user_type){
    
                $data['hostel_list']=Resident::select('residents.id', 'residents.hostels_id', 'residents.rooms_id', 'residents.beds_id', 'register_date', 'leave_date', 'user_type', 'member_id', 'residents.status','hostel_blocks.title as block','hostel_floors.title as floor','rooms.room_number','room_types.title as room_Type')
                    ->leftjoin('rooms','rooms.id','=','residents.rooms_id','hostels.name as hostel_name')
                    ->leftjoin('room_types','room_types.id','=','rooms.room_type')
                    ->leftjoin('hostel_blocks','hostel_blocks.id','=','rooms.block_id')
                    ->leftjoin('hostel_floors','hostel_floors.id','=','rooms.floor_id')
                    ->leftjoin('hostels','hostels.id','=','residents.hostels_id')
                    ->where('residents.branch_id',$request->branch_id)
                    ->where('residents.session_id',$request->session_id)
                    ->where('residents.status',1)
                
                  ->where(function($q) use ($request){
                         if($request->from_date!="" && $request->to_date!=""){
                            $q->wherebetween('register_date',[$request->from_date, $request->to_date]);
                         }
                         if($request->user_type!=""){
                            $q->where('residents.user_type',$request->user_type);
                         }
                     })
                 
                    ->get();
                   
                  
                    foreach ($data['hostel_list'] as $key => $value) {
                         if($value->user_type==1){
                          $value->Student_name= $this->GeStudentByID($value->member_id);
                          
                         }
                          if($value->user_type==2){
                          $value->Staff_name= $this->GeStaffByID($value->member_id);
                          
                         }
                    }
              }
              else{
                $data['msg']= "Invalid Request!! Msg validation Failed!!!";
              }
              return $this->return_data_in_json($data);
        }
            public function TransportDetailList(Request $request)
            {
                  if($request->branch_id && $request->session_id && $request->user_type){
                    $data['Transport_user'] = TransportUser::select('transport_users.id','transport_users.status','routes.title as RouteName','vehicles.number as vehicle_name','stoppages.title as stoppage_name','stoppages.distance','stoppages.fee_amount','transport_users.member_id','transport_users.user_type')
                    ->leftjoin('routes','routes.id','=','transport_users.routes_id')
                    ->leftjoin('vehicles','vehicles.id','=','transport_users.vehicles_id')
                    ->leftjoin('stoppages','stoppages.id','=','transport_users.stoppage_id')
                    ->where('stoppages.active_status',1)
                    ->where('routes.status',1)
                    ->where('vehicles.status',1)
                    ->where('transport_users.status',1)
                    ->where('transport_users.branch',$request->branch_id)
                    ->where('transport_users.session',$request->session_id)
                    ->where('transport_users.user_type', $request->user_type)
                    
                      ->where(function($q) use ($request){
                             if($request->from_date!="" && $request->to_date!=""){
                                $q->wherebetween('from_date',[$request->from_date,$request->to_date]);
                                $q->orwherebetween('to_date',[$request->from_date,$request->to_date]);
                                
                             }
                         })
                    ->get();
                    
                    foreach ($data['Transport_user'] as $key => $value) {
                       if($value->user_type==1){
                              $value->Student_name= $this->GeStudentByID($value->member_id);
                              
                             }
                              if($value->user_type==2){
                              $value->Staff_name= $this->GeStaffByID($value->member_id);
                              
                             }
                    }
                  }
                  else{
                    $data['msg']= "Invalid Request!! Msg validation Failed!!!";
                  }
                   return $this->return_data_in_json($data);
              }
            public function libraryMembers(Request $request) 
            {
     $data=[];
     $member=[];
     if($request->user_type){

          
                if($request->user_type==1){
                    if($request->course_id && $request->section_id && $request->session_id && $request->branch_id){
                      $member= $this->GetStudentLibrary($request); 
                    }
                    else{
                       $data['msg']= "Invalid Request!! Msg validation Failed!!!";   
                    }
                
                  
                 }
                if($request->user_type==2){
                    if($request->branch_id){
                      $member= $this->GetStaffLibrary($request); 
                    }
                    else{
                       $data['msg']= "Invalid Request!! Msg validation Failed!!!";   
                    }
                 }

                $data['library_member']= $member;
                  
               
         
     }
     else{
       $data['msg']= "Invalid Request!! Msg validation Failed!!!"; 
     }
      return $this->return_data_in_json($data);
  }


            public function libraryIssueHistory(Request $request)
            {
    $issue=[];
    $data=[];
     if($request->user_type){
                if($request->user_type==1){
                    if($request->course_id && $request->section_id && $request->session_id && $request->branch_id){
                      $issue= $this->GetStudentIssueHistory($request); 
                    }
                    else{
                       $data['msg']= "Invalid Request!! Msg validation Failed!!!";   
                    }
                
                  
                 } 
                if($request->user_type==2){
                    if($request->branch_id){
                      $issue= $this->GetStaffIssueHistory($request); 
                    }
                    else{
                       $data['msg']= "Invalid Request!! Msg validation Failed!!!";   
                    }
                 }
                  

                $data['library_member']= $issue;
                  
               
         
     }
       else{
          $data['msg']= "Invalid Request!! Msg validation Failed!!!"; 
       }
             return $this->return_data_in_json($data);
  }
        // Updated End
        
        // New
            public function GeStudentByID($studentid)
            {
               $student= DB::table('students')->select('first_name')
               ->where('id',$studentid)
               ->where('status',1)->first();
               if($student){
                $studentName= $student->first_name;
               }
               else{
                 $studentName= "Unknown";
               }
               return $studentName;
            }
            public function GeStaffByID($staffId)
            {
               $staff= DB::table('staff')->select('first_name')
               ->where('id',$staffId)
               ->where('status',1)->first();
               if($staff){
                $staffName= $staff->first_name;
               }
               else{
                 $staffName= "Unknown";
               }
               return $staffName;
            }
        // New End
    // 31-08-2021 End
    
    
    //01-09-2021
    
        public function AttendanceListBranchWise(Request $request)
        {
           if($request->branch_id && $request->session_id && $request->user_type && $request->from_date&& $request->to_date){
               $m_from= Carbon\Carbon::parse($request->from_date)->format('m');
               $m_to= Carbon\Carbon::parse($request->to_date)->format('m');
               $y_from= Carbon\Carbon::parse($request->from_date)->format('Y');
               $y_to= Carbon\Carbon::parse($request->to_date)->format('Y');
               $d_from= Carbon\Carbon::parse($request->from_date)->format('d');
               $d_to= Carbon\Carbon::parse($request->to_date)->format('d');
                if($request->user_type==1){
                    $attendance= $this->GetStudentAttendance(1,$request,$m_from,$m_to,$y_from,$y_to,$d_from,$d_to);
                   
                }
                if($request->user_type==2){
                   $attendance= $this->GetStaffAttendance(2,$request,$m_from,$m_to,$y_from,$y_to,$d_from,$d_to);
    
                }
                
                 $data['attendance']=[];
                 $return = [];
                 $temp=[];
                  //dd($attendance);
                 foreach ($attendance as $key => $value) {
                   
                    $from = ltrim($d_from,0);
                    $link_id = $value->link_id;
    
                    if(!isset($return[$value->link_id])){
                        $return[$link_id]['present'] = 0;
                        $return[$link_id]['absent'] = 0;
                        $return[$link_id]['leave'] = 0;
                        $return[$link_id]['halfday'] = 0;
                        $return[$link_id]['late'] = 0;
                        if($request->user_type==1){
                         $return[$link_id]['faculty']= $value->faculty;
                         $return[$link_id]['section']= $value->semester;
                         $return[$link_id]['name']= $value->student_name; 
                        }
                         if($request->user_type==2){
                         $return[$link_id]['name']= $value->staff_name .' '. $value->staff_last_name; 
                        }
                       
                        $return[$link_id]['year']= $value->year;
                        $return[$link_id]['month']= $value->month;
                        //$return[$link_id]['na'] = 0;
                    }
                   
                   for($from;$from<=$d_to;$from++){
                      
                        $day_name = 'day_'.$from;
    
                        if(isset($value->$day_name)){
                            $day = $value->$day_name;
                             if($request->user_type==1){
                                 $return[$link_id]['faculty']= $value->faculty;
                                 $return[$link_id]['section']= $value->semester;
                                 $return[$link_id]['name']= $value->student_name; 
                             }
                             if($request->user_type==2){
                                $return[$link_id]['name']= $value->staff_name .' '. $value->staff_last_name; 
                             }
                             $return[$link_id]['year']= $value->year;
                             $return[$link_id]['month']= $value->month;
                             
                            // dd($day,$day_name);
                            switch ($day) {
                                case 0:
                                   // $return[$link_id]['na']++;
                                    break;
    
                                case 1:
                                    $return[$link_id]['present']++;
                                    break;    
                                case 2:
                                    $return[$link_id]['absent']++;
                                    break;
                                case 3:
                                    $return[$link_id]['late']++;
                                    break;   
                                case 4:
                                    $return[$link_id]['leave']++;
                                    break; 
                                case 5:
                                    $return[$link_id]['halfday']++;
                                    break;         
                                default:
                                    # code...
                                    break;
                            }
                        }
    
                   }
    
    
                 }
                  foreach($return as $k=>$v){
                    $temp[]= (object)$v;
                  }
                
              
                    
           }
           else{
            $temp['msg']= "Invalid Request!! Msg validation Failed!!!";
          }
          return $this->return_data_in_json($temp);
        }
    
        public function GetStudentAttendance($user_type,$request,$m_from,$m_to,$y_from,$y_to,$d_from,$d_to)
        {
            $attendance = Attendance::select('attendances.*','s.first_name as student_name','f.faculty','sem.semester','y.title as year','m.title as month')
                    ->where('attendances.attendees_type',1)
                    ->join('students as s', 's.id', '=', 'attendances.link_id')
                    ->leftjoin('faculties as f', 'f.id', '=', 's.faculty')
                    ->leftjoin('semesters as sem', 'sem.id', '=', 's.semester')
                    ->leftjoin('years as y', 'y.id', '=', 'attendances.years_id')
                    ->leftjoin('months as m', 'm.id', '=', 'attendances.months_id')
                    ->where('s.branch_id',$request->branch_id)
                    ->where('s.session_id',$request->session_id)
                    ->where(function ($query) use ($request) {
                    
                        if ($request->course_id) {
                           
                            $query->where('s.faculty', '=', $request->course_id);
                           
                        }
    
                        if ($request->section_id) {
                            
                            $query->where('s.semester', '=', $request->section_id);
                        
                        }
                    })
                    ->whereBetween('y.title',[$y_from,$y_to])
                    ->whereBetween('attendances.months_id',[$m_from,$m_to])
                    ->orderBy('attendances.years_id','asc')
                    ->orderBy('f.id','asc')
                    ->orderBy('attendances.months_id','asc')
                    ->where('attendances.status',1)
                    ->get();
                    
    
                    return $attendance;
        }
        public function GetStaffAttendance($user_type,$request,$m_from,$m_to,$y_from,$y_to,$d_from,$d_to)
        {
    
            $attendance = Attendance::select('attendances.*','staff.first_name as staff_name','y.title as year','m.title as month','staff.last_name as staff_last_name')
                    ->where('attendances.attendees_type',2)
                    ->join('staff', 'staff.id', '=', 'attendances.link_id')
                    ->leftjoin('years as y', 'y.id', '=', 'attendances.years_id')
                    ->leftjoin('months as m', 'm.id', '=', 'attendances.months_id')
                    ->where('staff.branch_id',$request->branch_id)
                    ->whereBetween('y.title',[$y_from,$y_to])
                    ->whereBetween('attendances.months_id',[$m_from,$m_to])
                    ->orderBy('attendances.years_id','asc')
                    ->orderBy('attendances.months_id','asc')
                    ->where('attendances.status',1)
                    ->get();
                    
            
                    return $attendance;
        }
    
        public function GetStudentLibrary($request)
        {
            $student = LibraryMember::select('library_members.id','library_members.user_type', 'library_members.member_id',
                'library_members.status', 's.first_name',  's.middle_name',  's.last_name','f.faculty','sem.semester')
                ->join('students as s','s.id','=','library_members.member_id')
                ->leftjoin('student_detail_sessionwise as sds','sds.student_id','=','library_members.member_id')
                 ->leftjoin('faculties as f', 'f.id', '=', 's.faculty')
                    ->leftjoin('semesters as sem', 'sem.id', '=', 's.semester')
                ->where(['library_members.user_type'=> 1 ,'library_members.status' => 1])
                ->where(function ($query) use ($request) {
    
                    if ($request->course_id) {
                        $query->where('sds.course_id',$request->course_id);
                        
                    }
    
                    if ($request->section_id) {
                         $query->where('sds.Semester',$request->section_id);
                    }
    
                    
    
                })
                ->where('s.branch_id',$request->branch_id)
                ->where('sds.session_id',$request->session_id)
                ->where('s.status',1)
                ->get();
                return $student;
        }
         public function GetStaffLibrary($request)
        {
            $staff = LibraryMember::select('library_members.id','library_members.user_type', 'library_members.member_id',
                'library_members.status', 's.first_name',  's.middle_name',  's.last_name')
                ->join('staff as s','s.id','=','library_members.member_id')
                ->where(['library_members.user_type'=> 2 ,'library_members.status' => 1])
                ->where('s.branch_id',$request->branch_id)
                ->where('s.status',1)         
                ->get();
                return $staff;
        }
    
        public function GetStudentIssueHistory($request)
        {
            $student = BookIssue::select('book_issues.id', 'book_issues.member_id',
                'book_issues.book_id',  'book_issues.issued_on', 'book_issues.due_date','book_issues.return_date',
                'b.book_masters_id', 'b.book_code', 'bm.title','bm.categories','f.faculty','sem.semester','s.first_name','s.middle_name','s.last_name')
                ->leftjoin('library_members as lm','lm.id','=','book_issues.member_id')
                ->leftjoin('student_detail_sessionwise as sds','sds.student_id','=','lm.member_id')
                ->leftjoin('students as s','s.id','=','lm.member_id')
                 ->leftjoin('faculties as f', 'f.id', '=', 's.faculty')
                    ->leftjoin('semesters as sem', 'sem.id', '=', 's.semester')
                 ->where(['lm.user_type'=> 1 ,'lm.status' => 1])
                ->where(function ($query) use ($request) {
    
                  if ($request->course_id) {
                        $query->where('sds.course_id',$request->course_id);
                        
                    }
    
                    if ($request->section_id) {
                         $query->where('sds.Semester',$request->section_id);
                    }
                     if ($request->from_date && $request->to_date) {
                        $query->whereBetweenDate('book_issues.issued_on',[$request->from_date.' 00:00:00',$request->to_date.' 23:59:59']);
                        
                    }
    
                })
                ->join('books as b','b.id','=','book_issues.book_id')
                ->join('book_masters as bm','bm.id','=','b.book_masters_id')
                ->orderBy('book_issues.issued_on','asc')
                ->where('s.branch_id',$request->branch_id)
                ->where('sds.session_id',$request->session_id)
                ->where('s.status',1)
                ->get();
    
                return $student;
    
        }
        public function GetStaffIssueHistory($request)
        {
            $staff = BookIssue::select('book_issues.id', 'book_issues.member_id',
                'book_issues.book_id',  'book_issues.issued_on', 'book_issues.due_date','book_issues.return_date',
                'b.book_masters_id', 'b.book_code', 'bm.title','bm.categories','staff.first_name','staff.middle_name','staff.last_name')
                ->leftjoin('library_members as lm','lm.id','=','book_issues.member_id')
                ->leftjoin('staff','staff.id','=','lm.member_id')
                 ->where(['lm.user_type'=> 2 ,'lm.status' => 1])
                ->where(function ($query) use ($request) {
    
                  if ($request->from_date && $request->to_date) {
                        $query->whereBetween('book_issues.issued_on',[$request->from_date.' 00:00:00',$request->to_date.' 23:59:59']);
                        
                    }
    
                   
    
                })
                ->join('books as b','b.id','=','book_issues.book_id')
                ->join('book_masters as bm','bm.id','=','b.book_masters_id')
                ->orderBy('book_issues.issued_on','asc')
                ->where('staff.branch_id',$request->branch_id)
                ->where('staff.status',1)
                ->get();
    
                return $staff;
    
        }
    
    
    //01-09-2021 End
    
    
    //04-09-2021
    
    public function headwiseTotalReport(Request $req)
    {
       if($req->branch_id && $req->session_id && $req->course_id){
          $course_id= explode(',', $req->course_id);
            $students=[]; 
            if(count($course_id)>0){
                foreach ($course_id as $k => $fac) {
                    $collection=DB::table('assign_fee')->select('assign_fee.id','fee_amount','assign_fee.student_id','fh.fee_head_title','assign_fee.course_id','fac.faculty')
                    ->leftJoin('collect_fee as cf',function($q){
                        $q->on('assign_fee.id','=','cf.assign_fee_id')
                            ->Where('cf.status',1);
                    })
                    ->leftjoin('fee_heads as fh','fh.id','=','assign_fee.fee_head_id')
                    ->leftjoin('faculties as fac','fac.id','=','assign_fee.course_id')
                    ->where([
                        // ['cf.status','=',1],
                        ['assign_fee.status','=',1],
                        ['assign_fee.branch_id','=',$req->branch_id],
                        ['assign_fee.course_id','=',$fac],
                        ['assign_fee.session_id','=',$req->session_id]
                    ])
                    ->groupBy('assign_fee.id')
                    ->selectRaw('sum(cf.amount_paid) as fee_sum')
                    
                    ->orderBy('assign_fee_id','ASC')
                    ->get();
                    
                    if(count($collection)>0){
                        foreach ($collection as $key => $value) {
                            $count=DB::table('student_detail_sessionwise')
                            ->leftjoin('students as std','std.id','=','student_detail_sessionwise.student_id')
                            ->Where([
                                ['student_detail_sessionwise.session_id','=',$req->session_id],
                                ['student_detail_sessionwise.course_id','=',$value->course_id],
                                ['std.status','=',1]
                            ])->count('student_detail_sessionwise.id');
                            foreach ($value as $key => $val) {
                                $arr[$key]=$val;
                            }
                            $arr=array_prepend($arr,$count,'student_count');
                            $students[$value->faculty][$value->fee_head_title][$value->id]=$arr;
                        }   
                    }
                }   
            }
            $list=[];
            foreach($students as $k_course=>$v_course){
                 foreach ($v_course as $k_head => $v_head) {
                    //dd($v_head);
                     foreach($v_head as $k_assign=>$v_assign){
                        // dd($v_assign);
                        if($v_assign['fee_sum']==null){
                            $v_assign['fee_sum']=0;
                        }
                        else{
                            $v_assign['fee_sum']= $v_assign['fee_sum'];
                        }

                       
                        if($v_assign['student_id']==0){

                            if(!isset( $list[$k_course][$k_head]['total'])){
                              $list[$k_course][$k_head]['total'] = $v_assign['fee_amount']*$v_assign['student_count'];  
                            }
                            else{
                                $list[$k_course][$k_head]['total'] = ($v_assign['fee_amount']*$v_assign['student_count']) + $list[$k_course][$k_head]['total'];
                            }

                            
                          
                        }
                        else{
                          
                              if(!isset( $list[$k_course][$k_head]['total'])){
                                
                                $list[$k_course][$k_head]['total']=$v_assign['fee_amount']; 
                            }
                            else{
                                 
                                 $list[$k_course][$k_head]['total']=$v_assign['fee_amount']+$list[$k_course][$k_head]['total']; 
                            }
                            
                        }


                          if(!isset($list[$k_course][$k_head]['done'])){
                           
                                
                               $list[$k_course][$k_head]['done']=$v_assign['fee_sum'];
                            }
                            else{
                                
                                 
                                 $list[$k_course][$k_head]['done']=$v_assign['fee_sum']+$list[$k_course][$k_head]['done']; 
                            }
                         

                     }
                     //dd($list[$k_course][$k_head]['done']);
                 }
            }
            foreach ($list as $key_course => $value_course) {
                 foreach ($value_course as $key_head => $value_head) {

                    
                    $value_head['pending']= $value_head['total']-$value_head['done'];
                    $list[$key_course][$key_head]= $value_head;

                 }
            }
            $data['head_wise_report'][]=$list;
            // $data['head_wise_report']= $students;
       }
       else{
          $data['msg']= "Invalid Request!! Msg validation Failed!!!";
        }
         return $this->return_data_in_json($data);
    }

    
    
    //04-09-2021 end
    
    
    //06-09-2021
    
    public function FeeReportPaymentType(Request $req)
    {
        if($req->branch_id && $req->session_id){
          $student_session=StudentDetailSessionwise::select('students.branch_id', 'students.first_name', 'student_detail_sessionwise.*')
                ->leftjoin('students', function($join){
                    $join->on('student_detail_sessionwise.student_id', '=', 'students.id');
                })->where(function($query) use ($req){
                    if($req->course_id){ $query->where('student_detail_sessionwise.course_id', $req->course_id); }
                    if($req->section_id){ $query->where('student_detail_sessionwise.Semester', $req->section_id); }
                    $query->where('students.branch_id', $req->branch_id);
                    $query->where('student_detail_sessionwise.session_id', $req->session_id);
                })
                ->where('students.status','=',1)
                ->orderBy('students.first_name','asc')
                ->get();
                $due_tbl=[];
                foreach($student_session as $stud){

                $assign_qry=AssignFee::select('assign_fee.*', 'collect_fee.reciept_no', 'collect_fee.amount_paid', 'collect_fee.discount', 'collect_fee.fine','collect_fee.reciept_date','payment_type','reference','collect_fee.created_by as receipt_by','users.name','collect_fee.reciept_date', 'fee_heads.fee_head_title','parent_details.father_first_name','students.reg_no','students.first_name as student_name','faculties.faculty')->leftjoin('collect_fee', function($join){
                $join->on('assign_fee.id', '=', 'collect_fee.assign_fee_id');
                })->leftJoin('fee_heads', function($join){
                $join->on('assign_fee.fee_head_id', '=', 'fee_heads.id');
                })->leftJoin('users', function($join){
                $join->on('users.id', '=', 'collect_fee.created_by');
                })->leftJoin('parent_details', function($join){
                $join->on('parent_details.students_id', '=', 'collect_fee.student_id');
                })->leftJoin('students', function($join){
                $join->on('students.id', '=', 'collect_fee.student_id');
                })->leftJoin('faculties', function($join){
                $join->on('faculties.id', '=', 'assign_fee.course_id');
                 })
                // ->leftjoin('fee_heads as fee','fee.id','=','assign_fee.fee_head_id')
                // ->leftjoin('fee_heads as sub_head','sub_head.id','=','fee.parent_id')
                ->where(function($query) use ($req){
                if($req->fee_type){
                    $feetype= explode(',',$req->fee_type); 

                    $query->WhereIn('assign_fee.fee_head_id',$feetype);
                        
                }
                if($req->from_date && $req->to_date){
                $query->whereBetween('collect_fee.reciept_date', array($req->from_date, $req->to_date));
                }
                })->where([['collect_fee.amount_paid','!=', 0],['collect_fee.status','=',1],['course_id', $stud->course_id], ['assign_fee.session_id', $stud->session_id], ['collect_fee.student_id', $stud->student_id]])->whereIn('assign_fee.student_id', ['0', $stud->student_id])->orderBy('payment_type','asc')
                ->orderBy('reciept_date','asc')
                ->get();

                foreach($assign_qry as $assign_data){
                    $studId=$stud->student_id;
                    // $studId
                    $due_tbl[$assign_data['payment_type']][]=$assign_data;
                    $student_tbl[$studId]=$stud->first_name;
                }
                
            }
             $netPaid=0;
            foreach ($due_tbl as $key => $value) {
                foreach ($value as $k => $v) {
                    $netPaid= $netPaid+$v['amount_paid']; 
                }
            }
            $data['Report_payment_type'][]= $due_tbl;
            $data['net_total']= $netPaid;
            //dd($netPaid);
           
        }
        else{
          $data['msg']= "Invalid Request!! Msg validation Failed!!!"; 
        }
        return $this->return_data_in_json($data);
    }
     public function Getfaculty($id)
    {
       $faculty= DB::table('faculties')->select('faculty')->where('id',$id)->first();
       if($faculty){
        $fac_name= $faculty->faculty;
       }
       else{
        $fac_name= "";
       }
       return $fac_name;
    }
    public function DueReport(Request $req)
    {
       if($req->branch_id && $req->session_id){
            $assign['fee']=DB::table('assign_fee as af')->select('af.*','m.title as due_month','m.title','fee_amount','sds.Semester')
            ->leftjoin('faculties as f','f.id','=','af.course_id')
            ->leftjoin('months as m','m.id','=','af.due_month')
            ->leftjoin('student_detail_sessionwise as sds','sds.student_id','=','af.student_id')
            ->where('af.branch_id',$req->branch_id)
            ->where('af.session_id',$req->session_id)
            ->where(function($query) use ($req){
                    if($req->course_id){
                        
                        $query->where('af.course_id', $req->course_id);
                      }
                    
                    if($req->student_id){
                        
                        $query->where('af.student_id', $req->student_id);
                      }
                    if($req->month_id){
                       $month= explode(',',$req->month_id);

                        $query->whereIn('af.due_month', $month);
                    }       
            })
            ->where('af.status','=',1)
            ->groupBy('af.id')
            ->get();

            //dd( $assign['fee']);
            $student_head=[];$feehead=[];$master=[];
            foreach($assign['fee'] as $key =>$value){
                if($value->student_id==0){
                    $feehead[$value->due_month][$value->course_id][]=$value;
                }
                else{
                    $student_head[$value->due_month][$value->student_id][]=$value;

                }
            }
            foreach($feehead as $key =>$value){
                //dd($value,$key);
                foreach($value as $k=>$v){
                    if(isset($std[$k])){
                        $std_data=$std[$k];
                    //dd($std_data);
                    }
                    else{
                        
                        $std[$k]=DB::table('student_detail_sessionwise as sds')->select('course_id','students.id','students.reg_no','students.first_name','students.first_name','students.first_name','faculties.faculty','sem.semester','pd.father_first_name as fatherName')
                        ->leftjoin('faculties','faculties.id','=','sds.course_id')
                        ->leftjoin('semesters as sem','sem.id','=','sds.Semester')
                        ->leftjoin('students','sds.student_id', '=', 'students.id')
                        ->leftjoin('parent_details as pd', 'pd.students_id', '=', 'students.id')
                        ->where('students.status','=',1)
                        ->where('course_id','=',$k)
                        ->where('students.branch_id', $req->branch_id)
                        ->where('sds.session_id', $req->session_id)
                        ->where(function($query) use ($req){
                            if($req->section_id){
                        
                            
                                $query->where('sds.semester', $req->section_id);
                            }
                        })
                        ->get();
                        $std_data=$std[$k]; 
                    }
                     
                    // dd($std_data);
                    foreach($std_data as $key=>$valu){
                        
                        foreach($v as $ke=>$vl){

                            $collect_data=DB::table('collect_fee')->select('amount_paid')
                            ->where('student_id',$valu->id)
                            ->where('assign_fee_id',$vl->id)
                            ->where('collect_fee.status',1)
                            
                            ->selectRaw('SUM(amount_paid) as total_paid,SUM(discount) as disc')
                            ->first();
                            if(!isset($master[$vl->due_month][$vl->course_id][$valu->id])){
                                $master[$vl->due_month][$vl->course_id][$valu->id]['reg_no'] = $valu->reg_no;
                                $master[$vl->due_month][$vl->course_id][$valu->id]['first_name'] = $valu->first_name;
                                $master[$vl->due_month][$vl->course_id][$valu->id]['faculty'] = $valu->faculty;
                                $master[$vl->due_month][$vl->course_id][$valu->id]['semester'] = $valu->semester;
                                $master[$vl->due_month][$vl->course_id][$valu->id]['fatherName'] = $valu->fatherName;
                            }
                            if(!isset($master[$vl->due_month][$vl->course_id][$valu->id]['assign'])){
                                $master[$vl->due_month][$vl->course_id][$valu->id]['assign'] = 0;
                            }
                            if(!isset($master[$vl->due_month][$vl->course_id][$valu->id]['due'])){
                                $master[$vl->due_month][$vl->course_id][$valu->id]['due'] = 0;
                            }
                            if(!isset($master[$vl->due_month][$vl->course_id][$valu->id]['collect'])){
                                $master[$vl->due_month][$vl->course_id][$valu->id]['collect'] = 0;
                            }
                            if(!isset($master[$vl->due_month][$vl->course_id][$valu->id]['discount'])){
                                $master[$vl->due_month][$vl->course_id][$valu->id]['discount'] = 0;
                            }
                            if(!isset($master[$vl->due_month][$vl->course_id][$valu->id]['month'])){
                                $master[$vl->due_month][$vl->course_id][$valu->id]['month'] = $vl->title;
                            }
                            if(!isset($master[$vl->due_month][$vl->course_id][$valu->id]['month_id'])){
                                $master[$vl->due_month][$vl->course_id][$valu->id]['month_id'] = $vl->due_month;
                            }
                            $master[$vl->due_month][$vl->course_id][$valu->id]['assign'] = $master[$vl->due_month][$vl->course_id][$valu->id]['assign'] + $vl->fee_amount;
                            if($collect_data){
                                $master[$vl->due_month][$vl->course_id][$valu->id]['collect'] += $collect_data->total_paid;

                                $master[$vl->due_month][$vl->course_id][$valu->id]['discount'] += $collect_data->disc;
                            }
                            $master[$vl->due_month][$vl->course_id][$valu->id]['due']=$master[$vl->due_month][$vl->course_id][$valu->id]['assign']-($master[$vl->due_month][$vl->course_id][$valu->id]['collect']+$master[$vl->due_month][$vl->course_id][$valu->id]['discount']);  
                        }
                    }
                }
            }
              
            foreach($student_head as $ka=>$val){
                //dd($student_head);
                foreach($val as $k=>$v){
                    //dd($val);
                    $data=DB::table('student_detail_sessionwise as sds')->select('student_id','students.id','students.reg_no','students.first_name','students.first_name','students.first_name','faculties.faculty','sem.semester','pd.father_first_name as fatherName')
                        ->leftjoin('faculties','faculties.id','=','sds.course_id')
                        ->leftjoin('semesters as sem','sem.id','=','sds.Semester')
                        ->leftjoin('students','sds.student_id','=','students.id')
                        ->leftjoin('parent_details as pd', 'pd.students_id', '=', 'students.id')
                        ->where(function($query) use ($req){
                            if($req->section_id){
                            
                                $query->where('sds.semester', $req->section_id);
                            }
                        })
                        ->where('students.status','=',1)
                        ->where('students.id','=',$k)
                        ->where('students.branch_id', $req->branch_id)
                        ->where('sds.session_id', $req->session_id)
                        ->get();
                        foreach($data as $key=>$value){
                            //dd($value);
                            foreach($v as $ko=>$val){
                                //dd($val);
                                $collect_data=DB::table('collect_fee')->select('amount_paid')
                                    ->where('student_id',$value->id)
                                    ->where('assign_fee_id',$val->id)
                                    ->where('collect_fee.status',1)
                                    ->selectRaw('SUM(amount_paid) as total_paid,SUM(discount) as disc')
                                    ->first();  
                                    if(!isset($master[$val->due_month][$val->course_id][$val->student_id]['reg_no'])){
                                            $master[$val->due_month][$val->course_id][$val->student_id]['reg_no'] = $value->reg_no;

                                            $master[$val->due_month][$val->course_id][$val->student_id]['first_name'] = $value->first_name;
                                            $master[$val->due_month][$val->course_id][$val->student_id]['faculty'] = $value->faculty;
                                            $master[$val->due_month][$val->course_id][$val->student_id]['semester'] = $value->semester;
                                            $master[$val->due_month][$val->course_id][$val->student_id]['fatherName'] = $value->fatherName;
                                        }
                                    if(!isset($master[$val->due_month][$val->course_id][$val->student_id]['assign'])){
                                            $master[$val->due_month][$val->course_id][$val->student_id]['assign'] = 0;
                                        }
                                    if(!isset($master[$val->due_month][$val->course_id][$val->student_id]['due'])){
                                            $master[$val->due_month][$val->course_id][$val->student_id]['due'] = 0;
                                        }
                                    if(!isset($master[$val->due_month][$val->course_id][$val->student_id]['collect'])){
                                            $master[$val->due_month][$val->course_id][$val->student_id]['collect'] = 0;
                                        }
                                    if(!isset($master[$val->due_month][$val->course_id][$val->student_id]['discount'])){
                                            $master[$val->due_month][$val->course_id][$val->student_id]['discount'] = 0;
                                        }
                                    if(!isset($master[$val->due_month][$val->course_id][$val->student_id]['month'])){
                                            $master[$val->due_month][$val->course_id][$val->student_id]['month'] = $val->title;
                                        }
                                    if(!isset($master[$val->due_month][$val->course_id][$val->student_id]['month_id'])){
                                            $master[$val->due_month][$val->course_id][$val->student_id]['month_id'] = $val->due_month;
                                        }
                                    $master[$val->due_month][$val->course_id][$val->student_id]['assign'] = $master[$val->due_month][$val->course_id][$val->student_id]['assign'] + $val->fee_amount;
                                    if($collect_data){
                                        $master[$val->due_month][$val->course_id][$val->student_id]['collect'] += $collect_data->total_paid;
                                        $master[$val->due_month][$val->course_id][$val->student_id]['discount'] += $collect_data->disc;
                                    }
                                    $master[$val->due_month][$val->course_id][$val->student_id]['due']=$master[$val->due_month][$val->course_id][$val->student_id]['assign']-($master[$val->due_month][$val->course_id][$val->student_id]['collect']+$master[$val->due_month][$val->course_id][$val->student_id]['discount']);
                            }
                        }
                }   
            }
            //dd($master);
            $total= $pending=$done=$discount=0;
            foreach ($master as $key => $value) {
                
                foreach ($value as $k => $v) {
                    $fac= $this->Getfaculty($k);
                  
                    $v = (object)$v;
                   foreach ($v as $std_k => $std_v) {
                      $total= $total+$std_v['assign'];
                      $pending= $pending+$std_v['due'];
                      $done= $done+$std_v['collect'];
                      $discount= $discount+$std_v['discount'];
                      $value[$fac][] = (object)$std_v;
                   }
                   
                   unset($value[$k]);
                   $value[$fac] = (object)$value[$fac];
                    
                }
                
                foreach ($value as $k => $v1) {
                    $temp = [];
                    foreach ($v1 as $k2 => $v2) {
                        $temp[] = (object)$v2;
                    }
                    $value[$k] = $temp;
                }
                // $value = $this->make_array_object($value);
                $master[$key] = $value;
                $master['total'] = $total;
                $master['pending'] = $pending;
                $master['done'] = $done;
                $master['discount'] = $discount;
            }
        
            
            $master =$this->make_array_object($master);

           }
        else{
          $master['msg']= "Invalid Request!! Msg validation Failed!!!";
        }
         return $this->return_data_in_json($master);
    }
    public function StudentheadwiseTotalReport(Request $req)
    {
         $data=[];
        if($req->branch_id && $req->session_id && $req->course_id){
            $student_session=StudentDetailSessionwise::select('students.branch_id', 'students.first_name', 'students.reg_no', 'student_detail_sessionwise.*','pd.father_first_name as fatherName','faculties.faculty','sem.semester','pd.father_mobile_1 as mobile','students.batch_id as batch')
            ->leftjoin('faculties','faculties.id','=','student_detail_sessionwise.course_id')
            ->leftjoin('semesters as sem','sem.id','=','student_detail_sessionwise.Semester')
            
            ->leftjoin('students', function($join){
                $join->on('student_detail_sessionwise.student_id', '=', 'students.id');
                
            })->where(function($query) use ($req){
                if($req->course_id){ $query->where('student_detail_sessionwise.course_id', $req->course_id); }
                if($req->section_id){ $query->where('student_detail_sessionwise.Semester', $req->section_id); }
                $query->where('students.branch_id', $req->branch_id);
                $query->where('student_detail_sessionwise.session_id', $req->session_id);
            })
            ->where('students.status','=',1)
            ->join('parent_details as pd', 'pd.students_id', '=', 'students.id')
            ->orderBy('students.first_name','asc')
            ->get();

            foreach($student_session as $stud){
               
                $assign_qry=AssignFee::select('assign_fee.*', 'fee_heads.fee_head_title')->leftJoin('fee_heads', function($join){
                        $join->on('assign_fee.fee_head_id', '=', 'fee_heads.id');
                })->where('assign_fee.status',1)
                ->where([['course_id', $stud->course_id], ['session_id', $stud->session_id]])->whereIn('assign_fee.student_id', ['0', $stud->student_id])->get()->toArray();
                
                foreach($assign_qry as $assign_data){
                    //dd($assign_data);
                    $studId=$stud->student_id;
                    $assign_data['fee_head_title'];
                     $fee_name=$assign_data['fee_head_title'];
                    $fee_arr[$fee_name]=$fee_name;

                    $collect_qry=Collection::where([['student_id', $studId],['status','=',1], ['assign_fee_id', $assign_data['id']]])->get();
                    
                     
                    if(count($collect_qry)>0){
                            foreach($collect_qry as $collect){
                                $due_tbl[$studId][$assign_data['id']][$fee_name]['to_pay']=$assign_data['fee_amount'];
                                $due_tbl[$studId][$assign_data['id']][$fee_name]['paid'][]=$collect->amount_paid;
                                $due_tbl[$studId][$assign_data['id']][$fee_name]['disc'][]=$collect->discount;
                                $due_tbl[$studId][$assign_data['id']][$fee_name]['fine'][]=$collect->fine;
                                $due_tbl[$studId][$assign_data['id']][$fee_name]['student']=$stud->first_name;
                                $due_tbl[$studId][$assign_data['id']][$fee_name]['admission_no']=$stud->reg_no;
                                $due_tbl[$studId][$assign_data['id']][$fee_name]['fatherName']=$stud->fatherName;
                                $due_tbl[$studId][$assign_data['id']][$fee_name]['course']=$stud->faculty;
                                $due_tbl[$studId][$assign_data['id']][$fee_name]['mobile']=$stud->mobile;
                                $due_tbl[$studId][$assign_data['id']][$fee_name]['sec']=$stud->semester;
                                
                            }

                    }else{
                                $due_tbl[$studId][$assign_data['id']][$fee_name]['to_pay']=$assign_data['fee_amount'];
                                $due_tbl[$studId][$assign_data['id']][$fee_name]['paid'][]=0;
                                $due_tbl[$studId][$assign_data['id']][$fee_name]['disc'][]=0;
                                $due_tbl[$studId][$assign_data['id']][$fee_name]['fine'][]=0;
                                $due_tbl[$studId][$assign_data['id']][$fee_name]['student']=$stud->first_name;
                                $due_tbl[$studId][$assign_data['id']][$fee_name]['admission_no']=$stud->reg_no;
                                $due_tbl[$studId][$assign_data['id']][$fee_name]['fatherName']=$stud->fatherName;
                                $due_tbl[$studId][$assign_data['id']][$fee_name]['course']=$stud->faculty;
                                $due_tbl[$studId][$assign_data['id']][$fee_name]['mobile']=$stud->mobile;
                                $due_tbl[$studId][$assign_data['id']][$fee_name]['sec']=$stud->semester;
                    }

                    $student_tbl[$studId]=$stud->first_name;
                }
               

            }
            foreach ($due_tbl as $key => $assign_id_data) {
                foreach ($assign_id_data as $head_id => $assign_head_data) {

                    foreach ($assign_head_data as $head_name => $value){

                        if(isset($due[$key][$head_name]['to_pay'])){
                            $due[$key][$head_name]['to_pay']=$due[$key][$head_name]['to_pay']+$value['to_pay'];
                        }else{
                            $due[$key][$head_name]['to_pay']=$value['to_pay'];
                        }
                        $paid=array_sum($value['paid']);
                        if(isset($due[$key][$head_name]['paid'])){
                            $due[$key][$head_name]['paid']=$due[$key][$head_name]['paid']+$paid;
                        }else{
                            $due[$key][$head_name]['paid']=$paid;
                        }
                        $disc=array_sum($value['disc']);
                        if(isset($due[$key][$head_name]['disc'])){
                            $due[$key][$head_name]['disc']=$due[$key][$head_name]['disc']+$disc;
                        }else{
                            $due[$key][$head_name]['disc']=$disc;
                        }
                        $fine=array_sum($value['fine']);
                        if(isset($due[$key][$head_name]['fine'])){
                            $due[$key][$head_name]['fine']=$due[$key][$head_name]['fine']+$fine;
                        }else{
                            $due[$key][$head_name]['fine']=$fine;
                        }
                        $due[$key]['std_data']['student']=$value['student'];
                        $due[$key]['std_data']['admission_no']=$value['admission_no'];
                        $due[$key]['std_data']['fatherName']=$value['fatherName'];
                        $due[$key]['std_data']['course']=$value['course'];
                        $due[$key]['std_data']['sec']=$value['sec'];
                        $due[$key]['std_data']['mobile']=$value['mobile'];
                    }
                    
                }
            }
            $due_tbl=[];
            $due_tbl=$due;
            $list=[];

         // dd($due_tbl);

            foreach ($due_tbl as $key => $value) {

                
                    if(!isset($value['total_due'])){
                        $value['total_due']=0;
                       }

                       foreach ($value as $k => $v) {
                            if($k != 'std_data'){
                                $paid= $v['paid']+$v['disc'];
                                 $value['total_due']= $value['total_due']+($v['to_pay']-$paid);
                                 $list[$key]= $value; 
                            }
                        }
                   

               
            }
            
            
            $resp = []; 
           foreach ($list as $key => $value) {
              
               foreach ($value as $k => $v) {
                   if($k != 'std_data' && $k != 'total_due'){
                        $temp[$k]=$v;
                   }else{
                        if($k === 'std_data'  ){
                            $resp[$key] = $v;
                        }
                        if($k === 'total_due'  ){
                            $resp[$key]['total_due'] = $v;
                        }
                        

                   }
               }
               if(isset($temp)){
                    $resp[$key]['fee'][] = $temp;
                }else{
                     $resp[$key]['fee']=[];
                }
           }
           $temp_2 =[];
           foreach ($resp as $key => $value) {
                
               $temp_2[] = (object)$value;
           }
           // dd($resp);
           $resp = $temp_2;


           
        
         $netdue=0;
         foreach ($resp as $key => $value) {
             $netdue= $netdue+$value->total_due;
         }

         $data['Report_Headwise']= $resp;
            $data['net_due']= $netdue;   

        }
        else{
          $data['msg']= "Invalid Request!! Msg validation Failed!!!";
        }
         return $this->return_data_in_json($data);
    }
    
    public function make_array_object($array){
        $ret[] = (object)$array;
        return $ret;
    }
    
    //06-09-2021 end
    
    //07-09-2021
    public function MonthList()
    {
        $data=[];
        $data['month']= DB::table('months')->select('id','title')->orderBy('id','ASC')->get();
        return $this->return_data_in_json($data);
    }
    public function StudentList(Request $request)
    {

       if($request->branch_id && $request->session_id){

        $data['student_list']= Student::select('students.id','students.first_name')
         ->leftJoin('student_detail_sessionwise as sds', function($join){
          $join->on('sds.student_id', '=', 'students.id');
        })
        ->where('students.branch_id',$request->branch_id)
       
        ->where('sds.session_id',$request->session_id)
        ->where(function($q) use ($request){
            if($request->section_id!=''){
             $q->where('sds.Semester','=',$request->section_id);

            }
            if($request->course_id!=''){

             $q->where('sds.course_id',$request->course_id);
            }
        })
        ->where('students.status',1)
        ->get();
       
       }
        else{
          $data['msg']= "Invalid Request!! Msg validation Failed!!!";
        }
         return $this->return_data_in_json($data);
    }
    
    
    
    //Eve
    
    public function PurposeList()
    {
        $purpose=StudentStatus::where('status',1)->select('id','title')->get();
        $data = $purpose->all();
        $prep  = ['id'=>0,'title'=>'select purpose'];
        array_unshift($data,$prep);
        return $this->return_data_in_json($data);
    }
    
    public function ComplaintTypeList()
    {
        $data['complaint_type_list']=ComplainType::where('record_status',1)->select('id','title')->get();

         return $this->return_data_in_json($data);
    }
    
   public function SourceList()
    {
        $source=Source::where('record_status',1)->select('id','title')->get();
        $data = $source->all();
        $prep  = ['id'=>0,'title'=>'select Source'];
        array_unshift($data,$prep);

         return $this->return_data_in_json($data);
    } 
    
    
    
    //07-09-2021 end
    
    
    // 13-09-2021
    
    public function Reference()
    {
         $references=DB::table('reference')->where('record_status',1)->select('id','title')->get();
        $data = $references->all();
        $prep  = ['id'=>0,'title'=>'select Reference'];
        array_unshift($data,$prep);

         return $this->return_data_in_json($data);
    }
     public function Category()
    {
         $category=category_model::select('category_name','id')->get();
         $data = $category->all();
         $prep  = ['id'=>0,'category_name'=>'select category'];
         array_unshift($data,$prep);


         return $this->return_data_in_json($data);
    }
    public function Is_handicap()
    {
       $handi=[['id'=>0,'title'=>'select'],['id'=>1,'title'=>'Yes'],['id'=>2,'title'=>'No']];
                    
        $data['Is_handicap']= $handi;
        return $this->return_data_in_json($data);
    }
     public function Gender()
    {
       $gender=[['id'=>0,'title'=>'select Gender'],['id'=>'Male','title'=>'Male'],['id'=>'Female','title'=>'Female'],['id'=>'Other','title'=>'Other']];
                    
        $data['gender']= $gender;
        return $this->return_data_in_json($data);
    }
     public function Handicap(Request $request)
    {
         $handicap=[];
        if($request->is_handicap==1){
          $handicap=DB::table('handicaps')->select('id','title')->where('record_status',1)->get();
          $data = $handicap->all();
          $prep  = ['id'=>0,'title'=>'select handicap'];
          array_unshift($data,$prep);  
        }
        else{
           $data[]=  ['id'=>0,'title'=>'select handicap']; 
        }

         
         

         return $this->return_data_in_json($data);
    }
     public function Religion()
    {
        $religion=DB::table('religions')->select('id','title')->where('record_status',1)->get();
        $data = $religion->all();
        $prep  = ['id'=>0,'title'=>'select Religion'];
         array_unshift($data,$prep);
         return $this->return_data_in_json($data);
    }
    
    //13-09-2021 end
    
    // 15-09-2021
    
    public function CallType()
    {
        $calltype=[['id'=>0,'title'=>'select'],['id'=>1,'title'=>'Incoming'],['id'=>2,'title'=>'Outgoing']];
        $data['Call_Type']= $calltype;
        return $this->return_data_in_json($data);

    }
    // 15-09-2021 End
    /*16-09-2021*/
    
     public function PaymentModeList()
    {
        $paymentmode=DB::table('payment_type')->select('id','type_name')->where('status',1)->get();
        $data['payment_type'] = $paymentmode->all();
        $prep  = ['id'=>0,'type_name'=>'select payment Type'];
         array_unshift($data['payment_type'],$prep);
         return $this->return_data_in_json($data);
    }
    /*16-09-2021*/
    
    /*27-09-2021*/
    public function update_staff_attendance(Request $request){
       
        $date       = $request->date; 
        $staff_id   = $request->staff_id;
        $Session    = $request->Session;
        $Branch     = $request->Branch;
        $stdAtnd    = $request->Student_attendance_list; 
    
        $atdArr = json_decode($stdAtnd);
          //dd($atdArr);
        
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
            //dd($value);
            if($value->istakemarkp==true){
                $resp= DB::table('attendances')
                    ->updateOrInsert(
                    ['attendees_type' => 2, 'link_id' => $value->reg_no,'years_id'=>$year_id,'months_id'=>$month],
                    [$day => 1,'created_by'=>$staff_id,'created_at'=>$cdate->toDateTimeString(),'updated_at'=>$cdate->toDateTimeString()]
                    );
            }
            elseif($value->istakemarkp==false){
                $resp= DB::table('attendances')
                    ->updateOrInsert(
                    ['attendees_type' => 2, 'link_id' => $value->reg_no,'years_id'=>$year_id,'months_id'=>$month],
                    [$day => 2,'created_by'=>$staff_id,'created_at'=>$cdate->toDateTimeString(),'updated_at'=>$cdate->toDateTimeString()]
                    );
            }

            if($resp==0){
                $attStatus = 0;
            }              
        }  

        return $status = "{'Status':'".$attStatus."'}";
           
    }
    public function get_staff_attendance(Request $request){
       if($request->branch_id){
            $data['staff']=DB::select(DB::raw("SELECT CONCAT(staff.first_name,' ',COALESCE(middle_name,''),' ',last_name) as staff_name,staff.reg_no,staff.id as staff_id FROM staff 
            where(staff.branch_id=$request->branch_id and staff.status=1 and staff.designation=$request->designation_id) order By staff_name  asc 
            "));
       }
       else{
           $data['msg']= "Invalid Request Msg Validation Falied!!!";
       }

         return $this->return_data_in_json($data,$error_msg=""); 
    }
    /*27-09-2021*/
    
    /* 30-09-2021 */
    public function DesignationList($value='')
    {
        $designation=DB::table('staff_designations')->select('id','title')->where('status',1)->get();
        $data['designation'] = $designation->all();
        $prep = ['id'=>0,'title'=>'Select Designation'];
        array_unshift($data['designation'],$prep);
        return $this->return_data_in_json($data);
    }
    /* 30-09-2021 */
    
    /* 04-10-2021 */
    public function TeacherList(Request $request)
    {
        if($request->branch_id){
            $teacher=DB::table('staff')->select('id',DB::raw("CONCAT(staff.first_name,' ',COALESCE(staff.middle_name,'') , COALESCE(staff.last_name)) as title"))
            ->where('status',1)
            ->where('designation',6)
            ->where('branch_id', $request->branch_id)
            ->get();
            $data['teacher_list'] = $teacher->all();
            $prep = ['id'=>0,'title'=>'select teacher'];
            array_unshift($data['teacher_list'],$prep);
        }
        else{
            $data['msg']= "Invalid Request Msg Validation Failed!!!";
        }
    
         return $this->return_data_in_json($data);
    }
    /* 04-10-2021 */
    
}