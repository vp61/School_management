@extends('layouts.master')
@section('content')
@php 
    $course_list = $student_list = array();
    $course_list[0]="Select ".env('course_label');
    $student_list[0]="Select Student";
    $semester_list[0]="Select Section";

    foreach($data['course_list'] as $course_name=>$id){
        $course_list[$id] = $course_name;
    }

    foreach($data['student_list'] as $student){
        $student_list[$student->id] = $student->first_name;
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
                            Due Report
                        </small>
                    </h1>
                </div><!-- /.page-header -->
            </div>
            <div class="page-content">
                <form action="{{route('due_report')}}" method="post">
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
                                        <td style="padding-top:10px;">{{ env('course_label') }}:</td>
                                        <td >
                                            {{ Form::select('course', $course_list, '', ['class'=>'batch_wise_cousre form-control selectpicker', 'required'=>'required', 'data-live-search'=>'true','id'=>'batch_wise_cousre'])}}
                                            <span class="error">{{ $errors->first('course') }}</span>
                                        </td>
                                        <td style="padding-top:10px;">Batch:</td>
                                        <td >
                                            {{ Form::select('batch',[''=>'Select Batch'], '', ['class'=>'batch form-control selectpicker', 'data-live-search'=>'true','id'=>'batch'])}}
                                        </td>
                                    @else
                                        <td style="padding-top:10px;">{{ env('course_label') }}:</td>
                                        <td>
                                            {{ Form::select('course', $course_list, '', ['class'=>'cors form-control selectpicker', 'id'=>'course_drop', 'required'=>'required', 'data-live-search'=>'true'])}}
                                        <span class="error">{{ $errors->first('course') }}</span>
                                        </td>
                                        <td style="padding-top:10px;">Section:</td>
                                        <td>{{ Form::select('semester', $semester_list, '', ['class'=>'form-control semester']) }}</td>
                                    @endif
                                    <td style="padding-top:10px;">Student:</td>
                                    <td>{{ Form::select('student', $student_list, '', ['class'=>'student form-control selectpicker', 'data-live-search'=>'true'])}}</td>
                                    <td><b>OR</b></td>
                                    <td><input type="text" name="admission_no" placeholder="Registration No.." /></td>
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

