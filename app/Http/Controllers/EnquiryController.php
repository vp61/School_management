<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\CollegeBaseController;
 //use \App\Http\Controllers\UserStudent\Student;
use App\Models\Student;
use App\Enquiry;
use App\Branch;    
use App\category_model;
use Auth,Log;
use App\Charts\EnquiryChart;
use App\Models\Faculty;
use App\Models\StudentStatus;
use Session, DB;
use Carbon\Carbon;
use App\Models\EnquiryFollowUp;
use App\Models\Status; 
use App\User; 
class EnquiryController extends CollegeBaseController{
    
    protected $filter_query = [];
    protected $base_route = 'enquiry';
    protected $view_path = 'enquiry';

    public function index(Request $request)
    {
    
    $org_id= Auth::user()->org_id;
    $branch_id = Session::get('activeBranch');
    $academic_status_option = StudentStatus::select('id', 'title')->Active()->pluck('title','id')->toArray();
    //$academic_status_option = array_prepend($Status,'Select Status',0);

    $data = Branch::select('branch_name','branch_title','id','org_id')->where('org_id', $org_id)->where('id', $branch_id)->get();
    $courses = Faculty::select('id', 'faculty')->where('branch_id' , $branch_id)
    ->orderBy('faculty')
    ->pluck('faculty', 'id')
    ->toArray();
    $courses = array_prepend($courses,'Select '.env("course_label"),"");

    $category=category_model::select('category_name','id')->pluck('category_name', 'id')->toArray();
    $category=array_prepend($category, '--Select Category--', '');
    $panel="Enquiry";

    $session_option=DB::table('session')->select('id', 'session_name')->pluck('session_name', 'id')->toArray();
    $session_option=array_prepend($session_option, '----Select Session----', '');
    $religion=DB::table('religions')->select('id','title')->where('record_status',1)->pluck('title','id')->toArray();
    $religion=array_prepend($religion,"--Select Religion--",'');
    $handicap=DB::table('handicaps')->select('id','title')->where('record_status',1)->pluck('title','id')->toArray();
    $handicap=array_prepend($handicap,"--Select Handicap--",'');
    $references=DB::table('reference')->select('title','id')->where('record_status',1)->pluck('title','id')->toArray();
    $references=array_prepend($references,'--Select Reference--','');
    $sources=DB::table('sources')->select('id','title')->where('record_status','=',1)->pluck('title','id')->toArray();
    $sources=array_prepend($sources,'--Select Source--','');

    return view('enquiry.enquiry', compact('data','academic_status_option','courses', 'panel','category','branch_id', 'session_option','religion','handicap','references','sources'));
 
    }



    public function store(Request $request)
    {   
      
      $request->request->add(['branch_id'=>Session::get('activeBranch')]);
      $request->request->add(['session_id'=>Session::get('activeSession')]);
      $request->request->add(['created_by'=>auth()->user()->id]);
      $request->request->add(['org_id'=>auth()->user()->org_id]);
      //dd($request->all(),auth::user());
      $enquiry = Enquiry::create($request->all());
      return redirect()->route('.enquiry_list')->with('msg', 'Enquiry submited sucessfully');
    }

