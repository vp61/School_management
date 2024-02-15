@extends('layouts.master')

@section('css')
    <!-- page specific plugin styles -->

    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}" />
    @endsection

@section('content')

    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                @include('layouts.includes.template_setting')

                <div class="page-header">
                    <h1>
                     Reception Manager 
                        <small>
                        <i class="ace-icon fa fa-angle-double-right"></i>
                        Enquiry
                        </small>
                    </h1>
@php
    $fld_arr=array('id', 'branch_id','org_id', 'first_name','email','enq_date','mobile','date_of_birth','gender','course','academic_status','country','state','city','address','extra_info','responce','reference', 'refby','category_id', 'session_id'); $i=0;
@endphp

@foreach($fld_arr as $fldd)
    @if(isset($enquiry[0]->$fldd))
        @php $$fldd = $enquiry[0]->$fldd @endphp
    @else
        @php $$fldd = old($fldd) @endphp
    @endif

    @if($fldd=="date_of_birth" && $$fldd != "")
       @php $$fldd = date('Y-m-d', strtotime($$fldd)) @endphp
    @endif

@endforeach

@if(!$enq_date) 
    @php $enq_date=Carbon\Carbon::now()->format('Y-m-d'); @endphp
@else 
    @php $enq_date=Carbon\Carbon::parse($enq_date)->format('Y-m-d') @endphp
