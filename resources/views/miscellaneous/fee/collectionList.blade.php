@extends('layouts.master')
@section('content')

    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                <div class="page-header">

                    <h1> Miscellaneous Fees Manager 
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Collection List
                        </small>
                    </h1>
                </div><!-- /.page-header -->
            </div>
            @include('includes.flash_messages')
            

            <div class="page-content"><div class="table-responsive">
                <h4 class="header large lighter blue"><i class="fa fa-search" aria-hidden="true"></i>&nbsp;Search Student</h4>
    <div class="clearfix"><form>

        <div class="form-group">
            <label class="col-sm-2 control-label">{{ env('course_label') }}</label>
            <div class="col-sm-3">
                
                {!! Form::select('faculty', $course_list, null, ['class' => 'form-control', 'onChange' => 'loadSemesters(this);']) !!}

            </div>
            {!! Form::label('reg_date', 'Reg. Date', ['class' => 'col-sm-2 control-label']) !!}
            <div class=" col-sm-5">
                <div class="input-group ">
                    {!! Form::date('reg_start_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
                    <span class="input-group-addon">
                        <i class="fa fa-exchange"></i>
                    </span>
                    {!! Form::date('reg_end_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
                    @include('includes.form_fields_validation_message', ['name' => 'reg_start_date'])
                    @include('includes.form_fields_validation_message', ['name' => 'reg_end_date'])
                </div>
            </div>
        
    </div>
</div>
<div class="clearfix">
    <div class="form-group">
        <label class="col-sm-2 control-label">Category</label>
        <div class="col-sm-3">
            {{ Form::select('category', $category_list, null, ['class' => 'form-control']) }}
        </div>

        <label class="col-sm-2 control-label">Search By Name / Reg No</label>
        <div class="col-sm-2">
            <input type="text" name="name" class="form-control" value="{{ $data['filter_query']['name'] or ''}}" placeholder="Enter Name or Reg No"/>
        </div>
        {!!Form::label('mode','Mode',['class'=>'col-sm-1 control-label'])!!}
        <div class="col-sm-2">
            {!! Form::select('payment_type', $pay_type_list, null, ['class' => 'form-control']) !!}
        </div>
        
    </div>
     
</div>

<div class="clearfix">
    <div class="form-group">
        <label class="col-sm-2 control-label">Fee Head</label>
        <div class="col-sm-3">
            {!! Form::select('fee_head', $feeHead, null, ['class' => 'form-control']) !!}
        </div>

        <label class="col-sm-2 control-label">Ref. No. / Receipt No. </label>
        <div class="col-sm-2">
            <input type="text" name="ref_no" class="form-control" value="" placeholder="Enter Refe No / Receipt No"/>
        </div>
        <label class="col-sm-1 control-label">Receipt By</label>
        <div class="col-sm-2">
            {!!Form::select('receipt_by',$user,null,['class'=>'form-control'])!!}
        </div>
    </div>
    <div class="form-group">    
        <div class="col-sm-2">
            <div class="align-right">
                <button class="btn btn-info" type="submit" id="filter-btn">
                    <i class="fa fa-filter bigger-110"></i>
                    Search
                </button>
            </div>
        </div>
        
    </div>

</div>

<!--div class="clearfix form-actions">
    
</div-->
</form>

<div class="row">
    <div class="col-xs-12">
        @include('includes.data_table_header')
    </div>
</div>

            <table id="dynamic-table" class="table table-striped table-bordered table-hover" style="font-size: 11px !important">
                <thead>
                <tr>      
                    <th>S.N.</th>
                    <th>Reg No</th>   
                    <th>Student Name</th>
                    <th>Father Name</th>
                    <th>{{ env('course_label') }} Name</th>
                    <!-- <th>Session</th> -->
                    <th>Receipt No</th>
                    <!-- <th>Fees Head</th> -->
                    <th>Amount</th>
                    <th>Discount</th>
                    <th>Mode</th>
                    <th>Receipt By</th>
                    <th>Receipt Date</th>
                    <th>Ref No</th>
                    <th>Action</th>               
                </tr>
                </thead>
                <tbody> 
                    <?php $i=1; $ttl=0; $total_disc=0; ?>
                    @foreach($collection_list as $x)
                    <?php $ttl += $x->amount_paid;
                            $total_disc += $x->discount;
                     ?>
                            <tr>
                                <td class="center first-child">
                                    <label>
                                        {{$i++}}
                                    </label>
                                </td>
                                <td>{{$x->reg_no}}</td>
                                <td>{{$x->first_name}}</td>
                                <td>{{$x->father_name}}</td>
                                <td>{{$x->faculty}}</td>
                                <!-- <td>{{$x->session_name}}</td> -->
                                <td>{{$x->reciept_no}}</td>
                                <!-- <td>{{$x->fee_head_title}}</td> -->
                                <td>{{$x->amount_paid}}</td>
                                <td>{{$x->discount!=null?$x->discount:'0'}}</td>
                                <td>{{ucfirst($x->payment_type)}}</td>
                                <td>{{$x->name}}</td>
                <td>@if($x->reciept_date) {{ Carbon\Carbon::parse($x->reciept_date)->format('d-M-Y') }} @endif</td>
                                <td>{{$x->reference}}</td>
<td >@ability('super-admin','super-admin')
    <a href="/miscellaneous/edit/fee_collection/{{$x->id}}" title="Edit" class="tooltip-error btn btn-minier" data-rel="tooltip"><i class="fa fa-pencil"></i></a>
    <a href="/miscellaneous/delete/fee_collection/{{$x->id}}" class="tooltip-error red deleteConfirm btn btn-minier btn-danger" data-rel="tooltip" title="Delete" id="delete"><i class="fa fa-trash" ></i></a>
 @endability
 @ability('super-admin','account')
 <a href="/miscellaneous/cancel/fee_receipt/{{$x->id}}" class="tooltip-error green cancelConfirm btn btn-minier btn-warning" data-rel="tooltip" title="Cancel" id="cancel"><i class="fa fa-times" aria-hidden="true"></i></a>
 @endability
 <a target="_blank" href="/miscellaneous/studentfeeReceipt/{{$x->reciept_no}}" title="Print" class="tooltip-error btn btn-minier btn-info" data-rel="tooltip"><i class="fa fa-print"></i></a>  
</td>
                            </tr>
                           @endforeach
                   @if(empty($collection_list))
                        <tr>
                            <td colspan="12">No Collection Fees data found.</td>
                            </td>
                        </tr>
                    @endif
                   <tr>
                    <td>{{$i}}</td><td></td><td></td><td></td><td></td>
                    <td class="text-right"><b>Total: </b></td> 
                    <td class="text-left"><i class="fa fa-inr"></i> {{$ttl}}</td>
                    <td><i class="fa fa-inr"> {{$total_disc}}</td><td></td><td></td><td></td>
                    <td></td><td></td>                    
                </tbody>
              
            </table>
           <!-- {{-- $collection_list->links() --}}-->
        </div>
        </div>
    </div></div>
@endsection
@section('js')
    <script type=""> 
          $("a.deleteConfirm").on('click', function() { 
                var $this = $(this);
                bootbox.confirm({
                        title: "<div class='widget-header'><h4 class='smaller'><i class='ace-icon fa fa-exclamation-triangle red'></i> Delete Confirmation</h4></div>",
                        message: "<div class='ui-dialog-content ui-widget-content' style='width: auto; min-height: 30px; max-height: none; height: auto;'><div class='alert alert-info bigger-110'>" +
                        "This Record Will Be Deleted Permanently</div>" +
                        "<p class='bigger-110 bolder center grey'><i class='ace-icon fa fa-hand-o-right blue bigger-120'></i>Are you sure?</p>",
                        size: 'small',
                        buttons: {
                            confirm: {
                                label : "<i class='ace-icon fa fa-history'></i> Yes, Delete Now!",
                                className: "btn-danger btn-sm",
                            },
                            cancel: {
                                label: "<i class='ace-icon fa fa-remove'></i> Cancel",
                                className: "btn-primary btn-sm",
                            }
                        },
                        callback: function(result) {
                            if(result) {
                                location.href = $this.attr('href');
                            }
                        }
                    }
                );
                return false;
            });
          $("a.cancelConfirm").on('click', function() { 
                var $this = $(this);
                bootbox.confirm({
                        title: "<div class='widget-header'><h5 class='smaller'><i class='ace-icon fa fa-exclamation-triangle red'></i>Receipt Cancel Confirmation</h5></div>",
                        message: "<div class='ui-dialog-content ui-widget-content' style='width: auto; min-height: 30px; max-height: none; height: auto;'><div class='alert alert-info bigger-110'>" +
                        "All Fees Collection Related To This Receipt Number Will Be Cancelled.</div>" +
                        "<p class='bigger-110 bolder center grey'><i class='ace-icon fa fa-hand-o-right blue bigger-120'></i>Are you sure?</p>",
                        size: 'small',
                        buttons: {
                            confirm: {
                                label : "<i class='ace-icon fa fa-history'></i> Yes,Cancel Receipt!",
                                className: "btn-danger btn-sm",
                            },
                            cancel: {
                                label: "<i class='ace-icon fa fa-remove'></i> Cancel",
                                className: "btn-primary btn-sm",
                            }
                        },
                        callback: function(result) {
                            if(result) {
                                location.href = $this.attr('href');
                            }
                        }
                    }
                );
                return false;
            });
    </script>
     @include('includes.scripts.delete_confirm')
@endsection

