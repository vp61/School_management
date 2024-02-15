@extends('layouts.master')

@section('css')
    <!-- page specific plugin styles -->
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
                            Import
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    <div class="col-xs-12 ">
                        @include($view_path.'.includes.buttons')
                        @include('includes.flash_messages')
                        @include('includes.validation_error_messages')
                        <!-- PAGE CONTENT BEGINS -->
                        <h3 class="header large lighter blue"> Import Course</h3>
                        <div class="form-horizontal">
                            {!! Form::open(['route' => $base_route.'.import-course', 'method' => 'POST', 'class' => 'form-horizontal',
                             'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}
                                <hr>         
                                <a href="{{ asset('assets/csv-template/CSV-COURSE-IMPORT-FORMAT.csv') }}" target="_blank" class="easy-link-menu"><h4><i class="fa fa-download"></i> CSV Template for Bulk Course Import</h4></a>
                                <hr>
                                <div class="form-group">
                                    {!! Form::label('file', 'Select CSV File', ['class' => 'col-sm-2 control-label']) !!}
                                    <div class="col-sm-4">
                                        {!! Form::file('file', null, ["class" => "form-control border-form", "required"]) !!}
                                        @include('includes.form_fields_validation_message', ['name' => 'file'])
                                    </div>
                                    
                                    <div class="col-sm-6 text-right">
                                        <button class="btn btn-info" type="submit" id="filter-btn">
                                            <i class="fa fa-upload"></i>
                                            Import
                                        </button>
                                    </div>
                                </div>
                            {!! Form::close() !!}
                        </div>
                        <div class="hr hr-18 dotted hr-double"></div>
                        <h3 class="header large lighter blue"> Import Student</h3>
                        <div class="form-horizontal">
                            {!! Form::open(['route' => $base_route.'.import-student', 'method' => 'POST', 'class' => 'form-horizontal',
                             'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}
                                <hr> 
                                
                                
                                        
                                <a href="{{ asset('assets/csv-template/CSV-STUDENT-IMPORT-FORMAT.csv') }}" target="_blank" class="easy-link-menu"><h4><i class="fa fa-download"></i> CSV Template for Bulk Student Import</h4></a>
                                <hr>
                                <div class="form-group">
                                    {!! Form::label('file', 'Select CSV File', ['class' => 'col-sm-2 control-label']) !!}
                                    <div class="col-sm-4">
                                        {!! Form::file('file', null, ["class" => "form-control border-form", "required"]) !!}
                                        @include('includes.form_fields_validation_message', ['name' => 'file'])
                                    </div>
                                    {!! Form::label('file', 'Select CSV File', ['class' => 'col-sm-2 control-label']) !!}
                                    <div class="col-sm-4">
                                    
                                            <label class="radio-inline"><input type="radio" name="reg_check" required  value='1'>Generate New Registration NO.</label>
                                        
                                            <label class="radio-inline"><input type="radio" name="reg_check"  value='0'>Use Old Registration NO.</label>
                               
                                        
                                    </div>
                                </div>

                                <div class="clearfix form-actions">
                                    <div class="col-md-12 align-right">
                                        <button class="btn btn-info" type="submit" id="filter-btn">
                                            <i class="fa fa-upload"></i>
                                            Import
                                        </button>
                                    </div>
                                </div>

                                <div class="hr hr-18 dotted hr-double"></div>
                            {!! Form::close() !!}
                        </div>
                        </div>
                    </div><!-- /.col -->
                </div><!-- /.row -->

            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
    @endsection


@section('js')

@endsection