@extends('layouts.master')

@section('css')
    <!-- page specific plugin styles -->
@endsection

@section('content')

    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                <div class="page-header">
                    <h1>
                        {{ $panel }} Manager
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            {{ $panel }} Detail
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    @include('includes.flash_messages')
                    @include('includes.validation_error_messages')
                    <div class="col-md-4">
                        <h4 class="header large lighter blue"><i class="fa fa-search" aria-hidden="true"></i>&nbsp;{{$panel}} Form</h4>
                        @php
                            $session = isset($data['row']->session_id) ? $data['row']->session_id :'';
                            $branch = isset($data['row']->branch_id) ? $data['row']->branch_id :'';
                            $isCourseBatch = isset($data['row']->is_course_batch) ? $data['row']->is_course_batch :'';
                        @endphp
                            @if(isset($data['row']))
                                 {!!Form::model($data['row'],['route'=>['branch_batchwise.edit', $data['row']->id],'method'=>'POST', 'class' => 'form-horizontal',
                                        'id' => 'validation-form', "enctype" => "multipart/form-data"])!!}
                              @else 
                                 {!!Form::open(['route'=>'branch_batchwise','method'=>'POST', 'class' => 'form-horizontal',
                                        'id' => 'validation-form', "enctype" => "multipart/form-data"])!!} 
                              @endif  
                            <div class="form-group">
                                <div class="row">
                                    {!! Form::label('branch','Branch', ['class' => 'col-sm-4 control-label']) !!}
                                    <div class="col-sm-8">
                                        {{Form::select('branch_id',$data['branch'],$branch, ['class'=>'form-control','required'])}}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    {!! Form::label('session','Session', ['class' => 'col-sm-4 control-label']) !!}
                                    <div class="col-sm-8">
                                        {{Form::select('session_id',$data['session'],$session, ['class'=>'form-control','required'])}}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    {!! Form::label('course','Is Course Batch', ['class' => 'col-sm-4 control-label']) !!}
                                    <div class="col-sm-8">
                                        {!! Form::select('is_course_batch',[''=>'--Select--','1'=>'YES','0'=>'NO'],$isCourseBatch, ['class'=>'form-control','required'])!!}
                                    </div>
                                </div>

                            </div>
                                <div class="row"><div class="col-sm-12 text-right">
                                    <input type="submit" name="submit" value="Save" class="btn btn-info">
                                </div></div>

                            {!! Form::close() !!}
                    </div>
                   
                    <div class="col-md-8 col-xs-12">
                        <div class="row">
                            <div class="col-xs-12">
                                @include('includes.data_table_header')
                                <!-- div.table-responsive -->
                                <div class="table-responsive">
                                    <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                                        <thead>
                                        <tr>
                                            <th>S.N.</th>
                                            <th>Branch</th>
                                            <th>Session</th>
                                            <th>Is Course Batch</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if (isset($data['records']) && $data['records']->count() > 0)
                                            @php($i=1)
                                            @foreach($data['records'] as $tbl)
                                                <tr>
                                                    <td>{{ $i }}</td>
                                                    <td>
                                                        {{ $tbl->branch_name }}
                                                    </td>
                                                    <td >{{ $tbl->session_name }}</td>
                                                    <td>{{ $tbl->is_course_batch == 1?'Yes':'No' }}</td>
                                                    <td>
                                                        <div class="hidden-sm hidden-xs action-buttons">
                                                            <a class="green" href="{{route('branch_batchwise.edit',[$tbl->id])}}">
                                                                <i class="ace-icon fa fa-pencil bigger-130"></i>
                                                            </a>

                                                        </div>
                                                    </td>
                                                </tr>
                                                @php($i++)
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="5">No {{ $panel }} data found.</td>
                                            </tr>
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                    </div>
                    
                </div><!-- /.row -->

            </div><!-- /.page-content -->
    </div><!-- /.main-content -->



@endsection

@section('js')
    <!-- page specific plugin scripts -->
    @include('includes.scripts.delete_confirm')
    @include('includes.scripts.bulkaction_confirm')
    @include('includes.scripts.dataTable_scripts')
    <script>
        $(document).ready(function () {
            /*Change Field Value on Capital Letter When Keyup*/
            $(function() {
                $('.upper').keyup(function() {
                    this.value = this.value.toUpperCase();
                });
            });
            /*end capital function*/

        });
    </script>
@endsection