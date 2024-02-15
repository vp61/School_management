@extends('layouts.master')

@section('css')
@endsection

@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                @include('layouts.includes.template_setting')
                <div class="page-header">
                    <h1>
                       {{$panel}}
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Edit
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    <div class="col-xs-12 ">
                    
                    <!-- PAGE CONTENT BEGINS -->
                    @include($view_path.'.includes.buttons')
                        @include('includes.flash_messages')
                        <div class="row">
                            <div class="col-xs-4">  
                                <h4 class="header large lighter blue">   <i class="fa fa-pencil bigger-110"></i> Edit</h4> 
                                 {!!Form::open(['route'=>[$base_route.'.update',$id],'method'=>'POST', 'class' => 'form-horizontal',
                                        'id' => 'validation-form', "enctype" => "multipart/form-data"])!!} 
                                         <div class="form-group">
                                    {!!Form::label('name','Name',['class'=>'col-sm-4 control-label'])!!}
                                    <div class="col-sm-8">
                                        {!!Form::text('name',$data['edit']->name,['class'=>'form-control','required'=>'required'])!!}
                                    </div>
                                </div>
                                <div class="clearfix form-actions">
                                                <div class="align-right">            &nbsp; &nbsp; &nbsp;
                                                        <button class="btn btn-info" type="submit" id="filter-btn">
                                                                
                                                                Update 
                                                        </button>
                                                </div>
                                        </div>
                                  {!!Form::close()!!}
                            </div>
                            <div class="col-xs-8">
                                @include($view_path.'.includes.table')
                            </div>
                        </div>
                        <div class="hr hr-18 dotted hr-double"></div>
                       
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
@endsection


@section('js')
 @include('includes.scripts.dataTable_scripts')


@endsection