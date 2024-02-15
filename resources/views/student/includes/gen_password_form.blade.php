<h4 class="header large lighter blue"><i class="fa fa-search" aria-hidden="true"></i>&nbsp;{{ $panel }} Generate Password</h4>
<form method="post" action="">
    {{ csrf_field() }}
<div class="clearfix">
   

    {!! Form::label('faculty', env('course_label'), ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::select('faculty[]', $data['faculty'], '', ['class'=>'form-control selectpicker','data-live-search'=>'true','required','multiple','onChange' => 'loadSemesters(this)']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'faculty'])

    </div>
   
        <label class="col-sm-2 control-label">Section</label>
        <div class="col-sm-2">
            {!!Form::select('section',$data['semester'],null,['class'=>'form-control semester_select'])!!}
                       
        </div>
    
     {!! Form::label('password', 'Password', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        {!! Form::password('password', null, ["placeholder" => "", "class" => "form-control"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'password'])
    </div>
    
   
</div>
<div class="clearfix form-actions">
    <div class="align-right">        &nbsp; &nbsp; &nbsp;
        <button class="btn btn-info" type="submit" id="filter-btn">
            <i class="fa fa-filter bigger-110"></i>
            Generate
        </button>
    </div>
</div>
</form>