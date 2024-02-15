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
                                @include($view_path.'.includes.form')
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
   $('#faculty').on('change',function(){
    var faculty = $(this).val();
      $('#section').prop('selectedIndex',"");
      $('#subject').prop('selectedIndex',"");
        $.ajax({
          type: 'POST',
          url: "/api/timetable/loadSection",
          data:{
            _token: "{{csrf_token()}}",
            course: faculty
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
     });
     $('.border-form').on('change',function(){
      var mode = $('#mode').val();
      var faculty = $('#faculty').val();
      var section = $('#section').val();
      var term = $('#term').val();
      var session = "{{Session::get('activeSession')}}";
      var branch = "{{Session::get('activeBranch')}}";
      $('#exam').html('').append('<option value="">Select</option>')
      $.post(
          "{{route('loadExamsByMode')}}",
          {
            _token  : "{{ csrf_token()}}",
            section : section,
            faculty : faculty,
            session : session,
            branch  : branch,
            term    : term,
            mode    : mode,
          },
          function(response){
              var data = $.parseJSON(response);
              if(data.error){
                if(data.info){
                  toastr.info(data.msg,'Info');
                }else{
                  toastr.warning(data.msg,'Warning');
                }
              }else{
                toastr.success(data.msg,'Success');
                $('#exam').html('').append('<option value="">--Select Exam--</option>');
                $.each(data.data,function($k,$v){
                   $('#exam').append('<option value="'+$v.id+'">'+$v.title+' ( '+$v.type+' )</option>');
                })
                 $('select.selectpicker').selectpicker('refresh');
              }
          });
     })
 </script>
@endsection