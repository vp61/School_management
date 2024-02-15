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
                            Observe Class
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    @include('includes.flash_messages')
                    @if($data['observer']->observer=='Yes')
                        <div class="col-md-4">
                            <h4 class="header large lighter blue"><i class="fa fa-search" aria-hidden="true"></i>&nbsp;Search {{$panel}}</h4>
                            <form id="meeting_form"  action="{{route('live_class.observe_class')}}">
                                <div class="row form-group">
                                    {!! Form::label('faculty',env('course_label'), ['class' => 'col-sm-4 control-label']) !!}
                                    <div class="col-sm-8">
                                        <!-- teacher access -->
                                            {{Form::select('faculty',$data['faculty'],null, ['class'=>'form-control selectpicker','id'=>'faculty_id','onChange'=>'loadSection(this)','data-live-search'=>'true'])}}
                                        <!-- teacher access -->
                                    </div>
                                </div>
                                <div class="row form-group">
                                    {!! Form::label('section','Section', ['class' => 'col-sm-4 control-label']) !!}
                                    <div class="col-sm-8">
                                        {{Form::select('section',$data['section'],null, ['class'=>'form-control','id'=>'section_id'])}}
                                    </div>
                                </div>
                                <div class="row form-group">
                                    {!! Form::label('start_date','From Date', ['class' => 'col-sm-4 control-label']) !!}
                                    <div class="col-sm-8">
                                        {{Form::date('from_date',null,['class'=>'form-control'])}}
                                    </div>
                                </div>
                                <div class="row form-group">
                                    {!! Form::label('start_time','To Date', ['class' => 'col-sm-4 control-label']) !!}
                                    <div class="col-sm-8">
                                        {{Form::date('to_date',null,['class'=>'form-control'])}}
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-sm-5 col-sm-offset-7">
                                        <input type="submit" name="submit" value="Search" class="btn btn-info">
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
                                            <th>Created By</th>
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
                                                        {{$meeting->staff_name}}
                                                    </td>
                                                    <td>
                                                        {{ $meeting->topic }}
                                                    </td>
                                                    <?php 
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
                                                    ?>
                                                    <td>{{implode(', ',array_unique($faculty))}}</td>
                                                    <td>{{ implode(', ',array_unique($section)) }}</td>
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
                                                    <td style="text-align: center;" nowrap>
                                                        <?php
                                                            $route = route('live_class.observe',[$meeting->id]);
                                                            $route = str_replace('http://','https://',$route);
                                                        ?>
                                                            <a href="{{$route}}" class="host-confirm" title="Observe Live Class" data-rel='tooltip' class="tooltip-info">
                                                                <i class="fa fa-eye green" style="border: 1px solid #0000003d;border-radius: 50%;padding: 5px;"></i>
                                                            </a>  
                                                            <a href="{{route('live_class.list_attendance',[$meeting->id])}}" class="" title="Live Class Attendance">
                                                                <i class="fa fa-list" style="border: 1px solid #0000003d;border-radius: 50%;padding: 5px;"></i>
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
                    @else
                        <h3 class="red">You are not authorised to observe live classes.</h3>
                    @endif    
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
       
    </script>
@endsection