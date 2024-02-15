<h4 class="header large lighter blue"><i class="fa fa-search" aria-hidden="true"></i>&nbsp;{{ $panel }} Filter</h4>

<div class="form-group">
     {!! Form::open(['route' => $base_route,'method'=>'GET']) !!}
    {!! Form::label('tr_head', 'Head', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::select('tr_head', $data['tr_heads'], null, ['class' => 'form-control select_live_search']) !!}
    </div>

    {!! Form::label('tr_date_start', 'Date', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        <div class="input-group">
            {!! Form::text('tr_date_start', null, ["class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
            <span class="input-group-addon">
                <i class="fa fa-exchange"></i>
            </span>
            {!! Form::text('tr_date_end', null, [ "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'tr_date_start'])
            @include('includes.form_fields_validation_message', ['name' => 'tr_date_end'])
        </div>
    </div>
</div>
<div class="form-group">
     {!! Form::label('type', 'Type', ['class' => 'col-sm-2 control-label']) !!}
     <div class="col-sm-4">
       {!!Form::select('type',[''=>'--Select Type--','Credit'=>'Credit','Debit'=>'Debit'],null,['class'=>'form-control'])!!}
    </div>
    {!! Form::label('rcp', 'Receipt No.', ['class' => 'col-sm-2 control-label']) !!}
     <div class="col-sm-4">
       {!!Form::text('rcp_no',null,['class'=>'form-control','placeholder'=>'Enter Receipt No.'])!!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('branch', 'Branch', ['class' => 'col-sm-2 control-label']) !!}
     <div class="col-sm-4">
       {!!Form::select('branch_type',['1'=>'All Branches','2'=>'Current Branch'],null,['class'=>'form-control'])!!}
    </div>
</div>
<div class="form-group clearfix form-actions">
    <div class="align-right">
        <button class="btn btn-info" type="submit" id="filter-btn">
            <i class="fa fa-filter bigger-110"></i>
            Search
        </button>
    </div>
</div>
{!!Form::close()!!}