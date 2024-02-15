@extends('layouts.master')

@section('css')
    <!-- page specific plugin styles -->

    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}" />
    @endsection

@section('content')

    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                @include('layouts.includes.template_setting')

                <div class="page-header hidden-print">
                    <h1>
                     Reception Manager 
                        <small>
                        <i class="ace-icon fa fa-angle-double-right"></i>
                        Enquiry Details
                        </small>
                    </h1>

                </div><!-- /.page-header -->

                <div class="row">
                    <div class="col-xs-12 ">
                 
                    <!-- PAGE CONTENT BEGINS -->
                        <h4 class="header large lighter blue hidden-print"><i class="fa fa-search" aria-hidden="true"></i>&nbsp;Search Details</h4>
                        <form method="post" action="{{route('.enquiry_status')}}" class="hidden-print"> {{@csrf_field()}}
                            <div class="clearfix"> 
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">{{ env('course_label') }}</label>
                                    <div class="col-sm-3">
                                        
                                        {!! Form::select('faculty', $course, null, ['class' => 'form-control', 'onChange' => 'loadSemesters(this);']) !!}

                                    </div>
                                    {!! Form::label('reg_date', 'Reg. Date', ['class' => 'col-sm-2 control-label']) !!}
                                    <div class=" col-sm-5">
                                        <div class="input-group ">
                                            {!! Form::date('reg_start_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
                                            <span class="input-group-addon">
                                                <i class="fa fa-exchange"></i>
                                            </span>
                                            {!! Form::date('reg_end_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
                                            @include('includes.form_fields_validation_message', ['name' => 'reg_start_date'])
                                            @include('includes.form_fields_validation_message', ['name' => 'reg_end_date'])
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="clearfix">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Category</label>
                                    <div class="col-sm-3">
                                       {!!Form::select('category',$category,null,['class'=>'form-control'])!!}  
                                    </div>
                                    <label class="col-sm-2 control-label">Name</label>
                                    <div class="col-sm-2"><input type="text" name="name" class="form-control" value="{{ $data['filter_query']['name'] or ''}}" placeholder="Enter Student Name" /></div>

                                    <label class="col-sm-1 control-label">Mobile</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="mobile" class="form-control" placeholder="Enter Mobile No." value="" /></div>
                                </div>
                            </div>

                            <div class="clearfix">
                                <div class="form-group">
                                   
                                     <div class="col-md-12 align-right">        &nbsp; &nbsp; &nbsp;

                                        <button class="btn btn-info" type="submit" id="filter-btn">
                                            <i class="fa fa-filter bigger-110"></i>
                                            Search
                                        </button>
                                    </div>
                                </div>
                            </div>
                           

<!-- <div class="clearfix form-actions">
 -->                    </form>
 <hr>
                   <!--  <h4 class="header large lighter blue">ABC<button class="btn btn-danger pull-right">Print</button> </h4> -->
                   @if(isset($chart) || isset($count_chart))
                   <div class="row hidden-print">
                       <div class="col-md-12">
                           <button class="btn btn-danger pull-right header" onclick="printChartDiv()" id="ptn">Print</button> 
                       </div>
                   </div>
                  
                    <h4 class="header large lighter blue"><i class="fa fa-bar-chart"></i> Enquiry & Admission Report</h4>
                        <div class="row" >
                             @if(isset($chart))
                                <div class="col-sm-6 col-6" style="border: 1px solid;">
                                    <div>
                                      
                                         {!!$chart->container()!!}
                                      
                                    </div>
                                </div>
                             @endif
                              @if(isset($count_chart))
                                <div class="col-sm-6 col-6" style="border: 1px solid;">
                                    <div>
                                         {!!$count_chart->container()!!}
                                    </div>
                                </div>
                             @endif
                        </div>
                   
                    @endif

                    @if(isset($count))
                    <hr>
                        <h4 class="header lighter large blue"><i class="fa fa-line-chart"></i> Enquiry / Admission / Registration Ratio </h4>
                        <div class="row">
                            
                                <label class="control-label col-sm-3" style="height: 83px;">
                                     <div class="col-sm-8">
                                        <h3>Total Enquiries</h3>
                                    </div>
                                    <div class="col-sm-4">
                                        <h3><b>( {{$count[0]}} )</b></h3>
                                    </div>
                                </label>
                                <div class="col-sm-1 text-center"><h3><i class="fa fa-long-arrow-right fa-2x" ></i></h3></div>
                                <label class="control-label col-sm-4" style="height: 83px;">
                                    <div class="col-sm-8">
                                        <h3>Admissions Forms</h3>
                                    </div>
                                     <div class="col-sm-4">
                                        <h3><b>( {{$count[1]}} )</b></h3>
                                    </div>
                                </label>
                                <div class="col-sm-1 text-center"><h3><i class="fa fa-long-arrow-right fa-2x" ></i></h3></div>
                                 <label class="control-label col-sm-3" style="height: 83px;">
                                    <div class="col-sm-8">
                                        <h3>Registrations</h3>
                                    </div>
                                     <div class="col-sm-4">
                                        <h3><b>( {{$count[2]}} )</b></h3>
                                    </div>
                                </label>
                                
                               
                                
                            
                           
                        </div>
                    @endif    
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    
    </div><!-- /.main-content -->


@endsection

@section('js')
    <!-- page specific plugin scripts -->
    <script type="">
            function printChartDiv() { 
               window.print();
              
        }
            
       

    </script>
    <script src="{{asset('assets/js/chart.min.js')}}" charset="utf-8"></script>
   @if(isset($chart))
         {!! $chart->script() !!}
   @endif
    @if(isset($count_chart))
         {!! $count_chart->script() !!}
   @endif
    @include('includes.scripts.jquery_validation_scripts')
    @include('student.registration.includes.student-comman-script')
    
    @include('includes.scripts.inputMask_script')
    @include('includes.scripts.datepicker_script')
    
@endsection