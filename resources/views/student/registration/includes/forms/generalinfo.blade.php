@php 
     //dd($admData);
    $name=(isset($admData[0]->first_name)) ? $admData[0]->first_name:"";
    
     $place_of_birth=(isset($admData[0]->place_of_birth)) ? $admData[0]->place_of_birth : "";
     $previous_school=(isset($admData[0]->previous_school)) ? $admData[0]->previous_school : "";
     $age_in_year_as_on_1april=(isset($admData[0]->age_in_year_as_on_1april)) ? $admData[0]->age_in_year_as_on_1april : "";
     $mother_tongue=(isset($admData[0]->mother_tongue)) ? $admData[0]->mother_tongue : "";
     $previous_class=(isset($admData[0]->previous_class)) ? $admData[0]->previous_class : "";
    $indose_number=(isset($admData[0]->indose_number)) ? $admData[0]->indose_number:"";
    $passport_no=(isset($admData[0]->passport_no)) ? $admData[0]->passport_no:"";
    $religion_id=(isset($admData[0]->religion_id)) ? $admData[0]->religion_id:"";
    $handicap_id=(isset($admData[0]->handicap_id)) ? $admData[0]->handicap_id:"";
    $catego = (isset($admData[0]->category_id)) ? $admData[0]->category_id:"";
    $course=(isset($admData[0]->faculty)) ? $admData[0]->faculty: "";
    $course2=(isset($admData[0]->course)) ? $admData[0]->course: "";
    
    $semester=(isset($admData[0]->semester)) ? $admData[0]->semester: "";
    $universityRegNo=(isset($admData[0]->university_reg)) ? $admData[0]->university_reg: "";
    $session_id=(isset($admData[0]->session_id)) ? $admData[0]->session_id : "";
    $id=(isset($admData[0]->id)) ? $admData[0]->id: "";
    $date_of_birth=(isset($admData[0]->date_of_birth)) ? $admData[0]->date_of_birth:"";
    
    $reg_date = (isset($admData[0]->reg_date)) ? \Carbon\Carbon::parse($admData[0]->reg_date)->format('Y-m-d'):\Carbon\Carbon::now()->format('Y-m-d');
       

    $gender=(isset($admData[0]->gender)) ? $admData[0]->gender:"";
    $is_hostlier=(isset($admData[0]->is_hostlier)) ? $admData[0]->is_hostlier:"";
    //$mobile= ? $admData[0]->mobile_1:"";
    $country=(isset($admData[0]->country)) ? $admData[0]->country:"";
    $state=(isset($admData[0]->state)) ? $admData[0]->state:"";
    
    $zip=(isset($admData[0]->zip)) ? $admData[0]->zip:"";
    $city=(isset($admData[0]->city)) ? $admData[0]->city:"";
    $address=(isset($admData[0]->address)) ? $admData[0]->address:"";
    $aadhar_no=(isset($admData[0]->aadhar_no)) ? $admData[0]->aadhar_no:"";
    $randomString = (!isset($randomString)) ? $admData[0]->reg_no : $randomString;
    // $email=(isset($admData[0]->email)) ? $admData[0]->email : "";
    $email=(isset($admData[0]->email)) ? $admData[0]->email : $randomString.env('EMAIL_POST_FIX');
    $admission_condition=(isset($admData[0]->admission_condition)) ? $admData[0]->admission_condition : '';
    $subject=(isset($admData[0]->subject)) ? explode(',',$admData[0]->subject) : '';
    $std_subject=(isset($std_subject)) ? $std_subject : ($std_subject =[]);
@endphp
@if(isset($admData[0]->father_name))
     @php  $father_name= $admData[0]->father_name;   @endphp 
@elseif(isset($admData[0]->father_first_name))
     @php  $father_name= $admData[0]->father_first_name;  @endphp 
@else
     @php  $father_name= '';  @endphp 
@endif

@if(isset($admData[0]->mother_name))
     @php  $mother_first_name= $admData[0]->mother_name;  @endphp 
