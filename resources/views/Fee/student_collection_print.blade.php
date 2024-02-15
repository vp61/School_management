@extends('layouts.master')
@section('content')

<hr class="hr-8">
<div class="container">
    <div class="row">
      


        <div class="receipt-main col-xs-10 col-sm-10 col-md-10 col-xs-offset-1 col-sm-offset-1 col-md-offset-1">
            <div class="row">
                <div class="receipt-header">
                    <div class="col-xs-12 col-sm-12 col-md-12 text-center"><h4>{{$branch->branch_name}}</h4>
                </div>
            </div>
            
            <div class="row">
                <div class="receipt-header">
                    <div class="col-xs-3 col-sm-3 col-md-3">
                        <div class="receipt-left">
                            <img class="img-responsive" alt="iamgurdeeposahan" src="{{ asset('images/logo/')}}/{{$branch->branch_logo}}" style="width: 78px;">
                        </div>
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4 text-right">
                         
                    </div>
                    <div class="col-xs-5 col-sm-5 col-md-5 text-right">
                        <div class="receipt-right">
                           
                            <p><i class="fa fa-phone"></i> &nbsp; {{$branch->branch_mobile}}</p>
                            <p><i class="fa fa-envelope-o"></i> &nbsp; {{$branch->branch_email}}</p>
                            <p><i class="fa fa-location-arrow"></i> &nbsp; {{$branch->branch_address}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <hr/>
            
            <div class="row">
                <div class="col-xs-12">
                    <button class="btn btn-info" type="submit" id="filter-btn" onclick="Export('student-HeadWise-Report')">
                            <i class="fa fa-download bigger-110"></i>
                            Excel Export
                    </button>
                </div> 
                <div class="receipt-header receipt-header-mid">
                    <div class="col-xs-12 col-sm-12 col-md-12" style="padding-bottom: 3px;text-align: center;">
                       
                    </div> 
                    
                </div>
            </div>
           
            <div>
                <table class="table table-bordered" id="tblFeeHeadWiseExl">
                     
                    <thead>
                         <tr>
                           
                            <td style="font-weight: 700" colspan="2" >Search Criteria:</td>
                            <td class="text-center" colspan="6" >

                                 @foreach($search_criteria as $key=>$val)

                                    @if(!empty($val))

                                        {{$key}} - {{$val}} &nbsp;|
                                   
                                    @endif    
                                @endforeach
                            </td>
                        </tr>   
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th>Father's Name</th>
                            <th>Reg No</th>
                            <th>Receipt No</th>
                            <th>Receipt Date</th>
                            <th>Head Name</th>
                            <th>Total Paid</th>
                          
                        </tr>
                    </thead>
                    <tbody>
                       
                      @if(count($collect)>0)  
                        @php $i=1; $ttl=0;
                         @endphp 
                        @foreach($collect as $k => $v)
                         <?PHP $ttl= $ttl+$v->paid;?>
                           <tr>
                                <td>{{$i}}</td>
                                <td nowrap>{{ $v->first_name }}</td>
                                <td nowrap>{{$v->father_name}}</td>
                                <td nowrap>{{$v->reg_no}})</td>
                                <td>{{ $v->reciept_no }}</td>
                                <td nowrap>{{ $v->reciept_date?\Carbon\Carbon::parse($v->reciept_date)->format('d-M-Y'):'-' }}</td>
                                <td nowrap><i></i>
                                      <?php
                                        if( isset($head) && array_key_exists($v->reciept_no, $head)){
                                           $head_arr= $head[$v->reciept_no];
                                           
                                        }

                                      ?>
                                      @if(isset($head_arr) && count($head_arr)>0)
                                        @foreach($head_arr as $head_k=>$head_v)
                                          <p>{{$head_v->fee_head_title}}- {{$head_v->headpaid}}</p>
                                        @endforeach
                                      @endif 

                                 </td>
                                <td nowrap><i></i>{{$v->paid }} </td>
                           </tr>
                           @php $i++; @endphp
                        @endforeach
                        <tr>
                            <td colspan="7"><b>Total</b></td>
                           
                            <td>&#8377; {{$ttl}}</td>
                           
                        </tr>
                      @else
                            <tr>
                                <td colspan="8">No Data</td>
                            </tr>
                      @endif
                    
                     <tfoot>
                            @foreach($session as $sess_id=>$sess_value)

                               <thead>
                                <tr>
                                    <td colspan="8"> <center>Total Summary ({{$sess_value}})</center></td>
                                </tr>
                                <tr>
                                
                                    <td>S.no</td>
                                    <td colspan="6">Head Name</td>
                                    <td> Total Paid</td>
                                </tr>
                              </thead>
                               @php $i=1; $total=0; @endphp
                                @if(isset($head_master) && count($head_master)>0)
                                     @foreach($head_master as $head_key =>$session)
                                       @foreach($session as $session_id =>$head_name)
                                          
                                           @if($sess_id==$session_id)
                                             <tr>
                                                  <td nowrap>{{$i}}</td>
                                                  <td  colspan="6" nowrap>{{$head_name['head']}}</td>
                                                  <td nowrap>{{$head_name['paid']}}</td>
                                             </tr>
                                      
                                          @php $i++; $total+=$head_name['paid']; @endphp
                                          @endif
                                       @endforeach
                                     @endforeach

                                     <tr>
                                         <td colspan="7" nowrap>Total</td>
                                         <td  nowrap>&#8377; {{$total}}</td>
                                     </tr>
                                @endif
                            @endforeach
                           


                           


                               
                           

                     </tfoot>  
                    </tbody>
                </table>
            
            </div>
            
            </div>
            
        </div>    
    </div>
</div>



 <script type="text/javascript">
        function Export(fileName="") {
          if(fileName==""){
            fileName = "student-HeadWise-Report";
          }
            $("#tblFeeHeadWiseExl").table2excel({
                filename: fileName+".xls"
            });
            
        }
    </script>
  

 <script> window.print(); </script>

    @endsection
 

