@php
$fld_arr=['id','branch_id','org_id', 'first_name','email','admission_date','mobile','date_of_birth','gender','course','academic_status','country','state','city','address','extra_info','responce','reference','admission_fee','category_id','session_id','course','payment_type','reference_no'];

@endphp

@foreach($fld_arr as $fldd)
    @php 
    $$fldd = (isset($enquiry[0]->$fldd)) ? $enquiry[0]->$fldd : old($fldd) @endphp

    @if($fldd=="date_of_birth" && $$fldd != "")
       @php $$fldd = date('Y-m-d', strtotime($$fldd)) @endphp
    @endif
@endforeach

@if(!$admission_date) 
    @php $admission_date=date("Y-m-d") @endphp
@else
    @php $admission_date=date("Y-m-d", strtotime($admission_date))@endphp

@endif
@php 
 $agebybranch = Session::get('activeBranch');
 $activeSession = Session::get('activeSession');

@endphp

<div ng-app="myApp" ng-controller="feeCtrl">
    <!-- <div ng-init="selectcourse()"></div> -->
    <div class="form-group">
    {!! Form::label('course', 'Course', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
       <!--  {{ Form::select('course', $courses, $course, ['id'=>'course', 'class'=>'form-control']) }}
        {!! Form::hidden('branch_id', $branch_id, ["class" => ""]) !!} -->
        <!-- ng-change="feechange(formfeebycourse)" ng-model ="formfeebycourse" -->
        <select name="course" id="course" class="form-control" >
        <option  value="" >--Select Course-- </option>
        @foreach($courses as $x)
            <!-- <option ng-repeat ="x in allcoursedata" value="@{{x.id}}" >@{{x.faculty }}</option> -->
             @php $selected=($x->id==$course) ? "selected":""; @endphp
            <option value="{{$x->id}}" {{$selected}} >{{$x->faculty}}</option>
        @endforeach

        </select>

    </div>    
        
     
    
    {!! Form::label('first_name', 'NAME OF STUDENT', ['class' => 'col-sm-2 control-label',]) !!}
    <div class="col-sm-2">
        {!! Form::text('first_name', $first_name, ["class" => "form-control border-form upper","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'first_name'])
    </div>
    


    {!! Form::label('Admission Date', 'Admission Date', ['class' => 'col-sm-2 control-label',]) !!}
    <div class="col-sm-2">
        {!! Form::date('admission_date', $admission_date, ["class" => "form-control border-form upper","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'first_name'])
    </div>
</div>
<div class="form-group">
    {!! Form::label('Email', 'Email', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('email', $email, ["placeholder" => "", "class" => "form-control border-form input-mask-registration"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'reg_no'])
    </div>

    
    <input type="hidden" name="org_id" value="{{$org_id}}">

    {!! Form::label('Mobile', 'Mobile.', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::number('mobile', $mobile, ["placeholder" => "", "class" => "form-control border-form mobileKValidationCheck","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'university_reg'])
    </div>

    
    {!! Form::label('academic_status', 'Academic Status', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
    <select name="academic_status" id="department" class="form-control">
    <!--  <option value="" >Select Status</option> -->
     @foreach ($academic_status_option as $x)
     @php $selected=($x==$academic_status) ? "selected":""; @endphp
    <option {{$selected}} value="{{ $x}}">{{ $x}}</option>
        @endforeach 
        </select>
    </div>
    </div>

<div class="form-group" ng-app="myApp" ng-controller="feeCtrl">

     {!! Form::label('category_id', 'Category', ['class' => 'col-sm-2 control-label']) !!}
   
    @if($agebybranch == 4 || $agebybranch == 5 || $agebybranch == 6 || $agebybranch == 8)
    <div class="col-sm-2">
    <select name="category_id"   id="department" required="required" class="form-control" ng-change = 'categoryselect(category_id)'  ng-init="somethingHere = x[0]" ng-model= 'category_id'>

    <!-- <option  value="" >Select Category </option> -->
     @foreach ($category as $key=> $x)
     @php $selected=($key==$category_id) ? "selected":""; @endphp
    <option  value="{{$key}}" {{$selected}}>{{ $x}}</option>
    @endforeach 
    </select>
         
    </div>
     @else 
    <div class="col-sm-2">
         <select name="category_id"   id="department" required="required" class="form-control" >
    <!-- <option  value="" >Select Course </option> -->
     @foreach ($category as $key=>$x)
     @php $selected=($key==$category_id) ? "selected":""; @endphp

    <option  value="{{$key}}" {{$selected}}>{{$x}}</option>
    @endforeach 
    </select>
       <!--  {{Form::select('category_id', $category, $category_id,[ 'class'=>'form-control', "required"])}} -->
    </div>
     @endif
     {{ Form::text('session_id',$activeSession, ['class'=>'form-control', 'style'=>'display:none;']) }}


    
   
    @if($agebybranch == 4 || $agebybranch == 5 || $agebybranch == 6 || $agebybranch == 8)
    <div ng-if="category_id">
         {!! Form::label('date_of_birth', 'Date of Birth', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2"  >
      
        <input type="date" name="date_of_birth" value="$date_of_birth" class ="form-control border-form date-picker input-mask-date" data-date-format = "yyyy-mm-dd" max="@{{minAge | date:'yyyy-MM-dd'}}" min="@{{maxAge | date:'yyyy-MM-dd'}}" required>

    </div>
    </div>
   

     @else
       {!! Form::label('date_of_birth', 'Date of Birth', ['class' => 'col-sm-2 control-label']) !!}
       <div class="col-sm-2">
      
        {!! Form::date('date_of_birth', $date_of_birth, ["data-date-format" => "yyyy-mm-dd", "class" => "form-control border-form date-picker input-mask-date"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'date_of_birth'])
       
    </div>
    @endif

    {!! Form::label('gender', 'Gender', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::select('gender', ['' => 'Select Gender','MALE' => 'MALE', 'FEMALE' => 'FEMALE', 'OTHER' => 'OTHER'], $gender, ['class'=>'form-control border-form']); !!}
        @include('includes.form_fields_validation_message', ['name' => 'gender'])
    </div>

    
   


<!--     {!! Form::label('session', 'Session', ['class'=>'col-sm-1 control-label']) !!}
    <div class="col-sm-1"><strong style="font-size:20px; color:#c3c3c3; font-family: monospace;">
        @if($session_id)
        
        {{ ViewHelper::get_session_name($session_id) }}
        @else
            @php $session_id=Session::get('activeSession') @endphp
        {{ ViewHelper::get_session_name(Session::get('activeSession')) }}
        @endif
      
    </strong></div> -->
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

    {!! Form::label('reference', 'Reference', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-3">
        {!! Form::text('reference', $reference, ["placeholder" => "", "class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'reference'])
    </div>
</div>

<div class="label label-danger arrowed-in arrowed-right arrowed">Admission Fee</div>
<hr class="hr-8">    <div class="form-group">
    {!! Form::label('AdmissionFee', 'Admission Fee', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
      <!--  {!! Form::number('admission_fee', $admission_fee, ["placeholder" => "", "class" => "form-control border-form", "required"]) !!} -->
      <input type="text" class = "form-control border-form" name="admission_fee" value="{{$admission_fee}}" required>
       
    </div>
	
	{!! Form::label('payment_type', 'Payment Mode', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::select('payment_type',$pay_type_list, $payment_type, ['class'=>'form-control border-form', "required"]); !!}
        
    </div>
	
	{!! Form::label('ref', 'Invoice / Ref No', ['class' => 'col-sm-2 control-label']) !!}
    
    <div class="col-sm-2">
        {!! Form::text('reference_no', $reference_no, ["class" => "form-control border-form upper"]) !!} 
    </div>
	
    </div>
</div>