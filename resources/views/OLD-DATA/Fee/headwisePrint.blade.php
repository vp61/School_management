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
      


        <div class="receipt-main col-xs-12 col-sm-12 col-md-12 col-xs-offset-1 col-sm-offset-1 col-md-offset-1" style="margin-left: -10px !important;">
            <div class="row">
                <div class="receipt-header">
                    <div class="col-xs-12 col-sm-12 col-md-12 text-center"><h4>{{$data['branch_name']}}</h4>
                </div>
            </div>
            
            <div class="row">
                <div class="receipt-header">
                    <div class="col-xs-3 col-sm-3 col-md-3">
                        <div class="receipt-left">
                            <img class="img-responsive" alt="iamgurdeeposahan" src="{{ asset('images/logo/')}}/{{$data['branch_logo']}}" style="width: 78px; border-radius: 43px;">
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
                    <div class="col-xs-4 col-sm-4 col-md-4" style="padding-bottom: 3px;text-align: center;">
                        <h4 text-align="center">Due Report Head Wise- {{$info['courseName']}}</h4>
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4" style="padding-bottom: 3px;">
                        <div class="receipt-right"> 
                            <button class="btn btn-info" type="submit" id="filter-btn" onclick="Export('<?php echo "fileName"; ?>')" style="float: right;" >
                            <i class="fa fa-download bigger-110"></i>
                            Excel Export
                            </button>
                        </div>
                    </div>
                </div>
            </div>
           
            <div>
                <table class="table table-bordered" id="tblFeeHeadWiseExl">
                    <thead>
                        <tr style="font-size: 8px !important;">
                            <th>#</th>
                            <th>Reg No</th>
                            <th>Students</th>
                            <th>Father Name</th>
                            @foreach($fee_arr as $fee_name)
                                <th>{{ $fee_name }}</th>
                                <th>{{ $fee_name }} Deposited</th>
                                <th>{{ $fee_name }} Balance</th>
                            @endforeach
                            <th>Total Due</th>
                        </tr>
                    </thead>
                    <tbody> 
                    @php $i = 1;  @endphp
                    @foreach($due_tbl as $student_id=>$data_arr) 
                    @php $ttl_due=0; //dd($fee_arr); @endphp
                       <tr style="font-size: 10px;">     <td>{{ $i++ }}</td>
                                @foreach($fee_arr as $feename)
                                    
                                    <td>{{ $data_arr[$feename]['admission_no'] }}</td>
                                    <td>{{ $data_arr[$feename]['student'] }}</td>
                                    <td>{{ $data_arr[$feename]['fatherName'] }}</td>
                                    @php break; @endphp
                                
                                @endforeach
                                @foreach($fee_arr as $fee_name2)
                    <td>@php if(isset($data_arr[$fee_name2]['to_pay'])){
                       $to_pay = $data_arr[$fee_name2]['to_pay']; echo $to_pay;
                    } @endphp</td>
                    <td>@php
                    if(isset($data_arr[$fee_name2]['paid'])){
                        $paid=array_sum($data_arr[$fee_name2]['paid']);
                        $balance=$to_pay - $paid; $ttl_due+=$balance;
                        echo $paid; 
                    } 
                    @endphp</td>
                    <td>{{ $balance }}</td>
                                @endforeach
                                <td>{{ $ttl_due }}</td>
                            </tr>
                       
                    @endforeach                      
                </tbody>
           
                </table>
            </div>
            
            <div class="row">
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



                </div>
            </div>
            
        </div>    
    </div>
</div>




  

 <script> // window.print(); 
 </script>

    @endsection
 

