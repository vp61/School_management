<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            {!! Form::label('publish_date', 'Start Date', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-2">
                {!! Form::date('publish_date', null, ["placeholder" => "", "class" => "form-control border-form input-mask-date date-picker","data-date-format" => "yyyy-mm-dd",'required'=>'required']) !!}
                @include('includes.form_fields_validation_message', ['name' => 'publish_date'])
            </div>


            {!! Form::label('end_date', 'End Date', ['class' => 'col-sm-1 control-label']) !!}
            <div class="col-sm-2">
                {!! Form::date('end_date', null, ["placeholder" => "", "class" => "form-control border-form input-mask-date date-picker","data-date-format" => "yyyy-mm-dd",'required'=>'required']) !!}
                @include('includes.form_fields_validation_message', ['name' => 'end_date'])
            </div>
            <label class="col-sm-2 control-label">Month</label>
            <div class="col-sm-3">
               {!! Form::select('months_id', $data['month'], null, ['class' => 'form-control getSubject']) !!}
               @include('includes.form_fields_validation_message', ['name' => 'months_id'])
            </div>
        </div>
        <div class="form-group">
             
            <label class="col-sm-2 control-label">{{ env('course_label') }}</label>
            <div class="col-sm-5">
                {!! Form::select('faculty', $data['faculties'], null, ['class' => 'form-control getSubject', 'onChange' => 'loadSemesters(this);','id'=>'course','required'=>'required']) !!}
                @include('includes.form_fields_validation_message', ['name' => 'faculty'])
            </div>

            <label class="col-sm-2 control-label">Section</label>
            <div class="col-sm-3">
                <!-- subject list -->
                {!! Form::select('semesters_id', $data['semester'], null, ['class' => 'form-control semesters_id getSubject','id'=>'sem','onChange' => 'loadSubject(this);','required'=>'required']) !!}
                @include('includes.form_fields_validation_message', ['name' => 'semesters_id'])

            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">Subject</label>
            <div class="col-sm-5">
                <!-- subject list -->
                {!! Form::select('subjects_id', $data['subjects'], null, ['class' => 'form-control semester_subject','id'=>'subject','onChange' => 'loadChapter(this);','required'=>'required']) !!}
                @include('includes.form_fields_validation_message', ['name' => 'subjects_id'])
            </div>
            <label class="col-sm-2 control-label">Type</label>
            <div class="col-sm-3">
                {!! Form::text('type', null, ["class" => "form-control border-form",'required'=>'required']) !!}
                @include('includes.form_fields_validation_message', ['name' => 'type'])
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">Chapter number</label>
            <div class="col-sm-5">
                {!! Form::select('chapter_no_id', $data['chapter'], null, ['class' => 'form-control Lession','id'=>'chapter','required'=>'required']) !!}
                @include('includes.form_fields_validation_message', ['name' => 'chapter_no_id'])

            </div>

            <label class="col-sm-2 control-label">Topic</label>
            <div class="col-sm-3">
                <!-- subject list -->
                {!! Form::text('topic', null, ["class" => "form-control border-form",'required'=>'required']) !!}
                @include('includes.form_fields_validation_message', ['name' => 'topic'])

            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">Unit</label>
            <div class="col-sm-5">
                {!! Form::text('unit', null, ["class" => "form-control border-form"]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'unit'])


            </div>

            <label class="col-sm-2 control-label">Teaching Aids / HW</label>
            <div class="col-sm-3">
                <!-- subject list -->
                {!! Form::number('no_h/w', null, ["class" => "form-control border-form",'required'=>'required','min'=>0]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'no_h/w'])


            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">SEA</label>
            <div class="col-sm-5">
                {!! Form::text('sub_en_activity', null, ["class" => "form-control border-form"]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'sub_en_activity'])


            </div>

            <label class="col-sm-2 control-label">Serial No.</label>
            <div class="col-sm-3">
                <!-- subject list -->
                {!! Form::number('serial_no', null, ["class" => "form-control border-form",'min'=>0]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'serial_no'])


            </div>
        </div>
    </div>

    <div class="col-md-12 email">
        <div class="form-group">
            <label class="col-sm-2 control-label">Learning Outcome</label>
            <div class="col-sm-5">
            {!! Form::text('detail', null, ["class" => "form-control border-form"]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'detail'])

            </div>
            <label class="col-sm-2 control-label">Term</label>
            <div class="col-sm-3">
          <select  class="form-control" name="term">
              <option value="I">I</option>
              <option value="II">II</option>
          </select>

            
            
 
            </div>
        </div>
    </div>

     <div class="col-md-12 email">
        <div class="form-group">
            
            <label class="col-sm-2 control-label">Attachment File</label>
            <div class="col-sm-3">
            {!! Form::file('attach_file', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}

            
            
 
            </div>
        </div>
    </div>
    
    @if (isset($data['row']))

        <div class="space-4"></div>

        <div class="form-group">
            {!! Form::label('old_file', 'Old File', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-8 ace-file-input">
                @if ($data['row']->file)
                    <a href="{{ asset('Lesson_plans'.DIRECTORY_SEPARATOR.$data['row']->file) }}" target="_blank">
                        <i class="ace-icon fa fa-download bigger-120"></i> &nbsp;{{ $data['row']->file }}
                    </a>
                    
                @else
                    <p>No File.</p>
                @endif
            </div>
        </div>

    @endif

</div>
<div class="hr hr-24"></div>