@elseif(isset($admData[0]->mother_first_name))
     @php  $mother_first_name= $admData[0]->mother_first_name;  @endphp 
@else
     @php $mother_first_name= '';  @endphp 
@endif
@if(isset($admData[0]->handicap_id))
        @php
         
           $handi_type=1 
        @endphp   
       
    @else
        @php
           
            $handi_type=2;
    @endphp
@endif    
@if(isset($admData[0]->mobile_1))
    @php $mobile=$admData[0]->mobile_1 @endphp
@elseif(isset($admData[0]->mobile))
    @php $mobile=$admData[0]->mobile @endphp
@else @php $mobile="" @endphp @endif

@if(!$date_of_birth) 
    @php $date_of_birth=date("Y-m-d"); @endphp
@else 
    @php $date_of_birth=date("Y-m-d", strtotime($date_of_birth)) @endphp
@endif
@php
$getAge=  new App\Http\Controllers\AdmissionController;
     $age= $getAge->GetAge($date_of_birth);
      if(isset($admData[0])){
       $age= $age;
       }
       else{
       $age='';
    }
@endphp
{{-- code sahi hai apply later 
@if(request()->get('admId'))
    @php
        $fld_arr=['branch_id','org_id', 'first_name','email','admission_date','mobile','date_of_birth','gender','course','academic_status','country','state','city','address','extra_info','responce','reference','admission_fee','category_id','session_id','religion_id','handicap_id','whatsapp_no','previous_school','father_annual_income','father_aadhar_no','mother_annual_income','mother_aadhar_no','age_in_year_as_on_1april','father_occupation','father_name','mother_name','mother_occupation'];
    @endphp
@else
    @php
        $fld_arr=['id','branch_id','org_id', 'first_name','email','admission_date','mobile','date_of_birth','gender','course','academic_status','country','state','city','address','extra_info','responce','reference','admission_fee','category_id','session_id', 'created_by', 'last_updated_by', 'reg_no', 'reg_date', 'university_reg', 'faculty', 'semester','blood_group','nationality', 'mother_tongue', 'student_image','status', 'zip','religion_id','handicap_id','place_of_birth','previous_school','previous_class'];
    @endphp

@endif

@foreach($fld_arr as $fldd)
    @php 
    $$fldd = (isset($enquiry[0]->$fldd)) ? $enquiry[0]->$fldd : old($fldd) @endphp

    @if($fldd=="date_of_birth" && $$fldd != "")
       @php $$fldd = date('Y-m-d', strtotime($$fldd)) @endphp
    @endif
@endforeach
--}}
<span class="label label-info arrowed-in arrowed-right arrowed responsive">Red mark input are required. </span>
<hr class="hr-16">
<div ng-app="myApp" ng-controller="feeCtrl">
   

