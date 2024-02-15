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
        <div class="receipt-main col-xs-12 col-sm-12 col-md-12   ">
            <div class="row">
                <div class="receipt-header">
                    <div class="col-xs-12 col-sm-12 col-md-12 text-center"><h4>{{$data['branch_name']}}</h4>
                    </div>
                </div>    
            </div>
            
            <div class="row">
                <div class="receipt-header">
                    <div class="col-xs-5 col-sm-5 col-md-5">
                        <div class="receipt-left">
                            <img class="img-responsive" alt="iamgurdeeposahan" src="{{ asset('images/logo/')}}/{{$data['branch_logo']}}" style="width: 78px;">
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
                        <h4 text-align="center">Student Fee Report</h4>
                        {{--   - {{$info['courseName']}} --}}
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
            <div class="row">
                <div class="receipt-header receipt-header-mid">
                     <div class="col-xs-12 col-sm-12 col-md-12">
                        <table class="table table-bordered" style="font-size: 96%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Reg No</th>
                                    <th nowrap>Student</th>
                                    <th>{{env('course_label')}}</th>
                                    <th nowrap>Father's Name</th>
                                    <th>Fee-Type(Fees)</th>
                                    <th>Reciept No.</th>
                                    <th>Paid</th>
                                    <th nowrap>Receipt Date</th>
                                    <th nowrap>Receipt by</th>
                                    <th nowrap>Pay Mode</th>
                                </tr>
                            </thead>
                            <tbody> @php $gt=0;  $total_paid=0; @endphp
                                @if(count($due_tbl)>0)
                                    @foreach($due_tbl as $key=>$val)
                                    @php $total=0;$i = 1; @endphp
                                        @foreach($val as $k=>$v)
                                            <tr>
                                                <td>{{ $i }}</td> 
                                                <td>{{$v->reg_no}}</td>
                                                <td nowrap>{{ $v->student_name }}</td>
                                                <td nowrap>{{$v->faculty}}</td>
                                                <td nowrap>{{$v->father_first_name}}</td>
                                                <td nowrap>{{$v->fee_head_title}} ({{ $v->fee_amount }})</td>
                                                <td>{{ $v->reciept_no }}</td>
                                                <td nowrap><i class="fa fa-rupee"></i> {{ $v->amount_paid }}</td>
                                                <td nowrap><i></i>{{date('d-m-Y', strtotime($v->reciept_date)) }} </td>
                                                <td>  {{ ucwords($v->name) }} </td>
                                                <td nowrap>{{$v->payment_type}}</td>
                                            </tr>
                                            @php 
                                                $i++;
                                                $total=$total+$v->amount_paid;
                                            @endphp
                                        @endforeach
                                        <tr style="font-weight: 600;background: beige;">
                                            <td colspan="7">Total {{$v->payment_type}} Amount</td>
                                            <td nowrap>&#8377; {{$total}}</td>
                                            <td colspan="3"></td>
                                        </tr>
                                        @php 
                                            
                                            $gt=$gt+$total;
                                        @endphp
                                    @endforeach
                                    <tr style="font-weight: 700;background: antiquewhite;font-size: larger">
                                        <td colspan="7">Grand Total</td>
                                        <td nowrap>&#8377; {{$gt}}</td>
                                        <td colspan="3"></td>
                                    </tr>
                                @endif
                       
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
           
            
        </div>    
    </div>
</div>




  

 <script> window.print(); </script>

    @endsection
 

