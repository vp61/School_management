<?php
  namespace App\Http\Controllers\LiveClass;
  // use Illuminate\Support\Facades\Http;
  use App\Http\Controllers\CollegeBaseController;
  use App\Http\Controllers\Api\ServicesController;
  use App\category_model;
  use Illuminate\Http\Request;
  use App\Models\StudentStatus;
  use App\Models\Student;
  use App\Models\Faculty;
  use App\Models\LiveClass;
  use App\Enquiry;
  use App\Branch;
  use App\Admission;
  use App\User;
  use Auth;
  use DB, Session;
  use Carbon\Carbon;
  /*--teacher access--*/
    use App\Models\TeacherCoordinator;
  /*--teacher access--*/
  
  use Zizaco\Entrust\Traits\EntrustUserTrait;



class ZoomMeetingController extends CollegeBaseController
{
    
    protected $base_route = 'live_class';
    protected $view_path = 'LiveClass';
    protected $panel = 'Live Class';
    protected $filter_query = [];

    public function index(Request $request)
    {
      $email = $secret_key = $api_key = null;
      if(auth()->user()->role_id !=6 && auth()->user()->role_id !=7){
        $zoom_keys = DB::table('staff')->select('zoom_api_key','zoom_secret_key','zoom_email')->where('id',auth()->user()->hook_id)->first();
      }
      if(isset($zoom_keys->zoom_api_key) && isset($zoom_keys->zoom_secret_key) && isset($zoom_keys->zoom_email)){
        $api_key = $zoom_keys->zoom_api_key;
        $secret_key = $zoom_keys->zoom_secret_key;
        $email = $zoom_keys->zoom_email;
      }else{
        Session::flash('message_danger','You are not authorized to schedule live classes,please contact administration');
      }
      
      /*--teacher access--*/
      $classTeachercourse= TeacherCoordinator::where('teacher_id',Auth::user()->hook_id)
        ->where('branch_id',Session::get('activeBranch'))
        ->where('record_status',1)
        ->where('session_id',Session::get('activeSession'))->pluck('faculty_id')->toArray();
       $classTeacher= TeacherCoordinator::where('teacher_id',Auth::user()->hook_id)
        ->where('branch_id',Session::get('activeBranch'))
        ->where('record_status',1)
        ->where('session_id',Session::get('activeSession'))->pluck('section_id')->toArray();
       $data['faculty'] = $this->activeFaculties();

      $data['section'] = DB::table('semesters')->select('id','semester')->where([
              ['status','=',1]
            ])
            ->Where(function($q)use($classTeacher){
             if(count($classTeacher)>0){
                $q->whereIn('semesters.id',$classTeacher);
             }
             })->pluck('semester','id')->toArray();
      /*--teacher access--*/
      $data['meeting'] =  LiveClass::select('live_classes.*','sem.semester as section',DB::raw("CONCAT(stf.first_name,' ',stf.last_name) as staff_name"))
      ->leftjoin('semesters as sem','sem.id','=','live_classes.section_id')
      ->leftjoin('staff as stf','stf.id','=','live_classes.created_by')
      ->where([
        ['live_classes.created_by','=',auth()->user()->hook_id],
        ['live_classes.branch_id','=',Session::get('activeBranch')],
        ['live_classes.session_id','=',Session::get('activeSession')],
        ['live_classes.status','=',1]
      ])
      ->orderBy('start_time','desc')
      ->get();      
      // dd($data['meeting']);
      $data['general_setting'] = DB::table('general_settings')->select('live_class_scheduling')->first();
      $data['observer'] = DB::table('staff')->select('observer')->where('id',auth()->user()->hook_id)->first();
      return view(parent::loadDataToView($this->view_path.'.create'),compact('api_key','secret_key','email','data'));
    }
    public function host_class($meeting_id,$meeting_topic){
      $row = LiveClass::select('live_classes.*','staff.zoom_api_key','staff.zoom_secret_key',DB::raw("CONCAT(staff.first_name,' ',staff.last_name) as staff_name"))
      ->leftjoin('staff','staff.id','=','live_classes.created_by')
      ->where([
        ['live_classes.status','=',1],
        ['live_classes.id','=',$meeting_id],
        ['live_classes.topic','=',$meeting_topic]
      ])->first();
      if(!$row){
        return redirect()->back()->with('message_danger',"Something went wrong");
      }
      return view(parent::loadDataToView($this->view_path.'.host'),compact('row'));
    }
    public function meeting_attendance($meeting_id,Request $request){
      $drop['faculty'] = Faculty::select('id', 'faculty')
            ->where([
              ['branch_id','=',Session::get('activeBranch')],
              ['status','=',1]
            ])->pluck('faculty','id')->toArray();
      $drop['section'] = DB::table('semesters')->select('id','semester')->where([
              ['status','=',1]
            ])->pluck('semester','id')->toArray();
      $meeting = LiveClass::find($meeting_id);
      $list     = new ServicesController;
      $request->request->add(['meeting_id'=>$meeting_id]);
      $data     = new Request($request->all());
      $resp = $list->list_attendance($data);
      $resp =   $resp->getContent();
      $data = json_decode($resp);
      $data = $data->data;
      return view(parent::loadDataToView($this->view_path.'.list'),compact('data','meeting','drop'));
    }
    public function delete_class($meeting_id){
      $row = LiveClass::find($meeting_id);
      if(!$row){
        parent::invalidRequest();
      }
      $services = new ServicesController;
      $resp = $services->delete_live_class($meeting_id,auth()->user()->id);
     $data = json_decode($resp->getContent());
      if($data->data){
        return redirect()->back()->with('message_success','Live Class Deleted');
      }else{
        return redirect()->back()->with('message_warning','Something Went Wrong');
      }
    }
    
