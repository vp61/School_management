@extends('layouts.master')

@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                @include('layouts.includes.template_setting')
                <div class="page-header">
                    <h1>
                        @include($view_path.'.includes.breadcrumb-primary')
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Floor
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                   
                    @include('includes.flash_messages')
                    <div class="col-xs-12 ">
                    @include($view_path.'.detail.includes.buttons')
                        
                        <!-- PAGE CONTENT BEGINS -->
                        <div class="form-horizontal">
                            <div class="row">
                                <div class="col-md-4">
                          @if(isset($data['row']))
                             {!! Form::open(['route' => [$base_route.'.floor.edit', $id,$floor_id], 'method' => 'POST', 'class' => 'form-horizontal',
                        'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}

                                @include($view_path.'.detail.addFloor.edit-form')

                                  {!! Form::close() !!}
                          
                          @else
                                {!! Form::open(['route' => [$base_route.'.floor', $id], 'method' => 'POST', 'class' => 'form-horizontal',
                        'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}

                                @include($view_path.'.detail.addFloor.includes.form')

                                  {!! Form::close() !!}
                          @endif          
                                      

                            
                            
                          
                            
                           
                                </div>
                                <div class="col-md-8">
                                    @include($view_path.'.detail.addFloor.includes.table')
                                </div>
                            </div>

                            
                        </div>
                    </div><!-- /.col -->
                </div><!-- /.row -->

            </div>
        </div><!-- /.page-content -->
     </div>
    <!-- /.main-content -->
@endsection

@section('js') 
    @include('includes.scripts.dataTable_scripts')
@endsection