    <h4 class="header large lighter blue"><i class="fa fa-search" aria-hidden="true"></i>&nbsp;Search Student</h4>
    <div class="clearfix"><form> 
        <div class="form-group">
            <label class="col-sm-2 control-label">{{ env('course_label') }}</label>
            <div class="col-sm-3">
                @php $fac = (isset($data['faculties'])) ? $data['faculties']: ""; @endphp 
                {!! Form::select('faculty', $fac, null, ['class' => 'form-control', 'onChange' => 'loadSemesters(this);']) !!}

            </div>
            {!! Form::label('reg_date', 'Enquiry Date', ['class' => 'col-sm-2 control-label']) !!}
            <div class=" col-sm-5">
                <div class="input-group ">
                    {!! Form::date('enq_start_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
                    <span class="input-group-addon">
                        <i class="fa fa-exchange"></i>
                    </span>
                    {!! Form::date('enq_end_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
                    @include('includes.form_fields_validation_message', ['name' => 'reg_start_date'])
                    @include('includes.form_fields_validation_message', ['name' => 'reg_end_date'])
                </div>
            </div>
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
        </div>

        <label class="col-sm-2 control-label">Name</label>
        <div class="col-sm-2"><input type="text" name="name" class="form-control" value="{{ $data['filter_query']['name'] or ''}}" placeholder="Enter Student Name" /></div>

        <label class="col-sm-1 control-label">Mobile</label>
        <div class="col-sm-2">
            <input type="text" name="mobile" class="form-control" placeholder="Enter Mobile No." value="{{ $data['filter_query']['mobile'] or ''}}" /></div>
    </div>
</div>

<div class="clearfix">
    <div class="form-group">
        <label class="col-sm-2 control-label">Enquiry By</label>
         <div class="col-sm-3">
            {!!Form::select('enq_by',$data['user'],null,['class'=>'form-control'])!!}
            <!-- <input type="text" name="enq_by" class="form-control" placeholder="Enter Student Reg. No." value="{{ $data['filter_query']['reg_no'] or ''}}" /> -->
        </div>
        <label class="col-sm-2 control-label">Status</label>
         <div class="col-sm-2">
            {!!Form::select('status',$data['status'],null,['class'=>'form-control'])!!}
            <!-- <input type="text" name="enq_by" class="form-control" placeholder="Enter Student Reg. No." value="{{ $data['filter_query']['reg_no'] or ''}}" /> -->
        </div>
         {!! Form::label('reg_date', 'Follow Up Date', ['class' => 'col-sm-1 control-label']) !!}
         <div class=" col-sm-2">
                <div class="input-group ">
                    {!! Form::date('next_followup_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
                    
                </div>
        </div>
        
    </div>
</div>
 <div class="col-md-12 align-right">        &nbsp; &nbsp; &nbsp;

        <button class="btn btn-info" type="submit" id="filter-btn">
            <i class="fa fa-filter bigger-110"></i>
            Search
        </button>
    </div>
<!-- <div class="clearfix form-actions">
    -->

</form>

