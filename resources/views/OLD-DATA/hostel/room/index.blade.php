@extends('layouts.master')

@section('css')
    <!-- page specific plugin styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.custom.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datepicker3.min.css') }}" />
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
                            Add
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                   <div class="col-xs-12">
                        @include('hostel.includes.status_buttons')
                   </div>
                    <div class="col-xs-12 ">
                    @include('includes.flash_messages')
                    @include($view_path.'.includes.buttons')
                    <!-- PAGE CONTENT BEGINS -->
                        <div class="form-horizontal">
                            <div class="col-md-4">
                            @if(isset($data['row'])) 
                             {!! Form::open(['route' => [$base_route.'.room.edit',$id,$roomId], 'method' => 'POST', 'class' => 'form-horizontal',
                                        'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}
                            @include($view_path.'.includes.edit-form')
                            {!! Form::close() !!}  
                               
                            @else
                             {!! Form::open(['route' => [$base_route.'.room.add',$id], 'method' => 'POST', 'class' => 'form-horizontal',
                                        'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}
                            @include($view_path.'.includes.form')
                            {!! Form::close() !!}
                            @endif
                            </div>
                            <div class="col-md-8">
                                 @include($view_path.'.includes.table')
                            </div>
                          
                            <div class="hr hr-18 dotted hr-double"></div>
                        </div>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div>
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
    @endsection


@section('js')
    @include('includes.scripts.jquery_validation_scripts')
    <!-- inline scripts related to this page -->
    <script type="text/javascript">
        /*Change Field Value on Capital Letter When Keyup*/
        $(function() {
            $('.upper').keyup(function() {
                this.value = this.value.toUpperCase();
            });
        });

        $(document).ready(function () {

        });
        function loadFloor($this) {
            $.ajax({
                type: 'POST',
                url: '{{ route('hostel.room.floor') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    block: $this.value
                },
                success: function (response) {

                    var data = $.parseJSON(response);
                    if (data.error) {
                        $.notify(data.message, "warning");
                    } else {

                        $('#floor').html('').append('<option value>--Select Floor --</option>');
                        $.each(data.floor, function(key,valueObj){
                            $('#floor').append('<option value="'+valueObj.id+'">'+valueObj.title+'</option>');
                        });
                    }
                }
            });

        }

        
    </script>
    @include($view_path.'.includes.modal_values_script')
    @include('includes.scripts.inputMask_script')
    @include('includes.scripts.delete_confirm')
    @include('includes.scripts.bulkaction_confirm')
    @include('includes.scripts.dataTable_scripts')
    @include('includes.scripts.datepicker_script')
@endsection