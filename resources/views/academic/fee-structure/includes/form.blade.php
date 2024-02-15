<div class="form-group">
	{!!Form::label('class','Class',['class'=>'col-sm-4 control-label'])!!}
	<div class="col-sm-8">
		{!!Form::select('faculty_id',$data['faculty'],null,['class'=>'form-control','required'=>'required'])!!}
        <input type="hidden" name="org_id" value="{{Auth::user()->org_id}}">
	</div>
</div>
<div class="form-group">
    {!!Form::label('from','From Month',['class'=>'col-sm-4 control-label'])!!}
    <div class="col-sm-8">
        {!!Form::select('from_month',$data['month'],null,['class'=>'form-control','required'=>'required'])!!}
        <input type="hidden" name="org_id" value="{{Auth::user()->org_id}}">
    </div>
</div>
<div class="form-group">
    {!!Form::label('to','To Month',['class'=>'col-sm-4 control-label'])!!}
    <div class="col-sm-8">
        {!!Form::select('to_month',$data['month'],null,['class'=>'form-control','required'=>'required'])!!}
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