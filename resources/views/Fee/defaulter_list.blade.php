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
                                        Defaulter List
                                </small>
                            </h1>
                        </div><!-- /.page-header -->
                    </div>
                     @include('includes.flash_messages')
                    <form action="{{route('defaulter_list')}}" method="post">
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
                                                {{ Form::select('course', $data['faculty'], '', ['class'=>'batch_wise_cousre form-control selectpicker', 'required'=>'required', 'data-live-search'=>'true','id'=>'batch_wise_cousre'])}}
                                                <span class="error">{{ $errors->first('course') }}</span>
                                            </td>
                                            <td style="padding-top:10px;">Batch:</td>
                                            <td >
                                                {{ Form::select('batch',[''=>'Select Batch'], '', ['class'=>'batch form-control selectpicker', 'data-live-search'=>'true','id'=>'batch'])}}
                                            </td>
                                        @else
                                            <td style="padding-top:10px;">{{env('course_label')}}:</td>
                                            <td style="max-width: 150px">
                                                {{ Form::select('course', $data['faculty'],'', ['class'=>'form-control selectpicker','data-live-search'=>'true','required']) }}
                                            </td>
                                            <td style="padding-top:10px;">Section :</td>
                                            <td style="max-width: 150px">
                                                {{ Form::select('semester', $data['semester_list'],'', ['class'=>'form-control']) }}
                                            </td>
                                        @endif
                                        <td style="padding-top:10px;">Fee Head:</td>
                                        <td style="max-width: 150px">{{ Form::select('fee_type[]', $data['fee_list'],'', ['class'=>'form-control selectpicker','data-live-search'=>'true','multiple']) }}</td>
                                        <td style="padding-top:10px;">Due Amount:</td>
                                        <td style="max-width: 150px">{{ Form::number('due_amount','', ['class'=>'form-control','required']) }}</td>
                                        <td class="text-center">
                                            <input type="submit" name="submit" value="Search" class="btn btn-info" />
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </form>
                @elseif(isset($due_tbl))
                    <div class="receipt-main col-xs-10 col-sm-10 col-md-10 col-xs-offset-1 col-sm-offset-1 col-md-offset-1">
                        <div class="row">
                            <div class="receipt-header">
                                <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                                    <h4>{{$branch['branch_name']}}</h4>
                                </div>
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
                                <h4 text-align="center">Defaulter List <button class="pull-right btn btn-info hidden-print" onclick="window.print()"> <i class="fa fa-print"></i> Print</button></h4>
                            </div>  
                        </div>
                    </div>
                        <div class="col-xs-12" style="padding: 0px">
                            <form method="POST" action="{{ route('defaulter_notification') }}">
                                {{ csrf_field() }}
                                <table class="table">
                                   <?php 
                                        $total_pay=$total_paid=$total_disc=$total_due=0;
                                        
                                    ?>
                                    <tr>
                                        <th class="center">
                                            <label class="pos-rel">
                                                <input type="checkbox" class="ace" />
                                                <span class="lbl"></span>
                                            </label>
                                        </th>
                                        <th>Student Name</th>
                                        <th>Father Name</th>
                                        <th>Reg No.</th>
                                        <th>{{env('course_label')}}</th>
                                        <th>Sec</th>
                                        <!-- <th>Mobile</th> -->
                                        <th>To Pay</th>
                                        <th>Paid</th>
                                        <th>Concession</th>
                                        <th>Due</th>
                                    </tr>
                                    @foreach($due_tbl as $key=>$val)
                                    <?php $pay=$paid=$disc=$tdue=0; ?>
                                        @foreach($fee_arr as $feename2)
                                        <?php
                                            $temp_to_pay=(isset($val[$feename2]['to_pay'])?$val[$feename2]['to_pay']:0);
                                            $temp_paid=(isset($val[$feename2]['paid'])?$val[$feename2]['paid']:0);
                                            $temp_disc=(isset($val[$feename2]['disc'])?$val[$feename2]['disc']:0);
                                                $due=$temp_to_pay-($temp_paid+$temp_disc);
                                                $total_pay=$total_pay+$temp_to_pay;
                                                $total_paid=$total_paid+$temp_paid;
                                                $total_disc=$total_disc+$temp_disc;
                                                $total_due=$total_due+$due;
                                                $pay=$pay+$temp_to_pay;
                                                $paid=$paid+$temp_paid;
                                                $disc=$disc+$temp_disc;
                                                $tdue=$pay-($paid+$disc);
                                                if(isset($val[$feename2])){
                                                    $student = $val[$feename2]['student'];
                                                    $father = $val[$feename2]['fatherName'];
                                                    $reg_no = $val[$feename2]['admission_no'];
                                                    $course = $val[$feename2]['course'];
                                                    $sec = $val[$feename2]['sec'];
                                                    $mobile= $val[$feename2]['mobile'];
                                                }
                                            ?>
                                        @endforeach
                                            @if($tdue >= $due_amount)
                                                <tr>
                                                    <td class="center first-child">
                                                        <label>
                                                            <input type="checkbox" name="chkIds[]" value="{{ $reg_no }}" class="ace" />
                                                            <span class="lbl"></span>
                                                        </label>
                                                    </td>
                                                    <td nowrap >  
                                                        {{$student}}
                                                    </td>
                                                    <td nowrap >
                                                        {{$father}}
                                                    </td>
                                                    <td>
                                                    {{$reg_no}}
                                                    </td>
                                                    <td>
                                                    {{$course}}
                                                    </td>
                                                    <td>
                                                        {{$sec}}
                                                    </td>
                                                    {{-- <td>
                                                        {{$mobile}}
                                                    </td> --}}
                                                    <td>&#8377; 
                                                        
                                                        {{$pay}}
                                                    </td>
                                                    <td>&#8377; 
                                                        {{$paid}}
                                                    </td>
                                                    <td>&#8377; 
                                                        {{$disc}}
                                                    </td>
                                                    
                                                    <td>&#8377; {{$tdue}}</td>
                                                </tr>
                                            @endif 
                                        
                                    @endforeach
                                </table>
                                <div class="hidden-print">  
                                  <h4 class="header large lighter blue"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;  Send Notification</h4> 
                                  <div class="form-group">
                                    <div class="row">
                                        
                                   
                                        {!! Form::label('subject', 'Subject', ['class' => 'col-sm-2 control-label']) !!}
                                        <div class="col-sm-4">
                                                {!! Form::text('subject',null, ["placeholder" => "Enter Subject", "class" => "form-control",'required']) !!}
                                        </div>
                                        {!! Form::label('notification', 'Notification Type', ['class' => 'col-sm-2 control-label']) !!}
                                        <div class="col-sm-4">
                                              <label class="checkbox-inline"> {!! Form::checkbox('sms',null) !!} SMS 
                                                </label>
                                                <label class="checkbox-inline">
                                                {!! Form::checkbox('email',null) !!} E-mail
                                               </label>
                                        </div>
                                     </div>
                                       
                                  </div>
                                  <div class="form-group">
                                     <div class="row">
                                        {!! Form::label('content', 'Content', ['class' => 'col-sm-2 control-label']) !!}
                                        <div class="col-sm-10">
                                            {!! Form::textarea('content',null, ["placeholder" => "Enter Notification Content", "class" => "form-control",'required','rows'=>'4']) !!}
                                        </div>

                                     </div>  
                                  </div> 
                                  <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-12">
                                          <button class="btn btn-info pull-right" type="submit">Send</button>
                                      </div>
                                    </div>
                                  </div>
                          </div>
                            </form>    
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

@section('js')
     @include('includes.scripts.bulkaction_confirm')
    <script type="text/javascript">

            $('.check:button').toggle(function(){
                $('input:checkbox').attr('checked','checked');
                $(this).val('uncheck all')
            },function(){
                $('input:checkbox').removeAttr('checked');
                $(this).val('check all');        
            })
    </script>

@endsection