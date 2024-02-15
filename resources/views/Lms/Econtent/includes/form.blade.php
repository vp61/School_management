<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            {!! Form::label('publish_date', 'Start Date', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-4">
                {!! Form::date('publish_date', null, ["placeholder" => "", "class" => "form-control border-form input-mask-date date-picker","data-date-format" => "yyyy-mm-dd"]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'publish_date'])
            </div>


            {!! Form::label('end_date', 'End Date', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-4">
                {!! Form::date('end_date', null, ["placeholder" => "", "class" => "form-control border-form input-mask-date date-picker","data-date-format" => "yyyy-mm-dd"]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'end_date'])
            </div>
        </div>
        <div class="form-group">
             
            <label class="col-sm-2 control-label">{{ env('course_label') }}</label>
            <div class="col-sm-4">
                {!! Form::select('faculty', $data['faculties'], null, ['class' => 'form-control getSubject', 'onChange' => 'loadSemesters(this);','id'=>'course','required']) !!}
                @include('includes.form_fields_validation_message', ['name' => 'faculty'])
            </div>

            <label class="col-sm-2 control-label">Section</label>
            <div class="col-sm-4">
                <!-- subject list -->
                {!! Form::select('semesters_id', $data['semester'], null, ['class' => 'form-control semesters_id getSubject','id'=>'sem','onChange' => 'loadSubject(this);','required']) !!}
                @include('includes.form_fields_validation_message', ['name' => 'semesters_id'])

            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">Subject</label>
            <div class="col-sm-4">
                <!-- subject list -->
                {!! Form::select('subjects_id', $data['subjects'], null, ['class' => 'form-control semester_subject','id'=>'subject','onChange' => 'loadChapter(this);','required']) !!}
                @include('includes.form_fields_validation_message', ['name' => 'subjects_id'])
            </div>
             <label class="col-sm-2 control-label">Chapter number</label>
            <div class="col-sm-4">
                {!! Form::select('chapter_no_id', $data['chapter'], null, ['class' => 'form-control Lession','id'=>'chapter','required']) !!}
                @include('includes.form_fields_validation_message', ['name' => 'chapter_no_id'])
            </div>
        </div>
    </div>

    <div class="col-md-12 email">
        <div class="form-group">
            <label class="col-sm-2 control-label">worksheet</label>
            <div class="col-sm-4">
            {!! Form::select('assin_book_type_id',$data['worksheet'],null, ["class" => "form-control border-form",'required']) !!}
                @include('includes.form_fields_validation_message', ['name' => 'assin_book_type_id'])

            </div>
            <label class="col-sm-2 control-label">Attachment File</label>
            <div class="col-sm-4">
                {!! Form::file('attach_file', null, ["placeholder" => "", "class" => "form-control border-form",'required']) !!}
            </div>
        </div>
    </div>
    <div class="col-md-12 email">
        <div class="form-group">
            <label class="col-sm-2 control-label">Detail</label>
            <div class="col-sm-4">
            {!! Form::text('detail', null, ["class" => "form-control border-form",'required']) !!}
                @include('includes.form_fields_validation_message', ['name' => 'detail'])

            </div>
        </div>
    </div>
    
    @if (isset($data['row']))

        <div class="space-4"></div>

        <div class="form-group">
            {!! Form::label('old_file', 'Old File', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-8 ace-file-input">
                @if ($data['row']->file)
                    <a href="{{ asset('Econtent'.DIRECTORY_SEPARATOR.$data['row']->file) }}" target="_blank">
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