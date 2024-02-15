<h4 class="header large lighter blue"><i class="fa fa-plus" ></i>
    Add Room
</h4>

<div class="container-fluid">
         <div class="row">
            <div class="col-md-12">
               <div class="row">
                   <div class="col-md-12">
                        <h4 class="page-header lighter blue" style="text-align: center;">
                          <b>{{$data['hostel']['name']}}</b>
                        </h4>
                   </div>
               </div>
                <div class="form-group">
                  <input type="text" name="hostelId" value="{{$data['hostel']['id']}}" hidden="hidden">
                    <label class="col-sm-4 control-label">Select Block</label>
                     <div class="col-sm-8">
                        {!!Form::select('block',$data['block'],null,["class"=>"form-control",'required'=>'required',"onchange"=>"loadFloor(this)",'id'=>'block'])!!}
                    </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-4 control-label">Select Floor</label>
                     <div class="col-sm-8">
                        {!!Form::select('floor',[""=>"Select"],null,["class"=>"form-control",'required'=>'required','id'=>'floor'])!!}
                    </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-4 control-label">Room Type</label>
                    <div class="col-sm-8">
                     {!!Form::select('room_type',$data['room_type'],null,["class"=>"form-control",'required'=>'required'])!!}  
                    </div>
                </div>
                <div class="form-group">
                  
                    <label class="col-sm-4 control-label">Start Room No</label>
                    <div class="col-sm-8">
                      {!!Form::text('start',null,["class"=>"form-control"])!!}  
                    </div>
                    
                </div>
                <div class="form-group">
                  <label class="col-sm-4 control-label">End Room No</label>
                    <div class="col-sm-8">
                      {!!Form::text('end',null,["class"=>"form-control"])!!}  
                    </div>
                </div>
             
                <div class="clearfix form-actions">
                  <div class="align-right">            &nbsp; &nbsp; &nbsp;
                      <button class="btn btn-info" type="submit" id="filter-btn">
                          <i class="fa fa-plus bigger-110"></i>
                          Add Room
                      </button>
                  </div>
                </div>
              
            </div>
           
         </div>
 </div>
