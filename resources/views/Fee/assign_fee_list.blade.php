@extends('layouts.master')
@section('css')
    <link rel="{{asset('assets/css/bootstrap-multiselect.min.css')}}"></style>
    <style>
        .dis_none{
            display: none;
        }
    </style>
@endsection
@section('content')

    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                <div class="page-header">

                    <h1> Fees Manager 
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Assign Fees
                        </small>
                    </h1>
                </div><!-- /.page-header -->
            </div>
            
            <?php /*
            <div class="page-content">
                @include('includes.flash_messages')
                <form action="{{route('new_assign_fee')}}" method="post">
                    {{ csrf_field() }}
                    <div class="row" style="border:1px solid black; border-radius:20px; padding:5px 0px;">
                        <div class="col-sm-3" style="max-height: 500px;overflow: scroll;">
                            <ul class="list-group">
                                <li class="list-group-item  active">Choose From Fees Listed:</li>
                                @foreach($head_list as $heads)
                                    <li class="list-group-item">
                                        <input type="checkbox" class="head" name="heads[]" id="{{$heads->id}}" value="{{$heads->id}}"  /> &nbsp;
                                        <b style="font-size: 10px"> {{$heads->fee_head_title}} </b>
                                        <i class="fa fa-plus icon{{$heads->id}} pull-right" onclick="showSubmenu('{{$heads->id}}')" ></i>
                                        <i class="fa fa-minus icon{{$heads->id}} pull-right"  onclick="showSubmenu('{{$heads->id}}')" style="display: none"></i>
                                    </li>
                                    @foreach($sub_heads as $key =>$val)
                                            @if($heads->id==$key && (count($val)>0))
                                               <div id="slide{{$heads->id}}" style="display: none">
                                                    @foreach($val as $k=>$v)
                                                    <li class="list-group-item">
                                                        <ul class="submenu">
                                                            <li class="list-group-item">
                                                            <input type="checkbox" class="head" name="heads[]" id="{{$v->id}}" value="{{$v->id}}"  /> &nbsp;
                                                            <b style="font-size: 10px"> {{$v->fee_head_title}} </b>
                                                        </li> 
                                                        </ul>
                                                    </li>    
                                                    @endforeach
                                                </div>         
                                            @endif
                                    @endforeach
                                @endforeach
                            </ul>
                            <span class="error">{{ $errors->first('head') }}</span>
                        </div>
                        <div class="col-sm-9">
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
                                    <td style="padding-top:10px;">Branch:</td>
                                    <td>
                                        <select name="branch_id" class="branch_drop form-control" required>
                                            <option value="{{$branch_list[0]->id}}">{{$branch_list[0]->branch_name}}</option>
                                        </select>
                                        
                                        <span class="error">{{ $errors->first('branch_id') }}</span>
                                    </td>
                                    <td style="padding-top:10px;">Session:</td>
                                    <td>{{ Form::select('session',$session_list, $current_session, ['class'=>'form-control sesn', 'required'=>'required'])}}
                                        <span class="error">{{ $errors->first('session') }}</span>
                                    </td>
                                </tr>
                                @if(Session::get('isCourseBatch'))
                                    <tr>
                                        <td style="padding-top:10px;">{{ env('course_label') }}:</td>
                                        <td colspan="3">
                                            {{ Form::select('course', $course_list, '', ['class'=>'batch_wise_cousre form-control selectpicker', 'required'=>'required', 'data-live-search'=>'true','id'=>'batch_wise_cousre'])}}
                                            <span class="error">{{ $errors->first('course') }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding-top:10px;">Batch :</td>
                                        <td colspan="3">
                                            {{ Form::select('batch',[''=>'Select Batch'], '', ['class'=>'batch form-control selectpicker', 'required'=>'required', 'data-live-search'=>'true','id'=>'batch'])}}
                                        </td>
                                    </tr>
                                @else
                                   <tr>
                                        <td style="padding-top:10px;">{{ env('course_label') }}:</td>
                                        <td colspan="3">
                                            {{ Form::select('course', $course_list, '', ['class'=>'cors form-control selectpicker', 'required'=>'required', 'data-live-search'=>'true','id'=>'course'])}}
                                            <span class="error">{{ $errors->first('course') }}</span>
                                        </td>
                                    </tr> 
                                @endif    
                                <tr>
                                    <td style="padding-top:10px;">Student:</td>
                                    <td colspan="3">{{ Form::select('student', $student_list, '', ['class'=>'student form-control selectpicker', 'data-live-search'=>'true'])}}</td>
                                </tr>

                                @foreach($head_list as $heads)
                                    <tr style="display:none;" class="dspl_{{$heads->id}}">
                                        <td>{{$heads->fee_head_title}}</td>
                                        <td id="input_{{$heads->id}}">
                                            <input type="number" name="fee_amnt[{{$heads->id}}]" placeholder="Enter {{$heads->fee_head_title}} Amount" class="ttla form-control" />
                                            <input type="hidden" name="fee_hd[]" value="{{$heads->id}}" class="form-control" />
                                        </td>
                                        <td>Due Month</td>
                                         
                                        <td>{!!Form::select('due_month['.$heads->id.'][]',$months_list,null,['class'=>'form-control ttla selectpicker','data-live-search'=>'true','multiple'])!!}</td>
                                    </tr>               
                                                  
                                @endforeach
                                <tr>
                                    <td colspan="4" class="text-center">
                                        <input type="submit" name="submit" value="Save" class="btn btn-info" />
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
            */ ?>
            <div class="page-content">
                <h4 class="header large lighter blue strong"><i class="fa fa-search"></i> Search {{$panel}}</h4>
                {!!Form::open(['route'=>'assign_fee_list','method'=>'get','class'=>'form-horizontal','autocomplete'=>'off'])!!}
                <div class="form-group">
                    <label class="col-sm-2 control-label">{{env('course_label')}}:</label>
                    <div class="col-sm-4">
                        <?php 
                           
                        ?>
                        {!!Form::select('course',$course_list,null,['class'=>'form-control selectpicker cors','data-live-search'=>'true','required'=>'required'])!!}
                    </div>
                    <label class="col-sm-2 control-label">Student:</label>
                    <div class="col-sm-4">
                        
                        {!!Form::select('student_id',$student_list,null,['class'=>'form-control selectpicker student','data-live-search'=>'true'])!!}
                    </div>            
                </div>
                    <div class="form-group">
                    <div class="clearfix form-actions">
                        <button class="btn btn-info pull-right">Search</button>
                    </div>
                </div>
                </div>
                
                {!!Form::close()!!}
                
                <div class="table-responsive">
                    <h3 class="header large lighter blue">Assigned fee list</h3>
                    <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>      
                                <th>S.N.</th>
                                <th>{{ env('course_label') }}</th>
                                @if(Session::get('isCourseBatch'))
                                    <th>Batch</th>
                                @endif
                                <th>Session</th>
                                <th>Fee Head</th>
                                <th>Amount</th>
                                <th>Created By</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody> 
                            <?php $i =1;?>
                            
                            @foreach($assign_list as $x)
                                    <tr>
                                        <td class="center first-child">
                                            <label>
                                                {{$i++}}
                                            </label>
                                        </td>
                                        <td>{{$x->faculty}} @if($x->first_name) ({{$x->first_name}} @if($x->father_name)({{$x->father_name}})  @endif ) @endif</td>
                                        @if(Session::get('isCourseBatch'))
                                            <td>{{$x->batch_title}}</td>
                                        @endif
                                        <td>{{$x->session_name}}</td>
                                        <td>{{$x->fee_head_title}}</td>
                                     
                                        <td>{{$x->fee_amount}}</td>
                                        <td>{{$x->name}}</td>
                                        <td>{{date('d-m-Y', strtotime($x->created_at))}}</td>
                                        <td>
                                            <a data-toggle="modal" class="btn btn-success btn-minier" data-target="#editModal" onclick="editassign('{{$x->id}}')"><i class="fa fa-pencil"></i></a>
                                            <a href="{{route('assignFee.delete',$x->id)}}" class="btn btn-minier btn-danger bootbox-confirm"><i class="fa fa-trash"></i></a>
                                        </td>
                                    </tr>
                            @endforeach
                            @if(empty($assign_list))
                                <tr>
                                    <td colspan="7">No Fees Assign data found.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editModal" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="padding: 10px;">
                <div class="modal-header">
                    <button class="close" type="button" data-dismiss="modal"> &times;</button>
                    <h4 class="modal-title">Edit Assign Fee</h4>
                </div>
                <div class="modal-body" >
                    <div id="error_msg" style="display: none;">
                        
                    </div>
                    <div class="row" id="modal_row">
                        <h4 class="header large lighter blue">Current Assigned Values</h4>
                        <table class="table table-striped">
                            <tr>
                                <th>{{env('course_label')}}</th>
                                <th>Fee Head</th>
                                <th>Fee Amount</th>
                                <th>Assigned( {{env('course_label')}} / Student )</th>
                                <th>Due In</th>
                                <th>Created By</th>
                                <th>Created At</th>
                            </tr>
                            <tr>
                                <td id="feeCourse"></td>
                                <td id="feeHead"></td>
                                <td id="feeAmount"></td>
                                <td id="assignedTo"></td>
                                <td id="dueIn"></td>
                                <td id="createdBy"></td>
                                <td id="createdAt"></td>
                            </tr>
                        </table>
                        <hr>
                        <h4 class="header large lighter blue">Assign New Values</h4>
                        {!!Form::open(['route'=>'edit.assignfee','method'=>'POST','class'=>'form-horizontal'])!!}
                        @if(Session::get('isCourseBatch'))
                            <div class="form-group">
                                {!!Form::label('course','Select '.env('course_label'),['class'=>'col-sm-2 control-label'])!!}
                                <div class="col-sm-10">
                                    {!!Form::select('course_id',$course_list,null,['class'=>'form-control batch_wise_cousre_edit_modal selectpicker','data-live-search'=>'true','required'=>'required','id'=>'modal_course'])!!}
                                </div>
                            </div>  
                            <div class="form-group">  
                                {!!Form::label('batch','Select Batch',['class'=>'col-sm-2 control-label'])!!}
                                <div class="col-sm-4">
                                   {{ Form::select('batch',[''=>'Select Batch'], '', ['class'=>'batch_edit_modal form-control selectpicker', 'required'=>'required', 'data-live-search'=>'true','id'=>'modal_batch'])}}
                                </div>
                                {!!Form::label('course','Select Student',['class'=>'col-sm-2 control-label'])!!}
                                <div class="col-sm-4">
                                    {!!Form::select('student_id',[""=>'Select Student'],null,['class'=>'form-control std selectpicker','data-live-search'=>'true','id'=>'modal_student'])!!}
                                </div>
                            </div>
                        @else
                            <div class="form-group">
                            {!!Form::label('course','Select '.env('course_label'),['class'=>'col-sm-2 control-label'])!!}
                            <div class="col-sm-4">
                                {!!Form::select('course_id',$course_list,null,['class'=>'form-control course selectpicker','data-live-search'=>'true','required'=>'required','id'=>'modal_course'])!!}
                            </div>
                            {!!Form::label('course','Select Student',['class'=>'col-sm-2 control-label'])!!}
                            <div class="col-sm-4">
                                {!!Form::select('student_id',[""=>'Select Student'],null,['class'=>'form-control std selectpicker','data-live-search'=>'true','id'=>'modal_student'])!!}
                            </div>
                        </div>
                        @endif
                        <div class="form-group">
                            {!!Form::label('amount','Enter Amount',['class'=>'col-sm-2 control-label'])!!}
                            <div class="col-sm-4">
                                {!!Form::number('amount',null,['class'=>'form-control','required'=>'required','id'=>'modal_amount'])!!}
                                {!!Form::hidden('session',null,['id'=>'session'])!!}
                                {!!Form::hidden('id',null,['id'=>'id'])!!}
                            </div>
                            {!!Form::label('due_mnt','Due Month',['class'=>'col-sm-2 control-label'])!!}
                            <div class="col-sm-4">
                                {!!Form::select('due_month',$months_list,null,['class'=>'form-control','required'=>'required','id'=>'modal_month'])!!}
                            </div>
                        </div>
                        <div class="clearfix form-actions">
                                <button class="btn btn-info pull-right">Update</button>
                        </div>
                        {!!Form::close()!!}
                    </div>
                    
                </div>
                <div class="modal-footer">
                     <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
