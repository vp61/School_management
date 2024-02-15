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
                           @if(isset($data['row']))
                            Edit
                           @else
                            Add
                           @endif 
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    <div class="col-xs-12 ">
                    
                    <!-- PAGE CONTENT BEGINS -->
                   
                        @include('includes.flash_messages')
                        @include('includes.validation_error_messages')
                        <div class="row">
                            <div class="col-xs-12">  
                                <h4 class="header large lighter blue">     

                                  <i class="fa fa-plus bigger-110"></i> Assessment
                              </h4> 
                                 <form action="#" class="form-horizontal" id="form"> 
                                        <div class="form-group">
                                  {!! Form::label('mode', env('course_label'), ['class' => 'col-sm-1 control-label']) !!}
                                  <div class="col-sm-2">
                                      {!! Form::select('mode_id',$dropdowns['faculty'],null, ["class" => "form-control border-form upper","required"]) !!}
                                  </div>
                                  {!! Form::label('mode', 'Section', ['class' => 'col-sm-1 control-label']) !!}
                                  <div class="col-sm-2">
                                      {!! Form::select('section',[''=>'--Select Section--','1'=>'SEC A','2'=>'SEC B'],null, ["class" => "form-control border-form upper","required"]) !!}
                                  </div>
                                  {!! Form::label('mode','Term', ['class' => 'col-sm-1 control-label']) !!}
                                  <div class="col-sm-2">
                                      {!! Form::select('term',[''=>'--Select Term--','1'=>'Term 1 ','2'=>'Term 2'],null, ["class" => "form-control border-form upper","required"]) !!}
                                  </div>
                                  {!! Form::label('mode','Exam', ['class' => 'col-sm-1 control-label']) !!}
                                  <div class="col-sm-2">
                                      {!! Form::select('student',[''=>'--Select Exam--','1'=>'Exam 1','2'=>'Exam 2'],null, ["class" => "form-control border-form upper","required"]) !!}
                                  </div>
                              </div>
                              <div class="clearfix form-actions">
                                  <div class="align-right">            &nbsp; &nbsp; &nbsp;
                                      <button class="btn btn-info" type="submit" id="filter-btn">
                                          <i class="fa fa-search bigger-110"></i>
                                             Search
                                      </button>
                                  </div>
                              </div>
                                  {!!Form::close()!!}
                            </div>

                            <div class="col-xs-12 text-center" style="display: none;" id="assessment">
                              <hr>
                                <img src="/report/record-mark.png">
                            </div>
                        </div>
                        <div class="hr hr-18 dotted hr-double"></div>
                       
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
  $('#form').on('submit',function(){
   $('#assessment').css('display','block');
   return false;
  });
  $('select').on('change',function(){
    $('#assessment').css('display','none');
  })
   function loadSection($this){
      $('#section').prop('selectedIndex',"");
      $('#subject').prop('selectedIndex',"");
        $.ajax({
          type: 'POST',
          url: "/api/timetable/loadSection",
          data:{
            _token: "{{csrf_token()}}",
            course: $this.value
          },
          success: function(response){
            var data= $.parseJSON(response);
            if(data.error){
                toastr.warning(data.message,'warning');
            } else{
              $("#section").html('').append('<option value="">--Select Section--</option');
              $.each(data.section, function(key,val){
                  $("#section").append('<option value="'+val.id+'">'+val.semester+'</option');
              });
            }
          }
        })
     }

     $('#term').on('change',function(){
      var term = $(this).val();
      $.post(
          "{{route('loadExamType')}}",
          {
            _token  : "{{ csrf_token()}}",
            term_id :term
          },
          function(response){
              var data = $.parseJSON(response);
              if(data.error){
                toastr.warning(data.msg,'Warning');
              }else{
                toastr.success(data.msg,'Success');
                $('#exam_type').html('').append('<option value="">--Select Exam Type--</option>');
                $.each(data.type,function($k,$v){
                   $('#exam_type').append('<option value="'+$v.id+'">'+$v.title+'</option>');
                })
              }
          });
     });
     $('#section').on('change',function(){
      var section = $(this).val();
      var faculty = $('#faculty').val();
      var session = "{{Session::get('activeSession')}}";
      var branch = "{{Session::get('activeBranch')}}";
      $.post(
          "{{route('loadExamSubject')}}",
          {
            _token  : "{{ csrf_token()}}",
            section : section,
            faculty : faculty,
            session : session,
            branch : branch,
          },
          function(response){
              var data = $.parseJSON(response);
              if(data.error){
                toastr.warning(data.msg,'Warning');
              }else{
                toastr.success(data.msg,'Success');
                $('#subject').html('').append('<option value="">--Select Subject--</option>');
                $.each(data.type,function($k,$v){
                   $('#subject').append('<option value="'+$v.id+'">'+$v.title+'</option>');
                })
              }
          });
     })
 </script>
@endsection