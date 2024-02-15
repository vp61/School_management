

<div class="form-group">
    {!! Form::label('mode', 'Exam Mode', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::select('mode_id',$dropdowns['mode'],null, ["class" => "form-control border-form upper","required"]) !!}
    </div>
    {!! Form::label('mode', 'Grading Type', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::select('grading_type',$dropdowns['grading_type'],null, ["class" => "form-control border-form upper","required"]) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('term', 'Term', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::select('term_id',$dropdowns['term'],null, ["class" => "form-control border-form upper","required",'id'=>'term']) !!}
    </div>
    {!! Form::label('type', 'Exam Type', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::select('type_id',$dropdowns['type'],null, ["class" => "form-control border-form upper","required",'id'=>'exam_type']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('paper_type', 'Paper Type', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::select('paper_type',$dropdowns['paper-type'],null, [ "class" => "form-control border-form upper","required"]) !!}
    </div>
    {!! Form::label('faculty', env('course_label'), ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::select('faculty_id',$dropdowns['faculty'],null, [ "class" => "form-control border-form upper","required",'onChange'=>'loadSection(this)','id'=>'faculty']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('section', 'Section', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        <!-- exam change -->
        @if(isset($data['row']))
         {!! Form::select('section_id',$dropdowns['section'],null, [ "class" => "form-control border-form upper","required",'id'=>'section']) !!}
        @else
        {!! Form::select('section_id[]',$dropdowns['section'],null, [ "class" => "form-control selectpicker border-form upper","required",'id'=>'section','multiple'=>'multiple']) !!}
        @endif
        <!-- exam change -->
    </div>
    {!! Form::label('subject', 'Subject', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::select('subject_id',$dropdowns['subject'],null, [ "class" => "form-control border-form upper","required",'id'=>'subject']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('max_mark', 'Maximum Mark', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::number('max_mark',null, [ "class" => "form-control border-form upper","required"]) !!}
    </div>
     {!! Form::label('pass_mark', 'Passing Mark', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::number('pass_mark',null, [ "class" => "form-control border-form upper","required"]) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('title', 'Title', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('title',null, [ "class" => "form-control border-form upper","required"]) !!}
    </div>
     {!! Form::label('description', 'Description', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::textarea('description', null, ["placeholder" => "Enter Description", "class" => "form-control border-form upper",'rows'=>4]) !!}
    </div>
</div>
<div class="form-group">
   
</div>
<div style="border: 1px solid #ddd9d9;padding: 20px 20px;background: #f5f9ff;box-shadow: -3px 0px 9px 0px;margin: 10px 0px 10px 0px;">
    <h4 class="header large lighter blue"> Schedule Exam</h4>
    <div class="form-group">
        {!! Form::label('date','Exam Date', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::date('date',null, [ "class" => "form-control border-form date-picker"]) !!}
        </div>
         {!! Form::label('start_time', 'Start Time', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::time('start_time',null, [ "class" => "form-control border-form"]) !!}
        </div>
    </div>
    <div class="form-group">
       {!! Form::label('end_time', 'End Time', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::time('end_time',null, [ "class" => "form-control border-form"]) !!}
        </div>
        {!! Form::label('room_no', 'Hall / Room No.', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::text('room_no',null, [ "class" => "form-control border-form"]) !!}
        </div>
    </div>
    <div class="form-group">
       {!! Form::label('publish_status', 'Publish Status', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::select('publish_status',['0'=>'Pending','1'=>'Published'],null, [ "class" => "form-control border-form"]) !!}
        </div>
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