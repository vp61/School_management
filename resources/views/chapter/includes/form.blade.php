<div class="form-group">
    <label class="col-sm-2 control-label">{{env('course_label')}}</label>
    <div class=" col-md-4">
            <!-- load section code -->
        {!! Form::select('faculty', $data['faculties'], null, ['class' => 'form-control getSubject', 'onChange' => 'loadSemesters(this);','required'=>'required','id'=>'course']) !!}
        <!-- load section code -->
    </div>
    <label class="col-sm-2 control-label">Section</label>
    <div class="col-md-4">
        <!-- assign subject to class/course -->
        {!! Form::select('semesters_id', $data['semester'], null, ['class' => 'form-control semesters_id getSubject semester_select','required'=>'required','id'=>'sem','onChange' => 'loadSubject(this);']) !!}
        <!-- assign subject to class/course -->
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">subject</label>
    <div class="col-sm-4">
        <!-- {!!Form::text('title',null,['class'=>'form-control upper','required'=>'required'])!!} -->
        {!! Form::select('timetable_subjects_id', $data['subjects'], null, ['class' => 'form-control semester_subject','id'=>'subject','required'=>'required']) !!}
        <input type="hidden" name="org_id" value="{{Auth::user()->org_id}}">
    </div>
    <label class="col-sm-2 control-label">Chapter Name</label>
    <div class="col-sm-4">
        {!!Form::text('title',null,['class'=>'form-control upper','required'=>'required'])!!}
        <input type="hidden" name="org_id" value="{{Auth::user()->org_id}}">
    </div>
</div>