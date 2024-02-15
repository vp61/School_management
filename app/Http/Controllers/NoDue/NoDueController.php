<?php

namespace App\Http\Controllers\NoDue;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB,Session;
class NoDueController extends Controller
{
     public function nodue($student)
    {


        $sessiondata=DB::table('student_detail_sessionwise')->select('student_detail_sessionwise.student_id','student_detail_sessionwise.course_id','student_detail_sessionwise.session_id','session.session_name','faculties.faculty','students.first_name','students.reg_no','students.batch_id','parent_details.father_first_name','addressinfos.address','addressinfos.mobile_1','branches.branch_name','branches.id as branch_ids')
         ->where('student_id','=',$student)
         ->join('session','student_detail_sessionwise.session_id','=','session.id')
         ->join('students','students.id','=','student_detail_sessionwise.student_id')
         ->join('faculties','faculties.id','=','student_detail_sessionwise.course_id')
         // ->join('student_guardians','student_guardians.students_id','=','student_detail_sessionwise.student_id')
         ->join('parent_details','parent_details.students_id','=','student_detail_sessionwise.student_id')
         ->join('addressinfos','addressinfos.students_id','=','student_detail_sessionwise.student_id')
         ->join('branches','students.branch_id','=','branches.id')
         ->orderBy('session.session_name','asc')
        ->get();
        
        $c=count($sessiondata)-1;
        $faculty=DB::table('nodue_faculty')->select('nodue_faculty.faculty_name')
        ->where([
        	['branches_id','=',$sessiondata[$c]->branch_ids],
        	['nodue_faculty.status','=','1']
        ])
        
        ->get();
       		if(count($sessiondata)==0){
       			return redirect('student');
       		}

           foreach ($sessiondata as $key => $value) {
                $feepaid[]=DB::table('assign_fee')->select('session_id','assign_fee.id','fee_heads.fee_head_title','assign_fee.fee_head_id','assign_fee.times','assign_fee.fee_amount','session.session_name','collect_fee.amount_paid','faculties.faculty','collect_fee.discount')
                        ->where([
                            ['session_id','=',$value->session_id],
                            ['course_id','=',$value->course_id],
                            ['assign_fee.status','=',1],
                            ['collect_fee.student_id','=',$student]
                        ])
                        ->where(function($q)use($value){
                          if(Session::get('isCourseBatch')){
                              if($value->batch_id){
                                $q->where('assign_fee.batch_id',$value->batch_id);
                              }
                          }
                        })
                        ->join('fee_heads','assign_fee.fee_head_id','=','fee_heads.id')
                        ->join('session','session.id','=','assign_fee.session_id')
                        ->join('collect_fee','collect_fee.assign_fee_id','=','assign_fee.id')
                        ->join('faculties','assign_fee.course_id','=','faculties.id')
                        ->get();
                      
                 $assignfee[]=DB::table('assign_fee')->select('session_id','assign_fee.id','assign_fee.fee_head_id','assign_fee.times','assign_fee.fee_amount','fee_heads.fee_head_title','session.session_name')       
                    ->where([
                    		['student_id','=','0'],
                            ['session_id','=',$value->session_id],
                            ['course_id','=',$value->course_id],
                            ['assign_fee.status','=',1],
                           ])
                    ->where(function($q)use($value){
                          if(Session::get('isCourseBatch')){
                              if($value->batch_id){
                                $q->where('assign_fee.batch_id',$value->batch_id);
                              }
                          }
                        })
                    ->orwhere([['student_id','=',$student],
                            ['session_id','=',$value->session_id],
                            ['course_id','=',$value->course_id],['assign_fee.status','=',1]])
                      ->where(function($q)use($value){
                          if(Session::get('isCourseBatch')){
                              if($value->batch_id){
                                $q->where('assign_fee.batch_id',$value->batch_id);
                              }
                          }
                        })
                     ->join('fee_heads','assign_fee.fee_head_id','=','fee_heads.id')
                     ->join('session','assign_fee.session_id','=','session.id')
                     // ->leftjoin('fee_heads as fee','fee.id','=','assign_fee.fee_head_id')
                     //    ->leftjoin('fee_heads as sub_head','sub_head.id','=','fee.parent_id')
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
                  $sub_head=$val->fee_head_title;
            $heads[$sub_head][$val->session_name][]=array($val->id); 


                }
             }

