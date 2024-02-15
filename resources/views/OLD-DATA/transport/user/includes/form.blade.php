<h4 class="header large lighter blue"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;Assign Transport(Staff/Student)</h4>

<div class="form-group">
    {!! Form::label('user_type', 'Type', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::select('user_type', [""=>"Select Type","1"=>"Student","2"=>"Staff"], null, ['class' => 'form-control','required' => 'required']) !!}
    </div>

    {!! Form::label('reg_no', 'REG No.', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::text('reg_no', $data['reg_no'], ["placeholder" => "", "class" => "form-control border-form","autofocus",'required' => 'required']) !!}
    </div>

    {!! Form::label('status', 'Status', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::select('status', ["active"=>"Active","in-active"=>"In-Active"], null, ['class' => 'form-control']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'status'])
    </div>

</div>

@if(!isset($data['row']))
    <div class="form-group">
        {!! Form::label('route', 'Route', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-2">
            {!! Form::select('route', $data['routes'], null, ['class' => 'form-control','required' => 'required', "onChange" => "loadVehicle(this)"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'route'])
        </div>
        <label class="col-sm-2 control-label">Vehicle</label>
        <div class="col-sm-2">
            <select name="vehicle_select" class="form-control vehicle_select" required>
                <option value="" > Select Vehicle </option>
            </select>
        </div>
          {!! Form::label('duration','Duration',['class'=>'col-sm-2 control-label'])
        !!}
        <div class="col-sm-2">
            {!!Form::select('duration',[""=>"--Select--","monthly"=>"Monthly","quarterly"=>"Quarterly","half_yearly"=>"Half Yearly","yearly"=>"Yearly"],null,['class'=>'form-control', "onChange" => "getrent(this)",'required' => 'required']) !!}
        </div>
    </div>
    <div class="form-group">
      
        {!!Form::label('from','From Date',['class'=>'col-sm-2 control-label'])!!}
        <div class="col-sm-2">
          {!! Form::text('from_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd",'required' => 'required']) !!}
        </div>
         {!!Form::label('to','To Date',['class'=>'col-sm-2 control-label'])!!}
        <div class="col-sm-2">
           {!! Form::text('to_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd",'required' => 'required']) !!}
        </div>
    </div>
    <div class="form-group">
         {!!Form::label('amount','Rent',['class'=>'col-sm-2 control-label'])!!}
         <div class="col-sm-2">
             <span id="rent"></span>
         </div>
         {!!Form::label('total_rent','Total Fare',['class'=>'col-sm-2 control-label'])!!}
         <div class="col-sm-2">
             <input type="text" name="total_fare" id="show" class="form-control" style="display: none;" required>
         </div>
         <div class="col-sm-4"  style="background: #abbac35e;">
             <i class=""><b>Note* </b>Total Fare = Monthly(Rent x 1), Quarterly(Rent x 3), Half Yearly(Rent x 6),Yearly(Rent x 12)</i>
         </div>
         
    </div>
    <div class="form-group">
        {!!Form::label('paid','Paid',['class'=>'col-sm-2 control-label' ]) !!}
        <div class="col-sm-2">
            {!!Form::text('amount_paid',null,['class'=>'form-control'])!!}
        </div>
        {!!Form::label('payment_mode','Payment Mode',['class'=>'col-sm-2 control-label'])!!}
        <div class="col-sm-2">
           
            {!! Form::select('pay_mode',$data['pay_type'], '', ['class'=>'form-control border-form']); !!}
        </div>
        {!!Form::label('ref_no','Ref No.',['class'=>'col-sm-2 control-label'])!!}
        <div class="col-sm-2">
            {!!Form::text('ref_no',null,['class'=>'form-control'])!!}
        </div>
    </div>
@endif
