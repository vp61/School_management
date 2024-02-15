@extends('layouts.master')
@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap-clockpicker.min.css') }}" />
@endsection
@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                @include('layouts.includes.template_setting')

                <div class="page-header">
                    <h1>
                        {{ $panel }}
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                          @if(isset($data['row']))
                            Edit
                          @else  
                           Add
                          @endif 
                        </small>
                    </h1>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                    <h4 class="header large lighter blue">
                        @if(isset($data['row']))
                            <i class="fa fa-pencil"></i> Edit
                          @else 
                           <i class="fa fa-plus"></i> Add
                        @endif  
                      </h4> 
                       
                        @if(isset($data['row']))   
                        {!!Form::model($data['row'],['route'=>[$base_route.'.edit',$id],'method'=>'POST','class'=>'form-horizontal','id' => 'validation-form',"enctype"=>"multipart/form-data"])!!}
                        @php($date=null)
                             @include($view_path.'.includes.form')
                          <div class="form-group">
                            <div class="clearfix form-actions">
                                <div class="align-right">            &nbsp; &nbsp; &nbsp;
                                        <button class="btn btn-info" type="submit" id="filter-btn">
                                                Update
                                        </button>
                                </div>
                            </div> 
                        </div>   
                        @else
                        {!!Form::open(['route'=>$base_route.'.store','method'=>'POST','class'=>'form-horizontal','id' => 'validation-form',"enctype"=>"multipart/form-data"])!!}
                        @php($date=\Carbon\Carbon::now()->format('Y-m-d'))
                        @include($view_path.'.includes.form')
                        <div class="form-group">
                            <div class="clearfix form-actions">
                                <div class="align-right">            &nbsp; &nbsp; &nbsp;
                                        <button class="btn btn-info" type="submit" id="filter-btn">
                                             <i class="fa fa-plus bigger-110"></i>
                                                Add 
                                        </button>
                                </div>
                            </div> 
                        </div>
                        @endif 
                            
                        {!!Form::close()!!}    
                    </div>
                </div>
            </div>
        </div>
    </div>            
@endsection
@section('js')
<script type="text/javascript">
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
        function loadSemesters($this) {
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
                       toastr.warning(data.error, "warning:");
                       $('.semesters_id').html('').append('<option value="">No Section Found</option>');
                    } else {
                        $('.semesters_id').html('').append('<option value="">Select Section</option>');
                        $.each(data.semester, function(key,valueObj){
                            $('.semesters_id').append('<option value="'+valueObj.id+'">'+valueObj.semester+'</option>');
                        });
                        toastr.success(data.success, "Success:");
                    }
                }
            });

        }

        function loadSubject($this) {
            var faculty = $('select[name="faculty"]').val();
            var semester = $('select[name="semesters_id"]').val();
            $('.Lession').html('').append('<option value="">Select</option>');


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
                            $('.semester_subject').html('').append('<option value="">No Subject Found</option>');
                        } else {
                            $('.semester_subject').html('').append('<option value="">Select Subject</option>');
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
                success: function (response) {
                    var data = $.parseJSON(response);
                    if (data.error) {
                         toastr.warning(data.error,"Warning");
                         $('.Lession').html('').append('<option value="">No Chapter Found</option>');
                    } else {
                        $('.Lession').html('').append('<option value="">Select Chapter</option>');
                        $.each(data.chapter, function(key,valueObj){
                            $('.Lession').append('<option value="'+valueObj.id+'">'+valueObj.title+'</option>');
                        });
                        toastr.success(data.success, "Success:");
                    }
                }
            });

        }
</script>

<script src="{{ asset('assets/js/bootstrap-clockpicker.min.js') }}"></script>
<script>
    $('.clockpicker').clockpicker();
</script>
@endsection