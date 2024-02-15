@extends('layouts.master')

@section('css')
    <!-- page specific plugin styles -->

    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}" />
    @endsection

@section('content')
@php $panel="Registration"; @endphp
    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                @include('layouts.includes.template_setting')

                <div class="page-header">
                    <h1>
                     
                     {{$panel}} Manager 
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            {{$panel}}
                        </small>
                    </h1>
                    @if(Session::has('msg'))
        <div class="alert alert-info">
            <a class="close" data-dismiss="alert">×</a>
            <strong>Heads Up!</strong> {!!Session::get('msg')!!}
        </div>
    @endif
    

                </div><!-- /.page-header -->

<div class="form-horizontal ">
    @include('admission.include.search_form')
    <div class="hr hr-18 dotted hr-double"></div>
</div>

    <div class="text-right" >
         <a class="btn-primary btn-sm" href="{{ route('.admission') }}"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;New {{$panel}}</a>
     </div>

               <div class="row">
    <div class="col-xs-12">
        @include('includes.data_table_header')
        
        {!! Form::open(['route' => 'info.smsemail.dueReminder', 'id' => 'send_reminder_message']) !!}
       
        
        <div class="table-responsive">
            <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                   
                    <th>S.N.</th>
                    <th>Name</th>
                    <th>Father Name</th>
                    <th>Receipt No.</th>
                    <th>Form No.</th>
                    <th>Admission Date</th>
                    <th>Mobile</th> 
                    <!-- <th>Gender</th> -->
                    <th>{{ env('course_label') }}</th>
                    <th>Academic status</th>
                    <th>Address</th>
                    <th>Form fee</th>
                    <th>Payment Mode</th> 
                    <th>Ref No.</th>
                    <th>Created By</th>
                    <th>Status</th>
                    <th>Action</th>
                    <!--th></th-->
                </tr>
                </thead>
                <tbody> 
                    <?php $i =1; $ttl=0;?>
                    @foreach($admission_list as $x)
                            <tr>
                                <td class="center first-child">
                                    <label>
                                        {{$i++}}
                                    </label>
                                </td>
                                
                                <td>
                                   {{$x->first_name}} {{$x->middle_name}} {{$x->last_name}} 
                                </td>
                                <td >
                                    {{$x->father_name}}
                                </td>
                                <td>
                                    RSWS/REG/{{10000+$x->id}}
                                </td>
                                <td>
                                    {{$x->form_no}}
                                </td>
                                <td>
                                    
                                   @if($x->admission_date) {{ date('d-m-Y', strtotime($x->admission_date)) }} @endif
                                   
                                   
                                </td>
                                <td>
                                   {{$x->mobile}}
                                </td> 
                                <!-- <td>
                                  
                                </td> -->
                                <td>
                                   {{$x->course}}
                                </td>
                                <td>
                                   {{$x->academic_status}}
                                </td>
                                
                                <td>
                                   {{$x->address}}
                                </td>
                                
                                 <td>
                                   &#8377; {{$x->admission_fee}}
                                   @php
									$ttl += $x->admission_fee;
                                   @endphp
                                </td>
                                <td>
                                   {{$x->payment_type}}
                                </td> 
                                <td>
                                   {{$x->reference_no}}
                                </td>
                                <td>
                                   {{$x->name}}
                                </td>
                                <td>
                                    @if(isset($x->adm_id))
                                        <b class="green"> Closed</b>
                                    @else
                                        <b class="red">Pending</b>
                                    @endif    
                                </td>
                                <td nowrap> <?php $id = $x->id;?>
                                  
                                 <a href="{{ url('student/registration?admId='.$id) }}" class="fa fa-play" style="font-size:15px" title="Confirm Student"></a> 
                                 <a href="{{ url('admission/edit/'.$id) }}" class="fa fa-edit" style="font-size:15px" title="Edit"></a> 
								 <a class="fa" style="font-size:15px"  title="Print" target="_blank" href="{{ url('admission_print/'.$id) }}" ><i class="fa fa-print"></i></a>
                                </td>

                            </tr>
                           @endforeach
                   @if(empty($admission_list))
                        <tr>
                            <td colspan="14">No enquiry data found.</td>
                            </td>
                        </tr>
                    @endif

                </tbody>
<tfoot id="fixed_row">
 <tr >
    <td></td>
    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td><strong>Total: </strong></td>
    <td>&#8377; {{$ttl}}</td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
</tr>     
</tfoot>

            </table>
        </div>
        <!-- <div class="row"><div class="col-sm-2 col-sm-offset-10 text-left">
            <h5>Total: Rs {{$ttl}}</h5>
        </div></div> -->
        {!! Form::close() !!}
    </div>
</div>


<!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->


@endsection



@section('js')
    <!-- page specific plugin scripts -->
    
    
    
@endsection

