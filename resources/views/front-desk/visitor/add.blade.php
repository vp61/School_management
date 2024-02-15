@extends('layouts.master')
@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap-clockpicker.min.css') }}" />
@endsection
@section('content')
	<div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                @include('layouts.includes.template_setting')

                <div class="page-header">
                    <h1>
                        Visitor
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
                <div class="row">
                	<div class="col-sm-12">
                    <h4 class="header large lighter blue">
                        @if(isset($data['row']))
                            <i class="fa fa-pencil"></i> Edit
                          @else 
                           <i class="fa fa-plus"></i> Add
                        @endif  
                      </h4> 
                        @if(isset($data['row']))   
                        {!!Form::model($data['row'],['route'=>[$base_route.'.edit',$id],'method'=>'POST','class'=>'form-horizontal','id' => 'validation-form',"enctype"=>"miltipart/form-data"])!!}
                             @include($view_path.'.includes.form')
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
                		{!!Form::open(['route'=>$base_route.'.store','method'=>'POST','class'=>'form-horizontal','id' => 'validation-form',"enctype"=>"miltipart/form-data"])!!}
                        @include($view_path.'.includes.form')
                        <div class="form-group">
                            <div class="clearfix form-actions">
                                <div class="align-right">            &nbsp; &nbsp; &nbsp;
                                        <button class="btn btn-info" type="submit" id="filter-btn">
                                             <i class="fa fa-plus bigger-110"></i>
                                                Add 
                                        </button>
                                </div>
                            </div> 
                        </div>
                        @endif
                            
                        {!!Form::close()!!}    
                	</div>
                </div>
            </div>
        </div>
    </div>            
@endsection
@section('js')

<script src="{{ asset('assets/js/bootstrap-clockpicker.min.js') }}"></script>
<script>
    $('.clockpicker').clockpicker();
</script>
@endsection