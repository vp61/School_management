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
                       {{$panel}}
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Student Remark
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    <div class="col-xs-12 ">
                 
                        @include('includes.flash_messages')
                        <!-- PAGE CONTENT BEGINS -->
                        <div class="form-horizontal">
                            @include($view_path.'.includes.form')
                            <div class="hr hr-18 dotted hr-double"></div>
                        </div>
                    </div><!-- /.col -->
                </div><!-- /.row -->
               
                @include($view_path.'.includes.table')
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
              
                var faculty = $('select[name="faculty"]').val();
                var semester = $('select[name="semester_select[]"]').val();
                

               

                
                if (faculty !== '' & faculty >0) {

                    if (flag) {

                        url += '&faculty=' + faculty;

                    } else {

                        url += '?faculty=' + faculty;
                        flag = true;

                    }
                }

                if (semester !== '' & semester >0) {

                    if (flag) {

                        url += '&semester=' + semester;

                    } else {

                        url += '?semester=' + semester;
                        flag = true;

                    }
                }


                

                location.href = url;

            });

            $('#student-transfer-btn').click(function () {

                faculty = $('#transfer-faculty').val();
                semester = $('#transfer-semester').val();
                

                if (faculty !== '' & faculty >0) {
                    if (semester !== '' & semester >0) {
                        if (status !== '' & status >0) {
                            /*Check Student List Select Or not*/
                            $chkIds = document.getElementsByName('chkIds[]');
                            var $chkCount = 0;
                            $length = $chkIds.length;

                            for (var $i = 0; $i < $length; $i++) {
                                if ($chkIds[$i].type == 'checkbox' && $chkIds[$i].checked) {
                                    $chkCount++;
                                }
                            }

                            if ($chkCount <= 0) {
                                toastr.info("Please, Select At Least One Student Record.", "Info:");
                                return false;
                            }

                        }else{
                            toastr.info("Please, Select Correct Student Status.", "Info:");
                            return false;
                        }
                    }else{
                        toastr.info("Please, Select Your Target Section.", "Info:");
                        return false;
                    }
                }else{
                    toastr.info("Please, Select Your Target Faculty.", "Info:");
                    return false;
                }

            });


        });

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
                        $('#semester_select').html('').append('<option value="0">Select Section</option>');
                        $.each(data.semester, function(key,valueObj){
                            $('#semester_select').append('<option value="'+valueObj.id+'">'+valueObj.semester+'</option>');
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