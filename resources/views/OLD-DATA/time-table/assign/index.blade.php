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
                       {{$panel}} Manager
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                          Assign Subjects
                        </small>
                    </h1>
                </div>
                <div class="row">
                	<div class="col-md-12">
                		@include('time-table.includes.buttons')
                	</div>
                </div>
                @include('includes.flash_messages')
                <div class="row">
                	<div class="col-md-4">
                		@include($view_path.'.includes.form')
                	</div>
                	<div class="col-md-8">
                		@include($view_path.'.includes.table')
                	</div>
                </div>
            </div> 
        </div>
    </div>          

@endsection

@section('js')
 @include('includes.scripts.dataTable_scripts')
@endsection