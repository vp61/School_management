<div class="form-group">
    {!!Form::label('route','Resident',['class'=>'col-sm-2 control-label'])!!}
    <div class="col-sm-2">
        {!!Form::select('resident_id',$data['resident'],null,['class'=>'form-control','required'=>'required'])!!}
        <input type="hidden" name="org_id" value="{{Auth::user()->org_id}}">
    </div>
     {!!Form::label('leave_from','Leave From',['class'=>'col-sm-2 control-label'])!!}
    <div class="col-sm-2">
       {!! Form::text('leave_from', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd",'required' => 'required']) !!}
       
    </div>
     {!!Form::label('leave_to','Leave To',['class'=>'col-sm-2 control-label'])!!}
    <div class="col-sm-2">
       {!! Form::text('leave_to', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd",'required' => 'required']) !!}
       
    </div>
</div>

<div class="form-group">
   
     {!!Form::label('Leave Reason','Leave Reason',['class'=>'col-sm-2 control-label'])!!}
    <div class="col-sm-2">
        {!!Form::text('reason',null,['class'=>'form-control','required' => 'required'])!!}
      
    </div>
     {!!Form::label('Return Date','Return Date',['class'=>'col-sm-2 control-label'])!!}
    <div class="col-sm-2">
       {!! Form::text('return_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd",'required' => 'required']) !!}   
    </div>
     {!!Form::label('remark','Remark',['class'=>'col-sm-2 control-label'])!!}
    <div class="col-sm-2">
        {!!Form::text('remark',null,['class'=>'form-control'])!!}
       
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