@include('includes.scripts.dataTable_scripts')
@include('includes.scripts.delete_confirm')
<script src="{{asset('assets/js/bootstrap-multiselect.min.js')}}"></script>
<script type="text/javascript">
    $('.cors').prop('selectedIndex','');
   function showSubmenu($id){
        var id='slide'+$id;
        var icon='icon'+$id;
        $('#'+id).slideToggle();

        $('.'+icon).toggle();
   }
    function editassign($id){
         $('#modal_row').hide();
        $('#error_msg').hide();
        $
       var assign_id=$id;
       $.post('{{route('loadAssignFees')}}',
       {
        id:assign_id,
        _token:'{{csrf_token()}}'
       },
       function(response){
        var data=$.parseJSON(response);
        if(data.error){
            toastr.warning(data.msg,"Warning");
            $('#modal_row').hide();
            $('#error_msg').show();
            $('#error_msg').html('').append('<div  style="color:red;text-align:center;width:100%;font-size:18px !important">'+data.msg+'<br></div>');
        }
        else{
            $('#modal_row').show();
            $.each(data.data,function(key,val){
                $('#id').val(val.id);
                $('#feeCourse').html('').append(val.course);
                $('#feeHead').html('').append(val.fee_head);
                $('#feeAmount').html('').append(val.fee_amount);
                if(val.std_name!=null){
                  $('#assignedTo').html('').append(val.std_name+'( '+val.reg_no+' )');  
                }else{
                    $('#assignedTo').html('').append(val.course);
                }
                $('#dueIn').html('').append(val.month);
                $('#createdBy').html('').append(val.created_by);
                $('#createdAt').html('').append(val.created_at);
                $('#modal_amount').val('');
                $('select#modal_month').prop('selectedIndex','');
                $('select#modal_batch').html("<option value=''>Select Batch</option>");
                $('select#modal_student').html("<option value=''>Select Student</option>");
                $('select#modal_course').prop('selectedIndex','');
                $('.selectpicker').selectpicker('refresh');
                $('#session').val(val.session_id);
            });
        }
       }
       )
    }
    $('.course').change(function(){
        var cors_id = $(this).val(); 
        var brnch=$('.branch_drop').val();
        var selected_session=$('#session').val();
        var smstr=$('.semester').val();
        $.post("{{route('student_select')}}", {branch:brnch,selected_session:selected_session,semester:smstr,course:cors_id, _token:'{{ csrf_token() }}'}, function(response){
            $('select.std').html(response);
            $('.selectpicker').selectpicker('refresh');
        });
    });   
     /* TO CHANGE BATCH WISE ADD CONTENT MAIN   
    $('.batch_wise_cousre').change(function(){
            var course_id = $(this).val(); 
            $.post(
                "{{route('getBatchByCourse')}}",
                 {course:course_id, _token:'{{ csrf_token() }}'},
                 function(response){
                    var data = $.parseJSON(response);
                    
                    if(data.error){
                        toastr.warning(data.msg,"Warning");
                        var resp = "<option value=''>No Batch Found</option>";
                        
                    }
                    else{
                        toastr.success(data.msg,"Success");
                        var resp = "<option value=''>--Select Batch--</option>";
                        $.each(data.batch,function($k,$v){
                            resp +="<option value='"+$v.id+"'>'"+$v.title+"'</option>";
                        });
                    }
                    $('select.batch').html(resp);
                   
                    $('select.student').html("<option>Select Student</option>");
                    $('.selectpicker').selectpicker('refresh');
            });
    }); 
    $('.batch').change(function(){
        var cors_id = $('#batch_wise_cousre').val(); 
        var batch_id = $(this).val(); 
        var brnch=$('.branch_drop').val();
        var selected_session=$('.sesn').val();
        $.post("{{route('getStudentByBatch')}}", {branch:brnch,selected_session:selected_session,course:cors_id,batch:batch_id, _token:'{{ csrf_token() }}'}, function(response){
           var data = $.parseJSON(response);
                if(data.error){
                    toastr.warning(data.msg,"Warning");
                    var resp = "<option>No Student Found</option>";
                    
                }
                else{
                    toastr.success(data.msg,"Success");
                    var resp = "<option>--Select Student--</option>";
                    $.each(data.student,function($k,$v){
                        resp +="<option value='"+$v.id+"'>'"+$v.first_name+" ("+$v.reg_no+")</option>";
                    });
                }
                $('select.student').html(resp);
                $('.selectpicker').selectpicker('refresh');
        });
    }); */
     /* TO CHANGE BATCH WISE CONTENT MAIN END */ 

     /*  BATCH WISE EDIT MODAL CONTENT  */ 
    $('.batch_wise_cousre_edit_modal').change(function(){
            var course_id = $(this).val(); 
            var session_id = "{{Session::get('activeSession')}}";
            $.post(
                "{{route('getBatchByCourse')}}",
                 {course:course_id , session_id : session_id , _token:'{{ csrf_token() }}'},
                 function(response){
                    var data = $.parseJSON(response);
                    
                    if(data.error){
                        toastr.warning(data.msg,"Warning");
                        var resp = "<option value=''>No Batch Found</option>";
                        
                    }
                    else{
                        toastr.success(data.msg,"Success");
                        var resp = "<option value=''>--Select Batch--</option>";
                        $.each(data.batch,function($k,$v){
                            resp +="<option value='"+$v.id+"'>"+$v.title+"</option>";
                        });
                    }
                    $('select.batch_edit_modal').html(resp);
                   
                    $('select.std').html("<option>Select Student</option>");
                    $('.selectpicker').selectpicker('refresh');
            });
    });
    $('.batch_edit_modal').change(function(){
        var cors_id = $('#modal_course').val(); 
        var batch_id = $(this).val(); 
        var brnch=$('.branch_drop').val();
        var selected_session=$('.sesn').val();
        $.post("{{route('getStudentByBatch')}}", {branch:brnch,selected_session:selected_session,course:cors_id,batch:batch_id, _token:'{{ csrf_token() }}'}, function(response){
           var data = $.parseJSON(response);
                if(data.error){
                    toastr.warning(data.msg,"Warning");
                    var resp = "<option>No Student Found</option>";
                    
                }
                else{
                    toastr.success(data.msg,"Success");
                    var resp = "<option>--Select Student--</option>";
                    $.each(data.student,function($k,$v){
                        resp +="<option value='"+$v.id+"'>'"+$v.first_name+" ("+$v.reg_no+")</option>";
                    });
                }
                $('select.std').html(resp);
                $('.selectpicker').selectpicker('refresh');
        });
    });
     /*  BATCH WISE  MODAL CONTENT END  */ 
</script>
@endsection
