<?php

namespace App\Http\Controllers\Api\Timetable;
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
use Response;
use ViewHelper,Validator;
use App\Models\FeeHead;
use Carbon\Carbon;




       
class TimetableController extends CollegeBaseController
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

   
    public function AddSubject(Request $request)
    {
        if($request->user_id){
           $request->request->add(['created_by'=>$request->user_id]);
         }
           $request->request->add(['created_at'=>Carbon::now()]);

           if($request->branch_id && $request->session_id){
              $insert=DB::table('timetable_subjects')->insertGetId([
                'title'=>$request->subject,
                'branch_id'=>$request->branch_id,
                'status'=>1,
                'course_id'=>$request->course,
                'section_id'=>$request->section,
                'session_id'   =>$request->session_id,
                'created_by'=>$request->created_by
               ]);
              if($insert){
                 $data['msg']= "Subject Added";
                 $data['status']= 1;
              }
              else{
                  $data['msg']= "Something Went Wrong";
                  $data['status']= 0;
              }
           }
           else{
            $data['msg']= "Invalid Request Msg Validation Failed!!!";
           }
     
   
         return $this->return_data_in_json($data);
    }

    public function SubjectList(Request $request)
    {
       $data=[];
      if($request->branch_id && $request->session_id){
        $data['subject_list']=DB::table('timetable_subjects')->select('timetable_subjects.*','faculties.faculty as course','semesters.semester as section')
      ->leftjoin('faculties','faculties.id','timetable_subjects.course_id')
      ->leftjoin('semesters','semesters.id','timetable_subjects.section_id')
      ->where([
        ['timetable_subjects.branch_id','=',$request->branch_id],
        ['timetable_subjects.session_id','=',$request->session_id],
        ['timetable_subjects.status','=',1]
      ])
      ->where(function($q) use($request){
        if($request->course_id){
           $q->where('course_id',$request->course_id);
        }
         if($request->section_id){
           $q->where('section_id',$request->section_id);
        }
      })
      ->orderBy('course','ASC')
      ->where('timetable_subjects.status',1)
      ->get();
      }
      else{
        $data['msg']= "Invalid Request Msg Validation Failed!!!";
      }
       return $this->return_data_in_json($data);
    }
    public function SubjectDelete(Request $request)
    {
       $data=[];
     if($request->user_id){
          $request->request->add(['updated_by'=>$request->user_id]);
         }
          $request->request->add(['updated_at'=>Carbon::now()]);
         $list= DB::table('timetable_subjects')->select('*')->where('id',$request->id)->where('status',1)->first();
        if($list){
            $update=DB::table('timetable_subjects')->select('*')->where('id',$request->id)->update([
            'status'=>0,
            'updated_at'=>$request->updated_at,
            'updated_by'=>$request->updated_by
           ]);
            if($update){
                 $data['msg']= "Subject Deleted";
                 $data['status']= 1;
            }
            else{
                $data['msg']= "Something Went Wrong";
                $data['status']= 0;
            }
        }
        else{
          $data['msg']= "Data Not Found!!!";
        }
        return $this->return_data_in_json($data);
        
    }
    public function SubjectEdit(Request $request)
    {
      if($request->user_id){
           $request->request->add(['updated_by'=>$request->user_id]);
         }
          $request->request->add(['updated_at'=>Carbon::now()]);
          
           if($request->id){
              $update=DB::table('timetable_subjects')->where('id',$request->id)->update([
                'course_id'=>$request->course,
                'section_id'=>$request->section,
                'title'=>$request->subject,
                'updated_at'=>$request->updated_at,
                'updated_by'=>$request->updated_by
              ]);
              if($update){
                 $data['msg']= "Subject updated";
                 $data['status']= 1;
              }
              else{
                  $data['msg']= "Something Went Wrong";
                  $data['status']= 0;
              }
           }
           else{
            $data['msg']= "Invalid Request Msg Validation Failed!!!";
           }
     
   
         return $this->return_data_in_json($data);
    }
   public function AssignSubjectTeacher(Request $request)
    {
       if($request->branch_id && $request->session_id){
          if($request->user_id){
            $request->request->add(['created_by'=>$request->user_id]);
          }
          $request->request->add(['created_at'=>Carbon::now()]);

          if($request->branch_id && $request->session_id){
            $chkexist= DB::table('timetable_assign_subject')
            ->where('branch_id',$request->branch_id)
            ->where('session_id',$request->session_id)
            ->where('timetable_subject_id',$request->subject)
            ->where('staff_id',$request->teacher)
            ->where('status',1)
            ->first();
            if(!$chkexist){
               $insert=DB::table('timetable_assign_subject')->insertGetId([
                'created_by'=>$request->created_by,
                'staff_id'=>$request->teacher,
                'timetable_subject_id'=>$request->subject,
                'session_id'=>$request->session_id,
                'branch_id'=>$request->branch_id,
                'created_at'=>$request->created_at,
                'status'=>1
              ]);
                if($insert){
                   $data['msg']= "Subject assigned to selected teacher";
                   $data['status']= 1;
                }
                else{
                    $data['msg']= "Something Went Wrong";
                    $data['status']= 0;
                }
            }
            else{
              $data['msg']= "Subject Already Assigned To This Teacher!!!";
              $data['status']= 0;
            }
           
          }
       }
       else{
            $data['msg']= "Invalid Request Msg Validation Failed!!!";
            $data['status']= 0;
           }
       return $this->return_data_in_json($data);
    }

    public function AssignSubjectTeacherList(Request $request)
    {
      if($request->branch_id  && $request->session_id){
        $data['assign_subject_teacher'] = DB::table('timetable_assign_subject')->select('timetable_assign_subject.*','sbj.title as subject',DB::raw("CONCAT(st.first_name,' ',st.last_name,'(',st.reg_no,')') as teacher"))
        ->leftjoin('timetable_subjects as sbj','sbj.id','=','timetable_assign_subject.timetable_subject_id')
        ->leftjoin('staff as st','st.id','=','timetable_assign_subject.staff_id')
        ->where([
          ['sbj.status','=',1],
          ['timetable_assign_subject.status','=',1],
          ['timetable_assign_subject.branch_id','=',$request->branch_id],
          ['timetable_assign_subject.session_id','=',$request->session_id]
          ])
        ->orderBy('subject','ASC')
        ->where(function ($q) use ($request){
          if($request->staff_id){
            $q->where('timetable_assign_subject.staff_id','=',$request->staff_id);
          }
        })
        ->get();
        }
        else{
           $data['msg']= "Invalid Request Msg Validation Failed!!!";
        }
        return $this->return_data_in_json($data);
    }
    public function AssignSubjectTeacherEdit(Request $request)
    {
      if($request->user_id && $request->branch_id && $request->session_id){
           $request->request->add(['updated_by'=>$request->user_id]);
         }
          $request->request->add(['updated_at'=>Carbon::now()]);
          $chkexist= DB::table('timetable_assign_subject')
          ->where('branch_id',$request->branch_id)
          ->where('session_id',$request->session_id)
          ->where('timetable_subject_id',$request->subject)
          ->where('staff_id',$request->teacher)
          ->where('status',1)
          ->first();
          
          $list=DB::table('timetable_assign_subject')->select('id')->where('id',$request->id)->where('status',1)->first();
           if($list && !$chkexist){
              
                  $update=DB::table('timetable_assign_subject')->where('id',$request->id)->update([
                  'timetable_subject_id'=>$request->subject,
                  'staff_id'=>$request->teacher,
                  'updated_at'=>$request->updated_at,
                  'updated_by'=>$request->updated_by
                  ]);
                  if($update){
                     $data['msg']= "Updated Successfully";
                     $data['status']= 1;
                    }
                    else{
                        $data['msg']= "Something Went Wrong";
                        $data['status']= 0;
                    }
              
              
           }
           else{
             $data['msg']= "Subject Alredy Assigned to This Teacher !!!";
             $data['status']= 0;
           }
     
   
         return $this->return_data_in_json($data);
    }
    public function AssignSubjectTeacherDelete(Request $request)
    {
       if($request->user_id){
           $request->request->add(['updated_by'=>$request->user_id]);
          }
          $request->request->add(['updated_at'=>Carbon::now()]);
          $list=DB::table('timetable_assign_subject')->select('id')->where('id',$request->id)->where('status',1)->first();
          if($list){
            $update=DB::table('timetable_assign_subject')->where('id',$request->id)->update([
            'status'=>0,
            'updated_at'=>$request->updated_at,
            'updated_by'=>$request->updated_by
           ]);
             if($update){
                 $data['msg']= "Deleted Successfully";
                 $data['status']= 1;
              }
              else{
                  $data['msg']= "Something Went Wrong";
                  $data['status']= 0;
              }
          }
          else{
            $data['msg']= "No data Found!!!";
           }
        return $this->return_data_in_json($data);
    }
    /*01-10-2021*/
    public function AddNewSchedule(Request $request)
    {
       if($request->branch_id){
         $from=Carbon::parse($request->from)->addMinute()->format('H:i:s');
         $to=Carbon::parse($request->to)->subMinute()->format('H:i:s');   
         $check=DB::table('timetable')->select('*')
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
            
          if(count($check)==0){

             if((!empty($request->secondary_teacher)) && ($request->secondary_teacher==$request->teacher)){
               $data['status']=0;
               $data['msg']= "Secondary teacher cannot be same as primary teacher";
               return $this->return_data_in_json($data);
            }
            $data['timetable']=DB::table('timetable')->insert([
            'created_at'=>Carbon::now(),
            'created_by'=>$request->user_id,
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
            'session_id'=>$request->session_id,
            'branch_id'=>$request->branch_id,
            'status'=>1
            ]);
            if( $data['timetable']){
              $data['status']=1;
              $data['msg']= "Schedule Added Successfully";
            }
            else{
               $data['status']=0;
               $data['msg']= "Something Went wrong!!!";
            }
          }       
        }
       else{
         $data['status']=0;
         $data['msg']= "Invalid Request Msg Validation Failed!!!";
       }
        return $this->return_data_in_json($data);
    }
    public function checkTeacherAvailbale(Request $request)
    {
      if($request->from && $request->to && $request->day && $request->staff_id){
            $from= Carbon::parse($request->from)->addMinute()->format('H:i:s');
            $to= Carbon::parse($request->to)->subMinute()->format('H:i:s');
           // dd($from,$to);
            $check=DB::table('timetable')->select('*')
                ->where([
                  ['day_id','=',$request->day],
                  ['staff_id','=',$request->staff_id],
                  ['is_break','=',0]
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
             
              if(count($check)==0){
                   $data['msg']= "Available";
                 }
                else {
                 $data['msg']= " Not Available.";
                }
      }
      else{
        $data['msg']= "Invalid Request Msg Validation failed!!!";
      }
      return $this->return_data_in_json($data);
    }
    /*01-10-2021*/
    

   
   


   




    
 




        
}



