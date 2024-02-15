<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\CollegeBaseController;
 //use \App\Http\Controllers\UserStudent\Student;

use App\Models\Student;
use App\Enquiry;
use App\Branch;    
use App\category_model;
use Auth;
use App\Charts\EnquiryChart;
use App\Models\Faculty;
use App\Models\StudentStatus;
use Session, DB;
use Carbon\Carbon;
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
    $courses = array_prepend($courses,'Select Course',0);

    $category=category_model::select('category_name','id')->pluck('category_name', 'id')->toArray();
    $category=array_prepend($category, '--Select Category--', '');
    $panel="Enquiry";

    $session_option=DB::table('session')->select('id', 'session_name')->pluck('session_name', 'id')->toArray();
    $session_option=array_prepend($session_option, '----Select Session----', '');

    return view('enquiry.enquiry', compact('data','academic_status_option','courses', 'panel','category','branch_id', 'session_option'));
 
    }



    public function store(Request $request)
    {   
      $request->request->add(['branch_id'=>Session::get('activeBranch')]);
      $request->request->add(['session_id'=>Session::get('activeSession')]);
      $request->request->add(['created_by'=>auth()->user()->id]);
      $enquiry = Enquiry::create($request->all());
      return redirect()->route('.enquiry_list')->with('msg', 'Enquiry submited sucessfully');
    }

    public function enquirylist(Request $request)
    {
        
        $branch_id = Session::get('activeBranch');
        $data['faculties'] = $this->activeFaculties();
        $data['category_name'] = category_model::get();
        //$enquiries = Enquiry::get()->where('branch_id', $branch_id)->all();  
        $enquiries = Enquiry::select('enquiries.*', 'faculties.faculty','users.name','adm.id as adm_adm_id','adm.enquiry_id as adm_enq_id','std.admission_id as std_adm_id')
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

            if ($request->reg_start_date && $request->reg_end_date) {
                $query->whereBetween('enquiries.created_at', [$request->get('reg_start_date')." 00:00:00", $request->get('reg_end_date')." 23:59:00"]);
                
                $this->filter_query['reg-start-date'] = $request->get('reg-start-date')." 00:00:00";
                
                $this->filter_query['update-end-date'] = $request->get('reg_end_date')." 23:59:00";
            }else{
              if(isset($_GET['reg_start_date'])){ 
              }else{
                $start_date=date("Y-m-d")." 00:00:00";
                $end_date=date("Y-m-d")." 23:59:00";
                
                $query->whereBetween('enquiries.created_at', [$start_date, $end_date]);
                
                $this->filter_query['reg-start-date'] = $start_date;
                
                $this->filter_query['update-end-date'] = $end_date;
              }
          }
          })
          ->leftjoin('users' ,'enquiries.created_by' ,'=', 'users.id' )
          ->leftjoin('admissions as adm','enquiries.id','=','adm.enquiry_id')
          ->leftjoin('students as std','adm.id','=','std.admission_id')
          ->where('enquiries.branch_id', $branch_id)
          ->where('enquiries.session_id', Session::get('activeSession'))
          ->orderBy('enquiries.id', 'DESC')
          ->get();
          //return $enquiries;
        $panel="Enquiry";  $data['filter_query']=$this->filter_query;
        $view_path='student';
        return view('enquiry.list', compact('enquiries', 'panel', 'view_path', 'data'));
      
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

      $courses = array_prepend($courses, '--Select Course--', '');
      $session_option=DB::table('session')->select('id', 'session_name')->pluck('session_name', 'id')->toArray();
      $session_option=array_prepend($session_option, '----Select Session----', '');
      $academic_status_option = StudentStatus::select('id', 'title')->Active()->pluck('title','id')->toArray();

    $enquiry = Enquiry::where('id', $id)->get();
    $branch_id = $enquiry[0]['branch_id'];
    $branch= Branch::where('id', $branch_id)->get();
    //return $enquiry;
    $category=category_model::select('category_name','id')->pluck('category_name', 'id')->toArray();
    $category = array_prepend($category, '----Select Category----', '');
    return view('enquiry.enquiry', compact('enquiry', 'academic_status_option','courses','data','branch','category', 'session_option', 'id'));
    
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
      $course=array_prepend($course,"--Select Course--","");
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
    public function changedate(){
      $data=DB::table('enquiries')->select('enq_date','id')->get();
      foreach ($data as $key => $value) {
        
       $date=Carbon::parse($value->enq_date)->format('Y-m-d');
       $up=Enquiry::where('id',$value->id)->update([
          'enq_date'=>$date
       ]);

      }
      dd('DONE');
    }

}
