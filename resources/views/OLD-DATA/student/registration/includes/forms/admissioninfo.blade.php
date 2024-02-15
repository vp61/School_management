<span class="label label-info arrowed-in arrowed-right arrowed responsive">Red mark input are required. </span>




<hr class="hr-16">
<div class="form-group">

    {!! Form::label('College', 'College', ['class' => 'col-sm-3 control-label']) !!}
    @if(empty($data['branch_name']))
     <div class="col-sm-4">
            <select name="branch_id" id="department" class="form-control" co>
            <option value="{{$data['selected_branch'][0]['id']}}"> {{$data['selected_branch'][0]['branch_name']}}</option>

            </select>
    </div>
    @endif



    <input type="hidden" name="org_id" value="{{Auth::user()->org_id}}">
</div>
<div class="form-group">

    {!! Form::label('reg_no', 'REG.NO.', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('reg_no', null, ["placeholder" => "", "class" => "form-control border-form input-mask-registration"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'reg_no'])
    </div>

    {!! Form::label('reg_date', 'DATE', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('reg_date', null, ["data-date-format" => "yyyy-mm-dd", "class" => "form-control date-picker border-form input-mask-date"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'reg_date'])
    </div>

    {!! Form::label('university_reg', 'UNIVERSITY REG. NO.', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('university_reg', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'university_reg'])
    </div>
</div>

<div class="form-group">
    @if (!isset($data['row']))
        <label class="col-sm-2 control-label">Course</label>
        <div class="col-sm-4">
            {!! Form::select('faculty', $data['faculties'], $registration[0]['course'], ['class' => 'form-control', 'onChange' => 'loadSemesters(this);', "required" => "required"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'faculty'])
        </div>

        <label class="col-sm-1 control-label">Semester</label>
        <div class="col-sm-2">
            <select name="semester" class="form-control semester" required >
            </select>
            @include('includes.form_fields_validation_message', ['name' => 'semester'])
        </div>
        <label class="col-sm-1 control-label">Status</label>
        <div class="col-sm-2">
            {!! Form::select('academic_status', $data['academic_status'], 1, ['class' => 'form-control']) !!}
            @include('includes.form_fields_validation_message', ['name' => 'academic_status'])
        </div>
    @else
        <label class="col-sm-2 control-label">Faculty</label>
        <div class="col-sm-4">
            {!! Form::select('faculty', $data['faculties'], null, ['class' => 'form-control',"disabled"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'faculty'])
        </div>

        <label class="col-sm-1 control-label">Semester</label>
        <div class="col-sm-2">
            {!! Form::select('semester', $data['semester'], null, ['class' => 'form-control',"disabled"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'semester'])
        </div>
        <label class="col-sm-1 control-label">Status</label>
        <div class="col-sm-2">
            {!! Form::select('academic_status', $data['academic_status'], null, ['class' => 'form-control']) !!}
            @include('includes.form_fields_validation_message', ['name' => 'academic_status'])
        </div>
    @endif
</div>

<div class="form-group">
    {!! Form::label('first_name', 'NAME OF STUDENT', ['class' => 'col-sm-4 control-label',]) !!}
    <div class="col-sm-8">
        {!! Form::text('first_name', $registration[0]['first_name'], ["class" => "form-control border-form upper","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'first_name'])
    </div>
    
</div>

<div class="form-group">
    {!! Form::label('date_of_birth', 'Date of Birth', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('date_of_birth',$registration[0]['date_of_birth'], ["data-date-format" => "yyyy-mm-dd", "class" => "form-control border-form date-picker input-mask-date"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'date_of_birth'])
    </div>

    {!! Form::label('gender', 'Gender', ['class' => 'col-sm-2 control-label']) !!}
   
     <div class="col-sm-2">
        {!! Form::select('gender', ['MALE' => 'MALE', 'FEMALE' => 'FEMALE', 'OTHER' => 'OTHER'], $registration[0]['gender'], ['class'=>'form-control border-form']); !!}
        @include('includes.form_fields_validation_message', ['name' => 'gender'])
    </div>

    {!! Form::label('blood_group', 'Blood Group', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::select('blood_group', ['' => 'Select Blood Group','A+' => 'A+', 'A-' => 'A-', 'B+' => 'B+','B-' => 'B-','AB+' => 'AB+', 'AB-' => 'AB-', 'O+' => 'O+','O-' => 'O-', ], null,
        [ 'class'=>'form-control border-form']); !!}
        @include('includes.form_fields_validation_message', ['name' => 'blood_group'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('nationality', 'Nationality', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('nationality', $registration[0]['country'], ["placeholder" => "", "class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'nationality'])
    </div>

    {!! Form::label('mother_tongue', 'Mother Tong', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('mother_tongue', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'mother_tongue'])
    </div>

    {!! Form::label('email', 'E-mail', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-3">
        {!! Form::text('email', $registration[0]['email'], ["class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'email'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('extra_info', 'Extra Info', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::textarea('extra_info', $registration[0]['extra_info'], ["class" => "form-control border-form", "rows"=>"1"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'extra_info'])
    </div>
</div>

<div class="label label-warning arrowed-in arrowed-right arrowed">Contact</div>
<hr class="hr-8">
<div class="form-group">
    {!! Form::label('home_phone', 'Phone', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-3">
        {!! Form::text('home_phone', null, ["class" => "form-control border-form input-mask-phone"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'home_phone'])
    </div>

    {!! Form::label('mobile_1', 'Mobile 1', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-3">
        {!! Form::text('mobile_1', $registration[0]['mobile'], ["class" => "form-control border-form input-mask-mobile"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'mobile_1'])
    </div>

    {!! Form::label('mobile_2', 'Mobile 2', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-3">
        {!! Form::text('mobile_2', null, ["class" => "form-control border-form input-mask-mobile"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'mobile_2'])
    </div>
</div>

<div   ng-controller="feeCtrl">
<h4 class="header large lighter blue" ng-click="addtoggle()">Permanent Address :

 <section style="text-align: right;">
        <i class="ace-icon glyphicon glyphicon-plus" >(Expand)</i>
</section> 
</h4>
<hr class="hr-8">
<div class="form-group" ng-show="myadd">
    {!! Form::label('address', 'Address', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-3">
        {!! Form::text('address', $registration[0]['address'], ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'address'])
    </div>

    {!! Form::label('state', 'State', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-3">
        {!! Form::text('state', $registration[0]['state'], ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'state'])
    </div>

    {!! Form::label('country', 'Country', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-3">
        {!! Form::text('country', $registration[0]['country'], ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'country'])
    </div>
</div>

<div ng-controller="feeCtrl">
<!-- <div class="label label-warning arrowed-in arrowed-right arrowed" ng-click="tempaddtoggle()">Temporary Address</div> -->
<h4 class="header large lighter blue" ng-click="tempaddtoggle()">Temporary Address :

 <section style="text-align: right;">
        <i class="ace-icon glyphicon glyphicon-plus"  >(Expand)</i>
</section> 
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

<div class="form-group" ng-show="mytempadd">
    {!! Form::label('temp_address', 'Address', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-3">
        {!! Form::text('temp_address', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'temp_address'])
    </div>

    {!! Form::label('temp_state', 'State', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-3">
        {!! Form::text('temp_state', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'temp_state'])
    </div>

    {!! Form::label('temp_country', 'Country', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-3">
        {!! Form::text('temp_country', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'temp_country'])
    </div>
</div>
</div>
</div>

