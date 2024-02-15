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
<style>
    .subject_font_size{
        font-size: 8px!important;
    }
     .gt_font_size{
        font-size: 10px!important;
    }
    .student_detail{
        font-size: 10px!important;
    }
</style>
@if(Session::get('activeBranch') == 2)
<div class="row">
    <div class="receipt-header">
        <div class="col-xs-12 col-sm-12 col-md-12 ">
            <div class="pull-left">
                <img class="img-responsive" alt="" src="{{ asset('images/logo/')}}/main.png" style="width: 62PX;text-align: left;padding-top: 20px;">
            </div>
            <div class="text-center">
               <h3><b>{{$branch->branch_name}}</b></h3>
                
                <b><u>ACADEMIC SESSION {{$session->session_name}}</u></b>
               <br>
               
                <b><u class="uppercase">{{$report}}</u></b>
                <br>
                
                   
            </div>
            <div class="pull-right">
                <img class="img-responsive" alt="" src="{{ asset('images/logo/')}}/{{$branch->branch_logo}}" style="width: 62PX;text-align: left;position: absolute;right: 1%;top: 15px;">
            </div>
        </div>
    </div>   
</div>

@else
<div class="row">
    <div class="receipt-header">
        <div class="col-xs-12 col-sm-12 col-md-12 ">
            <div class="pull-left">
                <img class="img-responsive" alt="" src="{{ asset('images/logo/')}}/{{$branch->branch_logo}}" style="width: 62PX;text-align: left;">
            </div>
            <div class="text-center">
               <h3><b>{{$branch->branch_name}}</b></h3>
                
                <b><u>ACADEMIC SESSION {{$session->session_name}}</u></b>
               <br>
               
                <b><u class="uppercase">{{$report}}</u></b>
                <br>
                
                   
            </div>

        </div>
    </div>   
</div>
    
