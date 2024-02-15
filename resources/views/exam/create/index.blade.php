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
                       {{$panel}}
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
                            <div class="col-xs-12 col-md-12 col-sm-12">  
                                <h4 class="header large lighter blue"> 
                               
                                     <i class="fa fa-search bigger-110"></i> Search {{$panel}}
                                
                              </h4>
                                 {!!Form::open(['route'=>'exam.list','method'=>'GET', 'class' => 'form-horizontal',
                                        'id' => 'validation-form', "enctype" => "multipart/form-data"])!!} 
                                    @include($view_path.'.includes.search_form')
                                  {!!Form::close()!!}
                            </div>
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                @include($view_path.'.includes.table')
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
 @include('includes.scripts.bulkaction_confirm')
 <script >
   function loadSection($this){
      $('#section').prop('selectedIndex',"");
      $('#subject').html('').append('<option value="">--Select Subject--</option>');
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
                $('#exam_type').html('').append('<option value="">--Exam Type--</option>');
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
      $('#subject').html('').append('<option value="">--Select Subject--</option>');
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