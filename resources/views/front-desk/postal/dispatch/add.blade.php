@extends('layouts.master')

@section('content')
<div class="main-content">
	<div class="main-content-inner">
		<div class="page-content">
			<div class="page-header">
				<h1>
					Postal Dispatch
					<small>
						 <i class="ace-icon fa fa-angle-double-right"></i>
						 @if(isset($data['row']))
						 	Edit
						 @else
						 	Add
						 @endif
						
					</small>
				</h1>
			</div>
			@include('includes.flash_messages')
			<div class="row">
				<div class="col-sm-12">
					<h4 class="header large lighter blue">
						
						 @if(isset($data['row']))
						 <i class="fa fa-pencil"></i> 
						 	Edit Postal Dispatch
						 @else
						 <i class="fa fa-plus"></i> 
						 	Add Postal Dispatch
						 @endif

					</h4>
					 @if(isset($data['row']))
					{!!Form::model($data['row'],['route'=>[$base_route.'.edit',$data['row']->id],'method'=>'POST','class'=>'form-horizontal','id' => 'validation-form',"enctype"=>"miltipart/form-data"])!!}
					 @else
						 {!!Form::open(['route'=>$base_route.'.dispatch','method'=>'POST','class'=>'form-horizontal','id' => 'validation-form',"enctype"=>"miltipart/form-data"])!!}
					@endif
					
                        @php($date=null)
					@include($view_path.'.dispatch.includes.form')
					 @if(isset($data['row']))
						 <div class="form-group">
                            <div class="clearfix form-actions">
                                <div class="align-right">            &nbsp; &nbsp; &nbsp;
                                        <button class="btn btn-info" type="submit" id="filter-btn">
                                               Update
                                        </button>
                                </div>
                            </div> 
                    </div>   
						 @else
						<div class="form-group">
                            <div class="clearfix form-actions">
                                <div class="align-right">            &nbsp; &nbsp; &nbsp;
                                        <button class="btn btn-info" type="submit" id="filter-btn">
                                               Add
                                        </button>
                                </div>
                            </div> 
                    </div>   
						 @endif
					
				</div>
			</div>
		</div>
	</div>
</div>
@endsection