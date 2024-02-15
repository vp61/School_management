<div class="form-group">
    {!!Form::label('route','Vehicle',['class'=>'col-sm-6 control-label'])!!}
    <div class="col-sm-6">
        {!!Form::select('vehicle_id',$data['vehicle'],null,['class'=>'form-control','required'=>'required'])!!}
        <input type="hidden" name="org_id" value="{{Auth::user()->org_id}}">
    </div>
</div>

<div class="form-group">
    {!!Form::label('distance','Date',['class'=>'col-sm-6 control-label'])!!}
    <div class="col-sm-6">
       {!! Form::text('date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd",'required' => 'required']) !!}
        <input type="hidden" name="org_id" value="{{Auth::user()->org_id}}">
    </div>
</div>
<div class="form-group">
    {!!Form::label('distance','Total distance in a Day',['class'=>'col-sm-6 control-label'])!!}
    <div class="col-sm-6">
        {!!Form::text('distance',null,['class'=>'form-control'])!!}
        <input type="hidden" name="org_id" value="{{Auth::user()->org_id}}">
    </div>
</div>
<div class="form-group">
    {!!Form::label('fuel','Fuel (in Litre)',['class'=>'col-sm-6 control-label'])!!}
    <div class="col-sm-6">
        {!!Form::text('fuel',null,['class'=>'form-control'])!!}
        <input type="hidden" name="org_id" value="{{Auth::user()->org_id}}">
    </div>
</div>
<div class="form-group">
    {!!Form::label('name','Receipt No',['class'=>'col-sm-6 control-label'])!!}
    <div class="col-sm-6">
        {!!Form::text('receipt_no',null,['class'=>'form-control'])!!}
        <input type="hidden" name="org_id" value="{{Auth::user()->org_id}}">
    </div>
</div>
<div class="form-group">
    {!!Form::label('name','Fuel Amount',['class'=>'col-sm-6 control-label'])!!}
    <div class="col-sm-6">
        {!!Form::number('fuel_amount',null,['class'=>'form-control'])!!}
        <input type="hidden" name="org_id" value="{{Auth::user()->org_id}}">
    </div>
</div>
@if(isset($data['row']))
<div class="clearfix form-actions">
    <div class="align-right">            &nbsp; &nbsp; &nbsp;
        <button class="btn btn-info" type="submit" id="filter-btn">
           
                Update
        </button>
    </div>
</div>
@else
<div class="clearfix form-actions">
        <div class="align-right">            &nbsp; &nbsp; &nbsp;
                <button class="btn btn-info" type="submit" id="filter-btn">
                     <i class="fa fa-plus bigger-110"></i>
                        Add 
                </button>
        </div>
</div>
@endif