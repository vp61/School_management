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
                            Detail
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    @include($view_path.'.includes.buttons')
                    <div class="col-xs-12 ">
                        @include('includes.flash_messages')
                        @include($view_path.'.includes.search_form')
                        <!-- PAGE CONTENT BEGINS -->
                            <div class="form-horizontal">
                            @include($view_path.'.includes.table')
                            </div>
                        </div>
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
           

        });

        function loadSemesters($this){
            
            $.ajax({
                type: 'POST',
                url: '{{ route('Lms.Lesson_plans.find-semester') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    faculty_id: $this.value
                },
                success: function (response) {
                    var data = $.parseJSON(response);
                    if (data.error) {
                        $.notify(data.message, "warning");
                    } else {
                        $('.semesters_id').html('').append('<option value="0">Select Section</option>');
                        $.each(data.semester, function(key,valueObj){
                            $('.semesters_id').append('<option value="'+valueObj.id+'">'+valueObj.semester+'</option>');
                        });
                    }
                }
            });

        }
        function loadSubject($this) {
            var faculty = $('select[name="faculty"]').val();
            var semester = $('select[name="semesters_id"]').val();


            if (faculty == 0) {
                toastr.info("Please, Select Faculty", "Info:");
                return false;
            }

            if (semester == 0) {
                toastr.info("Please, Select Section", "Info:");
                return false;
            }

            if (!semester)
                toastr.warning("Please, Choose Section.", "Warning");
            else {
                $.ajax({
                    type: 'POST',
                    url: '{{ route('Lms.Lesson_plans.find-subject') }}',
                    data: {
                        _token: '{{ csrf_token() }}',
                        faculty_id: faculty,
                        semester_id: semester,
                        session_id : "{{Session::get('activeSession')}}",
                        branch_id : "{{Session::get('activeBranch')}}",
                    },
                    success: function (response) {
                        var data = $.parseJSON(response);
                        if (data.error) {
                            $('.semester_subject').html('')
                            toastr.warning(data.error, "Warning:");
                        } else {
                            $('.semester_subject').html('').append('<option value="0">Select Subject</option>');
                            $.each(data.subjects, function (key, valueObj) {
                                $('.semester_subject').append('<option value="' + valueObj.id + '">' + valueObj.title + '</option>');
                            });
                            toastr.success(data.success, "Success:");
                        }
                    }
                });
            }

        }
        function loadChapter($this){
            //alert('kfjdkjfkd');
            var faculty = $('select[name="faculty"]').val();
            var semester = $('select[name="semesters_id"]').val();
            var subject =$('select[name="subjects_id"]').val();

            if (faculty == 0) {
                toastr.info("Please, Select Faculty", "Info:");
                return false;
            }

            if (semester == 0) {
                toastr.info("Please, Select Section", "Info:");
                return false;
            }

            if (subject == 0) {
                toastr.info("Please, Select Section", "Info:");
                return false;
            }


            $.ajax({
                type: 'POST',
                url: '{{ route('Lms.Lesson_plans.find-chapter') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    faculty_id: faculty,
                    semester_id: semester,
                    subject_id:subject,
                },
                'beforeSend': function (request) {
                    if ($this){
                       $('.Lession').empty();
                    }
                },
                success: function (response){
                    var data = $.parseJSON(response);
                    if (data.error) {
                        $.notify(data.message, "warning");
                    } else {
                        $('.Lession').html('').append('<option value="0">Select lession</option>');
                        $.each(data.chapter, function(key,valueObj){
                            $('.Lession').append('<option value="'+valueObj.id+'">'+valueObj.title+'</option>');
                        });
                    }
                }
            });

        }
       
       $('.changeStatus').change(function(){
            //$('#getFeeByBatch').html('');
             var status= $(this).val();
             //alert(status);
             var id= $(this).attr('id'); 
            
            $.post(
                "{{route('Lms.Lesson_plans.changeStatus')}}",
                 {status:status , id : id, _token:'{{ csrf_token() }}'},
                 function(response){
                    var data = $.parseJSON(response);
                    
                    if(data.error){
                        toastr.warning(data.msg,"Warning");
                       
                        
                    }
                    else{
                        toastr.success(data.msg,"Success");
                       
                    }
                    
            });
        });
</script>


    @include('includes.scripts.inputMask_script')
    @include('includes.scripts.delete_confirm')
    @include('includes.scripts.bulkaction_confirm')
    @include('includes.scripts.dataTable_scripts')
    @include('includes.scripts.datepicker_script')

@endsection