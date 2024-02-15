@extends('layouts.master')

@section('css')
    <!-- page specific plugin styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.custom.min.css') }}" />
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
                            Member Detail
                        </small>
                    </h1>
                </div><!-- /.page-header -->
                <div class="row">
                    @include('hostel.includes.buttons')
                    <div class="col-xs-12 ">
                     @include($view_path.'.includes.buttons')
                        <hr class="hr-6">
                        @include('includes.flash_messages')
                        @include('includes.validation_error_messages')
                        {!! Form::open(['route' => $base_route, 'method' => 'GET', 'class' => 'form-horizontal',
                                        'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}
                        @include($view_path.'.includes.search_form')
                        {!! Form::close() !!}
                        <!-- PAGE CONTENT BEGINS -->
                            @include($view_path.'.includes.table')
                            @include($view_path.'.includes.renew_model')
                            @include($view_path.'.includes.shift_model')
                       
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
    @endsection


@section('js')

    <!-- inline scripts related to this page -->
    <script type="text/javascript">
        $(document).ready(function () {

            /*Change Field Value on Capital Letter When Keyup*/
            $(function() {
                $('.upper').keyup(function() {
                    this.value = this.value.toUpperCase();
                });
            });
            /*end capital function*/

            $('#filter-btn').click(function () {
                var flag = false;
                var user_type = $('select[name="user_type"]').val();
                var reg_no = $('input[name="reg_no"]').val();
                var status = $('select[name="status"]').val();
                var hostel = $('select[name="hostel"]').val();
                var room = $('select[name="room_select"]').val();
                var bed = $('select[name="bed_select"]').val();

                if (user_type !== '' & user_type >0) {
                    flag = true;
                }else{
                    toastr.warning('Please Select Type', 'Warning:')
                    return false;
                }

                if (reg_no !== '') {
                    flag = true;
                }


                if (status !== ''  && status > 0) {
                    flag = true;
                }

                if (hostel !== '' && hostel > 0 ) {
                    flag = true;
                }

                if (room !== '' && room > 0 ) {
                    flag = true;
                }

                if (bed !== '' && bed > 0 ) {
                    flag = true;
                }

                if(flag){
                    return true;
                }else{
                    toastr.warning('No any Target to Search', 'Warning:')
                    return false;
                }
            });

            $('#shift-btn').click(function () {
                var flag = false;
                var hostel = $('select[name="hostel_bulk"]').val();
                if (hostel > 0 ) {
                    return true;
                }else{
                    toastr.info('Please Select Hostel', 'Info:');
                    return false;
                }
            });

        });

        function loadAllRooms($this) {
            $.ajax({
                type: 'POST',
                url: '{{ route('hostel.bed.find-rooms') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    hostel_id: $this.value
                },
                success: function (response) {
                    var data = $.parseJSON(response);
                    if (data.error) {
                        $.notify(data.message, "warning");
                    } else {
                        $('.room_select').html('').append('<option value="0">Select Room...</option>');
                        $.each(data.rooms, function(key,valueObj){
                            $('.room_select').append('<option value="'+valueObj.id+'">'+valueObj.room_number+'</option>');
                        });
                    }
                }
            });

        }

        function loadAllBeds($this) {
            $.ajax({
                type: 'POST',
                url: '{{ route('hostel.find-beds') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    room_id: $this.value
                },
                success: function (response) {
                    var data = $.parseJSON(response);
                    if (data.error) {
                        $.notify(data.message, "warning");
                    } else {
                        $('.bed_select').html('').append('<option value="0">Select Beds...</option>');
                        $.each(data.beds, function(key,valueObj){
                            $('.bed_select').append('<option value="'+valueObj.id+'">'+valueObj.bed_number+'</option>');
                        });
                    }
                }
            });

        }

         function loadBlock($this){
            $.ajax({
                type: 'POST',
                url: '{{ route('hostel.resident.find-block') }}',
                data: {
                    _token: '{{csrf_token()}}',
                    hostel_id: $this.value
                },
                success: function (response){
                   
                    var data = $.parseJSON(response);
                        if(data.error){
                            $.notify(data.message,"warning");
                        } else{
                            $('#bed').html('').append('<option value="">Select</option>');
                            $('#room').html('').append('<option value="">Select</option>');
                            $('#floor').html('').append('<option value="">Select</option>');
                             $('.block_select').html('').append('<option value="">Select Block...</option>')
                             $.each(data.block,function(key,valueObj){
                                 $('.block_select').append('<option value="'+valueObj.id+'">'+valueObj.title+'</option>');
                             });
                        }
                }
            });
        }
        function loadFloor($this){
          
            $.ajax({
                type: 'POST',
                url: '{{ route('hostel.resident.find-floor') }}',
                data: {
                    _token: '{{csrf_token()}}',
                    block_id: $this.value
                },
                success: function (response){
                    var data = $.parseJSON(response);
                        if(data.error){
                            $.notify(data.message,"warning");
                        } else{
                             $('#bed').html('').append('<option value="">Select</option>');
                            $('#room').html('').append('<option value="">Select</option>');
                             $('.floor_select').html('').append('<option value="">Select Block...</option>')
                             $.each(data.floor,function(key,valueObj){
                                 $('.floor_select').append('<option value="'+valueObj.id+'">'+valueObj.title+'</option>');
                             });
                        }
                }
            });
        }

        function loadRooms($this) {
            var hostel = document.getElementById('hostel');
            var block = document.getElementById('block');
            $.ajax({
                type: 'POST',
                url: '{{ route('hostel.resident.find-rooms') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    floor_id: $this.value,
                },
                success: function (response) {
                    var data = $.parseJSON(response);
                    if (data.error) {
                        $.notify(data.message, "warning");
                    } else {
                        $('#bed').html('').append('<option value="">Select</option>');
                        $('.room_select').html('').append('<option value="">Select Room...</option>');
                        $.each(data.rooms, function(key,valueObj){
                            $('.room_select').append('<option value="'+valueObj.id+'">'+valueObj.room_number+'</option>');
                        });
                    }
                }
            });

        }

        function loadBeds($this) {
            $.ajax({
                type: 'POST',
                url: '{{ route('hostel.resident.find-bed') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    room_id: $this.value
                },
                success: function (response) {
                    var data = $.parseJSON(response);
                    if (data.error) {
                        $.notify(data.message, "warning");
                    } else {
                        if((data.bed).length>0){
                        $('.bed_select').html('').append('<option value=" ">Select Beds...</option>');
                    }else{
                        $('.bed_select').html('').append('<option value=" ">Select</option>');
                    }
                        $.each(data.bed, function(key,valueObj){
                            $('.bed_select').append('<option value="'+valueObj.id+'">'+valueObj.bed_number+'</option>');
                        });
                    }
                }
            });

        }
        function loadRate($this) {
            $.ajax({
                type: 'POST',
                url: '{{ route('hostel.resident.find-rate') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    bed_id: $this.value
                },
                success: function (response) {
                    var data = $.parseJSON(response);
                    if (data.error) {
                        $.notify(data.message, "warning");
                    } else {
                         document.getElementById('bed-text').style.display="Block";
                       $.each(data.bed, function(key,valueObj){
                             $('#bed-text').html('').append('<input name="rent" class="form-control" type="text" required value="'+valueObj.rate+'">');
                        });
                    }
                }
            });

        }
    </script>
    <script type="text/javascript">
        jQuery(function($) {
            $(".resident-confirm").on('click', function() {
                var $this = $(this);
                bootbox.confirm({
                        title: "<div class='widget-header'><h4 class='smaller'><i class='ace-icon fa fa-exclamation-triangle red'></i> Leave Confirmation</h4></div>",
                        message: "<div class='ui-dialog-content ui-widget-content' style='width: auto; min-height: 30px; max-height: none; height: auto;'><div class='alert alert-info bigger-110'>" +
                        "This Resident Leave When You Click on Yes Leave Now.<br>Don't Be Afraid, You Will Able To ReActive in Future</div>" +
                        "<p class='bigger-110 bolder center grey'><i class='ace-icon fa fa-hand-o-right blue bigger-120'></i>Are you sure?</p>",
                        size: 'small',
                        buttons: {
                            confirm: {
                                label : "<i class='ace-icon fa fa-history'></i> Yes, Leave Now!",
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
        })
    </script>

    @include($view_path.'.includes.modal_values_script')
    @include('includes.scripts.delete_confirm')
    @include('includes.scripts.bulkaction_confirm')
    @include('includes.scripts.dataTable_scripts')
@endsection