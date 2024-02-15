@php 
    //  dd($admData);
    $name=(isset($admData[0]->first_name)) ? $admData[0]->first_name:"";
    $indose_number=(isset($admData[0]->indose_number)) ? $admData[0]->indose_number:"";
    $passport_no=(isset($admData[0]->passport_no)) ? $admData[0]->passport_no:"";
    
    $catego = (isset($admData[0]->category_id)) ? $admData[0]->category_id:"";
    $course=(isset($admData[0]->faculty)) ? $admData[0]->faculty: "";
    $course2=(isset($admData[0]->course)) ? $admData[0]->course: "";

    $semester=(isset($admData[0]->semester)) ? $admData[0]->semester: "";
    $session_id=(isset($admData[0]->session_id)) ? $admData[0]->session_id : "";
    $id=(isset($admData[0]->id)) ? $admData[0]->id: "";
    $date_of_birth=(isset($admData[0]->date_of_birth)) ? $admData[0]->date_of_birth:"";
    
    $gender=(isset($admData[0]->gender)) ? $admData[0]->gender:"";
    //$mobile= ? $admData[0]->mobile_1:"";
    $country=(isset($admData[0]->country)) ? $admData[0]->country:"";
    $state=(isset($admData[0]->state)) ? $admData[0]->state:"";
    
    $zip=(isset($admData[0]->zip)) ? $admData[0]->zip:"";
    $city=(isset($admData[0]->city)) ? $admData[0]->city:"";
    $address=(isset($admData[0]->address)) ? $admData[0]->address:"";
    $randomString = (!isset($randomString)) ? $admData[0]->reg_no : $randomString;
    // $email=(isset($admData[0]->email)) ? $admData[0]->email : "";
    $email=(isset($admData[0]->email)) ? $admData[0]->email : $randomString."@asha.ac.in";
    
    //$=(isset($admData[0]->)) ? $admData[0]-> : ""; 
@endphp

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

{{-- code sahi hai apply later 
@if(request()->get('admId'))
    @php
        $fld_arr=['branch_id','org_id', 'first_name','email','admission_date','mobile','date_of_birth','gender','course','academic_status','country','state','city','address','extra_info','responce','reference','admission_fee','category_id','session_id'];
    @endphp
@else
    @php
        $fld_arr=['id','branch_id','org_id', 'first_name','email','admission_date','mobile','date_of_birth','gender','course','academic_status','country','state','city','address','extra_info','responce','reference','admission_fee','category_id','session_id', 'created_by', 'last_updated_by', 'reg_no', 'reg_date', 'university_reg', 'faculty', 'semester','blood_group','nationality', 'mother_tongue', 'student_image','status', 'zip'];
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
   {!! Form::label('College', 'College', ['class' => 'col-sm-2 control-label']) !!}
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
   
        <label class="col-sm-2 control-label">Course</label>
        <div class="col-sm-4">
        @if(!isset($admData[0]->id) || (isset($admId) && $admId != ""))
        <select name="faculty" id="course" class="form-control" ng-model="selectedCourse" ng-change="coursechange(selectedCourse)" required>
        @else
        <select name="faculty" id="course" class="form-control" required>
        @endif
        <option value="">Select Course</option> 
            @foreach($courses as $x)
            @php $selected=($x->id==$course || $x->id==$course2) ? "selected":""; @endphp
                <option {{$selected}} value="{{ $x->id }}" >{{ $x->faculty }}</option>
            @endforeach 
        </select>
        </div>
    {!! Form::label('Semester', 'Semester', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            <select name="semester" class="form-control semester"  required> 
                @foreach($Semester as $sem)
            @php $selected=($semester == $sem->id) ? "selected":""; @endphp
            <option {{$selected}} value="{{$sem->id}}">{{$sem->semester}}</option>
                @endforeach
            </select>
            @include('includes.form_fields_validation_message', ['name' => 'semester'])
        </div>   
</div>  




<div class="label label-warning arrowed-in arrowed-right arrowed">Student Information</div>
<hr class="hr-8">

@if(!isset($admData[0]->id) || (isset($admId) && $admId != ""))
<div class="form-group">
   {{--{!! Form::label('reg_no', 'Student Reg.No.', ['class' => 'col-sm-4 control-label']) !!}
          <div class="col-sm-2">
              <Strong  style="font-size: 20px;color: #c3c3c3;font-family: monospace;"></Strong>
              @include('includes.form_fields_validation_message', ['name' => 'reg_no'])
          </div>--}}

    {!! Form::label('reg_date', 'Reg Date', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        <input class="form-control border-form upper" max="{{date('Y-m-d')}}" name="reg_date" type="date" value="{{date('Y-m-d')}}">
    </div>

</div>
@endif

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
    
    {!! Form::label('first_name', 'NAME OF STUDENT', ['class' => 'col-sm-2 control-label',]) !!}
    <div class="col-sm-4">
        {!! Form::text('first_name', $name, ["class" => "form-control border-form upper" ,"required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'first_name'])
    </div>
    
    {!! Form::label('father_name', 'NAME OF FATHER', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('father_first_name', null, [ "class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'father_first_name'])
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
        {!! Form::text('email',null, ["class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'email'])
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
<h4 class="label label-warning arrowed-in arrowed-right arrowed" >Fee Collection:
</h4>
<hr class="hr-8">
@if(!isset($admData[0]->id) || (isset($admId) && $admId != ""))
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