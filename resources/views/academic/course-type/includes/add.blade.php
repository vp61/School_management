<div class="form-group">
	{!!Form::label('name','Name',['class'=>'col-sm-4 control-label'])!!}
	<div class="col-sm-8">
		{!!Form::text('name',null,['class'=>'form-control','required'=>'required'])!!}
        <input type="hidden" name="org_id" value="{{Auth::user()->org_id}}">
	</div>
</div>
<div class="clearfix form-actions">
                <div class="align-right">            &nbsp; &nbsp; &nbsp;
                        <button class="btn btn-info" type="submit" id="filter-btn">
                             <i class="fa fa-plus bigger-110"></i>
                                Add 
                        </button>
                </div>
        </div>