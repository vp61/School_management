<div class="form-group">
    {!!Form::label('route','Route',['class'=>'col-sm-4 control-label'])!!}
    <div class="col-sm-8">
        {!!Form::select('route_id',$data['route'],null,['class'=>'form-control','required'=>'required'])!!}
        <input type="hidden" name="org_id" value="{{Auth::user()->org_id}}">
    </div>
</div>
<div class="form-group">
	{!!Form::label('name','Stoppage',['class'=>'col-sm-4 control-label'])!!}
	<div class="col-sm-8">
		{!!Form::text('title',null,['class'=>'form-control','required'=>'required'])!!}
        <input type="hidden" name="org_id" value="{{Auth::user()->org_id}}">
	</div>
</div>
<div class="form-group">
    {!!Form::label('distance','Distance',['class'=>'col-sm-4 control-label'])!!}
    <div class="col-sm-8">
        {!!Form::number('distance',null,['class'=>'form-control','required'=>'required'])!!}
        <input type="hidden" name="org_id" value="{{Auth::user()->org_id}}">
    </div>
</div>
<div class="form-group">
    {!!Form::label('amount','Fee Amount',['class'=>'col-sm-4 control-label'])!!}
    <div class="col-sm-8">
        {!!Form::number('fee_amount',null,['class'=>'form-control','required'=>'required'])!!}
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