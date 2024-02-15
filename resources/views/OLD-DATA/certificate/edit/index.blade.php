@extends('layouts.master')
@section('css')
    <!-- page specific plugin styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-toggle.min.css')}}">
@endsection

@section('content')
	<div class="main-content">
		<div class="main-content-inner">
			<div class="page-content">
				 @include('layouts.includes.template_setting')
				 <div class="page-header">
				 	<h1>
				 		@include($view_path.'.edit.includes.breadcrumb-primary')
				 		<small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                           Transfer Certificate
                        </small>
				 	</h1>
				 	  
				 </div>
				 <div class="row">
				 	<div class="col-md-12">
				 		{!!Form::open(['route'=>[$base_route.'.update',$data['value']->id],'method'=>'POST','class'=>'form-horizontal','id'=>'validation-form',"enctype"=>"multipart/form-data"])!!}
                            @include($view_path.'.edit.includes.form')
                        {!!Form::close()!!} 
				 	</div>
				 	
				 </div>
				
			</div><!-- /.page-content -->
			
		</div>
		
	</div><!-- /.main-content -->
	  
@endsection

@section('js')
<script src="{{ asset('assets/js/select2.min.js') }}"></script>
<script src="{{asset('assets/js/bootstrap-toggle.min.js')}}"></script>

@endsection