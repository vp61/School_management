@extends('layouts.master')
@section('css')
<style type="text/css">
    .nopad{
        padding: 0px;
    }
</style>
@endsection
@section('content')

<!--h4 class="label label-warning arrowed-in arrowed-right arrowed" >Fee Reciept
</h4-->
<hr class="hr-8">

<div class="container" style="font-size: xx-small;">
    <div class="row" style="height: 50%">
      
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
                                <img class="img-responsive" alt="iamgurdeeposahan" src="{{URL::asset('images/logo/')}}/{{$data[0]->branch_logo}}" style="width: 78px;margin-top: -20px;">
                                
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
                
                
                <div class="row" style="margin-left: 0px;border-top: 1px dotted lightgray;padding-top: 3px;  padding-bottom: 3px;">
                    <div class="receipt-header receipt-header-mid">
                         <div class="col-xs-9 col-sm-9 col-md-9 text-left">
                            <div class="receipt-right">
                               <div class="row">
                                    <div class="col-sm-2 col-xs-2 col-md-2 nopad">
                                        <b>Name :</b>
                                    </div>
                                    <div class="col-sm-7 col-xs-7 col-md-7 nopad">
                                        {{$data[0]->first_name}}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-2 col-xs-2 col-md-2 nopad">
                                        <b>Father Name :</b>
                                    </div>
                                    <div class="col-sm-7 col-xs-7 col-md-7 nopad">
                                        {{$data[0]->father_name}}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-2 col-xs-2 col-md-2 nopad">
                                        <b>Reg No. :</b>
                                    </div>
                                    <div class="col-sm-7 col-xs-7 col-md-7 nopad">
                                        {{$data[0]->reg_no}}
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-sm-2 col-xs-2 col-md-2 nopad">
                                        <b>{{ env('course_label') }} :</b> 
                                    </div>
                                    <div class="col-sm-7 col-xs-7 col-md-7 nopad">
                                      {{$data[0]->faculty}}
                                       @if(Session::get('isCourseBatch')) 
                                        @if($data[0]->start_date)

                                            ({{ Carbon\Carbon::parse($data[0]->start_date)->format('d-M-Y')}} to {{ Carbon\Carbon::parse($data[0]->end_date)->format('d-M-Y')}})

                                        @endif   
                                      @endif   
                                      {{ !empty($subjects) ? ('( '.$subjects.' )') : "" }}
                                    </div>
                                </div>
                            
                            </div>
                        </div>    
                       
                        <!-- <div class="col-xs-5 col-sm-5 col-md-5 text-left">
                           
                        </div> -->
                        <div class="col-xs-3 col-sm-3 col-md-3">
                            <div class="receipt-right" style=""> 
                                <div class="row">
                                    <div class="col-sm-6 col-xs-6 col-md-6 nopad">
                                        <b>Receipt No. :</b>
                                    </div>
                                    <div class="col-sm-6 col-xs-6 col-md-6 nopad">
                                        {{$data[0]->reciept_no}}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6 col-xs-6 col-md-6 nopad">
                                        <b>Moble :</b>
                                    </div>
                                    <div class="col-sm-6 col-xs-6 col-md-6 nopad">
                                        {{$data[0]->mobile}}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6 col-xs-6 col-md-6 nopad">
                                        <b>Receipt Date :</b>
                                    </div>
                                    <div class="col-sm-6 col-xs-6 col-md-6 nopad">
                                        {{date('d-m-Y', strtotime($data[0]->reciept_date))}}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6 col-xs-6 col-md-6 nopad">
                                        <b>Payment Mode:</b>
                                    </div>
                                    <div class="col-sm-6 col-xs-6 col-md-6 nopad">
                                      {{$data[0]->payment_type}}
                                    </div>
                                </div>
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
                                <th>Discount</th>
                                <th nowrap>Total Due</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $ttl_due=0;$ttl_fee=0; @endphp
                            @inject('provider', 'App\Http\Controllers\Miscellaneous\MiscellaneousAssignFeeController')
                            
                            @foreach($data as $feedata)
                             
                            <tr>
                                <td class="col-md-6">
                                    {{$feedata->fee_head_title}}
                                </td>
                                <td class="col-md-2"><i class="fa fa-inr"></i> {{$feedata->fee_amount}}</td>

                                <td class="col-md-2"><i class="fa fa-inr"></i> {{$feedata->amount_paid}}</td>
                                <td class="col-md-2"><i class="fa fa-inr"></i> {{($feedata->discount!=null?$feedata->discount:0)}}</td>
                                <td class="col-md-2"> 
                                    <i class="fa fa-inr"></i >
                                    @php
                                   // print_r($feedata);
                                    $stdId      = $feedata->student_id;
                                    
                                    $feeAssignId  = $feedata->assign_fee_id;
                                    $session    = $feedata->session_name;
                                    $dueData = $provider::getStudentFeeHeadDueAmout($stdId,$feeAssignId,$session); 
                                   
                                    $totalAmtRequired   = $dueData[0]->NeedToPay;
                                    $totalPaidAmt       = $dueData[0]->totalPaid;
                                    
                                     $totalDueNow        = $totalAmtRequired - ($totalPaidAmt + $dueData[0]->totalDiscount);
                                     echo $totalDueNow;
                                    $ttl_due = $ttl_due+$totalDueNow;
                                    $ttl_fee = $ttl_fee+$totalAmtRequired;
                                    @endphp
                                
                                </td>
                            </tr>
                            @endforeach
                            
                            <?php /* <tr>
                                <td class="text-right"><strong>Total: </strong></td>
                                <td class="text-left text-danger"><strong><i class="fa fa-inr"></i > {{$ttl_fee}} /-</strong></td>
                                
                                <td class="text-left text-danger"><strong><i class="fa fa-inr"></i > {{$data->sum('amount_paid')}} /-</strong></td>
                                <td class="text-left text-danger"><strong><i class="fa fa-inr"></i > {{$data->sum('discount')}} /-</strong></td>
                                
                                <td class="text-left text-danger"><strong><i class="fa fa-inr"></i > {{$ttl_due}} /-</strong></td>
                            </tr> */ ?>
                            @php 
                                    $total_due = 0;
                                    $total_fee = 0;
                                @endphp
                            @if(count($otherDues)>0)
                               
                                @foreach($otherDues as $k =>$v)
                                    <tr>
                                        @php 
                                            $due = $v->fee_amount - ($v->total_paid + $v->total_discount );
                                            $total_due = $total_due + $due;
                                            $total_fee = $total_fee + $v->fee_amount;
                                        @endphp
                                        <td class="col-md-6">
                                            {{$v->fee_head_title}}
                                        </td>
                                        <td class="col-md-2"><i class="fa fa-inr"></i> {{$v->fee_amount}}</td>

                                        <td class="col-md-2"> - </td>
                                        <td class="col-md-2"> - </td>
                                        <td class="col-md-2"> 
                                            <i class="fa fa-inr"></i > {{ $due }}
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            <tr>
                                <td class="text-right"><strong>Total: </strong></td>
                                <td class="text-left text-danger" nowrap><strong><i class="fa fa-inr"></i > {{$ttl_fee + $total_fee}} /-</strong></td>
                                
                                <td class="text-left text-danger" nowrap><strong><i class="fa fa-inr"></i > {{$data->sum('amount_paid')}} /-</strong></td>
                                <td class="text-left text-danger" nowrap><strong><i class="fa fa-inr"></i > {{$data->sum('discount')}} /-</strong></td>
                                
                                <td class="text-left text-danger" nowrap><strong><i class="fa fa-inr"></i > {{$ttl_due + $total_due}} /-</strong></td>
                            </tr>
                            <tr> 
                                <td colspan="5"><b>Reference:</b> {{$feedata->reference}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <br>
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        {!! isset($generalSetting->receipt_footer)?$generalSetting->receipt_footer:'' !!}
                    </div>
                </div>
                <div class="row">
                    <div class="receipt-header receipt-header-mid receipt-footer">
                        <div class="col-xs-8 col-sm-8 col-md-8 text-left">
                           <b>Receipt By: </b>{{$feedata->name}}
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
    <hr>
</div>


 <script>

  window.print();

</script>

    @endsection
 

