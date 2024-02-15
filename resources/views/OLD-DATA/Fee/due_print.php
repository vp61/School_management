@extends('layouts.master')
@section('content')
@php
    $data=array();
    foreach($transaction[0] as $key=>$val){ $data[$key]=$val; }
@endphp
<hr class="hr-8">

<div class="container">
    <div class="row">
      
        <div class="receipt-main col-xs-10 col-sm-10 col-md-6 col-xs-offset-1 col-sm-offset-1 col-md-offset-3">
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
                  
                    <div class="col-xs-7 col-sm-7 col-md-7 text-right">
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
                    <div class="col-xs-8 col-sm-8 col-md-8 text-left">
                        <div class="receipt-right">
                            <p><b>Reciept No. :</b> AES{{$data['id']}}</p>
                        </div>
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4">
                        <div class="receipt-right"> 
                            <p><b>Receipt Date  :</b> {{date('d-m-Y', strtotime($data['date']))}}</p>
                        </div>
                    </div>
                </div>
            </div>
           
            <div>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Head</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                       <tr>
            <td>{{$data['tr_head']}}</td>
            @php if($data['cr_amount']){ @endphp
            <td>Credit</td>
            <td><i class="fa fa-inr"></i> {{$data['cr_amount']}}</td>
            @php }else{ @endphp
            <td>Debit</td>
            <td><i class="fa fa-inr"></i > {{$data['dr_amount']}}</td>
            @php } @endphp
            <td>{{$data['description']}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="row">
                <div class="receipt-header receipt-header-mid receipt-footer">
                    <div class="col-xs-8 col-sm-8 col-md-8 text-left">
                       <b>Receipt By: </b> {{$data['name']}}
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


 <script> window.print(); </script>

    @endsection
 

