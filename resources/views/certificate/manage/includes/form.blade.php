<div class="row">
	<div class="col-md-12 col-xs-12">
		{!!Form::open(['route'=>$base_route.'.store','method'=>'POST','class'=>'form-horizontal','id' => 'validation-form', "enctype" => "multipart/form-data"])!!}
			<div class="form-group">
				{!!Form::label('title','Title',['class'=>'col-sm-2 control-label'])!!}
				<div class="col-sm-10">
					{!!Form::text('title',null,['class'=>'form-control','required'=>'required'])!!}
				</div>
			</div>
			<div class="form-group">
				{!!Form::label('body','Body',['class'=>'col-sm-2 control-label'])!!}
				<div class="col-sm-10">
					{!!Form::textarea('body',null,['class'=>'form-control','rowspan'=>'4','required'=>'required'])!!}
				</div>
			</div>
			
	        <div class="align-right">
	            <button type="submit" class="btn btn-sm btn-primary" >
	                <i class="fa fa-plus bigger-120"></i> Add Certificate
	            </button>
	        </div>
		{!!Form::close() !!}
	</div>
</div>