            foreach ($assignfee as $key => $value) {
             
                foreach ($value as $key => $val) {
                  $sub_head=$val->fee_head_title;
                  $sum=0; 
                  $discount=0;
                  $disc=0;
                  $due=0;
                  $check[$sub_head][$val->id]=0;
                 	$x[$sub_head]=0;	
                 $ndisc=0;
                    foreach ($feepaid as $key => $data) {
                        foreach ($data as $key => $v) {
                            if($val->session_id==$v->session_id)
                            {
                                if($val->id==$v->id){


		                            if(isset($v->amount_paid)){ 
		                               	
		                                $sum=$sum+$v->amount_paid;
		                                if(!empty($v->discount) && isset($v->discount)){
		                                	
		                                     $discount=$discount+$v->discount;
		                                      
		                                    }		                                
		                            }
                                }
                            }    
                        }
                    }
                    $a[$sub_head][$val->times][$val->session_name]['discount'][]=$discount;  

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
                             }
                       }
                                  
                    } 

                    $tdue=$due+$tdue; 
                	 foreach ($feepaid as $key => $data) { 
                        
                        foreach ($data as $key => $v) {
                      	
                          

                            if($val->session_id==$v->session_id)
                            {

                                if(isset($fee1[$sub_head][$val->times][$val->session_name]['paid'])) {
                                	
                                	if($x[$sub_head]!=0){
                                	}
                                	else{	
                                		if(count($heads[$sub_head][$val->session_name])>1){
                                		
		               					$fee1[$sub_head][$val->times][$val->session_name]['paid']=$fee1[$sub_head][$val->times][$val->session_name]['paid']+$sum;
		               					}
		               				}
		                  		} 
                             else{
                                $fee1[$sub_head][$val->times][$v->session_name]=array('paid'=>$sum , 'discount'=>$discount,'due'=>$due,'fee_head'=>$sub_head);
                               } 
                              
//DISCOUNT

 							if(isset($fee1[$sub_head][$val->times][$val->session_name]['discount'])) {
                   				
                                	
                                	if($x[$sub_head]!=0){
                                	}
                                else{
                                        		
				               				$fee1[$sub_head][$val->times][$val->session_name]['discount']=$fee1[$sub_head][$val->times][$val->session_name]['discount'];
				    
		               			}
                        
		                  	} 
                             else{
                                $fee1[$sub_head][$val->times][$v->session_name]=array('paid'=>$sum , 'discount'=>$discount,'due'=>$due,'fee_head'=>$sub_head);
                               }
                              
                          	if(isset($fee1[$sub_head][$val->times][$val->session_name]['due'])) {
                                	
                                	
                               if($x[$sub_head]!=0){
                                	}
                               else{
                                	 if(count($heads[$sub_head][$val->session_name])>1 ){	
                                		if($v->session_id==$val->session_id){
                                		
                                			$z[$heads[$sub_head][$val->session_name][0][0]][]=$heads[$sub_head][$val->session_name][0][0];
                               
                                			
                                			if(count($z[$heads[$sub_head][$val->session_name][0][0]])!=1){		
                                			$abc[$sub_head][$val->times][$val->session_name]['due'][]=$due;
		                                		if($val->times=='Yearly')
		                                		{		
				               						$fee1[$sub_head][$val->times][$val->session_name]['due']=$fee1[$sub_head][$val->times][$val->session_name]['due']+$due;
				               						
				               						
				               					}
				               						elseif($val->times=='Semester 1'){
				               							
				               							$fee1[$sub_head][$val->times][$val->session_name]['due']=$fee1[$sub_head][$val->times][$val->session_name]['due']+$due;
				               						}
				               						else{
				               							if(isset($abc[$sub_head]['Semester 2'][$val->session_name]['due'])){
				               								$temp=0;
				               								for($i=0;$i<count($abc[$sub_head]['Semester 2'][$val->session_name]['due']);$i++){
				               									$temp=$temp+$abc[$sub_head]['Semester 2'][$val->session_name]['due'][$i];

				               								}
				               							
				               								$fee1[$sub_head][$val->times][$val->session_name]['due']=$temp;
				               							
				               							}

				               							
				               						}


				               						
		               						}
		               					}
		               				  }	
		               				
		               				}
		                  		} 
		                  		else{
                                $fee1[$sub_head][$val->times][$v->session_name]=array('paid'=>$sum , 'discount'=>$discount,'due'=>$due,'fee_head'=>$sub_head);
                               } 
		                  		
                             $x[$sub_head]++;  
                  	}
                  }
                 } 
                   
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
    // dd($fee1);

          return view('student/NoDue/report',compact('sessiondata','assignfeeid','fee1','gt','tdue','faculty'));
    }
}
