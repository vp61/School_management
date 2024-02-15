@extends('layouts.master')
@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            
            

            <div class="page-content">
                @if(isset($data))
                <div class="page-content">
                    <div class="page-header">
                        <h1> Fees Manager 
                            <small>
                                <i class="ace-icon fa fa-angle-double-right"></i>
                                    Headwise Total Report
                            </small>
                        </h1>
                    </div><!-- /.page-header -->
                </div>
                    <form action="{{route('headwiseTotalReport')}}" method="post">
                       {{ csrf_field() }}
                        <div class="row">
                            <div class="col-sm-12">
                                @if(Session::has('msG'))
                                <div class="alert alert-success">{{Session::get('msG')}}</div>
                                @endif                    
                                <table class="table table-striped">
                                    @if($errors->any())
                                        <div class="alert alert-danger">
                                            @foreach($errors->all() as $err)
                                                <div>{{$err}}</div>
                                            @endforeach
                                        </div>
                                    @endif
                                    <tr>
                                        @if(Session::get('isCourseBatch'))
                                            <td style="padding-top:10px;">{{ env('course_label') }}:</td>
                                            <td >
                                                {{ Form::select('faculty[]', $data['faculty'], '', ['class'=>'batch_wise_cousre form-control selectpicker', 'required'=>'required', 'data-live-search'=>'true','id'=>'batch_wise_cousre'])}}
                                                <span class="error">{{ $errors->first('course') }}</span>
                                            </td>
                                            <td style="padding-top:10px;">Batch:</td>
                                            <td >
                                                {{ Form::select('batch',[''=>'Select Batch'], '', ['class'=>'batch form-control selectpicker', 'data-live-search'=>'true','id'=>'batch'])}}
                                            </td>
                                        @else
                                           <td style="padding-top:10px;">{{env('course_label')}}:</td>
                                            <td style="max-width: 150px">{{ Form::select('faculty[]', $data['faculty'],'', ['class'=>'form-control selectpicker','data-live-search'=>'true','required','multiple']) }}</td>
                                        @endif    
                                        <td class="text-center">
                                            <input type="submit" name="submit" value="Search" class="btn btn-info" />
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </form>
                @elseif(isset($students))
                    <div class="receipt-main col-xs-10 col-sm-10 col-md-10 col-xs-offset-1 col-sm-offset-1 col-md-offset-1">
                    <div class="row">
                        <div class="receipt-header">
                            <div class="col-xs-12 col-sm-12 col-md-12 text-center"><h4>{{$branch['branch_name']}}</h4>
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
                    <hr/>
                    
                    <div class="row">
                        <div class="receipt-header receipt-header-mid">
                            <div class="col-xs-12 col-sm-12 col-md-12" style="padding-bottom: 3px;text-align: center;">
                                <h4 text-align="center">Fee Head Report
                                    <button class="pull-right btn btn-info hidden-print" onclick="window.print()"> <i class="fa fa-print"></i> Print</button>
                                </h4>
                                {{-- - {{$info['courseName']}} --}}
                            </div> 
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <table class="table">
                                    {{-- <tr>
                                        <td style="font-weight: 700">Search Criteria:</td>
                                        @foreach($search_criteria as $key=>$val)
                                            @if(!empty($val))
                                                <td class="text-center">{{$key}} - {{$val}}</td>
                                               
                                            @endif    
                                        @endforeach
                                    </tr> --}}
                                </table>
                            </div> 
                        </div>
                    </div>
                   
                    <div>
                        @if(count($students)>0)
                            @foreach($students as $course => $heads)
                            <table class="table table-bordered">
                                <thead>
                                    
                                    <tr>
                                        <th colspan="4" class="text-center">{{$course}}</th>
                                    </tr>
                                    
                                </thead>
                                <tbody>
                                    <tr>
                                        <th class="" style="width: 50%">Head Title</th>
                                        <th>Total Fee</th>
                                        <th>Total Collection</th>
                                        <th>Total Due</th>
                                        
                                    </tr>
                                    @foreach($heads as $head_title => $assigned)
                                        @php $total_fee = $total_sum = 0; @endphp
                                        <tr>
                                            <td class="col-sm-5">{{$head_title}}</td>
                                            @foreach($assigned as $key => $fee)
                                                
                                                @if($fee['student_id'] == 0)
                                                    @php
                                                       $total_fee = ( $fee['fee_amount'] * $fee['student_count'] ) + $total_fee;

                                                       $total_sum = $total_sum + $fee['fee_sum']
                                                    @endphp   
                                                @else
                                                    @php
                                                       $total_fee = ( $fee['fee_amount'] * 1 ) + $total_fee;

                                                       $total_sum = $total_sum + $fee['fee_sum']
                                                    @endphp 

                                                @endif
                                            @endforeach

                                            <td>{{$total_fee}}</td>
                                            <td>{{$total_sum}}</td>
                                            <td>{{$total_fee - $total_sum}}</td>

                                        </tr>
                                    @endforeach
                                </tbody>
                               
                            </table>
                            @endforeach
                        @else
                            <table class="table table-bordered">
                                <thead>
                                    
                                    <tr>
                                        <th class="text-center">Fee Head Report</th>
                                    </tr>
                                    
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>No record found for selected branch.</td>
                                    </tr>
                                </tbody>
                            </table>            
                        @endif
                    </div>
                    
                    </div>
                    
                </div>
                @else
                    <table class="table table-bordered">
                        <tr>
                            <th>Something Went Wrong, Please Try Again Later.</th>
                        </tr>
                    </table>    
                @endif    
            </div>
        </div>
    </div>
   
@endsection

