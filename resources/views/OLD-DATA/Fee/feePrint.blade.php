@extends('layouts.master')
@section('content')
@php
    $data=array();
    foreach($printed_data['branch'][0] as $key=>$val){
        $data[$key]=$val; 
    } //dd($due_tbl);

    foreach($printed_data as $key=>$val){ $data[$key]=$val; }
    if($due_tbl){
        foreach($due_tbl as $key=>$val){ $data['tbl'][$key]=$val; }
    }else{ $data['tbl']=""; }
    $topay_arr=$paid_arr=$due_arr=$ttl=array();
@endphp
<hr class="hr-8">
<div class="container">
    <div class="row">
        <div class="receipt-main col-xs-10 col-sm-10 col-md-10 col-xs-offset-1 col-sm-offset-1 col-md-offset-1">
            <div class="row">
                <div class="receipt-header">
                    <div class="col-xs-12 col-sm-12 col-md-12 text-center"><h4>{{$data['branch_name']}}</h4>
                </div>
            </div>
            
            <div class="row">
                <div class="receipt-header">
                    <div class="col-xs-5 col-sm-5 col-md-5">
                        <div class="receipt-left">
                            <img class="img-responsive" alt="iamgurdeeposahan" src="{{ asset('images/logo/')}}/{{$data['branch_logo']}}" style="width: 78px; border-radius: 43px;">
                        </div>
                    </div>
                    <div class="col-xs-2 col-sm-2 col-md-2">
                         
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
                    <div class="col-xs-12 col-sm-12 col-md-12" style="padding-bottom: 3px;text-align: center;">
                        <h4 text-align="center">Student Fee Report - {{$info['courseName']}}</h4>
                    </div> 
                </div>
            </div>
            <div>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Reg No</th>
                            <th>Student</th>
                            <th>Father's Name</th>
                            <th>Fee-Type(Fees)</th>
                            <th>Reciept No.</th>
                            <th>Paid</th>
                            <th>Receipt Date</th>
                            <th>Receipt by</th>
                            <th>Pay Mode</th>
                        </tr>
                    </thead>
                    <tbody> @php $i = 1;  @endphp
                @if(isset($data['tbl']) && $data['tbl']!="")
                      @foreach($data['tbl'] as $stdnt_id=>$tble_val)
                        @if(count($tble_val) > 1)
                            @foreach($tble_val as $tbl_val)
<tr>
   
@php 

$fee_name=$tbl_val['fee_head_title'];  //dd($tbl_val);
$topay_arr[$stdnt_id][$fee_name] = $tbl_val['fee_amount'] + $tbl_val['fine'];
$paid_arr[$stdnt_id][$fee_name][]=$tbl_val['amount_paid'] + $tbl_val['discount'];
$paid=array_sum($paid_arr[$stdnt_id][$fee_name]);

$due=$topay_arr[$stdnt_id][$fee_name] - $paid;
$due_arr[]=$due; //dd($paid);

$ttl['fee_amount'][$stdnt_id][$fee_name]=$tbl_val['fee_amount'];
$ttl['fine'][]=$tbl_val['fine'];
$ttl['amount_paid'][]=$tbl_val['amount_paid'];
$ttl['discount'][]=$tbl_val['discount'];
@endphp
<td>{{ $i }}</td> 
<td>{{$tbl_val['reg_no']}}</td>
<td>{{ $student_tbl[$stdnt_id] }}</td>
<td>{{$tbl_val['father_first_name']}}</td>
<td>{{ $tbl_val['fee_head_title'] }}  ({{ $tbl_val['fee_amount'] }})</td>
<td>{{ $tbl_val['reciept_no'] }}</td>

<td><i class="fa fa-rupee"></i> {{ $tbl_val['amount_paid'] }}</td>
<td nowrap><i></i>{{date('d-m-Y', strtotime($tbl_val['reciept_date'])) }} </td>
<td>  {{ ucwords($tbl_val['name']) }} </td>
<td>{{$tbl_val['payment_type']}}</td>
                    </tr>
                    
                    @php $i++; @endphp
                      @endforeach
                      @else
                      <tr>
@php 

$tbl_val = $tble_val[0]; //dd($tbl_val);

$fee_name=$tbl_val['fee_head_title'];
$topay_arr[$stdnt_id][$fee_name] = $tbl_val['fee_amount'] + $tbl_val['fine'];
$paid_arr[$stdnt_id][$fee_name][]=$tbl_val['amount_paid'] + $tbl_val['discount'];


$paid=array_sum($paid_arr[$stdnt_id][$fee_name]);
$due=$topay_arr[$stdnt_id][$fee_name] - $paid;
$due_arr[]=$due; //dd($paid);


$ttl['fee_amount'][$stdnt_id][$fee_name]=$tbl_val['fee_amount'];
$ttl['fine'][]=$tbl_val['fine'];
$ttl['amount_paid'][]=$tbl_val['amount_paid'];
$ttl['discount'][]=$tbl_val['discount'];
@endphp

<td>{{ $i }}</td> 
<td>{{$tbl_val['reg_no']}}</td>
<td>{{ $student_tbl[$stdnt_id] }}</td>
<td>{{$tbl_val['father_first_name']}}</td>

<td>{{ $tbl_val['fee_head_title'] }} ({{ $tbl_val['fee_amount'] }})</td>
<td>{{ $tbl_val['reciept_no'] }}</td>
<td><i class="fa fa-rupee"></i> {{ $tbl_val['amount_paid'] }}</td>
<td nowrap>{{date('d-m-Y', strtotime($tbl_val['reciept_date'])) }}  </td>
<td>{{ ucwords($tbl_val['name']) }}</td>
<td>{{$tbl_val['payment_type']}}</td>

                    </tr>

                    @endif
                    @php $i++; @endphp
                    @endforeach

  
                
                    @endif
                    </tbody>
                </table>
            </div>
            </div>
            
        </div>    
    </div>
</div>




  

 <script> window.print(); </script>

    @endsection
 

