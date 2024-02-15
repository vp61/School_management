<?php

namespace App\Http\Controllers\TimeTable;
use App\Http\Controllers\CollegeBaseController;
use Illuminate\Http\Request;
use App\Http\Requests\Transport\User\AddValidation;
use App\Http\Requests\Transport\User\EditValidation;
use App\Models\Faculty;
use App\Models\Staff;
use App\Models\Student;
use URL,DB,Session;
use Carbon\Carbon;

class TimeTableController extends CollegeBaseController
{
    protected $base_route = 'timetable';
    protected $view_path = 'time-table';
    protected $panel = 'Time Table';
    protected $filter_query = [];

    public function index(Request $request)
    {
      $date=Carbon::now()->format('Y-m-d');
      $date=explode('-', $date);
      $month=$date[1];
      if($month[0]==0){
        $month=$month[1];
      }
      $day=$date[2];
      if($day[0]==0){
        $day=$day[1];
      }
      $data['att']=DB::table('attendances')->select('attendees_type','link_id',"day_$day as present",'months_id','years_id','years.title',DB::raw("CONCAT(st.first_name,' ',st.last_name) as staff"))
      ->leftjoin('years','years.id','=','attendances.years_id')
      ->leftjoin('staff as st','st.id','=','attendances.link_id')
      ->where([
        ['years.title','=',$date[0]],
        ['attendees_type','=',2],
        ['months_id','=',$month],
      ])
      ->get();
      foreach ($data['att'] as $key => $value) {
        $atten[$value->link_id]=$value;
      }
      $branch_id=Session::get('activeBranch');
      $data['course']=Faculty::select('id','faculty')->where('branch_id',$branch_id)->orderBy('faculty','ASC')->pluck('faculty','id')->toArray();
      $data['course']=array_prepend($data['course'],"--Select ".env("course_label")."--","");  
      $data['section']=Faculty::select('semesters.id','semesters.semester')
      ->where('faculties.branch_id',Session::get('activeBranch'))
      ->distinct('semesters.id')
      ->leftjoin('faculty_semester','faculty_semester.faculty_id','=','faculties.id')
      ->leftjoin('semesters','semesters.id','=','faculty_semester.semester_id')
      ->orderBy('semester','ASC')
      ->pluck('semesters.semester','semesters.id')
      ->toArray();
      $data['section']=array_prepend($data['section'],"Section","");
      $data['day']=DB::table('days')->select('id','title')->orderBy('id','ASC')->pluck('title','id')->toArray();
      $data['day']=array_prepend($data['day'],"--Select Day--","");

      $data['teacher']=DB::table('staff')->select('id',DB::raw("CONCAT(first_name,' ',last_name,'(',reg_no,')') as title"))
      ->where([
        ['status','=',1],
        ['branch_id','=',Session::get('activeBranch')],
        ['designation','=',env('TEACHER_DESIGNATION')]
      ])
      ->orderBy('title','ASC')
      ->pluck('title','id')
      ->toArray();
      $data['teacher']=array_prepend($data['teacher'],"--Select Teacher--","");
      $data['subject']=DB::table('timetable_subjects')->select('id','title')
      ->where([
        ['timetable_subjects.branch_id','=',Session::get('activeBranch')],
        ['timetable_subjects.session_id','=',Session::get('activeSession')],
        ['timetable_subjects.status','=',1]
      ])
      ->orderBy('title','ASC')
      ->pluck('title','id')
      ->toArray();
      $data['subject']=array_prepend($data['subject'],"--Select Subject--","");
      /*Data For Table Header and Body*/
       $data['days']=DB::table('days')->select('id','title')->orderBy('id','ASC')->get();

      return view(parent::loadDataToView($this->view_path.'.index'),compact('data','atten'));
    }
    public function add(Request $request){
      
        $from=Carbon::parse($request->from)->addMinute()->format('H:i:s');
         $to=Carbon::parse($request->to)->subMinute()->format('H:i:s');   
               $data['check']=DB::table('timetable')->select('*')
                ->where([
                  ['day_id','=',$request->day],
                  ['staff_id','=',$request->teacher]
                ])
                ->where(function($query) use ($from,$to){ 
                        $query->where([
                        ['time_from','<=',$from],
                        ['time_to','>=',$from]
                                ])
                     ->orWhere([
                        ['time_from','<=',$to],
                        ['time_to','>=',$to]
                     ]);        
                })
                ->get();        
    $data['day']=DB::table('days')->select('title')->where('id',$request->day)->first();
    
        if(count($data['check'])==0){
            if((!empty($request->secondary_teacher)) && ($request->secondary_teacher==$request->teacher)){
              return redirect('timetable')->with('message_warning','Secondary teacher cannot be same as primary teacher');
            }
          $data['timetable']=DB::table('timetable')->insert([
            'created_at'=>Carbon::now(),
            'created_by'=>auth()->user()->id,
            'day_id'=>$request->day,
            'course_id'=>$request->course,
            'section_id'=>$request->section,
            'timetable_subject_id'=>$request->subject,
            'subject_type'=>$request->type,
            'staff_id'=>$request->teacher,
            'time_from'=>$request->from,
            'time_to'=>$request->to,
            'room_no'=>$request->room,
            's_staff_from'=>$request->s_staff_from,
            's_staff_to'=>$request->s_staff_to,
            'is_break'=>$request->break,
            'secondary_staff'=>$request->secondary_teacher,
            'session_id'=>Session::get('activeSession'),
            'branch_id'=>Session::get('activeBranch'),
            'status'=>1
          ]);
          if($data['timetable']){
            return redirect('timetable')->with('message_success','Schedule Added Successfully');
          }

          else{
               return redirect('timetable')->with('message_warning','Something went wrong.Please try again.');
          }
        }
        else{
           return redirect('timetable')->with('message_warning','Teacher/Class already assigned on '.$data['day']->title.' from '.$from.' to '.$to);
        }  
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
      $data['schedule']=DB::table('timetable')->select('*')->where('id',$id)->first();
     $branch_id=Session::get('activeBranch');
      $data['course']=Faculty::select('id','faculty')->where('branch_id',$branch_id)->orderBy('faculty','ASC')->pluck('faculty','id')->toArray();
      $data['course']=array_prepend($data['course'],"--Select ".env("course_label")."--","");  
      $data['section']=Faculty::select('semesters.id','semesters.semester')
      ->where('faculties.branch_id',Session::get('activeBranch'))
      ->distinct('semesters.id')
      ->leftjoin('faculty_semester','faculty_semester.faculty_id','=','faculties.id')
      ->leftjoin('semesters','semesters.id','=','faculty_semester.semester_id')
      ->orderBy('semester','ASC')
      ->pluck('semesters.semester','semesters.id')
      ->toArray();
      $data['section']=array_prepend($data['section'],"--Select Section--","");
      $data['day']=DB::table('days')->select('id','title')->orderBy('id','ASC')->pluck('title','id')->toArray();
      $data['day']=array_prepend($data['day'],"--Select Day--","");

      $data['teacher']=DB::table('staff')->select('id',DB::raw("CONCAT(first_name,' ',last_name,'(',reg_no,')') as title"))
      ->where([
        ['status','=',1],
        ['branch_id','=',Session::get('activeBranch')],
        ['designation','=',env('TEACHER_DESIGNATION')]
      ])
      ->orderBy('title','ASC')
      ->pluck('title','id')
      ->toArray();
      $data['teacher']=array_prepend($data['teacher'],"--Select Teacher--","");
      $data['subject']=DB::table('timetable_subjects')->select('id','title')
      ->where([
        ['timetable_subjects.branch_id','=',Session::get('activeBranch')],
        ['timetable_subjects.session_id','=',Session::get('activeSession')],
        ['timetable_subjects.status','=',1]
      ])
      ->orderBy('title','ASC')
      ->pluck('title','id')
      ->toArray();
      $data['subject']=array_prepend($data['subject'],"--Select Subject--","");
      return view(parent::loadDataToView($this->view_path.'.edit'),compact('data','id'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
  public function update(Request $request,$id)
  {
    $from=Carbon::parse($request->from)->format('H:i:s');
         $to=Carbon::parse($request->to)->format('H:i:s');
             $data['check']=DB::table('timetable')->select('*')
                ->where([
                  ['day_id','=',$request->day],
                  ['staff_id','=',$request->teacher]
                ])
                ->where(function($query) use ($from,$to){ 
                        $query->where([
                        ['time_from','<=',$from],
                        ['time_to','>=',$from]
                                ])
                     ->orWhere([
                        ['time_from','<=',$to],
                        ['time_to','>=',$to]
                     ]);        
                })
                ->get();        

          $data['day']=DB::table('days')->select('title')->where('id',$request->day)->first();
        if(count($data['check'])==0){
          if($request->secondary_teacher==$request->teacher){
              return redirect('timetable')->with('message_warning','Secondary teacher cannot be same as primary teacher');
            }
          $data['timetable']=DB::table('timetable')->updateOrInsert(
            ['id'=>$id],
            [
            'updated_at'=>Carbon::now(),
            'updated_by'=>auth()->user()->id,
            'day_id'=>$request->day,
            'timetable_subject_id'=>$request->subject,
            'subject_type'=>$request->type,
            'staff_id'=>$request->teacher,
            'time_from'=>$request->from,
            'time_to'=>$request->to,
            'secondary_staff'=>$request->secondary_teacher,
            'room_no'=>$request->room,
            'is_break'=>$request->break,
            's_staff_from'=>$request->s_staff_from,
            's_staff_to'=>$request->s_staff_to,
          ]);


          if($data['timetable']){
            return redirect('timetable')->with('message_success','Schedule Updated Successfully');
          }

          else{
               return redirect('timetable')->with('message_warning','Something went wrong.Please try again.');
          }
        }
        else{
           return redirect('timetable')->with('message_warning','Teacher not available on '.$data['day']->title.' from '.$from.' to '.$to);
        }  
      
      
  }
  public function daily_index(Request $request){
    $currentDate=Carbon::now()->format('Y-m-d');
    $weekday=DB::select(DB::raw("SELECT dayofweek('$currentDate') as day"));
     // dd($weekday);
    $weekday=$weekday[0]->day;

    $date=Carbon::now()->format('Y-m-d');
        $date=explode('-', $date);
        $month=$date[1];
        if($month[0]==0){
          $month=$month[1];
        }
        $day=$date[2];
        if($day[0]==0){
          $day=$day[1];
        }

    $data['timetable']=DB::table('timetable')->select('timetable.id','fcl.faculty as course',"att.day_$day as present",'timetable.course_id','timetable.section_id','sem.semester as section','sub.title as subject','timetable_subject_id as subject_id','subject_type as type',DB::raw("date_format(time_from,'%H:%i') as time_from "),DB::raw("date_format(time_to,'%H:%i') as time_to "),'room_no',DB::raw("CONCAT(st.first_name,' ',st.last_name) as staff"),'timetable.staff_id',DB::raw("CONCAT(altstf.first_name,' ',altstf.last_name) as altTeacher"),'alt.staff_id as alternate','br.branch_name','branch_logo','branch_mobile','branch_email','branch_address')
    ->leftjoin('attendances as att','att.link_id','=','timetable.staff_id')
    ->leftjoin('years','years.id','=','att.years_id')
    ->leftjoin('faculties as fcl','fcl.id','=','timetable.course_id')
    ->leftjoin('semesters as sem','sem.id','=','timetable.section_id')
    ->leftjoin('staff as st','st.id','=','timetable.staff_id')
    ->leftjoin('timetable_subjects as sub','sub.id','=','timetable.timetable_subject_id')
    ->leftjoin('timetable_alt_teacher as alt',function($q){
      $q->on('alt.timetable_id','=','timetable.id');
    })
    ->leftjoin('staff as altstf','altstf.id','=','alt.staff_id')
    ->leftjoin('branches as br','br.id','=','timetable.branch_id')
    ->where([
      ['timetable.day_id','=',$weekday],
      ['timetable.branch_id','=',Session::get('activeBranch')],
      ['timetable.session_id','=',Session::get('activeSession')],
      ['timetable.status','=',1],
      ['att.attendees_type','=',2],
    //   ['att.months_id','=',$month],
      ['years.title','=',$date[0]],
      ['is_break','=',0]

    ])
    ->orderBy('course','Asc')
    ->orderBy('timetable.time_from','Asc')
    ->get();
    // dd($data);
    
    if(count($data['timetable'])==0){
      return redirect('/timetable')->with('message_warning','No Schedule For Today');
    }
    foreach ($data['timetable'] as $key => $value) {
      $data['schedule'][$value->course_id][$value->section_id][]=$value;
      if($value->present!=1){
        $data['subject'][$value->id]=$value;
      }
    }
    $val=0;
    foreach ($data['schedule'] as $key => $value) {
      foreach ($value as $key => $valu) {
          $count=count($valu);
          if($count>$val){
            $val=$count;
          }
      }
    }
    $data['row']=$val;
    // dd($data['schedule']);
   
    return view(parent::loadDataToView($this->view_path.'.daily_index'),compact('data'));
  }

}
