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
                        @include($view_path.'.includes.breadcrumb-primary')
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Member Add
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    @include('hostel.includes.buttons')
                    <div class="col-xs-12 ">
                    @include($view_path.'.includes.buttons')
                    @include('includes.flash_messages')
                    @include('includes.validation_error_messages')
                    <!-- PAGE CONTENT BEGINS -->
                        <div class="form-horizontal">
                            {!! Form::open(['route' => $base_route.'.store', 'method' => 'POST', 'class' => 'form-horizontal',
                    'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}

                                @include($view_path.'.includes.form')

                            <div class="clearfix form-actions">
                                <div class="col-md-12 align-right">
                                    <button class="btn" type="reset">
                                        <i class="icon-undo bigger-110"></i>
                                        Reset
                                    </button>
                                    &nbsp; &nbsp; &nbsp;
                                    <button class="btn btn-info" type="submit" id="filter-btn">
                                        <i class="icon-ok bigger-110"></i>
                                        Register
                                    </button>
                                </div>
                            </div>

                            <div class="hr hr-18 dotted hr-double"></div>
                            {!! Form::close() !!}
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
    @include('includes.scripts.inputMask_script')
    <!-- inline scripts related to this page -->
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            /*Change Field Value on Capital Letter When Keyup*/
            $(function() {
                $('.upper').keyup(function() {
                    this.value = this.value.toUpperCase();
                });
                $('#course').select2();

                $('#user').change(function(){ 
                   var user= document.getElementById('user').value;
                    $('#staff').select2();
                    if(user==2){
                         $('.onstd').hide();
                         $('.onstaff').show();
                        document.getElementById('section').required=false;
                        document.getElementById('course').required=false;
                        document.getElementById('student').required=false;
                        document.getElementById('student').name="";
                        document.getElementById('staff').name="memberId";
                        document.getElementById('staff').required=true;
                    }
                    if(user==1){
                        $('.onstaff').hide();
                        $('.onstd').show();
                        document.getElementById('student').name="memberId";
                        document.getElementById('staff').name="";
                        document.getElementById('section').required=true;
                        document.getElementById('course').required=true;
                        document.getElementById('student').required=true;
                         document.getElementById('staff').required=false;
                    }
                });
            });
            /*end capital function*/
        });
        function loadStudent(){
            var course=document.getElementById('course').value;
            var section=document.getElementById('section').value;
           $.ajax({
            type:'POST',
            url: '{{route('hostel.resident.load-student')}}',
            data: {
                _token : '{{csrf_token()}}',
                course : course,
                section : section
            },
            success:function(response){
                var data = $.parseJSON(response);
                if(data.error){
                    $.notify(data.message,"warning");
                }else{
                    $('#student').html(' ').append('<option value="">--Select Student--</option>');
                    $.each(data.student,function(key,val){
                        $('#student').append('<option value="'+val.id+'">'+val.name+'</option>');
                    });
                }
            }
           });
           $('#student').select2();
        }
        function loadBlock($this){
            $('#block').select2();
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
                             $('.block_select').html('').append('<option value="">--Select Block--</option>')
                             $.each(data.block,function(key,valueObj){
                                 $('.block_select').append('<option value="'+valueObj.id+'">'+valueObj.title+'</option>');
                             });
                        }
                }
            });
        }
        function loadFloor($this){
            var v=document.getElementById('hostel').value;
            $.ajax({
                type: 'POST',
                url: '{{ route('hostel.resident.find-floor') }}',
                data: {
                    _token: '{{csrf_token()}}',
                    hostel_id: v,
                    block_id: $this.value
                },
                success: function (response){
                    var data = $.parseJSON(response);
                        if(data.error){
                            $.notify(data.message,"warning");
                        } else{
                             $('#bed').html('').append('<option value="">Select</option>');
                            $('#room').html('').append('<option value="">Select</option>');
                             $('.floor_select').html('').append('<option value="">--Select Floor--</option>');
                             document.getElementById('bed-text').style.display="none";
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
                        $('.room_select').html('').append('<option value="">--Select Room--</option>');
                        document.getElementById('bed-text').style.display="none";
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
                        $('.bed_select').html('').append('<option value="">--Select Beds--</option>');
                    }else{
                        $('.bed_select').html('').append('<option value="">Select</option>');
                    }
                    document.getElementById('bed-text').style.display="none";
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
                             $('#bed-text').html('').append('<input id="rent_amount" name="rent" class="form-control" type="text" required value="'+valueObj.rate+'">');
                        });
                    }
                }
            });

        }
        function payModeValidation($this){
            var rent=document.getElementById('rent_amount').value;
            var val=$this.value;
           if(val>0){
            document.getElementById('payMode').required=true;
           }
           else{
                document.getElementById('payMode').required=false;
           }
        }
        function studentStaffValidation($this){
            var user=document.getElementById('user').value;
            var std=document.getElementsByClassName('onstd');
            if(user==2){
            }
        }

    </script>
@endsection