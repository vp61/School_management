<h4 class="header large lighter blue" id="filterBox"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;Add Bed</h4>
<div class="form-horizontal" id="filterDiv">
    <div class="row">
                   <div class="col-md-12">
                        <h4 class="page-header lighter blue" style="text-align: center;">
                          <b>{{$data['hostel']['name']}}</b>
                        </h4>
                   </div>
               </div>
    <form action="" method="">
        <div class="form-group">
            <input type="text" name="hostelId" value="{{$data['hostel']->id}}" hidden="hidden">
            {!! Form::label('block', 'Block', ['class' => 'col-sm-4 control-label']) !!}
            <div class="col-sm-8">
                {!! Form::select('block', $data['block'],null,['class' => 'form-control' , "onChange"=>"loadFloor(this)" ,'required'=>'required','id'=>'block']) !!}
            </div>
        </div>
        <div class="form-group">
             {!! Form::label('floor', 'Floor', ['class' => 'col-sm-4 control-label']) !!}
            <div class="col-sm-8">
                {!! Form::select('floor',[""=>"Select"] ,null, [ "class" => "form-control ","id"=>"floor",'onChange'=>'loadRoom(this)','required'=>'required']) !!}
            </div>
        </div>
        <div class="form-group">
             {!!Form::label('room','Room',['class'=>'col-sm-4 control-label'])!!}
            <div class="col-sm-8">
                {!!Form::select('room',[""=>"Select"],null,['class'=>'form-control','id'=>'room','required'=>'required'])!!}
            </div>
        </div>
        <div class="form-group">                 
            <label class="col-sm-4 control-label">Bed No</label>
            <div class="col-sm-8">
              {!!Form::text('start',$data['row']->bed_number,["class"=>"form-control",'required'=>'required'])!!}  
            </div>        
        </div> 
        <div class="form-group">
             <label class="col-sm-4 control-label">Rate</label>
            <div class="col-sm-8">
              {!!Form::text('rate',$data['row']->rate,["class"=>"form-control",'required'=>'required'])!!}  
            </div>
        </div>

        <div class="clearfix form-actions">
                <div class="align-right">            &nbsp; &nbsp; &nbsp;
                        <button class="btn btn-info" type="submit" id="filter-btn">
                             <i class="fa fa-plus bigger-110"></i>
                                Update Bed
                        </button>
                </div>
        </div>
     </form>   
</div>