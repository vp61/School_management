<h4 class="header large lighter blue"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;{{ $panel }}</h4>
<div class="form-group">
    {!! Form::label('user_type', 'Type', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::select('user_type', [""=>"--Select Type--","1"=>"Student","2"=>"Staff"], null, ['class' => 'form-control','required'=>'required','onchange'=>'studentStaffValidation(this)','id'=>'user']) !!}
    </div>
    <span class="onstd">
        {!! Form::label('course',env('course_label'), ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::select('course', $data['course'], null, ['class' => 'form-control','required'=>'required','id'=>'course','onchange'=>'loadStudent()']) !!}
        </div>
    </span>
    <span class="onstaff" style="display: none;">
             {!! Form::label('staff', 'Staff', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::select('memberId', $data['staff'], null, ['required'=>'required','id'=>'staff','class'=>'form-control','style'=>'width:100%'])
             !!}
        </div>
    </span>

   {{--  {!! Form::label('reg_no', 'REG No.', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('reg_no', $data['reg_no'], ["placeholder" => "", "class" => "form-control border-form","autofocus",'required'=>'required']) !!}
    </div> --}}

   {{-- {!! Form::label('status', 'Status', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::select('status', ["1"=>"Active","0"=>"Leave"], null, ['class' => 'form-control']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'status'])
    </div>--}}

</div>
<div class="form-group onstd">   
        {!!Form::label('section','Section',['class'=>'col-sm-2 control-label '])!!}
        <div class="col-sm-4">
            {!!Form::select('section',$data['section'],null,['class'=>'form-control ', 'required'=>'required','id'=>'section','onchange'=>'loadStudent()'])!!}
        </div>
        {!!Form::label('student','Student',['class'=>'col-sm-2 control-label '])!!}
        <div class="col-sm-4">
            {!!Form::select('memberId',[""=>"Select"],null,['class'=>'form-control ', 'required'=>'required','id'=>'student'])!!}
        </div>
    </span>   
</div>
@if(!isset($data['row']))
 <div class="form-group">

    {!! Form::label('hostel', 'Hostel', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::select('hostel', $data['hostels'], null, ['class' => 'form-control',"onChange" => "loadBlock(this)",'id'=>'hostel','required'=>'required']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'hostel'])
    </div>
    {!! Form::label('block', 'BLock', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!!Form::select('block',[""=>"Select"],null,["class"=>"form-control block_select","onChange"=>"loadFloor(this)","id"=>"block",'required'=>'required'])!!}
    </div>
     
</div>
<div class="form-group">
       {!! Form::label('floor', 'Floor', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!!Form::select('floor',[""=>"Select"],null,["class"=>"form-control floor_select","onChange"=>"loadRooms(this)","id"=>"floor",'required'=>'required'])!!}
    </div>

        {!! Form::label('room', 'Room', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!!Form::select('room',[""=>"Select"],null,["class"=>"form-control room_select","onChange"=>"loadBeds(this)","id"=>"room",'required'=>'required'])!!}
    </div>
</div>  
<div class="form-group">
       {!! Form::label('bed', 'Bed', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!!Form::select('bed',[""=>"Select"],null,["class"=>"form-control bed_select","id"=>"bed","required"=>"required","onChange"=>"loadRate(this)"])!!}
    </div>
     {!! Form::label('rate', 'Rate', ['class' => 'col-sm-2 control-label']) !!}
     <div class="col-sm-4">
        <span id="bed-text"></span>
       
    </div>
</div>    
<div class="form-group">
    {!!Form::label('paid','Paid',['class'=>'col-sm-2 control-label'])!!}
    <div class="col-sm-4">
        {!!Form::text('paid',null,['class'=>'form-control','onkeyup'=>'payModeValidation(this)','id'=>'paid','required'=>'required'])!!}
    </div>
    {!!Form::label('pay_mode','Pay Mode',['class'=>'col-sm-2 control-label'])!!}
    <div class="col-sm-4">
        {!!Form::select('mode',$data['mode'],null,['class'=>'form-control','id'=>'payMode'])!!}
    </div>
</div>
<div class="form-group">
    {!!Form::label('ref_no','Ref No',['class'=>'col-sm-2 control-label'])!!}
    <div class="col-sm-4">
        {!!Form::text('ref_no',null,['class'=>'form-control'])!!}
    </div>
    {!!Form::label('date','Date',['class'=>'col-sm-2 control-label'])!!}
    <div class="col-sm-4">
       <input type="date"  name="reciept_date" class="input-mask-date date-picker form-control"  required />
    </div>
</div> 
@endif