    public function enquirylist(Request $request)
    {
        $data['status']=Status::where('record_status',1)->select('id','title')->pluck('title','id')->toArray();
        $branch_id = Session::get('activeBranch');
        $data['faculties'] = $this->activeFaculties();
        $data['category_name'] = category_model::get();
        $data['user']=User::select('users.id',DB::raw("CONCAT(users.name,' ( ',roles.display_name,' )') as name"))
      ->leftjoin('roles','users.role_id','=','roles.id')
        ->where(
          [
            ['users.branch_id','=',Session::get('activeBranch')],
            ['role_id','!=',6],
            ['role_id','!=',7]

        ])->pluck('name','id')->toArray();
        $data['user']=array_prepend($data['user'],'--Select Enquiry By--','');
        $data['status']=Status::select('id','title')->where('record_status',1)->pluck('title','id')->toArray();
        $data['status']=array_prepend($data['status'],'--Select Status--','');
        //$enquiries = Enquiry::get()->where('branch_id', $branch_id)->all();  
        $tem_enq = Enquiry::select('enquiries.*', 'faculties.faculty','users.name','adm.id as adm_adm_id','adm.enquiry_id as adm_enq_id','std.admission_id as std_adm_id','fw.followup_date','fw.next_followup','st.title as enq_status')
        ->leftjoin('enquiry_followup as fw','fw.enquiry_id','=','enquiries.id')
        ->leftjoin('statuses as st','st.id','=','enquiries.enq_status')
        ->leftJoin('faculties', function($q){
          $q->on('faculties.id', '=', 'enquiries.course');
        })->where(function ($query) use ($request){
            if($request->name) {
              $query->where('enquiries.first_name', 'like', '' . $request->name . '%')
              ->orWhere('enquiries.first_name', 'like', '%' . $request->name . '%')
              ->orWhere('enquiries.first_name', 'like', '%' . $request->name . '');
              $this->filter_query['name'] = $request->name;
            }

            if ($request->faculty) { 
                //die("inside course".$request->faculty);
                $query->where('enquiries.course', '=', $request->faculty);
                $this->filter_query['faculty'] = $request->faculty;
            }

            if ($request->mobile) {
                $query->where('enquiries.mobile', $request->mobile)->orWhere('mobile', 'like', '%'.$request->mobile.'%');
                $this->filter_query['mobile'] = $request->mobile;
            }


            if ($request->category) {
                $query->where('enquiries.category_id', $request->category);
                $this->filter_query['category'] = $request->category; 
            }

            if ($request->enq_start_date && $request->enq_end_date) {
                $query->whereBetween('enquiries.created_at', [$request->get('enq_start_date'), $request->get('enq_end_date')]);
                
                $this->filter_query['enq-start-date'] = $request->get('enq-start-date');
                
                $this->filter_query['update-end-date'] = $request->get('enq_end_date');
            }else{
              if(isset($_GET['enq_start_date'])){ 
              }else{
                $start_date=date("Y-m-d");
                $end_date=date("Y-m-d");
                
                $query->whereBetween('enquiries.enq_date', [$start_date, $end_date]);
                
                $this->filter_query['enq-start-date'] = $start_date;
                
                $this->filter_query['update-end-date'] = $end_date;
              }
          }
          if($request->enq_by){
            $query->where('enquiries.created_by',$request->enq_by);
          }
          if($request->status){
            $query->where('enquiries.enq_status',$request->status); 
          }
          if($request->next_followup_date){
            $query->where('enquiries.next_follow_up',$request->next_followup_date); 
           }
          })
          ->leftjoin('users' ,'enquiries.created_by' ,'=', 'users.id' )
          ->leftjoin('admissions as adm','enquiries.id','=','adm.enquiry_id')
          ->leftjoin('students as std','adm.id','=','std.admission_id')
          ->where('enquiries.branch_id', $branch_id)
          ->where('enquiries.session_id', Session::get('activeSession'))
          ->orderBy('fw.followup_date', 'DESC')
          ->get();
          $arr=[];
          $enquiries=[];
          foreach ($tem_enq as $key => $value) {
            if(!in_array($value->id, $arr)){
              $enquiries[]=$value;
            }
            $arr[]=$value->id;
          }

        $panel="Enquiry";  $data['filter_query']=$this->filter_query;
        
        return view('enquiry.list', compact('enquiries', 'panel', 'data'));
      
    }


