    @extends('layouts.master')

@section('css')
    <!-- page specific plugin styles -->
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
                    @include('transport.includes.buttons')
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
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
    @endsection


@section('js')
    @include('includes.scripts.jquery_validation_scripts')
    @include('includes.scripts.inputMask_script')
    <!-- inline scripts related to this page -->
    <script type="text/javascript">
     function getrent(x){
            var z=x.value;
            var rent=document.getElementById('rentAmount').value;
            if( z== 'monthly'){
                document.getElementById('show').value=rent;
                document.getElementById('show').style.display="block";
            }
            if( z== 'quarterly'){
                document.getElementById('show').value=rent*3;
                document.getElementById('show').style.display="block";
            }
            if( z == 'half_yearly'){
                document.getElementById('show').value=rent*6;
                document.getElementById('show').style.display="block";
            }
            if( z== 'yearly'){
                document.getElementById('show').value=rent*12;
                document.getElementById('show').style.display="block";
            }
            else if(z==""){
              document.getElementById('show').value="";
 
            }    
        }

        $(document).ready(function () {
            /*Change Field Value on Capital Letter When Keyup*/
            $(function() {
                $('.upper').keyup(function() {
                    this.value = this.value.toUpperCase();
                });
            });
            /*end capital function*/
        });
        
        function loadVehicle($this) {
            if($this.value == ""){
                document.getElementById('rentAmount').value="";
            }
            $.ajax({
                type: 'POST',
                url: '{{ route('transport.find-vehicles') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    route_id: $this.value
                },
                success: function (response) {
                    var data = $.parseJSON(response);
                    if (data.error) {
                        $.notify(data.message, "warning");
                    } else {
                        $('.vehicle_select').html('').append('<option value="0">Select Vehicle...</option>');
                        $.each(data.vehicles, function(key,valueObj){
                            $('.vehicle_select').append('<option value="'+valueObj.id+'">'+valueObj.number+' | '+valueObj.type+'</option>');
                            $('#rent').html('').append('<input type="text" value="'+valueObj.rent+'" class="form-control"  id="rentAmount" name="rent">');
                            $('#duration').prop('selectedIndex','');
                            $('#show').css("display","none");
                        });

                    }
                }
            });

        }

    </script>
@endsection