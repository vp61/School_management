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
                        @include($view_path.'.includes.breadcrumb-primary')
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                           Change Password
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row" style="text-align: center;">
                    
                    <div class="col-xs-3 "></div>
                    <div class="col-xs-6">
                    @include('includes.flash_messages')
                    @include('includes.validation_error_messages')
                    <!-- PAGE CONTENT BEGINS -->
                        <div class="form-horizontal">
                            <h4 class="header larde lighter blue"><i class="fa fa-pencil"></i> Change Password</h4>
                            {!!Form::open(['route'=>'user.changePassword','method'=>'POST','class'=>'form-horizontal'])!!}
                            <div class="form-group">
                                {!!Form::label('current','Current Password',['class'=>'col-sm-4 control-label'])!!}
                                <div class="col-sm-8">
                                    <input type="Password" name="current_password" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group">
                                {!!Form::label('new','New Password',['class'=>'col-sm-4 control-label'])!!}
                                <div class="col-sm-8">
                                    <input type="Password" name="new_password" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group">
                                {!!Form::label('confirm','Confirm Password',['class'=>'col-sm-4 control-label'])!!}
                                <div class="col-sm-8">
                                    <input type="Password" name="password_confirmation" class="form-control" required>
                                </div>

                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <button class="btn btn-info pull-right" type="submit">
                                        Update
                                    </button>
                                </div>
                            </div>
                            {!!Form::close()!!}
                        </div>
                    </div><!-- /.col -->
                    <div class="col-xs-3"></div>
                </div><!-- /.row -->
               
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
@endsection


