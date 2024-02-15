<div class="form-group">
	{!!Form::label('name','Name',['class'=>'col-sm-4 control-label'])!!}
	<div class="col-sm-8">
		{!!Form::text('name',null,['class'=>'form-control','required'=>'required'])!!}
	</div>
</div>
<div class="form-group">
    {!!Form::label('email','Email',['class'=>'col-sm-4 control-label'])!!}
    <div class="col-sm-8">
        {!!Form::email('email',null,['class'=>'form-control','rows'=>'3'])!!}
        
    </div>
</div>
<div class="form-group">
    {!!Form::label('mobile','Mobile',['class'=>'col-sm-4 control-label'])!!}
    <div class="col-sm-8">
        {!!Form::number('mobile',null,['class'=>'form-control','required'=>'required'])!!}
    </div>
</div>
<div class="form-group">
    {!!Form::label('alt_mobile','Alt. Mobile',['class'=>'col-sm-4 control-label'])!!}
    <div class="col-sm-8">
        {!!Form::number('alternate_mobile',null,['class'=>'form-control'])!!}
        
    </div>
</div>
<div class="form-group">
    {!!Form::label('name','GSTIN',['class'=>'col-sm-4 control-label'])!!}
    <div class="col-sm-8">
        {!!Form::text('gstin',null,['class'=>'form-control'])!!}
    </div>
</div>
<div class="form-group">
    {!!Form::label('address','Address',['class'=>'col-sm-4 control-label'])!!}
    <div class="col-sm-8">
        {!!Form::textarea('address',null,['class'=>'form-control','rows'=>3])!!}
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