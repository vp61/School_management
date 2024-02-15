@extends('layouts.master')
@section('content')

    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                <div class="page-header">
                    <h1> Fees Manager 
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Collect Fees
                        </small>
                    </h1>
                </div><!-- /.page-header -->
            </div>
            @include('includes.flash_messages')
            <div class="page-content">
                <form action="{{route('bulk_collect_fee')}}" method="post" class="disable_save_form">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-sm-12">
                            @if(Session::has('msG'))
                            <div class="alert alert-success">
                                {{Session::get('msG')}}
                            </div>
                            @endif                    
                            <table class="table table-striped">
                                @if($errors->any())
                                    <div class="alert alert-danger">
                                        @foreach($errors->all() as $err)
                                            <div>{{$err}}</div>
                                        @endforeach
                                    </div>
                                @endif
                                <tr>
                                    <!-- <td style="padding-top:10px;" class="hidden">
                                        Session:
                                    </td> -->
                                    <div class="hidden">
                                        {{ Form::select('session', $session_list, $current_session, ['class'=>'sesn form-control', 'required'=>'required'])}}
                                        <span class="error">{{ $errors->first('session') }}</span>
                                    </div>
                                    <!-- <td style="padding-top:10px;" class="hidden">Branch:</td> -->
                                        <div class="hidden">
                                             <select name="branch_id" class="branch_drop form-control" required>
                                                <option value="{{$branch_list[0]->id}}">{{$branch_list[0]->branch_name}}</option>
                                            </select>
                                            <span class="error">{{ $errors->first('branch_id') }}</span>
                                        </div>

                                    @if(Session::get('isCourseBatch'))
                                        <td style="padding-top:10px;">{{ env('course_label') }}:</td>
                                        <td >
                                            {{ Form::select('course', $course_list, '', ['class'=>'batch_wise_cousre form-control selectpicker', 'required'=>'required', 'data-live-search'=>'true','id'=>'batch_wise_cousre'])}}
                                            <span class="error">{{ $errors->first('course') }}</span>
                                        </td>
                                        <td style="padding-top:10px;">Batch:</td>
                                        <td >
                                            {{ Form::select('batch',[''=>'Select Batch'], '', ['class'=>'batch form-control selectpicker', 'required'=>'required', 'data-live-search'=>'true','id'=>'batch'])}}
                                        </td>
                                    @else
                                        <td style="padding-top:10px;">
                                            Months:
                                        </td>
                                        <td style="max-width:150px;">
                                            {{ Form::select('months[]', $months_list,null, ['class'=>'mnt form-control selectpicker ','id'=>'due_month', 'data-live-search'=>'true','multiple'])}}
                                            <span class="error">{{ $errors->first('months') }}</span>
                                        </td>
                                        <td style="padding-top:10px;">
                                            {{ env('course_label') }}:
                                        </td>
                                        <td>
                                            {{ Form::select('course', $course_list, '', ['class'=>'cors form-control selectpicker', 'required'=>'required', 'data-live-search'=>'true'])}}
                                            <span class="error">{{ $errors->first('course') }}</span>
                                        </td>
                                    @endif
                                    <td style="padding-top:10px;">
                                        Student:
                                    </td>
                                    <td>
                                        {{ Form::select('student', $student_list, '', ['class'=>'stdnt form-control selectpicker', 'data-live-search'=>'true','id'=>'std'])}}
                                    </td>
                                    <!-- <td><b>OR</b></td> -->
                                    <td style="border-left: 1px solid #b8b3b3;">
                                        Reg No.
                                    </td>
                                    <td>
                                        <div class="input-group"> 
                                                {!!Form::text('reg_no',null,['id'=>'reg_no','placeholder'=>'Registration No.'])!!}<span class="input-group-addon" style="width: 0px;cursor: pointer;background: #6fb3e0;color: white;" id="search_no">Search</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="9" class="fee_box">
                                        <div style="min-height:200px;"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <b>Payment Type: </b>
                                    </td>
                                    <td>
                                        {{ Form::select('payment_type', $pay_type_list, '', ['class'=>'form-control', 'required'=>'required']) }}
                                    </td>
                                    <td colspan="1">
                                        <b>Reference: </b>
                                    </td>
                                    <td colspan="2">
                                        <input type="text" name="reference" class="form-control" placeholder="Enter Reference" />
                                    </td>
                                    <td>
                                        <b>Date</b>
                                    </td>
                                    <td colspan="2">
                                        <input type="date" name="reciept_date" value="<?php echo date("Y-m-d"); ?>" required />
                                    </td>
                                    
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <b>Amount Paid</b>
                                    </td>
                                    <td colspan="1">
                                        <input type="number"  class="amount" onkeyup="sum_amount();"placeholder="Paid Amount" name="amount"  min="0" required />
                                    </td>
                                    
                                    <td>
                                        <b>Remark</b>
                                    </td>
                                    <td colspan="2">
                                        <input type="text"  class="pull-right" placeholder="Enter Remarks" name="remark" />
                                    </td>
                                     <td  class="text-center">
                                        <input type="submit" name="submit" value="Save" class="btn btn-info"  id="disable_save_btn" />
                                    </td>
                                    
                                </tr>
                                
                                    
                            </table>
                        </div>
                    </div>
                </form>
            </div>   
        </div>
    </div>
    <div class="label label-warning arrowed-in arrowed-right arrowed">Paymet History</div>
    <hr class="hr-8">
    <div class="table-responsive ">
            <table id="dynamic-table-fee-list" class="table table-striped table-bordered table-hover">
            </table>
    </div>
