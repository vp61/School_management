<div class="form-group">
	{!!Form::label('name','Subject Name',['class'=>'col-sm-4 control-label'])!!}
	<div class="col-sm-8">
		{!!Form::text('title',null,['class'=>'form-control upper','required'=>'required'])!!}
        <input type="hidden" name="org_id" value="{{Auth::user()->org_id}}">
	</div>
</div>
<div class="form-group">
    {!! Form::label('Is Practical', 'Is Practical', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
         {!! Form::select('is_practical',[''=>'--Select Is Added Practical--','0'=>'No','1'=>'Yes'],null, ["class" => "form-control border-form upper"]) !!}
    </div>

</div>
<div class="form-group">
    {!! Form::label('Is Main Subject', 'Is Main Subject', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
         {!! Form::select('is_main_subject',[''=>'--Select--','0'=>'Optional','1'=>'Main subject'],null, ["class" => "form-control border-form upper",'required'=>'required']) !!}
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