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
                            Student Attaendance List
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    @include('includes.flash_messages')
                    <div class="col-md-12 col-xs-12">
                        <h4 class="header large lighter blue">&nbsp;Meeting Details</h4>
                        <table class="table table-hover table-striped">
                            <tr>
                                <td class="label-info white">Topic</td>
                                <td class="" colspan="3">{{$meeting->topic}}</td>
                            </tr>
                            <?php 
                                $fac = explode(',',$meeting->faculty_id);
                                $faculty =[];
                                foreach ($fac as $key => $value) {
                                    if(isset($drop['faculty'][$value])){
                                        $faculty[] = $drop['faculty'][$value];
                                    }
                                }
                                $sec = explode(',',$meeting->section_id);
                                $section =[];
                                foreach ($sec as $key => $value) {
                                    if(isset($drop['section'][$value])){
                                        $section[] = $drop['section'][$value];
                                    }
                                }
                            ?>
                            <tr>
                                <td class="label-info white">
                                    {{env('course_label')}}
                                </td>
                                <td>
                                    {{implode(',',array_unique($faculty))}}
                                </td>
                                <td class="label-info white">
                                   Section/Sem.
                                </td>
                                <td>
                                    {{implode(',',array_unique($section))}}
                                </td>
                            </tr>
                            <tr>
                                <td class="label-info white">Start Date </td>
                                <td>{{\Carbon\Carbon::parse($meeting->start_time)->format('d-M-Y  h:i A ')}}</td>
                                <td class="label-info white">Duration </td>
                                <td>{{$meeting->duration}} Min.</td>
                            </tr>
                        </table>
                    </div>
                        
                    <div class="col-md-12 col-xs-12">
                        <div class="row">
                        <div class="col-xs-12">
                            @include('includes.data_table_header')
                            <!-- div.table-responsive -->
                            <div class="table-responsive">
                                <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>S.N.</th>
                                        <th>Student Name</th>
                                        <th>Reg No</th>
                                        <th>{{env('course_label')}}</th>
                                        <th>Section</th>
                                        <th>Meeting Status</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($data))
                                        @php($i=1)
                                            @foreach($data as $k=>$v)
                                            @if(isset($v->std))
                                                <tr>
                                                    <td>{{$i}}</td>
                                                    <td>
                                                        {{$v->std->name}}
                                                    </td>
                                                    <td>
                                                        {{$v->std->reg_no}}
                                                    </td>
                                                    <td>
                                                        {{$v->std->faculty}}
                                                    </td>
                                                    <td>
                                                        {{$v->std->section}}
                                                    </td>
                                                    <td>

                                                        @if(isset($v->data))
                                                            @foreach($v->data as $val)
                                                            
                                                                <?php 

                                                                    $status=$val->attendance_status==1?'Joined':'Left';
                                                                    $color=$val->attendance_status==1?'success':'danger';
                                                                ?>
                                                                <div>
                                                                 
                                                                    <p class="text-{{$color}}">{{$status}} at {{\Carbon\Carbon::parse($val->created_at)->format('h:i:s A d-M-Y')}}</p>
                                                                    <p style="background: #f9e4c369;{{($val->device_id || $val->login_ip)?"border-bottom: 1px solid;":""}}">
                                                                        {{$val->device_id?'Device Id -'. $val->device_id:''}} {{$val->login_ip? 'IP -'. $val->login_ip:''}}
                                                                        {{$val->device_name? 'Device Name -'. $val->device_name:''}}  {{$val->manufacturer?'Manufacturer -'. $val->manufacturer:''}}
                                                                    </p>
                                                                </div>
                                                                
                                                            @endforeach
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif    
                                            @php($i++)
                                            @endforeach
                                        @endif
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
@endsection