    /*
    public function observe($meeting_id='',Request $request){
       $email = $secret_key = $api_key = null;
       if($meeting_id){
          $row = DB::table('live_classes')->select('live_classes.*','staff.zoom_api_key','staff.zoom_secret_key',DB::raw("CONCAT(staff.first_name,' ',staff.last_name) as staff_name"))
          ->leftjoin('staff','staff.id','=','live_classes.created_by')
          ->where([
            ['live_classes.status','=',1],
            ['live_classes.id','=',$meeting_id],
            // ['live_classes.topic','=',$meeting_topic]
          ])->first();
          
          $user = DB::table('staff')->select(DB::raw("CONCAT(first_name,' ',last_name) as name"))
            ->where([
              ['id','=',auth()->user()->hook_id]
            ])->first();
          if(!$row){
            return redirect()->back()->with('message_danger',"Something went wrong");
          }
          return view(parent::loadDataToView($this->view_path.'.observe'),compact('row','user'));
        }
        if(auth()->user()->role_id !=6 && auth()->user()->role_id !=7){
          $zoom_keys = DB::table('staff')->select('zoom_api_key','zoom_secret_key','zoom_email')->where('id',auth()->user()->hook_id)->first();
        }
        
        $data['observer'] = DB::table('staff')->select('observer')->where('id',auth()->user()->hook_id)->first();
        
        $data['faculty'] = Faculty::select('id', 'faculty')
              ->where([
                ['branch_id','=',Session::get('activeBranch')],
                ['status','=',1]
              ])->pluck('faculty','id')->toArray();
        $data['section'] = DB::table('semesters')->select('id','semester')->where([
                ['status','=',1]
              ])->pluck('semester','id')->toArray();
        $data['section'] = array_prepend($data['section'],'--Select--','');
        $date = Carbon::now()->format('Y-m-d');
        // dd($request->all());
        $data['meeting'] =  LiveClass::select('live_classes.*','sem.semester as section',DB::raw("CONCAT(staff.first_name,' ',staff.last_name) as staff_name"))
        ->leftjoin('semesters as sem','sem.id','=','live_classes.section_id')
        ->leftjoin('staff','staff.id','=','live_classes.created_by')
        ->where(function($q)use($request,$date){
          if(!empty($request->from_date) && !empty($request->to_date)){
            $q->whereBetween('start_time',[$request->from_date.' 00:00:00',$request->to_date.' 23:59:59']);
          }else{
            if(isset($_GET['from_date'])){

            }else{
              $q->whereBetween('start_time',[$date.' 00:00:00',$date.' 23:59:59']);
            }
            
          }
          if($request->faculty){
            $q->where('faculty_id',$request->faculty);
          }
          if($request->section){
            $q->where('section_id',$request->section);
          }
        })
        ->where([
          ['live_classes.branch_id','=',Session::get('activeBranch')],
          ['live_classes.session_id','=',Session::get('activeSession')],
          ['live_classes.status','=',1]
        ])
        ->orderBy('start_time','desc')
        ->get();      
        // dd($data['meeting']);
        $data['general_setting'] = DB::table('general_settings')->select('live_class_scheduling')->first();
      
      return view(parent::loadDataToView($this->view_path.'.observe_class'),compact('data'));
    }
    
    */
    
    
    public function observe($meeting_id='',Request $request){
       $email = $secret_key = $api_key = null;
       if($meeting_id){
          $row = DB::table('live_classes')->select('live_classes.*','staff.zoom_api_key','staff.zoom_secret_key',DB::raw("CONCAT(staff.first_name,' ',staff.last_name) as staff_name"))
          ->leftjoin('staff','staff.id','=','live_classes.created_by')
          ->where([
            ['live_classes.status','=',1],
            ['live_classes.id','=',$meeting_id],
            // ['live_classes.topic','=',$meeting_topic]
          ])->first();
          
          $user = DB::table('staff')->select(DB::raw("CONCAT(first_name,' ',last_name) as name"))
            ->where([
              ['id','=',auth()->user()->hook_id]
            ])->first();
          if(!$row){
            return redirect()->back()->with('message_danger',"Something went wrong");
          }
          return view(parent::loadDataToView($this->view_path.'.observe'),compact('row','user'));
        }
        if(auth()->user()->role_id !=6 && auth()->user()->role_id !=7){
          $zoom_keys = DB::table('staff')->select('zoom_api_key','zoom_secret_key','zoom_email')->where('id',auth()->user()->hook_id)->first();
        }
        // dd($request->all());
        $data['observer'] = DB::table('staff')->select('observer')->where('id',auth()->user()->hook_id)->first();
         /*--teacher access--*/
        $data['faculty'] = $this->activeFaculties();
        $classTeacher= TeacherCoordinator::where('teacher_id',Auth::user()->hook_id)
        ->where('branch_id',Session::get('activeBranch'))
        ->where('record_status',1)
        ->where('session_id',Session::get('activeSession'))->pluck('section_id')->toArray();

        $data['section'] = DB::table('semesters')->select('id','semester')->where([
                ['status','=',1]
              ])
              ->Where(function($q)use($classTeacher){
              if(count($classTeacher)>0){
                $q->whereIn('semesters.id',$classTeacher);
               }
             })
         ->pluck('semester','id')->toArray();
          /*--teacher access--*/
        $data['section'] = array_prepend($data['section'],'--Select--','');
        $date = Carbon::now()->format('Y-m-d');
        // dd($request->all());
        $data['meeting'] =  LiveClass::select('live_classes.*','sem.semester as section',DB::raw("CONCAT(staff.first_name,' ',staff.last_name) as staff_name"))
        ->leftjoin('semesters as sem','sem.id','=','live_classes.section_id')
        ->leftjoin('staff','staff.id','=','live_classes.created_by')
        ->where(function($q)use($request,$date){
          if(!empty($request->from_date) && !empty($request->to_date)){
            $q->whereBetween('start_time',[$request->from_date.' 00:00:00',$request->to_date.' 23:59:59']);
          }else{
            if(isset($_GET['from_date'])){

            }else{
              $q->whereBetween('start_time',[$date.' 00:00:00',$date.' 23:59:59']);
            }
            
          }
          if($request->faculty){
            $q->where('faculty_id',$request->faculty);
          }
          if($request->section){
            $q->where('section_id',$request->section);
          }
        })
         /*--teacher access--*/
        ->Where(function($q)use($classTeacher){
             if(count($classTeacher)>0){
                $q->whereIn('live_classes.section_id',$classTeacher);
             }
             })
         /*--teacher access--*/
        ->where([
          ['live_classes.branch_id','=',Session::get('activeBranch')],
          ['live_classes.session_id','=',Session::get('activeSession')],
          ['live_classes.status','=',1]
        ])
        ->orderBy('start_time','desc')
        ->get();      
        // dd($data['meeting']);
        $data['general_setting'] = DB::table('general_settings')->select('live_class_scheduling')->first();
      
      return view(parent::loadDataToView($this->view_path.'.observe_class'),compact('data'));
    }
}
