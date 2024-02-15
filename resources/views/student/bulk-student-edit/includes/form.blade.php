<h4 class="header large lighter blue"><i class="fa fa-search" aria-hidden="true"></i>&nbsp;Search Student</h4>
{{@Form::open(['route'=>$base_route.'.bulk_edit_student','method'=>'post'])}}
<div class="clearfix">
    

    <div class="form-group">
        <label class="col-sm-1 control-label">{{ env('course_label') }}</label>
        <div class="col-sm-3">
            {!! Form::select('faculty', $data['faculties'], null, ['class' => 'form-control', 'onChange' => 'loadSemesters(this);']) !!}

        </div>

        <label class="col-sm-1 control-label">Section</label>
        <div class="col-sm-3">
            {!! Form::select('semester',$data['section'],null, ['class'=>'form-control','id'=>'semester_select']) !!}
        </div>
        {!! Form::label('mode','Edit', ['class' => 'col-sm-1 control-label']) !!}
          <div class="col-sm-3">
              {!! Form::select('edit',[''=>'select','1'=>'Edit RollNo','2'=>'Edit Section'],null, ["class" => "form-control border-form upper","required",'id'=>'term']) !!}
          </div>
       
    </div>
</div>

<div class="clearfix form-actions">
    <div class="col-md-12 align-right">        &nbsp; &nbsp; &nbsp;
        <button class="btn btn-info" type="submit" id="filter-btn">
            <i class="fa fa-filter bigger-110"></i>
            Search
        </button>
    </div>
</div>
{{ @Form::close() }}