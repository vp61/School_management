@extends('layouts.master')

@section('css')
	 <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}" />
	  <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-clockpicker.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.custom.min.css') }}" />
	   <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-loader.css') }}" />
	 <style type="text/css">
    .sch_box{
      padding: 5%;
      background: #96eba8d4;
      text-align: center;
      font-weight: 600;
           
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
                        @include($view_path.'.includes.breadcrumb-primary')
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                           Time Table
                        </small>
                    </h1>
                </div><!-- /.page-header -->
                
                
                <div class="row">
                  <div class="col-xs-12">
                      <button class="btn btn-primary pull-right" data-toggle="modal" data-target="#addModal" id="add_btn"><i class="fa fa-plus"></i> Add New Schedule</button>
                  </div>
                </div>
                <hr>
                @include('includes.flash_messages')
	              <div class="row">
	              		<div class="col-md-12 col-xs-12">
	              				
	              			<div class="form-group">
	              				  {!!Form::label('course','Select Course',['class'=>'col-sm-2 control-label'])!!}
	              				  <div class="col-md-4">
	              				  	{!! Form::select('main_course', $data['course'], null, ['class' => 'form-control','id'=>'main_course',  'onchange'=>'loadSection(this)']) !!}
	              				  </div>
	              				 {!!Form::label('section','Select Section',['class'=>'col-sm-2 control-label'])!!}
	              				  <div class="col-md-4">
	              				  	{!! Form::select('main_section', [""=>"Select"], null, ['class' => 'form-control','id'=>'main_section', 'onchange'=>'loadSchedule()']) !!}
	              				  </div>
	              				   @include($view_path.'.includes.add_modal')
	              			</div>
	              		</div>
	              </div>
	              <hr>
	              <div class="row">
	              	<div class="col-md-9 col-xs-12">
	              		@include($view_path.'.includes.table')	
	              	</div>
                  <div class="col-md-3 col-xs-12">
                    <div class="row">
                        <div class="col-md-12 col-xs-12">
                             <div id="attendance_div" style="height: 50vh;overflow: auto;">
                                <table id="attendance_box" class="table table-striped">
                                    
                                </table>
                            </div>
                        </div>
                        
                    </div>
                    
                    
                  </div>
	              </div>
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
@endsection

@section('js')
 @include('includes.scripts.delete_confirm')
	
	<script src="{{ asset('assets/js/select2.min.js') }}"></script>
	<script src="{{ asset('assets/js/bootstrap-clockpicker.min.js') }}"></script>
	<script type="text/javascript">
		
		$(document).ready(function(){
			$("#course").select2();
			$('.clockpicker').clockpicker();
      $("#from").on('change',function(){
          $("#staff").prop('selectedIndex',"");
         
      }); 
       $("#to").on('change',function(){
          $("#staff").prop('selectedIndex',"");
      });
		});
		 function checkTeacher(){
		 	var day=$("#day").val();
		 	var from=$("#from").val();
		 	var to=$("#to").val();
      if(from.length==0 || to.length==0){
         $("#submit").prop('disabled',true);
         $("#select-time").css('display','block');
       }else{
        $("#submit").prop('disabled',false);
        $("#select-time").css('display','none');
       }
      var staff=$('#staff').val();
		 	document.getElementById('teacher-loader').style.display="block";
		 	$("#available").css('display','none');
      $("#notavailable").css('display','none');
		 	$.ajax({
                type: 'POST',
                url: 'api/timetable/checkTeacher',
                data: {
                    _token: '{{ csrf_token() }}',
                    staff_id: staff,
                    from :from,
                    to : to,
                    day : day
                },
                success: function (response) {
                    var data = $.parseJSON(response);
                    if (data.error) {
                        toastr.warning(data.message, "warning");
                        $("#subject").html('').append('<option value="">Select</option>');
                        $("#teacher-loader").css('display','none');
                    } else {

                      	$("#teacher-loader").css('display','none');                  	
                        if(data.teacher){
                          if(data['teacher'].length==0){
                            $("#available").css('display','block');
                            $("#submit").prop('disabled',false);
                            
                          }
                          else{
                            $("#notavailable").css('display','block');
                            $("#submit").prop('disabled',true);
                          }
                        }
                        if(data.subject){
                           $("#subject").html('').append('<option value="">--Select Subject--</option>');
                            $.each(data.subject,function(key,val){
                                 $('#subject').append('<option value="'+val.id+'">'+val.subject+'</option>');
                            });
                        	
                          }
                      }
                }
            });
		 }
     function loadSection($this){
      $('#main_section').prop('selectedIndex',"");
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
              $("#main_section").html('').append('<option>--Select Section--</option');
              $.each(data.section, function(key,val){
                  $("#main_section").append('<option value="'+val.id+'">'+val.semester+'</option');
              });
            }
          }
        })
     }
     function loadSchedule(){
      var course=$('#main_course').val();
      var section=$('#main_section').val();
      $('#sch-table').html('');
        $('#no_data').css('display','block');
      $.ajax({
        type: 'POST',
        url: 'api/timetable/loadSchedule',
        data: {
          _token: '{{ csrf_token() }}',
          course: course,
          section : section
        },
        success: function (response) {
            var data = $.parseJSON(response);
            if (data.error) {
                toastr.warning(data.message, "warning");
            } else {
                $('#no_data').css('display','none');
                $('#check_alt').css('display','block');
               
                $('#sch-table').html('').append('<thead id="table_head"></thead>');
                  
                 $('#attendance_box').html('').append('<th style="padding:2%;text-align:center;">Attendance</th>');
                <?php 
                  if(!empty($atten)){
                  foreach ($atten as $key => $value) {?>
                    if('{{$value->present}}'!=1){
                      var color="color:red";
                    }else{
                      var color="";
                    }
                    $('#attendance_box').append('<tr><td style="'+color+'">{{$value->staff}}<b class="pull-right" >{{$value->present==1?'P':'A'}}</b></td>/td></tr>');
                   
                 <?php  } } ?>
                
                $.each(data.day, function(d_key,d_val){
                  $('#table_head').append('<th style="padding:1%;text-align:center;">'+d_val.title+'</th>');
                });                 
                $.each(data.schedule, function(key,value){
                          $('#sch-table').append('<tr id="'+key+'"></tr>');

                          $.each(value,function(k,val){
                              $('#'+key).append('<td id="'+key+k+'"></td>');
                                  
                              $.each(val,function(keyval,v){
                                if(keyval==k){
                                  if(v.altTeacher!=null){
                                    var altTeacher=v.altTeacher;
                                  }
                                  else{
                                    altTeacher=" ";
                                  }
                                  if(data.weekday==v.day_id){
                                   <?php 
                                   if(!empty($atten)){
                                    foreach ($atten as $key => $value){ ?>

                                      if('{{$value->link_id}}'==v.staff_id && '{{$value->present}}'!=1){
                                        var color="color:red";
                                      }
                                      
                                     
                                   <?php } 
                                   } ?>
                                  }
                                  else{
                                    var border="border:none;";
                                  }
                                  $('#'+key+k).html('').append('<div id="a/'+v.id+'" class="sch_box '+key+k+'">'+v.time_from+'-'+v.time_to+'<br>'+v.title+'<div style="height:15px;'+color+'">'+v.staff+'</div><div style="height:15px;color:black;">'+altTeacher+'</div><div>'+v.room_no+'<span class="pull-right"><a href="timetable/edit/'+v.id+'"><i class="fa fa-pencil"> </i> </a> <a id="a/'+v.id+'" class="bootbox-confirm" onclick="deleteConfirm(this.id)" style="color:red;" > <i class="ace-icon fa fa-trash-o" > </i> </a></span></div></div>');
                                }  
                                   
                                
                              });
                          });
                }); 
            }
        }
      });
     }
     function deleteConfirm($this) {
      var a=$this;
            var $this = $(this);
            bootbox.confirm({
                title: "<div class='widget-header'><h4 class='smaller'><i class='ace-icon fa fa-exclamation-triangle red'></i> Delete Confirmation</h4></div>",
                message: "<div class='ui-dialog-content ui-widget-content' style='width: auto; min-height: 30px; max-height: none; height: auto;'><div class='alert alert-info bigger-110'>This item will be permanently deleted and cannot be recovered.</div>" +
                "<p class='bigger-110 bolder center grey'><i class='ace-icon fa fa-hand-o-right blue bigger-120'></i>Are you sure?</p>",
                size: 'small',
                    buttons: {
                        confirm: {
                            label : "<i class='ace-icon fa fa-trash'></i> Yes, Delete Now!",
                            className: "btn-danger btn-sm",
                        },
                        cancel: {
                            label: "<i class='ace-icon fa fa-remove'></i> Cancel",
                            className: "btn-primary btn-sm",
                        }
                    },
                    callback: function(result) {
                        if(result) {
                          $.ajax({
                            type: 'POST',
                            url: '/api/timetable/delete',
                            data:{
                                _token: '{{csrf_token()}}',
                                id: a
                            },
                            success: function(response){
                              var data = $.parseJSON(response);
                              if(data.error){
                                toastr.warning(data.message,"warning"); 
                              }
                              else{
                                  document.getElementById(a).style.display="none";
                              }
                            }
                          });
                             
                        }
                    }
                }
            );
      
      
     }
     function is_break($this){
      var is_break=$this.value;
      if(is_break==1){
        $("#subject").prop("required",false);
         $("#staff").prop("required",false);
         $("#type").prop("required",false);
          $("#room").prop("required",false);
          $("#on_break").hide();
      }
      if(is_break==0){
         $("#subject").prop("required",true);
         $("#staff").prop("required",true);
         $("#type").prop("required",true);
          $("#room").prop("required",true);
          $("#on_break").show();
      }
     }
     

	</script>
@endsection