@extends('layouts.master')
@section('content')
@php
    $data=array();
    foreach($printed_data['branch'][0] as $key=>$val){
        $data[$key]=$val; 
    } 

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
                    <div class="col-xs-12 col-sm-12 col-md-12" style="padding-bottom: 3px;text-align: center;">
                        <h4 text-align="center">Student Due Report</h4>
                        {{-- - {{$info['courseName']}} --}}
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
           
            <div>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th>Father's Name</th>
                            <th>Reg No</th>
                            <th>Fees</th>
                            <th>Paid</th>
                            <th>Discount</th>
                            <th>Due</th>
                        </tr>
                    </thead>
                    <tbody>
                      @if(count($collect)>0)  
                        @php $i=1; $ttl=$paid=$due=$discount=0;
                         @endphp 
                        @foreach($collect as $key => $value)
                            @foreach($stdata as $k => $val)
                                @if($key==$k)
                                     <tr>
                                        <td>{{$i++}}</td>
                                        <td>{{$val[0]->first_name}}</td>
                                        <td>{{$val[0]->father_first_name}}</td>
                                        <td>{{$val[0]->reg_no}}</td>
                                        <td>&#8377; {{$collect[$key]['total']}}</td>    
                                        <td>&#8377; {{$collect[$key]['paid']}}</td>
                                        <td>&#8377; {{$collect[$key]['discount']}}</td>
                                        <td>&#8377; {{$collect[$key]['due']}}</td>
                                    </tr>
                                    @php 
                                        $ttl=$ttl+$collect[$key]['total'];
                                        $paid=$paid+$collect[$key]['paid'];
                                        $discount=$discount+$collect[$key]['discount'];
                                        $due=$collect[$key]['due']+$due;
                                    @endphp
                                @endif
                            @endforeach
                        @endforeach
                        <tr>
                            <td colspan="4"><b>Total</b></td>
                            <td>&#8377; {{$ttl}}</td>
                            <td>&#8377; {{$paid}}</td>
                            <td>&#8377; {{$discount}}</td>
                            <td>&#8377; {{$due}}</td>
                        </tr>
                      @else
                            <tr>
                                <td colspan="7">No Data</td>
                            </tr>
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
 

