<div class="form-group">
	{!!Form::label('name','Followup Date',['class'=>'col-sm-4 control-label'])!!}
	<div class="col-sm-8">
		 {!! Form::date('followup_date',null, ["class" => "form-control border-form upper" ,"required"]) !!}
        <input type="hidden" name="org_id" value="{{Auth::user()->org_id}}">
        <input type="hidden" name="career_id" value="{{$id}}">
	</div>
</div>
<div class="form-group">
    {!!Form::label('name',' Next Followup Date',['class'=>'col-sm-4 control-label'])!!}
    <div class="col-sm-8">
         {!! Form::date('next_followup_date',null, ["class" => "form-control border-form upper" ]) !!}
       
    </div>
</div>
<div class="form-group">
    {!!Form::label('response','Reason/Response',['class'=>'col-sm-4 control-label'])!!}
    <div class="col-sm-8">
         {!! Form::text('response',null, ["class" => "form-control border-form upper" ,"required"]) !!}
        
    </div>
</div>
<div class="form-group">
    {!!Form::label('st','Status',['class'=>'col-sm-4 control-label'])!!}
    <div class="col-sm-8">
         {!! Form::select('career_status',$data['career_status'],null, ["class" => "form-control border-form upper" ,"required"]) !!}
       
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