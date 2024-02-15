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
                       @php($panel='list')
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Detail
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    <div class="col-xs-12 ">
                    
                    <!-- PAGE CONTENT BEGINS -->
                        <div class="form-horizontal">
                            <div class="row">
                                <div class="col-xs-12">
                                @include('includes.data_table_header')
                                <!-- div.table-responsive -->
                                    <div class="table-responsive">
                                        
                                        <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>S.N.</th>
                                                    <th>Name</th>
                                                   
                                                    <th>Email</th>
                                                    <th>Password</th>
                                                   
                                                    <th>Branch</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($data as $k=>$v)
                                                    @php($i=1)
                                                    <tr>
                                                        <td>{{$i}}</td>
                                                        <td>{{$v->name}}</td>
                                                       
                                                        <td>{{$v->email}}</td>
                                                        <td>{{\Hash::check('123456', $v->password)?'123456':'User has changed his password'}}</td>
                                                        <td>{{$v->branch}}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="hr hr-18 dotted hr-double"></div>
                        </div>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
@endsection


@section('js')
    @include('includes.scripts.delete_confirm')
    @include('includes.scripts.bulkaction_confirm')
    @include('includes.scripts.dataTable_scripts')
    <script>

    </script>
@endsection
