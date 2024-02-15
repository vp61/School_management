<?php
  namespace App\Http\Controllers\InternalMeeting;
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


class InternalMeetingController extends CollegeBaseController
{
    
    protected $base_route = 'internal_meeting';
    protected $view_path = 'InternalMeeting';
    protected $panel = 'Internal Meeting';
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
      // $data['faculty'] = Faculty::select('id', 'faculty')
      //       ->where([
      //         ['branch_id','=',Session::get('activeBranch')],
      //         ['status','=',1]
      //       ])->pluck('faculty','id')->toArray();
      // $data['section'] = DB::table('semesters')->select('id','semester')->where([
      //         ['status','=',1]
      //       ])->pluck('semester','id')->toArray();

      $data['host_for'] = DB::table('internal_meeting_joiners')->select('title','value')
      ->where([
              ['record_status','=',1]
            ])->pluck('title','value')->toArray();


      $data['meeting'] =  DB::table('internal_meetings')->select('internal_meetings.*')
      
      ->where([
        ['internal_meetings.branch_id','=',Session::get('activeBranch')],
        ['internal_meetings.session_id','=',Session::get('activeSession')],
        ['internal_meetings.status','=',1]
      ])
      ->where(function($q){
        $q->where('internal_meetings.created_by',auth()->user()->hook_id);
        $q->orWhere('internal_meetings.host_for',0);
        $q->orwhere('internal_meetings.host_for',1);
      })
      ->orderBy('start_time','desc')
      ->get();      
      // dd($data['meeting']);
      $data['general_setting'] = DB::table('general_settings')->select('live_class_scheduling')->first();
      return view(parent::loadDataToView($this->view_path.'.create'),compact('api_key','secret_key','email','data'));
    }
    public function host_meeting($meeting_id){
      $row = DB::table('internal_meetings')->select('internal_meetings.*','staff.zoom_api_key','staff.zoom_secret_key',DB::raw("CONCAT(staff.first_name,' ',staff.last_name) as staff_name"))
      ->leftjoin('staff','staff.id','=','internal_meetings.created_by')
      ->where([
        ['internal_meetings.status','=',1],
        ['internal_meetings.id','=',$meeting_id],
        // ['live_classes.topic','=',$meeting_topic]
      ])->first();
      if(!$row){
        return redirect()->back()->with('message_danger',"Something went wrong");
      }
      return view(parent::loadDataToView($this->view_path.'.host'),compact('row'));
    }
    public function join_meeting($meeting_id){
      $row = DB::table('internal_meetings')->select('internal_meetings.*','staff.zoom_api_key','staff.zoom_secret_key',DB::raw("CONCAT(staff.first_name,' ',staff.last_name) as staff_name"))
      ->leftjoin('staff','staff.id','=','internal_meetings.created_by')
      ->where([
        ['internal_meetings.status','=',1],
        ['internal_meetings.id','=',$meeting_id],
        // ['live_classes.topic','=',$meeting_topic]
      ])->first();
      $user = DB::table('staff')->select(DB::raw("CONCAT(first_name,' ',last_name) as name"))
        ->where([
          ['id','=',auth()->user()->hook_id]
        ])->first();
      if(!$row){
        return redirect()->back()->with('message_danger',"Something went wrong");
      }
      return view(parent::loadDataToView($this->view_path.'.join'),compact('row','user'));
    }
    public function delete_meeting($meeting_id){
      $row = DB::table('internal_meetings')->find($meeting_id);
      if(!$row){
        parent::invalidRequest();
      }
      $services = new ServicesController;
      $resp = $services->delete_meeting($meeting_id,auth()->user()->id);
      $data = json_decode($resp->getContent());
      if($data->data){
        return redirect()->back()->with('message_success','Meeting Deleted');
      }else{
        return redirect()->back()->with('message_warning','Something Went Wrong');
      }
    }
}
