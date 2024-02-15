@extends('layouts.master')
@section('content')
<div class="main-content">
	<div class="main-content-inner">
		<div class="page-content">
			@include('layouts.includes.template_setting')
			<div class="page-header">
				<h1>
					{{$panel}}
					<small>
						 <i class="ace-icon fa fa-angle-double-right"></i>
						 Details
					</small>
				</h1>	
			</div>
			<div class="row">
				<div class="col-sm-12">
					@include('includes.flash_messages')
					@include($view_path.'.includes.search_form')
					@include($view_path.'.includes.table')
				</div>
			</div>
				
		</div>
	</div>
	
</div>
@endsection
@section('js')
<script src="{{ asset('assets/js/bootbox.js') }}"></script> 
@include('includes.scripts.delete_confirm')
	@include('includes.scripts.dataTable_scripts')
@endsection