@extends('layouts.master')

@section('css')
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
                            Add
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    <div class="col-xs-12 ">
                    
                    <!-- PAGE CONTENT BEGINS -->
                   
                        @include('includes.flash_messages')
                        @include('includes.validation_error_messages')
                        <div class="row">
                            <div class="col-sm-12">  
                              <table id="dynamic-table" class="table table-striped table-bordered table-hover">
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
                            <div class="hr hr-18 dotted hr-double"></div>
                            <div class="col-sm-12  form-actions">
                              <div class="row">
                                <div class="col-sm-12">
                                  <div class="row">
                                    
                                      <table class="table table-hover table-striped">
                                        <tr>
                                          <th>S.No.</th>
                                          <th>Student Name</th>
                                          <th>Father Name</th>
                                          <th>Maximum Mark</th>
                                          <th>Passing Mark</th>
                                          <th>Obtained Mark</th>
                                          <!-- assesmentGrade -->
                                          @if($subject_type==0)
                                          <th>Grade</th>
                                          @endif
                                          <!-- assesmentGrade -->
                                          <th>Attendance</th>
                                          @if($exam->mode_id==1)
                                            <th>Assessment Status</th>
                                            <th>Action</th>
                                          @endif  
                                        </tr>
                                          @php $i=1 @endphp
                                          @if($exam->mode_id==1)
                                            @foreach($students as $key =>$val)
                                                @foreach($marks as $std_id =>$mark)
                                                  @if($val->id == $std_id)
                                                   <tr>
                                                    <?php $obtained = $mark->obtained_mark?$mark->obtained_mark:0; ?>
                                                        <td>{{$i}}.</td>  
                                                        <td> {{$val->first_name}} ( {{$val->reg_no}} )</td>
                                                        <td> {{$val->father_name}}</td>
                                                        <td>{{ $exam->max_mark }}</td>
                                                        <td>{{ $exam->pass_mark }}</td>
                                                        <td>{{$obtained}}</td>
                                                        <td style="color:{{$mark->attendance==1?'Green':'Red'}} ">
                                                          <b>{{$mark->attendance==1?'PRESENT':'ABSENT'}}</b>
                                                        </td>
                                                        <td style="color:{{$mark->assessment_status==1?'Green':'Red'}} ">
                                                           <b> {{$mark->assessment_status==1?'DONE':'PENDING'}} </b> 
                                                        </td>
                                                        <td>
                                                          <div class="hidden-sm hidden-xs action-buttons">
                                                            <a href="{{route($base_route.'.view',['exam_id'=>$exam->id,'student_id'=>$val->id])}}" title="View Answers" class="tooltip-warning btn btn-minier btn-warning" data-rel="tooltip"><i class="fa fa-eye"></i>
                                                          </a>
                                                          </div>
                                                          
                                                        </td>
                                                    </tr>
                                                    @php($i++)
                                                  @endif
                                                @endforeach
                                            @endforeach
                                          @else
                                            {!!Form::open(['route'=>$base_route.'.save_offline_exam','method'=>'POST','class'=>'form-horizontal'])!!}
                                            {!!Form::hidden('exam_id',$exam->id)!!}
                                            @foreach($students as $key =>$val)
                                                @foreach($marks as $std_id =>$mark)

                                                  @if($val->id == $std_id)
                                                   <tr>
                                                    <?php $obtained = $mark->obtained_mark?$mark->obtained_mark:0; ?>
                                                        <td>{{$i}}.</td>  
                                                        <td> {{$val->first_name}} ( {{$val->reg_no}} )</td>
                                                        <td> {{$val->father_name}}</td>
                                                        <td>{{ $exam->max_mark }}</td>
                                                        <td>{{ $exam->pass_mark }}</td>
                                                        <td><input type="number" class="onlyNumber" name="mark[{{$val->id}}]" value="{{$obtained}}" required placeholder="Enter Mark" max="{{ $exam->max_mark }}" step='0.01'></td>
                                                        <!-- assesmentGrade -->
                                                         @if($subject_type==0)
                                                         <td><input type="text" name="grade[{{$val->id}}]" value="{{isset($mark->grade)?$mark->grade:''}}" placeholder="Grade"></td>
                                                        @endif
                                                        <!-- assesmentGrade -->
                                                        <td style="color:{{$mark->attendance==1?'Green':'Red'}} ">
                                                          
                                                          {!!Form::select('attendance['.$val->id.']',['1'=>'Present','2'=>'Absent','3'=>'Medical','4'=>'N/A'],$mark->attendance?$mark->attendance:'1')!!}
                                                        </td>
                                                    </tr>
                                                    @php($i++)
                                                  @endif
                                                @endforeach
                                            @endforeach
                                            
                                          @endif  
                                      </table>  
                                      @if($exam->mode_id==1)
                                      
                                      @else
                                      <tr>
                                              <td colspan="7"><button class="btn btn-info pull-right" type="submit">Save</button></td>
                                            </tr>
                                      @endif 
                                  </div>      
                                </div>
                              </div>
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