@extends('layouts.master')
@section('content')

<!--h4 class="label label-warning arrowed-in arrowed-right arrowed" >Fee Reciept
</h4-->
<hr class="hr-8">

<div class="container">
    <div class="row">
     
        <div class="receipt-main col-xs-10 col-sm-10 col-md-6 col-xs-offset-1 col-sm-offset-1 col-md-offset-3">
            <div class="row">
                <div class="receipt-header">
                    <div class="col-xs-12 col-sm-12 col-md-12 text-center"><h4>{{$receipt_data['branch_name']}}</h4>
                </div>
            </div>
            <div class="row">
                <div class="receipt-header">
                    <div class="col-xs-5 col-sm-5 col-md-5">
                        <div class="receipt-left">
                            <img class="img-responsive" alt="iamgurdeeposahan" src="{{URL::asset('images/logo/')}}/{{$receipt_data['branch_logo']}}" style="width: 78px; border-radius: 43px;">
                            
                        </div>
                    </div>
                  
                    <div class="col-xs-7 col-sm-7 col-md-7 text-right">
                        <div class="receipt-right">
                          
                            <p><i class="fa fa-phone"></i> &nbsp; {{$receipt_data['branch_mobile']}}</p>
                            <p><i class="fa fa-envelope-o"></i> &nbsp; {{$receipt_data['branch_email']}}</p>
                            <p><i class="fa fa-location-arrow"></i> &nbsp; {{$receipt_data['branch_address']}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <hr/>
            
            <div class="row">
                <div class="receipt-header receipt-header-mid">
                    <div class="col-xs-8 col-sm-8 col-md-8 text-left">
                        <div class="receipt-right">
                            <p><b>Name :</b> {{$receipt_data['first_name']}}</p>
                            <p><b>Registration Number  :</b> {{$receipt_data['reg_no']}}</p>
                           
                            
                        
                        </div>
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4">
                        <div class="receipt-right"> 
                            <p><b>Receipt No. : </b> {{$receipt_data['receipt_no']}}</p>
                            <p><b>Receipt Date  :</b> {{carbon\carbon::parse($receipt_data['receipt_date'])->format('d-m-Y')}}</p>
                            <p><b>Payment Mode :</b> {{$receipt_data['pay_mode']}}</p> 
                        </div>
                    </div>
                </div>
            </div>
            
            <div>
                 <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Fee-Head</th>
                            <th>Total Fee</th>
                            <th>Paid Amount</th>
                            <th>Total Due</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $ttl_due=0;$ttl_fee=0;$ttl_paid=0; @endphp
                        @inject('provider', 'App\Http\Controllers\Student\StudentController')
                        
                       
                        @if(count($data)>0)
                           @foreach($data as $collection=>$coll_data)
                            <tr>
                                <td class="col-md-6">Transport ( {{carbon\carbon::parse($coll_data->from_date)->format('d-m-Y')}} - {{carbon\carbon::parse($coll_data->to_date)->format('d-m-Y')}} )</td>
                                <td class="col-md-2">{{$coll_data->total_rent}}</td>
                                <td class="col-md-2"><i class="fa fa-inr"></i> {{$coll_data->amount_paid}}</td>
                                <td class="col-md-2"> 
                                    <i class="fa fa-inr"></i >
                                   {{$coll_data->total_rent - $coll_data->total_paid}}
                                </td>
                           </tr>
                       
                            @php
                            $ttl_fee+= $coll_data->total_rent;
                            $ttl_paid+= $coll_data->total_paid;
                            $ttl_due+=$coll_data->total_rent-$coll_data->total_paid;
                            @endphp


                           @endforeach
                            <tr>
                           
                                <td class="text-left"><h2 style="font-size: 15px;"><strong>Total: </strong></h2></td>
                                <td class="text-left text-danger"><h2 style="font-size: 15px;"><strong><i class="fa fa-inr"></i >{{$ttl_fee}} /-</strong></h2></td>
                                
                                
                                <td class="text-left text-danger"><h2 style="font-size: 15px;"><strong><i class="fa fa-inr"></i >{{$ttl_paid}} /-</strong></h2></td>
                                <td class="text-left text-danger"><h2 style="font-size: 15px;"><strong><i class="fa fa-inr"></i >{{$ttl_due}} /-</strong></h2></td>
                            </tr>
                            <tr> 
                                <td colspan="4"><b>Reference:</b> {{$receipt_data['ref_no']}}</td>
                            </tr>

                        @endif
                       
                        
                        
                       
                    </tbody>
                </table>
            </div>
            
            <div class="row">
                <div class="receipt-header receipt-header-mid receipt-footer">
                    <div class="col-xs-8 col-sm-8 col-md-8 text-left">
                       <b>Receipt By: </b>{{$receipt_data['name']}}
                    </div>

                    
                </div>
                <div class="col-xs-4 col-sm-4 col-md-4">
                    <div class="receipt-left">
                        <strong>Auth Sign: </strong>----------------------
                    </div>
                </div>


                </div>
            </div>
            
        </div>    
    </div>
</div>


 <script>

  window.print();

</script>

    @endsection
 

