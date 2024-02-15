@extends('layouts.master')
@section('css')
    <style type="text/css">
        .searchDropdownWidth .dropdown-menu{
            max-width: 100px !important;
        }
    </style>
@endsection
@section('content')
@php 
    $course_list = $student_list = array();
    $course_list[0]="Select ".env('course_label');
    $fee_list[0]="Select Fee Type";
    $semester_list[0]="Select Section";

    foreach($data['course_list'] as $course_name=>$id){
        $course_list[$id] = $course_name;
    }

    foreach($data['fee_list'] as $fee){
        $fee_list[$fee->id] = $fee->fee_head_title;
    }

    foreach($data['semester_list'] as $semester){
        $semester_list[$semester->id] = $semester->semester;
    } //print_r($course_list); exit;
    @endphp
    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                <div class="page-header">

                    <h1> Fees Manager 
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Fee Report
                        </small>
                    </h1>
                </div><!-- /.page-header -->
            </div>
            

            <div class="page-content">
                <form action="{{route('student_collection_report')}}" method="post">
                   {{ csrf_field() }}
                    <div class="row">
                        <div class="col-sm-12">
                            @if(Session::has('msG'))
                                <div class="alert alert-success">{{Session::get('msG')}}</div>
                            @endif                    
                            <table class="table table-striped">
                                @if($errors->any())
                                    <div class="alert alert-danger">
                                        @foreach($errors->all() as $err)
                                            <div>{{$err}}</div>
                                        @endforeach
                                    </div>
                                @endif
                                <tr>
                                    
                                    @if(Session::get('isCourseBatch'))
                                        <td>{{ env('course_label') }}:</td>
                                        <td>
                                            {{ Form::select('course', $course_list, '', ['class'=>'batch_wise_cousre form-control selectpicker', 'required'=>'required', 'data-live-search'=>'true','id'=>'batch_wise_cousre'])}}
                                            <span class="error">{{ $errors->first('course') }}</span>
                                        </td>
                                        <td>Batch:</td>
                                        <td >
                                            {{ Form::select('batch',[''=>'Select Batch'], '', ['class'=>'batch form-control selectpicker', 'data-live-search'=>'true','id'=>'batch'])}}
                                        </td>
                                    @else
                                        <td>{{env('course_label')}}:</td>
                                        <td>
                                            {{ Form::select('course', $course_list, '', ['class'=>'cors form-control selectpicker', 'id'=>'course_drop', 'required'=>'required', 'data-live-search'=>'true','onchange'=>'loadSemesters(this)'])}}
                                            <span class="error">{{ $errors->first('course') }}</span>
                                        </td>
                                        <td>Section:</td>
                                        <td>{{ Form::select('semester', $semester_list, '', ['class'=>'form-control semester','id'=>'semester_select']) }}</td>
                                    @endif

                                    
                                    <td><p>Student:</p></td>
                                    <td>
                                        {{ Form::select('student',[''=>'Select'], '', ['class'=>'student stdnt form-control selectpicker', 'data-live-search'=>'true','id'=>'std'])}}
                                    </td> 

                                    
                                </tr>
                               
                                <tr>
                                    <td><b>From: </b></td>
                                    <td><input placeholder="YYYY-MM-DD" class="input-sm form-control border-form input-mask-date date-picker" data-date-format="yyyy-mm-dd" name="from"  required type="text"></td>
                                    <td><b>To: </b></td>
                                    <td><input placeholder="YYYY-MM-DD" class="input-sm form-control border-form input-mask-date date-picker" data-date-format="yyyy-mm-dd" name="to" required  type="text"></td>
                                    <td class="text-center">
                                        <input type="submit" name="submit" value="Search" class="btn btn-info" />
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </form>
            </div>        
       </div>
    </div>
    
   
@endsection
@section('js')

<script>
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
@endsection






