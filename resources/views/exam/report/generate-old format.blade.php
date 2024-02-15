@extends('layouts.master')
@section('css')
<style type="text/css">
    .nopad{
        padding: 0px;
    }
    .row{
        margin: 0px !important;
    }
    .strong{
        font-weight: 600;
        text-transform: uppercase;
    }
    .name_fields{
        font-size: 13px;
    }
</style>
@endsection
@section('content')

<!--h4 class="label label-warning arrowed-in arrowed-right arrowed" >Fee Reciept
</h4-->
<hr class="hr-8">

<div class="container" style="font-size: xx-small;">
    <div class="row" style="height: 50%">
      
        <div class="receipt-main col-xs-12 col-sm-12 col-md-12 ">
            <div class="row">
                <div class="receipt-header">
                    <div class="col-xs-12 col-sm-12 col-md-12 text-center"><h4>{{$branch->branch_name}}</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="receipt-header">
                            <div class="col-xs-5 col-sm-5 col-md-5">
                                <div class="receipt-left">
                                    <img class="img-responsive" alt="logo" src="{{asset('images/logo/'.$branch->branch_logo)}}" style="width: 78px;margin-top: -20px;">
                                    
                                </div>
                            </div>
                          
                            <div class="col-xs-7 col-sm-7 col-md-7 text-right">
                                <div class="receipt-right">
                                   
                                    <p><i class="fa fa-phone"></i> &nbsp; {{$branch->branch_mobile}}</p>
                                    <p><i class="fa fa-envelope-o"></i> &nbsp; {{$branch->branch_email}}</p>
                                    <p><i class="fa fa-location-arrow"></i> &nbsp; {{$branch->branch_address}}</p>
                                </div>
                            </div>
                        </div>   
                    </div> 
                </div>
                <hr>
                
                <div class="row">
                    @if($student)
                        <div class="col-xs-12 col-sm-12 col-md-12 name_fields">
                            <div class="row">
                                <div class="col-sm-10 col-xs-10 col-md-10 pull-left">
                                    <div class="row">
                                        <div class="col-sm-2 col-xs-3 col-md-2 strong">
                                            NAME: 
                                        </div>
                                        <div class="col-sm-10 col-xs-9 col-md-10">    
                                            {{$student->first_name}}
                                        </div>
                                    </div>    
                                    <div class="row">
                                        <div class="col-sm-2 col-xs-3 col-md-2 strong">
                                            FATHER NAME:
                                        </div>    
                                        <div class="col-sm-10 col-xs-9 col-md-10">
                                            {{$student->father_first_name}}
                                        </div>
                                    </div>    
                                    <div class="row">
                                        <div class="col-sm-2 col-xs-3 col-md-2 strong">
                                            REG No.:
                                        </div>    
                                        <div class="col-sm-10 col-xs-9 col-md-10">
                                            {{$student->reg_no}}
                                        </div>
                                    </div>    
                                    <div class="row">    
                                        <div class="col-sm-2 col-xs-3 col-md-2 strong">
                                           DOB:
                                        </div>    
                                        <div class="col-sm-10 col-xs-9 col-md-10">
                                            {{\Carbon\Carbon::parse($student->date_of_birth)->format('d-M-Y')}}
                                        </div>
                                    </div>    
                                    <div class="row">
                                        <div class="col-sm-2 col-xs-3 col-md-2 strong">
                                           {{env('course_label')}}:
                                        </div>    
                                        <div class="col-sm-10 col-xs-9 col-md-10">
                                            {{$student->course}}
                                        </div>
                                    </div>    
                                    <div class="row">
                                        <div class="col-sm-2 col-xs-3 col-md-2 strong">
                                           Sem/Sec:
                                        </div>    
                                        <div class="col-sm-10 col-xs-9 col-md-10">
                                            {{$student->semester}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2 col-xs-2 col-md-2 pull-left" >
                                    @if($student->student_image)
                                        <div style="border: 1px solid black; height:100px;width: 100%">
                                            <img src="{{asset('images/studentProfile/'.$student->student_image)}}" height="100%" width="100%" >
                                        </div>
                                        
                                    @endif
                                </div>
                            </div>
                            <hr>
                        </div> 
                    @endif
                    @if(isset($data))
                        @if(count($data)>0)
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <table class="table table-bordered table-striped table-hover">
                                    <tr class="strong">
                                        @foreach($data as $key =>$val)
                                            <th colspan="{{count($val)*4}}">{{$key}}</th>
                                        @endforeach
                                    </tr>
                                     <tr class="strong">
                                        <?php $cnt = 0; ?>
                                        @foreach($data as $key =>$val)

                                            @foreach($val as $k=>$v)
                                                <?php 
                                                    // $i = count($val)+ count($val);
                                                    // $cnt = ($i>$cnt)?$i:$cnt;
                                                    $cnt++;
                                                ?>

                                                <td colspan="4">{{$k}}</td>
                                            @endforeach    
                                        @endforeach
                                        <?php $cnt = $cnt*4; ?>
                                    </tr>
                                     <tr class="strong">
                                        @foreach($data as $key =>$val)
                                            @foreach($val as $k=>$v)
                                                <td >Subject</td>
                                                <td >Max Mark</td>
                                                <td >Pass Mark</td>
                                                <td >Obtained Mark</td>
                                            @endforeach    
                                        @endforeach
                                    </tr>
                                    <?php
                                        $total =0;
                                        $obtained = 0; 
                                     ?>
                                        @foreach($data as $key =>$val)
                                            
                                                @foreach($val as $k=>$v)
                                                        @foreach($v as $k1=>$v1)
                                                            @if($v1)
                                                            <tr class="table_row">
                                                                <td>{{$v1->subject}}</td>
                                                                <td>{{$v1->max_mark}}</td>
                                                                <td>{{$v1->pass_mark}}</td>
                                                                <td>{{$v1->attendance == 1?$v1->obtained_mark:'AB'}}</td> 
                                                                <?php 
                                                                    $total = $v1->max_mark + $total;
                                                                    $obtained = $v1->obtained_mark + $obtained;
                                                                ?>
                                                            </tr>    
                                                            @endif 
                                                        @endforeach
                                                       
                                                @endforeach
                                            
                                        @endforeach
                                        <tr>
                                            <td colspan="3">
                                                <b>TOTAL MARKS</b>
                                            </td>
                                            <td>
                                                <b>{{$obtained}} / {{$total}}</b>
                                            </td>
                                        </tr>
                                </table>
                            </div>
                        @endif
                    @endif    
                </div> 
                
            </div>
            
        </div>    
    </div>
    
</div>


 <script>

  window.print();

</script>

    @endsection
 

@section('js')
   
@endsection