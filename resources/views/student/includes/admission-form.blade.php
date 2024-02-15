    <h4 class="header large lighter blue"><i class="fa fa-search" aria-hidden="true"></i>&nbsp;Search Student</h4>
    <div class="clearfix"><form>
        <!--div class="form-group">
            <!--{!! Form::label('reg_no', 'REG. NO.', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-3">
                {!! Form::text('reg_no', null, ["placeholder" => "", "class" => "form-control border-form input-mask-registration", "autofocus"]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'reg_no'])
            </div>

            {!! Form::label('reg_date', 'Reg. Date', ['class' => 'col-sm-2 control-label']) !!}
            <div class=" col-sm-5">
                <div class="input-group ">
                    {!! Form::text('reg_start_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
                    <span class="input-group-addon">
                        <i class="fa fa-exchange"></i>
                    </span>
                    {!! Form::text('reg_end_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
                    @include('includes.form_fields_validation_message', ['name' => 'reg_start_date'])
                    @include('includes.form_fields_validation_message', ['name' => 'reg_end_date'])
                </div>
            </div>
        </div-->

        <div class="form-group">
            <label class="col-sm-2 control-label">{{ env('course_label') }}</label>
            <div class="col-sm-3">
                @php $fac = (isset($data['faculties'])) ? $data['faculties']: ""; @endphp 
                {!! Form::select('faculty', $fac, null, ['class' => 'form-control', 'onChange' => 'loadSemesters(this);']) !!}

            </div>
            {!! Form::label('reg_date', 'Reg. Date', ['class' => 'col-sm-2 control-label']) !!}
            <div class=" col-sm-5">
                <div class="input-group ">
                    {!! Form::text('reg_start_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
                    <span class="input-group-addon">
                        <i class="fa fa-exchange"></i>
                    </span>
                    {!! Form::text('reg_end_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
                    @include('includes.form_fields_validation_message', ['name' => 'reg_start_date'])
                    @include('includes.form_fields_validation_message', ['name' => 'reg_end_date'])
                </div>
            </div>
        <!--label class="col-sm-2 control-label">Semester</label>
        <div class="col-sm-2">
            <select name="semester_select[]" class="form-control semester_select" >
                <option value=""> Select Semester </option>
            </select>
        </div-->

        <!--label class="col-sm-1 control-label">Status</label>
        <div class="col-sm-2">
            <select class="form-control border-form" name="status" id="cat_id">
                <option value="all"> Select Status </option>
                <option value="active" >Active</option>
                <option value="in-active" >In-Active</option>
            </select>
        </div-->
    </div>
</div>


<div class="clearfix">
    <div class="form-group">
        <label class="col-sm-2 control-label">Category</label>
        <div class="col-sm-3">
            <select class="form-control border-form" name="category" id="categ_id">
                    <option value=""> Select Category </option>
                    @foreach($data['category_name'] as $cat)
                    @php $selected=(isset($data['filter_query']['category']) && $cat['id']==$data['filter_query']['category']) ? "selected":""; @endphp
                    <option value="{{$cat['id']}}" {{$selected}}>{{$cat['category_name']}}</option>
                    @endforeach
                </select>
            <!--its not workinG {{ Form::select('category', $data['category_name'], null, ['class' => 'form-control']) }}-->

        </div>

        <label class="col-sm-2 control-label">Name</label>
        <div class="col-sm-2"><input type="text" name="name" class="form-control" value="{{ $data['filter_query']['name'] or ''}}"  placeholder="Enter name."/></div>

        <label class="col-sm-1 control-label">Mobile</label>
        <div class="col-sm-2"><input type="text" name="mobile" class="form-control" value="{{ $data['filter_query']['mobile'] or ''}}"  placeholder="Enter mobile no."/></div>
    </div>
</div>

<div class="clearfix">
    <div class="form-group">
        <label class="col-sm-2 control-label">Status/Fee Head</label>
         <div class="col-sm-3">
            {{ Form::select('head', $academic_status_option, null, ['class' => 'form-control']) }}
        </div>
        <label class="col-sm-2 control-label">Reference No.</label>
         <div class="col-sm-3">
            <input type="text" name="ref_no" class="form-control" value="{{ $data['filter_query']['ref_no'] or ''}}" placeholder="Enter reference no." />
        </div>

         <div class="col-md-2 align-right">        &nbsp; &nbsp; &nbsp;

        <button class="btn btn-info" type="submit" id="filter-btn">
            <i class="fa fa-filter bigger-110"></i>
            Search
        </button>
    </div>
    </div>
</div>

<!-- <div class="clearfix form-actions">
    -->

</form>

