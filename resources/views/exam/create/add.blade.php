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
                       @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                      
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>

                    @endif

                        <div class="row">
                            <div class="col-xs-12 col-md-12 col-sm-12">  
                                <h4 class="header large lighter blue"> 
                                @if(isset($data['row']))
                                     <i class="fa fa-pencil bigger-110"></i> Edit {{$panel}}
                                @else     

                                  <i class="fa fa-plus bigger-110"></i> Add {{$panel}}
                                 @endif 
                              </h4>

                              @if(isset($data['row']))
                                 {!!Form::model($data['row'],['route'=>[$base_route.'.edit',$data['row']->id],'method'=>'POST', 'class' => 'form-horizontal',
                                        'id' => 'validation-form', "enctype" => "multipart/form-data"])!!}
                              @else 
                                 {!!Form::open(['route'=>$base_route.'.store','method'=>'POST', 'class' => 'form-horizontal',
                                        'id' => 'validation-form', "enctype" => "multipart/form-data"])!!} 
                              @endif          
                                @include($view_path.'.includes.form')  
                                  {!!Form::close()!!}
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
              /*exam change*/
               $('.selectpicker').selectpicker('refresh');
                s/*exam change*/
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

     /*exam change*/
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
                $('#subject').html('').append('<option value="">--No Subject Found--</option>');
              }else{
                toastr.success(data.msg,'Success');
                $('#subject').html('').append('<option value="">--Select Subject--</option>');
                $.each(data.type,function($k,$v){
                   $('#subject').append('<option value="'+$v.id+'">'+$v.title+'</option>');
                })
               
              }
          });
     })
     /*exam change*/
 </script>
@endsection