@endsection
@section('js')

    <script>
       
        function sum_amount(){
            var amount=$('.amount');
            sum=0;
            for(var i = 0; i < amount.length; i++){
                var temp=parseInt($(amount[i]).val());
                if(temp>0)
                    sum=sum+temp;
            }
            $('#total_amount').html(sum);
            
        }
        function sum_discount(){
            var discount=$('.discount');
            sum=0;
            for(var i = 0; i < discount.length; i++){
                var temp=parseInt($(discount[i]).val());
                if(temp>0)
                    sum=sum+temp;
            }
            $('#total_discount').html(sum);
            
        }
        $('.cors').change(function(){
            var cors_id = $(this).val(); 
            var brnch=$('.branch_drop').val();
            var selected_session=$('.sesn').val();
            var smstr=$('.semester').val();
            $.post("{{route('student_select')}}", {branch:brnch,selected_session:selected_session,semester:smstr,course:cors_id, _token:'{{ csrf_token() }}'}, function(response){
                $('select.stdnt').html(response);
                $('.selectpicker').selectpicker('refresh');
            });
        });
         <?php if(Session::get('isCourseBatch')){
                ?>
                $('.stdnt').change(function(){
                    var cors_id=$('select.batch_wise_cousre').val(); 
                    var stud = $(this).val(); 
                    var brnch=$('.branch_drop').val();
                    var batch=$('#batch').val();
                    var session=$('.sesn').val();
                    $.post("{{route('bulk_student_fee')}}", {branch:brnch, ssn:session, course:cors_id, student:stud,batch:batch, _token:'{{ csrf_token() }}'}, function(response){
                            $('.fee_box').html(response);
                    });
                    $.post("{{route('student_fee_history')}}", {branch:brnch, ssn:session, course:cors_id, student:stud,batch:batch, _token:'{{ csrf_token() }}'}, function(response){
                        
                        $('#dynamic-table-fee-list').html(response);
                    });
                });
            <?php } else { ?>
                $('.stdnt').change(function(){
                    var cors_id=$('select.cors').val(); 
                    var stud = $(this).val(); 
                    var brnch=$('.branch_drop').val();
                    var month=$('#due_month').val();
                    var session=$('.sesn').val();
                    $.post("{{route('bulk_student_fee')}}", {branch:brnch, ssn:session, course:cors_id, student:stud,due_month:month, _token:'{{ csrf_token() }}'}, function(response){
                            
                            $('.fee_box').html(response);
                    });
                    $.post("{{route('student_fee_history')}}", {branch:brnch, ssn:session, course:cors_id, student:stud, _token:'{{ csrf_token() }}'}, function(response){
                        
                        $('#dynamic-table-fee-list').html(response);
                    });
                });
            <?php } ?>

        $('.disable_save_form').submit(function(){

        var btn = $(this).find('#disable_save_btn')
        $(btn).attr('disabled',true);
        return true;
    });    
    </script>
@endsection