@endif
<br>
<div class="row">
    <div class="row">
        @if($student)
            <div class="col-xs-12 col-sm-12 col-md-12 name_fields">
            <div class="row border_color no_border_bottom student_detail" style="border-top:solid 1px #000;" >
                <div class="col-sm-8 col-xs-8 col-md-8">
                    <table class="table no_border_table student_table">
                        <tr>
                            <td ><span class="strong">Student's Name:&nbsp;&nbsp;&nbsp;</span> <span class="uppercase"> {{$student->first_name}}</span></td>
                            
                        </tr>
                       
                        <tr>
                              <td ><span class="strong">Father's Name:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class="uppercase"> {{$student->father_first_name}}</span></td>
                            
                        </tr>
                         <tr>
                              <td ><span class="strong">D.O.B.:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class="uppercase"> {{\Carbon\Carbon::parse($student->date_of_birth)->format('d-M-Y')}}</span></td>
                          
                        </tr>
                        
                    </table>
                </div>
                
                <div class="col-sm-4 col-xs-4 col-md-4">
                    <table class="table no_border_table student_table table">
                         <tr>
                            <td ><span class="strong">{{env('course_label')}}:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> <span> {{$student->course}}&nbsp;&nbsp;{{$student->semester}}</span></td>
                        </tr>
                        <tr>
                            <td ><span class="strong">Roll No:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> <span></span></td>
                        </tr>
                       
                        <tr>
                            <td ><span class="strong">Attendance:&nbsp;&nbsp;&nbsp;</span> <span></span></td> 
                        </tr>
                    </table>
                </div>
                
            </div>
        </div> 
         
        @endif
        
        
        <div class="col-xs-12 col-sm-12 col-md-12">
            <table class="table  table-striped table-hover cell_border">
                @if(isset($term))
                    @if(count($term)>0)
                        <?php 
                            //TERM NAME 
                        ?>
                        <tr>
                            <th rowspan="2" class="strong border_color"></th>
                            @foreach($term as $t_name => $type)
                                <?php
                                    $col_span = count($type);
                                    $t_name_arr = explode('-', $t_name);
                                    $t_name = $t_name_arr[1];
                                ?>

                                <th colspan="{{$col_span * 2}}" class="text-center border_color">
                                    {{$t_name}}
                                </th>
                            @endforeach
                            
                            <th class="border_color" rowspan="2"></th>
                        </tr>
                        <?php 
                            //TERM NAME 
                        ?>
                        <tr class="strong">
                            @foreach($term as $t_name => $type)
                                   
                                    @foreach($type as $type_id => $type_title)
                                        <?php $paper= explode('==',$type_title);
                                          
                                          ?>
                                        <th colspan="2" class="text-center border_color">{{isset($paper)?$paper[0]:'-'}}</th>
                                    @endforeach
                            @endforeach
                            
                            
                        </tr>
                        <tr class="strong">
                            <td class="border_color subject_font_size" style="vertical-align: middle;">Subject</td>
                            @foreach($term as $t_name => $type)
                                    
                                    @foreach($type as $type_id => $type_title)
                                        <?php $paper= explode('==',$type_title);
                                          
                                          ?>
                                        <td class="border_color"> Marks Obtained <br> ( out of {{isset($paper)?$paper[1]:'-'}} )</td>
                                        <td class="border_color">Highest Marks</td>
                                        
                                        <?php

                                            $col_total_max[$type_id] =0;
                                            $col_total_obtained[$type_id] =0;
                                        ?>
                                    @endforeach
                            @endforeach
                            <td class="border_color">Grade</td>
                           
                        </tr>
                    @endif
                @endif
                <?php
                    $rep_cont = new App\Http\Controllers\Exam\ExamReportController;
                    $temp_arr =[];
                ?>
                @if(isset($term) && isset($subject))
                    @if((count($term)>0) && (count($subject)>0))
                        
                        @foreach($subject as $sub_id => $term_arr)
                            <?php
                                $row_total = 0;
                                $row_total_obtained = 0;
                                $sub_name = explode('-', $sub_id);
                            ?>
                            <tr>
                                <td class='strong border_color subject_font_size' style='text-transform: none;'>{{$sub_name[1]}}</td>

                                @foreach($term_arr as $term_name => $type_arr) 

                                    @foreach($term as $t_name => $term_type_arr)
                                        <?php
                                            $t_name_arr = explode('-', $t_name);
                                            $t_id = $t_name_arr[0]; //term id of term array

                                            $t_name_arr = explode('-', $term_name);
                                            $term_id = $t_name_arr[0]; //term id of subject array
                                        ?>
                                        @if($t_id == $term_id)
                                            @foreach($type_arr as $type_id => $sub)
                                                
                                                @foreach($term_type_arr as $term_type_id => $type_name)
                                                    @if($term_type_id == $type_id)
                                                        <td class="border_color strong">
                                                            @if($sub!='')
                                                                <?php
                                                                    $last_exam_id = $sub->id;
                                                                    if($sub->attendance == 3 || $sub->attendance == 4 ){
                                                                        
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
                                                        <td class="border_color strong">
                                                            <?php
                                                                if($sub != ''){
                                                                    echo $rep_cont->maxMarkByExamId($sub->id);
                                                                }else{
                                                                    echo '-';
                                                                }
                                                                
                                                            ?>

                                                        </td>
                                                        
                                                    @endif
                                                @endforeach

                                            @endforeach
                                        @endif

                                    @endforeach

                                @endforeach
                                <?php
                                
                                    $percentage = ($row_total_obtained/$row_total)*100;
                                    // dd($percentage);
                                    $grade = $rep_cont->getGrade($last_exam_id,round($percentage));

                                    ?>
                                <td class="border_color strong">
                                 {{$grade}}
                                </td>
                               
                            </tr>    
                        @endforeach
                        <tr class="strong">
                            <td class="strong border_color gt_font_size">GRAND TOTAL</td>
                            <?php
                                $gt_max_mark = 0;
                                $gt_obtained_mark = 0;
                            ?>
                            @foreach($term_arr as $term_name => $type_arr) 
                                @foreach($type_arr as $type_id => $type)

                                    <td class="border_color strong subject_font_size">{{$col_total_obtained[$type_id]}}</td>
                                    <td class="border_color strong subject_font_size"></td>
                                    <?php
                                        $gt_max_mark = $gt_max_mark + $col_total_max[$type_id];
                                        $gt_obtained_mark = $gt_obtained_mark + $col_total_obtained[$type_id];

                                    ?>
                                  
                                @endforeach
                            @endforeach
                            <?php
                                
                                $percentage = ($gt_obtained_mark/$gt_max_mark)*100;
                                // dd($percentage);
                                $over_all_grade = $rep_cont->getGrade($last_exam_id,round($percentage));

                             ?>
                            <td class="border_color strong subject_font_size">{{$over_all_grade}}</td>
                        </tr>
                       
                        @if(isset($optional_subject))
                        @foreach($optional_subject as $key=>$term_arr)
                          
                          <?php
                
                            
                        $op_sub = explode('-', $key);
                         
                         ?>
                          <tr class="">
                            <td class="strong subject_font_size">{{$op_sub[1]}}</td>
                            
                            @foreach($term_arr as $term_name => $type_arr)
                                @foreach($type_arr as $type_id => $type)
                                
                                    
                                    <td class="strong"> 
                                        <?php
                                            $show_grade = 0;
                                            $show_max_mak = 0;
                                        ?>
                                    @if($type)    
                                        <?php
                                       
                                            
                                        
                                            if($type->obtained_mark  == 0.00 && $type->obtained_mark != null){
                                                $show_max_mak = 1;
                                            }
                                         
                                        ?>
                                        @if($type->attendance == 1)
                                            <?php
    
                                                if($type->grade != ''){
                                                    $show_grade = 1;
                                                    echo $type->grade;
                                                }else{
                                                   echo $type->obtained_mark;
                                                }
                                            ?>
    
                                          
                                        @elseif($type->attendance == 3)
    
                                            <b class="red">ML</b>
                                        @elseif($type->attendance == 4)
    
                                            <b class="red">NA</b>        
                                        @else        
                                            <b class="red">AB</b>
                                        @endif  
                                    @endif    
                                    </td>
                                    <td class='strong'><?php
                                            if($type != ''){
                                                 if($type->grade != ''){
                                                     echo '-';
                                                 }
                                                  else{
                                                    echo $rep_cont->maxMarkByExamId($type->id);
                                                  }
                                                
                                            }else{
                                                echo '-';
                                            }
                                            
                                        ?></td>
                                   
                                @endforeach
                            @endforeach
                            <td></td>
                           
                        </tr>
                        @endforeach
                        @endif
                       
                        @foreach($disc as $disc_key=>$disc_val)
                        <?php $col_span=count($disc_val);?>
                        <tr>
                        <td class="strong subject_font_size" >{{$disc_key}}</td> 
                        @foreach($term_arr as $term_name => $type_arr)

                        <?php
                            $col_span = count($type_arr);
                            $t_name = explode('-', $term_name);
                            $term_name= $t_name[1];

                         ?>     
                        @foreach($disc_val as $disc_k=>$disc_v)
                         @if($term_name==$disc_v->term_name)
                        <td colspan="{{$col_span *3}}" class="strong">{{$disc_v->disciplin_grade}}</td>
                         @endif
                        @endforeach
                        @endforeach
                         
                        </tr>
                        @endforeach
                       <tr>
                            <td class="border_color strong uppercase subject_font_size">Date Of Issue</td>
                            @foreach($term_arr as $term_name => $type_arr) 

                                    <?php
                                        $temp_col_span = count($type_arr);
                                        $col_span += $temp_col_span * 2
                                    ?>

                                    
                                
                            @endforeach
                            <td colspan="{{$col_span * count($term_arr)}}"></td>
                    
                        </tr> 
                        <tr>
                            <td class="border_color uppercase subject_font_size strong">Height/Weight</td>

                            <td colspan="{{$col_span * count($term_arr)}}">-</td>
                        </tr> 
                        
                        
                    @endif
                @endif
            </table>
            <?php /*
            <table class='table  table-striped table-hover'>
                    <tr class="strong">
                        @foreach($term as $t_name => $type)
                            <?php
                                $col_span = count($type);
                                $t_name_arr = explode('-', $t_name);
                                $t_name = $t_name_arr[1];
                            ?>

                            
                        @endforeach
                        <td class="border_color subject_font_size">
                                Date
                            </td>
                        <td colspan='5' class='border_color strong uppercase'></td>
                    </tr>
                    <tr>    
                        <td class='border_color strong uppercase width_10'>Height/Weight</td>
                        
                        <td colspan='5' class='border_color strong uppercase'>-</td>
                        
                    </tr>
                   
            </table>
            
            */ ?>
            <br>
            <table class="table no_border_table strong">
                <tr>
                    <td width="20%">Class Teacher's Remark</td>
                    <td  class="border_bottom"></td>
                </tr>
            
                
            </table>
             <table class="table no_border_table strong">
                <tr class="text-center">
                    <td class="border_bottom"></td>
                    <td></td>
                    <td class="border_bottom"></td>
                    <td></td>
                    <td class="border_bottom"></td>
                </tr>
                <br>
                <br>
                <br>
                <tr class="text-center">
                    
                    <td>Class Teacher's Signature</td>
                    <td></td>
                    <td>Parent's Signature</td>
                    <td></td>
                    <td>Principal's Signature</td>
                    <td></td>
                </tr>
            </table>
        </div>    
    </div> 
    
</div>