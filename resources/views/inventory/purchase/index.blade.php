@extends('layouts.master')

@section('css')
<style>
	.strong{
		font-weight: 600 !important;
	}
	.noborder{
		border: none;
	}
</style>
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
                            Search
                        </small>
                    </h1>
                </div><!-- /.page-header -->
                <div class="text-right" >
                   <a class="btn-primary btn-sm" href="{{ route('inventory.product.add') }}"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;Add Product</a>
               </div>
               <hr>
                <div class="row">
                    <div class="col-xs-12 ">
                    
                    <!-- PAGE CONTENT BEGINS -->
                   
                        @include('includes.flash_messages')
                        @include('includes.validation_error_messages')
                        <div class="row">
                             <div class="col-xs-12">
                              @include($view_path.'.includes.search_form')
                            </div>
                            <div class="col-xs-12">
                            		@include($view_path.'.includes.table')
                            </div>
                           
                        </div>
                        <div class="hr hr-18 dotted hr-double"></div>
                       
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
@endsection


@section('js')
 
 @include('includes.scripts.dataTable_scripts')
 @include('includes.scripts.delete_confirm')
 <script type="">
  $('.search_show_icon').click(function(){
    $('.search_show_icon').toggle();
    $('.hidden_search_form').slideToggle();
  });
	
 </script>
@endsection