    public function enquiryedit($id)
    {
    $org_id = Auth::user()->org_id;
    $branch_id = Session::get('activeBranch');
    $data = Branch:: select('branch_name','branch_title','id','org_id')->where('org_id', $org_id)->get();
    // $courses = Faculty::select('id', 'faculty')->Active()->pluck('faculty','id')->toArray();
      $courses = Faculty::select('id', 'faculty')
       ->where('branch_id' , $branch_id)
       ->pluck('faculty', 'id')
       ->toArray();
       //->where('org_id' , $org_id)

      $courses = array_prepend($courses, '--Select '.env("course_label").'--', '');
      $session_option=DB::table('session')->select('id', 'session_name')->pluck('session_name', 'id')->toArray();
      $session_option=array_prepend($session_option, '----Select Session----', '');
      $academic_status_option = StudentStatus::select('id', 'title')->Active()->pluck('title','id')->toArray();

    $enquiry = Enquiry::where('id', $id)->get();
    $branch_id = $enquiry[0]['branch_id'];
    $branch= Branch::where('id', $branch_id)->get();
    //return $enquiry;
    $category=category_model::select('category_name','id')->pluck('category_name', 'id')->toArray();
    $category = array_prepend($category, '----Select Category----', '');
    $religion=DB::table('religions')->select('id','title')->where('record_status',1)->pluck('title','id')->toArray();
    $religion=array_prepend($religion,"--Select Religion--",'');
    $handicap=DB::table('handicaps')->select('id','title')->where('record_status',1)->pluck('title','id')->toArray();
    $handicap=array_prepend($handicap,"--Select Handicap--",'');
    $references=DB::table('reference')->select('title','id')->where('record_status',1)->pluck('title','id')->toArray();
    $references=array_prepend($references,'--Select Reference--','');
    $sources=DB::table('sources')->select('id','title')->where('record_status','=',1)->pluck('title','id')->toArray();
    $sources=array_prepend($sources,'--Select Source--','');
    return view('enquiry.enquiry', compact('enquiry', 'academic_status_option','courses','data','branch','category', 'session_option','id','religion','handicap','references','sources'));
    
    //return view('enquiry.edit', compact('enquiry', 'academic_status_option','courses','data','branch','category'));
  
    }


