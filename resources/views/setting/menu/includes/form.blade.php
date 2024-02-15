<div class="form-group">
	{!!Form::label('parent','Parent Group',['class'=>'col-sm-4 control-label'])!!}
	<div class="col-sm-8">
		{!!Form::select('parent_group',$data['parent'],null,['class'=>'form-control'])!!}
	</div>
</div>
<div class="form-group">
    {!!Form::label('group','Group',['class'=>'col-sm-4 control-label'])!!}
    <div class="col-sm-8">
        {!!Form::text('group',null,['class'=>'form-control','placeholder'=>'Enter Group'])!!}
    </div>
</div>
<div class="form-group">
    {!!Form::label('name','Name',['class'=>'col-sm-4 control-label'])!!}
    <div class="col-sm-8">
        {!!Form::text('name',null,['class'=>'form-control','placeholder'=>'Enter Name','required'])!!}
    </div>
</div>
<div class="form-group">
    {!!Form::label('disp_name','Display Name',['class'=>'col-sm-4 control-label'])!!}
    <div class="col-sm-8">
        {!!Form::text('display_name',null,['class'=>'form-control','placeholder'=>'Enter Display Name','required'])!!}
    </div>
</div>
<div class="form-group">
    {!!Form::label('route','Route',['class'=>'col-sm-4 control-label'])!!}
    <div class="col-sm-8">
        {!!Form::text('route',null,['class'=>'form-control','placeholder'=>'Enter Route'])!!}
    </div>
</div>
<div class="form-group">
    {!!Form::label('description','Description',['class'=>'col-sm-4 control-label'])!!}
    <div class="col-sm-8">
        {!!Form::textarea('description',null,['class'=>'form-control','rows'=>'3','placeholder'=>'Enter Description'])!!}
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