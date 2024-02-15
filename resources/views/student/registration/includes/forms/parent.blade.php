@php $Grand_name=(isset($admData[0]->grandfather_first_name)) ? $admData[0]->grandfather_first_name : "";
    $father_first_name=(isset($admData[0]->father_first_name)) ? $admData[0]->father_first_name : "";
    $father_eligibility=(isset($admData[0]->father_eligibility)) ? $admData[0]->father_eligibility : "";
    $father_occupation=(isset($admData[0]->father_occupation)) ? $admData[0]->father_occupation : "";
    $father_office=(isset($admData[0]->father_office)) ? $admData[0]->father_office : "";
    $father_office_number=(isset($admData[0]->father_office_number)) ? $admData[0]->father_office_number : "";
    $father_residence_number=(isset($admData[0]->father_residence_number)) ? $admData[0]->father_residence_number : "";

    $father_mobile_1=(isset($admData[0]->father_mobile_1)) ? $admData[0]->father_mobile_1 : "";
    $father_mobile_2=(isset($admData[0]->father_mobile_2)) ? $admData[0]->father_mobile_2 : "";
    $whatsapp_no=(isset($admData[0]->whatsapp_no)) ? $admData[0]->whatsapp_no : "";
    $father_annual_income=(isset($admData[0]->father_annual_income)) ? $admData[0]->father_annual_income : "";
    $father_aadhar_no=(isset($admData[0]->father_aadhar_no)) ? $admData[0]->father_aadhar_no : "";
    $father_email=(isset($admData[0]->father_email)) ? $admData[0]->father_email : "";
    $mother_first_name=(isset($admData[0]->mother_first_name)) ? $admData[0]->mother_first_name : "";
    $mother_eligibility=(isset($admData[0]->mother_eligibility)) ? $admData[0]->mother_eligibility : "";
    $mother_occupation=(isset($admData[0]->mother_occupation)) ? $admData[0]->mother_occupation : "";
    $mother_office=(isset($admData[0]->mother_office)) ? $admData[0]->mother_office : "";
    $mother_office_number=(isset($admData[0]->mother_office_number)) ? $admData[0]->mother_office_number : "";
    $mother_whatsapp_no=(isset($admData[0]->mother_whatsapp_no)) ? $admData[0]->mother_whatsapp_no : "";
    $mother_annual_income=(isset($admData[0]->mother_annual_income)) ? $admData[0]->mother_annual_income : "";
    $mother_aadhar_no=(isset($admData[0]->mother_aadhar_no)) ? $admData[0]->mother_aadhar_no : "";
    
    $mother_residence_number=(isset($admData[0]->mother_residence_number)) ? $admData[0]->mother_residence_number : "";
    $mother_mobile_1=(isset($admData[0]->mother_mobile_1)) ? $admData[0]->mother_mobile_1 : "";
    $mother_mobile_2=(isset($admData[0]->mother_mobile_2)) ? $admData[0]->mother_mobile_2 : "";
    $mother_email=(isset($admData[0]->mother_email)) ? $admData[0]->mother_email : "";
    $guardian_address=(isset($admData[0]->address)) ? $admData[0]->address : "";
    $guardian_first_name=(isset($admData[0]->guardian_first_name)) ? $admData[0]->guardian_first_name : "";
    $guardian_eligibility=(isset($admData[0]->guardian_eligibility)) ? $admData[0]->guardian_eligibility : "";
    $guardian_occupation=(isset($admData[0]->guardian_occupation)) ? $admData[0]->guardian_occupation : "";
    $guardian_office=(isset($admData[0]->guardian_office)) ? $admData[0]->guardian_office : "";
    $guardian_office_number=(isset($admData[0]->guardian_office_number)) ? $admData[0]->guardian_office_number : "";
    $guardian_residence_number=(isset($admData[0]->guardian_residence_number)) ? $admData[0]->guardian_residence_number : "";

    $guardian_mobile_1=(isset($admData[0]->guardian_mobile_1)) ? $admData[0]->guardian_mobile_1 : "";
    $guardian_mobile_2=(isset($admData[0]->guardian_mobile_2)) ? $admData[0]->guardian_mobile_2 : "";
    $guardian_email=(isset($admData[0]->guardian_email)) ? $admData[0]->guardian_email : "";
    $guardian_relation=(isset($admData[0]->guardian_relation)) ? $admData[0]->guardian_relation : "";
    $guardian_address=(isset($admData[0]->guardian_address)) ? $admData[0]->guardian_address : "";
    //$=(isset($admData[0]->)) ? $admData[0]-> : "";
