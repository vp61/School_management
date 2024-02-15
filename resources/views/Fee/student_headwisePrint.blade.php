@extends('layouts.master')
@section('content')
@php
    $data=array();
    foreach($printed_data['branch'][0] as $key=>$val){
        $data[$key]=$val; 
    } //dd($due_tbl);

    foreach($printed_data as $key=>$val){ $data[$key]=$val; }
    //foreach($due_tbl as $key=>$val){ $data['tbl'][$key]=$val; }
    $topay_arr=$paid_arr=$due_arr=$ttl=array();
@endphp
<hr class="hr-8">
<div class="container">
    <div class="row">
      

        <div class="receipt-main col-xs-12 col-sm-12 col-md-12 col-xs-offset-1 col-sm-offset-1 col-md-offset-1" style="margin-left: 0px !important;">
            <div class="row">
                <div class="receipt-header">
                    <div class="col-xs-12 col-sm-12 col-md-12 text-center"><h4>{{$data['branch_name']}}</h4>
                </div>
            </div>
            
            <div class="row">
                <div class="receipt-header">
                    <div class="col-xs-3 col-sm-3 col-md-3">
                        <div class="receipt-left">
                            <img class="img-responsive" alt="iamgurdeeposahan" src="{{ asset('images/logo/')}}/{{$data['branch_logo']}}" style="width: 78px;">
                        </div>
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4 text-right">
                        
                    </div>
                    <div class="col-xs-5 col-sm-5 col-md-5 text-right">
                        <div class="receipt-right">
                            <!--h2>{{$data['branch_name']}}</h2-->
                            <p><i class="fa fa-phone"></i> &nbsp; {{$data['branch_mobile']}}</p>
                            <p><i class="fa fa-envelope-o"></i> &nbsp; {{$data['branch_email']}}</p>
                            <p><i class="fa fa-location-arrow"></i> &nbsp; {{$data['branch_address']}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <hr/>
            

            <div class="row">
                <div class="receipt-header receipt-header-mid">
                    <div class="col-xs-4 col-sm-4 col-md-4 text-left">
                        <div class="receipt-right">
                            
                        </div>
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4" style="padding-bottom: 3px;text-align: center;white-space: nowrap;">
                        <h4 text-align="center">Due Report Head Wise- {{env('course_label')}} ({{$info['courseName']}})</h4>
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4 " style="padding-bottom: 3px;">
                        <div class="receipt-right "> 
                            <button class="btn btn-info display_type" type="submit" id="filter-btn" onclick="Export('<?php echo "fileName"; ?>')" style="float: right;" >
                            <i class="fa fa-download bigger-110"></i>
                            Excel Export
                            </button>
                        
                        </div>
                        <div class=" receipt-right" >
                            <button class="btn btn-info hidden-print display_type" style="display: none;float: right;" id="btn_print">Print <i class="fa fa-print"></i></button>
                        </div>
                        <button class="btn btn-success display_btn receipt-right" style="float: right;margin-right: 10px;">Column View</button>
                        <button class="btn btn-success display_btn receipt-right hidden-print" style="display: none;float: right;margin-right: 10px;">Row View</button>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <table class="table">
                            <tr>
                                <td style="font-weight: 700">Search Criteria:</td>
                                
                                @foreach($search_criteria as $key=>$val)
                                    @if(!empty($val))
                                        <td class="text-center">{{$key}} - {{$val}}</td>
                                       
                                    @endif    
                                @endforeach
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
           <br>
          
            <div style="overflow: scroll;" class="display_type">
                <table class="table table-bordered" id="tblFeeHeadWiseExl">
                    <thead>
                        <tr style="font-size: 10px !important;">
                            <th>#</th>
                            <th>Reg No</th>
                            <th nowrap>Students</th>
                            <th nowrap>Father Name</th>
                            @foreach($fee_arr as $fee_name)
                                @if(isset($colums['to_pay'])) <th nowrap>{{ $fee_name }}</th> @endif
                               @if(isset($colums['paid']))  <th nowrap>{{ $fee_name }} Deposited</th> @endif
                                @if(isset($colums['con'])) <th nowrap>{{$fee_name}} Discount</th> @endif
                                <th nowrap>{{ $fee_name }} Balance</th>
                            @endforeach
                            <th nowrap>Total Due</th>
                        </tr>
                    </thead>
                    <tbody> 
                    @php $i = 1;$total_due=0;$total=[];  @endphp
                    @foreach($due_tbl as $student_id=>$data_arr) 
                    @php $ttl_due=$gt=0; //dd($fee_arr); @endphp
                       <tr style="font-size: 10px;">     <td>{{ $i++ }}</td>

                                @foreach($fee_arr as $feename)
                                    <td>{{ $data_arr[$feename]['admission_no'] }}</td>
                                    <td nowrap>{{ $data_arr[$feename]['student'] }}</td>
                                    <td nowrap>{{ $data_arr[$feename]['fatherName'] }}</td>
                                    @php break; @endphp
                                
                                @endforeach

                                @foreach($fee_arr as $fee_name2)
                    @php if(isset($data_arr[$fee_name2]['to_pay'])){
                       $to_pay = $data_arr[$fee_name2]['to_pay']; //echo $to_pay;
                       if(isset($total[$fee_name2]['total_pay'])){
                         $total[$fee_name2]['total_pay']=$to_pay+$total[$fee_name2]['total_pay'];
                        }else{
                        $total[$fee_name2]['total_pay']=$to_pay;
                        }
                       
                    } @endphp
                    @if(isset($colums['to_pay']))
                        <td>
                        {{$to_pay}}      
                        </td>
                    @endif
                    @php
                    if(isset($data_arr[$fee_name2]['paid'])){
                        $paid=$data_arr[$fee_name2]['paid'];
                        $balance=$to_pay - $paid; 
                        //echo $paid; 
                    if(isset($total[$fee_name2]['paid'])){
                         $total[$fee_name2]['paid']=$paid+$total[$fee_name2]['paid'];
                        }else{
                        $total[$fee_name2]['paid']=$paid;
                        }
                        
                        
                    } 
                    @endphp
                    @if(isset($colums['paid']))
                        <td>   
                            {{$paid}}  
                        </td>
                    @endif 
                    @php
                    if(isset($data_arr[$fee_name2]['disc'])){
                        $discount=$data_arr[$fee_name2]['disc'];
                        $balance=$to_pay - ($paid + $discount); 
                        //echo $discount;
                    if(isset($total[$fee_name2]['discount'])){
                         $total[$fee_name2]['discount']=$discount+$total[$fee_name2]['discount'];
                        }else{
                        $total[$fee_name2]['discount']=$discount;
                        }
                         
                        

                    } 
                    @endphp
                    @if(isset($colums['con']))
                        <td>
                            {{$discount}}
                        </td>
                    @endif
                    <td>@php
                        if(isset($data_arr[$fee_name2]['to_pay'])){
                            $balance=$to_pay - ($paid + $discount);
                            echo $balance;
                            $ttl_due+=$balance;
                        if(isset($total[$fee_name2]['balance'])){
                         $total[$fee_name2]['balance']=$balance+$total[$fee_name2]['balance'];
                        }else{
                        $total[$fee_name2]['balance']=$balance;
                        }
                        }
                    @endphp  </td>
                   
                                @endforeach
                                <td>{{ $ttl_due }}</td>
                            </tr>
                            @php($total_due=$ttl_due+$total_due)
                    @endforeach 
                    <tr style="font-weight: 600;background: antiquewhite;
">
                        <td colspan="4">Total</td>
                        
                        @foreach($total as $key =>$val)
                           @if(isset($colums['to_pay']))
                            <td>
                                {{$val['total_pay']}}
                            </td>
                        @endif
                        @if(isset($colums['paid']))
                            <td>
                                {{$val['paid']}}
                            </td>
                        @endif
                        @if(isset($colums['con']))
                            <td>
                                {{$val['discount']}}
                            </td>
                        @endif
                        <td>{{$val['balance']}}</td>
                        @endforeach
                        <td>{{$total_due}}</td>
                    </tr>                     
                </tbody>
           
                </table>
            </div>

            <div class="row display_type" id="column_view"style="display: none;padding: 5px;">
                <div class="col-xs-12">
                    <table class="table">
                   <?php 
                        $total_pay=$total_paid=$total_disc=$total_due=0;
                        
                    ?>
                    <tr>
                        <th>Student Name</th>
                        <th>Father Name</th>
                        <th>Reg No.</th>
                        <th>Class</th>
                        <th>Sec</th>
                        <th>Mobile</th>
                        <th>Fee Head</th>
                        <th>To Pay</th>
                        <th>Paid</th>
                        <th>Concession</th>
                        <th>Due</th>
                    </tr>
                        @foreach($due_tbl as $key=>$val)
                         <!-- <tr>
                           @foreach($fee_arr as $feename)
                                    <th>Registration No. : {{ $val[$feename]['admission_no'] }}</th>
                                    <th colspan="2">Name : {{ $val[$feename]['student'] }}</th>
                                    <th colspan="2">Father's Name : {{ $val[$feename]['fatherName'] }}</th>
                                    <?php break; ?>
                            @endforeach
                        </tr>
                        <tr style="font-size: medium;font-weight: 600">
                           @foreach($fee_arr as $feename)
                                    <td>{{ $val[$feename]['admission_no'] }}</td>
                                    <td nowrap colspan="2">{{ $val[$feename]['student'] }}</td>
                                    <td nowrap colspan="2">{{ $val[$feename]['fatherName'] }}</td>
                                    <?php break; ?>
                            @endforeach
                        </tr>
                        <tr style="font-weight: 600;">
                            @foreach($fee_arr as $feename)
                                <td>Head Name</td>
                                <td>Total Fee</td>
                                <td>Total Paid</td>
                                <td>Total Discount</td>
                                <td>Total Due</td>
                                <?php break; ?>
                            @endforeach
                        </tr> -->   
                        <?php $pay=$paid=$disc=$tdue=0; ?>
                            @foreach($fee_arr as $feename2)
                            <?php
                                $temp_to_pay=(isset($val[$feename2]['to_pay'])?$val[$feename2]['to_pay']:0);
                                $temp_paid=(isset($val[$feename2]['paid'])?$val[$feename2]['paid']:0);
                                $temp_disc=(isset($val[$feename2]['disc'])?$val[$feename2]['disc']:0);
                             ?>
                             @if(isset($val[$feename2]))
                            <tr>
                                <td nowrap >  
                                    {{$val[$feename2]['student']}}
                                </td>
                                <td nowrap >
                                    {{$val[$feename2]['fatherName']}}
                                </td>
                                <td>
                                {{$val[$feename2]['admission_no']}}
                                </td>
                                <td>
                                {{$val[$feename2]['course']}}
                                </td>
                                <td>
                                    {{$val[$feename2]['sec']}}
                                </td>
                                <td>
                                    {{$val[$feename2]['mobile']}}
                                </td>
                                <td>{{$feename2}}</td>
                                <td>&#8377; 
                                    
                                    {{$val[$feename2]['to_pay']}}
                                </td>
                                <td>&#8377; 
                                    {{$val[$feename2]['paid']}}
                                </td>
                                <td>&#8377; 
                                    {{$val[$feename2]['disc']}}
                                </td>
                                <?php
                                    $due=$temp_to_pay-($temp_paid+$temp_disc);
                                    $total_pay=$total_pay+$temp_to_pay;
                                    $total_paid=$total_paid+$temp_paid;
                                    $total_disc=$total_disc+$temp_disc;
                                    $total_due=$total_due+$due;
                                    $pay=$pay+$temp_to_pay;
                                    $paid=$paid+$temp_paid;
                                    $disc=$disc+$temp_disc;
                                    $tdue=$pay-($paid+$disc);
                                 ?>
                                <td>&#8377; {{$due}}</td>
                            </tr>
                            @endif
                            @endforeach
                            
                            <tr style="font-weight: 600;font-size: 15px;background: beige">
                                <td colspan="7">Total</td>
                                <td>&#8377; {{$pay}}</td>
                                <td>&#8377; {{$paid}}</td>
                                <td>&#8377; {{$disc}}</td>
                                <td>&#8377; {{$tdue}}</td>
                                
                            </tr>

                          
                        @endforeach
                        
                        <tr style="font-weight: 600;font-size: 16px;background: bisque;">
                            <td colspan="7">Grand Total</td>
                            <td>&#8377; {{$total_pay}}</td>
                            <td>&#8377; {{$total_paid}}</td>
                            <td>&#8377; {{$total_disc}}</td>
                            <td>&#8377; {{$total_due}}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- <div class="row">
                <div class="receipt-header receipt-header-mid receipt-footer">
                    
<div class="col-xs-8 col-sm-8 col-md-8 text-left">
    <b>Receipt By: </b>
</div>
                </div>
                <div class="col-xs-4 col-sm-4 col-md-4">
                    <div class="text-right">
                        <strong>Auth Sign: </strong>----------------------
                    </div>
                </div>



                </div> -->
            </div>
            
        </div>    
    </div>
</div>




  



    @endsection
 
@section('js')
<script> // window.print(); 
 $('.display_btn').click(function(){
    $('.display_type').toggle();
    $('.display_btn').toggle();
 });
 $('#btn_print').click(function(){
    window.print();
 })
 </script>
@endsection
