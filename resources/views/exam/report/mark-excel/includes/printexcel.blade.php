@extends('layouts.master')
@section('css')
<style type="text/css">
    .nopad{
        padding: 0px;
    }
    .row{
        margin: 0px !important;
    }
    .strong{
        font-weight: 600;
        text-transform: uppercase;
    }
    .name_fields{
        font-size: 13px;
    }
    .border_bottom{
        border-bottom: 1px dotted black;
    }
    .no_border_table td{
        border-top: none !important;
    }
    .student_table td{
        padding: 1px !important;

    }
    .student_table{
        margin-bottom: 2px !important;
    }
    td{
        padding: 2px !important;
    }
    .no-pad{
        padding: 0px;
    }
</style>

@if(isset($term))
    @if(count($term) == 1)
      <style type="text/css"> 
       @page{
        /*size: A4;*/
        /*orientation: landscape;*/
        /*margin: 1mm 1mm 100mm 0mm !important;*/
        
      }
      </style>
    @endif
@endif
@endsection
@section('content')

<!--h4 class="label label-warning arrowed-in arrowed-right arrowed" >Fee Reciept
</h4-->


<div class="container-fluid" style="font-size: xx-small;padding: 0px">
    <div class="row" >
       <div class="col-xs-12"><button class="btn btn-info" type="submit" id="filter-btn" onclick="Export('student-result-excel')">
                            <i class="fa fa-download bigger-110"></i>
                            Excel Export
                            </button></div>  <br>
        <div class="receipt-main col-xs-12 col-sm-12 col-md-12 ">
          <div class="row"></div>
                <table  class="table-bordered table-striped table-hover" id="tblFeeHeadWiseExl">
                   <tr>
                    <!--  <td nowrap>{{isset($branch->branch_name)?$branch->branch_name:'-'}}</td> -->
                   </tr>
                   <tr>
                     <td nowrap>Class</td><td nowrap>{{isset($class->faculty)?$class->faculty:'-'}}</td>
                     <td nowrap>Sec</td><td nowrap>{{isset($section->semester)?$section->semester:'-'}}</td>
                   </tr>
                    <?php
                    $rep_cont = new App\Http\Controllers\Exam\ExamReportController;
                    ?>
                    @if(isset($main_sub_master))
           
                    <tr>
                       <th class="strong"></th>
                       <th class="strong"></th>
                       <th class="strong"></th>
                       <th class="strong"></th>
                       <th class="strong"></th>
                     
                       @foreach($main_sub_master as $sub_k=>$sub_val)

                          <?php 
                            foreach ($sub_val as $key => $value) {
                              $col_span= count($value);
                            }

                          // $col_span= count($sub_val);
                           $sub= explode('-',$sub_k);
                          ?>
                         
                         <th colspan="{{ $col_span + 1}}" class="text-center strong">
                                        {{$sub[1]}}
                         </th>
                       @endforeach
                       <?php
                          $cnt=0;
                        foreach ($op_sub_master as $key => $value) {
                          $cnt++;
                        }
                          
                        ?>
                         <?php
                          $dic_cnt= count($disciplin_master);
                          
                        ?>
                       <th colspan="5"></th>
                       <th  colspan="{{$cnt}}"></th>
                       <th colspan="{{$dic_cnt}}"></th>
                       <th colspan="3"></th>
                    </tr>           
                    <tr>
                       <th>Roll.No.</th>
                       <th>Name Of Student</th>
                       <th>Father Name</th>
                       <th>Dob</th>
                       <th>Attendance</th>
                        @foreach($main_sub_master as $sub_k => $sub_val)
                           <?php
                              $temp_total = 0;
                            ?>
                            @foreach($sub_val as $term_id => $term_arr)

                              @foreach( $term_arr as $type_id => $type_val)

                                <?php 
                                      $type_arr= explode('==',$type_val);
                                  ?>
                                <th  class="text-center" nowrap>
                                  {{isset($type_arr[0])?$type_arr[0]:''}}
                                  ( {{isset($type_arr[1])?$type_arr[1]:''}} )
                                    <?php
                                      $temp_total = $temp_total + (isset($type_arr[1])?$type_arr[1]:0)
                                    ?>
                                  </th>
                              @endforeach
                            @endforeach
                            
                            <th nowrap>Total ( {{$temp_total}} ) </th>
                            
                        @endforeach
                        <th>Grand Total</th>
                        <th>%</th>
                        <th>ML</th>
                        <th>Max Marks</th>
                        <th>Grade</th>
                         @foreach($op_sub_master as $sub_k=>$sub_v)
                        
                         <?php  $subname= explode('-',$sub_k);
                                ?>
                            <th  class="text-center" nowrap>{{isset($subname[1])?$subname[1]:'-'}} {{isset($subname[2])?$subname[2]:'-'}}</th>
                         @endforeach
                          @if(isset($disciplin_master))
                        @foreach($disciplin_master as $k=>$v)
                        <th>{{$v->title}}</th>
                        @endforeach
                      @endif
                      <th nowrap>Height</th>
                      <th nowrap>Weight</th>
                      <th nowrap>Swimming</th>
                    </tr>
                    <tbody>
                       @if(isset($student))
                         @foreach($student as $std_k=>$std_v)
                         <tr>
                          <td>{{$std_v->reg_no}}</td>
                          <td>{{$std_v->first_name}}</td>
                          <td>{{$std_v->father_first_name}}</td>
                          <td>{{$std_v->date_of_birth}}</td>
                          <td></td>
                          <?php 
                            $row_total = 0;
                            $row_max_mark = 0;
                            $row_ml = 0;
                          ?>
                          @if(isset($subject))
                            @if(is_array($subject))

                              @if(array_key_exists($std_v->id,$subject))
                                @foreach($subject[$std_v->id] as $sub_name => $sub_arr)
                                  <?php $subject_total = 0; ?>

                                   @foreach($sub_arr as $term_k=>$term_arr)
                                     @foreach($term_arr as $type_k=>$type_v)

                                      <?php
                                        $last_exam_id = $type_v->id;
                                        $show_mark = '0';
                                        $color = '';
                                        $row_max_mark += isset($type_v->max_mark)?$type_v->max_mark:'0';
                                      ?>
                                      @if(isset($type_v->attendance))

                                        @if($type_v->attendance == 1)
                                          @php($show_mark = $type_v->obtained_mark) 
                                        @elseif($type_v->attendance == 3)
                                          <?php
                                             $type_v->obtained_mark = 0;$show_mark = 'ML';$color='Blue'; 
                                             $row_max_mark -= $type_v->max_mark;
                                             $row_ml++;
                                          ?>
                                        @else
                                          <?php $type_v->obtained_mark = 0;$show_mark = 'AB';$color='Red'; ?>
                                        @endif

                                      @endif
                                     <td style='color: {{$color}}'>

                                      {{$show_mark}}</td>


                                     <?php 

                                        $subject_total = $subject_total + (isset($type_v->obtained_mark)?$type_v->obtained_mark:0); 
                                      ?>
                                     @endforeach
                                   @endforeach
                                   <td>{{$subject_total}}</td>
                                   <?php $row_total += $subject_total ?>
                                @endforeach
                              @else  
                                <td></td>
                                <td></td>
                              @endif
                            @else
                                <td></td>
                                <td></td>
                            @endif
                          @else
                              <td></td>
                              <td></td>
                          @endif

                           <td>{{$row_total}} / {{$row_max_mark}}</td>
                           <td>
                             <?php
                             try{
                                $per = ($row_total / $row_max_mark) * 100;
                                echo number_format($per,'2');
                              }catch(Throwable $e){

                             }
                             ?>
                             
                           </td>
                           <td>{{$row_ml}}</td>
                           <td>{{$row_max_mark}}</td>
                           <?php 
                           $grade = $rep_cont->getGrade($last_exam_id,round($per));

                            ?>
                           <td>{{$grade}}</td>
                               @if(isset($op_subject_master))
                                 @if(is_array($op_subject_master))
                                    @if(array_key_exists($std_v->id,$op_subject_master))
                                        @foreach($op_subject_master[$std_v->id] as $sub_name => $term_arr)
                                      
                                            @foreach($term_arr as $term_k=>$term_v)
                                              @foreach($term_v as $type_k => $type_v)

                                              @endforeach
                                              <td>
                                               @if($type_v->attendance == 1)
                                                <?php
        
                                                    if($type_v->grade != ''){
                                                        echo $type_v->grade;
                                                    }else{
                                                       echo $type_v->obtained_mark;
                                                    }
                                                ?>
      
                                            
                                                  @elseif($type_v->attendance == 3)
              
                                                      <b class="red">ML</b>
                                                  @else        
                                                      <b class="red">AB</b>
                                                  @endif  
                                               

                                              </td>
                                            @endforeach
                                            
                                        @endforeach
                                    @endif
                                 @endif
                              @endif
                              @if(isset($disc))
                                @if(is_array($disc))
                                    @if(array_key_exists($std_v->id,$disc))
                                        @foreach($disc[$std_v->id] as $disc_name => $term_arr)
                                           @foreach($term_arr as $disc_k=>$disc_v)
                                           @endforeach
                                           <td>{{isset($disc_v->disciplin_grade)?$disc_v->disciplin_grade:'-'}}</td>
                                        @endforeach

                                    @endif
                                @endif
                              @endif
                              <td>-</td>
                              <td>-</td>
                              <td>-</td>
                           </tr>
                         @endforeach
                        @endif
                    </tbody>

                @endif
        </table>
    </div>    
    </div>
    
</div>


 

  <script type="text/javascript">
        function Export(fileName="") {
          if(fileName==""){
            fileName = "Student-Result-Excel";
          }
            $("#tblFeeHeadWiseExl").table2excel({
                filename: fileName+".xls"
            });
        }
    </script>



    @endsection
 

@section('js')
   
@endsection