<div class="label label-warning arrowed-in arrowed-right arrowed">Academic  Information</div>
<hr class="hr-8">
<div class="form-group">
   {!! Form::label('College', 'Branch', ['class' => 'col-sm-2 control-label']) !!}
    @if(empty($data['branch_name']))
     <div class="col-sm-4">
            <select name="branch_id" id="department" class="form-control"  required>
            @foreach($branch as $branch_id =>$brnch)
            <option value="{{$brnch->id}}">{{$brnch->branch_name}}</option>
            @endforeach
            </select>
    </div>
    @endif

    {{Form::hidden('id', $id)}}
    {!! Form::label('Session', 'Session ', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4"><strong style="font-size: 20px;color: #c3c3c3;font-family: monospace;">
        @if($session_id)
        {{ ViewHelper::get_session_name($session_id) }}
        @else
            @php $session_id=Session::get('activeSession') @endphp
        {{ ViewHelper::get_session_name(Session::get('activeSession')) }}
        @endif
        

        {!! Form::select('session_id', $sessions, $session_id, ['id'=>'department', 'class'=>'form-control reg_cur_sessn', 'required'=>'required', 'style'=>'display:none;']) !!}
        
        {{--<select name="session_id" id="department" class="form-control"  required>
            @foreach($sessions as $session )
            <option value="{{$session->id}}">{{$session->session_name}}</option>
            @endforeach
            </select>--}}
    </strong></div>

</div>  
<div class="form-group">
   
        <label class="col-sm-2 control-label">{{ env('course_label') }}</label>
        <div class="col-sm-4">
            @if(!isset($admData[0]->id) || (isset($admId) && $admId != ""))
                @if(Session::get('isCourseBatch'))
                    <select name="faculty" onchange='loadSection(this)' id="batch_wise_cousre" class="form-control batch_wise_cousre" required>
                @else
                    <select name="faculty" id="course"  onchange='loadSection(this)' class="form-control getSubject" ng-model="selectedCourse" ng-change="coursechange(selectedCourse)"  required>
                @endif        
            @else
                 @if(Session::get('isCourseBatch'))
                <select name="faculty" id="batch_wise_cousre"  onchange='loadSection(this)' class="form-control batch_wise_cousre" required>
                @else
                    <select name="faculty" id="course"  onchange='loadSection(this)' class="form-control getSubject" required>
                @endif                        
            @endif
            <option value="">Select {{ env('course_label') }}</option> 
                @foreach($courses as $x)
                    @php $selected=($x->id==$course || $x->id==$course2) ? "selected":""; @endphp
                        <option {{$selected}} value="{{ $x->id }}" >{{ $x->faculty }}</option>
                @endforeach 
            </select>
        </div>
        @if(Session::get('isCourseBatch'))
            {!! Form::label('Batch','Batch', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-4">
                @php
                    $sel_batch[''] = 'Select Batch'; 
                  $batch =  isset($batch) ? $batch : $sel_batch;
                  $selected_batch = (isset($admData[0]->batch_id)) ? $admData[0]->batch_id:"";
                   
                @endphp
                {{ Form::select('batch_id',$batch,$selected_batch, ['class'=>' form-control','id'=>'batch'])}}
            </div>    
        @else
        {!! Form::label('Semester', 'Section', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-4">
                <select name="semester" class="form-control semester getSubject semester_select" id="sem"  required> 
                    <option value="">--Select Section--</option>
                    @foreach($Semester as $sem)
                        @php $selected=($semester == $sem->id) ? "selected":""; @endphp
                        <option {{$selected}} value="{{$sem->id}}">{{$sem->semester}}</option>
                    @endforeach
                </select>
                @include('includes.form_fields_validation_message', ['name' => 'semester'])
            </div>
        @endif       
</div>  
<div class="form-group">
    
    {!! Form::label('subject','Subjects', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!!Form::select('subjects[]',$std_subject,$subject,['class'=>'form-control selectpicker','multiple','data-live-search'=>'true','id'=>'subject'])!!}
    </div>
    {!! Form::label('admission_condition','Admission Condition', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!!Form::text('admission_condition',$admission_condition,['class'=>'form-control selectpicker','multiple','data-live-search'=>'true','id'=>'subject'])!!}
    </div>
</div>
<div class="form-group">
    
    {!! Form::label('subject','Scholar No.', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!!Form::text('university_reg',$universityRegNo,['class'=>'form-control'])!!}
    </div>

</div>



<div class="label label-warning arrowed-in arrowed-right arrowed">Student Information</div>
<hr class="hr-8">


<div class="form-group">
    {!! Form::label('reg_date', 'Adm. Date', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        <input class="form-control border-form upper" max="{{date('Y-m-d')}}" name="reg_date" type="date" value="{{$reg_date}}">
    </div>
    {!! Form::label('first_name', 'NAME OF STUDENT', ['class' => 'col-sm-2 control-label',]) !!}
    <div class="col-sm-4">
        {!! Form::text('first_name', $name, ["class" => "form-control border-form upper" ,"required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'first_name'])
    </div>
    
</div>


{{--{!! Form::hidden('reg_no',$randomString, ["placeholder" => "","readonly"=>"readonly", "class" => "form-control border-form input-mask-registration"]) !!}--}}
@php
 $branch_id = Session::get('activeBranch');
@endphp

@if(Route::current()->getName() == 'student.edit' && $branch_id == 4 || Route::current()->getName() == 'student.edit' && $branch_id == 5 || Route::current()->getName() == 'student.edit' && $branch_id == 6 || Route::current()->getName() == 'student.edit' && $branch_id == 8 ) 
<div class="form-group">
    {!! Form::label('Indose No ', 'Indose No ', ['class' => 'col-sm-2 control-label']) !!}

   <div class="col-sm-4">
        {!! Form::text('indose_number', $indose_number, ["class" => "form-control border-form upper" ]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'indose_number'])
    </div>

    {!! Form::label('Passport No', 'Passport No ', ['class' => 'col-sm-2 control-label']) !!}

   <div class="col-sm-4">
        {!! Form::text('passport_no', $passport_no, ["class" => "form-control border-form upper" ]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'passport_no'])
    </div>
</div>
@endif

<div class="form-group">
    
    
    
    {!! Form::label('father_name', 'NAME OF FATHER', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('father_first_name', $father_name, [ "class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'father_first_name'])
    </div>
    {!! Form::label('mother_name', 'NAME OF MOTHER', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('mother_first_name', $mother_first_name, [ "class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'mother_first_name'])
    </div>
</div>


<div class="form-group">
     {!! Form::label('category_id', 'Category', ['class' => 'col-sm-2 control-label']) !!} 
    <div class="col-sm-4">
    {{Form::select('category_id', $category, $catego, ['class'=>'form-control']) }}
    </div>

     {!! Form::label('nationality', 'Nationality', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
     {!! Form::text('nationality', null, ["class" => "form-control border-form upper"]) !!} 
    </div>
</div>

<div class="form-group">
     {!! Form::label('DOB', 'DOB', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
     {!! Form::date('date_of_birth', $date_of_birth, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'dob'])
    </div>
     {!! Form::label('gender', 'Gender', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::select('gender', ['' => 'Select Gender','MALE' => 'MALE', 'FEMALE' => 'FEMALE', 'OTHER' => 'OTHER'], $gender, ['class'=>'form-control border-form']); !!}
        @include('includes.form_fields_validation_message', ['name' => 'gender'])
    </div>
</div>
<div class="form-group">
    {!! Form::label('religion', 'Religion', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::select('religion_id',$religion,$religion_id, ["class" => "form-control border-form "]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'religion_id'])
    </div>
    {!! Form::label('handicap', 'Handicap', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::select('handicap_type',[''=>"--Select--",'1'=>'Yes','2'=>'No'],$handi_type, ["class" => "form-control border-form",'onchange'=>'handicap_cat(this)']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'handicap_type'])  
</div>
<div class="form-group">
</div>
    {!! Form::label('handicap_cat','Handicap Category', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::select('handicap_id',$handicap,$handicap_id, ["class" => "form-control border-form ",'id'=>'handicap']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'handicap_id'])
    </div>
    {!! Form::label('Place of Birth','Place of Birth', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
       {!! Form::text('place_of_birth', $place_of_birth, ["placeholder" => "", "class" => "form-control"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'handicap_id'])
    </div>
</div>
 
<div class="form-group">
    {!! Form::label('previous_school','Previous School', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
         {!! Form::text('previous_school', $previous_school, ["placeholder" => "", "class" => "form-control"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'handicap_id'])
    </div>
    {!! Form::label('Previous Class','Previous Class', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
       {!! Form::text('previous_class', $previous_class, ["placeholder" => "", "class" => "form-control"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'handicap_id'])
    </div>
</div>
<div class="form-group">
    {!! Form::label('Mother Tongue','Mother Tongue', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
       {!! Form::text('mother_tongue', $mother_tongue, ["placeholder" => "", "class" => "form-control"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'handicap_id'])
    </div>
     {!! Form::label('Seeking Admission For Which Section','Seeking Admission For Which Section', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
       {!! Form::select('is_hostlier',[''=>"--Select--",'1'=>'Day Scholar','2'=>'Boarding'],$is_hostlier, ["class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'handicap_id'])
    </div>
</div>
<div class="form-group">
    {!! Form::label('age_in_year_as_on_1april','Age in Year As On 1 April', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
       {!! Form::text('age_in_year_as_on_1april', $age, ["placeholder" => "", "class" => "form-control"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'handicap_id'])
    </div>
</div>

<div class="label label-warning arrowed-in arrowed-right arrowed">Student Communication Info</div>
<hr class="hr-8">

<div class="form-group">

    {!! Form::label('mobile_1', 'Mobile', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4"> 
        {!! Form::text('mobile_1', $mobile, ["class" => "form-control border-form input-mask-mobile mobileKValidationCheck" ,"required","maxlength"=>"10"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'mobile_1'])
        
    </div>
    {!! Form::label('home_phone', 'Phone', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('home_phone', null, ["class" => "form-control border-form input-mask-phone", "maxlength"=>"10", "pattern"=>"\d*"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'home_phone'])
    </div>
</div>


<div class="form-group">
    
    {!! Form::label('email', 'E-mail', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('email',$email, ["class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'email'])
    </div>  
    {!! Form::label('aadhar', 'Aadhaar No.', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::number('aadhar_no',null, ["class" => "form-control border-form"]) !!}
       
    </div>  
</div>

<h4 class="label label-warning arrowed-in arrowed-right arrowed" >Address info:
</h4>
<hr class="hr-8">
<div class="form-group">
    {!! Form::label('address', 'Address', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('address', $address, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'address'])
    </div>

    {!! Form::label('state', 'State', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('state', $state, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'state'])
    </div>
</div>
<div class="form-group">

    {!! Form::label('country', 'Country', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
       {!! Form::select('country', ['india' => 'India'], $country, ['class'=>'form-control border-form']); !!}
        @include('includes.form_fields_validation_message', ['name' => 'country'])
    </div>
    {!! Form::label('zip', 'Zip', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('zip', $zip, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'zip'])
    </div>
</div>
    <h4 class="label label-warning arrowed-in arrowed-right arrowed" >Details of Siblings:
    </h4>
    <hr class="hr-8">
    <div class="form-group">
    <table class="table table-hover table-striped" id="variation_tbl">
        <tr>
            <th >Student</th>
            <th><i class="fa fa-plus new_row pull-right btn btn-info btn-minier" title="Click to add new sibling row"> Add New</i></th>
        </tr>
        <tr id="head_tbl" class="" style="display: none;">
           
            
            <td >
             {!! Form::select('sibling_student_id[]',$data['student'], null, ["class" => "form-control futureSelectPicker" ,'data-live-search'=>'true']) !!}
            </td>
            <td class="col-sm-1"><i class="fa fa-trash delete_row btn btn-danger btn-minier" onclick="closest('tr').remove();"></i></td>
        </tr>
        @if(isset($data['row']))
          @foreach($data['siblings'] as $k=>$v)
            <tr id="head_tbl{{$k}}" class="">
                
                <td>
                 {!! Form::select('sibling_student_id[]',$data['student'], $v, ["class" => "form-control selectpicker" ,'data-live-search'=>'true']) !!}
                </td>
                 <td> <button type="button" onclick="deleteItem({{$k}})" id="Reco" class=""><i class="fa fa-trash  btn  btn-danger btn-minier"></i></button>   </td>    
            </tr>
          @endforeach

       @else
        <tr id="head_tbl" class="">
           
            <td>
             {!! Form::select('sibling_student_id[]',$data['student'], null, ["class" => "form-control selectpicker" ,'data-live-search'=>'true']) !!}
            </td>
             <td></td>
           
        </tr>
         @endif
       
    </table>
 </div>

<?php /*
<h4 class="label label-warning arrowed-in arrowed-right arrowed" >Details of Siblings:
</h4>
<hr class="hr-8">
 <div class="form-group">
     <table class="table table-hover table-striped" id="variation_tbl">
    <tr>
        <th>Course</th>
        <th>Section</th>
        <th >Student</th>
        <th><i class="fa fa-plus new_row pull-right btn btn-info btn-minier" title="Click to add new variation row"> Add New</i></th>
    </tr>
    <tr id="head_tbl" class="" style="display: none;">
       
        <td >
          {!!Form::select('sibling_course_id[]',$faculties,null,['class'=>'form-control',"onChange"=>"loadStudent()"])!!}
        </td>
        
        <td >
            {!! Form::select('sibling_section_id[]',$data['section'], null, ["class" => "form-control border-form upper",'id'=>'sem']) !!}
        </td>
        
        <td >
         {!! Form::select('sibling_student_id[]',$faculties, null, ["class" => "form-control border-form upper"]) !!}
        </td>
        <td class="col-sm-1"><i class="fa fa-trash delete_row btn btn-danger btn-minier" onclick="closest('tr').remove();"></i></td>
    </tr>
    <tr id="head_tbl" class="">
        
        <td >
          {!!Form::select('sibling_course_id[]',$faculties,null,['class'=>'form-control',"onChange"=>"loadStudent()"])!!}
        </td>
       
        <td >
             {!! Form::select('sibling_section_id[]',$data['section'], null, ["class" => "form-control border-form upper",'id'=>'sem']) !!}
        </td>
       
        <td>
         {!! Form::select('sibling_student_id[]',$faculties, null, ["class" => "form-control border-form upper"]) !!}
        </td>
         <td></td>
       
    </tr>
 
   
</table>

 </div>
 */ ?>

{{-- @if(!isset($admData[0]->id) || (isset($admId) && $admId != ""))
<div class="form-group">
    <table id="sample-table-1" class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th>&nbsp;</th>
            <th>Session</th>
            <th>Fee Head</th>
            <th>Fee Amount</th>
            <th>Amount</th>
            <!-- <th>
                <button type="button" class="btn btn-xs btn-primary pull-right" id="load-fee-html">
                    <i class="fa fa-plus" aria-hidden="true"></i> Insert Rows
                </button>
            </th> -->
        </tr>
        </thead>

        <tbody id="fee_wrapper">

           <tr class="option_value" ng-repeat = "x in feedata">
                <td>
                   
                </td>
                 <td >
                    @{{x.session_name}}
                     <input type="hidden" name="fee_masters_id[]" value="@{{x.id}}" readonly >
                </td>
                <td >
                    @{{x.fee_head_title}}
                   
                </td>
                <td >
                    @{{x.fee_amount}}
                   
                </td>
                
                <td>
                    <input type="number" name="fee_amount[]" value="0" max="@{{x.fee_amount}}" required>
                </td>
                
            </tr>
            <div >
                
            </div>
        </tbody>
        <tbody id="getFeeByBatch">
            
        </tbody>

    </table>


    {!! Form::label('payment_type', 'Payment Mode', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::select('payment_type',$pay_type_list, '', ['class'=>'form-control border-form']); !!}
        
    </div>

 {!! Form::label('ref_no', 'Invoice / Ref No', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
       {!! Form::text('ref_no', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'ref_no'])
    </div>
    
    {!! Form::label('remark', 'Remark', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('remark', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'remark'])
    </div>

</div>

@endif
--}}

<!-- <div>
<h4 class="label label-warning arrowed-in arrowed-right arrowed">Temporary Address :
</h4>

<div class="control-group col-sm-12">
    <div class="radio">
        <label>
            {!! Form::checkbox('permanent_address_copier', '', false, ['class' => 'ace', "onclick"=>"CopyAddress(this.form)"]) !!}
            <span class="lbl"> Temporaray Address Same As Permanent Address</span>
        </label>
    </div>
</div>

<hr>
<hr class="hr-8">

<div class="form-group" >
    {!! Form::label('temp_address', 'Address', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('temp_address', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'temp_address'])
    </div>

    {!! Form::label('temp_state', 'State', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('temp_state', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'temp_state'])
    </div>

  
</div>
<div class="form-group" >
     {!! Form::label('temp_country', 'Country', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('temp_country', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'temp_country'])
    </div>
</div>



</div> -->


</div>