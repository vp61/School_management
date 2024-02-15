
@extends('layouts.master')

@section('css')
    <!-- page specific plugin styles -->

@endsection

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
                           Import
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    <div class="col-xs-12 ">
                       @include($view_path.'.includes.buttons')
                        @include('includes.flash_messages')
                        @include('includes.validation_error_messages')
                        <!-- /.col -->
                </div><!-- /.row -->
                <div class="row">
                    <div class="col-xs-12">
                        <!-- div.table-responsive -->
                        @include('includes.data_table_header')
                            <!-- div.table-responsive -->
                        <div class="table-responsive">
                             @if(isset($status) && (count($status)>0))
                            <table id="dynamic-table" class="table table-striped table-bordered table-hover">

                               
                                <thead>
                                    <tr>
                                          @foreach($status[0] as $key =>$val)
                                                <th>{{$key}}</th>
                                        @endforeach
                                    </tr>
                                      
                                </thead>
                                
                                <tbody>
                                    @foreach($status as $key =>$val)
                                        <tr>
                                            @foreach($val as $k => $v)
                                                <td>{{$v}}</td>
                                            @endforeach
                                        </tr>
                                        
                                    @endforeach
                                </tbody>
                            </table>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
    @endsection


@section('js')
    @include('includes.scripts.jquery_validation_scripts')
    <!-- inline scripts related to this page -->
    @include('includes.scripts.dataTable_scripts')
    @include('includes.scripts.delete_confirm')
    @include('includes.scripts.bulkaction_confirm')
   
 {{--   @include('includes.scripts.datepicker_script')--}}

    @endsection