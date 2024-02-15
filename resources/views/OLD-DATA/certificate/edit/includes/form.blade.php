<h4 class="header large lighter blue"><i class="fa fa-pencil"></i>&nbsp;Edit</h4>
<div class="form-group">
	{!!Form::label('name','Certificate Name',['class'=>'col-sm-2 control-label'])!!}
	<div class="col-sm-10">
		{!!Form::text('name',$data['value']->title,['class'=>' form-control','placeholder'=>'Enter Certificate Name','required'=>'required'])!!}		
	</div>
	
</div>
<div class="form-group">
	{!!Form::label('header_img','Header Image',['class'=>'col-sm-2 control-label'])!!}
	<div class="col-sm-2">
		{!!Form::file('header_img',null,['class'=>'form-control'])!!}
	</div>
</div>
<div class="form-group">
	{!!Form::label('header_dim','Header Image Dimensions',['class'=>'col-sm-2 control-label'])!!}
	<div class="col-sm-2" >
		{!!Form::number('header_img_height',$data['value']->header_img_height,['class'=>' form-control','placeholder'=>'Header Image Height'])!!}	
	</div>
	<div class="col-sm-2">
		{!!Form::number('header_padding_top',$data['value']->header_img_ptop,['class'=>' form-control','placeholder'=>'Padding Top'])!!}	
	</div>
	<div class="col-sm-2">
		{!!Form::number('header_padding_bottom',$data['value']->header_img_pbottom,['class'=>' form-control','placeholder'=>'Padding Bottom'])!!}	
	</div>
	<div class="col-sm-2">
		{!!Form::number('header_padding_left',$data['value']->header_img_pleft,['class'=>' form-control','placeholder'=>'Padding Left'])!!}	
	</div>
	<div class="col-sm-2">
		{!!Form::number('header_padding_right',$data['value']->header_img_pright,['class'=>' form-control','placeholder'=>'Padding Right'])!!}	
	</div>
	
</div>
<div class="form-group">
	{!!Form::label('left_header','Left Header Text',['class'=>'col-sm-2 control-label'])!!}
	<div class="col-sm-2">
		{!!Form::text('left_header',$data['value']->left_header,['class'=>'form-control','placeholder'=>'Enter Left Header'])!!}
	</div>
	{!!Form::label('left_header','Center Header Text',['class'=>'col-sm-2 control-label'])!!}
	<div class="col-sm-2">
		{!!Form::text('center_header',$data['value']->center_header,['class'=>'form-control','placeholder'=>'Enter Center Header'])!!}
	</div>
	{!!Form::label('right_header','Right Header Text',['class'=>'col-sm-2 control-label'])!!}
	<div class="col-sm-2">
		{!!Form::text('right_header',$data['value']->right_header,['class'=>'form-control','placeholder'=>'Enter Right Header'])!!}		
	</div>
</div>
<div class="form-group">
	{!!Form::label('body','Body',['class'=>'col-sm-2 control-label'])!!}
	<div class="col-sm-10">
		{!!Form::textarea('body',$data['value']->body,['class'=>' form-control','placeholder'=>'Enter Certificate Body Text','required'=>'required'])!!}		
	</div>
</div>
<div class="form-group">
	<div class="col-sm-10 pull-right">
		<b>Add </b><i class="blue">|name|,|dob|,|father_name|,|mother_name|,|address|,|mobile|,|email|,|reg_no|,|reg_date|,|class|,|section|,|gender|</i><b> in body to get corresponding data.</b>
	</div>
</div>
<div class="form-group">
	{!!Form::label('body_dim','Body Dimensions',['class'=>'col-sm-2 control-label'])!!}
	<div class="col-sm-2" >
		{!!Form::number('body_height',$data['value']->body_height,['class'=>' form-control','placeholder'=>'Enter Body Height'])!!}	
	</div>
	<div class="col-sm-2">
		{!!Form::number('body_padding_top',$data['value']->body_ptop,['class'=>' form-control','placeholder'=>'Padding Top'])!!}	
	</div>
	<div class="col-sm-2">
		{!!Form::number('body_padding_bottom',$data['value']->body_pbottom,['class'=>' form-control','placeholder'=>'Padding Bottom'])!!}	
	</div>
	<div class="col-sm-2">
		{!!Form::number('body_padding_left',$data['value']->body_pleft,['class'=>' form-control','placeholder'=>'Padding Left'])!!}	
	</div>
	<div class="col-sm-2">
		{!!Form::number('body_padding_right',$data['value']->body_pright,['class'=>' form-control','placeholder'=>'Padding Right'])!!}	
	</div>
	
</div>
<div class="form-group">
	{!!Form::label('std_img','Require Student Image',['class'=>'col-sm-2 control-label'])!!}
	<div class="col-sm-2">
			<input data-toggle="toggle" data-onstyle="success" data-offstyle="danger" data-on="Enabled" data-off="No" type="checkbox" class="form-control" name="req_img">
	</div>
</div>
<div class="form-group">
	{!!Form::label('bg_img','Background Image',['class'=>'col-sm-2 control-label'])!!}
	<div class="col-sm-2">
		{!!Form::file('bg_img',null,['class'=>'form-control'])!!}
	</div>
</div>
<div class="form-group">
	{!!Form::label('left_footer','Left Footer Text',['class'=>'col-sm-2 control-label'])!!}
	<div class="col-sm-2">
		{!!Form::text('left_footer',$data['value']->left_footer,['class'=>'form-control','placeholder'=>'Enter Left Footer'])!!}
	</div>
	{!!Form::label('left_footer','Center Footer Text',['class'=>'col-sm-2 control-label'])!!}
	<div class="col-sm-2">
		{!!Form::text('center_footer',$data['value']->center_footer,['class'=>'form-control','placeholder'=>'Enter Center Footer'])!!}
	</div>
	{!!Form::label('right_footer','Right Footer Text',['class'=>'col-sm-2 control-label'])!!}
	<div class="col-sm-2">
		{!!Form::text('right_footer',$data['value']->right_footer,['class'=>'form-control','placeholder'=>'Enter Right Footer'])!!}		
	</div>
</div>
<div class="clearfix form-actions">
                <div class="align-right">            &nbsp; &nbsp; &nbsp;
                        <button class="btn btn-info" type="submit" id="filter-btn">
                               Save
                        </button>
                </div>
        </div>
