@extends('layouts.master')
@section('content')
	<div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                @include('layouts.includes.template_setting')

                <div class="page-header">
                    <h1>
                        Complain
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Details
                        </small>
                    </h1>
                </div>
                @include('includes.flash_messages')
                <div class="row">

                	<div class="col-sm-12 text-right">
                        
                         <a class="btn-primary btn-sm" href="{{route('frontdesk.complain.add')}}"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp; Add Complain</a>
                	</div>
                </div>
                @include($view_path.'.includes.search_form')
                
                @include($view_path.'.includes.table')
            </div>
        </div>
    </div>            
@endsection
@section('js')
    <script src="{{ asset('assets/js/bootbox.js') }}"></script> 
<script type=""> 
          $(".statusChange").on('click', function() { 
                var $this = $(this);
                bootbox.confirm({
                        title: "<div class='widget-header'><h4 class='smaller'><i class='ace-icon fa fa-exclamation-triangle red'></i> Delete Confirmation</h4></div>",
                        message: "<div class='ui-dialog-content ui-widget-content' style='width: auto; min-height: 30px; max-height: none; height: auto;'><div class='alert alert-info bigger-110'>" +
                        "The status of this record will be changed</div>" +
                        "<p class='bigger-110 bolder center grey'><i class='ace-icon fa fa-hand-o-right blue bigger-120'></i>Are you sure?</p>",
                        size: 'small',
                        buttons: {
                            confirm: {
                                label : "<i class='ace-icon fa fa-history'></i> Change Status",
                                className: "btn-danger btn-sm",
                            },
                            cancel: {
                                label: "<i class='ace-icon fa fa-remove'></i> Cancel",
                                className: "btn-primary btn-sm",
                            }
                        },
                        callback: function(result) {
                            if(result) {
                                location.href = $this.attr('href');
                            }
                        }
                    }
                );
                return false;
            });
</script>
    @include('includes.scripts.delete_confirm')
    @include('includes.scripts.dataTable_scripts')
@endsection