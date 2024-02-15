<h4 class="header large lighter blue"><i class="ace-icon glyphicon glyphicon-plus"></i>>Upload Profile Pictures From Camera:</h4>
 

<div class="row">
    <div class="col-md-6">
        <div id="my_camera"></div>
        <br/>
        <input type=button value="Take Snapshot" onClick="take_snapshot()">
        <input type="hidden" name="student_profile_image" class="image-tag">
    </div>
    <div class="col-md-6">
        <div id="results">Your captured image will appear here...</div>
    </div> 
</div>

<hr/><br/>

<h4 class="header large lighter blue"><i class="ace-icon glyphicon glyphicon-plus"></i>Upload Profile Pictures From Directory</h4>
<div class="form-group">
    {!! Form::label('student_main_image', 'Student Profile Picture', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-6">
        {!! Form::file('student_main_image', ["class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'student_main_image'])
    </div>

    @if (isset($data['row']))
        @if ($data['row']->student_image)
            <img id="avatar"  src="{{ asset('images'.DIRECTORY_SEPARATOR.'studentProfile'.DIRECTORY_SEPARATOR.$data['row']->student_image) }}" class="img-responsive" width="100px">
        @endif
    @else
        <img id="" class="img-responsive" alt="Avatar" src="{{ asset('assets/images/avatars/profile-pic.jpg') }}" width="100px">
    @endif
</div>

<div class="form-group">
    {!! Form::label('father_main_image', 'Father Picture', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-6">
        {!! Form::file('father_main_image', ["class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'father_main_image'])
    </div>
    @if (isset($data['row']))
        @if ($data['row']->father_image)
            <img id="avatar"  src="{{ asset('images'.DIRECTORY_SEPARATOR.'parents'.DIRECTORY_SEPARATOR.$data['row']->father_image) }}" class="img-responsive" width="100px">
        @endif
    @else
        <img id="" class="img-responsive" alt="Avatar" src="{{ asset('assets/images/avatars/profile-pic.jpg') }}" width="100px">
    @endif
</div>

<div class="form-group">
    {!! Form::label('mother_main_image', 'Mother Picture', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-6">
        {!! Form::file('mother_main_image', ["class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'mother_main_image'])
    </div>
    @if (isset($data['row']))
        @if ($data['row']->mother_image)
            <img id="avatar"  src="{{ asset('images'.DIRECTORY_SEPARATOR.'parents'.DIRECTORY_SEPARATOR.$data['row']->mother_image) }}" class="img-responsive" width="100px">
        @endif
    @else
        <img id="" class="img-responsive" alt="Avatar" src="{{ asset('assets/images/avatars/profile-pic.jpg') }}" width="100px">
    @endif
</div>

<div class="form-group">
    {!! Form::label('guardian_main_image', 'Guardian Picture', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-6">
        {!! Form::file('guardian_main_image', ["class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'guardian_main_image'])
    </div>
    @if (isset($data['row']))
        @if ($data['row']->guardian_image)
            <img id="avatar"  src="{{ asset('images'.DIRECTORY_SEPARATOR.'parents'.DIRECTORY_SEPARATOR.$data['row']->guardian_image) }}" class="img-responsive" width="100px">
        @endif
    @else
        <img id="" class="img-responsive" alt="Avatar" src="{{ asset('assets/images/avatars/profile-pic.jpg') }}" width="100px">
    @endif
</div>