@extends('layouts.master')

@section('css')

    <link rel="stylesheet" href="{{ asset('admin-panel/assets/css/datepicker.css') }}" />

@endsection

@section('content')

    <div class="main-content">

        <div class="breadcrumbs" id="breadcrumbs">
            <script type="text/javascript">
                try{ace.settings.check('breadcrumbs' , 'fixed')}catch(e){}
            </script>

            <ul class="breadcrumb">
                @include($view_path.'.includes.breadcrumb-primary')
                <li class="active">List</li>
            </ul><!-- .breadcrumb -->


        </div>

        <div class="page-content">
            <div class="page-header">
                <h1>
                    {{ $panel }} Manager
                    <small>
                        <i class="icon-double-angle-right"></i>
                        Changed Passwords
                    </small>
                </h1>
            </div><!-- /.page-header -->

            <div class="row">
                <div class="col-xs-12">
                    <!-- PAGE CONTENT BEGINS -->
                    <div class="row">
                        <div class="col-xs-12">
                        @include('includes.data_table_header')
                        <!-- div.table-responsive -->
                            <div class="table-responsive">
                                {!! Form::open(['route' => $base_route.'.bulk-action', 'id' => 'bulk_action_form']) !!}
                                <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>S.N.</th>
                                            <th>Course</th>
                                            <th>Section</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Password</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                            @php($i=1)
                                            @foreach($data as $row)
                                                <tr>
                                                    <td>{{ $i }}</td>

                                                    <td>
                                                        {{$row['course']}}
                                                    </td>
                                                    <td>
                                                        {{$row['sec']}}
                                                    </td>
                                                    <td>
                                                        {{$row['name']}}
                                                    </td>
                                                    <td>
                                                        {{$row['reg_no']}}
                                                    </td>
                                                    <td>
                                                        {{$row['login_email']}}
                                                    </td>
                                                    <td>
                                                        {{$row['pass']}}
                                                    </td>
                                                    
                                                </tr>
                                                @php($i++)
                                            @endforeach
                                         

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="hr hr-18 dotted hr-double"></div>


                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.page-content -->
    </div><!-- /.main-content -->

@endsection


@section('js')
@include('includes.scripts.dataTable_scripts')
    <script type="text/javascript">
        jQuery(function($) {

            $('table th input:checkbox').on('click' , function(){
                var that = this;
                $(this).closest('table').find('tr > td:first-child input:checkbox')
                    .each(function(){
                        this.checked = that.checked;
                        $(this).closest('tr').toggleClass('selected');
                    });

            });

        })
    </script>

@endsection