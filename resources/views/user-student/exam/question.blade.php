@extends('user-student.layouts.master')

@section('css')


@endsection

@section('content')

    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                @include('layouts.includes.template_setting')

                <div class="page-header">
                    <h1>
                        Exams
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                             Detail
                        </small>
                    </h1>
                </div><!-- /.page-header -->
                @if(count($data['exam'])>0)
                    <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <div id="demo"></div>
                        <table class="table table-striped table-bordered table-hover">
                            <tr>
                                <td class="label-info white">Exam Title</td>
                                <td>{{ $data['exam'][0]->exam_title }}</td>
                                <td class="label-info white">Subject</td>
                                <td>{{ $data['exam'][0]->subject }}</td>
                            </tr>
                            <tr> 
                                <td class="label-info white">{{env('course_label')}}</td>
                                <td>{{ $data['exam'][0]->faculty }} ({{ $data['exam'][0]->section }})</td>
                                <td class="label-info white">Duration</td>
                                <td>{{ \Carbon\Carbon::parse($data['exam'][0]->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($data['exam'][0]->end_time)->format('h:i A') }}</td>
                                
                            </tr>
                            <tr> 
                                <td class="label-info white">Maximum Mark</td>
                                <td>{{ $data['exam'][0]->max_mark }}</td>
                                <td class="label-info white">Passing Mark</td>
                                <td>{{ $data['exam'][0]->pass_mark }}</td>
                            </tr>
                            <tr> 
                                <td class="label-info white">Description</td>
                                <td colspan="3">{{ $data['exam'][0]->exam_description }}</td>
                            </tr>
                        </table>
                        <hr>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 text-center large lighter blue">
                                <h3>Time Left -<span id="timer"></span></h3>
                                
                            </div>
                        </div>
                        @php $i=1 @endphp
                        {!!Form::open(['route'=>'user-student.exams.submit','method'=>'POST','id'=>'validation-form','enctype'=>'multipart/form-data','class'=>'horizontal-form'])!!}
                        {!!Form::hidden('exam',$id)!!}
                            @foreach($data['exam'] as $k => $val)
                                <div class="row" style="border: 1px solid #ddd9d9;padding: 20px 20px;background: #f5f9ff;box-shadow: -3px 0px 3px 0px;margin: 10px 0px 10px 0px;">
                                    <div class="col-xs-12 col-sm-12">
                                        <h4 class=" lighter blue">Q{{$i}}. {{$val->question_title}} <b style="color: red">*</b></h4>
                                        @if($val->is_required)

                                        @endif
                                        @if($val->question_description)
                                           <div class="col-sm-12">
                                               {{$val->question_description}}
                                           </div> 
                                           
                                        @endif
                                        <br>
                                           <br>
                                        <div class="form-group">
                                            @switch($val->question_type)
                                                @case(1)
                                                    <div class="col-sm-12">
                                                        <textarea name="answer_1[{{$val->id}}]" class="form-control" placeholder="Enter Your Answer Here" rows="4"></textarea>
                                                    </div>
                                                    @break
                                                @case(2)
                                                    @if($val->option_1)
                                                        <div class="form-check">
                                                            
                                                            <label class="form-check-label"><input type="radio" name="answer_1[{{$val->id}}]" value="option_1" class="form-check-input"> {{$val->option_1}}</label>
                                                        </div>
                                                    @endif
                                                    
                                                    @if($val->option_2)
                                                        <div class="form-check">
                                                            
                                                            <label class="form-check-label"><input type="radio" name="answer_1[{{$val->id}}]" value="option_2" class="form-check-input"> {{$val->option_2}}</label>
                                                        </div>
                                                    @endif    
                                                    @if($val->option_3)
                                                        <div class="form-check">
                                                            <label class="form-check-label"><input type="radio" name="answer_1[{{$val->id}}]" value="option_3" class="form-check-input"> {{$val->option_3}}</label>
                                                        </div>
                                                    @endif    
                                                    @if($val->option_4)
                                                        <div class="form-check">
                                                            <label class="form-check-label"><input type="radio" name="answer_1[{{$val->id}}]" value="option_4" class="form-check-input"> {{$val->option_4}}</label>
                                                        </div>
                                                    @endif    
                                                    @if($val->option_5)
                                                        <div class="form-check">
                                                            <label class="form-check-label"><input type="radio" name="answer_1[{{$val->id}}]" value="option_5" class="form-check-input"> {{$val->option_5}}</label>
                                                        </div>
                                                    @endif    
                                                    @if($val->option_6)
                                                        <div class="form-check">
                                                            <label class="form-check-label"><input type="radio" name="answer_1[{{$val->id}}]" value="option_6" class="form-check-input">{{$val->option_6}}</label>
                                                        </div>
                                                    @endif    
                                                    
                                                    @break
                                                @case(3)
                                                    @if($val->option_1)
                                                        <div class="form-check">
                                                            <label class="form-check-label"><input type="checkbox" name="answer_1[{{$val->id}}]" value="option_1" class="form-check-input"> {{$val->option_1}}</label>
                                                        </div>
                                                    @endif
                                                    
                                                    @if($val->option_2)
                                                        <div class="form-check">
                                                            <label class="form-check-label"><input type="checkbox" name="answer_2[{{$val->id}}]" value="option_2" class="form-check-input"> {{$val->option_2}}</label>
                                                        </div>
                                                    @endif    
                                                    @if($val->option_3)
                                                        <div class="form-check">
                                                            <label class="form-check-label"><input type="checkbox" name="answer_3[{{$val->id}}]" value="option_3" class="form-check-input"> {{$val->option_3}}</label>
                                                        </div>
                                                    @endif    
                                                    @if($val->option_4)
                                                        <div class="form-check">
                                                            <label class="form-check-label"><input type="checkbox" name="answer_4[{{$val->id}}]" value="option_4" class="form-check-input"> {{$val->option_4}}</label>
                                                        </div>
                                                    @endif    
                                                    @if($val->option_5)
                                                        <div class="form-check">
                                                            <label class="form-check-label"><input type="checkbox" name="answer_5[{{$val->id}}]" value="option_5" class="form-check-input"> {{$val->option_5}}</label>
                                                        </div>
                                                    @endif    
                                                    @if($val->option_6)
                                                        <div class="form-check">
                                                            <label class="form-check-label"><input type="checkbox" name="answer_6[{{$val->id}}]" value="option_6" class="form-check-input"> {{$val->option_6}}</label>
                                                        </div>
                                                    @endif    
                                                    @break
                                                @case(4)
                                                    <div class="col-sm-6 col-xs-6">
                                                       <select class="form-control" name="answer_1[{{$val->id}}]">
                                                            <option value="">--Select--</option>
                                                        
                                                            @if($val->option_1)
                                                               <option value="option_1">{{$val->option_1}}</option>
                                                            @endif
                                                            @if($val->option_2)
                                                                <option value="option_2">{{$val->option_2}}</option>
                                                            @endif    
                                                            @if($val->option_3)
                                                                <option value="option_3">{{$val->option_3}}</option>
                                                            @endif    
                                                            @if($val->option_4)
                                                                <option value="option_4">{{$val->option_4}}</option>
                                                            @endif    
                                                            @if($val->option_5)
                                                                <option value="option_5">{{$val->option_5}}</option>
                                                            @endif    
                                                            @if($val->option_6)
                                                                <option value="option_6">{{$val->option_6}}</option>
                                                            @endif    
                                                     </select>   
                                                    </div>
                                                    
                                                    @break
                                                @case(5)
                                                    <div class="col-sm-3">
                                                        <input type="date" name="answer_1[{{$val->id}}]" class="form-control">
                                                    </div>
                                                    @break   
                                                @default
                                                    default
                                            @endswitch 
                                         </div>                                            
                                    </div>
                                </div>
                                @php $i++ @endphp 
                            @endforeach
                            <div class="row">
                                <div class="col-sm-12 clearfix form-actions text-right">
                                    <button class="btn btn-success">Submit</button>
                                </div>
                            </div>
                        {!!Form::close()!!}    
                    </div>
                </div><!-- /.row -->
                @else
                    <div class='row'>
                        <div class='col-sm-12 col-md-12 col-xs-12'>
                            <h3>No Question Found</h3>
                        </div>
                    </div>
                @endif    
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->



@endsection

@section('js')
    <!-- page specific plugin scripts -->
    @include('includes.scripts.dataTable_scripts')
    <script>
        document.addEventListener('contextmenu', function(e) {
          e.preventDefault();
        });
        <?php 
        if(count($data['exam'])>0){
            $date = \Carbon\Carbon::parse($data['exam'][0]->date.' '.$data['exam'][0]->end_time)->format('M d, Y H:i:s');
        ?>
        var countDownDate = new Date("{{$date}}").getTime();
        
        // Update the count down every 1 second
        var x = setInterval(function() {
        console.log(new Date());
          // Get today's date and time
          var now = new Date().getTime();

          // Find the distance between now and the count down date
          var distance = countDownDate - now;

          // Time calculations for days, hours, minutes and seconds
          var days = Math.floor(distance / (1000 * 60 * 60 * 24));
          var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
          var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
          var seconds = Math.floor((distance % (1000 * 60)) / 1000);

          // Display the result in the element with id="demo"
          document.getElementById("timer").innerHTML =  hours + "h "
          + minutes + "m " + seconds + "s ";

          // If the count down is finished, write some text
          if (distance < 0) {
            clearInterval(x);
            $('#validation-form').submit();
            alert('Time Over - Please submit your anser sheet by clicking on submit button now.');
            document.getElementById("demo").innerHTML = "EXPIRED";
          }
        }, 1000);
    <?php  } ?>
    </script>
@endsection