    public function enquiryupdate(Request $request, $id)
    {   
        //dd($id);
        $row = Enquiry::find($id);
         $row->update($request->all());
         // return 'updated';
         // dd();
        //return $row;
         //Session::set('branch', $branch_id);
         //$request['branch_id']=1;
         //$enquiry = Enquiry::create($request->all());
         //return'inserted';
         //return view('enquiry.enquiry');
         return redirect()->route('.enquiry_list')->with('msg', 'Enquiry updated sucessfully');

    }
    public function enquirystatus(Request $request){
      $course=Faculty::where([
        ['branch_id','=',Session::get('activeBranch')],
        ['status','=',1]
      ])->select('id','faculty')->pluck('faculty','id')->toArray();
      $course=array_prepend($course,"--Select ".env("course_label")."--","");
      $category=category_model::select('category_name','id')->pluck('category_name', 'id')->toArray();
      $category = array_prepend($category, '----Select Category----', '');
      if($request->all()){
        //Group By Ref BY COUNT
        $ref_enq=Enquiry::select('refby',DB::raw("COUNT(*) as total"))
        ->where(function($query)use($request){
          if($request->faculty){
            $query->where('course',$request->faculty);
            $this->filter_query['course']=$request->course;
          }
          if($request->reg_start_date && $request->reg_end_date){
            $query->whereBetween('enq_date',[$request->reg_start_date,$request->reg_end_date]);
            $this->filter_query['reg_start_date']=$request->reg_start_date;
            $this->filter_query['reg_end_date']=$request->reg_end_date;
          }
          if($request->category){
            $query->where('category_id',$request->category);
          }
          if($request->name){
            $query->where('enquiries.first_name', 'like', '%' . $request->name . '%');
          }
          if($request->mobile){
            $query->where('mobile',$request->mobile);
          }
        })->where('session_id',Session::get('activeSession'))
        ->where('branch_id',Session::get('activeBranch'))
        ->groupBy('refby')
        ->get();
         if($ref_enq){
          foreach ($ref_enq as $key => $value) {
           if($value->refby==null){
             $label[]='Visit'." ( $value->total )";
             $chart_data[]=$value->total;
           }else{
            $label[]=$value->refby." ( $value->total )";
            $chart_data[]=$value->total;
           }
          }

           if(count($ref_enq)>0){
             $chart = new EnquiryChart;
            $chart->labels($label);
            $chart->dataset('Enquiry Data(Referred By Count)','bar',$chart_data)
            ->options(['borderColor' => '#46b8da', 'backgroundColor'=>['#c78557','#80688f'] ]);
           }
            // dd($chart);
        }
        //Enquiry->Admission->Registration COUNT
        $enq_adm=Enquiry::select('enquiries.*','adm.id as adm_id','std.id as std_id')
        ->where(function($query)use($request){
          if($request->faculty){
            $query->where('enquiries.course',$request->faculty);
            $this->filter_query['course']=$request->course;
          }
          if($request->reg_start_date && $request->reg_end_date){
            $query->whereBetween('enq_date',[$request->reg_start_date,$request->reg_end_date]);
            $this->filter_query['reg_start_date']=$request->reg_start_date;
            $this->filter_query['reg_end_date']=$request->reg_end_date;
          }
          if($request->category){
            $query->where('enquiries.category_id',$request->category);
          }
          if($request->name){
            $query->where('enquiries.first_name', 'like', '%' . $request->name . '%');
          }
          if($request->mobile){
            $query->where('mobile',$request->mobile);
          }
        })->where('enquiries.session_id',Session::get('activeSession'))
        ->where('enquiries.branch_id',Session::get('activeBranch'))
        ->leftjoin('admissions as adm','adm.enquiry_id','=','enquiries.id')
        ->leftjoin('students as std','std.admission_id','=','adm.id')
        ->get();
        
        $count[0]=0;
        $count[1]=0;
        $count[2]=0;
        foreach ($enq_adm as $key => $value) {
          if(isset($value->id)){
            $count[0]+=1;
          }
          if(isset($value->adm_id)){
            $count[1]+=1;
          }
          if(isset($value->std_id)){
            $count[2]+=1;
          }
        }
        //Total Enquiry
        $total_enquiry=Enquiry::select('*')
        ->where(function($query)use($request){
          if($request->faculty){
            $query->where('enquiries.course',$request->faculty);
            $this->filter_query['course']=$request->course;
          }
          if($request->reg_start_date && $request->reg_end_date){
            $query->whereBetween('enq_date',[$request->reg_start_date,$request->reg_end_date]);
            $this->filter_query['reg_start_date']=$request->reg_start_date;
            $this->filter_query['reg_end_date']=$request->reg_end_date;
          }
          if($request->category){
            $query->where('enquiries.category_id',$request->category);
          }
          if($request->name){
            $query->where('enquiries.first_name', 'like', '%' . $request->name . '%');
          }
          if($request->mobile){
            $query->where('mobile',$request->mobile);
          }
        })->where('enquiries.session_id',Session::get('activeSession'))
        ->where('enquiries.branch_id',Session::get('activeBranch'))
        ->count('id');
        //TOTAL ADMISSION
        $total_admission=DB::Table('admissions')->select('*')
        ->where(function($query)use($request){
          if($request->faculty){
            $query->where('course',$request->faculty);
            $this->filter_query['course']=$request->course;
          }
          if($request->reg_start_date && $request->reg_end_date){
            $query->whereBetween('admission_date',[$request->reg_start_date,$request->reg_end_date]);
            $this->filter_query['reg_start_date']=$request->reg_start_date;
            $this->filter_query['reg_end_date']=$request->reg_end_date;
          }
          if($request->category){
            $query->where('category_id',$request->category);
          }
          if($request->name){
            $query->where('first_name', 'like', '%' . $request->name . '%');
          }
          if($request->mobile){
            $query->where('mobile',$request->mobile);
          }
        })->where('session_id',Session::get('activeSession'))
        ->where('branch_id',Session::get('activeBranch'))
        ->count('id');
        //Total Registration
        $total_registration=DB::Table('students')->select('*')
        ->where(function($query)use($request){
          if($request->faculty){
            $query->where('faculty',$request->faculty);
            $this->filter_query['course']=$request->course;
          }
          if($request->reg_start_date && $request->reg_end_date){
            $reg_start=$request->reg_start_date.'00:00:00';
            $reg_end=$request->reg_end_date.'23:59:59';
            $query->whereBetween('admission_date',[$reg_start,$reg_end]);
            $this->filter_query['reg_start_date']=$reg_start;
            $this->filter_query['reg_end_date']=$reg_end;
          }
          if($request->category){
            $query->where('category_id',$request->category);
          }
          if($request->name){
            $query->where('first_name', 'like', '%' . $request->name . '%');
          }
          if($request->mobile){
            $query->where('ad.mobile_1',$request->mobile);
          }
        })->where('session_id',Session::get('activeSession'))
        ->where('branch_id',Session::get('activeBranch'))
        ->leftjoin('addressinfos as ad','ad.students_id','=','students.id')
        ->count('students.id');
        $total_count[0]=$total_enquiry;
        $total_count[1]=$total_admission;
        $total_count[2]=$total_registration;
        $count_chart= new EnquiryChart;
        $count_chart->labels(['Total Enquiries ('.$total_enquiry.')','Total Forms ('.$total_admission.')','Total Registration ('.$total_registration.')']);
        $count_chart->dataset('Total Count For Active Branch & Session','doughnut',$total_count)
            ->options(['borderColor' => '#96bdda', 'backgroundColor'=>['#46b8da','#b6b065','#93e796']]);
      }
      return view('enquiry.enquiry_status',compact('course','category','chart','count_chart','count'));
    }
    public function add_followup(Request $request){
      // dd($request->all());
      $data['enq']=Enquiry::find($request->enquiry_id);
      if($request->next_followup || $request->enq_status){
        $next=$request->next_followup!=null?$request->next_followup:$data['enq']->next_follow_up;
        $dd=$data['enq']->update([
          'next_follow_up'=>$next,
          'enq_status'=>$request->enq_status
        ]);
      }
      $request->request->add(['created_at'=>Carbon::now()]);
      $request->request->add(['created_by'=>auth()->user()->id]);
      $request->request->add(['record_status'=>1]);
      EnquiryFollowUp::create($request->all());
      return redirect()->back()->with('message_success','Follow Up Record Added'); 
    }
    public function loadenquiry(Request $request){
      $response=[];
      $response['error']=true;
      if($request->enq_id){
        $data=Enquiry::select(DB::raw("date_format(enq_date,'%d-%M-%Y') as enq_date , date_format(fa.followup_date,'%d-%M-%Y') as followup_date,date_format(fa.next_followup,'%d-%M-%Y') as next_followup,date_format(next_follow_up,'%d-%M-%Y') as next_follow_up "),'first_name','enquiries.email','course','mobile','fac.faculty as course','no_of_child','refby','academic_status','fa.response','fa.note','us.name','enq_status','fa.id')
        ->leftjoin('enquiry_followup as fa',function($query){
          $query->on('fa.enquiry_id','=','enquiries.id')
              ->where([
                ['fa.record_status','=',1],
               // ['fa.enquiry_id','=',$request->enq_id],
              ]);
        })
        
        ->leftjoin('users as us','us.id','=','fa.created_by')
        ->leftjoin('faculties as fac','fac.id','=','enquiries.course')
        ->where('enquiries.id',$request->enq_id)
        ->orderBy('followup_date','desc')
        ->get();
        if($data){
          $response['error']=false;
          if(isset($data[0])){
            $response['data']=$data[0];  
          }else{
            $response['data']=$data; 
          }
          
          $response['list']=$data;
        }else{
          $response['message']="Not Found";
        }
      }else{
        $response['message']="Invalid Request";
      }
      return response()->json(json_encode($response));
    }
    public function delete_followup($id){
      $data['row']=EnquiryFollowUp::find($id);
      $data['row']->update([
        'record_status'=>0
      ]);
      return redirect()->back()->with('message_success','Record Deleted');
    }
}
