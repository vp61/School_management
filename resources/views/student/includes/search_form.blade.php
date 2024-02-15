<h4 class="header large lighter blue" id="filterBox"><i class="fa fa-search" aria-hidden="true"></i>&nbsp;Search {{ $panel }}</h4>
<form method="get" action="">
<div class="form-horizontal" id="filterDiv">
    <div class="form-group">
        {!! Form::label('faculty', env('course_label'), ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-3">
            {!! Form::select('faculty', $data['faculty'], null, ['class' => 'form-control']) !!}
            @include('includes.form_fields_validation_message', ['name' => 'faculty'])
        </div>

       

        <label class="col-sm-2 control-label">Status</label>
        <div class="col-sm-3">
            <select class="form-control border-form" name="status" id="cat_id">
                <option value=""> Select Status </option>
                <option value="active" >Active</option>
                <option value="in-active" >In-Active</option>
            </select>
        </div>
    </div>

    <div class="clearfix form-actions">
        <div class="align-right">            &nbsp; &nbsp; &nbsp;
            <button class="btn btn-info" type="submit" id="filter-btn">
                <i class="fa fa-filter bigger-110"></i>
                Search
            </button>
        </div>
    </div>
</div>
</form>