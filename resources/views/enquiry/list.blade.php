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
            @include('includes.flash_messages')
            <div class="form-horizontal ">
                @include('enquiry.search_form')
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
                    <th>Mobile</th>
                    <th>Enq Date</th>
                    <th>Last Follow Up</th>
                    <th>Next Follow Up</th>
                    <th>{{ env('course_label') }}</th>
                    <th>Academic status</th>
                   <!--  <th>Address</th> -->
                    <!-- <th>Extra_info</th> -->
                    <!-- <th>Responce</th>
                    <th>Reference</th> -->
                    <th>Enquiry By</th>
                    <th>Enquiry Status</th>
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
                                   {{$x->mobile}}

                                </td>
                                <td>
                                   {{$x->enq_date?\Carbon\Carbon::parse($x->enq_date)->format('d-M-Y'):'-'}}
                                </td>
                                <td>
                                    @php($last=$x->followup_date!=null?\Carbon\Carbon::parse($x->followup_date)->format('d-M-Y'):'')
                                    {{$last}}
                                </td>
                                 <td> @php($nxt_follow=$x->next_followup!=null?$x->next_followup:$x->next_follow_up)
                                    {{$nxt_follow?\Carbon\Carbon::parse($nxt_follow)->format('d-M-Y'):'-'}}
                                </td>
                                <td>
                                   {{$x->faculty}}
                                </td>
                                <td>
                                   {{$x->academic_status}}
                                </td>
                                <!-- <td>
                                   {{$x->address}}
                                </td> -->

                                <!-- <td>
                                   {{$x->extra_info}}
                                </td> -->
                                <!-- <td>
                                   {{$x->responce}}
                                </td>
                                <td>
                                   {{$x->reference}}
                                </td> -->
                                <td>
                                    @if($x->name)
                                        {{$x->name}}
                                    @else
                                        Online
                                    @endif
                                   
                                </td>
                                <td>
                                    {{$x->enq_status}}
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
                                  
                                 <a data-toggle="modal" data-target="#addFollowUp" onclick="addenq('{{$id}}')" title="Follow Up">
                                     <i class="fa fa-phone"></i>
                                 </a> | 
                                 <a href="{{ url('admission/'.$id) }}" title="Registration Form">
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
                            <td colspan="13">No enquiry data found.</td>
                            </td>
                        </tr>
                    @endif


                    
                </tbody>
              
            </table>
        </div>

        {!! Form::close() !!}
        <div class="modal fade" id="addFollowUp" role="dialog">
            <div class="modal-dialog modal-lg" style="width:85%">
            {!!Form::open(['route'=>'.enquiry.add_followup','method'=>'POST','class'=>'form-horizontal'])!!}    
              <div class="modal-content">
                <div class="modal-header" style="color: white; background: #438eb9;font-weight: 500;font-size: 15px;">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">Add Enqiry Follow Up</h4>
                </div>
                <div class="modal-body"> 
                   <div class="row">
                       <div class="col-sm-9">
                           <div class="form-group">
                               {!!Form::label('date','Follow Up Date',['class'=>'col-sm-2 control-label'])!!}
                               <div class="col-sm-4">
                                   {!!Form::date('followup_date',null,['class'=>'form-control date-picker','required'=>'required'])!!}
                               </div>
                               {!!Form::label('date','Next Follow Up',['class'=>'col-sm-2 control-label'])!!}
                               <div class="col-sm-4">
                                   {!!Form::date('next_followup',null,['class'=>'form-control date-picker'])!!}
                               </div>
                           </div>
                           <div class="form-group">
                                {!!Form::hidden('enquiry_id',null,['id'=>'enq_id'])!!}
                              {!!Form::label('response','Response',['class'=>'col-sm-2 control-label'])!!}
                               <div class="col-sm-4">
                                   {!!Form::textarea('response',null,['class'=>'form-control','required'=>'required', 'rows'=>'3'])!!}
                               </div> 
                               {!!Form::label('note','Note',['class'=>'col-sm-2 control-label'])!!}
                               <div class="col-sm-4">
                                   {!!Form::textarea('note',null,['class'=>'form-control', 'rows'=>'3'])!!}
                               </div>
                           </div>
                           <div class="form-group">
                               <div class="col-sm-12 text-right">
                                    <button class="btn btn-info" type="submit">
                                       Save
                                   </button>
                                   <button class="btn btn-default" type="reset">
                                       Reset
                                   </button>
                               </div>
                           </div>
                           <div>
                               <table class="table table-striped " >
                                    <thead style="">
                                        <th>By</th>
                                        <th>Date</th>
                                        <th>Response</th>
                                        <th>Note</th>
                                        <th>Delete</th>
                                    </thead>
                                    <tbody id="follow_history">
                                        
                                    </tbody>
                               </table>
                           </div>
                       </div>
                       <div class="col-sm-3" style="background: aliceblue;padding: 10px">
                           <div class="row">
                               
                               <div class="col-sm-7">
                                   
                               </div>
                           </div>
                           <div class="row">
                               <div class="col-sm-12">
                                   <table class=" table table-hover" style="font-weight: 600">
                                        <tr>
                                          <td>Status</td>
                                          <td >{!!Form::select('enq_status',$data['status'],null,['class'=>'form-control','id'=>'status'])!!}</td> 
                                       </tr>
                                       {!!Form::close()!!}
                                        <tr>
                                          <td>Enquiry Date:</td>
                                          <td id="edate"></td> 
                                       </tr>
                                       <tr>
                                          <td>Last Follow Up:</td>
                                          <td id="ldate"></td> 
                                       </tr>
                                       <tr>
                                          <td>Next Follow Up:</td>
                                          <td id="ndate"></td> 
                                       </tr>
                                       <tr>
                                          <td>Name:</td>
                                          <td id="name"></td> 
                                       </tr>
                                       <tr>
                                          <td>Mobile:</td>
                                          <td id="mobile"></td> 
                                       </tr>
                                       <tr>
                                          <td>Email:</td>
                                          <td id="email"></td> 
                                       </tr>
                                       <tr>
                                          <td>Class:</td>
                                          <td id="course"></td> 
                                       </tr>
                                       <tr>
                                           <td>No Of Child:</td>
                                           <td id="child"></td>
                                       </tr>
                                    
                                   </table>
                               </div>
                           </div>
                       </div>
                   </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
              </div>
              
            </div>
        </div>
    </div>
