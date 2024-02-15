@extends('layouts.master')

@section('content')
<div class="main-content">
	<div class="main-content-inner">
		<div class="page-content">
			<div class="page-header">
				<h1>
					Postal Recieve
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
						 	Edit Postal Recieve
						 @else
						 <i class="fa fa-plus"></i> 
						 	Add Postal Recieve
						 @endif
					</h4>
					 @if(isset($data['row']))
						{!!Form::model($data['row'],['route'=>[$base_route.'.edit',$data['row']->id],'method'=>'POST','class'=>'form-horizontal','id' => 'validation-form',"enctype"=>"miltipart/form-data"])!!}
					 @else
						{!!Form::open(['route'=>$base_route.'.receive','method'=>'POST','class'=>'form-horizontal','id' => 'validation-form',"enctype"=>"miltipart/form-data"])!!}
					@endif	
                        @php($date=null)
					@include($view_path.'.receive.includes.form')
					<div class="form-group">
                            <div class="clearfix form-actions">
                                <div class="align-right">            &nbsp; &nbsp; &nbsp;
                                        <button class="btn btn-info" type="submit" id="filter-btn">
                                               Add
                                        </button>
                                </div>
                            </div> 
                        </div>   
				</div>
			</div>
		</div>
	</div>
</div>
@endsection