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

                <div class="page-header">
                    <h1>
                     Reception Manager 
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Enquiry
                        </small>
                    </h1>
                    @if(Session::has('msg'))
        <div class="alert alert-info">
            <a class="close" data-dismiss="alert">×</a>
            <strong>Heads Up!</strong> {!!Session::get('msg')!!}
        </div>
    @endif
    

     </div><!-- /.page-header -->
     <div class="text-right" >
         <a class="btn-primary btn-sm" href="{{ route('.enquiry') }}"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp; New Enquiry</a>
     </div>

    <div class="row">
        <div class="col-xs-12 ">
            <!-- PAGE CONTENT BEGINS -->
            @include('includes.validation_error_messages')
            <div class="form-horizontal ">
                @include($view_path.'.includes.form')
                <div class="hr hr-18 dotted hr-double"></div>
            </div>
        </div><!-- /.col -->
    </div>


               <div class="row">
    <div class="col-xs-12">

        @include('includes.data_table_header')
        
        {!! Form::open(['route' => 'info.smsemail.dueReminder', 'id' => 'send_reminder_message']) !!}
       
       
        <!-- div.table-responsive -->
        <div class="table-responsive">
            <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                   
                    <th>S.N.</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Enq Date</th>
                    <th>Mobile</th>
                    <th>Date of birth</th>
                    <th>Gender</th>
                    <th>Course</th>
                    <th>Academic status</th>
                    <th>Address</th>
                    <!-- <th>Extra_info</th> -->
                    <th>Responce</th>
                    <th>Reference</th>
                    <th>Enquiry By</th>
                    <th>Status</th>
                    <th>Action</th>
                    <!--th></th-->
                </tr>
                </thead>
                <tbody> 
                    <?php $i =1;?>
                    @foreach($enquiries as $x)
                            <tr>
                                <td class="center first-child">
                                    <label>
                                        {{$i++}}
                                    </label>

                                </td>                                
                                <td>
                                   {{$x->first_name}} {{$x->middle_name}} {{$x->last_name}} 
                                </td>
                                <td>
                                   {{$x->email}}
                                </td>
                                <td>
                                   {{$x->enq_date}}
                                </td>
                                <td>
                                   {{$x->mobile}}

                                </td>
                                <td>
                                   {{ \Carbon\Carbon::parse($x->date_of_birth)->format('d-m-Y')}}
                                </td>
                                <td>
                                   {{$x->gender}}
                                </td>
                                <td>
                                   {{$x->faculty}}
                                </td>
                                <td>
                                   {{$x->academic_status}}
                                </td>
                                <td>
                                   {{$x->address}}
                                </td>

                                <!-- <td>
                                   {{$x->extra_info}}
                                </td> -->
                                <td>
                                   {{$x->responce}}
                                </td>
                                <td>
                                   {{$x->reference}}
                                </td>
                                <td>
                                   {{$x->name}}
                                </td>
                                <td>
                                    @if(!empty($x->std_adm_id))
                                       <b class="green">  Closed</b>
                                    @elseif(!empty($x->adm_enq_id))
                                        <b class="blue">In Progress</b>
                                    @else
                                        <b class="red">Pending</b>
                                    @endif    
                                                
                                </td>
                                <td>
                                    <?php $id = $x->id;?>
                                  
                                 
                                 <a href="{{ url('admission/'.$id) }}" title="Admission Form">
                                    <i class="fa fa-play"></i> 
                                 </a> | 
                                <a href="{{ url('enquiry/edit/'.$id) }}" title="Edit Enquiry">
                                    <i class="fa fa-edit"></i> 

                                </a> 
                                </td>

                            </tr>
                           @endforeach
                   @if(empty($enquiries))
                        <tr>
                            <td colspan="9">No enquiry data found.</td>
                            </td>
                        </tr>
                    @endif


                    
                </tbody>
              
            </table>
        </div>

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
    @include('includes.scripts.jquery_validation_scripts')
    @include('student.registration.includes.student-comman-script')
    @include('includes.scripts.inputMask_script')
    @include('includes.scripts.datepicker_script')
@endsection