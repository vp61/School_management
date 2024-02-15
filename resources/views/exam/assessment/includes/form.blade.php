<h4 class="header large lighter blue">     
  <i class="fa fa-search bigger-110"></i> Assessment
</h4> 
 <form action="{{route($base_route.'.add')}}" method="GET" class="form-horizontal" id="form">
        <div class="form-group">
          {!! Form::label('faculty', env('course_label'), ['class' => 'col-sm-1 control-label']) !!}
          <div class="col-sm-3">
              {!! Form::select('faculty',$dropdowns['faculty'],null, ["class" => "form-control border-form upper","required",'id'=>'faculty']) !!}
          </div>
          {!! Form::label('section', 'Section', ['class' => 'col-sm-1 control-label']) !!}
          <div class="col-sm-3">
              {!! Form::select('section',$dropdowns['section'],null, ["class" => "form-control border-form upper","required",'id'=>'section']) !!}
          </div>
         
        </div>
        <div class="form-group">  
           {!! Form::label('mode','Term', ['class' => 'col-sm-1 control-label']) !!}
          <div class="col-sm-3">
              {!! Form::select('term',$dropdowns['term'],null, ["class" => "form-control border-form upper","required",'id'=>'term']) !!}
          </div>
          {!! Form::label('mode','Mode', ['class' => 'col-sm-1 control-label']) !!}
          <div class="col-sm-3">
              {!! Form::select('exam_mode',[''=>'--Select Exam Mode--','1'=>'Online','2'=>'Offline'],null, ["class" => "form-control border-form upper","required",'id'=>'mode']) !!}
          </div>
            {!! Form::label('mode','Exams', ['class' => 'col-sm-1 control-label']) !!}
          <div class="col-sm-3">
              {!! Form::select('exam',[''=>'Select'],null, ["class" => "form-control selectpicker","required",'id'=>'exam','data-live-search'=>'true']) !!}
          </div> 
        </div>
<div class="clearfix form-actions">
  <div class="align-right">            &nbsp; &nbsp; &nbsp;
      <button class="btn btn-info" type="submit" id="filter-btn">
          <i class="fa fa-search bigger-110"></i>
             Search
      </button>
  </div>
</div>
  {!!Form::close()!!}