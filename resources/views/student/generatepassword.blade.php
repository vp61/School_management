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
                            {{ $panel }} Detail
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    <div class="col-xs-12 ">
                     
                        @include('includes.flash_messages')
                        <!-- PAGE CONTENT BEGINS -->
                        @include('includes.validation_error_messages')
                        <div class="form-horizontal">
                            @include($view_path.'.includes.gen_password_form') 
                            <div class="hr hr-18 dotted hr-double"></div>
                        </div>
                    </div><!-- /.col -->
                </div><!-- /.row -->
               
              
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
            $('#filter-btn').click(function () {

                var url = '{{ $data['url'] }}';
                var flag = false;
                var reg_no = $('input[name="reg_no"]').val();
                var designation = $('select[name="designation"]').val();
                var status = $('select[name="status"]').val();

                if (reg_no !== '') {
                    url += '?reg_no=' + reg_no;
                    flag = true;
                }

                if (designation !== '' && designation > 0 ) {

                    if (designation !== 'all') {

                        if (flag) {

                            url += '&designation=' + designation;

                        } else {

                            url += '?designation=' + designation;

                        }

                    }
                }

                if (status !== '' ) {

                    if (status !== 'all') {

                        if (flag) {

                            url += '&status=' + status;

                        } else {

                            url += '?status=' + status;

                        }

                    }
                }

                location.href = url;

            });

        });

  /*load semester*/
  function loadSemesters($this) {
               
            $.ajax({
                type: 'POST',
                url: '{{ route('student.find-semester') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    faculty_id: $this.value
                },
                success: function (response) {
                    var data = $.parseJSON(response);
                    if (data.error) {
                        $.notify(data.message, "warning");
                    } else {
                        $('.semester_select').html('').append('<option value="0">Select Section</option>');
                        $.each(data.semester, function(key,valueObj){
                            $('.semester_select').append('<option value="'+valueObj.id+'">'+valueObj.semester+'</option>');
                        });
                    }
                }
            });

        }

    </script>
    @include('includes.scripts.inputMask_script')
    @include('includes.scripts.delete_confirm')
    @include('includes.scripts.bulkaction_confirm')
    @include('includes.scripts.dataTable_scripts')
    @include('includes.scripts.datepicker_script')

@endsection