</div>


<!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->


@endsection

@section('js')
<script>
        function addenq($id){
            $('#enq_id').val($id);
            $.post(
                '{{route('enquiry.loadenquiry')}}',
                {
                    enq_id:$id,
                    _token:'{{csrf_token()}}'
                },
                function(response){
                var data= $.parseJSON(response);
                if(data.error){
                    toastr.warning('message',"Warning");
                }
                else{
                    var rec=data.data;
                        $('#edate').html('').append(rec.enq_date);
                         $('#ldate').html('').append(rec.followup_date);
                         var next_followup=rec.next_followup!=null?rec.next_followup:rec.next_follow_up;

                        $('#ndate').html('').append(next_followup);
                        $('#name').html('').append(rec.first_name);
                        $('#mobile').html('').append(rec.mobile);
                        $('#email').html('').append(rec.email);
                        $('#course').html('').append(rec.course);

                        $('#status').val(rec.enq_status);
                        $('#child').html('').append(rec.no_of_child);
                        $('#follow_history').html('');
                   $.each(data.list,function(key,val){
                    var res=val.response!=null?val.response:'';
                     var note=val.note!=null?val.note:'';
                    if(val.followup_date!=null){

                        $('#follow_history').append('<tr><td>'+val.name+'</td><td>'+val.followup_date+'</td><td>'+res+'</td><td>'+note+'</td><td><a href="/enquiry/followup/'+val.id+'/delete" ><i class="fa fa-trash red"></i></a>');
                    }
                   });
                }
            });
            
        }
    </script>
    <!-- page specific plugin scripts -->
    @include('includes.scripts.jquery_validation_scripts')
    @include('student.registration.includes.student-comman-script')
    @include('includes.scripts.inputMask_script')
    @include('includes.scripts.datepicker_script')
    @include('includes.scripts.delete_confirm')
    
@endsection