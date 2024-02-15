@extends('layouts.master')
@section('content')

<?php
    if(isset($payment) && !empty($payment)){

    }
    else{

    }
?>
<div class="container">
    <div class="row">
        <div class="well col-xs-10 col-sm-10 col-md-6 col-xs-offset-1 col-sm-offset-1 col-md-offset-3">
            <div class="row">
                <div class="col-xs-6 col-sm-6 col-md-6">
                    <address>
                        <strong>{{$payment->firstname}}</strong>
                        <br>
                      {{$payment->email}}
                       
                        <br>
                        <strong title="Phone">Phone :  </strong>   {{$payment->phone}}
                       
                    </address>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6 text-right">
                   
                    <p>
                        <em>Transaction ID #: {{$payment->txnid}}</em>
                    </p>
                </div>
              
            </div>

            <?php
                    $cssIn = ($temStatus !='Tampered / Failure' && $payment->status !='Failed')? "alert alert-success" : "alert alert-danger";
            ?>
            <div class="row">
                @if(!empty($payment->status=='Failed'))
                <div class="text-center">
                    <h1>Receipt</h1>

                    <!--img src="{{url('assets/images/paymenticon/transaction.jpg')}}" alt="Failed " height="82" width="102" -->
                    
                    <div class="{{$cssIn}}" role="alert">
                        <h5>Payment Status: {{$temStatus}}
                            <?php /*    {{$payment->status}} */ ?>
                        </h5>
                    </div>

                </div>
                @endif
                @if(!empty($payment->status=='Completed'))
                <div class="text-center">
                    <!--img src="{{url('assets/images/paymenticon/success.png')}}" alt="Failed " height="152" width="172">
                    <h3>Payment {{$payment->status}}</h3-->
                    <h1>Receipt</h1>

                    <div class="{{$cssIn}}" role="alert">
                        <h5>Payment Status: {{$temStatus}}
                            <?php /*    {{$payment->status}} */ ?>
                        </h5>
                    </div>
                </div>
                @endif
                </span>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Fees</th>
                            <th>#</th>
                            <th class="text-center">Price</th>
                            <th class="text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="col-md-9"><em>{{$feeheads[0]->feehead}}</em></h4></td>
                            <td class="col-md-1" style="text-align: center"> </td>
                            <td class="col-md-1 text-center"></td>
                            <td class="col-md-1 text-center">{{$payment->amount}}</td>
                        </tr>
                        
                        <tr>
                            <td>   </td>
                            <td>   </td>
                            <td class="text-right"><h4><strong>Total: </strong></h4></td>
                            <td class="text-center text-danger"><h4><strong>{{$payment->amount}}</strong></h4></td>
                        </tr>
                    </tbody>
                </table>
                
                <button   type="button" class="btn btn-success btn-lg btn-block"><a href="{{ route('user-student') }}"> GO BACK <span class="glyphicon glyphicon-chevron-left"></span></a>
                    
                </button>
              
            </td>
            </div>
        </div>
    </div>


    @endsection
 