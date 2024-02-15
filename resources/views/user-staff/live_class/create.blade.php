@extends('user-staff.layouts.master')

@section('css')
    <!-- page specific plugin styles -->
@endsection

@section('content')

    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                <div class="page-header">
                    @php
                         $panel = 'Live Class';
                    @endphp
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
                        <h4 class="header large lighter blue"><i class="fa fa-search" aria-hidden="true"></i>&nbsp;Schedule {{$panel}} </h4>
                        <form id="meeting_form"  action="#">
                            <div class="row form-group">
                                {!! Form::label('faculty',env('course_label'), ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                    {{Form::select('faculty_id',$data['faculty'],null, ['class'=>'form-control','required','id'=>'faculty_id','onChange'=>'loadSection(this)'])}}
                                </div>
                            </div>
                            <div class="row form-group">
                                {!! Form::label('section','Section', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                    {{Form::select('section_id',[""=>'Select'],null, ['class'=>'form-control','required','id'=>'section_id'])}}
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
                            <div class="row form-group">
                                {!! Form::label('duration','Duration (in min.)', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                    {{Form::number('duration',null,['class'=>'form-control','max'=>60,'required'])}}
                                </div>
                            </div>
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
                                        <th>{{env('course_label')}}</th>
                                        <th>Section</th>
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
                                                <td>{{ $meeting->faculty }}</td>
                                                <td>{{ $meeting->section }}</td>
                                                <td>{{ Carbon\Carbon::parse($meeting->start_time)->format('d-M-Y') }}</td>
                                                <td>{{ Carbon\Carbon::parse($meeting->start_time)->format('H:i') }}</td>
                                                <td>
                                                     @if($join_time > $current_date)
                                                       <b style="border: 1px solid blue;padding: 2px 5px;border-radius: 3px;color: blue;font-weight: 300">Upcoming</b>
                                                    
                                                    @elseif($current_date < $end_time && $current_date > $join_time)
                                                        <b style="border: 1px solid green;padding: 2px 5px;border-radius: 3px;color: green;font-weight: 300">Active</b>
                                                    @else
                                                        <b style="border: 1px solid red;padding: 2px 5px;border-radius: 3px;color: red;font-weight: 300">Exipred</b>
                                                        
                                                    @endif       
                                                </td>
                                                <td style="text-align: center;">
                                                        
                                                        <a href="{{route('user-staff.host_class',[$meeting->id,$meeting->topic])}}" class="host-confirm" title="Host Live Class">
                                                            <i class="fa fa-sitemap" style="border: 1px solid #0000003d;border-radius: 50%;padding: 5px;"></i>
                                                        </a>                                            
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
                      $("#section_id").html('').append('<option value="">--Select Section--</option');
                      $.each(data.section, function(key,val){
                          $("#section_id").append('<option value="'+val.id+'">'+val.semester+'</option');
                      });
                    }
                  }
                })
        }
        $('#meeting_form').submit(function(){
            var faculty = $('#faculty_id').val();
            var section_id = $('#section_id').val();
            var topic = $('input[name="topic"]').val();
            var start_time = $('input[name="start_date"]').val()+' '+$('input[name="start_time"]').val();
            var duration = $('input[name="duraton"]').val();
            var session_id = "{{Session::get('activeSession')}}";
            var branch_id = "{{Session::get('activeBranch')}}";
            var api_key = "{{$api_key}}";
            var secret_key = "{{$secret_key}}";
            var email = "{{$email}}";
            var staff_id = "{{auth()->user()->hook_id}}";
            if( api_key && secret_key && email){
                $('input[name="submit"]').attr('disabled',true);
               $.post(
                    "{{route('live_class.store')}}",
                     {
                        _token:'{{ csrf_token() }}',
                        faculty_id  : faculty,
                        section_id  : section_id,
                        session_id  : session_id,
                        branch_id   : branch_id,
                        topic       : topic,
                        start_time  : start_time,
                        duration    : duration,
                        api_key     : api_key,
                        secret_key  : secret_key,
                        email       : email,
                        staff_id    : staff_id
                    },
                     function(response){
                        if(response.data.error ==1){
                            toastr.warning(response.data.msg,"Warning");
                            $('input[name="submit"]').attr('disabled',false);
                        }
                        else{
                            toastr.success('Live Class Scheduled Successfully ',"Warning");
                            location.reload();
                        }
                }); 
           }else{
                toastr.warning("You are not authorized to schedule live classes,please contact administration","Warning");
           }
          
            return false;
        })
        $("a.host-confirm").on('click', function() {
            var $this = $(this);
            bootbox.confirm({
                title: "<div class='widget-header'><h4 class='smaller'><i class='ace-icon fa fa-exclamation-triangle red'></i> Start Live Class </h4></div>",
                message: "<div class='ui-dialog-content ui-widget-content' style='width: auto; min-height: 30px; max-height: none; height: auto;'><div class='alert alert-info bigger-110'>Are you sure you want to host this live class.</div>" +
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
    </script>
@endsection