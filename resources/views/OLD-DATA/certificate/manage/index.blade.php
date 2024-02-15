@extends('layouts.master')
@section('css')
    <!-- page specific plugin styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}" />

@endsection

@section('content')
	<div class="main-content">
		<div class="main-content-inner">
			<div class="page-content">
				 @include('layouts.includes.template_setting')
				 <div class="page-header">
				 	<h1>
				 		Certificate Manager
				 		<small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                          Manage
                        </small>
				 	</h1>
				 	
				 </div>
                   @include('includes.flash_messages')
                  	@include($view_path.'.includes.buttons')
				 <div class="row">
				 	<div class="col-md-12">
				 		
				 		<div class="row">
				 			<div class="col-md-4">
				 				<h4 class="header large lighter blue"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;Create Certificate</h4>
				 				@include($view_path.'.manage.includes.form')	
				 			</div>
				 			<div class="col-md-8">
				 				<h4 class="header large lighter blue"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;Certificate List</h4>
				 				
						 			@include($view_path.'.manage.includes.table')
						 	
				 			</div>
				 		</div>
				 		
				 	</div>
				 	
				 </div>
				
			</div><!-- /.page-content -->
			
		</div>
		
	</div><!-- /.main-content -->
	  
@endsection

@section('js')
	 @include('includes.scripts.dataTable_scripts')
@endsection