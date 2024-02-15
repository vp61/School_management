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
                       {{$panel}}  Manager
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Student Transfer
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    <div class="col-xs-12 ">
                    @include($view_path.'.includes.buttons')
                            @include('includes.flash_messages')
                        <!-- PAGE CONTENT BEGINS -->
                        <div class="form-horizontal">
                            @include($view_path.'.student-transfer.includes.form')
                            <div class="hr hr-18 dotted hr-double"></div>
                        </div>
                    </div><!-- /.col -->
                </div><!-- /.row -->

               <div class="row">
                   <div class="col-xs-12">
                        @include($view_path.'.student-transfer.includes.table')
                   </div>
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

        function loadSemesters($this) {
             var id=$this.id;
             var sem='semester'+id;

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
                        $('.'+sem).html('').append('<option value="">--Select Semester--</option>');
                        $.each(data.semester, function(key,valueObj){
                            $('.'+sem).append('<option value="'+valueObj.id+'">'+valueObj.semester+'</option>');
                        });
                    }
                }
            });

        }
        function loadCourse($this) {
            var id=$this.id;
            var course='course'+id;
            $('.semester'+id).prop('selectedIndex'," ");
            $.ajax({
                type: 'POST',
                url: '{{ route('student.find-course') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    branch_id: $this.value
                },
                success: function (response) {
                    var data = $.parseJSON(response);
                    if (data.error) {
                        $.notify(data.message, "warning");
                    } else {
                        $('.'+course).html('').append('<option value=" ">--Select Course--</option>');
                        $.each(data.course, function(key,valueObj){
                            $('.'+course).append('<option value="'+valueObj.id+'">'+valueObj.faculty+'</option>');
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