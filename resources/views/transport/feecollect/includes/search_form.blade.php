<h4 class="header large lighter blue" id="filterBox"><i class="fa fa-search" aria-hidden="true"></i>&nbsp;Search Student/Staff</h4>
<div class="form-horizontal" id="filterDiv">
    <div class="form-group">
        {!! Form::label('user_type', 'Type', ['class' => 'col-sm-1 control-label']) !!}
        <div class="col-sm-2">
            {!! Form::select('member_type', [" "=>"Select Type","1"=>"Student","2"=>"Staff"], null, ['class' => 'form-control','id'=>'type',"onChange"=>"CheckTransportMaster()" ]) !!}
        </div>

        {!!Form::label('route','Routes',['class'=>'col-sm-1 control-label'])!!}
        <div class="col-sm-2">
             {!! Form::select('route', $data['routes'], null, ['class' => 'form-control',"onChange"=>"loadTravellers()", "id"=>"route"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'route'])
        </div>
        {!!Form::label('travellers','Travellers',['class'=>'col-sm-1 control-label'])!!}
        <div class="col-sm-5">
            <select class="form-control" id="travel" onChange="abc()" name="travelId">
                <option value=" ">Select</option>
            </select>
            <!-- {!!form::select('traveller',[""=>'Select',"1"=>"Hii"],null,['class'=>'form-control',"id"=>"travel","onChange"=>"loadFeeData()"])!!} -->
            
        </div>
    </div>       
</div>