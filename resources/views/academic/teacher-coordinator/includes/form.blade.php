<div class="form-group">
	{!!Form::label('name','Teacher',['class'=>'col-sm-4 control-label'])!!}
	<div class="col-sm-8">
		{!!Form::select('teacher_id',$data['teacher'],null,['class'=>'form-control selectpicker','required'=>'required','data-live-search'=>'true'])!!}
        <input type="hidden" name="org_id" value="{{Auth::user()->org_id}}">
	</div>
</div>
<div class="form-group">
    {!! Form::label('Faculty', 'select '.env('course_label'), ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
         {!!Form::select('faculty_id',$data['course'],null,['class'=>'form-control','required'=>'required','onchange'=>'loadSemesters(this)'])!!}
    </div>

</div>
<div class="form-group">
        {!!Form::label('section','Section',['class'=>'col-sm-4 control-label'])!!}
        <div class="col-md-8">
           
            {!!Form::select('section_id[]',$data['section'],null,['class'=>'form-control selectpicker semester_select','required'=>'required','data-live-search'=>'true','multiple'=>'multiple'])!!}
          
        </div>
    </div> 
<div class="form-group">
    {!! Form::label('Type', 'Type', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
         {!! Form::select('type',[''=>'--Select--','1'=>'Class Teacher','2'=>'Co-Ordinator'],null, ["class" => "form-control border-form upper",'required'=>'required']) !!}
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