@extends('user-student.layouts.master')

@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                @include('layouts.includes.template_setting')
                <div class="page-header">
                    <h1>
                        Timetable
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Today's Schedule
                        </small>
                    </h1>

                </div><!-- /.page-header -->

                <div class="row">
                    <div class="col-xs-12 ">
                        @include('includes.flash_messages')
                        <!-- PAGE CONTENT BEGINS -->
                        <div class="form-horizontal">
                            @include($view_path.'.timetable.includes.table')
                        </div>
                    </div><!-- /.col -->
                </div><!-- /.row -->

                
            </div>
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
@endsection
