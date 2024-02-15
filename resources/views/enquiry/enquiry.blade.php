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
    $fld_arr=array('id', 'branch_id','org_id', 'first_name','email','enq_date','mobile','date_of_birth','gender','course','academic_status','country','state','city','address','extra_info','responce','reference', 'refby','category_id', 'session_id','religion_id','handicap_id','no_of_child','next_follow_up','father_name','father_occupation','mother_name','mother_occupation','whatsapp_no','previous_school'); $i=0;
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
@if(isset($enquiry[0]->handicap_id))
        @php
          
           $handi_type=1 
        @endphp   
       
    @else
        @php
            $handi_type=2;
        @endphp
       
    @endif 

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
                        <a class="{!! request()->is('enquirylist')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('.enquiry_list') }}"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;Enquiry List</a>
                        </div>
                        @if($id !="")
{!! Form::open(['route'=>['.enquiryupdate', $id], 'method'=>'POST', 'class'=>'form-horizontal', 'id'=>'validation-form', "enctype"=> "multipart/form-data"]) !!}
                        @else
{!! Form::open(['route' => '.enquiry', 'method' => 'POST', 'class' => 'form-horizontal', 'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}
                        @endif

<span class="label label-info arrowed-in arrowed-right arrowed responsive">Red mark input are required. </span>
<hr class="hr-16">

<div class="label label-warning arrowed-in arrowed-right arrowed">Student Details</div>
<hr class="hr-8">

<div ng-app="myApp" ng-controller="feeCtrl">
<div class="form-group">
    {!! Form::label('first_name', "Student's Name", ['class' => 'col-sm-2 control-label',]) !!}
    <div class="col-sm-2">
        {!! Form::text('first_name', $first_name, ["class" => "form-control border-form upper",'placeholder' => 'Please write your full name',"required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'first_name'])
    </div>

    {!! Form::hidden('id', $id, ['class'=>'']) !!}
    
    {!! Form::hidden('branch_id', $branch_id, ["class" => ""]) !!}
        
        
        
    {!! Form::label('course',env('course_label'), ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {{ Form::select('course', $courses, $course, ['class'=>'form-control', 'required'=>'required'])}}
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
<div class="form-group">
    {!! Form::label('date_of_birth', 'Date of Birth', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::date('date_of_birth', $date_of_birth, ["data-date-format" => "yyyy-mm-dd", "class" => "form-control border-form date-picker input-mask-date"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'date_of_birth'])
    </div>
   
    {!! Form::label('category_id', 'Category', ['class' => 'col-sm-2 control-label']) !!}

    <div class="col-sm-2">
    {{Form::select('category_id', $category, $category_id, ['class'=>'form-control']) }}
    
    {!! Form::hidden('enq_date', $enq_date, ["class" => "form-control border-form upper"]) !!}
    
    @include('includes.form_fields_validation_message', ['name' => 'enq_date'])
    </div>
     {!! Form::label('gender', 'Gender', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::select('gender', ['' => 'Select Gender','MALE' => 'MALE', 'FEMALE' => 'FEMALE', 'OTHER' => 'OTHER'], $gender, ['class'=>'form-control border-form']); !!}
        @include('includes.form_fields_validation_message', ['name' => 'gender'])
    </div>

</div>
<div class="form-group">
   

    
    {!! Form::label('religion', 'Religion', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::select('religion_id',$religion,$religion_id, ["class" => "form-control border-form "]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'religion_id'])
    </div>
    {!! Form::label('handicap', 'Handicap', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::select('handicap_type',[''=>"--Select--",'1'=>'Yes','2'=>'No'],$handi_type, ["class" => "form-control border-form abc",'onchange'=>'handicap_cat(this)']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'handicap_type'])
    </div>
     {!! Form::label('handicap_cat','Handicap Category', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::select('handicap_id',$handicap,$handicap_id, ["class" => "form-control border-form ",'id'=>'handicap']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'handicap_id'])
    </div> 
   
    
</div>

<div class="label label-warning arrowed-in arrowed-right arrowed">Parent's  Details</div>
<hr class="hr-8">
<div class="form-group">
     
    {!! Form::label('Fathers Name', "Father's Name", ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('father_name', $father_name, ['class'=>'form-control border-form']); !!}
        @include('includes.form_fields_validation_message', ['name' => 'gender'])
    </div>
     {!! Form::label('Fathers Name', "Father's Occupation", ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('father_occupation', $father_occupation, ['class'=>'form-control border-form']); !!}
        @include('includes.form_fields_validation_message', ['name' => 'gender'])
    </div>
     {!! Form::label('Mothers Name', "Mother's Name", ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('mother_name', $mother_name, ['class'=>'form-control border-form']); !!}
        @include('includes.form_fields_validation_message', ['name' => 'gender'])
    </div>
 
    
</div>
<div class="form-group">
   
   
    {!! Form::label('Mothers Name', "Mother's Occupation", ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('mother_occupation', $mother_occupation, ['class'=>'form-control border-form']); !!}
        @include('includes.form_fields_validation_message', ['name' => 'gender'])
    </div>
    
</div>

<div class="label label-warning arrowed-in arrowed-right arrowed">Contact  Details</div>
<hr class="hr-8">
<div class="form-group">
    {!! Form::label('Email', 'Email', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::email('email', $email, ["placeholder" => "", "class" => "form-control border-form upper input-mask-registration"]) !!}

        @include('includes.form_fields_validation_message', ['name' => 'reg_no'])
    </div>

   
    {!! Form::label('Mobile', 'Mobile.', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('mobile', $mobile, ["placeholder" => "", "class" => "form-control border-form mobileKValidationCheck","required", "maxlength"=>"10"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'university_reg'])
    </div>
    {!! Form::label('Whatsapp', ' Whatsapp No.', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('whatsapp_no', $mobile, ["placeholder" => "", "class" => "form-control border-form mobileKValidationCheck", "maxlength"=>"10"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'university_reg'])
    </div>
</div>


</div>




<div class="label label-warning arrowed-in arrowed-right arrowed">Permanent Address</div>
<hr class="hr-8">
<div class="form-group">
    
    {!! Form::label('country', 'Country', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('country', $country, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'country'])
    </div>
     {!! Form::label('state', 'State', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('state', $state, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'state'])
    </div>
    {!! Form::label('city', 'City', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('city', $city, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'city'])
    </div> 
</div>
<div class="form-group">
    {!! Form::label('address', 'Address', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-md-4">
        {!! Form::textarea('address', $address, ["class" => "form-control border-form upper",'rows'=>4]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'address'])
    </div>
</div>
<div class="label label-success arrowed-in arrowed-right arrowed">Some Other Info</div>
<hr class="hr-8">
<div class="form-group">
    {!! Form::label('source', 'Source', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::select('reference',$sources, $reference, ['class'=>'form-control border-form']); !!}
        
        @include('includes.form_fields_validation_message', ['name' => 'reference'])
    </div>
    

    {!! Form::label('Ref.By:', 'Ref By:', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
         {!! Form::select('refby',$references, $refby, ['class'=>'form-control border-form']); !!}
        @include('includes.form_fields_validation_message', ['name' => 'Reference'])
    </div>
    {!! Form::label('responce', 'Response ', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('responce', $responce, ["class" => "form-control date-picker border-form input-mask-date"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'responce'])
    </div>

</div>
<div class="form-group">
    {!! Form::label('extra_info', 'Extra Info', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('extra_info', $extra_info, ["placeholder" => "", "class" => "form-control border-form input-mask-registration"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'extra_info'])
    </div>
     {!! Form::label('date_of_birth', 'Previous school', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('previous_school', $previous_school, [ "class" => "form-control border-form "]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'date_of_birth'])
    </div>
    {!! Form::label('no_of_child', 'Number Of Child', ['class' => 'col-sm-2 control-label']) !!}
   <div class="col-sm-2">
        {!! Form::number('no_of_child',$no_of_child, ["class" => "form-control",'min'=>0]) !!}
    </div>
   
</div>
<div class="form-group">
    {!! Form::label('follow_up', 'Follow Up Date', ['class' => 'col-sm-2 control-label']) !!}
   <div class="col-sm-2">
        {!! Form::date('next_follow_up',$next_follow_up, ["class" => "form-control border-form date-picker "]) !!}
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
<script>
    function handicap_cat(id) {
            var type=id.value;
            if(type==1){
                $('#handicap').attr('required',true);
            }else{
                $('#handicap').prop('selectedIndex','');
                $('#handicap').attr('required',false);
               
            }
    }
</script>
    <!-- page specific plugin scripts -->
    @include('includes.scripts.jquery_validation_scripts')
    @include('student.registration.includes.student-comman-script')
    @include('includes.scripts.inputMask_script')
    @include('includes.scripts.datepicker_script')
    
@endsection