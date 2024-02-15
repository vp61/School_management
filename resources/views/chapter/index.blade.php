@extends('layouts.master')
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
                            Details
                        </small>
                    </h1>
                </div>
                @include('includes.flash_messages')
                <div class="row">
                    <div class="col-sm-12 text-right">
                         <a class="btn-primary btn-sm" href="{{route('.chapter_master.add')}}"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp; Add New chapter</a>
                    </div>
                </div>
                <hr>
                @include($view_path.'.includes.search_form')
                @include($view_path.'.includes.table')
            </div>
        </div>
    </div>
@endsection
@section('js')
<script type="text/javascript">
        /*$('.new_row').click(function(){
        var data=document.getElementById('head_tbl').innerHTML;
        var last_id=$('#variation_tbl tr:last').attr('id');
        $('#variation_tbl').append('<tr>'+data+'</tr>');
    });*/
     $(document).ready(function () {
            $('#assignment-btn').click(function () {
                var faculty = $('select[name="faculty"]').val();
                var semester = $('select[name="semesters_id"]').val();
                var subject = $('select[name="semester_subject"]').val();
                var publish_date = $('input[name="publish_date"]').val();
                var title = $('input[name="title"]').val();
                var description = $('textarea[name="description"]').val();


                if (faculty == 0) {
                    toastr.info("Please, Select Faculty", "Info:");
                    return false;
                }

                if (semester == 0) {
                    toastr.info("Please, Select Section", "Info:");
                    return false;
                }

                if (subject == 0) {
                    toastr.info("Please, Select Subject", "Info:");
                    return false;
                }

                if (publish_date == "") {
                    toastr.info("Please, Enter Publish Date", "Info:");
                    return false;
                }

                if (title == "") {
                toastr.info("Please, Enter Question Title", "Info:");
                    return false;
                }

                if (description == "") {
                    toastr.info("Please, Enter Question Detail", "Info:");
                    return false;
                }

            });

        });

/*load section code*/
        function loadSemesters($this){
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
                        $('select.semesters_id').html('').append('<option value="0">Select Section</option>');
                        $.each(data.semester, function(key,valueObj){
                            $('select.semesters_id').append('<option value="'+valueObj.id+'">'+valueObj.semester+'</option>');
                        });
                         $('select.selectpicker').selectpicker('refresh');
                         toastr.success(data.success, "Success:");
                    }
                }
            });

        }
        /*load section code end */
        function loadSubject($this) {

            var faculty = $('select[name="faculty"]').val();
            var semester = $('select[name="semesters_id"]').val();


            if (faculty == 0) {
                toastr.info("Please, Select Faculty", "Info:");
                return false;
            }

            if (semester == 0) {
                toastr.info("Please, Select Semester", "Info:");
                return false;
            }

            if (!semester)
                toastr.warning("Please, Choose Semester.", "Warning");
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
        
    </script>
<script src="{{ asset('assets/js/bootbox.js') }}"></script> 
@include('includes.scripts.delete_confirm')
@include('includes.scripts.dataTable_scripts')
@endsection