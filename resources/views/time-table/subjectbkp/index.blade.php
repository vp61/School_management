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
                          Add Subjects
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
                     <h4 class="header large lighter blue"><i class="fa fa-plus"></i> Add</h4>
                        {!!Form::open(['route'=>$base_route.'.store','method'=>'POST','class'=>'form-horizontal','id' => 'validation-form', "enctype" => "multipart/form-data"])!!}
                        		  @include($view_path.'.includes.form')
                        {!!Form::close()!!}
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