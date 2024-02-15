@extends('layouts.master')

@section('css')
  <style>
    .answer_div{
          padding: 10px 10px;
          background: white;
          /* color: white; */
          border: 1px solid black;
          border-radius: 3px;
      }
    }
  </style>
@endsection

@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                @include('layouts.includes.template_setting')
                <div class="page-header">
                    <h1>
                       Assessment
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            View Answer
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    <div class="col-xs-12 ">
                    
                    <!-- PAGE CONTENT BEGINS -->
                   
                        @include('includes.flash_messages')
                        @include('includes.validation_error_messages')
                         <div class="row">
                              <div class="col-sm-12 col-xs-12 col-md-12">  
                                   <table class="table table-striped table-bordered table-hover">
                                     <tr>
                                       <td colspan="4">
                                         <b style="font-family: sans-serif;">STUDENT DETAIL :</b>
                                       </td>
                                     </tr>
                                     <tr>
                                         <td class="label-info white">Name</td>
                                         <td>{{ $student->student_name }}</td>
                                         <td class="label-info white">Reg No</td>
                                         <td>{{ $student->reg_no }}</td>
                                     </tr>
                                     <tr> 
                                         <td class="label-info white">Father Name</td>
                                         <td colspan="3">{{ $student->father_name }}</td>
                                     </tr>
                                   </table>
                              </div>  
                         </div>
                         <hr>
                         <div class="row">     
                              <div class="col-sm-12 col-xs-12 col-md-12">  
                                   <table class="table table-striped table-bordered table-hover">
                                        <tr>
                                          <td colspan="4">
                                            <b style="font-family: sans-serif;">EXAM DETAILS :</b>
                                          </td>
                                        </tr>
                                        <tr>
                                              <td class="label-info white">Exam Title</td>
                                              <td>{{ $exam->title }}</td>
                                              <td class="label-info white">Subject</td>
                                              <td>{{ $exam->subject }}</td>
                                        </tr>
                                        <tr> 
                                             <td class="label-info white">{{env('course_label')}}</td>
                                             <td>{{ $exam->faculty }} ({{ $exam->section }})</td>
                                             <td class="label-info white">Date/Duration</td>
                                             <td>{{\Carbon\Carbon::parse($exam->date)->format('d-M-Y')}} ( {{ \Carbon\Carbon::parse($exam->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($exam->end_time)->format('h:i A') }} )</td>
                                        </tr>
                                        <tr> 
                                             <td class="label-info white">Maximum Mark</td>
                                             <td>{{ $exam->max_mark }}</td>
                                             <td class="label-info white">Passing Mark</td>
                                             <td>{{ $exam->pass_mark }}</td>
                                        </tr>
                                        <tr> 
                                             <td class="label-info white">Description</td>
                                             <td colspan="3">{{ $exam->description }}</td>
                                        </tr>
                                   </table>  
                              </div>
                        </div>
                        <div class="row">
                             <div class="col-sm-12 co-xs-12 col-md-12">
                              @php($i=1)
                                {!!Form::open(['route'=>$base_route.'.store_student_mark','method'=>'POST','class'=>'form-horizontal'])!!}
                                  @if(count($answer)>0)
                                    <div style="border: 1px solid #ddd9d9;padding: 20px 20px;background: #f9f9f9;box-shadow: -3px 0px 9px 0px;margin: 10px 0px 10px 0px;text-align: center;">
                                      <h2 class="large  blue">ANSWER SHEET</h2>
                                  </div>
                                  {!!Form::hidden('student_id',$student->student_id)!!}
                                  {!!Form::hidden('exam_id',$exam->id)!!}
                                    @foreach($answer as $key => $val)
                                          @if($val)
                                               <div style="border: 1px solid #ddd9d9;padding: 20px 20px;background: #f9f9f9;box-shadow: -3px 0px 9px 0px;margin: 10px 0px 10px 0px;">
                                                    <div class="row">
                                                         <div class="col-sm-11 col-xs-10 col-md-11 ">
                                                              <b>{{'Q.No -'.$i.'. '}} {{$val->question_title}} </b>
                                                         </div>
                                                         <div class="col-sm-1 col-xs-2 col-md-1 text-right">
                                                              <b>( {{$val->question_mark}} )</b>
                                                         </div>
                                                         <div class="col-sm-12 col-md-12 col-xs-12">
                                                              {{$val->question_description}}
                                                         </div>
                                                    </div>
                                                    <br>
                                                    <div class="row">
                                                         @switch($val->question_type)
                                                              @case(1)
                                                                <?php /* For INPUT TEXT */ ?>
                                                                   <div class="col-sm-12 col-xs-12 col-md-12 answer_div">
                                                                        <b>Answer : </b>
                                                                        {{$val->option_1_answer}}
                                                                   </div>
                                                                   @break
                                                                <?php /* For INPUT TEXT END */ ?>   
                                                              @case(2)
                                                                <?php /* For RADIO Button */ ?>
                                                                   @php($k=1)
                                                                   @for($j=1;$j<=6;$j++)
                                                                        <div class="col-sm-3 col-xs-6 col-md-3">
                                                                             @php($obj = 'option_'.$j)
                                                                             <?php
                                                                                  if($obj == $val->correct_answer){
                                                                                       $correct_answer_color = '#04bb04fc';
                                                                                       $correct_answer = 'Option '.$j;
                                                                                  }else{
                                                                                       $correct_answer_color ='';
                                                                                  }
                                                                             ?>
                                                                             <b style="color: {{$correct_answer_color}}">{{$val->$obj ? 'Option '.$k.'. '.$val->$obj:''}}</b>
                                                                        </div>
                                                                        @php($k++)
                                                                   @endfor
                                                                   <div class="col-sm-12 col-xs-12 col-md-12">
                                                                        <br>
                                                                       <b>Correct Option :</b> {{$correct_answer}}    
                                                                   </div>
                                                                  <div class="col-sm-12">
                                                                    <br>
                                                                  </div>
                                                                   <div class="col-sm-12 col-xs-12 col-md-12 answer_div">
                                                                        <?php 
                                                                          if($val->option_1_answer == $val->correct_answer){
                                                                               $given_answer_color = '#04bb04fc';
                                                                               // $correct_answer = 'Option '.$j;
                                                                          }else{
                                                                               $given_answer_color ='Red';
                                                                          }
                                                                        ?>          
                                                                       <b style="color:{{$given_answer_color}}">Given Option : {{str_replace('_',' ',ucfirst($val->option_1_answer))}}  </b>  
                                                                   </div>
                                                                <?php /* For RADIO Button End */ ?>   
                                                                  @break
                                                              @case(3)
                                                                  <?php /* For CheckBox  */ ?>
                                                                     @php($k=1)
                                                                     @for($j=1;$j<=6;$j++)
                                                                          <div class="col-sm-4 col-xs-6 col-md-4">
                                                                               @php($obj = 'option_'.$j)
                                                                               <b >{{$val->$obj ? 'Option '.$k.'. '.$val->$obj:''}}</b>
                                                                          </div>
                                                                          @php($k++)
                                                                     @endfor

                                                                     <?php $ans=[]; ?>
                                                                     @for($j=1;$j<=6;$j++)
                                                                        @php($obj = 'option_'.$j.'_answer')
                                                                        <?php 
                                                                            $given_ans = $val->$obj != null ? 'Option '.$j:''; 
                                                                             if($given_ans){
                                                                                $ans[] = $given_ans;  
                                                                             }
                                                                        ?>     
                                                                     @endfor
                                                                     <div class="col-sm-12">
                                                                      <br>
                                                                      </div>
                                                                     <div class="col-sm-12 col-xs-12 col-md-12 answer_div">
                                                                       <b>Give Answer: {{implode(',',$ans)}}</b>
                                                                     </div>
                                                                  @break   
                                                                  <?php /* For CHECKBOX END */ ?>
                                                              @case(4)
                                                                <?php /* For DROPDOWN*/ ?>
                                                                  @php($k=1)
                                                                   @for($j=1;$j<=6;$j++)
                                                                        <div class="col-sm-4 col-xs-6 col-md-4">
                                                                             @php($obj = 'option_'.$j)
                                                                             <?php
                                                                                  if($obj == $val->correct_answer){
                                                                                       $correct_answer_color = '#04bb04fc';
                                                                                       $correct_answer = 'Option '.$j;
                                                                                  }else{
                                                                                       $correct_answer_color ='';
                                                                                  }
                                                                             ?>
                                                                             <b style="color: {{$correct_answer_color}}">{{$val->$obj ? 'Option '.$k.'. '.$val->$obj:''}}</b>
                                                                        </div>
                                                                        @php($k++)
                                                                   @endfor
                                                                   <div class="col-sm-12 col-xs-12 col-md-12">
                                                                        <br>
                                                                       <b>Correct Option :</b> {{$correct_answer}}   

                                                                   </div>
                                                                    <div class="col-sm-12">
                                                                      <br>
                                                                    </div>
                                                                   <div class="col-sm-12 col-xs-12 col-md-12 answer_div">
                                                                        
                                                                        <?php 
                                                                          if($val->option_1_answer == $val->correct_answer){
                                                                               $given_answer_color = '#04bb04fc';
                                                                               // $correct_answer = 'Option '.$j;
                                                                          }else{
                                                                               $given_answer_color ='Red';
                                                                          }
                                                                        ?>          
                                                                       <b style="color:{{$given_answer_color}}">Given Option : {{str_replace('_',' ',ucfirst($val->option_1_answer))}}  </b>  
                                                                   </div>
                                                                  @break
                                                                  <?php /* For DROPDOWN End*/ ?>
                                                              @case(5)
                                                                  <?php /* For Date */ ?>
                                                                   <div class="col-sm-12 col-xs-12 col-md-12 answer_div">
                                                                        <b>Answer : </b>
                                                                        {{$val->option_1_answer}}
                                                                   </div>
                                                                   @break
                                                                <?php /* For Date */ ?>     
                                                              @default
                                                                   
                                                         @endswitch               
                                                    </div>
                                                    <br>
                                                    <div class="row">
                                                         <div class="form-group">
                                                              <label class="col-sm-2 col-xs-3 col-md-2"><b>Obtained Mark:</b></label>
                                                              <div class="col-sm-2 col-xs-2 col-md-2">
                                                                   <input type="number" name="obtained_mark[{{$val->answer_id}}]" class="form-control" value="{{$val->obtained_mark_on_answer?$val->obtained_mark_on_answer:0}}" placeholder="Enter Obtained Mark" required max="{{$val->question_mark}}">
                                                              </div>
                                                         </div>
                                                    </div>
                                               </div> 
                                               @php($i++)
                                          @endif    
                                    @endforeach
                                    <div style="border: 1px solid #ddd9d9;padding: 20px 20px;background: #f9f9f9;box-shadow: -3px 0px 9px 0px;margin: 10px 0px 10px 0px;text-align: right;">
                                      <button class="btn btn-success"> Save Exam</button>
                                  </div>
                                  @else
                                    <div style="border: 1px solid #ddd9d9;padding: 20px 20px;background: #f9f9f9;box-shadow: -3px 0px 9px 0px;margin: 10px 0px 10px 0px;text-align: center;">
                                      <h2 class="large  red">No Answer Found</h2>
                                  </div>
                                  @endif
                                {!!Form::close()!!}  
                             </div>
                        </div>
                        
                       
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
@endsection


@section('js')
 @include('includes.scripts.dataTable_scripts')
 @include('includes.scripts.delete_confirm')
 <script >
  
  
     
 </script>
@endsection