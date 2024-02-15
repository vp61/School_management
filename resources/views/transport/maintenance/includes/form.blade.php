<div class="form-group">
    {!!Form::label('route','Vehicle',['class'=>'col-sm-4 control-label'])!!}
    <div class="col-sm-8">
        {!!Form::select('vehicle_id',$data['vehicle'],null,['class'=>'form-control','required'=>'required'])!!}
        <input type="hidden" name="org_id" value="{{Auth::user()->org_id}}">
    </div>
</div>
<div class="form-group">
    {!!Form::label('Problem','Problem',['class'=>'col-sm-4 control-label'])!!}
    <div class="col-sm-8">
        {!!Form::text('problem',null,['class'=>'form-control','required'=>'required'])!!}
        <input type="hidden" name="org_id" value="{{Auth::user()->org_id}}">
    </div>
</div>

<div class="form-group">
    {!!Form::label('distance','Date',['class'=>'col-sm-4 control-label'])!!}
    <div class="col-sm-8">
       {!! Form::text('maintenance_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd",'required' => 'required']) !!}
        <input type="hidden" name="org_id" value="{{Auth::user()->org_id}}">
    </div>
</div>
<div class="form-group">
    {!!Form::label('Problem','Work Performed',['class'=>'col-sm-4 control-label'])!!}
    <div class="col-sm-8">
        {!!Form::text('work_performed',null,['class'=>'form-control','required'=>'required'])!!}
        <input type="hidden" name="org_id" value="{{Auth::user()->org_id}}">
    </div>
</div>
<div class="form-group">
    {!!Form::label('Problem','Performed By',['class'=>'col-sm-4 control-label'])!!}
    <div class="col-sm-8">
        {!!Form::text('performed_by',null,['class'=>'form-control','required'=>'required'])!!}
        <input type="hidden" name="org_id" value="{{Auth::user()->org_id}}">
    </div>
</div>
<div class="form-group">
    {!!Form::label('name','Cost',['class'=>'col-sm-4 control-label'])!!}
    <div class="col-sm-8">
        {!!Form::number('maintenance_charge',null,['class'=>'form-control','required'=>'required'])!!}
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