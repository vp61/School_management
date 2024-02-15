@extends('user-student.layouts.master')

@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                @include('layouts.includes.template_setting')
                <div class="page-header">
                    <h1>
                       Meeting
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Schedule
                        </small>
                    </h1>

                </div><!-- /.page-header -->

                <div class="row">
                    <div class="col-xs-12 ">
                        @include('includes.flash_messages')
                        <!-- PAGE CONTENT BEGINS -->
                        <div class="form-horizontal">
                            <div class="table-responsive">
                                <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>S.N.</th>
                                        <th>Topic</th>
                                        <th>Date</th>
                                        <th>Start Time</th>
                                        <!-- <th>Duration</th> -->
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
                                                <td>{{ $meeting->topic }}</td>
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
                                                    @if($join_time > $current_date)
                                                        <?php
                                                            $route = route('user-student.meeting.join',[$meeting->id]);
                                                            $route = str_replace('http://','https://',$route);
                                                        ?>
                                                        <a href="{{$route}}"  title="Join Meeting" data-rel='tooltip' class="tooltip-info">
                                                            <i class="fa fa-sitemap" style="border: 1px solid #0000003d;border-radius: 50%;padding: 5px;"></i>
                                                        </a>                                      
                                                    @elseif($current_date < $end_time && $current_date > $join_time)
                                                        <?php
                                                                $route = route('user-student.meeting.join',[$meeting->id]);
                                                                $route = str_replace('http://','https://',$route);
                                                        ?>
                                                        <a href="{{$route}}"  title="Join Meeting">
                                                            <i class="fa fa-sitemap" style="border: 1px solid #0000003d;border-radius: 50%;padding: 5px;"></i>
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
                    </div><!-- /.col -->
                </div><!-- /.row -->

                
            </div>
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
@endsection
