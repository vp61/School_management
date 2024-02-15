@extends('layouts.master')

@section('css')
    <!-- page specific plugin styles -->
@endsection

@section('content')

    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                <div class="page-header">
                    <h1>
                        {{ $panel }} Manager
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            {{ $panel }} Detail
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    @include('includes.flash_messages')
                    <div class="col-md-4">
                        <h4 class="header large lighter blue"><i class="fa fa-search" aria-hidden="true"></i>&nbsp;Schedule {{$panel}}</h4>
                        <form id="meeting_form"  action="#">
                            <?php /*<div class="row form-group">
                                {!! Form::label('faculty',env('course_label'), ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                    @if($data['general_setting']->live_class_scheduling == '1')
                                        {{Form::select('faculty_id[]',$data['faculty'],null, ['class'=>'form-control selectpicker','required','id'=>'faculty_id','onChange'=>'loadSection(this)','multiple'=>'multiple','data-live-search'=>'true'])}}
                                    @else
                                        <?php $data['faculty'] = array_prepend($data['faculty'],'--Select '.env('course_label').'--',''); ?>
                                        {{Form::select('faculty_id',$data['faculty'],null, ['class'=>'form-control selectpicker','required','id'=>'faculty_id','onChange'=>'loadSection(this)','data-live-search'=>'true'])}}
                                    @endif
                                </div>
                            </div>
                            <div class="row form-group">
                                {!! Form::label('section','Section', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                    @if($data['general_setting']->live_class_scheduling == '1')

                                        {{Form::select('section_id',[""=>'Select'],null, ['class'=>'form-control','required','id'=>'section_id'])}}

                                    @else
                                        {{Form::select('section_id[]',[""=>'Select'],null, ['class'=>'form-control selectpicker','required','id'=>'section_id','multiple'=>'multiple','data-live-search'=>'true'])}}
                                    @endif
                                    
                                </div>
                            </div> */?>
                             <div class="row form-group">
                                {!! Form::label('meeting_for','Schedule For', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                    {{Form::select('host_for',$data['host_for'],null, ['class'=>'form-control ','required','id'=>'host_for'])}}
                                </div>
                            </div>
                            <div class="row form-group">
                                {!! Form::label('title','Topic', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                    {{Form::text('topic',null, ['class'=>'form-control','required'])}}
                                </div>
                            </div>
                            <div class="row form-group">
                                {!! Form::label('start_date','Start Date', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                    {{Form::date('start_date',null,['class'=>'form-control','required'])}}
                                </div>
                            </div>
                            <div class="row form-group">
                                {!! Form::label('start_time','Start Time', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                    {{Form::time('start_time',null,['class'=>'form-control','required'])}}
                                </div>
                            </div>
                            <?php /*<div class="row form-group">
                                {!! Form::label('duration','Duration (in min.)', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                    {{Form::number('duration',null,['class'=>'form-control','max'=>60,'required'])}}
                                </div>
                            </div>*/?>
                            <div class="row">
                                <div class="col-sm-5 col-sm-offset-7">
                                    <input type="submit" name="submit" value="Save" class="btn btn-info">
                                </div>
                            </div>
                        {!! Form::close() !!}
                    </div>

                    <div class="col-md-8 col-xs-12">
                        <div class="row">
                        <div class="col-xs-12">
                            @include('includes.data_table_header')
                            <!-- div.table-responsive -->
                            <div class="table-responsive">
                                <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>S.N.</th>
                                        <th>Topic</th>
                                        <th>Scheduled For</th>
                                        <!-- <th>{{env('course_label')}}</th>
                                        <th>Section</th> -->
                                        <th>Date</th>
                                        <th>Start Time</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @php($i=1)
                                        @foreach($data['meeting'] as $meeting)
                                            <tr>
                                                <?php
                                                    $current_date = Carbon\Carbon::now();
                                                    $join_time = Carbon\Carbon::parse($meeting->start_time)->subMinutes(env('JOIN_BEFORE_TIME'));
                                                    $end_time = Carbon\Carbon::parse($meeting->start_time)->addMinutes($meeting->duration);
                                                ?>
                                                <td>{{ $i }}</td>
                                                <td>
                                                    {{ $meeting->topic }}
                                                </td>
                                                 <td>
                                                    {{ $data['host_for'][$meeting->host_for] }}
                                                </td>
                                                <?php /*
                                                    $fac = explode(',',$meeting->faculty_id);
                                                    $faculty =[];
                                                    foreach ($fac as $key => $value) {
                                                        if(isset($data['faculty'][$value])){
                                                            $faculty[] = $data['faculty'][$value];
                                                        }
                                                    }
                                                    $sec = explode(',',$meeting->section_id);
                                                    $section =[];
                                                    foreach ($sec as $key => $value) {
                                                        if(isset($data['section'][$value])){
                                                            $section[] = $data['section'][$value];
                                                        }
                                                    }
                                                
                                                <td>{{implode(', ',$faculty)}}</td>
                                                <td>{{ implode(', ',$section) }}</td>
                                                */?>
                                                <td>{{ Carbon\Carbon::parse($meeting->start_time)->format('d-M-Y') }}</td>
                                                <td>{{ Carbon\Carbon::parse($meeting->start_time)->format('H:i') }}</td>
                                                <td>
                                                    <?php $delete_btn = '0'?>
                                                     @if($join_time > $current_date)
                                                     <?php $delete_btn = '1'?>
                                                       <b style="border: 1px solid blue;padding: 2px 5px;border-radius: 3px;color: blue;font-weight: 300">Upcoming</b>
                                                    
                                                    @elseif($current_date < $end_time && $current_date > $join_time)
                                                    
                                                        <b style="border: 1px solid green;padding: 2px 5px;border-radius: 3px;color: green;font-weight: 300">Active</b>
                                                    @else
                                                        
                                                        <b style="border: 1px solid red;padding: 2px 5px;border-radius: 3px;color: red;font-weight: 300">Exipred</b>
                                                        
                                                    @endif       
                                                </td>
                                                <td style="text-align: center;">
                                                        @if(auth()->user()->hook_id == $meeting->created_by)
                                                            <?php
                                                                $route = route('internal_meeting.host',[$meeting->id]);
                                                                $route = str_replace('http://','https://',$route);
                                                            ?>
                                                            <a href="{{$route}}" class="host-confirm tooltip-success" data-rel='tooltip' title="Host Meeting">
                                                                <i class="fa fa-sitemap green" style="border: 1px solid #0000003d;border-radius: 50%;padding: 5px;"></i>
                                                            </a> 
                                                            @if($delete_btn == '1')
                                                                <a href="{{route('internal_meeting.delete',[$meeting->id])}}" class="delete-confirm" title="Delete Meeting">
                                                                    <i class="fa fa-trash red" style="border: 1px solid #0000003d;border-radius: 50%;padding: 5px;"></i>
                                                                </a>     
                                                            @endif   
                                                        @else
                                                            <?php
                                                                $route = route('internal_meeting.join',[$meeting->id]);
                                                                $route = str_replace('http://','https://',$route);
                                                            ?>
                                                            <a href="{{$route}}" class=" tooltip-info" data-rel='tooltip' title="Join Meeting">
                                                                <i class="fa fa-sitemap info" style="border: 1px solid #0000003d;border-radius: 50%;padding: 5px;"></i>
                                                            </a>  
                                                        @endif    
                                                                                                
                                                </td>
                                            </tr>
                                            @php($i++)
                                        @endforeach
                                    
                                    </tbody>
                                </table>
                            </div>
                        </div> 

                    </div>
                </div><!-- /.row -->

            </div><!-- /.page-content -->
    </div><!-- /.main-content -->



@endsection

@section('js')
    <!-- page specific plugin scripts -->
    @include('includes.scripts.delete_confirm')
    @include('includes.scripts.bulkaction_confirm')
    @include('includes.scripts.dataTable_scripts')
    <script>
        function loadSection($this){
              $('#section_id').prop('selectedIndex',"");
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
                      $("#section_id").html('');
                      $.each(data.section, function(key,val){
                          $("#section_id").append('<option value="'+val.id+'">'+val.semester+'</option');
                      });
                      $('.selectpicker').selectpicker('refresh');
                    }
                  }
                })
        }
        $('#meeting_form').submit(function(){
           <?php /* var fac = $('#faculty_id').val();
            var sec = $('#section_id').val();
            
            <?php 
            if($data['general_setting']->live_class_scheduling == '1'){
            ?>
                var faculty = fac.join();
                 var section_id = sec;
           <?php
            }
            else{?>
                var faculty = fac;
                var section_id = sec.join();
            <?php
            }
            
            ?> */?>

            var topic = $('input[name="topic"]').val();
            var start_time = $('input[name="start_date"]').val()+' '+$('input[name="start_time"]').val();
           <?php /* var duration = $('input[name="duration"]').val(); */?>
            var session_id = "{{Session::get('activeSession')}}";
            var branch_id = "{{Session::get('activeBranch')}}";
            var api_key = "{{$api_key}}";
            var secret_key = "{{$secret_key}}";
            var email = "{{$email}}";
            var staff_id = "{{auth()->user()->hook_id}}";
            var host_for = $('#host_for').val();
            if( api_key && secret_key && email){
                $('input[name="submit"]').attr('disabled',true);
               $.post(
                    "{{route('internal_meeting.store')}}",
                     {
                        _token:'{{ csrf_token() }}',
                        session_id  : session_id,
                        branch_id   : branch_id,
                        topic       : topic,
                        start_time  : start_time,
                        api_key     : api_key,
                        secret_key  : secret_key,
                        email       : email,
                        host_for    : host_for,
                        staff_id    : staff_id
                    },
                     function(response){
                        if(response.data.error ==1){
                            toastr.warning(response.data.msg,"Warning");
                            $('input[name="submit"]').attr('disabled',false);
                        }
                        else{
                            toastr.success('Meeting Scheduled Successfully ',"Warning");
                            location.reload();
                        }
                }); 
           }else{
                toastr.warning("You are not authorized to schedule meeting,please contact administration","Warning");
           }
          
            return false;
        })
        $("a.host-confirm").on('click', function() {
            var $this = $(this);
            bootbox.confirm({
                title: "<div class='widget-header'><h4 class='smaller'><i class='ace-icon fa fa-exclamation-triangle red'></i> Start Meeting</h4></div>",
                message: "<div class='ui-dialog-content ui-widget-content' style='width: auto; min-height: 30px; max-height: none; height: auto;'><div class='alert alert-info bigger-110'>Are you sure you want to host this meeting?</div>" +
                "<p class='bigger-110 bolder center grey'><i class='ace-icon fa fa-hand-o-right blue bigger-120'></i>Are you sure?</p>",
                size: 'small',
                    buttons: {
                        confirm: {
                            label : "<i class='ace-icon fa fa-sitemap'></i> Yes!",
                            className: "btn-danger btn-sm",
                        },
                        cancel: {
                            label: "<i class='ace-icon fa fa-remove'></i> Cancel",
                            className: "btn-primary btn-sm",
                        }
                    },
                    callback: function(result) {
                        if(result) {
                            location.href = $this.attr('href');
                        }
                    }
                }
            );
            return false;
        });
        $("a.delete-confirm").on('click', function() {
            var $this = $(this);
            bootbox.confirm({
                title: "<div class='widget-header'><h4 class='smaller'><i class='ace-icon fa fa-exclamation-triangle red'></i> Delete Meeting </h4></div>",
                message: "<div class='ui-dialog-content ui-widget-content' style='width: auto; min-height: 30px; max-height: none; height: auto;'><div class='alert alert-info bigger-110'>Are you sure you want to delete this meeting?</div>" +
                "<p class='bigger-110 bolder center grey'><i class='ace-icon fa fa-hand-o-right blue bigger-120'></i>Are you sure?</p>",
                size: 'small',
                    buttons: {
                        confirm: {
                            label : "<i class='ace-icon fa fa-trash'></i> Delete!",
                            className: "btn-danger btn-sm",
                        },
                        cancel: {
                            label: "<i class='ace-icon fa fa-remove'></i> Cancel",
                            className: "btn-primary btn-sm",
                        }
                    },
                    callback: function(result) {
                        if(result) {
                            location.href = $this.attr('href');
                        }
                    }
                }
            );
            return false;
        });
    </script>
@endsection