@endif
                </div><!-- /.page-header -->

                <div class="row">
                    <div class="col-xs-12 ">
                 
                    <!-- PAGE CONTENT BEGINS -->
                       
                        <div class="align-right">
                        <a class="{!! request()->is('student/import*')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('student.import') }}"><i class="fa fa-upload" aria-hidden="true"></i>&nbsp;Sale Form</a>
                        </div>
                        @if($id !="")
{!! Form::open(['route'=>['.enquiryupdate', $id], 'method'=>'POST', 'class'=>'form-horizontal', 'id'=>'validation-form', "enctype"=> "multipart/form-data"]) !!}
                        @else
{!! Form::open(['route' => '.enquiry', 'method' => 'POST', 'class' => 'form-horizontal', 'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}
                        @endif

<span class="label label-info arrowed-in arrowed-right arrowed responsive">Red mark input are required. </span>
<hr class="hr-16">
<div ng-app="myApp" ng-controller="feeCtrl">
<div class="form-group">
    {!! Form::hidden('id', $id, ['class'=>'']) !!}
    
    {!! Form::hidden('branch_id', $branch_id, ["class" => ""]) !!}
        
        
        
    {!! Form::label('course', 'Course', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {{ Form::select('course', $courses, $course, ['class'=>'form-control', 'required'=>'required'])}}
    </div>
    
    
    {!! Form::label('first_name', 'NAME OF STUDENT', ['class' => 'col-sm-2 control-label',]) !!}
    <div class="col-sm-2">
        {!! Form::text('first_name', $first_name, ["class" => "form-control border-form upper",'placeholder' => 'Please write your full name',"required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'first_name'])
    </div>

    {!! Form::label('category_id', 'Category', ['class' => 'col-sm-2 control-label']) !!}

    <div class="col-sm-2">
    {{Form::select('category_id', $category, $category_id, ['class'=>'form-control']) }}
    
    {!! Form::hidden('enq_date', $enq_date, ["class" => "form-control border-form upper"]) !!}
    
    @include('includes.form_fields_validation_message', ['name' => 'enq_date'])
    </div>

  
</div>
<div class="form-group">
    {!! Form::label('Email', 'Email', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::email('email', $email, ["placeholder" => "", "class" => "form-control border-form input-mask-registration"]) !!}

        @include('includes.form_fields_validation_message', ['name' => 'reg_no'])
    </div>

   {!! Form::label('academic_status', 'Academic Status', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
    <select name="academic_status" id="department" class="form-control">
     <option value="" >Select Status</option>
     @foreach ($academic_status_option as $x)
     @php $selected=($x==$academic_status) ? "selected":""; @endphp
    <option {{$selected}} value="{{ $x}}">{{ $x}}</option>
        @endforeach 
        </select>
    </div>
    @foreach ($data as $x)
    <input type="hidden" name="org_id" value="{{$x->org_id}}">
     @endforeach 

    {!! Form::label('Mobile', 'Mobile.', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('mobile', $mobile, ["placeholder" => "", "class" => "form-control border-form mobileKValidationCheck","required", "maxlength"=>"10"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'university_reg'])
    </div>
</div>
<div class="form-group">
    {!! Form::label('date_of_birth', 'Date of Birth', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::date('date_of_birth', $date_of_birth, ["data-date-format" => "yyyy-mm-dd", "class" => "form-control border-form date-picker input-mask-date"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'date_of_birth'])
    </div>

    {!! Form::label('gender', 'Gender', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::select('gender', ['' => 'Select Gender','MALE' => 'MALE', 'FEMALE' => 'FEMALE', 'OTHER' => 'OTHER'], $gender, ['class'=>'form-control border-form']); !!}
        @include('includes.form_fields_validation_message', ['name' => 'gender'])
    </div>
 
    {!! Form::label('session', 'Session', ['class'=>'col-sm-2 control-label']) !!}

    <div class="col-sm-2">
<strong style="font-size: 20px;color: #c3c3c3;font-family: monospace;">
    @if($session_id)
    {{ ViewHelper::get_session_name($session_id) }}
    @else
        @php $session_id=Session::get('activeSession') @endphp
    {{ ViewHelper::get_session_name(Session::get('activeSession')) }}
    @endif
</strong>
        
        {{ Form::select('session_id', $session_option, $session_id, ['class'=>'form-control', 'style'=>'display:none;']) }}
    </div>
</div>
</div>
<div class="form-group">
    
   
</div>

<div class="label label-warning arrowed-in arrowed-right arrowed">Permanent Address</div>
<hr class="hr-8">
<div class="form-group">
    
    {!! Form::label('country', 'Country', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-1">
        {!! Form::text('country', $country, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'country'])
    </div>
     {!! Form::label('state', 'State', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-1">
        {!! Form::text('state', $state, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'state'])
    </div>
    {!! Form::label('city', 'City', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('city', $city, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'city'])
    </div>
    
    {!! Form::label('address', 'Address', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-md-4">
        {!! Form::text('address', $address, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'address'])
    </div>
</div>
<div class="label label-success arrowed-in arrowed-right arrowed">Some Other Info</div>
<hr class="hr-8">
<div class="form-group">
    {!! Form::label('extra_info', 'Extra Info', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-3">
        {!! Form::text('extra_info', $extra_info, ["placeholder" => "", "class" => "form-control border-form input-mask-registration"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'extra_info'])
    </div>

    {!! Form::label('responce', 'Response ', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-3">
        {!! Form::text('responce', $responce, ["class" => "form-control date-picker border-form input-mask-date"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'responce'])
    </div>

    {!! Form::label('Ref.By:', 'Ref By:', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
         {!! Form::select('refby', ['' => 'Select Reference','google' => 'Google', 'SocialMedia' => 'Social Media', 'Person' => 'Person'], $refby, ['class'=>'form-control border-form']); !!}
        @include('includes.form_fields_validation_message', ['name' => 'Reference'])
    </div>
    <div class="form-group">
     {!! Form::label('reference', 'Reference.', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('reference', $reference, ["placeholder" => "", "class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'reference'])
    </div>

</div>
</div>

                        <div class="clearfix form-actions">
                            <div class="col-md-12 align-right">
                                <button class="btn" type="reset">
                                    <i class="icon-undo bigger-110"></i>
                                    Reset
                                </button>

                                <button class="btn btn-info" type="submit">
                                    <i class="icon-ok bigger-110"></i>
                                    Submit Enquiry
                                </button>
                            </div>
                        </div>

                        <div class="hr hr-24"></div>

                        {!! Form::close() !!}

                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    
    </div><!-- /.main-content -->


@endsection

@section('js')
    <!-- page specific plugin scripts -->
    @include('includes.scripts.jquery_validation_scripts')
    @include('student.registration.includes.student-comman-script')
    @include('includes.scripts.inputMask_script')
    @include('includes.scripts.datepicker_script')
    
@endsection