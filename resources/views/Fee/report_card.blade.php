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
                                    Due Report (Monthwise)
                                </small>
                            </h1>
                        </div><!-- /.page-header -->
                    </div>
                    @include('includes.flash_messages')            
                <form action="{{route('monthly_statement')}}" method="post">
                   {{ csrf_field() }}
                    <div class="row">
                       <div class="col-sm-12">
                            @if(Session::has('msG'))
                            <div class="alert alert-success">
                                {{Session::get('msG')}}
                            </div>
                            @endif                    
                            <table class="table table-striped">
                               
                                <tr>
                                    <!-- <td style="padding-top:10px;" class="hidden">
                                        Session:
                                    </td> -->
                                    <div class="hidden">
                                        {{ Form::select('session', $data['session_list'], $data['current_session'], ['class'=>'sesn form-control', 'required'=>'required'])}}
                                        <span class="error">{{ $errors->first('session') }}</span>
                                    </div>
                                    <!-- <td style="padding-top:10px;" class="hidden">Branch:</td> -->
                                       
                                    @if(Session::get('isCourseBatch'))
                                        <td style="padding-top:10px;">{{ env('course_label') }}:</td>
                                        <td >
                                            {{ Form::select('course', $data['course_list'], '', ['class'=>'batch_wise_cousre form-control selectpicker', 'data-live-search'=>'true','id'=>'batch_wise_cousre','onchange'=>'loadSemesters(this)'])}}
                                            <span class="error">{{ $errors->first('course') }}</span>
                                        </td>
                                        <td style="padding-top:10px;">Batch:</td>
                                        <td >
                                            {{ Form::select('batch',[''=>'Select Batch'], '', ['class'=>'batch form-control selectpicker', 'required'=>'required', 'data-live-search'=>'true','id'=>'batch'])}}
                                        </td>
                                    @else
                                        <td style="padding-top:10px;">
                                            Months:
                                        </td>
                                        <td style="max-width:120px;">
                                            {{ Form::select('months[]', $data['months_list'],null, ['class'=>'mnt form-control selectpicker ','id'=>'due_month', 'data-live-search'=>'true','multiple'])}}
                                            <span class="error">{{ $errors->first('months') }}</span>
                                        </td>
                                        <td style="padding-top:10px;">
                                            {{ env('course_label') }}:
                                        </td>
                                        <td>
                                            {{ Form::select('course', $data['course_list'], '', ['class'=>'cors form-control selectpicker', 'data-live-search'=>'true','onchange'=>'loadSemesters(this)','id'=>'course'])}}
                                            <span class="error">{{ $errors->first('course') }}</span>
                                        </td>
                                         <td style="padding-top:10px;">
                                        Section:
                                    </td>
                                    
                                    <td>
                                        {{ Form::select('section', [''=>'Select'], '', ['class'=>'section form-control selectpicker','id'=>'semester_select', 'onchange'=>'loadStudents(this)'])}}

                                    </td>
                                    @endif
                                    <td>
                                        {{ Form::select('student', $data['student_list'], '', ['class'=>'student stdnt form-control selectpicker', 'data-live-search'=>'true','id'=>'std'])}}
                                    </td>
                                    <td style="padding-top:10px;">
                                            Type:
                                    </td>
                                    <td>
                                        {!!Form::select('report_type',['1'=>'Only Due','2'=>'Full Report'])!!}
                                    </td>
                                   
                                    <!-- <td><b>OR</b></td> -->
                                    <td class="text-center">
                                        <input type="submit" name="submit" value="Search" class="btn btn-info" />
                                    </td>
                                   
                                </tr>
                               
                            </table>
                        </div>
                    </div>
                </form>
                @elseif(isset($master))
                    <div class="receipt-main col-xs-10 col-sm-10 col-md-10 col-xs-offset-1 col-sm-offset-1 col-md-offset-1">
                        <div class="row">
                            <div class="receipt-header">
                                <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                                    <h4>{{$branch['branch_title']}}</h4>
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
                                        <p><i class="fa fa-phone"></i> &nbsp;{{$branch['branch_mobile']}} </p>
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
                                <h4 text-align="center">Due Report (Monthwise) <button class="pull-right btn btn-success hidden-print" onclick="Export('DueReportMonthwise')"> Export</button><span>  </span> <button class="pull-right btn btn-info hidden-print" onclick="window.print()"> <i class="fa fa-print"></i> Print</button></h4>
                            </div> 
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <table class="table">
                                    <tr>
                                        <td style="font-weight: 700">Search Criteria:</td>
                                        @foreach($search_criteria as $key=>$val)
                                            @if(!empty($val))
                                                <td class="text-center">{{$key}} - {{$val}}</td>
                                               
                                            @endif    
                                        @endforeach
                                    </tr>
                                </table>
                            </div>  
                        </div>
                    </div>
                        <div class="col-xs-12" style="padding: 0px">
                            <form method="POST" action="{{ route('monthly_statement') }}">
                                {{ csrf_field() }}
                                <table class="table table-bordered" id='tblFeeHeadWiseExl'>
                                  
                                    <tr>
                                        <th>#</th>
                                        <th>Student Name</th>
                                        <th>Father Name</th>
                                        <th>Reg No.</th>
                                        <th>{{env('course_label')}}</th>
                                        <th>Sec</th>
                                        <th>Month</th>
                                        <th>Assign</th>
                                        <th>Paid</th>
                                        <th>Discount</th>
                                        <th>Due</th>
                                        
                                    </tr>
                                    <tbody>
                                        
                                        
                                        @if(count($master)>0  && count($master)>0)
                                       
                                            @php   $i=1;$Gtt=$Gp=0 ;
                                            @endphp                                        
                                            @foreach($master as $key => $value)
                                        
                                                @php $i=1; $ttl=$paid=0; $disc=0; $due=0;$ttl_due=0;
                                                @endphp                                           

                                                    @foreach($value as $ke => $valu)
                                                        @foreach($valu as $k => $va)
                                                                <?php
                                                                    $due= $va['assign']- ($va['collect']+$va['discount']);
                                                                    if($type == 2){
                                                                        $show = 1;
                                                                    }elseif($due > 0 ){
                                                                        $show = 1;
                                                                    }else{
                                                                        $show=0;
                                                                    }
                                                                    
                                  
                                                                ?>
                                                                
                                                                @if($show)
                                                                    <tr>
                                                                        <td>{{$i++}}</td>
                                                                        <td>{{$va['stdata']->first_name}}</td>
                                                                        <td>{{$va['stdata']->fatherName}}</td>
                                                                        <td>{{$va['stdata']->reg_no}}</td>
                                                                        <td>{{$va['stdata']->faculty}}</td>
                                                                        <td>{{$va['stdata']->semester}}</td>
                                                                        <td>{{$va['month']}}</td>
                                                                        <td>{{$va['assign']}}</td>
                                                                        <td>{{$va['collect']}}</td>
                                                                         <td>{{$va['discount']}}</td>
                                                                        <td>
                                                                            
                                                                            {{$due}}
                                                                        </td>
    
    
                                                                    </tr>
                                                                    
                                                                    @php 
                                                                       
                                                                        $ttl=$ttl+$va['assign'];
                                                                        $paid=$paid+$va['collect'];
                                                                        $disc+= $va['discount'];
                                                                         $ttl_due+= $due;
                                                                      
                                                                    @endphp    
                                                                @endif    
                                                        @endforeach        
                                                    @endforeach
                                                    
                                                
                                               <tr class="bg-danger">
                                                    <td colspan="7">  <b>{{$va['month']}}'s</b>  <b>Total</b></td>
                                                    <td> {{$ttl}}</td>
                                                    <td> {{$paid}}</td>
                                                    <td> {{$disc}}</td>  
                                                    <td> {{$ttl_due}}</td>

                                                </tr>
                                                
                                                @php 
                                                    $Gtt=$Gtt+$ttl;
                                                    $Gp=$Gp+$paid+$disc;
                                                    $Gd=$Gtt-$Gp;
                                                @endphp  
                                            @endforeach
                                            <tr class="bg-primary">
                                                <td colspan="7"><b>Grand Total</b></td>
                                                <td>&#8377; {{$Gtt}}</td>
                                                <td colspan="2" style="text-align: center;">&#8377; {{$Gp}}</td>
                                                <td>&#8377; {{$Gd}}</td>


                                            </tr>
                                        @else
                                                <tr>
                                                    <td colspan="7">No Data</td>
                                                </tr>
                                        @endif  
                                        
                                    </tbody>
                                </table>
                            </form>    
                        </div>
                    </div>
                    <script> </script>
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
    <script>
    
     function loadSemesters($this) {

            $.ajax({
                type: 'POST',
                url: '{{ route('student.find-semester') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    faculty_id: $this.value
                },
                success: function (response) {
                    var data = $.parseJSON(response);
                    if (data.error) {
                        $.notify(data.message, "warning");
                    } else {
                        $('#semester_select').html('').append('<option value="0">Select Section</option>');
                        $.each(data.semester, function(key,valueObj){
                            $('#semester_select').append('<option value="'+valueObj.id+'">'+valueObj.semester+'</option>');
                        });
                    }
                }
            });

        }

        function loadStudents($this) {
            var course_id = $('#course').val(); 
            
            $.ajax({
                type: 'POST',
                url: '{{ route('student.find-students') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    section_id: $this.value,course_id:course_id
                },
                success: function (response) {
                    var data = $.parseJSON(response);
                    //alert(data);
                    if (data.error) {
                        
                        $.notify(data.message, "warning");
                    } else {
                         $('select.stdnt').html('').append('<option value="0">Select stdnt</option>');
                        $.each(data.student, function(key,valueObj){
                            $('select.stdnt').append('<option value="'+valueObj.id+'">'+valueObj.first_name+'</option>');
                        });
                    }
                    $('select.selectpicker').selectpicker('refresh');
                }
            });

          

        }
</script>

   
@endsection







