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
                  {{-- @include('includes.validation_error_messages')--}}
                    <div class="col-md-4">
<h4 class="header large lighter blue"><i class="fa fa-search" aria-hidden="true"></i>&nbsp;{{$panel}} Form</h4>
{!! Form::open(['route' => 'branch_ops']) !!}
 
<input type="hidden" name="org_id" value="{{Auth::user()->org_id}}">
<input type="hidden" name="edt" value="{{ $db['id'] or old('edt')}}">
    @foreach($flds as $fld=>$lbl)
    @php $default = (isset($db[$fld])) ? $db[$fld]:""; @endphp
    <div class="row form-group">
        @if($fld!="id")
{!! Form::label($fld, $lbl, ['class' => 'col-sm-4 control-label']) !!}
        @endif
<div class="col-sm-8">
    @if($fld=="id")
    {{Form::hidden($fld, $default, ['class'=>'form-control'])}}
    {{$errors->first($fld)}}
    @else
    {{Form::text($fld, $default, ['class'=>'form-control', 'placeholder'=>'Enter '.$lbl])}}
    @endif
    <span class="error">{{$errors->first($fld)}}</span>
</div>
    </div>
@endforeach
    <div class="row"><div class="col-sm-5 col-sm-offset-7">
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
                    <th class="center">
                        <label class="pos-rel">
                            <input type="checkbox" class="ace" />
                            <span class="lbl"></span>
                        </label>
                    </th>
                    <th>S.N.</th>
                    <th>{{ $panel }}</th>
                    <th>Mobile</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @if (isset($data['tbl']) && $data['tbl']->count() > 0)
                    @php($i=1)
                    @foreach($data['tbl'] as $tbl)
                        <tr>
                            <td class="center first-child">
                                <label>
                                    <input type="checkbox" name="chkIds[]" value="{{ $tbl->id }}" class="ace" />
                                    <span class="lbl"></span>
                                </label>
                            </td>
                            <td>{{ $i }}</td>
                            <td>
                                {{ $tbl->branch_name }}
                            </td>
                            <td >{{ $tbl->branch_mobile }}</td>
                            <td>{{ $tbl->branch_email }}</td>
                            <td class="hidden-480">{{$tbl->branch_address}}</td>
                            <td>
                                <div class="hidden-sm hidden-xs action-buttons">
<a class="green" href="branch/{{ $tbl->id }}">
    <i class="ace-icon fa fa-pencil bigger-130"></i>
</a>

                                </div>
                                <div class="hidden-md hidden-lg">
                                    <div class="inline pos-rel">
                                        <button class="btn btn-minier btn-yellow dropdown-toggle" data-toggle="dropdown" data-position="auto">
                                            <i class="ace-icon fa fa-caret-down icon-only bigger-120"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-only-icon dropdown-yellow dropdown-menu-right dropdown-caret dropdown-close">
                                            <li>
<a href="branch/{{ $tbl->id }}" class="tooltip-success" data-rel="tooltip" title="Edit">
    <span class="green">
        <i class="ace-icon fa fa-pencil-square-o bigger-120"></i>
    </span>
</a>
                                            </li>

                                            
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @php($i++)
                    @endforeach
                @else
                    <tr>
                        <td colspan="7">No {{ $panel }} data found.</td>
                        </td>
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