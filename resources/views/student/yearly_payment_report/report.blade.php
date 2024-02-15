@extends('layouts.master')

@section('css')
<style>
    td{
        font-size: 10px !important;
    }
</style>
@endsection


@section('content')

    <div class="main-content">
        <div class="main-content-inner">
            <div class="receipt-main col-xs-10 col-sm-10 col-md-10 col-xs-offset-1 col-sm-offset-1 col-md-offset-1">
                    <div class="row">
                        <div class="receipt-header">
                            <div class="col-xs-12 col-sm-12 col-md-12 text-center"><h2>{{$branch['branch_name']}}</h2>
                        </div>
                        </div>
                    
                    <div class="row">
                        <div class="receipt-header">
                            <div class="col-xs-3 col-sm-3 col-md-3">
                                <div class="receipt-left">
                                    <img class="img-responsive" alt="iamgurdeeposahan" src="{{ asset('images/logo/')}}/{{$branch['branch_logo']}}" style="width: 78px;">
                                </div>
                            </div>
                            <div class="col-xs-4 col-sm-4 col-md-4 text-right">
                                 
                            </div>
                            <div class="col-xs-5 col-sm-5 col-md-5 text-right">
                                <div class="receipt-right">
                                    <p><i class="fa fa-phone"></i> &nbsp; {{$branch['branch_mobile']}}</p>
                                    <p><i class="fa fa-envelope-o"></i> &nbsp; {{$branch['branch_email']}}</p>
                                    <p><i class="fa fa-location-arrow"></i> &nbsp; {{$branch['branch_address']}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                   
                    <div class="row" style="margin-left: 0px;border-top: 1px dotted lightgray;padding-top: 3px;  padding-bottom: 3px;">
                    <div class="receipt-header receipt-header-mid">
                         <div class="col-xs-6 col-sm-6 col-md-6 text-left">
                            <div class="receipt-right">
                               <div class="row">
                                    <div class="col-sm-5 col-xs-5 col-md-5 nopad">
                                        <b>Student's Name :</b>
                                    </div>
                                    <div class="col-sm-7 col-xs-7 col-md-7 nopad text-left">
                                        {{$data['student']->first_name}}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-5 col-xs-5 col-md-5 nopad">
                                        <b>Father's Name :</b>
                                    </div>
                                    <div class="col-sm-7 col-xs-7 col-md-7 nopad text-left">
                                        {{$data['student']->father_first_name}}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-5 col-xs-5 col-md-5 nopad">
                                        <b>Reg No. :</b>
                                    </div>
                                    <div class="col-sm-7 col-xs-7 col-md-7 nopad text-left">
                                        {{$data['student']->reg_no}}
                                    </div>
                                </div>
                            </div>
                        </div>    
                       
                        <!-- <div class="col-xs-5 col-sm-5 col-md-5 text-left">
                           
                        </div> -->
                        <div class="col-xs-6 col-sm-6 col-md-6 text-right">
                            <div class="receipt-right" style=""> 
                                <div class="row">
                                    <div class="col-sm-6 col-xs-6 col-md-6 nopad">
                                        <b>{{env('course_label')}} :</b>
                                    </div>
                                    <div class="col-sm-6 col-xs-6 col-md-6 nopad text-left">
                                        {{$data['student']->faculty}}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6 col-xs-6 col-md-6 nopad">
                                        <b>Mobile :</b>
                                    </div>
                                    <div class="col-sm-6 col-xs-6 col-md-6 nopad text-left">
                                        {{$data['student']->mobile_1}}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6 col-xs-6 col-md-6 nopad">
                                        <b>Session :</b>
                                    </div>
                                    <div class="col-sm-6 col-xs-6 col-md-6 nopad text-left">
                                        {{$session_name}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                    <div>
                        <table class="table table-bordered">
                                  
                            <tr>
                                <th>Head</th>
                                <th>Receipt No</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Discount</th>  
                            </tr>
                            <tbody>
                                @if(count($data['collect'])>0)
                                    @php   $i=1;$collect=$disc=0;
                                    
                                    @endphp                                        
                                    @foreach($data['collect'] as $key => $value)
                                        <tr>
                                            <td style="font-size:10px;"><b>{{$value->fee_head_title}}</b></td>
                                            <td style="font-size:10px;">{{$value->reciept_no}} </td>
                                            <td style="font-size:10px;"> {{\Carbon\Carbon::parse($value->reciept_date)->format('d-M-Y')}}</td>
                                            <td style="font-size:10px;"> {{$value->amount_paid}}</td>  
                                            <td style="font-size:10px;">{{$value->discount}}</td>  
                                        </tr>
                                        <?php
                                            $collect +=$value->amount_paid;
                                            $disc +=$value->discount;
                                                
                                        ?>
                                    @endforeach
                                     <tr class="bg-danger strong">
                                        <td ><b>Total</b></td>
                                        <td ><b></b></td>
                                        <td ></td>
                                        <td>&#8377;{{$collect}} </td>
                                        <td>&#8377;{{$disc}} </td> 
                                    </tr>
                                @else
                                        <tr>
                                            <td colspan="5">No Data</td>
                                        </tr>
                                @endif
                            </tbody>
                        </table>           
                    </div>
                    
                    
                    <div class="row">
                    <div class="receipt-header receipt-header-mid receipt-footer">
                        <div class="col-xs-8 col-sm-8 col-md-8 text-left">
                           
                        </div>
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4">
                        <div class="receipt-left">
                            <br>
                            <br>
                            <strong>Auth Sign: </strong>----------------------
                        </div>
                    </div>
                </div>
                    <script> window.print(); </script>
                </div>
            </div>
        </div>
    </div>
   
@endsection

