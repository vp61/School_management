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
                    <div class="col-xs-12 col-sm-12 col-md-12 text-center"><h4>{{$data[0]->branch_name}}</h4>
                </div>
            </div>
            <div class="row">
                <div class="receipt-header">
                    <div class="col-xs-5 col-sm-5 col-md-5">
                        <div class="receipt-left">
                            <img class="img-responsive" alt="iamgurdeeposahan" src="{{URL::asset('images/logo/')}}/{{$data[0]->branch_logo}}" style="width: 78px; border-radius: 43px;">
                            
                        </div>
                    </div>
                  
                    <div class="col-xs-7 col-sm-7 col-md-7 text-right">
                        <div class="receipt-right">
                            <!--h2>{{$data[0]->branch_name}}</h2-->
                            <p><i class="fa fa-phone"></i> &nbsp; {{$data[0]->branch_mobile}}</p>
                            <p><i class="fa fa-envelope-o"></i> &nbsp; {{$data[0]->branch_email}}</p>
                            <p><i class="fa fa-location-arrow"></i> &nbsp; {{$data[0]->branch_address}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <hr/>
            
            <div class="row">
                <div class="receipt-header receipt-header-mid">
                    <div class="col-xs-8 col-sm-8 col-md-8 text-left">
                        <div class="receipt-right">
                            <p><b>Name :</b> {{$data[0]->first_name}}</p>
                            <p><b>Registration Number  :</b> {{$data[0]->reg_no}}</p>
                           
                            
                        
                        </div>
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4">
                        <div class="receipt-right"> 
                            <p><b>Receipt No. : </b> {{$data[0]->receipt_no}}</p>
                            <p><b>Receipt Date  :</b> {{date('d-m-Y', strtotime($data[0]->receipt_date))}}</p>
                            <p><b>Payment Mode :</b> {{$data[0]->pay_mode}}</p> 
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
                        @php $ttl_due=0;$ttl_fee=0; @endphp
                        @inject('provider', 'App\Http\Controllers\Student\StudentController')
                        
                       
                         
                        <tr>
                            <td class="col-md-6">Hostel</td>
                            <td class="col-md-2">{{$data[0]->rent}}</td>
                            <td class="col-md-2"><i class="fa fa-inr"></i> {{$data[0]->amount_paid}}</td>
                            <td class="col-md-2"> 
                                <i class="fa fa-inr"></i >
                               {{$data[0]->rent - $data['paid'][0]->total_paid}}
                            </td>
                        </tr>
                       
                        
                        <tr>
                           
                            <td class="text-right"><h2 style="font-size: 15px;"><strong>Total: </strong></h2></td>
                            <td class="text-left text-danger"><h2 style="font-size: 15px;"><strong><i class="fa fa-inr"></i >{{$data[0]->rent}} /-</strong></h2></td>
                            
                            
                            <td class="text-left text-danger"><h2 style="font-size: 15px;"><strong><i class="fa fa-inr"></i >{{$data[0]->amount_paid}} /-</strong></h2></td>
                            <td class="text-left text-danger"><h2 style="font-size: 15px;"><strong><i class="fa fa-inr"></i >{{$data[0]->rent - $data['paid'][0]->total_paid}} /-</strong></h2></td>
                        </tr>
                        <tr> 
                            <td colspan="4"><b>Reference:</b> {{$data[0]->ref_no}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="row">
                <div class="receipt-header receipt-header-mid receipt-footer">
                    <div class="col-xs-8 col-sm-8 col-md-8 text-left">
                       <b>Receipt By: </b>{{$data[0]->name}}
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
 

