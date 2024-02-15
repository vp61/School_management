
 <h4 class="header large lighter blue"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;Add Block</h4>
 <div class="container-fluid">
		 <div class="row">
		 	<div class="col-md-12">
		 		<div class="header lighter blue" style="">
		 			<h5><b>{{$data['hostels']->name}}</b></h5>
		 		</div>
		 		
				<div class="form-group">
				 	<label class="col-sm-4 control-label">Block Name</label>
					 <div class="col-sm-8">
					 	<input type="text" name="hostel" hidden="" value="{{$id}}">
					 	{!!Form::text('name',null,["class"=>"form-control",'required'=>'required'])!!}
				 	</div>
				</div>
				 <div class="clearfix form-actions">
                                <div class="col-md-12 align-right">
                                   
                                    <button class="btn btn-info" type="submit" id="assignment-btn">
                                        <i class="icon-ok bigger-110"></i>
                                       Add Block
                                    </button>
                                </div>
                            </div>

                            
                           
		 	</div>
		 </div>
 </div>