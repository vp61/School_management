
@if(isset($term))
    @if(count($term) == 1)
      <?php 
        $report= "Half Yearly Report Card";
       

       ?>
    @elseif(count($term) == 2)
      <?php 
        $report= "Annual Report Card";

       ?>
    @else
       <?php 
        $report= "Report Card";

       ?>
    @endif
@endif

<br>
<div class="row">
    <div class="receipt-header ">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="row border_color no_border_bottom">
                <div class="pull-left col-xs-2">
                    <img class="img-responsive logo" alt="" src="{{ asset('images/logo/')}}/{{$branch->branch_logo}}" style=" float: left;margin-top:5px;">
               </div>
                <div class="text-center col-xs-8" >
                   <h3 class="text-center uppercase font-emoji"><b>{{$branch->branch_name}}</b></h3>
                    <h5  class="text-center font-emoji">LEARN TODAY LEAD TOMORROW</h5>
                    <h6 class="orange text-center">Affilated to CBSE,New Delhi</h6>
                    <p><b class="red border_color text-center" style="font-size:13px;">Affiliation No-2133461</b></p>
                  
                   
                
                
                    
                       
                </div>
                 <div class="pull-right col-xs-2">
                    <img class="img-responsive logo" alt="" src="{{ asset('images/logo/')}}/{{$branch->branch_logo}}" style=" float: right;margin-top:5px;">
                </div>
            </div>


        </div>
        </div>
         @if($student)
        
        <div class="col-xs-12 col-sm-12 col-md-12 name_fields">
            <div class="row border_color" style="border-top:solid 2px gray!important;" >
                <div class="col-xs-12 col-md-12 col-sm-12">
                    <div class="text-center">
                        <b>Academic Session :{{$session->session_name}}</b>
                       <br>
                       
                        <b class=''>Report Card for {{$student->course}}</b>
                        <br>
                        
                           
                    </div>
                </div>
                <div class="col-sm-9 col-xs-9 col-md-9">

                    <table class="table no_border_table student_table" style="">
                         <tr>
                            <td class="font_size_td"><span class="strong">Roll No:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> <span></span></td>
                        </tr>
                        <tr>
                            <td class="font_size_td"><span class="strong">Student's Name:&nbsp;&nbsp;&nbsp;</span> <span class="uppercase"> {{$student->first_name}}</span></td>
                            
                        </tr>
                       
                        <tr>
                              <td class="font_size_td"><span class="strong"> Mother's/Father's/Guardian's Name:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class="uppercase"> {{$student->mother_first_name}} / {{$student->father_first_name}}</span></td>
                            
                        </tr>
                         <tr>
                              <td class="font_size_td"><span class="strong">D.O.B.:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class="uppercase"> {{\Carbon\Carbon::parse($student->date_of_birth)->format('d-M-Y')}}</span></td>
                          
                        </tr>
                        <tr>
                            <td class="font_size_td"><span class="strong">{{env('course_label')}}/Section:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> <span> {{$student->course}}&nbsp;/&nbsp;{{$student->semester}}</span></td>
                        </tr>
                        <!-- <tr>
                              <td class="strong uppercase font_size_td border_color">Rubrics:</td>
                        </tr> -->
                        
                    </table>
                </div>
                
                <div class="col-sm-3 col-xs-3 col-md-3" style="float:right">
                   
                              
                    <table class="table no_border_table student_table table">
                         <tr>
                            <td  rowspan="6"> 
                                @if($student->student_image!='')
                               <img src="{{ asset('images/studentProfile/'.$student->student_image) }}" class="img-responsive" style="height: 114px; margin-top:-13px;">
                               @else
                               <img src="{{ asset('assets/images/avatars/profile-pic.jpg') }}" class="img-responsive">
                               @endif</td>
                        </tr>
                       
                    </table>
                           
                </div>
                 
                
            </div>
        </div> 
    @endif
           
        <div class="col-xs-12 col-sm-12 col-md-12 name_fields">
       
            <table class="table border_color">

                    <tr>
                        <td class="text-uppercase strong border_color font_size_td"  style="border-top:none!important;font-size: 11px;">For pen paper test:</td>
                        <td class="text-uppercas" style="font-size:9px;">TWo Weekly tests have conducted per term in the marks of 20 in all subjects having average & weightage of 10 marks</td>
                    </tr>
                     <tr>
                        <td class="text-uppercase strong border_color font_size_td"  style="border-top:0px!important;font-size: 11px;">For multiple assessment:</td>
                        <td class="text-uppercas" style="font-size:9px;">1. quizess(1 mark); 2. oral test(1 mark);3.concept map(1 mark);4.Exit cards(1 mark); visual expression(1 mark)</td>
                    </tr>
                     <tr>
                        <td class="text-uppercase strong border_color font_size_td"  style="border-top:0px!important;font-size: 11px;">For portfolio:</td>
                        <td class="text-uppercas" style="font-size:9px;">1. regularity(1 mark); 2.neatness(1 mark);3.maintainenance of notebook(1 mark);4.assignment completion(1 mark)</td>
                    </tr>
               
              </table>
          <table class="table  table-striped table-hover border_color">
                <tr>
                    <td></td>
                </tr>
            </table>
        </div> 
            
            <br>
            <div class="col-xs-12 col-sm-12 col-md-12 ">
                @if(isset($term))
                    <table class="table  table-striped table-hover border_color">
                        <?php
                            $rep_cont = new App\Http\Controllers\Exam\ExamReportController;
                        ?>
                        @if(isset($term))
                            @if(count($term)>0)
                                <?php 
                                 $remark='';
                                    //TERM NAME 
                                // dd($subject);
                                ?>
                                <tr>
                                    <th rowspan="3" class="strong border_color"  >Scholastic Area</th>
                                    @foreach($term as $t_name => $type)
                                        <?php

                                            $col_span = count($type);
                                            $t_name_arr = explode('-', $t_name);
                                            $t_name = $t_name_arr[0];
                                        ?>

                                        <th colspan="{{($col_span+2)}}" class="text-center border_color">
                                            {{$t_name}}
                                        </th>
                                    @endforeach
                                    
                                </tr>
                                <?php 
                                    //TERM NAME 
                                ?>
                                <tr class="strong">
                                    @foreach($term as $t_name => $type)
                                        <?php

                                       $col_span = count($type);
                                            
                                        ?>
                                    <td  colspan="{{$col_span}}" class="text-center border_color strong">Internal assesment</td>
                                   <td class="border_color strong"></td>
                                   <td class="border_color strong"></td>
                                           
                                    @endforeach
                                    
                                    
                                </tr>
                                <tr class="strong">
                                    @foreach($term as $t_name => $type)
                                        <?php
                                        $col_span = count($type);
                                            
                                        ?>
                                    <td   colspan="{{$col_span}}" class="text-center border_color strong">Periodic assesment</td>
                                    
                                      <td class="border_color strong" ></td>     
                                      <td class="border_color strong" ></td>     
                                    @endforeach
                                    
                                    
                                </tr>
                                <tr class="strong">
                                    <td class="border_color subject_font_size"> Subject</td>
                                    @foreach($term as $t_name => $type)

                                            @foreach($type as $type_id => $type_title)
                                                 <?php $paper= explode('==',$type_title);
                                                  
                                                  ?>
                                                <td class="border_color width_10">{{isset($paper)?$paper[0]:'-'}} ({{(isset($paper[1])?$paper[1]:'' )}} marks)</td>
                                                
                                                
                                                <?php
                                                    $col_total_max[$type_id] =0;
                                                    $col_total_obtained[$type_id] =0;
                                                ?>
                                            @endforeach
                                            <td class="border_color width_10">Marks Obtained</td>
                                            <td class="border_color width_10">Gr</td>
                                    @endforeach
                                    
                                    <!--<td>Highest Marks Obtained In Class</td>-->
                                </tr>
                            @endif
                        @endif
                        <?php
                            $temp_arr =[];
                            // dd($term,$subject);
                        ?>
                        <!-- main subject start -->
                        @if(isset($term) && isset($subject))
                            @if((count($term)>0) && (count($subject)>0))
                                
                                @foreach($subject as $sub_id => $term_arr)
                                    <?php
                                    
                                        $row_total = 0;
                                        
                                       
                                        $sub_name = explode('-', $sub_id);
                                    ?>
                                    <tr>
                                        <td class='strong border_color subject_font_size subject_width'>{{$sub_name[1]}}</td>
           
                                        @foreach($term_arr as $term_name => $type_arr) 
                                            <?php  $row_total_obtained = 0; ?>
                                            @foreach($term as $t_name => $term_type_arr)
                                                <?php
                                                    $t_name_arr = explode('-', $t_name);
                                                    $t_id = $t_name_arr[1]; //term id of term array

                                                    $t_name_arr = explode('-', $term_name);
                                                    $term_id = $t_name_arr[1]; //term id of subject array
                                                ?>
                                                @if($t_id == $term_id)

                                                    @foreach($type_arr as $type_id => $sub)
                                                        
                                                        @foreach($term_type_arr as $term_type_id => $type_name)
                                                            @if($term_type_id == $type_id)
                                                                <td class="border_color strong">
                                                                    
                                                                    @if($sub!='')
                                                                        <?php
                                                                            $last_exam_id = $sub->id;
                                                                            if($sub->attendance == 3 || $sub->attendance == 4){
                                                                               
                                                                            }else{
                                                                                 $row_total = $row_total +  $sub->max_mark;
                                                                            }
                                                                           
                                                                           $row_total_obtained = $row_total_obtained +  $sub->obtained_mark;
                                                                            /*quarter wise total max mark */
                                                                            if(isset($col_total_max[$term_type_id])){
                                                                                if($sub->attendance == 3 || $sub->attendance == 4){
                                                                                    
                                                                                }else{
                                                                                    $col_total_max[$term_type_id] = $col_total_max[$term_type_id] + $sub->max_mark;
                                                                                }  
                                                                            }
                                                                            /*quarter wise total obtained mark */
                                                                            if(isset($col_total_obtained[$term_type_id])){
                                                                                $col_total_obtained[$term_type_id] = $col_total_obtained[$term_type_id] + $sub->obtained_mark;
                                                                            }
                                                                           

                                                                        ?>
                                                                        @if($sub->attendance == 1)
                                                                            {{isset($sub->obtained_mark)?$sub->obtained_mark:'0'}}
                                                                        @elseif($sub->attendance == 3)
                                                                            <b class="red">ML</b>
                                                                         @elseif($sub->attendance == 4)
                                                                            <b class="red">NA</b>
                                                                        @else        
                                                                            <b class="red">AB</b>
                                                                        @endif
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </td>
                                                               
                                                            @endif
                                                        @endforeach

                                                    @endforeach
                                                @endif

                                            @endforeach
                                         <?php
                                            if($row_total){

                                                $percentage = ($row_total_obtained/$row_total)*100;
                                            }else{
                                                $percentage = 0;
                                            }
                                            // dd($percentage);
                                            $grade = $rep_cont->getGrade($last_exam_id,round($percentage));

                                        ?>
                                        <td class="border_color strong">{{$row_total_obtained}}</td>
                                        <td class="border_color strong">{{$grade}}</td>
                                        @endforeach
                                       
                                        <?php /*
                                        <td>
                                            <?php
                                                if($sub != ''){
                                                    echo $rep_cont->maxMarkByExamId($sub->id);
                                                }else{
                                                    echo '-';
                                                }
                                                
                                            ?>
                                        </td>
                                        */ ?>
                                    </tr>    
                                @endforeach
                                <tr class="strong">
                                    <?php
                                        $gt_max_mark = 0;
                                        $gt_obtained_mark = 0;
                                    ?>
                                    @foreach($term_arr as $term_name => $type_arr) 
                                        @foreach($type_arr as $type_id => $type)
                                            <?php
                                                $gt_max_mark = $gt_max_mark + $col_total_max[$type_id];
                                                $gt_obtained_mark = $gt_obtained_mark + $col_total_obtained[$type_id];

                                            ?>
                                        @endforeach
                                    @endforeach
                                </tr>
                            @endif
                        @endif
                        <!-- main subject end -->
                        <!-- optional subject -->
                        
                       @if(isset($optional_subject) && isset($term))
                                   
                            @foreach($optional_subject as $key=>$value)

                              <?php
                    
                                // dd($optional_subject);
                            $op_sub = explode('-', $key);
                            $op_sub_id= $op_sub[0];
                              

                             ?>
                            @if($op_sub_id!='')
                            <tr class="strong">
                                <td class="border_color subject_width">{{$op_sub[1]}}</td>
                                @foreach($term as $t_name => $type)
                                
                                   
                                    @foreach($value as $v_t_name => $op_exam_data)
                                       @php $mm = 0;$om = 0; @endphp
                                        @foreach($op_exam_data as $op_exam_id => $op_mark_data) 

                                        <?php
                                        
                                            $att = 0; 
                                                if($op_mark_data){
                                                    $att =  $op_mark_data->attendance; 
                                                     $om = $op_mark_data->obtained_mark; 



                                                 
                                               }
                                            
                                            
                                            if(!empty($op_mark_data)){
                                               
                                                if($t_name == $v_t_name){
      

                                                    $mm += $op_mark_data->max_mark;
                                                      
                                                 }   
                                                }
                                            

                                        ?>
                                        @if($t_name==$v_t_name)
                                         <td class="border_color width_10"></td>
                                         @endif
                                        @endforeach
                                      <?php
                                    try{

                                       $op_percent = ($om/$mm)*100;

                                        // dd($percentage);
                                        $op_grade = $rep_cont->getGrade($op_sub_id,round($op_percent));
                                        // dd($op_grade);
                                    ?> 
                                    <?php
                                    }Catch(\Throwable $e){
                                        
                                    }
                                    ?>  
                                    @if($t_name==$v_t_name)
                                    <td class="border_color strong"></td>
                                    <td class="border_color strong">{{$op_grade}}</td>
                                    @endif
                                     
                                    @endforeach
                                   
                                    
                                @endforeach
                            </tr>
                            @endif
                            @endforeach
                    @endif
                        <!-- optional sub end -->
              </table>
                   
                
                
                @endif
                <!-- lifeskills -->
                <table class="table  table-striped table-hover border_color">
                    <tr>
                        <td></td>
                    </tr>
                </table>
                 <table class="table  table-striped table-hover border_color">
                    <tr>
                        <td  class="uppercase text-center font-emoji" style="font-size: 20px;">Life skills</td>
                    </tr>
                    @if(isset($disc) && isset($term) && count($disc)>0)
                    
                </table>    
                    <!-- For Disc Heading -->
                    <div class="row">
                        @php $i=0; @endphp
                        @foreach($disc as $disc_parent=>$discplic_arr)

                            <?php
                                $disc_parent_title= explode('=',$disc_parent);
                                 $i++;

                               ?>
                                @if($i<4)
                                <div class="col-sm-4 col-xs-4" style="padding:0px">
                                    <table class="table table-striped">
                                       
                                        <tr>
                                            <td class="border_color strong">Subjects</td>
                                            @if(isset($term))
                                            <td class="border_color strong"  colspan="{{count($term)}}">Grades</td>
                                            @endif
                                        </tr>
                                        
                                        <tr>
                                       
                                            <td class="border_color strong" >{{isset($disc_parent_title[1])?$disc_parent_title[1]:''}}</td>
                                            @if(isset($term))
                                                @foreach($term as $term_id=>$term_val)
                                                   <?php
                                                    $disc_term= explode('-',$term_id);

                                                   ?>
                                                   <td class="border_color strong">{{isset($disc_term[0])?$disc_term[0]:''}}</td>

                                               @endforeach
                                            @endif
                                        </tr> 
                                        @foreach($discplic_arr as $disc_name => $disc_term_val)
                                       
                                            <tr>
                                                <td class="border_color strong">{{$disc_name}}</td>
                                                @foreach($term as $term_id=>$term_val)
                                                    @foreach($disc_term_val as $disv_term_name => $disc_term_grade)
                                                        @if($term_id==$disv_term_name)
                                                        <?php
                                                          if(isset($disc_term_grade->remark)){

                                                            $remark= $disc_term_grade->remark;
                                                          }
                                                        ?>
                                                            <td class="border_color strong">{{isset($disc_term_grade->disciplin_grade)?$disc_term_grade->disciplin_grade:''}}</td>
                                                        @endif
                                                    @endforeach
                                                @endforeach
                                            </tr>
                                        @endforeach 
                                       
                                    </table>
                                </div>
                                @else
                                <div class="col-sm-6 col-xs-6" style="padding:0px"> 
                                    <table class="table table-striped">
                                      
                                        @foreach($discplic_arr as $disc_name => $disc_term_val)
                                       
                                            <tr>
                                                <td class="border_color strong">{{$disc_name}}</td>

                                                @foreach($disc_term_val as $disv_term_name => $disc_term_grade)

                                                  <?php
                                                   $disc_val_print= isset($disc_term_grade->disciplin_grade)?$disc_term_grade->disciplin_grade:'';
                                                  ?>
                                                @endforeach
                                               
                                                    <td class="border_color strong">{{$disc_val_print}}</td>
                                              
                                            </tr>
                                        @endforeach 
                                    </table> 
                                </div>
                                @endif
                               
                        @endforeach
                    </div>


                    @endif
                
                <!-- lifeskills end -->
                <!-- healthstatus -->

                 
                <!-- healthstatus --><br><br>
               <table class="table no_border_table strong" style='font-family: sans-serif;'>
                    <tr class='strong'>
                        <td rowspan="2" width="15%" class='strong' >Class Teacher's Remark:</td>
                         <td  class="strong">{{$remark}}</td>
                    </tr>
                    
                    
                </table><br>
                <table class="table no_border_table strong" style='font-family: sans-serif;'>
                    <tr class="text-center strong">
                        <td class='strong' width="10%">Promoted to Class:</td>
                        <td class="border_bottom"></td>
                        <td></td>
                        <td></td>
                    </tr>
                    
                    <br>
                    <tr class="text-center strong" style='font-family: sans-serif;'>
                        
                        <td class='strong' width="10%">Place:</td>
                        <td class="border_bottom">&nbpp;</td>
                        <td class='strong'> Signature Of</td>
                      
                        <td class='strong'>Signature Of</td>
                        
                    </tr>
                     <tr class="text-center strong" style='font-family: sans-serif;'>
                        
                        <td class='strong' width="10%">Date:</td>
                        <td class="border_bottom">&nbsp;</td>
                        <td class='strong'>Class Teacher</td>
                      
                        <td class='strong'>Principal</td>
                        
                    </tr>
                </table>
               
            </div>   

        
    </div>   
</div>
    

@if(isset($term))
    @if(count($term) == 1)
        
        
    @endif
@endif