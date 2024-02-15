<h4 class="header large lighter blue" id="filterBox"><i class="fa fa-search" aria-hidden="true"></i>&nbsp;Search Student/Staff</h4>
<div class="form-horizontal" id="filterDiv">
    <div class="form-group clearfix form-actions">
        
        {!! Form::label('user_type', ' User Type', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-2">
            {!! Form::select('member_type', [""=>"Select Type","1"=>"Student","2"=>"Staff"], null, ['class' => 'form-control','id'=>'type',"onChange"=>"CheckTransportMaster()"]) !!}
        </div>
         {!!Form::label('route','Routes',['class'=>'col-sm-1 control-label'])!!}
        <div class="col-sm-3">
             {!! Form::select('route', $data['routes'], null, ['class' => 'form-control']) !!}
            @include('includes.form_fields_validation_message', ['name' => 'route'])
        </div>
         {!! Form::label('reg_no', 'REG No.', ['class' => 'col-sm-1 control-label']) !!}
        <div class="col-sm-3">
            {!! Form::text('reg_no', null, ["placeholder" => "", "class" => "form-control border-form","autofocus"]) !!}
           
        </div>
        
 </div>   
 <div class="form-group">
      {!! Form::label('report_type', 'Report Type', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-2">
            {!! Form::select('report_type',['1'=>'Full Due Report','2'=>'Only Due'] ,null, [ "class" => "form-control border-form","autofocus"]) !!}
           
        </div>
    
    
        <div class="align-right col-sm-2">            &nbsp; &nbsp; &nbsp;
            <button class="btn btn-info" type="submit" id="filter-btn">
                <i class="fa fa-filter bigger-110"></i>
                Search
            </button>
        </div>
    </div> 
 </div>