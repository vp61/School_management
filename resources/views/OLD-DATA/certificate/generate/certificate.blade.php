@extends('layouts.master')
	
	@section('css')
		<style type="text/css" media="screen, print">

			 @media print{
			 	.bgImg{
				background-image:  url('{{asset($data['certificate']->bg_image)}}');
				background-size: cover;
				-webkit-print-color-adjust: exact; 
			 	}
			 }
		</style>
	@endsection
@section('content')
	<div class="main-content">
		<div class="main-content-inner">
			<div class="page-content">
				 @include('layouts.includes.template_setting')
				<div class="container bgImg"  style="background-image:  url('{{asset($data['certificate']->bg_image)}}');
				background-size: cover;" >
					<div class="row">
						<div class="col-xs-12 col-sm-12" style="height:{{$data['certificate']->header_img_height}}px;">
							@if(!empty($data['certificate']->header))
								<img src="{{ asset($data['certificate']->header) }}" style="height: {{$data['certificate']->header_img_height}}px;width: 100%;padding-top: {{$data['certificate']->header_img_ptop}}px;padding-bottom:{{$data['certificate']->header_img_pbottom}}px ;padding-left:{{$data['certificate']->header_img_pleft}}px ;padding-right:{{$data['certificate']->header_img_pright}}px;">
							@endif
						</div>
					</div>
					<div class="row">
						<div class="col-sm-2">
							@if(!empty($data['student']->student_image) && $data['certificate']->std_photo==1)
								<img src="{{ asset($data['student']->image) }}" style="height: 10vh;">
							@endif
						</div>
					</div>
					<div class="row" style="height: 5vh;">
						<div class="col-sm-4 col-xs-4 pull-left">
							{{$data['certificate']->left_header}}
						</div>
						
						<div class="col-sm-4 col-xs-4 pull-right" style="text-align: right;">
							{{$data['certificate']->right_header}}
						</div>
					</div>
					<div class="row" style="height: 5vh;">
						<div class="col-sm-12 col-xs-12 " style="text-align: center;">
							{{$data['certificate']->center_header}}
						</div>
					</div>
					<div class="row" >
						<div class="col-sm-12" style="text-align: center;height: {{$data['certificate']->body_height}}px;padding-top: {{$data['certificate']->body_ptop}}px;padding-bottom: {{$data['certificate']->body_pbottom}}px;padding-left: {{$data['certificate']->body_pleft}}px;padding-right: {{$data['certificate']->body_pright}}px">
							{{$data['body']}}
						</div>
					</div>
					<div class="row" style="margin-top: 40px;">
						<div class="col-sm-3 col-xs-3 pull-left">
							{{$data['certificate']->left_footer}}
						</div>
						<div class="col-sm-6 col-xs-6 " style="text-align: center;">
							{{$data['certificate']->center_footer}}
						</div>
						<div class="col-sm-3 col-xs-3 pull-right" style="text-align: right;">
							{{$data['certificate']->right_footer}}
						</div>
					</div>
				</div>
				
			</div><!-- /.page-content -->
			
		</div>
		
	</div><!-- /.main-content -->
	  
@endsection

@section('js')
	<script type="text/javascript">window.print();</script>
@endsection