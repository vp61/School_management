<h4 class="header large lighter blue" id="filterBox"><i class="fa fa-search" aria-hidden="true"></i>&nbsp;Search Student/Staff</h4>
<div class="form-horizontal" id="filterDiv">
    <div class="form-group">
        
        {!! Form::label('user_type', 'Type', ['class' => 'col-sm-1 control-label']) !!}
        <div class="col-sm-2">
            {!! Form::select('member_type', [""=>"Select Type","1"=>"Student","2"=>"Staff"], null, ['class' => 'form-control']) !!}
        </div>

        {!! Form::label('reg_no', 'REG No.', ['class' => 'col-sm-1 control-label']) !!}
        <div class="col-sm-2">
            {!! Form::text('reg_no', null, ["placeholder" => "", "class" => "form-control border-form","autofocus"]) !!}
           
        </div>
        {!!Form::label('from','From Date',['class'=>'col-sm-1 control-label'])!!}
        <div class="col-sm-2">
             {!! Form::text('from_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
        </div>
        {!!Form::label('to_date','To Date',['class'=>'col-sm-1 control-label'])!!}
        <div class="col-sm-2">
             {!! Form::text('to_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
        </div>
        
    </div>
    <div class="form-group">
        {!!Form::label('route','Routes',['class'=>'col-sm-1 control-label'])!!}
        <div class="col-sm-2">
             {!! Form::select('route', $data['routes'], null, ['class' => 'form-control']) !!}
            @include('includes.form_fields_validation_message', ['name' => 'route'])
        </div>
        {!!Form::label('pay_mode','Mode',['class'=>'col-sm-1 control-label'])!!}
        <div class="col-sm-2">  
            {!! Form::select('pay_mode',$data['pay_type'], '', ['class'=>'form-control border-form']); !!}
        </div>
        {!!Form::label('ref_no','Ref No',['class'=>'col-sm-1 control-label'])!!}
        <div class="col-sm-2">
            {!!Form::text('ref_no',null,['class'=>'form-control'])!!}
        </div>
    </div>    
    <div class="clearfix form-actions">
        <div class="align-right">            &nbsp; &nbsp; &nbsp;
            <button class="btn btn-info" type="submit" id="filter-btn">
                <i class="fa fa-filter bigger-110"></i>
                Search
            </button>
        </div>
    </div>
</div>