@endphp


<div ng-controller="feeCtrl">
    <h4 class="header large lighter blue" ng-click="toggle()">  &nbsp;Parent Details :
    <section style="text-align: right;">
        <i class="ace-icon glyphicon glyphicon-plus"  >(Expand)</i>
    </section> 
        
  </h4>
<div ng-show="myVar">
<div class="label label-warning arrowed-in arrowed-right arrowed">Grand Father's Detail</div>
<hr class="hr-8">
<div class="form-group">
    {!! Form::label('grandfather_name', 'NAME OF GRAND FATHER', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-3">
        {!! Form::text('grandfather_first_name', $Grand_name, [ "class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'grandfather_first_name'])
    </div>
    <!--div class="col-sm-3">
        {!! Form::text('grandfather_middle_name', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'grandfather_middle_name'])
    </div>
    <div class="col-sm-3">
        {!! Form::text('grandfather_last_name', null, [ "class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'grandfather_last_name'])
    </div-->
</div>

<div class="label label-warning arrowed-in arrowed-right arrowed">Father's Detail</div>
<hr class="hr-8">
@php
/*
@endphp
<div class="form-group">
    
    {!! Form::label('father_name', 'NAME OF FATHER', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-3">
        {!! Form::text('father_first_name', $father_first_name, [ "class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'father_first_name'])
    </div>
    
    <div class="col-sm-3">
        {!! Form::text('father_middle_name', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'father_first_name'])
    </div>
    <div class="col-sm-3">
        {!! Form::text('father_last_name', null, [ "class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'father_last_name'])
    </div>
</div>
@php
*/
@endphp

<div class="form-group">
    {!! Form::label('father_eligibility', 'Eligibility', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('father_eligibility', $father_eligibility, ["placeholder" => "", "class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'father_eligibility'])
    </div>

    {!! Form::label('father_occupation', 'Ocupation', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('father_occupation', $father_occupation, ["placeholder" => "", "class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'father_occupation'])
    </div>
</div>
<div class="form-group">
    {!! Form::label('whatsapp_no', 'Whatsapp No', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('whatsapp_no', $whatsapp_no, ["placeholder" => "", "class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'father_eligibility'])
    </div>

    {!! Form::label('father_annual_income', 'Annual Income', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('father_annual_income', $father_annual_income, ["placeholder" => "", "class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'father_occupation'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('father_office', 'Office', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('father_office', $father_office, ["placeholder" => "", "class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'father_office'])
    </div>

    {!! Form::label('father_office_number', 'Office Number', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('father_office_number', $father_office_number, ["placeholder" => "", "class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'father_office_number'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('father_residence_number', 'Residence Number', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('father_residence_number', $father_residence_number, ["class" => "form-control border-form input-mask-mobile"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'father_residence_number'])
    </div>

    {!! Form::label('father_mobile_1', 'Mobile 1', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('father_mobile_1', $father_mobile_1, ["class" => "form-control border-form input-mask-mobile"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'father_mobile_1'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('father_mobile_2', 'Mobile 2', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('father_mobile_2', $father_mobile_2, ["class" => "form-control border-form input-mask-mobile"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'father_mobile_2'])
    </div>

    {!! Form::label('father_email', 'E-mail', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('father_email', $father_email, ["class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'father_email'])
    </div>
</div>
<div class="form-group">
    {!! Form::label('father_aadhar_no', 'Father Aadhar no', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('father_aadhar_no', $father_aadhar_no, ["class" => "form-control border-form input-mask-mobile"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'father_residence_number'])
    </div>
</div>

<div class="label label-warning arrowed-in arrowed-right arrowed">Mothers's Detail</div>
<hr class="hr-8">

<div class="form-group">
   <?php /* {!! Form::label('mother_name', 'NAME OF MOTHER', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-3">
        {!! Form::text('mother_first_name', $mother_first_name, [ "class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'mother_first_name'])
    </div> */?>
    <!--div class="col-sm-3">
        {!! Form::text('mother_middle_name', null, ["class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'mother_first_name'])
    </div>
    <div class="col-sm-3">
        {!! Form::text('mother_last_name', null, [ "class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'mother_last_name'])
    </div-->
</div>

<div class="form-group">
    {!! Form::label('mother_eligibility', 'Eligibility', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('mother_eligibility', $mother_eligibility, ["placeholder" => "", "class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'mother_eligibility'])
    </div>

    {!! Form::label('mother_occupation', 'Ocupation', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('mother_occupation', $mother_occupation, ["placeholder" => "", "class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'mother_occupation'])
    </div>
</div>
<div class="form-group">
    {!! Form::label('mother_whatsapp_no', 'Whatsapp No', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('mother_whatsapp_no', $mother_whatsapp_no, ["placeholder" => "", "class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'father_eligibility'])
    </div>

    {!! Form::label('mother_annual_income', 'Annual Income', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('mother_annual_income', $mother_annual_income, ["placeholder" => "", "class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'father_occupation'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('mother_office', 'Office', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('mother_office', $mother_office, ["placeholder" => "", "class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'mother_office'])
    </div>

    {!! Form::label('mother_office_number', 'Office Number', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('mother_office_number', $mother_office_number, ["placeholder" => "", "class" => "form-control border-form upper"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'mother_office_number'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('mother_residence_number', 'Residence Number', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('mother_residence_number', $mother_residence_number, ["class" => "form-control border-form input-mask-phone"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'mother_residence_number'])
    </div>

    {!! Form::label('mother_mobile_1', 'Mobile 1', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('mother_mobile_1', $mother_mobile_1, ["class" => "form-control border-form input-mask-mobile"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'mother_mobile_1'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('mother_mobile_2', 'Mobile 2', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('mother_mobile_2', $mother_mobile_2, ["class" => "form-control border-form input-mask-mobile"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'mother_mobile_2'])
    </div>

    {!! Form::label('mother_email', 'E-mail', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('mother_email', $mother_email, ["class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'mother_email'])
    </div>
</div>
<div class="form-group">
    {!! Form::label('mother_aadhar_no', 'Mother Aadhar no', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('mother_aadhar_no', $mother_aadhar_no, ["class" => "form-control border-form input-mask-mobile"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'father_residence_number'])
    </div>
</div>

<hr class="hr-8">
<div class="label label-warning arrowed-in arrowed-right arrowed">Guardian's Detail</div>

<div class="control-group col-sm-12">
    <div class="radio">
        <label>
            {!! Form::radio('guardian_is', 'father_as_guardian', false, ['class' => 'ace', "onclick"=>"FatherAsGuardian(this.form)"]) !!}
            <span class="lbl"> Father is Guardian</span>
        </label>
        <label>
            {!! Form::radio('guardian_is', 'mother_as_guardian', false, ['class' => 'ace',"onclick"=>"MotherAsGuardian(this.form)"]) !!}
            <span class="lbl"> Mother is Guardian</span>
        </label>
        <label>
            {!! Form::radio('guardian_is', 'other_guardian', true, ['class' => 'ace', "onclick"=>"OtherGuardian(this.form)"]) !!}
            <span class="lbl"> Other's</span>
        </label>
        <label>
            {!! Form::radio('guardian_is', 'link_guardian', false, ['class' => 'ace', "onclick"=>"linkGuardian(this.form)"]) !!}
            <span class="lbl"> Link Guardian</span>
        </label>
    </div>
</div>
<hr>
<div id="guardian-detail">
    <hr class="hr-8">
    <div class="form-group">
        {!! Form::label('guardian_name', 'NAME OF GUARDIAN', ['class' => 'col-sm-3 control-label']) !!}
        <div class="col-sm-3">
            {!! Form::text('guardian_first_name', $guardian_first_name, [ "class" => "form-control border-form upper"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'guardian_first_name'])
        </div>
        <!--div class="col-sm-3">
            {!! Form::text('guardian_middle_name', null, ["class" => "form-control border-form upper"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'guardian_first_name'])
        </div>
        <div class="col-sm-3">
            {!! Form::text('guardian_last_name', null, [ "class" => "form-control border-form upper"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'guardian_last_name'])
        </div-->
    </div>

    <div class="form-group">
        {!! Form::label('guardian_eligibility', 'Eligibility', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::text('guardian_eligibility', $guardian_eligibility, ["placeholder" => "", "class" => "form-control border-form upper"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'guardian_eligibility'])
        </div>

        {!! Form::label('guardian_occupation', 'Ocupation', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::text('guardian_occupation', $guardian_occupation, ["placeholder" => "", "class" => "form-control border-form upper"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'guardian_occupation'])
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('guardian_office', 'Office', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::text('guardian_office', $guardian_office, ["placeholder" => "", "class" => "form-control border-form upper"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'guardian_office'])
        </div>

        {!! Form::label('guardian_office_number', 'Office Number', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::text('guardian_office_number', $guardian_office_number, ["placeholder" => "", "class" => "form-control border-form input-mask-mobile"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'guardian_office_number'])
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('guardian_residence_number', 'Residence Number', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::text('guardian_residence_number', $guardian_residence_number, ["class" => "form-control border-form input-mask-phone"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'guardian_residence_number'])
        </div>

        {!! Form::label('guardian_mobile_1', 'Mobile 1', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::text('guardian_mobile_1', $guardian_mobile_1, ["class" => "form-control border-form input-mask-mobile"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'guardian_mobile_1'])
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('guardian_mobile_2', 'Mobile 2', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::text('guardian_mobile_2', $guardian_mobile_2, ["class" => "form-control border-form input-mask-mobile"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'guardian_mobile_2'])
        </div>

        {!! Form::label('guardian_email', 'E-mail', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::text('guardian_email', $guardian_email, ["class" => "form-control border-form"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'guardian_email'])
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('guardian_relation', 'Relation', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::text('guardian_relation', $guardian_relation, ["class" => "form-control border-form upper"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'guardian_relation'])
        </div>

        {!! Form::label('guardian_address', 'Guardian Address', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::text('guardian_address', $guardian_address, ["class" => "form-control border-form upper"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'guardian_address'])
        </div>
    </div>
</div>
<div id="link-guardian-detail">
    <div class="form-group">
        {!! Form::label('guardian_info', 'Find Guardian Using Name | Mobile Number | Email & Click on Link Now ', ['class' => 'col-sm-12 control-label align-center']) !!}
        <div class="col-sm-12">
            {!! Form::select('guardian_link_id', [], null, ["placeholder" => "Type Student Reg.No. or Guardians Name...", "class" => "col-xs-12 col-sm-12", "style" => "width: 100%;"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'subject_id'])

            <hr>
            <div class="align-right">
                <button type="button" class="btn btn-sm btn-primary" id="load-guardian-html-btn">
                    <i class="fa fa-link bigger-120"></i> Link Now
                </button>
            </div>
        </div>
    </div>
    <div class="space-4"></div>
<div id="guardian_wrapper">

</div>
</div>
</div>
</div>
