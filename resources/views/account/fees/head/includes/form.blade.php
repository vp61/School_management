<div class="form-group">
    {!! Form::label('parent', 'Parent', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::select('parent_id',$data['parent'], null, ["class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'parent_id'])
    </div>
</div>
<div class="form-group">
    {!! Form::label('fee_head_title', 'Head', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('fee_head_title', null, ["placeholder" => "e.g. Monthly Fee", "class" => "form-control border-form upper","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'fee_head_title'])
    </div>
</div>
