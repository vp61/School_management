
<h4 class="header large lighter blue" id="filterBox"><i class="fa fa-search" aria-hidden="true"></i>&nbsp;Search Books</h4>
<div class="form-horizontal" id="filterDiv">
   
    <div class="form-group">
         {!!Form::label('route','Resident',['class'=>'col-sm-2 control-label'])!!}
        <div class="col-sm-2">
            {!!Form::select('resident_id',$data['resident'],null,['class'=>'form-control'])!!}
        
        </div>
        {!! Form::label('From Date', ' Leave From', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-2">
            {!! Form::text('leave_from', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}  
        </div>

        {!! Form::label('code', ' Leave To', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-2">
            {!! Form::text('leave_to', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
        </div>

        
    </div>

    <div class="clearfix form-actions">
        <div class="col-md-12 align-right">
            <button class="btn btn-info" type="submit" id="filter-btn">
                <i class="fa fa-filter bigger-110"></i>
                Search
            </button>
        </div>
    </div>
</div>
