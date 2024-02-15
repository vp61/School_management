@extends('layouts.master')
@section('content')

    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                <div class="page-header">

                    <h1> Fees Manager 
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            @php $panel='Discount' @endphp

                            {{$panel}}  List
                        </small>
                    </h1>
                </div><!-- /.page-header -->
            </div>
            @include('includes.flash_messages')
            

            <div class="page-content">
                <h4 class="header large lighter blue"><i class="fa fa-search" aria-hidden="true"></i>&nbsp;Search Student</h4>
                <form>
                    <div class="clearfix">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">{{ env('course_label') }}</label>
                            <div class="col-sm-3">
                                {!! Form::select('faculty', $data['course_list'], null, ['class' => 'form-control', 'onChange' => 'loadSemesters(this);']) !!}
                            </div>
                            {!! Form::label('reg_date', 'Receipt Date', ['class' => 'col-sm-2 control-label']) !!}
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
                            <label class="col-sm-2 control-label">Discount Status</label>
                            <div class="col-sm-3">
                                {{ Form::select('discount_status',[''=>'Select Discount Status','pending'=>'Pending','rejected'=>'Rejected','approved'=>'Approved'], null, ['class' => 'form-control']) }}
                            </div>
                            <label class="col-sm-2 control-label">Search By Name / Reg No</label>
                            <div class="col-sm-2">
                                <input type="text" name="name" class="form-control" value="{{ $data['filter_query']['name'] or ''}}" placeholder="Enter Name or Reg No"/>
                            </div>
                            {!!Form::label('mode','Mode',['class'=>'col-sm-1 control-label'])!!}
                            <div class="col-sm-2">
                                {!! Form::select('payment_type', $data['pay_type_list'], null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="clearfix">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Fee Head</label>
                            <div class="col-sm-3">
                                {!! Form::select('fee_head', $data['feeHead'], null, ['class' => 'form-control']) !!}
                            </div>

                            <label class="col-sm-2 control-label">Reference No.</label>
                            <div class="col-sm-2">
                                <input type="text" name="ref_no" class="form-control" value="" placeholder="Enter Reference No"/>
                            </div>
                            <label class="col-sm-1 control-label">Receipt By</label>
                            <div class="col-sm-2">
                                {!!Form::select('receipt_by',$data['user'],null,['class'=>'form-control'])!!}
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
                </form>
                <div class="row">
                    <div class="col-xs-12">
                        @include('includes.data_table_header')
                    </div>
                </div>
                <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>      
                            <th>S.N.</th>
                            <th>Reg No</th>   
                            <th>Student Name</th>
                            <th>{{ env('course_label') }} Name</th>
                            <!-- <th>Session</th> -->
                            <th>Receipt No</th>
                            <th>Fees Head</th>
                            <th>Amount</th>
                            <th>Discount</th>
                            <th>Remark</th>
                            <th>Receipt By</th>
                            <th>Receipt Date</th>
                            <th>Discount Status</th>
                            <th>Action</th>               
                        </tr>
                    </thead>
                    <tbody> 
                        <?php $i=1; $ttl=0; ?>
                        @foreach($data['collection_list'] as $x)
                        <?php $ttl += $x->amount_paid; ?>
                                <tr>
                                    <td class="center first-child">
                                        <label>
                                            {{$i++}}
                                        </label>
                                    </td>
                                    <td>{{$x->reg_no}}</td>
                                    <td>{{$x->first_name}}</td>
                                    <td>{{$x->faculty}}</td>
                                    <!-- <td>{{$x->session_name}}</td> -->
                                    <td>{{$x->reciept_no}}</td>
                                    <td>{{$x->fee_head_title}}</td>
                                    <td>{{$x->amount_paid}}</td>
                                    <td id="disc{{$x->id}}">{{$x->discount!=null?$x->discount:'0'}}</td>
                                    <td>{{$x->remark}}</td>
                                    <td>{{$x->name}}</td>
                                    <td>@if($x->reciept_date) {{ date('d-m-Y', strtotime($x->reciept_date)) }} @endif</td>
                                    <td id="status{{$x->id}}"><b>
                                            <?php if($x->discount_status==null){

                                            }
                                            elseif($x->discount_status==0){
                                                    echo "Rejected";
                                            }elseif($x->discount_status==1){
                                                echo "Approved";
                                            }
                                            
                                             ?>
                                        </b>    
                                    </td>
                                    <td>@ability('super-admin','super-admin')
                                        @if($x->discount_status == '0' || $x->discount_status == '1')

                                        @else
                                        <a href="#" class="tooltip-error green  btn btn-minier btn-success action_btn{{$x->id}}" data-rel="tooltip" title="Approve" id="approve" data-toggle="modal" onclick='discount_status("{{$x->id}}","{{$x->discount}}",1)' ><i class="fa fa-check" aria-hidden="true"></i></a>
                                        <a href="#" class="tooltip-error red  btn btn-minier btn-danger action_btn{{$x->id}}" data-rel="tooltip" title="Reject" id="reject" data-toggle="modal" onclick='discount_status("{{$x->id}}","{{$x->discount}}",0)'><i class="fa fa-times" ></i></a>
                                        @endif
                                     @endability
                                     @ability('super-admin','account')
                                     
                                     @endability
                                     <a target="_blank" href="studentfeeReceipt/{{$x->reciept_no}}" title="Print" class="tooltip-error btn btn-minier btn-info" data-rel="tooltip"><i class="fa fa-print"></i></a>  
                                    </td>
                                </tr>
                               @endforeach
                       @if(empty($data['collection_list']))
                            <tr>
                                <td colspan="12">No Collection Fees data found.</td>
                            </tr>
                        @endif
                       <tr>
                        <td>{{$i}}</td><td></td><td></td><td></td><td></td>
                        <td class="text-right"><b>Total: </b></td> 
                        <td class="text-left"><i class="fa fa-inr"></i> &nbsp; {{$ttl}}</td>
                        <td></td><td></td><td></td><td></td>
                        <td></td><td></td>                    
                    </tbody>
                </table>
                <div class="modal fade" id="statusModal" role="dialog">
            <div class="modal-dialog modal-sm" style="width:50%">
            <form id="statusForm">  
              <div class="modal-content">
                <div class="modal-header" style="color: white; background: #438eb9;font-weight: 500;font-size: 15px;">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title" id="statusModalTitle"></h4>
                </div>
                <div class="modal-body"> 
                   <div class="row hide_class status_modal_form">
                       <div class="col-sm-12">
                           <div class="form-group">
                               {!!Form::label('amount','Discount Amount ',['class'=>'col-sm-4 control-label'])!!}
                               <div class="col-sm-8">
                                    <b>&#8377;</b> <b id="statusModalAmount"></b>
                               </div>
                           </div>
                        </div>
                        <div class="col-sm-12">   
                           <div class="form-group">
                               {!!Form::label('comment','Reason / Comment',['class'=>'col-sm-4 control-label'])!!}
                               <div class="col-sm-8">
                                   {!!Form::textarea('comment',null,['class'=>'form-control','rows'=>'4','id'=>'comment','required'])!!}
                                   {!!Form::hidden('collect_id',null,['id'=>'collect_id'])!!}
                                   {!!Form::hidden('status',null,['id'=>'status'])!!}
                               </div>
                           </div>
                       </div>
                   </div>
                   <div class="row" >
                       <div class="col-sm-12 modalMsg" id="Msg">
                       </div>
                   </div>
                </div>
                <div class="modal-footer">
                  <button class="btn btn-info" id="save" type="submit">
                    Save
                  </button>
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
              </div>
            </form>
            </div>
        </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script type=""> 
        function discount_status($collect_id,$amount,$status){
            var amt=($amount>0?$amount:'0');
            var title=($status==1?'Approve Discount':'Reject Discount');
            $('#statusModalAmount').html(amt);
            $('#statusModalTitle').html(title);
            $('#comment').val('');
            $('#collect_id').val($collect_id);
            $('#status').val($status);
            $('.status_modal_form').show();
            $('#Msg').html('');
            $('#save').attr('disabled',false);
            $('#save').show();
            $('#statusModal').modal('show');
        }
        $("#statusForm").on('submit',function(){
            $('#save').attr('disabled',true);
            var collect_id=$('#collect_id').val();
            var status=$('#status').val();
            var comment=$('#comment').val();
            $.post(
                "{{route('change_discount_status')}}",
                {
                 _token : '{{ csrf_token() }}',
                 collect_id : collect_id,
                 status : status,
                 comment : comment
                },
                function(response){
                    var data = $.parseJSON(response);
                    if(data.error){
                        toastr.warning(data.msg,'Warning');
                    }else{
                        toastr.success(data.msg,'Success');
                        var disc='disc'+collect_id;
                        var stat='status'+collect_id;
                        var action_btn='action_btn'+collect_id;
                        if(status==1){
                            var disc_stat = 'Approved';
                        }else if(status==0){
                            var disc_stat = 'Rejected';
                        }
                        $('#'+disc).html('').append(data.amount);
                        $('#'+stat).html('').append(disc_stat);
                        $('.status_modal_form').hide();
                        $('#save').hide();
                        $('.'+action_btn).hide();
                        $('#Msg').html('').append('<h4>Discount Status Updated Successfully</h4>');
                    }
                }
        );
            return false;
        })
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

