<?php

namespace App\Http\Controllers\Api\FrontDesk;
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
use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\FrontDesk\VisitorController;
use App\Http\Controllers\FrontDesk\CallLogController;
use App\Http\Controllers\FrontDesk\ComplainController;
use App\Http\Controllers\FrontDesk\PostalController;
use App\Models\Postal;
use Carbon;
use App\Admission;
use App\Models\Complain;
use App\Models\CallLog; 
use App\Models\FollowUpHistory;



       
class FrontdeskController extends CollegeBaseController
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

   
    public function StoreEnquiry(Request $request)
    {
        
        Log::debug("Data_Store_Enquiry");
        Log::Debug($request->all());
        if($request->branch_id && $request->session_id && $request->user_id){
        
            $request->request->add(['created_at'=>Carbon\carbon::now()->format('Y-m-d')]);
            $request->request->add(['org_id'=>1]);
            
            $enquiry  =  new EnquiryController; 
            $insert= $enquiry->store($request);
            if($insert){
               $data['msg']= "Enquiry Added  SuccessFully";
               $data['status']= 1;
            }
            else{
                $data['msg']= "Something Went Wrong";
                $data['status']= 0;
            }
        }
         else{
              $data['msg']= "Invalid Request!! Msg validation Failed!!!";
            }
             return $this->return_data_in_json($data);
    }
    public function UpdateEnquiry(Request $request)
    {
        if($request->branch_id && $request->session_id && $request->user_id && $request->id){
            $id= $request->id;
            $request->request->add(['created_at'=>Carbon\carbon::now()->format('Y-m-d')]);
            $request->request->add(['org_id'=>1]);
            
            $enquiry  =  new EnquiryController; 
            $update= $enquiry->enquiryupdate($request,$id);
            if($update){
               $data['msg']= "Enquiry Updated  SuccessFully";
               $data['status']= 1;
            }
            else{
                $data['msg']= "Something Went Wrong";
                $data['status']= 0;
            }
        }
         else{
              $data['msg']= "Invalid Request!! Msg validation Failed!!!";
            }
             return $this->return_data_in_json($data);
    }
    public function EnquiryFollowup(Request $request)
    {
        if($request->enquiry_id && $request->user_id){
            $enquiryfollowUp  =  new EnquiryController; 
            $update= $enquiryfollowUp->add_followup($request);
            if($update){
               $data['msg']= "Enquiry FollwUp Added   SuccessFully";
               $data['status']= 1;
            }
            else{
                $data['msg']= "Something Went Wrong";
                $data['status']= 0;
            }
        }
        else{
              $data['msg']= "Invalid Request!! Msg validation Failed!!!";
            }
             return $this->return_data_in_json($data);
    }

    public function deleteFollowup(Request $request)
    {
        if($request->id){
            
            $enquiryfollowUp  =  new EnquiryController; 
            $delete= $enquiryfollowUp->delete_followup($request->id);
            if($delete){
               $data['msg']= "Enquiry Deleted   SuccessFully";
               $data['status']= 1;
            }
            else{
                $data['msg']= "Something Went Wrong";
                $data['status']= 0;
            }
       }
        else{
              $data['msg']= "Invalid Request!! Msg validation Failed!!!";
            }
             return $this->return_data_in_json($data);

    }

    public function VisitorAdd(Request $request)
    {
      if($request->branch_id && $request->session_id && $request->user_id){
         $visitor  =  new VisitorController;
         $insert= $visitor->store($request);
            if($insert){
               $data['msg']= "Visitor Added  SuccessFully";
               $data['status']= 1;
            }
            else{
                $data['msg']= "Something Went Wrong";
                $data['status']= 0;
            } 
       }
      else{
           $data['msg']= "Invalid Request!! Msg validation Failed!!!";
          }
        return $this->return_data_in_json($data);
    }

    public function VisitorEdit(Request $request)
    {
        if($request->branch_id && $request->session_id && $request->user_id  && $request->id){
          $id= $request->id;
          $visitor  =  new VisitorController;
          $update= $visitor->edit($request,$id);
            if($update){
               $data['msg']= "Visitor Updated  SuccessFully";
               $data['status']= 1;
            }
            else{
                $data['msg']= "Something Went Wrong";
                $data['status']= 0;
            } 
       }
      else{
           $data['msg']= "Invalid Request!! Msg validation Failed!!!";
          }
        return $this->return_data_in_json($data);
    }
    public function visitorDelete(Request $request)
    {
        if($request->id){ 
            $id=$request->id;
            $visitor  =  new VisitorController;
            $delete= $visitor->delete($id,$request);
            if($delete){
               $data['msg']= "Visitor Deleted  SuccessFully";
               $data['status']= 1;
            }
            else{
                $data['msg']= "Something Went Wrong";
                $data['status']= 0;
            } 
        }
        else{
           $data['msg']= "Invalid Request!! Msg validation Failed!!!";
          }
        return $this->return_data_in_json($data);
    }


    /* call log api*/
    public function callLogAdd(Request $request)
    { 
         if($request->branch_id && $request->session_id){
           if($request->user_id){
             $request->request->add(['created_by'=>$request->user_id]);
           }
           $request->request->add(['created_at'=>Carbon::now()]);
           $request->request->add(['record_status'=>1]);
           
           $insert= CallLog::create($request->all());
           if($request->date){
              $row=FollowUpHistory::insert([
                  'created_at'=> $request->created_at,
                  'created_by'=>$request->created_by,
                  'call_duration'=>$request->call_duration,
                  'date'=>$request->date,
                  'next_follow_up'=>$request->follow_up_date,
                  'call_log_id'=>$insert->id,
                  'record_status'=>1,
                  'follow_up_status'=>1,
                  'note'=>'FIRST CALL'
              ]);
            }
            if($insert){
               $data['msg']= "CallLog Added  SuccessFully";
               $data['status']= 1;
            }
            else{
                $data['msg']= "Something Went Wrong";
                $data['status']= 0;
            }
         }
        else{
           $data['msg']= "Invalid Request!! Msg validation Failed!!!";
          }
        return $this->return_data_in_json($data);
    }
      public function callLogEdit(Request $request)
    {
          if($request->user_id){
            $request->request->add(['updated_by'=>$request->user_id]);
           }
            $request->request->add(['updated_at'=>Carbon::now()]);
            $list= CallLog::where('record_status',1)->find($request->id);

            if($list){
              $update= $list->update($request->all());
              if($update){
                 $data['msg']= "Call Log Updated  Successfully";
                 $data['status']= 1;
              }
              else{
                  $data['msg']= "Something Went Wrong";
                  $data['status']= 0;
              } 
            }
            else{
                  $data['msg']= "No Data Found!!!";
                  $data['status']= 0;
            }
            
     
        return $this->return_data_in_json($data);
    }
    public function callLogDelete(Request $request)
    {
         $list= CallLog::where('record_status',1)->find($request->id);

         if($list){
           $delete= CallLog::where('id',$request->id)->update(['record_status'=>0]);
           if($delete){
               $data['msg']= "Call Log Deleted  Successfully";
               $data['status']= 1;
            }
            else{
                $data['msg']= "Something Went Wrong";
                $data['status']= 0;
            } 
         }
         else{
              $data['msg']= "No Data Found!!!";
              $data['status']= 0;
            }
        
        return $this->return_data_in_json($data);
    }
    /* call log api*/

    /*complaint api*/
    public function compliantAdd(Request $request)
    {
       if($request->branch_id && $request->session_id){
          if($request->user_id){
           $request->request->add(['created_by'=>$request->user_id]);
          }
          $request->request->add(['record_status'=>1]);
          $request->request->add(['complain_status'=>1]);
          $request->request->add(['created_at'=>Carbon::now()]);
          $insert=  Complain::create($request->all());
            if($insert){
               $data['msg']= "Complaint Added  SuccessFully";
               $data['status']= 1;
            }
            else{
                $data['msg']= "Something Went Wrong";
                $data['status']= 0;
            } 
       }
       else{
           $data['msg']= "Invalid Request!! Msg validation Failed!!!";
          }
        return $this->return_data_in_json($data);
    }
      public function compliantEdit(Request $request){
          
         if($request->user_id){
           $request->request->add(['updated_by'=>$request->user_id]);
          }
          $request->request->add(['updated_at'=>Carbon::now()]);
           $list= Complain::where('record_status',1)->find($request->id);

          if($list){
            $update=$list->update($request->all());
            if($update){
               $data['msg']= "Complaint Updated  SuccessFully";
               $data['status']= 1;
            }
            else{
                $data['msg']= "Something Went Wrong";
                $data['status']= 0;
            } 
          }
          else{
            $data['status']=0;
            $data['msg']= " No Data Found !!!";
          }
         
            
      
        return $this->return_data_in_json($data);
    }
    public function compliantDelete(Request $request)
    {
        if($request->id){ 
            $list= Complain::where('record_status',1)->find($request->id);
            if($list){
             $update=Complain::where('id',$request->id)->update(['record_status'=>0]);
            if($update){
               $data['msg']= "Complaint Deleted  SuccessFully";
               $data['status']= 1;
            }
            else{
                $data['msg']= "Something Went Wrong";
                $data['status']= 0;
            } 
          }
          else{
            $data['status']=0;
            $data['msg']= " No Data Found !!!";
          }
        }
        else{
           $data['msg']= "Invalid Request!! Msg validation Failed!!!";
          }
        return $this->return_data_in_json($data);
    }
    public function compliantStatusUpdate(Request $request)
    {
        
           $list= Complain::where('record_status',1)->find($request->id);
           if($list){
              if($request->user_id){
                $request->request->add(['updated_by'=>$request->user_id]);
                $request->request->add(['complain_status'=>$request->status]);
              }
              $request->request->add(['updated_at'=>Carbon::now()]);
              $status= $list->update($request->all());
              if($status){
                 $data['msg']= "Complaint Status Updated  SuccessFully";
                 $data['status']= 1;
              }
              else{
                  $data['msg']= "Something Went Wrong";
                  $data['status']= 0;
              } 
           }
           else{
            $data['status']=0;
            $data['msg']= 'No data Found !!!';
           }

           
      
         
        return $this->return_data_in_json($data);
    }

    /*complaint api*/
    /* 14-09-2021*/
    public function postalDispatch(Request $request)
    {
      
      if($request->branch_id && $request->session_id){
         if($request->user_id){
          $request->request->add(['created_by'=>$request->user_id]);
         }
         
         $request->request->add(['created_at'=>Carbon::now()]);
         $request->request->add(['record_status'=>1]);

          $insert= Postal::create($request->all());

          if($insert){
            $data['status']= 1;
            $data['msg']= "Dispatch Postal Added";

          }
          else{
            $data['status']= 0;
            $data['msg']= "Something Wrong";
          }
       }
       else{
           $data['msg']= "Invalid Request!! Msg validation Failed!!!";
          }
        return $this->return_data_in_json($data);
    }
    public function postalReceive(Request $request)
    {
      if($request->branch_id && $request->session_id){
         if($request->user_id){
          $request->request->add(['created_by'=>$request->user_id]);
         }
         $request->request->add(['created_at'=>Carbon::now()]);
         $request->request->add(['record_status'=>1]);
         
         $insert= Postal::create($request->all());
     
          if($insert){
            $data['status']= 1;
            $data['msg']= "Receive Postal Added";

          }
          else{
            $data['status']= 0;
            $data['msg']= "Something Wrong";
          }
       }
       else{
           $data['msg']= "Invalid Request!! Msg validation Failed!!!";
          }
        return $this->return_data_in_json($data);
    }
    public function postalEdit(Request $request)
    {
      
        if($request->user_id){
          $request->request->add(['updated_by'=>$request->user_id]);
         }
         $request->request->add(['updated_at'=>Carbon::now()]);
          $list=Postal::where('record_status',1)->find($request->id);
         
          $update=$list->update($request->all());
          if($update){
            $data['status']= 1;
            $data['msg']= "Record updated.";

          }
          else{
            $data['status']= 0;
            $data['msg']= "Something Wrong";
          }
      
        return $this->return_data_in_json($data);
    }

    public function postalDelete(Request $request)
    {
        $list=Postal::where('record_status',1)->find($request->id);
        if($list){
          $delete= Postal::where('id',$request->id)->update(['record_status'=>0]);
          if($delete){
            $data['status']= 1;
            $data['msg']= "Record Deleted Successfully!!!";
          }
          else{
            $data['status']= 0;
            $data['msg']= "Something Wrong";
          }
        }
        else{
          $data['status']= 0;
          $data['msg']= "No data Found!!!";
        }
         return $this->return_data_in_json($data);
    }
    public function Admision(Request $request)
    {
       if($request->branch_id && $request->session_id){
          if($request->user_id){
            $request->request->add(['created_by'=>$request->user_id]);
            $request->request->add(['org_id'=>1]);
          }
           $admission =Admission::create($request->all());
           
           if($admission){
            $data['status']= 1;
            $data['admission_id']= $admission->id;
            $data['msg']= "Record Deleted Successfully!!!";
          }
          else{
            $data['status']= 0;
            $data['msg']= "Something Wrong";
          }
       }
       else{
         $data['msg']= "Invalid Request!! Msg validation Failed!!!";
       }
        return $this->return_data_in_json($data);
     
    }

    public function AdmisionEdit(Request $request)
    {
      if($request->user_id){
           $request->request->add(['updated_by'=>$request->user_id]);
         }
         //dd($request->all());
         $list=Admission::find($request->id);
         if($list){
           $update=  $list->update($request->all());
           if($update){
            $data['status']= 1;
            $data['msg']= "Record Updated Successfully!!!";
           }
           else{
             $data['status']= 0;
             $data['msg']= "Something Wrong!!!";
           }
         }
         else{
          $data['status']= 0;
          $data['msg']= 'No data Found!!!';
         }
         return $this->return_data_in_json($data);
    }
    /* 14-09-2021*/

    /*20-09-2021*/
    public function AddCalResponse(Request $request)
    {
      if($request->call_log_id){
         if($request->user_id){
           $request->request->add(['created_by'=>$request->user_id]);
         }
         $request->request->add(['created_at'=>Carbon::now()]);
         $request->request->add(['record_status'=>1]);
         $request->request->add(['follow_up_status'=>1]);
        
         $insert=FollowUpHistory::create($request->all());
         if($insert){
           $data['status']=1;
           $data['msg']='Next Follow Up Date Added Successfully!!!';
         }
         else{
           $data['status']=0;
           $data['msg']='Something Went Wrong!!!';
         }
      }
     else{
         $data['status']=0;
         $data['msg']= "Invalid Request!! Msg validation Failed!!!";
       }
        return $this->return_data_in_json($data);
    }

    public function callFollowUpList(Request $request)
    {
     if($request->call_log_id){
         $data['call_follow_up_list']= FollowUpHistory::select('follow_up_history.id','follow_up_history.call_log_id','follow_up_history.date','follow_up_history.note','follow_up_history.response','follow_up_history.call_duration','follow_up_history.next_follow_up','follow_up_history.follow_up_status','call_logs.name')
      ->leftjoin('call_logs','follow_up_history.call_log_id','=','call_logs.id')
      ->where('follow_up_history.record_status',1)
      ->where('follow_up_history.call_log_id',$request->call_log_id)->get();
      
     }
     else{
         $data['msg']= "Invalid Request Msg Validation Failed!!!";
     }
     return $this->return_data_in_json($data);
    }
    public function CalResponseEdit(Request $request)
    {
       if($request->user_id){
           $request->request->add(['updated_by'=>$request->user_id]);
         }
         $request->request->add(['updated_at'=>Carbon::now()]);
         //dd($request->all());
         $list=FollowUpHistory::find($request->id);
         if($list){
           $update=  $list->update($request->all());
           if($update){
            $data['status']= 1;
            $data['msg']= "Follow Up History Updated!!!";
           }
           else{
             $data['status']= 0;
             $data['msg']= "Something Wrong!!!";
           }
         }
         else{
          $data['status']= 0;
          $data['msg']= 'No data Found!!!';
         }
         return $this->return_data_in_json($data);
    }

    public function CalResponseDelete(Request $request)
    {
       $list=FollowUpHistory::where('record_status',1)->find($request->id);
        if($list){
          $delete= FollowUpHistory::where('id',$request->id)->update(['record_status'=>0]);
          if($delete){
            $data['status']= 1;
            $data['msg']= "Call log Deleted!!!";
          }
          else{
            $data['status']= 0;
            $data['msg']= "Something Wrong";
          }
        }
        else{
          $data['status']= 0;
          $data['msg']= "No data Found!!!";
        }
         return $this->return_data_in_json($data);
    }
    public function CalResponseStatusUpdate(Request $request)
    {
       $list= FollowUpHistory::where('record_status',1)->find($request->id);
       if($list){
          if($request->user_id){
            $request->request->add(['updated_by'=>$request->user_id]);
          }
          $request->request->add(['updated_at'=>Carbon::now()]);
    
          $status= FollowUpHistory::where('id',$request->id)->update(['follow_up_status'=>$request->status]);

          if($status){
             $data['msg']= "Status Changed";
             $data['status']= 1;
          }
          else{
              $data['msg']= "Something Went Wrong";
              $data['status']= 0;
          } 
       }
       else{
        $data['status']=0;
        $data['msg']= 'No data Found !!!';
       }  
        return $this->return_data_in_json($data);
    }
    /*20-09-2021*/
   




       public function EnquiryFollowupList(Request $request)
        {
            
        Log::debug("Enquiry Follow Up List");
        Log::debug($request->all());
         if($request->id){
    
            $data['data']= DB::table('enquiry_followup')->select('followup_date','next_followup','response','note','enquiry_id','first_name','enq_date','enquiry_followup.id as followup_id')
            ->leftjoin('enquiries','enquiries.id','=','enquiry_followup.enquiry_id')
            ->where('enquiry_followup.record_status',1)
            ->get();
      
            }    
        else
            {
            $data['msg']= "Invalid Request Msg Validation Failed!!!";
            }
     return $this->return_data_in_json($data);
    }
 
    
 




        
}



