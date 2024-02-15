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
                          @include($view_path.'.includes.preview-exam')
                            <div class="col-xs-12">  
                                <h4 class="header large lighter blue"> 
                                @if(isset($data['row']))
                                     <i class="fa fa-pencil bigger-110"></i> Edit {{$panel}}
                                @else     
                                  <i class="fa fa-plus bigger-110"></i> Add {{$panel}}
                                 @endif 
                              </h4>

                              @if(isset($data['row']))
                                {!!Form::model($data['row'],['route'=>[$base_route.'.edit','id'=>$data['row']->id,'exam_id'=>$exam_id],'method'=>'POST', 'class' => 'form-horizontal',
                                        'id' => 'validation-form', "enctype" => "multipart/form-data"])!!}
                              @else 
                                 {!!Form::open(['route'=>[$base_route.'.store','exam_id' => $exam_id],'method'=>'POST', 'class' => 'form-horizontal',
                                        'id' => 'validation-form', "enctype" => "multipart/form-data"])!!} 
                              @endif         
                                        @include($view_path.'.includes.form')  
                                  {!!Form::close()!!}
                            </div>
                            <div class="col-xs-12">
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
 @include('includes.scripts.summarnote')
 @include('includes.scripts.delete_confirm')
 <script >
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
    $('#add_button').on('click',function(){
      var html = $('#question_div').html();
      var question_count  = $('#question_main_div .question_count').length;
      var lastid = $("#question_main_div .question_count:last").attr("id");
      if(lastid != null){
        question_count = parseInt(lastid) + 1;
      }else{
        question_count = question_count + 1;
      }
      html = html.replace(/question_id/g,question_count);
      html = html.replace(/add_required/g,'required');
      $('#question_main_div').append("<div class='question_count' id='"+question_count+"'>"+html+"</div>");
     
    });
    function getOptions(question_id,$this){
      var question_type = $this.value;
      var correct_option = "<div class='form-group'><label class=' col-sm-2 control-label'>Correct Option</label><div class='col-sm-4'><select class='form-control' required name='correct_answer["+question_id+"]'><option value=''>--Select Correct Option--</option><option value='option_1'>Option 1</option><option value='option_2'>Option 2</option><option value='option_3'>Option 3</option><option value='option_4'>Option 4</option><option value='option_5'>Option 5</option><option value='option_6'>Option 6</option></select></div></div>";
      switch (question_type) {
        case '1':
          input = "<div class='form-group'><div class='col-sm-12'><input type='text' name='option_1["+question_id+"]' placeholder='Answer text will be here' disabled class='form-control'></div>"
          break;
        case '2':
          input = "<div class='form-group'><div class='col-sm-2'><input type='text' name='option_1["+question_id+"]' placeholder='Enter Option 1' class='form-control' required></div><div class='col-sm-2'><input type='text' name='option_2["+question_id+"]' placeholder='Enter Option 2' class='form-control' required></div><div class='col-sm-2'><input type='text' name='option_3["+question_id+"]' placeholder='Enter Option 3' class='form-control' ></div><div class='col-sm-2'><input type='text' name='option_4["+question_id+"]' placeholder='Enter Option 4' class='form-control'></div><div class='col-sm-2'><input type='text' name='option_5["+question_id+"]' placeholder='Enter Option 5' class='form-control'></div><div class='col-sm-2'><input type='text' name='option_6["+question_id+"]' placeholder='Enter Option 6' class='form-control'></div></div>"+correct_option;
          break;
        case '3':
          input = "<div class='form-group'><div class='col-sm-2'><input type='text' name='option_1["+question_id+"]' placeholder='Enter Option 1' class='form-control' required></div><div class='col-sm-2'><input type='text' name='option_2["+question_id+"]' placeholder='Enter Option 2' class='form-control' required></div><div class='col-sm-2'><input type='text' name='option_3["+question_id+"]' placeholder='Enter Option 3' class='form-control' ></div><div class='col-sm-2'><input type='text' name='option_4["+question_id+"]' placeholder='Enter Option 4' class='form-control'></div><div class='col-sm-2'><input type='text' name='option_5["+question_id+"]' placeholder='Enter Option 5' class='form-control'></div><div class='col-sm-2'><input type='text' name='option_6["+question_id+"]' placeholder='Enter Option 6' class='form-control'></div></div>";
          break;
        case '4':
          input = "<div class='form-group'><div class='col-sm-2'><input type='text' name='option_1["+question_id+"]' placeholder='Enter Option 1' class='form-control' required></div><div class='col-sm-2'><input type='text' name='option_2["+question_id+"]' placeholder='Enter Option 2' class='form-control' required></div><div class='col-sm-2'><input type='text' name='option_3["+question_id+"]' placeholder='Enter Option 3' class='form-control' ></div><div class='col-sm-2'><input type='text' name='option_4["+question_id+"]' placeholder='Enter Option 4' class='form-control'></div><div class='col-sm-2'><input type='text' name='option_5["+question_id+"]' placeholder='Enter Option 5' class='form-control'></div><div class='col-sm-2'><input type='text' name='option_6["+question_id+"]' placeholder='Enter Option 6' class='form-control'></div></div>"+correct_option;
          break;
        case '5':
          input = "<div class='form-group'><div class='col-sm-12'><input type='date' name='option_1["+question_id+"]' placeholder='Enter Option 1' class='form-control' disabled></div></div>"
          break;
        case '6':
          input = "<div class='form-group'><div class='col-sm-12'><input type='file' name='option_1["+question_id+"]' placeholder='Enter Option 1' class='form-control' disabled></div></div>"
      }
      $('#question_type_'+question_id).html('').append(input);
    }
    $('#validation-form').on('submit',function(){
       var question_count  = $('#question_main_div .question_count').length;
        if(!question_count){
        toastr.warning('Please Add Question','Warning');
        return false;
       }
       var mark = $('.mark');
       var exclude_edit_mark = parseInt($('#edit_question').text());
       if(exclude_edit_mark){
        leave_mark = exclude_edit_mark;
       }else{
        leave_mark =0;
       }
       
       var sum = 0 - leave_mark;
       var max_mark = parseInt($('#max_mark').text());
       $.each(mark,function($k,$v){
        if($(this).val()){
          sum = sum + parseInt($(this).val());
        }else if($(this).text()){
           sum = sum + parseInt($(this).text());
        }
       });
       if(sum > max_mark){
        toastr.warning('Sum of "Question Mark" cannot be greater than "Maximum Mark"','Warning');
        return false;
      }
      
    })
 </script>
@endsection