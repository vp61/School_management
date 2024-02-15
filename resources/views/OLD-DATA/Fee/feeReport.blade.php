@extends('layouts.master')
@section('content')
@php 
    $course_list = $student_list = array();
    $course_list[0]="Select Faculty";
    $fee_list[0]="Select Fee Type";
    $semester_list[0]="Select Semester";

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
<form action="{{route('fee_report')}}" method="post">
   {{ csrf_field() }}
                <div class="row">
                    <div class="col-sm-12">
                        @if(Session::has('msG'))
                        <div class="alert alert-success">{{Session::get('msG')}}</div>
                        @endif                    
                        <table class="table table-striped">
            @if($errors->any())<div class="alert alert-danger">
               
                @foreach($errors->all() as $err)
                    <div>{{$err}}</div>
                @endforeach
            </div>@endif
                            <tr>
<td style="padding-top:10px;">Faculty:</td>
<td>
    {{ Form::select('course', $course_list, '', ['class'=>'cors form-control selectpicker', 'id'=>'course_drop', 'required'=>'required', 'data-live-search'=>'true'])}}
<span class="error">{{ $errors->first('course') }}</span>

</td>

<td style="padding-top:10px;">Semester:</td>
<td>{{ Form::select('semester', $semester_list, '', ['class'=>'form-control semester']) }}</td>

<td style="padding-top:10px;">Fee Type:</td>
<td>{{ Form::select('fee_type', $fee_list, '', ['class'=>'fee_type form-control selectpicker', 'data-live-search'=>'true'])}}</td>



                            </tr>

                            <tr>
<td><b>From: </b></td>
<td><input placeholder="YYYY-MM-DD" class="input-sm form-control border-form input-mask-date date-picker" data-date-format="yyyy-mm-dd" name="from" type="text"></td>
<td><b>To: </b></td>
<td><input placeholder="YYYY-MM-DD" class="input-sm form-control border-form input-mask-date date-picker" data-date-format="yyyy-mm-dd" name="to" type="text"></td>
<td><b>Registration No.: </b></td>
<td><input type="text" name="admission_no" placeholder="Registration No." class="form-control" />
    <br/>
    <input type="submit" name="submit" value="Search" class="btn btn-info" />
</td>
                            </tr>

                        </table>
                    </div>
                </div></form>
            </div>        
   
   </div></div>
   
@endsection







