@php
$fld_arr=['id','branch_id','org_id', 'first_name','email','admission_date','mobile','date_of_birth','gender','course','academic_status','country','state','city','address','extra_info','responce','reference','admission_fee','category_id','session_id','course','payment_type','reference_no','religion_id','handicap_id','father_name','form_no','refby','comm_country','comm_state','comm_city','comm_address','father_occupation','mother_name','mother_occupation','whatsapp_no','previous_school','father_annual_income','father_aadhar_no','mother_annual_income','mother_aadhar_no','age_in_year_as_on_1april'];

@endphp

@foreach($fld_arr as $fldd)
    @php 
    $$fldd = (isset($enquiry[0]->$fldd)) ? $enquiry[0]->$fldd : old($fldd) @endphp
      
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
@if(!$admission_date) 
    @php $admission_date=date("Y-m-d") @endphp
@else
    @php $admission_date=date("Y-m-d", strtotime($admission_date))@endphp

@endif
@php 
 $agebybranch = Session::get('activeBranch');
 $activeSession = Session::get('activeSession');
$getAge=  new App\Http\Controllers\AdmissionController;
  $age= $getAge->GetAge($date_of_birth);
  if(isset($enquiry[0])){
   $age= $age;
   }
   else{
       $age='';
    }
@endphp
<div ng-app="myApp" ng-controller="feeCtrl">
    <!-- <div ng-init="selectcourse()"></div> -->
    <div class="label label-warning arrowed-in arrowed-right arrowed">Student Details</div>
    <hr class="hr-8">
    <div class="form-group">
        {!! Form::label('first_name', 'Student Name', ['class' => 'col-sm-2 control-label',]) !!}
        <div class="col-sm-2">
            {!! Form::text('first_name', $first_name, ["class" => "form-control border-form upper","required"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'first_name'])
        </div>
          {!! Form::label('course', env('course_label'), ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-2">
           <!--  {{ Form::select('course', $courses, $course, ['id'=>'course', 'class'=>'form-control']) }}
            {!! Form::hidden('branch_id', $branch_id, ["class" => ""]) !!} -->
            <!-- ng-change="feechange(formfeebycourse)" ng-model ="formfeebycourse" -->
            <select name="course" id="course" class="form-control" >
            <option  value="" >--Select {{ env('course_label') }}-- </option>
            @foreach($courses as $x)
                <!-- <option ng-repeat ="x in allcoursedata" value="@{{x.id}}" >@{{x.faculty }}</option> -->
                 @php $selected=($x->id==$course) ? "selected":""; @endphp
                <option value="{{$x->id}}" {{$selected}} >{{$x->faculty}}</option>
            @endforeach

            </select>

        </div> 
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

    {!! Form::label('gender', 'Gender', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::select('gender', ['' => 'Select Gender','MALE' => 'MALE', 'FEMALE' => 'FEMALE', 'OTHER' => 'OTHER'], $gender, ['class'=>'form-control border-form']); !!}
        @include('includes.form_fields_validation_message', ['name' => 'gender'])
    </div>
    {!! Form::label('religion', 'Religion', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::select('religion_id',$religion,$religion_id, ["class" => "form-control border-form "]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'religion_id'])
    </div>
</div> 
<div class="form-group">
    
    {!! Form::label('handicap', 'Handicap', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::select('handicap_type',[''=>"--Select--",'1'=>'Yes','2'=>'No'],$handi_type, ["class" => "form-control border-form",'onchange'=>'handicap_cat(this)']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'handicap_type'])
    </div>
    {!! Form::label('handicap_cat','Handicap Category', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::select('handicap_id',$handicap,$handicap_id, ["class" => "form-control border-form ",'id'=>'handicap']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'handicap_id'])
    </div>
    {!! Form::label('form_no', 'Form No.', ['class' => 'col-sm-2 control-label',]) !!}
    <div class="col-sm-2">
        {!! Form::text('form_no',$form_no, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'first_name'])
    </div>  
</div>

    <div class="form-group">
    {!! Form::label('Admission Date', 'Reg. Date', ['class' => 'col-sm-2 control-label',]) !!}
    <div class="col-sm-2">
        {!! Form::date('admission_date', $admission_date, ["class" => "form-control border-form upper","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'first_name'])
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
    {!! Form::label('Fathers Annual Income', "Fathers Annual Income", ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('father_annual_income', $father_annual_income, ['class'=>'form-control border-form']); !!}
        @include('includes.form_fields_validation_message', ['name' => 'gender'])
    </div>
</div>
<div class="form-group">
    {!! Form::label('Fathers Aadhar No', "Father Aadhar No", ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('father_aadhar_no', $father_aadhar_no, ['class'=>'form-control border-form']); !!}
        @include('includes.form_fields_validation_message', ['name' => 'gender'])
    </div>
    {!! Form::label('Mothers Name', "Mother's Name", ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('mother_name', $mother_name, ['class'=>'form-control border-form']); !!}
        @include('includes.form_fields_validation_message', ['name' => 'gender'])
    </div>
   
    {!! Form::label('Mothers Occupation', "Mother's Occupation", ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('mother_occupation', $mother_occupation, ['class'=>'form-control border-form']); !!}
        @include('includes.form_fields_validation_message', ['name' => 'gender'])
    </div>
</div>
<div class="form-group">
    
    {!! Form::label('Mothers Annual Income', "Mother Annual Income", ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('mother_annual_income', $mother_annual_income, ['class'=>'form-control border-form']); !!}
        @include('includes.form_fields_validation_message', ['name' => 'gender'])
    </div>
    {!! Form::label('Mothers Aadhar No', "Mother Aadhar No", ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('mother_aadhar_no', $mother_aadhar_no, ['class'=>'form-control border-form']); !!}
        @include('includes.form_fields_validation_message', ['name' => 'gender'])
    </div>
    
</div>
<div class="label label-warning arrowed-in arrowed-right arrowed">Contact  Details</div>
<hr class="hr-8">
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
    {!! Form::label('Whatsapp', ' Whatsapp No.', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('whatsapp_no', $mobile, ["placeholder" => "", "class" => "form-control border-form mobileKValidationCheck", "maxlength"=>"10"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'university_reg'])
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
<div class="label label-warning arrowed-in arrowed-right arrowed">Communication Address</div>

    <div class="control-group col-sm-12">
        <div class="radio">
            <label>
                {!! Form::checkbox('permanent_address_copier', '', false, ['class' => 'ace', "onclick"=>"CopyCommAddress(this.form)"]) !!}
                <span class="lbl"> Communication Address Same As Permanent Address</span>
            </label>
        </div>
    </div>
    <hr>
    <hr class="hr-8"> 
    <div class="form-group">
           {!! Form::label('comm_country', 'Country', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-2">
                {!! Form::text('comm_country', $comm_country, ["class" => "form-control border-form upper"]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'comm_country'])
            </div>

            {!! Form::label('comm_state', 'State', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-2">
                {!! Form::text('comm_state', $comm_state, ["class" => "form-control border-form upper"]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'comm_state'])
            </div>

            {!! Form::label('comm_city', 'City', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-2">
                {!! Form::text('comm_city', $comm_city, ["class" => "form-control border-form upper"]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'comm_city'])
            </div>
             
        </div>
        <div class="form-group">
            {!! Form::label('comm_address', 'Address', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-4">
                {!! Form::textarea('comm_address', $comm_address, ["class" => "form-control border-form upper",'rows'=>4]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'comm_address'])
            </div>
        </div>


<hr class="hr-8">
<div class="label label-success arrowed-in arrowed-right arrowed">Some Other Info</div>
<hr class="hr-8">
<div class="form-group">
     {!! Form::label('Previous school', 'Previous school', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('previous_school', $previous_school, [ "class" => "form-control border-form "]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'date_of_birth'])
    </div>
    {!! Form::label('Age in year As on 1st April,2022', 'Age in year As on 1st April,2022', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-3">
        {!! Form::text('age_in_year_as_on_1april', $age, [ "class" => "form-control border-form "]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'date_of_birth'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('extra_info', 'Extra Info', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('extra_info', $extra_info, ["placeholder" => "", "class" => "form-control border-form input-mask-registration"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'extra_info'])
    </div>

    {!! Form::label('responce', 'Response ', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('responce', $responce, ["class" => "form-control date-picker border-form input-mask-date"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'responce'])
    </div>

    {!! Form::label('reference', 'Reference', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::select('reference',$references,$refby, [ "class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'reference'])
    </div>
</div>


<div class="label label-danger arrowed-in arrowed-right arrowed">Form Fee</div>
<hr class="hr-8">    <div class="form-group">
    {!! Form::label('AdmissionFee', 'Form Fee', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
      <!--  {!! Form::number('admission_fee', $admission_fee, ["placeholder" => "", "class" => "form-control border-form", "required"]) !!} -->
      <input type="number" class = "form-control border-form" name="admission_fee" value="{{$admission_fee}}" required>
       
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