<div class="row">
    <div class="col-xs-12">
        <h4 class="header large lighter blue"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;Fees List</h4>
        <div class="clearfix">
            <a class="label label-primary label-lg white" href="{{ route('print-out.fees.student-ledger', ['id' => $data['student']->id]) }}" target="_blank">
                Ledger
                <i class="ace-icon fa fa-print  align-top bigger-125 icon-on-right"></i>
            </a>
            <a class="label label-warning label-lg white" href="{{ route('print-out.fees.student-due-detail', ['id' => $data['student']->id]) }}" target="_blank">
                Due Detail Slip
                <i class="ace-icon fa fa-print  align-top bigger-125 icon-on-right"></i>
            </a>

            <a class="label label-warning label-lg white" href="{{ route('print-out.fees.student-due', ['id' => $data['student']->id]) }}" target="_blank">
                Total Due
                <i class="ace-icon fa fa-print  align-top bigger-125 icon-on-right"></i>
            </a>
            <a class="label label-success label-lg white" href="{{ route('print-out.fees.today-receipt-detail', ['id' => $data['student']->id]) }}" target="_blank">
                Today Receipt Detail
                <i class="ace-icon fa fa-print  align-top bigger-125 icon-on-right"></i>
            </a>
            <a class="label label-success label-lg white" href="{{ route('print-out.fees.today-receipt', ['id' => $data['student']->id]) }}" target="_blank">
                Receipt
                <i class="ace-icon fa fa-print  align-top bigger-125 icon-on-right"></i>
            </a>

            <span class="hidden-print">
                <a class="btn-primary btn-sm" href="{{ route('account.fees.collection.view', ['id' => $data['student']->id]) }}">
                     <i class="fa fa-calculator" aria-hidden="true"></i> View Ledger
                 </a>
            </span>
           <!--  //payment term -->

          

            <div class="hr hr-4 hr-dotted"></div>
            <div class="row text-uppercase">
                <div class="col-sm-5 pull-right align-right">
                    {{--<strong>Total Due :</strong>{{$data['student']->balance}}/---}}
                    <label class="label label-info label-lg white">Total Due : {{ number_format($data['student']->balance, 2) }}/-</label>
                </div>
                <div class="col-sm-7 pull-left">

                   
                </div>
            </div>
            <div class="hr hr-8 hr-dotted"></div>
             <div class="col-sm-1 pull-right align-right" >
              <strong style="margin: 0px 77px -22px -79px;"> Partial Payment : </strong>
              <label class="switch" style="margin: 0px 23px;">
                  <input type="checkbox">
                  <span class="slider round"></span>
                </label>

          </div>
        </div>


        <!-- div.table-responsive -->
        <div class="table-responsive">
           <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                    <th>S.N.</th>
                    <th>Fee-Head</th>
                    <th>Fees</th>
                    <th>Paid</th>
                    <th>Due</th>
                    <th>Discount</th>
                    <th>Status</th>
                    <th>pay Online</th>
                  
                </tr>
                </thead>
                <tbody>
                    @if (isset($fee_result) && $fee_result->count() > 0)
                        @php($i=0)
                        @foreach($fee_result as $feemaster)
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td>{{ $feemaster->fee_head_title}}</td>
                                <td>{{$feemaster->fee_amount}}</td>
                                <td>{{ $paid_result_arr[$i]}}</td>
                                <td>{{ $due[$i]}}</td>
                                
                                <td>{{ $disc_result_arr[$i]}}</td> 

                                  @php($net_balance = ($feemaster->fee_amount - ($paid_result_arr[$i])
                                        + $disc_result_arr[$i]))
                                <td align="left" class="text text-left"> 
                                    @if($due[$i] == 0)
                                        <span class="label label-success">Paid</span>
                                    @elseif($due[$i] < 0 )
                                        <span class="label label-warning">Negative / Advance</span>
                                    @elseif($due[$i] < $feemaster->fee_amount)
                                        <span class="label label-info">Partial</span>
                                    @else
                                        <span class="label label-danger">Due</span>
                                    @endif
                                </td>

                                <td>
                                 @if($due[$i] == 0)
                                
                                       <!--  <a class="btn btn-xs btn-primary" href="{{ route('feeReceipt', ['id' => $feemaster->id]) }}" target="_blank">print
                                    <i class="fa fa-print"></i>
                                    </a> -->
                                @elseif($due[$i] < 0 )


                                <!--  <a class="btn btn-xs btn-primary" href="{{ route('feeReceipt', ['id' => $feemaster->id]) }}" target="_blank">print
                                    <i class="fa fa-print"></i>
                                    </a> -->

                                 @else

                                  <hr class="hr-2">
                                    <form action="{{route('fees.payu-form')}}" method="POST">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="student_id" value="{{ $data['student']->id }}">
                                        <input type="hidden" name="fee_masters_id" value="{{ $feemaster->id }}">
                                        <input type="hidden" name="net_balance" value="{{ $net_balance }}">
                                        <input type="hidden" name="description" value="{{$feemaster->fee_head_title}}">

                                        <button type="submit">
                                            <img alt="PayUMoney Payment Request Form" src="{{ asset('assets/images/paymenticon/payu.jpg') }}" width="70px" height="30px" />
                                        </button>
                                    </form>
                                 
                                  @endif      
                                

                                </td>
                                
                            </tr>
                            @php($i++)
                        @endforeach

                    @endif

                            <tr style="font-size: 14px; background: orangered;color: white;">
                                <td >Total</td>
                                <td></td>


                                <td>{{ $data['student']->fee_amount}}</td> 
                                <td>{{ $data['student']->paid_amount}}</td>
                                <td>{{$data['student']->balance}}</td>
                                <td>{{ $data['student']->discount }}</td>
                                <td>
                                @if($data['student']->balance == 0)
                                <span class="label label-success">Paid</span>
                                @elseif($data['student']->balance < 0 )
                                <span class="label label-warning">Negative</span>
                                @elseif($data['student']->balance < $data['student']->fee_amount)
                                <span class="label label-warning">Partial</span>
                                @else
                                <span class="label label-danger">Due</span>
                                @endif
                                </td>
                                <td ></td>
                                
                            </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="{{ asset('js/angular.min.js') }}"></script>


  <div  ng-app="MyApp">
   


      <div ng-controller="MyController">
        <input type="button" value="Show Transaction History" ng-click="ShowHide()" style=" margin: 12px 10px 0px 11px; " />
        <br />
        <br />
        <div ng-show = "IsVisible">
            

     <div class="table-responsive">
            <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                <thead >
                <tr>
                    <th>S.N.</th>
                     <th>reciept No</th>
                    <th>Fee-Head</th>
                    <th>Paid</th>
                   <!--  <th>Fine</th> -->
                   <!--  <th>Discount</th> -->
                   <!--  <th>Total paid</th> -->
                    <th>Mode</th>
                    <th>Date</th>
                    <th>status</th>
                    <th>print</th>
                   
                    
                </tr>
                </thead>
                <tbody>
                @if (isset( $data['fee_master']) &&  $data['fee_master']->count() > 0)
                    @php($i=1)
                    @foreach( $data['fee_master'] as $history)
                        <tr>
                            <td>{{$i}}</td>
                            <td>{{$history->reciept_no}}</td>
                            <td>{{$history->fee_head_title}}</td>
                            <td>{{$history->amount_paid}}</td>
                           
                            <td>{{$history->payment_type}}</td> 
                            <td>{{ \Carbon\Carbon::parse($history->reciept_date)->format('d-m-Y')}} </td> 
                                                  
                            <td>
                                 @if($history->status == 1)
                                <span class="label label-success">Paid</span>
                            @elseif($history->status == 0 )
                                <span class="label label-danger">failed</span>

                                </td>  
                               @endif 
                            <td>
                             @if($history->status == 1)
                              
                                   <a class="btn btn-xs btn-primary" href="{{ route('feeReceipt', ['receipt_no' => $history->reciept_no]) }}" target="_blank">print
                                    <i class="fa fa-print"></i>
                                    </a>
                            

                              @elseif($history->status == 0 )

                                
                              
                            @endif                 
                            </td> 
                        </tr>
                        
                        @php($i++)
                    @endforeach
                @else
                    <tr>
                        <td colspan="11">No {{ $panel }} data found.</td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
        </div>
    </div>
  
</div>
 <script type="text/javascript">
     

     var app = angular.module('MyApp', [])
        app.controller('MyController', function ($scope) {
            //This will hide the DIV by default.
            $scope.IsVisible = false;
            $scope.ShowHide = function () {
                //If DIV is visible it will be hidden and vice versa.
                $scope.IsVisible = $scope.IsVisible ? false : true;
            }
        });

        app.controller('FailedController', function ($scope) {
            //This will hide the DIV by default.
            $scope.hidefailed = false;
            $scope.failedhideshow = function () {
                //If DIV is visible it will be hidden and vice versa.
                $scope.hidefailed = $scope.hidefailed ? false : true;
            }
        });
 </script>

