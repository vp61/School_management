<h4 class="header large lighter blue"><i class="fa fa-plus"></i> Add</h4>
{!!Form::open(['route'=>$base_route.'.store','method'=>'POST','class'=>'form-horizontal','id' => 'validation-form', "enctype" => "multipart/form-data"])!!}
	
	<div class="form-group">
		{!!Form::label('subject','Subject',['class'=>'col-sm-2 control-label'])!!}
		<div class="col-md-10">
			{!!Form::select('subject',$data['subject'],null,['class'=>'form-control','required'=>'required'])!!}
		</div>
	</div>
	<div class="form-group">
		{!!Form::label('teacher','Teacher',['class'=>'col-sm-2 control-label'])!!}
		<div class="col-md-10">
			{!!Form::select('teacher',$data['teacher'],null,['class'=>'form-control','required'=>'required'])!!}
		</div>
	</div>
	<div class="align-right">
	            <button type="submit" class="btn btn-sm " >
	                <i class="fa fa-plus bigger-120"></i> Assign
	            </button>
	        </div>

{!!Form::close()!!}