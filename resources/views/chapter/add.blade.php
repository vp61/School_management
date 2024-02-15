@extends('layouts.master')

@section('css')
@endsection

@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                @include('layouts.includes.template_setting')
                <div class="page-header">
                    <h1>
                       {{$panel}} Manager
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                           @if(isset($data['row']))
                            Edit
                           @else
                            Add
                           @endif 
                        </small>
                    </h1>
                </div><!-- /.page-header -->

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
                        {!!Form::model($data['row'],['route'=>[$base_route.'.edit',$id],'method'=>'POST','class'=>'form-horizontal','id' => 'validation-form',"enctype"=>"miltipart/form-data"])!!}
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
                        {!!Form::open(['route'=>$base_route.'.store','method'=>'POST','class'=>'form-horizontal','id' => 'validation-form',"enctype"=>"miltipart/form-data"])!!}
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
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
@endsection


@section('js')
@include('includes.scripts.dataTable_scripts')

<script type="text/javascript">
        $('.new_row').click(function(){
        var data=document.getElementById('head_tbl').innerHTML;
        var last_id=$('#variation_tbl tr:last').attr('id');
        $('#variation_tbl').append('<tr>'+data+'</tr>');
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
                        $('select.semester_select').html('').append('<option value="">Select Section</option>');
                        $.each(data.semester, function(key,valueObj){
                            $('select.semester_select').append('<option value="'+valueObj.id+'">'+valueObj.semester+'</option>');
                        });
                         $('select.selectpicker').selectpicker('refresh');
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
                            $('.semester_subject').html('').append('<option value="">No Subject Found</option>')
                            toastr.warning(data.error, "Warning:");
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
        
    </script>
@endsection