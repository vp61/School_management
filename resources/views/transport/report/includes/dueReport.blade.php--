@extends('layouts.master')
@section('content')
<hr class="hr-8">
<div class="container">
    <div class="row">
      


        <div class="receipt-main col-xs-10 col-sm-10 col-md-10 col-xs-offset-1 col-sm-offset-1 col-md-offset-1">
            <div class="row">
                <div class="receipt-header">
                    <div class="col-xs-12 col-sm-12 col-md-12 text-center"><h4>{{$data['user'][0]->branch_name}}</h4>
                </div>
            </div>
            
            <div class="row">
                <div class="receipt-header">
                    <div class="col-xs-3 col-sm-3 col-md-3">
                        <div class="receipt-left">
                            <img class="img-responsive" alt="iamgurdeeposahan" src="{{ asset('images/logo/')}}/{{$data['user'][0]->branch_logo}}" style="width: 78px; border-radius: 43px;">
                        </div>
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4 text-right">
                         
                    </div>
                    <div class="col-xs-5 col-sm-5 col-md-5 text-right">
                        <div class="receipt-right">
                          
                            <p><i class="fa fa-phone"></i> &nbsp; {{$data['user'][0]->branch_mobile}}</p>
                            <p><i class="fa fa-envelope-o"></i> &nbsp; {{$data['user'][0]->branch_email}}</p>
                            <p><i class="fa fa-location-arrow"></i> &nbsp;{{$data['user'][0]->branch_address}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <hr/>
            
            <div class="row">
                <div class="receipt-header receipt-header-mid">
                    <div class="col-xs-12 col-sm-12 col-md-12" style="padding-bottom: 3px;text-align: center;">
                        <h4 text-align="center">Transport Due Report</h4>
                    </div> 
                </div>
            </div>
           
            <div>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>SN</th>
                            <th>Type</th>
                            <th>Reg No</th>
                            <th>Name</th>
                            <th>Duration(From : To)</th>
                            <th>Fees</th>
                            <th>Paid</th>
                            <th>Due</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $total=$paid=$due=0;
                         @endphp
                        @if (isset($data['user']) && $data['user']->count() > 0)
                        @php($i=1)
                        @foreach($data['user'] as $user)
                            <tr>
                                <td>{{ $i }}</td>
                                <td>{{ $user->user_type==1?"Student":"Staff" }}</td>
                                <td>
                                    @if($user->user_type == 1)
                                     
                                            {{ ViewHelper::getStudentById($user->member_id) }}
                                      
                                    @else
                                      
                                            {{ ViewHelper::getStaffById($user->member_id) }}
                                      
                                    @endif
                                </td>
                                <td>
                                    @if($user->user_type==1)
                                        {{ ViewHelper::getStudentNameById($user->member_id) }}
                                    @else
                                        {{ ViewHelper::getStaffNameById($user->member_id) }}
                                    @endif
                                </td>
                                <td>
                                    <?php
                                        $dateFrom = \Carbon\Carbon::parse($user->from_date)->format('d-M-Y');
                                        $dateTo = \Carbon\Carbon::parse($user->to_date)->format('d-M-Y');
                                    ?>
                                    {{ucwords($user->duration) .' ('.$dateFrom.' To: '.$dateTo.')'}}
                                </td>
                                <td>
                                    {{$user->total_rent}}
                                </td>
                                <td>
                                    {{$rep[$user->id]['paid']}}
                                </td>
                                <td>
                                    {{$rep[$user->id]['due']}}
                                </td> 
                               <?php 
                                    $total=$user->total_rent + $total;
                                    $paid=$rep[$user->id]['paid'] + $paid;
                                    $due=$rep[$user->id]['due'] + $due;
                                ?> 
                            </tr>

                            @php($i++)
                        @endforeach
                        @endif
                        <tr>
                            <td colspan="5">
                                <b>Total</b>
                            </td>
                            <td>
                                {{$total}}
                            </td>
                            <td>
                                {{$paid}}
                            </td>
                            <td>
                                {{$due}}
                            </td>
                        </tr>
                    </tbody>
                </table>

            </div>
            
        </div>    
    </div>
</div>
                     <script> window.print(); </script>
 @endsection                   