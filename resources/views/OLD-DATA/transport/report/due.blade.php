@extends('layouts.master')


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
                            Due Report
                        </small>
                    </h1>
                </div><!-- /.page-header -->
                <div class="row">
                    @include('transport.includes.buttons')
                    <div class="col-xs-12 ">
                     @include($view_path.'.includes.buttons')
                        <hr class="hr-6">
                        @include('includes.flash_messages')
                        @include('includes.validation_error_messages')
                        {!! Form::open(['route' => $base_route.'.duereport', 'method' => 'GET', 'class' => 'form-horizontal',
                                        'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}
                        @include($view_path.'.includes.due_search')
                        {!! Form::close() !!}
                        <!-- PAGE CONTENT BEGINS -->
                            
                        </div>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
    @endsection
